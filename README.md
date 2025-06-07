# Event Management

## 🚀 Getting Started

Follow these steps to install and run the Event Management project on your machine.

---

## 🧰 System Requirements

### General Requirements

| Requirement     | Version                                    |
|-----------------|--------------------------------------------|
| PHP             | >= 8.2                                     |
| Composer        | >= 2.5                                     |
| Database        | MySQL 8+ / PostgreSQL 13+ / SQLite         |
| Git             | Latest                                     |

### Docker Requirements

#### Windows:
- **WSL2 (Windows Subsystem for Linux v2)**
- **Docker Desktop** (with WSL integration enabled)
- Ubuntu or Debian as your WSL distro

#### macOS / Linux:
- Docker installed and running

---

### 🐘 Option 1: Using Docker with Laravel Sail (Recommended)

1. **Clone the repository**

    ```sh
    git clone https://github.com/Bp3g3H/event-management.git
    cd event-management
    ```

2. **Copy and configure your environment**

    ```sh
    cp .env.example .env
    ```
    Edit the `.env` file to set your database credentials and other environment variables as needed.

3. **Install dependencies using Sail**

    ```sh
    ./vendor/bin/sail up -d
    ./vendor/bin/sail composer install
    ```

4. **Generate application key**

    ```sh
    ./vendor/bin/sail artisan key:generate
    ```

5. **Run migrations**

    ```sh
    ./vendor/bin/sail artisan migrate
    ```

    > **Note:** This will also create a default admin user if you have set `DEFAULT_ADMIN_EMAIL` and `DEFAULT_ADMIN_PASSWORD` in your `.env` file.

6. **(Optional) Seed additional data**

    ```sh
    ./vendor/bin/sail artisan db:seed
    ```

7. **Access the application**

    The application will be available at [http://localhost](http://localhost).

---

### 💻 Option 2: Manual Local Setup

1. **Clone the repository**

    ```sh
    git clone https://github.com/Bp3g3H/event-management.git
    cd event-management
    ```

2. **Install dependencies**

    ```sh
    composer install
    ```

3. **Copy and configure your environment**

    ```sh
    cp .env.example .env
    ```
    Edit the `.env` file to set your database credentials and other environment variables as needed.

4. **Generate application key**

    ```sh
    php artisan key:generate
    ```

5. **Run migrations**

    ```sh
    php artisan migrate
    ```

    > **Note:** This will also create a default admin user if you have set `DEFAULT_ADMIN_EMAIL` and `DEFAULT_ADMIN_PASSWORD` in your `.env` file.

6. **(Optional) Seed additional data**

    ```sh
    php artisan db:seed
    ```

7. **Start the development server**

    ```sh
    php artisan serve
    ```

    The application will be available at [http://localhost:8000](http://localhost:8000).

---

## 📚 About

Event Management is a Laravel-based web application for managing events, attendees, and users with role-based access control.

---

## 📑 Postman Collection

You can find and use the full API documentation and example requests in the shared Postman collection:

[Event Management System Postman Collection](https://www.postman.com/test22-5006/workspace/eventmanagementsystem/collection/21046332-9d35c61b-8d72-4aea-9832-a10835068a43?action=share&creator=21046332&active-environment=21046332-eaf2d077-582e-4f39-878d-6b7067e745f4)

---

## 🛡️ License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).