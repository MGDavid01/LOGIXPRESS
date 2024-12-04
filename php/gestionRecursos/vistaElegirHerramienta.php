
<script>
    function agregarHerramienta(herramienta) {
            let url = new URL(window.location.href);
            if (url.searchParams.has('herramienta')) {
                url.searchParams.set('herramienta', herramienta);
            } else {
                url.searchParams.append('herramienta', herramienta);
            }
            window.location.href = url.href;
        }
</script>
<div class="content-card">
    <!-- Tarjeta para Registrar Recursos -->
    <div class="card">
        <div class="image-container">
            <img src="imagenes/add.png" alt="Registrar Recursos">
        </div>
        <h3>Registrar Recursos</h3>
        <p>Añade nuevos recursos al sistema.</p>
        <button onclick="agregarHerramienta('registrar')">Ir a Registrar Recursos</button>
    </div>

    <!-- Tarjeta para Editar Recursos -->
    <div class="card">
        <div class="image-container">
            <img src="imagenes/edit.png" alt="Editar Recursos">
        </div>
        <h3>Editar Recursos</h3>
        <p>Modifica los recursos existentes.</p>
        <button onclick="agregarHerramienta('editar')">Ir a Editar Recursos</button>
    </div>

    <!-- Tarjeta para Eliminar Recursos -->
    <div class="card">
        <div class="image-container">
            <img src="imagenes/delete.png" alt="Eliminar Recursos">
        </div>
        <h3>Eliminar Recursos</h3>
        <p>Elimina los recursos no necesarios.</p>
        <button onclick="agregarHerramienta('eliminar')">Ir a Eliminar Recursos</button>
    </div>
</div>