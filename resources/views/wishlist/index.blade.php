<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Wishlist Saya</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Produk yang Anda simpan</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if($wishlists->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($wishlists as $wishlist)
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="relative">
                                <img src="{{ $wishlist->product->image ? asset('storage/'.$wishlist->product->image) : 'https://via.placeholder.com/300' }}" 
                                     class="w-full h-48 object-cover">
                                @if($wishlist->product->discount_price)
                                    <span class="absolute top-3 left-3 px-2.5 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                        {{ round((($wishlist->product->price - $wishlist->product->discount_price) / $wishlist->product->price) * 100) }}% OFF
                                    </span>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-1">
                                    {{ $wishlist->product->category->name ?? 'Tanpa Kategori' }}
                                </p>
                                <a href="{{ route('product.show', $wishlist->product) }}" class="block">
                                    <h3 class="font-semibold text-gray-900 dark:text-white hover:text-indigo-600 transition-colors line-clamp-1">
                                        {{ $wishlist->product->name }}
                                    </h3>
                                </a>
                                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm line-clamp-2">{{ Str::limit($wishlist->product->description, 60) }}</p>
                                
                                <div class="mt-3 flex items-baseline gap-2">
                                    @if($wishlist->product->discount_price)
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ formatRupiah($wishlist->product->discount_price) }}</span>
                                        <span class="text-sm text-gray-400 line-through">{{ formatRupiah($wishlist->product->price) }}</span>
                                    @else
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ formatRupiah($wishlist->product->price) }}</span>
                                    @endif
                                </div>

                                <div class="mt-4 flex gap-2">
                                    <form action="{{ route('cart.store', $wishlist->product) }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            Tambah ke Keranjang
                                        </button>
                                    </form>
                                    <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 text-red-600 bg-red-50 dark:bg-red-900/50 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($wishlists->hasPages())
                    <div class="mt-8">
                        {{ $wishlists->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Wishlist kosong</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Simpan produk favorit Anda ke wishlist.</p>
                    <a href="{{ route('home') }}" class="mt-4 inline-block px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Jelajahi Produk
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
