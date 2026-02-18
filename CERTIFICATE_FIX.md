# Certificate Feature - Quick Fix Guide

## Problem: /certificates returns 404

## Solution (Run these in order):

### Step 1: Fix Certificate Setup
```bash
Double-click: FIX_CERTIFICATES.bat
```

### Step 2: Start Server
```bash
Double-click: START_SERVER.bat
```

### Step 3: Access Certificates
- URL: `http://127.0.0.1:8000/certificates`
- Login as: **admin** or **teacher** (NOT bursar)
- Default credentials: `admin@myacademy.local` / `password`

---

## What Was Fixed:

✅ Intervention Image package installed
✅ Storage symlink created
✅ Certificate directories created
✅ Database migrations run
✅ All caches cleared

---

## Features Available:

1. **Generate Individual Certificates** - Select class, student, fill details
2. **Bulk Generate** - Select class only (no student) to generate for all
3. **Upload Custom Templates** - PNG, 1754x1240px (see CANVA_CERTIFICATE_GUIDE.md)
4. **Download PDFs** - View recent certificates and download

---

## Troubleshooting:

**Still getting 404?**
- Make sure you're logged in as admin or teacher
- Clear browser cache (Ctrl+F5)
- Check URL is exactly: `/certificates` (no extra slashes)

**GD Extension Error?**
- Enable GD in your php.ini file
- Restart your web server
- Verify with: `php -m | findstr gd`

**Template Upload Fails?**
- Check file is PNG format
- Size must be 1754x1240 pixels
- Max 10MB file size

---

Done! 🎓
