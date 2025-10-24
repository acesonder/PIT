# QUICK PIT Assessment - Implementation Guide

## Overview
The QUICK PIT Assessment is a new ultra-fast assessment option designed for rapid PIT count enumeration. It takes only 2-3 minutes to complete and collects essential information.

## Features

### Form Fields
1. **Personal Information**
   - First Name (required)
   - Last Name (required)
   - Gender dropdown (required)
   - Date of Birth (optional)
   - Age (optional - alternative to DOB)

2. **Current Housing Situation**
   - Currently staying dropdown (required) with options:
     - Transition House
     - Couch surfing
     - Just got a place
     - Unhoused
     - Renting a room
     - Living in Car
     - Prefer not to say

3. **Consent & Agreement**
   - Three consent options (one required):
     - Full name and other information
     - Initials and other information only
     - No name, but all other information
   - Auto-populated date and time
   - Signature field (required)
   - Agreement statement for Northumberland County and OUTSINC

## Installation

### For New Installations
1. Run the setup script as usual:
   ```bash
   ./setup.sh
   ```
   The schema already includes the QUICK assessment type.

### For Existing Installations
1. Run the migration script:
   ```bash
   mysql -u [username] -p pit_count < database/migration_add_quick_assessment.sql
   ```
   Replace `[username]` with your MySQL username.

2. Verify the migration:
   ```bash
   mysql -u [username] -p pit_count -e "SHOW COLUMNS FROM pit_assessments LIKE 'assessment_type';"
   ```
   You should see QUICK in the ENUM values.

## Usage

### For Outreach Staff
1. Navigate to "New Assessment" from the landing page
2. Select staff member
3. Enter client information
4. Choose consent type
5. Select "⚡ QUICK PIT Assessment"
6. Complete the 7-question form
7. Submit

### For Administrators
- QUICK assessments are tracked in the admin dashboard
- A new widget "Quick Assessments" shows the count
- All QUICK assessments contribute to the total PIT count

## Validation Rules
- First Name, Last Name, Gender, and Currently Staying are required
- Either Date of Birth OR Age must be provided
- One consent option must be selected
- Signature is required
- Submit button is disabled until all validation passes

## Technical Details

### Database Schema
```sql
assessment_type ENUM('SHORT', 'MEDIUM', 'HARD', 'QUICK')
```

### File Structure
- `assessment-quick.html` - Main form
- `database/schema.sql` - Updated schema
- `database/migration_add_quick_assessment.sql` - Migration script
- `css/style.css` - Includes signature field styling

### API Integration
The QUICK assessment uses the same API endpoints as other assessment types:
- `start_assessment` - Creates the assessment record
- `save_assessment` - Saves form data
- Assessment type is set to 'QUICK'

## Styling
The QUICK assessment follows the same design theme as other assessments:
- Blue gradient background
- White card-based form sections
- Section headers with emojis
- Progress bar at top
- Signature field has cursive font styling

## Testing
Validated features:
✅ Form loads correctly
✅ All dropdowns populate
✅ Date/time auto-updates
✅ Validation works properly
✅ Submit button enables when valid
✅ No security vulnerabilities

## Support
For questions or issues, contact OUTSINC at info@outsinc.ca
