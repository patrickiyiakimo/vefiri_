@extends('layouts.storefront')

@section('title', 'Terms and Conditions | Vefiri')
@section('description', 'Read the terms and conditions that govern your use of the Vefiri marketplace.')

@section('content')
    <x-legal-page
        title="Terms and Conditions"
        summary="The rules and conditions that govern your access to and use of the Vefiri marketplace."
    >
        <p>
            These Terms and Conditions govern your access to and use of the Vefiri marketplace, including our website, mobile
            experience, products, services and any related features. By accessing or using Vefiri, you agree to be bound
            by these terms. If you do not agree, please do not use the platform.
        </p>

        <h2>1. About Vefiri</h2>
        <p>
            Vefiri is an online marketplace that connects buyers with independent vendors and sellers. Vefiri provides
            the platform, payment infrastructure and customer support. Products are supplied by vendors, who are
            responsible for their listings, pricing, stock and compliance with applicable laws.
        </p>

        <h2>2. Eligibility</h2>
        <p>
            You must be at least 18 years old and legally capable of entering into binding contracts to use Vefiri. By
            using the platform, you confirm that the information you provide is accurate, complete and current.
        </p>

        <h2>3. Your Account</h2>
        <ul>
            <li>You are responsible for maintaining the confidentiality of your login details.</li>
            <li>You are responsible for all activity that occurs under your account.</li>
            <li>You must notify us immediately if you suspect unauthorised access to your account.</li>
            <li>We may suspend or close accounts that violate these terms, engage in fraud or misuse the platform.</li>
        </ul>

        <h2>4. Orders and Payments</h2>
        <ul>
            <li>An order is an offer to purchase a product from a vendor. Acceptance occurs when the vendor confirms the order or the order is marked as processing.</li>
            <li>Prices, availability and delivery estimates are displayed on the product page and may change without notice.</li>
            <li>You agree to pay the total amount shown at checkout, including any applicable delivery or service fees.</li>
            <li>Payments are processed by licensed payment providers. Vefiri does not store your full card details.</li>
            <li>We may cancel or refuse an order where there is a pricing error, suspected fraud, stock unavailability or a legal requirement.</li>
        </ul>

        <h2>5. Delivery</h2>
        <p>
            Delivery times are estimates and depend on the vendor, destination and delivery partner. Risk in the
            products passes to you on delivery. If an order is delayed, damaged or missing, contact us within the
            period stated in our <a href="{{ route('pages.returns') }}">Returns & Refunds Policy</a> so we can
            investigate.
        </p>

        <h2>6. Returns and Refunds</h2>
        <p>
            Returns and refunds are governed by our Returns & Refunds Policy, which forms part of these terms. Please
            review that policy before placing an order.
        </p>

        <h2>7. Vendor Responsibilities</h2>
        <ul>
            <li>Vendors must provide accurate product descriptions, images, prices and stock information.</li>
            <li>Vendors must only list products they are legally allowed to sell.</li>
            <li>Vendors are responsible for fulfilling orders, complying with tax obligations and handling product warranties.</li>
            <li>Vendors must not list counterfeit, stolen, unsafe, prohibited or misleading products.</li>
            <li>Vefiri may remove listings, suspend payouts or close vendor accounts that breach these terms.</li>
        </ul>

        <h2>8. Acceptable Use</h2>
        <p>You agree not to:</p>
        <ul>
            <li>Use Vefiri for unlawful, fraudulent or harmful purposes.</li>
            <li>Infringe the intellectual property or privacy rights of others.</li>
            <li>Post false, misleading, abusive or offensive content.</li>
            <li>Attempt to gain unauthorised access to our systems, accounts or data.</li>
            <li>Interfere with the operation of the platform or use automated tools that place excessive load on it.</li>
            <li>Circumvent our payment process or transact off-platform to avoid fees or buyer protection.</li>
        </ul>

        <h2>9. Intellectual Property</h2>
        <p>
            The Vefiri name, logo, website design, text, graphics and software are owned by Vefiri or its licensors and
            are protected by intellectual property laws. You may not copy, reproduce or use them without our written
            permission. Vendors retain ownership of their product content but grant Vefiri a licence to display and
            promote it on the platform.
        </p>

        <h2>10. Third-Party Links</h2>
        <p>
            Vefiri may contain links to third-party websites or services. We do not control and are not responsible for
            their content, policies or practices. Accessing third-party sites is at your own risk.
        </p>

        <h2>11. Disclaimers</h2>
        <p>
            To the fullest extent permitted by law, Vefiri is provided on an "as is" and "as available" basis. We do not
            guarantee that the platform will be uninterrupted, error-free or that all product information is accurate,
            complete or current. Nothing in these terms excludes liability that cannot lawfully be excluded.
        </p>

        <h2>12. Limitation of Liability</h2>
        <p>
            To the fullest extent permitted by law, Vefiri is not liable for indirect, incidental, special,
            consequential or punitive damages, loss of profits, loss of data or business interruption arising from your
            use of the platform. Our total liability for any claim relating to an order will not exceed the amount you
            paid for that order.
        </p>

        <h2>13. Indemnity</h2>
        <p>
            You agree to indemnify and hold harmless Vefiri, its officers, employees and partners from claims, losses
            or expenses arising from your breach of these terms, your misuse of the platform or your violation of any
            law or third-party right.
        </p>

        <h2>14. Suspension and Termination</h2>
        <p>
            We may suspend or terminate your access to Vefiri at any time if we reasonably believe you have breached
            these terms or pose a risk to other users, vendors or the platform. You may stop using Vefiri at any time.
            Provisions that by their nature should survive termination will continue to apply.
        </p>

        <h2>15. Governing Law</h2>
        <p>
            These terms are governed by the laws of the Federal Republic of Nigeria. Disputes will be subject to the
            exclusive jurisdiction of the courts of Nigeria, unless applicable consumer law provides otherwise.
        </p>

        <h2>16. Changes to These Terms</h2>
        <p>
            We may update these Terms and Conditions from time to time. We will post the revised terms on this page and update
            the "Last Updated" date. Your continued use of Vefiri after changes take effect means you accept the
            revised terms.
        </p>

        <h2>17. Contact Us</h2>
        <div class="legal-note">
            <p><strong>Email:</strong> legal@vefiri.com</p>
            <p><strong>Phone:</strong> +234 800 000 0000</p>
            <p><strong>Address:</strong> Lagos, Nigeria</p>
        </div>
    </x-legal-page>
@endsection
