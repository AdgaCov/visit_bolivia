-- ============================================
-- ESTRUCTURA ESCALABLE PARA LÍMITES POR ROL
-- ============================================

-- Opción 1: Tabla de límites por rol (MÁS ESCALABLE)
-- Permite agregar nuevos tipos de límites sin modificar estructura

CREATE TABLE `role_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `limit_type` varchar(50) NOT NULL COMMENT 'Tipo de límite: locations, images_per_location, reviews, etc.',
  `limit_value` int(11) NOT NULL DEFAULT 0 COMMENT 'Valor del límite',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_limit_type` (`role_id`, `limit_type`),
  KEY `fk_role_limits_role` (`role_id`),
  CONSTRAINT `fk_role_limits_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- Insertar límites por defecto para cada rol
INSERT INTO `role_limits` (`role_id`, `limit_type`, `limit_value`, `description`) VALUES
-- Super Admin: Sin límites
(1, 'max_locations', 9999, 'Máximo de locations que puede crear'),
(1, 'max_images_per_location', 20, 'Máximo de imágenes por location'),
(1, 'max_reviews_per_day', 9999, 'Máximo de reviews por día'),

-- Admin: Límites moderados
(2, 'max_locations', 100, 'Máximo de locations que puede crear'),
(2, 'max_images_per_location', 10, 'Máximo de imágenes por location'),
(2, 'max_reviews_per_day', 50, 'Máximo de reviews por día'),

-- Editor (Negocio): Límites básicos
(3, 'max_locations', 10, 'Máximo de locations que puede crear'),
(3, 'max_images_per_location', 5, 'Máximo de imágenes por location'),
(3, 'max_reviews_per_day', 10, 'Máximo de reviews por día'),

-- User: Muy limitado
(4, 'max_locations', 3, 'Máximo de locations que puede crear'),
(4, 'max_images_per_location', 2, 'Máximo de imágenes por location'),
(4, 'max_reviews_per_day', 5, 'Máximo de reviews por día');

-- ============================================
-- OPCIÓN 2: Tabla de planes/suscripciones (AÚN MÁS FLEXIBLE)
-- Permite que usuarios tengan planes diferentes incluso con el mismo rol
-- ============================================

CREATE TABLE `plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Nombre del plan: Free, Basic, Premium, Enterprise',
  `slug` varchar(50) NOT NULL COMMENT 'Identificador único del plan',
  `price` decimal(10,2) DEFAULT 0.00 COMMENT 'Precio mensual (si aplica)',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_plan_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- Límites por plan
CREATE TABLE `plan_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `limit_type` varchar(50) NOT NULL,
  `limit_value` int(11) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_plan_limit_type` (`plan_id`, `limit_type`),
  KEY `fk_plan_limits_plan` (`plan_id`),
  CONSTRAINT `fk_plan_limits_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;

-- Relacionar usuarios con planes (opcional, si quieres planes independientes del rol)
ALTER TABLE `users` 
ADD COLUMN `plan_id` int(11) DEFAULT NULL COMMENT 'Plan del usuario (opcional)',
ADD KEY `fk_user_plan` (`plan_id`),
ADD CONSTRAINT `fk_user_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL;

-- ============================================
-- RECOMENDACIÓN: Usar OPCIÓN 1 (role_limits)
-- Es más simple y escalable para tu caso actual
-- ============================================

-- Vista útil para obtener límites de un usuario fácilmente
CREATE OR REPLACE VIEW `v_user_limits` AS
SELECT 
    u.id AS user_id,
    u.role_id,
    r.name AS role_name,
    rl.limit_type,
    rl.limit_value,
    rl.description
FROM users u
INNER JOIN roles r ON u.role_id = r.id
LEFT JOIN role_limits rl ON r.id = rl.role_id;

-- ============================================
-- FUNCIONES ÚTILES PARA CONSULTAR LÍMITES
-- ============================================

-- Función para obtener el límite de un usuario por tipo
DELIMITER //
CREATE FUNCTION `get_user_limit`(p_user_id INT, p_limit_type VARCHAR(50))
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE v_limit INT DEFAULT 0;
    
    SELECT rl.limit_value INTO v_limit
    FROM users u
    INNER JOIN role_limits rl ON u.role_id = rl.role_id
    WHERE u.id = p_user_id 
      AND rl.limit_type = p_limit_type
    LIMIT 1;
    
    RETURN IFNULL(v_limit, 0);
END//
DELIMITER ;

-- ============================================
-- EJEMPLOS DE USO
-- ============================================

-- Consultar límites de un usuario
-- SELECT * FROM v_user_limits WHERE user_id = 1;

-- Obtener límite específico
-- SELECT get_user_limit(1, 'max_locations') AS max_locations;

-- Agregar nuevo tipo de límite (sin modificar estructura)
-- INSERT INTO role_limits (role_id, limit_type, limit_value, description) 
-- VALUES (3, 'max_articles', 5, 'Máximo de artículos que puede crear');

-- Actualizar límite existente
-- UPDATE role_limits 
-- SET limit_value = 20 
-- WHERE role_id = 3 AND limit_type = 'max_locations';

