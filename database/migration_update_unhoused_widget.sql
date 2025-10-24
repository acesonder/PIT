-- Migration: Update widget for filtered unhoused count
-- This migration updates the widget query to filter by specific living situations

USE pit_count;

-- Update the "People Currently Unhoused" widget if it exists (or create it)
-- First check if widget exists for "People Unhoused" or similar
DELETE FROM admin_widgets WHERE widget_name = 'People Currently Unhoused';

-- Insert the new filtered widget
INSERT INTO admin_widgets (widget_name, widget_query, widget_description, is_visible_on_landing, display_order) 
VALUES (
    'People Currently Unhoused',
    'SELECT COUNT(*) as value FROM pit_assessments WHERE is_complete = TRUE AND (JSON_UNQUOTE(JSON_EXTRACT(assessment_data, "$.currently_staying")) = "Prefer not to say" OR JSON_UNQUOTE(JSON_EXTRACT(assessment_data, "$.currently_staying")) = "Living in Car" OR JSON_UNQUOTE(JSON_EXTRACT(assessment_data, "$.currently_staying")) = "Unhoused" OR JSON_UNQUOTE(JSON_EXTRACT(assessment_data, "$.currently_staying")) = "Couch surfing")',
    'Number of people currently unhoused (includes: Prefer not to say, Living in Car, Unhoused, Couch surfing)',
    TRUE,
    1
);
