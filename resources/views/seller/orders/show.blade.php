<x-seller-layout>
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('seller.orders.index') }}" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pesanan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $order->invoice_number }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pesanan</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Status</p>
                        <div class="mt-1">
                            @if($order->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Selesai
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    Dikirim
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Tanggal Pesanan</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Metode Pembayaran</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white capitalize">{{ $order->payment_method ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Total</p>
                        <p class="mt-1 font-bold text-lg text-green-600">{{ formatRupiah($order->total_amount) }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Item Pesanan</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://via.placeholder.com/64' }}" 
                                 class="w-16 h-16 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }} x {{ formatRupiah($item->price) }}</p>
                            </div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ formatRupiah($item->price * $item->quantity) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Tracking Input -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Nomor Resi</h2>
                
                @if($order->tracking_number)
                    <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl mb-4">
                        <p class="text-sm text-green-600 dark:text-green-400">Resi sudah diinput:</p>
                        <p class="font-mono font-bold text-green-700 dark:text-green-300 text-lg">{{ $order->tracking_number }}</p>
                    </div>
                @endif

                <form action="{{ route('seller.orders.tracking', $order) }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" required
                        placeholder="Contoh: JNE123456789"
                        class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        {{ $order->tracking_number ? 'Update' : 'Simpan' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Buyer Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pembeli</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Nama</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->shipping_name ?? $order->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Telepon</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->shipping_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Alamat</p>
                        <p class="mt-1 font-medium text-gray-900 dark:text-white">{{ $order->shipping_address ?? 'Tidak ada alamat' }}</p>
                    </div>
                </div>
            </div>

            <!-- Confirmation Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status Konfirmasi</h2>
                @if($order->buyer_confirmed_at)
                    <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-center">
                        <svg class="mx-auto w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-2 font-medium text-green-700 dark:text-green-300">Pembeli sudah konfirmasi</p>
                        <p class="text-sm text-green-600 dark:text-green-400">{{ $order->buyer_confirmed_at->format('d M Y, H:i') }}</p>
                    </div>
                @elseif($order->status === 'pending')
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl text-center">
                        <svg class="mx-auto w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="mt-2 font-medium text-amber-700 dark:text-amber-300">Pesanan Perlu Dikirim</p>
                        <p class="text-sm text-amber-600 dark:text-amber-400">Silakan input nomor resi untuk mengirim pesanan</p>
                    </div>
                @else
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                        <svg class="mx-auto w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-2 text-gray-500 dark:text-gray-400">Menunggu konfirmasi pembeli</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-seller-layout>
