<x-public-layout>
    <x-slot:head>
        <title>Privacy Policy | {{ config('app.name') }}</title>
    </x-slot:head>

    {{-- Header --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pt-20 pb-12">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-headline font-bold text-on-surface tracking-tight mb-6 letterpress">
                Privacy Policy
            </h1>
            <p class="text-sm text-outline font-label uppercase tracking-widest">
                Last updated: March 17, 2026
            </p>
        </div>
    </section>

    {{-- Content --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-8 pb-20">
        <div class="max-w-4xl mx-auto">
            <article class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-headline font-body">
                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Overview</h2>
                <p class="leading-relaxed mb-6">
                    CloudHerder.nz ("we", "us", "our") is committed to protecting your privacy. 
                    This Privacy Policy explains how we collect, use, disclose, and safeguard your 
                    information in compliance with the <strong>New Zealand Privacy Act 2020</strong> 
                    and the <strong>EU General Data Protection Regulation (GDPR)</strong> where applicable.
                </p>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Information We Collect</h2>
                
                <h3 class="text-xl font-headline font-semibold text-primary mt-8 mb-4">Essential Data (Automatically Collected)</h3>
                <ul class="space-y-3 mb-8">
                    <li><strong>Session cookies</strong> — Required for login functionality and form submissions (CSRF protection)</li>
                    <li><strong>Server logs</strong> — IP addresses, browser type, pages visited (retained for 30 days for security)</li>
                    <li><strong>Contact form submissions</strong> — Name, email, message content (only when you submit)</li>
                </ul>

                <h3 class="text-xl font-headline font-semibold text-primary mt-8 mb-4">Account Data (When You Register)</h3>
                <ul class="space-y-3 mb-8">
                    <li>Name and email address</li>
                    <li>Encrypted password (we cannot read this)</li>
                    <li>Optional: Two-factor authentication settings</li>
                </ul>

                <h3 class="text-xl font-headline font-semibold text-primary mt-8 mb-4">Newsletter Data</h3>
                <ul class="space-y-3 mb-8">
                    <li>Email address (only with explicit opt-in)</li>
                    <li>Subscription preferences</li>
                    <li>Confirmation timestamps (double opt-in)</li>
                </ul>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">How We Use Your Information</h2>
                <ul class="space-y-3 mb-8">
                    <li>To provide and maintain the website functionality</li>
                    <li>To respond to contact form submissions</li>
                    <li>To send newsletters (only to confirmed subscribers)</li>
                    <li>To detect and prevent security incidents</li>
                    <li>To comply with legal obligations</li>
                </ul>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Cookies We Use</h2>
                <div class="overflow-x-auto mb-8">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-outline-variant/30">
                                <th class="text-left py-3 px-4 font-label uppercase tracking-wider text-xs text-outline">Cookie Name</th>
                                <th class="text-left py-3 px-4 font-label uppercase tracking-wider text-xs text-outline">Purpose</th>
                                <th class="text-left py-3 px-4 font-label uppercase tracking-wider text-xs text-outline">Duration</th>
                                <th class="text-left py-3 px-4 font-label uppercase tracking-wider text-xs text-outline">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/20">
                            <tr>
                                <td class="py-3 px-4 font-mono text-sm text-primary">laravel_session</td>
                                <td class="py-3 px-4">Session management</td>
                                <td class="py-3 px-4">2 hours</td>
                                <td class="py-3 px-4">Essential</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-mono text-sm text-primary">XSRF-TOKEN</td>
                                <td class="py-3 px-4">Security (CSRF protection)</td>
                                <td class="py-3 px-4">2 hours</td>
                                <td class="py-3 px-4">Essential</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-mono text-sm text-primary">remember_web_*</td>
                                <td class="py-3 px-4">"Remember me" login</td>
                                <td class="py-3 px-4">30 days (if selected)</td>
                                <td class="py-3 px-4">Functional</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-mono text-sm text-primary">cookie_notice_acknowledged</td>
                                <td class="py-3 px-4">Privacy notice acknowledgment</td>
                                <td class="py-3 px-4">1 year</td>
                                <td class="py-3 px-4">Essential</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Third-Party Services</h2>
                <p class="leading-relaxed mb-8">
                    We do not use analytics, advertising, or tracking pixels. Your browsing activity on this site is not shared with third parties for marketing purposes.
                </p>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Data Retention</h2>
                <ul class="space-y-3 mb-8">
                    <li><strong>Server logs:</strong> 30 days</li>
                    <li><strong>Contact form submissions:</strong> 2 years (or until you request deletion)</li>
                    <li><strong>Newsletter subscriptions:</strong> Until you unsubscribe</li>
                    <li><strong>Account data:</strong> Until you delete your account</li>
                </ul>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Your Rights (GDPR & NZ Privacy Act)</h2>
                <p class="leading-relaxed mb-4">Under the New Zealand Privacy Act 2020 and GDPR, you have the right to:</p>
                <ul class="space-y-3 mb-8">
                    <li><strong>Access:</strong> Request a copy of your personal data</li>
                    <li><strong>Correction:</strong> Request correction of inaccurate data</li>
                    <li><strong>Deletion:</strong> Request deletion of your data ("right to be forgotten")</li>
                    <li><strong>Objection:</strong> Object to processing of your data</li>
                    <li><strong>Portability:</strong> Request transfer of your data to another service</li>
                </ul>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Data Security</h2>
                <p class="leading-relaxed mb-4">We implement appropriate technical and organizational measures to protect your data:</p>
                <ul class="space-y-3 mb-8">
                    <li>HTTPS encryption for all data transmission</li>
                    <li>Password hashing using bcrypt</li>
                    <li>Database transactions for data integrity</li>
                    <li>Regular security audits</li>
                    <li>Rate limiting to prevent abuse</li>
                </ul>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Data Breaches</h2>
                <p class="leading-relaxed mb-8">
                    In the unlikely event of a data breach, we will notify affected users and the 
                    <a href="https://www.privacy.org.nz/" target="_blank" rel="noopener" class="text-primary hover:underline">Office of the Privacy Commissioner</a> 
                    as required by the Privacy Act 2020.
                </p>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Children's Privacy</h2>
                <p class="leading-relaxed mb-8">Our services are not directed to individuals under 16. We do not knowingly collect personal information from children.</p>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Changes to This Policy</h2>
                <p class="leading-relaxed mb-8">We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated "Last updated" date.</p>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Contact Us</h2>
                <p class="leading-relaxed mb-4">For privacy-related inquiries, data access requests, or to exercise your rights:</p>
                <ul class="space-y-3 mb-8">
                    <li>Email: privacy@cloudherder.nz</li>
                    <li>Contact form: <a href="{{ route('contact.show') }}" class="text-primary hover:underline">Contact Page</a></li>
                </ul>

                <h2 class="text-2xl font-headline font-bold text-on-surface mt-12 mb-6">Regulatory Compliance</h2>
                <ul class="space-y-3 mb-12">
                    <li><strong>New Zealand:</strong> Privacy Act 2020</li>
                    <li><strong>European Union:</strong> General Data Protection Regulation (GDPR)</li>
                    <li><strong>Data Location:</strong> New Zealand-based servers</li>
                </ul>
            </article>
        </div>
    </section>
</x-public-layout>
