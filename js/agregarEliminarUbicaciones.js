function agregarUbicacion() {
    const container = document.getElementById("locations-container");
    const count = container.querySelectorAll(".location-destination-field").length + 1;

    // Crear un nuevo div contenedor para la ubicación
    const newDiv = document.createElement("div");
    newDiv.className = "location-destination-field";

    // Obtener opciones dinámicas, excluyendo las seleccionadas
    const selects = document.querySelectorAll("select[name='desLocation[]']");
    const seleccionados = Array.from(selects).map(select => select.value); // Valores seleccionados

    const selectTemplate = document.querySelector("select[name='desLocation[]']");
    const options = Array.from(selectTemplate.options).filter(option => !seleccionados.includes(option.value)); // Filtrar opciones no seleccionadas

    // Construir el nuevo `<select>` solo con las opciones no seleccionadas
    newDiv.innerHTML = `
        <label for="desLocation${count - 1}">Destination Location ${count -1}:</label>
        <select name="desLocation[]" required onchange="actualizarOpcionesUbicacion()">
            ${options.map(option => `
                <option value="${option.value}">
                    ${option.text}
                </option>
            `).join("")}
        </select>
    `;

    // Agregar el nuevo campo al contenedor
    container.appendChild(newDiv);

    // Actualizar las opciones en todos los `<select>`
    actualizarOpcionesUbicacion();
}

function eliminarUltimaUbicacion() {
    const container = document.getElementById("locations-container");
    const locationFields = container.querySelectorAll(".location-destination-field");

    if (locationFields.length > 1) {
        container.removeChild(locationFields[locationFields.length - 1]); // Eliminar la última fila de ubicación
        actualizarOpcionesUbicacion(); // Actualizar las opciones después de eliminar
    } else {
        alert("No hay más ubicaciones para eliminar.");
    }
}

function actualizarOpcionesUbicacion() {
    const origenSelect = document.getElementById("originLocation");
    const selects = document.querySelectorAll("select[name='desLocation[]']");
    const seleccionados = Array.from(selects).map(select => select.value); // Obtener valores seleccionados

    // Agregar la ubicación de origen a las opciones seleccionadas
    if (origenSelect && origenSelect.value) {
        seleccionados.push(origenSelect.value);
    }

    // Actualizar opciones para cada select de destino
    selects.forEach(select => {
        Array.from(select.options).forEach(option => {
            // Rehabilitar todas las opciones inicialmente
            option.disabled = false;
        });

        // También, si la opción actualmente seleccionada está deshabilitada, remover la selección
        if (select.selectedOptions[0].disabled) {
            select.selectedIndex = 0; // Asumiendo que el índice 0 es una opción válida como "Seleccione una opción"
        }
    });
}
