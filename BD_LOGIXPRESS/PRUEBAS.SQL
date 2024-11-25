CALL SP_registrarAsociarUbicacion('AL2TI', 'Almacen 2 Tijuana', 'Calle Comun', '1234',
'Colonia Comun', '123456', 1, @mensaje);
SELECT @mensaje;

-- Llamar al procedimiento almacenado SP_registrarAsociarProducto con valores directos
CALL SP_registrarAsociarProducto('P001', 'Producto Ejemplo', 'Este es un producto de ejemplo para prueba',
'ALI01', 'PER', 1.25, 0.8, 0.15, 20, 1,@mensaje);
SELECT @mensaje;

CALL SP_actualizarInformacionCliente(2, 'Empresa Ejemplo', 'Juan', 'Pérez', 'Gómez', '5551234567',
'juan.perez@example.com', 'npassword', @mensaje);

SELECT @mensaje AS mensaje;

