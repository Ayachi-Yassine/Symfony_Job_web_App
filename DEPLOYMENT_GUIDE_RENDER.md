# Deploy Job Portal to Render.com (FREE)

This guide walks you through deploying the job portal to Render.com completely free.

## Prerequisites

- GitHub account (you already have this ✓)
- Render.com account (free signup)
- Project pushed to GitHub (already done ✓)

---

## Step 1: Sign Up on Render.com

1. Go to **https://render.com**
2. Click **"Sign Up"**
3. Choose **"Continue with GitHub"**
4. Authorize Render to access your GitHub account
5. Complete signup

---

## Step 2: Create a New Web Service

1. On Render dashboard, click **"New +"** (top right)
2. Select **"Web Service"**
3. Click **"Connect a repository"**
4. Find and select **`job-portal-symfony`** repo
5. Click **"Connect"**

---

## Step 3: Configure Web Service

Fill in the following fields:

| Field | Value |
|-------|-------|
| **Name** | `job-portal` (or your preferred name) |
| **Region** | Choose closest to you (e.g., `Frankfurt`, `Singapore`) |
| **Branch** | `main` |
| **Runtime** | `PHP` |
| **Build Command** | `composer install --no-dev && php bin/console assets:install public` |
| **Start Command** | `php -S 0.0.0.0:$PORT -t public` |

---

## Step 4: Add Environment Variables

Click **"Advanced"** → **"Add Environment Variable"** and add:

```
APP_ENV=prod
APP_DEBUG=0
DATABASE_URL=postgresql://<USER>:<PASSWORD>@<HOST>:<PORT>/<DB>
SYMFONY_ENV=prod
```

(We'll fill in the database details after creating the PostgreSQL instance)

---

## Step 5: Create PostgreSQL Database

1. On Render dashboard, click **"New +"**
2. Select **"PostgreSQL"**
3. Fill in:
   - **Name**: `job-portal-db` (or your name)
   - **Database**: `job_portal` (auto-filled)
   - **User**: `job_portal_user` (auto-filled)
   - **Region**: Same as web service
   - **PostgreSQL Version**: `15` or latest
4. Click **"Create Database"**
5. Wait for creation (2-3 minutes)
6. Once created, copy the **"Internal Database URL"**

---

## Step 6: Link Database to Web Service

1. Go back to your **Web Service** (job-portal)
2. Scroll to **"Environment"**
3. Update **`DATABASE_URL`** with the copied database URL
4. Click **"Save"**

---

## Step 7: Deploy

1. Click **"Deploy"** button (or it may auto-deploy)
2. Watch the build logs in the console
3. Wait for deployment to complete (5-10 minutes)
4. You'll see: **"Your service is live at: https://job-portal-xxxx.onrender.com"**

---

## Step 8: Run Database Migrations

After deployment succeeds:

1. Go to your Web Service dashboard
2. Click **"Shell"** (top right)
3. Run these commands in the shell:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
# or if no migrations exist:
php bin/console doctrine:schema:create
```

4. Exit shell (Ctrl+C or close tab)

---

## Step 9: Create Admin User (Optional but Recommended)

In the Shell, run:

```bash
php bin/console app:create-admin --email=admin@example.com --password=YourPassword123
```

Replace with your preferred email/password.

---

## Step 10: Access Your Live App

1. Go to the URL shown: **`https://job-portal-xxxx.onrender.com`**
2. Browse the site
3. Login with admin credentials (if created)
4. Test the application

---

## Troubleshooting

### Build Fails

**Error: "Composer install failed"**
- Check that `composer.json` exists in repo root ✓
- Ensure all dependencies are compatible

**Error: "DATABASE_URL not set"**
- Make sure you added the env variable correctly
- Check format: `postgresql://user:pass@host:port/dbname`

### App is Slow or Crashes

- Render free tier has limited resources
- App goes to sleep after 15 min of inactivity
- First request after sleep takes 30 seconds

### Database Connection Issues

- Verify DATABASE_URL in Environment variables
- Check PostgreSQL instance is running
- Try redeploying (click Deploy button again)

---

## After Deployment

### View Live Logs

Go to Web Service → Logs to debug issues.

### Update Code

Just push changes to GitHub `main` branch:

```powershell
git add .
git commit -m "your message"
git push origin main
```

Render will auto-redeploy!

### Custom Domain (Optional)

1. Go to Web Service settings
2. Scroll to "Custom Domain"
3. Add your domain (requires DNS setup)

---

## Important Notes

- **Free tier limitations**:
  - 0.5 GB RAM (may be tight)
  - Shared CPU
  - App spins down after 15 min inactivity
  - Limited storage

- **Production considerations**:
  - Upgrade to paid tier for production apps ($7+/month)
  - Enable auto-deploy for automatic updates
  - Set up monitoring/alerts

---

## Quick Reference

| Item | Value |
|------|-------|
| Website | https://render.com |
| Build command | `composer install --no-dev && php bin/console assets:install public` |
| Start command | `php -S 0.0.0.0:$PORT -t public` |
| Database | PostgreSQL 15 |
| Free tier price | $0 (with limitations) |
| Typical response time | 1-2 seconds |

---

## Need Help?

- Render docs: https://render.com/docs
- Symfony deployment: https://symfony.com/doc/current/deployment.html
- Check Render logs for specific errors
- Try redeploying (sometimes fixes transient issues)

---

**Your live job portal is now on the web! 🚀**
