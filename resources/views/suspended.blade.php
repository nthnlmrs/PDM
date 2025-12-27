<x-guest-layout>
    <div class="text-center">
        <div class="mb-4 text-red-600">
            <svg class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Account Suspended</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            Your account has been suspended due to a violation of our terms or suspicious activity.
        </p>

        @if(isset($existingTicket))
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 text-left mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            You have an open appeal ticket (Subject: <strong>{{ $existingTicket->subject }}</strong>).<br>
                            Please wait for the admin to respond.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow text-left">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Request Unban (Create Ticket)</h3>
                
                <form action="{{ route('tickets.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Subject</label>
                        <input type="text" name="subject" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., Unfair suspension appeal" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Message/Reason</label>
                        <textarea name="message" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-900 leading-tight focus:outline-none focus:shadow-outline" rows="4" placeholder="Explain why your account should be restored..." required></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition">
                        Submit Appeal
                    </button>
                </form>
            </div>
        @endif

        <div class="mt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
