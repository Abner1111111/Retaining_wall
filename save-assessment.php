<?php
session_start();
require "back/db_configs.php";

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid form submission']);
    exit();
}

$_SESSION['form_token'] = bin2hex(random_bytes(32));

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $user_id = $_SESSION['user_id'];
        $date_of_inspection = sanitize($_POST['Date']);
        $date_of_construction = sanitize($_POST['ConstructionDate'] . '-01');
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

        if (isset($_POST['test'])) {
            $visual_indicators = $_POST['test'];
            
            foreach ($visual_indicators as $indicator) {
                $stmt = $pdo->prepare("INSERT INTO visual_indicators (assessment_id, indicator) VALUES (?, ?)");
                $stmt->execute([$assessment_id, sanitize($indicator)]);
            }
        }
        
        if (isset($_POST['analysis'])) {
            $analysis_methods = $_POST['analysis'];
            
            foreach ($analysis_methods as $method) {
                $stmt = $pdo->prepare("INSERT INTO analysis_methods (assessment_id, method) VALUES (?, ?)");
                $stmt->execute([$assessment_id, sanitize($method)]);
            }
        }
        
        if (isset($_POST['failureTypes']) && isset($_POST['causeOfFailure'])) {
            $failure_types = $_POST['failureTypes'];
            $cause_of_failure = sanitize($_POST['causeOfFailure']);
            $condition_diagnosis = sanitize($_POST['conditionDiagnosis']);
            $severity = sanitize($_POST['severity']);
            $explanation = sanitize($_POST['explanation']);
            
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
            
            $indicators = isset($_POST['test']) ? $_POST['test'] : [];
            if (is_string($indicators)) {
                $indicators = json_decode($indicators, true);
            }
            $recommendations = generateRecommendationsForDB($failure_types, $cause_of_failure, $indicators);

            if (!empty($recommendations['remediationMethod']['diagnosis1'])) {
                foreach ($recommendations['remediationMethod']['diagnosis1'] as $method) {
                    $stmt = $pdo->prepare("INSERT INTO recommendations (assessment_id, remediation_type, remediation_method) VALUES (?, 'diagnosis1', ?)");
                    $stmt->execute([$assessment_id, sanitize($method)]);
                }
            }
            
            if (!empty($recommendations['remediationMethod']['diagnosis2'])) {
                foreach ($recommendations['remediationMethod']['diagnosis2'] as $method) {
                    $stmt = $pdo->prepare("INSERT INTO recommendations (assessment_id, remediation_type, remediation_method) VALUES (?, 'diagnosis2', ?)");
                    $stmt->execute([$assessment_id, sanitize($method)]);
                }
            }
            
            if (!empty($recommendations['remediationMethod']['diagnosis3'])) {
                foreach ($recommendations['remediationMethod']['diagnosis3'] as $method) {
                    $stmt = $pdo->prepare("INSERT INTO recommendations (assessment_id, remediation_type, remediation_method) VALUES (?, 'diagnosis3', ?)");
                    $stmt->execute([$assessment_id, sanitize($method)]);
                }
            }
            
            if (!empty($recommendations['supportingLabTests'])) {
                foreach ($recommendations['supportingLabTests'] as $test) {
                    $stmt = $pdo->prepare("INSERT INTO recommended_lab_tests (assessment_id, test_name) VALUES (?, ?)");
                    $stmt->execute([$assessment_id, sanitize($test)]);
                }
            }
        }
        
        $pdo->commit();
      
        echo json_encode([
            'status' => 'success', 
            'message' => 'Assessment saved successfully',
            'assessment_id' => $assessment_id
        ]);
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log('Database Error: ' . $e->getMessage());

        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Database error occurred: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log('General Error: ' . $e->getMessage());
       
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

function generateRecommendationsForDB($failureTypes, $causeOfFailure, $indicators) {
    $recommendations = [
        'remediationMethod' => [
            'diagnosis1' => [],
            'diagnosis2' => [],
            'diagnosis3' => [],
        ],
        'supportingLabTests' => []
    ];

    if (is_string($failureTypes)) {
        $failureTypes = json_decode($failureTypes, true);
    }
    
    if (is_string($indicators)) {
        $indicators = json_decode($indicators, true);
    }
    
    if (!is_array($indicators)) {
        $indicators = [];
    }

    if (count($indicators) === 0) {
        $recommendations['remediationMethod']['diagnosis1'][] = 'No need of Remediation';
    } elseif (in_array('Collapse of upper-height', $indicators) || 
              in_array('Displacement of entire structure', $indicators)) {
        $recommendations['remediationMethod']['diagnosis3'][] = 'Wall Replacement';
    } else {
        foreach ($failureTypes as $type) {
            switch($type) {
                case 'Sliding':
                    $recommendations['remediationMethod']['diagnosis2'] = array_merge(
                        $recommendations['remediationMethod']['diagnosis2'],
                        ['Soil nailing', 'Anchoring', 'Concrete jacket']
                    );
                    break;
                case 'Overturning':
                    $recommendations['remediationMethod']['diagnosis2'] = array_merge(
                        $recommendations['remediationMethod']['diagnosis2'],
                        ['Buttressing', 'Anchoring', 'Tiebacks']
                    );
                    break;
                case 'Wall Bending':
                    $recommendations['remediationMethod']['diagnosis2'] = array_merge(
                        $recommendations['remediationMethod']['diagnosis2'],
                        ['Steel Bracing', 'Fiber-Reinforced Shotcrete', 'Concrete jacket']
                    );
                    break;
                case 'Drainage Failure':
                    $recommendations['remediationMethod']['diagnosis2'] = array_merge(
                        $recommendations['remediationMethod']['diagnosis2'],
                        ['Perforated pipes', 'Geocomposite drains', 'Geotextiles']
                    );
                    break;
                case 'Wall Fracture':
                    $recommendations['remediationMethod']['diagnosis2'] = array_merge(
                        $recommendations['remediationMethod']['diagnosis2'],
                        ['Crack Injection', 'Surface Sealing', 'Reinforcement Addition']
                    );
                    break;
                case 'Foundation Failure':
                    $recommendations['remediationMethod']['diagnosis2'] = array_merge(
                        $recommendations['remediationMethod']['diagnosis2'],
                        ['Underpinning', 'Micropiles', 'Foundation Reinforcement']
                    );
                    break;
                case 'Base Failure':
                    $recommendations['remediationMethod']['diagnosis2'] = array_merge(
                        $recommendations['remediationMethod']['diagnosis2'],
                        ['Base Reinforcement', 'Soil Replacement', 'Erosion Control Measures']
                    );
                    break;
                default:
                    $recommendations['remediationMethod']['diagnosis2'][] = 'Professional structural assessment';
                    break;
            }
        }
    }

    switch($causeOfFailure) {
        case 'Poor Drainage':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['(CH&FHT) Constant Head and Falling Head Test', '(GDM) Groundwater Depth Measurement']
            );
            break;
        case 'Base Material Failure':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['(PCT) Proctor Compaction Test', '(DST) Direct Shear Test']
            );
            break;
        case 'Foundation Issues':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['(SPT) Standard Penetration Test', '(CPT) Cone Penetration Test']
            );
            break;
        case 'Material Degradation':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Material Strength Test', 'Chemical Analysis']
            );
            break;
        case 'Excessive Earth Pressure':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Lateral Earth Pressure Measurement', '(DST) Direct Shear Test']
            );
            break;
        case 'Drainage Issues':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Permeability Test', 'Infiltration Test']
            );
            break;
        case 'Poor Soil Conditions':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Soil Classification Test', '(ALT) Atterberg Limits Test']
            );
            break;
        case 'Water Infiltration':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Moisture Content Test', 'Hydraulic Conductivity Test']
            );
            break;
        case 'Structural Stress':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Stress Analysis', 'Material Strength Test']
            );
            break;
        case 'Slope Instability':
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Slope Stability Analysis', 'Soil Shear Strength Test']
            );
            break;
        default:
            $recommendations['supportingLabTests'] = array_merge(
                $recommendations['supportingLabTests'],
                ['Comprehensive Soil Testing Suite', 'Structural Integrity Assessment']
            );
            break;
    }

    return $recommendations;
}