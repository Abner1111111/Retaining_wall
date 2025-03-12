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
