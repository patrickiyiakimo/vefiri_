@extends('layouts.storefront')

@section('title', 'Returns & Refunds | Vefiri')
@section('description', 'Learn how to create a return, what items are eligible and when to expect your refund on Vefiri.')

@section('content')
    <x-legal-page
        title="Returns & Refunds"
        summary="How to return an item, what is eligible and when to expect your refund."
    >
        <p>
            We want you to shop with confidence on Vefiri. If something is not right with your order, you may be able
            to return it for a refund or replacement under the conditions below. This policy applies to products bought
            from vendors on Vefiri.
        </p>

        <div class="legal-note">
            <p><strong>Our promise:</strong> If you receive a damaged, defective, counterfeit, incomplete or wrong item,
            report it within the return window and we will help resolve it. Change-of-mind returns are accepted for
            eligible unused items.</p>
        </div>

        <h2>1. Return Windows</h2>
        <ul>
            <li><strong>Marketplace items:</strong> return within <strong>7 days</strong> of delivery.</li>
            <li><strong>Vefiri Choice and eligible verified-store items:</strong> return within <strong>15 days</strong> of delivery where the listing allows it.</li>
            <li><strong>Damaged or wrong items:</strong> report within <strong>48 hours</strong> of delivery so we can investigate quickly.</li>
            <li><strong>Counterfeit items:</strong> report as soon as you discover the issue; we may request photos or additional evidence.</li>
        </ul>
        <p>The return window starts on the delivery date recorded by our delivery partner.</p>

        <h2>2. Eligible Return Reasons</h2>
        <ul>
            <li>You received the wrong item, size or colour.</li>
            <li>The item arrived damaged, broken or defective.</li>
            <li>The item is counterfeit or materially different from the listing.</li>
            <li>Parts, accessories or free gifts included in the listing are missing.</li>
            <li>The item does not fit and you want a different size, where the listing allows returns.</li>
            <li>You changed your mind and the item is unused, unopened and in resalable condition.</li>
        </ul>

        <h2>3. Items That Cannot Be Returned</h2>
        <p>For health, safety and hygiene reasons, some items are not eligible for change-of-mind returns:</p>
        <ul>
            <li>Innerwear, swimwear, lingerie and personal care items that have been opened or used.</li>
            <li>Opened cosmetics, skincare, perfumes and fragrances.</li>
            <li>Perishable goods, food, beverages and fresh produce.</li>
            <li>Personalised, custom-made or made-to-order items.</li>
            <li>Digital products, gift cards, vouchers and software licences.</li>
            <li>Hazardous materials, flammable liquids and restricted products.</li>
            <li>Items that have been used, installed, altered or damaged after delivery.</li>
            <li>Items missing serial numbers, security seals, original packaging or accessories.</li>
        </ul>
        <p>
            Items that arrive damaged, defective, counterfeit or materially different from the listing are still
            eligible for review even if they fall into one of the categories above.
        </p>

        <h2>4. Return Conditions</h2>
        <ul>
            <li>The item must be unused and in the same condition you received it, unless it arrived damaged or defective.</li>
            <li>Include all original packaging, tags, manuals, accessories, free gifts and promotional items.</li>
            <li>Do not attach labels or tape directly to the product packaging.</li>
            <li>Keep proof of delivery and any photos or videos that show the issue.</li>
            <li>Remove or mask personal information before returning a device or storage product.</li>
        </ul>
        <p>We may decline a return or reduce a refund if these conditions are not met.</p>

        <h2>5. How to Request a Return</h2>
        <ol>
            <li>Sign in to your Vefiri account and open <strong>My Orders</strong>.</li>
            <li>Select the order and the item you want to return.</li>
            <li>Choose <strong>Request return</strong> and select the reason that best describes the issue.</li>
            <li>Add a short explanation and upload clear photos or a video where relevant.</li>
            <li>Submit the request and follow the instructions in your confirmation email or account.</li>
        </ol>
        <p>
            If you cannot access your account, contact our support team with your order number and the email address
            used at checkout.
        </p>

        <h2>6. Pickup and Drop-Off</h2>
        <p>
            Depending on your location and the vendor, we may arrange a pickup or ask you to drop the item at a
            designated return point. Return instructions and any applicable timelines will be shared when your request
            is approved. Returns are usually collected within 2-5 business days.
        </p>
        <p>
            Please keep the item securely packaged until it is collected. If you miss a scheduled pickup, we may need to
            arrange another one.
        </p>

        <h2>7. Inspection and Approval</h2>
        <p>
            Once we receive the item, it is inspected to confirm that it matches the return reason and meets the return
            conditions. Inspection usually takes 1-3 business days after the item arrives at our facility or the
            vendor. We will notify you if we need more information or if the return is approved or declined.
        </p>

        <h2>8. Refund Timelines</h2>
        <p>Approved refunds are processed to your original payment method where possible:</p>
        <ul>
            <li><strong>Vefiri wallet or store credit:</strong> usually within 24-48 hours after approval.</li>
            <li><strong>Bank transfer:</strong> usually within 3-7 business days after approval.</li>
            <li><strong>Debit or credit card:</strong> usually within 3-15 business days after approval, depending on your bank.</li>
        </ul>
        <p>
            Delivery fees are refunded only where the return is due to an error by Vefiri or the vendor, or where the
            item arrived damaged, defective or counterfeit. Original delivery fees are not refunded for change-of-mind
            returns.
        </p>

        <h2>9. Replacements and Exchanges</h2>
        <p>
            We do not offer direct exchanges at this time. If you need a different size, colour or product, return the
            eligible item for a refund and place a new order. If a replacement is available, our support team will
            explain the options after your return is approved.
        </p>

        <h2>10. Damaged, Wrong or Missing Items</h2>
        <p>
            If your order arrives damaged, incomplete or different from what you ordered, report it within 48 hours of
            delivery. Include photos of the item, packaging and delivery label where possible. Where an item is missing
            from a multi-item order, tell us which item is missing so we can check with the vendor and delivery partner.
        </p>

        <h2>11. Counterfeit Items</h2>
        <p>
            Vefiri does not permit counterfeit or unauthorised products. If you believe you received a counterfeit
            item, contact us with your order number, photos and any supporting evidence. We may request the item for
            inspection. Confirmed counterfeit items are eligible for a refund and the vendor may be suspended.
        </p>

        <h2>12. Disputes</h2>
        <p>
            If you and a vendor cannot agree on a return, contact Vefiri support. We will review the order, the listing,
            the evidence provided and any communication between you and the vendor. Our decision will be made in good
            faith and in line with this policy and applicable consumer law.
        </p>

        <h2>13. Contact Us</h2>
        <p>
            Need help with a return or refund? Contact our support team with your order number and the details of the
            issue.
        </p>
        <div class="legal-note">
            <p><strong>Email:</strong> support@vefiri.com</p>
            <p><strong>Phone:</strong> +234 800 000 0000</p>
            <p><strong>Address:</strong> Lagos, Nigeria</p>
        </div>

        <p class="text-xs text-muted">
            This policy should be read together with our
            <a href="{{ route('pages.terms') }}">Terms and Conditions</a> and
            <a href="{{ route('pages.privacy') }}">Privacy Policy</a>.
        </p>
    </x-legal-page>
@endsection
