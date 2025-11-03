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
    function validMatricula(matricula) { // Comprueba que se ha introducido una matrícula de 4 letras, con o sin espacio y 3 números (1234( )ABC)
        if (typeof matricula !== 'string') return false;
   		matricula = matricula.trim().toUpperCase();
    	if (matricula.length === 0) return false;
    	return /^[0-9]{4}\s?[A-Z]{3}$/.test(matricula);
    }
    
    function validMarca(marca) { // Comprueba que se ha introducido un string de longitud mayor a 0, con o sin números
        if (typeof marca !== 'string') return false;
        marca = marca.trim();
        if (marca.length === 0) return false;
        return (/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s-]+$/).test(marca);
    }
    
    function validModelo(modelo) { // Comprueba que se ha introducido un string de longitud mayor a 0, con o sin números
        if (typeof modelo !== 'string') return false;
		modelo = modelo.trim();
		if (modelo.length === 0) return false;
		return (/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s-]+$/).test(modelo);
    }

	function validAno(ano) { // Comprueba que se ha introducido un año mayor que 1800
        if (ano === null || ano === undefined) return false;
        ano = String(ano).trim();
        if (ano.length === 0) return false;
        if (!/^\d{4}$/.test(ano)) return false;
        return Number(ano) >= 1800;
    }

    function validKms(kms) { // Comprueba que se ha introducido un número
        if (kms === null || kms === undefined) return false;
        kms = String(kms).trim();
        if (kms.length === 0) return false;
        return /^\d+$/.test(kms);
    }

    //Comprobar datos en funcion del nombre del campo
    function validateField(input) {
        const name = input.getAttribute('name');
        const val = (input.value || '').trim();
        const span = getErrorSpan(input);
        span.textContent = '';

        if (val.length === 0) {
            span.textContent = 'Este campo no puede estar vacío.';
            return false;
        }
        
        if (name === 'matricula') {
            if (!validMatricula(val)) {
                span.textContent = 'La matrícula debe tener el formato 1234 ABC.';
                return false;
            }
            input.value = val.toUpperCase();
            return true;
        }
        
        if (name === 'marca') {
            if (!validMarca(val)) {
                span.textContent = 'La marca solo puede contener letras, números, espacios o guiones.';
                return false;
            }
            return true;
        }

        if (name === 'modelo') {
            if (!validModelo(val)) {
                span.textContent = 'El modelo solo puede contener letras, números, espacios o guiones.';
                return false;
            }
            return true;
        }

        if (name === 'ano') {
            if (!validAno(val)) {
                span.textContent = 'El año debe ser un número mayor o igual a 1800 de 4 digitos.';
                return false;
            }
            return true;
        }

        if (name === 'kms') {
            if (!validKms(val)) {
                span.textContent = 'Los kilómetros deben ser un número.';
                return false;
            }
            return true;
        }

        return true; // Para cualquier otro campo no definido
    }

	// Enviar el formulario
    function enviarFormulario(formId) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input[name="matricula"], input[name="marca"], input[name="modelo"], input[name="ano"], input[name="kms"]');
    
        let todoOk = true;

		// Se ejecuta validateField para cada parámetro del formulario
        inputs.forEach(input => {
            if (!validateField(input)) {
                todoOk = false;
            }
        });

		// Si no hay errores, enviar el formulario al servidor
        if (todoOk) {
            form.submit(); // Enviar el formulario al servidor
        } 
		// Si hay errores, no se envia el formulario al servidor
		else {
            // Poner foco en el primer input con error
            const firstErr = form.querySelector('.field-error:not(:empty)');
            if (firstErr) {
                const before = firstErr.previousElementSibling;
                if (before && before.tagName === 'INPUT') before.focus();
            }
        }
    }

	// Se añaden los listeners necesarios para que se ejecute correctamente el js
    document.addEventListener('DOMContentLoaded', function() {
        const botonModify = document.getElementById('item_modify_submit');
        if (botonModify) {
            botonModify.addEventListener('click', function() {
                enviarFormulario('item_modify_form');
            });
        }
        const botonRegister = document.getElementById('item_add_submit');
        if (botonRegister) {
            botonRegister.addEventListener('click', function() {
                enviarFormulario('item_add_form');
            });
        }
    });
})();
