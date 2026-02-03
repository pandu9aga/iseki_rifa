# Iseki Rifa - Reporting & Overtime Management System

## 📋 Overview

**Iseki Rifa** is a comprehensive Human Resources and Operational reporting system built with Laravel. It is designed to manage employee attendance reports, overtime (Lembur) workflows, and annual performance assessments. The system provides a robust framework for recording daily activities, managing department budgets for overtime, and generating professional-grade PDF and Excel reports.

## ✨ Key Features

### 1. Advanced Reporting Module
*   **Daily & Monthly Reports**: Track daily attendance and export monthly summaries.
*   **Approval Workflows**: Integrated approval system for reports (Member & Supervisor levels).
*   **Special Report Types**: Support for "Nihil" reports (zero activity) to ensure 100% compliance.
*   **Multi-Format Export**: Generate reports in **PDF** (via DomPDF/Browsershot) and **Excel** (via PHPSpreadsheet).

### 2. Overtime (Lembur) Management
*   **Submission & Approval**: Streamlined workflow for overtime requests and multi-stage approvals.
*   **Budget Tracking**: Dedicated module to manage and bulk-update overtime budgets per division.
*   **Detailed Analytics**: Export comprehensive overtime reports to monitor costs and hours.

### 3. HR & Assessment Modules
*   **Annual Performance Assessment**: Record and track annual employee evaluations (`Penilaian Tahunan`).
*   **Employee & User CRUD**: Full management of employee profiles (NIK, Division, etc.) and system users.
*   **Replacement Tracking**: System for managing employee replacements (`Pengganti`) based on attendance data.
*   **Division Management**: Real-time insights into headcount across different divisions.

### 4. Utility Features
*   **Special Date Management**: Track holidays and special work dates.
*   **Activity Logging**: Integrated activity logs for auditing system changes (via Spatie Activity Log).
*   **Google API Integration**: Prepared for Google Cloud services integration.

## 🛠️ Technology Stack

### Backend
*   **Framework**: [Laravel 12.x](https://laravel.com)
*   **Language**: PHP ^8.2
*   **Database**: MySQL / MariaDB (Primary)
*   **Key Libraries**:
    *   `phpoffice/phpspreadsheet`: Advanced Excel manipulation.
    *   `barryvdh/laravel-dompdf`: PDF generation.
    *   `spatie/browsershot`: High-quality HTML to Image/PDF conversion.
    *   `spatie/laravel-activitylog`: System audit trails.
    *   `google/apiclient`: Google Cloud connectivity.

### Frontend
*   **Build Tool**: [Vite](https://vitejs.dev)
*   **Styling**: [Tailwind CSS v4.0](https://tailwindcss.com)
*   **HTTP Client**: Axios

## 🚀 Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone <repository-url>
    cd iseki_rifa
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install Node Dependencies**
    ```bash
    npm install
    ```

4.  **Environment Configuration**
    *   Copy the `.env.example` file:
        ```bash
        cp .env.example .env
        ```
    *   Set your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.
    *   Ensure `APP_LOCALE=id` for Indonesian language support.

5.  **Database Migration & Seeding**
    ```bash
    php artisan key:generate
    php artisan migrate
    # If a backup.sql is provided:
    # mysql -u root -p reportabsendb < backup.sql
    ```

6.  **Build Frontend**
    ```bash
    npm run build
    ```

7.  **Run Development Server**
    ```bash
    php artisan serve
    ```

## 📝 Usage

*   **Employee Portal**: Access the `/employee/reporting` route for self-service reporting.
*   **Admin Dashboard**: Manage users, employees, and process assessments via the authenticated administrative routes.

## 📄 License

This project is proprietary.
