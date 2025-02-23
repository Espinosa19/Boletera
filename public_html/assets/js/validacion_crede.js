document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault(); 
    const correo = document.getElementById('correo').value;
    const contra = document.getElementById('contra').value;
    const response = await fetch('./perfil_protegido/apis/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ correo, contra })
    });
    const data = await response.json();
    console.log(data)
    if (data.error) {
        document.getElementById('error').textContent = data.error;
    } else {
        window.location.href = 'formulario_token.php';
    }
});