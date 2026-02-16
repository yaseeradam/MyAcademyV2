# MyAcademy - One-Click Installation Scripts

## 🚀 Quick Start (3 Steps)

### Step 1: Install Laragon
1. Download from: https://laragon.org/download/
2. Install to `C:\laragon` (default)
3. Done!

### Step 2: Copy Files
1. Copy `myacademy-laravel` folder to `C:\laragon\www\`
2. Done!

### Step 3: Run Installer
1. Right-click `INSTALL.bat`
2. Select **"Run as administrator"**
3. Wait 5-10 minutes
4. Done!

---

## 📁 Available Scripts

### 🔧 Installation & Setup

#### `INSTALL.bat` ⭐
**First-time installation**
- Creates database
- Installs dependencies
- Sets up system
- **Run as Administrator!**

#### `CHECK_SYSTEM.bat`
**System diagnostics**
- Checks if everything is installed correctly
- Verifies database connection
- Shows system status

---

### ▶️ Daily Operations

#### `START_MYACADEMY.bat` ⭐
**Start the system**
- Starts Laragon services
- Starts MyAcademy server
- Shows access URLs
- **Use this every day!**

#### `STOP_MYACADEMY.bat`
**Stop the system**
- Stops server safely
- Stops Laragon services
- Use when closing for the day

#### `NETWORK_INFO.bat`
**Get teacher access URL**
- Shows your IP address
- Shows URL for teachers
- Instructions for sharing

---

### 💾 Backup & Maintenance

#### `CREATE_BACKUP.bat` ⭐
**Create system backup**
- Creates full backup (database + files)
- Saves to `storage/app/backups/`
- **Run weekly!**

#### `UPDATE_SYSTEM.bat`
**Update to new version**
- Updates dependencies
- Runs database migrations
- Rebuilds assets
- **Backup first!**

---

## 📋 Daily Workflow

### Morning (Starting School Day)
1. Double-click `START_MYACADEMY.bat`
2. Wait for "Server Started Successfully!"
3. Share the teacher URL with staff
4. Leave the window open

### Afternoon (Closing School Day)
1. Double-click `STOP_MYACADEMY.bat`
2. Wait for "Server stopped successfully!"
3. Close all windows

### Weekly (Every Friday)
1. Double-click `CREATE_BACKUP.bat`
2. Copy backup ZIP to external drive
3. Store safely

---

## 🎯 Common Tasks

### First Time Setup
```
1. INSTALL.bat (as Administrator)
2. START_MYACADEMY.bat
3. Open browser: http://127.0.0.1:8000
4. Login and configure school
```

### Daily Use
```
Morning:  START_MYACADEMY.bat
Evening:  STOP_MYACADEMY.bat
```

### Share with Teachers
```
1. NETWORK_INFO.bat
2. Share the URL shown
3. Teachers bookmark it
```

### Create Backup
```
1. CREATE_BACKUP.bat
2. Copy ZIP from storage/app/backups/
3. Save to external drive
```

### Update System
```
1. CREATE_BACKUP.bat (backup first!)
2. STOP_MYACADEMY.bat
3. Copy new files
4. UPDATE_SYSTEM.bat
5. START_MYACADEMY.bat
```

### Troubleshooting
```
1. CHECK_SYSTEM.bat
2. Fix any errors shown
3. Try START_MYACADEMY.bat again
```

---

## ⚠️ Important Notes

### Administrator Rights
- `INSTALL.bat` **MUST** run as Administrator
- Right-click → "Run as administrator"
- Other scripts don't need admin rights

### Keep Window Open
- Don't close the START_MYACADEMY window
- Closing it stops the server
- Minimize it instead

### Backup Regularly
- Run `CREATE_BACKUP.bat` every Friday
- Copy backup to external drive
- Keep at least 3 recent backups

### Network Requirements
- Admin laptop and teachers must be on same WiFi
- Or use mobile hotspot from admin laptop
- Firewall may need to allow port 8000

---

## 🆘 Troubleshooting

### "Please run as Administrator"
- Right-click `INSTALL.bat`
- Select "Run as administrator"

### "Laragon not found"
- Install Laragon from https://laragon.org/download/
- Install to `C:\laragon`

### "Database connection failed"
- Run `CHECK_SYSTEM.bat` to diagnose
- Make sure Laragon is running
- Check `.env` file settings

### "Teachers can't connect"
- Run `NETWORK_INFO.bat` to get correct URL
- Verify same WiFi network
- Check Windows Firewall settings

### "Port 8000 already in use"
- Run `STOP_MYACADEMY.bat`
- Wait 10 seconds
- Run `START_MYACADEMY.bat` again

---

## 📞 Support

### Self-Help
1. Run `CHECK_SYSTEM.bat` first
2. Check `INSTALLATION_GUIDE.md`
3. Review error messages carefully

### Contact Support
- **Email:** support@myacademy.com
- **WhatsApp:** +234-XXX-XXX-XXXX
- **Hours:** Mon-Fri, 9AM-5PM

---

## ✅ Quick Reference

| Task | Script | Admin Required? |
|------|--------|----------------|
| First Install | `INSTALL.bat` | ✅ Yes |
| Start System | `START_MYACADEMY.bat` | ❌ No |
| Stop System | `STOP_MYACADEMY.bat` | ❌ No |
| Create Backup | `CREATE_BACKUP.bat` | ❌ No |
| Update System | `UPDATE_SYSTEM.bat` | ❌ No |
| Check Status | `CHECK_SYSTEM.bat` | ❌ No |
| Get Network Info | `NETWORK_INFO.bat` | ❌ No |

---

## 🎉 You're Ready!

Your MyAcademy system is now fully automated. Just:

1. ✅ Run `INSTALL.bat` once (as Administrator)
2. ✅ Use `START_MYACADEMY.bat` daily
3. ✅ Use `CREATE_BACKUP.bat` weekly
4. ✅ Enjoy hassle-free school management!

**Questions?** Check `INSTALLATION_GUIDE.md` or contact support.
