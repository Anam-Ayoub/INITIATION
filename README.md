# Chronos

Chronos is a comprehensive, full-stack educational scheduling and campus navigation platform. It is designed to modernize how academic institutions manage their timetables, allocate resources, and help students and staff navigate the campus.

## 🚀 Features

*   **Smart Scheduling:** Dynamically manage timetables linking professors, courses, classrooms, and student cohorts.
*   **Interactive Campus Map:** An embedded, interactive grid-based map of the university layout (including roads, entrances, and classrooms) to help users find their way.
*   **Role-Based Access Control:** Secure access tailored for Administrators, Students, Professors, and Security personnel via robust API tokens.
*   **Admin Dashboard:** A centralized web hub for administrators to input schedules, manage users, and update the virtual campus map.
*   **Cross-Platform Mobile App:** On-the-go access for end-users to check their daily schedule and navigate the campus.

## 🏗️ Architecture

Chronos is divided into three primary layers:

### 1. Database (`chronos_db.sql`)
A robust MySQL/MariaDB database acting as the single source of truth.
*   Manages academic entities: `COURS` (courses), `CLASSE` (cohorts), `PROF` (professors), and `SALLE` (classrooms).
*   Maintains the `EMPLOI_DU_TEMPS` (timetable) relations.
*   Stores the interactive visual map data (`CARTE_LAYOUT`) in JSON format.
*   Handles user authentication (`ADMIN`) and secure session management (`API_TOKENS`).

### 2. Web Application (`WebApp/`)
A PHP-based backend and administrative interface.
*   **TIMETABLE_APP:** Contains the main administrative dashboard (`admin/`), views, and the RESTful API server (`api/`) that issues tokens and serves data to the mobile application.
*   **Users_Interface:** Web-based interfaces tailored for specific non-administrative interactions.

### 3. Mobile Application (`MobileApp/chronos/`)
A modern, cross-platform mobile application built with **Flutter**.
*   Consumes the PHP-based REST API.
*   Provides students, professors, and security staff with a sleek interface to view schedules and interact with the campus map.

## 🛠️ Technology Stack

*   **Mobile Frontend:** Flutter, Dart
*   **Web Backend & Dashboard:** PHP, HTML, CSS, JavaScript
*   **Database:** MySQL / MariaDB

## ⚙️ Getting Started

### Prerequisites
*   A local web server (XAMPP, MAMP, or LAMP stack) running PHP and MySQL.
*   Flutter SDK installed for the mobile application.

### Installation

1.  **Database Setup:**
    *   Create a database named `chronos_db` in your MySQL server.
    *   Import the provided `chronos_db.sql` file to generate the schema and seed initial data.
2.  **Web App Setup:**
    *   Place the `WebApp` folder in your web server's document root (e.g., `htdocs` or `/var/www/html`).
    *   Update database connection credentials in the backend configuration files (likely located in `WebApp/TIMETABLE_APP/config/` or `includes/`).
3.  **Mobile App Setup:**
    *   Navigate to `MobileApp/chronos` in your terminal.
    *   Run `flutter pub get` to install dependencies.
    *   Ensure the API base URLs in the Flutter application point to your local or hosted PHP web server.
    *   Run the app using `flutter run`.

---
*Developed as part of the INITIATION project.*
