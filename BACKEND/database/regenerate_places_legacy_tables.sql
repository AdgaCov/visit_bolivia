-- Regenera las tablas legacy que usa App\Models\Place y /api/places.
-- Es idempotente: se puede ejecutar mas de una vez sin duplicar datos base.

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cities_department_id` (`department_id`),
  CONSTRAINT `fk_cities_department`
    FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_places_users_id` (`users_id`),
  KEY `idx_places_coords` (`latitude`, `longitude`),
  CONSTRAINT `fk_places_user`
    FOREIGN KEY (`users_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `place_city` (
  `place_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  PRIMARY KEY (`place_id`, `city_id`),
  KEY `idx_place_city_city_id` (`city_id`),
  CONSTRAINT `fk_place_city_place`
    FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_place_city_city`
    FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `place_subcategories` (
  `place_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  PRIMARY KEY (`place_id`, `subcategory_id`),
  KEY `idx_place_subcategories_subcategory_id` (`subcategory_id`),
  CONSTRAINT `fk_place_subcategories_place`
    FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_place_subcategories_subcategory`
    FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `images_places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_images_places_place_id` (`place_id`),
  UNIQUE KEY `uq_images_places_place_url` (`place_id`, `image_url`),
  CONSTRAINT `fk_images_places_place`
    FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `restaurants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(11,7) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(25) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_restaurants_place_id` (`place_id`),
  CONSTRAINT `fk_restaurants_place`
    FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `restaurant_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_restaurant_images_restaurant_id` (`restaurant_id`),
  UNIQUE KEY `uq_restaurant_images_restaurant_url` (`restaurant_id`, `image_url`),
  CONSTRAINT `fk_restaurant_images_restaurant`
    FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `accommodations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `room_type` varchar(100) DEFAULT NULL,
  `price_per_night` decimal(10,2) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(11,7) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(25) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_accommodations_place_id` (`place_id`),
  CONSTRAINT `fk_accommodations_place`
    FOREIGN KEY (`place_id`) REFERENCES `places` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

CREATE TABLE IF NOT EXISTS `accommodation_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_accommodation_images_accommodation_id` (`accommodation_id`),
  UNIQUE KEY `uq_accommodation_images_accommodation_url` (`accommodation_id`, `image_url`),
  CONSTRAINT `fk_accommodation_images_accommodation`
    FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Datos base desde el esquema actual `locations`.
INSERT IGNORE INTO `cities` (
  `id`, `department_id`, `name`, `latitude`, `longitude`, `description`, `created_at`, `updated_at`
)
SELECT
  `id`, `department_id`, `name`, `latitude`, `longitude`, `description`, `created_at`, `updated_at`
FROM `locations`
WHERE `type` = 'city';

INSERT IGNORE INTO `places` (
  `id`, `users_id`, `name`, `description`, `latitude`, `longitude`,
  `address`, `opening_hours`, `contact_info`, `created_at`, `updated_at`
)
SELECT
  `id`, `user_id`, `name`, `description`, `latitude`, `longitude`,
  `address`, `opening_hours`, `contact_info`, `created_at`, `updated_at`
FROM `locations`
WHERE `type` <> 'city';

INSERT IGNORE INTO `images_places` (`place_id`, `image_url`, `uploaded_at`)
SELECT `location_id`, `image_url`, `uploaded_at`
FROM `location_images`
WHERE `location_id` IN (SELECT `id` FROM `places`);

INSERT IGNORE INTO `place_subcategories` (`place_id`, `subcategory_id`)
SELECT `location_id`, `subcategory_id`
FROM `location_subcategories`
WHERE `location_id` IN (SELECT `id` FROM `places`);

INSERT IGNORE INTO `place_city` (`place_id`, `city_id`)
SELECT place_locations.`id`, city_locations.`id`
FROM `locations` AS place_locations
INNER JOIN `locations` AS city_locations
  ON city_locations.`type` = 'city'
  AND city_locations.`department_id` = place_locations.`department_id`
WHERE place_locations.`type` <> 'city'
  AND place_locations.`department_id` IS NOT NULL
  AND place_locations.`id` IN (SELECT `id` FROM `places`)
  AND city_locations.`id` IN (SELECT `id` FROM `cities`);
