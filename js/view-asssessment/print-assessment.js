async function printAssessment(id) {
    let printWindow = null;
    
    try {
    const response = await fetch(`get-assessment.php?id=${id}`);
    if (!response.ok) throw new Error(`Failed to fetch assessment data: ${response.status}`);
    const data = await response.json();
    const assessmentData = data.data || data;
    
    const recommendationsResponse = await fetch(`get-recommendations.php?assessment_id=${id}`);
    if (!recommendationsResponse.ok) throw new Error(`Failed to fetch recommendations: ${recommendationsResponse.status}`);
    const recommendationsData = await recommendationsResponse.json();
    const recommendations = recommendationsData.data || [];
    
    const labTestsResponse = await fetch(`get-lab-tests.php?assessment_id=${id}`);
    if (!labTestsResponse.ok) throw new Error(`Failed to fetch lab tests: ${labTestsResponse.status}`);
    const labTestsData = await labTestsResponse.json();
    const labTests = labTestsData.data || [];
    
    printWindow = window.open('', '_blank');
    if (!printWindow) throw new Error('Failed to open print window. Please check if pop-ups are blocked.');
    
    let provinceName = 'N/A';
    let cityName = 'N/A';
    let barangayName = 'N/A';
    
    if (assessmentData.province) {
        try {
            provinceName = await getLocationNameByCode('provinces', assessmentData.province);
        } catch (e) {
            console.error('Error fetching province name:', e);
        }
    }
    
    if (assessmentData.city) {
        try {
            cityName = await getLocationNameByCode('cities-municipalities', assessmentData.city);
        } catch (e) {
            console.error('Error fetching city name:', e);
        }
    }
    
    if (assessmentData.barangay) {
        try {
            barangayName = await getLocationNameByCode('barangays', assessmentData.barangay);
        } catch (e) {
            console.error('Error fetching barangay name:', e);
        }
    }
    
    const locationString = [
        assessmentData.street_address, 
        barangayName, 
        cityName, 
        provinceName
    ].filter(item => item && item !== 'N/A').join(', ');
    
    let age = 'N/A';
    if (assessmentData.date_of_construction) {
        try {
            age = calculateDetailedAge(assessmentData.date_of_construction);
        } catch (e) {
            console.error('Error calculating age:', e);
        }
    }
    
    let failureTypesHTML = 'None recorded';
    if (assessmentData.failure_types) {
        try {
            const failureTypesArray = typeof assessmentData.failure_types === 'string' 
                ? JSON.parse(assessmentData.failure_types) 
                : assessmentData.failure_types;
                
            if (failureTypesArray && Array.isArray(failureTypesArray) && failureTypesArray.length > 0) {
                failureTypesHTML = failureTypesArray.join(', ');
            }
        } catch (e) {
            console.error('Error parsing failure types:', e);
            failureTypesHTML = String(assessmentData.failure_types);
        }
    }
    
    let visualIndicatorsHTML = 'None recorded';
    if (assessmentData.visual_indicators) {
        try {
            const indicatorsArray = typeof assessmentData.visual_indicators === 'string' 
                ? JSON.parse(assessmentData.visual_indicators) 
                : assessmentData.visual_indicators;
                
            if (indicatorsArray && Array.isArray(indicatorsArray) && indicatorsArray.length > 0) {
                visualIndicatorsHTML = indicatorsArray.join(', ');
            }
        } catch (e) {
            console.error('Error parsing visual indicators:', e);
            visualIndicatorsHTML = String(assessmentData.visual_indicators);
        }
    }
    
        
    let inSituConditionsHTML = '<p>No in-situ conditions available</p>';
    if (assessmentData.in_situ_conditions && assessmentData.in_situ_conditions.length > 0) {
        inSituConditionsHTML = '<table width="100%" border="1" cellspacing="0" cellpadding="5">';
        inSituConditionsHTML += `
            <tr>
                <th style="background-color: #f2f2f2;">Condition Type</th>
                <th style="background-color: #f2f2f2;">Test Result</th>
            </tr>
        `;
        
        assessmentData.in_situ_conditions.forEach(condition => {
            inSituConditionsHTML += `
                <tr>
                    <td>${condition.condition_type || 'N/A'}</td>
                    <td>${condition.test_result || 'N/A'}</td>
                </tr>
            `;
        });
        
        inSituConditionsHTML += '</table>';
    }
    
    let structuralAnalysisHTML = '<p>No structural analysis available</p>';
    if (assessmentData.structural_analysis && assessmentData.structural_analysis.length > 0) {
        structuralAnalysisHTML = '<table width="100%" border="1" cellspacing="0" cellpadding="5">';
        structuralAnalysisHTML += `
            <tr>
                <th style="background-color: #f2f2f2;">Analysis Type</th>
                <th style="background-color: #f2f2f2;">Test Result</th>
            </tr>
        `;
        
        assessmentData.structural_analysis.forEach(analysis => {
            structuralAnalysisHTML += `
                <tr>
                    <td>${analysis.analysis_type || 'N/A'}</td>
                    <td>${analysis.test_result || 'N/A'}</td>
                </tr>
            `;
        });
        
        structuralAnalysisHTML += '</table>';
    }
    try {
    
        const inSituTestsResponse = await fetch(`get-in-situ-tests.php?assessment_id=${id}`);
        if (inSituTestsResponse.ok) {
            const inSituTestsData = await inSituTestsResponse.json();
            const inSituTests = inSituTestsData.data || [];
            
            if (inSituTests && Array.isArray(inSituTests) && inSituTests.length > 0) {
                inSituConditionsHTML = inSituTests.map(test => `
                    <tr>
                        <td>${test.id || 'N/A'}</td>
                        <td>${test.assessment_id || id}</td>
                        <td>${test.condition_type || 'N/A'}</td>
                        <td>${test.test_result || 'N/A'}</td>
                    </tr>
                `).join('');
            }
        }
    } catch (e) {
        console.error('Error fetching in-situ tests:', e);
     
    }
    
    let recommendationsHTML = '<p>No recommendations available</p>';
    if (recommendations && Array.isArray(recommendations) && recommendations.length > 0) {
        recommendationsHTML = '<ul style="margin-top: 2mm; padding-left: 5mm;">';
        recommendations.forEach(rec => {
            recommendationsHTML += `
                <li style="margin-bottom: 2mm;">
                    ${rec.remediation_method || 'No specific method provided'}
                </li>
            `;
        });
        recommendationsHTML += '</ul>';
    }
    
    
    let labTestsHTML = '<p>No laboratory tests recommended</p>';
    if (labTests && Array.isArray(labTests) && labTests.length > 0) {
        labTestsHTML = '<ul style="margin-top: 2mm; padding-left: 5mm;">';
        labTests.forEach(test => {
            labTestsHTML += `<li style="margin-bottom: 1mm;">${test.test_name || 'Unnamed test'}</li>`;
        });
        labTestsHTML += '</ul>';
    }
    
    const currentDate = new Date();
    const reportNumber = `${currentDate.getFullYear()}-RTW-${(assessmentData.id || id).toString().padStart(3, '0')}`;
    
    printWindow.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assessment Report - ${assessmentData.structure_name || 'Report'}</title>
        <style>
            /* A4 paper dimensions: 210mm × 297mm */
            @page {
                size: A4;
                margin: 1cm;
            }
            
            @media print {
                html, body {
                    width: 210mm;
                    height: 297mm;
                    margin: 0;
                    padding: 0;
                }
                
                body {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }
                
                .no-break {
                    page-break-inside: avoid;
                }
                
                .page-break {
                    page-break-before: always;
                }
                
                /* Add footer to each printed page */
                @page {
                    @bottom-right {
                        content: element(footer);
                    }
                }
                    #footer {
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    width: 100%;
                    border-top: 0.5px solid #eee;
                    padding-top: 3mm;
                    background-color: white;
                    font-size: 8pt;
                    color: #666;
                    text-align: right;
                }
                
              
                .content-wrapper {
                    margin-bottom: 20mm;
                }
            }
                
        
            
            body {
                font-family: Arial, sans-serif;
                color: #333;
                line-height: 1.4;
                font-size: 11pt;
                padding: 10mm;
                box-sizing: border-box;
                max-width: 190mm; 
                margin: 0 auto;
            }
            
            .header {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 15mm;
                page-break-inside: avoid;
                text-align: center;
            }
            
            .logo {
                width: 20mm;
                height: 20mm;
                margin-right: 10mm;
            }
            
            .university-info {
                text-align: center;
            }
            
            .university-info p {
                margin: 0;
                font-size: 10pt;
            }
    
    
            .report-title {
                font-size: 14pt;
                font-weight: bold;
                text-align: center;
                margin: 15mm 0;
                page-break-after: avoid;
            }
                    
            .assessment-details {
                margin-top: 10mm;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            .section-title {
                font-weight: bold;
                border-bottom: 1px solid #ccc;
                padding-bottom: 2mm;
                margin-bottom: 5mm;
                page-break-after: avoid;
                font-size: 12pt;
            }
            
            .info-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 5mm;
            }
            
            .info-item {
                margin-bottom: 3mm;
            }
            
            .info-item strong {
                display: block;
                font-weight: bold;
                color: #555;
                font-size: 10pt;
            }
            
            .severity-indicator {
                display: inline-block;
                padding: 1mm 2mm;
                border-radius: 1mm;
                font-weight: bold;
                font-size: 10pt;
            }
            
            .high {
                background-color: #ffeeee;
                color: #cc0000;
                border: 0.5px solid #cc0000;
            }
            
            .medium {
                background-color: #fff6e6;
                color: #cc7700;
                border: 0.5px solid #cc7700;
            }
            
            .low {
                background-color: #e6ffe6;
                color: #007700;
                border: 0.5px solid #007700;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 5mm;
            }
            
            table th, table td {
                border: 1px solid #ddd;
                padding: 2mm;
                text-align: left;
                font-size: 10pt;
            }
            
            table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            
            table tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            
            #footer {
                font-size: 8pt;
                color: #666;
                text-align: right;
                border-top: 0.5px solid #eee;
                padding-top: 3mm;
                margin-top: 10mm;
            }
            
            #footer p {
                margin: 1mm 0;
            }  
        </style>
    </head>
    <body>
        <div class="content-wrapper">
            <div class="header">
                <img src="pictures/school logo.png" alt="University Logo" class="logo">
                <div class="university-info">
                    <p style="font-weight: bold;">BACHELOR OF SCIENCE IN CIVIL ENGINEERING</p>
                    <p>College of Engineering, Architecture and Technology</p>
                    <p>Notre Dame of Dadiangas University</p>
                </div>
            </div>
            
            <div class="report-title">
                Retaining Wall Failure Assessment and Recommendation Report
            </div>
    
            <div class="assessment-details">
                <div class="section no-break">
                    <div class="section-title">Basic Information</div>
                    <div class="info-grid">
                        <div class="info-item" style: left 2px;>
                            <strong>Structure Name:</strong>
                            ${assessmentData.structure_name || 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Contract ID:</strong>
                            ${assessmentData.contract_id || 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Construction Date:</strong>
                            ${assessmentData.date_of_construction ? new Date(assessmentData.date_of_construction).toLocaleDateString() : 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Age:</strong>
                            ${age}
                        </div>
                        <div class="info-item">
                            <strong>Inspection Date:</strong>
                            ${assessmentData.date_of_inspection ? new Date(assessmentData.date_of_inspection).toLocaleDateString() : 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Location:</strong>
                            ${locationString || 'N/A'}
                        </div>
                    </div>
                </div>
    
                <div class="section no-break">
                    <div class="section-title">Structure Details</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Type of Design:</strong>
                            ${assessmentData.type_of_design || 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Material:</strong>
                            ${assessmentData.type_of_material || 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Height:</strong>
                            ${assessmentData.height ? assessmentData.height + ' m' : 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Base Width:</strong>
                            ${assessmentData.base ? assessmentData.base + ' m' : 'N/A'}
                        </div>
                    </div>
                </div>
    
                <div class="section no-break">
                    <div class="section-title">Visual Indicators</div>
                    <div class="info-item">
                        ${visualIndicatorsHTML}
                    </div>
                </div>
                <div class="section no-break">
                    <div class="section-title">In-Situ Conditions</div>
                    ${inSituConditionsHTML}
                </div>
    
                <div class="section no-break">
                    <div class="section-title">Structural Analysis</div>
                    ${structuralAnalysisHTML}
                </div>
    
                <div class="section no-break">
                    <div class="section-title">Assessment Results</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Severity Level:</strong>
                            <div class="severity-indicator ${assessmentData.severity ? assessmentData.severity.toLowerCase() : 'unknown'}">
                                ${assessmentData.severity || 'Unknown'}
                            </div>
                        </div>
                        <div class="info-item">
                            <strong>Condition Diagnosis:</strong>
                            ${assessmentData.condition_diagnosis || 'N/A'}
                        </div>
                        <div class="info-item">
                            <strong>Failure Types:</strong>
                            ${failureTypesHTML}
                        </div>
                        <div class="info-item">
                            <strong>Cause of Failure:</strong>
                            ${assessmentData.cause_of_failure || 'Not specified'}
                        </div>
                    </div>
                    <div class="info-item" style="margin-top: 5mm;">
                        <strong>Explanation:</strong>
                        <p>${assessmentData.explanation || 'No detailed explanation available'}</p>
                    </div>
                </div>
    
                <div class="section no-break">
                    <div class="section-title">Recommendations</div>
                    <div class="info-item">
                        ${recommendationsHTML}
                    </div>
                </div>
    
                <div class="section no-break">
                    <div class="section-title">Recommended Laboratory Tests</div>
                    <div class="info-item">
                        ${labTestsHTML}
                    </div>
                </div>
            </div>
        </div>
    
        <div id="footer">
            <p>Report No: ${reportNumber}</p>
            <p>Generated by: Thesis: Retaining Wall Assessment and Recommendation Tool</p>
            <p>The tool that generated this report is an undergraduate thesis titled: "Development of a Failure Assessment and Recommendation Tool for Retaining Walls"</p>
        </div>
    
    </body>
    </html>
            `);
    
            printWindow.document.close();
            printWindow.focus();
    
            setTimeout(() => {
                try {
                    printWindow.print();
                } catch (e) {
                    console.error('Print dialog error:', e);
                    alert('Error in print dialog. Please try printing manually from the opened window.');
                }
            }, 2000);
            
        } catch (error) {
            console.error('Error printing assessment:', error);
            
            if (printWindow) {
                try {
                    printWindow.close();
                } catch (e) {
                    console.error('Error closing print window:', e);
                }
            }
            
            alert(`Error printing assessment: ${error.message}. Please try again.`);
        }
    }