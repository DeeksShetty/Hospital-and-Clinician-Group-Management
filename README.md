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

1. Clone the repository:
   ```bash
   git clone https://github.com/DeeksShetty/Hospital-and-Clinician-Group-Management.git
   cd Hospital-and-Clinician-Group-Management
2. Copy environment file
