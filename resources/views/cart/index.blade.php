<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🛒 Keranjang Belanja
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($cartItems->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Produk</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Jumlah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subtotal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($cartItems as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://via.placeholder.com/60' }}" class="w-16 h-16 object-cover rounded-xl">
                                                    <div class="ml-4">
                                                        <a href="{{ route('product.show', $item->product) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                                            {{ $item->product->name }}
                                                        </a>
                                                        <div class="text-sm text-gray-500">{{ $item->product->seller->store_name ?? $item->product->seller->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($item->product->discount_price)
                                                    <span class="text-gray-500 line-through text-sm">{{ formatRupiah($item->product->price) }}</span>
                                                    <span class="text-indigo-600 font-bold block">{{ formatRupiah($item->product->discount_price) }}</span>
                                                @else
                                                    <span class="text-indigo-600 font-bold">{{ formatRupiah($item->product->price) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="w-20 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-center">
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm">Update</button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                                {{ formatRupiah($item->subtotal) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('cart.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Voucher Section -->
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <h3 class="font-semibold mb-3">🎫 Kode Voucher</h3>
                            @if($voucher ?? false)
                                <div class="flex items-center justify-between bg-green-100 dark:bg-green-900 p-3 rounded-xl">
                                    <div>
                                        <span class="font-bold text-green-700 dark:text-green-300">{{ $voucher['code'] }}</span>
                                        <span class="text-sm text-green-600 dark:text-green-400 ml-2">{{ $voucher['name'] }}</span>
                                    </div>
                                    <span class="text-green-700 dark:text-green-300 font-bold">-{{ formatRupiah($discount ?? 0) }}</span>
                                </div>
                            @else
                                <form action="{{ route('cart.applyVoucher') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="voucher_code" placeholder="Masukkan kode voucher" 
                                        class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-600 uppercase">
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                                        Terapkan
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Cart Summary -->
                        <div class="mt-6 border-t dark:border-gray-700 pt-6">
                            <form action="{{ route('cart.checkout') }}" method="POST" id="checkout-form">
                                @csrf
                                <div class="grid md:grid-cols-2 gap-6">
                                    <!-- Shipping Address Form -->
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                                        <h3 class="font-semibold mb-3">📦 Alamat Pengiriman</h3>
                                        <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Nama Penerima</label>
                                            <input type="text" name="shipping_name" required value="{{ auth()->user()->name }}"
                                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-600">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">No. Telepon</label>
                                            <input type="text" name="shipping_phone" required placeholder="08xxxxxxxxxx"
                                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-600">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Alamat Lengkap</label>
                                            <textarea name="shipping_address" required rows="3" placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-600"></textarea>
                                        </div>
                                </div>

                                <!-- Order Summary -->
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-xl">
                                    <h3 class="font-semibold mb-3">📋 Ringkasan Pesanan</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span>Subtotal</span>
                                            <span>{{ formatRupiah($total) }}</span>
                                        </div>
                                        @if(($discount ?? 0) > 0)
                                            <div class="flex justify-between text-green-600">
                                                <span>Diskon Voucher</span>
                                                <span>-{{ formatRupiah($discount) }}</span>
                                            </div>
                                        @endif
                                        <div class="border-t dark:border-gray-600 pt-2">
                                            <div class="flex justify-between text-lg font-bold">
                                                <span>Total</span>
                                                <span class="text-green-600">{{ formatRupiah($total - ($discount ?? 0)) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Metode Pembayaran</label>
                                        <select name="payment_method" required class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-600">
                                            <option value="">Pilih Metode Pembayaran</option>
                                            <option value="wallet">Wallet (Saldo: {{ formatRupiah(auth()->user()->balance) }})</option>
                                            <option value="credit_card">Kartu Kredit</option>
                                            <option value="paypal">PayPal</option>
                                            <option value="apple_pay">Apple Pay</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="w-full mt-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-600 shadow-lg transition-all">
                                        Checkout Sekarang
                                    </button>
                                </div>
                            </div>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Keranjang Anda kosong</h3>
                            <p class="mt-2 text-gray-500">Mulai belanja untuk menambahkan produk ke keranjang.</p>
                            <a href="{{ route('home') }}" class="mt-4 inline-block bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-6 py-2 rounded-xl hover:from-indigo-600 hover:to-purple-600 shadow-lg">
                                Jelajahi Produk
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
