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
    createProductWithDetails(5.0, 50, 5);
    createProductWithDetails(5.0, 150);

    $response = $this->getJson(route('products.index', ['max_price' => 100]));

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonCount(5, 'data.0.variants');
    $response->assertJsonPath('data.0.variants.0.price', 50);
});


