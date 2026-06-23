# Product Requirements Document (PRD)
# Sistem Booking Lapangan Olahraga Kampus (CSBS)
### Campus Sports Booking System

---

**Versi Dokumen:** 1.0.0  
**Tanggal:** Juni 2025  
**Status:** Draft — Siap Review  
**Penulis:** Tim Product & Engineering  
**Klasifikasi:** Internal — Confidential  

---

## Daftar Isi

1. [Executive Summary](#1-executive-summary)
2. [Product Overview](#2-product-overview)
3. [Problem Statement](#3-problem-statement)
4. [Product Goals](#4-product-goals)
5. [Business Goals](#5-business-goals)
6. [Target Users](#6-target-users)
7. [User Persona](#7-user-persona)
8. [Stakeholder Identification](#8-stakeholder-identification)
9. [Functional Requirements](#9-functional-requirements)
10. [Non-Functional Requirements](#10-non-functional-requirements)
11. [User Roles & Permissions](#11-user-roles--permissions)
12. [User Journey](#12-user-journey)
13. [Use Case Scenario](#13-use-case-scenario)
14. [Core Features](#14-core-features)
15. [MVP Features](#15-mvp-features)
16. [Future Features / Roadmap](#16-future-features--roadmap)
17. [System Architecture Overview](#17-system-architecture-overview)
18. [Technology Stack](#18-technology-stack)
19. [API Architecture](#19-api-architecture)
20. [Database Design Overview](#20-database-design-overview)
21. [ERD Description](#21-erd-description)
22. [UI/UX Direction](#22-uiux-direction)
23. [Mobile App Flow](#23-mobile-app-flow)
24. [Web Admin Flow](#24-web-admin-flow)
25. [Authentication & Authorization Flow](#25-authentication--authorization-flow)
26. [Booking Flow](#26-booking-flow)
27. [Payment Flow](#27-payment-flow)
28. [QR Validation Flow](#28-qr-validation-flow)
29. [Notification Flow](#29-notification-flow)
30. [Voucher & Promo System](#30-voucher--promo-system)
31. [Real-Time Availability System](#31-real-time-availability-system)
32. [Dashboard & Analytics](#32-dashboard--analytics)
33. [Security Requirements](#33-security-requirements)
34. [Performance Requirements](#34-performance-requirements)
35. [Scalability Requirements](#35-scalability-requirements)
36. [Logging & Monitoring](#36-logging--monitoring)
37. [Backup & Recovery](#37-backup--recovery)
38. [Risks & Challenges](#38-risks--challenges)
39. [Development Milestones](#39-development-milestones)
40. [Sprint Planning Recommendation](#40-sprint-planning-recommendation)
41. [Suggested Folder Structure Laravel](#41-suggested-folder-structure-laravel)
42. [Suggested Folder Structure Flutter](#42-suggested-folder-structure-flutter)
43. [Suggested REST API Endpoint List](#43-suggested-rest-api-endpoint-list)
44. [Suggested Database Tables](#44-suggested-database-tables)
45. [Suggested Admin Panel Features](#45-suggested-admin-panel-features)
46. [Suggested Mobile Features](#46-suggested-mobile-features)
47. [Suggested Laravel Packages](#47-suggested-laravel-packages)
48. [Suggested Flutter Packages](#48-suggested-flutter-packages)
49. [Deployment Recommendation](#49-deployment-recommendation)
50. [Testing Strategy](#50-testing-strategy)
51. [CI/CD Recommendation](#51-cicd-recommendation)
52. [Conclusion](#52-conclusion)

---

## 1. Executive Summary

**Campus Sports Booking System (CSBS)** adalah platform digital terintegrasi yang dirancang untuk mengotomatisasi dan menyederhanakan proses pemesanan fasilitas lapangan olahraga di lingkungan kampus. Sistem ini menjembatani kebutuhan dua segmen pengguna utama — **Waban (Warga Kampus)** dan **Masyarakat Umum** — dengan menyediakan alur pemesanan yang transparan, real-time, dan berbasis mobile-first.

Dengan arsitektur modern berbasis **Laravel 13** (backend/web admin) dan **Flutter** (aplikasi mobile cross-platform), sistem ini mengimplementasikan:

- **Diferensiasi harga otomatis** berdasarkan status keanggotaan kampus yang telah diverifikasi
- **Validasi kedatangan digital** via QR/Barcode scanning oleh Koordinator Lapangan (Koorlap)
- **Sistem notifikasi proaktif** untuk pengingat jadwal dan status booking
- **Manajemen voucher promo** yang fleksibel
- **Dashboard analitik real-time** untuk pengambilan keputusan manajemen

CSBS bertujuan meningkatkan utilitas fasilitas olahraga kampus, mengurangi konflik jadwal manual, meningkatkan pendapatan institusi dari pengelolaan fasilitas, dan memberikan pengalaman pengguna yang modern dan efisien.

---

## 2. Product Overview

| Atribut | Detail |
|---|---|
| **Nama Produk** | Campus Sports Booking System (CSBS) |
| **Versi** | 1.0 (MVP) |
| **Platform** | Web (Admin/Koorlap), Mobile Android & iOS (Waban & Umum) |
| **Backend** | Laravel 13 + REST API |
| **Frontend Web** | Laravel Blade + Alpine.js + Tailwind CSS |
| **Mobile** | Flutter (Dart) |
| **Database** | MySQL 8.0+ |
| **Target Launch** | Q3 2025 |
| **Bahasa Sistem** | Indonesia (ID) |
| **Zona Waktu** | Asia/Makassar (WITA) |

### Deskripsi Produk

CSBS adalah aplikasi booking lapangan olahraga kampus yang memungkinkan pengguna untuk:
1. Melihat ketersediaan lapangan secara real-time
2. Melakukan pemesanan slot waktu lapangan
3. Membayar secara digital melalui payment gateway terintegrasi
4. Mendapatkan konfirmasi booking berupa QR Code
5. Memperoleh pengingat otomatis sebelum jadwal bermain

Sistem ini juga menyediakan panel administrasi untuk pengelolaan lapangan, pengguna, harga, voucher, dan laporan.

---

## 3. Problem Statement

### Permasalahan Saat Ini

Pengelolaan fasilitas olahraga kampus umumnya masih dilakukan secara manual dengan berbagai keterbatasan:

| # | Masalah | Dampak |
|---|---|---|
| P-01 | Pemesanan melalui pesan WhatsApp atau datang langsung | Tidak efisien, rawan konflik jadwal |
| P-02 | Tidak ada sistem pencatatan terpusat | Data mudah hilang, tidak ada riwayat |
| P-03 | Tidak ada diferensiasi harga otomatis untuk warga kampus | Proses manual, rawan kesalahan dan kecurangan |
| P-04 | Tidak ada validasi kehadiran digital | Lapangan sering digunakan tanpa izin |
| P-05 | Tidak ada notifikasi pengingat | Pembatalan mendadak dan no-show tinggi |
| P-06 | Laporan pendapatan tidak real-time | Pengambilan keputusan manajemen lambat |
| P-07 | Masyarakat umum sulit mengetahui ketersediaan lapangan | Utilisasi lapangan tidak optimal |
| P-08 | Tidak ada sistem voucher/promo terstruktur | Peluang promosi terlewat |

### Root Cause Analysis

```
Ketergantungan pada proses manual
        │
        ├── Tidak ada sistem digital booking
        ├── Tidak ada integrasi data pengguna kampus
        └── Tidak ada infrastruktur notifikasi & monitoring
```

---

## 4. Product Goals

### Tujuan Produk (SMART Goals)

| ID | Goal | Metric | Target | Timeframe |
|---|---|---|---|---|
| G-01 | Digitalisasi proses booking | % booking via platform | ≥ 90% | 6 bulan pasca launch |
| G-02 | Eliminasi konflik jadwal | Jumlah konflik jadwal per bulan | 0 | Sejak launch |
| G-03 | Peningkatan utilisasi lapangan | % occupancy rate lapangan | +40% dari baseline | 3 bulan pasca launch |
| G-04 | Penurunan no-show rate | % no-show bookings | < 5% | 6 bulan pasca launch |
| G-05 | Kepuasan pengguna tinggi | App Store rating | ≥ 4.5/5 | 6 bulan pasca launch |
| G-06 | Adopsi fitur QR validasi | % sesi dengan QR scan | ≥ 85% | 3 bulan pasca launch |

---

## 5. Business Goals

### Tujuan Bisnis

| ID | Goal Bisnis | Indikator Keberhasilan |
|---|---|---|
| BG-01 | Meningkatkan pendapatan fasilitas olahraga | Revenue bulanan naik ≥ 30% dari kondisi saat ini |
| BG-02 | Mengurangi biaya operasional pengelolaan manual | Pengurangan jam kerja staf administrasi ≥ 50% |
| BG-03 | Meningkatkan brand kampus sebagai smart campus | Publikasi & pengakuan minimal 2 media regional |
| BG-04 | Membuka jalur pendapatan baru via promo & mitra | Pendapatan voucher/sponsorship ≥ 10% total revenue |
| BG-05 | Data-driven decision making untuk manajemen fasilitas | Laporan analitik mingguan tersedia otomatis |

### Revenue Model

```
┌─────────────────────────────────────────────────────┐
│              REVENUE STREAMS CSBS                   │
├─────────────────┬───────────────────────────────────┤
│ Primary         │ Biaya sewa lapangan (tarif normal) │
│ Secondary       │ Biaya sewa dengan markup layanan   │
│ Tertiary        │ Biaya penerbitan voucher promo     │
│ Future          │ Iklan & sponsorship fitur premium  │
└─────────────────┴───────────────────────────────────┘
```

---

## 6. Target Users

### Segmentasi Pengguna

```
                    CSBS USER ECOSYSTEM
                           │
          ┌────────────────┼────────────────┐
          │                │                │
       WABAN            UMUM            INTERNAL
  (Warga Kampus)   (Masyarakat)      (Staf Sistem)
          │                │                │
   ┌──────┴───┐      ┌─────┴────┐    ┌──────┴───────┐
   │Mahasiswa │      │Komunitas │    │   Koorlap    │
   │  Dosen   │      │Perorangan│    │   Admin      │
   │  Staf    │      │ Keluarga │    └──────────────┘
   └──────────┘      └──────────┘
```

### Profil Pengguna

| Segmen | Karakteristik | Akses Harga |
|---|---|---|
| **Mahasiswa** | Terdaftar aktif, memiliki NIM valid | Diskon waban (cth: 30-50%) |
| **Dosen** | Memiliki NIDN/NIDK, staf akademik | Diskon waban |
| **Staf Kampus** | Karyawan tetap/kontrak kampus | Diskon waban |
| **Masyarakat Umum** | Non-civitas akademika | Tarif normal |
| **Koorlap** | Petugas pengelola lapangan | Akses scan & monitoring |
| **Admin** | Pengelola sistem | Akses penuh |

---

## 7. User Persona

### Persona 1: Mahasiswa Aktif (Waban)
```
┌─────────────────────────────────────────────┐
│  👤 RIZKY PRATAMA                           │
│  Mahasiswa Teknik Informatika, Semester 5   │
│─────────────────────────────────────────────│
│  Usia: 21 tahun                             │
│  Perangkat: Android (mid-range)             │
│  Koneksi: WiFi kampus + data seluler        │
│─────────────────────────────────────────────│
│  MOTIVASI:                                  │
│  • Rutin main futsal 2x seminggu            │
│  • Ingin booking cepat lewat HP             │
│  • Mengharapkan harga khusus mahasiswa      │
│─────────────────────────────────────────────│
│  PAIN POINTS:                               │
│  • Sering rebutan slot via WhatsApp         │
│  • Tidak tahu jadwal lapangan real-time     │
│  • Harga kadang tidak konsisten             │
│─────────────────────────────────────────────│
│  GOALS:                                     │
│  • Booking dalam < 2 menit                 │
│  • Bukti booking digital (tidak perlu cetak)│
│  • Notifikasi pengingat H-1                 │
└─────────────────────────────────────────────┘
```

### Persona 2: Masyarakat Umum
```
┌─────────────────────────────────────────────┐
│  👤 BUDI SANTOSO                            │
│  Karyawan Swasta, Penggemar Badminton       │
│─────────────────────────────────────────────│
│  Usia: 35 tahun                             │
│  Perangkat: Android (mid-range)             │
│  Koneksi: WiFi rumah + data seluler         │
│─────────────────────────────────────────────│
│  MOTIVASI:                                  │
│  • Mencari lapangan badminton berkualitas   │
│  • Harga terjangkau dibanding GOR kota      │
│  • Bisa booking dari rumah/kantor           │
│─────────────────────────────────────────────│
│  PAIN POINTS:                               │
│  • Tidak tahu jam operasional lapangan      │
│  • Khawatir slot sudah terisi               │
│  • Pembayaran hanya tunai saat ini          │
│─────────────────────────────────────────────│
│  GOALS:                                     │
│  • Kejelasan harga dan ketersediaan         │
│  • Pembayaran digital yang mudah            │
│  • Konfirmasi booking instan                │
└─────────────────────────────────────────────┘
```

### Persona 3: Koordinator Lapangan (Koorlap)
```
┌─────────────────────────────────────────────┐
│  👤 SITI RAHAYU                             │
│  Petugas Fasilitas Olahraga Kampus          │
│─────────────────────────────────────────────│
│  Usia: 42 tahun                             │
│  Perangkat: Tablet Android                  │
│─────────────────────────────────────────────│
│  MOTIVASI:                                  │
│  • Memvalidasi pengguna lapangan             │
│  • Laporan harian tanpa pencatatan manual   │
│─────────────────────────────────────────────│
│  PAIN POINTS:                               │
│  • Sulit cek validitas bukti booking cetak  │
│  • Sering ada pengguna tidak punya booking  │
│─────────────────────────────────────────────│
│  GOALS:                                     │
│  • Scan QR cepat dan akurat                 │
│  • Info booking langsung tampil di layar    │
└─────────────────────────────────────────────┘
```

### Persona 4: Admin Sistem
```
┌─────────────────────────────────────────────┐
│  👤 AHMAD FAUZI                             │
│  Kepala Unit IT & Fasilitas Kampus          │
│─────────────────────────────────────────────│
│  Usia: 38 tahun                             │
│  Perangkat: Laptop + Tablet                 │
│─────────────────────────────────────────────│
│  MOTIVASI:                                  │
│  • Kelola semua data sistem dari satu panel │
│  • Laporan pendapatan otomatis              │
│  • Monitoring penggunaan real-time          │
│─────────────────────────────────────────────│
│  GOALS:                                     │
│  • Dashboard informatif & mudah dipahami    │
│  • Manajemen data mudah tanpa coding        │
│  • Audit trail lengkap setiap aktivitas     │
└─────────────────────────────────────────────┘
```

---

## 8. Stakeholder Identification

| Stakeholder | Peran | Kepentingan | Level Keterlibatan |
|---|---|---|---|
| Pimpinan Kampus | Sponsor & Decision Maker | ROI & branding smart campus | Tinggi |
| Unit Fasilitas & Sarana | Pemilik proses bisnis | Efisiensi operasional | Tinggi |
| Tim IT Kampus | Operator teknis | Infrastruktur & keamanan | Tinggi |
| Mahasiswa | End-user utama | Kemudahan booking | Tinggi |
| Dosen & Staf | End-user sekunder | Kemudahan booking | Sedang |
| Masyarakat Umum | End-user eksternal | Akses fasilitas | Sedang |
| Payment Gateway Provider | Vendor teknis | Integrasi pembayaran | Sedang |
| Tim Developer CSBS | Pembuat sistem | Delivery produk | Tinggi |

---

## 9. Functional Requirements

### Format Requirement

> **Format:** `[ID] | Nama Fitur | Deskripsi | Prioritas | Role`  
> **Prioritas:** P1 = Critical, P2 = High, P3 = Medium, P4 = Low

### 9.1 Autentikasi & Manajemen Akun

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-01 | Registrasi Pengguna | Pengguna dapat mendaftar dengan email, nama, password, nomor HP | P1 | Semua |
| FR-02 | Registrasi Waban | Tambahan field NIM/NIP/NIDN saat registrasi + upload KTM/KTP | P1 | Waban |
| FR-03 | Login Multi-Role | Login dengan email/password, sistem mengarahkan berdasarkan role | P1 | Semua |
| FR-04 | Verifikasi Email | Email konfirmasi setelah registrasi | P1 | Semua |
| FR-05 | Verifikasi Waban | Admin memvalidasi identitas waban, mengaktifkan harga khusus | P1 | Admin |
| FR-06 | Lupa Password | Reset password via link email | P1 | Semua |
| FR-07 | Manajemen Profil | Edit nama, nomor HP, foto profil, ubah password | P2 | Semua |
| FR-08 | Logout | Invalidasi token/session | P1 | Semua |
| FR-09 | Refresh Token | Perpanjang sesi tanpa re-login | P2 | Mobile |

### 9.2 Manajemen Lapangan

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-10 | CRUD Lapangan | Admin buat, baca, update, hapus data lapangan | P1 | Admin |
| FR-11 | Informasi Lapangan | Nama, jenis olahraga, foto, fasilitas, lokasi, deskripsi | P1 | Semua |
| FR-12 | Pengaturan Jam Operasional | Definisi slot waktu per hari per lapangan | P1 | Admin |
| FR-13 | Pengaturan Harga | Harga normal & harga waban per slot, diferensiasi peak/off-peak | P1 | Admin |
| FR-14 | Nonaktifkan Lapangan | Admin/Koorlap dapat menonaktifkan lapangan sementara (maintenance) | P1 | Admin, Koorlap |
| FR-15 | Galeri Foto Lapangan | Upload multiple foto, tampil di detail lapangan | P2 | Admin |
| FR-16 | Rating & Ulasan | Pengguna beri rating bintang dan ulasan teks setelah sesi selesai | P2 | Waban, Umum |

### 9.3 Sistem Booking

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-17 | Ketersediaan Real-time | Tampilkan slot tersedia/terbooked/maintenance secara real-time | P1 | Semua |
| FR-18 | Buat Booking | Pilih lapangan → slot waktu → review → bayar | P1 | Waban, Umum |
| FR-19 | Validasi Konflik Jadwal | Sistem tolak booking yang bertumpang tindih secara otomatis | P1 | System |
| FR-20 | Reservasi Sementara | Slot dikunci 10 menit selama proses pembayaran | P1 | System |
| FR-21 | Konfirmasi Booking | Booking dikonfirmasi otomatis setelah pembayaran sukses | P1 | System |
| FR-22 | Riwayat Booking | Daftar booking aktif, selesai, dibatalkan | P1 | Waban, Umum |
| FR-23 | Detail Booking | Detail lengkap + QR Code + status | P1 | Waban, Umum |
| FR-24 | Pembatalan Booking | Pengguna dapat batalkan sesuai kebijakan pembatalan | P2 | Waban, Umum |
| FR-25 | Booking berulang | Pesan slot yang sama untuk beberapa minggu ke depan sekaligus | P3 | Waban |

### 9.4 Pembayaran

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-26 | Integrasi Payment Gateway | Midtrans/Xendit untuk transfer bank, QRIS, e-wallet | P1 | System |
| FR-27 | Kalkulasi Harga Otomatis | Harga dihitung berdasarkan role + slot + durasi + voucher | P1 | System |
| FR-28 | Penerapan Voucher | Input kode voucher saat checkout, diskon diterapkan otomatis | P1 | Waban, Umum |
| FR-29 | Bukti Pembayaran | Sistem kirim bukti pembayaran via email + in-app | P1 | System |
| FR-30 | Status Pembayaran | Pending, Success, Failed, Expired, Refunded | P1 | System |
| FR-31 | Refund | Proses pengembalian dana sesuai kebijakan pembatalan | P3 | Admin |

### 9.5 QR Code & Validasi

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-32 | Generate QR Code | Sistem buat QR unik per booking saat pembayaran sukses | P1 | System |
| FR-33 | Tampilkan QR | QR tampil di aplikasi mobile (layar detail booking) | P1 | Waban, Umum |
| FR-34 | Scan QR | Koorlap scan QR via kamera perangkat untuk validasi | P1 | Koorlap |
| FR-35 | Hasil Validasi | Tampilkan info booking + konfirmasi valid/tidak valid | P1 | Koorlap |
| FR-36 | Update Status Kehadiran | Status booking berubah menjadi "Checked In" setelah scan | P1 | System |
| FR-37 | Cegah Scan Ganda | QR tidak bisa di-scan lebih dari sekali | P1 | System |

### 9.6 Notifikasi

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-38 | Notif Konfirmasi Booking | Push + email saat booking dikonfirmasi | P1 | Waban, Umum |
| FR-39 | Notif Pengingat H-1 | Pengingat 24 jam sebelum sesi | P1 | Waban, Umum |
| FR-40 | Notif Pengingat H-0 | Pengingat 1 jam sebelum sesi | P2 | Waban, Umum |
| FR-41 | Notif Pembatalan | Notifikasi jika booking dibatalkan sistem/admin | P1 | Waban, Umum |
| FR-42 | Notif Status Verifikasi | Notif hasil verifikasi identitas waban | P1 | Waban |
| FR-43 | Notif Promo/Voucher | Broadcast promo ke pengguna aktif | P3 | Admin |

### 9.7 Voucher & Promo

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-44 | CRUD Voucher | Admin buat, edit, hapus kode voucher | P1 | Admin |
| FR-45 | Jenis Diskon Voucher | Persentase (%) atau nominal (Rp) | P1 | Admin |
| FR-46 | Batas Penggunaan Voucher | Per pengguna, total penggunaan, tanggal berlaku | P1 | Admin |
| FR-47 | Validasi Voucher | Sistem cek kevalidan voucher saat diinput | P1 | System |
| FR-48 | Riwayat Penggunaan Voucher | Log semua penggunaan voucher | P2 | Admin |

### 9.8 Admin Panel

| ID | Fitur | Deskripsi | Prioritas | Role |
|---|---|---|---|---|
| FR-49 | Dashboard Admin | Statistik booking, revenue, occupancy hari ini | P1 | Admin |
| FR-50 | Manajemen Pengguna | CRUD users, ubah role, nonaktifkan akun | P1 | Admin |
| FR-51 | Verifikasi Waban | Review & approve/reject permohonan waban | P1 | Admin |
| FR-52 | Laporan Booking | Filter laporan per periode, lapangan, status | P1 | Admin |
| FR-53 | Laporan Keuangan | Revenue per periode, metode bayar, lapangan | P1 | Admin |
| FR-54 | Manajemen Koorlap | Assign koorlap ke lapangan tertentu | P2 | Admin |
| FR-55 | Activity Log | Log semua aktivitas sistem | P2 | Admin |
| FR-56 | Export Data | Export laporan ke CSV/Excel/PDF | P2 | Admin |

---

## 10. Non-Functional Requirements

| ID | Kategori | Requirement | Target |
|---|---|---|---|
| NFR-01 | Performance | Response time API | ≤ 3 detik (95th percentile) |
| NFR-02 | Performance | Response time halaman web | ≤ 2 detik |
| NFR-03 | Security | Enkripsi komunikasi | HTTPS/TLS 1.3 wajib |
| NFR-04 | Security | Enkripsi password | bcrypt (cost factor ≥ 12) |
| NFR-05 | Security | Session timeout | 30 menit (web), 7 hari (mobile dengan refresh token) |
| NFR-06 | Security | Rate limiting API | 60 req/menit per IP |
| NFR-07 | Authorization | Role-based access control | Setiap endpoint dilindungi middleware role |
| NFR-08 | Availability | Uptime sistem | ≥ 99.5% per bulan |
| NFR-09 | Compatibility | Platform mobile | Android 8.0+, iOS 14+ |
| NFR-10 | Compatibility | Browser web | Chrome, Firefox, Safari (2 versi terbaru) |
| NFR-11 | Usability | Responsif | Adaptive layout mobile/tablet/desktop |
| NFR-12 | Logging | Activity logging | Semua aksi user & system dicatat |
| NFR-13 | Consistency | Real-time booking | Race condition terdegah, tidak ada double booking |
| NFR-14 | Backup | Automated backup | Daily backup, retensi 30 hari |
| NFR-15 | Scalability | Concurrent users | Support ≥ 500 concurrent users |

---

## 11. User Roles & Permissions

### Matriks Permission

| Fitur / Module | Waban | Umum | Koorlap | Admin |
|---|:---:|:---:|:---:|:---:|
| Registrasi & Login | ✅ | ✅ | ✅ | ✅ |
| Edit Profil Sendiri | ✅ | ✅ | ✅ | ✅ |
| Verifikasi Identitas Waban | ✅ | ❌ | ❌ | ✅ |
| Lihat Lapangan | ✅ | ✅ | ✅ | ✅ |
| CRUD Lapangan | ❌ | ❌ | ❌ | ✅ |
| Nonaktifkan Lapangan | ❌ | ❌ | ✅ | ✅ |
| Booking Lapangan | ✅ | ✅ | ❌ | ✅ |
| Harga Diskon Waban | ✅ | ❌ | ❌ | ❌ |
| Lihat Riwayat Booking Sendiri | ✅ | ✅ | ❌ | ✅ |
| Lihat Semua Booking | ❌ | ❌ | ✅* | ✅ |
| Batalkan Booking Sendiri | ✅ | ✅ | ❌ | ✅ |
| Batalkan Booking Manapun | ❌ | ❌ | ❌ | ✅ |
| Scan QR Booking | ❌ | ❌ | ✅ | ✅ |
| Beri Ulasan & Rating | ✅ | ✅ | ❌ | ❌ |
| CRUD Voucher | ❌ | ❌ | ❌ | ✅ |
| Gunakan Voucher | ✅ | ✅ | ❌ | ❌ |
| Kelola Pengguna | ❌ | ❌ | ❌ | ✅ |
| Dashboard Analitik | ❌ | ❌ | ✅* | ✅ |
| Laporan Keuangan | ❌ | ❌ | ❌ | ✅ |
| Activity Log | ❌ | ❌ | ❌ | ✅ |
| Kirim Notifikasi Broadcast | ❌ | ❌ | ❌ | ✅ |

> *Koorlap hanya melihat data lapangan yang dikelolanya

### Definisi Role

```
ROLE HIERARCHY
══════════════
  SUPER_ADMIN (future)
       │
     ADMIN ──── Full System Access
       │
   KOORLAP ──── Field Operations Access
       │
    WABAN ──── Discounted Booking Access
       │
     UMUM ──── Standard Booking Access
```

---

## 12. User Journey

### Journey: Waban Melakukan Booking

```
[1] DISCOVERY
Pengguna buka aplikasi
    │
    ▼
[2] BROWSE
Lihat daftar lapangan → pilih jenis olahraga
    │
    ▼
[3] CHECK AVAILABILITY
Pilih tanggal → lihat slot tersedia (kalender/grid)
    │
    ▼
[4] SELECT SLOT
Tap slot yang diinginkan → lihat detail harga (harga waban otomatis)
    │
    ▼
[5] CHECKOUT
Review booking summary → input voucher (opsional) → lihat total bayar
    │
    ▼
[6] PAYMENT
Pilih metode bayar → redirect payment gateway → selesaikan pembayaran
    │
    ▼
[7] CONFIRMATION
Booking dikonfirmasi → QR Code diterima → Notifikasi dikirim
    │
    ▼
[8] REMINDER
Notifikasi H-1 dan H-0 sebelum sesi
    │
    ▼
[9] CHECK-IN
Tunjukkan QR ke Koorlap → Koorlap scan → Check-in dicatat
    │
    ▼
[10] POST-SESSION
Sesi selesai → sistem kirim prompt untuk beri ulasan
```

### Journey: Masyarakat Umum

Sama seperti Waban, kecuali:
- Tidak ada step verifikasi identitas kampus
- Harga yang tampil adalah tarif normal
- Tidak ada akses ke voucher khusus waban

### Journey: Admin Mengelola Sistem

```
[1] LOGIN → Dashboard
    │
    ├─► Monitor: booking hari ini, revenue, occupancy
    ├─► Manage: lapangan, pengguna, voucher
    ├─► Review: permohonan verifikasi waban
    ├─► Generate: laporan harian/mingguan/bulanan
    └─► Broadcast: notifikasi promo
```

---

## 13. Use Case Scenario

### UC-01: Booking Lapangan (Happy Path)

**Actor:** Waban (terverifikasi)  
**Precondition:** Login, identitas terverifikasi  
**Main Flow:**
1. Buka tab "Lapangan" di aplikasi
2. Pilih kategori olahraga (Futsal/Badminton/Basket/Voli)
3. Pilih lapangan spesifik
4. Pilih tanggal booking
5. Sistem tampilkan kalender slot (hijau=tersedia, merah=terbooked, abu=maintenance)
6. Tap slot waktu yang diinginkan
7. Sistem tampilkan harga waban otomatis
8. Tap "Pesan Sekarang"
9. Tampil halaman checkout (lapangan, tanggal, jam, durasi, harga)
10. Opsional: input kode voucher
11. Pilih metode pembayaran
12. Konfirmasi & bayar
13. Payment gateway memproses pembayaran
14. Sistem terima konfirmasi pembayaran
15. Booking status = "Confirmed"
16. QR Code digenerate & disimpan
17. Notifikasi push + email dikirim
18. Slot tidak bisa dipesan lagi oleh pengguna lain

**Postcondition:** Booking confirmed, QR tersedia di aplikasi

### UC-02: Validasi QR oleh Koorlap

**Actor:** Koorlap  
**Precondition:** Login sebagai Koorlap  
**Main Flow:**
1. Koorlap buka menu "Scan QR"
2. Izin kamera diberikan
3. Pengguna perlihatkan QR Code dari aplikasi
4. Koorlap arahkan kamera ke QR
5. Sistem decode QR → ekstrak booking_id + token validasi
6. Sistem verifikasi: token valid, booking belum di-scan, waktu sesi sesuai
7. Tampilkan: Nama pemesan, lapangan, jam sesi, status ✅ VALID
8. Status booking berubah menjadi "Checked In"
9. Waktu check-in dicatat

**Error Flow:**
- QR sudah pernah di-scan → tampil "❌ QR Sudah Digunakan"
- Booking dibatalkan → tampil "❌ Booking Tidak Aktif"
- Waktu tidak sesuai → tampil "⚠️ Sesi Belum/Sudah Berakhir"

### UC-03: Verifikasi Waban oleh Admin

**Actor:** Admin  
**Precondition:** Ada permohonan verifikasi waban pending  
**Main Flow:**
1. Admin buka "Manajemen Pengguna" → tab "Verifikasi Waban"
2. Lihat daftar permohonan pending
3. Klik detail permohonan → lihat foto KTM/KTP
4. Bandingkan data NIM/NIP dengan database kampus (manual/API)
5. Klik "Approve" atau "Reject" + alasan
6. Sistem update status user → `is_campus_member = true`
7. Notifikasi hasil verifikasi dikirim ke pengguna
8. Pengguna otomatis mendapat akses harga waban

---

## 14. Core Features

### Feature Map

```
┌─────────────────────────────────────────────────────────────────┐
│                      CSBS CORE FEATURES                         │
├─────────────────┬────────────────────┬───────────────────────────┤
│  USER LAYER     │  BOOKING LAYER     │  MANAGEMENT LAYER         │
├─────────────────┼────────────────────┼───────────────────────────┤
│ • Multi-role    │ • Real-time avail  │ • Admin panel             │
│   auth          │ • Smart pricing    │ • Koorlap dashboard       │
│ • Waban verify  │ • QR generation    │ • Analytics & reports     │
│ • Profile mgmt  │ • Payment gateway  │ • User management         │
│ • Push notif    │ • Voucher system   │ • Venue management        │
│ • Review/rating │ • Booking history  │ • Activity logging        │
└─────────────────┴────────────────────┴───────────────────────────┘
```

---

## 15. MVP Features

Fitur yang harus ada di versi 1.0 (Minimum Viable Product):

| # | Fitur MVP | Justifikasi |
|---|---|---|
| 1 | Registrasi & Login Multi-role | Core foundation |
| 2 | Verifikasi Waban (manual admin) | Pricing differentiator |
| 3 | Daftar & Detail Lapangan | Discovery experience |
| 4 | Real-time Slot Availability | Core booking value |
| 5 | Booking Flow + Konfirmasi | Core transaction |
| 6 | Pembayaran via Midtrans (QRIS, Transfer) | Revenue enabler |
| 7 | QR Code Generate & Scan | Validasi fisik |
| 8 | Notifikasi Push (FCM) | User engagement |
| 9 | Riwayat Booking | User trust |
| 10 | Dashboard Admin Dasar | Operasional |
| 11 | Nonaktifkan Lapangan | Operasional |
| 12 | Voucher Dasar | Revenue optimization |

---

## 16. Future Features / Roadmap

### Phase 2 (3-6 bulan pasca MVP)

| Fitur | Deskripsi |
|---|---|
| Integrasi SIAKAD | Auto-verifikasi NIM dari sistem akademik kampus |
| Booking Berulang | Pesan slot reguler mingguan/bulanan |
| Leaderboard Olahraga | Gamifikasi penggunaan fasilitas |
| Turnamen & Event | Modul khusus pengelolaan turnamen |
| Loyalty Points | Poin reward untuk pengguna aktif |

### Phase 3 (6-12 bulan pasca MVP)

| Fitur | Deskripsi |
|---|---|
| Multi-Kampus | Platform untuk jaringan beberapa kampus |
| Marketplace Peralatan | Sewa peralatan olahraga via platform |
| Live Streaming | Streaming pertandingan dari lapangan |
| API Partner | Buka API untuk aplikasi pihak ketiga |
| AI Recommendation | Rekomendasi lapangan & waktu favorit |

---

## 17. System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                         CSBS ARCHITECTURE                           │
├───────────────┬──────────────────────────┬──────────────────────────┤
│  CLIENT LAYER │      SERVER LAYER        │      DATA LAYER          │
├───────────────┼──────────────────────────┼──────────────────────────┤
│               │                          │                          │
│  Flutter App  │  ┌──────────────────┐   │  ┌──────────────────┐   │
│  (Mobile)     │  │   Laravel 13     │   │  │   MySQL 8.0      │   │
│  ─────────────┤  │   REST API       │   │  │   (Primary DB)   │   │
│               │  │   + Blade Web    │   │  └──────────────────┘   │
│  Web Browser  │  └──────┬───────────┘   │                          │
│  (Admin)      │         │               │  ┌──────────────────┐   │
│               │  ┌──────▼───────────┐   │  │   Redis          │   │
│               │  │   Middleware     │   │  │   (Cache/Queue/  │   │
│               │  │   • Auth         │   │  │    Session)      │   │
│               │  │   • Rate Limit   │   │  └──────────────────┘   │
│               │  │   • CORS         │   │                          │
│               │  └──────┬───────────┘   │  ┌──────────────────┐   │
│               │         │               │  │   Firebase       │   │
│               │  ┌──────▼───────────┐   │  │   (FCM Push      │   │
│               │  │   Services       │   │  │    Notification) │   │
│               │  │   • Booking      │   │  └──────────────────┘   │
│               │  │   • Payment      │   │                          │
│               │  │   • QR Code      │   │  ┌──────────────────┐   │
│               │  │   • Notification │   │  │   Storage        │   │
│               │  │   • Queue/Jobs   │   │  │   (Local/S3)     │   │
│               │  └──────┬───────────┘   │  └──────────────────┘   │
│               │         │               │                          │
│               │  ┌──────▼───────────┐   │                          │
│               │  │ External Services│   │                          │
│               │  │ • Midtrans       │   │                          │
│               │  │ • FCM Firebase   │   │                          │
│               │  │ • Mail SMTP      │   │                          │
│               │  └──────────────────┘   │                          │
└───────────────┴──────────────────────────┴──────────────────────────┘
```

---

## 18. Technology Stack

| Layer | Teknologi | Versi | Keterangan |
|---|---|---|---|
| **Backend Framework** | Laravel | 13.x | PHP MVC Framework |
| **PHP Runtime** | PHP | 8.3+ | Required by Laravel 13 |
| **Web Rendering** | Laravel Blade | - | Server-side templating |
| **Web UI** | Tailwind CSS | 3.x | Utility-first CSS |
| **Web Interactivity** | Alpine.js | 3.x | Lightweight JS framework |
| **API** | REST API | - | JSON-based |
| **Real-time** | Laravel Reverb / Pusher | - | WebSocket server |
| **Database** | MySQL | 8.0+ | Primary data store |
| **Cache** | Redis | 7.x | Session, cache, queue |
| **Queue** | Laravel Queue + Redis | - | Async job processing |
| **Mobile** | Flutter | 3.x (Dart) | Cross-platform mobile |
| **State Management** | BLoC / Riverpod | - | Flutter state |
| **HTTP Client** | Dio | - | Flutter HTTP |
| **Auth Backend** | Laravel Sanctum | - | Token-based API auth |
| **Payment** | Midtrans | - | Payment gateway |
| **Push Notif** | Firebase Cloud Messaging | - | Push notifications |
| **Email** | Laravel Mail + SMTP | - | Transactional email |
| **QR Code** | `chillerlan/php-qrcode` | - | QR generation (PHP) |
| **Storage** | Laravel Storage (Local/S3) | - | File/image storage |
| **Web Server** | Nginx | 1.24+ | Reverse proxy |
| **Process Manager** | Supervisor | - | Queue worker |
| **Containerization** | Docker (opsional) | - | Dev environment |
| **Monitoring** | Laravel Telescope | - | Debug & monitoring |

---

## 19. API Architecture

### API Design Principles

- **RESTful:** Resource-based URLs, proper HTTP verbs
- **Versioned:** Semua endpoint diawali `/api/v1/`
- **Stateless:** Tidak ada session server-side, gunakan Sanctum token
- **Consistent Response:** Format JSON standar di semua endpoint
- **Documented:** Setiap endpoint terdokumentasi (Postman / OpenAPI)

### Standard API Response Structure

```json
// SUCCESS RESPONSE
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": {
    // payload
  },
  "meta": {
    "timestamp": "2025-06-01T10:00:00+08:00",
    "version": "1.0.0"
  }
}

// PAGINATED RESPONSE
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [ ... ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  },
  "meta": {
    "timestamp": "2025-06-01T10:00:00+08:00"
  }
}

// ERROR RESPONSE
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "email": ["Email sudah digunakan"],
    "password": ["Password minimal 8 karakter"]
  },
  "meta": {
    "timestamp": "2025-06-01T10:00:00+08:00"
  }
}
```

### HTTP Status Code Standards

| Code | Situasi |
|---|---|
| 200 | OK — Request berhasil |
| 201 | Created — Resource berhasil dibuat |
| 204 | No Content — Berhasil tanpa response body |
| 400 | Bad Request — Request tidak valid |
| 401 | Unauthorized — Token tidak ada/kadaluarsa |
| 403 | Forbidden — Tidak punya izin |
| 404 | Not Found — Resource tidak ditemukan |
| 409 | Conflict — Konflik data (cth: slot sudah dibooked) |
| 422 | Unprocessable Entity — Validasi gagal |
| 429 | Too Many Requests — Rate limit terlampaui |
| 500 | Internal Server Error — Error server |

---

## 20. Database Design Overview

### Prinsip Desain Database

- **Normalisasi:** Minimal 3NF untuk menghindari redundansi
- **Soft Delete:** Gunakan `deleted_at` (SoftDeletes) untuk data penting
- **Timestamps:** Semua tabel memiliki `created_at` dan `updated_at`
- **UUID/ULID:** Pertimbangkan ULID untuk primary key di tabel transaksi
- **Indexing:** Index pada kolom yang sering diquery (FK, status, tanggal)
- **Enum:** Gunakan string enum via check constraint atau tabel referensi

### Entity Relationship Overview

```
users ──────────────── user_campus_verifications
  │
  ├── bookings ──────── booking_payments
  │      │               │
  │      │           payment_gateway_logs
  │      │
  │      └── booking_qr_codes
  │
  ├── user_fcm_tokens
  │
  └── reviews

venues ──────────────── venue_photos
  │
  ├── venue_slots ────── bookings
  │
  └── venue_closures

vouchers ──────────── voucher_usages
notifications ──────── notification_logs
```

---

## 21. ERD Description

### Tabel Utama dan Relasinya

**users**
- PK: `id` (BIGINT UNSIGNED, AUTO_INCREMENT)
- Fields: `name`, `email` (UNIQUE), `password`, `phone`, `avatar`, `role` (ENUM: waban, umum, koorlap, admin), `is_campus_member` (BOOLEAN), `is_active` (BOOLEAN), `email_verified_at`

**user_campus_verifications**
- PK: `id`, FK: `user_id`
- Fields: `identity_number` (NIM/NIP/NIDN), `identity_type` (ENUM: nim, nip, nidn), `document_path`, `status` (ENUM: pending, approved, rejected), `reviewed_by` (FK: users.id), `reviewed_at`, `rejection_reason`

**venues**
- PK: `id`, FK: `managed_by` (koorlap user_id)
- Fields: `name`, `slug`, `sport_type` (ENUM: futsal, badminton, basket, voli), `description`, `location`, `facilities`, `is_active`

**venue_slots**
- PK: `id`, FK: `venue_id`
- Fields: `day_of_week` (0-6), `start_time`, `end_time`, `price_normal` (DECIMAL), `price_campus` (DECIMAL), `is_active`

**bookings**
- PK: `id`, `booking_code` (UNIQUE, human-readable), FK: `user_id`, `venue_id`, `slot_id`
- Fields: `booking_date`, `start_time`, `end_time`, `duration_hours`, `price_per_hour`, `total_price`, `discount_amount`, `voucher_code`, `status` (ENUM: pending_payment, confirmed, checked_in, completed, cancelled, expired)

**booking_payments**
- PK: `id`, FK: `booking_id`
- Fields: `payment_method`, `payment_gateway`, `gateway_order_id`, `gateway_transaction_id`, `amount`, `status` (ENUM: pending, success, failed, expired, refunded), `paid_at`, `gateway_response` (JSON)

**booking_qr_codes**
- PK: `id`, FK: `booking_id`
- Fields: `token` (UNIQUE, random hash), `qr_image_path`, `scanned_at`, `scanned_by` (FK: users.id), `is_used`

**vouchers**
- PK: `id`
- Fields: `code` (UNIQUE), `name`, `description`, `discount_type` (ENUM: percentage, fixed), `discount_value`, `min_booking_amount`, `max_discount_amount`, `max_total_usage`, `max_per_user`, `valid_from`, `valid_until`, `is_active`

**reviews**
- PK: `id`, FK: `user_id`, `booking_id`, `venue_id`
- Fields: `rating` (TINYINT 1-5), `comment`, `is_visible`

**notifications**
- PK: `id`, FK: `user_id`
- Fields: `type`, `title`, `body`, `data` (JSON), `channel` (push, email, sms), `status` (pending, sent, failed), `sent_at`, `read_at`

**activity_logs**
- PK: `id`, FK: `user_id`
- Fields: `action`, `model_type`, `model_id`, `description`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`

---

## 22. UI/UX Direction

### Design System

| Aspek | Spesifikasi |
|---|---|
| **Design Language** | Material Design 3 (Mobile) / Custom (Web) |
| **Color Palette** | Primary: `#1B5E9B` (biru kampus), Secondary: `#F59E0B` (aksen), Neutral: `#F3F4F6` |
| **Typography** | Mobile: Poppins. Web: Inter |
| **Icon Set** | Phosphor Icons / Material Icons |
| **Spacing** | 4px grid system |
| **Border Radius** | 8px (button), 12px (card), 16px (modal) |
| **Shadow** | Soft shadow system (3 level: none, sm, md) |

### UX Principles

1. **Mobile-First:** Desain dimulai dari layar mobile (360px), kemudian tablet dan desktop
2. **Progressive Disclosure:** Tampilkan informasi bertahap, jangan overwhelm pengguna
3. **Error Prevention:** Validasi real-time sebelum submit, bukan hanya setelah
4. **Feedback Instant:** Setiap aksi user mendapat respons visual dalam < 200ms
5. **Consistency:** Komponen dan pattern yang konsisten di seluruh aplikasi

### Color Coding untuk Slot Lapangan

| Warna | Status | Hex |
|---|---|---|
| 🟢 Hijau | Tersedia | `#22C55E` |
| 🔴 Merah | Sudah Dipesan | `#EF4444` |
| 🟡 Kuning | Hampir Habis (< 2 slot) | `#F59E0B` |
| ⚫ Abu-abu | Maintenance / Tutup | `#9CA3AF` |
| 🔵 Biru | Dipilih Pengguna | `#1B5E9B` |

---

## 23. Mobile App Flow

### Screen Architecture (Flutter)

```
APP
│
├── ONBOARDING (sekali tampil)
│   └── Splash → Welcome → Login/Register
│
├── AUTH
│   ├── Login Screen
│   ├── Register Screen (+ pilih role waban/umum)
│   ├── Verifikasi Email Screen
│   ├── Upload Dokumen Waban Screen
│   └── Lupa Password Screen
│
├── MAIN APP (Bottom Navigation)
│   ├── 🏟️ BERANDA
│   │   ├── Banner promo
│   │   ├── Shortcut kategori olahraga
│   │   ├── Lapangan terpopuler
│   │   └── Booking aktif user (jika ada)
│   │
│   ├── 🔍 LAPANGAN
│   │   ├── Search & Filter Screen
│   │   ├── List Lapangan Screen
│   │   ├── Detail Lapangan Screen
│   │   │   ├── Foto galeri
│   │   │   ├── Info & fasilitas
│   │   │   ├── Kalender slot
│   │   │   └── Rating & ulasan
│   │   └── Pilih Slot Screen
│   │
│   ├── 📋 BOOKING
│   │   ├── Checkout Screen
│   │   ├── Pilih Metode Bayar Screen
│   │   ├── Riwayat Booking Screen
│   │   └── Detail Booking Screen
│   │       └── QR Code Display Screen
│   │
│   └── 👤 PROFIL
│       ├── Profile Screen
│       ├── Edit Profile Screen
│       ├── Status Verifikasi Screen
│       ├── Notifikasi Screen
│       └── Pengaturan Screen
│
└── KOORLAP VIEW (jika role = koorlap)
    ├── Dashboard Koorlap
    ├── Scan QR Screen
    ├── Hasil Validasi Screen
    └── Daftar Booking Hari Ini
```

---

## 24. Web Admin Flow

### Admin Panel Navigation

```
WEB ADMIN (Blade)
│
├── 🔐 AUTH
│   └── Login → Dashboard
│
├── 📊 DASHBOARD
│   ├── KPI Cards (booking hari ini, revenue, occupancy)
│   ├── Chart booking per lapangan
│   └── Aktivitas terbaru
│
├── 🏟️ LAPANGAN
│   ├── Daftar Lapangan
│   ├── Tambah/Edit Lapangan
│   ├── Atur Slot & Harga
│   └── Galeri Foto
│
├── 📋 BOOKING
│   ├── Semua Booking
│   ├── Filter & Search
│   └── Detail Booking
│
├── 👥 PENGGUNA
│   ├── Daftar Pengguna
│   ├── Detail Pengguna
│   ├── Verifikasi Waban (Pending)
│   └── Kelola Koorlap
│
├── 🎟️ VOUCHER
│   ├── Daftar Voucher
│   ├── Tambah/Edit Voucher
│   └── Riwayat Penggunaan
│
├── 💰 KEUANGAN
│   ├── Laporan Revenue
│   ├── Laporan per Lapangan
│   └── Export Data
│
├── 🔔 NOTIFIKASI
│   └── Broadcast Notifikasi
│
└── ⚙️ PENGATURAN
    ├── Pengaturan Umum Sistem
    ├── Manajemen Admin
    └── Activity Log
```

---

## 25. Authentication & Authorization Flow

### Registration Flow

```
Pengguna Input Data Registrasi
          │
          ▼
    Validasi Input ──── FAIL ──► Tampilkan Error
          │ OK
          ▼
    Buat User Record (role = waban/umum)
          │
          ▼
    Kirim Email Verifikasi
          │
          ▼
    Pengguna Klik Link Email
          │
          ▼
    Email Terverifikasi → Login Aktif
          │
          ▼ (jika waban)
    Upload Dokumen Identitas
          │
          ▼
    Admin Review → Approve/Reject
          │ Approve
          ▼
    is_campus_member = true → Harga waban aktif
```

### Login Flow (Mobile)

```
Input Email + Password
          │
          ▼
    Sanctum: verifyCredentials()
          │ Sukses
          ▼
    Generate Personal Access Token
          │
          ▼
    Return token + user data + role
          │
          ▼
    Flutter simpan token di SecureStorage
          │
          ▼
    Route ke HomeScreen sesuai role
```

### Token Refresh Flow

```
API Request dengan token
          │
          ▼
    Middleware: auth:sanctum
          │
          ├── Token valid → Lanjutkan request
          │
          └── Token expired (401)
                    │
                    ▼
              Flutter: refresh token
                    │ Sukses
                    ▼
              Retry request dengan token baru
                    │ Gagal
                    ▼
              Redirect ke Login Screen
```

---

## 26. Booking Flow

### Booking State Machine

```
[SLOT TERSEDIA]
      │
      ▼ User pilih slot
[SLOT_LOCKED] ─── 10 menit timeout ──► [SLOT TERSEDIA kembali]
      │
      ▼ Pembayaran berhasil
[PENDING_PAYMENT]
      │
      ├─ Payment Success ──► [CONFIRMED] ──► [CHECKED_IN] ──► [COMPLETED]
      │
      ├─ Payment Failed ────► [FAILED] ──► Slot dilepas
      │
      ├─ Payment Expired ───► [EXPIRED] ──► Slot dilepas
      │
      └─ User Cancel ───────► [CANCELLED] ──► Slot dilepas (sesuai kebijakan)
```

### Booking Flow Detail

```
[1] User pilih slot
          │
          ▼
[2] Server: cek ketersediaan slot (SELECT ... FOR UPDATE)
          │ Tersedia
          ▼
[3] Server: buat booking record (status: pending_payment)
          │ + kunci slot dengan booking_id
          ▼
[4] Server: hitung harga (role + voucher + peak/off-peak)
          │
          ▼
[5] Client: tampilkan checkout summary
          │
          ▼
[6] User konfirmasi → pilih metode bayar
          │
          ▼
[7] Server: buat transaksi Midtrans
          │
          ▼
[8] Client: redirect/WebView ke Midtrans
          │
          ▼
[9] User selesaikan pembayaran
          │
          ▼
[10] Midtrans: webhook ke server (payment_success)
          │
          ▼
[11] Server: update booking status → "confirmed"
          │ + generate QR token
          │ + generate QR image
          ▼
[12] Server: dispatch job:
          │ - SendBookingConfirmationEmail
          │ - SendPushNotification
          │ - ScheduleReminderNotification
          ▼
[13] Client: booking berhasil → tampilkan QR
```

### Validasi Konflik Jadwal

```sql
-- Server-side conflict check sebelum booking dikonfirmasi
SELECT COUNT(*) 
FROM bookings 
WHERE venue_id = :venue_id
  AND booking_date = :booking_date
  AND status IN ('pending_payment', 'confirmed', 'checked_in')
  AND (
    (start_time < :end_time AND end_time > :start_time) -- Overlap check
  )
```

Jika COUNT > 0, server tolak booking dengan error 409 Conflict.

---

## 27. Payment Flow

### Payment Gateway Integration (Midtrans)

```
[CREATE ORDER]
Flutter → POST /api/v1/bookings/{id}/payment
          │
          ▼
Server: Midtrans Snap API
  createTransaction({
    order_id: booking_code,
    gross_amount: total_price,
    customer_details: { ... },
    item_details: [ venue, slot, discount ]
  })
          │
          ▼
Server return: { snap_token, redirect_url }
          │
          ▼
Flutter: tampilkan Midtrans payment sheet/WebView

[PAYMENT SUCCESS]
Midtrans → POST /api/webhook/midtrans (signature verified)
          │
          ▼
Server: verify signature
          │
          ▼
Server: update booking + payment record
          │
          ▼
Server: dispatch notification jobs
          │
          ▼
Flutter: poll /api/v1/bookings/{id}/status setiap 3 detik
```

### Kebijakan Pembatalan & Refund

| Waktu Pembatalan | Refund |
|---|---|
| > 24 jam sebelum sesi | 100% |
| 12-24 jam sebelum sesi | 50% |
| < 12 jam sebelum sesi | 0% |
| No-show | 0% |

---

## 28. QR Validation Flow

### QR Code Structure

```json
// QR Code berisi encrypted payload:
{
  "booking_id": 123,
  "booking_code": "CSBS-20250601-ABCD",
  "token": "a1b2c3d4e5f6...", // HMAC-SHA256 dari booking data
  "expires_at": "2025-06-01T15:00:00+08:00"
}
```

### Scan & Validation Sequence

```
Koorlap tap "Scan QR"
      │
      ▼
Kamera aktif → baca QR
      │
      ▼
Flutter: decode QR → POST /api/v1/qr/validate
      │
      ▼
Server: verifikasi token (HMAC)
      │
      ├─ INVALID TOKEN → return error "QR tidak valid"
      │
      ▼
Server: cari booking by ID
      │
      ├─ BOOKING NOT FOUND → return error "Booking tidak ditemukan"
      │
      ▼
Server: cek status booking == 'confirmed'
      │
      ├─ STATUS BUKAN CONFIRMED → return error sesuai status
      │
      ▼
Server: cek is_used == false
      │
      ├─ ALREADY USED → return error "QR sudah digunakan"
      │
      ▼
Server: cek waktu (booking_date + start_time - 30 menit)
      │
      ├─ TERLALU AWAL/TERLAMBAT → return warning
      │
      ▼
Server: update is_used = true, scanned_at = now(), scanned_by = koorlap_id
      │
      ▼
Server: update booking.status = 'checked_in'
      │
      ▼
Return: { valid: true, booking_info: { ... } }
      │
      ▼
Flutter: tampilkan "✅ CHECK-IN BERHASIL" + detail booking
```

---

## 29. Notification Flow

### Notification Architecture

```
TRIGGER EVENT
      │
      ▼
Laravel: dispatch NotificationJob to Queue
      │
      ▼
Queue Worker (Redis): proses job
      │
      ├──► Firebase FCM (Push Notification) ──► Mobile App
      │
      ├──► Laravel Mail (Email) ──► SMTP Server ──► Email Pengguna
      │
      └──► Database (in-app notification) ──► /api/v1/notifications
```

### Notification Types & Schedule

| Jenis | Trigger | Channel | Delay |
|---|---|---|---|
| Booking Confirmed | Payment sukses | Push + Email | Segera |
| Reminder H-1 | Booking created | Push | 24 jam sebelum sesi |
| Reminder H-0 | Booking created | Push | 1 jam sebelum sesi |
| Booking Cancelled | Cancel action | Push + Email | Segera |
| Payment Failed | Gateway response | Push + Email | Segera |
| Verifikasi Disetujui | Admin approve | Push + Email | Segera |
| Verifikasi Ditolak | Admin reject | Push + Email | Segera |
| Promo/Voucher Baru | Admin broadcast | Push | Jadwal admin |

### Notification Payload (FCM)

```json
{
  "to": "device_fcm_token",
  "notification": {
    "title": "Booking Dikonfirmasi! 🎉",
    "body": "Lapangan Futsal A | Senin, 2 Jun 2025 | 15:00-17:00"
  },
  "data": {
    "type": "booking_confirmed",
    "booking_id": "123",
    "route": "/booking/detail/123"
  }
}
```

---

## 30. Voucher & Promo System

### Voucher Data Model

```
VOUCHER
├── code (unique, case-insensitive)
├── discount_type: percentage | fixed
├── discount_value: 20 (%) atau 50000 (Rp)
├── min_booking_amount: 100000
├── max_discount_amount: 50000 (cap untuk percentage)
├── max_total_usage: 100 (total semua user)
├── max_per_user: 1 (per user)
├── valid_from: 2025-06-01
├── valid_until: 2025-06-30
├── target_role: all | waban | umum
└── is_active: true
```

### Voucher Validation Flow

```
User input kode voucher
      │
      ▼
POST /api/v1/vouchers/validate
      │
      ▼
Server checks:
  1. Voucher exists? ──────────── NO → "Kode tidak ditemukan"
  2. is_active == true? ──────── NO → "Voucher tidak aktif"
  3. Dalam rentang tanggal? ──── NO → "Voucher kadaluarsa"
  4. Total usage < max? ──────── NO → "Voucher sudah habis"
  5. User usage < max_per_user? ─ NO → "Kamu sudah pakai voucher ini"
  6. Amount >= min_booking? ───── NO → "Minimum booking tidak terpenuhi"
  7. Role sesuai target? ──────── NO → "Voucher tidak berlaku untukmu"
      │ ALL PASS
      ▼
Return: { valid: true, discount_amount: 25000, final_price: 75000 }
```

### Kalkulasi Harga Final

```
harga_dasar = slot.price_campus (jika waban) ATAU slot.price_normal (jika umum)
total_sebelum_diskon = harga_dasar × durasi_jam
diskon_voucher = hitung_diskon(voucher, total_sebelum_diskon)
TOTAL_BAYAR = total_sebelum_diskon - diskon_voucher
```

---

## 31. Real-Time Availability System

### Strategi Konsistensi Slot

Untuk mencegah double booking, sistem menggunakan:

1. **Database Lock:** `SELECT ... FOR UPDATE` saat booking
2. **Redis Lock:** Distributed lock per slot selama proses checkout (10 menit)
3. **Optimistic Locking:** Versi field untuk detect concurrent update
4. **WebSocket (opsional MVP+):** Broadcast perubahan status slot ke semua client

### Slot Availability Response

```json
{
  "venue_id": 1,
  "date": "2025-06-02",
  "slots": [
    {
      "id": 1,
      "start_time": "07:00",
      "end_time": "09:00",
      "price_normal": 150000,
      "price_campus": 90000,
      "status": "available"
    },
    {
      "id": 2,
      "start_time": "09:00",
      "end_time": "11:00",
      "price_normal": 150000,
      "price_campus": 90000,
      "status": "booked"
    },
    {
      "id": 3,
      "start_time": "11:00",
      "end_time": "13:00",
      "price_normal": 150000,
      "price_campus": 90000,
      "status": "maintenance"
    }
  ]
}
```

### Cache Strategy untuk Slot

```
Request slot availability
      │
      ▼
Check Redis cache: slot:{venue_id}:{date}
      │
      ├── HIT (cache fresh) → return cached data
      │
      └── MISS
            │
            ▼
        Query MySQL
            │
            ▼
        Set Redis cache (TTL: 30 detik)
            │
            ▼
        Return data

[Saat booking dikonfirmasi/dibatalkan]
Invalidate cache: slot:{venue_id}:{date}
```

---

## 32. Dashboard & Analytics

### Admin Dashboard KPIs

| KPI | Deskripsi | Period |
|---|---|---|
| Total Booking Hari Ini | Jumlah booking confirmed hari ini | Real-time |
| Revenue Hari Ini | Total pembayaran sukses hari ini | Real-time |
| Occupancy Rate | % slot terisi vs total slot | Hari ini |
| Pending Verifikasi | Jumlah permohonan waban belum diproses | Real-time |
| New Users | Registrasi baru hari ini | Hari ini |
| Top Lapangan | Lapangan paling banyak dibooking | 7 hari |

### Charts & Visualizations

- **Line Chart:** Revenue per hari (30 hari terakhir)
- **Bar Chart:** Booking per lapangan (7 hari terakhir)
- **Pie Chart:** Distribusi metode pembayaran
- **Heatmap:** Jam populer per hari dalam seminggu
- **Table:** Booking terbaru dengan status

---

## 33. Security Requirements

### Authentication Security

| Aspek | Implementasi |
|---|---|
| Password Hashing | bcrypt, cost factor 12 |
| Token | Laravel Sanctum Personal Access Token |
| Token Storage | Flutter: `flutter_secure_storage` (Keychain/Keystore) |
| HTTPS | TLS 1.3 mandatory, HSTS header |
| Rate Limiting | 60 req/min (API umum), 10 req/min (login) |
| Brute Force | Lockout 15 menit setelah 5 gagal login |
| CSRF | Laravel CSRF token (web), tidak diperlukan (API Sanctum) |

### Data Security

| Aspek | Implementasi |
|---|---|
| SQL Injection | Eloquent ORM + Query Builder (parameterized) |
| XSS | Blade auto-escape, CSP header |
| API Key | Env file, tidak di-commit ke repo |
| Webhook Validation | Verifikasi signature Midtrans di setiap webhook |
| QR Token | HMAC-SHA256, expires dengan waktu sesi |
| Sensitive Data | Dokumen identitas disimpan di private storage |

### CORS Configuration

```php
// Izinkan hanya dari domain Flutter app (untuk web) dan Blade web
'allowed_origins' => [
    env('FRONTEND_URL', 'https://app.csbs.kampus.ac.id'),
    env('ADMIN_URL', 'https://admin.csbs.kampus.ac.id'),
]
```

---

## 34. Performance Requirements

| Metric | Target | Cara Ukur |
|---|---|---|
| API Response Time | P95 ≤ 3 detik | Laravel Telescope / APM |
| Web Page Load | ≤ 2 detik (LCP) | Lighthouse |
| Mobile App Launch | ≤ 2 detik (cold start) | Flutter DevTools |
| Database Query | Slow query log > 1 detik | MySQL slow_query_log |
| Queue Processing | Job selesai ≤ 30 detik | Laravel Horizon |
| Concurrent Users | ≥ 500 user simultan | Load testing (k6) |

### Optimisasi Strategi

- **Database:** Indexing pada FK dan kolom filter, query optimization, connection pooling
- **Cache:** Redis untuk slot availability, user session, rate limiting
- **Queue:** Semua operasi berat (email, push notif, laporan) via queue
- **Image:** Compress upload foto, lazy loading di client
- **API:** Pagination wajib untuk list data, field selection opsional

---

## 35. Scalability Requirements

### Horizontal Scaling Plan

```
┌───────────────────────────────────────────────────────┐
│                    LOAD BALANCER (Nginx)               │
└───────────────────┬───────────────────────────────────┘
                    │
         ┌──────────┴───────────┐
         ▼                      ▼
  ┌─────────────┐       ┌─────────────┐
  │  App Server │       │  App Server │  (dapat ditambah)
  │  Laravel 1  │       │  Laravel 2  │
  └─────────────┘       └─────────────┘
         │                      │
         └──────────┬───────────┘
                    ▼
         ┌──────────────────┐
         │  MySQL Primary   │───► MySQL Replica (Read)
         └──────────────────┘
                    │
         ┌──────────────────┐
         │   Redis Cluster  │
         └──────────────────┘
```

### Scaling Triggers

| Metric | Threshold | Action |
|---|---|---|
| CPU Usage | > 70% sustained | Tambah app server |
| Memory Usage | > 80% | Upgrade server spec |
| DB Connections | > 80% pool | Read replica |
| Queue depth | > 1000 jobs | Tambah queue worker |

---

## 36. Logging & Monitoring

### Logging Strategy

```php
// Log channels yang digunakan:
'channels' => [
    'daily'   => [...],  // General application logs
    'api'     => [...],  // Semua API request/response
    'booking' => [...],  // Semua booking transactions
    'payment' => [...],  // Semua payment events
    'audit'   => [...],  // User actions audit trail
    'error'   => [...],  // Errors & exceptions
]
```

### Activity Logging

Setiap aksi penting dicatat di tabel `activity_logs`:
- Login / Logout
- Booking created / cancelled
- Payment success / failed
- QR scan
- Admin actions (user management, venue management, voucher CRUD)
- Verifikasi waban (approve/reject)

### Monitoring Tools

| Tool | Fungsi |
|---|---|
| Laravel Telescope | Debug & profiling lokal |
| Laravel Horizon | Monitor queue jobs |
| Nginx access log | Traffic analysis |
| MySQL slow query log | Query optimization |
| Uptime Robot | Availability monitoring |
| Sentry (rekomendasi) | Error tracking production |

---

## 37. Backup & Recovery

### Backup Strategy

| Jenis | Frekuensi | Retensi | Storage |
|---|---|---|---|
| Database Full | Harian (02:00 WIB) | 30 hari | Object storage (S3/B2) |
| Database Incremental | Setiap 6 jam | 7 hari | Object storage |
| File/Media | Mingguan | 4 minggu | Object storage |
| Config & Code | Setiap push (Git) | Permanent | Git repository |

### Recovery Time Objective (RTO & RPO)

| Metric | Target |
|---|---|
| RTO (Recovery Time) | ≤ 4 jam |
| RPO (Recovery Point) | ≤ 6 jam (max data loss) |

### Backup Automation Script

```bash
# /etc/cron.d/csbs-backup
0 2 * * * www-data /usr/bin/php /var/www/csbs/artisan backup:run --only-db
0 3 * * 0 www-data /usr/bin/php /var/www/csbs/artisan backup:run
```

---

## 38. Risks & Challenges

| # | Risiko | Dampak | Probabilitas | Mitigasi |
|---|---|---|---|---|
| R-01 | Payment gateway downtime | Transaksi gagal | Sedang | Retry mechanism, fallback notif ke user |
| R-02 | Double booking (race condition) | User tidak puas, konflik | Rendah | DB lock + Redis lock |
| R-03 | FCM push notif gagal | User tidak ternotif | Sedang | Fallback ke email, in-app notification |
| R-04 | QR Code dipalsukan | Penggunaan tidak sah | Rendah | HMAC signature + expiry time |
| R-05 | Data breach | Kehilangan kepercayaan | Rendah | Enkripsi, audit log, security review |
| R-06 | Adopsi pengguna rendah | ROI tidak tercapai | Sedang | UX testing, sosialisasi aktif, onboarding |
| R-07 | Integrasi SIAKAD kompleks | Verifikasi waban lambat | Tinggi | Verifikasi manual dulu (MVP), API SIAKAD Phase 2 |
| R-08 | Flutter compatibility issues | App crash di device tertentu | Sedang | Device testing matrix, CI/CD |

---

## 39. Development Milestones

| Milestone | Deliverable | Target Tanggal | Durasi |
|---|---|---|---|
| M-01 | Setup & Architecture | Repo, DB schema, Laravel skeleton | Minggu 1-2 |
| M-02 | Auth & User Management | Login, register, verifikasi waban | Minggu 3-4 |
| M-03 | Venue & Slot Management | CRUD lapangan, slot, harga | Minggu 5-6 |
| M-04 | Booking System | Booking flow, konflik validasi, QR | Minggu 7-9 |
| M-05 | Payment Integration | Midtrans integration, webhook | Minggu 10-11 |
| M-06 | Notification System | FCM, email, queue jobs | Minggu 12 |
| M-07 | Admin Panel (Web) | Blade admin dashboard | Minggu 13-15 |
| M-08 | Flutter App | Mobile app full feature | Minggu 10-17 |
| M-09 | QA & Testing | Unit, integration, UAT | Minggu 18-19 |
| M-10 | Deployment & Launch | Production deploy | Minggu 20 |

---

## 40. Sprint Planning Recommendation

### Sprint 1 (Minggu 1-2): Foundation
- Setup Laravel 13 project + PostgreSQL
- Setup Git repository + branching strategy
- Konfigurasi Docker development environment
- Database schema + migration awal
- Setup Laravel Sanctum
- API response standard helper

### Sprint 2 (Minggu 3-4): Auth System
- Register & login endpoint
- Email verification
- Role middleware
- Profile management
- Waban verification upload
- Admin approve/reject waban

### Sprint 3 (Minggu 5-6): Venue Management
- CRUD venues endpoint + Blade admin
- Venue photos upload
- Slot & pricing management
- Venue enable/disable

### Sprint 4-5 (Minggu 7-9): Booking Core
- Slot availability endpoint (+ Redis cache)
- Create booking with conflict validation
- Booking status management
- QR Code generation
- Booking history endpoint

### Sprint 6 (Minggu 10-11): Payment
- Midtrans integration (create transaction)
- Midtrans webhook handler
- Payment status tracking
- Voucher system

### Sprint 7 (Minggu 12): Notifications
- FCM setup + token management
- Queue jobs for notifications
- Scheduled reminders (Laravel Scheduler)
- Email templates

### Sprint 8-9 (Minggu 13-15): Admin Panel
- Dashboard with charts
- User management Blade
- Booking management
- Reports & export

### Sprint 10-13 (Minggu 10-17): Flutter App
- Project setup + BLoC/Riverpod
- Auth screens
- Venue & slot selection
- Booking flow + payment
- QR display & scan (Koorlap)
- Profile & notification

### Sprint 14-15 (Minggu 18-19): QA
- Unit testing (PHPUnit)
- Integration testing
- Flutter widget testing
- UAT dengan user representatif
- Performance testing (k6)
- Security review

### Sprint 16 (Minggu 20): Launch
- Production server setup
- SSL certificate
- DNS configuration
- Deployment
- Monitoring setup
- Soft launch

---

## 41. Suggested Folder Structure Laravel

```
csbs-backend/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── SendReminderNotifications.php
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── V1/
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   ├── BookingController.php
│   │   │   │   │   ├── NotificationController.php
│   │   │   │   │   ├── PaymentController.php
│   │   │   │   │   ├── ProfileController.php
│   │   │   │   │   ├── QRCodeController.php
│   │   │   │   │   ├── ReviewController.php
│   │   │   │   │   ├── VenueController.php
│   │   │   │   │   └── VoucherController.php
│   │   │   └── Admin/
│   │   │       ├── AdminDashboardController.php
│   │   │       ├── AdminBookingController.php
│   │   │       ├── AdminUserController.php
│   │   │       ├── AdminVenueController.php
│   │   │       └── AdminVoucherController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   ├── EnsureEmailIsVerified.php
│   │   │   └── ApiRateLimiter.php
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Booking/
│   │   │   │   └── CreateBookingRequest.php
│   │   │   └── Venue/
│   │   │       └── CreateVenueRequest.php
│   │   └── Resources/
│   │       ├── BookingResource.php
│   │       ├── UserResource.php
│   │       ├── VenueResource.php
│   │       └── SlotResource.php
│   ├── Jobs/
│   │   ├── SendBookingConfirmationJob.php
│   │   ├── SendReminderNotificationJob.php
│   │   └── ProcessPaymentWebhookJob.php
│   ├── Mail/
│   │   ├── BookingConfirmedMail.php
│   │   └── WabanVerificationResultMail.php
│   ├── Models/
│   │   ├── ActivityLog.php
│   │   ├── Booking.php
│   │   ├── BookingPayment.php
│   │   ├── BookingQrCode.php
│   │   ├── Notification.php
│   │   ├── Review.php
│   │   ├── User.php
│   │   ├── UserCampusVerification.php
│   │   ├── UserFcmToken.php
│   │   ├── Venue.php
│   │   ├── VenuePhoto.php
│   │   ├── VenueSlot.php
│   │   ├── Voucher.php
│   │   └── VoucherUsage.php
│   ├── Notifications/
│   │   └── BookingReminderNotification.php
│   ├── Observers/
│   │   └── BookingObserver.php
│   ├── Policies/
│   │   ├── BookingPolicy.php
│   │   └── VenuePolicy.php
│   ├── Services/
│   │   ├── BookingService.php
│   │   ├── MidtransService.php
│   │   ├── NotificationService.php
│   │   ├── QRCodeService.php
│   │   ├── SlotAvailabilityService.php
│   │   └── VoucherService.php
│   └── Traits/
│       ├── ApiResponse.php
│       └── HasActivityLog.php
├── config/
│   ├── midtrans.php
│   └── csbs.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── layouts/
│   │   │   ├── dashboard/
│   │   │   ├── bookings/
│   │   │   ├── users/
│   │   │   ├── venues/
│   │   │   └── vouchers/
│   │   ├── emails/
│   │   │   ├── booking-confirmed.blade.php
│   │   │   └── waban-verification.blade.php
│   │   └── auth/
│   ├── css/
│   └── js/
├── routes/
│   ├── api.php        ← REST API routes
│   ├── web.php        ← Admin panel routes
│   └── console.php
├── storage/
│   └── app/
│       ├── public/
│       │   ├── venues/
│       │   └── qr-codes/
│       └── private/
│           └── documents/  ← KTM/KTP upload
└── tests/
    ├── Feature/
    │   ├── Auth/
    │   ├── Booking/
    │   └── Payment/
    └── Unit/
        ├── Services/
        └── Models/
```

---

## 42. Suggested Folder Structure Flutter

```
csbs_mobile/
├── lib/
│   ├── main.dart
│   ├── app.dart
│   ├── core/
│   │   ├── constants/
│   │   │   ├── api_constants.dart
│   │   │   ├── app_colors.dart
│   │   │   └── app_strings.dart
│   │   ├── errors/
│   │   │   ├── exceptions.dart
│   │   │   └── failures.dart
│   │   ├── network/
│   │   │   ├── api_client.dart        ← Dio instance
│   │   │   ├── interceptors/
│   │   │   │   ├── auth_interceptor.dart
│   │   │   │   └── log_interceptor.dart
│   │   │   └── network_info.dart
│   │   ├── storage/
│   │   │   └── secure_storage.dart    ← Token storage
│   │   ├── theme/
│   │   │   └── app_theme.dart
│   │   └── utils/
│   │       ├── date_formatter.dart
│   │       └── currency_formatter.dart
│   ├── data/
│   │   ├── datasources/
│   │   │   ├── remote/
│   │   │   │   ├── auth_remote_datasource.dart
│   │   │   │   ├── booking_remote_datasource.dart
│   │   │   │   ├── venue_remote_datasource.dart
│   │   │   │   └── voucher_remote_datasource.dart
│   │   │   └── local/
│   │   │       └── auth_local_datasource.dart
│   │   ├── models/
│   │   │   ├── booking_model.dart
│   │   │   ├── user_model.dart
│   │   │   ├── venue_model.dart
│   │   │   └── slot_model.dart
│   │   └── repositories/
│   │       ├── auth_repository_impl.dart
│   │       ├── booking_repository_impl.dart
│   │       └── venue_repository_impl.dart
│   ├── domain/
│   │   ├── entities/
│   │   │   ├── booking_entity.dart
│   │   │   ├── user_entity.dart
│   │   │   └── venue_entity.dart
│   │   ├── repositories/
│   │   │   ├── auth_repository.dart
│   │   │   └── booking_repository.dart
│   │   └── usecases/
│   │       ├── auth/
│   │       │   ├── login_usecase.dart
│   │       │   └── register_usecase.dart
│   │       ├── booking/
│   │       │   ├── create_booking_usecase.dart
│   │       │   └── get_bookings_usecase.dart
│   │       └── venue/
│   │           └── get_venues_usecase.dart
│   ├── presentation/
│   │   ├── blocs/ (atau providers/)
│   │   │   ├── auth/
│   │   │   │   ├── auth_bloc.dart
│   │   │   │   ├── auth_event.dart
│   │   │   │   └── auth_state.dart
│   │   │   ├── booking/
│   │   │   │   └── booking_bloc.dart
│   │   │   └── venue/
│   │   │       └── venue_bloc.dart
│   │   ├── pages/
│   │   │   ├── auth/
│   │   │   │   ├── login_page.dart
│   │   │   │   ├── register_page.dart
│   │   │   │   └── verification_page.dart
│   │   │   ├── home/
│   │   │   │   └── home_page.dart
│   │   │   ├── venue/
│   │   │   │   ├── venue_list_page.dart
│   │   │   │   ├── venue_detail_page.dart
│   │   │   │   └── slot_selection_page.dart
│   │   │   ├── booking/
│   │   │   │   ├── checkout_page.dart
│   │   │   │   ├── booking_history_page.dart
│   │   │   │   ├── booking_detail_page.dart
│   │   │   │   └── qr_display_page.dart
│   │   │   ├── koorlap/
│   │   │   │   ├── koorlap_dashboard_page.dart
│   │   │   │   └── qr_scanner_page.dart
│   │   │   └── profile/
│   │   │       ├── profile_page.dart
│   │   │       └── edit_profile_page.dart
│   │   └── widgets/
│   │       ├── common/
│   │       │   ├── custom_button.dart
│   │       │   ├── custom_text_field.dart
│   │       │   └── loading_overlay.dart
│   │       ├── venue/
│   │       │   ├── venue_card.dart
│   │       │   └── slot_grid.dart
│   │       └── booking/
│   │           ├── booking_card.dart
│   │           └── qr_code_widget.dart
├── test/
│   ├── unit/
│   ├── widget/
│   └── integration/
├── android/
├── ios/
└── pubspec.yaml
```

---

## 43. Suggested REST API Endpoint List

### Auth Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| POST | `/api/v1/auth/register` | Registrasi pengguna baru | No |
| POST | `/api/v1/auth/login` | Login & dapatkan token | No |
| POST | `/api/v1/auth/logout` | Logout & invalidasi token | Yes |
| POST | `/api/v1/auth/refresh` | Refresh access token | Yes |
| POST | `/api/v1/auth/forgot-password` | Kirim reset link ke email | No |
| POST | `/api/v1/auth/reset-password` | Reset password dengan token | No |
| GET | `/api/v1/auth/verify-email/{hash}` | Verifikasi email | No |
| POST | `/api/v1/auth/resend-verification` | Kirim ulang email verifikasi | Yes |

### Profile Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/api/v1/profile` | Lihat profil sendiri | Yes |
| PUT | `/api/v1/profile` | Update profil | Yes |
| POST | `/api/v1/profile/avatar` | Upload foto profil | Yes |
| POST | `/api/v1/profile/fcm-token` | Daftarkan FCM token | Yes |
| POST | `/api/v1/profile/campus-verification` | Upload dokumen waban | Yes (waban) |
| GET | `/api/v1/profile/campus-verification` | Cek status verifikasi | Yes (waban) |

### Venue Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/api/v1/venues` | Daftar semua lapangan | No |
| GET | `/api/v1/venues/{id}` | Detail lapangan | No |
| GET | `/api/v1/venues/{id}/slots` | Slot lapangan per tanggal | No |
| GET | `/api/v1/venues/{id}/reviews` | Ulasan lapangan | No |
| POST | `/api/v1/venues/{id}/reviews` | Tambah ulasan | Yes |

### Booking Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/api/v1/bookings` | Riwayat booking saya | Yes |
| POST | `/api/v1/bookings` | Buat booking baru | Yes |
| GET | `/api/v1/bookings/{id}` | Detail booking | Yes |
| DELETE | `/api/v1/bookings/{id}` | Batalkan booking | Yes |
| GET | `/api/v1/bookings/{id}/qr` | QR Code booking | Yes |
| POST | `/api/v1/bookings/{id}/payment` | Inisiasi pembayaran | Yes |
| GET | `/api/v1/bookings/{id}/payment/status` | Cek status pembayaran | Yes |

### Voucher Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| POST | `/api/v1/vouchers/validate` | Validasi kode voucher | Yes |

### QR Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| POST | `/api/v1/qr/validate` | Validasi QR Code | Yes (koorlap) |

### Notification Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/api/v1/notifications` | Daftar notifikasi saya | Yes |
| PUT | `/api/v1/notifications/{id}/read` | Tandai dibaca | Yes |
| PUT | `/api/v1/notifications/read-all` | Tandai semua dibaca | Yes |

### Webhook Endpoints

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| POST | `/api/webhook/midtrans` | Terima notifikasi Midtrans | Signature |

### Admin Endpoints (Web)

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/admin/dashboard` | Dashboard admin | Admin |
| RESOURCE | `/admin/users` | CRUD users | Admin |
| GET | `/admin/users/pending-verification` | List verifikasi pending | Admin |
| POST | `/admin/users/{id}/verify` | Proses verifikasi waban | Admin |
| RESOURCE | `/admin/venues` | CRUD venues | Admin |
| RESOURCE | `/admin/vouchers` | CRUD vouchers | Admin |
| GET | `/admin/bookings` | Semua booking | Admin |
| GET | `/admin/reports/revenue` | Laporan revenue | Admin |
| GET | `/admin/reports/booking` | Laporan booking | Admin |

---

## 44. Suggested Database Tables

### DDL Overview

```sql
-- USERS
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  avatar VARCHAR(500),
  role ENUM('waban','umum','koorlap','admin') DEFAULT 'umum',
  is_campus_member BOOLEAN DEFAULT FALSE,
  is_active BOOLEAN DEFAULT TRUE,
  email_verified_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL
);

-- USER CAMPUS VERIFICATIONS
CREATE TABLE user_campus_verifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  identity_number VARCHAR(50) NOT NULL,
  identity_type ENUM('nim','nip','nidn') NOT NULL,
  document_path VARCHAR(500) NOT NULL,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  reviewed_by BIGINT UNSIGNED NULL,
  reviewed_at TIMESTAMP NULL,
  rejection_reason TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- VENUES
CREATE TABLE venues (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  sport_type ENUM('futsal','badminton','basket','voli','tenis') NOT NULL,
  description TEXT,
  location TEXT,
  facilities JSON,
  managed_by BIGINT UNSIGNED NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  FOREIGN KEY (managed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- VENUE SLOTS
CREATE TABLE venue_slots (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  venue_id BIGINT UNSIGNED NOT NULL,
  day_of_week TINYINT NOT NULL COMMENT '0=Sunday, 6=Saturday',
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  price_normal DECIMAL(10,2) NOT NULL,
  price_campus DECIMAL(10,2) NOT NULL,
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE,
  UNIQUE KEY unique_slot (venue_id, day_of_week, start_time, end_time)
);

-- BOOKINGS
CREATE TABLE bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_code VARCHAR(50) UNIQUE NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  venue_id BIGINT UNSIGNED NOT NULL,
  slot_id BIGINT UNSIGNED NOT NULL,
  booking_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  duration_hours DECIMAL(4,2) NOT NULL,
  price_per_hour DECIMAL(10,2) NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  discount_amount DECIMAL(10,2) DEFAULT 0,
  final_price DECIMAL(10,2) NOT NULL,
  voucher_code VARCHAR(50) NULL,
  is_campus_price BOOLEAN DEFAULT FALSE,
  status ENUM('pending_payment','confirmed','checked_in','completed','cancelled','expired','failed') DEFAULT 'pending_payment',
  cancelled_at TIMESTAMP NULL,
  cancellation_reason TEXT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_user_bookings (user_id, status),
  INDEX idx_venue_date (venue_id, booking_date, status),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (venue_id) REFERENCES venues(id),
  FOREIGN KEY (slot_id) REFERENCES venue_slots(id)
);

-- BOOKING PAYMENTS
CREATE TABLE booking_payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED NOT NULL,
  payment_method VARCHAR(50),
  payment_gateway VARCHAR(50) DEFAULT 'midtrans',
  gateway_order_id VARCHAR(100) UNIQUE,
  gateway_transaction_id VARCHAR(100),
  snap_token VARCHAR(500),
  amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','success','failed','expired','refunded') DEFAULT 'pending',
  paid_at TIMESTAMP NULL,
  expired_at TIMESTAMP NULL,
  gateway_response JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- BOOKING QR CODES
CREATE TABLE booking_qr_codes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id BIGINT UNSIGNED UNIQUE NOT NULL,
  token VARCHAR(255) UNIQUE NOT NULL,
  qr_image_path VARCHAR(500),
  is_used BOOLEAN DEFAULT FALSE,
  scanned_at TIMESTAMP NULL,
  scanned_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id),
  FOREIGN KEY (scanned_by) REFERENCES users(id) ON DELETE SET NULL
);

-- VOUCHERS
CREATE TABLE vouchers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  discount_type ENUM('percentage','fixed') NOT NULL,
  discount_value DECIMAL(10,2) NOT NULL,
  min_booking_amount DECIMAL(10,2) DEFAULT 0,
  max_discount_amount DECIMAL(10,2) NULL,
  max_total_usage INT UNSIGNED DEFAULT 0 COMMENT '0 = unlimited',
  max_per_user INT UNSIGNED DEFAULT 1,
  valid_from DATE NOT NULL,
  valid_until DATE NOT NULL,
  target_role ENUM('all','waban','umum') DEFAULT 'all',
  is_active BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- VOUCHER USAGES
CREATE TABLE voucher_usages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  voucher_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  booking_id BIGINT UNSIGNED NOT NULL,
  discount_amount DECIMAL(10,2) NOT NULL,
  used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (voucher_id) REFERENCES vouchers(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- REVIEWS
CREATE TABLE reviews (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  booking_id BIGINT UNSIGNED UNIQUE NOT NULL,
  venue_id BIGINT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  is_visible BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (booking_id) REFERENCES bookings(id),
  FOREIGN KEY (venue_id) REFERENCES venues(id)
);

-- USER FCM TOKENS
CREATE TABLE user_fcm_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  token TEXT NOT NULL,
  device_type ENUM('android','ios','web') DEFAULT 'android',
  is_active BOOLEAN DEFAULT TRUE,
  last_used_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- NOTIFICATIONS
CREATE TABLE notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(100) NOT NULL,
  title VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  data JSON,
  channel ENUM('push','email','in_app') DEFAULT 'push',
  status ENUM('pending','sent','failed','read') DEFAULT 'pending',
  sent_at TIMESTAMP NULL,
  read_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ACTIVITY LOGS
CREATE TABLE activity_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  model_type VARCHAR(100),
  model_id BIGINT UNSIGNED,
  description TEXT,
  old_values JSON,
  new_values JSON,
  ip_address VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

---

## 45. Suggested Admin Panel Features

| Modul | Fitur Spesifik |
|---|---|
| **Dashboard** | KPI cards, revenue chart, occupancy chart, booking terbaru, aktivitas log |
| **Lapangan** | CRUD lapangan, upload foto galeri, atur slot, atur harga peak/off-peak, nonaktifkan |
| **Booking** | Tabel semua booking (filter: lapangan, status, tanggal, user), detail booking, batalkan manual |
| **Pengguna** | Tabel users (filter role, status), detail user, ganti role, nonaktifkan, lihat riwayat booking |
| **Verifikasi Waban** | Antrian permohonan, preview dokumen identitas, approve/reject + alasan |
| **Voucher** | CRUD voucher, tracking penggunaan, preview diskon |
| **Keuangan** | Revenue report (per hari/minggu/bulan/lapangan), tabel transaksi, export Excel/PDF |
| **Notifikasi** | Form broadcast push notif ke semua/segmen pengguna |
| **Log** | Tabel activity log, filter aksi, filter user, filter tanggal |
| **Pengaturan** | Nama sistem, jam operasional default, kebijakan pembatalan, info kontak |

---

## 46. Suggested Mobile Features

### Fitur Aplikasi Mobile (Flutter)

| Fitur | Deskripsi |
|---|---|
| **Splash & Onboarding** | Animasi splash, 3 slide onboarding, lanjut ke login |
| **Biometric Login** | Login dengan fingerprint/Face ID setelah login pertama |
| **Home Screen** | Banner promo, shortcut olahraga, lapangan populer, booking aktif saya |
| **Venue Discovery** | Filter jenis olahraga, search nama, sorting popularitas/harga |
| **Venue Detail** | Foto carousel, fasilitas, lokasi (maps link), rating, review list |
| **Slot Picker** | Kalender interaktif, grid slot dengan color coding, multi-slot selection |
| **Checkout** | Summary booking, input voucher, kalkulasi real-time |
| **Payment** | Integrasi Midtrans SDK, status pembayaran real-time |
| **QR Display** | QR Code full-screen, brightness auto-increase, share QR |
| **Booking History** | Tab: Aktif / Selesai / Dibatalkan, pull-to-refresh |
| **QR Scanner (Koorlap)** | Camera scan, vibrate on scan, hasil validasi dengan animasi |
| **Push Notification** | Terima notif di foreground & background, tap-to-navigate |
| **Dark Mode** | Support light/dark mode otomatis |
| **Offline Graceful** | Tampil pesan "Tidak ada koneksi" saat offline |
| **Profile** | Lihat & edit profil, upload foto, status verifikasi waban |

---

## 47. Suggested Laravel Packages

```json
// composer.json (require)
{
  "laravel/framework": "^13.0",
  "laravel/sanctum": "^4.0",
  "laravel/horizon": "^5.0",
  "laravel/telescope": "^5.0",
  "laravel/socialite": "^5.0",
  "spatie/laravel-permission": "^6.0",
  "spatie/laravel-activity-log": "^4.0",
  "spatie/laravel-backup": "^8.0",
  "spatie/laravel-media-library": "^11.0",
  "chillerlan/php-qrcode": "^5.0",
  "midtrans/midtrans-php": "^2.0",
  "kreait/laravel-firebase": "^5.0",
  "maatwebsite/excel": "^3.1",
  "barryvdh/laravel-dompdf": "^3.0",
  "intervention/image": "^3.0",
  "guzzlehttp/guzzle": "^7.0",
  "predis/predis": "^2.0"
}
```

| Package | Fungsi |
|---|---|
| `laravel/sanctum` | API token authentication |
| `laravel/horizon` | Queue monitoring dashboard |
| `laravel/telescope` | Debug & profiling (dev) |
| `spatie/laravel-permission` | RBAC (Role & Permission) |
| `spatie/laravel-activity-log` | Activity logging otomatis |
| `spatie/laravel-backup` | Automated backup |
| `spatie/laravel-media-library` | File & image management |
| `chillerlan/php-qrcode` | Generate QR Code |
| `midtrans/midtrans-php` | Midtrans payment gateway |
| `kreait/laravel-firebase` | Firebase FCM integration |
| `maatwebsite/excel` | Export Excel |
| `barryvdh/laravel-dompdf` | Export PDF |
| `intervention/image` | Image processing/resize |

---

## 48. Suggested Flutter Packages

```yaml
# pubspec.yaml dependencies
dependencies:
  flutter_bloc: ^8.1.3       # State management
  dio: ^5.4.0                 # HTTP client
  get_it: ^7.6.7              # Dependency injection
  equatable: ^2.0.5           # Value equality
  flutter_secure_storage: ^9.0.0  # Secure token storage
  shared_preferences: ^2.2.2  # Local preferences
  mobile_scanner: ^5.0.0     # QR code scanner
  qr_flutter: ^4.1.0         # QR code display
  firebase_core: ^3.0.0      # Firebase
  firebase_messaging: ^15.0.0 # Push notifications
  flutter_local_notifications: ^17.0.0  # Local notif
  image_picker: ^1.0.7        # Camera/gallery
  cached_network_image: ^3.3.1  # Image caching
  intl: ^0.19.0               # Internationalization
  url_launcher: ^6.3.0        # Open URLs/maps
  connectivity_plus: ^6.0.3   # Network check
  shimmer: ^3.0.0             # Loading skeleton
  fl_chart: ^0.68.0          # Charts (koorlap)
  table_calendar: ^3.1.0     # Kalender booking
  lottie: ^3.1.0             # Animasi Lottie
  webview_flutter: ^4.7.0    # Midtrans WebView
  package_info_plus: ^8.0.0  # App version info
  local_auth: ^2.2.0         # Biometric auth
```

---

## 49. Deployment Recommendation

### Production Stack

```
┌─────────────────────────────────────────────────────────────────────┐
│                     PRODUCTION ENVIRONMENT                           │
├─────────────────────────────────────────────────────────────────────┤
│  Domain: csbs.kampus.ac.id                                          │
│  SSL: Let's Encrypt (auto-renew via Certbot)                        │
│─────────────────────────────────────────────────────────────────────│
│  Cloud Provider: VPS (DigitalOcean / Vultr / IDCloudHost)          │
│  Server OS: Ubuntu 24.04 LTS                                        │
│  CPU: 4 vCPU | RAM: 8GB | Storage: 100GB SSD                       │
│─────────────────────────────────────────────────────────────────────│
│  Web Server: Nginx 1.24                                             │
│  PHP: 8.3 + PHP-FPM                                                │
│  Database: MySQL 8.0                                                │
│  Cache/Queue: Redis 7                                               │
│  Process Manager: Supervisor (queue workers)                        │
│─────────────────────────────────────────────────────────────────────│
│  Mobile: Google Play Store + Apple App Store                        │
│─────────────────────────────────────────────────────────────────────│
│  Storage: MinIO (self-hosted) atau AWS S3                           │
│  Email: Mailgun / Brevo (Sendinblue)                                │
│  Monitoring: UptimeRobot + Sentry                                   │
└─────────────────────────────────────────────────────────────────────┘
```

### Nginx Config (Ringkasan)

```nginx
server {
    listen 443 ssl http2;
    server_name csbs.kampus.ac.id;
    root /var/www/csbs/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/csbs.kampus.ac.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/csbs.kampus.ac.id/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Strict-Transport-Security "max-age=31536000";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    client_max_body_size 10M;
}
```

### Supervisor Config (Queue Worker)

```ini
[program:csbs-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/csbs/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=3
redirect_stderr=true
stdout_logfile=/var/log/csbs-worker.log
```

---

## 50. Testing Strategy

### Testing Pyramid

```
          ▲
         /E2E\          ← Sedikit, mahal, lambat
        /──────\
       / Integ. \       ← Sedang, API contract tests
      /──────────\
     /  Unit Tests \    ← Banyak, cepat, murah
    ──────────────────
```

### Testing Plan

| Layer | Tools | Cakupan Target |
|---|---|---|
| **Unit Test (PHP)** | PHPUnit | Service classes, helper functions | ≥ 80% |
| **Feature Test (PHP)** | PHPUnit + HTTP tests | Semua API endpoint | ≥ 90% |
| **Database Test** | PHPUnit RefreshDatabase | Model relationships, seeders |  |
| **Widget Test (Flutter)** | Flutter Test | UI components, forms |  |
| **Integration Test (Flutter)** | flutter_test + Mockito | Repository & BLoC tests |  |
| **API Contract Test** | Postman Newman | Semua endpoint per sprint |  |
| **Load Test** | k6 | Simulate 500 concurrent users |  |
| **Security Test** | OWASP ZAP | Scan kerentanan umum |  |
| **UAT** | Manual | Semua happy path & critical flows |  |

### Sample PHPUnit Test

```php
// tests/Feature/Booking/CreateBookingTest.php

public function test_user_can_create_booking_for_available_slot(): void
{
    $user = User::factory()->create(['role' => 'waban', 'is_campus_member' => true]);
    $venue = Venue::factory()->create(['is_active' => true]);
    $slot = VenueSlot::factory()->for($venue)->create();

    $response = $this->actingAs($user)->postJson('/api/v1/bookings', [
        'venue_id' => $venue->id,
        'slot_id' => $slot->id,
        'booking_date' => now()->addDays(2)->format('Y-m-d'),
    ]);

    $response->assertStatus(201)
             ->assertJsonPath('data.status', 'pending_payment')
             ->assertJsonPath('success', true);
}

public function test_booking_fails_when_slot_already_booked(): void
{
    // ... setup conflict booking ...
    $response = $this->actingAs($user)->postJson('/api/v1/bookings', [...]);
    $response->assertStatus(409);
}
```

---

## 51. CI/CD Recommendation

### GitHub Actions Pipeline

```yaml
# .github/workflows/ci.yml
name: CSBS CI Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test-backend:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: csbs_test
          MYSQL_ROOT_PASSWORD: root
      redis:
        image: redis:7
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.3
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: mbstring, xml, mysql, redis
      - run: composer install --no-interaction
      - run: cp .env.testing .env
      - run: php artisan key:generate
      - run: php artisan migrate --force
      - run: php artisan test --coverage

  test-flutter:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: subosito/flutter-action@v2
        with:
          flutter-version: '3.x'
      - run: flutter pub get
      - run: flutter test
      - run: flutter build apk --release

  deploy-production:
    needs: [test-backend, test-flutter]
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/csbs
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            sudo supervisorctl restart csbs-worker:*
```

### Branching Strategy

```
main ──────────── Production (protected, auto-deploy)
  │
develop ─────────── Staging (merge from feature branches)
  │
feature/xxx ─────── Individual features
  │
hotfix/xxx ──────── Emergency fixes (merge to main & develop)
```

---

## 52. Conclusion

### Ringkasan Dokumen

**Campus Sports Booking System (CSBS)** adalah solusi digital komprehensif yang dirancang untuk mentransformasi pengelolaan fasilitas olahraga kampus dari sistem manual yang tidak efisien menjadi platform digital modern yang terintegrasi, real-time, dan scalable.

### Keunggulan Kompetitif CSBS

1. **Diferensiasi Harga Otomatis** — Sistem pertama yang memberikan harga khusus waban secara otomatis berbasis verifikasi identitas kampus
2. **QR Validation System** — Validasi kehadiran digital yang akurat dan tidak bisa dipalsukan
3. **Real-time Availability** — Tidak ada lagi booking ganda atau ketidakpastian jadwal
4. **Mobile-First Experience** — Dibangun untuk generasi smartphone-first
5. **Analytics Terintegrasi** — Data-driven decision making untuk manajemen

### Next Steps

| # | Aksi | PIC | Timeline |
|---|---|---|---|
| 1 | Review & approve PRD ini | Product Owner | Minggu 1 |
| 2 | Setup development environment | Tim Backend | Minggu 1 |
| 3 | Finalisasi design system (Figma) | Tim UI/UX | Minggu 1-2 |
| 4 | Kickoff Sprint 1 | Tim Engineering | Minggu 2 |
| 5 | Setup akun Midtrans & Firebase | DevOps | Minggu 1 |
| 6 | User research & prototype validation | Tim Product | Minggu 2-3 |

---

*Dokumen ini adalah living document yang akan diperbarui seiring perkembangan produk.*  
*Versi terbaru selalu tersedia di repository internal tim.*

---
**© 2025 Campus Sports Booking System — Internal Documentation**
