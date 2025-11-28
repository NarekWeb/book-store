@component('mail::message')
    # Rental overdue

    Hello {{ $user->name ?? 'reader' }},

    Your rental of the book **{{ $book->title ?? '' }}** is overdue.

    Due date: {{ $rental->due_date?->format('Y-m-d H:i') }}

    Please return the book as soon as possible.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
