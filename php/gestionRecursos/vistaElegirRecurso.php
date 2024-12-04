<script>
    function agregarRecurso(recurso) {
            let url = new URL(window.location.href);
            if (url.searchParams.has('recurso')) {
                url.searchParams.set('recurso', recurso);
            } else {
                url.searchParams.append('recurso', recurso);
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
        <h3>Employee</h3>
        <p>Añade nuevos recursos al sistema.</p>
        <button onclick="agregarRecurso('empleado')">Ir a Registrar Recursos</button>
    </div>

    <!-- Tarjeta para Editar Recursos -->
    <div class="card">
        <div class="image-container">
            <img src="imagenes/edit.png" alt="Editar Recursos">
        </div>
        <h3>Trailer</h3>
        <p>Modifica los recursos existentes.</p>
        <button onclick="agregarRecurso('remolque')">Ir a Editar Recursos</button>
    </div>

    <!-- Tarjeta para Eliminar Recursos -->
    <div class="card">
        <div class="image-container">
            <img src="imagenes/delete.png" alt="Eliminar Recursos">
        </div>
        <h3>Vehicle</h3>
        <p>Elimina los recursos no necesarios.</p>
        <button onclick="agregarRecurso('vehiculo')">Ir a Eliminar Recursos</button>
    </div>
</div>
