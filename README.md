# 💼 Remote Job Recruitment Portal

## 📌 Project Description

The **Remote Job Recruitment Portal** is a web-based application developed using **PHP, MySQL, HTML, CSS, and JavaScript**.

The application helps connect **Job Seekers** and **Employers** through an online remote job platform.

Job seekers can browse available remote jobs, view job details, apply for jobs, and track their applications. Employers can register, log in, post remote job opportunities, and view applications submitted by candidates.

The project provides a simple recruitment platform called **Remotely**.

---

# 🚀 Features

## 👤 Job Seeker Features

* Register as a Job Seeker.
* Login securely.
* Browse available remote jobs.
* View detailed job information.
* Apply for jobs.
* Add portfolio links.
* Submit cover letters.
* View submitted applications.
* Logout securely.

---

## 🏢 Employer Features

* Register as an Employer.
* Add company name.
* Login securely.
* Access Employer Dashboard.
* Post new remote jobs.
* Add job title.
* Add job description.
* Add job tags.
* Add salary information.
* View posted jobs.
* View candidate applications.

---

## 🔐 Authentication Features

* User registration.
* User login.
* Password hashing.
* Password verification.
* Role-based authentication.
* Session management.
* Session ID regeneration.
* Secure logout.
* Prepared SQL statements.

---

# 🛠️ Technologies Used

## Frontend

* HTML
* CSS
* JavaScript

## Backend

* PHP

## Database

* MySQL

## Database Connection

* PDO

## Server

* Apache Server
* XAMPP

---

# 📂 Project Structure

```text
Remote Job Recruitment Portal/
│
└── exp8/
    │
    ├── index.php
    ├── login.php
    ├── auth.php
    ├── db.php
    ├── dashboard.php
    ├── job.php
    ├── profile.php
    └── logout.php
```

---

# 📄 File Description

## 1. index.php

This is the main page of the Remote Job Recruitment Portal.

It displays all available remote jobs.

The jobs are retrieved from the MySQL database and displayed in job cards.

### Features

* View all available jobs.
* View latest jobs.
* Search interface.
* View job title.
* View company name.
* View job information.
* Navigate to job details.
* Login and registration access.

The jobs are retrieved using:

```sql
SELECT * FROM jobs ORDER BY posted_at DESC;
```

---

# 2. login.php

This file provides the authentication interface.

The page contains two options:

* Login
* Register

Users can register as:

```text
Job Seeker

Employer / Hiring Manager
```

For Employers, the system also collects the company name.

---

# 3. auth.php

This file handles user registration and login.

## Registration

During registration, the application:

1. Receives user information.
2. Validates the email.
3. Checks the user role.
4. Hashes the password.
5. Stores the user information in the database.

Passwords are encrypted using:

```php
password_hash($password, PASSWORD_BCRYPT);
```

---

## Login

During login, the application:

1. Receives email and password.
2. Searches for the user.
3. Retrieves the password hash.
4. Verifies the password.
5. Creates a session.
6. Redirects the user based on their role.

The password is verified using:

```php
password_verify($password, $user['password_hash']);
```

---

# 4. db.php

The `db.php` file connects the application to the MySQL database.

The project uses PDO.

Database configuration:

```php
$host = 'localhost';
$dbname = 'exp8';
$username = 'root';
$password = '';
```

PDO is used for secure database communication.

---

# 5. dashboard.php

This file is the Employer Dashboard.

Only users with the Employer role can access this page.

Employers can:

* Post new jobs.
* View their posted jobs.
* View job applications.

The system checks the user role before providing access.

```php
$_SESSION['role'] === 'employer'
```

---

# 6. job.php

This page displays detailed information about a selected job.

The job is selected using its unique ID.

Example:

```text
job.php?id=1
```

Job seekers can apply for a job.

The application form collects:

* Applicant Name.
* Applicant Email.
* Portfolio Link.
* Cover Letter.

The application information is stored in the `applications` table.

---

# 7. profile.php

This page is designed for Job Seekers.

Job seekers can view their submitted applications.

The application displays:

* Job Title.
* Company Name.
* Portfolio Link.
* Application Date.
* Application Status.

Only users with the Job Seeker role can access this page.

---

# 8. logout.php

This file handles user logout.

It performs the following operations:

* Starts the session.
* Clears session variables.
* Destroys the session.
* Removes session cookies.
* Redirects the user to the home page.

---

# 🗄️ Database Setup

The project uses a database named:

```text
exp8
```

The application requires the following tables:

* users
* jobs
* applications

---

# 📊 Database Tables

## 1. Users Table

The `users` table stores user information.

Fields include:

* User ID
* Email
* Password Hash
* User Role
* Company Name

---

## 2. Jobs Table

The `jobs` table stores job information.

Fields include:

* Job ID
* Employer ID
* Job Title
* Company
* Description
* Tags
* Salary
* Logo URL
* Posted Date

---

## 3. Applications Table

The `applications` table stores job applications.

Fields include:

* Application ID
* Job ID
* Applicant Name
* Applicant Email
* Portfolio Link
* Cover Letter
* Application Date

---

# 🧱 SQL Database Code

```sql
CREATE DATABASE exp8;

USE exp8;

CREATE TABLE users (

    id INT AUTO_INCREMENT PRIMARY KEY,

    email VARCHAR(150) NOT NULL UNIQUE,

    password_hash VARCHAR(255) NOT NULL,

    role ENUM('seeker', 'employer') NOT NULL DEFAULT 'seeker',

    company_name VARCHAR(150),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);


CREATE TABLE jobs (

    id INT AUTO_INCREMENT PRIMARY KEY,

    employer_id INT NOT NULL,

    title VARCHAR(255) NOT NULL,

    company VARCHAR(150) NOT NULL,

    description TEXT NOT NULL,

    tags VARCHAR(255),

    salary VARCHAR(100),

    logo_url VARCHAR(255),

    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (employer_id)

    REFERENCES users(id)

    ON DELETE CASCADE

);


CREATE TABLE applications (

    id INT AUTO_INCREMENT PRIMARY KEY,

    job_id INT NOT NULL,

    applicant_name VARCHAR(150) NOT NULL,

    applicant_email VARCHAR(150) NOT NULL,

    portfolio_link VARCHAR(255),

    cover_letter TEXT,

    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (job_id)

    REFERENCES jobs(id)

    ON DELETE CASCADE

);
```

---

# ⚙️ Installation and Setup

## Step 1: Install XAMPP

Install XAMPP on your computer.

XAMPP provides:

* Apache Server
* PHP
* MySQL
* phpMyAdmin

---

## Step 2: Extract the Project

Extract the project ZIP file.

Copy the `exp8` folder into:

```text
C:\xampp\htdocs\
```

The project path should be:

```text
C:\xampp\htdocs\exp8
```

---

## Step 3: Start XAMPP

Open the XAMPP Control Panel.

Start:

```text
Apache

MySQL
```

---

## Step 4: Create the Database

Open phpMyAdmin.

Create the database:

```text
exp8
```

Create the following tables:

* users
* jobs
* applications

Use the SQL code provided above.

---

## Step 5: Configure Database Connection

Open:

```text
db.php
```

Verify the database configuration:

```php
$host = 'localhost';

$dbname = 'exp8';

$username = 'root';

$password = '';
```

---

# ▶️ Running the Application

Open your web browser and enter:

```text
http://localhost/exp8/
```

The **Remotely Remote Job Recruitment Portal** will open.

---

# 🔄 Working Flow

```text
User
 │
 ▼
Open Website
 │
 ▼
Browse Remote Jobs
 │
 ├─────────────────┐
 │                 │
 ▼                 ▼
Job Seeker      Employer
 │                 │
 ▼                 ▼
Register/Login  Register/Login
 │                 │
 ▼                 ▼
View Jobs       Employer Dashboard
 │                 │
 ▼                 ▼
View Job        Post Jobs
 │                 │
 ▼                 ▼
Apply Job       View Applications
 │
 ▼
View My Applications
```

---

# 🔐 Authentication Flow

```text
User
 │
 ▼
Login / Register
 │
 ▼
auth.php
 │
 ▼
Check User Role
 │
 ├────────────────┐
 │                │
 ▼                ▼
Job Seeker      Employer
 │                │
 ▼                ▼
index.php       dashboard.php
 │
 ▼
Browse Jobs
 │
 ▼
Apply for Jobs
 │
 ▼
profile.php
```

---

# 💼 Job Posting Flow

```text
Employer
   │
   ▼
Login
   │
   ▼
Employer Dashboard
   │
   ▼
Enter Job Details
   │
   ▼
Submit Job
   │
   ▼
PHP Processes Data
   │
   ▼
MySQL Database
   │
   ▼
Job Posted Successfully
   │
   ▼
Job Visible on Home Page
```

---

# 📝 Job Application Flow

```text
Job Seeker
    │
    ▼
Browse Jobs
    │
    ▼
Select Job
    │
    ▼
View Job Details
    │
    ▼
Fill Application Form
    │
    ├── Name
    ├── Email
    ├── Portfolio Link
    └── Cover Letter
    │
    ▼
Submit Application
    │
    ▼
Store in Database
    │
    ▼
Employer Can View Application
```

---

# 🔒 Security Features

The project includes basic security features.

## Password Hashing

Passwords are securely hashed using:

```php
password_hash()
```

---

## Password Verification

Passwords are verified using:

```php
password_verify()
```

---

## Prepared Statements

PDO prepared statements are used to reduce the risk of SQL Injection.

Example:

```php
$stmt = $pdo->prepare(

    "SELECT id, email, password_hash, role

     FROM users

     WHERE email = ?"

);
```

---

## Session Management

After successful login, the application creates a user session.

```php
$_SESSION['user_id'] = $user['id'];

$_SESSION['role'] = $user['role'];

$_SESSION['email'] = $user['email'];
```

---

## Role-Based Access

The system has two user roles:

```text
seeker

employer
```

Employers can access:

```text
dashboard.php
```

Job Seekers can access:

```text
profile.php
```

---

# 🎯 Project Objective

The main objective of this project is to develop an online recruitment system for remote job opportunities.

The project demonstrates:

* PHP programming.
* MySQL database connectivity.
* PDO.
* User authentication.
* Password hashing.
* Session management.
* Role-based access control.
* Job posting.
* Job applications.
* Dynamic data retrieval.
* Prepared statements.
* Web application development.

---

# 📋 Requirements

The following software is required:

* XAMPP
* PHP
* MySQL
* Apache Server
* Web Browser

Recommended browsers:

* Google Chrome
* Microsoft Edge
* Mozilla Firefox

---

# 🔮 Future Enhancements

The following features can be added in future versions:

* 🔍 Advanced job search.
* 🏷️ Filter jobs by category.
* 🌍 Filter jobs by location.
* 💰 Filter jobs by salary.
* 📄 Resume upload.
* 📧 Email notifications.
* 🔔 Application status notifications.
* ❤️ Save favorite jobs.
* 💬 Employer and applicant messaging.
* ⭐ Job recommendations.
* 📊 Employer analytics dashboard.
* 👤 Complete user profiles.
* 🔐 Two-factor authentication.
* 📱 Mobile responsive improvements.
* 🤖 AI-based job recommendations.

---

# 👨‍💻 Conclusion

The **Remote Job Recruitment Portal** is a web-based application developed using **PHP and MySQL** to connect Job Seekers and Employers.

Job Seekers can browse remote job opportunities and submit applications. Employers can post jobs and view applications submitted by candidates.

The project demonstrates important web development concepts including **authentication, role-based access control, database connectivity, job posting, job applications, session management, and PHP-MySQL integration**.

The project provides a simple foundation for building a complete online remote job recruitment platform.
