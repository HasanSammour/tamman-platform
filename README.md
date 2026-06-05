# Tamman - Digital Mental Health Platform

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> **Tamman** (طمأن) is a secure digital mental health platform designed to provide accessible, private, and stigma-free psychological support to the Gaza community and beyond.

## 📋 Table of Contents

- [Overview](#overview)
- [Problem Statement](#problem-statement)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation Guide](#installation-guide)
- [Configuration](#configuration)
- [Database Schema](#database-schema)
- [Testing Credentials](#testing-credentials)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Security Features](#security-features)
- [Future Enhancements](#future-enhancements)
- [License](#license)
- [Contact](#contact)

## 🎯 Overview

**Tamman** *(meaning "reassure" or "comfort" in Arabic) is a comprehensive web-based mental health platform that connects individuals seeking psychological support with licensed mental health professionals through secure online sessions. The platform addresses the critical mental health crisis in Gaza, where ongoing conflicts, economic instability, and social stigma prevent many from seeking help.*


## ⚠️ Problem Statement

The Gaza community faces unprecedented psychological challenges:

- **High stress levels** due to ongoing conflicts and displacement
- **Limited access** to mental health professionals
- **Social stigma** surrounding mental health treatment
- **Privacy concerns** preventing individuals from seeking help
- **Economic barriers** making therapy unaffordable for many

Tamman solves these challenges by providing:
- ✅ Anonymous, stigma-free access to care
- ✅ Secure, encrypted video/audio/text sessions
- ✅ Donor-supported financial assistance
- ✅ Gamified engagement through Tamman Points

## ✨ Features

### 👤 Patient Features
- **User Authentication** - Secure registration, login, email verification
- **Specialist Search** - Filter by specialization, language, gender, price, availability
- **Session Booking** - Book video, audio, or text sessions with real-time availability
- **Mood Tracking** - Daily mood logging (1-10 scale) with streak bonuses
- **Psychological Assessments** - 6 clinically-validated tests (PHQ-9, GAD-7, PCL-5, ISI, PSS, CIS)
- **Treatment Plans** - Complete tasks assigned by specialists, earn points
- **Real-time Chat** - Secure messaging with edit/delete functionality
- **Rewards System** - Earn Tamman Points for healthy activities, redeem for discounts/free sessions
- **Credit System** - Add funds to account for session payments
- **Donor Support** - Receive financial assistance from donors
- **Bilingual Support** - Full Arabic and English interface with RTL layout

### 👨‍⚕️ Specialist Features
- **Professional Profile** - Upload credentials, license, certificates
- **Application Workflow** - Submit application, admin approval process
- **Schedule Management** - Set recurring availability, block time off
- **Client Management** - View patient history, mood trends, test results
- **Session Management** - Join sessions, add notes, mark completion
- **Treatment Plans** - Create custom treatment plans with tasks for patients
- **Earnings Dashboard** - Track earnings, view payment history, download invoices
- **Real-time Chat** - Communicate with patients securely

### 👑 Admin Features
- **User Management** - Manage patients and specialists (CRUD, suspend/activate)
- **Specialist Verification** - Review applications, approve/reject, request info
- **Content Management** - Create/edit/publish articles, videos, tips, guides
- **Payment Management** - Process credit requests, donations, specialist payouts
- **Financial Reports** - Generate revenue, donation, payout reports (PDF export)
- **Analytics Dashboard** - Interactive charts for users, sessions, finances, tests
- **System Logs** - Complete audit trail of all admin actions
- **Platform Analytics** - Track user growth, retention, session trends

### 💝 Donor Features
- **Make Donations** - Contribute funds to support patients in need
- **Donation History** - Track donations and see impact
- **Anonymous Giving** - Option to remain anonymous to recipients

## 🛠 Tech Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| Laravel | 12.x | PHP Framework |
| PHP | 8.2+ | Server-side language |
| MySQL | 8.0+ | Relational database |
| Pusher | Latest | Real-time WebSockets |
| Spatie Permission | Latest | Role-based access control |
| DomPDF | Latest | PDF report generation |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| Bootstrap | 5.x | UI Framework |
| FontAwesome | 6.x | Icons |
| jQuery | 3.7+ | DOM manipulation |
| ApexCharts | Latest | Interactive charts |
| FullCalendar | Latest | Schedule calendar |
| SweetAlert2 | Latest | Beautiful alerts |

### Third-Party Services
- **Jitsi Meet** - Secure video conferencing (self-hosted alternative available)
- **Pusher** - WebSocket server for real-time chat
- **SMTP (Gmail)** - Email notifications

## 📦 Installation Guide

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & NPM
- Git

### Step-by-Step Installation

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/tamman-platform.git
cd tamman-platform

# 2. Install PHP dependencies
composer install

# 3. Install NPM dependencies
npm install

# 4. Create environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tamman_db
DB_USERNAME=root
DB_PASSWORD=yourpassword

# 7. Run migrations and seeders
php artisan migrate:fresh --seed

# 8. Create storage link
php artisan storage:link

# 9. Build frontend assets
npm run build

# 10. Start the development server
php artisan serve
```

### Running Schedulers (For Auto-unlock & No-Show Features)

```bash
# Run this command in a separate terminal (Windows) or use cron (Linux/Mac)
php artisan schedule:work
```

## ⚙ Configuration

### Pusher WebSockets (Real-time Chat)

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### Jitsi Meet (Video Conferencing)

```env
JITSI_DOMAIN=meet.jit.si
JITSI_APP_NAME=Tamman
```

### Mail Configuration

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
```

## 📊 Database Schema

### Core Tables (18+ tables)

| Table | Description |
|-------|-------------|
| `users` | User accounts (patients, specialists, admins) |
| `specialist_profiles` | Specialist professional information |
| `donor_profiles` | Donor statistics and information |
| `therapy_sessions` | Booked sessions with status tracking |
| `availabilities` | Specialist availability schedule |
| `mood_logs` | Daily mood entries with streak tracking |
| `test_results` | Psychological test submissions |
| `point_transactions` | Points earned/redeemed history |
| `credit_transactions` | Financial credit movements |
| `conversations` | Chat conversations between users |
| `messages` | Chat messages with edit/delete support |
| `treatment_plans` | Patient treatment plans |
| `treatment_tasks` | Tasks within treatment plans |
| `rewards` | Available rewards for point redemption |
| `reward_redemptions` | User reward redemption history |
| `specialist_payments` | Specialist payout records |
| `notifications` | User notifications |
| `system_logs` | Admin action audit trail |

### Entity Relationship Diagram


## 🔑 Testing Credentials

After running the seeder, you can use these credentials:

### Admin Access
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@tamman.ps | admin123 |

### Specialist Access
| Role | Email | Password |
|------|-------|----------|
| Specialist | specialist_X@tamman.ps | password123 |

### Patient Access
| Role | Email | Password |
|------|-------|----------|
| Patient | patient_X@tamman.ps | password123 |

> **Note:** The seeder creates 1 admin, 200 specialists, 750 patients, and 50 donors with realistic data.

## 📁 Project Structure

```
tamman-platform/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── UpdateNoShowSessions.php
│   │       └── UnlockTextSessions.php
│   ├── Events/
│   │   ├── MessageSent.php
│   │   ├── MessageEdited.php
│   │   ├── MessageDeleted.php
│   │   └── UserTyping.php
│   ├── Helpers/
│   │   ├── MoodHelper.php
│   │   ├── TestHelper.php
│   │   ├── RewardHelper.php
│   │   ├── EmailHelper.php
│   │   ├── HtmlPurifierHelper.php
│   │   └── NotificationHelper.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Patient/
│   │   │   └── Specialist/
│   │   ├── Middleware/
│   │   │   ├── TrackUserActivity.php
│   │   │   └── SetLocale.php
│   │   └── Requests/
│   ├── Mail/
│   │   ├── BookingConfirmationMail.php
│   │   ├── BookingCancellationMail.php
│   │   ├── SpecialistApprovedMail.php
│   │   └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── TherapySession.php
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   └── ... (15+ models)
│   └── Notifications/
├── database/
│   ├── migrations/
│   │   └── ... (30+ migration files)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── RolePermissionSeeder.php
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   ├── patient/
│   │   ├── specialist/
│   │   ├── auth/
│   │   ├── layouts/
│   │   ├── email/
│   │   └── ... etc.
│   ├── css/
│   └── js/
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── ... etc.
├── routes/
│   ├── web.php
│   ├── api.php
│   ├── channels.php
│   └── console.php
├── config/
├── .env.example
├── composer.json
└── package.json
```

## 🔌 API Endpoints

### Public Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/specialists` | List verified specialists |
| GET | `/specialists/{id}` | View specialist profile |
| GET | `/resources` | Educational content listing |

### Authenticated Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/chat/messages` | Send chat message |
| PUT | `/chat/messages/{id}` | Edit message |
| DELETE | `/chat/messages/{id}/delete-for-me` | Delete for self |
| POST | `/patient/mood-tracker` | Log daily mood |
| POST | `/patient/tests/{test}/submit` | Submit assessment |
| POST | `/patient/bookings` | Book therapy session |
| GET | `/specialist/earnings/data` | Get earnings data |
| GET | `/admin/analytics/overview` | Get analytics stats |

## 🔒 Security Features

- ✅ **BCrypt password hashing** for all user credentials
- ✅ **CSRF protection** on all POST/PUT/DELETE forms
- ✅ **XSS prevention** with automatic output escaping (`{{ }}` vs `{!! !!}`)
- ✅ **SQL injection protection** via Eloquent ORM
- ✅ **Mass assignment protection** with `$fillable` / `$guarded`
- ✅ **Role-based access control** (RBAC) using Spatie Permission
- ✅ **Rate limiting** on authentication endpoints
- ✅ **Secure session management** with database driver
- ✅ **Email verification** required for full access
- ✅ **Secure room names** (64 random chars) for Jitsi meetings
- ✅ **Participant limit enforcement** (2 max per session)
- ✅ **Activity tracking** for online/offline status
- ✅ **System logs** for all admin actions

## 🚀 Future Enhancements

- [ ] Mobile application (iOS/Android)
- [ ] AI-powered mental health assistant
- [ ] Voice/video recording with patient consent
- [ ] Group therapy sessions
- [ ] Blockchain-based reward tokens
- [ ] Integration with electronic health records
- [ ] Teletherapy outcome prediction models
- [ ] Multi-language support expansion
- [ ] Multiple UI themes - Allow users to choose calming visual modes (light/dark/soft colors)


## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Author

| Name | Role | Contact |
|------|------|---------|
| Hassan Sammour | Full-Stack Developer | [GitHub](https://github.com/hassansammour) |

## 📞 Contact

- **Email:** hasansammour01@gmail.com
- **GitHub:** [github.com/hassansammour](https://github.com/hassansammour)
- **LinkedIn:** [linkedin.com/in/hassan-sammour](https://www.linkedin.com/in/hasan-sammour-72657a3a1/)
- **Project Link:** [github.com/hassansammour/tamman-platform](https://github.com/HasanSammour/tamman-platform)
---

## ⭐ Show Your Support

If you found this project helpful or interesting, please consider:

- Starring the repository ⭐
- Sharing with others who might benefit
- Providing feedback or suggestions

---

<div align="center">
  <sub>Built with ❤️ for better mental health in Gaza and beyond</sub>
</div>