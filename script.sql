-- ====================================
-- FORMULARIO
-- ===================================
drop database formulario;
CREATE DATABASE formulario;

use formulario;

CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,

    parent_id INT NULL,
    tipo ENUM('PRINCIPAL','SUB') NOT NULL,

    nombre VARCHAR(100),
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    telefono CHAR(10),
    correo VARCHAR(150),
    area VARCHAR(150),
    cargo varchar(150),
    anos_laborando INT,
    grado_estudios VARCHAR(50),
    profesion VARCHAR(150),
    sexo VARCHAR(20),
    edad INT,
    descripcion_labores TEXT,
    estado int(1) DEFAULT 1 comment '1 es activo',

    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,

    FOREIGN KEY (parent_id) REFERENCES empleados(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE software (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    estado int(1) DEFAULT 1 comment '1 es activo',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE empleado_software (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT NOT NULL,
    software_id INT NOT NULL,
    nivel_uso ENUM(
		'No lo utilizo',
		'Sin conocimiento',
		'Basico',
		'Intermedio',
		'Avanzado'
    ) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,

    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE,
    FOREIGN KEY (software_id) REFERENCES software(id) ON DELETE CASCADE,
    UNIQUE (empleado_id, software_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE areas 
ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

CREATE TABLE roles(
	id INT auto_increment PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users(
	id INT auto_increment PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ap_paterno VARCHAR(100) NOT NULL,
    ap_materno VARCHAR(100) NOT NULL,
    telefono CHAR(10),
    cargo varchar(150),
    area_id int,
    rol_id int,
    estado TINYINT(1) DEFAULT 1 COMMENT '1=activo, 0=inactivo',
    
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    FOREIGN KEY (area_id) REFERENCES areas(id),
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    UNIQUE (nombre, ap_paterno, ap_materno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users
ADD COLUMN is_root TINYINT(1) NOT NULL DEFAULT 0;

CREATE TABLE accesos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado TINYINT(1) DEFAULT 1 COMMENT '1=activo, 0=bloqueado',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE accesos 
ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

CREATE TABLE permisos(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_permisos(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    permiso_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    UNIQUE (user_id, permiso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE roles_permisos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    permiso_id BIGINT UNSIGNED NOT NULL,
    
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,

    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    UNIQUE (rol_id, permiso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_parent_id ON empleados(parent_id);
CREATE INDEX idx_empleado_id ON empleado_software(empleado_id);
CREATE INDEX idx_area_id ON users(area_id);
CREATE INDEX idx_rol_id ON users(rol_id);
CREATE INDEX idx_user_permiso_user ON user_permisos(user_id);
CREATE INDEX idx_user_permiso_permiso ON user_permisos(permiso_id);

INSERT INTO roles_permisos (rol_id, permiso_id)
SELECT 1, id FROM permisos;

INSERT INTO roles (nombre) VALUES
('Super Administrador'),
('Administrador Dependencia'),
('Enlace'),
('Capturista'),
('Validador'),
('Consulta'),
('Externo');

INSERT INTO permisos (name, description) VALUES
-- Dashboard
('ver-dashboard', 'Acceso al panel principal'),

-- Usuarios
('usuarios-ver', 'Ver usuarios'),
('usuarios-crear', 'Crear usuarios'),
('usuarios-editar', 'Editar usuarios'),
('usuarios-eliminar', 'Eliminar usuarios'),

-- Roles y permisos (solo TI)
('roles-ver', 'Ver roles'),
('roles-crear', 'Crear roles'),
('roles-editar', 'Editar roles'),
('roles-asignar-permisos', 'Asignar permisos a roles'),

-- Áreas y dependencias
('areas-ver', 'Ver áreas'),
('areas-crear', 'Crear áreas'),
('areas-editar', 'Editar áreas'),

-- Registros del sistema (tu módulo principal)
('registros-ver', 'Ver registros'),
('registros-crear', 'Crear registros'),
('registros-editar', 'Editar registros'),
('registros-eliminar', 'Eliminar registros'),

-- Evidencias / archivos
('evidencias-subir', 'Subir evidencias'),
('evidencias-ver', 'Ver evidencias'),
('evidencias-eliminar', 'Eliminar evidencias'),

-- Reportes
('reportes-ver', 'Ver reportes'),
('exportar-excel', 'Exportar reportes en Excel'),
('generar-pdf', 'Generar reportes PDF'),

-- Estadísticas
('ver-estadisticas', 'Ver estadísticas y gráficos'),

-- Administración avanzada
('configuracion-sistema', 'Configurar parámetros del sistema'),
('admin-modulos', 'Acceso a módulos administrativos');

INSERT INTO user_permisos (user_id, permiso_id)
SELECT 1, id FROM permisos;


INSERT INTO software (nombre) VALUES
('WORD'),
('EXCEL'),
('POWERPOINT'),
('ACCESS'),
('ONEDRIVE'),
('TEAMS'),
('GOOGLE DOCS'),
('GOOGLE SHEETS'),
('GOOGLE DRIVE'),
('ADOBE PDF'),
('CANVA'),
('PHOTOSHOP'),
('AUTOCAD'),
('QGIS'),
('ARCGIS'),
('WORDPRESS'),
('GEMINI'),
('CHATGPT'),
('COPILOT');

INSERT INTO areas (nombre, activo) VALUES
('COMISARIA GENERAL DE SEGURIDAD CIUDADANA', 1),
('CONSEJERIA JURIDICA', 1),
('CONTRALORIA INTERNA MUNICIPAL', 1),
('COORDINACION MUNICIPAL DE PROTECCION CIVIL', 1),
('DEFENSORIA MUNICIPAL DE LOS DERECHOS HUMANOS', 1),
('SISTEMA MUNICIPAL PARA EL DESARROLLO INTEGRAL DE LA FAMILIA (SMDIF)', 1),
('DIRECCION DE ADMINISTRACION', 1),
('DIRECCION DE COMUNICACION SOCIAL', 1),
('DIRECCION DE CULTURA', 1),
('DIRECCION DE DESARROLLO URBANO', 1),
('DIRECCION DE EDUCACION', 1),
('DIRECCION DE FOMENTO ECONOMICO Y TURISMO', 1),
('DIRECCION DE GOBIERNO', 1),
('DIRECCION DE LAS MUJERES', 1),
('DIRECCION DE MEDIO AMBIENTE', 1),
('DIRECCION DE MOVILIDAD', 1),
('DIRECCION DE OBRAS PUBLICAS', 1),
('DIRECCION DE RELACIONES PUBLICAS', 1),
('DIRECCION DE SERVICIOS PUBLICOS', 1),
('DIRECCION DEL BIENESTAR', 1),
('IMCUFIDENE', 1),
('INSTITUTO MUNICIPAL DE LA JUVENTUD', 1),
('INSTITUTO MUNICIPAL DE PLANEACION', 1),
('ODAPAS', 1),
('OFICIALIA MAYOR MUNICIPAL', 1),
('SECRETARIA DEL H AYUNTAMIENTO', 1),
('SECRETARIA PARTICULAR DE LA PRESIDENCIA MUNICIPAL', 1),
('SECRETARIA TECNICA', 1),
('TESORERIA MUNICIPAL', 1),
('UNIDAD ADMINISTRATIVA NEZAHUALCOYOTL', 1),
('UNIDAD DE SIMPLIFICACION Y DIGITALIZACION DE NEZAHUALCOYOTL', 1),
('UNIDAD DE TRANSPARENCIA Y ACCESO A LA INFORMACION PUBLICA', 1);


CREATE TABLE paginas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    icono VARCHAR(100),
    es_global TINYINT(1) DEFAULT 0 COMMENT '1=visible en todas las areas',
    estado TINYINT(1) DEFAULT 1 COMMENT '1=activa, 0=inactiva',

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB;

ALTER TABLE paginas
ADD COLUMN ruta VARCHAR(255) NOT NULL AFTER slug;


CREATE TABLE area_paginas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_id INT NOT NULL,
    pagina_id INT NOT NULL,
    orden INT DEFAULT 0,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_area_paginas_area
        FOREIGN KEY (area_id) REFERENCES areas(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_area_paginas_pagina
        FOREIGN KEY (pagina_id) REFERENCES paginas(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_area_pagina UNIQUE (area_id, pagina_id)
) ENGINE=InnoDB;

CREATE TABLE usuario_paginas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    pagina_id INT NOT NULL,
    puede_ver TINYINT(1) DEFAULT 1 COMMENT '1=puede ver, 0=no puede',

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_paginas_usuario
        FOREIGN KEY (usuario_id) REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_usuario_paginas_pagina
        FOREIGN KEY (pagina_id) REFERENCES paginas(id)
        ON DELETE CASCADE,

    CONSTRAINT unique_usuario_pagina UNIQUE (usuario_id, pagina_id)
) ENGINE=InnoDB;


CREATE INDEX idx_area_paginas_area ON area_paginas(area_id);
CREATE INDEX idx_area_paginas_pagina ON area_paginas(pagina_id);

CREATE INDEX idx_usuario_paginas_usuario ON usuario_paginas(usuario_id);
CREATE INDEX idx_usuario_paginas_pagina ON usuario_paginas(pagina_id);