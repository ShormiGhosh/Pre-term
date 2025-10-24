# Pre-term System - Dark Color Theme

## Color Palette

This document describes the color scheme used throughout the Pre-term Attendance System.

### Primary Colors

| Color Name | Hex Code | Usage |
|------------|----------|-------|
| Deep Purple | `#302e4a` | Primary buttons, navbar, user info backgrounds |
| Dark Blue | `#100f21` | Main background, input backgrounds |
| Medium Purple | `#1c1a36` | Secondary background (cards, dashboard cards) |
| Highlight Purple | `#401a75` | Hover effects, borders, focus states, accents |
| Light Gray-Blue | `#C1CEE5` | Text labels, secondary text, links |
| Off-White | `#F1F5FB` | Primary text, headings |
| Coral Orange | `#F9896B` | Error alerts only (minimal use) |
| Pure White | `#FFFFFF` | Button text, emphasized text |

## Usage Guide

### Background Colors
- **Main Background**: `#100f21` (very dark blue)
- **Cards/Forms**: `#1c1a36` (dark purple-blue)
- **User Info Boxes**: `#302e4a` (medium purple)
- **Navbar**: `#302e4a` (medium purple)
- **Input Fields**: `#100f21` (very dark blue)

### Text Colors
- **Primary Text**: `#F1F5FB` (off-white on dark backgrounds)
- **Labels**: `#C1CEE5` (light gray-blue)
- **Headings**: `#C1CEE5` (light gray-blue) or `#F1F5FB` (off-white)
- **White Text**: `#FFFFFF` (on buttons and colored backgrounds)

### Interactive Elements
- **Primary Buttons**: 
  - Background: `#302e4a`
  - Text: `#FFFFFF`
  - Hover: `#401a75` (highlight purple)
  
- **Secondary Buttons**: 
  - Background: `#1c1a36` (dark purple-blue)
  - Text: `#C1CEE5`
  - Border: `#302e4a`
  - Hover Background: `#302e4a`
  - Hover Text: `#FFFFFF`

- **Danger/Logout Buttons**:
  - Background: `#401a75` (highlight purple)
  - Text: `#FFFFFF`
  - Hover: `#302e4a`

- **Links**: 
  - Default: `#C1CEE5`
  - Hover: `#401a75`

- **Navbar Links**:
  - Default: `#F1F5FB`
  - Hover Background: `#401a75`

### Alerts & Notifications
- **Success Alert**: 
  - Background: `#302e4a`
  - Text: `#C1CEE5`
  - Border: `#401a75`

- **Error Alert**: 
  - Background: `#F9896B`
  - Text: `#FFFFFF`
  - Border: `#F9896B`

### Form Elements
- **Input Fields**:
  - Background: `#100f21`
  - Border: `#302e4a`
  - Focus Border: `#401a75`
  - Text: `#F1F5FB`

- **Labels**: `#C1CEE5`
- **Error Messages**: `#F9896B`
- **Password Toggle Icons**: `#C1CEE5`
- **Feature Icons**: `#401a75` (highlight purple)

## Color Psychology

- **Deep Purple (#302e4a, #1c1a36, #100f21)**: Professional, sophisticated, reduces eye strain - perfect for a modern educational system
- **Highlight Purple (#401a75)**: Engaging, memorable, provides clear visual feedback for interactive elements
- **Coral Orange (#F9896B)**: Used sparingly for errors only - attention-grabbing when needed
- **Light Blue-Gray (#C1CEE5)**: Calm, professional, provides subtle contrast
- **Off-White (#F1F5FB)**: Clean, readable, reduces harsh contrast

## Accessibility Notes

- All text has sufficient contrast ratios (WCAG AA compliant)
- Dark theme significantly reduces eye strain during extended use
- Color is never the only indicator (icons and text labels provided)
- Interactive elements have clear hover states with `#401a75`
- Password visibility toggle icons are clearly visible in `#C1CEE5`

## Implementation

Colors are applied directly in Blade templates using inline styles. For consistency:
- Main layout: `resources/views/layouts/app.blade.php`
- Home page: `resources/views/home.blade.php`
- All forms and dashboards use these colors consistently

Last Updated: October 24, 2025
