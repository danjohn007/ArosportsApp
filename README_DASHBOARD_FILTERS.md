# Dashboard Filters and Representative Data - Implementation Guide

## Overview

This implementation adds the following features to the Arosports system:

1. **Date filters for all user levels** - Allow filtering dashboard data by date range
2. **SuperAdmin entity filters** - Club/empresa/fraccionamiento filters for SuperAdmin
3. **Daily income vs expense charts** - Instead of monthly charts
4. **Representative data for entities** - Personal data and passwords for club/empresa/fraccionamiento representatives

## Database Updates Required

Before using the new features, run the database update script:

```sql
-- Execute this SQL script on your database
source sql/arosports_update_v1.2.sql
```

Or run the SQL commands manually:

```sql
-- Add representative fields to clubs table
ALTER TABLE `clubes` 
ADD COLUMN `representante_nombre` varchar(100) DEFAULT NULL AFTER `email`,
ADD COLUMN `representante_email` varchar(100) DEFAULT NULL AFTER `representante_nombre`,
ADD COLUMN `representante_telefono` varchar(20) DEFAULT NULL AFTER `representante_email`,
ADD COLUMN `representante_password` varchar(255) DEFAULT NULL AFTER `representante_telefono`;

-- Add representative fields to empresas table
ALTER TABLE `empresas` 
ADD COLUMN `representante_nombre` varchar(100) DEFAULT NULL AFTER `email`,
ADD COLUMN `representante_email` varchar(100) DEFAULT NULL AFTER `representante_nombre`,
ADD COLUMN `representante_telefono` varchar(20) DEFAULT NULL AFTER `representante_email`,
ADD COLUMN `representante_password` varchar(255) DEFAULT NULL AFTER `representante_telefono`;

-- Add representative fields to fraccionamientos table
ALTER TABLE `fraccionamientos` 
ADD COLUMN `representante_nombre` varchar(100) DEFAULT NULL AFTER `club_id`,
ADD COLUMN `representante_email` varchar(100) DEFAULT NULL AFTER `representante_nombre`,
ADD COLUMN `representante_telefono` varchar(20) DEFAULT NULL AFTER `representante_email`,
ADD COLUMN `representante_password` varchar(255) DEFAULT NULL AFTER `representante_telefono`;
```

## New Features

### 1. Dashboard Date Filters

- **Available for all user roles**: cliente, admin, superadmin
- **Default range**: Last 30 days
- **Location**: Top of dashboard in filter card
- **Functionality**: Filters all statistics, charts, and recent reservations

### 2. SuperAdmin Entity Filters

- **Available only for SuperAdmin role**
- **Filters**:
  - Club selector
  - Empresa selector  
  - Fraccionamiento selector
- **Functionality**: Further refines data based on selected entities

### 3. Daily Charts

- **Previous**: Monthly income vs expenses chart
- **New**: Daily income vs expenses chart based on selected date range
- **Benefits**: More granular view of financial performance

### 4. Representative Data Management

When creating or editing clubs, empresas, or fraccionamientos as SuperAdmin, you can now specify:

- Representative name
- Representative email
- Representative phone
- Representative password (encrypted)

This enables future functionality for representatives to have their own dashboard access.

## Files Modified

### Controllers
- `controllers/DashboardController.php` - Added filter processing and daily chart data
- `controllers/AdminController.php` - Added representative data handling

### Views  
- `views/dashboard/index.php` - Added filter interface and updated charts
- `views/admin/clubes/form.php` - Added representative fields
- `views/admin/empresas/form.php` - Added representative fields
- `views/admin/fraccionamientos/form.php` - Added representative fields

### Database
- `sql/arosports_update_v1.2.sql` - Database schema updates

## Usage Instructions

### For All Users
1. Access the dashboard
2. Use the date filters at the top to select your desired date range
3. Click "Aplicar Filtros" to update the dashboard
4. View the daily income vs expenses chart for detailed analysis

### For SuperAdmin
1. Use additional entity filters (Club, Empresa, Fraccionamiento) 
2. Create/edit entities with representative information
3. Representative passwords are automatically encrypted
4. Leave password field empty when editing to keep existing password

## Technical Notes

- All SQL queries are parameterized to prevent SQL injection
- Passwords are hashed using PHP's `password_hash()` function
- Date filters use HTML5 date inputs for better UX
- Charts are updated to show daily data with proper date formatting
- Filter state is maintained through GET parameters

## Testing

Test the following scenarios:
1. Date filter functionality for different user roles
2. SuperAdmin entity filters working correctly
3. Daily chart data displaying properly
4. Representative data creation and editing
5. Password encryption working as expected