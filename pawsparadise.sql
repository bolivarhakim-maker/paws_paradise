-- ============================================
-- SQL: TABLA USUARIOS (EJECUTAR EN MySQL)
-- ============================================

CREATE DATABASE IF NOT EXISTS paws_paradise;
USE paws_paradise;

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') DEFAULT 'cliente',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultima_sesion DATETIME NULL,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_email (email),
    INDEX idx_tipo (tipo)
);

-- ADMIN DE PRUEBA (CONTRASEÑA: admin123)
INSERT INTO usuarios (nombre, email, password, tipo) VALUES 
('Administrador', 'admin@pawsparadise.com', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- CLIENTE DE PRUEBA (CONTRASEÑA: 123456)
INSERT INTO usuarios (nombre, email, password, tipo) VALUES 
('Juan Pérez', 'cliente@pawsparadise.com', 
 '$2y$10$8XJ7YkL9mPqR5tUvWxYzAb.cDeFgHiJkLmNoPqRsTuVwXyZ123456', 'cliente');