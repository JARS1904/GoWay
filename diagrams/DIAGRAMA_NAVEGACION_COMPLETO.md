# 📊 Diagrama de Navegación del Sistema GoWay

## 📋 Descripción General

Este diagrama representa el flujo completo de navegación del sistema GoWay, mostrando:
- **Sección de Usuario** (rol=2): Búsqueda y selección de rutas y horarios
- **Sección de Administrador** (rol=1): Gestión completa de recursos del sistema

---

## 🔑 Componentes Principales

### 1️⃣ **AUTENTICACIÓN** 🔐
```
Login → Validar Credenciales → Seleccionar Rol → Redirigir según permiso
```
- Sistema de sesión PHP
- Validación de credenciales en `config/login_registro.php`
- Redirección según rol de usuario

---

## 👤 SECCIÓN USUARIO (rol=2)

### 🗺️ **Route Selected Screen** (Pantalla Principal)
La pantalla más importante para el usuario. Permite buscar y seleccionar rutas.

#### Flujo de Interacción:
```
1. Seleccionar Origen
   └─ Dropdown con ubicaciones disponibles
   
2. Seleccionar Destino
   └─ Dropdown con ubicaciones disponibles
   
3. Buscar Rutas
   └─ GET /api/routes_api.php
   └─ Parámetros: origen, destino
   
4. Mostrar Resultados
   └─ Renderizar tarjetas de rutas
   └─ Mostrar primera ruta por defecto
   
5. Seleccionar Ruta
   └─ Click en tarjeta de ruta
   
6. Ver Detalles
   └─ Horarios
   └─ Paradas
   └─ Tarifas
   └─ Información general
   
7. Marcar como Favorita (Opcional)
   └─ POST /api/favorites_api.php
   └─ Acción: add_favorite / remove_favorite
   └─ Ícono cambia (♡ → ♥)
```

#### Archivos Asociados:
```
Frontend:
├─ pages/route_selected_screen.php      (Pantalla principal)
├─ assets/js/main.js                    (Lógica de búsqueda)
├─ assets/js/card-pagination.js         (Paginación de tarjetas)
└─ assets/css/style.css                 (Estilos)

Backend:
├─ api/routes_api.php                   (API GET de rutas)
├─ api/favorites_routes_api.php         (API POST de favoritas)
└─ config/conexion_bd.php               (Conexión a BD)
```

### 📊 **Reportes de Usuario**
- Ver historial de viajes
- Estadísticas personales

### ⭐ **Mis Favoritas**
- Rutas guardadas por el usuario
- Acceso rápido a rutas frecuentes

---

## ⚙️ SECCIÓN ADMINISTRADOR (rol=1)

### 🛣️ **Gestión de Rutas**
```
Listar Rutas → Crear / Editar / Eliminar Rutas
```
**Operaciones CRUD:**
- **C**reate: `controllers/insertar_ruta.php`
- **R**ead: `api/routes_api.php`
- **U**pdate: `controllers/actualizar_ruta.php`
- **D**elete: `controllers/eliminar_ruta.php`

### ⏰ **Gestión de Horarios**
```
Listar Horarios → Crear / Editar / Eliminar Horarios
```
**Operaciones CRUD:**
- **C**reate: `controllers/insert_horarios.php`
- **R**ead: `pages/horarios.php`
- **U**pdate: `pages/actualizar/actu_horariosSql.php`
- **D**elete: `controllers/delete/delete_horarios.php`

### 👨‍✈️ **Gestión de Conductores**
```
Listar Conductores → Crear / Editar / Eliminar Conductores
```
**Operaciones CRUD:**
- **C**reate: `controllers/insert_conductor.php`
- **R**ead: `pages/conductores.php`
- **U**pdate: `pages/actualizar/actu_conductoresSql.php`
- **D**elete: `controllers/delete/delete_conductores.php`

### 🚌 **Gestión de Vehículos**
```
Listar Vehículos → Crear / Editar / Eliminar Vehículos
```
**Operaciones CRUD:**
- **C**reate: `controllers/insert_vehiculos.php`
- **R**ead: `pages/vehiculos.php`
- **U**pdate: `pages/actualizar/actu_vehiculo.php`
- **D**elete: `controllers/delete/delete_vehiculo.php`

### 🏢 **Gestión de Empresas**
```
Listar Empresas → Crear / Editar / Eliminar Empresas
```
**Operaciones CRUD:**
- **C**reate: `controllers/insert_empresa.php`
- **R**ead: `pages/empresas.php`
- **U**pdate: `pages/actualizar/actu_empresasSql.php`
- **D**elete: `controllers/delete/delete_empresas.php`

### ✓ **Gestión de Checadores**
```
Listar Checadores → Crear / Editar / Eliminar Checadores
```
**Operaciones CRUD:**
- **C**reate: `controllers/insert_checador.php`
- **R**ead: `pages/checadores.php`
- **U**pdate: `pages/actualizar/actu_checadoresSql.php`
- **D**elete: `controllers/delete/delete_checadores.php`

### 👥 **Gestión de Usuarios**
```
Listar Usuarios → Crear / Editar / Eliminar Usuarios
```
**Operaciones CRUD:**
- **C**reate: `controllers/insert_user.php`
- **R**ead: `pages/usuarios.php`
- **U**pdate: `pages/actualizar/actu_usuariosSql.php`
- **D**elete: `controllers/delete/delete_usuarios.php`

### 📈 **Reportes Avanzados**
- Estadísticas de viajes
- Análisis de ocupación
- Informes de rendimiento

---

## 🔄 Flujo General del Sistema

```
┌─────────────────────────────────────────┐
│   VISITANTE SIN SESIÓN                  │
└──────────────┬──────────────────────────┘
               │
               ↓
        ┌──────────────┐
        │   LOGIN      │
        │ (Validar)    │
        └──────┬───────┘
               │
        ┌──────┴──────────┐
        │                 │
        ↓                 ↓
  ┌──────────────┐  ┌──────────────┐
  │   USUARIO    │  │ ADMINISTRADOR│
  │   (rol=2)    │  │   (rol=1)    │
  └──────┬───────┘  └──────┬───────┘
         │                 │
         ↓                 ↓
  Route Selected      Admin Panel
  Screen             (7 módulos CRUD)
         │                 │
         ├─ Búsqueda       ├─ Rutas
         ├─ Favoritas      ├─ Horarios
         └─ Reportes       ├─ Conductores
                          ├─ Vehículos
                          ├─ Empresas
                          ├─ Checadores
                          ├─ Usuarios
                          └─ Reportes
         
         │                 │
         └────────┬────────┘
                  │
                  ↓
         ┌──────────────┐
         │   LOGOUT     │
         │  (Cerrar)    │
         └──────┬───────┘
                │
                ↓
         Volver a LOGIN
```

---

## 🌐 Endpoints de API

### Rutas
- `GET /api/routes_api.php` - Buscar rutas por origen/destino
- `POST /api/routes_api.php` - Crear ruta
- `PUT /api/routes_api.php` - Actualizar ruta
- `DELETE /api/routes_api.php` - Eliminar ruta

### Favoritas
- `GET /api/favorites_routes_api.php` - Obtener rutas favoritas
- `POST /api/favorites_routes_api.php` - Añadir favorita (action=add_favorite)
- `POST /api/favorites_routes_api.php` - Remover favorita (action=remove_favorite)

### Usuarios
- `GET /api/usuarios.php` - Listar usuarios
- `POST /api/usuarios.php` - Crear usuario
- `PUT /api/usuarios.php` - Actualizar usuario
- `DELETE /api/usuarios.php` - Eliminar usuario

---

## 📂 Estructura de Carpetas Clave

```
GoWay/
├── api/                          (Endpoints API)
│   ├── routes_api.php
│   ├── favorites_routes_api.php
│   ├── usuarios.php
│   └── login.php
│
├── pages/                        (Vistas)
│   ├── route_selected_screen.php (★ Principal Usuario)
│   ├── login.php
│   ├── horarios.php
│   ├── conductores.php
│   ├── vehiculos.php
│   ├── empresas.php
│   ├── checadores.php
│   ├── usuarios.php
│   ├── reportes.php
│   └── actualizar/               (Formularios de edición)
│
├── controllers/                  (Lógica de negocio)
│   ├── insert_*.php
│   ├── actualizar_ruta.php
│   ├── delete/                   (Eliminaciones)
│   └── update/                   (Actualizaciones)
│
├── assets/
│   ├── js/                       (JavaScript)
│   │   ├── main.js               (Lógica principal)
│   │   ├── card-pagination.js
│   │   ├── delete/               (Funciones delete AJAX)
│   │   └── update/               (Funciones update AJAX)
│   │
│   └── css/                      (Estilos)
│       └── style.css
│
└── config/
    ├── conexion_bd.php           (Conexión a DB)
    ├── login_registro.php        (Lógica de autenticación)
    └── validar_login.php         (Validación de sesión)
```

---

## 🎯 Características Especiales

### Route Selected Screen
- **Búsqueda Dinámica**: GET a API con parámetros origen/destino
- **Paginación**: Soporte para múltiples rutas
- **Favoritas**: Sistema de marcado con corazón (♡/♥)
- **Detalles Expandibles**: Ver horarios, paradas y tarifas
- **Responsive**: Optimizado para móvil

### Admin Panel
- **7 Módulos CRUD Independientes**
- **Consistencia**: Mismo patrón de operaciones
- **Validación Backend**: Todas las operaciones validadas
- **Seguridad**: Control de rol en cada página

---

## 🔐 Seguridad

- Sesión PHP requerida en todas las páginas
- Validación de rol (1 = Admin, 2 = Usuario)
- Redireccionamiento a login si no hay sesión
- Validación de datos en backend
- Protección CSRF (implementado en forms)

---

## 📌 Notas Importantes

1. **Route Selected Screen** es la pantalla más importante para usuarios
2. **Admin Panel** tiene 7 secciones de gestión (CRUD)
3. **Logout** está disponible en todas las pantallas
4. **API** es el puente entre frontend y base de datos
5. **Controllers** contienen la lógica de negocio

---

## 📞 Contacto / Mantenimiento

Para actualizar este diagrama:
1. Editar archivo: `navigation_diagram.puml`
2. Generar con PlantUML
3. Actualizar este documento si hay cambios

**Última actualización:** Enero 2026
