document.addEventListener('DOMContentLoaded', () => {
    // Punto de entrada principal para la gestión de testimonios.
    iniciarGestionTestimonios();
});

/**
 * Inicializa la carga de testimonios y configura los manejadores de eventos.
 */
function iniciarGestionTestimonios() {
    cargarTestimonios();
    configurarManejadoresEventos();
}

/**
 * Carga los testimonios desde el controlador y los muestra en la tabla.
 */
async function cargarTestimonios() {
    const tablaBody = document.getElementById('tablaTestimonios');
    // Muestra un estado de carga mientras se obtienen los datos.
    tablaBody.innerHTML = '<tr><td colspan="5">Cargando testimonios...</td></tr>';

    try {
        // Pide al controlador la lista de todos los testimonios para el admin.
        const respuesta = await realizarPeticion('../controllers/testimonioController.php?accion=listarAdmin', 'GET');
        
        if (respuesta.success && respuesta.data.length > 0) {
            // Si hay datos, se limpia la tabla y se rellena.
            tablaBody.innerHTML = '';
            respuesta.data.forEach(testimonio => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${testimonio.id}</td>
                    <td>${escapeHTML(testimonio.nombre)}</td>
                    <td>${escapeHTML(testimonio.mensaje)}</td>
                    <td>${testimonio.fecha}</td>
                    <td>
                        <span class="estado ${testimonio.estado}">${testimonio.estado}</span>
                    </td>
                    <td class="acciones">
                        ${testimonio.estado === 'pendiente' ? `<button class="btn-aprobar" data-id="${testimonio.id}">Aprobar</button>` : ''}
                        <button class="btn-eliminar" data-id="${testimonio.id}">Eliminar</button>
                    </td>
                `;
                tablaBody.appendChild(fila);
            });
        } else {
            // Si no hay testimonios, se muestra un mensaje.
            tablaBody.innerHTML = '<tr><td colspan="5">No hay testimonios para mostrar.</td></tr>';
        }
    } catch (error) {
        // En caso de error en la petición, se muestra en la tabla y en la consola.
        tablaBody.innerHTML = '<tr><td colspan="5">Error al cargar los testimonios.</td></tr>';
        console.error('Error al cargar testimonios:', error);
    }
}

/**
 * Configura un único manejador de eventos en la tabla para delegar los clics de los botones.
 */
function configurarManejadoresEventos() {
    const tablaBody = document.getElementById('tablaTestimonios');
    tablaBody.addEventListener('click', (evento) => {
        const boton = evento.target;
        const id = boton.dataset.id;

        if (id) {
            if (boton.classList.contains('btn-aprobar')) {
                aprobarTestimonio(id);
            } else if (boton.classList.contains('btn-eliminar')) {
                eliminarTestimonio(id);
            }
        }
    });
}

/**
 * Cambia el estado de un testimonio a 'aprobado'.
 * @param {number} id - El ID del testimonio a aprobar.
 */
async function aprobarTestimonio(id) {
    const datos = new FormData();
    datos.append('id', id);
    datos.append('estado', 'aprobado');

    try {
        const respuesta = await realizarPeticion('../controllers/testimonioController.php?accion=cambiarEstado', 'POST', datos);
        if (respuesta.success) {
            mostrarNotificacion('Testimonio aprobado correctamente.', 'success');
            cargarTestimonios(); // Recarga la tabla para reflejar el cambio.
        } else {
            mostrarNotificacion(respuesta.mensaje || 'Error al aprobar el testimonio.', 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error de conexión al intentar aprobar.', 'error');
    }
}

/**
 * Elimina un testimonio, pidiendo confirmación primero.
 * @param {number} id - El ID del testimonio a eliminar.
 */
async function eliminarTestimonio(id) {
    // Pide confirmación al usuario antes de una acción destructiva.
    if (!confirm('¿Estás seguro de que quieres eliminar este testimonio?')) {
        return;
    }

    const datos = new FormData();
    datos.append('id', id);

    try {
        const respuesta = await realizarPeticion('../controllers/testimonioController.php?accion=eliminar', 'POST', datos);
        if (respuesta.success) {
            mostrarNotificacion('Testimonio eliminado correctamente.', 'success');
            cargarTestimonios(); // Recarga la tabla.
        } else {
            mostrarNotificacion(respuesta.mensaje || 'Error al eliminar el testimonio.', 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error de conexión al intentar eliminar.', 'error');
    }
}

/**
 * Escapa caracteres HTML para prevenir ataques XSS.
 * @param {string} str - La cadena de texto a escapar.
 * @returns {string} - La cadena con los caracteres HTML escapados.
 */
function escapeHTML(str) {
    if (str === null || str === undefined) {
        return '';
    }
    const p = document.createElement('p');
    p.appendChild(document.createTextNode(str));
    return p.innerHTML;
}
