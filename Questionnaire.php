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
       <Style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Roboto', sans-serif;
    line-height: 1.6;
    background-color: #f5f5f5;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.header {
    background-color: #73877b;
    color: white;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}


.user-info {
    float: right;
    font-size: 0.9em;
}

.section {
    background: white;
    padding: 25px;
    margin-bottom: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

h1, h2, h3 {
    color: #333;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

input[type="text"],
input[type="number"],
textarea {
    width: 100%;
    padding: 10px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 14px;
}

textarea {
    height: 100px;
    resize: vertical;
}

.checkbox-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.category {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #e1e1e1;
}

.category h3 {
    color: #73877b;
    font-size: 1.1em;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #73877b;
}

.checkbox-item {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.checkbox-item:hover {
    background: #e9ecef;
}

.checkbox-item input[type="checkbox"] {
    margin-right: 12px;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-item input[type="checkbox"]:checked + label {
    color: #73877b;
    font-weight: 500;
}

.checkbox-item label {
    cursor: pointer;
    font-size: 0.95em;
    margin-bottom: 0;
    flex: 1;
}


.checkbox-item input[type="checkbox"] {
    -webkit-appearance: none;
    appearance: none;
    background-color: #fff;
    border: 2px solid #73877b;
    border-radius: 4px;
    width: 20px;
    height: 20px;
    position: relative;
    transition: all 0.2s ease;
    
}

.checkbox-item input[type="checkbox"]:checked {
    background-color: #73877b;
}

.checkbox-item input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 14px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
button {
    background: #73877b;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
}

button:hover {
    background: #5a6e62;
    transform: translateY(-1px);
}

.results {
    display: none;
    margin-top: 30px;
}

.error-message {
    color: #dc3545;
    padding: 10px;
    margin: 10px 0;
    background: #ffe6e6;
    border-radius: 5px;
}

.success-message {
    color: #28a745;
    padding: 10px;
    margin: 10px 0;
    background: #e6ffe6;
    border-radius: 5px;
}
.location-fields {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.location-field {
    margin-bottom: 15px;
}

.location-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.location-field input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;

    border-radius: 4px;
}
.address-section {
    margin: 20px 0;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.address-section h3 {
    color: #73877b;
    font-size: 1.1em;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #73877b;
}

.address-section .form-group {
    margin-bottom: 15px;
}

.address-section select,
.address-section input[type="text"] {
    width: 100%;
    padding: 10px;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 14px;
    background: white;
}

.address-section select:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.address-section select:valid,
.address-section input[type="text"]:valid {
    border-color: #28a745;
}
.coordinates-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.coordinates-group input {
    width: 100%;
}

input:valid {
    border-color: #28a745;
}
select:valid {
    border-color: #28a745;
}
select {
    width: 100%;
    padding: 10px;
    border: 2px solid #e1e1e1;
    color: #5e5e5e;
    border-radius: 5px;
    font-size: 14px;
    background: white;
}

.assessment-results {
    margin-top: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e1e1e1;
}

.assessment-results h3 {
    color: #333;
    margin-bottom: 15px;
}

.assessment-results h4 {
    color: #73877b;
    margin: 15px 0 8px 0;
}

.severity-indicator {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 4px;
    color: white;
    font-weight: 500;
    margin: 10px 0;
}

.severity-indicator.high {
    background-color: #dc3545;
}

.severity-indicator.medium {
    background-color: #ffc107;
    color: #000;
}

.severity-indicator.low {
    background-color: #28a745;
}

.assessment-results p {
    margin: 8px 0;
    line-height: 1.6;
}


        @media (max-width: 768px) {
            .checkbox-group {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .checkbox-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .category {
                padding: 15px;
            }
        }
       </Style>
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

                    <form id="assessmentForm" method="POST" action="save-assessment.php">
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
        <div id="formatRecommendations" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Recommendations</h2>
        <div id="formatRecommendations"></div>
    </div>
</div>

        <script>
   document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const form = document.getElementById('assessmentForm');
    const submitButton = document.getElementById('submitButton');
    const results = document.getElementById('results');
    const resultContent = document.getElementById('resultContent');

    // Add styles
    const styles = `
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close-modal:hover {
            color: #333;
        }

        .recommendations-container {
            margin-top: 20px;
        }

        .recommendation-section {
            margin-bottom: 30px;
        }

        .priority-high,
        .priority-medium,
        .priority-low {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
        }

        .priority-high {
            background-color: #ffe6e6;
            border-left: 4px solid #dc3545;
        }

        .priority-medium {
            background-color: #fff3e6;
            border-left: 4px solid #fd7e14;
        }

        .priority-low {
            background-color: #e6ffe6;
            border-left: 4px solid #28a745;
        }

        .recommendation-section h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .recommendation-section h4 {
            margin-bottom: 10px;
        }

        .recommendation-section ul {
            list-style-type: none;
            padding-left: 0;
        }

        .recommendation-section li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .recommendation-section li:before {
            content: "•";
            position: absolute;
            left: 0;
            color: #73877b;
        }
    `;

    // Add styles to document
    const styleSheet = document.createElement('style');
    styleSheet.textContent = styles;
    document.head.appendChild(styleSheet);

    // Add modal HTML
    const modalHTML = `
        <div id="recommendationModal" class="modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h2>Recommendations</h2>
                <div id="recommendationContent"></div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);

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

    // Function to generate recommendations
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
        }

        return recommendations;
    }

    // Function to show recommendations modal
    function showRecommendations(failureTypes, causeOfFailure, indicators) {
        const modal = document.getElementById('recommendationModal');
        const content = document.getElementById('recommendationContent');
        const recommendations = generateRecommendations(failureTypes, causeOfFailure, indicators);
        
        let html = '<div class="recommendations-container">';
        
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
        html += '</div>';
        
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

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitButton.disabled = true;
        submitButton.innerText = "Processing...";
        
        try {
            // Collect all checked indicators
            const checkedIndicators = Array.from(document.querySelectorAll('input[name="test[]"]:checked'))
                .map(checkbox => checkbox.value);
            
            // Analyze the data
            const failureTypes = determineFailureTypes(checkedIndicators);
            const causeOfFailure = determineCauseOfFailure(checkedIndicators);
            const conditionDiagnosis = determineConditionDiagnosis(checkedIndicators, failureTypes);

            // Show results section
            results.style.display = 'block';
            
            // Generate results HTML
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

                    <button onclick="showRecommendations(${JSON.stringify(failureTypes)}, '${causeOfFailure}', ${JSON.stringify(checkedIndicators)})" class="button">
                        View Recommendations
                    </button>
                </div>
            `;

            // Scroll to results
            results.scrollIntoView({ behavior: 'smooth' });

        } catch (error) {
            console.error('Error:', error);
            resultContent.innerHTML = `
                <div class="error-message">
                    An error occurred while processing the assessment: ${error.message}
                </div>
            `;
        } finally {
            submitButton.disabled = false;
            submitButton.innerText = "Submit Assessment";
        }
    });

    // Make showRecommendations available globally
    window.showRecommendations = showRecommendations;
});


            const phase2 = document.getElementById('phase2');
            const assessmentText = document.getElementById('assessmentText');
            const conditionText = document.getElementById('conditionText');
            const failureText = document.getElementById('failureText');

            phase2.style.display = 'block';

            assessmentText.textContent = `The following issues were detected: ${selectedIssues.join(", ")}. These indicate potential structural and drainage problems.`;

       
            let condition = "";
            if (selectedIssues.includes('Cracks') || selectedIssues.includes('Seepage')) {
                condition += "The wall shows signs of wear and potential water infiltration. ";
            }
            if (selectedIssues.includes('Leaning') || selectedIssues.includes('Bulging')) {
                condition += "The wall might be experiencing significant structural instability. ";
            }

            conditionText.textContent = `Condition Diagnosis: ${condition}`;

            let failureTypes = [];
            if (selectedIssues.includes('Cracks')) failureTypes.push("Tensile failure");
            if (selectedIssues.includes('Leaning')) failureTypes.push("Overturning failure");
            if (selectedIssues.includes('Bulging')) failureTypes.push("Sliding failure");
            if (selectedIssues.includes('Seepage')) failureTypes.push("Hydrostatic pressure failure");

            failureText.textContent = `Type of Failure Identified: ${failureTypes.join(", ")}`;

            $(document).ready(function() {
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
        });

        
        </script>
    </body>
    </html>


