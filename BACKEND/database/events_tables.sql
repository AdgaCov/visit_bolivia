-- =====================================================
-- Tablas para el sistema de Eventos - Conoce Bolivia
-- =====================================================

-- Tabla de eventos
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    place_id INT NOT NULL,                 -- ¡OBLIGATORIO! Garantiza ubicación geográfica
    name VARCHAR(200) NOT NULL,
    description TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NULL,
    event_type ENUM('festival', 'concert', 'exhibition', 'tour', 'workshop', 'other') DEFAULT 'other',
    price DECIMAL(10,2) NULL,
    is_recurring TINYINT DEFAULT 0,

    -- 📍 Coordenadas específicas del evento (opcional, pero recomendado)
    latitude DECIMAL(10,8) NULL,           -- Latitud exacta del evento
    longitude DECIMAL(11,8) NULL,          -- Longitud exacta del evento

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Clave foránea
    CONSTRAINT fk_events_place FOREIGN KEY (place_id) REFERENCES places(id) ON DELETE CASCADE,

    -- Índices
    INDEX idx_events_coords (latitude, longitude),
    INDEX idx_events_start_date (start_date),
    INDEX idx_events_type (event_type),
    INDEX idx_events_recurring (is_recurring),
    INDEX idx_events_place_id (place_id)
);

-- Tabla de imágenes de eventos
CREATE TABLE event_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) DEFAULT NULL,
    is_main TINYINT DEFAULT 0,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_event_images_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_images_event_id (event_id),
    INDEX idx_event_images_main (is_main)
);

-- =====================================================
-- Datos de ejemplo para testing
-- =====================================================

-- Insertar algunos eventos de ejemplo (asegúrate de que existan lugares con ID 1, 2, 3)
INSERT INTO events (place_id, name, description, start_date, end_date, event_type, price, is_recurring, latitude, longitude) VALUES
(1, 'Festival de Música Andina', 'Gran festival de música tradicional boliviana con artistas locales e internacionales', '2024-03-15 18:00:00', '2024-03-17 23:00:00', 'festival', 50.00, 0, -16.5000, -68.1500),
(2, 'Exposición de Arte Contemporáneo', 'Muestra de arte moderno de artistas bolivianos emergentes', '2024-03-20 10:00:00', '2024-04-20 18:00:00', 'exhibition', 15.00, 0, -16.2902, -63.5887),
(3, 'Tour Gastronómico La Paz', 'Recorrido por los mejores restaurantes y mercados tradicionales', '2024-03-25 09:00:00', '2024-03-25 17:00:00', 'tour', 80.00, 1, -16.5000, -68.1500),
(1, 'Concierto de Rock Nacional', 'Presentación de las mejores bandas de rock boliviano', '2024-04-01 20:00:00', '2024-04-01 23:30:00', 'concert', 30.00, 0, -16.5000, -68.1500),
(2, 'Taller de Cerámica Tradicional', 'Aprende las técnicas ancestrales de cerámica boliviana', '2024-04-05 14:00:00', '2024-04-05 18:00:00', 'workshop', 25.00, 1, -16.2902, -63.5887);

-- Insertar algunas imágenes de ejemplo
INSERT INTO event_images (event_id, image_url, alt_text, is_main) VALUES
(1, 'https://example.com/images/festival-musica-andina.jpg', 'Festival de Música Andina - Escenario principal', 1),
(1, 'https://example.com/images/festival-musica-andina-2.jpg', 'Festival de Música Andina - Artistas en escena', 0),
(2, 'https://example.com/images/exposicion-arte.jpg', 'Exposición de Arte Contemporáneo - Obra principal', 1),
(3, 'https://example.com/images/tour-gastronomico.jpg', 'Tour Gastronómico - Mercado tradicional', 1),
(4, 'https://example.com/images/concierto-rock.jpg', 'Concierto de Rock Nacional - Banda en escena', 1),
(5, 'https://example.com/images/taller-ceramica.jpg', 'Taller de Cerámica - Artesano trabajando', 1);

-- =====================================================
-- Comentarios y notas importantes
-- =====================================================

/*
NOTAS IMPORTANTES:

1. COORDENADAS GEOGRÁFICAS:
   - latitude: Decimal(10,8) permite precisión hasta 1.11 metros
   - longitude: Decimal(11,8) permite precisión hasta 1.11 metros
   - Las coordenadas son opcionales pero altamente recomendadas para búsquedas geográficas

2. TIPOS DE EVENTOS:
   - festival: Festivales culturales, musicales, etc.
   - concert: Conciertos y presentaciones musicales
   - exhibition: Exposiciones de arte, fotografía, etc.
   - tour: Tours guiados, recorridos turísticos
   - workshop: Talleres, cursos, capacitaciones
   - other: Otros tipos de eventos

3. EVENTOS RECURRENTES:
   - is_recurring = 1: El evento se repite periódicamente
   - is_recurring = 0: Evento único

4. PRECIOS:
   - NULL: Evento gratuito
   - DECIMAL(10,2): Precio en bolivianos (máximo 99,999,999.99)

5. FECHAS:
   - start_date: Fecha y hora de inicio (OBLIGATORIO)
   - end_date: Fecha y hora de fin (OPCIONAL)
   - Si no hay end_date, se asume que el evento termina el mismo día

6. IMÁGENES:
   - is_main = 1: Imagen principal del evento
   - is_main = 0: Imagen secundaria
   - Solo puede haber una imagen principal por evento

7. RELACIONES:
   - events.place_id -> places.id (CASCADE DELETE)
   - event_images.event_id -> events.id (CASCADE DELETE)

8. ÍNDICES:
   - Índice espacial para búsquedas geográficas
   - Índices en fechas para consultas temporales
   - Índices en tipos para filtros
*/
