# 🐾 PawCare System

PawCare is a modern Veterinary Management System designed to streamline pet registrations, medical records, and appointment scheduling. Built with **Laravel**, **MySQL**, and **Bootstrap**, it provides a seamless experience for Admin, Staff, and Pet Owners.

---

## 🚀 Key Features

### 📡 Smart Notifications
- **SMS Integration**: Powered by **Semaphore API**. Automatically notifies owners about pet enrollment, appointment bookings, and approvals.
- **Simulation Mode**: Built-in developer mode to test SMS logic without using credits.

### 💳 Digital Pet ID & Verification
- **QR Code System**: Each pet gets a unique Digital ID.
- **Public Profile**: Scanning the QR code allows anyone (e.g., vet clinics) to verify a pet's status without needing an account.

### 🛠️ Developer Assistant Widget
- **Floating Widget**: A modern, interactive assistant accessible from any page.
- **Real-time Messaging**: Users can send messages directly to developers, integrated with **Gmail SMTP**.

### 📊 Role-Based Dashboards
- **Admin**: Full control over staff management, pet records, and system logs.
- **Staff**: Manage appointments, update vaccination histories, and inventory.
- **Owner**: Book appointments, view pet digital IDs, and track medical history.

---

## 👥 The Developer Team

Developed with ❤️ by the PawCare Team:

- **Crizneil** ([Facebook](https://web.facebook.com/alpha.criz)): **Lead Full Stack & UI/UX Designer**
- **Edrine** ([Facebook](https://web.facebook.com/ejramos28)): **Full Stack & QA**
- **Angelo** ([Facebook](https://web.facebook.com/ANGELO.HOMIEZYD.ADVINCULA)): **Full Stack & ST**
- **Marvin** ([Facebook](https://web.facebook.com/marvin.cinco.752)): **Front End & SA**

---

## 🛠️ Technical Setup

### 1. Prerequisites
- PHP 8.1+
- Composer
- MySQL

### 2. Installation
```bash
git clone https://github.com/Crizneil/pawcarelaravel2.git
cd PawCareLaravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 3. Environment Configuration (`.env`)
To enable full functionality, configure the following:

**SMS (Semaphore):**
```properties
SEMAPHORE_API_KEY=your_api_key_here
SEMAPHORE_SENDER_NAME=PawCare
```

**Email (Gmail SMTP):**
```properties
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=ssl
```

---

## 📄 License
This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
