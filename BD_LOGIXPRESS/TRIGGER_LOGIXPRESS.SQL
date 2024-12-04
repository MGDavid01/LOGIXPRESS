/*********************
        CLIENTE
**********************/
DELIMITER $$
CREATE TRIGGER TR_validarEntrega
BEFORE INSERT ON entrega 
FOR EACH ROW
BEGIN
    -- Validar que la fechaEntrega no sea sea null.
    IF NEW.fechaEntrega = NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fecha de entrega no debe estar vacio .';
    -- Validar que la fechaEntrega no sea sea anterior a la fechaRegistro.
    ELSEIF NEW.fechaEntrega <= NEW.fechaRegistro THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La fecha de entrega debe ser al menos un día posterior a la fecha de registro.';
    -- Validar que horaInicio y horaFin no sean nulas
    ELSEIF NEW.horaInicio IS NULL OR NEW.horaFin IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Las horas de inicio y/o fin no deben estar vacías.';
    -- Validar que la diferencia entre horaInicio y horaFin sea al menos de 4 horas
    ELSEIF TIMESTAMPDIFF(HOUR, NEW.horaInicio, NEW.horaFin) < 4 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Debe haber un margen de 4hrs    mo ventana de horario.';
    -- Validar que horaInicio esté dentro del rango permitido
    ELSEIF NEW.horaInicio < '00:00:00' OR NEW.horaInicio > '23:59:59' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La hora de inicio debe estar entre 00:00:00 y 23:59:59.';
    -- Validar que horaFin esté dentro del rango permitido
    ELSEIF NEW.horaFin < '00:00:00' OR NEW.horaFin > '23:59:59' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La hora de fin debe estar entre 00:00:00 y 23:59:59.';
    END IF;
END$$
/*************************************************************/

/*************************************************************/

/*************************************************************/
DELIMITER $$
CREATE TRIGGER TR_asignarEstadoEntrega
AFTER INSERT ON entrega
FOR EACH ROW
BEGIN
    -- Hacer update directamente, las fechas y horas ya estan validadas
    INSERT INTO entre_estado (entrega, estadoEntrega, fechaCambio)
    VALUES (NEW.num, 'PROG', NOW());
END$$
/*************************************************************/

/*********************
  Vehiculo y Remolque
**********************/
DELIMITER $$
CREATE TRIGGER TR_actualizarCostoVehiculo
AFTER INSERT ON mantenimiento
FOR EACH ROW
BEGIN
    -- Verifica que el costoAcumulado del vehículo exista y se actualice correctamente.
    UPDATE vehiculo
    SET costoAcumulado = IFNULL(costoAcumulado, 0) + NEW.costo
    WHERE num = NEW.vehiculo;
END$$
 /*************************************************************/
DELIMITER $$
CREATE TRIGGER TR_actualizarCostoRemolque
AFTER INSERT ON mantenimiento
FOR EACH ROW
BEGIN
    -- Verifica que el costoAcumulado del mantenimiento exista y se actualice correctamente.
    UPDATE remolque
    SET costoAcumulado = IFNULL(costoAcumulado, 0) + NEW.costo
    WHERE num = NEW.remolque;
END$$
 /*************************************************************/
DELIMITER $$
CREATE TRIGGER TR_cambiarEstadosVehiculoRemolqueEntrega
AFTER INSERT ON entre_vehi_remo
FOR EACH ROW
BEGIN
    -- Actualizar la disponibilidad del vehículo a RESER (reservado)
    UPDATE vehiculo
    SET disponibilidad = 'RESER'
    WHERE num = NEW.vehiculo;

    -- Actualizar la disponibilidad del remolque a RESER (reservado) si remolque no es 0 (que representa que no hay remolque)
    IF NEW.remolque IS NOT NULL AND NEW.remolque != 1 THEN
        UPDATE remolque
        SET disponibilidad = 'RESER'
        WHERE num = NEW.remolque;
    END IF;

    -- Insertar un nuevo estado para la entrega indicando que está ATEN (en atención)
    INSERT INTO entre_estado (entrega, estadoEntrega, fechaCambio)
    VALUES (NEW.entrega, 'ATEN', NOW());
END$$
 /*************************************************************/
DELIMITER $$
CREATE TRIGGER TR_cambiarEstadoEmpleadoEntrega
AFTER INSERT ON entre_empleado
FOR EACH ROW
BEGIN
    -- Actualizar la disponibilidad del empleado a RES (reservado)
    UPDATE empleado
    SET estadoEmpleado = 'RES'
    WHERE num = NEW.empleado;

END$$
 /*************************************************************/
DELIMITER $$
CREATE TRIGGER TR_cambiarEstadosVehiculoRemolqueMantenimiento
AFTER INSERT ON mantenimiento
FOR EACH ROW
BEGIN
     -- Cambiar estado del vehículo si fue incluido en el mantenimiento
    IF NEW.vehiculo IS NOT NULL THEN
        UPDATE vehiculo
        SET disponibilidad = 'DISPO'
        WHERE num = NEW.vehiculo;
    END IF;

    -- Cambiar estado del remolque si fue incluido en el mantenimiento
    IF NEW.remolque IS NOT NULL THEN
        UPDATE remolque
        SET disponibilidad = 'DISPO'
        WHERE num = NEW.remolque;
    END IF;
END$$
 /*************************************************************/