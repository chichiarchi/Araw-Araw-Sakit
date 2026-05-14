# Araw-Araw Sakit

A premium, mood-driven web application for the broken-hearted. Built with PHP 8.3 and SQLite.

## Features
- **Immersive Frontend**: Glassmorphic landing screen with video/audio autoplay bypass.
- **Dynamic Content**: Daily Filipino quotes with background media support.
- **Engagement Tracking**: Real-time view counts and interactive "Heart" button.
- **Admin Dashboard**: Secure control center for analytics and content management.
- **Premium Design**: Dark-themed, modern aesthetics with smooth animations.

## Setup Instructions

### 1. Requirements
- PHP 8.3+
- SQLite3 support enabled in PHP (`php-sqlite3`)
- A web server (Nginx, Apache, or built-in PHP server for testing)

### 2. Installation
1. Clone or copy the files to your web root.
2. Ensure the `includes/` directory and `database.sqlite` (it will be created automatically) are writable by the web server.
3. Place your background videos and optional audio files in `assets/media/`.

### 3. Media
- **Videos**: Use `.mp4` files. High-quality but optimized for web.
- **Audio**: Use `.mp3` files.

### 4. Admin Access
- The default admin password is **`admin123`**.
- To change it, update the hash in `login.php` (or use the planned `.env` implementation).
- To generate a new hash, run: `php -r "echo password_hash('your_new_password', PASSWORD_DEFAULT);"`

### 5. Local Development
You can run the app locally using the PHP built-in server:
```bash
php -S localhost:8000
```
Then visit `http://localhost:8000` for the public view and `http://localhost:8000/login.php` for the admin dashboard.

## Deployment
For production, it is recommended to:
- Use a reverse proxy (like Nginx).
- Secure the connection with SSL (e.g., Cloudflare Tunnel as suggested in the blueprint).
- Set proper file permissions for the SQLite database.
