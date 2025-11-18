# 🚀 INICIO RÁPIDO - Red Dragons Cup Brackets

## ✅ Pasos para Configurar en 5 Minutos

### 1️⃣ Asegúrate de que XAMPP esté corriendo
- ✅ Apache: CORRIENDO
- ✅ MySQL: CORRIENDO

---

### 2️⃣ Crear la Base de Datos

1. Abre phpMyAdmin: `http://localhost/phpmyadmin`
2. Ve a la pestaña **SQL** (arriba)
3. Abre el archivo: `cnt/setup_local_db.sql`
4. Copia **TODO** el contenido
5. Pégalo en phpMyAdmin
6. Haz clic en **Continuar** o **Ejecutar**

✅ **Resultado**: Base de datos `red_dragons_db` creada

---

### 3️⃣ Iniciar Sesión como Administrador

1. Ve a: `http://localhost/Pagina-Red-Dragon/login.php`
2. Credenciales por defecto:
   - **Usuario**: `admin`
   - **Contraseña**: `admin123`
3. Haz clic en **Iniciar Sesión**

✅ **Resultado**: Sesión iniciada como administrador

---

### 4️⃣ Acceder al Panel de Brackets

1. Ve a: `http://localhost/Pagina-Red-Dragon/admin_brackets.php`
   
   O haz clic en el enlace **"Admin Brackets"** en el menú

✅ **Resultado**: Panel de administración abierto

---

### 5️⃣ Agregar Equipos

1. En **"Gestión de Equipos"**
2. Llena el formulario:
   - Nombre del Equipo: `Equipo Test`
   - Seed: `1`
3. Haz clic en **➕ Agregar Equipo**
4. Repite para agregar más equipos (necesitas 48 para el torneo completo)

✅ **Resultado**: Equipos agregados

---

### 6️⃣ Generar Matches

1. Una vez que tengas equipos agregados
2. Haz clic en **🎲 Generar Matches de Ronda 1**
3. Confirma la acción

✅ **Resultado**: Matches generados automáticamente

---

### 7️⃣ Actualizar Puntajes

1. En la tabla de **"Gestión de Matches"**
2. Encuentra un match
3. Haz clic en **📊 Actualizar Puntaje**
4. Ingresa los puntos:
   - Puntos Equipo 1: `10`
   - Puntos Equipo 2: `5`
5. Haz clic en **💾 Guardar Puntaje**

✅ **Resultado**: 
- El equipo ganador aparece en **AZUL** 🔵
- El equipo perdedor aparece en **ROJO** 🔴

---

### 8️⃣ Ver los Brackets Públicos

1. Ve a: `http://localhost/Pagina-Red-Dragon/brackets.php`
2. Desplázate horizontalmente para ver todas las rondas

✅ **Resultado**: Brackets visibles con equipos y puntajes

---

## 🆘 Solución Rápida de Problemas

### ❌ Error: "Host desconocido"
**Solución**: Ya está arreglado. El archivo `conexion.php` ahora usa localhost automáticamente.

### ❌ Error: "Base de datos no existe"
**Solución**: Ejecuta el archivo `cnt/setup_local_db.sql` en phpMyAdmin.

### ❌ No puedo acceder al panel de administración
**Solución**: 
1. Inicia sesión con: `admin` / `admin123`
2. Verifica que tu usuario tenga `rol = 'admin'` en la base de datos

### ❌ Los brackets están vacíos
**Solución**:
1. Primero agrega equipos
2. Luego genera los matches de Ronda 1
3. Refresca la página de brackets

---

## 📞 ¿Necesitas Ayuda?

1. Lee el archivo completo: `INSTRUCCIONES_BRACKETS.md`
2. Verifica la consola del navegador (F12)
3. Revisa que MySQL esté corriendo en XAMPP

---

## 🎯 Checklist Rápido

- [ ] XAMPP corriendo (Apache + MySQL)
- [ ] Base de datos creada (`red_dragons_db`)
- [ ] Sesión iniciada como admin
- [ ] Equipos agregados
- [ ] Matches generados
- [ ] Puntajes actualizados
- [ ] Brackets visualizados

---

**¡Listo! Ya tienes el sistema funcionando.** 🎮🏆

📝 **Próximo paso**: Agrega los 48 equipos y empieza el torneo.
