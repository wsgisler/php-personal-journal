// Journal Application JavaScript

// Utility function for AJAX requests
function sendAjax(action, data, callback) {
    data.ajax = '1';
    data.action = action;
    
    const formData = new URLSearchParams();
    for (let key in data) {
        formData.append(key, data[key]);
    }
    
    fetch('index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData
    })
    .then(response => response.json())
    .then(callback)
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Auto-save functionality for entries (optional)
let autoSaveTimer = null;

function enableAutoSave() {
    const textarea = document.getElementById('entryContent');
    if (!textarea) return;
    
    textarea.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Could implement auto-save here
            console.log('Auto-save triggered');
        }, 5000);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    enableAutoSave();
});
