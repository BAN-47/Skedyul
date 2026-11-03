<x-mail::message>
# Account Approved

Hello **{{ $name }}**,

Your SKEDYUL faculty account has been approved successfully.

Before you can access your dashboard, complete your required education information: Bachelor's degree (required), Master's and Doctorate degrees (optional).

<x-mail::button :url="$continuationUrl">
Continue to Complete Profile
</x-mail::button>

This link opens your required profile form. After saving your education information, you can sign in and access your dashboard.

If you did not register for SKEDYUL, please contact the administrator.

Regards,<br>
**{{ config('app.name') }}** — Faculty Scheduling System
</x-mail::message>
