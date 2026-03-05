# Jasaku - Complete Setup Guide

## Project Structure Lengkap

```
/jasaku
├── index.php                    # Landing page (homepage)
├── PRD.md                       # Product Requirements Document
├── DASHBOARD_README.md          # Dashboard implementation guide
├── DASHBOARD_VISUAL_GUIDE.md    # Visual reference guide
│
├── /auth                        # Authentication pages
│   ├── login.php               # Login form
│   ├── register.php            # Registration form
│   └── logout.php              # Logout handler
│
├── /pages                       # Main application pages
│   ├── dashboard.php           # Main dashboard with KPIs & charts
│   └── setup-business.php      # First-time business setup
│
├── /includes                    # Reusable components
│   ├── db.php                  # Database connection
│   ├── functions.php           # Helper functions
│   ├── header.php              # Page header template
│   ├── sidebar.php             # Navigation sidebar
│   └── footer.php              # Page footer template
│
├── /assets                      # Static assets
│   ├── /css
│   │   └── style.css           # Custom styles
│   ├── /js
│   │   └── main.js             # JavaScript utilities
│   └── /uploads                # User uploads (logos, etc)
│       └── /logos
│
└── /database
    └── schema.sql              # Database schema & sample data
```

## Installation Steps

### 1. Setup Database

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create new database: `jasaku_db`
3. Select database `jasaku_db`
4. Click "Import" tab
5. Choose file: `database/schema.sql`
6. Click "Go"

**Option B: Using Command Line**
```bash
mysql -u root -p
CREATE DATABASE jasaku_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jasaku_db;
SOURCE C:/xampp/htdocs/faydev/jasaku/database/schema.sql;
EXIT;
```

### 2. Configure Database Connection

File: `includes/db.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Change if different
define('DB_PASS', '');              // Add password if needed
define('DB_NAME', 'jasaku_db');
```

### 3. Create Upload Directory

```bash
# Create directory for logo uploads
mkdir -p assets/uploads/logos
chmod 755 assets/uploads/logos
```

On Windows (create manually):
```
assets/
  └── uploads/
      └── logos/
```

### 4. Start XAMPP

1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### 5. Access Application

Open browser and navigate to:
- **Landing Page**: `http://localhost/faydev/jasaku/`
- **Login**: `http://localhost/faydev/jasaku/auth/login.php`
- **Register**: `http://localhost/faydev/jasaku/auth/register.php`

## User Flow

```
┌─────────────────┐
│  Landing Page   │
│   (index.php)   │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼──────┐
│Login  │ │Register │
└───┬───┘ └──┬──────┘
    │        │
    │    ┌───▼───────────────┐
    │    │ Business Setup    │
    │    │ (First time only) │
    │    └───┬───────────────┘
    │        │
    └────┬───┘
         │
    ┌────▼────────┐
    │  Dashboard  │
    └─────────────┘
```

## Sample Login Credentials

After importing the database, you can login with:

- **Email**: `admin@jasaku.com`
- **Password**: `password`
- **Business**: Jasaku Digital Agency (already created)

## Features Overview

### Landing Page (index.php)
- Modern hero section with gradient background
- Features showcase (6 feature cards)
- Call-to-action section
- Responsive navigation
- Footer with links

### Authentication

**Login Page (auth/login.php)**
- Email & password form
- Password visibility toggle
- Remember me checkbox
- Secure password verification
- Auto-redirect to dashboard or business setup

**Register Page (auth/register.php)**
- Full name, email, password form
- Password strength indicator
- Password confirmation
- Email validation
- Auto-redirect to business setup after registration

**Logout (auth/logout.php)**
- Session destruction
- Cookie cleanup
- Redirect to landing page

### Business Setup (pages/setup-business.php)
- Required fields: Business name, category
- Optional fields: Description, phone, email, address, logo
- Logo upload (max 2MB, JPG/PNG)
- Preview logo before submit
- Sets business as primary automatically for first business
- Can be skipped if already has business

### Dashboard (pages/dashboard.php)
- **5 KPI Cards**:
  1. Total Revenue (current month)
  2. Active Deals
  3. Total Clients
  4. Deal Conversion Rate
  5. Outstanding Payments alert
  
- **Chart.js Visualization**:
  - Revenue vs Expense bar chart
  - 6 months historical data
  - Export to PNG feature
  - Interactive tooltips

- **Recent Deals Widget**:
  - 5 most recent deals
  - Stage badges (color-coded)
  - Client info & deal value
  - Expected close date

- **Quick Actions**:
  - Create new deal
  - Add client
  - Record transaction
  - Add service package

## Security Features

1. **Password Hashing**: Using `password_hash()` with bcrypt
2. **SQL Injection Prevention**: Prepared statements throughout
3. **XSS Prevention**: Output escaping with `htmlspecialchars()`
4. **Session Management**: Secure session handling
5. **File Upload Security**: Type & size validation
6. **CSRF Protection**: Token functions ready (in functions.php)

## Helper Functions

Located in `includes/functions.php`:

```php
// Authentication
isLoggedIn()
getCurrentBusinessId()
getCurrentUserId()
requireLogin()

// Formatting
formatCurrency($amount, $withSymbol)
formatDate($date, $withTime)
e($string)  // XSS protection

// Calculations
calculatePercentageChange($current, $previous)
getChangeClass($percentage)
getChangeIcon($percentage)

// Flash Messages
setFlashMessage($type, $message)
getFlashMessage()

// CSRF Protection
generateCSRFToken()
verifyCSRFToken($token)
```

## Database Tables

1. **users** - User accounts
2. **businesses** - Business profiles (multi-business support)
3. **service_packages** - Service offerings
4. **clients** - Client database
5. **deals** - Deal pipeline tracking
6. **deal_payments** - Payment tracking for deals
7. **transactions** - Income & expense records

## Responsive Design

All pages are fully responsive with breakpoints:
- **Mobile**: < 768px (hamburger menu, stacked layout)
- **Tablet**: 768px - 1200px (collapsible sidebar)
- **Desktop**: > 1200px (full layout with fixed sidebar)

## Browser Support

- Chrome (latest) ✅
- Firefox (latest) ✅
- Safari (latest) ✅
- Edge (latest) ✅
- IE11 (limited) ⚠️

## Technologies Used

| Component | Technology |
|-----------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5 |
| Icons | Font Awesome 6 |
| Charts | Chart.js 4.4.0 |
| JavaScript | jQuery 3.x |
| Backend | PHP 7.4+ |
| Database | MySQL 5.7+ |
| Server | Apache (XAMPP) |

## Testing Checklist

- [ ] Database imported successfully
- [ ] Can access landing page
- [ ] Can register new account
- [ ] Can login with credentials
- [ ] Business setup form works
- [ ] Logo upload works
- [ ] Dashboard displays correctly
- [ ] KPI metrics show data
- [ ] Chart renders properly
- [ ] Recent deals widget shows data
- [ ] Can logout successfully

## Troubleshooting

### "Cannot connect to database"
- Check MySQL is running in XAMPP
- Verify credentials in `includes/db.php`
- Ensure database `jasaku_db` exists

### "Call to undefined function mysqli_connect()"
- Enable mysqli extension in php.ini
- Restart Apache

### "Permission denied" on logo upload
- Check folder permissions: `chmod 755 assets/uploads/logos`
- Ensure folder exists

### Dashboard shows "No data"
- Run sample data from `database/schema.sql`
- Check if business_id is set in session

### Styles not loading
- Clear browser cache
- Check Bootstrap CDN is accessible
- Verify CSS file path

## Next Steps

After setup, you can:

1. Create more pages:
   - `pages/deals.php` - Deal pipeline management
   - `pages/clients.php` - Client list & management
   - `pages/services.php` - Service packages
   - `pages/finance.php` - Financial tracking

2. Add features:
   - Deal stage management
   - Client detail view
   - Invoice generation
   - Payment tracking
   - Reports & analytics

3. Enhance security:
   - Implement CSRF tokens on forms
   - Add rate limiting
   - Email verification
   - 2FA authentication

## Support

For issues or questions, refer to:
- **PRD.md** - Product requirements and specifications
- **DASHBOARD_README.md** - Dashboard implementation details
- **DASHBOARD_VISUAL_GUIDE.md** - UI/UX reference

## License

This project is part of Jasaku MVP development.

---

**Version**: 1.0  
**Last Updated**: March 5, 2026  
**Status**: MVP Complete - Ready for Development
