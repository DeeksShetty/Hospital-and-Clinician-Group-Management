# Hospital and Clinician Group Management API

A RESTful API built with **Laravel 12** for managing hospitals and clinician groups in a hierarchical (tree) structure.  
This project was developed as part of an assessment task given by EGDK INDIA PRIVATE LIMITED.

---

## 📌 Features

- **Authentication**: Secure login/logout using **Laravel Sanctum**.  
- **Group Management**:
  - Create groups under hospitals (group without parent cosider as parent group or hospitals) or other groups.
  - Retrieve groups (single or full tree).
  - Update group details with validations to prevent:
    - A group being its own parent.
    - Cyclic/descendant relationships.
  - Delete groups (only if no children exist).  
- **Validation**: Robust request validation using Laravel Form Requests.  
- **API Documentation**: Integrated with **Swagger (l5-swagger)** for interactive API docs.  
- **Testing**: Includes unit and feature tests for core functionalities.  

---

## 🚀 Tech Stack

- **Backend**: PHP 8.2, Laravel 12  
- **Database**: MySQL 8
- **Authentication**: Laravel Sanctum  
- **Documentation**: Swagger (OpenAPI 3.0 via l5-swagger)  
- **Testing**: PHPUnit, Laravel Test Suite  
- **Containerization**: Docker + Laravel Sail  
- **Database UI**: PhpMyAdmin  

---

## ⚙️ Installation

1.  Clone the repository:
    ```bash
    git clone https://github.com/DeeksShetty/Hospital-and-Clinician-Group-Management.git
    cd Hospital-and-Clinician-Group-Management
-  Linux / macOS
You can clone the repository anywhere on your system. Docker Desktop (macOS) or native Docker (Linux) will work without issues.
-  Windows with Docker Desktop + WSL2
It is recommended to clone the repository inside your WSL2 home directory (e.g., /home/<username>/projects/...).
This improves performance and avoids permission issues when Docker mounts project files.
Example:
```bash
C:\Users\Deekshith shetty>wsl
cd ~/projects
git clone https://github.com/DeeksShetty/Hospital-and-Clinician-Group-Management.git

2.  Copy environment file
    ```bash
    cp .env.example .env
3.  Update .env with your DB credentials (already set for Sail):
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE="h_and_c_group_management"
    DB_USERNAME=admin
    DB_PASSWORD=password
4.  Build and start containers
    ```bash
    ./vendor/bin/sail up -d
5.  Install dependencies
    ```bash
    ./vendor/bin/sail composer install
6.  Run migrations
    ```bash
    ./vendor/bin/sail artisan migrate
7.  Run seeder (create admin user)
    ```bash
    ./vendor/bin/sail artisan db:seed
8.  Generate app key
    ```bash
    ./vendor/bin/sail artisan key:generate


## 🌐 API Access

Once the application is running via Sail:

- Site URL: http://localhost:8080
- API URL: http://localhost:8080/api  
- PhpMyAdmin: http://localhost:8088 (login with DB_USERNAME / DB_PASSWORD)

### Available Endpoints
- POST /login
- POST /logout
- GET /groups
- GET /groups/{id}
- POST /groups
- PUT /groups/{id}
- DELETE /groups/{id}


## 📖 API Documentation

Swagger docs are generated with l5-swagger.
1.  Generate docs:
    ```bash
    ./vendor/bin/sail artisan l5-swagger:generate
2.  Access UI: http://localhost:8080/api/documentation


## 🧪 Running Tests
    ./vendor/bin/sail artisan test



## 🔑 Authentication Flow

1.  Login: POST /api/login → Returns user info + Sanctum token.
2.  Authenticated routes: Pass token in header:
    ```bash
    Authorization: Bearer {token}
3.  Logout: POST /api/logout → Revokes the current token.


## 📂 Project Structure (Key Parts)
    app/
    ├── Http/
    │   ├── Controllers/
    │   │   ├── AuthController.php
    │   │   ├── GroupController.php
    │   ├── Requests/
    │   │   ├── CreateGroupRequest.php
    │   ├── Traits/
    │   │   ├── ResponseTrait.php
    │
    ├── Models/
    │   ├── Group.php
    │   ├── User.php
    │
    ├── Services/
    │   ├── GroupService.php

## 📌 Notes

1.  Built with Laravel Sail (Docker-first dev environment).
2.  PhpMyAdmin runs at port 8088 for DB management.
3.  Swagger/OpenAPI 3.0 compliant API docs are available for frontend integration.

📌 Author

👤 Deekshith Shetty
