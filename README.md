# Secure Inventory REST API (PHP 8.2)

![PHP Version](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Deployed_with-Docker-2496ED?logo=docker&logoColor=white)
![Status](https://img.shields.io/badge/Status-Production_Ready-success)

Developed in **2023**, this project is a lightweight, high-performance RESTful API for inventory management. It was built with a "Zero-Dependency" philosophy (Vanilla PHP, no heavy frameworks like Laravel or Symfony) to maximize execution speed and minimize security vulnerabilities.

##  Technical Stack (2023 Standards)

* **Core:** PHP 8.2 (Strict Typing `declare(strict_types=1)`, Modern Syntax)
* **Database:** SQLite via PDO (Prepared Statements against SQL Injection)
* **Infrastructure:** Docker & Docker Compose for isolated deployment
* **Security:** Custom API Key Middleware & CORS Header Management

##  Deployment & Usage

Because this API is fully containerized, deployment takes less than a minute.

### 1. Start the Server
git clone [https://github.com/Assimous/secure-inventory-api-php.git](https://github.com/Assimous/secure-inventory-api-php.git)
cd secure-inventory-api-php
docker-compose up -d
