# Assessment Workflow Updates - Implementation Guide

This document describes the changes made to implement the three requested adjustments to the PIT assessment system.

## Summary of Changes

### 1. Assessment Workflow Updates (Step 1 & 2)

**Changes to Step 1:**
- Added password field requirement before starting assessment
- Password: `079777` (same as admin password)
- Both password and staff selection required to proceed

**Changes to Step 2:**
- **REMOVED**: First Name, Last Name, and Date of Birth input fields
- **ADDED**: "Previously Assessed Clients" list display
- **ADDED**: Question: "Is the client on this list?"
  - **YES**: Asks "Are you wanting to update or do a more extensive PIT assessment?"
    - YES: Select client from dropdown → Proceed to Step 3 (Consent)
    - NO: Return to landing page
  - **NO**: Create new placeholder client → Proceed to Step 3 (Consent)

**Files Modified:**
- `new-assessment.html` - Updated HTML structure and JavaScript workflow
- `php/api.php` - No new endpoints needed for this change

### 2. Landing Page Widget Filter

**Change:**
The "People Unhoused in Our Community" counter on the landing page now only counts people who selected specific living situations in their assessment:
- Prefer not to say
- Living in Car
- Unhoused
- Couch surfing

**Files Modified:**
- `php/api.php` - Updated `get_total_count` action to filter by `currently_staying` field
- `database/migration_update_unhoused_widget.sql` - New migration file for widget update

**Implementation:**
The count query now filters completed assessments where the `currently_staying` field (stored in JSON `assessment_data`) matches one of the four specified values.

### 3. Admin Portal Client Status Management

**Changes:**
Added a new "Client Status Management" section to the admin dashboard:
- Dropdown to select a client from all clients in system
- Displays current living situation for selected client
- Dropdown to change living situation with these options:
  - Transition House
  - Couch surfing
  - Just got a place
  - Unhoused
  - Renting a room
  - Living in Car
  - Prefer not to say
- "Apply Changes" button to update status
- Shows timestamp and success/failure message

**Files Modified:**
- `admin.html` - Added client status management section and JavaScript functions
- `php/api.php` - Added two new API endpoints:
  - `get_client_latest_assessment` - Retrieves the most recent assessment for a client
  - `update_client_status` - Updates the `currently_staying` field in assessment data

## Testing Instructions

### Test 1: New Assessment Workflow

1. Navigate to landing page (`index.html`)
2. Click "New Assessment"
3. **Step 1**: 
   - Try to proceed without password → Should show error
   - Enter incorrect password → Should show error
   - Enter correct password `079777` and select staff → Should proceed
4. **Step 2**:
   - Verify client list is displayed
   - Click "YES - Client is on the list"
   - Click "NO - Return to home" → Should return to landing page
   - Repeat Step 2, click "YES"
   - Click "YES - Update or extend assessment"
   - Select a client from dropdown → Should proceed to Step 3
5. **Alternative Step 2 Flow**:
   - Click "NO - Client is NOT on the list"
   - Should proceed to Step 3 (Consent)

### Test 2: Landing Page Count

1. Create several test assessments with different `currently_staying` values
2. Verify the count on the landing page only includes:
   - Prefer not to say
   - Living in Car
   - Unhoused
   - Couch surfing
3. Assessments with other values (e.g., "Just got a place") should NOT be counted

### Test 3: Admin Client Status Management

1. Log in to admin portal with password `079777`
2. Navigate to admin dashboard (`admin.html`)
3. Scroll to "Client Status Management" section
4. Select a client from dropdown
5. Current living situation should populate (if client has assessments)
6. Change living situation to a different value
7. Click "Apply Changes"
8. Verify success message appears with timestamp
9. Refresh the page and select same client
10. Verify the living situation was updated

## Database Migration

If upgrading an existing installation, run the migration:

```bash
mysql -u root -p pit_count < database/migration_update_unhoused_widget.sql
```

This adds a new widget specifically for the filtered unhoused count.

## API Changes

### New Endpoints

#### `get_client_latest_assessment`
- **Auth Required**: Yes (Admin)
- **Parameters**: `client_id`
- **Returns**: Latest assessment for the specified client
- **Purpose**: Used by admin portal to display current living situation

#### `update_client_status`
- **Auth Required**: Yes (Admin)
- **Parameters**: `client_id`, `living_situation`
- **Returns**: Success/failure message
- **Purpose**: Updates the `currently_staying` field in the most recent assessment for a client

### Modified Endpoints

#### `get_total_count`
- **Change**: Now filters assessments by `currently_staying` field
- **Filter Values**: Prefer not to say, Living in Car, Unhoused, Couch surfing
- **Impact**: Landing page count will show filtered results

## Security Considerations

1. **Password Protection**: Step 1 now requires password to prevent unauthorized assessments
2. **Admin Only**: Client status updates require admin authentication
3. **Session Validation**: All admin endpoints verify session before execution
4. **SQL Injection**: All queries use prepared statements with parameterized values

## Backward Compatibility

- Existing assessments will continue to work
- The new filtered count may show different numbers than before
- Client status updates only affect the most recent assessment for a client
- No breaking changes to database schema (JSON field structure maintained)

## Known Limitations

1. **New Client Flow**: When selecting "NO - Client is NOT on list", a placeholder client is created. The actual client name/DOB will be collected in the assessment form itself.
2. **Status Updates**: Only the most recent assessment for a client is updated when changing living situation in admin portal.
3. **Widget Update**: Manual database migration required for widget changes.

## Future Enhancements

1. Add ability to update client information (name, DOB) from admin portal
2. Show assessment history when updating client status
3. Add bulk status update functionality
4. Implement audit log for status changes
