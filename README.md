# 🎫 Eventbrite - Advanced Event Management Platform

## 📋 Project Context
Eventbrite is a comprehensive platform that enables organizers to create, manage, and promote both online and in-person events. Integrated with Stripe, it offers a seamless and secure ticket purchasing experience for participants.

This project aims to develop an advanced Eventbrite clone by following best practices in PHP MVC with PostgreSQL and integrating AJAX for dynamic interactions.

## 🎯 Objectives
✅ Organizers can publish and manage events  
✅ Participants can book tickets online  
✅ An admin back-office allows management of users and events  
✅ Advanced statistics provide detailed insights into events and sales  

## 🚀 Key Features

### 👥 User Management
✔ Secure registration and login (email, password hashed with bcrypt)  
✔ Role management: Organizer, Participant, Admin  
✔ User profile (avatar, name, event history)  
✔ Notification system (email, site alerts)  

### 🎪 Event Management
✔ Create and edit events (title, description, date, location, price, capacity)  
✔ Manage categories and tags (Conference, Concert, Sports, etc.)  
✔ Add promotional images and videos  
✔ Event validation by an administrator  
✔ Featured event system (sponsored events)  

### 💳 Booking & Payment
✔ Purchase tickets with different options (free, paid, VIP, early bird)  
✔ Secure payment via Stripe or PayPal (sandbox mode)  
✔ Generate QR codes for ticket validation at entry  
✔ Refund and ticket cancellation system  
✔ Download tickets as PDFs after purchase  

### 📊 Organizer Dashboard
✔ List of created events with status (active, pending, completed)  
✔ Real-time sales and booking statistics  
✔ Export participants list in CSV/PDF format  
✔ Manage promotions and discounts (promo codes, early bird offers)  

### 🛡️ Admin Back-Office
✔ Manage users (ban, delete, modify)  
✔ Manage events (validate, delete, modify)  
✔ Global statistics (users, tickets sold, revenue)  
✔ Content moderation (comments, reports)  

### ⚡ Dynamic Interactions with AJAX
✔ Dynamic event loading (pagination without reloading)  
✔ Advanced search and filters (category, price, date, location)  
✔ Search autocomplete with suggestions  
✔ Real-time form validation (email availability, password security)  

## 📁 Project Structure
```
app/
├── config/                     # Database configuration
│   ├── Database.php           # Database connection setup
│   └── database.sql           # Database schema
├── controllers/               # Application logic
│   ├── Authentication/        # Login/Register functionality
│   │   └── AuthController.php
│   ├── backOffice/           # Admin panel controllers
│   │   ├── AdminController.php
│   │   ├── OrganizerController.php
│   │   ├── ParticipantController.php
│   │   └── UserController.php
│   └── FrontOffice/          # Public-facing controllers
│       ├── blogController.php
│       ├── contactController.php
│       ├── EventController.php
│       └── HomeController.php
├── core/                      # Core framework files
│   ├── Auth.php              # Authentication handler
│   ├── Controller.php        # Base controller class
│   ├── Router.php            # URL routing
│   ├── Security.php          # Security utilities
│   ├── Session.php           # Session management
│   └── Validator.php         # Input validation
├── views/                     # Twig templates
│   ├── Authentication/        # Login/Register pages
│   ├── back/Admin/           # Admin dashboard
│   ├── organiser/            # Event organizer interface
│   ├── participant/          # Participant views
│   └── Profile/              # User profile pages
├── public/                    # Web-accessible files
│   ├── assets/               # CSS, JS, images
│   ├── component/            # Reusable components
│   ├── .htaccess             # Apache configuration
│   └── index.php             # Application entry point
└── vendor/                    # Composer dependencies
```

## 📖 User Stories

### 👥 As a Participant, I want to:
✅ Create an account and log in with email or Google/Facebook  
✅ Browse and filter event listings by category  
✅ Book a ticket online and receive a QR code  
✅ Cancel my reservation and request a refund  
✅ Receive notifications for upcoming events  

### 👤 As an Organizer, I want to:
✅ Publish an event and set ticket prices  
✅ Manage my sales and view registration statistics  
✅ Offer promo codes and manage discounts  
✅ Export participant lists in CSV or PDF format  

### 🛡️ As an Administrator, I want to:
✅ Manage users (ban, modify roles)  
✅ Approve or reject submitted events  
✅ Monitor global statistics and moderate content  

## 🏗️ Business Logic

### 📌 Role & Permission Management
- A Participant can only book public events  
- An Organizer can only manage their own events  
- An Admin has full access (validation, moderation, management)  

### 📌 Booking System
- Verifies ticket availability before confirmation  
- Sends an email with the ticket as an attachment after purchase  
- Allows cancellations under specific conditions (partial or full refund)  

### 📌 Advanced Security
- Protection against CSRF and SQL injections  
- Password hashing with bcrypt  
- Secure session management  

### 📌 Performance Optimization
- Optimized PostgreSQL queries with indexes and partitions  
- Lazy loading for events using AJAX  

## ⚙️ Installation Guide

1. **Clone the repository:**
    ```bash
    git clone https://github.com/MouadHallaffou/Eventbrite.git
    ```

2. **Navigate to the project directory:**
    ```bash
    cd Eventbrite
    ```

3. **Install dependencies:**
    ```bash
    composer install
    ```

4. **Configure your database in .env:**
    ```env
    DB_DSN = mysql:host=localhost;port=3306;dbname=Eventbrite_db
    DB_USERNAME = your_username
    DB_PASSWORD = your_password # default ''
    ```

5. **Start the development server:**
    ```bash
    php -S localhost:8000 -t public
    ```

6. **Open http://localhost:8000 in your browser**

## 🤝 Contributing
Contributions are welcome! To contribute:

1. Fork the repository  
2. Create a new branch (`feature-name`)  
3. Commit your changes and push to your branch  
4. Open a Pull Request  
