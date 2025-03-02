// scripts.js
function showErrorModal(message) {
    document.getElementById('errorMessage').innerText = message;
    document.getElementById('errorModal').style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('errorModal').style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('errorModal')) {
            document.getElementById('errorModal').style.display = 'none';
        }
    });
});