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

# Clone the repository
```bash
git clone https://github.com/chalaa/User_management.git
cd User_management
```
# Install PHP dependencies
```bash
composer install
```
# Copy the environment configuration
```bash
cp .env.example .env
```
# Generate the application key
```bash
php artisan key:generate
```
# Set up your .env database values manually here before proceeding

# generate JWT secrete key
```bash
php artisan jwt:secret
```

# Run database migrations and seeders
```bash
php artisan migrate --seed
```

# Start the Laravel development server
```bash
php artisan serve
```