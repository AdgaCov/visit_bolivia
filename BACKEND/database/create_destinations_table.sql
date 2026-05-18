-- Destinos turisticos
-- Ejecuta este script en la base de datos antes de crear/editar destinos
-- desde el panel de administracion.

ALTER TABLE locations
  MODIFY type VARCHAR(50) NOT NULL;

ALTER TABLE locations
  ADD COLUMN IF NOT EXISTS destination_route_type VARCHAR(80) NULL AFTER type;

ALTER TABLE location_items
  MODIFY type VARCHAR(50) DEFAULT 'other';

CREATE TABLE IF NOT EXISTS destinations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  location_id INT NULL,
  image_url VARCHAR(500) NULL,
  name VARCHAR(255) NOT NULL,
  destination_route_type ENUM(
    'salar_laguna_colorada',
    'la_paz_tiwanaku_titicaca',
    'tarija_vino_cintis',
    'santa_cruz_misiones_samaipata',
    'rurrenabaque_madidi_pampas',
    'cochabamba_potosi'
  ) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_destinations_route_type (destination_route_type),
  INDEX idx_destinations_location_id (location_id),
  CONSTRAINT fk_destinations_location
    FOREIGN KEY (location_id) REFERENCES locations(id)
    ON DELETE SET NULL
);
