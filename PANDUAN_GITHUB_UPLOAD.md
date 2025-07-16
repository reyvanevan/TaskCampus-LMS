PANDUAN UPLOAD KE GITHUB

Langkah-langkah untuk Upload TaskCampus ke GitHub Repository

PERSIAPAN PROJECT

1. Pastikan semua file sudah siap
   - README.md sudah lengkap ✓
   - .gitignore sudah ada ✓  
   - .env.example sudah ada
   - Project bersih dari file cache

2. Cek dan bersihkan file yang tidak perlu
   - Hapus file log dan cache
   - Pastikan tidak ada file sensitif (.env)

LANGKAH UPLOAD KE GITHUB

STEP 1: Buat Repository di GitHub (Manual)
1. Login ke https://github.com
2. Click tombol "New" atau "+" → "New repository"
3. Isi form repository:
   - Repository name: TaskCampus-LMS
   - Description: Learning Management System built with Laravel 9
   - Set sebagai Public (agar bisa dilihat)
   - JANGAN centang "Add a README file" (karena sudah ada)
   - JANGAN centang "Add .gitignore" (karena sudah ada)
   - Click "Create repository"

STEP 2: Initialize Git di Project (Terminal)
Jalankan command ini di direktori TaskCampus:

git init
git add .
git commit -m "Initial commit: TaskCampus LMS with Laravel 9"

STEP 3: Connect ke GitHub Repository
Ganti [username] dengan username GitHub Anda:

git branch -M main
git remote add origin https://github.com/[username]/TaskCampus-LMS.git
git push -u origin main

CONTOH LENGKAP (Ganti reyvanpurnama dengan username Anda):

git init
git add .
git commit -m "Initial commit: TaskCampus LMS with Laravel 9

- Multi-role authentication system (Admin, Lecturer, Student)
- 10 table database with complex relationships
- Excel import/export functionality  
- File upload/download system
- Rubric-based grading system
- Real-time notifications
- Built with Laravel 9, Tailwind CSS, Alpine.js"

git branch -M main
git remote add origin https://github.com/reyvanpurnama/TaskCampus-LMS.git
git push -u origin main

STEP 4: Verify Upload
1. Refresh halaman GitHub repository
2. Pastikan semua file terupload
3. Cek README.md tampil dengan baik

TIPS TAMBAHAN

File yang Perlu Diperiksa Sebelum Upload:
✓ README.md - Dokumentasi lengkap
✓ .env.example - Template environment
✓ composer.json - Dependencies
✓ package.json - Frontend dependencies  
✓ database/migrations/ - Database schema
✓ app/ - Source code aplikasi
✓ resources/ - Views dan assets
✓ routes/ - Routing definitions

File yang TIDAK Boleh Diupload:
✗ .env - File environment (ada password)
✗ vendor/ - Dependencies (akan di-ignore)
✗ node_modules/ - Frontend dependencies (akan di-ignore)
✗ storage/logs/ - Log files
✗ public/storage - Symlink storage

PESAN COMMIT YANG BAIK

Initial commit:
"Initial commit: TaskCampus LMS with Laravel 9"

Update berikutnya:
"Add feature: Excel import validation"
"Fix: Authentication middleware bug" 
"Update: README documentation"
"Improve: Database relationships"

DESCRIPTION REPOSITORY GITHUB

Tulis description yang menarik:

"🎓 TaskCampus - Learning Management System

Modern LMS built with Laravel 9 featuring multi-role authentication, Excel import/export, rubric-based grading, and real-time notifications. Perfect for educational institutions.

🔧 Tech Stack: Laravel 9, Tailwind CSS, Alpine.js, MySQL
👥 Features: Admin/Lecturer/Student roles, Assignment management, File uploads
📊 Advanced: 10-table relationships, Detailed scoring system"

TROUBLESHOOTING

Jika ada error saat push:
1. Pastikan internet stabil
2. Cek username/password GitHub benar
3. Gunakan personal access token jika perlu
4. Pastikan repository name sama persis

Jika file terlalu besar:
1. Cek apakah ada file yang seharusnya di-ignore
2. Hapus file cache dan log
3. Compress file jika perlu

AFTER UPLOAD

Setelah berhasil upload:
1. Add topics/tags di GitHub: laravel, lms, php, education
2. Add description yang menarik
3. Enable Issues jika ingin feedback
4. Buat branch untuk development selanjutnya

Repository siap untuk:
- Portfolio showcase
- Thesis presentation
- Collaboration
- Job application reference

SELAMAT! Project TaskCampus sudah live di GitHub! 🚀
