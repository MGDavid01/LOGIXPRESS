// Obtener el modal y el botón de cerrar
var modal = document.getElementById("modalDetallesEntrega");
var span = document.getElementsByClassName("close")[0];

// Cuando el usuario hace clic en el botón "Ver detalles de la entrega"
function mostrarDetallesEntrega(entregaId) {
    // Mostrar el modal
    modal.style.display = "block";

    // Cargar detalles de la entrega mediante AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "detalles_entrega.php?entrega=" + entregaId, true);
    xhr.onload = function () {
        if (this.status == 200) {
            document.getElementById("entregaIdModal").innerText = entregaId;
            document.getElementById("detallesContenido").innerHTML = this.responseText;
        }
    };
    xhr.send();
}

// Cuando el usuario hace clic en el botón de cerrar
span.onclick = function() {
    modal.style.display = "none";
}

// Cuando el usuario hace clic fuera del modal, cerrarlo
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}