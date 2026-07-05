document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.querySelector('.caja-login form');
    if (!formulario) {
        return;
    }

    const campoCorreo = document.getElementById('correo');
    const campoContrasena = document.getElementById('contrasena');

    // Crea el contenedor donde se muestran los errores del lado del cliente.
    const cajaError = document.createElement('div');
    cajaError.className = 'alerta alerta-error alerta-js';
    cajaError.style.display = 'none';
    formulario.insertBefore(cajaError, formulario.firstChild);

    function mostrarError(mensaje) {
        cajaError.textContent = mensaje;
        cajaError.style.display = 'block';
    }

    function ocultarError() {
        cajaError.style.display = 'none';
    }

    function correoValido(correo) {
        const patron = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return patron.test(correo);
    }

    formulario.addEventListener('submit', function (evento) {
        const correo = campoCorreo.value.trim();
        const contrasena = campoContrasena.value.trim();

        if (correo === '' || contrasena === '') {
            evento.preventDefault();
            mostrarError('Completa correo y contraseña.');
            return;
        }

        if (!correoValido(correo)) {
            evento.preventDefault();
            mostrarError('El correo no tiene un formato válido.');
            return;
        }

        ocultarError();
    });
});