@component('mail::message')
# Booking Dibatalkan

Halo {{ $booking->user->name }},

Booking Anda dengan kode **{{ $booking->booking_code }}** pada tanggal **{{ $booking->booking_date->toDateString() }}** telah dibatalkan.

**Alasan pembatalan:**
{{ $reason }}

Jika Anda membutuhkan bantuan lebih lanjut, silakan hubungi tim LapPol.

Terima kasih.

Salam,
Tim LapPol
@endcomponent
