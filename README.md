# 🏫 Primary School Management System

A practical web-based school management system built for **Cambodian government primary schools**. Designed to follow real school workflows — not generic LMS patterns.

---

## 📋 Overview

This system replaces paper-based and Excel-based school records with a clean, teacher-friendly digital workflow. It handles attendance, score entry, monthly reports, semester reports, and annual reports — all following the official Cambodian government school structure.

---

## ⚙️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 / PHP 8.3+ |
| Frontend | Blade Templates + TailwindCSS |
| Database | MySQL |
| Authentication | Laravel Breeze |
| Architecture | Simple MVC |

---

## 👥 User Roles

### Admin
- Manage academic years, grades, subjects
- Manage teachers, students, classes, enrollments
- View and manage all class reports
- Lock / unlock score sheets
- Manage attendance sessions

### Teacher
- Access assigned classes only
- Take attendance for assigned classes
- Enter examination scores (monthly & semester)
- View monthly, semester, and annual reports

---

## 📦 Modules

| # | Module | Status |
|---|---|---|
| 1 | Authentication | ✅ Done |
| 2 | Academic Years | ✅ Done |
| 3 | Grades | ✅ Done |
| 4 | Subjects | ✅ Done |
| 5 | Teachers | ✅ Done |
| 6 | Students | ✅ Done |
| 7 | Classes | ✅ Done |
| 8 | Enrollments | ✅ Done |
| 9 | Teacher Assignments | ✅ Done |
| 10 | Attendance | ✅ Done |
| 11 | Examination Scores | ✅ Done |
| 12 | Monthly Reports | ✅ Done |
| 13 | Semester Reports | ✅ Done |
| 14 | Annual Reports | ✅ Done |
| 15 | Printing & Exports | 🔄 Planned |

---

## 🗄️ Database Structure

### Core Tables
- `users` — system accounts
- `teachers` — teacher profiles
- `students` — student profiles
- `academic_years` — school years
- `grades` — grade levels (Grade 1–6)
- `classes` — class per grade per year
- `enrollments` — students enrolled in classes
- `class_teachers` — teacher assignments to classes
- `attendance_sessions` — attendance date records
- `attendances` — per-student attendance marks

### Score & Report Tables
- `monthly_scores` — per-student per-subject monthly scores
- `semester_scores` — averaged semester scores with rank
- `annual_scores` — yearly aggregated scores
- `monthly_report_locks` — admin lock control for monthly sheets
- `semester_report_locks` — admin lock control for semester sheets
- `annual_report_locks` — admin lock control for annual sheets

---

## 🚀 Local Setup

### Requirements
- PHP 8.3+
- Composer
- MySQL
- Node.js + NPM
- Laravel 13

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/Sophireak/student-management-system.git
cd student-management-system

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies
npm install && npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Configure your database in .env
DB_DATABASE=school_management
DB_USERNAME=root
DB_PASSWORD=

# 7. Run migrations
php artisan migrate

# 8. Seed initial data (optional)
php artisan db:seed

# 9. Start development server
php artisan serve
```

Visit `http://127.0.0.1:8000`

---

## 📁 Route Structure

Routes are split into 3 files for clarity:

```
routes/
├── web.php        # Public root + requires admin & teacher routes
├── admin.php      # All admin-only routes
└── teacher.php    # All teacher-only routes
```

---

## 🔒 Security

- Role-based middleware (`admin`, `teacher`)
- Teachers can only access their assigned classes
- Admins can lock score sheets to prevent edits
- CSRF protection on all forms
- Soft deletes on sensitive records

---

## 🏗️ Architecture Notes

- Students are **never** attached directly to classes
- Correct flow: `students → enrollments → classes`
- This enables yearly promotion, transfer history, and historical reports
- Reports behave like **digital Excel sheets** — spreadsheet-style inline editing with bulk save

---

## 👨‍💻 Development Team

> Add your team members here.

---

## 📄 License

This project is for internal use by Cambodian government primary schools.