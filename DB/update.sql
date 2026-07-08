USE sistema_contable;

-- =========================================================
-- 1. Tabla de bitácora
-- Registra cada acción relevante de cada usuario dentro del sistema
-- (login, login fallido, crear, actualizar, ocultar, cerrar informe, etc.)
-- =========================================================
CREATE TABLE bitacora (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,               -- NULL permitido: ej. intento de login con correo inexistente
  accion VARCHAR(50) NOT NULL,
  tabla_afectada VARCHAR(50) NULL,
  registro_id INT NULL,
  detalle TEXT NULL,
  ip_address VARCHAR(45) NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- =========================================================
-- 2. Auditoría de actualizaciones en usuarios
-- (el soft-delete de usuarios YA existe: estado_actividad)
-- =========================================================
ALTER TABLE usuarios
  ADD COLUMN actualizado_por INT NULL AFTER estado_actividad,
  ADD COLUMN fecha_actualizacion DATETIME NULL AFTER actualizado_por,
  ADD CONSTRAINT fk_usuarios_actualizado_por
    FOREIGN KEY (actualizado_por) REFERENCES usuarios(id);

-- =========================================================
-- 3. Clave pública por usuario, para verificar la firma digital
-- que se guarda en "cierres.firma" al cerrar un informe
-- =========================================================
ALTER TABLE usuarios
  ADD COLUMN clave_publica TEXT NULL AFTER contrasena;

-- =========================================================
-- 4. Auditoría en cuentas (ya tenía "activo", solo faltaba quién y cuándo)
-- =========================================================
ALTER TABLE cuentas
  ADD COLUMN actualizado_por INT NULL AFTER activo,
  ADD COLUMN fecha_actualizacion DATETIME NULL AFTER actualizado_por,
  ADD CONSTRAINT fk_cuentas_actualizado_por
    FOREIGN KEY (actualizado_por) REFERENCES usuarios(id);

-- =========================================================
-- 5. Soft-delete + auditoría en diario (no existía "activo" aquí)
-- diario_detalle NO necesita su propio "activo": al ocultar el
-- diario (cabecera), el detalle se oculta al hacer el JOIN.
-- =========================================================
ALTER TABLE diario
  ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER estado,
  ADD COLUMN actualizado_por INT NULL AFTER activo,
  ADD COLUMN fecha_actualizacion DATETIME NULL AFTER actualizado_por,
  ADD CONSTRAINT fk_diario_actualizado_por
    FOREIGN KEY (actualizado_por) REFERENCES usuarios(id);

-- Nota: "cierres" no necesita soft-delete ni "actualizado_por".
-- Un cierre firmado no se debe editar ni ocultar nunca; si se necesita
-- anular, se hace con un nuevo registro de estado ('anulado') que
-- referencia al cierre original, nunca modificando el existente.