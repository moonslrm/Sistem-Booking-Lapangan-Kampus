@component('mail::message')
# Booking Dikonfirmasi 🎉

Halo {{ $booking->user->name }},

Booking Anda dengan kode **{{ $booking->booking_code }}** telah berhasil dikonfirmasi.

**Detail Booking:**
- Venue: {{ $booking->venue->name }}
- Tanggal: {{ $booking->booking_date->toDateString() }}
- Waktu: {{ $booking->start_time }} - {{ $booking->end_time }}
- Total: Rp{{ number_format($booking->final_price, 0, ',', '.') }}

@component('mail::panel')
Silakan siapkan diri untuk check-in di lokasi paling lambat 15 menit sebelum waktu mulai.
@endcomponent

**Instruksi Check-in:**
1. Tunjukkan QR code booking Anda.
2. Login dengan akun yang sama saat memesan.
3. Jaga kesehatan dan patuhi peraturan venue.

Terima kasih telah menggunakan LapPol.

Salam,
Tim LapPol
@endcomponent
