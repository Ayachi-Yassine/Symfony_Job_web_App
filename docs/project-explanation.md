# Job Portal Symfony Project — Beginner's Guide

This document explains the Job Portal project in your workspace in a beginner-friendly way. It covers the project's purpose, structure, main components, how the pieces work together, common developer tasks, and how to run and troubleshoot the app locally.

---

## 1. Project Overview

- Purpose: a small job portal where companies post jobs and users can apply. It includes authentication, user profiles (with CV upload), job listings, single-click applications, notifications, and an admin panel to manage users, jobs and applications.
- Stack: PHP 8.2, Symfony 7.x, Doctrine ORM, Twig templates, Bootstrap front-end, MySQL (or other Doctrine-supported DB).


## 2. High-level Architecture

- HTTP requests → Symfony Router → Controller → Services / Repositories → Entities (Doctrine) → Templates (Twig) → Response.
- Security: Symfony Security system with roles (`ROLE_USER`, `ROLE_ADMIN`).
- File uploads: CVs stored under `public/uploads/`.
- Routes: Some routes are defined as YAML in `config/routes/` (explicit files created to avoid attribute registration issues).


## 3. Project Structure (important files and folders)

- `public/` — the web root.
- `src/Controller/` — controllers that handle requests.
  - `AdminController.php` — admin pages (users, jobs, categories, applications, activities).
  - `ProfileController.php` — user profile pages.
  - `JobApplicationController.php` — apply/withdraw/view applications.
  - `NotificationController.php` — notification actions.
- `src/Entity/` — Doctrine entities.
  - `User.php`, `UserProfile.php`, `Job.php`, `JobApplication.php`, `Notification.php`, `UserActivity.php`.
- `templates/` — Twig templates for pages.
  - `templates/job/show.html.twig` — job detail and apply button.
  - `templates/admin/users/edit.html.twig` — admin edit user form.
  - `templates/base.html.twig` — base layout and header (notification/profile links guarded with `route_exists()` function).
- `config/routes/` — explicit YAML route files (e.g., `notifications.yaml`, `profile.yaml`, `user_applications.yaml`).
- `src/Twig/RouteExistsExtension.php` — Twig function `route_exists()` used to avoid `RouteNotFoundException` in templates.


## 4. Key Entities and Relationships (simple explanations)

- User
  - Represents an application user.
  - Key fields: `id`, `email`, `roles` (JSON array), `password`, `isActive`, relations to `UserProfile`, `JobApplication`, `Notification`, `UserActivity`.

- UserProfile
  - One-to-one with `User`.
  - Stores profile fields and `cvFileName` pointing at `public/uploads/cv/`.

- Job
  - Represents a job posting: `title`, `company`, `description`, etc.
  - One-to-many with `JobApplication`.

- JobApplication
  - Links a `User` to a `Job`, holds application letter, CV filename, status.

- Notification
  - Simple notifications for users; `relatedLink` can point to related routes.

- UserActivity
  - Audit trail of actions like application submitted, profile updated; useful for admin.


## 5. Important Controllers & Flows

- Auth (login/registration)
  - `SecurityController` or equivalent handles login and logout via Symfony Security.

- Profile Flow
  - User can edit profile and upload a CV (PDF). CVs go to `public/uploads/cv/`.
  - `ProfileController` handles the form; there is fallback logic if `symfony/mime` is missing.

- Job Application Flow (single-click apply)
  - `templates/job/show.html.twig` shows an "Apply Now" button.
  - `JobApplicationController::apply()` validates CSRF token, ensures user has not already applied, attaches a CV (either uploaded or copied from profile), persists application and creates a notification.
  - Redirects to the job page or applications list.

- Admin Flow
  - Admin can list, edit, delete users; manage jobs and applications.
  - `AdminController::editUser()` shows a form to change user role and active status. Important: `User::roles` is a JSON array — the edit form uses a single-choice field (`role`) and saves back as an array.
  - Deleting a user requires clearing the security token if deleting the currently logged-in user to prevent Symfony's `EntityUserProvider` refresh error.


## 6. Routes and Why Some Were Moved to YAML

- Symfony usually registers routes from attribute annotations on controllers.
- In this project we had issues where controllers with class-level `#[IsGranted('ROLE_USER')]` attributes were not auto-registered. To ensure routes are always present, explicit YAML route files were created under `config/routes/`.
- Templates use `route_exists('route_name')` to guard calls to `path()` or `url()` to avoid exceptions if a route is ever missing.


## 7. Templates and Twig Notes

- Twig is used for templates; some filters (like `truncate`) may not exist — alternatives: use `slice()` and conditional length checks.
- Avoid null-coalesce on expressions using filters (`{{ date(...) ? date(...) : '-' }}` instead of `{{ date(...) ?? '-' }}` if filter calls may return null).
- Guard routes using `route_exists()` to keep templates robust.


## 8. Security and Roles

- Roles used: `ROLE_USER` (regular user) and `ROLE_ADMIN` (admin).
- Be careful with class-level `IsGranted` attributes — they can prevent route auto-registration and inadvertently block admins. Use runtime checks (e.g., `is_granted('ROLE_USER') or is_granted('ROLE_ADMIN')`) where appropriate.


## 9. Common Errors You May See & How They Were Fixed (Troubleshooting)

- RouteNotFoundException in templates
  - Fix: Add `route_exists()` Twig function and wrap `path()` calls: `route_exists('app_jobs_show') ? path('app_jobs_show', {'id': job.id}) : '#'`.

- Controllers/Attributes not registering routes
  - Fix: Add explicit YAML route files in `config/routes/` for critical controllers.

- Unknown Twig filter `truncate`
  - Fix: Replace with `slice()` and `length` checks.

- Array to string conversion when editing user roles
  - Cause: `User::roles` is an array but `ChoiceType` was bound expecting a string.
  - Fix: Show a single-choice `role` field in the form and on save set `User::setRoles([$selectedRole])`.

- InvalidArgumentException from EntityUserProvider on user delete
  - Cause: Symfony attempted to refresh a deleted user object during a request.
  - Fix: Clear the security token (logout) before removing the current user from the DB.

- CV upload LogicException: missing `symfony/mime`
  - Fix: `composer require symfony/mime` and add a try/catch fallback to handle missing Mime component gracefully.


## 10. How to Run the Project Locally (development)

1. Install dependencies:

```powershell
cd "c:\Users\yassi\Desktop\New folder\job-portal-symfony"
composer install
```

2. Configure `.env` (database URL) — set `DATABASE_URL` to your DB.

3. Create or update the database schema:

```powershell
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
# OR use migrations if present:
php bin/console doctrine:migrations:migrate
```

4. Install assets and clear cache:

```powershell
php bin/console assets:install public
php bin/console cache:clear
```

5. Start local server:

```powershell
symfony server:start -d
# or
php -S 127.0.0.1:8000 -t public
```

6. Open `http://127.0.0.1:8000` in your browser.


## 11. How to Convert This Guide to PDF

I created this as Markdown so you can edit it. To convert to PDF on Windows, you can use `pandoc` or `wkhtmltopdf`.

- Using `pandoc` (recommended):

```powershell
# install pandoc if you don't have it (e.g. via chocolatey):
choco install pandoc -y
# then convert:
pandoc docs\project-explanation.md -o docs\project-explanation.pdf --pdf-engine=xelatex
```

- Using `wkhtmltopdf` (HTML to PDF):

```powershell
# install wkhtmltopdf (download installer) and add to PATH
wkhtmltopdf docs\project-explanation.html docs\project-explanation.pdf
```

If you want, I can try converting it on your machine now (if a converter is installed). I checked and common converters were not found in the environment at the moment.


## 12. How to Modify Common Parts

- Add a route: Prefer controller attribute `#[Route('/path', name:'app_name')]`; if you run into route registration issues, add an entry to `config/routes/*.yaml`.
- Add a template: Put new Twig templates under `templates/` and reference them from controllers with `return $this->render('path/to/template.html.twig', [...]);`.
- Add a field to an entity: Update the entity file, run `php bin/console make:migration` and `php bin/console doctrine:migrations:migrate`.


## 13. Debugging Tips

- Clear cache: `php bin/console cache:clear`
- See routes: `php bin/console debug:router`
- See doctrine schema issues: `php bin/console doctrine:schema:validate`
- Check logs: `var/log/dev.log` or `var/log/prod.log`
- Use `dd()` or `dump()` in controllers when debugging in dev environment.


## 14. Next Steps / Learning Path (for you as a beginner)

1. Read Symfony docs on Controllers, Routing, Forms, Security, and Doctrine.
2. Practice making small changes: add a simple controller + template.
3. Learn Twig basics and how to render forms.
4. Learn Doctrine basics (entities, relationships, migrations).
5. Try deploying a small test change and verify route/template behavior.


## 15. Contact / Ask me to do the conversion

If you want, I can:
- Try converting to PDF now if you install `pandoc` or `wkhtmltopdf` and tell me to proceed.
- Or I can keep the Markdown/HTML here and update the content to add more examples or diagrams.


---

File locations created:
- `docs/project-explanation.md` (this file)

If you'd like, I can now:
- Convert this to PDF automatically (if you want and you have a converter), or
- Add diagrams, code snippets, or shorten/expand sections.

Tell me what you'd like changed next.
