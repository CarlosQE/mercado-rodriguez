# Imágenes del mercado generadas con IA

La galería de la portada acepta imágenes de relleno (las composiciones SVG de `public/img/mercado/`) y fotos subidas por la administración desde **`/admin/fotos`**.

## Recomendaciones técnicas

| Aspecto | Recomendación |
|---|---|
| Tamaño | 1600 × 900 px (proporción 16:9). También sirve 1200 × 675 px. |
| Formato | JPG o WebP para fotografías; PNG solo si necesitas transparencia. |
| Peso | Menos de 1 MB por imagen; el límite del formulario es 4 MB. |
| Color | Luz natural, tonos cálidos y sobrios, sin saturación excesiva. |
| Texto | Sin texto legible, rótulos, marcas ni logotipos. |
| Personas | Preferible mostrar manos, siluetas o personas de espaldas; evitar rostros reconocibles. |
| Composición | Horizontal, con el punto de interés al centro y espacio para recortes. |

## Cómo subir las fotos

1. Entra con un usuario administrador.
2. Abre **Fotos** en la barra superior (`/admin/fotos`).
3. Completa título, descripción y orden; elige el archivo y pulsa **Subir foto**.
4. Las fotos se guardan en el disco `public`, carpeta `mercado/`. En el servidor debe existir el enlace `public/storage` (`php artisan storage:link`).
5. Para reemplazar una imagen, usa **Reemplazar imagen** en su tarjeta. Para eliminarla, pulsa **Eliminar**; el archivo del disco también se borra. Las imágenes de relleno de `public/img/mercado/` no se eliminan del disco.

## Prompts sugeridos

1. **Fachada:** "Fotografía realista de la fachada de un mercado municipal de barrio en La Paz, Bolivia, al amanecer, toldos de lona, paredes claras y puertas metálicas, luz suave, sin texto ni letreros legibles, 16:9".
2. **Pasillo de frutas y verduras:** "Pasillo luminoso de un mercado de barrio con cajones de madera llenos de frutas y verduras de temporada, colores naturales, techo de calamina, sin personas reconocibles, sin texto, fotografía documental, 16:9".
3. **Puestos de carne:** "Mostrador de carnes frescas en un mercado municipal, vitrina de vidrio, azulejos blancos, luz cálida, manos de un vendedor con guantes, sin rostros ni texto legible, realismo fotográfico, 16:9".
4. **Zona de comida:** "Puestos de comida casera en un mercado de barrio, ollas grandes, vapor suave, mesas de madera y bancos, ambiente acogedor, sin texto ni marcas, fotografía realista, 16:9".
5. **Vista general:** "Vista general desde un pasillo elevado de un mercado municipal lleno de puestos ordenados por secciones, techos de calamina y luz que entra por claraboyas, sin texto, realismo documental, 16:9".
6. **Detalle de productos:** "Primer plano de papas, tomates y hierbas frescas acomodadas en canastas de mimbre, gotas de agua, fondo desenfocado de un mercado, luz natural, sin texto ni etiquetas, 16:9".
7. **Abarrotes:** "Estantes de un puesto de abarrotes con costales de granos, arroz, azúcar y especias, iluminación cálida, orden cotidiano, sin marcas ni texto legible, fotografía realista, 16:9".
8. **Ambiente de barrio:** "Entrada de un mercado de barrio en una calle de La Paz, gente caminando de espaldas con bolsas de compras, paredes de ladrillo y montañas al fondo, luz de mañana, sin texto ni rostros reconocibles, 16:9".
