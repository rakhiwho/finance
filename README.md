# 💰 Financial Management Backend API

A backend system for managing users, roles, and financial records with role-based access control and dashboard analytics.

---

## 🚀 Features

### 👤 User & Role Management

* Create and manage users
* Assign roles: **Viewer, Analyst, Admin**
* Activate / Deactivate users
* Role-based access control (RBAC)

---

### 💵 Financial Records Management

* Create, update, delete financial records
* Fields:

  * Amount
  * Type (Income / Expense)
  * Category
  * Date
  * Notes
* Filter records by:

  * Type
  * Category
  * Date range

---

### 📊 Dashboard APIs

* Total income
* Total expenses
* Net balance
* Category-wise totals
* Monthly trends
* Recent transactions

---

### 🔐 Access Control

| Role    | Permissions                   |
| ------- | ----------------------------- |
| Viewer  | View dashboard       |
| Analyst | View records + analytics      |
| Admin   | Full access (users + records) |

---

## 🛠️ Tech Stack

* **Backend:** Laravel
* **Database:** SQLite
* **Auth:** Laravel Sanctum
* **Architecture:** REST API

---

## ⚙️ Setup Instructions

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd <project-folder>
```

---

### 2. Install dependencies

```bash
composer install
```

---

### 3. Setup environment

```bash
cp .env.example .env
```

Update `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

---

### 4. Create SQLite database file

```bash
touch database/database.sqlite
```

(Windows: create file manually)

---

### 5. Run migrations

```bash
php artisan migrate
```

---

### 6. Start server

```bash
php artisan serve
```

---

## 🔑 Creating Admin User

Use Laravel Tinker:

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'your@email.com')->first();
$user->role = 'admin';
$user->status = 'active';
$user->save();
```

---

## 📡 API Endpoints

### 🔐 Auth

* `POST /auth/register`
* `POST /auth/login`

---

### 👤 Users (Admin only)

* `GET /users`
* `PUT /users/{id}`
* `PATCH /users/{id}/status`
* `PATCH /users/{id}/role`

---

### 💰 Financial Records

#### Read (All roles)

* `GET /records`
* `GET /records/{id}`

#### Admin only

* `POST /records`
* `PUT /records/{id}`
* `DELETE /records/{id}`

---

### 🔍 Filtering Example

```http
GET /records?type=income&category=salary&startDate=2025-01-01&endDate=2025-12-31
```

---

### 📊 Dashboard

* `GET /dashboard/summary`
* `GET /dashboard/categories`
* `GET /dashboard/trends`
* `GET /dashboard/recent`

---

## 🧠 Design Decisions

* Used **SQLite** for simplicity and portability
* Implemented **role-based middleware** for access control
* Separated **CRUD APIs** and **analytics APIs**
* Used **query parameters** for flexible filtering

---

## 🔮 Future Improvements

* Add pagination & sorting enhancements
* Implement soft deletes
* Add audit logs (user activity tracking)
* Support multi-user financial data isolation
* Add frontend dashboard

---

## 📌 Summary

This project demonstrates:

* Clean API design
* Role-based authorization
* Scalable backend structure
* Real-world financial data handling

---

 Test Credentials (For Demo Only)

 These credentials are provided strictly for testing purposes.

Email: rakhi@test.com
Password: 123456

## 👨‍💻 Author

Rakhi Dubey
