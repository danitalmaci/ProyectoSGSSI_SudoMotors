// js para la comprobación de parámetros de usuario
(function(){
    // Indica cualquier error por formato no válido
    function getErrorSpan(input) {
        let span = input.nextElementSibling;
        if (!span || !span.classList || !span.classList.contains('field-error')) {
            span = document.createElement('span');
            span.className = 'field-error';
            span.style.color = 'red';
            span.style.display = 'block';
            span.style.fontSize = '0.9em';
            input.parentNode.insertBefore(span, input.nextSibling);
        }
        return span;
    }

    // COMPROBACIONES
    function validName(s) { // Comprueba que se ha introducido un string de longitud mayor a 0 sin números
        if (typeof s !== 'string') return false;
        s = s.trim();
        if (s.length === 0) return false;
        return (/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü\s\-]+$/).test(s);
    }

    function validDni(dni) { // Comprueba que se ha introducido un dni de 8 dígitos, guión y la letra adecuada letra. (12345678-Z)
        if (typeof dni !== 'string') return false;

        dni = dni.trim().replace(/[\u2010-\u2015\u2212\uFE58\uFE63\uFF0D]/g, '-');

        const m = dni.match(/^(\d{8})-([A-Za-z])$/);
        if (!m) return false;

        const num = m[1];
        const letter = m[2].toUpperCase();
        return dniExpectedLetter(num) === letter;
    }


    function dniExpectedLetter(num) { // Comprueba que la letra del dni coincide con la serie de números del mismo
        const letters = "TRWAGMYFPDXBNJZSQVHLCKE";
        return letters.charAt(Number(num) % 23);
    }

    function validPhone(phone) { // Comprueba que el número de teléfono introducido es de 9 dígitos
        return (/^\d{9}$/).test(String(phone).trim());
    }

    function validDateYMD(d) { // Comprueba que la fecha introducida tiene el formato YYYY-MM-DD
        if (!(/^\d{4}-\d{2}-\d{2}$/).test(d)) return false;
        const [y, m, day] = d.split('-').map(Number);
        const dt = new Date(y, m-1, day);
        return dt.getFullYear() === y && (dt.getMonth()+1) === m && dt.getDate() === day;
    }

    function validEmail(e) { // Comprueba que el email introducido no contiene espacios antes del @, que contiene un único @, y que hay un .'dominio' (ejemplo@servidor.dominio)
        if (typeof e !== 'string') return false;
        return (/^[^\s@]+@[^\s@]+\.[^\s@]+$/).test(e.trim());
    }
    
    function validPsswd(psswd) { // Comprueba que la contraseña es segura
		return (/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/).test(psswd.trim());
    }

    function validateField(input) { // Comprobar datos en funcion del nombre del campo
        const name = input.getAttribute('name');
        const val = (input.value || '').trim();
        const span = getErrorSpan(input);
        span.textContent = '';

        if (name === 'nombre') {
            if (!validName(val)) {
                span.textContent = 'Nombre inválido: solo letras, espacios y guiones.';
                return false;
            }
            return true;
        }

        if (name === 'apellidos') {
            if (!validName(val)) {
                span.textContent = 'Apellidos inválidos: solo letras, espacios y guiones.';
                return false;
            }
            return true;
        }

        if (name === 'dni') {
            if (!validDni(val)) {
                span.textContent = 'DNI inválido. Formato: 11111111-Z.';
                return false;
            }
            // Normalizar la letra a mayúscula antes de enviar
            const parts = val.split('-');
            if (parts.length === 2) {
                input.value = parts[0] + '-' + parts[1].toUpperCase();
            }
            return true;
        }

        if (name === 'telefono') {
            if (!validPhone(val)) {
                span.textContent = 'Teléfono inválido. Debe ser de 9 dígitos.';
                return false;
            }
            return true;
        }

        if (name === 'f_nacimiento') {
            if (!validDateYMD(val)) {
                span.textContent = 'Fecha inválida. Formato: aaaa-mm-dd.';
                return false;
            }
            return true;
        }

        if (name === 'email') {
            if (!validEmail(val)) {
                span.textContent = 'Email inválido. Formato: ejemplo@servidor.extensión.';
                return false;
            }
            return true;
        }

        if (name === 'usuario') {
            if (val.length === 0) {
                span.textContent = 'El usuario no puede estar vacío.';
                return false;
            }
            return true;
        }

        if (name === 'contrasena') {
            if (val.length < 6) {
                span.textContent = 'La contraseña debe tener al menos 6 caracteres.';
                return false;
           	else if (!validPsswd(val)){
           		span.textContent = 'La contraseña debe incluir al menos una mayúscula, un número y un carácter especial.';
                return false;
            }
            return true;
        }
        
        if (name === 'confirmar_contrasena') {
            const original = document.querySelector('input[name="contrasena"]').value.trim();
            if (val !== original) {
                span.textContent = 'Las contraseñas no coinciden.';
                return false;
            }
            return true;
        }

        return true; // Para cualquier otro campo no definido
    }

    // Enviar el formulario
    function enviarFormulario(formId) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input[name="nombre"], input[name="apellidos"], input[name="dni"], input[name="telefono"], input[name="f_nacimiento"], input[name="email"], input[name="usuario"], input[name="contrasena"], input[name="confirmar_contrasena"]');
    
        let todoOk = true;

        // Se ejecuta validateField para cada parámetro del formulario
        inputs.forEach(input => {
            if (!validateField(input)) {
                todoOk = false;
            }
        });

        // Si no hay errores, enviar el formulario al servidor
        if (todoOk) {
            form.submit(); 
        } 
        // Si hay errores, no se envia el formulario al servidor
        else {
            //Poner foco en el primer input con error
            const firstErr = form.querySelector('.field-error:not(:empty)');
            if (firstErr) {
                const before = firstErr.previousElementSibling;
                if (before && before.tagName === 'INPUT') before.focus();
            }
        }
    }

    // Se añaden los listeners necesarios para que se ejecute correctamente el js
    document.addEventListener('DOMContentLoaded', function() {
        const botonModify = document.getElementById('user_modify_submit');
        if (botonModify) {
            botonModify.addEventListener('click', function() {
                enviarFormulario('user_modify_form');
            });
        }
        const botonRegister = document.getElementById('register_submit');
        if (botonRegister) {
            botonRegister.addEventListener('click', function() {
                enviarFormulario('register_form');
            });
        }
    });
})();
