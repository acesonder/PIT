-- PIT (Point-in-Time) Count Database Schema
-- Database for Cobourg Ontario and Northumberland area homeless count

CREATE DATABASE IF NOT EXISTS pit_count;
USE pit_count;

-- Table for storing outreach staff information
CREATE TABLE IF NOT EXISTS outreach_staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for storing clients (people being assessed)
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NULL,
    unique_identifier VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (first_name, last_name),
    INDEX idx_dob (date_of_birth)
);

-- Table for storing consent information
CREATE TABLE IF NOT EXISTS consent_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    full_consent BOOLEAN DEFAULT FALSE,
    partial_consent BOOLEAN DEFAULT FALSE,
    consent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Table for storing PIT assessments
CREATE TABLE IF NOT EXISTS pit_assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    staff_id INT NOT NULL,
    assessment_type ENUM('SHORT', 'MEDIUM', 'HARD', 'QUICK') NOT NULL,
    assessment_data JSON,
    is_complete BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES outreach_staff(id),
    INDEX idx_type (assessment_type),
    INDEX idx_created (created_at)
);

-- Table for admin settings and widgets
CREATE TABLE IF NOT EXISTS admin_widgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    widget_name VARCHAR(100) NOT NULL,
    widget_query TEXT NOT NULL,
    widget_description TEXT,
    is_visible_on_landing BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for system settings
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default outreach staff
INSERT INTO outreach_staff (first_name, last_name, is_active) VALUES
('Jordan', 'Staff', TRUE),
('London', 'Staff', TRUE),
('Chance', 'Staff', TRUE),
('Deb', 'Staff', TRUE),
('Jenni', 'Staff', TRUE);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value) VALUES
('admin_passcode', '079777'),
('site_title', 'Point-in-Time Count - Cobourg & Northumberland'),
('last_updated', NOW());

-- Insert default widgets
INSERT INTO admin_widgets (widget_name, widget_query, widget_description, is_visible_on_landing, display_order) VALUES
('Total Assessments', 'SELECT COUNT(*) as value FROM pit_assessments WHERE is_complete = TRUE', 'Total number of completed PIT assessments', TRUE, 1),
('Unique Individuals', 'SELECT COUNT(DISTINCT client_id) as value FROM pit_assessments', 'Total unique individuals assessed', TRUE, 2),
('Quick Assessments', 'SELECT COUNT(*) as value FROM pit_assessments WHERE assessment_type = "QUICK" AND is_complete = TRUE', 'Number of quick assessments completed', TRUE, 3),
('Short Assessments', 'SELECT COUNT(*) as value FROM pit_assessments WHERE assessment_type = "SHORT" AND is_complete = TRUE', 'Number of short assessments completed', FALSE, 4),
('Medium Assessments', 'SELECT COUNT(*) as value FROM pit_assessments WHERE assessment_type = "MEDIUM" AND is_complete = TRUE', 'Number of medium assessments completed', FALSE, 5),
('Hard Assessments', 'SELECT COUNT(*) as value FROM pit_assessments WHERE assessment_type = "HARD" AND is_complete = TRUE', 'Number of hard assessments completed', FALSE, 6),
('Full Consent Count', 'SELECT COUNT(*) as value FROM consent_records WHERE full_consent = TRUE', 'Individuals who gave full consent', FALSE, 7),
('Partial Consent Count', 'SELECT COUNT(*) as value FROM consent_records WHERE partial_consent = TRUE', 'Individuals who gave partial consent', FALSE, 8),
('Assessments Today', 'SELECT COUNT(*) as value FROM pit_assessments WHERE DATE(created_at) = CURDATE()', 'Assessments completed today', TRUE, 9),
('Assessments This Week', 'SELECT COUNT(*) as value FROM pit_assessments WHERE YEARWEEK(created_at) = YEARWEEK(NOW())', 'Assessments completed this week', FALSE, 10),
('Assessments This Month', 'SELECT COUNT(*) as value FROM pit_assessments WHERE YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())', 'Assessments completed this month', FALSE, 11),
('Active Staff', 'SELECT COUNT(*) as value FROM outreach_staff WHERE is_active = TRUE', 'Number of active outreach staff', FALSE, 12),
('Average Assessment Duration', 'SELECT "N/A" as value', 'Average time to complete assessment', FALSE, 13),
('Male Identified', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.gender") = "Man"', 'Individuals identifying as male', FALSE, 14),
('Female Identified', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.gender") = "Woman"', 'Individuals identifying as female', FALSE, 15),
('Non-Binary', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.gender") IN ("Non-binary", "Two-Spirit")', 'Non-binary and Two-Spirit individuals', FALSE, 16),
('Indigenous Identity', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.indigenous") = "Yes"', 'Individuals identifying as Indigenous', FALSE, 17),
('Veterans', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.veteran") = "Yes"', 'Veterans counted', FALSE, 18),
('Sheltered', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.location_type") = "Shelter"', 'Staying in shelters', FALSE, 19),
('Unsheltered', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.location_type") IN ("Outside", "Vehicle", "Encampment")', 'Unsheltered individuals', FALSE, 20),
('With Children', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.children_count") > 0', 'Individuals with children', FALSE, 20),
('Chronic Homeless (1yr+)', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.duration") IN ("More than 1 year", "> 1 year", "> 2 years")', 'Chronically homeless (1+ year)', FALSE, 21),
('Mental Health Concerns', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.mental_health") = "Yes"', 'Reported mental health concerns', FALSE, 22),
('Substance Use', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.substance_use") = "Yes"', 'Reported substance use', FALSE, 23),
('No Income', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.income_source") = "None"', 'Individuals with no income', FALSE, 24),
('Ontario Works', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_CONTAINS(assessment_data, ''"Ontario Works"'', "$.income_source")', 'Receiving Ontario Works', FALSE, 25),
('ODSP', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_CONTAINS(assessment_data, ''"ODSP"'', "$.income_source")', 'Receiving ODSP', FALSE, 26),
('Age 16-24', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.age") BETWEEN 16 AND 24', 'Youth (16-24 years)', FALSE, 27),
('Age 25-54', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.age") BETWEEN 25 AND 54', 'Adults (25-54 years)', FALSE, 28),
('Age 55+', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.age") >= 55', 'Seniors (55+ years)', FALSE, 29),
('First Time Homeless', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.homeless_before_25") = "No"', 'First time experiencing homelessness', FALSE, 30),
('Want Housing Support', 'SELECT COUNT(*) as value FROM pit_assessments WHERE JSON_EXTRACT(assessment_data, "$.want_contact") = "Yes"', 'Want housing/support contact', FALSE, 31);
