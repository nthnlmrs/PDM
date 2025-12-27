<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Hero Section with Enhanced Carousel -->
            @if($carouselProducts->count() > 0)
            <div class="relative w-full h-[480px] mb-12 rounded-3xl overflow-hidden shadow-2xl group" x-data="{ 
                activeSlide: 0, 
                slides: {{ $carouselProducts->map(fn($p) => [
                    'image' => $p->image ? asset('storage/'.$p->image) : 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200',
                    'name' => $p->name,
                    'desc' => Str::limit($p->description, 80),
                    'slug' => $p->slug,
                    'price' => $p->discount_price ?? $p->price,
                    'originalPrice' => $p->discount_price ? $p->price : null
                ])->toJson() }},
                autoplay: null,
                next() { this.activeSlide = (this.activeSlide === this.slides.length - 1) ? 0 : this.activeSlide + 1 },
                prev() { this.activeSlide = (this.activeSlide === 0) ? this.slides.length - 1 : this.activeSlide - 1 },
                init() { this.autoplay = setInterval(() => this.next(), 5000); }
            }" @mouseenter="clearInterval(autoplay)" @mouseleave="autoplay = setInterval(() => next(), 5000)">
                
                <!-- Decorative Elements -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-br from-purple-500/30 to-pink-500/30 rounded-full blur-3xl z-0"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-gradient-to-br from-blue-500/30 to-cyan-500/30 rounded-full blur-3xl z-0"></div>
                
                <!-- Slides -->
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="activeSlide === index" 
                         x-transition:enter="transition transform duration-700 ease-out"
                         x-transition:enter-start="opacity-0 scale-105"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition transform duration-500 ease-in"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute inset-0 w-full h-full">
                        <img :src="slide.image" class="w-full h-full object-cover transform transition-transform duration-[10000ms] group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        
                        <div class="absolute bottom-0 left-0 p-10 max-w-2xl z-10">
                            <span class="inline-block px-4 py-1.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-sm font-medium rounded-full mb-4 shadow-lg shadow-indigo-500/30">
                                ✨ Featured Product
                            </span>
                            <a :href="'/product/' + slide.slug" class="group/title">
                                <h2 class="text-4xl md:text-5xl font-bold text-white mb-3 leading-tight group-hover/title:text-indigo-300 transition-colors" x-text="slide.name"></h2>
                            </a>
                            <p class="text-lg text-gray-200 mb-6 leading-relaxed" x-text="slide.desc"></p>
                            <div class="flex items-center gap-6">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-bold text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(slide.price)"></span>
                                    <span x-show="slide.originalPrice" class="text-lg text-gray-400 line-through" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(slide.originalPrice)"></span>
                                </div>
                                <a :href="'/product/' + slide.slug" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 font-semibold rounded-xl hover:bg-indigo-50 hover:shadow-xl hover:shadow-white/20 transition-all duration-300 transform hover:-translate-y-0.5">
                                    Beli Sekarang
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Navigation Arrows -->
                <button @click="prev()" class="absolute left-6 top-1/2 -translate-y-1/2 w-14 h-14 bg-white/10 backdrop-blur-md hover:bg-white/30 text-white rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100 flex items-center justify-center shadow-xl hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button @click="next()" class="absolute right-6 top-1/2 -translate-y-1/2 w-14 h-14 bg-white/10 backdrop-blur-md hover:bg-white/30 text-white rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100 flex items-center justify-center shadow-xl hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <!-- Slide Indicators -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                    <template x-for="(slide, index) in slides" :key="'dot-' + index">
                        <button @click="activeSlide = index" 
                                :class="activeSlide === index ? 'w-8 bg-white' : 'w-2 bg-white/40 hover:bg-white/60'" 
                                class="h-2 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>
            @endif

            <!-- Category Pills -->
            @if($categories->count() > 0)
            <div class="mb-10 overflow-x-auto pb-2 scrollbar-hide">
                <div class="flex gap-3 min-w-max">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 {{ !request('category') ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg shadow-indigo-500/30' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 shadow-md' }}">
                        🏠 Semua Produk
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('home', ['category' => $category->id]) }}" 
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300 {{ request('category') == $category->id ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg shadow-indigo-500/30' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 shadow-md hover:shadow-lg hover:-translate-y-0.5' }}">
                            {{ $category->icon ?? '📦' }} {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Main Content -->
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Filters Sidebar -->
                <div class="lg:w-72 flex-shrink-0">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 sticky top-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Filters</h3>
                        </div>
                        
                        <form action="{{ route('home') }}" method="GET" class="space-y-5">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search</label>
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                        placeholder="Search products...">
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">💰 Price Range</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                        <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                            class="w-full pl-7 pr-2 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Min">
                                    </div>
                                    <span class="text-gray-400 self-center">—</span>
                                    <div class="relative flex-1">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                        <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                            class="w-full pl-7 pr-2 py-2.5 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                            placeholder="Max">
                                    </div>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">⭐ Minimum Rating</label>
                                <div class="space-y-2">
                                    @foreach([4, 3, 2, 1] as $rating)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="min_rating" value="{{ $rating }}" {{ request('min_rating') == $rating ? 'checked' : '' }}
                                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-xs text-gray-500 ml-1 group-hover:text-gray-700">& up</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Sort -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">📊 Sort By</label>
                                <select name="sort" class="w-full py-2.5 px-4 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>🆕 Newest First</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>💵 Price: Low to High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>💎 Price: High to Low</option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>⭐ Highest Rated</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-300 shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 transform hover:-translate-y-0.5">
                                Apply Filters
                            </button>

                            @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'min_rating', 'sort']))
                                <a href="{{ route('home') }}" class="block text-center w-full py-2 text-gray-500 hover:text-indigo-600 text-sm font-medium transition-colors">
                                    ✕ Clear All Filters
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="flex-1">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                                @if(request('search'))
                                    Results for "<span class="text-indigo-600">{{ request('search') }}</span>"
                                @elseif(request('category'))
                                    {{ $categories->firstWhere('id', request('category'))->name ?? 'Products' }}
                                @else
                                    Discover Products
                                @endif
                            </h2>
                            <p class="text-gray-500 mt-1">{{ $products->total() }} products found</p>
                        </div>
                    </div>

                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 dark:border-gray-700 hover:-translate-y-1">
                                    <!-- Image Container -->
                                    <div class="relative h-56 overflow-hidden">
                                        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400' }}" 
                                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                        
                                        <!-- Overlay on Hover -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="absolute top-4 right-4 flex flex-col gap-2 translate-x-12 group-hover:translate-x-0 transition-transform duration-300">
                                            @auth
                                            <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-10 h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow-lg hover:bg-red-50 hover:scale-110 transition-all">
                                                    <svg class="w-5 h-5 text-gray-600 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endauth
                                        </div>
                                        
                                        <!-- Badges -->
                                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                                            @if($product->discount_price)
                                                <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                    {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                                </span>
                                            @endif
                                            @if($product->created_at->diffInDays() < 7)
                                                <span class="px-3 py-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded-full shadow-lg">
                                                    NEW
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-5">
                                        <!-- Category & Rating -->
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                                                {{ $product->category->name ?? 'Uncategorized' }}
                                            </span>
                                            <div class="flex items-center gap-1">
                                                @php $avgRating = $product->averageRating(); @endphp
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ number_format($avgRating, 1) }}</span>
                                                <span class="text-xs text-gray-400">({{ $product->reviewCount() }})</span>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <a href="{{ route('product.show', $product) }}" class="block">
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 transition-colors line-clamp-1">
                                                {{ $product->name }}
                                            </h3>
                                        </a>
                                        
                                        <!-- Description -->
                                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1 line-clamp-2">{{ Str::limit($product->description, 70) }}</p>
                                        
                                        <!-- Price & Action -->
                                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                            <div class="flex items-baseline gap-2">
                                                @if($product->discount_price)
                                                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ formatRupiah($product->discount_price) }}</span>
                                                    <span class="text-sm text-gray-400 line-through">{{ formatRupiah($product->price) }}</span>
                                                @else
                                                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ formatRupiah($product->price) }}</span>
                                                @endif
                                            </div>
                                            
                                            <a href="{{ route('product.show', $product) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-sm font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                                                Lihat
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-10">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
                            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No products found</h3>
                            <p class="text-gray-500 mb-6">Try adjusting your filters or search terms.</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</x-app-layout>
