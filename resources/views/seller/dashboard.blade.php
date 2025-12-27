<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Seller Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sidebar -->
                <div class="col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-gray-100">Menu</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('seller.dashboard') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">Overview</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.products.index') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">My Products</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.refunds.index') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">Refund Requests</a>
                        </li>
                    </ul>
                </div>

                <!-- Main Content -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-gray-100">Overview</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-indigo-100 dark:bg-indigo-900 p-4 rounded text-center">
                            <span class="block text-2xl font-bold text-indigo-800 dark:text-indigo-200">{{ Auth::user()->products()->count() }}</span>
                            <span class="text-sm text-indigo-600 dark:text-indigo-300">Products</span>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900 p-4 rounded text-center">
                            <span class="block text-2xl font-bold text-green-800 dark:text-green-200">${{ Auth::user()->balance }}</span>
                            <span class="text-sm text-green-600 dark:text-green-300">Earnings (Balance)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
