# 🎬 ANIMACIONES DE ENTRADA - RED DRAGONS CUP

## ✨ Características Implementadas

### 1. **Pantalla de Carga (Loading Screen)**
- ✅ Logo animado con efecto flotante
- ✅ Spinner rotatorio con colores del tema (naranja/dorado)
- ✅ Texto "Cargando..." con pulso animado
- ✅ Transición suave de desvanecimiento
- ⏱️ **Duración**: 1.5 segundos

### 2. **Animaciones de Elementos**
- ✅ **Navbar**: Desliza desde arriba
- ✅ **Logo Left 4 Dead**: Rotación y escala de entrada
- ✅ **Título Hero**: Efecto de revelado con desenfoque
- ✅ **Logo Hero**: Escala suave de entrada
- ✅ **Botones**: Deslizamiento secuencial hacia arriba
- ✅ **Secciones**: Fade-in cuando aparecen en viewport

### 3. **Efectos Visuales**
- ✅ **Partículas flotantes**: 20 partículas doradas animadas en el fondo
- ✅ **Efecto de brillo hover**: Resplandor en botones y links
- ✅ **Parallax del mouse**: Logo hero se mueve sutilmente con el cursor
- ✅ **Scroll reveal**: Animaciones al hacer scroll

### 4. **Animaciones Opcionales**
- 🎮 **Efecto Glitch**: Disponible para títulos (solo agregar clase `glitch-effect`)
- ✨ **Glow on hover**: Efecto de resplandor automático en elementos interactivos

---

## 📂 Archivos Creados

### `animations.css` (9.7 KB)
Contiene todas las animaciones y efectos visuales:
- Pantalla de carga
- Keyframes de animaciones
- Efectos de partículas
- Responsive design

### `page-animations.js` (5.2 KB)
Script que controla:
- Creación de pantalla de carga
- Inicialización de animaciones
- Generación de partículas
- Efectos de scroll reveal
- Parallax del mouse

---

## 🎨 Personalización

### Cambiar duración de la pantalla de carga

En `page-animations.js`, línea 18:
```javascript
setTimeout(function() {
  hideLoadingScreen();
  animatePageElements();
}, 1500); // Cambiar este valor (en milisegundos)
```

### Modificar cantidad de partículas

En `page-animations.js`, línea 118:
```javascript
const particleCount = 20; // Cambiar este número
```

### Cambiar velocidad de animaciones

En `animations.css`, modifica las duraciones:
```css
@keyframes fadeInUp {
  /* Cambiar el timing en las reglas de animación */
}
```

### Activar efecto Glitch en un título

Agrega la clase en tu HTML:
```html
<h1 class="glitch-effect">Tu Título</h1>
```

---

## 🎯 Animaciones por Elemento

| Elemento | Animación | Delay | Duración |
|----------|-----------|-------|----------|
| Navbar | Slide Down | 0.3s | 0.8s |
| Logo Left 4 Dead | Rotate + Scale | 0.5s | 1.0s |
| Título Hero | Reveal + Blur | 0.5s | 1.5s |
| Logo Hero | Scale In | 0.8s | 1.0s |
| Subtítulo | Fade Up | 0.4s | 1.0s |
| Botones | Slide Up | 1.2s+ | 0.8s |
| Info Tags | Fade Up | 0.6s | 1.0s |
| Secciones | Fade Up | On Scroll | 1.0s |

---

## 🔧 Desactivar Animaciones

### Opción 1: Comentar archivos en PHP
Comenta estas líneas en cada archivo .php:
```html
<!-- <link rel="stylesheet" href="animations.css" /> -->
<!-- <script src="page-animations.js"></script> -->
```

### Opción 2: Desactivar solo la pantalla de carga
En `page-animations.js`, cambia:
```javascript
}, 1500); // A: }, 0);
```

### Opción 3: Desactivar animaciones específicas
Elimina las clases correspondientes en `page-animations.js`

---

## 🎭 Efectos Especiales

### Parallax del Mouse
El logo hero se mueve sutilmente siguiendo el cursor:
- **Rango**: ±20px horizontal y vertical
- **Transición**: 0.3s ease-out
- **Activación**: Automática en el logo hero

### Scroll Reveal
Las secciones aparecen cuando son visibles:
- **Threshold**: 10% del elemento visible
- **Observador**: IntersectionObserver API
- **Compatibilidad**: Navegadores modernos

### Partículas Flotantes
- **Cantidad**: 20 partículas
- **Duración**: 8 segundos por ciclo
- **Variación**: Posición, tamaño y deriva aleatorios
- **Efecto**: Movimiento ascendente con desvanecimiento

---

## 💡 Tips de Optimización

1. **Reducir partículas en móviles**:
   ```javascript
   const particleCount = window.innerWidth < 768 ? 10 : 20;
   ```

2. **Acortar tiempo de carga**:
   - Reducir el `setTimeout` de 1500ms a 800ms

3. **Simplificar animaciones**:
   - Reducir los delays en animaciones secuenciales

4. **Mejorar rendimiento**:
   - Las animaciones usan `transform` y `opacity` (GPU accelerated)
   - IntersectionObserver es eficiente para scroll reveals

---

## 🌐 Compatibilidad

| Característica | Chrome | Firefox | Safari | Edge |
|----------------|--------|---------|--------|------|
| CSS Animations | ✅ | ✅ | ✅ | ✅ |
| IntersectionObserver | ✅ | ✅ | ✅ | ✅ |
| Backdrop Filter | ✅ | ⚠️ | ✅ | ✅ |
| CSS Variables | ✅ | ✅ | ✅ | ✅ |

⚠️ Firefox: Backdrop filter requiere habilitarlo en about:config

---

## 🚀 Resultado Final

### Al cargar la página:
1. **0.0s**: Aparece pantalla de carga con logo flotante
2. **1.5s**: Pantalla se desvanece
3. **1.8s**: Navbar desliza desde arriba
4. **2.0s**: Logo Left 4 Dead aparece con rotación
5. **2.0s**: Título hero se revela con efecto blur
6. **2.3s**: Logo hero escala suavemente
7. **2.5s**: Subtítulo y botones aparecen secuencialmente
8. **3.0s**: Partículas comienzan a flotar
9. **On Scroll**: Secciones aparecen progresivamente

### Efectos interactivos:
- ✨ Hover en botones: Efecto de brillo radial
- 🖱️ Movimiento del mouse: Parallax sutil en logo hero
- 📜 Scroll: Revelado progresivo de contenido
- 🎯 Click: Transiciones suaves

---

## 📝 Notas Importantes

- **Rendimiento**: Las animaciones están optimizadas con GPU acceleration
- **Accesibilidad**: Respetar `prefers-reduced-motion` (próxima implementación)
- **SEO**: Las animaciones no afectan el contenido indexable
- **UX**: Los delays están calculados para fluidez visual

---

## 🎉 ¡Disfruta de tus animaciones GOOOOOD!

Las animaciones están diseñadas para ser impactantes pero no intrusivas, 
creando una experiencia profesional y moderna que complementa el tema 
gaming de tu torneo Red Dragons Cup.
