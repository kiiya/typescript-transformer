<?php

use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Actions\TranspilePhpStanTypeToTypeScriptNodeAction;
use Spatie\TypeScriptTransformer\References\ClassStringReference;
use Spatie\TypeScriptTransformer\Tests\Fakes\PropertyTypes\PhpDocTypesStub;
use Spatie\TypeScriptTransformer\TypeResolvers\DocTypeResolver;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeReference;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptAny;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptArray;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptBoolean;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptFunction;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptGeneric;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptIdentifier;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptIntersection;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptNode;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptNull;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptNumber;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptObject;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptProperty;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptString;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptUnion;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptUnknown;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptVoid;

it('can transpile PHPStan doc types', function (
    string $property,
    TypeScriptNode $expectedTypeScriptNode,
) {
    $docTypeResolver = new DocTypeResolver();
    $transpiler = new TranspilePhpStanTypeToTypeScriptNodeAction();

    $typeScriptNode = $transpiler->execute(
        $docTypeResolver->property(new ReflectionProperty(PhpDocTypesStub::class, $property))->type,
        new ReflectionClass(PhpDocTypesStub::class)
    );

    expect($typeScriptNode)->toBeInstanceOf($expectedTypeScriptNode::class);
    expect($typeScriptNode)->toEqual($expectedTypeScriptNode);
})->with(function () {
    yield [
        'string',
        new TypeScriptString(),
    ];

    yield [
        'bool',
        new TypeScriptBoolean(),
    ];

    yield [
        'boolean',
        new TypeScriptBoolean(),
    ];

    yield [
        'int',
        new TypeScriptNumber(),
    ];

    yield [
        'integer',
        new TypeScriptNumber(),
    ];

    yield [
        'float',
        new TypeScriptNumber(),
    ];

    yield [
        'double',
        new TypeScriptNumber(),
    ];

    yield [
        'mixed',
        new TypeScriptAny(),
    ];

    yield [
        'void',
        new TypeScriptVoid(),
    ];

    yield [
        'callable',
        new TypeScriptFunction(),
    ];

    yield [
        'false',
        new TypeScriptBoolean(),
    ];

    yield [
        'true',
        new TypeScriptBoolean(),
    ];

    yield [
        'null',
        new TypeScriptNull(),
    ];

    yield [
        'nullable',
        new TypeScriptUnion([
            new TypeScriptString(),
            new TypeScriptNull(),
        ]),
    ];

    yield [
        'union',
        new TypeScriptUnion([
            new TypeScriptNumber(),
            new TypeScriptString(),
        ]),
    ];

    yield [
        'intersection',
        new TypeScriptIntersection([
            new TypeScriptNumber(),
            new TypeScriptString(),
        ]),
    ];

    yield [
        'bnf',
        new TypeScriptUnion([
            new TypeScriptIntersection([
                new TypeScriptNumber(),
                new TypeScriptString(),
            ]),
            new TypeScriptNull(),
        ]),
    ];

    yield [
        'self',
        new TypeReference(new ClassStringReference(PhpDocTypesStub::class)),
    ];

    yield [
        'static',
        new TypeReference(new ClassStringReference(PhpDocTypesStub::class)),
    ];

    yield [
        'parent',
        new TypeScriptUnknown(),
    ];

    yield [
        'object',
        new TypeScriptObject([]),
    ];

    yield [
        'objectShape',
        new TypeScriptObject([
            new TypeScriptProperty('a', new TypeScriptNumber()),
            new TypeScriptProperty('b', new TypeScriptNumber()),
            new TypeScriptProperty('c', new TypeScriptNumber()),
            new TypeScriptProperty('d', new TypeScriptNumber(), isOptional: true),
        ]),
    ];

    yield [
        'array',
        new TypeScriptArray([]),
    ];

    yield [
        'arrayGeneric',
        new TypeScriptArray([new TypeScriptString()]),
    ];

    yield [
        'arrayGenericWithIntKey',
        new TypeScriptArray([new TypeScriptString()]),
    ];

    yield [
        'arrayGenericWithStringKey',
        new TypeScriptGeneric(
            new TypeScriptIdentifier('Record'),
            [
                new TypeScriptString(),
                new TypeScriptString(),
            ]
        ),
    ];

    yield [
        'arrayGenericWithArrayKey',
        new TypeScriptGeneric(
            new TypeScriptIdentifier('Record'),
            [
                new TypeScriptUnion([
                    new TypeScriptString(),
                    new TypeScriptNumber(),
                ]),
                new TypeScriptString(),
            ]
        ),
    ];

    yield [
        'typeArray',
        new TypeScriptArray([new TypeScriptString()]),
    ];

    yield [
        'nestedArray',
        new TypeScriptGeneric(
            new TypeScriptIdentifier('Record'),
            [
                new TypeScriptString(),
               new TypeScriptArray([new TypeScriptString()]),
            ]
        ),
    ];

    yield [
        'arrayShape',
        new TypeScriptObject([
            new TypeScriptProperty('a', new TypeScriptNumber()),
            new TypeScriptProperty('b', new TypeScriptNumber()),
            new TypeScriptProperty('c', new TypeScriptNumber()),
            new TypeScriptProperty('d', new TypeScriptNumber(), isOptional: true),
        ]),
    ];

    yield [
        'classString',
        new TypeScriptString(),
    ];

    yield [
        'classStringGeneric',
        new TypeScriptString(),
    ];

    yield [
        'reference',
        new TypeReference(new ClassStringReference(Collection::class)),
    ];

    yield [
        'referenceWithImport',
        new TypeReference(new ClassStringReference(Collection::class)),
    ];

    yield [
        'generic',
        new TypeScriptGeneric(
            new TypeReference(new ClassStringReference(Collection::class)),
            [
                new TypeScriptNumber(),
                new TypeScriptString(),
            ]
        ),
    ];
});
