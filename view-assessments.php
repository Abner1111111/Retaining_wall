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
        a.id,
        a.name,
        a.age,
        a.external_factor,
        a.issues_observed,
        a.type_of_design,
        a.type_of_material,
        a.created_at,
        ar.severity_level,
        ar.condition_diagnosis,
        ar.recommendations
    FROM assessments a
    LEFT JOIN assessment_results ar ON a.id = ar.assessment_id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to safely handle null values
function safe_value($value, $default = 'N/A') {
    return ($value !== null && $value !== '') ? htmlspecialchars($value) : $default;
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
                        <th>Name</th>
                        <th>Design Type</th>
                        <th>Material</th>
                        <th>Severity</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assessments as $assessment): ?>
                    <tr>
                        <td><?php echo safe_value($assessment['id']); ?></td>
                        <td><?php echo safe_value($assessment['name']); ?></td>
                        <td><?php echo safe_value($assessment['type_of_design']); ?></td>
                        <td><?php echo safe_value($assessment['type_of_material']); ?></td>
                        <td>
                            <?php 
                            $severity = safe_value($assessment['severity_level'], 'Unknown');
                            $severityClass = strtolower($severity);
                            ?>
                            <span class="severity-badge severity-<?php echo $severityClass; ?>">
                                <?php echo $severity; ?>
                            </span>
                        </td>
                        <td><?php echo $assessment['created_at'] ? date('Y-m-d', strtotime($assessment['created_at'])) : 'N/A'; ?></td>
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

        function viewAssessment(id) {
    fetch(`get-assessment.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('assessmentModal');
            const modalContent = document.getElementById('modalContent');
            
            const severityClass = data.severity_level ? data.severity_level.toLowerCase() : 'unknown';
            
            modalContent.innerHTML = `
                <div class="modal-header">
                    <h2>Assessment Details</h2>
                </div>
                
                <div class="assessment-section">
                    <h3>Basic Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Structure Name</strong>
                            ${data.name || 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Age</strong>
                            ${data.age ? data.age + ' years' : 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Type of Design</strong>
                            ${data.type_of_design || 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Material</strong>
                            ${data.type_of_material || 'N/A'}
                        </div>
                    </div>
                </div>
                
                <div class="assessment-section">
                    <h3>Assessment Results</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Severity Level</strong>
                            <div class="severity-indicator ${severityClass}">
                                ${data.severity_level || 'Unknown'}
                            </div>
                        </div>
                        <div class="info-item">
                            <strong>Condition</strong>
                            ${data.condition_diagnosis || 'N/A'}
                        </div>
                    </div>
                    <div class="info-item" style="margin-top: 20px;">
                        <strong>Issues Observed</strong>
                        ${data.issues_observed || 'No issues recorded'}
                    </div>
                </div>
                
                <div class="recommendations">
                    <h3>Recommendations</h3>
                    <p>${data.recommendations || 'No recommendations available'}</p>
                </div>
            `;
            
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading assessment details. Please try again.');
        });
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