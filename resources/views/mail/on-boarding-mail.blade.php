<x-mail.layout :title="'Welcome'">
    <h2 style="color:#1f2933; margin-top:0; text-transform: capitalize">Welcome, {{ $name }}!</h2>
    <p style="color:#52606d; font-size:14px; margin-top:-6px;">Account created for {{ $email }}</p>

    <p>Thank you for creating an account with {{ config('app.name') }}. We're glad to have you with us, and your account
        is now fully set up and ready to use.</p>

    <p>Here's a quick overview of what you can do next:</p>

    <ul style="padding-left:20px; margin:16px 0; color:#1f2933;">
        <li style="margin-bottom:8px;">Browse our latest products and collections</li>
        <li style="margin-bottom:8px;">Save items to your wishlist for later</li>
        <li style="margin-bottom:8px;">Track your orders from your account page</li>
    </ul>

    <p style="text-align:center; margin:24px 0;">
        <a href="/"
           style="background-color:#0f766e; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:4px; display:inline-block; font-weight:bold;">
            Start Shopping
        </a>
    </p>

    <p>If you have any questions or need help getting started, our support team is available at <a
            href="mailto:{{ config('mail.from.address') }}"
            style="color:#0f766e;">{{ config('mail.from.address') }}</a> and happy to assist.</p>

    <p>If you didn't create this account, you can safely ignore this email or contact us to report it.</p>

    <p style="margin-bottom:0;">Best regards,<br>The {{ config('app.name') }} Team</p>
</x-mail.layout>
