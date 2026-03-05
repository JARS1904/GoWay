# 🚀 GUÍA DE INSTALACIÓN Y VISUALIZACIÓN - DIAGRAMAS PLANTUML

## 📦 Archivos Disponibles

Se han creado **6 diagramas PlantUML** en tu proyecto GoWay:

```
route_selected_screen_usecase.puml           (Casos de Uso)
route_selected_screen_sequence.puml          (Secuencias)
route_selected_screen_class.puml             (Clases)
route_selected_screen_activity.puml          (Actividades)
route_selected_screen_components.puml        (Componentes)
route_selected_screen_deployment.puml        (Despliegue)
```

---

## 🌐 OPCIÓN 1: Ver Online (Más Fácil)

### Método: PlantUML Online Editor

1. **Abre el navegador**:
   - Ve a: https://www.plantuml.com/plantuml/uml/

2. **Copia el contenido de un archivo .puml**:
   ```bash
   # Por ejemplo, copia el contenido de:
   route_selected_screen_usecase.puml
   ```

3. **Pega en el editor online**:
   - Selecciona todo el contenido del archivo
   - Copia (Ctrl+C)
   - Pega en el cuadro de texto izquierdo
   - El diagrama se renderiza automáticamente a la derecha

4. **Exporta si necesitas**:
   - Botón "Download as PNG"
   - Botón "Download as SVG"

### Ventajas:
✅ Sin instalación  
✅ Funciona en cualquier navegador  
✅ Exportación fácil  
✅ Siempre disponible online  

---

## 💻 OPCIÓN 2: VS Code Extension (Recomendado)

### Paso 1: Instalar Extension

1. Abre **VS Code**
2. Ve a **Extensiones** (Ctrl+Shift+X)
3. Busca: **PlantUML**
4. Instala: **PlantUML** by jebbs
   - ID: jebbs.plantuml
   - URL: https://marketplace.visualstudio.com/items?itemName=jebbs.plantuml

```
Pasos en VS Code:
┌─────────────────────────────────┐
│ Ctrl+Shift+X                    │
│ → Buscar "PlantUML"             │
│ → Click "Install" (by jebbs)    │
│ → Esperar instalación           │
│ → Reload VS Code                │
└─────────────────────────────────┘
```

### Paso 2: Usar la Extension

1. **Abre un archivo .puml**:
   - File → Open File
   - Selecciona `route_selected_screen_usecase.puml`

2. **Vista Previa**:
   - Opción A: Botón de vista previa (esquina superior derecha)
   - Opción B: Presiona `Alt + D`
   - Opción C: Click derecho → "Preview Current Diagram"

3. **Edita y ve cambios en tiempo real**:
   - La vista previa se actualiza automáticamente

### Ventajas:
✅ Integrado en tu editor  
✅ Edición en tiempo real  
✅ Soporte para múltiples archivos  
✅ Sin necesidad de browser  

### Configuración Opcional:

Añade esto a `settings.json` (Ctrl+,):

```json
{
  "plantuml.exportOutDir": "./diagrams",
  "plantuml.exportFormat": "png",
  "plantuml.renderer": "Local",
  "plantuml.previewAutoUpdate": true
}
```

---

## 🔧 OPCIÓN 3: Línea de Comandos (Avanzado)

### Requisitos:
- Node.js instalado
- Java Runtime Environment (JRE)

### Paso 1: Instalar PlantUML globalmente

```bash
# Con npm
npm install -g plantuml

# O con Chocolatey (Windows)
choco install plantuml

# O con brew (macOS)
brew install plantuml
```

### Paso 2: Generar diagramas

```bash
# Generar un diagrama
plantuml route_selected_screen_usecase.puml

# Generar múltiples
plantuml route_selected_screen_*.puml

# Especificar formato
plantuml -tpng route_selected_screen_usecase.puml
plantuml -tsvg route_selected_screen_usecase.puml

# Especificar carpeta de salida
plantuml -o ./generated route_selected_screen_*.puml
```

### Paso 3: Ver resultado

```bash
# Windows
start route_selected_screen_usecase.png

# macOS
open route_selected_screen_usecase.png

# Linux
xdg-open route_selected_screen_usecase.png
```

### Ventajas:
✅ Control total  
✅ Automatización  
✅ Integración en scripts  
✅ Batches de múltiples diagramas  

---

## 🎨 OPCIÓN 4: Integración CI/CD (Para Equipos)

### GitHub Actions (Generar automáticamente)

Crea `.github/workflows/plantuml.yml`:

```yaml
name: Generate PlantUML Diagrams

on:
  push:
    paths:
      - '**.puml'
  workflow_dispatch:

jobs:
  generate:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Generate PlantUML diagrams
        uses: grassedge/generate-plantuml-action@v1.5
        with:
          path: ./
          format: png,svg
          output: ./diagrams
      
      - name: Commit changes
        run: |
          git config --local user.email "action@github.com"
          git config --local user.name "GitHub Action"
          git add diagrams/
          git commit -m "Auto: Generate PlantUML diagrams" || exit 0
          git push
```

---

## 📊 COMPARATIVA DE OPCIONES

| Opción | Instalación | Facilidad | Tiempo Real | Automatización |
|--------|------------|----------|------------|----------------|
| **Online** | ❌ No | ⭐⭐⭐⭐⭐ | ❌ | ❌ |
| **VS Code** | ✅ Extension | ⭐⭐⭐⭐ | ✅ | ❌ |
| **CLI** | ✅ npm/brew | ⭐⭐⭐ | ❌ | ✅ |
| **CI/CD** | ✅ GitHub | ⭐⭐ | ✅ | ✅ |

**Recomendación**: Usa **VS Code Extension** para desarrollo local.

---

## 🖼️ EXPORTAR DIAGRAMAS

### Desde VS Code Extension

1. Abre vista previa (Alt+D)
2. Click en el diagrama
3. Botones en la esquina superior derecha:
   - 💾 Save as PNG
   - 💾 Save as SVG
   - 🔍 Zoom

### Desde Online Editor

1. Diagrama renderizado a la derecha
2. Botón "Download"
3. Selecciona formato:
   - PNG
   - SVG
   - PDF (requiere cuenta)

### Desde CLI

```bash
plantuml -tpng route_selected_screen_usecase.puml
# Genera: route_selected_screen_usecase.png
```

---

## 📁 ESTRUCTURA DE CARPETAS RECOMENDADA

```
GoWay/
├── diagrams/ (nuevo)
│   ├── usecase.png
│   ├── sequence.png
│   ├── class.png
│   ├── activity.png
│   ├── components.png
│   └── deployment.png
│
├── route_selected_screen_*.puml (archivos originales)
│
└── README_DIAGRAMAS_PLANTUML.md
```

Para organizar:
```bash
mkdir diagrams
plantuml -o ./diagrams route_selected_screen_*.puml
```

---

## 🐛 SOLUCIÓN DE PROBLEMAS

### Problema: "Comando plantuml no encontrado"

**Solución**:
```bash
# Verifica que esté instalado
npm list -g plantuml

# Si no está, instala
npm install -g plantuml

# O verifica ruta
where plantuml  # Windows
which plantuml  # macOS/Linux
```

### Problema: Extension VS Code no funciona

**Solución**:
1. Abre Command Palette (Ctrl+Shift+P)
2. Escribe: "PlantUML: Preview"
3. Si da error, reinstala:
   - Desinstala extension
   - Reload VS Code
   - Reinstala desde Marketplace

### Problema: No se ve la vista previa en VS Code

**Solución**:
1. Verifica que sea archivo `.puml`
2. Presiona Alt+D
3. Si no funciona, prueba con:
   - Extension "PlantUML Export"
   - O usa online editor

### Problema: Generar PNG muy lento

**Solución**:
```bash
# Usa formato SVG (más rápido)
plantuml -tsvg route_selected_screen_*.puml

# O especifica máximo de threads
plantuml -Djava.awt.headless=true route_selected_screen_*.puml
```

---

## 🎓 FLUJO RECOMENDADO

### Para Principiantes:
```
1. Abre: https://www.plantuml.com/plantuml/uml/
2. Copia contenido .puml
3. Pega en editor online
4. ¡Listo! Ves el diagrama
```

### Para Desarrolladores:
```
1. Instala VS Code Extension
2. Abre archivo .puml
3. Presiona Alt+D
4. Edita y ve cambios en tiempo real
5. Exporta cuando necesites
```

### Para Teams:
```
1. Configura CI/CD (GitHub Actions)
2. Diagramas se generan automáticamente
3. Se suben a repo
4. Todo el equipo los ve en web
```

---

## 📚 REFERENCIAS

### PlantUML Oficial:
- Sitio Web: https://plantuml.com/
- Documentación: https://plantuml.com/guide
- Online Editor: https://www.plantuml.com/plantuml/uml/

### VS Code Extension:
- ID: jebbs.plantuml
- GitHub: https://github.com/jebbs/vscode-plantuml
- Marketplace: https://marketplace.visualstudio.com/items?itemName=jebbs.plantuml

### Tutoriales:
- Casos de Uso: https://plantuml.com/use-case-diagram
- Secuencias: https://plantuml.com/sequence-diagram
- Clases: https://plantuml.com/class-diagram
- Actividades: https://plantuml.com/activity-diagram

---

## ✅ CHECKLIST INSTALACIÓN

```
[ ] Descargaste archivos .puml
[ ] Elegiste método de visualización
[ ] Instalaste dependencias (si necesario)
[ ] Abriste primer diagrama
[ ] Viste diagrama renderizado
[ ] (Opcional) Exportaste como PNG/SVG
[ ] (Opcional) Configuraste para equipo
```

---

## 🎉 ¡LISTO!

Ya puedes:
- ✅ Ver todos los diagramas
- ✅ Entender la arquitectura
- ✅ Editar si necesitas
- ✅ Exportar para presentaciones
- ✅ Compartir con el equipo

**Próximos pasos**:
1. Explore cada diagrama
2. Lee la documentación asociada
3. Úsalos como referencia durante desarrollo
4. Mantén actualizados si el sistema cambia

---

**¿Necesitas ayuda?** Revisa la sección "Solución de Problemas" o consulta la documentación oficial de PlantUML.

Última actualización: Enero 2026  
Versión: 1.0
