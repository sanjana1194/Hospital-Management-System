# 🏥 Hospital Management System

A full-stack web application designed to streamline and digitize hospital operations. Built using **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**, this system allows patients to book appointments, view prescriptions, and find specialists based on symptoms with integrated **Google Maps API** for nearby recommendations.

---

## 🌐 Live Demo

> Deployment Video Link: (https://drive.google.com/file/d/1eAbqugXwZ7vnxhurKDOI3PSVjzaWuyfp/view?usp=sharing)

---

## ✨ Key Features

- 👨‍⚕️ **Doctor Management**
  - Register doctors with specialization, fees, and credentials
- 👤 **Patient Module**
  - User registration and login
  - View appointment history and prescriptions
- 📅 **Appointment Booking**
  - Choose doctor by specialization and availability
  - Automatically pre-fills form from symptom checker
- 💊 **Prescription Module**
  - View doctor-generated prescriptions by date and time
- 🔎 **Symptom-Based Specialist Finder**
  - Suggests medical specialization based on symptoms
  - Shows in-hospital doctors with “Book Appointment” button
  - Google Maps shows nearby specialists if none available
- 🌍 **Portfolio Front Page**
  - Clean, responsive HTML/CSS/JS landing page for hospital branding

---

## 🛠 Tech Stack

| Frontend       | Backend     | Database | APIs                     |
|----------------|-------------|----------|---------------------------|
| HTML, CSS, JS  | PHP         | MySQL    | Google Maps & Places API |

---

## 🚀 Getting Started

### 🧰 Prerequisites
- [XAMPP](https://www.apachefriends.org/index.html) (Apache + MySQL)
- PHP >= 7.0
- Git (optional)

### 🛠 Setup Instructions

1. Clone the repository
   ```bash
   git clone https://github.com/your-username/Hospital-Management-System.git
Move project folder to:

bash
Copy
Edit
C:/xampp/htdocs/Hospital-Management-System
Start Apache & MySQL in XAMPP control panel.

Import the database:

Go to http://localhost/phpmyadmin

Create a database: hospitalms

Import the provided hospitalms.sql or use phpMyAdmin import

Open the project in browser:

bash
Copy
Edit
http://localhost/Hospital-Management-System/index.html
🔑 Default Credentials
👩‍💼 Admin
makefile
Copy
Edit
Username: admin
Password: admin123
🖼 Screenshots
Login Page	Admin Panel	Symptom Checker

📂 Project Structure
pgsql
Copy
Edit
Hospital-Management-System/
│
├── admin-panel.php
├── symptom_checker.php
├── index.html             # Portfolio front page
├── css/
├── js/
├── images/
├── sql/ (if exists)
└── README.md
🌍 Google Maps Integration
Used Maps JavaScript API and Places API

Automatically locates patient and suggests nearby specialists

Triggered when no internal specialist is available

👩‍💻 Developer
Dorathi Sai Sanjana

Internship Project @ MNM Hospital
