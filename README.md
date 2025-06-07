# 🚀 Laravel Project Setup Guide

This guide helps you clone and run the Laravel project locally using Laravel 11 and JWT authentication.

---

## 📦 Requirements

Make sure the following are installed on your system:

- PHP >= 8.2  
- Composer  
- Laravel CLI  
- MySQL or PostgreSQL  
- Git  

---

## 🧰 Full Setup Instructions (Copy & Paste Friendly)

```bash
# Clone the repository
git clone https://github.com/chalaa/User_management.git
cd User_management

# Install PHP dependencies
composer install

# Copy the environment configuration
cp .env.example .env

# Generate the application key
php artisan key:generate

# Set up your .env database values manually here before proceeding

# Run database migrations and seeders
php artisan migrate --seed

# If using JWT Auth package (e.g. tymon/jwt-auth), generate JWT secret
php artisan jwt:secret

# Start the Laravel development server
php artisan serve
