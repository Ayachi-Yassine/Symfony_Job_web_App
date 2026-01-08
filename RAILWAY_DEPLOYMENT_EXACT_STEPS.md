# Railway Deployment - Exact Steps (Copy-Paste Ready)

## ✅ Prerequisites
- GitHub account connected to Railway
- Repository pushed to GitHub (already done)
- Railway account created

---

## 🚀 Step 1: Create a New Project on Railway

**What to do:**
1. Go to: https://railway.app
2. Click **"New Project"**
3. Click **"Deploy from GitHub"**
4. Select your repository: **Ayachi-Yassine/Symfony_Job_web_App** (or your repo name)
5. Click **"Deploy Now"**

**Wait:** Railway will start deploying. You'll see a loading screen.

---

## 📦 Step 2: Add a MySQL Database

**What to do:**
1. In Railway dashboard, you'll see your app card.
2. Click the **"+"** button (bottom right of dashboard).
3. Select **"MySQL"** from the list.
4. Click **"Add"** or **"Deploy"**.

**Wait:** MySQL will start provisioning (takes 1-2 minutes).

---

## 🔗 Step 3: Get Your Database Connection String

**What to do:**
1. In Railway dashboard, click the **MySQL** service tile (it will appear as a card).
2. Click the **"Connect"** tab at the top.
3. Look for a section labeled **"MySQL Connection String"** or **"Connection"**.
4. You will see something like:
```
mysql://root:PASSWORD@containers.railway.app:PORT/railway
```
5. **Copy the entire string** (the full URL).

---

## 🔐 Step 4: Add Environment Variables to Your App

**What to do:**
1. Click your **Web Service** (the main app card, not MySQL).
2. Click the **"Variables"** tab.
3. Add **FOUR** variables by clicking **"+ New Variable"** four times:

### Variable 1: DATABASE_URL
- **Name field:** `DATABASE_URL`
- **Value field:** Paste the MySQL connection string from Step 3
- Example:
```
mysql://root:abc123password@containers.railway.app:12345/railway
```
- Click **"Save"** or **"Add"**

### Variable 2: APP_ENV
- **Name field:** `APP_ENV`
- **Value field:** `prod`
- Click **"Save"**

### Variable 3: APP_SECRET
- **Name field:** `APP_SECRET`
- **Value field:** Copy-paste this (it's a random secure string):
```
a7f9e2c1d4b6h3j8k5m2n9p0q1r2s3t4u5v6w7x8y9z0a1b2c3d4e5f6g7h8
```
- Click **"Save"**

### Variable 4: APP_DEBUG
- **Name field:** `APP_DEBUG`
- **Value field:** `0`
- Click **"Save"**

---

## 🔄 Step 5: Redeploy Your App

**What to do:**
1. In your Web Service, click the **"Deployments"** tab.
2. Find your latest deployment (top of list).
3. Click the three dots **"⋯"** on the right side.
4. Select **"Redeploy"**.

**Wait:** Deployment will run. Watch the logs for completion (green checkmark).

---

## ✓ Step 6: Verify the Deployment

**What to do:**
1. Still in the **Deployments** tab, click **"Logs"** to view output.
2. Look for these messages:
   - `No migrations to execute` (database is connected)
   - OR `Migration Version... was executed successfully` (migrations ran)
3. If you see **no errors**, scroll down and find your **Railway URL** at the top of the service.
4. Click the URL or copy it and open it in your browser.

**Expected result:**
- Your JobEntry app loads in the browser.
- You can browse jobs, search, etc.
- No error messages.

---

## 🌐 Step 7: Access Your Deployed App

**Your app is now LIVE!** Here's how to access it:

### Method 1: From Railway Dashboard (Easiest)
1. In Railway dashboard, click your **Web Service** card (the app).
2. Look at the top right — you'll see a URL like:
```
https://your-app-12345.railway.app
```
3. Click it directly to open your app in the browser.

### Method 2: Copy the URL manually
1. In Railway dashboard, click your **Web Service**.
2. Click the **"Settings"** tab.
3. Look for **"Service URL"** or **"Public URL"**.
4. Copy the full URL (e.g., `https://your-app-12345.railway.app`).
5. Paste it into your browser's address bar.
6. Press **Enter**.

### Method 3: Share with others
- Copy your Railway URL and send it to friends/team.
- They can visit it in their browser — no special setup needed.
- Example: `https://jobentry-portal-prod.railway.app`

---

## ✅ What You Should See

When you open your app URL, you'll see:

1. **Home page loads** with the JobEntry header
2. **Featured jobs display** (if you added sample data locally)
3. **Search bar works** at the top
4. **Navigation menu** (Jobs, Categories, About, Contact, etc.)
5. **No error messages** (if everything deployed correctly)

### Test These Features:
- Click **"Jobs"** → browse all jobs
- Use the **search bar** → search for a job keyword
- Click **"Categories"** → view job categories
- Click a **job listing** → view full job details

---

## 🔗 Your App URL Format

Your deployed app URL will look like:
```
https://[PROJECT_NAME]-[RANDOM_CHARS].railway.app
```

**Example URLs:**
```
https://job-portal-symfony-xyz123.railway.app
https://jobentry-app-abc456.railway.app
https://my-symfony-job-site-def789.railway.app
```

Railway generates a unique URL automatically. **You cannot change it on the free tier** (custom domains require paid plan).

---

## 📱 Mobile & Desktop

Your app works on:
- ✅ Desktop browsers (Chrome, Firefox, Safari, Edge)
- ✅ Mobile browsers (iPhone, Android)
- ✅ Tablets
- ✅ Any device with internet access

No app store needed — it's a web app!

---

## 🐛 If Something Goes Wrong

**Check these:**

### Error: "Database connection refused"
- Go back to Step 4, Variable 1.
- Verify you copied the entire MySQL connection string correctly.
- Re-save and redeploy.

### Error: "Migration failed"
- In Deployments → Logs, look for the exact error message.
- Common cause: `DATABASE_URL` is wrong or MySQL isn't running yet.
- Wait 2 minutes, then redeploy again.

### App loads but shows blank page
- Go to Deployments → Logs.
- Look for PHP errors or Symfony exceptions.
- Share the error with support.

### Logs show: "No such file or directory: bin/console"
- Your code didn't deploy correctly.
- Go to GitHub and check that `bin/console` exists in your repo.
- Push again, redeploy.

---

## 📝 Example of Completed Variables Tab

```
DATABASE_URL        mysql://root:pass123@containers.railway.app:5432/railway
APP_ENV             prod
APP_SECRET          a7f9e2c1d4b6h3j8k5m2n9p0q1r2s3t4u5v6w7x8y9z0a1b2c3d4e5f6g7h8
APP_DEBUG           0
```

---

## ✅ You're Done!

Your Symfony app is now live on Railway. Share your Railway URL with friends!

**Next steps (optional):**
- Add a custom domain in Railway settings.
- Monitor logs in the Deployments tab.
- Push new code to GitHub → automatic redeploy.

