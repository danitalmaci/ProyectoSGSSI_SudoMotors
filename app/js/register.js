document.addEventListener('DOMContentLoaded', () => {
  const pass1 = document.getElementById('contrasena');
  const pass2 = document.getElementById('confirmar_contrasena'); // solo existe en register.php
  const toggle1 = document.getElementById('togglePass');
  const cancelBtn = document.getElementById('cancelar');
  const eliminarBtn = document.getElementById('eliminar');
  const modificarBtn = document.getElementById('modificar');

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
      if (window.location.pathname.includes('register.php')) {
        window.location.href = 'index.php';
      } else if (window.location.pathname.includes('login.php')){
        window.location.href = 'index.php';
      } else if (window.location.pathname.includes('show_item.php')){
        window.location.href = 'items.php';
      }
    });
  }
  
  // Botón eliminar
  if (eliminarBtn) {
    eliminarBtn.addEventListener('click', () => {
      if (window.location.pathname.includes('show_item.php')) {
        window.location.href = 'delete_item.php';
      }
    });
  }
  
  // Botón modificar
  if (modificarBtn) {
    modificarBtn.addEventListener('click', () => {
      if (window.location.pathname.includes('show_item.php')) {
        window.location.href = 'modify_item.php';
      } else if (window.location.pathname.includes('show_user.php')){
        window.location.href = 'modify_user.php';
      }
    });
  }
});

