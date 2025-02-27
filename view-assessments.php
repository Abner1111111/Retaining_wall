<?php
session_start();
require "back/db_configs.php";

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$query = "
    SELECT 
        wa.id,
        wa.structure_name,
        wa.date_of_construction,
        wa.date_of_inspection,
        wa.contract_id,
        wa.province,
        wa.city,
        wa.barangay,
        wa.street_address,
        wa.height,
        wa.base,
        wa.type_of_design,
        wa.type_of_material,
        ar.severity,
        ar.condition_diagnosis,
        ar.cause_of_failure,
        ar.explanation
    FROM wall_assessments wa
    LEFT JOIN assessment_results ar ON wa.id = ar.assessment_id
    WHERE wa.user_id = ?
    ORDER BY wa.date_of_inspection DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to safely handle null values
function safe_value($value, $default = 'N/A') {
    return ($value !== null && $value !== '') ? htmlspecialchars($value) : $default;
}

// Calculate age from date of construction with years, months, and days
function calculate_age($construction_date) {
    if (!$construction_date) return 'N/A';
    
    $then = new DateTime($construction_date);
    $now = new DateTime();
    $diff = $now->diff($then);
    
    $age = [];
    
    if ($diff->y > 0) {
        $age[] = $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
    }
    
    if ($diff->m > 0) {
        $age[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
    }
    
    if ($diff->d > 0 && count($age) < 2) {
        $age[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
    }
    
    return count($age) > 0 ? implode(', ', $age) : 'Less than a day';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assessments</title> 
   <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Roboto', sans-serif;
    line-height: 1.6;
    background-color: #f5f5f5;
    color: #333;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    background-color: #73877b;
    color: white;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content {
    flex-grow: 1;
}

.user-info {
    text-align: right;
    font-size: 0.9em;
}

.section {
    background: white;
    padding: 25px;
    margin-bottom: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.filter-item {
    margin-bottom: 15px;
}

.filter-item label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.filter-item select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.assessments-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.assessments-table th,
.assessments-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.assessments-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.severity-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 500;
    text-align: center;
}

.severity-high {
    background-color: #dc3545;
    color: white;
}

.severity-medium {
    background-color: #ffc107;
    color: black;
}

.severity-low {
    background-color: #28a745;
    color: white;
}

.view-button {
    background-color: #73877b;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.9em;
}

.view-button:hover {
    background-color: #5a6e62;
}


.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    overflow-y: auto;
}

.modal-content {
    position: relative;
    background-color: white;
    margin: 50px auto;
    padding: 30px;
    width: 90%;
    max-width: 800px;
    border-radius: 12px;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    animation: modalFadeIn 0.3s ease-out;
}

.modal-header {
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.modal-header h2 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin: 0;
    padding-right: 40px;
}

.close-button {
    position: absolute;
    right: 25px;
    top: 25px;
    font-size: 28px;
    color: #666;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.close-button:hover {
    background-color: #f0f0f0;
    color: #333;
}

.assessment-section {
    padding: 20px;
    margin-bottom: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #73877b;
}

.assessment-section h3 {
    color: #2c3e50;
    font-size: 1.3rem;
    margin-bottom: 15px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 15px;
}

.info-item {
    padding: 15px;
    background-color: white;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.info-item strong {
    color: #5a6e62;
    display: block;
    margin-bottom: 5px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}


.severity-indicator {
    display: inline-flex;
    align-items: center;
    padding: 8px 12px;
    border-radius: 6px;
    font-weight: 500;
    margin-top: 5px;
}

.severity-indicator.high {
    background-color: #fde8e8;
    color: #dc3545;
}

.severity-indicator.medium {
    background-color: #fff3cd;
    color: #856404;
}

.severity-indicator.low {
    background-color: #d4edda;
    color: #155724;
}

.recommendations {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    margin-top: 20px;
}

.recommendations h3 {
    color: #2c3e50;
    margin-bottom: 15px;
}

.recommendations p {
    color: #444;
    line-height: 1.6;
    white-space: pre-line;
}
@keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
            to {
            opacity: 1;
            transform: translateY(0);
            }
}

@media (max-width: 768px) {
        .modal-content {
        margin: 20px;
        padding: 20px;
        width: auto;
        }

        .info-grid {
        grid-template-columns: 1fr;
        }
}
@media (max-width: 768px) {
    .filters {
        grid-template-columns: 1fr;
    }

    .assessments-table {
        display: block;
        overflow-x: auto;
    }
}
   </style>
</head>
<body>
       
<?php
      include 'sidebar.php';
?>

    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>Assessment Records</h1>
                <p>View and manage your retaining wall assessments</p>
            </div>
            <div class="user-info">
                Welcome, <?php echo safe_value($user['first_name'] . ' ' . $user['last_name']); ?>
                <br>
             
            </div>
        </div>

        <div class="section">
            <div class="filters">
                <div class="filter-item">
                    <label for="severity-filter">Filter by Severity</label>
                    <select id="severity-filter">
                        <option value="">All Severities</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="design-filter">Filter by Design Type</label>
                    <select id="design-filter">
                        <option value="">All Designs</option>
                        <option value="Gravity retaining walls">Gravity</option>
                        <option value="Cantilever retaining walls">Cantilever</option>
                        <option value="Mechanically Stabilized Earth (MSE) Walls">MSE</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="material-filter">Filter by Material</label>
                    <select id="material-filter">
                        <option value="">All Materials</option>
                        <option value="Reinforced Concrete">Reinforced Concrete</option>
                        <option value="Stone Masonry">Stone Masonry</option>
                        <option value="Gabion">Gabion</option>
                    </select>
                </div>
            </div>

            <table class="assessments-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Structure Name</th>
                        <th>Design Type</th>
                        <th>Material</th>
                        <th>Severity</th>
                        <th>Inspection Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assessments as $assessment): ?>
                    <tr>
                        <td><?php echo safe_value($assessment['id']); ?></td>
                        <td><?php echo safe_value($assessment['structure_name']); ?></td>
                        <td><?php echo safe_value($assessment['type_of_design']); ?></td>
                        <td><?php echo safe_value($assessment['type_of_material']); ?></td>
                        <td>
                            <?php 
                            $severity = safe_value($assessment['severity'], 'Unknown');
                            $severityClass = strtolower($severity);
                            ?>
                            <span class="severity-badge severity-<?php echo $severityClass; ?>">
                                <?php echo $severity; ?>
                            </span>
                        </td>
                        <td><?php echo $assessment['date_of_inspection'] ? date('Y-m-d', strtotime($assessment['date_of_inspection'])) : 'N/A'; ?></td>
                        <td>
                            <button class="view-button" onclick="viewAssessment(<?php echo $assessment['id']; ?>)">
                                View Details
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="assessmentModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // Add the API base URL for PSGC
        const API_BASE_URL = 'https://psgc.gitlab.io/api';
        const REGION_12_CODE = '120000000';
        
        function toggleNav() {
            const sidenav = document.getElementById("mySidenav");
            sidenav.classList.toggle("active");
        }
        
        function filterTable() {
            const severityFilter = document.getElementById('severity-filter').value.toLowerCase();
            const designFilter = document.getElementById('design-filter').value;
            const materialFilter = document.getElementById('material-filter').value;
            
            const rows = document.querySelectorAll('.assessments-table tbody tr');
            
            rows.forEach(row => {
                const severityElement = row.querySelector('.severity-badge');
                const severity = severityElement ? severityElement.textContent.toLowerCase() : '';
                const design = row.cells[2].textContent;
                const material = row.cells[3].textContent;
                
                const severityMatch = !severityFilter || severity.includes(severityFilter);
                const designMatch = !designFilter || design === designFilter;
                const materialMatch = !materialFilter || material === materialFilter;
                
                row.style.display = severityMatch && designMatch && materialMatch ? '' : 'none';
            });
        }

        document.getElementById('severity-filter').addEventListener('change', filterTable);
        document.getElementById('design-filter').addEventListener('change', filterTable);
        document.getElementById('material-filter').addEventListener('change', filterTable);
        
        // Function to fetch location name by code
        async function getLocationNameByCode(type, code) {
            if (!code) return 'N/A';
            
            try {
                const response = await fetch(`${API_BASE_URL}/${type}/${code}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                return data.name;
            } catch (error) {
                console.error(`Error fetching ${type} data:`, error);
                return code; // Return the code if we couldn't get the name
            }
        }

        // Calculate age from construction date with years, months, and days
        function calculateDetailedAge(constructionDateString) {
            if (!constructionDateString) return 'N/A';
            
            const constructionDate = new Date(constructionDateString);
            const today = new Date();
            
            // Calculate difference in milliseconds
            const diffTime = Math.abs(today - constructionDate);
            
            // Create a Date object for date calculations
            const diffDate = new Date(diffTime);
            
            // Calculate years (accounting for JavaScript's date epoch starting in 1970)
            const years = diffDate.getUTCFullYear() - 1970;
            
            // Calculate months
            const months = diffDate.getUTCMonth();
            
            // Calculate days (subtract 1 because getUTCDate() starts from 1)
            const days = diffDate.getUTCDate() - 1;
            
            // Build the age string
            const age = [];
            
            if (years > 0) {
                age.push(years + ' year' + (years > 1 ? 's' : ''));
            }
            
            if (months > 0) {
                age.push(months + ' month' + (months > 1 ? 's' : ''));
            }
            
            if (days > 0 && age.length < 2) {
                age.push(days + ' day' + (days > 1 ? 's' : ''));
            }
            
            return age.length > 0 ? age.join(', ') : 'Less than a day';
        }

        async function viewAssessment(id) {
            try {
                const response = await fetch(`get-assessment.php?id=${id}`);
                const data = await response.json();
                
                // Make sure we're accessing the data property
                const assessmentData = data.data || data;
                
                const modal = document.getElementById('assessmentModal');
                const modalContent = document.getElementById('modalContent');
                
                const severityClass = assessmentData.severity ? assessmentData.severity.toLowerCase() : 'unknown';
                
                // Calculate detailed age from construction date
                let age = 'N/A';
                if (assessmentData.date_of_construction) {
                    age = calculateDetailedAge(assessmentData.date_of_construction);
                }
                
                // Format visual indicators if available
                let visualIndicators = '<p>No visual indicators recorded</p>';
                if (assessmentData.visual_indicators && assessmentData.visual_indicators.length > 0) {
                    visualIndicators = '<ul>';
                    assessmentData.visual_indicators.forEach(indicator => {
                        visualIndicators += `<li>${indicator}</li>`;
                    });
                    visualIndicators += '</ul>';
                }
                
                // Format failure types if available
                let failureTypes = '<p>No failure types recorded</p>';
                if (assessmentData.failure_types && assessmentData.failure_types.length > 0) {
                    failureTypes = '<ul>';
                    
                    // Handle both array and string formats
                    const failureTypesArray = Array.isArray(assessmentData.failure_types) 
                        ? assessmentData.failure_types 
                        : JSON.parse(assessmentData.failure_types);
                        
                    failureTypesArray.forEach(type => {
                        failureTypes += `<li>${type}</li>`;
                    });
                    failureTypes += '</ul>';
                }
                
                // First display modal with placeholder for location
                modalContent.innerHTML = `
                    <div class="modal-header">
                        <h2>Assessment Details</h2>
                    </div>
                    
                    <div class="assessment-section">
                        <h3>Basic Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Structure Name</strong>
                                ${assessmentData.structure_name || 'N/A'}
                            </div>
                            <div class="info-item">
                                <strong>Contract ID</strong>
                                ${assessmentData.contract_id || 'N/A'}
                            </div>
                            <div class="info-item">
                                <strong>Construction Date</strong>
                                ${assessmentData.date_of_construction ? new Date(assessmentData.date_of_construction).toLocaleDateString() : 'N/A'}
                            </div>
                            <div class="info-item">
                                <strong>Age</strong>
                                ${age}
                            </div>
                            <div class="info-item">
                                <strong>Inspection Date</strong>
                                ${assessmentData.date_of_inspection ? new Date(assessmentData.date_of_inspection).toLocaleDateString() : 'N/A'}
                            </div>
                            <div class="info-item" id="location-info">
                                <strong>Location</strong>
                                <span id="location-placeholder">Loading location data...</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="assessment-section">
                        <h3>Structure Details</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Type of Design</strong>
                                ${assessmentData.type_of_design || 'N/A'}
                            </div>
                            <div class="info-item">
                                <strong>Material</strong>
                                ${assessmentData.type_of_material || 'N/A'}
                            </div>
                            <div class="info-item">
                                <strong>Height</strong>
                                ${assessmentData.height ? assessmentData.height + ' m' : 'N/A'}
                            </div>
                            <div class="info-item">
                                <strong>Base Width</strong>
                                ${assessmentData.base ? assessmentData.base + ' m' : 'N/A'}
                            </div>
                        </div>
                    </div>
                    
                    <div class="assessment-section">
                        <h3>Visual Indicators</h3>
                        ${visualIndicators}
                    </div>
                    
                    <div class="assessment-section">
                        <h3>Assessment Results</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Severity Level</strong>
                                <div class="severity-indicator ${severityClass}">
                                    ${assessmentData.severity || 'Unknown'}
                                </div>
                            </div>
                           
                            <div class="info-item">
                                <strong>Condition Diagnosis</strong>
                                ${assessmentData.condition_diagnosis || 'N/A'}
                            </div>
                        </div>
                        
                        <div class="info-item" style="margin-top: 20px;">
                            <strong>Failure Types</strong>
                            ${failureTypes}
                        </div>
                          <br>
                        <div class="info-item">
                            <strong>Cause of Failure</strong>
                            <p>${assessmentData.cause_of_failure || 'Not specified'}</p>
                        </div>
                          <br>
                        <div class="info-item">
                            <strong>Explanation</strong>
                            <p>${assessmentData.explanation || 'No detailed explanation available'}</p>
                        </div>
                    </div>
                `;
                
                // Show the modal immediately
                modal.style.display = 'block';
                
                // Asynchronously fetch location data and update the placeholder
                const locationPlaceholder = document.getElementById('location-placeholder');
                
                // Get actual location names
                let provinceName = 'N/A';
                let cityName = 'N/A';
                let barangayName = 'N/A';
                
                if (assessmentData.province) {
                    provinceName = await getLocationNameByCode('provinces', assessmentData.province);
                }
                
                if (assessmentData.city) {
                    cityName = await getLocationNameByCode('cities-municipalities', assessmentData.city);
                }
                
                if (assessmentData.barangay) {
                    barangayName = await getLocationNameByCode('barangays', assessmentData.barangay);
                }
                
                // Create formatted location string
                const locationString = [
                    assessmentData.street_address, 
                    barangayName, 
                    cityName, 
                    provinceName
                ].filter(item => item && item !== 'N/A').join(', ');
                
                // Update the location placeholder with the actual location
                if (locationPlaceholder) {
                    locationPlaceholder.textContent = locationString || 'N/A';
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading assessment details. Please try again.');
            }
        }
        
        function closeModal() {
            document.getElementById('assessmentModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('assessmentModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>