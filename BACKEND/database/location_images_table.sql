-- Tabla para imágenes de ubicaciones unificadas
CREATE TABLE location_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    is_main BOOLEAN DEFAULT FALSE,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_location_images_location FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
);

-- Índices para mejorar el rendimiento
CREATE INDEX idx_location_images_location_id ON location_images(location_id);
CREATE INDEX idx_location_images_is_main ON location_images(is_main);
CREATE INDEX idx_location_images_uploaded_at ON location_images(uploaded_at);

