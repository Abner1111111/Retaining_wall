function calculateDetailedAge(constructionDateString) {
    if (!constructionDateString) return 'N/A';
    
    const constructionDate = new Date(constructionDateString);
    const today = new Date();
    const diffTime = Math.abs(today - constructionDate);
    const diffDate = new Date(diffTime);
    
    const years = diffDate.getUTCFullYear() - 1970;
    

    const months = diffDate.getUTCMonth();
    const days = diffDate.getUTCDate() - 1;
    
   
    const age = [];
    
    if (years > 0) {
        age.push(years + ' year' + (years > 1 ? 's' : ''));
    }
    
    if (months > 0) {
        age.push(months + ' month' + (months > 1 ? 's' : ''));
    }
    
    if (days > 0 && age.length < 2) {
        age.push(days + ' day' + (days > 1 ? 's' : ''));
    }
    
    return age.length > 0 ? age.join(', ') : 'Less than a day';
}
