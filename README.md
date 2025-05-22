# Budget Management System

A PHP-based budget management application for tracking departmental budget demands and deposits.

## Features

- User authentication and role-based access control
- Department management
- Budget deposit tracking
- Demand creation and approval workflow
- Admin dashboard with budget overview
- User management

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

## Installation

1. Clone or extract this repository to your web server directory
2. Create a MySQL database named 'budget'
3. Import the database schema from `database.sql`
4. Configure database connection in `config/config.php` if needed
5. Access the application through your web browser

## Default Login

- Admin: admin@example.com / admin123 (change this in production!)

## Directory Structure

- `/actions` - Action handlers for user operations
- `/admin` - Admin panel and management interfaces
- `/auth` - Authentication related pages
- `/config` - Configuration files
- `/includes` - Reusable page components
- `/models` - Data models and business logic
- `/user` - User-specific pages

## Security Notes

- Change the default admin password immediately after installation
- Update database credentials in config/config.php
- Consider implementing HTTPS for production use

## License

This project is licensed under the MIT License.
