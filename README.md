# CHRONOS — Gestion des emplois du temps ⏱️

CHRONOS is an intelligent, web-based platform designed to manage and consult university or school timetables. It provides dedicated views for students and professors, a secure administration dashboard, and an interactive spatial map of the campus rooms.

## ✨ Key Features

- **Public Interfaces:** 
  - 📅 **Emploi Classe:** View the weekly schedule filtered by specific classes.
  - 👨‍🏫 **Emploi Professeur:** View the weekly schedule filtered by specific professors.
  - 🗺️ **Sécurité / Salles:** View an interactive layout map of the educational facility.
- **Administration Portal:** 
  - Secure login with hashed passwords and CSRF protection.
  - Full CRUD capabilities for scheduling (`EMPLOI_DU_TEMPS`).
  - **Smart Conflict Detection:** Automatically prevents double-booking of rooms, classes, or professors at the same time.
- **Interactive Map Builder:** Admin interface to visually design the room layout map using a drag-and-drop grid system backed by a JSON API.

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3 (Vanilla flexbox/grid with a modern glassmorphism aesthetic), JavaScript.
- **Backend:** PHP (Vanilla)
- **Database:** MySQL / MariaDB

## 📂 Project Structure

```text
TIMETABLE_APP/
├── index.php                 # Main modern landing page
├── config/
│   ├── db.php                # Database connection configuration
│   └── functions.php         # Core utility functions (conflict detection, CSRF, ID generation)
├── admin/                    # Secure administration area
│   ├── login.php / logout.php# Admin authentication
│   ├── dashboard.php         # Admin control panel
│   ├── api_carte.php         # JSON API for the interactive layout map
│   ├── carte.php             # Map builder interface
│   └── [add/edit/delete/list]_et.php # CRUD operations for the timetable
├── views/                    # Public-facing timetable views
│   ├── emploi_classe.php     # Schedule by class
│   ├── emploi_prof.php       # Schedule by professor
│   └── securite.php          # Public interactive map view
├── includes/
│   └── sidebar.php           # Admin navigation sidebar component
└── assets/                   # Static resources 
```

## 🗄️ Database Schema

The application relies on a relational schema with 7 core tables:

1. **`ADMIN`**: Stores administrator credentials (hashed).
2. **`PROF`**: Professor details.
3. **`CLASSE`**: Class identifiers (e.g., "SIIA", "SMI").
4. **`COURS`**: Course names (e.g., "Oracle", "Big Data").
5. **`SALLE`**: Room configuration and capacity.
6. **`EMPLOI_DU_TEMPS`**: The central scheduling table that links a Professor, Course, Room, and Class to a specific Day (`JOUR`), Start Time (`HEURE_DEB`), and End Time (`HEURE_FIN`).
7. **`CARTE_LAYOUT`**: Stores a stringified JSON representation (`grid_data`) of the interactive grid map layout.

## 🛡️ Security & Integrity

- **CSRF Protection:** Implemented on all administrative forms requiring data mutation (refer to `generateCsrfToken()` in `config/functions.php`).
- **Data Integrity:** Strict conflict validation (`existeConflit()`) prevents the insertion of overlapping schedules for the same resource.
- **Authentication:** Admin sessions control access to the `/admin/` directory.

## 🚀 Getting Started

1. Ensure you have a local web server environment like XAMPP, WAMP, or LAMP installed.
2. Import the provided ``if0_41365925_timetable_system.sql`` file into your MySQL database to structure the tables and seed initial data.
3. Update the database connection credentials in ``config/db.php`` to match your local environment.
4. Launch the application by navigating to the project's root `index.php` in your web browser. 
   *(Default admin credentials: Username: `admin` | Password: `[Check your seeder or reset] `)*
