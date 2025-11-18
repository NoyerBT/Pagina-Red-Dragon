# 🏆 Sistema de Brackets - Red Dragons Cup

## 📋 Instrucciones de Configuración para LOCALHOST

### ⚠️ IMPORTANTE: Configuración Actualizada
El archivo `cnt/conexion.php` ahora detecta automáticamente si estás en localhost o producción.

### Paso 1: Crear la Base de Datos Local

1. Asegúrate de que **XAMPP** esté ejecutándose (Apache y MySQL)
2. Abre **phpMyAdmin**: `http://localhost/phpmyadmin`
3. Haz clic en **"Nuevo"** o ve a la pestaña **"Bases de datos"**
4. NO selecciones ninguna base de datos aún
5. Ve a la pestaña **"SQL"** en la parte superior
6. Copia y pega **TODO** el contenido del archivo `cnt/setup_local_db.sql`
7. Haz clic en **"Ejecutar"**

✅ Esto creará:
- **Base de datos**: `red_dragons_db`
- **Tablas**:
  - `usuarios` (con un admin por defecto)
  - `equipos` (para los equipos del torneo)
  - `matches` (para los matches del bracket)

### Paso 2: Verificar la Configuración

Después de ejecutar el SQL, deberías ver:
```
Base de datos configurada correctamente!
total_usuarios: 1
total_equipos: 0
total_matches: 0
```

### 📝 Credenciales de Administrador Por Defecto

- **Usuario**: `admin`
- **Contraseña**: `admin123`
- **Email**: `admin@reddragonscup.com`

⚠️ **IMPORTANTE**: Cambia estas credenciales después del primer inicio de sesión

---

## 🎮 Cómo Usar el Panel de Administración

### Acceder al Panel
- URL: `http://localhost/Pagina-Red-Dragon/admin_brackets.php`
- **Requisito**: Debes iniciar sesión como administrador

### 1. Agregar Equipos

1. En la sección **"Gestión de Equipos"**
2. Completa los campos:
   - **Nombre del Equipo**: Nombre del equipo
   - **Seed (Posición)**: Número del 1 al 48
3. Haz clic en **"➕ Agregar Equipo"**

**Nota**: Agrega los 48 equipos antes de generar los matches

### 2. Generar Matches de Ronda 1

1. Después de agregar los 48 equipos
2. Haz clic en **"🎲 Generar Matches de Ronda 1"**
3. Esto creará automáticamente 24 matches emparejando los equipos

### 3. Actualizar Puntajes

1. En la sección **"Gestión de Matches y Puntajes"**
2. Encuentra el match que quieres actualizar
3. Haz clic en **"📊 Actualizar Puntaje"**
4. Ingresa los puntos de cada equipo
5. Haz clic en **"💾 Guardar Puntaje"**

**Colores de Puntajes**:
- 🔵 **Azul**: Equipo ganador (puntaje más alto)
- 🔴 **Rojo**: Equipo perdedor (puntaje más bajo)

### 4. Editar o Eliminar Equipos

- **Editar**: Haz clic en **"✏️ Editar"** junto al equipo
- **Eliminar**: Haz clic en **"🗑️ Eliminar"** (confirmación requerida)

---

## 👀 Ver los Brackets Públicos

Los usuarios pueden ver los brackets en:
- URL: `http://localhost/Pagina-Red-Dragon/brackets.php`
- No necesitan iniciar sesión
- Los brackets muestran:
  - Equipos y sus enfrentamientos
  - Puntajes actualizados
  - Winners Bracket (arriba)
  - Losers Bracket (abajo)
  - Gran Final

---

## 🎨 Características del Sistema

### ✅ Winners Bracket
- Ronda 1: 24 matches (48 equipos)
- Ronda 2: 12 matches
- Ronda 3: 6 matches
- Ronda 4: 3 matches
- Semifinales: 2 matches
- Final Winners: 1 match

### ✅ Losers Bracket
- Los equipos que pierden caen al Losers Bracket
- Tienen una segunda oportunidad
- 8 rondas progresivas

### ✅ Gran Final
- Ganador Winners vs Ganador Losers
- Best of 5 (Bo5)

### ✅ Sistema de Líneas Visuales
- Líneas doradas conectan los matches
- Muestra claramente la progresión del torneo
- Scroll horizontal para ver todas las rondas

### ✅ Puntajes con Colores
- **Azul brillante**: Ganador del match
- **Rojo**: Perdedor del match
- Actualización en tiempo real

---

## 🔧 Solución de Problemas

### Las tablas no se crean
- Verifica que la base de datos esté seleccionada
- Asegúrate de ejecutar el SQL completo

### No puedo acceder al panel de administración
- Verifica que tu usuario tenga `rol = 'admin'` en la base de datos
- Tabla: `usuarios`, Campo: `rol`

### Los brackets se ven vacíos
- Primero agrega los equipos
- Luego genera los matches de Ronda 1
- Los matches aparecerán automáticamente

### Las líneas no se conectan bien
- Refresca la página (F5)
- Limpia el caché del navegador

---

## 📱 Responsive

El sistema es responsive y funciona en:
- 💻 Desktop
- 📱 Tablet
- 📱 Mobile (scroll horizontal disponible)

---

## 🎯 Tips para el Administrador

1. **Agrega todos los equipos primero** antes de generar matches
2. **Usa seeds ordenados** (1-48) para un bracket balanceado
3. **Actualiza puntajes inmediatamente** después de cada match
4. **Verifica los ganadores** antes de avanzar a la siguiente ronda
5. **Puedes editar equipos** en cualquier momento

---

## 📞 Soporte

Si tienes problemas:
1. Verifica la consola del navegador (F12)
2. Revisa que la base de datos esté conectada
3. Asegúrate de tener permisos de administrador

---

**¡Listo para usar! 🎮🏆**
