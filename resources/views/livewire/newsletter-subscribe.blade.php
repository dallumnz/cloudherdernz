<div>
    <form wire:submit.prevent="subscribe" class="flex">
        <input 
            type="email" 
            wire:model="email"
            placeholder="Your email" 
            class="flex-1 px-4 py-2 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-l-lg text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-indigo-500"
            required
        >
        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-r-lg transition">
            Subscribe
        </button>
    </form>

    @if($message)
        <p class="mt-2 text-sm {{ $messageType === 'success' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
            {{ $message }}
        </p>
    @endif
</div>
