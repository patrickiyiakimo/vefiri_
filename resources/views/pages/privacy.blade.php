@extends('layouts.storefront')

@section('title', 'Privacy Policy | Vefiri')
@section('description', 'Learn how Vefiri collects, uses, shares and protects your personal information.')

@section('content')
    <x-legal-page
        title="Privacy Policy"
        summary="How Vefiri collects, uses, shares and protects your personal information when you shop, sell or interact with our platform."
    >
        <p>
            Vefiri ("we", "us", "our") is committed to protecting your privacy. This Privacy Policy explains how we
            collect, use, disclose and safeguard your information when you use our website, mobile experience and
            related services. By using Vefiri, you agree to the practices described in this policy.
        </p>

        <h2>1. Information We Collect</h2>

        <h3>Information you provide directly</h3>
        <ul>
            <li><strong>Account information:</strong> your name, email address, phone number and password when you create an account or sign in.</li>
            <li><strong>Order and delivery information:</strong> delivery address, recipient details, order history and communication about your orders.</li>
            <li><strong>Payment information:</strong> the payment method you choose and transaction references. We do not store your full card number or card security code on our servers.</li>
            <li><strong>Vendor information:</strong> business name, store name, business email, phone number, address, registration details and the products you upload if you apply to sell on Vefiri.</li>
            <li><strong>Communications:</strong> messages you send to our support team, reviews, feedback and survey responses.</li>
        </ul>

        <h3>Information collected automatically</h3>
        <ul>
            <li><strong>Device and usage data:</strong> Internet Protocol (IP) address, browser type, device type, operating system, pages viewed and the dates and times you access Vefiri.</li>
            <li><strong>Cookies and similar technologies:</strong> small files that help us keep you signed in, remember preferences, measure site performance and understand how shoppers use our storefront.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>Create and manage your account and provide customer support.</li>
            <li>Process orders, arrange delivery and communicate order updates.</li>
            <li>Process payments and prevent fraudulent transactions.</li>
            <li>Review vendor applications and operate vendor storefronts.</li>
            <li>Improve our products, services, website performance and shopping experience.</li>
            <li>Send service messages and, where you have agreed, marketing communications.</li>
            <li>Comply with legal, tax, regulatory and security obligations.</li>
            <li>Enforce our Terms and Conditions and protect the rights, safety and security of Vefiri, our users and the public.</li>
        </ul>

        <h2>3. How We Share Your Information</h2>
        <p>We do not sell your personal information. We may share it with:</p>
        <ul>
            <li><strong>Vendors and sellers:</strong> the information needed to fulfil and deliver your order, such as your name, delivery address and order details.</li>
            <li><strong>Delivery partners:</strong> logistics providers that collect and deliver your orders.</li>
            <li><strong>Payment providers:</strong> banks, card networks and payment processors that complete your transaction.</li>
            <li><strong>Service providers:</strong> trusted companies that support hosting, analytics, communications, fraud prevention and customer support.</li>
            <li><strong>Legal and regulatory authorities:</strong> where required by law, court order or to protect our legal rights.</li>
            <li><strong>Business transfers:</strong> in connection with a merger, acquisition, financing or sale of assets, subject to appropriate confidentiality protections.</li>
        </ul>

        <h2>4. Payments</h2>
        <p>
            Payments on Vefiri are processed by licensed payment providers. Payment card details are submitted directly
            to our payment processors and are not stored on Vefiri servers. We retain transaction references and
            payment status so we can manage orders, refunds and disputes.
        </p>

        <h2>5. Data Security</h2>
        <p>
            We use reasonable technical and organisational measures to protect your information, including encrypted
            connections, access controls and secure password storage. No method of transmission or storage is completely
            secure, so we cannot guarantee absolute security. Please keep your password confidential and contact us
            immediately if you believe your account has been compromised.
        </p>

        <h2>6. Your Privacy Rights</h2>
        <p>Subject to applicable law, you may have the right to:</p>
        <ul>
            <li>Access the personal information we hold about you.</li>
            <li>Request correction of inaccurate or incomplete information.</li>
            <li>Request deletion of your information, where we are not required to retain it.</li>
            <li>Object to or request restriction of certain processing.</li>
            <li>Withdraw consent for marketing communications at any time.</li>
            <li>Request a portable copy of information you provided to us.</li>
            <li>Lodge a complaint with a relevant data protection authority.</li>
        </ul>
        <p>
            To exercise these rights, email <a href="mailto:privacy@vefiri.com">privacy@vefiri.com</a>. We may need to
            verify your identity before completing your request.
        </p>

        <h2>7. Data Retention</h2>
        <p>
            We retain personal information for as long as necessary to provide our services, fulfil the purposes
            described in this policy, resolve disputes, enforce agreements and meet legal, tax and regulatory
            requirements. When information is no longer needed, we delete it or anonymise it.
        </p>

        <h2>8. Cookies and Tracking</h2>
        <p>
            We use essential cookies to operate the storefront, remember your cart, keep you signed in and protect
            against fraud. We may also use analytics and advertising cookies to understand how our services are used and
            improve your experience. You can manage cookies through your browser settings. Disabling essential cookies
            may affect how Vefiri works.
        </p>

        <h2>9. Children's Privacy</h2>
        <p>
            Vefiri is not intended for children under 18. We do not knowingly collect personal information from
            children. If you believe a child has provided us with personal information, contact us and we will take
            appropriate steps to delete it.
        </p>

        <h2>10. Changes to This Privacy Policy</h2>
        <p>
            We may update this Privacy Policy from time to time. When we make material changes, we will post the updated
            policy on this page and update the "Last Updated" date. Your continued use of Vefiri after an update means
            you accept the revised policy.
        </p>

        <h2>11. Contact Us</h2>
        <div class="legal-note">
            <p><strong>Email:</strong> privacy@vefiri.com</p>
            <p><strong>Phone:</strong> +234 800 000 0000</p>
            <p><strong>Address:</strong> Lagos, Nigeria</p>
        </div>
    </x-legal-page>
@endsection
