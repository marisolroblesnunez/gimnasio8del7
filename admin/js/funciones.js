/**
 * Muestra una notificación emergente en la pantalla.
 * @param {string} mensaje - El mensaje que se mostrará en la notificación.
 * @param {string} [tipo='info'] - El tipo de notificación. Puede ser 'success' (éxito), 'error', o 'info'.
 * @param {number} [duracion=3000] - La duración en milisegundos que la notificación permanecerá visible.
 */
function mostrarNotificacion(mensaje, tipo = 'info', duracion = 3000) {
    const notificacion = document.createElement('div');
    // Se asignan clases CSS para el estilo y el tipo de notificación.
    notificacion.className = `notificacion ${tipo}`;
    notificacion.textContent = mensaje;

    document.body.appendChild(notificacion);

    // Se usa un pequeño retardo para permitir que el navegador aplique la transición CSS.
    setTimeout(() => {
        notificacion.classList.add('visible');
    }, 10);

    // Se configura un temporizador para ocultar y eliminar la notificación.
    setTimeout(() => {
        notificacion.classList.remove('visible');
        // Se espera a que la transición de ocultado termine para eliminar el elemento del DOM.
        notificacion.addEventListener('transitionend', () => {
            notificacion.remove();
        });
    }, duracion);
}

/**
 * Realiza una petición asíncrona usando fetch a un endpoint de la API.
 * @param {string} url - La URL del endpoint al que se hará la petición.
 * @param {string} metodo - El método HTTP para la petición (GET, POST, PUT, DELETE).
 * @param {object|FormData} [datos=null] - Los datos a enviar en el cuerpo de la petición. Puede ser un objeto JSON o un objeto FormData.
 * @returns {Promise<object>} - Una promesa que resuelve con la respuesta de la API en formato JSON.
 */
async function realizarPeticion(url, metodo, datos = null) {
    const opciones = {
        method: metodo,
        headers: {},
    };

    if (datos) {
        if (datos instanceof FormData) {
            // Si los datos son FormData, el navegador establece el 'Content-Type' automáticamente,
            // lo cual es necesario para el envío de archivos.
            opciones.body = datos;
        } else {
            // Si es un objeto JavaScript, se convierte a una cadena JSON.
            opciones.headers['Content-Type'] = 'application/json';
            opciones.body = JSON.stringify(datos);
        }
    }

    try {
        const respuesta = await fetch(url, opciones);
        if (!respuesta.ok) {
            // Si la respuesta del servidor no es exitosa (ej. status 400, 500),
            // se intenta obtener un mensaje de error del cuerpo de la respuesta.
            const errorData = await respuesta.json().catch(() => ({}));
            const mensajeError = errorData.mensaje || `Error HTTP: ${respuesta.status}`;
            throw new Error(mensajeError);
        }
        return await respuesta.json();
    } catch (error) {
        console.error(`Error en la petición a ${url}:`, error);
        // Se relanza el error para que la función que llamó a 'realizarPeticion' pueda manejarlo.
        throw error;
    }
}

/**
 * Valida un campo de texto genérico.
 * @param {string} valor - El valor del campo a validar.
 * @param {string} nombreCampo - El nombre del campo para usar en los mensajes de error (ej. "Nombre", "Asunto").
 * @param {number} [longitudMin=0] - La longitud mínima requerida para el valor.
 * @param {number} [longitudMax=Infinity] - La longitud máxima permitida.
 * @returns {{esValido: boolean, mensaje: string}} - Un objeto que indica si la validación fue exitosa y un mensaje de error si no lo fue.
 */
function validarCampoTexto(valor, nombreCampo, longitudMin = 0, longitudMax = Infinity) {
    const valorTrim = valor.trim();
    if (longitudMin > 0 && valorTrim.length === 0) {
        return { esValido: false, mensaje: `${nombreCampo} no puede estar vacío.` };
    }
    if (valorTrim.length < longitudMin) {
        return { esValido: false, mensaje: `${nombreCampo} debe tener al menos ${longitudMin} caracteres.` };
    }
    if (valorTrim.length > longitudMax) {
        return { esValido: false, mensaje: `${nombreCampo} no puede exceder los ${longitudMax} caracteres.` };
    }
    return { esValido: true, mensaje: '' };
}

/**
 * Valida el formato de una dirección de correo electrónico.
 * @param {string} email - La dirección de email a validar.
 * @returns {{esValido: boolean, mensaje: string}} - Un objeto que indica si la validación fue exitosa y un mensaje de error si no lo fue.
 */
function validarEmail(email) {
    const emailTrim = email.trim();
    if (emailTrim.length === 0) {
        return { esValido: false, mensaje: 'El email no puede estar vacío.' };
    }
    // Expresión regular estándar para la validación de formato de email.
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(emailTrim)) {
        return { esValido: false, mensaje: 'El formato del email no es válido.' };
    }
    return { esValido: true, mensaje: '' };
}
