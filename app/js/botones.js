document.addEventListener('DOMContentLoaded', () => {
  const pass1 = document.getElementById('contrasena');
  const pass2 = document.getElementById('confirmar_contrasena');
  const toggle1 = document.getElementById('togglePass');
  const cancelBtn = document.getElementById('cancelar');
  const eliminarBtn = document.getElementById('eliminar');
  const modificarBtn = document.getElementById('modificar');
  const logoutBtn = document.getElementById('logout');
  const loginBtn = document.getElementById('login');
  const registerBtn = document.getElementById('register');
  const listaBtn = document.getElementById('lista');
  const perfilBtn = document.getElementById('perfil');

  // Mostrar / ocultar contraseña
  if (toggle1 && pass1) {
    toggle1.addEventListener('change', () => {
      const type = toggle1.checked ? 'text' : 'password';
      pass1.type = type;
      if (pass2) pass2.type = type;
    });
  }

  // Obtener matrícula de la URL si existe
  const urlParams = new URLSearchParams(window.location.search);
  const matricula = urlParams.get('matricula');
  
  // Botón cancelar
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      if (window.location.pathname.includes('register.php') || window.location.pathname.includes('login.php')) {
        window.location.href = 'index.php';
      } else if (window.location.pathname.includes('show_item.php')) {
        window.location.href = 'items.php';
      }
      else if (window.location.pathname.includes('show_user.php')) {
        window.location.href = 'items.php';
      }
    });
  }
  
  // Iniciar sesión
  if (loginBtn) {
    loginBtn.addEventListener('click', () => {
      if (window.location.pathname.endsWith('/') || window.location.pathname.includes('index.php')) {
 
        window.location.href = 'login.php';
      }
    });
  }
  
  // Cerrar sesión
  if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
      if (window.location.pathname.includes('show_user.php') || window.location.pathname.includes('index.php')) {
        window.location.href = 'login.php?logout=1';
      }
    });
  }
  
  // Registrar
  if (registerBtn) {
    registerBtn.addEventListener('click', () => {
      if (window.location.pathname.includes('index.php')) {
        window.location.href = 'register.php';
      }
    });
  }

  // Botón eliminar
  if (eliminarBtn) {
    eliminarBtn.addEventListener('click', () => {
      if (window.location.pathname.includes('show_item.php') && matricula) {
        window.location.href = `delete_item.php?matricula=${encodeURIComponent(matricula)}`;
      }
    });
  }
  
  // Ver lista vehículos
  if (listaBtn) {
    listaBtn.addEventListener('click', () => {
      if (window.location.pathname.endsWith('/') || window.location.pathname.includes('index.php')) {
 
        window.location.href = 'items.php';
      }
    });
  }
  
  // Ver perfil
  if (perfilBtn) {
    perfilBtn.addEventListener('click', () => {
    if (window.location.pathname.endsWith('/') || window.location.pathname.includes('index.php')) {

        const user = new URLSearchParams(window.location.search).get('user');
        window.location.href = `show_user.php?user=${encodeURIComponent(user)}`;
      }
    });
  }

  // Botón modificar
  if (modificarBtn) {
    modificarBtn.addEventListener('click', () => {
      if (window.location.pathname.includes('show_item.php') && matricula) {
        window.location.href = `modify_item.php?matricula=${encodeURIComponent(matricula)}`;
      } else if (window.location.pathname.includes('show_user.php')) {
        const user = new URLSearchParams(window.location.search).get('user');
        window.location.href = `modify_user.php?user=${encodeURIComponent(user)}`;
      }
    });
  }
});

