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