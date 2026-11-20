<?php
// ============================================
// conexion.php - CONEXIÓN SEGURA CENTRALIZADA
// ============================================

session_start(); // ✅ SESIONES SIEMPRE ACTIVAS

// Configuración segura de BD
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'paws_paradise');

// Conexión mysqli
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    http_response_code(500);
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Error de conexión: ' . mysqli_connect_error()]));
}

// Configuraciones de seguridad
mysqli_set_charset($conn, 'utf8mb4');
mysqli_options($conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

// 🛡️ FUNCIONES DE SEGURIDAD
function limpiarDato($dato) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($dato));
}

function validarCliente() {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') {
        http_response_code(401);
        header('Content-Type: application/json');
        die(json_encode(['error' => 'Acceso denegado. Cliente requerido.']));
    }
    return $_SESSION['usuario_id'];
}

function validarAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        die(json_encode(['error' => 'Acceso denegado. Admin requerido.']));
    }
    return $_SESSION['admin_id'];
}
?>