<?php

namespace Spatie\TypeScriptTransformer\Laravel\ClassPropertyProcessors;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use ReflectionProperty;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\DataConfig;
use Spatie\TypeScriptTransformer\References\ClassStringReference;
use Spatie\TypeScriptTransformer\Transformers\ClassPropertyProcessors\ClassPropertyProcessor;
use Spatie\TypeScriptTransformer\TypeScript\TypeReference;
use Spatie\TypeScriptTransformer\TypeScript\TypeScriptIdentifier;
use Spatie\TypeScriptTransformer\TypeScript\TypeScriptProperty;
use Spatie\TypeScriptTransformer\TypeScript\TypeScriptUnion;

class DataClassPropertyProcessor implements ClassPropertyProcessor
{
    protected array $lazyTypes = [
        'Spatie\LaravelData\Lazy',
        'Spatie\LaravelData\Support\Lazy\ClosureLazy',
        'Spatie\LaravelData\Support\Lazy\ConditionalLazy',
        'Spatie\LaravelData\Support\Lazy\DefaultLazy',
        'Spatie\LaravelData\Support\Lazy\InertiaLazy',
        'Spatie\LaravelData\Support\Lazy\RelationalLazy',
    ];

    public function __construct(
        protected DataConfig $dataConfig,
        protected array $customLazyTypes = [],
    ) {
        $this->lazyTypes = array_merge($this->lazyTypes, $this->customLazyTypes);
    }

    public function execute(
        ReflectionProperty $reflection,
        ?TypeNode $annotation,
        TypeScriptProperty $property
    ): ?TypeScriptProperty {
        $dataClass = $this->dataConfig->getDataClass($reflection->getDeclaringClass()->getName());
        $dataProperty = $dataClass->properties->get($reflection->getName());

        if ($dataProperty->hidden) {
            return null;
        }

        if ($dataProperty->outputMappedName) {
            $property->name = new TypeScriptIdentifier($dataProperty->outputMappedName);
        }

        if (! $property->type instanceof TypeScriptUnion) {
            return $property;
        }

        for ($i = 0; $i < count($property->type->types); $i++) {
            $subType = $property->type->types[$i];

            if ($subType instanceof TypeReference && $this->shouldHideReference($subType)) {
                $property->isOptional = true;

                unset($property->type->types[$i]);
            }
        }

        $property->type->types = array_values($property->type->types);

        if (count($property->type->types) === 1) {
            $property->type = $property->type->types[0];
        }

        return $property;
    }

    protected function shouldHideReference(
        TypeReference $reference
    ): bool {
        if (! $reference->reference instanceof ClassStringReference) {
            return false;
        }

        return in_array($reference->reference->classString, $this->lazyTypes)
            || $reference->reference->classString === Optional::class;
    }
}