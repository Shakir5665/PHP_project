# Department Communication Management System
## Complete Student Setup, Architecture & Implementation Guide

This guide is designed for beginners. It provides a complete, step-by-step walkthrough to set up, understand, and run the **Department Communication Management System** on a local computer using **HTML, CSS, PHP, and XAMPP (Apache + MySQL)**.


## 1. File Structure & Code Explanation

The project has a clean and minimal structure:

```text
Your_Project_Folder/
│
├── config.php          # Database credentials & PDO connection setup
├── index.php           # Core application logic, routing, and HTML views
├── style.css           # Styling, responsive layout, colors, and typography
├── schema.sql          # Complete MySQL database creation script & sample data
└── STUDENT_GUIDE.md    # This beginner setup and architecture manual
```

### File Details:

#### 1. `config.php`
- Initializes connection variables (`$host`, `$port`, `$db`, `$user`, `$pass`).
- Creates a `PDO` instance with error handling (`ERRMODE_EXCEPTION`) and disabled prepared statement emulation for enhanced security.
- Contains an informative error display card that immediately helps diagnose connection issues if MySQL is not running.

#### 2. `index.php`
Contains the entire web application interface and logic:
- **Security Helper `e()`**: Wraps text with `htmlspecialchars()` to prevent Cross-Site Scripting (XSS).
- **Controller Logic**: Handles `POST` actions (`add_meeting`, `add_decision`, `update_confirmation`, `add_agenda`, `add_participation`).
- **Data Queries**: Executes aggregate queries (`COUNT(*)`) for the dashboard and joins (`JOIN`) to link meetings, HODs, and users.
- **Navigation Views**:
  - `dashboard`: Quick stats and upcoming meeting table.
  - `meetings`: Schedule new meetings and view all existing meetings.
  - `users`: View registered department staff and student representatives.
  - `agenda`: Link an agenda document reference to a meeting.
  - `decisions`: Record official decisions made during a meeting.
  - `attendance`: Manage attendance confirmation (`Confirmed`/`Pending`) and participation records.

#### 3. `style.css`
- Uses modern CSS Flexbox and Grid layouts.
- Fully responsive on desktop, tablet, and mobile devices.
- Uses clean CSS custom properties (variables) for consistent branding.

#### 4. `schema.sql`
- Contains database creation (`CREATE DATABASE IF NOT EXISTS Your_DB_Name`).
- Defines all 10 relational tables with foreign keys and check constraints.
- Inserts rich sample data for immediate testing.

---


## 2. Prerequisites & Tools Installation

You only need **one tool** installed on your Windows computer: **XAMPP**.

### How to Install XAMPP:
1. Visit the official website: **[https://www.apachefriends.org/](https://www.apachefriends.org/)**
2. Download **XAMPP for Windows** (PHP 8.1, 8.2, or latest).
3. Run the installer `.exe` file.
   - When prompted to select components, ensure at least **Apache**, **MySQL**, and **phpMyAdmin** are checked.
   - Leave the install folder as default: `C:\xampp`.
4. Finish the installation and launch the **XAMPP Control Panel**.

---

## 3. Step-by-Step Setup Guide

Follow these 5 simple steps to get the project running:

### Step 1: Copy Project Folder to XAMPP `htdocs`
1. Copy your project folder `Yor_Project_Folder`.
2. Paste it into your XAMPP web root directory:
   ```text
   C:\xampp\htdocs\Yor_Project_Folder
   ```
3. Verify that `C:\xampp\htdocs\Yor_Project_Folder` contains:
   - `config.php`
   - `index.php`
   - `style.css`
   - `schema.sql`

---

### Step 2: Start Apache and MySQL in XAMPP
1. Open the **XAMPP Control Panel** (search for "XAMPP" in your Windows Start menu).
2. Click **Start** next to **Apache**. (The background turns green).
3. Click **Start** next to **MySQL**. (The background turns green).

> ⚠️ **Note:** Both must be green. If either shows an error (such as a port block), see Section 8 (Troubleshooting).

---

### Step 3: Import the Database (`schema.sql`)
1. Open your web browser (Google Chrome, Edge, etc.) and go to:
   ```text
   http://localhost/phpmyadmin/
   ```
2. Click the **SQL** tab in the top navigation bar.
3. Open [`schema.sql`](schema.sql) in any text editor (Notepad, VS Code, etc.), select all text (`Ctrl + A`), and copy it (`Ctrl + C`).
4. Paste the entire SQL script into the text area in phpMyAdmin.
5. Click the **Go** button at the bottom right.
6. Look at the left sidebar in phpMyAdmin: you will see **`Your_DB_Name`** listed with all 10 tables!

---

### Step 4: Verify `config.php`
Open `config.php` in a text editor and make sure Option A (Local MySQL) has the following settings:

```php
$host = 'localhost';             // Local machine
$port = '3306';                  // Default MySQL port in XAMPP
$db   = 'Your_DB_Name';   // The database created by schema.sql
$user = 'root';                  // Default XAMPP user
$pass = '';                      // Default XAMPP password is empty
```
*(Leave `$pass = '';` empty because standard XAMPP installations have no root password).*

---

### Step 5: Launch the Application

You have two convenient ways to run the web application. **Method 1 is recommended for students** because it is faster and does not require moving files into `htdocs`.

---


#### 🌐 How to Run: Using Traditional Apache in XAMPP
If you prefer running through Apache:
1. Ensure the folder is placed in `C:\xampp\htdocs\Yor_Project_Folder`.
2. In the XAMPP Control Panel, click **Start** on both **Apache** and **MySQL**.
3. Open your browser and go to:
   ```text
   http://localhost/Yor_Project_Folder/
   ```


### Summary Checklist for Students
- [ ] XAMPP installed and running (`Apache` and `MySQL` green).
- [ ] `schema.sql` imported into phpMyAdmin (creating `Your_DB_Name`).
- [ ] Project folder placed inside `C:\xampp\htdocs\`.
- [ ] `config.php` has `$host = 'localhost'`, `$user = 'root'`, `$pass = ''`.
- [ ] Browser loads `http://localhost/Yor_Project_Folder/`.
