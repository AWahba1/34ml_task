<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);


it('returns all products without any filters', function () {
    Product::factory()->count(5)->create();

    $response = $this->get(route('products.index'));

    $response->assertOk();
    $response->assertJsonCount(5, 'data');
});

it('paginates the results', function ($productCount, $perPage) {
    Product::factory()->count($productCount)->create();

    $response = $this->get(route('products.index', ['per_page' => $perPage]));

    $response->assertOk();
    $response->assertJsonCount($perPage, 'data');
    $response->assertJsonPath('per_page', $perPage);

    $lastPageUrl = $response->json('last_page_url');
    preg_match('/page=(\d+)$/', $lastPageUrl, $matches); // extracting page number

    expect($matches)->not->toBeEmpty();

    $lastPageNumber = $matches[1];
    $expectedLastPage = ceil($productCount / $perPage);
    expect($lastPageNumber)->toEqual($expectedLastPage);
})->with([
    [15, 5],
    [20, 10],
    [21, 10],
    [34, 10],
    [35, 10],
]);

it('filters products by average rating', function () {
    createProductWithDetails(4.5, 100);
    createProductWithDetails(4.5, 100);
    createProductWithDetails(3.5, 100);

    $response = $this->getJson(route('products.index', ['average_rating' => 4.5]));

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonPath('data.0.average_rating', 4.5);
    $response->assertJsonPath('data.1.average_rating', 4.5);
});



it('filters products by max price', function () {
    createProductWithDetails(5.0, 50, variantCount: 5);
    createProductWithDetails(5.0, 150);

    $response = $this->getJson(route('products.index', ['max_price' => 100]));

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonCount(5, 'data.0.variants');
    $response->assertJsonPath('data.0.variants.0.price', 50);
});


describe('Filter with Options', function () {

    beforeEach(function () {
        createProductWithDetails(5.0, 100, options: ['size' => ['small', 'medium'], 'color' => ['blue', 'green']]);
        createProductWithDetails(1.9, 80, options: ['size' => ['small', 'medium', 'large'], 'color' => ['red', 'green', 'blue']]);
        createProductWithDetails(3.4, 70, options: ['size' => ['small', 'medium', 'large']]);
        createProductWithDetails(3.4, 70, options: ['color' => ['red']]);
    });


    it('returns products filtered by a single option', function () {
        $response = $this->get(route('products.index', ['options' => 'red']));
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $values = data_get($response->json(), 'data.0.options.1.values', []);
        expect(in_array('red', $values))->toBeTrue();
    });

    it('returns products filtered by multiple options', function () {
        $response = $this->getJson(route('products.index', ['options' => 'red,small']));
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $size_values = data_get($response->json(), 'data.0.options.0.values', []);
        expect(in_array('small', $size_values))->toBeTrue();

        $color_values = data_get($response->json(), 'data.0.options.1.values', []);
        expect(in_array('red', $color_values))->toBeTrue();
    });
});
