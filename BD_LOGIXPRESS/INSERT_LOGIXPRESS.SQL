INSERT INTO CLIENTE (nomEmpresa, nomPila, primerApe, segundoApe, numTelefono, email, password) VALUES
('Transporte Seguro S.A.', 'Juan', 'Pérez', 'García', '5551234567', 'juan.perez@ejemplo.com', 'contraseña123'),
('Logística Global', 'María', 'López', NULL, '5559876543', 'maria.lopez@ejemplo.com', 'segura456'),
('Envíos Rápidos', 'Carlos', 'Rodríguez', 'Martínez', '5555555555', 'carlos.rodriguez@ejemplo.com', 'envio789'),
('Distribuciones MX', 'Ana', 'Gómez', 'Hernández', '5553332211', 'ana.gomez@ejemplo.com', 'distribucion101'),
('Carga Express', 'Luis', 'Fernández', 'Díaz', '5557778899', 'luis.fernandez@ejemplo.com', 'express202');

INSERT INTO ESTADO_ENTRE (descripcion) VALUES
('pendiente'),
('en proceso'),
('completado');

INSERT INTO PRIORIDAD (descripcion, prioridadPorce) VALUES
('Baja', 10.00),
('Media', 12.50),
('Alta', 15.00);

INSERT INTO TIPO_CARGA (codigo, descripcion) VALUES
('GRN', 'Granel'),
('PER', 'Perecedero'),
('GEN', 'General');

INSERT INTO CAT_PROD (codigo, nombre, descripcion, categoriaPrecio) VALUES
('ALI01', 'Alimentos y Bebidas', 'Productos alimenticios y bebidas variadas', 5000.00),
('FRE01', 'Productos Frescos', 'Alimentos frescos que requieren refrigeración', 7000.00),
('CON01', 'Productos Congelados', 'Alimentos congelados para conservación prolongada', 8000.00),
('CAR01', 'Carnes y Pescados', 'Carne roja, aves y pescados', 9000.00),
('LAC01', 'Lácteos y Derivados', 'Productos derivados de la leche', 6000.00),
('PAN01', 'Panadería y Pastelería', 'Pan, pasteles y productos horneados', 5500.00),
('SNA01', 'Snacks y Dulces', 'Botanas, golosinas y confitería', 4000.00),
('COS01', 'Cosméticos y Cuidado Personal', 'Productos para cuidado y belleza personal', 10000.00),
('HIG01', 'Higiene Personal', 'Artículos de higiene y aseo personal', 7000.00),
('LIM01', 'Limpieza del Hogar', 'Productos para limpieza y mantenimiento del hogar', 6500.00),
('MOB01', 'Mobiliario y Decoración', 'Muebles y artículos decorativos', 20000.00),
('ROP01', 'Ropa y Accesorios', 'Prendas de vestir y accesorios de moda', 15000.00),
('CAL01', 'Calzado', 'Zapatos y artículos relacionados', 12000.00),
('JUG01', 'Juguetes y Juegos', 'Juguetes infantiles y juegos de mesa', 8000.00),
('MAT01', 'Material de Oficina', 'Suministros y equipos de oficina', 11000.00),
('PAP01', 'Papelería y Librería', 'Artículos de papelería y libros', 5000.00),
('ELE01', 'Electrónica y Electrodomésticos', 'Dispositivos electrónicos y electrodomésticos', 25000.00),
('COM01', 'Computadoras y Accesorios', 'Ordenadores y periféricos', 22000.00),
('TEL01', 'Telefonía Móvil y Accesorios', 'Teléfonos móviles y accesorios', 18000.00),
('JAR01', 'Jardinería y Herramientas de Exterior', 'Herramientas y productos para jardín', 9000.00),
('HER01', 'Herramientas y Ferretería', 'Herramientas manuales y eléctricas', 10000.00),
('MAC01', 'Material de Construcción', 'Materiales para construcción y remodelación', 20000.00),
('AUT01', 'Automotriz y Repuestos', 'Vehículos y piezas de repuesto', 30000.00),
('DEP01', 'Artículos Deportivos', 'Equipo y accesorios deportivos', 12000.00),
('CAM01', 'Ropa y Artículos de Cama', 'Sábanas, cobertores y ropa de cama', 8000.00),
('PLA01', 'Plásticos y Embalajes', 'Productos plásticos y materiales de embalaje', 7000.00),
('AGR01', 'Suministros Agrícolas', 'Equipos y materiales para agricultura', 15000.00),
('EDU01', 'Material Educativo', 'Recursos y materiales educativos', 6000.00),
('ECO01', 'Electrodomésticos de Cocina', 'Aparatos eléctricos para cocina', 14000.00),
('CLI01', 'Productos de Calefacción y Climatización', 'Equipos de aire acondicionado y calefacción', 20000.00),
('ILU01', 'Artículos de Iluminación', 'Lámparas y sistemas de iluminación', 9000.00),
('AUD01', 'Productos de Audio y Video', 'Equipos de sonido y video', 16000.00),
('HOG01', 'Productos de Cuidado del Hogar', 'Artículos para mantenimiento del hogar', 7000.00),
('AUT02', 'Productos para el Cuidado del Automóvil', 'Accesorios y productos de mantenimiento automotriz', 8000.00),
('SAL01', 'Artículos para el Cuidado de la Salud', 'Productos de bienestar y salud', 10000.00),
('UNI01', 'Ropa de Trabajo y Uniformes', 'Indumentaria profesional y uniformes', 9000.00),
('CON02', 'Material de Construcción Pesado', 'Maquinaria y materiales pesados para construcción', 50000.00),
('FER01', 'Fertilizantes y Productos de Jardinería', 'Productos para cuidado de jardines y cultivos', 8000.00),
('CER01', 'Cerámica y Productos de Vidrio', 'Artículos de cerámica y vidrio', 8500.00),
('INS01', 'Instrumentos Musicales y de Precisión', 'Instrumentos y equipos especializados', 18000.00),
('SAN01', 'Material Sanitario y Desechables Médicos', 'Suministros médicos no farmacéuticos', 12000.00),
('SEG01', 'Productos de Seguridad Industrial', 'Equipo de protección y seguridad laboral', 13000.00),
('TEC01', 'Productos de Oficina y Tecnología', 'Equipos tecnológicos para oficinas', 15000.00),
('LIB01', 'Libros y Revistas', 'Publicaciones impresas y digitales', 5000.00);

INSERT INTO ETIQUETADO (codigo, descripcion, etiquetadoPrecio) VALUES
('EST', 'Estándar', 0.00),
('FRA', 'Frágil', 1000.00);

INSERT INTO TARIFA_PESO (pesoMin, pesoMax, porcePeso) VALUES
(100.01, 200.00, 10),
(200.01, 300.00, 11),
(300.01, 400.00, 12),
(400.01, 500.00, 13),
(500.01, 600.00, 14),
(600.01, 700.00, 15),
(700.01, 800.00, 16),
(800.01, 900.00, 17),
(900.01, 1000.00, 18),
(1000.01, 1500.00, 20),
(1500.01, 2000.00, 22),
(2000.01, 2500.00, 24),
(2500.01, 3000.00, 26),
(3000.01, 3500.00, 28),
(3500.01, 4000.00, 30),
(4000.01, 4500.00, 32),
(4500.01, 5000.00, 34),
(5000.01, 6000.00, 36),
(6000.01, 7000.00, 38),
(7000.01, 8000.00, 40),
(8000.01, 9000.00, 42),
(9000.01, 10000.00, 44),
(10000.01, 15000.00, 46),
(15000.01, 20000.00, 48),
(20000.01, 25000.00, 50);

