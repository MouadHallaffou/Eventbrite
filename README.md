# Eventbrite Platform

A collaborative event reservation and management platform built with PHP and Twig templating engine.

## 📋 Project Overview

This is a team project replicating Eventbrite's core functionality - an event reservation platform that allows users to discover, book, and manage events. Features include event creation, ticket booking, user management, and administrative controls.

## 🎫 Key Features

- **Event Discovery**: Browse and search available events
- **Ticket Reservation**: Book tickets for events with secure payment
- **Event Management**: Create and manage events (for organizers)
- **User Profiles**: Manage personal information and booking history
- **Admin Dashboard**: Platform administration and user management

## 🏗️ Project Structure

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

## 👥 Team Collaboration Guidelines

### Getting Started
1. Clone the repository
2. Run `composer install` to install dependencies
3. Import `config/database.sql` to your MySQL database
4. Configure database connection in `config/Database.php`

### Development Workflow
- Create feature branches from `main`
- Use descriptive commit messages
- Test your changes before pushing
- Create pull requests for code review

### Code Standards
- Follow PSR-4 autoloading standards
- Use meaningful variable and function names
- Comment complex logic
- Maintain consistent indentation
