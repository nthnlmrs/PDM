<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 'active')->with('category', 'reviews');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where(function($q) use ($request) {
                $q->where('discount_price', '>=', $request->min_price)
                  ->orWhere(function($q2) use ($request) {
                      $q2->whereNull('discount_price')
                         ->where('price', '>=', $request->min_price);
                  });
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function($q) use ($request) {
                $q->where('discount_price', '<=', $request->max_price)
                  ->orWhere(function($q2) use ($request) {
                      $q2->whereNull('discount_price')
                         ->where('price', '<=', $request->max_price);
                  });
            });
        }

        // Rating filter
        if ($request->filled('min_rating')) {
            $minRating = $request->min_rating;
            $query->whereHas('reviews', function($q) use ($minRating) {
                // Group by product_id to calculate average
            })->withAvg('reviews', 'rating')
              ->having('reviews_avg_rating', '>=', $minRating);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')
                      ->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        // Random 5 products for carousel
        $carouselProducts = Product::where('status', 'active')->inRandomOrder()->take(5)->get();

        // Categories for filter
        $categories = Category::withCount('products')->get();

        return view('welcome', compact('products', 'carouselProducts', 'categories'));
    }

    public function show(Product $product)
    {
        if ($product->status !== 'active') {
            abort(404);
        }

        $product->load(['reviews.user', 'category', 'seller']);
        
        return view('products.show', compact('product'));
    }
}

