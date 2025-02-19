<?php
session_start();
require "back/db_configs.php";

if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("Location: index.php");
    exit();
}



function identifyFailureTypes($assessmentData) {
    $failureTypes = [];
    $indicators = $assessmentData['visual_indicators'] ?? [];
    $structuralAnalysis = $assessmentData['structural_analysis'] ?? '';
    $inSituConditions = $assessmentData['in_situ_conditions'] ?? '';

    if (
        in_array('Horizontal cracks at the base or lower-height', $indicators) ||
        in_array('Lateral displacement of entire structure', $indicators) ||
        in_array('Soil Erosion', $indicators) ||
        $structuralAnalysis === 'Narrow base' ||
        $inSituConditions === 'Low Shear Resistance' ||
        $inSituConditions === 'Poor soil compaction'
    ) {
        $failureTypes['sliding'] = [
            'type' => 'Sliding Failure',
            'evidence' => 'Horizontal base cracks, lateral movement, and/or poor foundation conditions indicate potential sliding failure.'
        ];
    }
    if (
        in_array('Leaning of entire structure', $indicators) ||
        in_array('Rotational movement of entire structure', $indicators) ||
        in_array('Tilting along the length of entire structure', $indicators) ||
        $structuralAnalysis === 'Too tall relative to base width' ||
        $inSituConditions === 'High Surcharge Load' ||
        $inSituConditions === 'High Water Table'
    ) {
        $failureTypes['overturning'] = [
            'type' => 'Overturning Failure',
            'evidence' => 'Wall rotation, leaning, and/or excessive height-to-width ratio suggest risk of overturning.'
        ];
    }

    if (
        in_array('Horizontal cracks at middle-height', $indicators) ||
        in_array('Bulging in middle height', $indicators) ||
        in_array('Bulges', $indicators) ||
        $structuralAnalysis === 'Low unit weight of wall material' ||
        $inSituConditions === 'High Lateral Earth Pressures'
    ) {
        $failureTypes['bending'] = [
            'type' => 'Wall Bending',
            'evidence' => 'Mid-height horizontal cracks, bulging, and/or excessive lateral pressure indicate bending failure.'
        ];
    }

    if (
        in_array('Vertical Cracks', $indicators) ||
        in_array('Shear Cracks', $indicators) ||
        in_array('Crumbling wall material', $indicators) ||
        $structuralAnalysis === 'Low compressive strength of wall material' ||
        $inSituConditions === 'High Compressibility/Compression Index'
    ) {
        $failureTypes['fracture'] = [
            'type' => 'Wall Fracture',
            'evidence' => 'Vertical and shear cracks, material deterioration, and/or low material strength indicate fracture failure.'
        ];
    }

    return $failureTypes;
}

function identifyCauseOfFailure($assessmentData) {
    $causes = [];
    $indicators = $assessmentData['visual_indicators'] ?? [];
    $structuralAnalysis = $assessmentData['structural_analysis'] ?? '';
    $inSituConditions = $assessmentData['in_situ_conditions'] ?? '';
    if (
        in_array('Bulging sections', $indicators) ||
        in_array('Soil creep', $indicators) ||
        $inSituConditions === 'High Surcharge Load'
    ) {
        $causes['backfill_pressure'] = 'Backfill Earth Pressure: Excessive lateral pressure from soil mass';
    }
    if (
        in_array('Water seepage', $indicators) ||
        in_array('Muddy Soil', $indicators) ||
        $inSituConditions === 'High Water Table' ||
        $inSituConditions === 'High Moisture Content'
    ) {
        $causes['backfill_saturation'] = 'Backfill Saturation: Poor drainage leading to saturated soil conditions';
    }

    if (
        $inSituConditions === 'Low Shear Strength' ||
        $inSituConditions === 'Low Soil Resistance to Penetration' ||
        in_array('Settlement', $indicators)
    ) {
        $causes['low_shear_strength'] = 'Base Low Shear Strength: Inadequate foundation soil strength';
    }

    if (
        $inSituConditions === 'High Compressibility/Compression Index' ||
        $inSituConditions === 'High Consolidation Potential' ||
        in_array('Settlement', $indicators)
    ) {
        $causes['compressible_soil'] = 'Compressible Soil Foundation: Excessive foundation soil compression';
    }

    return $causes;
}

function assessCondition($assessmentData, $failureTypes, $causes) {
    $hasDistress = !empty($failureTypes) || !empty($causes);
    $indicators = $assessmentData['visual_indicators'] ?? [];
    
    $severeIndicators = [
        'Collapse of upper-height',
        'Leaning of entire structure',
        'Crumbling wall material',
        'Exposed reinforcements',
        'Landslide'
    ];

    $severeIssues = array_intersect($indicators, $severeIndicators) !== [];

    if (!$hasDistress) {
        return [
            'category' => 'No need of Remediation',
            'description' => 'No signs of distress indicating functionality problems.',
            'functionality' => 'The wall maintains stability and effectively performs its intended function.'
        ];
    } elseif ($hasDistress && !$severeIssues) {
        return [
            'category' => 'Need of Remediation',
            'description' => 'There are signs of distress indicating functionality problems.',
            'functionality' => 'The wall requires intervention to maintain proper functionality.'
        ];
    } else {
        return [
            'category' => 'Wall Replacement',
            'description' => 'Remediation will no longer improve functionality.',
            'functionality' => 'The wall has severe functionality issues requiring complete replacement.'
        ];
    }
}

function determineRemediation($condition, $failureTypes, $causes) {
    $remediation = [];
    
    if ($condition['category'] === 'No need of Remediation') {
        return ['Routine maintenance and monitoring'];
    }
    
    if ($condition['category'] === 'Wall Replacement') {
        return ['Complete wall replacement required'];
    }
    foreach ($failureTypes as $key => $failure) {
        switch ($key) {
            case 'sliding':
                $remediation = array_merge($remediation, ['Soil nailing', 'Soil Riveting', 'Bench Footing']);
                break;
            case 'overturning':
                $remediation = array_merge($remediation, ['Anchoring', 'Tiebacks', 'Wall Buttressing']);
                break;
            case 'bending':
                $remediation = array_merge($remediation, ['Concrete jacket', 'Fiber-Reinforced Shotcrete', 'Steel Bracing']);
                break;
            case 'fracture':
                $remediation = array_merge($remediation, ['Crack Injections', 'Concrete jacket']);
                break;
        }
    }

    foreach ($causes as $key => $cause) {
        switch ($key) {
            case 'backfill_saturation':
                $remediation = array_merge($remediation, ['Perforated pipes', 'Geocomposite drains', 'Geotextiles']);
                break;
            case 'low_shear_strength':
                $remediation = array_merge($remediation, ['Soil Chemical Grouting', 'Soil Binders', 'Underpinning']);
                break;
            case 'compressible_soil':
                $remediation = array_merge($remediation, ['Piling', 'Soil Replacement', 'Underpinning']);
                break;
        }
    }

    return array_unique($remediation);
}

function generateAssessmentResults($assessmentData) {
    $failureTypes = identifyFailureTypes($assessmentData);
    $causes = identifyCauseOfFailure($assessmentData);
    $condition = assessCondition($assessmentData, $failureTypes, $causes);
    $issuesSummary = [];
    if (!empty($assessmentData['structural_analysis'])) {
        $issuesSummary[] = "Structural Issue: " . $assessmentData['structural_analysis'];
    }
    if (!empty($assessmentData['issues_observed'])) {
        $issuesSummary[] = "Observed Issue: " . $assessmentData['issues_observed'];
    }
    if (!empty($assessmentData['in_situ_conditions'])) {
        $issuesSummary[] = "Ground Condition: " . $assessmentData['in_situ_conditions'];
    }

    if (!empty($assessmentData['visual_indicators'])) {
        foreach ($assessmentData['visual_indicators'] as $indicator) {
            $issuesSummary[] = "Visual Indicator: " . $indicator;
        }
    }

    $labTests = [];
    
    foreach ($failureTypes as $key => $failure) {
        switch ($key) {
            case 'sliding':
                $labTests = array_merge($labTests, [
                    'Direct Shear Test' => 'Determine soil shear strength parameters',
                    'Standard Penetration Test (SPT)' => 'Evaluate foundation soil density and strength',
                    'Field Density Test' => 'Check backfill compaction'
                ]);
                break;
            case 'overturning':
                $labTests = array_merge($labTests, [
                    'Unit Weight Test' => 'Determine soil mass properties',
                    'Moisture Content Test' => 'Assess soil water content',
                    'Consolidation Test' => 'Evaluate soil settlement characteristics'
                ]);
                break;
            case 'bending':
                $labTests = array_merge($labTests, [
                    'Core Compression Test' => 'Evaluate wall material strength',
                    'Schmidt Hammer Test' => 'Non-destructive strength assessment',
                    'Ultrasonic Pulse Velocity Test' => 'Check material integrity'
                ]);
                break;
            case 'fracture':
                $labTests = array_merge($labTests, [
                    'Concrete Core Testing' => 'Assess material strength',
                    'Crack Width Measurement' => 'Monitor crack progression',
                    'Carbonation Test' => 'Check concrete deterioration'
                ]);
                break;
        }
    }
    
    switch($assessmentData['type_of_material']) {
        case 'Reinforced Concrete':
            $labTests = array_merge($labTests, [
                'Rebound Hammer Test' => 'Non-destructive strength assessment',
                'Half-cell Potential Test' => 'Check reinforcement corrosion',
                'Carbonation Depth Test' => 'Assess concrete deterioration'
            ]);
            break;
        case 'Stone Masonry':
            $labTests = array_merge($labTests, [
                'Point Load Test' => 'Stone strength assessment',
                'Water Absorption Test' => 'Evaluate material durability',
                'Mortar Analysis' => 'Check binding material quality'
            ]);
            break;
        case 'Gabion':
            $labTests = array_merge($labTests, [
                'Wire Tensile Test' => 'Check mesh strength',
                'Rock Quality Test' => 'Assess fill material durability',
                'Corrosion Assessment' => 'Evaluate wire mesh condition'
            ]);
            break;
    }
    
    $remediation = determineRemediation($condition, $failureTypes, $causes);
    
    $conclusion = [
        'Primary Assessment' => [
            'Failure Types' => array_column($failureTypes, 'type'),
            'Root Causes' => array_values($causes),
            'Current Condition' => [
                'Category' => $condition['category'],
                'Description' => $condition['description'],
                'Functionality Status' => $condition['functionality']
            ]
        ],
        'Required Testing' => [
            'Immediate Tests' => array_slice($labTests, 0, 3), 
            'Secondary Tests' => array_slice($labTests, 3),
        ],
        'Action Plan' => [
            'Remediation Methods' => $remediation,
            'Monitoring Requirements' => [
                'Inspection Frequency' => $condition['category'] === 'Need of Remediation' ? 'Monthly' : 'Quarterly',
                'Key Parameters' => [
                    'Displacement monitoring',
                    'Crack width measurement',
                    'Water seepage observation',
                    'Ground movement assessment'
                ]
            ]
        ]
    ];


    $severityScore = 0;
    $severityScore += count($failureTypes) * 2; 
    $severityScore += count($causes); 
    $severityScore += ($condition['category'] === 'Wall Replacement') ? 5 : 
                     ($condition['category'] === 'Need of Remediation' ? 3 : 0);

    $severityLevel = $severityScore >= 8 ? "High" : 
                     ($severityScore >= 5 ? "Medium" : "Low");

    return [
        'issues_summary' => implode("; ", $issuesSummary),
        'failure_types' => implode("; ", array_column($failureTypes, 'type')),
        'causes_of_failure' => implode("; ", array_values($causes)),
        'condition_diagnosis' => "{$condition['category']}: {$condition['description']}",
        'functionality' => $condition['functionality'],
        'recommendations' => implode("; ", $remediation),
        'recommended_tests' => implode("; ", array_keys($labTests)),
        'severity_level' => $severityLevel,
        'conclusion' => $conclusion
    ];
}
function saveAddress($pdo, $addressData) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO addresses (
                street_address,
                barangay_code,
                city_code,
                province_code,
                region_code
            ) VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $addressData['street_address'],
            $addressData['barangay_code'],
            $addressData['city_code'],
            $addressData['province_code'],
            '120000000' // Region 12 code
        ]);
        
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Error saving address: " . $e->getMessage());
        throw $e;
    }
}

function saveAssessment($pdo, $assessmentData) {
    try {
        $pdo->beginTransaction();
        
        // First save the address
        $addressId = saveAddress($pdo, [
            'street_address' => $assessmentData['street_address'],
            'barangay_code' => $assessmentData['barangay_code'],
            'city_code' => $assessmentData['city_code'],
            'province_code' => $assessmentData['province_code']
        ]);
        
        // Then save the assessment with the address_id
        $stmt = $pdo->prepare("
            INSERT INTO assessments (
                user_id,
                name,
                age,
                address_id,
                issues_observed,
                type_of_design,
                type_of_material,
                structural_analysis,
                in_situ_conditions
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $assessmentData['name'],
            $assessmentData['age'],
            $addressId,
            $assessmentData['issues_observed'],
            $assessmentData['type_of_design'],
            $assessmentData['type_of_material'],
            $assessmentData['structural_analysis'],
            $assessmentData['in_situ_conditions']
        ]);
        
        
        $assessmentId = $pdo->lastInsertId();

        $results = generateAssessmentResults($assessmentData);

        $stmt = $pdo->prepare("
            INSERT INTO assessment_results (
                assessment_id,
                issues_summary,
                condition_diagnosis,
                failure_types,
                recommended_tests,
                recommendations,
                severity_level
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $issuesSummary = array_filter([
            $assessmentData['issues_observed'],
            $assessmentData['structural_analysis'],
            $assessmentData['in_situ_conditions']
        ]);
        
        $stmt->execute([
            $assessmentId,
            implode("; ", $issuesSummary),
            $results['condition_diagnosis'],
            $results['failure_types'],
            $results['recommended_tests'],
            $results['recommendations'],
            $results['severity_level']
        ]);


        if (!empty($assessmentData['visual_indicators'])) {
            $stmt = $pdo->prepare("INSERT INTO visual_indicators (assessment_id, indicator_type) VALUES (?, ?)");
            foreach ($assessmentData['visual_indicators'] as $indicator) {
                $stmt->execute([$assessmentId, $indicator]);
            }
        }

        if (!empty($assessmentData['analysis_methods'])) {
            $stmt = $pdo->prepare("INSERT INTO analysis_methods (assessment_id, method_type) VALUES (?, ?)");
            foreach ($assessmentData['analysis_methods'] as $method) {
                $stmt->execute([$assessmentId, $method]);
            }
        }

        $pdo->commit();
        return [
            'success' => true,
            'assessment_id' => $pdo->lastInsertId(),
            'address_id' => $addressId
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assessmentData = [
        'name' => $_POST['Name'] ?? '',
        'age' => $_POST['Age'] ?? '',
    
        'issues_observed' => $_POST['Issues_Observed'] ?? '',
        'type_of_design' => $_POST['Type_of_Design'] ?? '',
        'type_of_material' => $_POST['Type_of_Material'] ?? '',
        'structural_analysis' => $_POST['structural_analysis'] ?? '',
        'in_situ_conditions' => $_POST['in_situ_conditions'] ?? '',
        'visual_indicators' => $_POST['test'] ?? [],
        'analysis_methods' => $_POST['analysis'] ?? []
    ];
    
    $result = saveAssessment($pdo, $assessmentData);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}
?>