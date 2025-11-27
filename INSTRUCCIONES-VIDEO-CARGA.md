# 🎬 Cómo Usar Video en la Pantalla de Carga

## 📋 Resumen
Ahora puedes usar un **video personalizado** en lugar de la animación de carga básica (spinner + logo) en tu página web.

---

## 🚀 Pasos para Activar el Video

### 1. Prepara tu video
- **Formato recomendado**: MP4 (H.264)
- **Duración sugerida**: 2-5 segundos (puede ser en loop)
- **Resolución recomendada**: 1920x1080 o 1280x720
- **Tamaño**: Mantén el archivo lo más ligero posible (idealmente < 5MB)

### 2. Coloca tu video en la carpeta correcta
Guarda tu video en la carpeta `Img/` con el nombre `loading-video.mp4`:
```
c:\xampp\htdocs\Pagina-Red-Dragon\Img\loading-video.mp4
```

**O** si prefieres otro nombre/ubicación, edita la línea 42 en `page-animations.js`:
```javascript
const videoPath = 'Img/tu-video-personalizado.mp4';
```

### 3. Activa el video
Abre el archivo `page-animations.js` y busca la línea 41:
```javascript
const useVideo = true; // true = usar video, false = usar spinner tradicional
```

- **`true`** = Usa el video
- **`false`** = Usa el spinner tradicional (animación original)

---

## 🎨 Personalización Adicional

### Cambiar el tamaño del video
Edita el archivo `animations.css` en la línea 38-46:
```css
.loading-video {
  max-width: 600px;  /* Cambia este valor */
  width: 90%;
  border-radius: 15px;  /* Bordes redondeados */
  box-shadow: 0 0 50px rgba(212, 175, 55, 0.5);  /* Brillo dorado */
}
```

### Cambiar el texto de carga
Edita `page-animations.js` línea 53:
```javascript
<div class="loading-text">Cargando...</div>
```

### Quitar el texto de carga
Elimina o comenta la línea 53 en `page-animations.js`:
```javascript
// <div class="loading-text">Cargando...</div>
```

---

## 📱 Compatibilidad Móvil
El video está optimizado para dispositivos móviles:
- Se ajusta automáticamente al tamaño de pantalla
- Incluye el atributo `playsinline` para iOS
- En pantallas pequeñas, el tamaño máximo es 400px

---

## 🔧 Solución de Problemas

### El video no se reproduce
1. **Verifica la ruta**: Asegúrate de que el archivo existe en `Img/loading-video.mp4`
2. **Formato correcto**: Usa MP4 con códec H.264
3. **Permisos**: Verifica que el archivo tenga permisos de lectura
4. **Consola del navegador**: Presiona F12 y revisa si hay errores

### El video se ve muy grande/pequeño
Ajusta el valor `max-width` en `animations.css` línea 40:
```css
max-width: 400px;  /* Prueba diferentes valores */
```

### Quiero volver al spinner original
Cambia `useVideo` a `false` en `page-animations.js` línea 41:
```javascript
const useVideo = false;
```

---

## 💡 Recomendaciones

### Para mejor rendimiento:
- **Comprime tu video**: Usa herramientas como HandBrake o FFmpeg
- **Optimiza el códec**: H.264 es el más compatible
- **Reduce la duración**: Videos cortos en loop funcionan mejor
- **Bitrate bajo**: 1-3 Mbps es suficiente para videos de carga

### Ejemplo de compresión con FFmpeg:
```bash
ffmpeg -i input.mp4 -vcodec h264 -acodec aac -b:v 2M -s 1280x720 loading-video.mp4
```

---

## 📂 Archivos Modificados
- ✅ `animations.css` - Estilos para el video
- ✅ `page-animations.js` - Lógica para mostrar video/spinner
- 📄 Este archivo de instrucciones

---

## 🎯 Ejemplo Rápido

**Opción 1: Video simple**
```javascript
// page-animations.js línea 41-42
const useVideo = true;
const videoPath = 'Img/loading-video.mp4';
```

**Opción 2: Video con ruta externa**
```javascript
const useVideo = true;
const videoPath = 'https://tu-servidor.com/video.mp4';
```

**Opción 3: Múltiples formatos (mejor compatibilidad)**
Edita la línea 48-50 en `page-animations.js`:
```html
<video autoplay muted loop playsinline>
  <source src="Img/loading-video.mp4" type="video/mp4">
  <source src="Img/loading-video.webm" type="video/webm">
  Tu navegador no soporta el elemento de video.
</video>
```

---

## ✨ ¡Listo!
Ahora tu página tiene una pantalla de carga personalizada con video. 🎉

**Recuerda**: Siempre prueba en diferentes navegadores y dispositivos.
