@component('mail::message')
# Pengingat Booking

Halo {{ $booking->user->name }},

Booking Anda dengan kode **{{ $booking->booking_code }}** akan segera dimulai.

**Detail Booking:**
- Venue: {{ $booking->venue->name }}
- Tanggal: {{ $booking->booking_date->toDateString() }}
- Waktu: {{ $booking->start_time }} - {{ $booking->end_time }}

@if ($reminderType === 'h1')
Ini adalah pengingat 24 jam sebelum acara dimulai.
@else
Ini adalah pengingat 1 jam sebelum acara dimulai. Pastikan Anda sudah bersiap.
@endif

Terima kasih telah menggunakan LapPol.

Salam,
Tim LapPol
@endcomponent
