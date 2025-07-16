TaskCampus Learning Management System

Kelompok 9
220102043 - M Reyvan Purnama
220102011 - Aldi Zaidahanis
220102029 - Farhan Fahrudin

Deskripsi Proyek

TaskCampus adalah sistem Learning Management System (LMS) berbasis web yang dirancang untuk memfasilitasi pengelolaan pembelajaran di lingkungan akademik. Sistem ini dikembangkan menggunakan framework Laravel 9 dengan PHP 8.0+ dan menyediakan interface yang user-friendly untuk admin, dosen, dan mahasiswa.

Latar Belakang

Dalam era digital saat ini, institusi pendidikan memerlukan sistem yang dapat mengotomatisasi proses pembelajaran. Masalah yang sering dihadapi adalah:

- Pengelolaan tugas dan pengumpulan yang masih manual
- Komunikasi antara dosen dan mahasiswa yang tidak real-time
- Data mahasiswa, mata kuliah, dan tugas yang tersebar di berbagai platform
- Proses penilaian dan feedback yang memakan waktu lama
- Kesulitan dalam monitoring progress pembelajaran

TaskCampus hadir sebagai solusi terintegrasi untuk mengatasi permasalahan tersebut.

Kriteria Tugas Akhir yang Dipenuhi

1. Minimal 4 Tabel Berelasi
Sistem menggunakan 10 tabel yang saling berelasi:
- users - Data pengguna multi-role (admin, lecturer, student)
- courses - Mata kuliah dengan informasi lengkap
- assignments - Tugas per mata kuliah
- submissions - Pengumpulan tugas oleh mahasiswa
- semesters - Periode akademik
- enrollments - Pendaftaran mahasiswa ke mata kuliah (many-to-many pivot)
- rubrics - Rubrik penilaian untuk setiap tugas
- rubric_criterias - Kriteria detail dalam rubrik
- submission_scores - Skor penilaian per kriteria (detail grading)
- notifications - Sistem notifikasi terintegrasi

2. Upload Data dari Excel
Fitur import/export Excel untuk:
- Import data mahasiswa massal dengan validation
- Import data mata kuliah dengan error handling
- Export laporan nilai dalam format Excel
- Template format yang benar untuk import
- Bulk processing dengan progress feedback

3. Autentikasi dan Otorisasi
Sistem multi-role dengan 3 level akses:
- Admin: Pengelolaan user, sistem, import/export data
- Dosen: Pengelolaan course, assignment, grading, rubric
- Mahasiswa: Enrollment, submission tugas, view grades

Keamanan yang diimplementasi:
- Password hashing dengan bcrypt
- Session-based authentication
- CSRF protection untuk semua form
- Role-based middleware protection
- Authorization check pada setiap endpoint

4. Framework Laravel
Menggunakan Laravel 9 dengan:
- PHP 8.0+ sebagai backend language
- MySQL database dengan Eloquent ORM
- Blade templating untuk views
- Artisan commands untuk automation
- Migration dan seeding untuk database
- Middleware untuk authorization
- Service layer untuk business logic

Fitur Utama

1. Manajemen User Multi-Role
- Admin: Pengelolaan user, monitoring sistem, import/export data
- Dosen: Pengelolaan mata kuliah, tugas, dan penilaian
- Mahasiswa: Mengikuti mata kuliah, submit tugas, melihat nilai

2. Sistem Mata Kuliah dan Tugas
- Pengelolaan mata kuliah berdasarkan semester
- Upload tugas dengan file attachment
- Sistem rubrik penilaian multi-criteria
- Grading terintegrasi dengan feedback detail

3. Import/Export Data Excel
- Import data mahasiswa secara massal via Excel/CSV
- Import data mata kuliah dengan validasi
- Export laporan nilai dan data mahasiswa
- Template download untuk format yang benar

4. Sistem Notifikasi Real-time
- Notifikasi assignment baru
- Notifikasi nilai telah diupload
- Notifikasi enrollment course
- Mark as read functionality

5. File Management
- Upload file tugas dengan keamanan
- Download file dengan nama asli
- Organisasi file berdasarkan course dan assignment
- Validasi tipe dan ukuran file

6. Advanced Features
- Force delete dengan konfirmasi untuk data sensitif
- Soft delete untuk data penting
- Audit trail dengan timestamps
- Responsive design untuk mobile

Teknologi yang Digunakan

Backend
- Laravel 9 (PHP Framework)
- PHP 8.0+
- MySQL Database
- Composer (Dependency Management)

Frontend
- Blade Templates (Laravel View Engine)
- Tailwind CSS (Styling Framework)
- Alpine.js (Reactive Components)
- JavaScript (Client-side Interaction)

Libraries dan Tools
- Maatwebsite/Excel - Import/Export Excel functionality
- Laravel Sanctum - Authentication system
- Vite - Asset building dan compilation
- NPM - Package management untuk frontend

Struktur Database

Sistem menggunakan 10 tabel yang saling berelasi:

Tabel Utama

1. users - Data pengguna dengan multi-role (admin, lecturer, student)
2. courses - Data mata kuliah dengan informasi lengkap
3. assignments - Data tugas per mata kuliah
4. submissions - Pengumpulan tugas oleh mahasiswa
5. semesters - Data semester akademik
6. enrollments - Pendaftaran mahasiswa ke mata kuliah
7. rubrics - Rubrik penilaian untuk setiap tugas
8. rubric_criterias - Kriteria detail dalam rubrik
9. submission_scores - Skor penilaian per kriteria
10. notifications - Sistem notifikasi terintegrasi

Relasi Database

users (1:M) enrollments (M:1) courses (M:1) semesters
users (1:M) submissions (M:1) assignments (M:1) courses
assignments (1:1) rubrics (1:M) rubric_criterias
submissions (1:M) submission_scores (M:1) rubric_criterias
users (1:M) notifications

Instalasi dan Setup

Requirement Sistem
- PHP 8.0 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Composer
- Node.js dan NPM
- Web server (Apache/Nginx)

Langkah Instalasi

1. Clone repository
git clone [repository-url]
cd TaskCampus

2. Install dependencies PHP
composer install

3. Install dependencies Node.js
npm install

4. Copy environment file
copy .env.example .env

5. Generate application key
php artisan key:generate

6. Setup database di file .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskcampus
DB_USERNAME=root
DB_PASSWORD=

7. Jalankan migration dan seeder
php artisan migrate
php artisan db:seed

8. Build assets
npm run build

9. Jalankan aplikasi
php artisan serve

Default User Account

Setelah menjalankan seeder, tersedia akun default:

Admin
- Email: admin@taskcampus.com
- Password: password

Dosen
- Email: lecturer@taskcampus.com
- Password: password

Mahasiswa
- Email: student@taskcampus.com
- Password: password

Panduan Penggunaan

Untuk Admin

1. User Management:
   - Login menggunakan akun admin
   - Akses menu User Management untuk CRUD user
   - Buat akun lecturer dan manage role
   - Monitor sistem melalui dashboard admin
   - Kelola data semester dan course secara global

2. Import/Export Data:
   - Import data mahasiswa via Excel di menu Import/Export
   - Download template format yang benar
   - Validasi data dan error handling
   - Export laporan dalam format Excel
   - Bulk operations untuk efficiency

3. System Administration:
   - Monitor seluruh aktivitas sistem
   - Manage semester dan periode akademik
   - Oversight semua course dan enrollment
   - Access ke semua fitur sistem

Untuk Dosen

1. Login menggunakan akun dosen
2. Buat mata kuliah baru dengan detail semester
3. Tambahkan tugas dengan rubrik penilaian
4. Upload file pendukung untuk tugas
5. Lakukan grading submission mahasiswa
6. Berikan feedback berdasarkan kriteria rubrik

Untuk Mahasiswa

1. Login menggunakan akun mahasiswa
2. Enroll ke mata kuliah yang tersedia
3. Lihat daftar tugas dan deadline
4. Submit tugas dengan upload file
5. Cek nilai dan feedback dari dosen
6. Terima notifikasi real-time

Fitur Import/Export Excel

Format Import Mahasiswa
File Excel harus memiliki kolom:
- name (Nama lengkap)
- email (Email unik)
- nim (Nomor Induk Mahasiswa)
- password (Password default)

Format Import Mata Kuliah
File Excel harus memiliki kolom:
- name (Nama mata kuliah)
- code (Kode mata kuliah)
- description (Deskripsi)
- semester_id (ID semester)
- lecturer_id (ID dosen pengampu)

Export Data
- Export daftar mahasiswa per mata kuliah
- Export laporan nilai dalam format Excel
- Export summary course dan enrollment

Sistem Rubrik Penilaian

Sistem rubrik memungkinkan dosen untuk:

1. Membuat rubrik dengan multiple criteria
2. Set bobot nilai untuk setiap criteria
3. Berikan nilai detail per criteria untuk setiap submission
4. Generate feedback otomatis berdasarkan rubrik
5. Transparansi penilaian untuk mahasiswa

Keamanan Sistem

Authentication dan Authorization
- Password hashing menggunakan bcrypt
- Session-based authentication
- CSRF protection untuk semua form
- Middleware role-based access control

File Security
- Validasi tipe file upload
- Pembatasan ukuran file
- Unique filename generation
- Authorization check untuk file access

Database Security
- SQL injection prevention dengan Eloquent ORM
- XSS protection dengan Blade templating
- Foreign key constraints untuk data integrity
- Soft delete untuk data penting

Testing

Unit Testing
php artisan test

Feature Testing
- Testing CRUD operations untuk semua entitas
- Testing authentication dan authorization
- Testing file upload/download functionality
- Testing import/export Excel features

Browser Testing
- UI/UX testing untuk semua role
- Responsive design testing
- Cross-browser compatibility

Performance Optimization

Database Optimization
- Proper indexing untuk query performance
- Eager loading untuk menghindari N+1 problem
- Database query optimization

Caching Strategy
- Route caching
- View caching
- Configuration caching
- Query result caching

Asset Optimization
- CSS dan JavaScript minification
- Image optimization
- Lazy loading untuk performance

Deployment

Production Environment
1. Setup web server (Apache/Nginx)
2. Install PHP 8.0+ dan extensions
3. Setup MySQL database
4. Clone dan configure aplikasi
5. Set proper file permissions
6. Setup SSL certificate
7. Configure backup strategy

Environment Configuration
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

Troubleshooting

Common Issues

1. Permission Error
chmod -R 755 storage
chmod -R 755 bootstrap/cache

2. Cache Issues
php artisan cache:clear
php artisan config:clear
php artisan view:clear

3. Database Connection Error
- Periksa konfigurasi database di .env
- Pastikan MySQL service berjalan
- Cek username dan password database

Kontribusi dan Development

Code Style
- Follow PSR-12 coding standard
- Use meaningful variable dan function names
- Add comments untuk complex logic
- Write unit tests untuk new features

Git Workflow
- Create feature branch untuk development
- Write clear commit messages
- Create pull request untuk review
- Merge setelah testing passed

Lisensi

Proyek ini dikembangkan untuk keperluan akademik dan dapat digunakan sebagai referensi pembelajaran.

Kontak

Untuk pertanyaan atau dukungan teknis, silakan hubungi developer melalui email atau buat issue di repository.

Changelog

Version 1.0.0
- Initial release dengan semua fitur dasar
- Multi-role authentication system
- Import/export Excel functionality
- Real-time notification system
- Rubric-based grading system
- File management system
- Force delete dengan konfirmasi
- Responsive UI dengan Tailwind CSS

Dokumentasi Tambahan

Untuk dokumentasi teknis lebih detail, silakan lihat:
- FINAL_DOCUMENTATION.md - Dokumentasi teknis lengkap
- TESTING_GUIDE_COMPLETE.md - Panduan testing aplikasi
- USER_MANAGEMENT_GUIDE.md - Panduan manajemen user

Panduan Presentasi dan Demo

Quick Demo Script (10 Menit)

1. Database & Relasi Demo (2 menit)
php artisan tinker
$user = App\Models\User::find(1);
$user->enrolledCourses;  // Show many-to-many relationship
$user->submissions;      // Show one-to-many relationship

2. Import Excel Demo (3 menit)
- Login sebagai admin
- Akses menu Import/Export
- Download sample data mahasiswa
- Upload file CSV dan tunjukkan validasi

3. Authentication Demo (2 menit)
- Demo login dengan role berbeda (admin, lecturer, student)
- Tunjukkan redirect otomatis berdasarkan role
- Demo unauthorized access (403 error)

4. Authorization Demo (3 menit)
- Login sebagai student, coba akses admin URL
- Tunjukkan middleware protection
- Demo conditional menu berdasarkan role

Referensi Kode untuk Presentasi

Database Relasi (10 Tabel):
- File: app/Models/User.php (baris 94-142)
- Demo: User relationships (teachingCourses, enrolledCourses, submissions)

Import Excel:
- File: app/Http/Controllers/ImportController.php (baris 67-108)
- Route: routes/web.php (baris 105-115)
- Demo: CSV upload dengan validation dan error handling

Authentication:
- File: app/Models/User.php (baris 72-86)
- Demo: Role checking methods (isAdmin, isLecturer, isStudent)

Authorization:
- File: app/Http/Middleware/CheckRole.php (baris 16-30)
- Demo: Dynamic role-based middleware protection


Testing Checklist

Fungsional Testing:
- Authentication untuk semua role
- CRUD operations untuk semua entitas
- File upload/download functionality
- Import/export Excel features
- Authorization pada setiap endpoint

Database Testing:
- Foreign key constraints
- Cascade delete operations
- Data integrity validation
- Complex relationship queries

Security Testing:
- CSRF protection
- SQL injection prevention
- XSS protection
- File upload security

Changelog

Version 1.0.0
- Initial release dengan semua fitur dasar
- Multi-role authentication system
- Import/export Excel functionality
- Real-time notification system
- Rubric-based grading system
- File management system
- Force delete dengan konfirmasi
- Responsive UI dengan Tailwind CSS
- 10 tabel database dengan relasi kompleks
- Detailed grading per criteria dengan submission_scores

Kontak dan Support

Untuk pertanyaan atau dukungan teknis, silakan hubungi developer melalui email atau buat issue di repository.

Developed by Kelompok 9:
- 220102043 - M Reyvan Purnama
- 220102011 - Aldi Zaidahanis  
- 220102029 - Farhan Fahrudin

Acknowledgments

Terima kasih kepada:
- Laravel Framework untuk foundation yang solid
- Tailwind CSS untuk styling framework yang modern
- Maatwebsite/Excel untuk Excel functionality
- Alpine.js untuk reactive components
- Komunitas open source yang telah berkontribusi

---

Dikembangkan dengan menggunakan Laravel 9

Dokumentasi lengkap dalam satu file untuk kemudahan akses dan maintenance.
