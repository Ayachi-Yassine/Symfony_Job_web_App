# JobEntry - Symfony Job Portal Setup Guide

## Project Overview
JobEntry is a full-featured job portal built with Symfony 7.4 and MySQL. It allows job seekers to browse jobs by category, search for positions, and view detailed job listings. Employers can manage job postings through the admin interface.

## Prerequisites

Before starting, ensure you have the following installed on your system:

1. **PHP 8.1 or higher** - [Download](https://www.php.net/downloads)
2. **Composer** - [Download](https://getcomposer.org/download/)
3. **MySQL Server** - [Download](https://dev.mysql.com/downloads/mysql/)
4. **Git** (optional) - [Download](https://git-scm.com/downloads)

## Installation Steps

### Step 1: Database Setup

1. **Start MySQL Server**
   - Windows: Start the MySQL service from Services or use `mysql` command
   - Linux/Mac: Run `sudo systemctl start mysql` or `brew services start mysql@8.0`

2. **Verify MySQL is running**
   ```bash
   mysql -u root -p
   ```
   - Enter your password (leave blank if none)
   - Type `EXIT` to quit

### Step 2: Navigate to Project Directory

```bash
cd "c:\Users\yassi\Desktop\New folder\job-portal-symfony"
```

### Step 3: Create the Database

The database `job_portal` has already been created during setup with the connection:
- **Host:** 127.0.0.1
- **User:** root
- **Password:** (empty)
- **Database:** job_portal

### Step 4: Install Dependencies

```bash
composer install
```

This command installs all required PHP packages listed in `composer.json`.

### Step 5: Run Migrations

The database tables have already been created, but if you need to migrate again:

```bash
php bin/console doctrine:migrations:migrate
```

### Step 6: Seed Sample Data (Optional)

To add sample data to test the application, create a migration or use the command line:

```bash
php bin/console doctrine:fixtures:load
```

**Or manually via MySQL:**

```sql
USE job_portal;

-- Insert categories
INSERT INTO categories (name, description, created_at) VALUES
('Software Development', 'Software engineering and development roles', NOW()),
('Design', 'UI/UX and graphic design positions', NOW()),
('Marketing', 'Marketing and digital marketing jobs', NOW()),
('Sales', 'Sales and business development roles', NOW());

-- Insert sample jobs
INSERT INTO jobs (title, description, company, location, salary, job_type, category_id, is_active, created_at, updated_at) VALUES
('Senior PHP Developer', 'We are looking for an experienced PHP developer...', 'TechCorp Inc', 'San Francisco, CA', '120000', 'Full Time', 1, 1, NOW(), NOW()),
('UI/UX Designer', 'Design beautiful and intuitive user interfaces...', 'DesignStudio Co', 'New York, NY', '90000', 'Full Time', 2, 1, NOW(), NOW()),
('Digital Marketing Manager', 'Lead our digital marketing initiatives...', 'MarketingPro Ltd', 'Chicago, IL', '85000', 'Full Time', 3, 1, NOW(), NOW()),
('Sales Executive', 'Join our growing sales team...', 'SalesCorp Global', 'Los Angeles, CA', '75000', 'Full Time', 4, 1, NOW(), NOW());
```

## Running the Application

### Step 1: Start the Symfony Development Server

```bash
symfony server:start
```

Or if you don't have Symfony CLI installed:

```bash
php -S localhost:8000 -t public
```

### Step 2: Access the Application

Open your web browser and navigate to:

```
http://localhost:8000
```

or

```
http://127.0.0.1:8000
```

## Project Structure

```
job-portal-symfony/
├── bin/
│   └── console              # Symfony console commands
├── config/
│   ├── packages/           # Package configurations
│   └── routes.yaml         # Route definitions
├── migrations/             # Database migrations
├── public/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   ├── img/               # Images
│   ├── lib/               # Third-party libraries
│   └── index.php          # Application entry point
├── src/
│   ├── Controller/        # Application controllers
│   ├── Entity/            # Database entities (Job, Category)
│   └── Repository/        # Database repositories
├── templates/
│   ├── base.html.twig     # Base template
│   ├── home/              # Home pages
│   ├── job/               # Job pages
│   └── category/          # Category pages
├── var/
│   └── cache/             # Cache files
├── .env                   # Environment configuration
└── composer.json          # PHP dependencies
```

## Routes

### Frontend Routes

| Route | Description |
|-------|-------------|
| `/` | Home page with featured jobs |
| `/about` | About page |
| `/contact` | Contact page |
| `/jobs` | Jobs listing page with search and filter |
| `/jobs/{id}` | Individual job detail page |
| `/categories` | Job categories listing |
| `/categories/{id}` | Jobs by category |
| `/testimonials` | Testimonials/success stories page |

## Database Schema

### Categories Table
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    created_at DATETIME NOT NULL
);
```

### Jobs Table
```sql
CREATE TABLE jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description LONGTEXT NOT NULL,
    company VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    salary DECIMAL(10, 2),
    job_type VARCHAR(50) NOT NULL,
    category_id INT NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

## Troubleshooting

### Issue: Database connection failed

**Solution:**
1. Verify MySQL is running: `mysql -u root`
2. Check .env file has correct DATABASE_URL
3. Recreate database: `php bin/console doctrine:database:drop --force && php bin/console doctrine:database:create`

### Issue: Port 8000 already in use

**Solution:**
Use a different port:
```bash
symfony server:start --port=8001
# or
php -S localhost:8001 -t public
```

### Issue: "No such file or directory" for composer

**Solution:**
Install Composer globally or use:
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php composer.phar install
```

### Issue: Twig template errors

**Solution:**
Clear cache:
```bash
php bin/console cache:clear
```

## File Permissions (Linux/Mac)

If you encounter permission issues:

```bash
chmod -R 777 var/
chmod -R 777 public/
```

## Performance Tips

1. **Cache warming (Production)**
   ```bash
   php bin/console cache:warmup --env=prod
   ```

2. **Asset compilation**
   ```bash
   php bin/console asset-map:compile
   ```

3. **Database optimization**
   Create indexes on frequently searched columns:
   ```sql
   CREATE INDEX idx_job_category ON jobs(category_id);
   CREATE INDEX idx_job_company ON jobs(company);
   CREATE INDEX idx_job_active ON jobs(is_active);
   ```

## Environment Configuration

The `.env` file contains application configuration:

```env
APP_ENV=dev
APP_DEBUG=true
DATABASE_URL="mysql://root:@127.0.0.1:3306/job_portal?serverVersion=8.0.32&charset=utf8mb4"
```

### Changing to Production

Create `.env.prod` or `.env.prod.local`:

```env
APP_ENV=prod
APP_DEBUG=false
DATABASE_URL="mysql://username:password@your-server:3306/job_portal"
```

Then run:
```bash
APP_ENV=prod composer install --no-dev
php bin/console cache:clear --env=prod
```

## Common Commands

| Command | Description |
|---------|-------------|
| `php bin/console doctrine:database:create` | Create database |
| `php bin/console make:entity` | Create new entity |
| `php bin/console make:controller` | Create new controller |
| `php bin/console make:migration` | Create migration file |
| `php bin/console doctrine:migrations:migrate` | Run migrations |
| `php bin/console cache:clear` | Clear application cache |
| `php bin/console list` | List all available commands |

## Features

✓ Browse job listings with pagination
✓ Search jobs by keyword
✓ Filter jobs by category
✓ View detailed job information
✓ Apply to jobs with quick form
✓ Category management
✓ Responsive design with Bootstrap
✓ Animated UI elements with WOW.js
✓ Owl Carousel for featured jobs
✓ MySQL database with Doctrine ORM

## Future Enhancements

- [ ] User authentication and profiles
- [ ] Admin panel for managing jobs and categories
- [ ] Email notifications
- [ ] User saved jobs list
- [ ] Application tracking system
- [ ] Company profiles and ratings
- [ ] Advanced job filters
- [ ] API endpoints
- [ ] Social login integration
- [ ] Application history and status tracking

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review Symfony documentation: https://symfony.com/doc
3. Check Doctrine ORM documentation: https://www.doctrine-project.org/

## License

This project is open source and available under the MIT License.

---

**Happy job hunting!** 🚀
