@component('mail::message')
# Pembayaran Gagal

Halo {{ $booking->user->name }},

Pembayaran untuk booking Anda dengan kode **{{ $booking->booking_code }}** pada venue **{{ $booking->venue->name }}** gagal.

Silakan periksa metode pembayaran Anda dan coba kembali.

Jika Anda memerlukan bantuan, hubungi tim LapPol.

Salam,
Tim LapPol
@endcomponent
