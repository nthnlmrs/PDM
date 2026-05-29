<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pesanan Saya</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Riwayat pembelian Anda</p>
            </div>

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

            <!-- Orders Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Pembayaran</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Resi</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $order->invoice_number }}</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('d M Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-3">
                                            @foreach($order->items as $item)
                                                @if($item->product)
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://via.placeholder.com/40' }}" class="w-10 h-10 rounded-lg object-cover">
                                                        <div>
                                                            <p class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                                                            <p class="text-sm text-gray-500">x{{ $item->quantity }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ formatRupiah($order->total_amount) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="capitalize text-sm text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', $order->payment_method ?? '-') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
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
                                        @elseif($order->status === 'disputed' || $order->status === 'refunded')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($order->tracking_number)
                                            <span class="font-mono text-sm text-gray-900 dark:text-white">{{ $order->tracking_number }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($order->status === 'shipped' && !$order->buyer_confirmed_at)
                                                <form action="{{ route('order.confirmReceipt', $order) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                                        Konfirmasi Terima
                                                    </button>
                                                </form>
                                                <button @click="$dispatch('open-refund-modal', { orderId: {{ $order->id }} })" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:text-red-300 rounded-lg transition-colors">
                                                    Ajukan Refund
                                                </button>
                                            @elseif($order->status === 'completed' && !$order->refund)
                                                <button @click="$dispatch('open-refund-modal', { orderId: {{ $order->id }} })" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:text-red-300 rounded-lg transition-colors">
                                                    Ajukan Refund
                                                </button>
                                            @elseif($order->status === 'pending')
                                                <span class="text-xs text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2 py-1 rounded">Menunggu Pengiriman</span>
                                            @elseif($order->buyer_confirmed_at)
                                                <span class="text-xs text-green-600">✓ Diterima</span>
                                            @endif
                                            
                                            @if($order->refund)
                                                <span class="text-xs text-gray-500">Refund: {{ ucfirst($order->refund->status) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="mt-4 text-gray-500 dark:text-gray-400">Belum ada pesanan</p>
                                        <a href="{{ route('home') }}" class="mt-3 inline-block text-indigo-600 hover:text-indigo-500 font-medium text-sm">
                                            Mulai Belanja
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div x-data="{ open: false, orderId: null }" @open-refund-modal.window="open = true; orderId = $event.detail.orderId" class="fixed inset-0 z-50 overflow-y-auto" x-show="open" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="open = false"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ajukan Refund</h3>
                <form method="POST" :action="`/orders/${orderId}/refund`">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alasan Refund</label>
                        <textarea name="reason" rows="4" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Jelaskan alasan refund..."></textarea>
                    </div>
                    <div class="mt-6 flex gap-3 justify-end">
                        <button type="button" @click="open = false" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors">
                            Ajukan Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
