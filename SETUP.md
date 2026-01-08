# Quick Setup Guide

## Step 1: Set Your Password

1. Open `config.php`
2. Find this line:
   ```php
   define('JOURNAL_PASSWORD', 'your_password_here');
   ```
3. Replace `'your_password_here'` with your desired password
4. Save the file

## Step 2: Start Your Server

If using XAMPP:
1. Open XAMPP Control Panel
2. Start Apache
3. The application is now running at: `http://localhost/journal/`

## Step 3: Login

1. Open your browser and go to: `http://localhost/journal/`
2. Enter the password you set in Step 1
3. Click "Login"

## Step 4: Create Your First Entry

1. You'll see the daily dashboard
2. Click "Make a new entry..."
3. Fill in the sections (at minimum, add something to the Summary section)
4. Optionally add a Google Photos link
5. Click "Save"

## Step 5: View Your Entry

1. You'll be redirected to the entry view
2. Click "← Back to dashboard" to return to the main page
3. Your entry summary will now be visible on the dashboard

## Managing Anniversaries

1. On the sidebar, find "Birthdays / Anniversaries"
2. Click the ✏️ (edit) button
3. Add entries one per line, e.g.:
   ```
   Mom's Birthday 1965
   Wedding Anniversary 2015
   ```
4. Click "Save"

## Tips

- The **Summary** section is the most important - it's what shows on your dashboard
- Entries are automatically organized by date and year
- You can navigate between dates using the ◄ ► arrows
- Each date can have multiple entries (one per year)
- The template automatically fills in when you create a new entry

## Customizing Your Journal

### Change the Color Scheme
Edit `style.css` and search for `#2c6b8e` (the blue color) and replace with your preferred color.

### Modify the Entry Template
Edit `entries/template.md` to add, remove, or modify the sections in new entries.

### Adjust Timezone
Edit `config.php` and change:
```php
date_default_timezone_set('Europe/Zurich');
```
to your timezone (e.g., 'America/New_York', 'Asia/Tokyo', etc.)

## Need Help?

Check the full README.md for more detailed information and troubleshooting tips.
