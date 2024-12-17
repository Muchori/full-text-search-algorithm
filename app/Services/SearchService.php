<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class SearchService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const MIN_SEARCH_LENGTH = 3;
    private const MAX_RESULTS = 100;
    
    /**
     * Search products using MySQL full-text search
     */
    public function search(string $query, array $filters = []): Collection
    {
        if (strlen($query) < self::MIN_SEARCH_LENGTH) {
            return new Collection();
        }
        
        $cacheKey = $this->generateCacheKey($query, $filters);
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Prepare search terms for MySQL boolean mode
        $searchQuery = $this->prepareSearchQuery($query);
        
        // Build the base query using MySQL MATCH AGAINST
        $baseQuery = Product::query()
            ->select([
                'products.id',
                'products.name',
                'products.description',
                'products.price',
                'products.stock',
                DB::raw("MATCH(name, description) AGAINST(? IN BOOLEAN MODE) as relevance")
            ])
            ->whereRaw(
                "MATCH(name, description) AGAINST(? IN BOOLEAN MODE)",
                [$searchQuery, $searchQuery]
            );
            
        // Apply filters
        $this->applyFilters($baseQuery, $filters);
        
        // Execute query with optimizations
        $results = $baseQuery
            ->orderBy('relevance', 'desc')
            ->limit(self::MAX_RESULTS)
            ->get();
            
        Cache::put($cacheKey, $results, self::CACHE_TTL);
        
        return $results;
    }
    
    /**
     * Prepare search query for MySQL boolean mode
     */
    private function prepareSearchQuery(string $query): string
    {
        // Split the query into words
        $words = preg_split('/\s+/', trim($query));
        
        // Prepare terms for MySQL boolean mode
        $terms = array_map(function($word) {
            // Add wildcards for partial matches
            return '+' . $word . '*';
        }, $words);
        
        return implode(' ', $terms);
    }
    
    private function generateCacheKey(string $query, array $filters): string
    {
        return 'search:' . md5($query . serialize($filters));
    }
    
    private function applyFilters($query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            switch ($field) {
                case 'category':
                    $query->whereHas('categories', function ($q) use ($value) {
                        $q->where('categories.id', $value);
                    });
                    break;
                    
                case 'price_range':
                    if (isset($value['min'])) {
                        $query->where('price', '>=', $value['min']);
                    }
                    if (isset($value['max'])) {
                        $query->where('price', '<=', $value['max']);
                    }
                    break;
                    
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
            }
        }
    }
}