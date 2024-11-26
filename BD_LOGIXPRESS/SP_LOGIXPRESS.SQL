/*********************
        CLIENTE
**********************/
DELIMITER $$
CREATE PROCEDURE SP_registrarEntregaBase (
    IN fechaEntregaE DATE,
    IN horaInicioE TIME,
    IN horaFinE TIME,
    IN tipoCargaE VARCHAR(5),
    IN cliente_id INT,
    IN prioridadE VARCHAR(5),
    OUT entrega_id INT, -- Devuelve el ID de la entrega creada
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE error_msg TEXT;
    DECLARE error_code INT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al registrar la entrega: ", error_code, " - ", error_msg);
        SET entrega_id = NULL;
    END;

    START TRANSACTION;
        -- Insertar en la tabla `entrega`
        INSERT INTO entrega (fechaEntrega, horaInicio, horaFin, tipoCarga, cliente, prioridad)
        VALUES (fechaEntregaE, horaInicioE, horaFinE, tipoCargaE, cliente_id, prioridadE);

        -- Obtener el ID de la entrega recién insertada
        SET entrega_id = LAST_INSERT_ID();
        SET mensaje = 'OK';
    COMMIT;
END $$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_asociarOrigenEntrega (
    IN entrega_id INT, -- ID de la entrega ya registrada
    IN origen VARCHAR(5),
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE error_msg TEXT;
    DECLARE error_code INT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al asociar origen: ", error_code, " - ", error_msg);
    END;

    START TRANSACTION;
        -- Insertar en la tabla `ubi_entrega_salida`
        INSERT INTO ubi_entrega_salida (entrega, ubicacion, hraSalida)
        VALUES (entrega_id, origen, NOW());
    SET mensaje = "OK";
    COMMIT;
END $$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_asociarDestinosEntrega (
    IN entrega_id INT, -- ID de la entrega ya registrada
    IN origen VARCHAR(5),
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE error_msg TEXT;
    DECLARE error_code INT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al asociar destino: ", error_code, " - ", error_msg);
    END;

    START TRANSACTION;
        -- Insertar en la tabla `ubi_entrega_llegada`
        INSERT INTO ubi_entrega_llegada (entrega, ubicacion, hraLlegada)
        VALUES (entrega_id, origen, NULL);
        SET mensaje = "OK";
    COMMIT;
END $$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularVolumen(
    IN p_producto VARCHAR(5),
    IN p_cantidad INT,
    OUT volumenProducto FLOAT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_alto FLOAT;
    DECLARE v_ancho FLOAT;
    DECLARE v_largo FLOAT;
    DECLARE v_not_found INT DEFAULT 0;

    -- Manejador para el caso de que no se encuentre el producto
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_not_found = 1;

    -- Obtener las dimensiones del producto
    SELECT alto, ancho, largo
    INTO v_alto, v_ancho, v_largo
    FROM producto
    WHERE num = p_producto
    LIMIT 1;

    -- Validar que las dimensiones no sean nulas o cero
    IF v_alto IS NULL OR v_ancho IS NULL OR v_largo IS NULL OR
        v_alto = 0 OR v_ancho = 0 OR v_largo = 0 THEN
        SET mensaje = 'Error: Dimensiones del producto inválidas.';
        SET volumenProducto = NULL;
    ELSE
        -- Calcular el volumen total
        SET volumenProducto = v_alto * v_ancho * v_largo * p_cantidad;
        SET mensaje = 'OK';
    END IF;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularTarifaDistancia(
    IN p_entrega_id INT,
    OUT v_tarifaDistancia FLOAT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_distanciaTotal FLOAT DEFAULT 0;
    DECLARE v_porceDista FLOAT DEFAULT 0;

    -- Calcular distancia total
    SELECT IFNULL(SUM(r.distanciaTotal), 0)
    INTO v_distanciaTotal
    FROM ruta r
    INNER JOIN ruta_vehiculo rv ON r.num = rv.ruta
    INNER JOIN entre_vehi ev ON rv.vehiculo = ev.vehiculo
    WHERE ev.entrega = p_entrega_id;

    -- Como no se calcula la distancia total en otro funcion o SP, esto es una solucion temporal
    IF v_distanciaTotal < 0 THEN
        SET mensaje = 'Error: La distancia total no esta registrada.';
    ELSE
        -- Obtener porcentaje de tarifa de distancia
        SELECT IFNULL(td.porceDista, 0)
        INTO v_porceDista
        FROM tarifa_distancia td
        WHERE v_distanciaTotal BETWEEN td.distaMin AND td.distaMax
        ORDER BY td.distaMin ASC
        LIMIT 1;
        IF v_porceDista < 0 THEN
            SET mensaje = 'Error: El porcentaje de distancia no esta definida.';
        ELSE
            -- Calcular tarifa de distancia
            SET v_tarifaDistancia = (v_porceDista/100.0);
            -- Confirmar éxito
            SET mensaje = "OK";
        END IF;
    END IF;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularTarifaPeso(
    IN p_entrega_id INT,
    OUT p_pesoTotal FLOAT,
    OUT p_tarifaPeso FLOAT,
    OUT p_mensaje TEXT
)
BEGIN
    DECLARE v_porcePeso FLOAT DEFAULT 0;
    DECLARE v_numProductos INT DEFAULT 0;

    -- Verificar si la entrega existe y tiene productos asociados
    SELECT COUNT(*) INTO v_numProductos
    FROM entre_producto
    WHERE entrega = p_entrega_id;

    IF v_numProductos = 0 THEN
        SET p_mensaje = 'Error: La entrega no tiene productos asociados.';
    ELSE
        -- Calcular peso total
        SELECT SUM(p.peso * ep.cantidad)
        INTO p_pesoTotal
        FROM entre_producto ep
        INNER JOIN producto p ON ep.producto = p.num
        WHERE ep.entrega = p_entrega_id;

        IF p_pesoTotal IS NULL OR p_pesoTotal = 0 THEN
            SET p_mensaje = 'Error: El peso total es cero o no se pudo calcular.';
        ELSE
            -- Obtener porcentaje de tarifa de peso
            SELECT tp.porcePeso
            INTO v_porcePeso
            FROM tarifa_peso tp
            WHERE p_pesoTotal BETWEEN tp.pesoMin AND tp.pesoMax
            ORDER BY tp.pesoMin ASC
            LIMIT 1;

            IF v_porcePeso IS NULL THEN
                SET p_mensaje = 'Error: No se encontró una Tarifa de Peso aplicable.';
            ELSE
                -- Calcular tarifa de peso
                SET p_tarifaPeso = (v_porcePeso/100.0);
                -- Confirmar el éxito
                SET p_mensaje = 'OK';
            END IF;
        END IF;
    END IF;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularTarifaVolumen(
    IN p_voluTotal FLOAT,
    OUT v_tarifaVolumen FLOAT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_porceVolumen FLOAT DEFAULT 0;

    -- Obtener porcentaje de tarifa de volumen
    SELECT IFNULL(tv.porceVolu, 0)
    INTO v_porceVolumen
    FROM tarifa_volumen tv
    WHERE p_voluTotal BETWEEN tv.volumenMin AND tv.volumenMax
    ORDER BY tv.volumenMin ASC
    LIMIT 1;

    IF v_porceVolumen < 0 THEN
        SET mensaje = 'Error: No se encontró una Tarifa de Volumen aplicable.';
    ELSE
        -- Calcular tarifa de Volumen
        SET v_tarifaVolumen = (v_porceVolumen/100.0);
        -- Confirmar éxito
        SET mensaje = "OK";
    END IF;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularTarifaCategoria(
    IN p_entrega_id INT,
    OUT v_tarifaCat FLOAT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    -- Calcular tarifa por categoría
    SELECT IFNULL(SUM((cp.categoriaPrecio * ep.cantidad)), 0)
    INTO v_tarifaCat
    FROM entre_producto ep
    INNER JOIN producto p ON ep.producto = p.num
    INNER JOIN cat_prod cp ON p.categoria = cp.codigo
    WHERE ep.entrega = p_entrega_id;

    IF v_tarifaCat < 0 THEN
        SET mensaje = "Error: El recargo de Categoria Producto no esta definida.";
    ELSE
        -- Confirmar éxito
        SET mensaje = "OK";
    END IF;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularTarifaEtiquetado(
    IN p_entrega_id INT,
    OUT v_tarifaEti FLOAT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    -- Calcular tarifa por etiquetado
    SELECT IFNULL(SUM((e.etiquetadoPrecio * ep.cantidad)), 0)
    INTO v_tarifaEti
    FROM entre_producto ep
    INNER JOIN producto p ON ep.producto = p.num
    INNER JOIN etiquetado e ON p.etiquetado = e.codigo
    WHERE ep.entrega = p_entrega_id;

    IF v_tarifaEti < 0 THEN
        SET mensaje = 'Error: No se encontró tarifa de etiquetado para la entrega.';
    ELSE
        -- Confirmar éxito
        SET mensaje = "OK";
    END IF;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularTarifaPrioridad(
    IN p_prioridadSel VARCHAR(5),
    IN subtotal FLOAT,
    OUT v_tarifaPrio FLOAT,
    OUT mensaje TEXT
)
BEGIN
    -- Declarar variables locales
    DECLARE v_prioridadPorce FLOAT DEFAULT 0;

    -- Validar la prioridad seleccionada
    IF p_prioridadSel IS NULL OR p_prioridadSel = '' THEN
        SET mensaje = 'Error: Prioridad no especificada.';
        SET v_tarifaPrio = 0;
    ELSE
        -- Obtener porcentaje por prioridad
        SELECT IFNULL(pr.prioridadPorce, 0) INTO v_prioridadPorce
        FROM prioridad pr
        WHERE pr.codigo = p_prioridadSel;

        -- Validar si se encontró el porcentaje
        IF v_prioridadPorce < 0 THEN
            SET mensaje = CONCAT('Error: No se encontró un porcentaje para la prioridad ', p_prioridadSel, '.');
            SET v_tarifaPrio = 0;
        ELSE
            -- Calcular tarifa de prioridad
            SET v_tarifaPrio = (v_prioridadPorce/100.0) * subtotal;
            -- Confirmar éxito
            SET mensaje = 'OK';
        END IF;
    END IF;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_calcularEntrega(
    IN p_entrega_id INT,
    IN p_voluTotal FLOAT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    -- Declaración de variables locales
    DECLARE v_pesoTotal FLOAT DEFAULT 0;
    DECLARE v_tarifaPeso FLOAT DEFAULT 0;
    DECLARE v_tarifaVolumen FLOAT DEFAULT 0;
    DECLARE v_tarifaDistancia FLOAT DEFAULT 0;
    DECLARE v_tarifaEti FLOAT DEFAULT 0;
    DECLARE v_tarifaCat FLOAT DEFAULT 0;
    DECLARE v_tarifaPrio FLOAT DEFAULT 0;
    DECLARE v_subtotal FLOAT DEFAULT 0;
    DECLARE v_tarifas FLOAT DEFAULT 0;
    DECLARE v_IVA FLOAT DEFAULT 0;
    DECLARE v_precio FLOAT DEFAULT 0;
    DECLARE v_prioridadSel VARCHAR(5) DEFAULT '';
    DECLARE v_mensaje_local VARCHAR(255) DEFAULT 'OK';
    DECLARE error_msg TEXT;
    DECLARE error_code INT;
    DECLARE v_error INT DEFAULT 0;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al calcular la entrega: ", error_code, " - ", error_msg);
        SET v_error = 1;
    END;

    -- Iniciar transacción
    START TRANSACTION;
    mi_bloque: BEGIN
        -- Validar existencia de la entrega
        IF NOT EXISTS (SELECT 1 FROM entrega WHERE num = p_entrega_id) THEN
            SET mensaje = 'Error: El ID de la entrega no existe.';
            ROLLBACK;
            SET v_error = 1;
            LEAVE mi_bloque;
        END IF;

        -- Obtener prioridad seleccionada
        SELECT prioridad INTO v_prioridadSel
        FROM entrega
        WHERE num = p_entrega_id;

        -- Llamar a SP_calcularTarifaPeso
        CALL SP_calcularTarifaPeso(p_entrega_id, v_pesoTotal, v_tarifaPeso, v_mensaje_local);
        IF v_mensaje_local != 'OK' THEN
            SET mensaje = CONCAT('Error en Tarifa Peso: ', v_mensaje_local);
            ROLLBACK;
            SET v_error = 1;

            LEAVE mi_bloque;
        END IF;

        -- Llamar a SP_calcularTarifaVolumen
        CALL SP_calcularTarifaVolumen(p_voluTotal, v_tarifaVolumen, v_mensaje_local);
        IF v_mensaje_local != 'OK' THEN
            SET mensaje = CONCAT('Error en Tarifa Volumen: ', v_mensaje_local);
            ROLLBACK;
            SET v_error = 1;
            LEAVE mi_bloque;
        END IF;

        -- Llamar a SP_calcularTarifaDistancia
        CALL SP_calcularTarifaDistancia(p_entrega_id, v_tarifaDistancia, v_mensaje_local);
        IF v_mensaje_local != 'OK' THEN
            SET mensaje = CONCAT('Error en Tarifa Distancia: ', v_mensaje_local);
            ROLLBACK;
            SET v_error = 1;
            LEAVE mi_bloque;
        END IF;

        -- Llamar a SP_calcularTarifaEtiquetado
        CALL SP_calcularTarifaEtiquetado(p_entrega_id, v_tarifaEti, v_mensaje_local);
        IF v_mensaje_local != 'OK' THEN
            SET mensaje = CONCAT('Error en Tarifa Etiquetado: ', v_mensaje_local);
            ROLLBACK;
            SET v_error = 1;
            LEAVE mi_bloque;
        END IF;

        -- Llamar a SP_calcularTarifaCategoria
        CALL SP_calcularTarifaCategoria(p_entrega_id, v_tarifaCat, v_mensaje_local);
        IF v_mensaje_local != 'OK' THEN
            SET mensaje = CONCAT('Error en Tarifa Categoría: ', v_mensaje_local);
            ROLLBACK;
            SET v_error = 1;
            LEAVE mi_bloque;
        END IF;

        -- Calcular subtotal
        SET v_subtotal =  v_tarifaEti + v_tarifaCat;
        SET v_tarifaPeso = v_subtotal * v_tarifaPeso;
        SET v_tarifaVolumen = v_subtotal * v_tarifaVolumen;
        SET v_tarifaDistancia = v_subtotal * v_tarifaDistancia;
        SET v_tarifas = v_tarifaPeso + v_tarifaVolumen + v_tarifaDistancia;
        SET v_subtotal = v_subtotal + v_tarifas;

        -- Calcular tarifa de prioridad
        CALL SP_calcularTarifaPrioridad(v_prioridadSel, v_subtotal, v_tarifaPrio, v_mensaje_local);
        IF v_mensaje_local != 'OK' THEN
            SET mensaje = CONCAT('Error en Tarifa Prioridad: ', v_mensaje_local);
            ROLLBACK;
            SET v_error = 1;
            LEAVE mi_bloque;
        END IF;

        -- Actualizar subtotal con prioridad
        SET v_subtotal = v_subtotal + v_tarifaPrio;

        -- Calcular IVA y precio final
        SET v_IVA = v_subtotal * 0.16;
        SET v_precio = v_subtotal + v_IVA;

        -- Actualizar tabla entrega
        UPDATE entrega
        SET pesoTotal = v_pesoTotal,
            volumenTotal = p_voluTotal,
            tarifaPeso = v_tarifaPeso,
            tarifaVolumen = v_tarifaVolumen,
            tarifaDistancia = v_tarifaDistancia,
            tarifaPrio = v_tarifaPrio,
            tarifaEti = v_tarifaEti,
            tarifaCat = v_tarifaCat,
            subtotal = v_subtotal,
            IVA = v_IVA,
            precio = v_precio
        WHERE num = p_entrega_id;

        -- Confirmar la transacción
        IF v_mensaje_local = 'OK' THEN
            SET mensaje = 'OK';
            COMMIT;
        ELSE
            SET mensaje = v_mensaje_local;
            ROLLBACK;
            LEAVE mi_bloque;
        END IF;
    END mi_bloque;
END$$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_registrarProductoEntrega (
    IN entrega_id INT, -- ID de la entrega ya registrada
    IN p_producto VARCHAR(5),
    IN p_cantidad INT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE error_msg TEXT;
    DECLARE error_code INT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al asociar producto: ", error_code, " - ", error_msg);
    END;
    START TRANSACTION;
        -- Insertar en la tabla `entre_producto`
        INSERT INTO entre_producto (producto, entrega, cantidad)
        VALUES (p_producto, entrega_id, p_cantidad);
    COMMIT;
    SET mensaje = 'OK';
END $$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_registrarAsociarUbicacion(
    IN EnomUbicacion VARCHAR(100),
    IN EnomCalle VARCHAR(100),
    IN EnumCalle VARCHAR(50),
    IN Ecolonia VARCHAR(100),
    IN EcodigoPostal VARCHAR(10),
    IN Ecliente_id INT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE error_msg TEXT;
    DECLARE error_code INT;
    DECLARE new_ubicacion_id INT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al asociar ubicacion: ", error_code, " - ", error_msg);
    END;

    START TRANSACTION;

        -- Insertar en la tabla `ubicacion`
        INSERT INTO ubicacion (nombreUbicacion, nombreCalle, numCalle, colonia, codigoPostal)
        VALUES (EnomUbicacion, EnomCalle, EnumCalle, Ecolonia, EcodigoPostal);

        -- Obtener el ID recién insertado
        SET new_ubicacion_id = LAST_INSERT_ID();

        -- Asociar la ubicación al cliente
        INSERT INTO cliente_ubi (cliente, ubicacion, fechaRegistro)
        VALUES (Ecliente_id, new_ubicacion_id, CURRENT_DATE);

    COMMIT;
    SET mensaje = "Ubicacion asociada exitosamente.";
END $$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_registrarAsociarProducto(
    IN Enombre VARCHAR(100),
    IN Edescripcion VARCHAR(100),
    IN Ecategoria VARCHAR(5),
    IN Eetiquetado VARCHAR(5),
    IN Ealto FLOAT,
    IN Eancho FLOAT,
    IN Elargo FLOAT,
    IN Epeso FLOAT,
    IN Ecliente INT,
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE error_msg TEXT;
    DECLARE error_code INT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al asociar producto: ", error_code, " - ", error_msg);
    END;
    START TRANSACTION;
        INSERT INTO producto (nombre, descripcion, categoria, etiquetado, alto, ancho, largo, peso, cliente)
        VALUES (Enombre, Edescripcion, Ecategoria, Eetiquetado, Ealto, Eancho, Elargo, Epeso, Ecliente);
    COMMIT;
    SET mensaje = "Producto asociado exitosamente.";
END $$
/*************************************************************/
DELIMITER $$
CREATE PROCEDURE SP_actualizarInformacionCliente(
    IN clienteID INT,
    IN EnombreEmpresa VARCHAR(100),
    IN Enombre VARCHAR(100),
    IN EprimerApe VARCHAR(100),
    IN EsegundoApe VARCHAR(100),
    IN EnumTelefono VARCHAR(100),
    IN Eemail VARCHAR(100),
    IN Epassword VARCHAR(100),
    OUT mensaje VARCHAR(255)
)
BEGIN
    DECLARE error_msg TEXT;
    DECLARE error_code INT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            error_code = MYSQL_ERRNO,
            error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET mensaje = CONCAT("Error al actualizar la informacion de la cuenta: ", error_code, " - ", error_msg);
    END;
    START TRANSACTION;
        UPDATE cliente
        SET nomEmpresa = EnombreEmpresa,
            nomPila = Enombre,
            primerApe = EprimerApe,
            segundoApe = EsegundoApe,
            numTelefono = EnumTelefono,
            email = Eemail,
            password = Epassword
        WHERE num = clienteID;
    COMMIT;
    SET mensaje = "OK.";
END $$
/*************************************************************/

/************************
        CHECADOR
************************/
DELIMITER $$
CREATE PROCEDURE obtenerVehiculosLibres(
    IN pesoTotal FLOAT, 
    IN tipoCargaEntrega VARCHAR(5)
)
BEGIN
    -- Selecciona vehículos que están libres y cumplen con la capacidad de carga y el tipo de carga requerido
    SELECT 
        v.num AS vehiculo_id,
        v.capacidadCarga,
        cv.tipoCarga
        v.categoriavehiculo
    WHERE v.disponibilidad
    FROM vehiculo AS v
    INNER JOIN cat_vehi cv ON cv.codigo = v.categoriavehiculo
    WHERE 
        v.disponibilidad = 1 -- Filtra solo los vehículos libres
        AND v.capacidadCarga >= pesoTotal -- Capacidad debe ser suficiente para el pesoTotal de la entrega
        AND v.tipoCarga = tipoCargaEntrega -- El tipo de carga debe coincidir con el de la entrega
    ORDER BY v.capacidadCarga DESC;
END $$
/*ahora tambien quiero el procedimiento pero en este caso tambien agregar que alguna de las dimensiones del producto,
no sobrepasen las dimesiones*/