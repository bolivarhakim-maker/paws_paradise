<?php
// ============================================
// login.php - AUTENTICACIÓN SEGURA CON BCRYPT
// ============================================

require_once 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Solo POST permitido']));
}

$input = json_decode(file_get_contents('php://input'), true);
$email = limpiarDato($input['email'] ?? '');
$password = $input['password'] ?? '';

// 🛡️ VALIDACIONES
if (empty($email) || empty($password)) {
    die(json_encode(['success' => false, 'error' => 'Email y contraseña requeridos']));
}

// 🔍 BUSCAR USUARIO ACTIVO
$stmt = mysqli_prepare($conn, "SELECT * FROM usuarios WHERE email = ? AND activo = 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);

if ($usuario) {
    // ✅ VERIFICAR HASH BCRYPT
    if (password_verify($password, $usuario['password'])) {
        
        // 📅 ACTUALIZAR ÚLTIMA SESIÓN
        mysqli_query($conn, "UPDATE usuarios SET ultima_sesion = NOW() WHERE id = {$usuario['id']}");
        
        // 🚀 CREAR SESIÓN POR TIPO DE USUARIO
        if ($usuario['tipo'] === 'admin') {
            $_SESSION['admin_id'] = $usuario['id'];
            $_SESSION['admin_nombre'] = $usuario['nombre'];
            $_SESSION['admin_email'] = $usuario['email'];
        } else {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
        }
        
        echo json_encode([
            'success' => true,
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'tipo' => $usuario['tipo']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Contraseña incorrecta']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
}

mysqli_stmt_close($stmt);
?>