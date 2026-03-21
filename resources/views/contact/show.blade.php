<x-public-layout>
    {{-- Contact Header --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="max-w-2xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface tracking-tight mb-6 letterpress">
                Contact Us
            </h1>
            <p class="text-xl md:text-2xl font-headline italic text-on-surface-variant">
                Have a question or feedback? We'd love to hear from you.
            </p>
        </div>
    </section>

    {{-- Success Message --}}
    @if (session('success'))
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-primary-fixed border border-primary/20 rounded-lg p-6 flex items-center gap-4">
                <svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-primary font-body">{{ session('success') }}</p>
            </div>
        </div>
    </section>
    @endif

    {{-- Contact Form --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-20">
        <div class="max-w-2xl mx-auto">
            <div class="bg-surface-container-low rounded-xl p-8 lg:p-12">
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block font-label text-sm font-bold text-on-surface uppercase tracking-wider mb-3">
                            Name <span class="text-tertiary">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-surface border-none rounded-lg text-on-surface font-body placeholder:text-outline/60 focus:ring-2 focus:ring-primary/30 @error('name') ring-2 ring-tertiary @enderror"
                            placeholder="Your name"
                            required
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-tertiary font-label">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block font-label text-sm font-bold text-on-surface uppercase tracking-wider mb-3">
                            Email <span class="text-tertiary">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 bg-surface border-none rounded-lg text-on-surface font-body placeholder:text-outline/60 focus:ring-2 focus:ring-primary/30 @error('email') ring-2 ring-tertiary @enderror"
                            placeholder="your.email@example.com"
                            required
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-tertiary font-label">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label for="subject" class="block font-label text-sm font-bold text-on-surface uppercase tracking-wider mb-3">
                            Subject
                        </label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            value="{{ old('subject') }}"
                            class="w-full px-4 py-3 bg-surface border-none rounded-lg text-on-surface font-body placeholder:text-outline/60 focus:ring-2 focus:ring-primary/30 @error('subject') ring-2 ring-tertiary @enderror"
                            placeholder="What's this about?"
                        >
                        @error('subject')
                            <p class="mt-2 text-sm text-tertiary font-label">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="message" class="block font-label text-sm font-bold text-on-surface uppercase tracking-wider mb-3">
                            Message <span class="text-tertiary">*</span>
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            rows="6"
                            class="w-full px-4 py-3 bg-surface border-none rounded-lg text-on-surface font-body placeholder:text-outline/60 focus:ring-2 focus:ring-primary/30 @error('message') ring-2 ring-tertiary @enderror resize-none"
                            placeholder="Your message..."
                            required
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-2 text-sm text-tertiary font-label">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-outline italic">Maximum 5000 characters</p>
                    </div>

                    {{-- hCaptcha --}}
                    @if (config('HCaptcha.enabled'))
                    <div>
                        <div class="h-captcha" data-sitekey="{{ config('HCaptcha.sitekey') }}"></div>
                        @error('h-captcha-response')
                            <p class="mt-2 text-sm text-tertiary font-label">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    {{-- Submit Button --}}
                    <div>
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-br from-primary to-primary-container text-on-primary font-bold py-4 px-8 rounded-lg hover:opacity-90 transition-all focus:outline-none focus:ring-2 focus:ring-primary/50"
                        >
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- hCaptcha Script --}}
    @if (config('HCaptcha.enabled'))
        <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
    @endif
</x-public-layout>
