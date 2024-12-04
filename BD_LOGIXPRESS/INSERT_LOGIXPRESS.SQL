-- Active: 1732599920807@@127.0.0.1@3306@LOGIXPRESS
INSERT INTO cliente (nomEmpresa, nomPila, primerApe, segundoApe, numTelefono, email, password) VALUES
('Transporte Seguro S.A.', 'Juan', 'Pérez', 'García', '5551234567', 'juan.perez@ejemplo.com', sha2('contraseña123', 256)),
('Logística Global', 'María', 'López', NULL, '5559876543', 'maria.lopez@ejemplo.com', sha2('contraseña123', 256)),
('Envíos Rápidos', 'Carlos', 'Rodríguez', 'Martínez', '5555555555', 'carlos.rodriguez@ejemplo.com', sha2('contraseña123', 256)),
('Distribuciones MX', 'Ana', 'Gómez', 'Hernández', '5553332211', 'ana.gomez@ejemplo.com', sha2('contraseña123', 256)),
('Carga Express', 'Luis', 'Fernández', 'Díaz', '5557778899', 'luis.fernandez@ejemplo.com', sha2('contraseña123', 256)),
('Distribuidora Alimentos', 'María', 'González', 'Pérez', '5551234567', 'maria.gonzalez@distribalimentos.com', sha2('contraseña123', 256)),
('Frescos del Campo', 'Juan', 'López', 'Ramírez', '5552345678', 'juan.lopez@frescoscampo.com', sha2('contraseña123', 256)),
('Congelados S.A.', 'Ana', 'Martínez', 'Sánchez', '5553456789', 'ana.martinez@congeladossa.com', sha2('contraseña123', 256)),
('Carnes Premium', 'Pedro', 'Hernández', 'Díaz', '5554567890', 'pedro.hernandez@carnespremium.com', sha2('contraseña123', 256)),
('Lácteos del Norte', 'Laura', 'García', 'Torres', '5555678901', 'laura.garcia@lacteosnorte.com', sha2('contraseña123', 256)),
('Panaderías Unidas', 'Carlos', 'Sánchez', 'Flores', '5556789012', 'carlos.sanchez@panaderiasunidas.com', sha2('contraseña123', 256)),
('Snacks y Dulces S.A.', 'Elena', 'Jiménez', 'Gómez', '5557890123', 'elena.jimenez@snacksdulces.com', sha2('contraseña123', 256)),
('Cosméticos Bella', 'Miguel', 'Vargas', 'Morales', '5558901234', 'miguel.vargas@cosmeticosbella.com', sha2('contraseña123', 256)),
('Higiene Global', 'Sofía', 'Mendoza', 'Cruz', '5559012345', 'sofia.mendoza@higieneglobal.com', sha2('contraseña123', 256)),
('Limpieza Hogar', 'Daniel', 'Castro', 'Ortiz', '5550123456', 'daniel.castro@limpiezahogar.com', sha2('contraseña123', 256)),
('Nada prueba', 'David', 'Prueba', '', '5559012345', 'nada.nada@nada.com', sha2('contraseña123', 256));

INSERT INTO estado_entre (codigo, descripcion) VALUES
('PROG', 'Programada'),
('ATEN', 'Peticion Atendida'),
('ENPR', 'En Proceso'),
('COMP', 'Completada'),
('CANC', 'Cancelada');

INSERT INTO prioridad (codigo, descripcion, prioridadPorce) VALUES
('BAJA', 'Baja', 0.0),
('MEDI', 'Media', 5.0),
('ALTA', 'Alta', 10.0),
('URGE', 'Urgente', 20.0);

INSERT INTO tipo_carga (codigo, descripcion) VALUES
('GRN', 'Granel'),
('PER', 'Perecedero'),
('GEN', 'General'),
('UNV', 'Universal');

INSERT INTO etiquetado (codigo, descripcion, etiquetadoPrecio) VALUES
('EST', 'Estándar', 0.00),
('FRA', 'Frágil', 500.00),
('PER', 'Perecedero', 200.00);

INSERT INTO cat_prod (codigo, nombre, descripcion, categoriaPrecio) VALUES
('ALI01', 'Alimentos y Bebidas', 'Productos alimenticios y bebidas variadas', 500.00),
('FRE01', 'Productos Frescos', 'Alimentos frescos que requieren refrigeración', 700.00),
('CON01', 'Productos Congelados', 'Alimentos congelados para conservación prolongada', 800.00),
('CAR01', 'Carnes y Pescados', 'Carne roja, aves y pescados', 900.00),
('LAC01', 'Lácteos y Derivados', 'Productos derivados de la leche', 600.00),
('PAN01', 'Panadería y Pastelería', 'Pan, pasteles y productos horneados', 550.00),
('SNA01', 'Snacks y Dulces', 'Botanas, golosinas y confitería', 400.00),
('COS01', 'Cosméticos y Cuidado Personal', 'Productos para cuidado y belleza personal', 1000.00),
('HIG01', 'Higiene Personal', 'Artículos de higiene y aseo personal', 700.00),
('LIM01', 'Limpieza del Hogar', 'Productos para limpieza y mantenimiento del hogar', 600.00),
('MOB01', 'Mobiliario y Decoración', 'Muebles y artículos decorativos', 2000.00),
('ROP01', 'Ropa y Accesorios', 'Prendas de vestir y accesorios de moda', 1500.00),
('CAL01', 'Calzado', 'Zapatos y artículos relacionados', 1200.00),
('JUG01', 'Juguetes y Juegos', 'Juguetes infantiles y juegos de mesa', 800.00),
('MAT01', 'Material de Oficina', 'Suministros y equipos de oficina', 1100.00),
('PAP01', 'Papelería y Librería', 'Artículos de papelería y libros', 500.00),
('ELE01', 'Electrónica y Electrodomésticos', 'Dispositivos electrónicos y electrodomésticos', 2500.00),
('COM01', 'Computadoras y Accesorios', 'Ordenadores y periféricos', 2200.00),
('TEL01', 'Telefonía Móvil y Accesorios', 'Teléfonos móviles y accesorios', 1800.00),
('JAR01', 'Jardinería y Herramientas de Exterior', 'Herramientas y productos para jardín', 900.00),
('HER01', 'Herramientas y Ferretería', 'Herramientas manuales y eléctricas', 1000.00),
('MAC01', 'Material de Construcción', 'Materiales para construcción y remodelación', 2000.00),
('AUT01', 'Automotriz y Repuestos', 'Vehículos y piezas de repuesto', 3000.00),
('DEP01', 'Artículos Deportivos', 'Equipo y accesorios deportivos', 1200.00),
('CAM01', 'Ropa y Artículos de Cama', 'Sábanas, cobertores y ropa de cama', 800.00),
('PLA01', 'Plásticos y Embalajes', 'Productos plásticos y materiales de embalaje', 700.00),
('AGR01', 'Suministros Agrícolas', 'Equipos y materiales para agricultura', 1500.00),
('EDU01', 'Material Educativo', 'Recursos y materiales educativos', 600.00),
('ECO01', 'Electrodomésticos de Cocina', 'Aparatos eléctricos para cocina', 1400.00),
('CLI01', 'Productos de Calefacción y Climatización', 'Equipos de aire acondicionado y calefacción', 2000.00),
('ILU01', 'Artículos de Iluminación', 'Lámparas y sistemas de iluminación', 900.00),
('AUD01', 'Productos de Audio y Video', 'Equipos de sonido y video', 1600.00),
('HOG01', 'Productos de Cuidado del Hogar', 'Artículos para mantenimiento del hogar', 700.00),
('AUT02', 'Productos para el Cuidado del Automóvil', 'Accesorios y productos de mantenimiento automotriz', 800.00),
('SAL01', 'Artículos para el Cuidado de la Salud', 'Productos de bienestar y salud', 1000.00),
('UNI01', 'Ropa de Trabajo y Uniformes', 'Indumentaria profesional y uniformes', 900.00),
('CON02', 'Material de Construcción Pesado', 'Maquinaria y materiales pesados para construcción', 5000.00),
('FER01', 'Fertilizantes y Productos de Jardinería', 'Productos para cuidado de jardines y cultivos', 800.00),
('CER01', 'Cerámica y Productos de Vidrio', 'Artículos de cerámica y vidrio', 850.00),
('INS01', 'Instrumentos Musicales y de Precisión', 'Instrumentos y equipos especializados', 1800.00),
('SAN01', 'Material Sanitario y Desechables Médicos', 'Suministros médicos no farmacéuticos', 1200.00),
('SEG01', 'Productos de Seguridad Industrial', 'Equipo de protección y seguridad laboral', 1300.00),
('TEC01', 'Productos de Oficina y Tecnología', 'Equipos tecnológicos para oficinas', 1500.00),
('LIB01', 'Libros y Revistas', 'Publicaciones impresas y digitales', 500.00);
UPDATE producto
SET etiquetado = 'PER'
WHERE categoria IN ('FRE01', 'CON01', 'CAR01', 'LAC01', 'PAN01', 'AGR01', 'FER01', 'SAL01')
  AND etiquetado = 'EST';

INSERT INTO tarifa_peso (pesoMin, pesoMax, porcePeso) VALUES
(100.01, 200.00, 0),
(200.01, 300.00, 5),
(300.01, 400.00, 10),
(400.01, 500.00, 15),
(500.01, 600.00, 20),
(600.01, 700.00, 25),
(700.01, 800.00, 30),
(800.01, 900.00, 35),
(900.01, 1000.00, 40),
(1000.01, 1500.00, 45),
(1500.01, 2000.00, 50),
(2000.01, 2500.00, 55),
(2500.01, 3000.00, 60),
(3000.01, 3500.00, 65),
(3500.01, 4000.00, 70),
(4000.01, 4500.00, 75),
(4500.01, 5000.00, 80),
(5000.01, 6000.00, 85),
(6000.01, 7000.00, 90),
(7000.01, 8000.00, 95),
(8000.01, 9000.00, 100),
(9000.01, 10000.00, 105),
(10000.01, 15000.00, 110),
(15000.01, 20000.00, 115),
(20000.01, 25000.00, 120);

INSERT INTO tarifa_distancia (distaMin, distaMax, porceDista) VALUES
(0.01, 1.00, 5),      -- Distancias muy cortas dentro de la misma zona
(1.01, 3.00, 6),      -- Distancias cortas entre colonias cercanas
(3.01, 5.00, 7),      -- Distancias típicas en la ciudad
(5.01, 10.00, 8),     -- De una zona a otra, pero aún en Tijuana
(10.01, 20.00, 10),   -- Cobertura de áreas suburbanas
(20.01, 30.00, 12),   -- Periferia de la ciudad
(30.01, 40.00, 14),   -- Hacia zonas más alejadas
(40.01, 50.00, 16),   -- Alcance hacia áreas cercanas de Baja California
(50.01, 60.00, 18),
(60.01, 70.00, 20),
(70.01, 80.00, 22),   -- Alcance máximo para zonas de Baja California
(80.01, 90.00, 25),
(90.01, 100.00, 28);  -- Límite en zonas de hasta 100 km

INSERT INTO tarifa_volumen (volumenMin, volumenMax, porceVolu) VALUES
(0.01, 1.00, 5),       -- Volumen muy pequeño (ej. paquetes pequeños)
(1.01, 3.00, 6),       -- Volumen pequeño (cajas medianas)
(3.01, 5.00, 8),       -- Volumen mediano (cajas grandes o varios artículos)
(5.01, 10.00, 10),     -- Volumen grande (pallet pequeño)
(10.01, 20.00, 12),    -- Pallet completo
(20.01, 30.00, 15),    -- Pallets grandes o carga voluminosa
(30.01, 40.00, 18),
(40.01, 50.00, 20),    -- Carga en contenedores medianos
(50.01, 60.00, 23),
(60.01, 70.00, 25),    -- Contenedores grandes o varios pallets
(70.01, 80.00, 28),
(80.01, 90.00, 30),
(90.01, 100.00, 35);   -- Volumen máximo para transporte pesado


-- Tabla: disponibilidad
INSERT INTO disponibilidad (codigo, descripcion) VALUES
('DISPO', 'Disponible'),
('NODIS', 'No Disponible'),
('MANTE', 'En Mantenimiento'),
('RESER', 'Reservado');

-- Tabla: cat_vehi
INSERT INTO cat_vehi (codigo, descripcion, tipoCarga) VALUES
('FURGG', 'Furgoneta', "GEN"),
('FURGR', 'Furgoneta de Refrigeracion', 'PER'),
('CARCG', 'Camión Rígido de Caja Cerrada', 'GEN'),
('CARCR', 'Camión Rígido de Caja Cerrada Refrigerada', 'PER'),
('CARTO', 'Camión Rígido de Tolva', 'GRN'),
('CAMRP', 'Camión Rígido de Plataforma', 'GEN'),
('CAMAP', 'Tractocamion Articulado', 'UNV');

-- Tabla: puesto
INSERT INTO puesto (codigo, descripcion) VALUES
('ADM', 'Administrador'),
('CHD', 'Checador'),
('CHF', 'Chofer');

-- Tabla: marca
INSERT INTO marca (codigo, nombre) VALUES 
('TOY', 'Toyota'),
('HON', 'Honda'),
('FOR', 'Ford'),
('CHE', 'Chevrolet'),
('BMW', 'BMW'),
('FRE', 'Freightliner'),
('VOL', 'Volvo'),
('MAN', 'MAN'),
('INT', 'International'),
('KEN', 'Kenworth'),
('SCNA', 'Scania'),
('KRNE', 'Krone'),
('WBER', 'Weber'),
('MAC', 'Mac Trailer');

INSERT INTO ubicacion (nombreUbicacion, nombreCalle, numCalle, colonia, codigoPostal) VALUES
('Almacén Central Tijuana', 'Avenida Revolución', '1234', 'Zona Centro', '22000'),
('Sucursal Otay', 'Boulevard Bellas Artes', '5678', 'Otay Universidad', '22427'),
('Sucursal Playas', 'Paseo Ensenada', '910', 'Playas de Tijuana', '22504'),
('Cliente Maquiladora XYZ', 'Boulevard Insurgentes', '1122', 'Río Tijuana 3ra Etapa', '22226'),
('Proveedor ABC', 'Calle Segunda', '3344', 'Zona Centro', '22000'),
('Punto de Entrega Macroplaza', 'Boulevard Insurgentes', '5566', 'El Lago', '22210'),
('Punto de Entrega Plaza Río', 'Paseo de los Héroes', '7788', 'Zona Río', '22010'),
('Centro de Distribución Este', 'Carretera Libre Tijuana-Tecate', '9900', 'El Florido', '22244'),
('Centro de Distribución Oeste', 'Carretera Escénica Tijuana-Ensenada', '1010', 'San Antonio del Mar', '22565'),
('Almacén de Productos Perecederos', 'Boulevard Díaz Ordaz', '1212', 'La Mesa', '22115'),
('Oficinas Corporativas', 'Paseo de los Héroes', '1313', 'Zona Río', '22010'),
('Centro Logístico Alamar', 'Boulevard Terán Terán', '1414', 'Alamar', '22204'),
('Planta de Producción', 'Carretera Aeropuerto', '1515', 'Mesa de Otay', '22435'),
('Depósito Regional', 'Boulevard Cuauhtémoc Sur', '1616', 'Hipódromo', '22020'),
('Terminal de Carga Aérea', 'Avenida Aeropuerto', '1717', 'Colonia Nueva Tijuana', '22435'),
('Estación de Transferencia', 'Boulevard Gustavo Díaz Ordaz', '1818', 'La Mesa', '22115'),
('Cliente Corporativo ABC', 'Boulevard Agua Caliente', '1919', 'Zona Río', '22420'),
('Proveedor Industrial XYZ', 'Boulevard Benítez', '2020', 'La Mesa', '22105'),
('Punto de Recolección Plaza Monarca', 'Boulevard Manuel J. Clouthier', '2121', 'Chapultepec Alamar', '22195'),
('Centro de Servicio Playas', 'Avenida Del Pacifico', '2222', 'Playas de Tijuana', '22504');

INSERT INTO modelo (codigo, nombre, marca) VALUES
('TOY01', 'Toyota Hiace', 'TOY'),               -- Furgoneta versátil para distribución urbana
('HON01', 'Honda Acty', 'HON'),                 -- Mini camión de carga ligera
('FOR01', 'Ford Transit', 'FOR'),               -- Furgoneta de carga para entregas urbanas y suburbanas
('FOR02', 'Ford E-Transit', 'FOR'),             -- Furgoneta eléctrica para distribución urbana
('CHE01', 'Chevrolet Express Cargo', 'CHE'),    -- Van de carga común en logística urbana
('TOY02', 'Toyota Dyna', 'TOY'),                -- Camión ligero de carga para entregas urbanas
('CHE02', 'Chevrolet N-Series', 'CHE'),         -- Camión ligero de carga mediana para distribución
('VOL01', 'Volvo FL', 'VOL'),                   -- Camión rígido para distribución en áreas urbanas y periurbanas
('MAN01', 'MAN TGL', 'MAN'),                    -- Camión rígido para distribución regional y urbana
('TOY03', 'Toyota Dyna Plataforma', 'TOY'),     -- Versión de plataforma del Toyota Dyna
('VOL03', 'Volvo FL Plataforma', 'VOL'),        -- Volvo FL con plataforma abierta para carga
('MAN03', 'MAN TGL Plataforma', 'MAN'),         -- MAN TGL con plataforma rígida para transporte de materiales
('INT03', 'International CV Plataforma', 'INT'),-- International CV con plataforma para carga diversa
('KEN02', 'Kenworth T370 Plataforma', 'KEN'),    -- Kenworth T370 para transporte de carga en plataforma abierta
('INT01', 'International CV Series', 'INT'),    -- Camión ligero de carga para aplicaciones comerciales
('FRE01', 'Freightliner Cascadia', 'FRE'),      -- Tractocamión para largas distancias y carga pesada
('VOL02', 'Volvo FH16', 'VOL'),                 -- Tractocamión de gran capacidad para transporte de larga distancia
('MAN02', 'MAN TGX', 'MAN'),                    -- Tractocamión diseñado para transporte de carga pesada
('INT02', 'International LT Series', 'INT'),    -- Tractocamión para largas distancias y alta capacidad de carga
('KEN01', 'Kenworth T680', 'KEN'),              -- Tractocamión robusto para cargas pesadas y transporte de larga distancia
('FLAT1', 'Flatbed Standard', 'VOL'),           -- Remolques
('FRIG1', 'Refrigerated 20ft', 'SCNA'),
('BOX40', 'Box Trailer 40ft', 'KRNE'),
('TANK1', 'Tanker', 'WBER'),
('TIPPR', 'Tipper Trailer', 'MAC');

INSERT INTO producto (nombre, descripcion, categoria, etiquetado, alto, ancho, largo, peso, cliente) VALUES
('Caja de cereales (Pallet)', 'Pallet de cajas de cereales integrales', 'ALI01', 'EST', 1.5, 1.0, 1.2, 300, 1),
('Botellas de vino tinto (Caja)', 'Caja con botellas de vino tinto de reserva', 'ALI01', 'FRA', 0.3, 0.25, 0.4, 7, 1),
('Lechugas frescas (Caja)', 'Caja con lechugas orgánicas recién cosechadas', 'FRE01', 'EST', 0.3, 0.25, 0.4, 5, 2),
('Huevos (Pallet)', 'Pallet con cajas de huevos frescos de granja', 'FRE01', 'FRA', 1.5, 1.0, 1.2, 500, 2),
('Pizzas congeladas (Pallet)', 'Pallet de pizzas de pepperoni listas para hornear', 'CON01', 'EST', 1.5, 1.0, 1.2, 500, 3),
('Tartas heladas (Caja)', 'Caja con tartas de queso congeladas delicadas', 'CON01', 'FRA', 0.4, 0.3, 0.4, 8, 3),
('Filetes de res (Caja)', 'Caja con cortes premium de carne de res', 'CAR01', 'EST', 0.3, 0.3, 0.4, 12, 4),
('Pescados frescos (Pallet)', 'Pallet con pescados frescos que requieren cuidado', 'CAR01', 'FRA', 1.2, 1.0, 1.2, 600, 4),
('Quesos cheddar (Caja)', 'Caja con bloques de queso cheddar', 'LAC01', 'EST', 0.3, 0.25, 0.4, 10, 5),
('Yogur en frascos de vidrio (Pallet)', 'Pallet con yogures en envases de vidrio', 'LAC01', 'FRA', 1.2, 1.0, 1.2, 700, 5),
('Panes integrales (Caja)', 'Caja con panes integrales recién horneados', 'PAN01', 'EST', 0.3, 0.25, 0.4, 6, 6),
('Pasteles de boda (Pallet)', 'Pallet con pasteles de boda delicados', 'PAN01', 'FRA', 1.2, 1.0, 1.2, 400, 6),
('Chips de patata (Pallet)', 'Pallet de bolsas de chips de patata clásica', 'SNA01', 'EST', 1.5, 1.0, 1.2, 250, 7),
('Figuras de chocolate (Caja)', 'Caja con figuras de chocolate delicadas', 'SNA01', 'FRA', 0.3, 0.25, 0.3, 5, 7),
('Champús revitalizantes (Pallet)', 'Pallet de champús para todo tipo de cabello', 'COS01', 'EST', 1.5, 1.0, 1.2, 600, 8),
('Perfumes de lujo (Caja)', 'Caja con perfumes en botellas de vidrio', 'COS01', 'FRA', 0.3, 0.25, 0.3, 8, 8),
('Jabones en barra (Caja)', 'Caja con jabones antibacteriales', 'HIG01', 'EST', 0.3, 0.25, 0.3, 10, 9),
('Enjuagues bucales en vidrio (Pallet)', 'Pallet con enjuagues bucales en botellas de vidrio', 'HIG01', 'FRA', 1.2, 1.0, 1.2, 700, 9),
('Detergentes líquidos (Pallet)', 'Pallet de detergentes para ropa', 'LIM01', 'EST', 1.5, 1.0, 1.2, 800, 10),
('Limpiavidrios en vidrio (Caja)', 'Caja con limpiavidrios en envases frágiles', 'LIM01', 'FRA', 0.3, 0.25, 0.4, 8, 10),
('Mesas de centro (Pallet)', 'Pallet con mesas de centro de madera', 'MOB01', 'EST', 2.0, 1.2, 1.5, 500, 1),
('Mesas de vidrio (Pallet)', 'Pallet con mesas de centro de vidrio', 'MOB01', 'FRA', 2.0, 1.2, 1.5, 400, 1),
('Camisetas de algodón (Caja)', 'Caja con camisetas básicas de algodón', 'ROP01', 'EST', 0.4, 0.3, 0.4, 10, 2),
('Collares de perlas (Caja)', 'Caja con collares de perlas naturales', 'ROP01', 'FRA', 0.2, 0.15, 0.2, 3, 2),
('Zapatillas deportivas (Pallet)', 'Pallet de calzado para correr', 'CAL01', 'EST', 1.5, 1.0, 1.2, 400, 3),
('Zapatos de tacón alto (Caja)', 'Caja con zapatos de tacón delicados', 'CAL01', 'FRA', 0.3, 0.25, 0.4, 8, 3),
('Pelotas de fútbol (Pallet)', 'Pallet de balones de fútbol estándar', 'JUG01', 'EST', 1.5, 1.0, 1.2, 250, 4),
('Muñecas de porcelana (Caja)', 'Caja con muñecas coleccionables de porcelana', 'JUG01', 'FRA', 0.3, 0.25, 0.4, 5, 4),
('Papeles para impresora (Pallet)', 'Pallet de resmas de papel A4', 'MAT01', 'EST', 1.2, 1.0, 1.2, 800, 5),
('Organizadores de escritorio de vidrio (Caja)', 'Caja con organizadores de vidrio', 'MAT01', 'FRA', 0.3, 0.25, 0.4, 10, 5),
('Cuadernos de notas (Caja)', 'Caja con cuadernos de espiral', 'PAP01', 'EST', 0.3, 0.25, 0.4, 8, 6),
('Plumas estilográficas de cristal (Caja)', 'Caja con plumas de cristal', 'PAP01', 'FRA', 0.2, 0.15, 0.2, 2, 6),
('Televisores LED 40" (Pallet)', 'Pallet de televisores LED de 40 pulgadas', 'ELE01', 'EST', 1.5, 1.0, 1.2, 800, 7),
('Microondas con puerta de vidrio (Caja)', 'Caja con hornos microondas frágiles', 'ELE01', 'FRA', 0.5, 0.4, 0.5, 18, 7),
('Laptops 15" (Pallet)', 'Pallet de computadoras portátiles de 15 pulgadas', 'COM01', 'EST', 1.2, 1.0, 1.2, 600, 8),
('Monitores LED 24" (Caja)', 'Caja con monitores de pantalla frágil', 'COM01', 'FRA', 0.4, 0.3, 0.4, 10, 8),
('Teléfonos inteligentes (Caja)', 'Caja con smartphones de última generación', 'TEL01', 'EST', 0.3, 0.2, 0.2, 6, 9),
('Protectores de pantalla de vidrio (Caja)', 'Caja con protectores de pantalla frágiles', 'TEL01', 'FRA', 0.2, 0.15, 0.1, 2, 9),
('Tijeras de podar (Caja)', 'Caja con herramientas para poda', 'JAR01', 'EST', 0.4, 0.3, 0.5, 15, 10),
('Esferas decorativas de vidrio (Caja)', 'Caja con adornos de vidrio para jardín', 'JAR01', 'FRA', 0.3, 0.25, 0.3, 8, 10),
('Martillos estándar (Pallet)', 'Pallet de martillos de uso general', 'HER01', 'EST', 1.2, 1.0, 1.2, 800, 1),
('Niveles de burbuja de vidrio (Caja)', 'Caja con niveles de burbuja frágiles', 'HER01', 'FRA', 0.3, 0.25, 0.3, 5, 1),
('Sacos de cemento (Pallet)', 'Pallet de cemento para construcción', 'MAC01', 'EST', 1.0, 1.0, 1.2, 2000, 2),
('Azulejos de cerámica (Pallet)', 'Pallet de azulejos frágiles', 'MAC01', 'FRA', 1.2, 1.0, 1.2, 1500, 2),
('Filtros de aceite (Caja)', 'Caja con filtros para motor', 'AUT01', 'EST', 0.3, 0.3, 0.4, 8, 3),
('Parabrisas (Pallet)', 'Pallet de cristales frontales para automóvil', 'AUT01', 'FRA', 1.5, 1.2, 0.2, 500, 3),
('Raquetas de tenis (Caja)', 'Caja con raquetas para jugadores intermedios', 'DEP01', 'EST', 0.6, 0.3, 0.7, 10, 4),
('Gafas de natación (Caja)', 'Caja con gafas de lente de vidrio', 'DEP01', 'FRA', 0.2, 0.15, 0.2, 3, 4),
('Sábanas de algodón (Caja)', 'Caja con sábanas para cama matrimonial', 'CAM01', 'EST', 0.4, 0.3, 0.5, 8, 5),
('Lámparas de mesita de noche (Caja)', 'Caja con lámparas de vidrio', 'CAM01', 'FRA', 0.4, 0.3, 0.4, 6, 5),
('Botellas de plástico (Pallet)', 'Pallet de botellas reutilizables', 'PLA01', 'EST', 1.5, 1.0, 1.2, 300, 6),
('Cajas de embalaje con burbujas (Caja)', 'Caja frágil para envío de productos', 'PLA01', 'FRA', 0.3, 0.25, 0.3, 4, 6),
('Semillas de maíz (Saco)', 'Saco con semillas para siembra', 'AGR01', 'EST', 0.8, 0.5, 0.3, 40, 7),
('Termómetros de suelo de vidrio (Caja)', 'Caja con instrumentos frágiles', 'AGR01', 'FRA', 0.3, 0.2, 0.2, 3, 7),
('Libros de texto (Pallet)', 'Pallet de libros educativos', 'EDU01', 'EST', 1.0, 1.0, 1.2, 700, 8),
('Microscopios escolares (Caja)', 'Caja con instrumentos ópticos frágiles', 'EDU01', 'FRA', 0.4, 0.3, 0.4, 8, 8),
('Licuadoras (Pallet)', 'Pallet de licuadoras multifunción', 'ECO01', 'EST', 1.2, 1.0, 1.2, 500, 9),
('Juegos de copas de vino (Caja)', 'Caja con copas de vidrio frágiles', 'ECO01', 'FRA', 0.4, 0.3, 0.4, 8, 9),
('Ventiladores de piso (Pallet)', 'Pallet de ventiladores con varias velocidades', 'CLI01', 'EST', 1.5, 1.0, 1.2, 500, 10),
('Paneles calefactores de vidrio (Caja)', 'Caja con equipos de calefacción frágiles', 'CLI01', 'FRA', 0.6, 0.5, 0.4, 15, 10),
('Bombillas LED (Caja)', 'Caja con bombillas de bajo consumo', 'ILU01', 'EST', 0.3, 0.25, 0.3, 5, 1),
('Lámparas de techo de cristal (Caja)', 'Caja con lámparas colgantes frágiles', 'ILU01', 'FRA', 0.5, 0.4, 0.5, 10, 1),
('Altavoces portátiles (Pallet)', 'Pallet de altavoces Bluetooth resistentes', 'AUD01', 'EST', 1.2, 1.0, 1.2, 400, 2),
('Proyectores de cine en casa (Caja)', 'Caja con proyectores de lentes frágiles', 'AUD01', 'FRA', 0.4, 0.3, 0.3, 8, 2),
('Ambientadores de aire (Caja)', 'Caja con sprays aromatizantes', 'HOG01', 'EST', 0.3, 0.25, 0.3, 6, 3),
('Termómetros de pared de vidrio (Caja)', 'Caja con termómetros frágiles', 'HOG01', 'FRA', 0.2, 0.15, 0.2, 3, 3),
('Cera para autos (Caja)', 'Caja con productos para pulir vehículos', 'AUT02', 'EST', 0.3, 0.25, 0.4, 8, 4),
('Espejos retrovisores (Caja)', 'Caja con espejos frágiles para automóvil', 'AUT02', 'FRA', 0.3, 0.2, 0.2, 5, 4),
('Vitaminas (Caja)', 'Caja con suplementos alimenticios', 'SAL01', 'EST', 0.3, 0.25, 0.3, 5, 5),
('Termómetros clínicos de vidrio (Caja)', 'Caja con termómetros frágiles', 'SAL01', 'FRA', 0.2, 0.15, 0.1, 2, 5),
('Uniformes de seguridad (Caja)', 'Caja con ropa para trabajo industrial', 'UNI01', 'EST', 0.4, 0.3, 0.4, 12, 6),
('Gafas de protección con vidrio (Caja)', 'Caja con equipos frágiles para protección ocular', 'UNI01', 'FRA', 0.2, 0.15, 0.1, 3, 6),
('Vigas de acero (Pallet)', 'Pallet con materiales estructurales', 'CON02', 'EST', 2.0, 1.0, 6.0, 5000, 7),
('Ventanas de vidrio templado (Pallet)', 'Pallet con elementos frágiles para construcción', 'CON02', 'FRA', 2.0, 1.2, 1.0, 1000, 7),
('Abonos orgánicos (Pallet)', 'Pallet de fertilizantes para plantas', 'FER01', 'EST', 1.0, 1.0, 1.2, 900, 8),
('Macetas de cerámica (Caja)', 'Caja con macetas frágiles para plantas', 'FER01', 'FRA', 0.4, 0.3, 0.4, 12, 8),
('Platos de cerámica (Caja)', 'Caja con platos resistentes para uso diario', 'CER01', 'EST', 0.3, 0.25, 0.3, 8, 9),
('Floreros de vidrio (Caja)', 'Caja con floreros decorativos frágiles', 'CER01', 'FRA', 0.4, 0.3, 0.4, 6, 9),
('Guitarras acústicas (Pallet)', 'Pallet de instrumentos musicales de cuerda', 'INS01', 'EST', 1.5, 1.0, 1.2, 400, 10),
('Violines (Caja)', 'Caja con instrumentos frágiles de precisión', 'INS01', 'FRA', 0.5, 0.4, 0.2, 5, 10),
('Guantes desechables (Caja)', 'Caja con guantes de látex', 'SAN01', 'EST', 0.3, 0.25, 0.3, 5, 1),
('Jeringas de vidrio (Caja)', 'Caja con suministros médicos frágiles', 'SAN01', 'FRA', 0.2, 0.15, 0.1, 2, 1),
('Cascos de seguridad (Pallet)', 'Pallet de equipos de protección para la cabeza', 'SEG01', 'EST', 1.2, 1.0, 1.2, 600, 2),
('Máscaras de soldadura con vidrio (Caja)', 'Caja con equipos frágiles para soldadores', 'SEG01', 'FRA', 0.4, 0.3, 0.3, 8, 2),
('Impresoras láser (Pallet)', 'Pallet de impresoras para oficinas', 'TEC01', 'EST', 1.2, 1.0, 1.2, 700, 3),
('Escáneres de documentos (Caja)', 'Caja con equipos frágiles para digitalización', 'TEC01', 'FRA', 0.4, 0.3, 0.4, 12, 3),
('Revistas mensuales (Caja)', 'Caja con publicaciones periódicas', 'LIB01', 'EST', 0.3, 0.25, 0.3, 6, 4),
('Libros de edición coleccionista (Caja)', 'Caja con libros frágiles de edición limitada', 'LIB01', 'FRA', 0.4, 0.3, 0.4, 8, 4);


INSERT INTO estado_emple (codigo, descripcion) VALUES
('ACT', 'Activo'),
('ENR', 'En Entrega'),
('RES', 'Reservado'),
('SUS', 'Suspendido'),
('VAC', 'De Vacaciones'),
('RET', 'Retirado');

INSERT INTO empleado (nombre, primerApe, segundoApe, telefono, email, password, puesto, estadoEmpleado) VALUES 
('Carlos', 'García', 'Hernández', '5551234567', 'carlos.gh@example.com', sha2('123', 256), 'ADM', 'ACT'),
('María', 'López', 'Rodríguez', '5559876543', 'maria.lr@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Juan', 'Martínez', 'Sánchez', '5551112233', 'juan.ms@example.com', sha2('123', 256), 'CHD', 'ACT'),
('Ana', 'Pérez', 'Jiménez', '5554445566', 'ana.pj@example.com', sha2('contraseña123', 256), 'ADM', 'ACT'),
('Luis', 'Ramírez', 'González', '5556667788', 'luis.rg@example.com', sha2('contraseña123', 256), 'CHF', 'ACT');

-- Furgonetas de Carga General (FURGG): Vehículos ligeros, versátiles para la distribución urbana.
INSERT INTO vehiculo (numSerie, gasXKM, capacidadCarga, kilometraje, costoAcumulado, marca, modelo, categoriavehiculo, disponibilidad) VALUES
('SNTOY01HIACE001', 0.08, 1000, 50000, 0, 'TOY', 'TOY01', 'FURGG', 'DISPO'), -- Toyota Hiace
('SNHON01ACTY001', 0.06, 500, 30000, 0, 'HON', 'HON01', 'FURGG', 'DISPO'), -- Honda Acty
('SNFOR01TRANSIT001', 0.09, 1200, 60000, 0, 'FOR', 'FOR01', 'FURGG', 'DISPO'), -- Ford Transit
('SNFOR02ETRANSIT001', 0.00, 1100, 20000, 0, 'FOR', 'FOR02', 'FURGG', 'DISPO'), -- Ford E-Transit (Eléctrico)
('SNCHE01EXPRESS001', 0.10, 1300, 55000, 0, 'CHE', 'CHE01', 'FURGG', 'DISPO'); -- Chevrolet Express Cargo

-- Camiones Rígidos de Carga General (CARCG): Vehículos pesados para distribución urbana y regional.
INSERT INTO vehiculo (numSerie, gasXKM, capacidadCarga, kilometraje, costoAcumulado, marca, modelo, categoriavehiculo, disponibilidad) VALUES
('SNTOY02DYNA001', 0.12, 2000, 70000, 0, 'TOY', 'TOY02', 'CARCG', 'DISPO'), -- Toyota Dyna
('SNCHE02NSERIES001', 0.11, 2500, 65000, 0, 'CHE', 'CHE02', 'CARCG', 'DISPO'), -- Chevrolet N-Series
('SNVOL01FL001', 0.15, 5000, 80000, 0, 'VOL', 'VOL01', 'CARCG', 'DISPO'), -- Volvo FL
('SNMAN01TGL001', 0.14, 4500, 75000, 0, 'MAN', 'MAN01', 'CARCG', 'DISPO'), -- MAN TGL
('SNINT01CVSERIES001', 0.13, 4000, 60000, 0, 'INT', 'INT01', 'CARCG', 'DISPO'); -- International CV Series

-- Camiones Rígidos con Plataforma (CAMRP): Camiones de plataforma ideales para transportar materiales grandes.
INSERT INTO vehiculo (numSerie, gasXKM, capacidadCarga, kilometraje, costoAcumulado, marca, modelo, categoriavehiculo, disponibilidad) VALUES
('SNTOY03DYNAPLAT001', 0.12, 2200, 40000, 0, 'TOY', 'TOY03', 'CAMRP', 'DISPO'), -- Toyota Dyna Plataforma
('SNVOL03FLPLAT001', 0.15, 5200, 60000, 0, 'VOL', 'VOL03', 'CAMRP', 'DISPO'), -- Volvo FL Plataforma
('SNMAN03TGLPLAT001', 0.14, 4800, 55000, 0, 'MAN', 'MAN03', 'CAMRP', 'DISPO'), -- MAN TGL Plataforma
('SNINT03CVPLAT001', 0.13, 4600, 50000, 0, 'INT', 'INT03', 'CAMRP', 'DISPO'), -- International CV Plataforma
('SNKEN02T370PLAT001', 0.14, 4700, 45000, 0, 'KEN', 'KEN02', 'CAMRP', 'DISPO'); -- Kenworth T370 Plataforma

-- Tractocamiones para Largas Distancias (CAMAP): Vehículos de gran capacidad para el transporte de carga pesada y viajes de larga distancia.
INSERT INTO vehiculo (numSerie, gasXKM, capacidadCarga, kilometraje, costoAcumulado, marca, modelo, categoriavehiculo, disponibilidad) VALUES
('SNFRE01CASCADIA001', 0.30, 20000, 120000, 0, 'FRE', 'FRE01', 'CAMAP', 'DISPO'), -- Freightliner Cascadia
('SNVOL02FH16001', 0.32, 22000, 110000, 0, 'VOL', 'VOL02', 'CAMAP', 'DISPO'), -- Volvo FH16
('SNMAN02TGX001', 0.31, 21000, 115000, 0, 'MAN', 'MAN02', 'CAMAP', 'DISPO'), -- MAN TGX
('SNINT02LTSERIES001', 0.29, 20000, 105000, 0, 'INT', 'INT02', 'CAMAP', 'DISPO'), -- International LT Series
('SNKEN01T680001', 0.30, 21000, 100000, 0, 'KEN', 'KEN01', 'CAMAP', 'DISPO'); -- Kenworth T680

-- Camión Rígido de Caja Cerrada Refrigerada (Categoría: CARCR)
INSERT INTO vehiculo (numSerie, gasXKM, capacidadCarga, kilometraje, costoAcumulado, marca, modelo, categoriavehiculo, disponibilidad) VALUES
('SNTOY02DYNA001', 0.32, 3500, 10000, 0, 'TOY', 'TOY02', 'CARCR', 'DISPO'), -- Toyota Dyna
('SNVOL01FL001', 0.33, 4000, 12000, 0, 'VOL', 'VOL01', 'CARCR', 'DISPO'), -- Volvo FL
('SNMAN01TGL001', 0.30, 3800, 8000, 0, 'MAN', 'MAN01', 'CARCR', 'DISPO'), -- MAN TGL
('SNCHE02NSER001', 0.31, 3700, 9500, 0, 'CHE', 'CHE02', 'CARCR', 'DISPO'), -- Chevrolet N-Series
('SNTOY02DYNA002', 0.29, 3600, 11000, 0, 'TOY', 'TOY02', 'CARCR', 'DISPO'); -- Toyota Dyna

-- Camión Rígido de Tolva (Categoría: CARTO)
INSERT INTO vehiculo (numSerie, gasXKM, capacidadCarga, kilometraje, costoAcumulado, marca, modelo, categoriavehiculo, disponibilidad) VALUES
('SNKEN02T370001', 0.28, 7500, 15000, 0, 'KEN', 'KEN02', 'CARTO', 'DISPO'), -- Kenworth T370
('SNMAN03TGL001', 0.27, 7200, 14500, 0, 'MAN', 'MAN03', 'CARTO', 'DISPO'), -- MAN TGL Plataforma
('SNVOL03FL001', 0.30, 7100, 14000, 0, 'VOL', 'VOL03', 'CARTO', 'DISPO'), -- Volvo FL Plataforma
('SNINT03CV001', 0.29, 7000, 13000, 0, 'INT', 'INT03', 'CARTO', 'DISPO'), -- International CV Plataforma
('SNCHE02NSER002', 0.28, 7400, 15500, 0, 'CHE', 'CHE02', 'CARTO', 'DISPO'); -- Chevrolet N-Series

-- Furgoneta de Refrigeración (Categoría: FURGR)
INSERT INTO vehiculo (numSerie, gasXKM, capacidadCarga, kilometraje, costoAcumulado, marca, modelo, categoriavehiculo, disponibilidad) VALUES
('SNTOY01HIACE001', 0.35, 1500, 5000, 0, 'TOY', 'TOY01', 'FURGR', 'DISPO'), -- Toyota Hiace
('SNHON01ACTY001', 0.34, 1300, 6000, 0, 'HON', 'HON01', 'FURGR', 'DISPO'), -- Honda Acty
('SNFOR01TRANSIT001', 0.33, 1700, 7000, 0, 'FOR', 'FOR01', 'FURGR', 'DISPO'), -- Ford Transit
('SNFOR02ETRANSIT001', 0.32, 1600, 7500, 0, 'FOR', 'FOR02', 'FURGR', 'DISPO'), -- Ford E-Transit
('SNCHE01EXP001', 0.31, 1550, 8000, 0, 'CHE', 'CHE01', 'FURGR', 'DISPO'); -- Chevrolet Express Cargo

INSERT INTO tipo_remolque (codigo, descripcion) VALUES
('TR001', 'Remolque Caja Cerrada'),
('TR002', 'Remolque Refrigerado'),
('TR003', 'Remolque Plataforma');

INSERT INTO remolque (numSerie, capacidadCarga, disponibilidad) VALUES 
('No Aplica', 0, 'DISPO');

INSERT INTO remolque (numSerie, alto, largo, ancho, capacidadCarga, costoAcumulado, marca, modelo, tipoCarga, tipoRemolque, disponibilidad) VALUES
-- Flatbed Remolques (General - Remolque Plataforma)
('SN12350VOLV', 2.5, 13.6, 2.4, 25000, 1100.0, 'VOL', 'FLAT1', 'GEN', 'TR003', 'DISPO'), -- Flatbed 1
('SN12351VOLV', 2.5, 13.6, 2.4, 26000, 1150.0, 'VOL', 'FLAT1', 'GEN', 'TR003', 'DISPO'), -- Flatbed 2
('SN12352VOLV', 2.5, 13.6, 2.4, 24000, 1200.0, 'VOL', 'FLAT1', 'GEN', 'TR003', 'DISPO'), -- Flatbed 3
('SN12353VOLV', 2.5, 13.6, 2.4, 25000, 1250.0, 'VOL', 'FLAT1', 'GEN', 'TR003', 'DISPO'), -- Flatbed 4
('SN12354VOLV', 2.5, 13.6, 2.4, 24500, 1300.0, 'VOL', 'FLAT1', 'GEN', 'TR003', 'DISPO'), -- Flatbed 5
-- Refrigerated Remolques (Perecedero - Remolque Refrigerado)
('SN12355SCNA', 2.8, 6.0, 2.5, 18000, 1400.0, 'SCNA', 'FRIG1', 'PER', 'TR002', 'DISPO'), -- Refrigerated 1
('SN12356SCNA', 2.8, 6.0, 2.5, 18500, 1450.0, 'SCNA', 'FRIG1', 'PER', 'TR002', 'DISPO'), -- Refrigerated 2
('SN12357SCNA', 2.8, 6.0, 2.5, 17500, 1500.0, 'SCNA', 'FRIG1', 'PER', 'TR002', 'DISPO'), -- Refrigerated 3
('SN12358SCNA', 2.8, 6.0, 2.5, 18000, 1550.0, 'SCNA', 'FRIG1', 'PER', 'TR002', 'DISPO'), -- Refrigerated 4
('SN12359SCNA', 2.8, 6.0, 2.5, 19000, 1600.0, 'SCNA', 'FRIG1', 'PER', 'TR002', 'DISPO'), -- Refrigerated 5
-- Box Remolques (General - Remolque Estándar)
('SN12360KRNE', 2.5, 12.0, 2.4, 22000, 1700.0, 'KRNE', 'BOX40', 'GEN', 'TR001', 'DISPO'), -- Box 1
('SN12361KRNE', 2.5, 12.0, 2.4, 22500, 1750.0, 'KRNE', 'BOX40', 'GEN', 'TR001', 'DISPO'), -- Box 2
('SN12362KRNE', 2.5, 12.0, 2.4, 23000, 1800.0, 'KRNE', 'BOX40', 'GEN', 'TR001', 'DISPO'), -- Box 3
('SN12363KRNE', 2.5, 12.0, 2.4, 21500, 1850.0, 'KRNE', 'BOX40', 'GEN', 'TR001', 'DISPO'), -- Box 4
('SN12364KRNE', 2.5, 12.0, 2.4, 22000, 1900.0, 'KRNE', 'BOX40', 'GEN', 'TR001', 'DISPO'), -- Box 5
-- Tanker Remolques (Granel - Remolque Estándar)
('SN12365WBER', 3.0, 14.0, 2.6, 30000, 2000.0, 'WBER', 'TANK1', 'GRN', 'TR001', 'DISPO'), -- Tanker 1
('SN12366WBER', 3.0, 14.0, 2.6, 31000, 2100.0, 'WBER', 'TANK1', 'GRN', 'TR001', 'DISPO'), -- Tanker 2
('SN12367WBER', 3.0, 14.0, 2.6, 29500, 2200.0, 'WBER', 'TANK1', 'GRN', 'TR001', 'DISPO'), -- Tanker 3
('SN12368WBER', 3.0, 14.0, 2.6, 30500, 2300.0, 'WBER', 'TANK1', 'GRN', 'TR001', 'DISPO'), -- Tanker 4
('SN12369WBER', 3.0, 14.0, 2.6, 30000, 2400.0, 'WBER', 'TANK1', 'GRN', 'TR001', 'DISPO'), -- Tanker 5
-- Tipper Remolques (Granel - Remolque Estándar)
('SN12370MAC', 2.4, 13.5, 2.5, 28000, 2500.0, 'MAC', 'TIPPR', 'GRN', 'TR001', 'DISPO'), -- Tipper 1
('SN12371MAC', 2.4, 13.5, 2.5, 27500, 2600.0, 'MAC', 'TIPPR', 'GRN', 'TR001', 'DISPO'), -- Tipper 2
('SN12372MAC', 2.4, 13.5, 2.5, 28500, 2700.0, 'MAC', 'TIPPR', 'GRN', 'TR001', 'DISPO'), -- Tipper 3
('SN12373MAC', 2.4, 13.5, 2.5, 29000, 2800.0, 'MAC', 'TIPPR', 'GRN', 'TR001', 'DISPO'), -- Tipper 4
('SN12374MAC', 2.4, 13.5, 2.5, 28000, 2900.0, 'MAC', 'TIPPR', 'GRN', 'TR001', 'DISPO'); -- Tipper 5

-- Insertar registros en mantenimiento
INSERT INTO mantenimiento (fechas, costo, descripcion, vehiculo, remolque) VALUES
-- Mantenimientos de Vehículos
('2024-01-05', 7500.00, 'Cambio de aceite y filtro', 1, NULL), -- Vehículo 1
('2024-01-10', 12000.00, 'Alineación y balanceo', 2, NULL), -- Vehículo 2
('2024-01-15', 5000.00, 'Revisión de frenos', 3, NULL), -- Vehículo 3
('2024-01-20', 8000.00, 'Cambio de neumáticos', 4, NULL), -- Vehículo 4
('2024-02-05', 15000.00, 'Reparación del sistema eléctrico', 5, NULL), -- Vehículo 5
('2024-02-10', 20000.00, 'Mantenimiento preventivo', 6, NULL), -- Vehículo 6
('2024-02-15', 18000.00, 'Reparación de motor', 7, NULL), -- Vehículo 7
('2024-02-20', 9000.00, 'Cambio de batería', 8, NULL), -- Vehículo 8
('2024-03-05', 6500.00, 'Cambio de filtro de aire', 9, NULL), -- Vehículo 9
('2024-03-10', 11000.00, 'Revisión general', 10, NULL), -- Vehículo 10
-- Mantenimientos de Remolques
('2024-01-07', 5000.00, 'Reparación de ejes', NULL, 1), -- Remolque 1
('2024-01-12', 7000.00, 'Cambio de llantas', NULL, 2), -- Remolque 2
('2024-01-17', 4500.00, 'Mantenimiento del sistema de refrigeración', NULL, 6), -- Remolque 6
('2024-01-22', 8000.00, 'Reparación de puertas', NULL, 11), -- Remolque 11
('2024-02-07', 6000.00, 'Revisión del sistema eléctrico', NULL, 16), -- Remolque 16
('2024-02-12', 9500.00, 'Cambio de suspensión', NULL, 21), -- Remolque 21
('2024-02-17', 4000.00, 'Reparación de luces', NULL, 7), -- Remolque 7
('2024-02-22', 5500.00, 'Ajuste de frenos', NULL, 12), -- Remolque 12
('2024-03-07', 7500.00, 'Mantenimiento general', NULL, 17), -- Remolque 17
('2024-03-12', 8500.00, 'Reemplazo de piso', NULL, 22); -- Remolque 22

INSERT INTO cliente_ubi (cliente, ubicacion, fechaRegistro) VALUES
(1,4, CURRENT_DATE),
(1,3, CURRENT_DATE),
(1,6, CURRENT_DATE);


INSERT INTO empleado (nombre, primerApe, segundoApe, telefono, email, password, puesto, estadoEmpleado) VALUES
('María', 'Pérez', 'Hernández', '5551234567', 'maria.ph@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Carlos', 'Sánchez', 'López', '5559876543', 'carlos.sl@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Ana', 'Martínez', 'García', '5551112233', 'ana.mg@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Jorge', 'Ruiz', 'Torres', '5555555555', 'jorge.rt@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Laura', 'Castro', 'Ortiz', '5557778899', 'laura.co@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Sofía', 'Gómez', 'Flores', '5552223344', 'sofia.gf@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Diego', 'Morales', 'Vargas', '5558889999', 'diego.mv@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Paola', 'Ríos', 'Velázquez', '5554445566', 'paola.rv@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Antonio', 'López', 'Ramírez', '5556667788', 'antonio.lr@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Gabriela', 'Hernández', 'Gómez', '5553334455', 'gabriela.hg@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Ricardo', 'Fernández', 'Pérez', '5559991111', 'ricardo.fp@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Valeria', 'Medina', 'Santos', '5551212121', 'valeria.ms@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Esteban', 'Vargas', 'Mendoza', '5551313131', 'esteban.vm@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Lucía', 'Cruz', 'Álvarez', '5551414141', 'lucia.ca@example.com', sha2('123', 256), 'CHF', 'ACT'),
('Pedro', 'Navarro', 'Blanco', '5551515151', 'pedro.nb@example.com', sha2('123', 256), 'CHF', 'ACT');

INSERT INTO ubicacion (nombreUbicacion, nombreCalle, numCalle, colonia, codigoPostal) VALUES
('Centro de Acopio Tijuana', 'Boulevard Federico Benítez', '2323', 'Los Álamos', '22226'),
('Sucursal La Gloria', 'Carretera Libre Tijuana-Ensenada', '2424', 'La Gloria', '22600'),
('Centro de Operaciones Frontera', 'Avenida Internacional', '2525', 'Zona Norte', '22010'),
('Punto de Entrega Las Américas', 'Boulevard Las Américas', '2626', 'Otay Galerías', '22457'),
('Almacén Lomas Verdes', 'Calle Principal', '2727', 'Lomas Verdes', '22615'),
('Centro de Distribución Valle Sur', 'Boulevard Casa Blanca', '2828', 'El Lago', '22210'),
('Terminal de Carga Sur', 'Boulevard Fundadores', '2929', 'Buena Vista', '22000'),
('Cliente Industrial Omega', 'Calle 16 de Septiembre', '3030', 'El Refugio', '22115'),
('Proveedor Logístico Delta', 'Boulevard Rosas Magallón', '3131', 'Los Altos', '22134'),
('Plataforma de Envíos Oriente', 'Carretera Federal 2D', '3232', 'Valle Verde', '22226'),
('Oficinas Zona Norte', 'Avenida Constitución', '3333', 'Zona Centro', '22000'),
('Sucursal Jardines de la Mesa', 'Boulevard Díaz Ordaz', '3434', 'Jardines de la Mesa', '22115'),
('Centro de Almacenaje San Antonio', 'Carretera Escénica', '3535', 'San Antonio del Mar', '22565'),
('Estación de Transferencia Las Palmas', 'Callejón Palmas', '3636', 'Las Palmas', '22430'),
('Cliente Comercial Sigma', 'Avenida Ermita Sur', '3737', 'El Rubí', '22116'),
('Proveedor General Beta', 'Calle Ocampo', '3838', 'Zona Centro', '22000'),
('Punto de Recolección Plaza Otay', 'Boulevard Industrial', '3939', 'Otay Centenario', '22457'),
('Depósito Parque Industrial', 'Avenida De Los Maestros', '4040', 'Parque Industrial Pacífico', '22643'),
('Centro Logístico Pacifico', 'Boulevard Insurgentes', '4141', 'Río Tijuana', '22226'),
('Sucursal Insurgentes Norte', 'Boulevard Cucapah', '4242', 'Mariano Matamoros Norte', '22234');
