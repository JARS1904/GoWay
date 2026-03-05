## DIAGRAMA DE CASOS DE USO - RUTA SELECCIONADA (UML FORMAL)

```
╔════════════════════════════════════════════════════════════════╗
║           DIAGRAMA DE CASOS DE USO                             ║
║      PANTALLA: ROUTE_SELECTED_SCREEN (Ruta Seleccionada)     ║
╚════════════════════════════════════════════════════════════════╝


                        ┏━━━━━━━━━━━━━━━━━━━━━━┓
                        ┃                      ┃
                        ┃    USUARIO ROL=2     ┃
                        ┃   (Conductor/Pasaj.) ┃
                        ┃                      ┃
                        ┗━━━━━━━━━━━━━━━━━━━━━━┛
                                  │
                  ┌───────────────┼───────────────┐
                  │               │               │
                  │               │               │
        ┌─────────┴──────┐  ┌─────┴──────┐  ┌────┴──────────┐
        │                │  │            │  │               │
        │                │  │            │  │               │
    ◇───┤   CU-01        ├──┤   CU-02    ├──┤   CU-03      │
    │   │  BUSCAR        │  │   MARCAR   │  │  VER          │
    │   │  RUTAS         │  │  FAVORITAS │  │  DETALLES     │
    │   │                │  │            │  │               │
    │   └────────────────┘  └────────────┘  └───────────────┘
    │
    │   ┌──────────────────────────────────────────────┐
    │   │    SISTEMA GOWAY - ROUTE SELECTED SCREEN     │
    │   │                                              │
    │   │  Actores:                                   │
    │   │  - Usuario autenticado (rol=2)             │
    │   │  - Sistema (validación de sesión)          │
    │   │  - API Backend (consultas de rutas)        │
    │   │  - BD (almacenamiento)                     │
    │   │                                              │
    │   └──────────────────────────────────────────────┘
    │
    └─► CASOS DE USO DETALLADOS:


┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-01: BUSCAR RUTAS POR ORIGEN Y DESTINO                 ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Usuario                                 ┃
┃ Precondiciones:                                          ┃
┃   - Usuario debe estar autenticado (rol=2)              ┃
┃   - Página debe estar cargada                            ┃
┃   - Ubicaciones deben estar disponibles en dropdown     ┃
┃ Flujo Básico:                                           ┃
┃   1. Usuario selecciona ubicación de ORIGEN             ┃
┃   2. Usuario selecciona ubicación de DESTINO            ┃
┃   3. Sistema habilita botón "Buscar" (disabled=false)   ┃
┃   4. Usuario hace clic en botón "Buscar"                ┃
┃   5. Sistema realiza GET a /api/routes_api.php          ┃
┃   6. API retorna lista de rutas disponibles             ┃
┃   7. Sistema limpia selección anterior                  ┃
┃   8. Sistema renderiza tarjetas de rutas                ┃
┃   9. Usuario puede seleccionar una ruta                 ┃
┃ Postcondiciones:                                        ┃
┃   - Lista de rutas actualizada                          ┃
┃   - Primera ruta seleccionada por defecto               ┃
┃   - Detalles mostrados en panel derecho                 ┃
┃ Flujos Alternativos:                                    ┃
┃   A1: Si no hay rutas disponibles:                      ┃
┃       - Mostrar mensaje "No hay rutas disponibles"     ┃
┃   A2: Si hay error en API:                              ┃
┃       - Mostrar toast de error                          ┃
┃       - Mantener lista anterior si existía              ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-02: MARCAR RUTA COMO FAVORITA                         ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Usuario                                 ┃
┃ Precondiciones:                                          ┃
┃   - Usuario debe tener una ruta visible                  ┃
┃   - Usuario debe estar autenticado                       ┃
┃ Flujo Básico:                                           ┃
┃   1. Usuario ve icono de corazón vacío (♡) en ruta      ┃
┃   2. Usuario hace clic en icono de corazón              ┃
┃   3. Sistema realiza POST a /api/favorites_api.php      ┃
┃      - Parámetros: action=add_favorite, id_ruta, ...   ┃
┃   4. API almacena ruta en tabla de favoritas            ┃
┃   5. API retorna success                                ┃
┃   6. Sistema actualiza icono a corazón lleno (♥)        ┃
┃   7. Sistema cambia color a rojo (#D32F2F)              ┃
┃ Postcondiciones:                                        ┃
┃   - Ruta guardada en base de datos                       ┃
┃   - Icono actualizado visualmente                       ┃
┃   - Ruta aparecerá en filtro "Mis Favoritas"            ┃
┃ Flujos Alternativos:                                    ┃
┃   A1: Si ya es favorita:                                ┃
┃       - Sistema permite remover (ver CU-03)             ┃
┃   A2: Si hay error:                                     ┃
┃       - Toast de error rojo                             ┃
┃       - Icono mantiene estado anterior                  ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-03: REMOVER RUTA DE FAVORITAS                         ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Usuario                                 ┃
┃ Precondiciones:                                          ┃
┃   - Ruta debe estar marcada como favorita (♥)           ┃
┃   - Usuario debe estar autenticado                       ┃
┃ Flujo Básico:                                           ┃
┃   1. Usuario ve icono de corazón lleno (♥) en ruta      ┃
┃   2. Usuario hace clic en icono de corazón              ┃
┃   3. Sistema realiza DELETE a /api/favorites_api.php   ┃
┃      - Parámetros: action=delete_favorite, id_ruta      ┃
┃   4. API elimina ruta de tabla de favoritas             ┃
┃   5. API retorna success                                ┃
┃   6. Sistema actualiza icono a corazón vacío (♡)        ┃
┃   7. Sistema cambia color a gris                        ┃
┃ Postcondiciones:                                        ┃
┃   - Ruta removida de base de datos                       ┃
┃   - Icono actualizado                                   ┃
┃   - Ruta desaparece de "Mis Favoritas" si está filtrado │
┃ Flujos Alternativos:                                    ┃
┃   A1: Si no es favorita:                                ┃
┃       - Sistema permite agregar (ver CU-02)             ┃
┃   A2: Si hay error:                                     ┃
┃       - Toast de error                                  ┃
┃       - Icono mantiene estado anterior                  ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-04: VER DETALLES DE RUTA                              ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Usuario                                 ┃
┃ Precondiciones:                                          ┃
┃   - Ruta debe estar en lista visible                     ┃
┃ Flujo Básico:                                           ┃
┃   1. Usuario hace clic en tarjeta de ruta                ┃
┃   2. Sistema marca ruta como seleccionada (border azul)  ┃
┃   3. Sistema cambia fondo a azul claro (#f0f7ff)        ┃
┃   4. Sistema muestra en panel derecho:                  ┃
┃      - Trayecto: ORIGEN ----► DESTINO                   ┃
┃      - Empresa:                                          ┃
┃        * Nombre de empresa                               ┃
┃        * Teléfono                                        ┃
┃        * Email                                           ┃
┃      - Horarios:                                         ┃
┃        * Día de operación (Lunes, Martes, etc.)         ┃
┃        * Hora de salida                                 ┃
┃        * Hora de llegada                                ┃
┃        * Paradas intermedias (lista)                    ┃
┃      - Conductor:                                        ┃
┃        * Nombre del conductor                            ┃
┃      - Vehículo:                                         ┃
┃        * Placa/Identificación                            ┃
┃        * Tipo de vehículo                                ┃
┃        * Capacidad                                       ┃
┃ Postcondiciones:                                        ┃
┃   - Panel derecho actualizado                            ┃
┃   - Ruta seleccionada visualmente destacada              ┃
┃ Flujos Alternativos:                                    ┃
┃   A1: Si hay muchas paradas:                             ┃
┃       - Mostrar lista scrollable                         ┃
┃   A2: Si panel derecho estaba vacío:                    ┃
┃       - Mostrar datos cargando con animación             ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-05: FILTRAR POR FAVORITAS                             ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Usuario                                 ┃
┃ Precondiciones:                                          ┃
┃   - Usuario debe tener al menos una ruta favorita        ┃
┃ Flujo Básico:                                           ┃
┃   1. Usuario hace clic en botón "Mis Favoritas"          ┃
┃   2. Sistema activa filtro (cambio visual del botón)     ┃
┃   3. Sistema realiza GET a /api/favorites_api.php       ┃
┃   4. API retorna lista de IDs de rutas favoritas        ┃
┃   5. Sistema filtra la lista de rutas actual             ┃
┃   6. Sistema muestra solo rutas favoritas                ┃
┃   7. Todos los otros CU funcionan normalmente            ┃
┃ Postcondiciones:                                        ┃
┃   - Vista filtrada activa                               ┃
┃   - Botón "Ver Todas" disponible para limpiar filtro    ┃
┃ Flujos Alternativos:                                    ┃
┃   A1: Si no hay favoritas:                               ┃
┃       - Mostrar mensaje "No hay rutas favoritas"        ┃
┃       - Panel derecho muestra "Sin selección"            ┃
┃   A2: Si el filtro ya estaba activo:                    ┃
┃       - Recargar favoritas de API                       ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-06: VER TODAS LAS RUTAS (LIMPIAR FILTRO)             ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Usuario                                 ┃
┃ Precondiciones:                                          ┃
┃   - Debe haber un filtro activo (estado: "favs")        ┃
┃ Flujo Básico:                                           ┃
┃   1. Usuario hace clic en botón "Ver Todas"              ┃
┃   2. Sistema desactiva filtro                            ┃
┃   3. Sistema cambia estado a "all"                       ┃
┃   4. Sistema recarga todas las rutas                     ┃
┃   5. Sistema muestra lista completa                      ┃
┃ Postcondiciones:                                        ┃
┃   - Filtro desactivado                                   ┃
┃   - Lista completa visible                               ┃
┃ Flujos Alternativos:                                    ┃
┃   A1: Si no hay rutas buscadas:                         ┃
┃       - Mostrar panel derecho vacío                      ┃
┃       - Mostrar "Realiza una búsqueda primero"           ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-07: ACCEDER A MENÚ DE USUARIO                         ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Usuario                                 ┃
┃ Precondiciones:                                          ┃
┃   - Usuario debe estar autenticado                       ┃
┃ Flujo Básico:                                           ┃
┃   1. Usuario hace clic en icono de perfil (header)       ┃
┃   2. Sistema despliega menú dropdown                     ┃
┃   3. Menú muestra opciones:                              ┃
┃      - "Ver sitio principal" (goway.netlify.app)        ┃
┃      - (Otras opciones futuras)                          ┃
┃   4. Usuario puede hacer clic en opción                  ┃
┃ Postcondiciones:                                        ┃
┃   - Menú visible                                         ┃
┃   - Navegación según opción seleccionada                ┃
┃ Flujos Alternativos:                                    ┃
┃   A1: Si usuario hace clic fuera del menú:              ┃
┃       - Menú se cierra automáticamente                   ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ CU-08: VALIDAR SESIÓN (Caso de Uso del Sistema)         ┃
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Actor Principal: Sistema                                 ┃
┃ Precondiciones:                                          ┃
┃   - Petición HTTP a route_selected_screen.php            ┃
┃ Flujo Básico:                                           ┃
┃   1. Sistema verifica: isset($_SESSION['id'])           ┃
┃   2. Sistema verifica: $_SESSION['rol'] == 2             ┃
┃   3. Si verificaciones OK:                              ┃
┃      - Cargar página normalmente                        ┃
┃   4. Si verificaciones FALLAN:                          ┃
┃      - header("Location: login.php")                    ┃
┃      - exit()                                            ┃
┃ Postcondiciones:                                        ┃
┃   - Página cargada (caso OK)                             ┃
┃   - Redireccionado a login (caso FALLO)                 ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛


═══════════════════════════════════════════════════════════════════

                    MATRIZ DE TRAZABILIDAD

  CU-01  CU-02  CU-03  CU-04  CU-05  CU-06  CU-07  CU-08
   │      │      │      │      │      │      │      │
   ├──────┼──────┼──────┼──────┼──────┼──────┼──────┤
   ▼      ▼      ▼      ▼      ▼      ▼      ▼      ▼
 BUSCAR MARCAR REMOVER VER   FILTRO VER   USUARIO VALIDAR
 RUTAS  FAV.   FAV.   DETAL. FAV.   TODAS  MENU   SESION
   │      │      │      │      │      │      │      │
   └──────┴──────┴──────┴──────┴──────┴──────┴──────┘
          │              │              │
          └──────────────┼──────────────┘
                         ▼
              ROUTE_SELECTED_SCREEN
                  (PHP + HTML + CSS + JS)
                         │
          ┌──────────────┼──────────────┐
          ▼              ▼              ▼
      API BACKEND     USUARIO BD      FAVORITAS BD
      (routes_api)    (login)          (favoritas)


═══════════════════════════════════════════════════════════════════

                    REQUERIMIENTOS NO FUNCIONALES

┌─────────────────────────────────────────────────────────┐
│ RNF-01: RENDIMIENTO                                      │
│ - Tiempo de carga inicial: < 2 segundos                 │
│ - Búsqueda de rutas: < 1 segundo                        │
│ - Actualización de favoritas: respuesta inmediata       │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ RNF-02: RESPONSIVIDAD                                    │
│ - Interfaz funcional en:                                │
│   * Escritorio (1920+ px)                              │
│   * Tablet (768-1024px)                                │
│   * Móvil (< 768px)                                    │
│ - Punto de quiebre principal: 1200px                    │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ RNF-03: SEGURIDAD                                        │
│ - Validación de sesión en servidor                      │
│ - Solo rol=2 puede acceder                              │
│ - Redireccionamiento automático si sesión inválida      │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ RNF-04: USABILIDAD                                       │
│ - Interfaz intuitiva                                    │
│ - Feedback visual en cada acción                        │
│ - Mensajes de error claros                              │
│ - Animaciones suaves (0.3s)                             │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ RNF-05: COMPATIBILIDAD                                   │
│ - Navegadores modernos (Chrome, Firefox, Safari, Edge)  │
│ - ES6+ JavaScript                                       │
│ - CSS3 con prefijos de vendedor donde sea necesario     │
└─────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════════
```

## NOTAS IMPORTANTES

- **Rol requerido**: Solo usuarios con `rol=2` pueden acceder (Conductores/Pasajeros)
- **Variables de sesión**: `$_SESSION['id']` y `$_SESSION['rol']`
- **API Base**: `/api/` con los endpoints específicos
- **Favoritas**: Almacenadas en tabla `favoritas` asociada al usuario
- **Responsividad**: Layout de dos columnas que se apila en móviles
