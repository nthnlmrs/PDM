<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">Home</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @if($product->category)
                    <a href="{{ route('home', ['category' => $product->category_id]) }}" class="hover:text-indigo-600 transition-colors">{{ $product->category->name }}</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @endif
                <span class="text-gray-900 dark:text-white font-medium">{{ $product->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Image -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400' }}" 
                             class="w-full h-auto object-cover" alt="{{ $product->name }}">
                    </div>
                </div>

                <!-- Middle: Product Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $product->name }}</h1>
                        
                        <!-- Rating -->
                        @php $avgRating = $product->averageRating(); @endphp
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($avgRating, 1) }} ({{ $product->reviewCount() }} ulasan)</span>
                        </div>

                        <!-- Seller & Category -->
                        <div class="flex flex-wrap items-center gap-2 mb-6">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Dijual oleh</span>
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $product->seller->store_name ?? $product->seller->name }}</span>
                            @if($product->category)
                                <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg">
                                    {{ $product->category->icon }} {{ $product->category->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            @if($product->discount_price)
                                <div class="flex items-center gap-3">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatRupiah($product->discount_price) }}</span>
                                    <span class="text-lg text-gray-400 line-through">{{ formatRupiah($product->price) }}</span>
                                    <span class="px-2.5 py-1 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 text-xs font-bold rounded-full">
                                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                    </span>
                                </div>
                            @else
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatRupiah($product->price) }}</span>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="prose prose-sm dark:prose-invert text-gray-700 dark:text-gray-300">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Right: Action Box -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 sticky top-6" x-data="{ 
                        quantity: 1, 
                        maxStock: {{ $product->stock }}, 
                        price: {{ $product->discount_price ?? $product->price }},
                        modalOpen: false,
                        paymentMethod: 'wallet',
                        calculateTotal() { return this.price * this.quantity; }
                    }">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Atur Jumlah</h3>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                <button @click="if(quantity > 1) quantity--" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">-</button>
                                <input type="text" x-model="quantity" class="w-14 text-center border-0 focus:ring-0 bg-transparent text-gray-900 dark:text-white" readonly>
                                <button @click="if(quantity < maxStock) quantity++" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">+</button>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Stok: <span class="font-bold text-gray-900 dark:text-white" x-text="maxStock"></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-6 py-4 border-t border-b border-gray-200 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white" x-text="'Rp ' + calculateTotal().toLocaleString('id-ID')"></span>
                        </div>

                        <button 
                            @click="if(maxStock > 0) modalOpen = true" 
                            :disabled="maxStock <= 0"
                            class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed mb-3">
                            <span x-text="maxStock > 0 ? 'Beli Sekarang' : 'Stok Habis'"></span>
                        </button>

                        @auth
                        <form action="{{ route('cart.store', $product) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="quantity" :value="quantity">
                            <button type="submit" :disabled="maxStock <= 0" class="w-full py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-lg transition-colors disabled:opacity-50">
                                🛒 Tambah ke Keranjang
                            </button>
                        </form>

                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                            @csrf
                            @php
                                $inWishlist = Auth::check() && \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists();
                            @endphp
                            <button type="submit" class="w-full py-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg transition-colors">
                                {{ $inWishlist ? '❤️ Di Wishlist' : '🤍 Tambah ke Wishlist' }}
                            </button>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="block text-center w-full py-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg transition-colors">
                            Login untuk Membeli
                        </a>
                        @endauth
                        
                        <!-- Checkout Modal -->
                        <div x-show="modalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="modalOpen = false"></div>

                                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Konfirmasi Pembelian</h3>
                                    
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        Anda akan membeli <strong x-text="quantity"></strong> x <strong>{{ $product->name }}</strong>
                                    </p>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran</label>
                                        <select x-model="paymentMethod" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                            <option value="wallet">Saldo ({{ formatRupiah(Auth::user()->balance ?? 0) }})</option>
                                            <option value="bank_transfer">Transfer Bank</option>
                                            <option value="e_wallet">E-Wallet</option>
                                        </select>
                                    </div>

                                    <div class="flex justify-between items-center py-4 border-t border-gray-200 dark:border-gray-700 mb-4">
                                        <span class="font-bold text-gray-700 dark:text-gray-300">Total:</span>
                                        <span class="text-2xl font-bold text-indigo-600" x-text="'Rp ' + calculateTotal().toLocaleString('id-ID')"></span>
                                    </div>

                                    <div class="flex gap-3">
                                        <button @click="modalOpen = false" type="button" class="flex-1 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                            Batal
                                        </button>
                                        <form action="{{ route('order.store', $product) }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="quantity" :value="quantity">
                                            <input type="hidden" name="payment_method" :value="paymentMethod">
                                            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                                                Bayar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Ulasan Pembeli</h2>

                @auth
                    @php
                        $completedOrders = Auth::user()->orders()
                            ->where('status', 'completed')
                            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                            ->whereDoesntHave('reviews', fn($q) => $q->where('product_id', $product->id)->where('user_id', Auth::id()))
                            ->get();
                    @endphp

                    @if($completedOrders->count() > 0)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tulis Ulasan</h3>
                            <form action="{{ route('reviews.store', $product) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Pesanan</label>
                                    <select name="order_id" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        @foreach($completedOrders as $order)
                                            <option value="{{ $order->id }}">#{{ $order->invoice_number }} - {{ $order->created_at->format('d M Y') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4" x-data="{ rating: 5 }">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                    <div class="flex gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" @click="rating = {{ $i }}" :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'" class="text-3xl focus:outline-none hover:scale-110 transition-transform">★</button>
                                        @endfor
                                        <input type="hidden" name="rating" x-model="rating">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Komentar (opsional)</label>
                                    <textarea name="comment" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Bagikan pengalaman Anda..."></textarea>
                                </div>
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                    Kirim Ulasan
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth

                @if($product->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($product->reviews as $review)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">{{ substr($review->user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $review->user->name }}</span>
                                            <div class="flex mt-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                @if($review->comment)
                                    <p class="text-gray-700 dark:text-gray-300 ml-13">{{ $review->comment }}</p>
                                @endif
                                @if(Auth::id() === $review->user_id)
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="mt-2 ml-13" onsubmit="return confirm('Hapus ulasan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 text-sm hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">Belum ada ulasan. Jadilah yang pertama!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
