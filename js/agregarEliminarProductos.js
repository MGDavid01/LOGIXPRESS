function agregarProducto() {
    const container = document.getElementById("producto-container");
    const count = container.querySelectorAll(".producto-field").length + 1;

    // Crear un nuevo div contenedor para el producto y cantidad
    const newDiv = document.createElement("div");
    newDiv.className = "producto-field";

    // Obtener opciones dinámicas, excluyendo las seleccionadas
    const selects = document.querySelectorAll("select[name='producto[]']");
    const seleccionados = Array.from(selects).map(select => select.value); // Valores seleccionados

    const selectTemplate = document.querySelector("select[name='producto[]']");
    const options = Array.from(selectTemplate.options).filter(option => !seleccionados.includes(option.value)); // Filtrar opciones no seleccionadas

    // Construir el nuevo `<select>` solo con las opciones no seleccionadas
    newDiv.innerHTML = `
        <div class="cantidad-producto">
            <label for="producto${count}">Product ${count}:</label>
            <select name="producto[]" required onchange="actualizarOpciones()">
                ${options.map(option => `
                    <option value="${option.value}">
                        ${option.text}
                    </option>
                `).join("")}
            </select>
        </div><br>
        <div class="cantidad-producto">
            <label for="cantidad${count}">Amount:</label>
            <input type="number" name="cantidad[]" id="cantidad${count}" required>
        </div><br>
    `;

    // Agregar el nuevo conjunto de producto-cantidad al contenedor
    container.appendChild(newDiv);

    // Actualizar las opciones en todos los `<select>`
    actualizarOpciones();
}


function eliminarUltimoProducto() {
    const container = document.getElementById("producto-container");
    const productoFields = container.querySelectorAll(".producto-field");

    if (productoFields.length > 1) {
        container.removeChild(productoFields[productoFields.length - 1]); // Eliminar la última fila de producto
        actualizarOpciones(); // Actualizar las opciones después de eliminar
    } else {
        alert("No hay más productos para eliminar.");
    }
}

function actualizarOpciones() {
    const selects = document.querySelectorAll("select[name='producto[]']");
    const seleccionados = Array.from(selects).map(select => select.value); // Obtener valores seleccionados

    selects.forEach(select => {
        Array.from(select.options).forEach(option => {
            // Rehabilitar todas las opciones inicialmente
            option.disabled = false;

            // Deshabilitar opciones seleccionadas en otros selectores
            if (seleccionados.includes(option.value) && option.value !== select.value) {
                option.disabled = true;
            }
        });
    });
}
