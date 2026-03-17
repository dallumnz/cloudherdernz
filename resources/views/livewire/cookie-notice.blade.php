<div>
    @if($showNotice)
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-slate-900 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex-1 text-sm">
                        <p class="mb-2">
                            <strong>Privacy Notice</strong>
                        </p>
                        <p class="text-slate-300">
                            We use essential cookies for site functionality (session management, CSRF protection) 
                            and do not track you across the web. By using this site, you acknowledge our 
                            <a href="{{ route('privacy') }}" class="underline hover:text-white">Privacy Policy</a> 
                            and New Zealand Privacy Act 2020 compliance.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('privacy') }}" 
                           class="text-sm text-slate-300 hover:text-white underline whitespace-nowrap">
                            Learn More
                        </a>
                        <button wire:click="acknowledge" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                            Acknowledge
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
