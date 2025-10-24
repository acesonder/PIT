-- Migration: Add QUICK assessment type
-- This migration adds the QUICK option to the assessment_type ENUM

USE pit_count;

-- Modify the pit_assessments table to include QUICK in the ENUM
ALTER TABLE pit_assessments 
MODIFY COLUMN assessment_type ENUM('SHORT', 'MEDIUM', 'HARD', 'QUICK') NOT NULL;

-- Add a widget for Quick Assessments
INSERT INTO admin_widgets (widget_name, widget_query, widget_description, is_visible_on_landing, display_order) 
VALUES ('Quick Assessments', 'SELECT COUNT(*) as value FROM pit_assessments WHERE assessment_type = "QUICK" AND is_complete = TRUE', 'Number of quick assessments completed', TRUE, 3)
ON DUPLICATE KEY UPDATE widget_query = VALUES(widget_query);

-- Update display orders for existing widgets (shift them down by 1)
UPDATE admin_widgets SET display_order = display_order + 1 
WHERE widget_name IN ('Short Assessments', 'Medium Assessments', 'Hard Assessments') 
AND display_order >= 3;
