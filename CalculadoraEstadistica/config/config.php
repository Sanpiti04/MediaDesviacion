<?php
// config/config.php

// Define la configuración para la conexión a la base de datos.
// ¡REQUISITO DE SEGURIDAD!: En producción, estas credenciales DEBERÍAN cargarse de forma más segura (ej. variables de entorno).
return [
    'host' => 'localhost',
    'port' => '3306',
    'dbName' => 'bd_datos_estadisticos', // Nombre de tu base de datos
    'user' => 'root',                    // <--- ¡CAMBIA ESTO!
    'password' => '', // <--- ¡CAMBIA ESTO!
];