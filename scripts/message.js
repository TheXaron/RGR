document.addEventListener('DOMContentLoaded', (event) => {
    const message = document.getElementById('message');
    if (message) {
        setTimeout(() => {
            message.style.display = 'none';
        }, 5000);
    }
});
