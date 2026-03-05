# Jasaku Dashboard Implementation

Complete dashboard implementation for Jasaku - Platform Manajemen Bisnis Jasa.

## Overview

This is a comprehensive dashboard implementation featuring:
- **5 KPI Metrics Cards** with trend indicators
- **Chart.js Visualization** (Revenue vs Expense for 6 months)
- **Recent Deals Widget** with activity list
- **Quick Actions** for common tasks
- **Responsive Design** using Bootstrap 5
- **Modern UI** with smooth animations and transitions

## Features

### Dashboard Metrics

1. **Total Revenue (Current Month)**
   - Shows current month's total income
   - Percentage change vs previous month
   - Trend indicator (up/down arrow)

2. **Active Deals**
   - Count of deals not in Won/Lost stage
   - Percentage change from previous month

3. **Total Clients**
   - Total number of clients in database
   - Growth percentage indicator

4. **Deal Conversion Rate**
   - Percentage of Won deals vs Total deals
   - Badge indicator (Excellent/Good/Needs Improvement)

5. **Outstanding Payments**
   - Total unpaid amounts from Won deals
   - Alert banner if outstanding > 0

### Visualizations

- **Revenue vs Expense Chart**: Interactive bar chart showing 6 months of financial data
- **Recent Deals**: List of 5 most recent deals with stage badges
- **Quick Actions**: Fast access buttons for common tasks

## Project Structure

```
/jasaku
├── /assets
│   ├── /css
│   │   └── style.css          # Custom styles
│   └── /js
│       └── main.js             # JavaScript utilities
├── /database
│   └── schema.sql              # Database schema with sample data
├── /includes
│   ├── db.php                  # Database connection
│   ├── functions.php           # Helper functions
│   ├── header.php              # Page header template
│   ├── sidebar.php             # Navigation sidebar
│   └── footer.php              # Page footer template
├── /pages
│   └── dashboard.php           # Main dashboard page
└── PRD.md                      # Product Requirements Document
```

## Installation

### 1. Database Setup

```bash
# Import the database schema
mysql -u root -p < database/schema.sql
```

Or manually:
1. Open phpMyAdmin
2. Create database `jasaku_db`
3. Import `database/schema.sql`

### 2. Configure Database Connection

Edit `includes/db.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jasaku_db');
```

### 3. Start Development Server

```bash
# Using PHP built-in server
cd C:\xampp\htdocs\faydev\jasaku
php -S localhost:8000
```

Or use XAMPP:
1. Place project in `C:\xampp\htdocs\faydev\jasaku`
2. Start Apache & MySQL
3. Access: `http://localhost/faydev/jasaku/pages/dashboard.php`

### 4. Login Credentials (Sample Data)

- **Email**: admin@jasaku.com
- **Password**: password

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5, Font Awesome 6 |
| JavaScript | jQuery 3.x, Chart.js 4.4.0 |
| Backend | PHP 7.4+ |
| Database | MySQL 5.7+ |
| Hosting | XAMPP / cPanel Compatible |

## Dashboard Components

### KPI Cards

Each KPI card includes:
- Icon with gradient background
- Label and period
- Large value display
- Trend indicator with percentage change
- Smooth hover animation

### Chart Configuration

The Revenue vs Expense chart uses Chart.js with:
- Bar chart type
- Responsive design
- Custom tooltips with Rupiah formatting
- Export to PNG functionality
- 6 months of historical data

### Recent Deals Widget

Features:
- Icon badges for each deal stage
- Client name and deal value
- Stage badge with color coding
- Expected close date
- Empty state for no deals

## Key Functions (functions.php)

```php
// Currency formatting
formatCurrency($amount, $withSymbol = true)

// Date formatting
formatDate($date, $withTime = false)

// Percentage calculations
calculatePercentageChange($current, $previous)

// Trend indicators
getChangeClass($percentage)  // Returns: success/danger/secondary
getChangeIcon($percentage)   // Returns: fa-arrow-up/fa-arrow-down/fa-minus

// Flash messages
setFlashMessage($type, $message)
getFlashMessage()

// Security
generateCSRFToken()
verifyCSRFToken($token)
```

## Responsive Design

The dashboard is fully responsive with breakpoints:
- **Desktop** (>1200px): Full layout with sidebar
- **Tablet** (768-1200px): Collapsible sidebar
- **Mobile** (<768px): Hamburger menu, stacked cards

## Color Scheme

```css
--primary-color: #4F46E5    /* Indigo */
--success-color: #10B981    /* Green */
--danger-color: #EF4444     /* Red */
--warning-color: #F59E0B    /* Amber */
--info-color: #3B82F6       /* Blue */
```

## Database Queries

All dashboard metrics use optimized SQL queries with:
- Prepared statements (SQL injection prevention)
- Proper indexes for fast queries
- Joins to minimize N+1 queries
- Date-based filtering for trends

## Performance Optimizations

1. **Database Indexes**: On foreign keys and frequently searched columns
2. **Query Optimization**: Using JOINs instead of multiple queries
3. **CSS/JS Minification**: For production (not in dev)
4. **CDN Resources**: Bootstrap, Font Awesome, Chart.js from CDN
5. **Caching**: Browser caching for static assets

## Security Features

- Password hashing with `password_hash()`
- SQL injection prevention (prepared statements)
- XSS prevention with `htmlspecialchars()`
- CSRF token support (functions included)
- Session management
- Input validation

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- IE11 (limited support)

## Future Enhancements

From the PRD (Out of Scope for MVP):
- Multi-user/role management
- Recurring/subscription services
- Invoice generation & email
- Payment gateway integration
- Client portal
- Advanced reporting & analytics
- Mobile app
- Real-time notifications

## Development Guidelines

### Adding New Metrics

1. Add SQL query in `dashboard.php`
2. Create new KPI card in HTML
3. Add corresponding styles in `<style>` section
4. Update helper functions if needed

### Adding New Charts

1. Prepare data array in PHP
2. Pass to JavaScript via `json_encode()`
3. Create canvas element with unique ID
4. Initialize Chart.js with configuration
5. Use `ChartUtils` for consistent styling

### Customizing Colors

Edit CSS variables in `assets/css/style.css`:

```css
:root {
    --primary-color: #YOUR_COLOR;
    --success-color: #YOUR_COLOR;
    /* etc. */
}
```

## Troubleshooting

### Charts not displaying
- Check browser console for JavaScript errors
- Verify Chart.js CDN is loading
- Ensure canvas element has proper ID
- Check if data array is properly formatted

### Database connection errors
- Verify MySQL is running
- Check credentials in `includes/db.php`
- Ensure database exists and schema is imported
- Check PHP error logs

### Styling issues
- Clear browser cache
- Check if Bootstrap CSS is loading
- Verify custom CSS path is correct
- Inspect element to see applied styles

## License

This project is part of Jasaku MVP development.

## Support

For issues or questions, refer to the PRD.md or contact the development team.

---

**Created**: March 5, 2026  
**Version**: 1.0 (MVP)  
**Status**: Complete Dashboard Implementation
