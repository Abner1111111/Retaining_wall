document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('in-situ-conditions-container');
    const addButton = document.getElementById('add-in-situ-test');
    const MAX_ENTRIES = 17;
    
    function updateAddButtonVisibility() {
        const currentEntries = document.querySelectorAll('.in-situ-entry').length;
        addButton.style.display = currentEntries >= MAX_ENTRIES ? 'none' : 'block';
    }

    function updateDisabledOptions() {
        const selects = document.querySelectorAll('.in-situ-condition');
        const selectedValues = Array.from(selects).map(select => select.value);
        
        selects.forEach(select => {
            Array.from(select.options).forEach(option => {
                if (option.value) {  
                    option.disabled = option.value !== select.value && selectedValues.includes(option.value);
                }
            });
        });
        

        updateAddButtonVisibility();
    }

    function initializeSelectListeners() {
        document.querySelectorAll('.in-situ-condition').forEach(select => {
            if (!select.hasEventListener) {
                select.addEventListener('change', function() {
                    const inputField = this.parentElement.querySelector('.in-situ-value');
                    const removeButton = this.parentElement.querySelector('.remove-test');
                    
                    if (this.value) {
                        inputField.disabled = false;
                        inputField.placeholder = `Enter ${this.value} result`;
                        inputField.focus();
                        
                        if (document.querySelectorAll('.in-situ-entry').length > 1) {
                            document.querySelectorAll('.remove-test').forEach(btn => {
                                btn.style.display = 'block';
                            });
                        }
                    } else {
                        inputField.disabled = true;
                        inputField.value = '';
                        inputField.placeholder = 'Enter test result';
                    }
                    
                    updateDisabledOptions();
                });
                select.hasEventListener = true;
            }
        });
    }

    initializeSelectListeners();
    
    addButton.addEventListener('click', function() {
        const currentEntries = document.querySelectorAll('.in-situ-entry').length;
        
        if (currentEntries >= MAX_ENTRIES) {
            return;
        }
        
        const entryDiv = document.createElement('div');
        entryDiv.className = 'in-situ-entry';
        entryDiv.style.display = 'flex';
        entryDiv.style.gap = '10px';
        entryDiv.style.marginBottom = '10px';
        
        const firstSelect = document.querySelector('.in-situ-condition');
        const newSelect = firstSelect.cloneNode(true);
        newSelect.value = '';
        newSelect.className = 'in-situ-condition';
        Array.from(newSelect.options).forEach(option => {
            option.disabled = false;
        });
        
        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.className = 'in-situ-value';
        newInput.name = 'in_situ_values[]';
        newInput.placeholder = 'Enter test result';
        newInput.disabled = true;
        newInput.style.flex = '1';
        
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'remove-test';
        removeButton.innerHTML = '✕';
        removeButton.style.background = '#dc3545';
        removeButton.style.padding = '0 10px';
        removeButton.style.display = 'block';
        
        removeButton.addEventListener('click', function() {
            container.removeChild(entryDiv);
            
            if (document.querySelectorAll('.in-situ-entry').length === 1) {
                document.querySelector('.remove-test').style.display = 'none';
            }
            
            updateDisabledOptions();
        });
        
        entryDiv.appendChild(newSelect);
        entryDiv.appendChild(newInput);
        entryDiv.appendChild(removeButton);
        container.appendChild(entryDiv);

        document.querySelectorAll('.remove-test').forEach(btn => {
            btn.style.display = 'block';
        });
        
        initializeSelectListeners();
        

        updateDisabledOptions();
    });

    updateAddButtonVisibility();
});