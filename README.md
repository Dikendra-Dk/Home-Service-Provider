# SewaSathi (सेवासाथी) — Nepal's Home Service Booking Platform

> A modern, trustworthy, mobile-first home service booking platform for Nepal. Pathao/InDrive-level polish designed specifically for hiring local verified workers (plumbers, electricians, mistiris, cleaners, painters, appliance repair, carpenters, geyser/AC technicians) directly to your home.

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript (zero emoji policy, 100% crisp SVG icons)
- **Backend**: PHP 8.2 (OOP / PDO Database Layer, Session-Based Auth, REST APIs)
- **Database**: MySQL 8.0 (Structured Relational Schema + Realistic Nepali Seed Data)
- **Containers**: Docker & Docker Compose with **phpMyAdmin** integration

---

## 🚀 Quick Start with Docker

### 1. Launch all containers:
```bash
cd /home/dikendradk/.gemini/antigravity/scratch/sewasathi
docker compose up -d --build
```

### 2. Access the Application:
- **Web App**: [http://localhost:8080](http://localhost:8080)
- **phpMyAdmin**: [http://localhost:8085](http://localhost:8085)
  - *Server*: `db`
  - *Username*: `root`
  - *Password*: `rootpassword` (or user: `sewasathi_user` / pass: `sewasathi_pass`)

---

## 🔑 Demo Login Accounts

| Role | Email | Password | Details |
|---|---|---|---|
| **Customer** | `customer@sewasathi.com` | `password123` | Aayush Shrestha (Kathmandu Ward 4) |
| **Provider** | `provider@sewasathi.com` | `password123` | Ram Bahadur Shrestha (Master Plumber) |
| **Admin** | `admin@sewasathi.com` | `admin123` | SewaSathi Superadmin |

*Tip: The login screen also features 1-click quick fill buttons for instant testing.*

---

## 🌟 Core Screens & Capabilities

1. **Landing Page (`index.php`)**:
   - Hero section with quick search bar and cascading Nepali location selector (**Province → District → Municipality → Ward**).
   - "How it Works" 3-step section, 8 illustrated service category cards, Trust badges, and Top-rated nearby workers.
2. **Search / Results Page (`search.php`)**:
   - Ward-level filter sidebar, live availability toggle, minimum star ratings, proximity sorting, and verified provider cards.
3. **Provider Profile Page (`provider.php`)**:
   - Header with verified checkmark badge, bio, live availability pulse dot, service wards, itemized rate card, and customer reviews.
4. **Step-by-Step Booking Flow (`book.php`)**:
   - Step 1: Issue details & urgency selection (Standard vs Urgent ASAP within 45 mins).
   - Step 2: Date & time slot picker + Ward & street address details.
   - Step 3: Transparent price calculation & payment method choice (Cash, eSewa, Khalti).
   - Step 4: Live confirmation tracker with reference code.
5. **Customer Dashboard (`customer-dashboard.php`)**:
   - Active bookings with visual progress stepper (`Pending` → `Accepted` → `In Progress` → `Completed`).
   - "Confirm Payment Made" action button & "Leave Review" 5-star modal.
6. **Provider Dashboard (`provider-dashboard.php`)**:
   - Live availability switch (`Available for Jobs` / `Busy`).
   - Incoming job requests with sound/pulse notification and instant `Accept` / `Decline` actions.
   - Active on-site jobs tracker & profile rate card editor.
7. **Admin Panel (`admin-dashboard.php`)**:
   - Pending provider verification queue with citizenship document preview modal and `Approve` / `Reject with Remarks` actions.
   - Live searchable tables for users, bookings, and categories.
8. **Auth Portals (`login.php`, `register.php`, `register-provider.php`)**:
   - Separate customer registration and provider onboarding with identity citizenship verification.

---

## 🇳🇵 Nepali Cultural & UX Highlights
- **Administrative Location Selection**: Province → District → Municipality → Ward No. (e.g. Kathmandu Metro Ward 4 Baluwatar, Lalitpur Metro Ward 3 Pulchowk).
- **Currency**: Fixed NPR (`रू`) pricing.
- **Safety**: Police and CTEVT trade verification flow with a 7-Day Satisfaction Warranty.
- **Zero Emojis**: 100% sleek SVG vector iconography.
- **Mobile First**: Native app feel with bottom navigation bar for mobile users.
