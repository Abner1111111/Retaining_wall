<?php
session_start();
require "back/db_configs.php";
require "includes/validate_session.php";

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

function safe_value($value, $default = 'N/A') {
    return ($value !== null && $value !== '') ? htmlspecialchars($value) : $default;
}


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
  <link rel="stylesheet" href="Css/view-assessment.css">
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
                        <button class="print-button" onclick="printAssessment(<?php echo $assessment['id']; ?>)">
                            <i class="fas fa-print"></i> Print
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

    <script src="js/view-assessment.js"></script>
    <script src="js/print-assessment.js"></script>
    <script src="js/calculateDetailedAge-view-assessment.js"></script>
    <script src="js/get-location-view-assesmment.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>