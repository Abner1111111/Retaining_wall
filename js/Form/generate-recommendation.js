function generateRecommendations(failureTypes, causeOfFailure, indicators) {
    const recommendations = {
        remediationMethod: {
            diagnosis1: [],
            diagnosis2: [],
            diagnosis3: [],
        },
        supportingLabTests: []
    };


    if (indicators.length === 0) {
        recommendations.remediationMethod.diagnosis1.push('No need of Remediation');
    } else if (indicators.includes('Collapse of upper-height') || 
               indicators.includes('Displacement of entire structure')) {
        recommendations.remediationMethod.diagnosis3.push('Wall Replacement');
    } else {

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
