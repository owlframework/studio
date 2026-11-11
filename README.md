# 🦉 Owl Framework

Owl Framework is a lightweight, high-performance, and security-first PHP MVC framework engineered from the ground up for modern web applications, administrative panels, and fintech portals. It eliminates bloated third-party dependencies in favor of a clean, native, and robust architecture.

---

## 🚀 Key Features

*   **Secure Authentication & Throttling:** Built-in database-backed brute-force protection (`login_attempts` and `lockout_time`) to automatically secure user and admin accounts against credential stuffing.
*   **Privilege-Aware Admin Impersonation:** Allows authorized administrators to securely troubleshoot user accounts with built-in anti-escalation guards (preventing lower-tier staff from hijacking higher-level roles).
*   **Strict CSP Nonce Integration:** Native Content Security Policy (CSP) nonce generation (`get_nonce()`) to completely block Cross-Site Scripting (XSS) vulnerabilities.
*   **Clean MVC Architecture:** Strict separation of concerns utilizing a dedicated `core/` engine and an application-specific `app/` layer.
*   **Database Abstraction:** Secure PDO wrapper with intuitive CRUD methods and model-based query building.
*   **Built-in SMTP Management:** Dynamic mail server configuration and management straight from the admin dashboard.

---

## 📂 Anatomy of the Framework

The project structure is organized for maximum maintainability and clean separation of concerns:

```text
Owl_Studio/
├── api/                  # Endpoints for third-party service communication
├── app/                  # Application-specific business logic
│   ├── Requests/         # Route request handlers Contains the (auth, admin) folder
│   ├── models/           # Data models (User, Login, Mail, etc.)
│   └── Mailer/           # Built-in dynamic SMTP mail configuration
├── core/                 # Framework engine (Internal core files)
│   ├── Config.php        # Core application configuration settings
│   ├── Database.php      # PDO database connection layer
│   ├── Env.php           # Environment variable management (.env parser)
│   ├── ErrorHandler.php  # System debugging and error reporting
│   ├── Functions.php     # Global core helper functions
│   ├── Model.php         # Base model CRUD abstraction layer
│   ├── Response.php      # HTTP redirect and response utilities
│   ├── Route.php         # URL router and request handler
│   ├── Session.php       # Session management and flash messaging
│   ├── SessionTracker.php# Active user session monitoring
│   └── Validator.php     # Request payload validation engine
├── public/               # Web root assets
│   └── assets/           # Static assets (CSS, JS, Images)
├── vendor/               # Composer dependencies and autoloading
├── views/                # Presentation layer templates
│   ├── frontend/         # Public-facing user views
│   ├── backend/          # Administrative control panels
│   ├── email_template/   # Dynamic email notification templates
│   └── 404/              # Custom HTTP error pages
├── .env                  # Private system credentials and environment variables
├── .htaccess             # Apache URL rewriting and routing rules
├── index.php             # Front controller entry point
├── Nest.php              # Core framework bootstrapper
└── schema.sql            # Initial database schema export


⚙️ Requirements
PHP: >= 8.0 (Native typed properties and strict typing supported)

PHP (Recommended): >= 8.3

Database: MySQL / MariaDB

Web Server: Apache (with mod_rewrite enabled) or Nginx

📥 Installation & Setup
Clone or Download the Repository:
Place the project files into your web server directory (e.g., htdocs or your local virtual host root path).

Configure Database Connection:

Production / Remote Server: Update your database credentials inside your root .env file.

Local Machine (Without .env support): Locate the Config.php file inside the core/ folder and update your database credentials within the $is_local configuration block.

Configure Apache .htaccess:
Ensure your root .htaccess file is active to route all traffic through index.php:

Apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [L,QSA]
</IfModule>

Enable HTTPS (Production Only):
Uncomment the following block in your .htaccess file to enforce secure HTTPS connections on live production servers:

Apache
<IfModule mod_rewrite.c>
    # Force HTTPS (Comment this out while working locally on http://localhost)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

🛡️ Security Overview
Owl Framework prioritizes proactive defense:

Session Fixation Protection: Automated session ID regeneration upon successful login.

Timing Attack Prevention: Constant-time hash checks implemented on authentication lookups.

Input Sanitization: Native trimming, typing checks, and data validation wrappers across request controllers.

📄 License
Owl Framework is open-source software licensed under the MIT License.

Copyright (c) 2026 Owl Global Network. See the LICENSE file for more details.