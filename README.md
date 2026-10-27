# Department Communication Management System

A clean, beginner-friendly PHP + MySQL web application designed to demonstrate relational database system design for university assignments using **HTML, CSS, PHP, and XAMPP (Apache & MySQL)**.

---

## 📖 Complete Student & Assignment Guide
A comprehensive, step-by-step setup and architecture guide for students has been prepared in:
👉 **[STUDENT_GUIDE.md](file:///c:/Users/shaki/Desktop/department_communication_system/STUDENT_GUIDE.md)**

It covers:
1. **Architecture & Request Flow**: 3-Tier architecture diagram (Browser $\leftrightarrow$ Apache/PHP $\leftrightarrow$ MySQL).
2. **Database Design**: The 10 relational tables, specialization hierarchy (`User` $\rightarrow$ `Permanent_lecturer` $\rightarrow$ `HOD`), check constraints, and foreign key rules.
3. **Prerequisites & Tool Setup**: How to install XAMPP on Windows.
4. **Step-by-Step Setup**: Copying to `htdocs`, starting Apache & MySQL, importing [`schema.sql`](file:///c:/Users/shaki/Desktop/department_communication_system/schema.sql) in phpMyAdmin, and configuring [`config.php`](file:///c:/Users/shaki/Desktop/department_communication_system/config.php).
5. **Feature Walkthrough**: Testing meetings, users, agendas, decisions, and attendance confirmations.
6. **Troubleshooting & FAQs**: Solutions for common student issues (e.g., access denied, port blocks).

---

## ⚡ Quick Start Options

### Option 1: Fast Run via PHP Built-in Server (No Apache needed!)
1. Start **MySQL** in XAMPP Control Panel.
2. Import [`schema.sql`](file:///c:/Users/shaki/Desktop/department_communication_system/schema.sql) in phpMyAdmin (`http://localhost/phpmyadmin/`).
3. Open PowerShell inside this folder and run:
   ```powershell
   & "C:\xampp\php\php.exe" -S localhost:8000
   ```
4. Open your browser at `http://localhost:8000`.

### Option 2: Run via Apache in XAMPP
1. Move this folder to `C:\xampp\htdocs\department_communication_system`.
2. Start **Apache** and **MySQL** in XAMPP Control Panel.
3. Import [`schema.sql`](file:///c:/Users/shaki/Desktop/department_communication_system/schema.sql) in phpMyAdmin.
4. Open your browser at `http://localhost/department_communication_system/`.
