@component('mail::message')
# Hasil Verifikasi Waban

@if ($approved)
Selamat! Verifikasi kampus Anda telah disetujui.

Anda sekarang dapat menikmati harga khusus dan fasilitas untuk pengguna kampus.
@else
Maaf, verifikasi kampus Anda ditolak.

**Alasan:** {{ $reason ?? 'Tidak tersedia' }}

Silakan periksa kembali data dan ajukan ulang jika diperlukan.
@endif

Terima kasih telah menggunakan LapPol.

Salam,
Tim LapPol
@endcomponent
