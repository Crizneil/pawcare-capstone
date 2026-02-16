# PawCare - Veterinary Clinic Management System

<!-- #### See the live demo [here](https://pawcare.example.com) -->

## Description

> **Warning**
> This project is still under active development.

<br />

**PawCare** is a comprehensive veterinary clinic management system built with **Laravel** and **MySQL**. It streamlines clinic operations by centralizing pet medical records, vaccination history, and vaccine inventory management. It was designed to provide a seamless experience for administrators, veterinarians, and pet owners through dedicated dashboards and interactive features.

The platform includes a robust booking and vaccination management system, where clinic staff can manage pet patients, record medical events, and monitor vaccine stock levels in real-time. The system also features digital pet identity cards accessible via QR codes for quick verification.

Unlike the original ARKA build which relied on Next.js, this version leverages the power of the **Laravel 12** ecosystem, utilizing Eloquent ORM for database interactions and Blade templating for a dynamic and responsive UI. QR code generation is handled by `simplesoftwareio/simple-qrcode`, and the UI is polished with **Bootstrap** and **Lucide Icons**.

<br />

## Usage instructions:

To explore the functionality of PawCare and its accompanying management systems, you can use the following demo credentials in the local environment:

- **Admin Account**: `admin@pawcare.com` / `password123`
- **Vet Account**: `vet@pawcare.com` / `password123`
- **Pet Owner Account**: `owner@pawcare.com` / `password123`

Feel free to navigate the platform, and test various features, including pet enrollment, vaccination recording, and inventory management. Please note that data in the local environment is subject to reset during development.

**As this is a development environment, I would like to emphasize that the platform is intended for testing clinic workflows. Responsibility for data integrity remains with the local administrator. I appreciate your understanding and encourage responsible exploration of this management system.**

<br />

![PawCare Dashboard Placeholder](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

<br />

## Tech Stack

- **Framework:** [Laravel 12](https://laravel.com)
- **Authentication:** Laravel Session-based Auth
- **Database:** MySQL
- **ORM:** Eloquent ORM
- **Graphics:** [Simple QR Code](https://github.com/SimpleSoftwareIO/simple-qrcode)
- **Icons:** [Lucide Icons](https://lucide.dev/) & [Font Awesome](https://fontawesome.com/)
- **Styling:** [Bootstrap 4](https://getbootstrap.com)
- **Validation:** Laravel Request Validation
- **Hosting:** Local / VPS

<br />

## Features:

- [x] Multi-role Authentication (Admin, Vet, Pet Owner)
- [x] Unified Pet Database with Search functionality
- [x] Centralized Vaccine Inventory (Manage Stock & Prices)
- [x] Automatic Stock Deduction on Vaccination
- [x] Digital Pet Identity Cards with unique QR codes
- [x] Public Pet Profile accessible via QR Scan
- [x] Vaccination Status Tracking (Vaccinated vs Unvaccinated)
- [x] Responsive Dashboard UI for all roles
- [x] Modern aesthetics with Glassmorphism and vibrant gradients

<br />

## Roadmap:

- [ ] Implement mobile navigation for dashboards
- [ ] Add Appointment Booking functionality
- [ ] Implement automated Email/SMS reminders for next due dates
- [ ] Add Financial Reports and Daily Sales tracking
- [ ] Integrate real-time chat between Owners and Clinic
- [ ] Cloud Storage for Pet medical documents (X-rays, Lab results)
- [ ] Multi-language support
- [ ] Dark Mode toggle
- [ ] Comprehensive Unit and Feature testing
- [ ] CI/CD pipeline implementation
