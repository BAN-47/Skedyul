<x-mail::message>
# Registration Received

Hello **{{ $name }}**,

Thank you for registering with **SKEDYUL**.

Your account has been created successfully using **{{ $email }}**, but it is currently **pending administrator approval**.

<x-mail::panel>
What happens next?

1. A system administrator will review your submitted information and ID photo.
2. Once approved, we will send you another email with a link to continue.
3. You will then complete your personal information (Bachelors, Masters, Doctorate) before accessing your dashboard.
</x-mail::panel>

No action is needed from you right now. Please wait for the approval email.

<x-mail::subcopy>
If you did not register for a SKEDYUL account, you can safely ignore this email.
</x-mail::subcopy>

Regards,<br>
**{{ config('app.name') }}** — Faculty Scheduling System
</x-mail::message>
