<?php
/**
 * API Handler for PIT Count Application
 * Handles all AJAX requests
 */

require_once '../config/config.php';

header('Content-Type: application/json');

// Get request data
$request = json_decode(file_get_contents('php://input'), true);
$action = $request['action'] ?? $_GET['action'] ?? '';

try {
    $db = getDB();
    
    switch ($action) {
        // Admin Authentication
        case 'admin_login':
            $passcode = $request['passcode'] ?? '';
            if ($passcode === ADMIN_PASSCODE) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['login_time'] = time();
                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid passcode']);
            }
            break;
            
        case 'admin_logout':
            unset($_SESSION['admin_logged_in']);
            echo json_encode(['success' => true]);
            break;
            
        case 'check_admin':
            $loggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
            echo json_encode(['logged_in' => $loggedIn]);
            break;
        
        // Get total count for landing page
        case 'get_total_count':
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE is_complete = TRUE");
            $result = $stmt->fetch();
            echo json_encode(['success' => true, 'total' => $result['total']]);
            break;
            
        // Get visible widgets for landing page
        case 'get_landing_widgets':
            $stmt = $db->prepare("
                SELECT widget_name, widget_query, widget_description 
                FROM admin_widgets 
                WHERE is_visible_on_landing = TRUE 
                ORDER BY display_order
            ");
            $stmt->execute();
            $widgets = $stmt->fetchAll();
            
            $widgetData = [];
            foreach ($widgets as $widget) {
                try {
                    $valueStmt = $db->query($widget['widget_query']);
                    $value = $valueStmt->fetch();
                    $widgetData[] = [
                        'name' => $widget['widget_name'],
                        'value' => $value['value'] ?? 0,
                        'description' => $widget['widget_description']
                    ];
                } catch (Exception $e) {
                    $widgetData[] = [
                        'name' => $widget['widget_name'],
                        'value' => 'N/A',
                        'description' => $widget['widget_description']
                    ];
                }
            }
            
            echo json_encode(['success' => true, 'widgets' => $widgetData]);
            break;
        
        // Get all outreach staff
        case 'get_staff':
            $stmt = $db->query("SELECT * FROM outreach_staff WHERE is_active = TRUE ORDER BY first_name");
            $staff = $stmt->fetchAll();
            echo json_encode(['success' => true, 'staff' => $staff]);
            break;
            
        // Add new staff member
        case 'add_staff':
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                break;
            }
            
            $firstName = $request['first_name'] ?? '';
            $lastName = $request['last_name'] ?? '';
            
            $stmt = $db->prepare("INSERT INTO outreach_staff (first_name, last_name) VALUES (?, ?)");
            $stmt->execute([$firstName, $lastName]);
            
            echo json_encode(['success' => true, 'message' => 'Staff member added', 'id' => $db->lastInsertId()]);
            break;
            
        // Remove staff member
        case 'remove_staff':
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                break;
            }
            
            $staffId = $request['staff_id'] ?? 0;
            $stmt = $db->prepare("UPDATE outreach_staff SET is_active = FALSE WHERE id = ?");
            $stmt->execute([$staffId]);
            
            echo json_encode(['success' => true, 'message' => 'Staff member removed']);
            break;
        
        // Check for duplicate client
        case 'check_duplicate':
            $firstName = $request['first_name'] ?? '';
            $lastName = $request['last_name'] ?? '';
            $dob = $request['dob'] ?? null;
            
            if ($dob) {
                $stmt = $db->prepare("SELECT * FROM clients WHERE first_name = ? AND last_name = ? AND date_of_birth = ?");
                $stmt->execute([$firstName, $lastName, $dob]);
            } else {
                $stmt = $db->prepare("SELECT * FROM clients WHERE first_name = ? AND last_name = ?");
                $stmt->execute([$firstName, $lastName]);
            }
            
            $exists = $stmt->fetch();
            echo json_encode(['success' => true, 'exists' => $exists !== false, 'client' => $exists]);
            break;
            
        // Get all existing clients
        case 'get_clients':
            $stmt = $db->query("
                SELECT c.*, COUNT(pa.id) as assessment_count 
                FROM clients c 
                LEFT JOIN pit_assessments pa ON c.id = pa.client_id 
                GROUP BY c.id 
                ORDER BY c.created_at DESC
            ");
            $clients = $stmt->fetchAll();
            echo json_encode(['success' => true, 'clients' => $clients]);
            break;
        
        // Create new client
        case 'create_client':
            $firstName = $request['first_name'] ?? '';
            $lastName = $request['last_name'] ?? '';
            $dob = $request['dob'] ?? null;
            $uniqueId = uniqid('client_', true);
            
            $stmt = $db->prepare("INSERT INTO clients (first_name, last_name, date_of_birth, unique_identifier) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $dob, $uniqueId]);
            
            $clientId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Client created', 'client_id' => $clientId]);
            break;
            
        // Save consent
        case 'save_consent':
            $clientId = $request['client_id'] ?? 0;
            $fullConsent = $request['full_consent'] ?? false;
            $partialConsent = $request['partial_consent'] ?? false;
            
            // Convert boolean values to integers (0 or 1) for MySQL BOOLEAN/TINYINT compatibility
            $fullConsentInt = $fullConsent ? 1 : 0;
            $partialConsentInt = $partialConsent ? 1 : 0;
            
            $stmt = $db->prepare("INSERT INTO consent_records (client_id, full_consent, partial_consent) VALUES (?, ?, ?)");
            $stmt->execute([$clientId, $fullConsentInt, $partialConsentInt]);
            
            echo json_encode(['success' => true, 'message' => 'Consent saved']);
            break;
        
        // Start new assessment
        case 'start_assessment':
            $clientId = $request['client_id'] ?? 0;
            $staffId = $request['staff_id'] ?? 0;
            $assessmentType = $request['assessment_type'] ?? 'SHORT';
            
            $stmt = $db->prepare("INSERT INTO pit_assessments (client_id, staff_id, assessment_type, assessment_data) VALUES (?, ?, ?, ?)");
            $stmt->execute([$clientId, $staffId, $assessmentType, json_encode([])]);
            
            $assessmentId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Assessment started', 'assessment_id' => $assessmentId]);
            break;
            
        // Save assessment progress
        case 'save_assessment':
            $assessmentId = $request['assessment_id'] ?? 0;
            $assessmentData = $request['assessment_data'] ?? [];
            $isComplete = $request['is_complete'] ?? false;
            
            $stmt = $db->prepare("UPDATE pit_assessments SET assessment_data = ?, is_complete = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([json_encode($assessmentData), $isComplete, $assessmentId]);
            
            echo json_encode(['success' => true, 'message' => 'Assessment saved']);
            break;
            
        // Get assessment by ID
        case 'get_assessment':
            $assessmentId = $request['assessment_id'] ?? $_GET['assessment_id'] ?? 0;
            
            $stmt = $db->prepare("
                SELECT pa.*, c.first_name, c.last_name, s.first_name as staff_first_name, s.last_name as staff_last_name
                FROM pit_assessments pa
                JOIN clients c ON pa.client_id = c.id
                JOIN outreach_staff s ON pa.staff_id = s.id
                WHERE pa.id = ?
            ");
            $stmt->execute([$assessmentId]);
            $assessment = $stmt->fetch();
            
            if ($assessment) {
                $assessment['assessment_data'] = json_decode($assessment['assessment_data'], true);
                echo json_encode(['success' => true, 'assessment' => $assessment]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Assessment not found']);
            }
            break;
            
        // Get all assessments (admin)
        case 'get_all_assessments':
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                break;
            }
            
            $type = $request['type'] ?? $_GET['type'] ?? null;
            
            $query = "
                SELECT pa.*, c.first_name, c.last_name, c.date_of_birth,
                       s.first_name as staff_first_name, s.last_name as staff_last_name
                FROM pit_assessments pa
                JOIN clients c ON pa.client_id = c.id
                JOIN outreach_staff s ON pa.staff_id = s.id
            ";
            
            if ($type) {
                $query .= " WHERE pa.assessment_type = ?";
                $stmt = $db->prepare($query . " ORDER BY pa.created_at DESC");
                $stmt->execute([$type]);
            } else {
                $stmt = $db->query($query . " ORDER BY pa.created_at DESC");
            }
            
            $assessments = $stmt->fetchAll();
            
            // Decode JSON data for each assessment
            foreach ($assessments as &$assessment) {
                $assessment['assessment_data'] = json_decode($assessment['assessment_data'], true);
            }
            
            echo json_encode(['success' => true, 'assessments' => $assessments]);
            break;
            
        // Update assessment (admin)
        case 'update_assessment':
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                break;
            }
            
            $assessmentId = $request['assessment_id'] ?? 0;
            $assessmentData = $request['assessment_data'] ?? [];
            
            $stmt = $db->prepare("UPDATE pit_assessments SET assessment_data = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([json_encode($assessmentData), $assessmentId]);
            
            echo json_encode(['success' => true, 'message' => 'Assessment updated']);
            break;
            
        // Delete assessment (admin)
        case 'delete_assessment':
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                break;
            }
            
            $assessmentId = $request['assessment_id'] ?? 0;
            
            $stmt = $db->prepare("DELETE FROM pit_assessments WHERE id = ?");
            $stmt->execute([$assessmentId]);
            
            echo json_encode(['success' => true, 'message' => 'Assessment deleted']);
            break;
            
        // Get all widgets (admin)
        case 'get_all_widgets':
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                break;
            }
            
            $stmt = $db->query("SELECT * FROM admin_widgets ORDER BY display_order");
            $widgets = $stmt->fetchAll();
            
            // Execute each widget query to get current value
            foreach ($widgets as &$widget) {
                try {
                    $valueStmt = $db->query($widget['widget_query']);
                    $value = $valueStmt->fetch();
                    $widget['current_value'] = $value['value'] ?? 'N/A';
                } catch (Exception $e) {
                    $widget['current_value'] = 'Error';
                }
            }
            
            echo json_encode(['success' => true, 'widgets' => $widgets]);
            break;
            
        // Toggle widget visibility
        case 'toggle_widget':
            if (!isset($_SESSION['admin_logged_in'])) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                break;
            }
            
            $widgetId = $request['widget_id'] ?? 0;
            $isVisible = $request['is_visible'] ?? false;
            
            $stmt = $db->prepare("UPDATE admin_widgets SET is_visible_on_landing = ? WHERE id = ?");
            $stmt->execute([$isVisible, $widgetId]);
            
            echo json_encode(['success' => true, 'message' => 'Widget visibility updated']);
            break;
            
        // Get dashboard stats
        case 'get_dashboard_stats':
            $stats = [
                'total_assessments' => 0,
                'short_assessments' => 0,
                'medium_assessments' => 0,
                'hard_assessments' => 0,
                'unique_clients' => 0,
                'assessments_today' => 0,
                'assessments_week' => 0,
                'assessments_month' => 0,
            ];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE is_complete = TRUE");
            $stats['total_assessments'] = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE assessment_type = 'SHORT' AND is_complete = TRUE");
            $stats['short_assessments'] = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE assessment_type = 'MEDIUM' AND is_complete = TRUE");
            $stats['medium_assessments'] = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE assessment_type = 'HARD' AND is_complete = TRUE");
            $stats['hard_assessments'] = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(DISTINCT client_id) as total FROM pit_assessments");
            $stats['unique_clients'] = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE DATE(created_at) = CURDATE()");
            $stats['assessments_today'] = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE YEARWEEK(created_at) = YEARWEEK(NOW())");
            $stats['assessments_week'] = $stmt->fetch()['total'];
            
            $stmt = $db->query("SELECT COUNT(*) as total FROM pit_assessments WHERE YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())");
            $stats['assessments_month'] = $stmt->fetch()['total'];
            
            // Get last updated time
            $stmt = $db->query("SELECT MAX(updated_at) as last_update FROM pit_assessments");
            $lastUpdate = $stmt->fetch();
            $stats['last_updated'] = $lastUpdate['last_update'] ?? date('Y-m-d H:i:s');
            
            echo json_encode(['success' => true, 'stats' => $stats]);
            break;
        
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
