<?php
// ============================================
// registro.php - REGISTRO SEGURO CON HASH
// ============================================

require_once 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Solo POST permitido']));
}

$input = json_decode(file_get_contents('php://input'), true);
$nombre = limpiarDato($input['nombre'] ?? '');
$email = limpiarDato($input['email'] ?? '');
$telefono = limpiarDato($input['telefono'] ?? '');
$password = $input['password'] ?? '';

// 🛡️ VALIDACIONES ESTRICTAS
if (empty($nombre) || empty($email) || empty($telefono) || empty($password)) {
    die(json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']));
}

if (strlen($password) < 6) {
    die(json_encode(['success' => false, 'error' => 'Contraseña mínima: 6 caracteres']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['success' => false, 'error' => 'Email inválido']));
}

// 🔍 VERIFICAR EMAIL DUPLICADO
$check = mysqli_query($conn, "SELECT id FROM usuarios WHERE email = '$email' AND activo = 1");
if (mysqli_num_rows($check) > 0) {
    die(json_encode(['success' => false, 'error' => 'Email ya registrado']));
}

// ✅ HASH BCRYPT AUTOMÁTICO
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO usuarios (nombre, email, telefono, password, tipo) VALUES (?, ?, ?, ?, 'cliente')");
mysqli_stmt_bind_param($stmt, "ssss", $nombre, $email, $telefono, $password_hash);

if (mysqli_stmt_execute($stmt)) {
    $usuario_id = mysqli_insert_id($conn);
    
    // 🚀 CREAR SESIÓN INMEDIATAMENTE
    $_SESSION['usuario_id'] = $usuario_id;
    $_SESSION['usuario_nombre'] = $nombre;
    $_SESSION['usuario_tipo'] = 'cliente';
    
    echo json_encode([
        'success' => true,
        'id' => $usuario_id,
        'nombre' => $nombre,
        'email' => $email,
        'tipo' => 'cliente',
        'mensaje' => '¡Registro exitoso!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor'
    ]);
}

mysqli_stmt_close($stmt);
?>