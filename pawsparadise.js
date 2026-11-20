// ============================================
// scripts.js - MÓDULO AUTENTICACIÓN (REEMPLAZAR COMPLETO)
// ============================================

// ===== CONFIGURACIÓN DE RUTAS =====
const API_AUTH = {
    login: 'admin/login.php',
    registro: 'admin/registro.php'
};

// ===== FUNCIÓN LOGIN ASÍNCRONA =====
async function enviarLogin(email, password) {
    try {
        const response = await fetch(API_AUTH.login, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        
        const data = await response.json();
        return data;
        
    } catch (error) {
        console.error('❌ Error de red:', error);
        return { success: false, error: 'Error de conexión' };
    }
}

// ===== FUNCIÓN REGISTRO ASÍNCRONA =====
async function enviarRegistro(nombre, email, telefono, password) {
    try {
        const response = await fetch(API_AUTH.registro, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre, email, telefono, password })
        });
        
        return await response.json();
        
    } catch (error) {
        console.error('❌ Error de red:', error);
        return { success: false, error: 'Error de conexión' };
    }
}

// ===== MANEJADOR DE FORMULARIO LOGIN =====
function configurarLogin() {
    const form = document.getElementById('login-form');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email-login').value;
        const password = document.getElementById('password-login').value;
        const btn = document.getElementById('btn-login');
        
        // 🌀 UI Loading
        const textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
        
        const resultado = await enviarLogin(email, password);
        
        // 🔄 Restaurar botón
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
        
        if (resultado.success) {
            mostrarNotificacion(`✅ ¡Bienvenido ${resultado.nombre}!`);
            
            // 🚀 REDIRECCIÓN INTELIGENTE POR TIPO
            setTimeout(() => {
                if (resultado.tipo === 'admin') {
                    window.location.href = 'admin/index.php';
                } else {
                    window.location.href = 'index.html';
                }
            }, 1500);
            
        } else {
            mostrarNotificacion(`❌ ${resultado.error}`, 'error');
            document.getElementById('email-login').focus();
        }
    });
}

// ===== MANEJADOR DE FORMULARIO REGISTRO =====
function configurarRegistro() {
    const form = document.getElementById('registro-form');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const nombre = document.getElementById('nombre-completo').value;
        const email = document.getElementById('email-registro').value;
        const telefono = document.getElementById('telefono-registro').value;
        const password = document.getElementById('password-registro').value;
        const confirmPass = document.getElementById('confirm-password').value;
        const btn = document.getElementById('btn-registro');
        
        // ✅ VALIDAR CONTRASEÑAS
        if (password !== confirmPass) {
            mostrarNotificacion('❌ Las contraseñas no coinciden', 'error');
            return;
        }
        
        // 🌀 UI Loading
        const textoOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando cuenta...';
        
        const resultado = await enviarRegistro(nombre, email, telefono, password);
        
        // 🔄 Restaurar botón
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
        
        if (resultado.success) {
            mostrarNotificacion('✅ ¡Cuenta creada! Redirigiendo...');
            
            // 🚀 Ya está logueado, ir al inicio
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 2000);
            
        } else {
            mostrarNotificacion(`❌ ${resultado.error}`, 'error');
        }
    });
}

// ===== CERRAR SESIÓN =====
function cerrarSesion() {
    fetch('admin/logout.php')
        .then(() => {
            mostrarNotificacion('👋 Sesión cerrada');
            setTimeout(() => window.location.href = 'index.html', 1500);
        })
        .catch(() => window.location.href = 'index.html');
}

// ===== INICIALIZACIÓN AUTOMÁTICA =====
document.addEventListener('DOMContentLoaded', function() {
    configurarLogin();
    configurarRegistro();
    
    // Verificar si hay botones de logout en la página
    document.querySelectorAll('[onclick="cerrarSesion()"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            cerrarSesion();
        });
    });
});

// ===== NOTIFICACIONES (FUNCIÓN AUXILIAR) =====
function mostrarNotificacion(mensaje, tipo = 'success') {
    const notif = document.createElement('div');
    notif.className = `notificacion notif-${tipo}`;
    notif.innerHTML = mensaje;
    notif.style.cssText = `
        position: fixed; top: 20px; right: 20px; 
        background: ${tipo === 'error' ? '#e74c3c' : '#2ecc71'};
        color: white; padding: 15px 25px; border-radius: 50px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 9999;
        animation: slideIn 0.4s ease;
    `;
    document.body.appendChild(notif);
    
    setTimeout(() => notif.remove(), 4000);
}