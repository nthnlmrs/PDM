<x-admin-layout>
    <div x-data="{ 
        roleModalOpen: false, 
        suspendModalOpen: false,
        topUpModalOpen: false,
        userName: '', 
        currentRole: '', 
        actionUrl: '',
        suspendActionUrl: '',
        topUpActionUrl: '',
        isSuspending: true
    }">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">User Management</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua pengguna terdaftar</p>
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

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $users->total() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Buyers</p>
                <p class="mt-1 text-2xl font-bold text-blue-600">{{ $users->where('role', 'buyer')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sellers</p>
                <p class="mt-1 text-2xl font-bold text-green-600">{{ $users->where('role', 'seller')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Suspended</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ $users->where('is_suspended', true)->count() }}</p>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Saldo</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Registered</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->role === 'admin')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                            Admin
                                        </span>
                                    @elseif($user->role === 'seller')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                                            Seller
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                            Buyer
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ formatRupiah($user->balance) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->is_suspended)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Suspended
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($user->role !== 'admin' || Auth::id() !== $user->id)
                                        <div class="flex items-center justify-end gap-2">
                                            <button 
                                                @click="topUpModalOpen = true; userName = '{{ $user->name }}'; topUpActionUrl = '{{ route('admin.users.topup', $user) }}'"
                                                class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-900/50 dark:text-emerald-300 rounded-lg transition-colors">
                                                Top Up
                                            </button>

                                            <button 
                                                @click="roleModalOpen = true; userName = '{{ $user->name }}'; currentRole = '{{ $user->role }}'; actionUrl = '{{ route('admin.users.role', $user) }}'"
                                                class="px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/50 dark:text-indigo-300 rounded-lg transition-colors">
                                                Role
                                            </button>

                                            @if($user->is_suspended)
                                                <button 
                                                    @click="suspendModalOpen = true; userName = '{{ $user->name }}'; isSuspending = false; suspendActionUrl = '{{ route('admin.users.suspend', $user) }}'"
                                                    class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/50 dark:text-green-300 rounded-lg transition-colors">
                                                    Unsuspend
                                                </button>
                                            @else
                                                <button 
                                                    @click="suspendModalOpen = true; userName = '{{ $user->name }}'; isSuspending = true; suspendActionUrl = '{{ route('admin.users.suspend', $user) }}'"
                                                    class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/50 dark:text-red-300 rounded-lg transition-colors">
                                                    Suspend
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">Restricted</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Role Change Modal -->
        <div x-show="roleModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="roleModalOpen = false"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Ubah Role User</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Ubah role untuk user: <span class="font-semibold text-gray-900 dark:text-white" x-text="userName"></span></p>
                    
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Role</label>
                            <select name="role" x-model="currentRole" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                <option value="user">Buyer</option>
                                <option value="seller">Seller</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="roleModalOpen = false" class="flex-1 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Start Top Up Modal -->
        <div x-show="topUpModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="topUpModalOpen = false"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Top Up / Deduct Balance</h3>
                    </div>
                    
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Kelola saldo untuk user: <span class="font-semibold text-gray-900 dark:text-white" x-text="userName"></span></p>
                    
                    <form :action="topUpActionUrl" method="POST">
                        @csrf
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Aksi</label>
                                <select name="action" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                                    <option value="add">Tambah Saldo (+)</option>
                                    <option value="deduct">Kurangi Saldo (-)</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah (Rp)</label>
                                <input type="number" name="amount" step="1000" min="0" required 
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500" 
                                       placeholder="50000">
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="topUpModalOpen = false" class="flex-1 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors">
                                Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End Top Up Modal -->

        <!-- Suspend/Unsuspend Confirmation Modal -->
        <div x-show="suspendModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="suspendModalOpen = false"></div>

                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <div class="mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" :class="isSuspending ? 'bg-red-100 text-red-500' : 'bg-green-100 text-green-500'">
                            <svg x-show="isSuspending" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <svg x-show="!isSuspending" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="isSuspending ? 'Suspend User?' : 'Unsuspend User?'"></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Apakah Anda yakin ingin <span x-text="isSuspending ? 'men-suspend' : 'mengaktifkan kembali'"></span> user <span class="font-semibold text-gray-900 dark:text-white" x-text="userName"></span>?
                        </p>
                    </div>
                    
                    <form :action="suspendActionUrl" method="POST">
                        @csrf
                        <div class="flex gap-3">
                            <button type="button" @click="suspendModalOpen = false" class="flex-1 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="flex-1 py-2.5 text-white font-semibold rounded-lg transition-colors" :class="isSuspending ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                                <span x-text="isSuspending ? 'Ya, Suspend' : 'Ya, Aktifkan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
