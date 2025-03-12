
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
            explanation: 'Early signs of wear  detected. Preventive maintenance recommended.'
        };
    }

    return {
        severity: 'none',
        diagnosis: 'Good Condition',
        explanation: 'No significant issues detected.'
    };
}
