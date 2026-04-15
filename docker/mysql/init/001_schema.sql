USE gropper;

CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(115) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    tipo ENUM('organizador', 'acompanante') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS viaje (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizador_id INT NOT NULL,
    destino VARCHAR(100) NOT NULL,
    presupuesto_limite DECIMAL(10, 2) NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_viaje_organizador FOREIGN KEY (organizador_id)
        REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('vuelo', 'alojamiento', 'actividad') NOT NULL,
    descripcion TEXT NOT NULL,
    precio_total DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255) DEFAULT NULL,
    destino VARCHAR(100) DEFAULT NULL,
    precio_por_persona TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reserva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    viaje_id INT NOT NULL,
    servicio_id INT NOT NULL,
    pagado_por_id INT DEFAULT NULL,
    personas INT NOT NULL DEFAULT 1,
    precio_aplicado DECIMAL(10,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reserva_viaje FOREIGN KEY (viaje_id)
        REFERENCES viaje(id) ON DELETE CASCADE,
    CONSTRAINT fk_reserva_servicio FOREIGN KEY (servicio_id)
        REFERENCES servicio(id) ON DELETE RESTRICT,
    CONSTRAINT fk_reserva_usuario_pago FOREIGN KEY (pagado_por_id)
        REFERENCES usuario(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS participante_viaje (
    viaje_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado_invitacion ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (viaje_id, usuario_id),
    CONSTRAINT fk_part_viaje FOREIGN KEY (viaje_id)
        REFERENCES viaje(id) ON DELETE CASCADE,
    CONSTRAINT fk_part_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    usuario_id INT NOT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pago_reserva FOREIGN KEY (reserva_id)
        REFERENCES reserva(id) ON DELETE CASCADE,
    CONSTRAINT fk_pago_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuario(id) ON DELETE RESTRICT
);

INSERT INTO servicio (tipo, descripcion, precio_total, imagen, destino, precio_por_persona)
SELECT 'vuelo', 'Vuelo Madrid - Tokio', 820.00, 'tokio.jpg', 'Tokio', 1
WHERE NOT EXISTS (SELECT 1 FROM servicio WHERE descripcion = 'Vuelo Madrid - Tokio');
INSERT INTO servicio (tipo, descripcion, precio_total, imagen, destino, precio_por_persona)
SELECT 'alojamiento', 'Shinjuku Granbell Hotel', 480.00, 'tokio.jpg', 'Tokio', 0
WHERE NOT EXISTS (SELECT 1 FROM servicio WHERE descripcion = 'Shinjuku Granbell Hotel');
INSERT INTO servicio (tipo, descripcion, precio_total, imagen, destino, precio_por_persona)
SELECT 'actividad', 'Tour por el Monte Fuji', 80.00, 'tokio.jpg', 'Tokio', 1
WHERE NOT EXISTS (SELECT 1 FROM servicio WHERE descripcion = 'Tour por el Monte Fuji');
