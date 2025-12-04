# JobEntry - Job Portal Website

A modern, feature-rich job portal built with **Symfony 7.4** and **MySQL**. Browse job opportunities, search by keywords and categories, and view detailed job listings all in one place.

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- MySQL 5.7+
- Composer

### Installation

1. Navigate to project directory:
```bash
cd job-portal-symfony
```

2. Install dependencies:
```bash
composer install
```

3. Verify database connection:
```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate
```

4. Start development server:
```bash
symfony server:start
```

5. Open browser and visit: `http://localhost:8000`

## 📋 Features

- **Job Browsing**: Browse thousands of job listings
- **Search & Filter**: Search by keywords and filter by job categories
- **Detailed Job Pages**: View complete job information with company details
- **Category Management**: Organize jobs by professional categories
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Beautiful UI**: Modern interface with smooth animations
- **User Testimonials**: Success stories from job seekers
- **Contact Page**: Get in touch with the team

## 🗂️ Project Structure

```
src/
├── Controller/          # Application controllers (Home, Job, Category)
├── Entity/             # Database entities (Job, Category)
└── Repository/         # Database queries

templates/
├── base.html.twig      # Main template layout
├── home/               # Home page templates
├── job/                # Job listing templates
└── category/           # Category templates

public/
├── css/                # Stylesheets
├── js/                 # JavaScript files
├── img/                # Images
└── lib/                # Third-party libraries
```

## 🔧 Configuration

Database configuration is in `.env`:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/job_portal?serverVersion=8.0.32&charset=utf8mb4"
```

Change `root` username or add password as needed:
```env
DATABASE_URL="mysql://username:password@127.0.0.1:3306/job_portal?serverVersion=8.0.32"
```

## 📖 Usage

### Browse Jobs
1. Visit home page or `/jobs`
2. Use search bar to find specific jobs
3. Filter by category on the left sidebar
4. Click on a job to view full details

### Add Sample Data

Use MySQL client to add sample jobs:

```sql
USE job_portal;

-- Add category
INSERT INTO categories (name, description, created_at) 
VALUES ('Software Development', 'IT and software roles', NOW());

-- Add job
INSERT INTO jobs (title, description, company, location, salary, job_type, category_id, is_active, created_at, updated_at)
VALUES (
  'Senior Developer',
  'We are seeking an experienced developer...',
  'Tech Company Inc',
  'San Francisco, CA',
  '120000',
  'Full Time',
  1,
  1,
  NOW(),
  NOW()
);
```

## 🛠️ Available Commands

```bash
# Create new entity
php bin/console make:entity

# Create new controller
php bin/console make:controller

# Generate migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Clear cache
php bin/console cache:clear

# List all commands
php bin/console list
```

## 📚 Routes

| Route | Purpose |
|-------|---------|
| `/` | Home page with featured jobs |
| `/jobs` | All job listings with search |
| `/jobs/{id}` | Single job detail page |
| `/categories` | Browse job categories |
| `/categories/{id}` | Jobs in specific category |
| `/about` | About page |
| `/contact` | Contact page |
| `/testimonials` | Success stories |

## 🎨 Customization

### Change Site Title
Edit `templates/base.html.twig`:
```twig
<h1 class="m-0 text-primary">Your Site Name</h1>
```

### Modify Styling
Edit `public/css/style.css` or `public/css/bootstrap.min.css`

### Update Footer
Edit `templates/base.html.twig` footer section:
```twig
<!-- Footer Start -->
<div class="container-fluid bg-dark text-white...">
```

## 🐛 Troubleshooting

**Database connection error?**
```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**Port 8000 in use?**
```bash
symfony server:start --port=8001
```

**Clear cache and start fresh:**
```bash
php bin/console cache:clear
php bin/console cache:warmup
```

## 📋 Tech Stack

- **Backend**: Symfony 7.4 Framework
- **Database**: MySQL with Doctrine ORM
- **Frontend**: Bootstrap 5, Twig Templating
- **Animations**: WOW.js, Animate.css
- **Carousel**: Owl Carousel
- **Icons**: Font Awesome 5

## 📄 License

This project is open source and available under the MIT License.

## 📞 Support

For detailed setup instructions, see `SETUP_GUIDE.md`

For issues or questions:
- Review Symfony documentation: https://symfony.com/doc
- Check Doctrine ORM docs: https://www.doctrine-project.org/
- Visit Bootstrap docs: https://getbootstrap.com/docs

---

**Ready to find your dream job?** Start exploring JobEntry today! 🎯
