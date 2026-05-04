# Database Schema

## Table: `asignaciones`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id_asignacion | int(11) | NO | PRI | NULL | auto_increment |
| rfc_empresa | varchar(13) | NO | MUL | NULL |  |
| id_vehiculo | int(11) | NO | MUL | NULL |  |
| rfc_conductor | varchar(13) | NO | MUL | NULL |  |
| id_ruta | int(11) | NO | MUL | NULL |  |
| id_horario | int(11) | NO | MUL | NULL |  |
| asientos_disp | int(11) | NO |  | 0 |  |
| fecha | date | NO |  | NULL |  |
| activa | tinyint(1) | YES |  | 1 |  |
| estado | enum('programado','en_ruta','completado','cancelado','retrasado') | NO |  | programado |  |
| created_at | timestamp | NO |  | current_timestamp() |  |

## Table: `checadores`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| rfc_checador | varchar(13) | NO | PRI | NULL |  |
| rfc_empresa | varchar(13) | NO | MUL | NULL |  |
| nombre | varchar(100) | NO |  | NULL |  |
| usuario | varchar(50) | NO | UNI | NULL |  |
| contrasena | varchar(255) | NO |  | NULL |  |
| activo | tinyint(1) | YES |  | 1 |  |
| created_at | timestamp | NO |  | current_timestamp() |  |
| foto | varchar(255) | YES |  | NULL |  |

## Table: `conductores`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| rfc_conductor | varchar(13) | NO | PRI | NULL |  |
| rfc_empresa | varchar(13) | NO | MUL | NULL |  |
| nombre | varchar(100) | NO |  | NULL |  |
| licencia | varchar(20) | YES |  | NULL |  |
| telefono | varchar(20) | YES |  | NULL |  |
| activo | tinyint(1) | YES |  | 1 |  |
| created_at | timestamp | NO |  | current_timestamp() |  |
| foto | varchar(255) | YES |  | NULL |  |

## Table: `empresas`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| rfc_empresa | varchar(13) | NO | PRI | NULL |  |
| nombre | varchar(100) | NO |  | NULL |  |
| direccion | text | YES |  | NULL |  |
| telefono | varchar(20) | YES |  | NULL |  |
| email | varchar(100) | YES |  | NULL |  |
| activo | tinyint(1) | YES |  | 1 |  |
| created_at | timestamp | NO |  | current_timestamp() |  |

## Table: `horarios`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id_horario | int(11) | NO | PRI | NULL | auto_increment |
| id_ruta | int(11) | NO | MUL | NULL |  |
| tipo_dia | varchar(30) | NO |  | NULL |  |
| hora_salida | time | NO |  | NULL |  |
| hora_llegada | time | NO |  | NULL |  |
| frecuencia | varchar(50) | YES |  | NULL |  |
| created_at | timestamp | NO |  | current_timestamp() |  |

## Table: `notificaciones`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id_notificacion | int(11) | NO | PRI | NULL | auto_increment |
| id_usuario | int(11) | YES | MUL | NULL |  |
| titulo | varchar(255) | NO |  | NULL |  |
| mensaje | text | NO |  | NULL |  |
| tipo | varchar(50) | YES |  | general |  |
| leido | tinyint(1) | YES |  | 0 |  |
| fecha_creacion | timestamp | NO |  | current_timestamp() |  |

## Table: `paradas_ruta`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id_parada | int(11) | NO | PRI | NULL | auto_increment |
| id_ruta | int(11) | NO | MUL | NULL |  |
| nombre | varchar(255) | NO |  | NULL |  |
| orden | int(11) | NO |  | 0 |  |
| minutos_desde_origen | int(11) | NO |  | 0 |  |

## Table: `reportes`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id | int(11) | NO | PRI | NULL | auto_increment |
| id_vehiculo | int(11) | NO | MUL | NULL |  |
| rfc_conductor | varchar(13) | NO | MUL | NULL |  |
| id_ruta | int(11) | NO | MUL | NULL |  |
| tipo_incidente | varchar(50) | NO |  | NULL |  |
| fecha_incidente | datetime | NO |  | NULL |  |
| descripcion | text | NO |  | NULL |  |
| gravedad | varchar(20) | NO |  | NULL |  |
| estado | varchar(20) | YES |  | pendiente |  |
| id_usuario | int(11) | YES | MUL | NULL |  |
| rfc_checador | varchar(13) | YES | MUL | NULL |  |
| created_at | timestamp | NO |  | current_timestamp() |  |
| archivado | tinyint(1) | YES |  | 0 |  |

## Table: `rutas`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id_ruta | int(11) | NO | PRI | NULL | auto_increment |
| rfc_empresa | varchar(13) | NO | MUL | NULL |  |
| nombre | varchar(100) | NO |  | NULL |  |
| origen | varchar(100) | NO |  | NULL |  |
| destino | varchar(100) | NO |  | NULL |  |
| paradas | text | YES |  | NULL |  |
| activa | tinyint(1) | YES |  | 1 |  |
| id_ruta_retorno | int(11) | YES | MUL | NULL |  |
| created_at | timestamp | NO |  | current_timestamp() |  |

## Table: `rutas_favoritas`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id_favorita | int(11) | NO | PRI | NULL | auto_increment |
| id_usuario | int(11) | NO | MUL | NULL |  |
| id_ruta | int(11) | NO | MUL | NULL |  |
| fecha_agregada | timestamp | NO |  | current_timestamp() |  |

## Table: `usuarios`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id | int(11) | NO | PRI | NULL | auto_increment |
| nombre | varchar(50) | NO |  | NULL |  |
| email | varchar(50) | NO |  | NULL |  |
| password | varchar(255) | NO |  | NULL |  |
| rol | varchar(50) | NO |  | NULL |  |
| foto | varchar(255) | YES |  | NULL |  |

## Table: `vehiculos`
| Field | Type | Null | Key | Default | Extra |
|-------|------|------|-----|---------|-------|
| id_vehiculo | int(11) | NO | PRI | NULL | auto_increment |
| rfc_empresa | varchar(13) | NO | MUL | NULL |  |
| placa | varchar(20) | NO |  | NULL |  |
| modelo | varchar(50) | YES |  | NULL |  |
| capacidad | int(11) | YES |  | NULL |  |
| activo | tinyint(1) | YES |  | 1 |  |
| created_at | timestamp | NO |  | current_timestamp() |  |

