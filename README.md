<h1 align="center">System Admin Billiard</h1>

<p align="center">
  <strong>🎱 Solusi Manajemen Billiard untuk Meningkatkan Akurasi Pencatatan Transaksi</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white"/>
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white"/>
</p>

---

## 📋 Table of Contents
- [🎯 Overview](#-overview)
- [✨ Features](#-features)
- [🛠️ Tech Stack](#-tech-stack)
- [📸 Screenshots](#-screenshots)
- [🚀 Installation](#-installation)
- [📁 Project Structure](#-project-structure)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

---

## 🎯 Overview

**System Admin Billiard** adalah sistem manajemen yang dirancang khusus untuk membantu bisnis billiard dalam mengelola operasional secara efisien. Sistem ini fokus pada akurasi pencatatan transaksi untuk menghindari kesalahan keuangan yang sering terjadi dalam sistem manual.

### 🎯 Masalah yang Dipecahkan
- ❌ **Kesalahan pencatatan keuangan** → ✅ **Sistem otomatis dengan tracking real-time**
- ❌ **Data transaksi tidak terstruktur** → ✅ **Database terpusat dengan laporan detail**
- ❌ **Kesulitan analisis penjualan** → ✅ **Dashboard analytic yang komprehensif**

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🏓 **Manajemen Meja** | Mengelola status meja (tersedia/dipakai/maintenance) |
| 💳 **Tracking Transaksi** | Pencatatan transaksi berbasis meja dengan detail lengkap |
| 🛒 **Pemesanan Produk** | Order makanan/minuman langsung dari meja transaksi |
| 📦 **Manajemen Produk** | CRUD produk dengan kategori dan stok |
| 📊 **Analytic Dashboard** | Visualisasi data penjualan harian, mingguan, bulanan |
| 🖥️ **Desktop Optimized** | Interface yang optimal untuk penggunaan di kasir |

---

## 🛠️ Tech Stack

### Backend
- **Laravel 11** - PHP Framework
- **MySQL** - Database Management
- **Eloquent ORM** - Database Interaction

### Frontend
- **Laravel Blade** - Template Engine
- **Tailwind CSS** - Utility-first CSS Framework
- **Vite** - Build Tool & Development Server
- **Alpine.js** - Lightweight JavaScript Framework

### Development Tools
- **PostCSS** - CSS Processing
- **Axios** - HTTP Client
- **Concurrently** - Run Multiple Commands

---

## 📸 Screenshots
Halaman Dashboard
![Struktur Project](public/dashboard.jpg)

### 🖼️ Preview Coming Soon
- **Dashboard Overview**
- **Table Management Interface**
- **Transaction History**
- **Product Management**

---

## 🚀 Installation

### 📋 Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ & NPM
- MySQL 8.0+

### 🔧 Installation Steps

1. **Clone Repository**
   ```bash
   git clone https://github.com/[your-username]/system-admin-billiard.git
   cd system-admin-billiard

2. **Install PHP Dependencies**
   ```bash
   composer install

3. **Install NPM Dependencies**
   ```bash
   npm install

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate

5. **Database Configuration**
    - Create database in MySQL
    - Update .env file with your database credentials

6. **Run Migrations & Seeders**
   ```bash
   php artisan migrate --seed

7. **Build Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
8. **Start Development Server**
   ```bash
   php artisan serve

🌐 Access Application
    - Local URL: http://localhost:8000

🤝 Contributing
Kontribusi sangat terbuka! Silakan fork repository ini dan ajukan pull request untuk perbaikan atau fitur baru. Jangan lupa untuk membuat issue terlebih dahulu jika ada yang ingin didiskusikan.

## 📁 Project Structure
```bash
system-admin-billiard/
├── app/
│   ├── Models/            # Eloquent Models
│   └── Http/
│       ├── Controllers/   # Application Controllers
│       └── Middleware/    # Route Middleware
├── resources/
│   ├── views/             # Blade Templates
│   ├── css/               # Tailwind CSS
│   └── js/                # Alpine.js Components
├── routes/
│   ├── web.php            # Web Routes
│   └── api.php            # API Routes
├── database/
│   ├── migrations/        # Database Migrations
│   └── seeders/           # Database Seeders
└── public/
    ├── build/             # Vite Build Assets
    └── storage/           # File Storage
```

🤝 Contributing
Kontribusi sangat terbuka! Silakan fork repository ini dan ajukan pull request untuk perbaikan atau fitur baru. Jangan lupa untuk membuat issue terlebih dahulu jika ada yang ingin didiskusikan.

