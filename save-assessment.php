<?php
session_start();
require "back/db_configs.php";

// Check if the user is authenticated
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("Location: index.php");
    exit();
}

// Validate form token to prevent CSRF attacks
if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
    die("Invalid form submission.");
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Basic information
        $user_id = $_SESSION['user_id'];
        $date = $_POST['Date'] ?? null;
        $construction_date = $_POST['ConstructionDate'] ?? null;
        $name = $_POST['Name'] ?? null;
        $contract_id = $_POST['ContractID'] ?? null;
        $province = $_POST['province'] ?? null;
        $city = $_POST['city'] ?? null;
        $barangay = $_POST['barangay'] ?? null;
        $street_address = $_POST['street_address'] ?? null;
        
        // Structural dimensions
        $height = $_POST['Height'] ?? null;
        $base = $_POST['Base'] ?? null;
        $type_of_design = $_POST['Type_of_Design'] ?? null;
        $type_of_material = $_POST['Type_of_Material'] ?? null;
        
        // Visual indicators (checkbox values)
        $visual_indicators = isset($_POST['test']) ? implode(',', $_POST['test']) : '';
        
        // Analysis methods (checkbox values)
        $analysis_methods = isset($_POST['analysis']) ? implode(',', $_POST['analysis']) : '';
        
        // In-Situ Conditions (multiple values)
        $in_situ_conditions = isset($_POST['in_situ_conditions']) ? $_POST['in_situ_conditions'] : [];
        $in_situ_values = isset($_POST['in_situ_values']) ? $_POST['in_situ_values'] : [];
        $in_situ_data = [];
        
        for ($i = 0; $i < count($in_situ_conditions); $i++) {
            if (!empty($in_situ_conditions[$i]) && isset($in_situ_values[$i])) {
                $in_situ_data[] = [
                    'condition' => $in_situ_conditions[$i],
                    'value' => $in_situ_values[$i]
                ];
            }
        }
        
        // Structural Analysis (multiple values)
        $structural_analysis = isset($_POST['structural_analysis']) ? $_POST['structural_analysis'] : [];
        $structural_analysis_values = isset($_POST['structural_analysis_value']) ? $_POST['structural_analysis_value'] : [];
        $structural_data = [];
        
        for ($i = 0; $i < count($structural_analysis); $i++) {
            if (!empty($structural_analysis[$i]) && isset($structural_analysis_values[$i])) {
                $structural_data[] = [
                    'analysis' => $structural_analysis[$i],
                    'value' => $structural_analysis_values[$i]
                ];
            }
        }
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert the main assessment record
        $stmt = $pdo->prepare("
            INSERT INTO assessments (
                user_id, date, construction_date, structure_name, contract_id, province, 
                city, barangay, street_address, height, base, type_of_design, 
                type_of_material, visual_indicators, analysis_methods, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, NOW()
            )
        ");
        
        $stmt->execute([
            $user_id, $date, $construction_date, $name, $contract_id, $province,
            $city, $barangay, $street_address, $height, $base, $type_of_design,
            $type_of_material, $visual_indicators, $analysis_methods
        ]);
        
        $assessment_id = $pdo->lastInsertId();
        
        // Insert in-situ conditions
        if (!empty($in_situ_data)) {
            $stmt = $pdo->prepare("
                INSERT INTO in_situ_conditions (
                    assessment_id, condition_type, test_value
                ) VALUES (?, ?, ?)
            ");
            
            foreach ($in_situ_data as $data) {
                $stmt->execute([
                    $assessment_id, 
                    $data['condition'], 
                    $data['value']
                ]);
            }
        }
        
        // Insert structural analysis data
        if (!empty($structural_data)) {
            $stmt = $pdo->prepare("
                INSERT INTO structural_analysis (
                    assessment_id, analysis_type, test_value
                ) VALUES (?, ?, ?)
            ");
            
            foreach ($structural_data as $data) {
                $stmt->execute([
                    $assessment_id, 
                    $data['analysis'], 
                    $data['value']
                ]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Generate a new form token for the next submission
        $_SESSION['form_token'] = bin2hex(random_bytes(32));
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Assessment saved successfully', 'assessment_id' => $assessment_id]);
        exit;
        
    } catch (PDOException $e) {
        // Roll back transaction in case of error
        $pdo->rollBack();
        
        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        // Return error response for any other exceptions
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
} else {
    // Not a POST request
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}