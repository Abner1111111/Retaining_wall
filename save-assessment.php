<?php
session_start();
require "db_configs.php";

// Check authentication
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

// Verify CSRF token
if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid form token']);
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO assessments (user_id, contract_id, structure_name, inspection_date, 
        construction_date, street_address, province_code, city_code, barangay_code) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['ContractID'],
        $_POST['Name'] ?? null,
        $_POST['Date'],
        $_POST['ConstructionDate'],
        $_POST['street_address'],
        $_POST['province'],
        $_POST['city'],
        $_POST['barangay']
    ]);

    $assessment_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO wall_details (assessment_id, height, base_width, design_type, material_type) 
        VALUES (?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $assessment_id,
        $_POST['Height'],
        $_POST['Base'],
        $_POST['Type_of_Design'],
        $_POST['Type_of_Material']
    ]);

    if (isset($_POST['in_situ_conditions']) && isset($_POST['in_situ_values'])) {
        $stmt = $pdo->prepare("INSERT INTO in_situ_conditions (assessment_id, test_type, test_result) VALUES (?, ?, ?)");
        
        foreach ($_POST['in_situ_conditions'] as $key => $test_type) {
            if (!empty($test_type) && !empty($_POST['in_situ_values'][$key])) {
                $stmt->execute([$assessment_id, $test_type, $_POST['in_situ_values'][$key]]);
            }
        }
    }

    // Insert structural analysis
    if (isset($_POST['structural_analysis']) && isset($_POST['structural_analysis_value'])) {
        $stmt = $pdo->prepare("INSERT INTO structural_analysis (assessment_id, test_type, test_result) VALUES (?, ?, ?)");
        
        foreach ($_POST['structural_analysis'] as $key => $test_type) {
            if (!empty($test_type) && !empty($_POST['structural_analysis_value'][$key])) {
                $stmt->execute([$assessment_id, $test_type, $_POST['structural_analysis_value'][$key]]);
            }
        }
    }

    // Insert visual indicators
    if (isset($_POST['test'])) {
        $stmt = $pdo->prepare("INSERT INTO visual_indicators (assessment_id, indicator_category, indicator_name) 
            VALUES (?, ?, ?)");
        
        foreach ($_POST['test'] as $indicator) {
            // Determine category based on indicator
            $category = determineIndicatorCategory($indicator);
            $stmt->execute([$assessment_id, $category, $indicator]);
        }
    }

    // Insert analysis methods
    if (isset($_POST['analysis'])) {
        $stmt = $pdo->prepare("INSERT INTO analysis_methods (assessment_id, method_name) VALUES (?, ?)");
        
        foreach ($_POST['analysis'] as $method) {
            $stmt->execute([$assessment_id, $method]);
        }
    }

    // Calculate and insert assessment results
    $severity_level = calculateSeverityLevel($_POST);
    $diagnosis = generateDiagnosis($_POST);
    $recommendations = generateRecommendations($_POST);

    $stmt = $pdo->prepare("INSERT INTO assessment_results (assessment_id, severity_level, condition_diagnosis, 
        recommendations) VALUES (?, ?, ?, ?)");
    
    $stmt->execute([
        $assessment_id,
        $severity_level,
        $diagnosis,
        $recommendations
    ]);

    $pdo->commit();

    // Return success response with results
    echo json_encode([
        'success' => true,
        'results' => [
            'severity_level' => $severity_level,
            'condition_diagnosis' => $diagnosis,
            'recommendations' => $recommendations
        ]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Helper function to determine indicator category
function determineIndicatorCategory($indicator) {
    $categories = [
        'Front Face' => ['Horizontal or diagonal cracks', 'Shear Cracks', 'Vertical Cracks', 'Water Seepage'],
        'Wall Displacements' => ['Leaning', 'Rotational movement', 'Lateral displacement'],
        'Wall Base' => ['Misalignment', 'Settlement', 'Soil Erosion'],
        'Foundation Soil' => ['Muddy Soil', 'Water Pooling', 'Soil heave'],
        'Backfill Soil' => ['Soil creep', 'Tension cracks', 'Landslide'],
        'Nearby Structure' => ['Natural loads', 'Vibrations', 'Overloading']
    ];

    foreach ($categories as $category => $indicators) {
        foreach ($indicators as $ind) {
            if (stripos($indicator, $ind) !== false) {
                return $category;
            }
        }
    }
    
    return 'Other';
}

// Helper function to calculate severity level
function calculateSeverityLevel($data) {
    $severity_score = 0;
    
    // Check visual indicators
    if (isset($data['test'])) {
        $critical_indicators = ['Collapse', 'Landslide', 'Rotational movement'];
        $major_indicators = ['Leaning', 'Bulging', 'Settlement'];
        
        foreach ($data['test'] as $indicator) {
            if (in_array($indicator, $critical_indicators)) {
                $severity_score += 3;
            } elseif (in_array($indicator, $major_indicators)) {
                $severity_score += 2;
            } else {
                $severity_score += 1;
            }
        }
    }
    
    // Return severity level based on score
    if ($severity_score >= 5) {
        return 'High';
    } elseif ($severity_score >= 3) {
        return 'Medium';
    } else {
        return 'Low';
    }
}

// Helper function to generate diagnosis
function generateDiagnosis($data) {
    $diagnosis = [];
    
    if (isset($data['test'])) {
        // Group issues by category
        $structural_issues = array_filter($data['test'], function($indicator) {
            return stripos($indicator, 'crack') !== false || 
                   stripos($indicator, 'bulging') !== false ||
                   stripos($indicator, 'leaning') !== false;
        });
        
        $drainage_issues = array_filter($data['test'], function($indicator) {
            return stripos($indicator, 'water') !== false || 
                   stripos($indicator, 'seepage') !== false ||
                   stripos($indicator, 'erosion') !== false;
        });
        
        // Build diagnosis
        if ($structural_issues) {
            $diagnosis[] = "Structural integrity concerns detected: " . implode(", ", $structural_issues);
        }
        
        if ($drainage_issues) {
            $diagnosis[] = "Drainage issues identified: " . implode(", ", $drainage_issues);
        }
    }
    
    return implode(" ", $diagnosis) ?: "No significant issues detected.";
}

// Helper function to generate recommendations
function generateRecommendations($data) {
    $recommendations = [];
    
    if (isset($data['test'])) {
        // Add recommendations based on issues
        foreach ($data['test'] as $indicator) {
            if (stripos($indicator, 'crack') !== false) {
                $recommendations[] = "Conduct detailed structural analysis and consider crack repair or reinforcement.";
            }
            if (stripos($indicator, 'water') !== false || stripos($indicator, 'seepage') !== false) {
                $recommendations[] = "Improve drainage system and waterproofing measures.";
            }
            if (stripos($indicator, 'leaning') !== false || stripos($indicator, 'bulging') !== false) {
                $recommendations[] = "Immediate structural evaluation required. Consider stabilization measures.";
            }
            if (stripos($indicator, 'erosion') !== false) {
                $recommendations[] = "Implement erosion control measures and improve surface drainage.";
            }
        }
    }
    
    // Add general recommendations if no specific issues
    if (empty($recommendations)) {
        $recommendations[] = "Regular monitoring and maintenance recommended.";
    }
    
    return implode(" ", array_unique($recommendations));
}
?>