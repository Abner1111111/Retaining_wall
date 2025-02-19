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
                <div id="clock" style="color: #f5f5f5; font-size: 18px; font-weight: bold;"></div>
            </div>
            <h1 style="color: #f5f5f5;">Retaining Wall Assessment Tool</h1>
            <p>Professional tool for diagnosing and resolving retaining wall issues</p>
            
        </div>
     <script> function updateClock() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        const formattedTime = `${hours}:${minutes}:${seconds}`;
        document.getElementById('clock').textContent = formattedTime;
    }
    
    setInterval(updateClock, 1000);
    updateClock();
    </script>
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

<script>
    const API_BASE_URL = 'https://psgc.gitlab.io/api';
        const REGION_12_CODE = '120000000';
        async function fetchFromAPI(url) {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('API Response:', url, data); 
                return data;
            } catch (error) {
                console.error('Error fetching data:', url, error);
                return [];
            }
        }

        async function initializeProvinces() {
            const provinceSelect = document.getElementById('province');
            console.log('Fetching provinces for Region 12...'); 
            

            const provinces = await fetchFromAPI(`${API_BASE_URL}/regions/${REGION_12_CODE}/provinces`);
            
            if (provinces.length > 0) {
                provinces.sort((a, b) => a.name.localeCompare(b.name)).forEach(province => {
                    provinceSelect.add(new Option(province.name, province.code));
                });
                console.log('Provinces loaded:', provinces.length); 
            } else {
                console.error('No provinces found for Region 12');
            }
        }

        document.getElementById('province').addEventListener('change', async function() {
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');
            
            citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            
            if (this.value) {
                citySelect.disabled = false;
                const cities = await fetchFromAPI(`${API_BASE_URL}/provinces/${this.value}/cities-municipalities`);
                cities.sort((a, b) => a.name.localeCompare(b.name)).forEach(city => {
                    citySelect.add(new Option(city.name, city.code));
                });
            } else {
                citySelect.disabled = true;
                barangaySelect.disabled = true;
            }
        });

        document.getElementById('city').addEventListener('change', async function() {
            const barangaySelect = document.getElementById('barangay');
            
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            
            if (this.value) {
                barangaySelect.disabled = false;
                const barangays = await fetchFromAPI(`${API_BASE_URL}/cities-municipalities/${this.value}/barangays`);
                barangays.sort((a, b) => a.name.localeCompare(b.name)).forEach(barangay => {
                    barangaySelect.add(new Option(barangay.name, barangay.code));
                });
            } else {
                barangaySelect.disabled = true;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            initializeProvinces();
        });

</script>
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
                    <input type="text" id="in_situ_conditions" name="in_situ_conditions"  placeholder="Input Lab Result" required >
        <!-- <label for="in_situ_conditions">In-Situ Conditions</label>
        <select id="in_situ_conditions" name="in_situ_conditions" required>
            <option value="">-- Select Condition --</option>
            <option value="Poor soil compaction">Poor soil compaction</option>
            <option value="Uneven soil compaction">Uneven soil compaction</option>
            <option value="Slope greater than the Angle of Repose">Slope greater than the Angle of Repose</option>
            <option value="High Unit Weight">High Unit Weight</option>
            <option value="Low Density">Low Density</option>
            <option value="High Cohesion">High Cohesion</option>
            <option value="Low Cohesion">Low Cohesion</option>
            <option value="High Void Ratio">High Void Ratio</option>
            <option value="High Moisture Content">High Moisture Content</option>
            <option value="High Plasticity Index">High Plasticity Index</option>
            <option value="High Liquid Limit">High Liquid Limit</option>
            <option value="High Capillarity">High Capillarity</option>
            <option value="High Consolidation Potential">High Consolidation Potential</option>
            <option value="High Compressibility/Compression Index">High Compressibility/Compression Index</option>
            <option value="High Proportion of Fine-Grained Soil">High Proportion of Fine-Grained Soil</option>
            <option value="Low Permeability">Low Permeability</option>
            <option value="Low Shear Strength">Low Shear Strength</option>
            <option value="Low Shear Strength for cohesive soil">Low Shear Strength for cohesive soil</option>
            <option value="Low Angle of Internal Friction">Low Angle of Internal Friction</option>
            <option value="Low Shear Resistance">Low Shear Resistance</option>
            <option value="Low Cone Resistance">Low Cone Resistance</option>
            <option value="Low Soil Resistance to Penetration">Low Soil Resistance to Penetration</option>
            <option value="High Surcharge Load">High Surcharge Load</option>
            <option value="High Water Table">High Water Table</option>
        </select> -->
    </div>  
    <div class="form-group">
    <label for="structural_analysis">Structural Analysis</label>
    <input type="text" id="structural_analysis" name="structural_analysis"  placeholder="Input Lab Result" required >
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

        <script>
           document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('assessmentForm');
            const submitButton = document.getElementById('submitButton');
            const resultContent = document.getElementById('resultContent');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                submitButton.disabled = true;
                submitButton.innerText = "Submitting...";
                
                try {
                    const formData = new FormData(form);
                    const checkedIndicators = Array.from(document.querySelectorAll('input[name="test[]"]:checked'))
                        .map(checkbox => checkbox.value);
                    const checkedMethods = Array.from(document.querySelectorAll('input[name="analysis[]"]:checked'))
                        .map(checkbox => checkbox.value);
                    checkedIndicators.forEach(indicator => {
                        formData.append('test[]', indicator);
                    });
                    
                    checkedMethods.forEach(method => {
                        formData.append('analysis[]', method);
                    });

                    const response = await fetch('save-assessment.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    
                    if (result.success) {
                        document.getElementById('results').style.display = 'block';
                        const addressParts = [
                            formData.get('street_address'),
                            document.querySelector('#barangay option:checked')?.text,
                            document.querySelector('#city option:checked')?.text,
                            document.querySelector('#province option:checked')?.text
                        ].filter(Boolean);
                        
                        const addressString = addressParts.join(', ');
                        resultContent.innerHTML = `
                            <div class="success-message">Assessment saved successfully!</div>
                            <div class="assessment-results">
                                <h3>Assessment Summary</h3>
                                
                                <div class="summary-section">
                                    <h4>Project Information</h4>
                                    <p><strong>Structure Name:</strong> ${formData.get('Name') || 'Not specified'}</p>
                                    <p><strong>Contract ID:</strong> ${formData.get('ContractID') || 'Not specified'}</p>
                                    <p><strong>Date of Inspection:</strong> ${formData.get('Date') || 'Not specified'}</p>
                                    <p><strong>Date of Construction:</strong> ${formData.get('ConstructionDate') || 'Not specified'}</p>
                                    <p><strong>Location:</strong> ${addressString}</p>
                                </div>

                                <div class="summary-section">
                                    <h4>Wall Details</h4>
                                    <p><strong>Type of Design:</strong> ${formData.get('Type_of_Design') || 'Not specified'}</p>
                                    <p><strong>Type of Material:</strong> ${formData.get('Type_of_Material') || 'Not specified'}</p>
                                </div>

                                <div class="summary-section">
                                    <h4>Analysis Results</h4>
                                    <p><strong>Structural Analysis:</strong> ${formData.get('structural_analysis') || 'Not specified'}</p>
                                    <p><strong>In-Situ Conditions:</strong> ${formData.get('in_situ_conditions') || 'Not specified'}</p>
                                    <p><strong>Visual Indicators:</strong> ${checkedIndicators.join(', ') || 'None selected'}</p>
                                    <p><strong>Analysis Methods:</strong> ${checkedMethods.join(', ') || 'None selected'}</p>
                                </div>

                                ${result.results ? `
                                    <div class="severity-indicator ${(result.results.severity_level || '').toLowerCase()}">
                                        Severity Level: ${result.results.severity_level || 'Not specified'}
                                    </div>

                                    <div class="summary-section">
                                        <h4>Diagnosis</h4>
                                        <p>${result.results.condition_diagnosis || 'No diagnosis available'}</p>
                                    </div>

                                    <div class="summary-section">
                                        <h4>Recommendations</h4>
                                        <p>${result.results.recommendations || 'No recommendations available'}</p>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                        document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
                    } else {
                        throw new Error(result.error || 'Unknown error occurred');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    resultContent.innerHTML = `
                        <div class="error-message">
                            Error saving assessment: ${error.message}
                        </div>
                    `;
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerText = "Submit Assessment";
                }
            });
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


