# 📊 DIAGRAMAS PLANTUML - ROUTE SELECTED SCREEN

## ✅ Diagramas Disponibles

Aquí se encuentran todos los diagramas PlantUML para la pantalla **Route Selected Screen** del sistema GoWay.

---

## 📋 Tabla de Contenidos

| # | Archivo | Tipo | Descripción |
|---|---------|------|-------------|
| 1️⃣ | `route_selected_screen_usecase.puml` | **Casos de Uso** | Actores y casos de uso principales |
| 2️⃣ | `route_selected_screen_sequence.puml` | **Secuencia** | Flujos de interacción paso a paso |
| 3️⃣ | `route_selected_screen_class.puml` | **Clases** | Estructura de datos y entidades |
| 4️⃣ | `route_selected_screen_activity.puml` | **Actividades** | Flujo de control y decisiones |
| 5️⃣ | `route_selected_screen_components.puml` | **Componentes** | Arquitectura de componentes |
| 6️⃣ | `route_selected_screen_deployment.puml` | **Despliegue** | Infraestructura y configuración |

---

## 🎯 1. Diagrama de Casos de Uso

**Archivo**: `route_selected_screen_usecase.puml`

### Actores
- 👤 **Usuario** (Rol=2): Conductor/Pasajero autenticado
- 🖥️ **Sistema**: Servidor PHP
- 🌐 **API Backend**: Servicios HTTP
- 💾 **Base de Datos**: Almacenamiento persistente

### Casos de Uso
```
UC-01 ────► Buscar Rutas por Origen/Destino
UC-02 ────► Marcar como Favorita
UC-03 ────► Remover de Favoritas
UC-04 ────► Seleccionar y Ver Detalles
UC-05 ────► Filtrar por Favoritas
UC-06 ────► Ver Todas las Rutas
UC-07 ────► Menú de Usuario
UC-08 ────► Validar Sesión
UC-09 ────► Cargar Ubicaciones
UC-10 ────► Cargar Favoritas
```

### Relaciones
- `<<incluye>>` - Un UC incluye otro
- `<<utiliza>>` - Un UC utiliza otro
- `<<habilita>>` - Un UC habilita otro

---

## 📞 2. Diagrama de Secuencia

**Archivo**: `route_selected_screen_sequence.puml`

### Participantes
```
Usuario ─────┐
             │
       ┌─────▼─────────┐
       │ Navegador     │
       │ (HTML/JS)     │
       └─────┬─────────┘
             │
       ┌─────▼─────────┐
       │ Servidor PHP  │
       └─────┬─────────┘
             │
       ┌─────▼─────────┐
       │ API Backend   │
       └─────┬─────────┘
             │
       ┌─────▼─────────┐
       │ Base de Datos │
       └───────────────┘
```

### Flujos Principales
1. ✅ **Validación de Sesión**: Verificar rol=2
2. ✅ **Carga de Datos**: Ubicaciones y Favoritas
3. ✅ **Búsqueda**: GET rutas
4. ✅ **Detalles**: Mostrar información
5. ✅ **Favorita**: POST/DELETE

---

## 🗂️ 3. Diagrama de Clases

**Archivo**: `route_selected_screen_class.puml`

### Entidades Principales

```
┌─────────────────────────────────────┐
│ Usuario                             │
├─────────────────────────────────────┤
│ - id: int                           │
│ - nombre: string                    │
│ - email: string                     │
│ - rol: int (2 = Conductor/Pasajero) │
├─────────────────────────────────────┤
│ + validarSesion()                   │
│ + obtenerFavoritas()                │
└─────────────────────────────────────┘
         │
         ▼ 1..*
    ┌─────────────┐
    │ Favorita    │
    │─────────────│
    │ id_usuario  │
    │ id_ruta     │
    └─────────────┘
         │
         ▼ 1..1
    ┌────────────────┐
    │ Ruta           │
    │────────────────│
    │ - origen       │
    │ - destino      │
    │ - id_empresa   │
    │ - id_conductor │
    │ - id_vehiculo  │
    └────────────────┘
```

### Relaciones
- `Usuario "1" --> "*" Favorita`
- `Ruta "1" --> "1" Empresa`
- `Ruta "1" --> "1" Conductor`
- `Ruta "1" --> "1" Vehículo`
- `Ruta "1" --> "*" Horario`

---

## 🔄 4. Diagrama de Actividades

**Archivo**: `route_selected_screen_activity.puml`

### Puntos de Decisión

```
¿Sesión válida?
      ↓
¿Campos completos?
      ↓
¿API responde?
      ↓
¿Hay rutas?
      ↓
¿Marcar favorita?
      ↓
¿Filtro activo?
```

### Actividades Principales
1. 🔐 Validar sesión
2. 📦 Cargar datos iniciales
3. 🔍 Búsqueda de rutas
4. 👁️ Mostrar detalles
5. ❤️ Gestionar favoritas
6. 🏷️ Filtrar resultados

---

## 🧩 5. Diagrama de Componentes

**Archivo**: `route_selected_screen_components.puml`

### Capas de Arquitectura

```
┌─────────────────────────────┐
│  Cliente (Navegador)        │
│ ┌───┬────────┬──────────┐   │
│ │HTML│ CSS   │JavaScript│   │
│ └───┴────────┴──────────┘   │
└─────────────────────────────┘
           │
           │ HTTP/JSON
           ▼
┌─────────────────────────────┐
│  Servidor (PHP + Apache)    │
│ ┌───────────────────────┐   │
│ │ Route Selected Screen  │   │
│ │ Session Manager       │   │
│ │ API Router            │   │
│ └───────────────────────┘   │
└─────────────────────────────┘
           │
           │ SQL
           ▼
┌─────────────────────────────┐
│  Base de Datos (MySQL)      │
│ ├─ rutas                    │
│ ├─ favoritas               │
│ ├─ usuarios                │
│ ├─ horarios                │
│ └─ ...                      │
└─────────────────────────────┘
```

### Componentes
- **HTML Structure**: Página PHP
- **CSS Styles**: Estilos responsivos
- **JavaScript Logic**: Interacciones AJAX
- **API Router**: Endpoints HTTP
- **Database**: Almacenamiento

---

## 🚀 6. Diagrama de Despliegue

**Archivo**: `route_selected_screen_deployment.puml`

### Nodos de Despliegue

```
┌──────────────────────┐
│  Dispositivo Usuario │
│ ┌────────────────┐   │
│ │ Navegador Web  │   │
│ │ (HTML/CSS/JS)  │   │
│ └────────────────┘   │
└──────────────────────┘
          │ HTTP
          ▼
┌──────────────────────┐
│   Servidor XAMPP     │
│ ┌────────────────┐   │
│ │ Apache + PHP   │   │
│ │ /GoWay/        │   │
│ └────────────────┘   │
└──────────────────────┘
          │ SQL
          ▼
┌──────────────────────┐
│   MySQL/MariaDB      │
│ ┌────────────────┐   │
│ │ goway_db       │   │
│ │ (Tablas)       │   │
│ └────────────────┘   │
└──────────────────────┘
```

### Configuración
- **Protocolo**: HTTP/HTTPS + JSON
- **Puerto Servidor**: 80
- **Puerto MySQL**: 3306
- **Ubicación**: `c:\xampp\htdocs\GoWay\`

---

## 🔐 Seguridad Implementada

### Validación de Sesión
```php
if (!isset($_SESSION['id']) || $_SESSION['rol'] != 2) {
    header("Location: login.php");
    exit();
}
```

### Verificaciones
- ✅ Sesión activa (isset)
- ✅ Rol correcto (rol == 2)
- ✅ Usuario autenticado
- ✅ CSRF protection (implícito con sesión)
- ✅ Validación de entrada en API
- ✅ Prepared statements en BD

---

## 📱 Responsive Design

### Breakpoints
| Tamaño | Layout |
|--------|--------|
| 1200px+ | Dos columnas lado a lado |
| 768-1200px | Dos columnas apiladas |
| <768px | Una columna |

### Componentes
- Header fijo (sticky)
- Left panel scrollable
- Right panel scrollable
- Menú responsive

---

## 🚀 Cómo Ver los Diagramas

### Opción 1: PlantUML Online
1. Ir a https://www.plantuml.com/plantuml/uml/
2. Copiar contenido del archivo `.puml`
3. Pegar en el editor online
4. Ver resultado renderizado

### Opción 2: VS Code Extension
1. Instalar "PlantUML" by jebbs
2. Abrir archivo `.puml`
3. Click derecho → "Preview Current Diagram"
4. O presionar `Alt + D`

### Opción 3: Comando PlantUML
```bash
# Instalar PlantUML
npm install -g plantuml

# Generar PNG
plantuml route_selected_screen_usecase.puml

# Ver resultado
start route_selected_screen_usecase.png
```

---

## 📊 Matriz de Cobertura

| Elemento | UseCase | Sequence | Class | Activity | Component | Deployment |
|----------|---------|----------|-------|----------|-----------|------------|
| UC-01 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-02 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-03 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-04 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-05 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-06 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-07 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-08 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-09 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| UC-10 | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 📚 Referencias Cruzadas

- [route_selected_screen.php](pages/route_selected_screen.php)
- [routes_api.php](api/routes_api.php)
- [favorites_api.php](api/favorites_api.php)
- [main.js](assets/js/main.js)
- [style.css](assets/css/style.css)

---

## 💡 Tips de Uso

1. **Estudiar en orden**:
   - Primero: Casos de Uso
   - Luego: Secuencia
   - Después: Clases
   - Finalmente: Actividades, Componentes, Despliegue

2. **Para desarrollo**:
   - Usar Diagrama de Clases para estructura BD
   - Usar Diagrama de Secuencia para debugging
   - Usar Diagrama de Componentes para arquitectura

3. **Para documentación**:
   - Exportar como PNG/SVG
   - Incluir en README
   - Presentar a stakeholders

---

**Última actualización**: Enero 2026  
**Total de diagramas**: 6  
**Estado**: ✅ Completo
