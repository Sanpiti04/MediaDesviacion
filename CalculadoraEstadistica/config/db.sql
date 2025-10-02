-- SCRIPT SQL PARA MYSQL
-- Paso 1: Crear la Base de Datos (si no existe)
-- La Base de Datos debe coincidir con el valor 'bd_datos_estadisticos' en config/config.php

CREATE DATABASE IF NOT EXISTS bd_datos_estadisticos;

-- Usar la Base de Datos recién creada
USE bd_datos_estadisticos;

-- Paso 2: Crear la Tabla de Prueba
-- Nombramos la tabla 'registros_metricos' para que aparezca en el select del controlador.

CREATE TABLE IF NOT EXISTS registros_metricos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Columna clave para el cálculo (DEBE ser el valor que uses en el formulario: 'valor_numerico')
    valor_numerico DECIMAL(10, 4) NULL,
    
    -- Columna de ejemplo para simular datos que DEBEN ser ignorados (valores nulos o no numéricos)
    nombre_producto VARCHAR(100) NOT NULL,
    fecha_registro DATE,
    
    -- Columna con datos mixtos (simula datos que deben ser filtrados en PHP)
    estado VARCHAR(50)
);

-- Paso 3: Insertar Datos de Prueba
-- Incluimos valores para probar todos los REQUISITOS DE LECTURA DE DATOS:
-- 1. Valores numéricos válidos.
-- 2. Valores NULL.
-- 3. Valores no numéricos ('Error', 'N/A') que PHP debe filtrar.

INSERT INTO registros_metricos (valor_numerico, nombre_producto, fecha_registro, estado) VALUES
(10.50, 'Producto A', '2024-01-01', 'Activo'),
(12.00, 'Producto B', '2024-01-05', 'Activo'),
(8.50, 'Producto C', '2024-01-10', 'Inactivo'),
(15.00, 'Producto D', '2024-01-15', 'Activo'),
(NULL, 'Producto E', '2024-01-20', 'Pendiente'), -- Valor NULL (debe ser ignorado)
(11.20, 'Producto F', '2024-01-25', 'Activo'),
(9.80, 'Producto G', '2024-02-01', 'Activo'),
(13.10, 'Producto H', '2024-02-05', 'Inactivo'),
(10.00, 'Producto I', '2024-02-10', 'Activo'),
(14.50, 'Producto J', '2024-02-15', 'Activo'),
(NULL, 'Producto K', '2024-02-20', 'Activo'),   -- Otro valor NULL
(9.90, 'Producto L', '2024-02-25', 'Activo');   
-- Nota: Para simular un valor no numérico que PHP filtra, en MySQL tendrías que cambiar 
-- el tipo de columna a VARCHAR, pero para una columna numérica real, estos son los datos más comunes.

-- Paso 4: (Opcional) Crear una tabla con un nombre diferente para probar la selección
CREATE TABLE IF NOT EXISTS datos_ejemplo_1 (
    id INT PRIMARY KEY,
    metrica_principal DECIMAL(10, 2)
);

INSERT INTO datos_ejemplo_1 (id, metrica_principal) VALUES
(1, 20.1), (2, 22.5), (3, 21.0), (4, 23.9), (5, 20.8);