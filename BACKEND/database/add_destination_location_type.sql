-- Ejecuta esto si tu base aún tiene locations.type como ENUM.
-- Lo deja flexible para "destination" y futuros tipos.
ALTER TABLE locations
  MODIFY type VARCHAR(50) NOT NULL;

-- Permite usar items como videos dentro de destinos/locaciones.
ALTER TABLE location_items
  MODIFY type VARCHAR(50) DEFAULT 'other';
