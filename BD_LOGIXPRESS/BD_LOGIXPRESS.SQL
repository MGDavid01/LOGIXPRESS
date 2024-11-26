-- Active: 1732599920807@@127.0.0.1@3306@LOGIXPRESS
CREATE TABLE cliente (
    num INT PRIMARY KEY AUTO_INCREMENT,
    nomEmpresa VARCHAR(100) NOT NULL,
    nomPila VARCHAR(50) NOT NULL,
    primerApe VARCHAR(50) NOT NULL,
    segundoApe VARCHAR(50),
    numTelefono VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password BLOB NOT NULL
);

CREATE TABLE estado_entre (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE prioridad (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL,
    prioridadPorce FLOAT NOT NULL
);

CREATE TABLE tipo_carga (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE cat_prod (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    categoriaPrecio FLOAT NOT NULL
);

CREATE TABLE etiquetado (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL,
    etiquetadoPrecio FLOAT NOT NULL
);

CREATE TABLE tarifa_peso (
    num INT PRIMARY KEY AUTO_INCREMENT,
    pesoMin FLOAT NOT NULL,
    pesoMax FLOAT NOT NULL,
    porcePeso FLOAT NOT NULL
);

CREATE TABLE tarifa_distancia (
    num INT PRIMARY KEY AUTO_INCREMENT,
    distaMin FLOAT NOT NULL,
    distaMax FLOAT NOT NULL,
    porceDista FLOAT NOT NULL
);

CREATE TABLE tarifa_volumen (
    num INT PRIMARY KEY AUTO_INCREMENT,
    volumenMin FLOAT NOT NULL,
    volumenMax FLOAT NOT NULL,
    porceVolu FLOAT NOT NULL
);

CREATE TABLE disponibilidad (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE cat_vehi (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE marca (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE ubicacion (
    codigo VARCHAR(5) PRIMARY KEY,
    nombreUbicacion VARCHAR(100),
    nombreCalle VARCHAR(100),
    numCalle VARCHAR(50) NOT NULL,
    colonia VARCHAR(100) NOT NULL,
    codigoPostal VARCHAR(10) NOT NULL
);

CREATE TABLE modelo (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    marca VARCHAR(5),
    Foreign Key (marca) REFERENCES marca(codigo)
);

CREATE TABLE producto (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    categoria VARCHAR(5),
    etiquetado VARCHAR(5),
    alto FLOAT NOT NULL,
    ancho FLOAT NOT NULL,
    largo FLOAT NOT NULL,
    peso FLOAT NOT NULL,
    cliente INT,
    FOREIGN KEY (categoria) REFERENCES cat_prod(codigo),
    FOREIGN KEY (etiquetado) REFERENCES etiquetado(codigo),
    FOREIGN KEY (cliente) REFERENCES cliente(num)
);

CREATE TABLE estado_emple (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(20)
);

CREATE TABLE puesto (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE empleado (
    num INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    primerApe VARCHAR(50) NOT NULL,
    segundoApe VARCHAR(50),
    telefono VARCHAR(15),
    email VARCHAR(100),
    password BLOB NOT NULL,
    puesto VARCHAR(5),
    estadoEmpleado VARCHAR(5),
    FOREIGN KEY (puesto) REFERENCES puesto(codigo),
    FOREIGN KEY (estadoEmpleado) REFERENCES estado_emple(codigo)
);

CREATE TABLE ruta (
    num INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(255) NOT NULL,
    distanciaTotal FLOAT,
    tiempoEstimado TIME
);

CREATE TABLE entrega (
    num INT PRIMARY KEY AUTO_INCREMENT,
    fechaRegistro date DEFAULT CURDATE(),
    fechaEntrega DATE NOT NULL,
    horaInicio TIME NOT NULL,
    horaFin TIME NOT NULL,
    pesoTotal FLOAT,
    volumenTotal FLOAT,
    tarifaPeso FLOAT,
    tarifaDistancia FLOAT,
    tarifaVolumen FLOAT,
    tarifaPrio FLOAT,
    tarifaEti FLOAT,
    tarifaCat FLOAT,
    subtotal FLOAT,
    IVA FLOAT,
    precio FLOAT,
    tipoCarga VARCHAR(5),
    cliente INT,
    prioridad VARCHAR(5),
    FOREIGN KEY (cliente) REFERENCES cliente(num),
    FOREIGN KEY (prioridad) REFERENCES prioridad(codigo)
);

CREATE TABLE entre_estado (
    entrega INT,
    estadoEntrega VARCHAR(5),
    fechaCambio DATETIME,
    PRIMARY KEY (entrega, estadoEntrega),
    FOREIGN KEY (entrega) REFERENCES entrega(num),
    FOREIGN KEY (estadoEntrega) REFERENCES estado_entre(codigo)
);

CREATE TABLE vehiculo (
    num INT PRIMARY KEY AUTO_INCREMENT,
    numSerie VARCHAR(20) NOT NULL,
    gasXKM FLOAT NOT NULL,
    capacidadCarga FLOAT,
    kilometraje FLOAT,
    costoAcumulado FLOAT,
    marca VARCHAR(5),
    modelo VARCHAR(5),
    categoriavehiculo VARCHAR(5),
    tipoCarga VARCHAR(5),
    disponibilidad VARCHAR(5),
    FOREIGN KEY (marca) REFERENCES marca(codigo),
    FOREIGN KEY (modelo) REFERENCES modelo(codigo),
    FOREIGN KEY (tipoCarga) REFERENCES tipo_carga(codigo),
    FOREIGN KEY (categoriavehiculo) REFERENCES cat_vehi(codigo),
    FOREIGN KEY (disponibilidad) REFERENCES disponibilidad(codigo)
);

CREATE TABLE tipo_remolque (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(60)
);

CREATE TABLE remolque (
    num INT PRIMARY KEY AUTO_INCREMENT,
    numSerie VARCHAR(20) NOT NULL,
    alto FLOAT,
    largo FLOAT,
    ancho FLOAT,
    capacidadCarga FLOAT,
    costoAcumulado FLOAT,
    marca VARCHAR(5),
    modelo VARCHAR(5),
    tipoCarga VARCHAR(5),
    tipoRemolque VARCHAR(5),
    disponibilidad VARCHAR(5),
    FOREIGN KEY (marca) REFERENCES marca(codigo),
    FOREIGN KEY (modelo) REFERENCES modelo(codigo),
    FOREIGN KEY (tipoCarga) REFERENCES tipo_carga(codigo),
    FOREIGN KEY (tipoRemolque) REFERENCES tipo_remolque(codigo),
    FOREIGN KEY (disponibilidad) REFERENCES disponibilidad(codigo)
);

CREATE TABLE mantenimiento (
    num INT PRIMARY KEY AUTO_INCREMENT,
    fechas DATE NOT NULL,
    costo FLOAT NOT NULL,
    descripcion VARCHAR(255),
    vehiculo INT,
    remolque INT,
    FOREIGN KEY (vehiculo) REFERENCES vehiculo(num),
    FOREIGN KEY (remolque) REFERENCES remolque(num)
);

CREATE TABLE entre_producto (
    entrega INT,
    producto VARCHAR(5),
    cantidad INT NOT NULL,
    PRIMARY KEY (entrega, producto),
    FOREIGN KEY (entrega) REFERENCES entrega(num),
    FOREIGN KEY (producto) REFERENCES producto(codigo)
);

CREATE TABLE entre_tipocarga (
    entrega INT,
    tipoCarga VARCHAR(5),
    instrucciones VARCHAR(1250),
    PRIMARY KEY (entrega, tipoCarga),
    FOREIGN KEY (entrega) REFERENCES entrega(num),
    FOREIGN KEY (tipoCarga) REFERENCES tipo_carga(codigo)
);

CREATE TABLE entre_vehi (
    entrega INT,
    vehiculo INT,
    cargaPorce FLOAT,
    volumenPorce FLOAT,
    PRIMARY KEY (entrega, vehiculo),
    FOREIGN KEY (entrega) REFERENCES entrega(num),
    FOREIGN KEY (vehiculo) REFERENCES vehiculo(num)
);

CREATE TABLE entre_empleado (
    entrega INT,
    empleado INT,
    incidencias VARCHAR(255),
    PRIMARY KEY (entrega, empleado),
    FOREIGN KEY (entrega) REFERENCES entrega(num),
    FOREIGN KEY (empleado) REFERENCES empleado(num)
);

CREATE TABLE ubi_entrega_llegada (
    entrega INT,
    ubicacion VARCHAR(5),
    hraLlegada TIME,
    PRIMARY KEY (entrega, ubicacion),
    FOREIGN KEY (entrega) REFERENCES entrega(num),
    FOREIGN KEY (ubicacion) REFERENCES ubicacion(codigo)
);

CREATE TABLE cliente_ubi (
    cliente INT,
    ubicacion VARCHAR(5),
    fechaRegistro DATE,
    PRIMARY KEY (cliente, ubicacion),
    FOREIGN KEY (cliente) REFERENCES cliente(num),
    FOREIGN KEY (ubicacion) REFERENCES ubicacion(codigo)
);

CREATE TABLE ubi_entrega_salida (
    entrega INT,
    ubicacion VARCHAR(5),
    hraSalida TIME,
    PRIMARY KEY (entrega, ubicacion),
    FOREIGN KEY (entrega) REFERENCES entrega(num),
    FOREIGN KEY (ubicacion) REFERENCES ubicacion(codigo)
);

CREATE TABLE ubicacion_ruta (
    ubicacion VARCHAR(5),
    ruta INT,
    distanXPun VARCHAR(25),
    ordenentrega INT,
    PRIMARY KEY (ubicacion, ruta),
    FOREIGN KEY (ubicacion) REFERENCES ubicacion(codigo),
    FOREIGN KEY (ruta) REFERENCES ruta(num)
);

CREATE TABLE vehi_remo (
    vehiculo INT,
    remolque INT,
    fechaAsig DATE,
    PRIMARY KEY (vehiculo, remolque),
    FOREIGN KEY (vehiculo) REFERENCES vehiculo(num),
    FOREIGN KEY (remolque) REFERENCES remolque(num)
);

CREATE TABLE ruta_vehiculo (
    ruta INT,
    vehiculo INT,
    fechaAsig DATE,
    PRIMARY KEY (ruta, vehiculo),
    FOREIGN KEY (ruta) REFERENCES ruta(num),
    FOREIGN KEY (vehiculo) REFERENCES vehiculo(num)
);