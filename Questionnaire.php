    <?php
    session_start();
    require "back/db_configs.php";
    require "includes/validate_session.php";
  
  
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
        const testUnits = {
            "(PCT) Proctor Compaction Test": {
                "Maximum Dry Density": "kN/m³",
                "Optimum Moisture Content": "%"
            },
            "(DST) Direct Shear Test": {
                "Cohesion": "kPa",
                "Angle of Internal Friction": "degrees",
                "Shear Strength": "kPa"
            },
            "(ALT) Atterberg Limits Test": {
                "Plasticity Index": "%",
                "Liquid Limit": "%"
            },
            "(MCT) Moisture Content Test": {
                "Moisture Content": "%"
            },
            "(CH&FHT) Constant Head and Falling Head Test": {
                "Permeability": "m/s"
            },
            "(C/WT) Capillary/Wicking Test": {
                "Capillary Rise Height": "m"
            },
            "(GDM) Groundwater Depth Measurement": {
                "Water Table Depth": "m"
            },
            "(CT) Consolidation Test": {
                "Compression Index": "",
                "Void Ratio": ""
            },
            "(SA) Sieve Analysis": {
                "Grain Size Distribution": "%"
            },
            "(IST) Interface Shear Test": {
                "Shear Strength at Interface": "kPa"
            },
            "(VSTU) Vane Shear Test Undrained": {
                "Undrained Shear Strength": "kPa"
            },
            "(TCT) Triaxial Compression Test": {
                "Shear Strength": "kPa"
            },
            "(UUT) Unconsolidated Undrained Test": {
                "Undrained Shear Strength": "kPa"
            },
            "(SLT) Surcharge Load Testing": {
                "Settlement within 24 hours": "mm"
            },
            "(CPT) Cone Penetration Test": {
                "Cone Resistance": "kPa"
            },
            "(SPT) Standard Penetration Test": {
                "N-value": "blows per 30cm"
            },
            "(D/U WT) Density/Unit Weight Test": {
                "Bulk Density": "kN/m³",
                "Dry Density": "kN/m³"
            },
            "(CST) Compressive Strength Test": {
                "Compressive Strength": "kPa"
            }
        };

        function updateInputField(select, input) {
            const selectedTest = select.value;
            
            if (selectedTest) {
                input.disabled = false;
                
                // Get the test parameters
                const testParameters = testUnits[selectedTest];
                
                if (testParameters) {
                    // If test has multiple parameters, create a sub-selection dropdown
                    if (Object.keys(testParameters).length > 1) {
                        // Remove existing sub-parameter selector if any
                        const existingSubParam = select.parentElement.querySelector('.sub-parameter');
                        if (existingSubParam) {
                            select.parentElement.removeChild(existingSubParam);
                        }
                        
                        // Create sub-parameter dropdown
                        const subParamSelect = document.createElement('select');
                        subParamSelect.className = 'sub-parameter';
                        subParamSelect.style.flex = '1';
                        
                        // Add option for each parameter
                        const defaultOption = document.createElement('option');
                        defaultOption.value = "";
                        defaultOption.textContent = "-- Select Parameter --";
                        subParamSelect.appendChild(defaultOption);
                        
                        for (const param in testParameters) {
                            const option = document.createElement('option');
                            option.value = param;
                            option.textContent = param;
                            subParamSelect.appendChild(option);
                        }
                        
                        // Insert after the test select dropdown
                        select.parentElement.insertBefore(subParamSelect, input);
                        
                        // Update input field when sub-parameter changes
                        subParamSelect.addEventListener('change', function() {
                            const selectedParam = this.value;
                            if (selectedParam) {
                                const unit = testParameters[selectedParam];
                                updateInputWithUnit(input, unit);
                                input.dataset.parameter = selectedParam;
                            } else {
                                input.placeholder = 'Select parameter first';
                                input.disabled = true;
                            }
                        });
                        
                        input.placeholder = 'Select parameter first';
                        input.disabled = true;
                    } else {
                        // Single parameter test - get the only key/value pair
                        const param = Object.keys(testParameters)[0];
                        const unit = testParameters[param];
                        updateInputWithUnit(input, unit);
                        input.dataset.parameter = param;
                    }
                } else {
                    // Fallback for undefined test
                    input.placeholder = `Enter ${selectedTest} result`;
                }
                
                input.focus();
            } else {
                // No test selected
                input.disabled = true;
                input.value = '';
                input.placeholder = 'Enter test result';
                
                // Remove sub-parameter selector if any
                const existingSubParam = select.parentElement.querySelector('.sub-parameter');
                if (existingSubParam) {
                    select.parentElement.removeChild(existingSubParam);
                }
            }
        }
        
        // Function to update input with appropriate unit
        function updateInputWithUnit(input, unit) {
            // Clear existing unit display if any
            const existingUnitDisplay = input.parentElement.querySelector('.unit-display');
            if (existingUnitDisplay) {
                input.parentElement.removeChild(existingUnitDisplay);
            }
            
            // Set up input based on unit
            if (unit) {
                input.placeholder = `Enter value`;
                
                // Create unit display element
                const unitDisplay = document.createElement('span');
                unitDisplay.className = 'unit-display';
                unitDisplay.textContent = unit;
                unitDisplay.style.padding = '8px';
                unitDisplay.style.backgroundColor = '#f0f0f0';
                unitDisplay.style.border = '1px solid #ced4da';
                unitDisplay.style.borderRadius = '0 4px 4px 0';
                unitDisplay.style.marginLeft = '-1px';
                
                // Position input and unit display in a container
                const inputContainer = document.createElement('div');
                inputContainer.style.display = 'flex';
                inputContainer.style.flex = '1';
                
                // Move input into the container
                input.parentNode.insertBefore(inputContainer, input);
                inputContainer.appendChild(input);
                inputContainer.appendChild(unitDisplay);
                
                // Adjust input style
                input.style.borderRadius = '4px 0 0 4px';
                input.style.flex = '1';
                
                // Set validation based on unit type
                if (unit.includes('%')) {
                    input.type = 'number';
                    input.min = '0';
                    input.max = '100';
                    input.step = '0.1';
                } else if (unit.includes('kPa') || unit.includes('kN/m') || unit.includes('m') || unit.includes('mm')) {
                    input.type = 'number';
                    input.min = '0';
                    input.step = '0.01';
                } else if (unit.includes('degrees')) {
                    input.type = 'number';
                    input.min = '0';
                    input.max = '90';
                    input.step = '0.1';
                } else if (unit.includes('blows')) {
                    input.type = 'number';
                    input.min = '0';
                    input.step = '1';
                } else {
                    // Default for other units
                    input.type = 'number';
                    input.step = '0.01';
                }
            } else {
                // No unit (like void ratio)
                input.placeholder = 'Enter value (no unit)';
                input.type = 'number';
                input.step = '0.01';
            }
        }

        // Initialize in-situ conditions section
        const inSituContainer = document.getElementById('in-situ-conditions-container');
        if (inSituContainer) {
            // Add listener to existing select
            const existingSelect = inSituContainer.querySelector('select.in-situ-condition');
            const existingInput = inSituContainer.querySelector('input.in-situ-value');
            
            existingSelect.addEventListener('change', function() {
                updateInputField(this, existingInput);
            });
            
            // Handle add button click
            const addInSituButton = document.getElementById('add-in-situ-test');
            if (addInSituButton) {
                addInSituButton.addEventListener('click', function() {
                    const entryDiv = document.createElement('div');
                    entryDiv.className = 'in-situ-entry';
                    entryDiv.style.display = 'flex';
                    entryDiv.style.gap = '10px';
                    entryDiv.style.marginBottom = '10px';
                    
                    const newSelect = existingSelect.cloneNode(true);
                    newSelect.value = '';
                    
                    const newInput = document.createElement('input');
                    newInput.type = 'text';
                    newInput.className = 'in-situ-value';
                    newInput.name = 'in_situ_values[]';
                    newInput.placeholder = 'Enter test result';
                    newInput.disabled = true;
                    newInput.style.flex = '1';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-test';
                    removeBtn.innerHTML = '✕';
                    removeBtn.style.background = '#dc3545';
                    removeBtn.style.padding = '0 10px';
                    removeBtn.style.color = 'white';
                    removeBtn.style.border = 'none';
                    removeBtn.style.borderRadius = '5px';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.style.display = 'block';
                    
                    entryDiv.appendChild(newSelect);
                    entryDiv.appendChild(newInput);
                    entryDiv.appendChild(removeBtn);
                    inSituContainer.appendChild(entryDiv);
                    
                    // Add change listener to the new select
                    newSelect.addEventListener('change', function() {
                        updateInputField(this, newInput);
                    });
                    
                    // Show remove button on existing entries
                    document.querySelectorAll('.in-situ-entry .remove-test').forEach(btn => {
                        btn.style.display = 'block';
                    });
                });
            }
            
            // Handle remove buttons
            inSituContainer.addEventListener('click', function(e) {
                if (e.target && e.target.className === 'remove-test') {
                    const entry = e.target.parentElement;
                    inSituContainer.removeChild(entry);
                    
                    // Hide remove button if only one entry remains
                    if (inSituContainer.querySelectorAll('.in-situ-entry').length <= 1) {
                        document.querySelectorAll('.in-situ-entry .remove-test').forEach(btn => {
                            btn.style.display = 'none';
                        });
                    }
                }
            });
        }

        // Initialize structural analysis section (using the existing code structure)
        const structuralContainer = document.getElementById('structural-analysis-container');
        if (structuralContainer) {
            document.querySelectorAll('.structural-analysis-entry select').forEach(select => {
                const input = select.parentElement.querySelector('input');
                
                // Replace the existing change listener
                select.addEventListener('change', function() {
                    updateInputField(this, input);
                    updateDropdownOptions();
                });
            });
            
            // The rest of the structural analysis code remains as is
            // But when adding new entries, use updateInputField
            const addStructuralButton = document.getElementById('add-structural-test');
            if (addStructuralButton) {
                const originalAddClick = addStructuralButton.onclick;
                addStructuralButton.onclick = null;
                
                addStructuralButton.addEventListener('click', function() {
                    const firstSelect = structuralContainer.querySelector('select');
                    
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
                        structuralContainer.removeChild(entryDiv);
                        updateAddButtonVisibility();
                        updateDropdownOptions();
                    });
                    
                    entryDiv.appendChild(newSelect);
                    entryDiv.appendChild(newInput);
                    entryDiv.appendChild(removeBtn);
                    structuralContainer.appendChild(entryDiv);
                    
                    // Add change listener to the new select
                    newSelect.addEventListener('change', function() {
                        updateInputField(this, newInput);
                        updateDropdownOptions();
                    });
                    
                    updateAddButtonVisibility();
                    updateDropdownOptions();
                });
            }
        }

        // Keep the dropdown options update function as is
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

        // Keep the button visibility function as is
        function updateAddButtonVisibility() {
            const testCount = document.querySelectorAll('.structural-analysis-entry').length;
            const addButton = document.getElementById('add-structural-test');
            
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
                        'Backfill Soil' => ['Soil creep', 'Tension cracks', 'Landslide', 'Bulges'],
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
    <script src=""></script>
    </body>
    </html>


