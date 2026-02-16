# Fix Backup Upload Failed Error

## Problem
The backup file is too large to upload due to PHP upload limits.

## Solution (Laragon)

### 1. Open PHP Settings
- Right-click Laragon tray icon
- Click **PHP** → **php.ini**

### 2. Find and Update These Lines
```ini
upload_max_filesize = 512M
post_max_size = 512M
max_execution_time = 300
memory_limit = 512M
```

### 3. Save and Restart
- Save the file
- Right-click Laragon → **Stop All**
- Right-click Laragon → **Start All**

### 4. Verify
- Go to Settings → Backup & Restore
- Check the "Max upload size" shown on the page
- Should now show **512M**

## Alternative: Use Smaller Backups

If you can't change PHP settings:

1. **Delete old backups** from `storage/app/backups/`
2. **Clean uploads folder** - remove unnecessary files from `public/uploads/`
3. **Create fresh backup** - should be smaller now

## Quick Test
```bash
# Check current limits
php -i | findstr upload_max_filesize
php -i | findstr post_max_size
```
