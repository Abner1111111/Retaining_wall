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