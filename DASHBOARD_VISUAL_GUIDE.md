# Jasaku Dashboard - Visual Guide

## Dashboard Layout Structure

```
┌─────────────────────────────────────────────────────────────────────────┐
│ SIDEBAR                 │ TOPBAR                                        │
│ ┌─────────────┐        │ ┌──────────┐  ┌──────────────┐               │
│ │ Jasaku Logo │        │ │ Business │  │ User Profile │               │
│ └─────────────┘        │ │ Switcher │  │   Dropdown   │               │
│                        │ └──────────┘  └──────────────┘               │
│ ┌ Menu Utama ┐         ├───────────────────────────────────────────────┤
│ • Dashboard           │ MAIN CONTENT AREA                              │
│ • Deals               │                                                │
│ • Klien               │ Dashboard                                      │
│                       │ Selamat datang kembali! Berikut ringkasan...   │
│ ┌ Layanan ┐            │                                                │
│ • Paket Jasa          │ ┌─────────────────────────────────────────────┐│
│ • Keuangan            │ │ KPI METRICS CARDS (4 columns)               ││
│                       │ ├─────────┬─────────┬─────────┬───────────────┤│
│ ┌ Pengaturan ┐         │ │ Revenue │ Active  │ Total   │ Conversion    ││
│ • Profil Bisnis       │ │         │ Deals   │ Clients │ Rate          ││
│                       │ └─────────┴─────────┴─────────┴───────────────┘│
│                       │                                                │
│                       │ ┌─────────────────────────────────────────────┐│
│                       │ │ OUTSTANDING PAYMENTS ALERT (if any)         ││
│                       │ └─────────────────────────────────────────────┘│
│                       │                                                │
│                       │ ┌─────────────────────┬─────────────────────┐ │
│                       │ │ Revenue vs Expense  │ Recent Deals        │ │
│                       │ │ Chart (Chart.js)    │ Activity List       │ │
│                       │ │                     │                     │ │
│                       │ │ [Bar Chart 6mo]     │ • Deal 1            │ │
│                       │ │                     │ • Deal 2            │ │
│                       │ │                     │ • Deal 3            │ │
│                       │ └─────────────────────┴─────────────────────┘ │
│                       │                                                │
│                       │ ┌─────────────────────────────────────────────┐│
│                       │ │ Quick Actions                               ││
│                       │ │ [+Deal] [+Client] [+Transaction] [+Service] ││
│                       │ └─────────────────────────────────────────────┘│
└───────────────────────┴────────────────────────────────────────────────┘
```

## KPI Card Detailed Structure

```
┌────────────────────────────────────┐
│ ┌──┐                               │
│ │💰│  Total Revenue                │
│ └──┘  Bulan Ini                    │
│                                    │
│  Rp 45.231.890                     │  ← Big, bold number
│                                    │
│  ↑ 15.3%  vs bulan lalu            │  ← Trend (green if up)
└────────────────────────────────────┘
```

Each card features:
- Gradient icon background
- Clear label and period
- Large value display (formatted currency or number)
- Trend indicator with percentage change
- Color-coded (green=up, red=down, gray=neutral)
- Smooth hover effect (lifts up slightly)

## Revenue vs Expense Chart

```
Revenue vs Expense
6 Bulan Terakhir                    [Export ▼]

        ┌─────────────────────────────────────┐
15M ─   │     ▌▌                             │
        │     ▌▌  ▌▌                         │
10M ─   │  ▌▌ ▌▌  ▌▌  ▌▌                    │
        │  ▌▌ ▌▌  ▌▌  ▌▌  ▌▌                │
5M  ─   │  ▌▌ ▌▌  ▌▌  ▌▌  ▌▌  ▌▌            │
        └─────────────────────────────────────┘
          Oct Nov Dec Jan Feb Mar

        ■ Revenue (Green)    ■ Expense (Red)
```

Chart features:
- Interactive tooltips with Rupiah formatting
- Responsive sizing
- Export to PNG functionality
- 6 months historical data
- Custom colors matching brand
- Grid lines for readability

## Recent Deals Widget

```
Recent Deals                      Lihat Semua →

┌────────────────────────────────────────────┐
│ [📧] Website Company Profile               │
│      PT. Maju Bersama    Rp 13.500.000    │
│      [Proposal]  📅 15 Apr 2026            │
├────────────────────────────────────────────┤
│ [💬] Social Media Management               │
│      CV. Sukses Mandiri  Rp 5.000.000     │
│      [Negotiation]  📅 25 Mar 2026         │
├────────────────────────────────────────────┤
│ [🏆] Logo & Brand Identity                 │
│      Toko Elektronik     Rp 2.500.000     │
│      [Won]  📅 01 Mar 2026                 │
└────────────────────────────────────────────┘
```

Features:
- Icon badges for each stage (color-coded)
- Client name and deal value
- Stage badge with appropriate color
- Expected close date (if set)
- Hover effect on each item
- Empty state when no deals

## Stage Badge Colors

```
Lead          → Gray    (📧 Envelope icon)
Qualified     → Blue    (✓ Check circle)
Proposal      → Purple  (📄 File icon)
Negotiation   → Amber   (💬 Comments)
Won           → Green   (🏆 Trophy)
Lost          → Red     (✕ Times circle)
```

## Quick Actions Section

```
Quick Actions

┌─────────────┬─────────────┬─────────────┬─────────────┐
│    [ + ]    │    [ + ]    │    [ + ]    │    [ + ]    │
│             │             │             │             │
│ Buat Deal   │ Tambah      │ Catat       │ Tambah      │
│ Baru        │ Klien       │ Transaksi   │ Paket Jasa  │
└─────────────┴─────────────┴─────────────┴─────────────┘
```

Each button:
- Large icon at top
- Clear label below
- Dashed border (becomes solid on hover)
- Changes to primary color on hover
- Box shadow effect

## Responsive Behavior

### Desktop (>1200px)
- Sidebar always visible (260px width)
- 4 KPI cards in one row
- Chart and Recent Deals side-by-side (8:4 columns)
- Quick Actions in 4 columns

### Tablet (768-1200px)
- Collapsible sidebar
- 2 KPI cards per row
- Chart and Recent Deals stacked
- Quick Actions in 2 columns

### Mobile (<768px)
- Hamburger menu (sidebar off-canvas)
- 1 KPI card per row (stacked)
- Chart below KPIs
- Recent Deals below chart
- Quick Actions in 2 columns

## Color Palette

```
Primary (Indigo):  #4F46E5  ████████
Success (Green):   #10B981  ████████
Danger (Red):      #EF4444  ████████
Warning (Amber):   #F59E0B  ████████
Info (Blue):       #3B82F6  ████████
Dark:              #1F2937  ████████
Light:             #F9FAFB  ████████
```

## Typography

- **Headings**: Inter, 700 weight
- **Body**: Inter, 400 weight
- **Labels**: Inter, 600 weight
- **Metrics**: Inter, 700 weight, 2rem size

## Spacing System

- **Card padding**: 1.5rem (24px)
- **Gap between cards**: 1.5rem (24px)
- **Button padding**: 0.625rem 1.25rem
- **Icon size**: 1.25rem (20px)
- **Border radius**: 8-12px

## Animation & Transitions

All elements use smooth transitions:
- Duration: 150-300ms
- Easing: cubic-bezier(0.4, 0, 0.2, 1)
- Hover effects: translateY(-2px) + box-shadow

## Performance Metrics

Expected load times:
- Initial page load: < 2s
- Database queries: < 100ms each
- Chart rendering: < 500ms
- Total queries per page: 8-10

## Browser Compatibility

✅ Chrome 90+
✅ Firefox 88+
✅ Safari 14+
✅ Edge 90+
⚠️  IE11 (limited support)

---

For implementation details, see DASHBOARD_README.md
