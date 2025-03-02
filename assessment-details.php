<?php
session_start();
require "back/db_configs.php";

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view-assessment.php");
    exit();
}

$assessment_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM wall_assessments WHERE id = ? AND user_id = ?");
$stmt->execute([$assessment_id, $_SESSION['user_id']]);
$assessment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assessment) {
    header("Location: view-assessment.php");
    exit();
}
$stmt = $pdo->prepare("SELECT * FROM in_situ_conditions WHERE assessment_id = ?");
$stmt->execute([$assessment_id]);
$in_situ_conditions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM structural_analysis WHERE assessment_id = ?");
$stmt->execute([$assessment_id]);
$structural_analysis = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM visual_indicators WHERE assessment_id = ?");
$stmt->execute([$assessment_id]);
$visual_indicators = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM analysis_methods WHERE assessment_id = ?");
$stmt->execute([$assessment_id]);
$analysis_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT * FROM assessment_results WHERE assessment_id = ?");
$stmt->execute([$assessment_id]);
$assessment_results = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Details - Retaining Wall Assessment Tool</title>
    <link rel="stylesheet" href="Css/Questionnaire.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="container">
        <div class="header">
            <div class="user-info">
                Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
            </div>
            <h1 style="color: #f5f5f5;">Assessment Details</h1>
            <p>Detailed information for assessment ID: <?php echo htmlspecialchars($assessment_id); ?></p>
        </div>

        <div class="section">
            <h2>Basic Information</h2>
            <p><strong>Date of Inspection:</strong> <?php echo htmlspecialchars($assessment['date_of_inspection']); ?></p>
            <p><strong>Structure Name:</strong> <?php echo htmlspecialchars($assessment['structure_name'] ?? 'N/A'); ?></p>
            <p><strong>Contract ID:</strong> <?php echo htmlspecialchars($assessment['contract_id']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($assessment['street_address'] . ', ' . $assessment['barangay'] . ', ' . $assessment['city'] . ', ' . $assessment['province']); ?></p>
            <p><strong>Height:</strong> <?php echo htmlspecialchars($assessment['height']); ?> meters</p>
            <p><strong>Base:</strong> <?php echo htmlspecialchars($assessment['base']); ?> meters</p>
            <p><strong>Type of Design:</strong> <?php echo htmlspecialchars($assessment['type_of_design']); ?></p>
            <p><strong>Type of Material:</strong> <?php echo htmlspecialchars($assessment['type_of_material']); ?></p>
        </div>

        <div class="section">
            <h2>In-Situ Conditions</h2>
            <?php if (empty($in_situ_conditions)): ?>
                <p>No in-situ conditions recorded.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($in_situ_conditions as $condition): ?>
                        <li><?php echo htmlspecialchars($condition['condition_type']); ?>: <?php echo htmlspecialchars($condition['test_result']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Structural Analysis</h2>
            <?php if (empty($structural_analysis)): ?>
                <p>No structural analysis recorded.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($structural_analysis as $analysis): ?>
                        <li><?php echo htmlspecialchars($analysis['analysis_type']); ?>: <?php echo htmlspecialchars($analysis['test_result']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Visual Indicators</h2>
            <?php if (empty($visual_indicators)): ?>
                <p>No visual indicators recorded.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($visual_indicators as $indicator): ?>
                        <li><?php echo htmlspecialchars($indicator['indicator']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Analysis Methods</h2>
            <?php if (empty($analysis_methods)): ?>
                <p>No analysis methods recorded.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($analysis_methods as $method): ?>
                        <li><?php echo htmlspecialchars($method['method']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Assessment Results</h2>
            <?php if (empty($assessment_results)): ?>
                <p>No results recorded.</p>
            <?php else: ?>
                <p><strong>Failure Types:</strong> <?php echo htmlspecialchars($assessment_results['failure_types']); ?></p>
                <p><strong>Cause of Failure:</strong> <?php echo htmlspecialchars($assessment_results['cause_of_failure']); ?></p>
                <p><strong>Condition Diagnosis:</strong> <?php echo htmlspecialchars($assessment_results['condition_diagnosis']); ?></p>
                <p><strong>Severity:</strong> <?php echo htmlspecialchars($assessment_results['severity']); ?></p>
                <p><strong>Explanation:</strong> <?php echo htmlspecialchars($assessment_results['explanation']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>