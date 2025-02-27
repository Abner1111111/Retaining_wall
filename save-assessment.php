<?php
session_start();
require "back/db_configs.php";

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Verify CSRF token
if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid form submission']);
    exit();
}

// Create a new token for next submission
$_SESSION['form_token'] = bin2hex(random_bytes(32));

// Function to sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Basic assessment data
        $user_id = $_SESSION['user_id'];
        $date_of_inspection = sanitize($_POST['Date']);
        $date_of_construction = sanitize($_POST['ConstructionDate'] . '-01'); // Add day to make a complete date
        $structure_name = isset($_POST['Name']) ? sanitize($_POST['Name']) : null;
        $contract_id = sanitize($_POST['ContractID']);
        $province = sanitize($_POST['province']);
        $city = sanitize($_POST['city']);
        $barangay = sanitize($_POST['barangay']);
        $street_address = sanitize($_POST['street_address']);
        $height = floatval($_POST['Height']);
        $base = floatval($_POST['Base']);
        $type_of_design = sanitize($_POST['Type_of_Design']);
        $type_of_material = sanitize($_POST['Type_of_Material']);
        
        // Insert main assessment data
        $stmt = $pdo->prepare("INSERT INTO wall_assessments 
            (user_id, date_of_inspection, date_of_construction, structure_name, 
            contract_id, province, city, barangay, street_address, height, base, 
            type_of_design, type_of_material) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $user_id, $date_of_inspection, $date_of_construction, $structure_name, 
            $contract_id, $province, $city, $barangay, $street_address, $height, $base, 
            $type_of_design, $type_of_material
        ]);
        
        $assessment_id = $pdo->lastInsertId();
        
        // In-situ conditions
        if (isset($_POST['in_situ_conditions']) && isset($_POST['in_situ_values'])) {
            $in_situ_conditions = $_POST['in_situ_conditions'];
            $in_situ_values = $_POST['in_situ_values'];
            
            for ($i = 0; $i < count($in_situ_conditions); $i++) {
                if (!empty($in_situ_conditions[$i]) && !empty($in_situ_values[$i])) {
                    $stmt = $pdo->prepare("INSERT INTO in_situ_conditions (assessment_id, condition_type, test_result) VALUES (?, ?, ?)");
                    $stmt->execute([$assessment_id, sanitize($in_situ_conditions[$i]), sanitize($in_situ_values[$i])]);
                }
            }
        }
        
        // Structural analysis
        if (isset($_POST['structural_analysis']) && isset($_POST['structural_analysis_value'])) {
            $structural_analysis = $_POST['structural_analysis'];
            $structural_analysis_values = $_POST['structural_analysis_value'];
            
            for ($i = 0; $i < count($structural_analysis); $i++) {
                if (!empty($structural_analysis[$i]) && !empty($structural_analysis_values[$i])) {
                    $stmt = $pdo->prepare("INSERT INTO structural_analysis (assessment_id, analysis_type, test_result) VALUES (?, ?, ?)");
                    $stmt->execute([$assessment_id, sanitize($structural_analysis[$i]), sanitize($structural_analysis_values[$i])]);
                }
            }
        }
        
        // Visual indicators
        if (isset($_POST['test'])) {
            $visual_indicators = $_POST['test'];
            
            foreach ($visual_indicators as $indicator) {
                $stmt = $pdo->prepare("INSERT INTO visual_indicators (assessment_id, indicator) VALUES (?, ?)");
                $stmt->execute([$assessment_id, sanitize($indicator)]);
            }
        }
        
        // Analysis methods
        if (isset($_POST['analysis'])) {
            $analysis_methods = $_POST['analysis'];
            
            foreach ($analysis_methods as $method) {
                $stmt = $pdo->prepare("INSERT INTO analysis_methods (assessment_id, method) VALUES (?, ?)");
                $stmt->execute([$assessment_id, sanitize($method)]);
            }
        }
        
        // Process results if they were submitted
        if (isset($_POST['failureTypes']) && isset($_POST['causeOfFailure'])) {
            $failure_types = $_POST['failureTypes'];
            $cause_of_failure = sanitize($_POST['causeOfFailure']);
            $condition_diagnosis = sanitize($_POST['conditionDiagnosis']);
            $severity = sanitize($_POST['severity']);
            $explanation = sanitize($_POST['explanation']);
            
            // Convert failureTypes to JSON if it's an array
            if (is_array($failure_types)) {
                $failure_types = json_encode($failure_types);
            }
            
            $stmt = $pdo->prepare("INSERT INTO assessment_results 
                (assessment_id, failure_types, cause_of_failure, condition_diagnosis, severity, explanation) 
                VALUES (?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $assessment_id, 
                $failure_types, 
                $cause_of_failure, 
                $condition_diagnosis, 
                $severity, 
                $explanation
            ]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Return success response
        echo json_encode([
            'status' => 'success', 
            'message' => 'Assessment saved successfully',
            'assessment_id' => $assessment_id
        ]);
        
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log error
        error_log('Database Error: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Database error occurred: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Log error
        error_log('General Error: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    // Not a POST request
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}