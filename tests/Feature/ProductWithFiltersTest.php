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

