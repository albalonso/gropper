<?php

// Abre una conexion MySQL.
function conectar()
{
    // Lee los datos de conexion desde el entorno para facilitar despliegues distintos.
    $server = getenv('DB_HOST') ?: '127.0.0.1';
    $user = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
    $db = getenv('DB_NAME') ?: 'gropper';

    // Desactiva el modo de reporte automatico de errores de mysqli.
    mysqli_report(MYSQLI_REPORT_OFF);
    // Crea la conexion real contra la base de datos.
    $conexion = mysqli_connect($server, $user, $password, $db);

    // Si la conexion falla, corta la ejecucion mostrando error.
    if (!$conexion) {
        die('Error de conexion: ' . mysqli_connect_error());
    }

    // Fuerza UTF-8 completo para soportar tildes, simbolos y emojis si los hubiera.
    mysqli_set_charset($conexion, 'utf8mb4');
    // Devuelve la conexion lista para ser usada por el resto de funciones.
    return $conexion;
}

// Convierte un resultado mysqli en un array de filas asociativas mas comodo de manejar.
function fetch_all_assoc(mysqli_result $resultado): array
{
    // Inicializa el array donde se iran acumulando las filas.
    $filas = [];
    // Recorre el resultado una fila cada vez.
    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Guarda la fila actual dentro del array final.
        $filas[] = $fila;
    }
    // Devuelve todas las filas obtenidas.
    return $filas;
}
