<?php
session_start();
require "back/db_configs.php";


if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing assessment ID']);
    exit();
}

$assessmentId = (int)$_GET['id'];
$userId = (int)$_SESSION['user_id'];

try {

    $query = "
        SELECT 
            wa.*
        FROM wall_assessments wa
        WHERE wa.id = ? AND wa.user_id = ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$assessmentId, $userId]);
    $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$assessment) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Assessment not found']);
        exit();
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            failure_types, 
            cause_of_failure, 
            condition_diagnosis, 
            severity, 
            explanation
        FROM assessment_results 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($results) {
        $assessment = array_merge($assessment, $results);
        
        if (isset($assessment['failure_types']) && is_string($assessment['failure_types'])) {
            $assessment['failure_types'] = json_decode($assessment['failure_types'], true) ?? [];
        }
    }
    
    $stmt = $pdo->prepare("
        SELECT condition_type, test_result 
        FROM in_situ_conditions 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    $assessment['in_situ_conditions'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    

    $stmt = $pdo->prepare("
        SELECT analysis_type, test_result 
        FROM structural_analysis 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    $assessment['structural_analysis'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

    $stmt = $pdo->prepare("
        SELECT indicator 
        FROM visual_indicators 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    $assessment['visual_indicators'] = $stmt->fetchAll(PDO::FETCH_COLUMN) ?? [];

    $stmt = $pdo->prepare("
        SELECT method 
        FROM analysis_methods 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    $assessment['analysis_methods'] = $stmt->fetchAll(PDO::FETCH_COLUMN) ?? [];

    $stmt = $pdo->prepare("
        SELECT test_name, created_at
        FROM recommended_lab_tests
        WHERE assessment_id = ?
        ORDER BY created_at
    ");
    $stmt->execute([$assessmentId]);
    $assessment['lab_tests'] = $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];

    echo json_encode([
        'status' => 'success',
        'data' => $assessment
    ]);
    
} catch (PDOException $e) {
    error_log('Database error retrieving assessment: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'A database error occurred'
    ]);
} catch (Exception $e) {
    error_log('Error retrieving assessment: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'An error occurred while processing your request'
    ]);
}
?>