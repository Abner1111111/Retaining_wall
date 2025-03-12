const API_BASE_URL = 'https://psgc.gitlab.io/api';
const REGION_12_CODE = '120000000';

function toggleNav() {
    const sidenav = document.getElementById("mySidenav");
    sidenav.classList.toggle("active");
}

function filterTable() {
    const severityFilter = document.getElementById('severity-filter').value.toLowerCase();
    const designFilter = document.getElementById('design-filter').value;
    const materialFilter = document.getElementById('material-filter').value;
    
    const rows = document.querySelectorAll('.assessments-table tbody tr');
    
    rows.forEach(row => {
        const severityElement = row.querySelector('.severity-badge');
        const severity = severityElement ? severityElement.textContent.toLowerCase() : '';
        const design = row.cells[2].textContent;
        const material = row.cells[3].textContent;
        
        const severityMatch = !severityFilter || severity.includes(severityFilter);
        const designMatch = !designFilter || design === designFilter;
        const materialMatch = !materialFilter || material === materialFilter;
        
        row.style.display = severityMatch && designMatch && materialMatch ? '' : 'none';
    });
}

document.getElementById('severity-filter').addEventListener('change', filterTable);
document.getElementById('design-filter').addEventListener('change', filterTable);
document.getElementById('material-filter').addEventListener('change', filterTable);

async function getLocationNameByCode(type, code) {
    if (!code) return 'N/A';
    
    try {
        const response = await fetch(`${API_BASE_URL}/${type}/${code}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        return data.name;
    } catch (error) {
        console.error(`Error fetching ${type} data:`, error);
        return code; 
    }
}