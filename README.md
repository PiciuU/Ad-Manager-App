# AdManager

AdManager is a comprehensive advertisement management system that allows users to create, manage, and monitor their advertising campaigns. The system provides features for creating and editing ads, tracking ad performance, managing invoices, and more. The application is built using Vue 3 and Laravel 10, providing performance, scalability, and intuitive user interfaces.

## Live Demo

A live version of the application is available at: [dev.dream-speak.pl/system](https://dev.dream-speak.pl/system)

## Features

- User registration and login
- Creation and management of advertising campaigns
- Ad creation, editing, and scheduling
- Tracking ad performance through views and clicks
- Invoice management for ad payments
- Notification system for important updates
- Logging system for tracking operations and activities
- And more!

## Technologies

- Vue 3 - a modern JavaScript framework for building user interfaces
- Laravel 10 - a powerful PHP framework for rapid and secure web application development

## System Requirements

- Node.js (version >= 18.12.0)
- Npm (version >= 8.19.2)
- PHP (version >= 8.1)
- Composer (version >= 2.2.6)
- MySQL or any other database

## Installation

1. Clone the repository: `git clone https://github.com/PiciuU/ad-manager-system.git`
2. Navigate to the project directory: `cd ad-manager-system`
3. Install backend dependencies: `cd server && composer install`
4. Create and configure the `.env` file with the appropriate database credentials
5. Generate application key: `php artisan key:generate`
6. Run database migrations: `php artisan migrate` (You can run migration with data to test application using `php artisan migrate:refresh --seed`)
7. Install frontend dependencies: `cd ../client && npm install`
8. Create and configure the `.env.development` file with the appropriate data
9. Start the Laravel backend server: `cd ../server && php artisan serve`
10. In a separate terminal, start the Vue frontend server: `cd ../client && npm run dev`

Please make sure to have PHP, Composer, and a compatible database installed on your system before proceeding with the installation.

## Authors

All rights reserved. This repository is authored by PiciuU, mokrzycj, T4YF4N.