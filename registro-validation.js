// Validación de contraseñas para el formulario de registro
document.getElementById('confirm_password').addEventListener('input', function() {
  const password = document.getElementById('password').value;
  const confirmPassword = this.value;
  
  if (password !== confirmPassword) {
    this.setCustomValidity('Las contraseñas no coinciden');
  } else {
    this.setCustomValidity('');
  }
});
