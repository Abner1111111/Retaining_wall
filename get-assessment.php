<?php
session_start();
require "back/db_configs.php";

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No assessment ID provided']);
    exit();
}

try {
    // Get main assessment data
    $query = "
        SELECT 
            wa.*
        FROM wall_assessments wa
        WHERE wa.id = ? AND wa.user_id = ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$assessment) {
        throw new Exception('Assessment not found');
    }
    
    // Get assessment results
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
    $stmt->execute([$_GET['id']]);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($results) {
        // Add results to assessment data
        $assessment = array_merge($assessment, $results);
        
        // Convert JSON string back to array if needed
        if (isset($assessment['failure_types']) && is_string($assessment['failure_types'])) {
            $assessment['failure_types'] = json_decode($assessment['failure_types'], true);
        }
    }
    
    // Get in-situ conditions
    $stmt = $pdo->prepare("
        SELECT condition_type, test_result 
        FROM in_situ_conditions 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $assessment['in_situ_conditions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get structural analysis
    $stmt = $pdo->prepare("
        SELECT analysis_type, test_result 
        FROM structural_analysis 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $assessment['structural_analysis'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get visual indicators
    $stmt = $pdo->prepare("
        SELECT indicator 
        FROM visual_indicators 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $assessment['visual_indicators'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get analysis methods
    $stmt = $pdo->prepare("
        SELECT method 
        FROM analysis_methods 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $assessment['analysis_methods'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Return success response
    echo json_encode([
        'status' => 'success',
        'data' => $assessment
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Error retrieving assessment: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
