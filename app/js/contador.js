document.addEventListener('DOMContentLoaded', () => {
  // Mostrar/ocultar contraseña
  const pass1 = document.getElementById('contrasena');
  const toggle1 = document.getElementById('togglePass');
  if (toggle1 && pass1) {
    toggle1.addEventListener('change', () => {
      pass1.type = toggle1.checked ? 'text' : 'password';
    });
  }

  // --- Cronómetro de bloqueo ---
  const contador = document.getElementById('contador');

  if (contador) {
    let tiempo = parseInt(contador.textContent);

    const intervalo = setInterval(() => {
      tiempo--;

      // Actualiza visualmente el número
      contador.textContent = tiempo;

      // Cuando llega a 0, recarga la página
      if (tiempo <= 0) {
        clearInterval(intervalo);
        location.reload();
      }
    }, 1000);
  }
});
