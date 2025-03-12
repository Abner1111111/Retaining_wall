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

                fetch('save-assessment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayResults(checkedIndicators, failureTypes, causeOfFailure, conditionDiagnosis);
                        
                        const successAlert = document.createElement('div');
                        successAlert.className = 'success-alert';
                        successAlert.textContent = 'Assessment saved successfully!';
                        form.prepend(successAlert);
                        
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

    if (window.jQuery && $.fn.select2 && $('#wall_material').length) {
        $('#wall_material').select2({
            placeholder: "-- Select Material --",
            allowClear: true
        });

        const labTests = {
            "Poor soil compaction": "Proctor Compaction Test (PCT)",
            "Uneven soil compaction": "Field Density Test (FDT)",
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

    window.determineFailureTypes = determineFailureTypes;
    window.determineCauseOfFailure = determineCauseOfFailure;
    window.determineConditionDiagnosis = determineConditionDiagnosis;
    window.showRecommendations = showRecommendations;
    window.generateRecommendations = generateRecommendations;
    window.displayResults = displayResults;
});