/**
 * main.js
 * Lógica para el desplazamiento suave, el modal,
 * y el manejo unificado de formularios (Login/Registro) vía AJAX.
 */

// ------------------------------------------
// 1. UTILIDADES Y MENSAJE DE ESTADO
// ------------------------------------------

function clearMessage() {
    const msg = document.getElementById('message');
    if (msg) {
        msg.textContent = '';
        msg.classList.remove('success', 'error');
    }
}

// Muestra un mensaje y asigna la clase CSS 'success' o 'error'.
function showMessage(text, isSuccess = false) {
    const msg = document.getElementById('message');
    if (msg) {
        clearMessage();
        msg.textContent = text;
        msg.classList.add(isSuccess ? 'success' : 'error');
    }
}

// Alternar visibilidad de contraseña (usada en onclick del span)
function togglePassword(id, iconElement) {
    const passInput = document.getElementById(id);
    if (passInput) {
        passInput.type = passInput.type === 'password' ? 'text' : 'password';
        iconElement.textContent = passInput.type === 'password' ? '👁️' : '🙈';
    }
}


// ------------------------------------------
// 2. LÓGICA DEL MODAL (openModal, closeModal, switchForm)
// ------------------------------------------

// Hacemos estas funciones globales usando window. para que puedan ser llamadas
// directamente desde atributos onclick="" en el HTML.

window.closeModal = function() {
    const modal = document.getElementById('modalLogin');
    if (modal) {
        modal.classList.remove('show');
        clearMessage();
    }
}

window.switchForm = function(type) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const title = document.getElementById('modalTitle');
    const desc = document.getElementById('modalDesc');

    clearMessage();

    if (type === 'login' && loginForm && registerForm) {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        title.textContent = 'Iniciar Sesión';
        desc.textContent = 'Ingresa tus credenciales.';
    } else if (type === 'register' && loginForm && registerForm) {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        title.textContent = 'Crear Cuenta';
        desc.textContent = 'Regístrate para comenzar a explorar.';
    }
}

window.openModal = function(type) {
    clearMessage();
    const modal = document.getElementById('modalLogin');
    if (modal) {
        modal.classList.add('show');
        switchForm(type);
    }
}


// ------------------------------------------
// 3. MANEJADOR UNIFICADO DE FORMULARIOS (AJAX)
// ------------------------------------------

/**
 * Maneja el envío de formularios de registro y login, corrigiendo la ruta del Live Server.
 */
async function handleFormSubmit(e) {
    e.preventDefault();
    clearMessage();

    const form = e.target;
    const formData = new FormData(form);
    const actionUrl = form.action || form.getAttribute('data-action'); 
    
    // --- INICIO DE LA CORRECCIÓN CRÍTICA DE RUTA ---
    let finalUrl = actionUrl;
    const projectFolder = 'sportrulescenter'; // Asegúrate de que este sea el nombre correcto de tu carpeta en htdocs

    // Si la URL de acción es un archivo local (ej: registro.php), lo prefijamos con la ruta de Apache.
    if (actionUrl && (actionUrl.includes('registro.php') || actionUrl.includes('login.php'))) {
        // Esto fuerza la solicitud a XAMPP, resolviendo el error 405 del Live Server.
        finalUrl = `http://localhost/${projectFolder}/${actionUrl}`; 
    }
    // --- FIN DE LA CORRECCIÓN CRÍTICA DE RUTA ---

    if (!finalUrl) {
        showMessage('Error: No se encontró la ruta del script PHP.', false);
        return;
    }

    try {
        const response = await fetch(finalUrl, {
            method: 'POST',
            body: formData
        });
        
        // Si el PHP devuelve un error 500 o 404, esto falla.
        if (!response.ok) {
             // Muestra un error más específico si el servidor XAMPP devuelve 404 o 500
            throw new Error(`Error de servidor (${response.status}): ${response.statusText}`);
        }

        const data = await response.json(); // Intentamos leer el JSON

        if (data.status === 'success') {
            showMessage(data.message, true);
            
            // Lógica post-éxito
            if (finalUrl.includes('registro.php')) {
                form.reset(); 
            } else if (finalUrl.includes('login.php')) {
                setTimeout(closeModal, 1500); 
            }
            
        } else {
            // Mensaje de error (ej: contraseña incorrecta, error de base de datos)
            showMessage(data.message || 'Ocurrió un error desconocido.', false);
        }

    } catch (error) {
        console.error('Error de red, JSON, o Servidor:', error);
        // Mensaje genérico para fallos que no son errores de validación de PHP
        showMessage(`Error al conectar con el servidor: ${error.message}.`, false);
    }
}


// ------------------------------------------
// 4. INICIALIZACIÓN DE EVENTOS
// ------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    
    // A. Evento de Desplazamiento Suave (Scroll)
    document.querySelectorAll('.btn[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // B. Evento de Cierre de Modal
    const closeModalBtn = document.getElementById('closeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    // C. Envío de Formularios (Usamos el manejador unificado)
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        // Asignamos el manejador unificado a ambos
        loginForm.addEventListener('submit', handleFormSubmit); 
    }
    if (registerForm) {
        registerForm.addEventListener('submit', handleFormSubmit);
    }
});