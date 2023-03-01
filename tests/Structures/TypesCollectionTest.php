<?php

use Spatie\TypeScriptTransformer\Tests\Factories\TransformedTypeFactory;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNull;
use Spatie\TypeScriptTransformer\Exceptions\SymbolAlreadyExists;
use Spatie\TypeScriptTransformer\Structures\TypesCollection;
use Spatie\TypeScriptTransformer\Tests\FakeClasses\Enum\TypeScriptEnum;
use Spatie\TypeScriptTransformer\Tests\Fakes\FakeTransformedType;

it('can get a type', function () {
    $collection = TypesCollection::create();

    $collection->add($fake = TransformedTypeFactory::create('a\b\c\Enum')->build());

    assertEquals($fake, $collection->get('a\b\c\Enum'));
});

it('can get a type in the root namespace', function () {
    $collection = TypesCollection::create();

    $collection->add($fake = TransformedTypeFactory::create('Enum')->build());

    assertEquals($fake, $collection->get('Enum'));
});

it('when searching a non existing type null is returned', function () {
    $collection = TypesCollection::create();

    assertNull($collection->get('Enum'));
    assertNull($collection->get('a\b\Enum'));
    assertNull($collection->get('a\b\Enum'));
});

it('can count types', function () {
    $collection = TypesCollection::create();

    $collection->add(TransformedTypeFactory::create('EnumA')->build());
    $collection->add(TransformedTypeFactory::create('EnumB')->build());

    expect($collection)->toHaveCount(2);
});

it('can iterate over types', function () {
    $collection = TypesCollection::create();

    $collection->add($fakeA = TransformedTypeFactory::create('EnumA')->build());
    $collection->add($fakeB = TransformedTypeFactory::create('EnumB')->build());

    $types = [];

    foreach ($collection as $type){
        $types[] = $type;
    }

    expect($types)
        ->toHaveCount(2)
        ->toContain($fakeA)
        ->toContain($fakeB);
});
