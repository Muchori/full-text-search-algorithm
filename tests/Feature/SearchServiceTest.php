<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SearchService;
use Database\Seeders\ProductTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private SearchService $searchService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProductTestSeeder::class);
        $this->searchService = new SearchService();
    }

    public function test_basic_search()
    {
        $results = $this->searchService->search('iPhone');
        $this->assertCount(1, $results);
        $this->assertEquals('iPhone 13 Pro', $results->first()->name);

        $results = $this->searchService->search('laptop');
        $this->assertCount(2, $results);
    }

    public function test_partial_word_search()
    {
        $results = $this->searchService->search('pro');
        $this->assertGreaterThan(2, $results->count());
    }

    public function test_search_with_filters()
    {
        $results = $this->searchService->search('pro', [
            'price_range' => ['min' => 1000, 'max' => 2000]
        ]);
        
        foreach ($results as $result) {
            $this->assertGreaterThanOrEqual(1000, $result->price);
            $this->assertLessThanOrEqual(2000, $result->price);
        }
    }

    public function test_min_search_length()
    {
        $results = $this->searchService->search('ip');
        $this->assertCount(0, $results, 'Search with less than 3 characters should return empty collection');
    }

    public function test_search_with_stock_filter()
    {
        $results = $this->searchService->search('pro', ['in_stock' => true]);
        
        foreach ($results as $result) {
            $this->assertGreaterThan(0, $result->stock);
        }
    }
}