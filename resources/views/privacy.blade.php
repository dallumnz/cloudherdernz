<x-layouts::public title="Privacy Policy">
    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <article class="prose lg:prose-xl">
            <h1>Privacy Policy</h1>
            <p class="text-slate-600">Last updated: March 17, 2026</p>

            <h2>Overview</h2>
            <p>
                CloudHerder.nz ("we", "us", "our") is committed to protecting your privacy. 
                This Privacy Policy explains how we collect, use, disclose, and safeguard your 
                information in compliance with the <strong>New Zealand Privacy Act 2020</strong> 
                and the <strong>EU General Data Protection Regulation (GDPR)</strong> where applicable.
            </p>

            <h2>Information We Collect</h2>
            
            <h3>Essential Data (Automatically Collected)</h3>
            <ul>
                <li><strong>Session cookies</strong> — Required for login functionality and form submissions (CSRF protection)</li>
                <li><strong>Server logs</strong> — IP addresses, browser type, pages visited (retained for 30 days for security)</li>
                <li><strong>Contact form submissions</strong> — Name, email, message content (only when you submit)</li>
            </ul>

            <h3>Account Data (When You Register)</h3>
            <ul>
                <li>Name and email address</li>
                <li>Encrypted password (we cannot read this)</li>
                <li>Optional: Two-factor authentication settings</li>
            </ul>

            <h3>Newsletter Data</h3>
            <ul>
                <li>Email address (only with explicit opt-in)</li>
                <li>Subscription preferences</li>
                <li>Confirmation timestamps (double opt-in)</li>
            </ul>

            <h2>How We Use Your Information</h2>
            <ul>
                <li>To provide and maintain the website functionality</li>
                <li>To respond to contact form submissions</li>
                <li>To send newsletters (only to confirmed subscribers)</li>
                <li>To detect and prevent security incidents</li>
                <li>To comply with legal obligations</li>
            </ul>

            <h2>Cookies We Use</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Cookie Name</th>
                        <th class="text-left py-2">Purpose</th>
                        <th class="text-left py-2">Duration</th>
                        <th class="text-left py-2">Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-2 font-mono">laravel_session</td>
                        <td>Session management</td>
                        <td>2 hours</td>
                        <td>Essential</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 font-mono">XSRF-TOKEN</td>
                        <td>Security (CSRF protection)</td>
                        <td>2 hours</td>
                        <td>Essential</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 font-mono">remember_web_*</td>
                        <td>"Remember me" login</td>
                        <td>30 days (if selected)</td>
                        <td>Functional</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 font-mono">cookie_notice_acknowledged</td>
                        <td>Privacy notice acknowledgment</td>
                        <td>1 year</td>
                        <td>Essential</td>
                    </tr>
                </tbody>
            </table>

            <h2>Third-Party Services</h2>
            <p>We do not use analytics, advertising, or tracking pixels. Your browsing activity on this site is not shared with third parties for marketing purposes.</p>

            <h2>Data Retention</h2>
            <ul>
                <li><strong>Server logs:</strong> 30 days</li>
                <li><strong>Contact form submissions:</strong> 2 years (or until you request deletion)</li>
                <li><strong>Newsletter subscriptions:</strong> Until you unsubscribe</li>
                <li><strong>Account data:</strong> Until you delete your account</li>
            </ul>

            <h2>Your Rights (GDPR & NZ Privacy Act)</h2>
            <p>Under the New Zealand Privacy Act 2020 and GDPR, you have the right to:</p>
            <ul>
                <li><strong>Access:</strong> Request a copy of your personal data</li>
                <li><strong>Correction:</strong> Request correction of inaccurate data</li>
                <li><strong>Deletion:</strong> Request deletion of your data ("right to be forgotten")</li>
                <li><strong>Objection:</strong> Object to processing of your data</li>
                <li><strong>Portability:</strong> Request transfer of your data to another service</li>
            </ul>

            <h2>Data Security</h2>
            <p>We implement appropriate technical and organizational measures to protect your data:</p>
            <ul>
                <li>HTTPS encryption for all data transmission</li>
                <li>Password hashing using bcrypt</li>
                <li>Database transactions for data integrity</li>
                <li>Regular security audits</li>
                <li>Rate limiting to prevent abuse</li>
            </ul>

            <h2>Data Breaches</h2>
            <p>
                In the unlikely event of a data breach, we will notify affected users and the 
                <a href="https://www.privacy.org.nz/" target="_blank" rel="noopener" class="underline">Office of the Privacy Commissioner</a> 
                as required by the Privacy Act 2020.
            </p>

            <h2>Children's Privacy</h2>
            <p>Our services are not directed to individuals under 16. We do not knowingly collect personal information from children.</p>

            <h2>Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated "Last updated" date.</p>

            <h2>Contact Us</h2>
            <p>
                For privacy-related inquiries, data access requests, or to exercise your rights:
            </p>
            <ul>
                <li>Email: privacy@cloudherder.nz</li>
                <li>Contact form: <a href="{{ route('contact.show') }}" class="underline">Contact Page</a></li>
            </ul>

            <h2>Regulatory Compliance</h2>
            <ul>
                <li><strong>New Zealand:</strong> Privacy Act 2020</li>
                <li><strong>European Union:</strong> General Data Protection Regulation (GDPR)</li>
                <li><strong>Data Location:</strong> New Zealand-based servers</li>
            </ul>
        </article>
    </div>
</x-layouts::public>
