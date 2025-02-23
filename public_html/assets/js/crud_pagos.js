
document.addEventListener("DOMContentLoaded", function () {
    
document.getElementById("buscarFecha").addEventListener("input", function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#pagoTableBody tr");

    filas.forEach(function(fila) {
        let fecha = fila.cells[1].textContent.toLowerCase();
        fila.style.display = fecha.includes(filtro) ? "" : "none";
    });
});

});