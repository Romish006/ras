document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var loader = document.getElementById('loader');
        if (loader) {
            loader.remove();
        }
    }, 1200);  
});