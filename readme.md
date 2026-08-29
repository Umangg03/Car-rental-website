# Car Rental Project

A PHP-based car rental web application for browsing available vehicles, booking rides, managing bookings, and operating an admin dashboard. The project is designed for a local WAMP/XAMPP environment and uses MySQL as its database.

## 1. Project Overview

This application allows:

- Users to browse cars on the home and cars pages.
- Search cars by name or type.
- View detailed car information and rental availability.
- Book cars with customer details and driving licence upload.
- Track bookings in "My Bookings".
- Update or cancel bookings.
- Pay a deposit for bookings through a mock payment flow.
- View generated booking receipts.
- Admin users to add, edit, delete, and manage cars and bookings.
- Admin users to monitor dashboard statistics and customer messages.

The app combines:

- PHP for backend logic
- MySQL for persistence
- JavaScript for client-side interaction
- CSS and HTML for UI styling

---

## 2. Tech Stack

- PHP 7+
- MySQL / MariaDB
- Apache / WAMP / XAMPP
- JavaScript
- HTML & CSS
- Session-based authentication
- File uploads for driving licences and car images

---

## 3. Main Features

### Customer Features

- Landing page with featured cars and brand sections
- Car listing and search
- Car detail modal with booking form
- Booking date validation and availability checks
- Driving licence upload requirement during booking
- Booking summary with deposit logic
- Payment page and mock transaction flow
- Booking receipt page
- User booking dashboard with status tracking
- Update and cancel booking actions

### Admin Features

- Dashboard with total cars, users, bookings, and revenue stats
- Add new car listing with image upload
- Manage existing cars
- Manage bookings and approvals
- Check uploaded licence documents
- Cancel or confirm bookings
- Review recent customer messages

---

## 4. Project Structure

### Root Folder Files

- `index.php` - Home page with featured cars and landing sections
- `cars.php` - Car listing page with search and booking modal
- `about.php` - About page
- `contact.php` - Contact form page
- `my-bookings.php` - User booking management page
- `navbar.php` - Shared navigation for all pages
- `footer.php` - Shared footer
- `payment.php` - Payment screen for booking deposit
- `receipt.php` - Receipt view for confirmed bookings
- `auth_modal.php` - Reusable login/signup modal
- `main.css` - Main styling for general website layout
- `cars.css` - Styling for car listing and booking UI
- `about.css` - Styling for About page
- `contact.css` - Contact page styling
- `my-bookings.css` - Booking dashboard styles
- `Components.css` - Shared component styling
- `animations.css` - Scroll reveal and visual animation effects
- `nav.js` - Navigation behavior and auth state logic
- `utils.js` - Utility functions for auth, search, logout, and car filtering
- `bookings.js` - Additional booking-related browser logic if used in the project
- `search.js` - Search behavior support
- `admin.html` - Legacy/admin entry page
- `mcar.png`, `bancar.png`, `carlogo1.png`, etc. - project images and branding assets
- `uploads/` - uploaded car images and documents

### Backend Folder

The `backend/` directory contains all PHP logic for database operations and API endpoints.

- `db.php` - MySQL database connection setup
- `setup.php` - Creates the database and required tables
- `create_admin.php` - Helper script to create an admin user record
- `admin_auth.php` - Admin signup/login logic
- `login.php` - Customer login API
- `signup.php` - Customer signup endpoint
- `logout.php` - Logout endpoint
- `check_session.php` - Session state JSON for frontend auth checks
- `get_cars.php` - Returns car list as JSON
- `get_bookings.php` - Returns logged-in user's bookings as JSON
- `book_car.php` - Handles booking creation, validation, document upload, and deposit calculations
- `confirm_booking.php` - Booking confirmation flow
- `update_booking.php` - Updates booking fields
- `delete_booking.php` - Cancels or removes a booking
- `check_availability.php` - Checks car availability for selected dates
- `save_message.php` - Saves visitor messages from contact form
- `admin_dashboard.php` - Main admin dashboard with statistics and recent records
- `admin_add_car.php` - Admin interface for adding cars
- `manage_cars.php` - Admin car management page
- `manage_bookings.php` - Admin booking management page
- `edit_car.php` - Admin edit car page
- `delete_car.php` - Deletes a car
- `admin_updatebooking.php` - Updates booking status from admin interface

---

## 5. Database Design

The project expects a MySQL database called `carrental`.

The setup script creates these tables:

### users
- id
- name
- email
- password
- role (referenced in session/login logic, though some parts of the app rely on it dynamically)

### cars
- id
- name
- type
- transmission
- year
- price
- seats
- fuel
- location
- description
- image

### bookings
- id
- user_id
- car_id
- car_name
- booking_date
- start_date
- end_date
- status
- customer_name
- customer_email
- customer_phone
- customer_address
- total_amount
- deposit_amount
- payment_status
- mock_transaction_id
- licence_front

### admins
- id
- email
- password

### messages
- used by the contact form to store customer messages

---

## 6. How the App Works

### User Flow

1. Visitor lands on the home page.
2. User can browse featured cars or open the full car list.
3. User searches for a car and opens the booking modal.
4. User selects custom dates and fills customer details.
5. Driving licence front image must be uploaded.
6. Booking is created as `Awaiting Payment` if valid.
7. User goes to the payment page and completes a mock secure payment.
8. Booking status is updated and receipt can be viewed.
9. User can access their booking list from `my-bookings.php`.

### Admin Flow

1. Admin logs in through the role-based session flow.
2. Dashboard displays totals and recent records.
3. Admin can add cars with images and details.
4. Admin reviews bookings, confirms or cancels them.
5. Admin checks uploaded documents and payment states.
6. Admin manages the car inventory.

---

## 7. Local Setup Steps

### Requirement

Install one of the following:

- WAMP (recommended for Windows)
- XAMPP
- Another PHP + MySQL stack

### Step 1: Place the project in the web server folder

If you are using WAMP:

- Copy the project folder to `C:/wamp64/www/`
- The project folder should be something like:
  - `C:/wamp64/www/car_rental1`

If you are using XAMPP:

- Copy the project folder to `C:/xampp/htdocs/`

### Step 2: Start Apache and MySQL

- Start WAMP or XAMPP
- Make sure both Apache and MySQL are running

### Step 3: Create the database tables

Open your browser and visit:

- `http://localhost/car_rental1/backend/setup.php`

This script creates the database named `carrental` and creates the tables required by the app.

### Step 4: Create an admin account (if needed)

Open:

- `http://localhost/car_rental1/backend/create_admin.php`

This helper inserts an admin user in the database.

> Note: The project depends on a role-based login flow, so depending on your environment you may need to adjust admin creation or login logic to match your database schema.

### Step 5: Open the app

- Home page: `http://localhost/car_rental1/index.php`
- Cars page: `http://localhost/car_rental1/cars.php`
- My bookings: `http://localhost/car_rental1/my-bookings.php`
- Admin dashboard: `http://localhost/car_rental1/backend/admin_dashboard.php`

---

## 8. Database Connection Settings

The project uses this configuration in `backend/db.php`:

- Host: `localhost`
- Username: `root`
- Password: empty string
- Database: `carrental`

This matches the default WAMP setup.

If your local MySQL has a different username or password, update this file before running the app.

---

## 9. Important Notes

- The project uses sessions extensively, so the browser must allow cookies.
- The app expects the `uploads/` folder to be writable.
- Uploaded files are stored in `uploads/` and `uploads/documents/`.
- For a new system, ensure file permissions allow PHP to write to these folders.
- If the project is running on a different server or PHP version, the database and file permissions may need minor adjustments.

---

## 10. Recommended Development Workflow

1. Start WAMP/XAMPP.
2. Run `backend/setup.php` once.
3. Create admin user if required.
4. Load `index.php` to view the site.
5. Register/login as a customer to browse and book cars.
6. Use the admin dashboard to manage the fleet.

---

## 11. Typical User Credentials

The project includes helper scripts for admin account creation, but the actual credentials will depend on what you insert in the database.

Example admin creation script content in `backend/create_admin.php` is:

- Name: `Umang`
- Email: `umangchaudhari670@gmail.com`
- Password: `umang@123`

You may need to update these values or create your own admin record based on your local setup.

---

## 12. Project Summary

This is a full-stack PHP car rental application focused on:

- vehicle browsing
- online booking
- payment flow simulation
- user booking management
- admin fleet and booking management

It is a good learning project or base for a real rental business portal, and it is ready to be customized with additional features such as:

- email confirmation
- stronger role-based auth
- production database security
- modern payment integration
- better validation and sanitization
- admin login page and dashboard polish

---

## 13. Quick Start

```bash
# 1. Start Apache and MySQL in WAMP/XAMPP
# 2. Put the project in the web root
# 3. Visit:
http://localhost/car_rental1/backend/setup.php
# 4. Visit:
http://localhost/car_rental1/index.php
```

---

## 14. File Notes

This project has a mix of normal page files and backend API files. In general:

- Pages in the root folder render the website UI.
- API and database logic live in the `backend/` folder.
- CSS files style the front-end pages.
- Uploaded images/documents are saved under `uploads/`.

---

## 15. Final Note

This project is highly suitable for educational use, demo builds, or as a starting point for a larger rental business website. With a few refinements, it can be expanded into a production-ready platform.

If you want, this README can also be expanded with screenshots, a feature roadmap, and a developer setup guide for Windows, Linux, and Mac.
