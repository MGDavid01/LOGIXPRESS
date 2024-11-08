CREATE TABLE CLIENTE (
    num INT PRIMARY KEY AUTO_INCREMENT,
    nomEmpresa VARCHAR(100) NOT NULL,
    nomPila VARCHAR(50) NOT NULL,
    primerApe VARCHAR(50) NOT NULL,
    segundoApe VARCHAR(50),
    numTelefono VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);


CREATE TABLE ESTADO_ENTRE (
    num INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE PRIORIDAD (
    num INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(100) NOT NULL,
    prioridadPorce FLOAT NOT NULL
);

CREATE TABLE TIPO_CARGA (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE CAT_PROD (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    categoriaPrecio FLOAT NOT NULL
);

CREATE TABLE ETIQUETADO (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL,
    etiquetadoPrecio FLOAT NOT NULL
);

CREATE TABLE TARIFA_PESO (
    num INT PRIMARY KEY AUTO_INCREMENT,
    pesoMin FLOAT NOT NULL,
    pesoMax FLOAT NOT NULL,
    porcePeso INT NOT NULL
);

CREATE TABLE TARIFA_DISTANCIA (
    num INT PRIMARY KEY AUTO_INCREMENT,
    distaMin FLOAT NOT NULL,
    distaMax FLOAT NOT NULL,
    porceDista INT NOT NULL
);

CREATE TABLE TARIFA_VOLUMEN (
    num INT PRIMARY KEY AUTO_INCREMENT,
    volumenMin FLOAT NOT NULL,
    volumenMax FLOAT NOT NULL,
    porceVolu INT NOT NULL
);

CREATE TABLE DISPONIBILIDAD (
    num INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE CAT_VEHI (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE PUESTO (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE MARCA (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE MODELO (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE TIPO_UBICACION (
    codigo VARCHAR(5) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

CREATE TABLE UBICACION (
    codigo VARCHAR(5) PRIMARY KEY,
    numCalle VARCHAR(50) NOT NULL,
    origenColonia VARCHAR(100),
    codigoPostal VARCHAR(10),
    tipo_Ubicacion VARCHAR(5),
    FOREIGN KEY (tipo_Ubicacion) REFERENCES TIPO_UBICACION(codigo)
);

CREATE TABLE PRODUCTO (
    codigo VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    categoria VARCHAR(5),
    etiquetado VARCHAR(5),
    FOREIGN KEY (categoria) REFERENCES CAT_PROD(codigo),
    FOREIGN KEY (etiquetado) REFERENCES ETIQUETADO(codigo)
);

CREATE TABLE EMPLEADO (
    num INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    primerApe VARCHAR(50) NOT NULL,
    segundoApe VARCHAR(50),
    fechaNacimiento DATE,
    telefono VARCHAR(15),
    email VARCHAR(100),
    puesto VARCHAR(5),
    FOREIGN KEY (puesto) REFERENCES PUESTO(codigo)
);

CREATE TABLE ENTREGA (
    num INT PRIMARY KEY AUTO_INCREMENT,
    fechaInicio DATE NOT NULL,
    fechaFin DATE NOT NULL,
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
    estado INT,
    prioridad INT,
    FOREIGN KEY (cliente) REFERENCES CLIENTE(num),
    FOREIGN KEY (estado) REFERENCES ESTADO_ENTRE(num),
    FOREIGN KEY (prioridad) REFERENCES PRIORIDAD(num),
    FOREIGN KEY (tipoCarga) REFERENCES TIPO_CARGA(codigo)
);

CREATE TABLE VEHICULO (
    num INT PRIMARY KEY AUTO_INCREMENT,
    numSerie VARCHAR(20) NOT NULL,
    gasXKM FLOAT NOT NULL,
    capacidadCarga FLOAT,
    kilometraje FLOAT,
    costoAcumulado FLOAT,
    marca VARCHAR(5),
    modelo VARCHAR(5),
    categoriaVehiculo VARCHAR(5),
    tipoCarga VARCHAR(5),
    disponibilidad INT,
    FOREIGN KEY (marca) REFERENCES MARCA(codigo),
    FOREIGN KEY (modelo) REFERENCES MODELO(codigo),
    FOREIGN KEY (tipoCarga) REFERENCES TIPO_CARGA(codigo),
    FOREIGN KEY (categoriaVehiculo) REFERENCES CAT_VEHI(codigo),
    FOREIGN KEY (disponibilidad) REFERENCES DISPONIBILIDAD(num)
);

CREATE TABLE REMOLQUE (
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
    disponibilidad INT,
    FOREIGN KEY (marca) REFERENCES MARCA(codigo),
    FOREIGN KEY (modelo) REFERENCES MODELO(codigo),
    FOREIGN KEY (tipoCarga) REFERENCES TIPO_CARGA(codigo),
    FOREIGN KEY (disponibilidad) REFERENCES DISPONIBILIDAD(num)
);

CREATE TABLE RUTA (
    num INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(255) NOT NULL,
    distanciaTotal FLOAT,
    tiempoEstimado TIME
);

CREATE TABLE MANTENIMIENTO (
    num INT PRIMARY KEY AUTO_INCREMENT,
    fechas DATE NOT NULL,
    costo FLOAT NOT NULL,
    descripcion VARCHAR(255),
    vehiculo INT,
    remolque INT,
    FOREIGN KEY (vehiculo) REFERENCES VEHICULO(num),
    FOREIGN KEY (remolque) REFERENCES REMOLQUE(num)
);

CREATE TABLE ENTRE_PROD (
    entrega INT,
    producto VARCHAR(5),
    cantidad INT NOT NULL,
    PRIMARY KEY (entrega, producto),
    FOREIGN KEY (entrega) REFERENCES ENTREGA(num),
    FOREIGN KEY (producto) REFERENCES PRODUCTO(codigo)
);

CREATE TABLE ENTRE_TIPOCARGA (
    entrega INT,
    tipoCarga VARCHAR(5),
    tempRequerida FLOAT,
    PRIMARY KEY (entrega, tipoCarga),
    FOREIGN KEY (entrega) REFERENCES ENTREGA(num),
    FOREIGN KEY (tipoCarga) REFERENCES TIPO_CARGA(codigo)
);

CREATE TABLE ENTRE_VEHI (
    entrega INT,
    vehiculo INT,
    cargaPorce FLOAT,
    volumenPorce FLOAT,
    PRIMARY KEY (entrega, vehiculo),
    FOREIGN KEY (entrega) REFERENCES ENTREGA(num),
    FOREIGN KEY (vehiculo) REFERENCES VEHICULO(num)
);

CREATE TABLE ENTRE_EMPLEADO (
    entrega INT,
    empleado INT,
    incidencias VARCHAR(255),
    PRIMARY KEY (entrega, empleado),
    FOREIGN KEY (entrega) REFERENCES ENTREGA(num),
    FOREIGN KEY (empleado) REFERENCES EMPLEADO(num)
);

CREATE TABLE UBI_ENTREGA_LLE (
    entrega INT,
    ubicacion VARCHAR(5),
    hraLlegada TIME,
    PRIMARY KEY (entrega, ubicacion),
    FOREIGN KEY (entrega) REFERENCES ENTREGA(num),
    FOREIGN KEY (ubicacion) REFERENCES UBICACION(codigo)
);

CREATE TABLE CLIENTE_UBI (
    cliente INT,
    ubicacion VARCHAR(5),
    fechaRegistro DATE,
    PRIMARY KEY (cliente, ubicacion),
    FOREIGN KEY (cliente) REFERENCES CLIENTE(num),
    FOREIGN KEY (ubicacion) REFERENCES UBICACION(codigo)
);

CREATE TABLE UBI_ENTREGA_SAL (
    entrega INT,
    ubicacion VARCHAR(5),
    hraSalida TIME,
    PRIMARY KEY (entrega, ubicacion),
    FOREIGN KEY (entrega) REFERENCES ENTREGA(num),
    FOREIGN KEY (ubicacion) REFERENCES UBICACION(codigo)
);

CREATE TABLE UBICACION_RUTA (
    ubicacion VARCHAR(5),
    ruta INT,
    distanXPun VARCHAR(25),
    ordenEntrega INT,
    PRIMARY KEY (ubicacion, ruta),
    FOREIGN KEY (ubicacion) REFERENCES UBICACION(codigo),
    FOREIGN KEY (ruta) REFERENCES RUTA(num)
);

CREATE TABLE VEHI_REMO (
    vehiculo INT,
    remolque INT,
    fechaAsig DATE,
    PRIMARY KEY (vehiculo, remolque),
    FOREIGN KEY (vehiculo) REFERENCES VEHICULO(num),
    FOREIGN KEY (remolque) REFERENCES REMOLQUE(num)
);

CREATE TABLE RUTA_VEHICULO (
    ruta INT,
    vehiculo INT,
    fechaAsig DATE,
    PRIMARY KEY (ruta, vehiculo),
    FOREIGN KEY (ruta) REFERENCES RUTA(num),
    FOREIGN KEY (vehiculo) REFERENCES VEHICULO(num)
);