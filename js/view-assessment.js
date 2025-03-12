async function viewAssessment(id) {
try {
const response = await fetch(`get-assessment.php?id=${id}`);
const data = await response.json();
const assessmentData = data.data || data;

const recommendationsResponse = await fetch(`get-recommendations.php?assessment_id=${id}`);
const recommendationsData = await recommendationsResponse.json();
const recommendations = recommendationsData.data || [];

const labTestsResponse = await fetch(`get-lab-tests.php?assessment_id=${id}`);
const labTestsData = await labTestsResponse.json();
const labTests = labTestsData.data || [];

const modal = document.getElementById('assessmentModal');
const modalContent = document.getElementById('modalContent');

const severityClass = assessmentData.severity ? assessmentData.severity.toLowerCase() : 'unknown';


let age = 'N/A';
if (assessmentData.date_of_construction) {
    age = calculateDetailedAge(assessmentData.date_of_construction);
}

let visualIndicators = '<p>No visual indicators recorded</p>';
if (assessmentData.visual_indicators) {
    try {
      
           const indicatorsArray = typeof assessmentData.visual_indicators === 'string' 
            ? JSON.parse(assessmentData.visual_indicators) 
            : assessmentData.visual_indicators;
        
        if (indicatorsArray && indicatorsArray.length > 0) {
            visualIndicators = '<ul>';
            indicatorsArray.forEach(indicator => {
                visualIndicators += `<li>${indicator}</li>`;
            });
            visualIndicators += '</ul>';
        }
    } catch (e) {
        console.error('Error parsing visual indicators:', e);
        visualIndicators = `<p>${assessmentData.visual_indicators}</p>`;
    }
}

let failureTypes = '<p>No failure types recorded</p>';
if (assessmentData.failure_types) {
    try {
        const failureTypesArray = typeof assessmentData.failure_types === 'string' 
            ? JSON.parse(assessmentData.failure_types) 
            : assessmentData.failure_types;
        
        if (failureTypesArray && failureTypesArray.length > 0) {
            failureTypes = '<ul>';
            failureTypesArray.forEach(type => {
                failureTypes += `<li>${type}</li>`;
            });
            failureTypes += '</ul>';
        }
    } catch (e) {
        console.error('Error parsing failure types:', e);
        failureTypes = `<p>${assessmentData.failure_types}</p>`;
    }
}

let inSituConditionsTable = '<p>No in-situ conditions recorded</p>';
if (assessmentData.in_situ_conditions && assessmentData.in_situ_conditions.length > 0) {
    inSituConditionsTable = `
        <table class="assessment-table">
            <thead>
                <tr>
                    <th style="border-right: 1px solid #ccc; padding-right: 10px;">Condition Type</th>
                    <th style="padding-left: 10px;">Test Result</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    assessmentData.in_situ_conditions.forEach(condition => {
        inSituConditionsTable += `
            <tr>
                <td style="border-right: 1px solid #ccc; padding-right: 10px;">${condition.condition_type || 'N/A'}</td>
                <td style="padding-left: 10px;">${condition.test_result || 'N/A'}</td>
            </tr>
        `;
    });
    
    inSituConditionsTable += `
            </tbody>
        </table>
    `;
}


let structuralAnalysisTable = '<p>No structural analysis recorded</p>';
if (assessmentData.structural_analysis && assessmentData.structural_analysis.length > 0) {
    structuralAnalysisTable = `
        <table class="assessment-table">
            <thead>
                <tr>
                    <th style="border-right: 1px solid #ccc; padding-right: 10px;">Analysis Type</th>
                    <th style="padding-left: 10px;">Test Result</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    assessmentData.structural_analysis.forEach(analysis => {
        structuralAnalysisTable += `
            <tr>
                <td style="border-right: 1px solid #ccc; padding-right: 10px;">${analysis.analysis_type || 'N/A'}</td>
                <td style="padding-left: 10px;">${analysis.test_result || 'N/A'}</td>
            </tr>
        `;
    });

    structuralAnalysisTable += `
            </tbody>
        </table>
    `;


structuralAnalysisTable += `
            </tbody>
        </table>
    `;
}

let labTestsTable = '<p>No laboratory tests recommended</p>';
if (labTests && labTests.length > 0) {
    labTestsTable = `
        <table class="assessment-table">
            <thead>
                <tr>
                    <th>Test Name</th>
                   
                </tr>
            </thead>
            <tbody>
    `;
    
    labTests.forEach(test => {
        const testDate = test.created_at ? new Date(test.created_at).toLocaleDateString() : 'N/A';
        labTestsTable += `
            <tr>
                <td>${test.test_name || 'Unnamed test'}</td>
         
            </tr>
        `;
    });
    
    labTestsTable += `
            </tbody>
        </table>
    `;
}

let recommendationsHTML = '<p>No recommendations available</p>';
if (recommendations && recommendations.length > 0) {
    recommendationsHTML = '<ul class="recommendations-list">';
    recommendations.forEach(rec => {
        recommendationsHTML += `
            <li class="recommendation-item">
                <div class="recommendation-method">${rec.remediation_method || 'No specific method provided'}</div>
            </li>
        `;
    });
    recommendationsHTML += '</ul>';
}
modalContent.innerHTML = `
    <div class="modal-header">
        <h2>Assessment Details</h2>
        <button class="print-button" onclick="printAssessment(${id})">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
    
    <div class="assessment-section">
        <h3>Basic Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Structure Name</strong>
                ${assessmentData.structure_name || 'N/A'}
            </div>
            <div class="info-item">
                <strong>Contract ID</strong>
                ${assessmentData.contract_id || 'N/A'}
            </div>
            <div class="info-item">
                <strong>Construction Date</strong>
                ${assessmentData.date_of_construction ? new Date(assessmentData.date_of_construction).toLocaleDateString() : 'N/A'}
            </div>
            <div class="info-item">
                <strong>Age</strong>
                ${age}
            </div>
            <div class="info-item">
                <strong>Inspection Date</strong>
                ${assessmentData.date_of_inspection ? new Date(assessmentData.date_of_inspection).toLocaleDateString() : 'N/A'}
            </div>
            <div class="info-item" id="location-info">
                <strong>Location</strong>
                <span id="location-placeholder">Loading location data...</span>
            </div>
        </div>
    </div>
    
    <div class="assessment-section">
        <h3>Structure Details</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Type of Design</strong>
                ${assessmentData.type_of_design || 'N/A'}
            </div>
            <div class="info-item">
                <strong>Material</strong>
                ${assessmentData.type_of_material || 'N/A'}
            </div>
            <div class="info-item">
                <strong>Height</strong>
                ${assessmentData.height ? assessmentData.height + ' m' : 'N/A'}
            </div>
            <div class="info-item">
                <strong>Base Width</strong>
                ${assessmentData.base ? assessmentData.base + ' m' : 'N/A'}
            </div>
        </div>
    </div>
    
    <div class="assessment-section">
        <h3>Visual Indicators</h3>
        ${visualIndicators}
    </div>
    
    <div class="assessment-section">
        <h3>In-Situ Conditions</h3>
        ${inSituConditionsTable}
    </div>
    
    <div class="assessment-section">
        <h3>Structural Analysis</h3>
        ${structuralAnalysisTable}
    </div>
    
    <div class="assessment-section">
        <h3>Assessment Results</h3>
        <div class="info-grid">
            <div class="info-item">
                <strong>Severity Level</strong>
                <div class="severity-indicator ${severityClass}">
                    ${assessmentData.severity || 'Unknown'}
                </div>
            </div>
           
            <div class="info-item">
                <strong>Condition Diagnosis</strong>
                ${assessmentData.condition_diagnosis || 'N/A'}
            </div>
        </div>
        
        <div class="info-item" style="margin-top: 20px;">
            <strong>Failure Types</strong>
            ${failureTypes}
        </div>
        <br>
        <div class="info-item">
            <strong>Cause of Failure</strong>
            <p>${assessmentData.cause_of_failure || 'Not specified'}</p>
        </div>
        <br>
        <div class="info-item">
            <strong>Explanation</strong>
            <p>${assessmentData.explanation || 'No detailed explanation available'}</p>
        </div>
    </div>
    
    <div class="assessment-section">
        <h3>Recommendations</h3>
        ${recommendationsHTML}
    </div>
    
    <div class="assessment-section">
        <h3>Recommended Laboratory Tests</h3>
        ${labTestsTable}
    </div>
`;

modal.style.display = 'block';
const locationPlaceholder = document.getElementById('location-placeholder');

let provinceName = 'N/A';
let cityName = 'N/A';
let barangayName = 'N/A';

if (assessmentData.province) {
    provinceName = await getLocationNameByCode('provinces', assessmentData.province);
}

if (assessmentData.city) {
    cityName = await getLocationNameByCode('cities-municipalities', assessmentData.city);
}

if (assessmentData.barangay) {
    barangayName = await getLocationNameByCode('barangays', assessmentData.barangay);
}

const locationString = [
    assessmentData.street_address, 
    barangayName, 
    cityName, 
    provinceName
].filter(item => item && item !== 'N/A').join(', ');

if (locationPlaceholder) {
    locationPlaceholder.textContent = locationString || 'N/A';
}

} catch (error) {
console.error('Error:', error);
alert('Error loading assessment details. Please try again.');
}
}

function closeModal() {
    document.getElementById('assessmentModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('assessmentModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

