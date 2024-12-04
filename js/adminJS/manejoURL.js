function agregarRecurso(recurso) {
    let url = new URL(window.location.href);
    if (url.searchParams.has('recurso')) {
        url.searchParams.set('recurso', recurso);
    } else {
        url.searchParams.append('recurso', recurso);
    }
    window.location.href = url.href;
}

function eliminarRecurso() {
    let url = new URL(window.location.href);
    if (url.searchParams.has('recurso')) {
        url.searchParams.delete('recurso');
    }
    window.location.href = url.href;
}
