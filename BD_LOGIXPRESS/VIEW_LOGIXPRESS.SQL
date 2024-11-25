/******************************
    Cliente         Vistas
*******************************/

--Lista de entregas completadas (con un SP se podra especificar para el cliente)
CREATE VIEW vw_historial_entregas_general AS
SELECT num, fechaInicio, fechaFin, horaInicio, horaFin, pesoTotal, volumenTotal,
FROM entrega;

--Lista de ubicaciones registradas (con un SP se podra especificar para el cliente
--y ademas contenga los joins de las tablas ubi_cliente y cliente)
CREATE VIEW vw_lista_ubicaciones_registradas 
SELECT nombreUbicacion, nombreCalle, numCalle, colonia, codigoPostal
FROM ubicacion;

--Lista de productos asociados (con un SP se podra especificar para el cliente
--y ademas contenga el join de cliente)
CREATE VIEW vw_lista_productos_registrados
SELECT p.nombre, p.descripcion, c.nombre, e.descripcion
FROM producto AS p
INNER JOIN cat_prod AS c ON c.codigo =  p.categoria
INNER JOIN etiquetado as e ON e.codigo = p.etiquetado;
/******************************
    Chofer          Vistas
*******************************/

/******************************
    Checador        Vistas
*******************************/

/******************************
    Administrador   Vistas
*******************************/