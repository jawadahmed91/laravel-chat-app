# Laravel Chat Integration with CodeIgniter SSO

> A full-stack chat integration built on top of an existing **CodeIgniter 4 CRM system**, using **Laravel 12** as the real-time chat backend and **Vue.js** for the frontend. Includes **JWT-based Single Sign-On (SSO)**, CORS support, and modular architecture.

---

## 🧩 Overview

This project integrates three separate applications:

| App | Purpose |
|-----|---------|
| **CodeIgniter 4 CRM** | User authentication & customer/sales team/supplier management |
| **Laravel 12 Chat App** | Real-time chat API between roles (Customer ↔ Sales Team ↔ Supplier) |
| **Vue 3 Chat Frontend** | Chat UI using Axios + JWT auth |

All apps are connected via:
- ✅ Shared MySQL database
- ✅ JWT-based SSO from CodeIgniter to Laravel/Vue
- ✅ CORS middleware to allow cross-origin communication

---

## 🛠️ Features Implemented So Far

### 🔐 Authentication Flow
- Users log into CodeIgniter CRM
- Auto-login via SSO to Laravel Chat App using JWT tokens
- Vue app detects login status and starts chat session

### 💬 Chat System
- RESTful Laravel API for messages
- Supports multiple users: Customers ↔ Suppliers ↔ Sales Team
- Built with scalability in mind (ready for WebSockets/Socket.io later)

### 🌐 Cross-Origin Support
- CORS middleware added in both CodeIgniter and Laravel
- Allows secure communication between:
  - CodeIgniter (localhost:8080)
  - Laravel (localhost:8000)
  - Vue Dev Server (localhost:5173)

### 📁 Project Structure


```bash
  composer install
  npm install
  npm run dev
  cp .env.example .env
  php artisan key:generate
  php artisan serve --port=8001
  php artisan reverb:start
  redis-server
  php artisan queue:work