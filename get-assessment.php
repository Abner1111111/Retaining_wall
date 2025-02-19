<?php
session_start();
require "back/db_configs.php";

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No assessment ID provided']);
    exit();
}

try {
    $query = "
        SELECT 
            a.*,
            ar.severity_level,
            ar.condition_diagnosis,
            ar.issues_summary,
            ar.failure_types,
            ar.recommended_tests,
            ar.recommendations
        FROM assessments a
        LEFT JOIN assessment_results ar ON a.id = ar.assessment_id
        WHERE a.id = ? AND a.user_id = ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$assessment) {
        throw new Exception('Assessment not found');
    }
    
    // Get visual indicators
    $stmt = $pdo->prepare("SELECT indicator_type FROM visual_indicators WHERE assessment_id = ?");
    $stmt->execute([$_GET['id']]);
    $assessment['visual_indicators'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get analysis methods
    $stmt = $pdo->prepare("SELECT method_type FROM analysis_methods WHERE assessment_id = ?");
    $stmt->execute([$_GET['id']]);
    $assessment['analysis_methods'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    header('Content-Type: application/json');
    echo json_encode($assessment);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>