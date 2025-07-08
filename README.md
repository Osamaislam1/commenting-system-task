# Commenting System Task (Laravel + Livewire)

A simple nested commenting system with depth limit, built with Laravel 12 and Livewire.

## Features

- CRUD for posts
- Posts and nested comments (up to 3 levels deep)
- Replies disabled at max depth
- Recursive comment display with Livewire
- Scheduled command to delete empty comments

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
   ```
3. Copy `.env.example` to `.env` and set your database credentials or you can use sqlite database for testing
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
6. Serve the application:
   ```bash
   php artisan serve
   ```
7. Visit `http://localhost:8000/`

## Scheduled Command

To delete comments with empty content, run:
```bash
php artisan schedule:run
```
This is scheduled to run every minute (manual trigger recommended).

## Usage

- CRUD for posts
- Create posts and view them
- Add comments and replies to posts
- Replies are limited to 3 levels deep
- Comments with empty content are deleted by the scheduled command

