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
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Retaining Wall Assessment Tool</title>
      <link rel="stylesheet" href="Css/Questionnaire.css">
            </head>
            <body>
        <?php
        include 'sidebar.php';
        ?>
        <div class="container">
        <div class="header">
            
            <div class="user-info">
                
                Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                
            </div>
            <h1 style="color: #f5f5f5;">Retaining Wall Assessment Tool</h1>
            <p>Professional tool for diagnosing and resolving retaining wall issues</p>
            
        </div>

            <form id="assessmentForm">
            <div class="section" id="phase1">
                <h2>Phase 0</h2>
                <div class="form-group">
                    <div class="location-fields">
                        <div class="location-field">
                        <label for="Date">Date of Inspection</label>
                        <input type="date" id="Date" name="Date" required>
                            
                            <div class="form-group">
                                <br>
                                <label for="ConstructionDate">Date of Construction</label>
                                <input type="month" id="ConstructionDate" name="ConstructionDate" required>
                            </div>  
                            
                            <div class="form-group">
                                <label for="barangay">Barangay</label>
                                <select id="barangay" name="barangay" required disabled>
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                        
                        </div>

                        <div class="location-field">
                            <label for="Name">Name</label>
                            <input type="text" id="Name" name="Name" 
                                placeholder="Structure Name(optional) " >
                                <div class="form-group">
                                    <br>
                                <label for="province">Province</label>
                                <select id="province" name="province" required>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="location-field">
                                <label for="street_address">Street Address</label>
                                <input type="text" id="street_address" name="street_address" 
                                    placeholder="Enter street address" required>
                            </div>
                            
                        </div>    
                        <div class="location-field">
                        <div class="form-group">
                                <label for="ContractID">Contract ID</label>
                                <input type="text" id="ContractID" name="ContractID" 
                                    placeholder="Contract ID" required>
                            </div>
                            <div class="form-group">
                                <label for="city">City/Municipality</label>
                                <select id="city" name="city" required disabled>
                                    <option value="">Select City/Municipality</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="js/address.js"></script>

        <div class="section">
            <h2>Phase 1</h2>
            <div class="location-field">
            <h4>
            Structural Dimensions</h4>
            <div class="location-fields">     
            <div class="location-field">
            <label for="Height">Height</label>
            <input type="number" id="Height" name="Height" step="0.01" min="0" placeholder="in meters" required>
              </div>
                <div class="form-group">
                <label for="Base">Base</label>
                <input type="number" id="Base" name="Base" step="0.01" min="0" placeholder="in meters" required>
                    </div>
                </div>

                <div class="location-field">
                    <div class="form-group">
                        <label for="Type_of_Design">Type of Design</label>
                        <select id="Type_of_Design" name="Type_of_Design" required>
                            <option value="">-- Select Design --</option>
                            <option value="Gravity retaining walls"> Gravity retaining walls</option>
                            <option value="Cantilever retaining walls">Cantilever retaining walls</option>
                            <option value="Mechanically Stabilized Earth (MSE) Walls"> Mechanically Stabilized Earth (MSE) Walls</option>
                        </select>
                    </div>
                </div>

                    <div class="location-field">
                        <div class="form-group">
                            <label for="Type_of_Material">Type of Material</label>
                            <select id="Type_of_Material" name="Type_of_Material" required>
                                <option value="">-- Select Material --</option>
                                <option value="Reinforced Concrete"> Reinforced Concrete</option>
                                <option value="Stone Masonry">Stone Masonry</option>
                                <option value="Gabion"> Gabion </option>
                                <option value="Reinforced soil">Reinforced soil</option>
                                <option value="Geogrid Materials">Geogrid Materials</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
    <label for="in_situ_conditions">In-Situ Conditions</label>
    <div id="in-situ-conditions-container">
        <div class="in-situ-entry" style="display: flex; gap: 10px; margin-bottom: 10px;">
            <select class="in-situ-condition" name="in_situ_conditions[]" required style="flex: 1;">
                <option value="">-- Select Condition --</option>
                <option value="(FFM, DM) Fixed Funnel Method or Direct Measurement">(FFM, DM) Fixed Funnel Method or Direct Measurement</option>
                <option value="(PCT) Proctor Compaction Test">(PCT) Proctor Compaction Test</option>
                <option value="(DST) Direct Shear Test">(DST) Direct Shear Test</option>
                <option value="ALT) Atterberg Limits Test">(ALT) Atterberg Limits Test</option>
                <option value="(MCT) Moisture Content Test">(MCT) Moisture Content Test</option>
                <option value="(CH&FHT) Constant Head and Falling Head Test">(CH&FHT) Constant Head and Falling Head Test</option>
                <option value="(C/WT) Capillary/Wicking Test">(C/WT) Capillary/Wicking Test</option>
                <option value="(GDM) Groundwater Depth Measurement">(GDM) Groundwater Depth Measurement</option>
                <option value="(CT) Consolidation Test">(CT) Consolidation Test</option>
                <option value="(SA) Sieve Analysis">(SA) Sieve Analysis</option>
                <option value="(IST) Interface Shear Test">(IST) Interface Shear Test</option>
                <option value="(VSTU) Vane Shear Test Undrained">(VSTU) Vane Shear Test Undrained</option>
                <option value="(TCT) Triaxial Compression Test">(TCT) Triaxial Compression Test</option>
                <option value="(UUT) Unconsolidated Undrained Test">(UUT) Unconsolidated Undrained Test</option>
                <option value="(SLT) Surcharge Load Testing">(SLT) Surcharge Load Testing</option>
                <option value="(CPT) Cone Penetration Test">(CPT) Cone Penetration Test</option>
                <option value="(SPT) Standard Penetration Test">(SPT) Standard Penetration Test</option>
            </select>
            <input type="text" class="in-situ-value" name="in_situ_values[]" placeholder="Enter test result" disabled style="flex: 1;">
            <button type="button" class="remove-test" style="display: none; background: #dc3545; padding: 0 10px;">✕</button>
        </div>
    </div>
    <button type="button" id="add-in-situ-test" style="margin-top: 10px; background: #4682B4; padding: 8px 15px; font-size: 14px;">+ Add Another Test</button>
</div>
<script src="js/in-situ-entry-fields.js"></script>


    <div class="form-group">
    <label for="structural_analysiss">Structural Analysis</label>
                <div style="display: flex; gap: 10px;">
                    <select id="structural_analysis" name="structural_analysis" required style="flex: 1;">
                        <option value="">-- Select Condition --</option>
                        <option value="(D/U WT) Density/Unit Weight Test">(D/U WT) Density/Unit Weight Test</option>
                        <option value="(CST) Compressive Strength Test">(CST) Compressive Strength Test</option>
                       
                    </select>
                    <input type="text" id="structural_analysis_value" name="structural_analysis_value" placeholder="Enter test result" disabled style="flex: 1;">
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
    const structuralAnalysisGroup = document.querySelector('label[for="structural_analysiss"]').parentElement;
    const container = document.createElement('div');
    container.id = 'structural-analysis-container';
    const label = structuralAnalysisGroup.querySelector('label');
    const originalContent = structuralAnalysisGroup.querySelector('div[style="display: flex; gap: 10px;"]');
    
    const firstEntry = originalContent.cloneNode(true);
    firstEntry.classList.add('structural-analysis-entry');
    firstEntry.style.marginBottom = '10px';
    
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'remove-structural-test';
    removeButton.innerHTML = '✕';
    removeButton.style.background = '#dc3545';
    removeButton.style.padding = '0 10px';
    removeButton.style.display = 'none';
    firstEntry.appendChild(removeButton);
    
    structuralAnalysisGroup.removeChild(originalContent);
    
    structuralAnalysisGroup.appendChild(container);
    container.appendChild(firstEntry);
    
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.id = 'add-structural-test';
    addButton.innerHTML = '+ Add Another Structural Test';
    addButton.style.marginTop = '10px';
    addButton.style.background = '#4682B4';
    addButton.style.padding = '8px 15px';
    addButton.style.fontSize = '14px';
    addButton.style.color = 'white';
    addButton.style.border = 'none';
    addButton.style.borderRadius = '5px';
    addButton.style.cursor = 'pointer';
    structuralAnalysisGroup.appendChild(addButton);
    
    const firstSelect = firstEntry.querySelector('select');
    const firstInput = firstEntry.querySelector('input');
    firstSelect.name = 'structural_analysis[]';
    firstInput.name = 'structural_analysis_value[]';
    
    function updateDropdownOptions() {
        const allSelects = document.querySelectorAll('.structural-analysis-entry select');
        const selectedValues = Array.from(allSelects).map(select => select.value).filter(value => value !== '');

        allSelects.forEach(select => {
            const currentValue = select.value;

            Array.from(select.options).forEach(option => {
                option.disabled = false;
            });

            selectedValues.forEach(value => {
                if (value !== currentValue && value !== '') {
                    const option = select.querySelector(`option[value="${value}"]`);
                    if (option) option.disabled = true;
                }
            });
        });
    }
    
    function initializeStructuralSelectListeners() {
        document.querySelectorAll('.structural-analysis-entry select').forEach(select => {
            if (!select.hasEventListener) {
                select.addEventListener('change', function() {
                    const inputField = this.parentElement.querySelector('input');
                    
                    if (this.value) {
                        inputField.disabled = false;
                        inputField.placeholder = `Enter ${this.value} result`;
                        inputField.focus();
                    } else {
                        inputField.disabled = true;
                        inputField.value = '';
                        inputField.placeholder = 'Enter test result';
                    }
                    
                    updateDropdownOptions();
                });
                select.hasEventListener = true;
            }
        });
    }

    function updateAddButtonVisibility() {
        const testCount = document.querySelectorAll('.structural-analysis-entry').length;
        
        if (testCount >= 2) {
            addButton.style.display = 'none';
        } else {
            addButton.style.display = 'block';
        }

        if (testCount > 1) {
            document.querySelectorAll('.remove-structural-test').forEach(btn => {
                btn.style.display = 'block';
            });
        } else {
            document.querySelectorAll('.remove-structural-test').forEach(btn => {
                btn.style.display = 'none';
            });
        }
    }

    initializeStructuralSelectListeners();
    

    addButton.addEventListener('click', function() {
        const entryDiv = document.createElement('div');
        entryDiv.className = 'structural-analysis-entry';
        entryDiv.style.display = 'flex';
        entryDiv.style.gap = '10px';
        entryDiv.style.marginBottom = '10px';
    
        const newSelect = firstSelect.cloneNode(true);
        newSelect.value = '';
        
        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = 'structural_analysis_value[]';
        newInput.placeholder = 'Enter test result';
        newInput.disabled = true;
        newInput.style.flex = '1';
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-structural-test';
        removeBtn.innerHTML = '✕';
        removeBtn.style.background = '#dc3545';
        removeBtn.style.padding = '0 10px';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '5px';
        removeBtn.style.cursor = 'pointer';
        
        removeBtn.addEventListener('click', function() {
            container.removeChild(entryDiv);
            updateAddButtonVisibility();
            updateDropdownOptions();
        });
        
        entryDiv.appendChild(newSelect);
        entryDiv.appendChild(newInput);
        entryDiv.appendChild(removeBtn);
        container.appendChild(entryDiv);
        updateAddButtonVisibility();
    
        initializeStructuralSelectListeners();

        updateDropdownOptions();
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.className === 'remove-structural-test') {
            const entry = e.target.parentElement;
            container.removeChild(entry);
            updateAddButtonVisibility();
            updateDropdownOptions();
        }
    });

    updateAddButtonVisibility();
});       
            </script>
    </div>

            <h3>Visual Indicators</h3>
            <div class="checkbox-group">
                <?php
                    $categories = [
                        'Front Face' => ['Horizontal or diagonal cracks at the base', 'Horizontal cracks at middle-height', 'Horizontal cracks at the base or lower-height',
                                        'Shear Cracks', 'Vertical Cracks', 'Water Seepage marks', 'Mold Growth', 'Soil Erosion', 'Rust Stains', 'Detachment of wall facing',
                                        'Crumbling wall material', 'Absent drainage system', 'Exposed reinforcements', 'Bulging in middle height', 'Collapse of upper-height'],
                        'Wall Displacements' => ['Leaning of upper-height', 'Rotational movement of entire structure', 'Lateral displacement of entire structure',
                                                'Tilting along the length of entire structure', 'Leaning of entire structure', 'Displacement of entire structure'],
                        'Wall Base' => ['Misalignment in foundation elements', 'Settlement', 'Soil Erosion'],
                        'Foundation Soil' => ['Muddy Soil', 'Water Pooling', 'Soil heave at the toe', 'Scouring', 'Settlement', 'Depression', 'Soft or Spongy', 'Separation of wall base and foundation soil'],
                        'Backfill Soil' => ['Soil creep', 'Tension cracks', 'Landslide', 'Muddy Soil', 'Bulges'],
                        'Nearby Structure' => ['Natural loads contribution', 'Vibrations from nearby structures', 'Overloading signs', 'Cracks', 'Displacement', 'Settlement', 'Damaged Drainage System']
                    ];
                    echo "<div class='checkbox-container'>";

                    foreach ($categories as $title => $indicators) {
                        echo "<div class='category'>";
                        echo "<h3>$title</h3>";
                        foreach ($indicators as $indicator) {
                            $id = strtolower(str_replace([' ', '&'], '_', $indicator));
                            echo "<div class='checkbox-item'>
                                    <input type='checkbox' id='$id' name='test[]' value='$indicator'>
                                    <label for='$id'>$indicator</label>
                                </div>";
                        }
                        echo "</div>";
                    }

                    echo "</div>";
                ?>
            </div>
        </div>
                <div class="section">
                    <h2>Analysis Methods</h2>
                    <div class="checkbox-group">
                        <?php
                        $methods = [
                            'Finite Element Analysis (FEA)',
                            'Finite Difference Methods (FDM)',
                            'In-Situ Conditions'
                        ];
                        foreach ($methods as $method) {
                            $id = strtolower(str_replace(' ', '_', $method));
                            echo "<div class='checkbox-item'>
                                    <input type='checkbox' id='$id' name='analysis[]' value='$method'>
                                    <label for='$id'>$method</label>
                                </div>";
                        }
                        ?>
                    </div>
                    <?php

                    if (!isset($_SESSION['form_token'])) {
                        $_SESSION['form_token'] = bin2hex(random_bytes(32));
                    }
                    ?>

                    <form id="assessmentForm" method="POST" >
                        <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
                        <button type="submit" id="submitButton">Submit Assessment</button>
                    </form>

                </div>
            </form>
            <div id="results" class="results section">
                <h2>Assessment Results</h2>
                <div id="resultContent"></div>
            </div>
        </div>
        <div id="recommendationModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Recommendations</h2>
        <div id="recommendationContent"></div>
    </div>
</div>
    <script>
        // This function needs to be properly exposed to the window object
// Define generateRecommendations function globally, outside any event handlers
function generateRecommendations(failureTypes, causeOfFailure, indicators) {
    const recommendations = {
        remediationMethod: {
            diagnosis1: [],
            diagnosis2: [],
            diagnosis3: [],
        },
        supportingLabTests: []
    };

    // Determine diagnosis based on severity
    if (indicators.length === 0) {
        recommendations.remediationMethod.diagnosis1.push('No need of Remediation');
    } else if (indicators.includes('Collapse of upper-height') || 
               indicators.includes('Displacement of entire structure')) {
        recommendations.remediationMethod.diagnosis3.push('Wall Replacement');
    } else {
        // Select appropriate remediation methods based on failure types
        failureTypes.forEach(type => {
            switch(type) {
                case 'Sliding':
                    recommendations.remediationMethod.diagnosis2.push(...[
                        'Soil nailing',
                        'Anchoring',
                        'Concrete jacket'
                    ]);
                    break;
                case 'Overturning':
                    recommendations.remediationMethod.diagnosis2.push(...[
                        'Buttressing',
                        'Anchoring',
                        'Tiebacks'
                    ]);
                    break;
                case 'Wall Bending':
                    recommendations.remediationMethod.diagnosis2.push(...[
                        'Steel Bracing',
                        'Fiber-Reinforced Shotcrete',
                        'Concrete jacket'
                    ]);
                    break;
                case 'Drainage Failure':
                    recommendations.remediationMethod.diagnosis2.push(...[
                        'Perforated pipes',
                        'Geocomposite drains',
                        'Geotextiles'
                    ]);
                    break;
                case 'Wall Fracture':
                    recommendations.remediationMethod.diagnosis2.push(...[
                        'Crack Injection',
                        'Surface Sealing',
                        'Reinforcement Addition'
                    ]);
                    break;
                case 'Foundation Failure':
                    recommendations.remediationMethod.diagnosis2.push(...[
                        'Underpinning',
                        'Micropiles',
                        'Foundation Reinforcement'
                    ]);
                    break;
                case 'Base Failure':
                    recommendations.remediationMethod.diagnosis2.push(...[
                        'Base Reinforcement',
                        'Soil Replacement',
                        'Erosion Control Measures'
                    ]);
                    break;
                default:
                    recommendations.remediationMethod.diagnosis2.push('Professional structural assessment');
                    break;
            }
        });
    }

    // Select supporting laboratory tests
    switch(causeOfFailure) {
        case 'Poor Drainage':
            recommendations.supportingLabTests.push(
                '(CH&FHT) Constant Head and Falling Head Test',
                '(GDM) Groundwater Depth Measurement'
            );
            break;
        case 'Base Material Failure':
            recommendations.supportingLabTests.push(
                '(PCT) Proctor Compaction Test',
                '(DST) Direct Shear Test'
            );
            break;
        case 'Foundation Issues':
            recommendations.supportingLabTests.push(
                '(SPT) Standard Penetration Test',
                '(CPT) Cone Penetration Test'
            );
            break;
        case 'Material Degradation':
            recommendations.supportingLabTests.push(
                'Material Strength Test',
                'Chemical Analysis'
            );
            break;
        case 'Excessive Earth Pressure':
            recommendations.supportingLabTests.push(
                'Lateral Earth Pressure Measurement',
                '(DST) Direct Shear Test'
            );
            break;
        case 'Drainage Issues':
            recommendations.supportingLabTests.push(
                'Permeability Test',
                'Infiltration Test'
            );
            break;
        case 'Poor Soil Conditions':
            recommendations.supportingLabTests.push(
                'Soil Classification Test',
                '(ALT) Atterberg Limits Test'
            );
            break;
        case 'Water Infiltration':
            recommendations.supportingLabTests.push(
                'Moisture Content Test',
                'Hydraulic Conductivity Test'
            );
            break;
        case 'Structural Stress':
            recommendations.supportingLabTests.push(
                'Stress Analysis',
                'Material Strength Test'
            );
            break;
        case 'Slope Instability':
            recommendations.supportingLabTests.push(
                'Slope Stability Analysis',
                'Soil Shear Strength Test'
            );
            break;
        default:
            recommendations.supportingLabTests.push(
                'Comprehensive Soil Testing Suite',
                'Structural Integrity Assessment'
            );
            break;
    }

    return recommendations;
}

// This function needs to be properly exposed to the window object
function showRecommendations(failureTypes, causeOfFailure, indicators) {
    const modal = document.getElementById('recommendationModal');
    const content = document.getElementById('recommendationContent');
    
    // Convert parameters if they're strings (which can happen when called from HTML)
    if (typeof failureTypes === 'string') {
        try {
            failureTypes = JSON.parse(failureTypes);
        } catch(e) {
            failureTypes = [];
        }
    }
    
    if (typeof indicators === 'string') {
        try {
            indicators = JSON.parse(indicators);
        } catch(e) {
            indicators = [];
        }
    }
    
    let html = '<div class="recommendations-container">';
    
    // Check if there are no indicators (good condition)
    if (indicators.length === 0) {
        html += `
            <div class="recommendation-section">
                <h3>Maintenance Recommendations</h3>
                <div class="priority-low">
                    <h4>Good Condition - Routine Maintenance</h4>
                    <ul>
                        <li>Conduct regular visual inspections (every 6-12 months)</li>
                        <li>Keep drainage systems clean and functional</li>
                        <li>Monitor for any new cracks or movements</li>
                        <li>Document wall condition with photographs</li>
                        <li>Maintain proper surface water drainage</li>
                    </ul>
                </div>
                <div class="recommendation-section">
                    <h3>Preventive Monitoring</h3>
                    <ul>
                        <li>Annual structural inspection</li>
                        <li>Drainage system effectiveness check</li>
                        <li>Review of surrounding soil conditions</li>
                    </ul>
                </div>
            </div>`;
    } else {
        // Generate recommendations based on the identified issues
        const recommendations = generateRecommendations(failureTypes, causeOfFailure, indicators);
        
        // Remediation Methods Section
        html += '<div class="recommendation-section">';
        html += '<h3>Remediation Methods</h3>';
        
        if (recommendations.remediationMethod.diagnosis1.length > 0) {
            html += `
                <div class="priority-low">
                    <h4>No Remediation Required</h4>
                    <p>${recommendations.remediationMethod.diagnosis1.join(', ')}</p>
                </div>
            `;
        }
        
        if (recommendations.remediationMethod.diagnosis2.length > 0) {
            html += `
                <div class="priority-medium">
                    <h4>Recommended Remediation Methods</h4>
                    <ul>
                        ${recommendations.remediationMethod.diagnosis2.map(method => `<li>${method}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        if (recommendations.remediationMethod.diagnosis3.length > 0) {
            html += `
                <div class="priority-high">
                    <h4>Critical Action Required</h4>
                    <p>${recommendations.remediationMethod.diagnosis3.join(', ')}</p>
                </div>
            `;
        }
        
        // Supporting Lab Tests Section
        if (recommendations.supportingLabTests.length > 0) {
            html += `
                <div class="recommendation-section">
                    <h3>Recommended Laboratory Tests</h3>
                    <ul>
                        ${recommendations.supportingLabTests.map(test => `<li>${test}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
    }
    
    html += '</div>';
    content.innerHTML = html;
    modal.style.display = 'block';

    // Close button functionality
    const closeBtn = document.querySelector('.close-modal');
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };

    // Click outside to close
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

// Helper function to determine failure types
function determineFailureTypes(indicators) {
    const failureTypes = new Set();
    
    const failureMapping = {
        'Horizontal or diagonal cracks at the base': ['Sliding'],
        'Leaning of upper-height': ['Overturning'],
        'Leaning of entire structure': ['Overturning'],
        'Horizontal cracks at middle-height': ['Wall Bending'],
        'Shear Cracks': ['Wall Fracture'],
        'Vertical Cracks': ['Wall Fracture'],
        'Bulging in middle height': ['Wall Bending'],
        'Rotational movement of entire structure': ['Overturning'],
        'Lateral displacement of entire structure': ['Sliding'],
        'Settlement': ['Foundation Failure'],
        'Soil Erosion': ['Base Failure'],
        'Water Seepage marks': ['Drainage Failure']
    };

    indicators.forEach(indicator => {
        if (failureMapping[indicator]) {
            failureMapping[indicator].forEach(type => failureTypes.add(type));
        }
    });

    return Array.from(failureTypes).slice(0, 2);
}

// Helper function to determine cause of failure
function determineCauseOfFailure(indicators) {
    const causes = {
        'Water Seepage marks': 'Poor Drainage',
        'Soil Erosion': 'Base Material Failure',
        'Settlement': 'Foundation Issues',
        'Crumbling wall material': 'Material Degradation',
        'Bulges': 'Excessive Earth Pressure',
        'Water Pooling': 'Drainage Issues',
        'Soft or Spongy': 'Poor Soil Conditions',
        'Muddy Soil': 'Water Infiltration',
        'Tension cracks': 'Structural Stress',
        'Landslide': 'Slope Instability'
    };

    for (let indicator of indicators) {
        if (causes[indicator]) {
            return causes[indicator];
        }
    }

    return 'Multiple Contributing Factors';
}

// Helper function to determine condition diagnosis
function determineConditionDiagnosis(indicators, failureTypes) {
    const criticalIndicators = [
        'Collapse of upper-height',
        'Leaning of entire structure',
        'Displacement of entire structure',
        'Landslide'
    ];

    const hasCriticalIssues = indicators.some(indicator => 
        criticalIndicators.includes(indicator));

    if (hasCriticalIssues || failureTypes.length > 1) {
        return {
            severity: 'high',
            diagnosis: 'Critical - Wall Replacement Needed',
            explanation: 'Multiple critical issues detected. Immediate action required.'
        };
    }

    if (indicators.length > 3) {
        return {
            severity: 'medium',
            diagnosis: 'Significant Deterioration',
            explanation: 'Multiple issues detected requiring prompt attention and remediation.'
        };
    }

    if (indicators.length > 0) {
        return {
            severity: 'low',
            diagnosis: 'Minor Issues Present',
            explanation: 'Early signs of wear detected. Preventive maintenance recommended.'
        };
    }

    return {
        severity: 'none',
        diagnosis: 'Good Condition',
        explanation: 'No significant issues detected.'
    };
}

function displayResults(checkedIndicators, failureTypes, causeOfFailure, conditionDiagnosis) {
    const results = document.getElementById('results');
    const resultContent = document.getElementById('resultContent');
    
    results.style.display = 'block';
    
    resultContent.innerHTML = `
        <div class="assessment-results">
            <h3>Assessment Results</h3>
            
            <div class="summary-section">
                <h4>Failure Types Identified</h4>
                ${failureTypes.length > 0 
                    ? `<p>${failureTypes.join(', ')}</p>`
                    : '<p>No specific failure types identified</p>'
                }
            </div>

            <div class="summary-section">
                <h4>Primary Cause of Issues</h4>
                <p>${causeOfFailure}</p>
            </div>

            <div class="summary-section">
                <h4>Condition Assessment</h4>
                <p class="severity-indicator ${conditionDiagnosis.severity}">
                    ${conditionDiagnosis.diagnosis}
                </p>
                <p>${conditionDiagnosis.explanation}</p>
            </div>

            <div class="summary-section">
                <h4>Detected Issues</h4>
                <ul>
                    ${checkedIndicators.map(indicator => `<li>${indicator}</li>`).join('')}
                </ul>
            </div>

            <button id="viewRecommendationsBtn" class="button">
                View Recommendations
            </button>
        </div>
    `;
    document.getElementById('viewRecommendationsBtn').addEventListener('click', function() {
        showRecommendations(failureTypes, causeOfFailure, checkedIndicators);
    });

    results.scrollIntoView({ behavior: 'smooth' });
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('assessmentForm');
    const submitButton = document.getElementById('submitButton');
    const results = document.getElementById('results');
    const resultContent = document.getElementById('resultContent');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            submitButton.disabled = true;
            submitButton.innerText = "Processing...";
            
            try {
                const checkedIndicators = Array.from(document.querySelectorAll('input[name="test[]"]:checked'))
                    .map(checkbox => checkbox.value);
                
                const failureTypes = determineFailureTypes(checkedIndicators);
                const causeOfFailure = determineCauseOfFailure(checkedIndicators);
                const conditionDiagnosis = determineConditionDiagnosis(checkedIndicators, failureTypes);
                const formData = new FormData(form);
                formData.append('failureTypes', JSON.stringify(failureTypes));
                formData.append('causeOfFailure', causeOfFailure);
                formData.append('conditionDiagnosis', conditionDiagnosis.diagnosis);
                formData.append('severity', conditionDiagnosis.severity);
                formData.append('explanation', conditionDiagnosis.explanation);
                
                // Send the data via AJAX to save in database
                fetch('save-assessment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Show success message
                    if (data.status === 'success') {
                        // Display results
                        displayResults(checkedIndicators, failureTypes, causeOfFailure, conditionDiagnosis);
                        
                        // Show a success message
                        const successAlert = document.createElement('div');
                        successAlert.className = 'success-alert';
                        successAlert.textContent = 'Assessment saved successfully!';
                        form.prepend(successAlert);
                        
                        // Remove success message after 5 seconds
                        setTimeout(() => {
                            if (successAlert.parentNode) {
                                successAlert.parentNode.removeChild(successAlert);
                            }
                        }, 5000);
                    } else {
                        throw new Error(data.message || 'Failed to save assessment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultContent.innerHTML = `
                        <div class="error-message">
                            An error occurred while saving the assessment: ${error.message}
                        </div>
                    `;
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerText = "Submit Assessment";
                });
                
            } catch (error) {
                console.error('Error:', error);
                resultContent.innerHTML = `
                    <div class="error-message">
                        An error occurred while processing the assessment: ${error.message}
                    </div>
                `;
                submitButton.disabled = false;
                submitButton.innerText = "Submit Assessment";
            }
        });
    }

    // Initialize Select2 if it exists
    if (window.jQuery && $.fn.select2 && $('#wall_material').length) {
        $('#wall_material').select2({
            placeholder: "-- Select Material --",
            allowClear: true
        });

        const labTests = {
            "Poor soil compaction": "Proctor Compaction Test (PCT)",
            "Uneven soil compaction": "Field Density Test (FDT)",
            "Slope greater than the Angle of Repose": "Fixed Funnel Method (FFM), Direct Measurement (DM)",
            "High Unit Weight": "Density/Unit Weight Test (D/U WT)",
            "Low Density": "Density/Unit Weight Test (D/U WT)",
            "High Cohesion": "Direct Shear Test (DST)",
            "Low Cohesion": "Direct Shear Test (DST)",
            "High Void Ratio": "Consolidation Test (CT)",
            "High Moisture Content": "Moisture Content Test (MCT)",
            "High Plasticity Index": "Atterberg Limits Test (ALT)",
            "High Liquid Limit": "Atterberg Limits Test (ALT)",
            "High Capillarity": "Capillary/Wicking Test (C/WT)",
            "High Consolidation Potential": "Consolidation Test (CT)",
            "High Compressibility/Compression Index": "Consolidation Test (CT)",
            "High Proportion of Fined-Grained Soil": "Sieve Analysis (SA)",
            "Low Permeability": "Constant Head & Falling Head Test (CH&FHT)",
            "Low Shear Strength": "Unconsolidated Undrained Test (UUT), Triaxial Compression Test (TCT)",
            "Low Shear Strength for cohesive soil": "Unconsolidated Undrained Test (UUT)",
            "Low Angle of Internal Friction": "Direct Shear Test (DST)",
            "Low Shear Resistance": "Interface Shear Test (IST)",
            "Low Cone Resistance": "Cone Penetration Test (CPT)",
            "Low Soil Resistance to Penetration": "Standard Penetration Test (SPT)",
            "High Surcharge Load": "Surcharge Load Testing (SLT)",
            "High Water Table": "Groundwater Depth Measurement (GDM)"
        };

        $('#wall_material').change(function() {
            let selectedIssue = $(this).val();
            let recommendedTest = labTests[selectedIssue] || "No specific test available.";

            $('#labTest').text(recommendedTest);
        });
    }

    // Expose key functions to window object for use elsewhere
    window.determineFailureTypes = determineFailureTypes;
    window.determineCauseOfFailure = determineCauseOfFailure;
    window.determineConditionDiagnosis = determineConditionDiagnosis;
    window.showRecommendations = showRecommendations;
    window.generateRecommendations = generateRecommendations;
    window.displayResults = displayResults;
});
    </script>
    </body>
    </html>


