# 🗑️ LIMPIEZA DE ARCHIVOS INNECESARIOS

## Fecha: 27 de Noviembre, 2025

---

## 📋 ARCHIVOS PARA ELIMINAR

### 1. **Archivos de Documentación Obsoletos**
- ❌ `LIMPIEZA.md` - Documentación antigua de limpieza anterior
- ❌ `ANIMACIONES-INFO.md` - Documentación de animaciones (ya implementadas)
- ❌ `INICIO_RAPIDO.md` - Guía de inicio rápido (obsoleta)
- ❌ `INSTRUCCIONES_BRACKETS.md` - Instrucciones de brackets (obsoletas)

### 2. **Archivos de Seguridad/Desarrollo**
- ❌ `rst.php` - **CRÍTICO**: Script de reseteo de contraseñas (RIESGO DE SEGURIDAD)
- ❌ `instalar.php` - Instalador de BD (solo usar una vez, luego eliminar)

### 3. **Archivos Backup**
- ❌ `brackets.css.backup` - Backup de CSS (innecesario si tienes control de versiones)

### 4. **Archivos SQL Redundantes**
- ❌ `bdactualizada.sql` - SQL de respaldo (mover a carpeta backup fuera del web root)
- ❌ `cnt/brackets_db.sql` - SQL de brackets (mover a carpeta backup)
- ❌ `cnt/setup_local_db.sql` - SQL de setup (mover a carpeta backup)

### 5. **Imágenes No Utilizadas**
- ⚠️ `Img/imagen de carga.png` - Ya no se usa (reemplazada por video)
- ⚠️ `Img/logo hacia la derecha.png` - Verificar si se usa

---

## ⚠️ ARCHIVOS A MANTENER (IMPORTANTES)

### Archivos PHP Funcionales:
- ✅ `index.php` - Página principal
- ✅ `torneo.php` - Gestión de torneos
- ✅ `anticheats.php` - Sistema anticheat
- ✅ `contacto.php` - Página de contacto
- ✅ `brackets.php` - Sistema de brackets
- ✅ `dashboard.php` - Panel de usuario
- ✅ `login.php` - Inicio de sesión
- ✅ `registro.php` - Registro de usuarios
- ✅ `generar_claves.php` - Generador de claves
- ✅ `privacidad.php` - Política de privacidad
- ✅ `terminos.php` - Términos y condiciones
- ✅ `registro_exitoso.php` - Confirmación de registro
- ✅ `logout.php` - Cerrar sesión
- ✅ `procesar_login.php` - Procesamiento de login
- ✅ `procesar_registro.php` - Procesamiento de registro
- ✅ `admin_brackets.php` - Admin de brackets

### Archivos CSS/JS:
- ✅ `styles.css` - Estilos principales
- ✅ `animations.css` - Animaciones
- ✅ `brackets.css` - Estilos de brackets
- ✅ `scripts.js` - Scripts principales
- ✅ `page-animations.js` - Animaciones de página
- ✅ `registro-validation.js` - Validación de registro

### Carpetas:
- ✅ `admin/` - Panel de administración
- ✅ `cnt/` - Conexión a BD (mantener solo conexion.php)
- ✅ `Img/` - Imágenes (limpiar las no usadas)

### Archivos de Datos:
- ✅ `tokens_database.json` - Base de datos de tokens
- ✅ `README.md` - Documentación principal
- ✅ `INSTRUCCIONES-VIDEO-CARGA.md` - Instrucciones del video de carga

---

## 🎯 ACCIONES RECOMENDADAS

### Prioridad ALTA (Seguridad):
1. **ELIMINAR INMEDIATAMENTE**:
   - `rst.php` - Riesgo de seguridad crítico
   - `instalar.php` - Solo si ya instalaste la BD

### Prioridad MEDIA (Limpieza):
2. **Mover a carpeta backup externa**:
   - Archivos `.sql` a una carpeta fuera del directorio web
   - Archivos `.backup` a carpeta de respaldos

3. **Eliminar documentación obsoleta**:
   - Archivos `.md` antiguos que ya no son relevantes

### Prioridad BAJA (Optimización):
4. **Revisar imágenes no utilizadas**:
   - Verificar qué imágenes realmente se usan
   - Eliminar las que no se referencian en ningún archivo

---

## 📊 ESPACIO A LIBERAR ESTIMADO

| Tipo de Archivo | Cantidad | Espacio Aprox. |
|------------------|----------|----------------|
| Documentación MD | 4 archivos | ~50 KB |
| Scripts PHP | 2 archivos | ~5 KB |
| Backups CSS | 1 archivo | ~20 KB |
| SQL | 3 archivos | ~100 KB |
| Imágenes | 1-2 archivos | ~150 KB |
| **TOTAL** | **~10 archivos** | **~325 KB** |

---

## ✅ BENEFICIOS DE LA LIMPIEZA

1. **Seguridad Mejorada**:
   - Eliminar scripts de reseteo de contraseñas
   - Remover instaladores expuestos

2. **Mejor Organización**:
   - Menos archivos innecesarios
   - Estructura más clara

3. **Rendimiento**:
   - Menos archivos para escanear
   - Backups más rápidos

4. **Mantenimiento**:
   - Más fácil encontrar archivos importantes
   - Menos confusión

---

## 🔒 RECOMENDACIONES DE SEGURIDAD

1. **Crear carpeta backup fuera del web root**:
   ```
   c:\xampp\backups\Pagina-Red-Dragon\
   ```

2. **Mover archivos SQL a backup**:
   - No deben estar accesibles vía web

3. **Eliminar scripts de instalación/reseteo**:
   - Solo mantenerlos en backup local

4. **Revisar permisos de archivos**:
   - Archivos PHP: 644
   - Carpetas: 755

---

## 📝 NOTAS FINALES

- Hacer backup completo antes de eliminar
- Verificar que la página funcione después de cada eliminación
- Mantener solo `README.md` e `INSTRUCCIONES-VIDEO-CARGA.md`
- Considerar usar `.gitignore` para archivos temporales
