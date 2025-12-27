<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                 <!-- Sidebar -->
                <div class="col-span-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-gray-100">Admin Menu</h3>
                    <ul class="space-y-2">
                         <li>
                            <a href="{{ route('admin.dashboard') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">Overview</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">User Management</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.refunds') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">Refund Resolution</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.requests') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">Seller Requests</a>
                        </li>
                    </ul>
                </div>

                 <!-- Main Content -->
                <div class="col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                     <h3 class="font-bold text-lg mb-4 text-gray-900 dark:text-gray-100">System Overview</h3>
                     <p class="text-gray-600 dark:text-gray-400">Welcome, Admin. Select an option from the menu.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
