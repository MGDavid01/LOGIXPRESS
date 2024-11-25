document.getElementById('nextBtn').addEventListener('click', function() {
    // Desaparece el formulario 1 con animación
    var form1 = document.getElementById('form1');
    var form2 = document.getElementById('form2');
    
    // Inicia la animación de deslizamiento
    form1.classList.add('hide-form');

    // Mostrar el formulario 2 con una transición suave
    form2.style.opacity = "1";
    form2.style.zIndex = "5"; // Traer el formulario 2 al frente
});
