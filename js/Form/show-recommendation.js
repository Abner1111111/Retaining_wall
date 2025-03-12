
function showRecommendations(failureTypes, causeOfFailure, indicators) {
    const modal = document.getElementById('recommendationModal');
    const content = document.getElementById('recommendationContent');
    

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
        const recommendations = generateRecommendations(failureTypes, causeOfFailure, indicators);
 
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

    const closeBtn = document.querySelector('.close-modal');
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}