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

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM wall_assessments WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalAssessments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->prepare("
    SELECT 
        COALESCE(ar.severity, 'Unknown') as severity, 
        COUNT(*) as count 
    FROM wall_assessments wa 
    LEFT JOIN assessment_results ar ON wa.id = ar.assessment_id 
    WHERE wa.user_id = ? 
    GROUP BY COALESCE(ar.severity, 'Unknown')
");
$stmt->execute([$_SESSION['user_id']]);
$severityCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$criticalCount = 0;
$highCount = 0;
$mediumCount = 0;
$lowCount = 0;
$unknownCount = 0;

foreach ($severityCounts as $count) {
    switch ($count['severity']) {
        case 'Critical':
            $criticalCount = $count['count'];
            break;
        case 'High':
            $highCount = $count['count'];
            break;
        case 'Medium':
            $mediumCount = $count['count'];
            break;
        case 'Low':
            $lowCount = $count['count'];
            break;
        case 'Unknown': 
        case null:
            $unknownCount = $count['count'];
            break;
    }
}

$stmt = $pdo->prepare("
    SELECT 
        wa.id,
        wa.structure_name,
        wa.date_of_inspection,
        wa.type_of_design,
        wa.type_of_material,
        ar.severity
    FROM wall_assessments wa
    LEFT JOIN assessment_results ar ON wa.id = ar.assessment_id
    WHERE wa.user_id = ?
    ORDER BY wa.date_of_inspection DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recentAssessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT 
        type_of_design,
        COUNT(*) as count
    FROM wall_assessments
    WHERE user_id = ?
    GROUP BY type_of_design
");
$stmt->execute([$_SESSION['user_id']]);
$designTypeCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT 
        type_of_material,
        COUNT(*) as count
    FROM wall_assessments
    WHERE user_id = ?
    GROUP BY type_of_material
");
$stmt->execute([$_SESSION['user_id']]);
$materialTypeCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);


function safe_value($value, $default = 'N/A') {
    return ($value !== null && $value !== '') ? htmlspecialchars($value) : $default;
}


$stmt = $pdo->prepare("
    SELECT 
        MIN(date_of_inspection) as oldest,
        MAX(date_of_inspection) as newest
    FROM wall_assessments
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$dateRange = $stmt->fetch(PDO::FETCH_ASSOC);
$oldestDate = $dateRange['oldest'] ? date('M d, Y', strtotime($dateRange['oldest'])) : 'N/A';
$newestDate = $dateRange['newest'] ? date('M d, Y', strtotime($dateRange['newest'])) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

        /* Modified dashboard top grid to have two columns */
        .top-dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            height: 100%;
        }

        .card-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.2rem;
            color: #2c3e50;
            margin: 0;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #73877b;
            font-size: 20px;
        }

        .big-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .card-footer {
            margin-top: 15px;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .progress-container {
            margin-top: 20px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .progress-bar {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-fill {
            height: 100%;
            border-radius: 5px;
        }

        .progress-fill.high {
            background-color: #dc3545;
        }

        .progress-fill.medium {
            background-color: #ffc107;
        }

        .progress-fill.low {
            background-color: #28a745;
        }

        .severity-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .severity-item {
            text-align: center;
            flex-grow: 1;
        }

        .severity-number {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .severity-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 5px;
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

        .section {
            background: white;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 1.4rem;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}


        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }

        .recent-table th,
        .recent-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .recent-table th {
            background-color: #f8f9fa;
            font-weight: 600;
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

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .summary-item {
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .summary-title {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .summary-value {
            color: #2c3e50;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .progress-fill.unknown {
    background-color: #6c757d;
}

.severity-unknown {
    background-color: #6c757d;
    color: white;
}

        @media (max-width: 992px) {
            .top-dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .recent-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>Dashboard</h1>
                <p>Overview of your retaining wall assessments</p>
            </div>
            <div class="user-info">
                Welcome, <?php echo safe_value($user['first_name'] . ' ' . $user['last_name']); ?>
                <br>
                <small><?php echo date('F d, Y'); ?></small>
            </div>
        </div>

        <div class="top-dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Assessment Summary</h2>
                    <div class="card-icon">📋</div>
                </div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-title">Total Assessments</div>
                        <div class="summary-value"><?php echo $totalAssessments; ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-title">High Risk Structures</div>
                        <div class="summary-value"><?php echo $highCount; ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-title">Medium Risk Structures</div>
                        <div class="summary-value"><?php echo $mediumCount; ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-title">Low Risk Structures</div>
                        <div class="summary-value"><?php echo $lowCount; ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-title">First Assessment</div>
                        <div class="summary-value"><?php echo $oldestDate; ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-title">Latest Assessment</div>
                        <div class="summary-value"><?php echo $newestDate; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Severity Breakdown</h2>
                    <div class="card-icon">⚠️</div>
                </div>
                <div class="severity-stats">
                    <div class="severity-item">
                        <div class="severity-number"><?php echo $highCount; ?></div>
                        <span class="severity-badge severity-high">High</span>
                    </div>
                    <div class="severity-item">
                        <div class="severity-number"><?php echo $mediumCount; ?></div>
                        <span class="severity-badge severity-medium">Medium</span>
                    </div>
                    <div class="severity-item">
                        <div class="severity-number"><?php echo $lowCount; ?></div>
                        <span class="severity-badge severity-low">Low</span>
                    </div>
                    <?php if ($unknownCount > 0): ?>
                    <div class="severity-item">
                        <div class="severity-number"><?php echo $unknownCount; ?></div>
                        <span class="severity-badge severity-unknown">Unknown</span>
                    </div>
                    <?php endif; ?>
                </div>
                 <div class="progress-container">
                <?php if ($totalAssessments > 0): ?>
    <!-- High severity progress -->
    <div class="progress-label">
        <span>High</span>
        <span><?php echo round(($highCount / $totalAssessments) * 100); ?>%</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill high" style="width: <?php echo ($highCount / $totalAssessments) * 100; ?>%"></div>
    </div>

    <!-- Medium severity progress -->
    <div class="progress-label">
        <span>Medium</span>
        <span><?php echo round(($mediumCount / $totalAssessments) * 100); ?>%</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill medium" style="width: <?php echo ($mediumCount / $totalAssessments) * 100; ?>%"></div>
    </div>

    <!-- Low severity progress -->
    <div class="progress-label">
        <span>Low</span>
        <span><?php echo round(($lowCount / $totalAssessments) * 100); ?>%</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill low" style="width: <?php echo ($lowCount / $totalAssessments) * 100; ?>%"></div>
    </div>

    <!-- Unknown severity progress -->
    <?php if ($unknownCount > 0): ?>
    <div class="progress-label">
        <span>Unknown</span>
        <span><?php echo round(($unknownCount / $totalAssessments) * 100); ?>%</span>
    </div>
    <div class="progress-bar">
        <div class="progress-fill unknown" style="width: <?php echo ($unknownCount / $totalAssessments) * 100; ?>%"></div>
    </div>
    <?php endif; ?>
<?php else: ?>
    <p>No severity data available</p>
<?php endif; ?>
                </div>
            </div>
        </div>

        <div class="section">
        <div class="section-header">
    <h2 class="section-title">Recent Assessments</h2>
    <a href="view-assessments.php" class="view-button">View All Assessments</a>
</div>

<?php if (count($recentAssessments) > 0): ?>
    <table class="recent-table">
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
            <?php foreach ($recentAssessments as $assessment): ?>
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
<?php else: ?>
    <p>No recent assessments found</p>
<?php endif; ?>

    </div>

    <div id="assessmentModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        const API_BASE_URL = 'https://psgc.gitlab.io/api';
        
        function toggleNav() {
            const sidenav = document.getElementById("mySidenav");
            sidenav.classList.toggle("active");
        }
        
        async function viewAssessment(id) {
            try {
                const response = await fetch(`get-assessment.php?id=${id}`);
                const data = await response.json();
            
                const assessmentData = data.data || data;
                
                const modal = document.getElementById('assessmentModal');
                const modalContent = document.getElementById('modalContent');
                
                const severityClass = assessmentData.severity ? assessmentData.severity.toLowerCase() : 'unknown';
                
                let age = 'N/A';
                if (assessmentData.date_of_construction) {
                    age = calculateDetailedAge(assessmentData.date_of_construction);
                }
                
       
                let visualIndicators = '<p>No visual indicators recorded</p>';
                if (assessmentData.visual_indicators && assessmentData.visual_indicators.length > 0) {
                    visualIndicators = '<ul>';
                    assessmentData.visual_indicators.forEach(indicator => {
                        visualIndicators += `<li>${indicator}</li>`;
                    });
                    visualIndicators += '</ul>';
                }

                let failureTypes = '<p>No failure types recorded</p>';
                if (assessmentData.failure_types && assessmentData.failure_types.length > 0) {
                    failureTypes = '<ul>';
                    
                    const failureTypesArray = Array.isArray(assessmentData.failure_types) 
                        ? assessmentData.failure_types 
                        : JSON.parse(assessmentData.failure_types);
                        
                    failureTypesArray.forEach(type => {
                        failureTypes += `<li>${type}</li>`;
                    });
                    failureTypes += '</ul>';
                }
                
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

                modal.style.display = 'block';

                const locationPlaceholder = document.getElementById('location-placeholder');

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

                const locationString = [
                    assessmentData.street_address, 
                    barangayName, 
                    cityName, 
                    provinceName
                    ].filter(item => item && item !== 'N/A').join(', ');
                locationPlaceholder.innerHTML = locationString || 'No location data available';
            } catch (error) {
                console.error('Error fetching assessment details:', error);
                alert('Failed to load assessment details. Please try again.');
            }
        }

        function closeModal() {
            const modal = document.getElementById('assessmentModal');
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('assessmentModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        function calculateDetailedAge(constructionDateString) {
            if (!constructionDateString) return 'N/A';
            
            const constructionDate = new Date(constructionDateString);
            const today = new Date();
            const diffTime = Math.abs(today - constructionDate);
            const diffDate = new Date(diffTime);
            const years = diffDate.getUTCFullYear() - 1970;
        
            const months = diffDate.getUTCMonth();
            const days = diffDate.getUTCDate() - 1;
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
                return code; 
            }
        }
    </script>

    <style>
     
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .assessment-section {
            margin-bottom: 25px;
        }

        .assessment-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            margin-bottom: 10px;
        }

        .info-item strong {
            display: block;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .severity-indicator {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 500;
        }

        .severity-indicator.high {
            background-color: #dc3545;
            color: white;
        }

        .severity-indicator.medium {
            background-color: #ffc107;
            color: black;
        }

        .severity-indicator.low {
            background-color: #28a745;
            color: white;
        }

        .severity-indicator.unknown {
            background-color: #6c757d;
            color: white;
        }
    </style>
</body>
</html>