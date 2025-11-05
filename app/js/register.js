document.addEventListener('DOMContentLoaded', () => {
  const pass1 = document.getElementById('contrasena');
  const pass2 = document.getElementById('confirmar_contrasena'); // solo existe en register.php
  const toggle1 = document.getElementById('togglePass');
  const cancelBtn = document.getElementById('cancelar');

  // Mostrar / ocultar contraseña
  if (toggle1 && pass1) {
    toggle1.addEventListener('change', () => {
      const type = toggle1.checked ? 'text' : 'password';
      pass1.type = type;
      if (pass2) pass2.type = type; // si existe, también lo aplica (solo en registro)
    });
  }

  // Botón cancelar
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      // Si estamos en register.php → volver a login
      // Si estamos en login.php → volver al index
      if (window.location.pathname.includes('register.php')) {
        window.location.href = 'index.php';
      } else {
        window.location.href = 'index.php';
      }
    });
  }
});

