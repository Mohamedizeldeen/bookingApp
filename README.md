# BookingApp - Multi-Tenant Booking Management System

<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## 📋 Overview

BookingApp is a comprehensive SaaS booking management platform built with Laravel, designed for multi-company operations with role-based access control. The system enables businesses to manage appointments, customers, and services through an intuitive interface.

## ✨ Features

### Core Features
- **Multi-tenant Architecture**: Complete isolation between company data
- **Role-based Access Control**: Admin and Staff permission levels
- **Appointment Management**: Schedule, track, and manage bookings
- **Customer Management**: Store and access customer information
- **Service Catalog**: Manage your business offerings
- **Real-time Dashboard**: Track key metrics and statistics
- **Modern UI**: Responsive design with Tailwind CSS
- **Notification System**: Keep users informed of important events

### 🚀 Enterprise Features
- **Advanced Calendar Sync**: 
  - Google Calendar, Outlook, Apple Calendar, and CalDAV integration
  - Intelligent token management with automatic refresh
  - Configurable sync frequency and bidirectional updates
  - Real-time sync status monitoring with conflict resolution

- **Multi-Location Support with GPS Intelligence**:
  - GPS-based location discovery and optimization
  - Intelligent location recommendations for customers
  - Real-time capacity monitoring and utilization tracking
  - Distance-based appointment routing and scheduling
  - Location performance analytics and optimization suggestions

- **Advanced Real-Time Analytics**:
  - Live appointment tracking and monitoring
  - Hourly trend analysis with performance insights
  - Location activity metrics and capacity optimization
  - Staff performance monitoring and productivity tracking
  - Performance alerts and automated recommendations
  - Comprehensive business intelligence dashboard

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
- **Advanced Calendar Management**: Configure and monitor calendar integrations
- **Multi-Location Oversight**: Manage multiple locations with GPS optimization
- **Enterprise Analytics**: Access real-time business intelligence and performance metrics
- **Location Optimization**: Receive intelligent recommendations for business growth

### Staff Features
- View appointment dashboard
- Manage personal schedules
- Access customer information
- Handle day-to-day booking operations
- **Personal Calendar Sync**: Integrate with personal calendar applications
- **Location Insights**: View location-specific performance and capacity data
- **Real-Time Metrics**: Monitor live appointment data and trends
- **Performance Tracking**: Access personal productivity and performance analytics

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 10 with advanced service architecture
- **Database**: MySQL with optimized queries and indexing
- **Authentication**: Laravel Fortify with role-based security
- **Authorization**: Laravel Policies with multi-tenant isolation
- **Real-Time Processing**: Advanced caching with performance optimization
- **GPS Integration**: Haversine formula for location intelligence

### Frontend
- **Templates**: Blade with component-based architecture
- **Styling**: Tailwind CSS with responsive design patterns
- **Real-Time UI**: Dynamic dashboard with live metrics
- **Interactive Maps**: GPS-based location visualization

### Integrations
- **Calendar APIs**: Google Calendar, Microsoft Outlook, Apple Calendar, CalDAV
- **Location Services**: GPS coordinate processing and optimization
- **Analytics Engine**: Real-time data processing and business intelligence

## 🏗️ Enterprise Architecture

### Advanced Services Layer
- **RealTimeAnalyticsService**: Live metrics processing with caching optimization
- **LocationService**: GPS-based intelligence with distance calculations and recommendations
- **CalendarSyncService**: Enhanced multi-platform calendar integration with token management
- **AnalyticsService**: Comprehensive business intelligence with performance tracking

### Key Enterprise Components
- **Multi-Location Management**: GPS-optimized location discovery and capacity planning
- **Intelligent Calendar Sync**: Token-aware synchronization with conflict resolution
- **Performance Monitoring**: Real-time alerts and automated business recommendations
- **Advanced Analytics**: Hourly trends, staff monitoring, and location optimization

## 📄 License

The BookingApp is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
