function openTab(tabName) {
    // Ocultar todas las pestañas
    var i, tabcontent, tabbuttons;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        tabcontent[i].classList.remove("active-content");
    }

    // Eliminar la clase 'active' de todos los botones
    tabbuttons = document.getElementsByClassName("sticky");
    for (i = 0; i < tabbuttons.length; i++) {
        tabbuttons[i].classList.remove("active");
    }

    // Mostrar el contenido de la pestaña seleccionada y marcar el botón como activo
    document.getElementById(tabName).style.display = "block";
    document.getElementById(tabName).classList.add("active-content");
    document.querySelector(`.sticky[onclick="openTab('${tabName}')"]`).classList.add("active");
}

function nextTab(event) {
    event.preventDefault(); // Evitar el comportamiento por defecto del formulario

    var tabbuttons = document.getElementsByClassName("sticky");
    var activeIndex = -1;

    // Encontrar cuál es el botón activo actualmente
    for (var i = 0; i < tabbuttons.length; i++) {
        if (tabbuttons[i].classList.contains("active")) {
            activeIndex = i;
            break;
        }
    }

    // Si estamos en el último tab, simplemente podemos cambiar el texto o hacer algo más
    if (activeIndex >= tabbuttons.length - 1) {
        alert('Has llegado al último paso.');
        return;
    }

    // Mover a la siguiente pestaña
    var nextTabButton = tabbuttons[activeIndex + 1];
    var nextTabName = nextTabButton.getAttribute("onclick").match(/openTab\('([^']+)'\)/)[1];
    openTab(nextTabName);
}
