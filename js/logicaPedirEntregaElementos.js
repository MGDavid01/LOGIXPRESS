document.addEventListener("DOMContentLoaded", function () {
    let currentStep = 1;
    const totalSteps = 4;

    function showStep(step) {
        // Hide all steps
        for (let i = 1; i <= totalSteps; i++) {
            const stepElement = document.querySelector(`.step-${i}`);
            if (stepElement) {
                stepElement.style.display = "none";
                stepElement.classList.remove("active");
            }
        }
        // Show the current step
        const currentStepElement = document.querySelector(`.step-${step}`);
        if (currentStepElement) {
            currentStepElement.style.display = "flex";
            currentStepElement.classList.add("active");
            currentStep = step;
            if (step === totalSteps) {
                generateSummary();
            }
        }
    }

    function validateStep(step) {
        const stepElement = document.querySelector(`.step-${step}`);
        const inputs = stepElement.querySelectorAll("input, select, textarea");
        for (let input of inputs) {
            if (!input.checkValidity()) {
                input.reportValidity();
                return false;
            }
        }
        return true;
    }

    window.nextStep = function (step) {
        if (step <= totalSteps && validateStep(currentStep)) {
            currentStep = step;
            showStep(currentStep);
        }
    };

    window.previousStep = function (step) {
        if (step > 0) {
            currentStep = step;
            showStep(currentStep);
        }
    };

    // Initialize the first step
    showStep(currentStep);

    // Function to generate a summary in the last step
    function generateSummary() {
        const summaryContainer = document.querySelector(".step-4 .form-section");
        summaryContainer.innerHTML = "";

        // Generate the summary of entered data
        let summaryHTML = "<h3>Summary of Delivery</h3>";
        summaryHTML += `<p><strong>Delivery Date:</strong> ${document.querySelector('[name="fechaEntrega"]').value}</p>`;
        summaryHTML += `<p><strong>Start Time:</strong> ${document.querySelector('[name="horaInicio"]').value}</p>`;
        summaryHTML += `<p><strong>End Time:</strong> ${document.querySelector('[name="horaFin"]').value}</p>`;
        summaryHTML += `<p><strong>Delivery Priority:</strong> ${document.querySelector('[name="prioridad"]').value}</p>`;

        // Summary for products
        const products = document.querySelectorAll('[name="producto[]"]');
        const amounts = document.querySelectorAll('[name="cantidad[]"]');
        const loadTypes = document.querySelectorAll('[name="tipoCarga[]"]');
        if (products.length > 0) {
            summaryHTML += "<h4>Products to Deliver:</h4><ul>";
            products.forEach((product, index) => {
                summaryHTML += `<li><strong>Product:</strong> ${product.options[product.selectedIndex].text}, ` +
                    `<strong>Amount:</strong> ${amounts[index].value}, ` +
                    `<strong>Load Type:</strong> ${loadTypes[index].options[loadTypes[index].selectedIndex].text}</li>`;
            });
            summaryHTML += "</ul>";
        }

        // Summary for locations
        summaryHTML += `<p><strong>Origin Location:</strong> ${document.querySelector('#originLocation').options[document.querySelector('#originLocation').selectedIndex].text}</p>`;
        const destinations = document.querySelectorAll('[name="desLocation[]"]');
        if (destinations.length > 0) {
            summaryHTML += "<h4>Destination Locations:</h4><ul>";
            destinations.forEach(destination => {
                summaryHTML += `<li>${destination.options[destination.selectedIndex].text}</li>`;
            });
            summaryHTML += "</ul>";
        }

        // Instructions
        const instructions = document.querySelector('[name="instrucciones"]').value;
        if (instructions) {
            summaryHTML += `<p><strong>Instructions:</strong> ${instructions}</p>`;
        }

        summaryContainer.innerHTML = summaryHTML;
    }
});

// Functions to dynamically add/remove products and locations
function agregarProducto() {
    const productContainer = document.getElementById("producto-container");
    const selectedProducts = Array.from(document.querySelectorAll('[name="producto[]"]')).map(select => select.value);
    const allOptions = document.querySelector('[name="producto[]"]').querySelectorAll('option');

    if (productContainer.children.length < allOptions.length) { // Ensure not to exceed available options
        const newProductField = document.createElement("div");
        newProductField.classList.add("producto-field");

        let optionsHTML = '';
        allOptions.forEach(option => {
            if (!selectedProducts.includes(option.value)) {
                optionsHTML += `<option value="${option.value}">${option.text}</option>`;
            }
        });

        newProductField.innerHTML = `
            <div class="form-field">
                <label for="producto">Product:</label>
                <select name="producto[]" required>
                    ${optionsHTML}
                </select>
            </div>
            <div class="form-field form-field-inline">
                <div class="form-element">
                    <label for="cantidad">Amount:</label>
                    <input type="number" name="cantidad[]" required>
                </div>
                <div class="form-element">
                    <label for="tipoCarga">Load Type:</label>
                    <select name="tipoCarga[]" required>
                        ${document.querySelector('[name="tipoCarga[]"]').innerHTML}
                    </select>
                </div>
            </div>
        `;
        productContainer.appendChild(newProductField);
    } else {
        alert("No more products available to add.");
    }
}

function agregarUbicacion() {
    const locationsContainer = document.getElementById("locations-container");
    const originLocation = document.getElementById("originLocation").value;
    const newLocationField = document.createElement("div");
    newLocationField.classList.add("form-field");

    let optionsHTML = '';
    const allOptions = document.querySelector('[name="desLocation[]"]').querySelectorAll('option');
    allOptions.forEach(option => {
        if (option.value !== originLocation) {
            optionsHTML += `<option value="${option.value}">${option.text}</option>`;
        }
    });

    if (locationsContainer.children.length - 1 < allOptions.length - 1) { // Ensure not to exceed available options
        newLocationField.innerHTML = `
            <label for="desLocation">Destination Location:</label>
            <select name="desLocation[]" required>
                ${optionsHTML}
            </select>
        `;
        locationsContainer.appendChild(newLocationField);
    } else {
        alert("No more locations available to add.");
    }
}

function eliminarUltimoProducto() {
    const productContainer = document.getElementById("producto-container");
    if (productContainer.children.length > 1) {
        productContainer.removeChild(productContainer.lastElementChild);
    }
}

function eliminarUltimaUbicacion() {
    const locationsContainer = document.getElementById("locations-container");
    if (locationsContainer.children.length > 2) { // Keep the origin location field
        locationsContainer.removeChild(locationsContainer.lastElementChild);
    }
}

function actualizarOpcionesDestino() {
    const originLocation = document.getElementById("originLocation").value;
    const destinationSelects = document.querySelectorAll('[name="desLocation[]"]');
    destinationSelects.forEach(select => {
        const allOptions = select.querySelectorAll('option');
        allOptions.forEach(option => {
            if (option.value === originLocation) {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    });
}

function actualizarCamposDestino() {
    const originLocation = document.getElementById("originLocation").value;
    const destinationSelects = document.querySelectorAll('[name="desLocation[]"]');
    destinationSelects.forEach(select => {
        if (select.value === originLocation) {
            // Cambiar el valor del campo de destino si coincide con el origen
            alert("The destination location selected was used as the origin location. Please select another destination.");
            select.value = "";
            select.dispatchEvent(new Event('change'));
        }
    });
}
