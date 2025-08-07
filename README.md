# BookingApp - Multi-Tenant Booking Management System

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## 📋 Overview

BookingApp is a comprehensive SaaS booking management platform built with Laravel, designed for multi-company operations with role-based access control. The system enables businesses to manage appointments, customers, and services through an intuitive interface.

## ✨ Features

- **Multi-tenant Architecture**: Complete isolation between company data
- **Role-based Access Control**: Admin and Staff permission levels
- **Appointment Management**: Schedule, track, and manage bookings
- **Customer Management**: Store and access customer information
- **Service Catalog**: Manage your business offerings
- **Real-time Dashboard**: Track key metrics and statistics
- **Modern UI**: Responsive design with Tailwind CSS
- **Notification System**: Keep users informed of important events

## 🚀 Getting Started

1. Clone the repository
     ```bash
     git clone https://github.com/yourusername/BookingApp.git
     ```
2. Install dependencies
     ```bash
     composer install
     npm install
     ```
3. Set up environment
     ```bash
     cp .env.example .env
     php artisan key:generate
     ```
4. Configure database in `.env`
5. Run migrations and seed data
     ```bash
     php artisan migrate
     php artisan db:seed
     ```
6. Start the development server
     ```bash
     php artisan serve
     npm run dev
     ```

## 🔑 Test Login Credentials

### Tech Solutions Inc (Professional Plan)
| Role  | Email                       | Password |
|-------|----------------------------|----------|
| Admin | admin@techsolutions.com     | `123456` |
| Staff | john.doe@techsolutions.com  | `123456` |
| Staff | jane.smith@techsolutions.com| `123456` |
| Staff | mike.johnson@techsolutions.com | `123456` |

### Beauty Spa Wellness (Starter Plan)
| Role  | Email                   | Password |
|-------|------------------------|----------|
| Admin | admin@beautyspa.com     | `123456` |
| Staff | sarah.wilson@beautyspa.com | `123456` |
| Staff | emily.brown@beautyspa.com | `123456` |
| Staff | lisa.davis@beautyspa.com | `123456` |

### HealthCare Clinic (Enterprise Plan)
| Role  | Email                      | Password |
|-------|---------------------------|----------|
| Admin | admin@healthcareclinic.com | `123456` |
| Staff | dr.miller@healthcareclinic.com | `123456` |
| Staff | nurse.garcia@healthcareclinic.com | `123456` |
| Staff | dr.martinez@healthcareclinic.com | `123456` |

## 👩‍💼 User Roles

### Admin Features
- Add and manage team members
- View comprehensive statistics and reports
- Configure services and appointment types
- Full access to company settings and data

### Staff Features
- View appointment dashboard
- Manage personal schedules
- Access customer information
- Handle day-to-day booking operations

## 🛠️ Technology Stack

- **Framework**: Laravel 10
- **Frontend**: Blade Templates, Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Fortify
- **Authorization**: Laravel Policies

## 📄 License

The BookingApp is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
