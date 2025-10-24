// PIT Count Application JavaScript

// API endpoint
const API_URL = './php/api.php';

// Utility function for AJAX requests
async function apiRequest(action, data = {}) {
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action, ...data })
        });
        
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: 'Network error occurred' };
    }
}

// Show alert message
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => alertDiv.remove(), 5000);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-CA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Load total count for landing page
async function loadTotalCount() {
    const result = await apiRequest('get_total_count');
    if (result.success) {
        const countElement = document.getElementById('total-count');
        if (countElement) {
            animateCount(countElement, result.total);
        }
    }
}

// Animate counter
function animateCount(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 30);
}

// Load landing page widgets
async function loadLandingWidgets() {
    const result = await apiRequest('get_landing_widgets');
    if (result.success && result.widgets) {
        const container = document.getElementById('widgets-container');
        if (container) {
            container.innerHTML = '';
            result.widgets.forEach(widget => {
                const widgetHTML = `
                    <div class="widget">
                        <div class="widget-header">
                            <span class="widget-title">${widget.name}</span>
                        </div>
                        <div class="widget-value">${widget.value}</div>
                        <div class="widget-description">${widget.description}</div>
                    </div>
                `;
                container.innerHTML += widgetHTML;
            });
        }
    }
}

// Update last updated timestamp
function updateLastUpdated() {
    const element = document.getElementById('last-updated');
    if (element) {
        const now = new Date();
        element.textContent = `Last updated: ${formatDate(now)}`;
    }
}

// Admin Login
async function adminLogin(passcode) {
    const result = await apiRequest('admin_login', { passcode });
    if (result.success) {
        showAlert('Login successful!', 'success');
        window.location.href = 'admin.html';
    } else {
        showAlert(result.message || 'Invalid passcode', 'danger');
    }
}

// Check admin session
async function checkAdminSession() {
    const result = await apiRequest('check_admin');
    if (!result.logged_in) {
        window.location.href = 'admin-login.html';
    }
}

// Admin Logout
async function adminLogout() {
    const result = await apiRequest('admin_logout');
    if (result.success) {
        window.location.href = 'index.html';
    }
}

// Load outreach staff
async function loadStaff() {
    const result = await apiRequest('get_staff');
    if (result.success && result.staff) {
        return result.staff;
    }
    return [];
}

// Populate staff dropdown
async function populateStaffDropdown(selectId) {
    const staff = await loadStaff();
    const select = document.getElementById(selectId);
    if (select) {
        select.innerHTML = '<option value="">Select staff member...</option>';
        staff.forEach(member => {
            const option = document.createElement('option');
            option.value = member.id;
            option.textContent = `${member.first_name} ${member.last_name}`;
            select.appendChild(option);
        });
    }
}

// Check for duplicate client
async function checkDuplicate(firstName, lastName, dob = null) {
    const result = await apiRequest('check_duplicate', {
        first_name: firstName,
        last_name: lastName,
        dob: dob
    });
    return result;
}

// Load existing clients
async function loadClients() {
    const result = await apiRequest('get_clients');
    if (result.success && result.clients) {
        return result.clients;
    }
    return [];
}

// Display client list
async function displayClientList(containerId) {
    const clients = await loadClients();
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = '';
        if (clients.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #666;">No clients found</p>';
        } else {
            clients.forEach(client => {
                const clientHTML = `
                    <div class="client-item">
                        <div class="client-info">
                            <div class="client-name">${client.first_name} ${client.last_name}</div>
                            ${client.date_of_birth ? `<div class="client-dob">DOB: ${client.date_of_birth}</div>` : ''}
                            <div class="client-assessments">${client.assessment_count} assessment(s)</div>
                        </div>
                    </div>
                `;
                container.innerHTML += clientHTML;
            });
        }
    }
}

// Create new client
async function createClient(firstName, lastName, dob = null) {
    const result = await apiRequest('create_client', {
        first_name: firstName,
        last_name: lastName,
        dob: dob
    });
    return result;
}

// Save consent
async function saveConsent(clientId, fullConsent, partialConsent) {
    const result = await apiRequest('save_consent', {
        client_id: clientId,
        full_consent: fullConsent,
        partial_consent: partialConsent
    });
    return result;
}

// Start new assessment
async function startAssessment(clientId, staffId, assessmentType) {
    const result = await apiRequest('start_assessment', {
        client_id: clientId,
        staff_id: staffId,
        assessment_type: assessmentType
    });
    return result;
}

// Save assessment progress
async function saveAssessment(assessmentId, assessmentData, isComplete = false) {
    const result = await apiRequest('save_assessment', {
        assessment_id: assessmentId,
        assessment_data: assessmentData,
        is_complete: isComplete
    });
    return result;
}

// Get assessment by ID
async function getAssessment(assessmentId) {
    const result = await apiRequest('get_assessment', { assessment_id: assessmentId });
    return result;
}

// Load all assessments (admin)
async function loadAllAssessments(type = null) {
    const result = await apiRequest('get_all_assessments', { type });
    if (result.success && result.assessments) {
        return result.assessments;
    }
    return [];
}

// Display assessments table
async function displayAssessmentsTable(containerId, type = null) {
    const assessments = await loadAllAssessments(type);
    const container = document.getElementById(containerId);
    if (container) {
        if (assessments.length === 0) {
            container.innerHTML = '<p style="text-align: center; padding: 20px;">No assessments found</p>';
        } else {
            let tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Staff</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            assessments.forEach(assessment => {
                const statusBadge = assessment.is_complete 
                    ? '<span style="color: green;">✓ Complete</span>' 
                    : '<span style="color: orange;">○ In Progress</span>';
                    
                tableHTML += `
                    <tr>
                        <td>${assessment.id}</td>
                        <td>${assessment.first_name} ${assessment.last_name}</td>
                        <td>${assessment.assessment_type}</td>
                        <td>${assessment.staff_first_name} ${assessment.staff_last_name}</td>
                        <td>${new Date(assessment.created_at).toLocaleDateString()}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="viewAssessment(${assessment.id})">View</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteAssessment(${assessment.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += `
                    </tbody>
                </table>
            `;
            
            container.innerHTML = tableHTML;
        }
    }
}

// Delete assessment (admin)
async function deleteAssessment(assessmentId) {
    if (confirm('Are you sure you want to delete this assessment?')) {
        const result = await apiRequest('delete_assessment', { assessment_id: assessmentId });
        if (result.success) {
            showAlert('Assessment deleted successfully', 'success');
            location.reload();
        } else {
            showAlert(result.message || 'Error deleting assessment', 'danger');
        }
    }
}

// Load dashboard stats
async function loadDashboardStats() {
    const result = await apiRequest('get_dashboard_stats');
    if (result.success && result.stats) {
        return result.stats;
    }
    return null;
}

// Display dashboard stats
async function displayDashboardStats() {
    const stats = await loadDashboardStats();
    if (stats) {
        document.getElementById('stat-total')?.textContent = stats.total_assessments || 0;
        document.getElementById('stat-short')?.textContent = stats.short_assessments || 0;
        document.getElementById('stat-medium')?.textContent = stats.medium_assessments || 0;
        document.getElementById('stat-hard')?.textContent = stats.hard_assessments || 0;
        document.getElementById('stat-clients')?.textContent = stats.unique_clients || 0;
        document.getElementById('stat-today')?.textContent = stats.assessments_today || 0;
        document.getElementById('stat-week')?.textContent = stats.assessments_week || 0;
        document.getElementById('stat-month')?.textContent = stats.assessments_month || 0;
        document.getElementById('last-updated')?.textContent = `Last updated: ${formatDate(stats.last_updated)}`;
    }
}

// Load all widgets (admin)
async function loadAllWidgets() {
    const result = await apiRequest('get_all_widgets');
    if (result.success && result.widgets) {
        return result.widgets;
    }
    return [];
}

// Display widgets management
async function displayWidgetsManagement(containerId) {
    const widgets = await loadAllWidgets();
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = '';
        widgets.forEach(widget => {
            const widgetHTML = `
                <div class="widget">
                    <div class="widget-header">
                        <span class="widget-title">${widget.widget_name}</span>
                        <input type="checkbox" 
                               ${widget.is_visible_on_landing ? 'checked' : ''} 
                               onchange="toggleWidgetVisibility(${widget.id}, this.checked)"
                               title="Show on landing page">
                    </div>
                    <div class="widget-value">${widget.current_value}</div>
                    <div class="widget-description">${widget.widget_description}</div>
                </div>
            `;
            container.innerHTML += widgetHTML;
        });
    }
}

// Toggle widget visibility
async function toggleWidgetVisibility(widgetId, isVisible) {
    const result = await apiRequest('toggle_widget', {
        widget_id: widgetId,
        is_visible: isVisible
    });
    if (result.success) {
        showAlert('Widget visibility updated', 'success');
    } else {
        showAlert(result.message || 'Error updating widget', 'danger');
    }
}

// Assessment form handling
class AssessmentForm {
    constructor(assessmentType, assessmentId, clientId, staffId) {
        this.assessmentType = assessmentType;
        this.assessmentId = assessmentId;
        this.clientId = clientId;
        this.staffId = staffId;
        this.data = {};
        this.currentSection = 0;
        this.totalSections = 0;
    }
    
    collectFormData() {
        const formElements = document.querySelectorAll('input, select, textarea');
        formElements.forEach(element => {
            if (element.type === 'checkbox') {
                if (element.checked) {
                    if (!this.data[element.name]) {
                        this.data[element.name] = [];
                    }
                    if (Array.isArray(this.data[element.name])) {
                        this.data[element.name].push(element.value);
                    }
                }
            } else if (element.type === 'radio') {
                if (element.checked) {
                    this.data[element.name] = element.value;
                }
            } else if (element.name) {
                this.data[element.name] = element.value;
            }
        });
    }
    
    async save(isComplete = false) {
        this.collectFormData();
        const result = await saveAssessment(this.assessmentId, this.data, isComplete);
        return result;
    }
    
    updateProgress() {
        const progress = ((this.currentSection + 1) / this.totalSections) * 100;
        const progressBar = document.querySelector('.progress-fill');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }
    }
}

// Modal management
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Initialize tooltips
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.position = 'absolute';
            tooltip.style.top = `${rect.top - tooltip.offsetHeight - 5}px`;
            tooltip.style.left = `${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
}

// Auto-save functionality
function enableAutoSave(form, interval = 30000) {
    setInterval(() => {
        if (form instanceof AssessmentForm) {
            form.save(false).then(result => {
                if (result.success) {
                    console.log('Auto-saved at', new Date().toLocaleTimeString());
                }
            });
        }
    }, interval);
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'red';
            isValid = false;
        } else {
            field.style.borderColor = '';
        }
    });
    
    return isValid;
}

// Export data to CSV (admin)
function exportToCSV(data, filename) {
    const csv = convertToCSV(data);
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

function convertToCSV(data) {
    if (!data || data.length === 0) return '';
    
    const headers = Object.keys(data[0]);
    const rows = data.map(row => 
        headers.map(header => JSON.stringify(row[header] || '')).join(',')
    );
    
    return [headers.join(','), ...rows].join('\n');
}

// Initialize page based on context
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the landing page
    if (document.getElementById('total-count')) {
        loadTotalCount();
        loadLandingWidgets();
        updateLastUpdated();
        
        // Refresh every 30 seconds
        setInterval(() => {
            loadTotalCount();
            loadLandingWidgets();
            updateLastUpdated();
        }, 30000);
    }
    
    // Check if we're on admin pages
    if (document.body.classList.contains('admin-page')) {
        checkAdminSession();
    }
    
    // Initialize tooltips
    initTooltips();
});

// Make functions available globally
window.apiRequest = apiRequest;
window.showAlert = showAlert;
window.adminLogin = adminLogin;
window.adminLogout = adminLogout;
window.checkAdminSession = checkAdminSession;
window.loadStaff = loadStaff;
window.populateStaffDropdown = populateStaffDropdown;
window.checkDuplicate = checkDuplicate;
window.loadClients = loadClients;
window.displayClientList = displayClientList;
window.createClient = createClient;
window.saveConsent = saveConsent;
window.startAssessment = startAssessment;
window.saveAssessment = saveAssessment;
window.getAssessment = getAssessment;
window.loadAllAssessments = loadAllAssessments;
window.displayAssessmentsTable = displayAssessmentsTable;
window.deleteAssessment = deleteAssessment;
window.loadDashboardStats = loadDashboardStats;
window.displayDashboardStats = displayDashboardStats;
window.loadAllWidgets = loadAllWidgets;
window.displayWidgetsManagement = displayWidgetsManagement;
window.toggleWidgetVisibility = toggleWidgetVisibility;
window.AssessmentForm = AssessmentForm;
window.openModal = openModal;
window.closeModal = closeModal;
window.validateForm = validateForm;
window.exportToCSV = exportToCSV;
