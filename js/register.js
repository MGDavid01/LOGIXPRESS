function mostrarFormulario2() {
    var form1 = document.getElementById('form1');
    var form2 = document.getElementById('form2');

    // Iniciar la animación de deslizamiento para ocultar form1
    form1.classList.add('hide-form');

    // Mostrar form2 con opacidad gradual durante la animación de form1
    form2.style.transition = "opacity 1s ease"; // Transición suave de opacidad
    form2.style.opacity = "1"; // Aumenta la opacidad de form2 a 1
}