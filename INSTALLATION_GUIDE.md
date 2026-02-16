# MyAcademy Installation Guide

## ⚡ FASTEST METHOD: One-Click Installation

### Prerequisites
1. Download Laragon from https://laragon.org/download/
2. Install to `C:\laragon` (default location)
3. Copy `myacademy-laravel` folder to `C:\laragon\www\`

### Installation (5 Minutes)
1. Right-click `INSTALL.bat`
2. Select **"Run as administrator"**
3. Wait for completion
4. Double-click `START_MYACADEMY.bat`
5. Open browser: `http://127.0.0.1:8000`

**Done!** See `AUTOMATED_SCRIPTS_README.md` for all available scripts.

---

## 📋 System Requirements

### Admin Laptop (Server)
- **OS:** Windows 10/11
- **RAM:** 4GB minimum (8GB recommended)
- **Storage:** 20GB free space
- **Processor:** Intel Core i3 or equivalent
- **Network:** WiFi capability

### Client Devices (Teachers)
- Any device with a web browser
- WiFi connection to school network

---

## 🚀 Quick Installation (30 Minutes)

### Step 1: Download Laragon (5 minutes)

1. Go to https://laragon.org/download/
2. Download **Laragon Full** (includes PHP, MySQL, Node.js)
3. Run the installer
4. Install to `C:\laragon` (default)
5. Click **Start All** in Laragon

### Step 2: Get MyAcademy Files (2 minutes)

1. Copy the `myacademy-laravel` folder to `C:\laragon\www\`
2. Final path should be: `C:\laragon\www\myacademy-laravel`

### Step 3: Create Database (3 minutes)

1. In Laragon, click **Database** → **Open**
2. This opens HeidiSQL
3. Right-click → **Create new** → **Database**
4. Name: `myacademy`
5. Collation: `utf8mb4_unicode_ci`
6. Click **OK**

### Step 4: Configure Environment (5 minutes)

1. Open `C:\laragon\www\myacademy-laravel\.env` in Notepad
2. Update these lines:

```env
DB_CONNECTION=mysql
DB_DATABASE=myacademy
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=

MYACADEMY_SCHOOL_NAME="Your School Name"
MYACADEMY_ADMIN_EMAIL=admin@yourschool.com
MYACADEMY_ADMIN_PASSWORD=YourSecurePassword123
```

3. Save and close

### Step 5: Install Dependencies (10 minutes)

1. In Laragon, click **Terminal**
2. Navigate to project:
```bash
cd C:\laragon\www\myacademy-laravel
```

3. Install PHP dependencies:
```bash
php composer.phar install
```

4. Install JavaScript dependencies:
```bash
npm install
```

5. Build assets:
```bash
npm run build
```

### Step 6: Setup Database (3 minutes)

Run these commands in the terminal:

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Step 7: Start the System (2 minutes)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Done!** Open browser: `http://127.0.0.1:8000`

---

## 🔐 Default Login Credentials

After installation, use these accounts:

### Admin Account
- **Email:** admin@myacademy.local
- **Password:** password

### Bursar Account
- **Email:** bursar@myacademy.local
- **Password:** password

### Teacher Account
- **Email:** teacher@myacademy.local
- **Password:** password

**⚠️ IMPORTANT:** Change these passwords immediately after first login!

---

## 🌐 Network Setup for Teachers

### Option 1: Using School WiFi

1. **Find Your IP Address:**
   - Press `Win + R`
   - Type `cmd` and press Enter
   - Type `ipconfig`
   - Look for **IPv4 Address** (e.g., `192.168.1.5`)

2. **Share with Teachers:**
   - Teachers connect to school WiFi
   - They open: `http://192.168.1.5:8000`
   - They can now access the system

### Option 2: Using Mobile Hotspot

1. **Enable Hotspot on Admin Laptop:**
   - Settings → Network & Internet → Mobile hotspot
   - Turn on **Share my Internet connection**
   - Note the **Network name** and **Password**

2. **Teachers Connect:**
   - Connect to the hotspot
   - Open: `http://192.168.137.1:8000`

---

## 📱 Access from Phones/Tablets

Teachers can use their phones:

1. Connect to school WiFi
2. Open Chrome/Safari
3. Go to: `http://[ADMIN_IP]:8000`
4. Bookmark for easy access

---

## 🔧 Troubleshooting

### Problem: "Connection Refused"

**Solution:**
1. Check if Laragon is running (green icon)
2. Restart Laragon: **Stop All** → **Start All**
3. Check firewall: Allow port 8000

### Problem: "Database Connection Error"

**Solution:**
1. Open HeidiSQL from Laragon
2. Verify database `myacademy` exists
3. Check `.env` file has correct credentials

### Problem: "Page Not Found"

**Solution:**
1. Make sure you're running: `php artisan serve --host=0.0.0.0 --port=8000`
2. Check the URL includes port `:8000`

### Problem: "Upload Failed" (Backup Restore)

**Solution:**
1. Already fixed! Upload limit is 5GB
2. If still failing, check file isn't corrupted

### Problem: Teachers Can't Connect

**Solution:**
1. Verify admin laptop and teacher devices are on same WiFi
2. Disable Windows Firewall temporarily to test
3. Use correct IP address (run `ipconfig` to confirm)

---

## 🎯 First-Time Setup Checklist

After installation, complete these steps:

### 1. Change Admin Password
- Login as admin
- Go to Profile → Change Password

### 2. Configure School Settings
- Go to Settings → School Configuration
- Upload school logo
- Set school name, address, contact

### 3. Setup Academic Session
- Go to Settings → Academic Sessions
- Create current session (e.g., 2024/2025)
- Set current term (Term 1, 2, or 3)

### 4. Add Classes
- Go to Classes → Add Class
- Create all classes (e.g., JSS 1, JSS 2, SS 1, etc.)

### 5. Add Subjects
- Go to Subjects → Add Subject
- Add all subjects taught in school

### 6. Add Teachers
- Go to Teachers → Add Teacher
- Create accounts for all teachers
- Assign subjects to teachers

### 7. Add Students
- Go to Students → Add Student
- Or use Bulk Import (CSV)

### 8. Create Backup
- Go to Settings → Backup & Restore
- Click **Backup Now**
- Save the ZIP file safely

---

## 💾 Daily Operations

### Starting the System Each Day

1. Open Laragon
2. Click **Start All**
3. Open Terminal
4. Run:
```bash
cd C:\laragon\www\myacademy-laravel
php artisan serve --host=0.0.0.0 --port=8000
```

### Stopping the System

1. Press `Ctrl + C` in terminal
2. In Laragon, click **Stop All**

### Creating Backups

**Weekly Backup (Recommended):**
1. Login as admin
2. Settings → Backup & Restore
3. Click **Backup Now**
4. Save ZIP to external drive

---

## 🔄 Updating the System

When you receive an update:

1. **Backup First!**
   - Create backup from Settings

2. **Stop the System**
   - Press `Ctrl + C` in terminal
   - Stop Laragon

3. **Replace Files**
   - Copy new files to `C:\laragon\www\myacademy-laravel`
   - Keep your `.env` file

4. **Update Database**
```bash
php artisan migrate --force
```

5. **Rebuild Assets**
```bash
npm run build
```

6. **Restart**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 📞 Support

### Self-Help
- Check `README.md` in project folder
- Review `TROUBLESHOOTING.md`

### Contact Support
- **Email:** support@myacademy.com
- **WhatsApp:** +234-XXX-XXX-XXXX
- **Hours:** Mon-Fri, 9AM-5PM

---

## 🎓 Training Resources

### Video Tutorials
1. Installation Guide (15 mins)
2. Admin Dashboard Tour (20 mins)
3. Adding Students (10 mins)
4. Score Entry (15 mins)
5. Generating Report Cards (10 mins)

### User Manuals
- Admin Manual (PDF)
- Teacher Manual (PDF)
- Bursar Manual (PDF)

---

## ⚡ Quick Reference

### Important URLs
- **Admin Access:** `http://127.0.0.1:8000`
- **Teacher Access:** `http://[ADMIN_IP]:8000`
- **Database:** Open HeidiSQL from Laragon

### Important Folders
- **Project:** `C:\laragon\www\myacademy-laravel`
- **Uploads:** `C:\laragon\www\myacademy-laravel\public\uploads`
- **Backups:** `C:\laragon\www\myacademy-laravel\storage\app\backups`

### Important Commands
```bash
# Start server
php artisan serve --host=0.0.0.0 --port=8000

# Create backup (manual)
php artisan backup:run

# Clear cache
php artisan cache:clear

# Check system status
php artisan about
```

---

## 🎉 You're All Set!

Your MyAcademy system is now ready to use. Start by:

1. ✅ Logging in as admin
2. ✅ Changing default passwords
3. ✅ Configuring school settings
4. ✅ Adding your first class
5. ✅ Creating a backup

**Need help?** Contact support or check the troubleshooting guide.
