<?php

use App\Models\Product;
use Database\Factories\VariantFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Events\ProductOutOfStock;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Mail\OutOfStockNotification;
use App\Listeners\SendOutOfStockNotification;



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

it('returns the correct default variant based on the lowest price', function () {
    $product = Product::factory()->create();
    $higherPricedVariant = VariantFactory::new(['price' => 200, 'product_id' => $product->id])->create();
    $lowerPricedVariant = VariantFactory::new(['price' => 100, 'product_id' => $product->id])->create();
    $mediumPricedVariant = VariantFactory::new(['price' => 150, 'product_id' => $product->id])->create();

    $defaultVariant = $product->default_variant;
    expect($defaultVariant->id)->toBe($lowerPricedVariant->id);
});

describe('Filters with multiple combinations', function () {
    it('filters products by average rating and max price', function () {
        createProductWithDetails(4.5, 80);
        createProductWithDetails(4.5, 120);
        createProductWithDetails(3.5, 100);

        $response = $this->getJson(route('products.index', [
            'average_rating' => 4.5,
            'max_price' => 100
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.average_rating', 4.5);
        $response->assertJsonPath('data.0.variants.0.price', 80);
    });

    it('filters products by average rating and options', function () {
        createProductWithDetails(4.5, 100, options: ['color' => ['red']]);
        createProductWithDetails(4.5, 100, options: ['color' => ['blue']]);
        createProductWithDetails(3.5, 100, options: ['color' => ['red']]);

        $response = $this->getJson(route('products.index', [
            'average_rating' => 4.5,
            'options' => 'red'
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.average_rating', 4.5);
        $values = data_get($response->json(), 'data.0.options.0.values', []);
        expect(in_array('red', $values))->toBeTrue();
    });

    it('filters products by max price and options', function () {
        createProductWithDetails(5.0, 50, options: ['size' => ['small']]);
        createProductWithDetails(5.0, 150, options: ['size' => ['large']]);

        $response = $this->getJson(route('products.index', [
            'max_price' => 100,
            'options' => 'small'
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.variants.0.price', 50);
        $values = data_get($response->json(), 'data.0.options.0.values', []);
        expect(in_array('small', $values))->toBeTrue();
    });

    it('filters products by average rating, max price, and options', function () {
        createProductWithDetails(4.5, 80, options: ['color' => ['red'], 'size' => ['small', 'large']]);
        createProductWithDetails(4.5, 120, options: ['color' => ['blue']]);
        createProductWithDetails(3.5, 100, options: ['color' => ['red']]);

        $response = $this->getJson(route('products.index', [
            'average_rating' => 4.5,
            'max_price' => 100,
            'options' => 'red,small'
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.variants.0.price', 80);
        $response->assertJsonPath('data.0.average_rating', 4.5);
        $color_values = data_get($response->json(), 'data.0.options.0.values', []);
        $size_values = data_get($response->json(), 'data.0.options.1.values', []);
        expect(in_array('small', $size_values))->toBeTrue();
        expect(in_array('red', $color_values))->toBeTrue();
    });
});



it('fires ProductOutOfStock event and sends an email', function () {

    Event::fake();
    Mail::fake();
    $product = Product::factory()->create();

    // Testing event firing
    $event = new ProductOutOfStock($product);
    event($event);

    Event::assertDispatched(ProductOutOfStock::class, function ($event) use ($product) {
        return $event->product->id === $product->id;
    });

    // Test sending email upon event handling
    $listener = new SendOutOfStockNotification();
    $listener->handle($event);

    $adminEmail = env('ADMIN_EMAIL_ADDRESS');
    Mail::assertSent(OutOfStockNotification::class, function ($mail) use ($adminEmail, $product) {
        return $mail->product->id === $product->id
            && $mail->hasTo($adminEmail);
    });
});
