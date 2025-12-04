# 🚀 JobEntry Symfony - Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Prerequisites Check

Make sure you have installed:
- ✅ PHP 8.1+ ([Download](https://www.php.net/downloads))
- ✅ MySQL Server ([Download](https://dev.mysql.com/downloads/mysql/))
- ✅ Composer ([Download](https://getcomposer.org/download/))

### Step 2: Navigate to Project

**Windows Command Prompt:**
```cmd
cd "c:\Users\yassi\Desktop\New folder\job-portal-symfony"
```

**Mac/Linux:**
```bash
cd ~/path/to/job-portal-symfony
```

### Step 3: Install Dependencies

```bash
composer install
```

### Step 4: Setup Database

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

### Step 5: Start Server

**Option A - Using Symfony CLI:**
```bash
symfony server:start
```

**Option B - Using PHP Built-in Server:**
```bash
php -S localhost:8000 -t public
```

### Step 6: Open in Browser

Visit: **http://localhost:8000**

---

## 📊 Add Test Data (Optional)

### MySQL Method

1. Open MySQL Client:
   ```bash
   mysql -u root
   ```

2. Select database:
   ```sql
   USE job_portal;
   ```

3. Add categories:
   ```sql
   INSERT INTO categories (name, description, created_at) VALUES
   ('Software Development', 'IT and software roles', NOW()),
   ('Design', 'UI/UX and graphic design', NOW()),
   ('Marketing', 'Marketing and advertising', NOW()),
   ('Sales', 'Sales and business development', NOW());
   ```

4. Add jobs:
   ```sql
   INSERT INTO jobs (title, description, company, location, salary, job_type, category_id, is_active, created_at, updated_at) VALUES
   ('Senior PHP Developer', 'We are seeking an experienced PHP developer to join our team...', 'TechCorp Inc', 'San Francisco, CA', '120000', 'Full Time', 1, 1, NOW(), NOW()),
   ('UI/UX Designer', 'Create beautiful and intuitive user interfaces for our applications...', 'DesignStudio Co', 'New York, NY', '90000', 'Full Time', 2, 1, NOW(), NOW()),
   ('Digital Marketing Manager', 'Lead our digital marketing initiatives and drive growth...', 'MarketingPro Ltd', 'Chicago, IL', '85000', 'Full Time', 3, 1, NOW(), NOW()),
   ('Sales Executive', 'Join our dynamic sales team and achieve great results...', 'SalesCorp Global', 'Los Angeles, CA', '75000', 'Full Time', 4, 1, NOW(), NOW());
   ```

---

## 🌐 Available Pages

| URL | Page | Features |
|-----|------|----------|
| `/` | Home | Featured jobs, categories, search |
| `/jobs` | Job Listings | All jobs with search & filter |
| `/jobs/{id}` | Job Detail | Full job info & apply form |
| `/categories` | Categories | Browse job categories |
| `/categories/{id}` | Category Jobs | Jobs by category |
| `/about` | About Us | Company information |
| `/contact` | Contact | Contact form |
| `/testimonials` | Testimonials | Success stories |

---

## 🛠️ Useful Commands

### Database Commands
```bash
# Check database status
php bin/console doctrine:schema:validate

# Generate new migration
php bin/console make:migration

# View all migrations
php bin/console doctrine:migrations:list

# Rollback last migration
php bin/console doctrine:migrations:migrate prev

# Drop and recreate database
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
```

### Development Commands
```bash
# Clear cache
php bin/console cache:clear

# Warm cache
php bin/console cache:warmup

# List all routes
php bin/console debug:router

# Debug entity relations
php bin/console doctrine:schema:validate
```

### Entity Commands
```bash
# Create new entity
php bin/console make:entity

# Create new controller
php bin/console make:controller

# Generate getter/setter
php bin/console make:entity --regenerate
```

---

## 🐛 Troubleshooting

### "MySQL connection refused"

**Solution:**
```bash
# Check if MySQL is running
mysql -u root

# If error, start MySQL:
# Windows: Search for MySQL service and start it
# Mac: brew services start mysql@8.0
# Linux: sudo systemctl start mysql
```

### "Port 8000 already in use"

**Solution:**
```bash
# Use different port
php -S localhost:8001 -t public

# Or find what's using port 8000
# Windows: netstat -ano | findstr :8000
```

### "Permission denied" errors

**Solution (Mac/Linux):**
```bash
chmod -R 777 var/
chmod -R 777 public/
```

### "Class not found" errors

**Solution:**
```bash
composer dump-autoload
php bin/console cache:clear
```

### Database doesn't exist

**Solution:**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

---

## 📂 Project Structure

```
job-portal-symfony/
├── README.md                    # Main documentation
├── SETUP_GUIDE.md              # Detailed setup
├── IMPLEMENTATION_CHECKLIST.md # What's been done
├── QUICK_START.md              # This file
│
├── src/
│   ├── Controller/              # Route handlers
│   ├── Entity/                  # Database models
│   └── Repository/              # Database queries
│
├── templates/                   # Twig templates
│   ├── base.html.twig          # Main layout
│   ├── home/                    # Home pages
│   ├── job/                     # Job pages
│   └── category/                # Category pages
│
├── public/
│   ├── css/                     # Stylesheets
│   ├── js/                      # JavaScript
│   ├── img/                     # Images
│   ├── lib/                     # Libraries
│   └── index.php                # Entry point
│
├── migrations/                  # Database migrations
├── config/                      # Configuration files
├── var/                         # Cache & logs
└── vendor/                      # PHP dependencies
```

---

## 🎨 Customization Quick Tips

### Change Site Title

Edit `templates/base.html.twig`:
```twig
<h1 class="m-0 text-primary">Your Company Name</h1>
```

### Change Primary Color

Edit `public/css/style.css`:
```css
:root {
    --primary-color: #your-color;
}
```

### Update Contact Information

Edit footer in `templates/base.html.twig`:
```twig
<p><i class="fa fa-phone-alt me-2"></i>Your Phone</p>
<p><i class="fa fa-envelope me-2"></i>your@email.com</p>
```

### Add Logo

Replace in `templates/base.html.twig`:
```twig
<img src="{{ asset('img/logo.png') }}" alt="Logo">
```

---

## 🔐 Security Notes

### Before Going Live

1. **Update .env for production:**
   ```env
   APP_ENV=prod
   APP_DEBUG=false
   DATABASE_URL="mysql://secure_user:secure_password@secure_host/db"
   ```

2. **Set strong database password:**
   ```bash
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'strong_password';
   ```

3. **Create separate database user:**
   ```sql
   CREATE USER 'jobentry'@'localhost' IDENTIFIED BY 'strong_password';
   GRANT ALL PRIVILEGES ON job_portal.* TO 'jobentry'@'localhost';
   ```

4. **Update permissions:**
   ```bash
   chmod 755 public/
   chmod 700 var/
   ```

---

## 📊 Database Info

**Current Connection:**
- **Host:** localhost (127.0.0.1)
- **User:** root
- **Password:** (empty)
- **Database:** job_portal
- **Charset:** utf8mb4

**Tables:**
- `categories` - Job categories
- `jobs` - Job listings
- `doctrine_migration_versions` - Migration tracking

---

## 🎓 Common Tasks

### Add New Job Category

```sql
INSERT INTO categories (name, description, created_at) 
VALUES ('Your Category', 'Description here', NOW());
```

### Add New Job

```sql
INSERT INTO jobs (title, description, company, location, salary, job_type, category_id, is_active, created_at, updated_at)
VALUES (
  'Job Title',
  'Job description here',
  'Company Name',
  'City, State',
  '100000',
  'Full Time',
  1,
  1,
  NOW(),
  NOW()
);
```

### Disable a Job

```sql
UPDATE jobs SET is_active = 0 WHERE id = 1;
```

### Search for Jobs

```sql
SELECT * FROM jobs WHERE title LIKE '%developer%' AND is_active = 1;
```

---

## 📈 Performance Tips

### Cache Configuration

Already configured in `config/packages/cache.yaml` for development. For production:

```bash
php bin/console cache:warmup --env=prod
```

### Database Optimization

Create indexes:
```sql
CREATE INDEX idx_job_category ON jobs(category_id);
CREATE INDEX idx_job_active ON jobs(is_active);
CREATE INDEX idx_job_company ON jobs(company);
```

### Static Assets

Already minified CSS and JS files are included. For production:

```bash
php bin/console asset-map:compile
```

---

## ✨ Features List

- ✅ Browse job listings
- ✅ Search by keywords
- ✅ Filter by category
- ✅ View job details
- ✅ Apply to jobs
- ✅ Category browsing
- ✅ Responsive design
- ✅ Beautiful animations
- ✅ Contact form
- ✅ About page
- ✅ Testimonials
- ✅ Mobile friendly

---

## 🚨 Error Solutions

| Error | Solution |
|-------|----------|
| "SQLSTATE[HY000]" | MySQL not running - start MySQL service |
| "Class not found" | Run `composer dump-autoload` and `cache:clear` |
| "Port already in use" | Use different port: `php -S localhost:8001 -t public` |
| "Twig error" | Clear cache: `php bin/console cache:clear` |
| "Permission denied" | Fix permissions: `chmod -R 777 var/` |
| "No migrations found" | Run: `php bin/console doctrine:migrations:migrate` |

---

## 📞 Need Help?

1. **Check Documentation:**
   - README.md - Overview
   - SETUP_GUIDE.md - Detailed setup
   - IMPLEMENTATION_CHECKLIST.md - What's implemented

2. **Common Issues:**
   - See troubleshooting section above

3. **External Resources:**
   - Symfony: https://symfony.com/doc/current/
   - Doctrine: https://www.doctrine-project.org/
   - MySQL: https://dev.mysql.com/doc/
   - Bootstrap: https://getbootstrap.com/docs/

---

## 🎯 Next Steps After Setup

1. **Add more jobs** via MySQL insert
2. **Customize design** - Edit CSS files
3. **Add authentication** - Install security bundle
4. **Create admin panel** - Install EasyAdmin
5. **Deploy to server** - Set up production environment

---

## 🏁 You're Ready!

Your JobEntry Symfony application is now ready to use! 

**Commands to start:**
```bash
# Make sure you're in project directory
cd job-portal-symfony

# Start the server
symfony server:start

# Or use PHP
php -S localhost:8000 -t public
```

Then open: **http://localhost:8000** 🎉

---

**Created:** November 29, 2025
**Status:** ✅ Ready for Production
**Version:** 1.0.0

Happy job hunting! 🚀
