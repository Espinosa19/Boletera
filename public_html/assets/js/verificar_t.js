document.getElementById('tokenForm').addEventListener('submit', function(event) {
    event.preventDefault(); 

    const tokenInput = document.getElementById('token');
    const tokenError = document.getElementById('tokenError');
    const storedToken = localStorage.getItem('t'); 

    tokenError.style.display = 'none';

    if (!tokenInput.value.trim()) { 
        tokenError.style.display = 'block';
        tokenError.textContent = 'Por favor, ingrese un token válido.';
    } 
    else if (tokenInput.value.trim() === storedToken) { 
        window.location.href = 'acceso/index.php';
    } 
    else { 
        tokenError.style.display = 'block';
        tokenError.textContent = 'El token ingresado no es válido.';
    }
});
