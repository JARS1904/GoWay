# 📊 DIAGRAMAS PLANTUML - ROUTE SELECTED SCREEN

Este directorio contiene todos los diagramas UML de la pantalla **Route Selected Screen** del proyecto GoWay en formato **PlantUML**.

## 📁 Archivos de Diagramas

### 1. **route_selected_screen_usecase.puml**
**Tipo**: Diagrama de Casos de Uso  
**Propósito**: Muestra todos los casos de uso principales y las relaciones entre actores

**Contiene**:
- ✅ UC-01: Buscar Rutas por Origen/Destino
- ✅ UC-02: Marcar como Favorita
- ✅ UC-03: Remover de Favoritas
- ✅ UC-04: Seleccionar y Ver Detalles
- ✅ UC-05: Filtrar por Favoritas
- ✅ UC-06: Ver Todas las Rutas
- ✅ UC-07: Menú de Usuario
- ✅ UC-08: Validar Sesión
- ✅ UC-09: Cargar Ubicaciones
- ✅ UC-10: Cargar Favoritas

**Actores**:
- 👤 Usuario (Rol=2: Conductor/Pasajero)
- 🖥️ Sistema (Servidor)
- 🌐 API Backend
- 💾 Base de Datos

---

### 2. **route_selected_screen_sequence.puml**
**Tipo**: Diagrama de Secuencia  
**Propósito**: Detalla el flujo de interacción entre componentes en cada caso de uso

**Flujos Documentados**:
1. 🔐 **Carga Inicial**: Validación de sesión
2. 🚀 **Inicialización**: Carga de ubicaciones y favoritas
3. 🔍 **Búsqueda**: Usuario busca rutas
4. 👁️ **Detalles**: Visualización de información completa
5. ❤️ **Marcar Favorita**: POST a API
6. 💔 **Remover**: DELETE de API
7. 🏷️ **Filtrar**: Mostrar solo favoritas
8. 🎛️ **Menú**: Navegación de usuario

**Interacciones**:
- Usuario ↔ Navegador (JavaScript)
- Navegador ↔ Servidor PHP
- Servidor ↔ API Backend
- API ↔ Base de Datos

---

### 3. **route_selected_screen_class.puml**
**Tipo**: Diagrama de Clases  
**Propósito**: Define la estructura de datos y entidades del sistema

**Entidades**:
- 👤 **Usuario**: ID, nombre, email, rol
- 🛣️ **Ruta**: ID, origen, destino, empresa, conductor, vehículo
- 🏢 **Empresa**: Información de contacto
- 🚗 **Conductor**: Datos personales
- 🚙 **Vehículo**: Placa, tipo, capacidad
- ⏰ **Horario**: Días de operación, horas
- 🛑 **Parada**: Ubicaciones intermedias
- ⭐ **Favorita**: Relación usuario-ruta
- 📍 **Ubicacion**: Ciudades/puntos geográficos

**Relaciones**:
- Usuario → 1..* Favorita
- Ruta → 1 Empresa
- Ruta → 1 Conductor
- Ruta → 1 Vehículo
- Ruta → 1..* Horario
- Horario → 1..* Parada
- Parada → 1 Ubicacion

---

### 4. **route_selected_screen_activity.puml**
**Tipo**: Diagrama de Actividades  
**Propósito**: Muestra el flujo completo de la aplicación y decisiones

**Decisiones Clave**:
- ✅ ¿Sesión válida?
- ✅ ¿Campos completos para búsqueda?
- ✅ ¿Respuesta exitosa de API?
- ✅ ¿Hay rutas disponibles?
- ✅ ¿Es agregar o remover favorita?
- ✅ ¿Filtro activo?

**Flujos de Usuario**:
- 📍 Seleccionar origen/destino
- 🔍 Buscar rutas
- 👆 Hacer clic en tarjeta
- ❤️ Marcar/desmarcar favoritas
- 🏷️ Filtrar por tipo
- 🎯 Acceder a menú usuario

---

## 🛠️ Cómo Usar estos Diagramas

### Opción 1: Ver en PlantUML Online
1. Ir a https://www.plantuml.com/plantuml/uml/
2. Copiar el contenido de cualquier archivo `.puml`
3. Pegar en el editor
4. Ver el diagrama renderizado

### Opción 2: Instalar PlantUML Localmente
```bash
# Con NPM
npm install -g plantuml

# O con Chocolatey (Windows)
choco install plantuml
```

### Opción 3: Usar VS Code Extension
1. Instalar: "PlantUML" by jebbs
2. Abrir archivo `.puml`
3. Presionar `Alt + D` para vista previa

---

## 📋 Matriz de Cobertura de Casos de Uso

| UC | Tipo | UseCase | Class | Sequence | Activity |
|----|------|---------|-------|----------|----------|
| 01 | Búsqueda | ✅ | ✅ | ✅ | ✅ |
| 02 | Favorita | ✅ | ✅ | ✅ | ✅ |
| 03 | Favorita | ✅ | ✅ | ✅ | ✅ |
| 04 | Visualización | ✅ | ✅ | ✅ | ✅ |
| 05 | Filtro | ✅ | ✅ | ✅ | ✅ |
| 06 | Filtro | ✅ | ✅ | ✅ | ✅ |
| 07 | Navegación | ✅ | ✅ | ✅ | ✅ |
| 08 | Seguridad | ✅ | ✅ | ✅ | ✅ |
| 09 | Carga | ✅ | ✅ | ✅ | ✅ |
| 10 | Carga | ✅ | ✅ | ✅ | ✅ |

---

## 🔐 Seguridad

### Validación de Sesión (UC-08)
```php
// En route_selected_screen.php
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header("Location: login.php");
    exit();
}
```

### Roles Permitidos
- **Rol 2**: Conductor / Pasajero (único acceso permitido)
- Otros roles: Redireccionados a login

---

## 💾 Endpoints de API Utilizados

| Endpoint | Método | Propósito | UC |
|----------|--------|-----------|-----|
| `/api/routes_api.php` | GET | Obtener rutas | UC-01 |
| `/api/favorites_api.php` | GET | Obtener favoritas | UC-10 |
| `/api/favorites_api.php` | POST | Agregar favorita | UC-02 |
| `/api/favorites_api.php` | DELETE | Remover favorita | UC-03 |
| `/api/usuarios.php` | GET | Ubicaciones | UC-09 |

---

## 📱 Layouts y Responsividad

### Diseño Desktop (1200px+)
```
┌─ HEADER ────────────────────────┐
├─ LEFT (450px) │ RIGHT (flex) ───┤
│               │                 │
│ Búsqueda      │ Detalles       │
│ Lista rutas   │                 │
│               │                 │
└────────────────────────────────┘
```

### Diseño Tablet (768px - 1200px)
```
┌─ HEADER ────────────────────────┐
├─ LEFT (flex) ───────────────────┤
│ Búsqueda + Lista                │
├─ RIGHT (flex) ──────────────────┤
│ Detalles                        │
└────────────────────────────────┘
```

### Diseño Móvil (<768px)
```
┌─ HEADER ────────────────────────┐
├─ Búsqueda (100%) ───────────────┤
├─ Lista rutas (100%) ────────────┤
├─ Detalles (100%) ───────────────┤
└────────────────────────────────┘
```

---

## 🎨 Colores de Interfaz

```
Primary Color:        #2962FF (Azul)
Primary Dark:         #1565C0 (Azul Oscuro)
Secondary Color:      #FFC107 (Amarillo/Oro)
Success Color:        #388E3C (Verde)
Error Color:          #D32F2F (Rojo)
Text Color:           #333333 (Gris Oscuro)
Light Gray:           #f5f5f5
Medium Gray:          #e0e0e0
Dark Gray:            #757575
```

---

## ⏱️ Transiciones y Animaciones

Todas las interacciones usan transiciones suaves:
```css
transition: all 0.3s;
```

---

## 📞 Referencia Rápida

### Archivos Relacionados
- `pages/route_selected_screen.php` - Página principal
- `api/routes_api.php` - API de rutas
- `api/favorites_api.php` - API de favoritas
- `assets/js/main.js` - JavaScript principal
- `assets/css/style.css` - Estilos

### Variables de Sesión
- `$_SESSION['id']` - ID del usuario
- `$_SESSION['rol']` - Rol del usuario (debe ser 2)

### Arrays JavaScript
- `currentRoutes` - Rutas actuales en pantalla
- `favorites` - IDs de rutas favoritas
- `currentFilter` - Estado del filtro ("all" o "favs")

---

## 📚 Documentación Adicional

Para más detalles, ver:
- [DIAGRAMA_CASOS_USO_ROUTE_SELECTED_SCREEN.md](../DIAGRAMA_CASOS_USO_ROUTE_SELECTED_SCREEN.md)
- [DIAGRAMA_CASOS_USO_FORMAL.md](../DIAGRAMA_CASOS_USO_FORMAL.md)

---

**Última actualización**: Enero 2026  
**Versión**: 1.0  
**Estado**: ✅ Completo
