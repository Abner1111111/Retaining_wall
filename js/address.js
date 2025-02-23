const API_BASE_URL = 'https://psgc.gitlab.io/api';
const REGION_12_CODE = '120000000';
async function fetchFromAPI(url) {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log('API Response:', url, data); 
        return data;
    } catch (error) {
        console.error('Error fetching data:', url, error);
        return [];
    }
}

async function initializeProvinces() {
    const provinceSelect = document.getElementById('province');
    console.log('Fetching provinces for Region 12...'); 
    

    const provinces = await fetchFromAPI(`${API_BASE_URL}/regions/${REGION_12_CODE}/provinces`);
    
    if (provinces.length > 0) {
        provinces.sort((a, b) => a.name.localeCompare(b.name)).forEach(province => {
            provinceSelect.add(new Option(province.name, province.code));
        });
        console.log('Provinces loaded:', provinces.length); 
    } else {
        console.error('No provinces found for Region 12');
    }
}

document.getElementById('province').addEventListener('change', async function() {
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');
    
    citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (this.value) {
        citySelect.disabled = false;
        const cities = await fetchFromAPI(`${API_BASE_URL}/provinces/${this.value}/cities-municipalities`);
        cities.sort((a, b) => a.name.localeCompare(b.name)).forEach(city => {
            citySelect.add(new Option(city.name, city.code));
        });
    } else {
        citySelect.disabled = true;
        barangaySelect.disabled = true;
    }
});

document.getElementById('city').addEventListener('change', async function() {
    const barangaySelect = document.getElementById('barangay');
    
    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
    
    if (this.value) {
        barangaySelect.disabled = false;
        const barangays = await fetchFromAPI(`${API_BASE_URL}/cities-municipalities/${this.value}/barangays`);
        barangays.sort((a, b) => a.name.localeCompare(b.name)).forEach(barangay => {
            barangaySelect.add(new Option(barangay.name, barangay.code));
        });
    } else {
        barangaySelect.disabled = true;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    initializeProvinces();
});
