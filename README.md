# Grant Portal

A PHP-based grant application and management system with user authentication, admin dashboard, and email notifications.

## Features

- User registration and login
- Grant application system
- Admin dashboard for managing users and transactions
- Wallet system for deposits/withdrawals
- Real-time chat between users and admins
- Email notifications via Gmail SMTP
- File upload for payment proofs

## Local Development Setup

1. **Install XAMPP** (Apache + MySQL + PHP)
2. **Clone/download** this repository to `C:\xampp\htdocs\grant_portal`
3. **Create database:**
   ```sql
   CREATE DATABASE grant_portal;
   ```
4. **Import database schema** (if provided) or create tables manually
5. **Configure environment:**
   - Copy `.env.example` to `.env`
   - Update database credentials in `.env`
   - Update Gmail credentials in `.env`
6. **Start XAMPP** and visit `http://localhost/grant_portal`

## Deployment to Render

1. **Push to GitHub** (excluding sensitive files via `.gitignore`)
2. **Create Render account** at [render.com](https://render.com)
3. **Connect GitHub repo** to Render Web Service
4. **Set environment variables** in Render dashboard:
   ```
   DB_HOST=your-mysql-host
   DB_USER=your-db-user
   DB_PASS=your-db-password
   DB_NAME=your-db-name
   GMAIL_USERNAME=your-gmail@gmail.com
   GMAIL_APP_PASSWORD=your-app-password
   ADMIN_EMAIL=your-gmail@gmail.com
   ```
5. **Add MySQL database** via Render's Database service
6. **Deploy** - Render auto-deploys on git push

## Database Tables

Required tables:
- `users` - User accounts
- `transactions` - Deposits/withdrawals
- `grants` - Available grants
- `applications` - Grant applications
- `chat_messages` - Chat system

## Technologies Used

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5
- PHPMailer
- Font Awesome
- jQuery

## Security Notes

- Never commit `.env` file to GitHub
- Use strong passwords for Gmail app password
- Keep PHPMailer updated
- Use prepared statements for database queries

## License

MIT License