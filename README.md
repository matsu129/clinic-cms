# ClinicCMS

ClinicCMS is a simple Content Management System (CMS) designed for clinics or hospitals. It allows you to manage patients, medical notes, appointments, users, and more directly from your web browser.

---

## Table of Contents
1. [Features](#features)
2. [Directory Structure](#directory-structure)
3. [Setup Instructions](#setup-instructions)
4. [Usage](#usage)
5. [Developer Information](#developer-information)

---

## Features
- **User Management**
  - Manage accounts for Admin, Doctor, Reception, etc.
  - Login and Logout
  - View and edit user profiles
  - Upload profile pictures
- **Patient Management**
  - Add, edit, and view patient information
- **Medical Notes Management**
  - Create, edit, and view medical notes
- **Appointment Management**
  - Manage patient appointments (add, edit, delete)
- **Audit Logs**
  - Record user actions
- **Dashboard**
  - Overview of key information and basic statistics

---

## Directory Structure

├── public/ // Web-accessible directory
│ ├── css/ // Stylesheets
│ ├── images/ // Static images
│ ├── includes/ // Common header & footer
│ ├── index.php // Entry point
│ └── uploads/ // Uploaded files (medical documents, profile images)
├── src/
│ ├── config/ // DB and constants configuration
│ ├── controllers/ // MVC Controllers
│ ├── core/ // Core classes (Database, Auth, AuditLogger)
│ ├── models/ // Data models (User, Patient, Appointment, etc.)
│ ├── views/ // HTML templates
│ └── logs/ // Error logs
├── vendor/ // Composer libraries
├── composer.json
└── README.md


---

## Setup Instructions

1. **Clone the repository**
git clone <repository-url>
cd ClinicCMS
Install dependencies via Composer


composer install
Create the database

Use MySQL or other supported DB

Update DB configuration in src/config/config.php

Create tables

Use SQL scripts provided in src/models/ to create necessary tables

Access the public directory

Set public/ as your web server document root

Example: http://localhost/ClinicCMS/public/

Usage
Login

Access /auth/login

Dashboard

Access /dashboard to view key information

Edit Users

Access /user/edit?id=XX to edit user details

Edit Profile

Access /profile to update the logged-in user's profile

File Upload

Upload profile images or medical documents

Developer Information
Framework

PHP + PDO

Follows MVC structure

Dependencies

Managed via Composer

Logs

Errors and exceptions are recorded in src/logs/error.log

Recommended PHP version

PHP 8.x

Notes
Ensure the public/uploads/ directory has write permissions.

For production, secure sensitive data in config.php, including DB credentials.

License
MIT License


