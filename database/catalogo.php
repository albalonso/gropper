<?php

// Devuelve el mapa base de destinos usados por la aplicacion.
function catalogo_destinos(): array
{
    return [
        // Cada destino define imagen principal y aeropuerto de referencia.
        'Tokio' => [
            'imagen' => 'Tokio.jpg',
            'aeropuerto' => 'Tokio (NRT)',
        ],
        'Bali' => [
            'imagen' => 'Bali.jpg',
            'aeropuerto' => 'Bali (DPS)',
        ],
        'Paris' => [
            'imagen' => 'Paris.jpeg',
            'aeropuerto' => 'Paris (CDG)',
        ],
        'Nueva York' => [
            'imagen' => 'NuevaYork.jpg',
            'aeropuerto' => 'Nueva York (JFK)',
        ],
        'Islandia' => [
            'imagen' => 'Islandia.jpg',
            'aeropuerto' => 'Reikiavik (KEF)',
        ],
    ];
}

// Genera el listado de vuelos disponibles para un destino concreto.
function catalogo_vuelos_destino(string $destino): array
{
    $mapa = [
        'Tokio' => [
            ['airline' => 'Iberia', 'price' => 620.00, 'ida_out' => '08:15', 'ida_in' => '11:30', 'vuelta_out' => '15:35', 'vuelta_in' => '18:45', 'duracion' => '14h 30m', 'escalas' => '1 escala', 'imagen' => 'Iberia-Logo.png'],
            ['airline' => 'Air Europa', 'price' => 710.00, 'ida_out' => '07:45', 'ida_in' => '16:20', 'vuelta_out' => '12:35', 'vuelta_in' => '21:05', 'duracion' => '14h 30m', 'escalas' => 'Directo', 'imagen' => 'Air-Europa-Logo.png'],
            ['airline' => 'Etihad Airways', 'price' => 760.00, 'ida_out' => '11:20', 'ida_in' => '19:10', 'vuelta_out' => '10:20', 'vuelta_in' => '17:55', 'duracion' => '14h 30m', 'escalas' => '1 escala', 'imagen' => 'Etihad-Airways-Logo.png'],
            ['airline' => 'Ryanair', 'price' => 575.00, 'ida_out' => '13:50', 'ida_in' => '10:55', 'vuelta_out' => '10:20', 'vuelta_in' => '13:30', 'duracion' => '14h 30m', 'escalas' => '1 escala', 'imagen' => 'ryanair_logo.jpg'],
        ],
        'Bali' => [
            ['airline' => 'Iberia', 'price' => 672.00, 'ida_out' => '08:15', 'ida_in' => '11:30', 'vuelta_out' => '15:50', 'vuelta_in' => '19:00', 'duracion' => '17h 50m', 'escalas' => '1 escala', 'imagen' => 'Iberia-Logo.png'],
            ['airline' => 'Ryanair', 'price' => 676.40, 'ida_out' => '13:50', 'ida_in' => '11:05', 'vuelta_out' => '10:20', 'vuelta_in' => '13:30', 'duracion' => '17h 50m', 'escalas' => '1 escala', 'imagen' => 'ryanair_logo.jpg'],
            ['airline' => 'Air Europa', 'price' => 849.60, 'ida_out' => '07:45', 'ida_in' => '16:30', 'vuelta_out' => '12:55', 'vuelta_in' => '21:15', 'duracion' => '17h 50m', 'escalas' => 'Directo', 'imagen' => 'Air-Europa-Logo.png'],
            ['airline' => 'Etihad Airways', 'price' => 610.00, 'ida_out' => '11:20', 'ida_in' => '19:10', 'vuelta_out' => '10:20', 'vuelta_in' => '17:55', 'duracion' => '17h 50m', 'escalas' => '1 escala', 'imagen' => 'Etihad-Airways-Logo.png'],
        ],
        'Paris' => [
            ['airline' => 'Iberia', 'price' => 210.00, 'ida_out' => '08:00', 'ida_in' => '10:05', 'vuelta_out' => '18:30', 'vuelta_in' => '20:35', 'duracion' => '2h 05m', 'escalas' => 'Directo', 'imagen' => 'Iberia-Logo.png'],
            ['airline' => 'Ryanair', 'price' => 170.00, 'ida_out' => '06:50', 'ida_in' => '08:55', 'vuelta_out' => '17:10', 'vuelta_in' => '19:15', 'duracion' => '2h 05m', 'escalas' => 'Directo', 'imagen' => 'ryanair_logo.jpg'],
            ['airline' => 'Air Europa', 'price' => 230.00, 'ida_out' => '07:40', 'ida_in' => '09:45', 'vuelta_out' => '15:55', 'vuelta_in' => '18:00', 'duracion' => '2h 05m', 'escalas' => 'Directo', 'imagen' => 'Air-Europa-Logo.png'],
            ['airline' => 'Etihad Airways', 'price' => 280.00, 'ida_out' => '10:40', 'ida_in' => '12:45', 'vuelta_out' => '19:35', 'vuelta_in' => '21:40', 'duracion' => '2h 05m', 'escalas' => '1 escala', 'imagen' => 'Etihad-Airways-Logo.png'],
        ],
        'Nueva York' => [
            ['airline' => 'Iberia', 'price' => 670.00, 'ida_out' => '08:35', 'ida_in' => '16:55', 'vuelta_out' => '18:25', 'vuelta_in' => '06:45', 'duracion' => '8h 20m', 'escalas' => 'Directo', 'imagen' => 'Iberia-Logo.png'],
            ['airline' => 'Ryanair', 'price' => 590.00, 'ida_out' => '07:15', 'ida_in' => '15:35', 'vuelta_out' => '17:05', 'vuelta_in' => '05:25', 'duracion' => '8h 20m', 'escalas' => '1 escala', 'imagen' => 'ryanair_logo.jpg'],
            ['airline' => 'Air Europa', 'price' => 710.00, 'ida_out' => '09:45', 'ida_in' => '18:05', 'vuelta_out' => '19:10', 'vuelta_in' => '07:30', 'duracion' => '8h 20m', 'escalas' => 'Directo', 'imagen' => 'Air-Europa-Logo.png'],
            ['airline' => 'Etihad Airways', 'price' => 760.00, 'ida_out' => '11:50', 'ida_in' => '20:10', 'vuelta_out' => '16:20', 'vuelta_in' => '04:40', 'duracion' => '8h 20m', 'escalas' => '1 escala', 'imagen' => 'Etihad-Airways-Logo.png'],
        ],
        'Islandia' => [
            ['airline' => 'Iberia', 'price' => 390.00, 'ida_out' => '08:00', 'ida_in' => '12:25', 'vuelta_out' => '13:40', 'vuelta_in' => '18:05', 'duracion' => '4h 25m', 'escalas' => 'Directo', 'imagen' => 'Iberia-Logo.png'],
            ['airline' => 'Ryanair', 'price' => 330.00, 'ida_out' => '06:50', 'ida_in' => '11:15', 'vuelta_out' => '12:25', 'vuelta_in' => '16:50', 'duracion' => '4h 25m', 'escalas' => 'Directo', 'imagen' => 'ryanair_logo.jpg'],
            ['airline' => 'Air Europa', 'price' => 420.00, 'ida_out' => '09:15', 'ida_in' => '13:40', 'vuelta_out' => '16:00', 'vuelta_in' => '20:25', 'duracion' => '4h 25m', 'escalas' => 'Directo', 'imagen' => 'Air-Europa-Logo.png'],
            ['airline' => 'Etihad Airways', 'price' => 460.00, 'ida_out' => '11:10', 'ida_in' => '15:35', 'vuelta_out' => '17:50', 'vuelta_in' => '22:15', 'duracion' => '4h 25m', 'escalas' => '1 escala', 'imagen' => 'Etihad-Airways-Logo.png'],
        ],
    ];

    // Convierte el formato interno del mapa en el formato comun de servicios del proyecto.
    $vuelos = [];
    foreach ($mapa[$destino] ?? [] as $vuelo) {
        $vuelos[] = [
            'descripcion' => 'Vuelo Madrid - ' . $destino . ' con ' . $vuelo['airline'],
            'precio_total' => $vuelo['price'],
            'imagen' => $vuelo['imagen'],
            'detalle' => 'Ida y vuelta para las fechas seleccionadas.',
            'airline' => $vuelo['airline'],
            'ida_out' => $vuelo['ida_out'],
            'ida_in' => $vuelo['ida_in'],
            'vuelta_out' => $vuelo['vuelta_out'],
            'vuelta_in' => $vuelo['vuelta_in'],
            'duracion' => $vuelo['duracion'],
            'escalas' => $vuelo['escalas'],
        ];
    }
    return $vuelos;
}

// Devuelve el catalogo general de servicios agrupado por destino y categoria.
function catalogo_servicios(): array
{
    return [
        // Servicios disponibles para Tokio.
        'Tokio' => [
            'vuelo' => catalogo_vuelos_destino('Tokio'),
            'alojamiento' => [
                ['descripcion' => 'Shinjuku Granbell Hotel', 'precio_total' => 160.00, 'imagen' => 'RoppongiHillsResidence.jpg', 'detalle' => 'Hotel centrico con desayuno y wifi.', 'zona' => 'Shinjuku', 'servicios_hotel' => 'WiFi · Desayuno · AC', 'estrellas' => 4, 'puntuacion' => 8.0, 'reviews' => 2902],
                ['descripcion' => 'Park Hyatt Tokyo', 'precio_total' => 220.00, 'imagen' => 'ParkHyatt.jpg', 'detalle' => 'Opcion de alta gama para el grupo.', 'zona' => 'Shinjuku', 'servicios_hotel' => 'Piscina · Spa · WiFi', 'estrellas' => 5, 'puntuacion' => 9.1, 'reviews' => 1840],
                ['descripcion' => 'Hotel Gracery Shinjuku', 'precio_total' => 140.00, 'imagen' => 'HotelGraceryShinjuku.jpg', 'detalle' => 'Ubicacion excelente junto al ocio nocturno.', 'zona' => 'Kabukicho', 'servicios_hotel' => 'WiFi · Recepcion 24h', 'estrellas' => 4, 'puntuacion' => 8.4, 'reviews' => 2213],
                ['descripcion' => 'Mitsui Garden Ginza', 'precio_total' => 175.00, 'imagen' => 'MitsuiGardenGinza.jpg', 'detalle' => 'Buena conexion y habitaciones amplias.', 'zona' => 'Ginza', 'servicios_hotel' => 'Desayuno · Gym · WiFi', 'estrellas' => 4, 'puntuacion' => 8.7, 'reviews' => 1320],
                ['descripcion' => 'Tokyo Bay Shiomi Prince', 'precio_total' => 130.00, 'imagen' => 'TokyoBayShiomi.jpg', 'detalle' => 'Alojamiento tranquilo con buena conexion.', 'zona' => 'Shiomi', 'servicios_hotel' => 'Spa · WiFi · Parking', 'estrellas' => 4, 'puntuacion' => 8.5, 'reviews' => 1011],
                ['descripcion' => 'Tokyo Station Hotel', 'precio_total' => 200.00, 'imagen' => 'TokyoStationHotel.jpeg', 'detalle' => 'Muy comodo para moverse por toda la ciudad.', 'zona' => 'Tokyo Station', 'servicios_hotel' => 'WiFi · Restaurante · Gym', 'estrellas' => 4, 'puntuacion' => 8.6, 'reviews' => 2150],
                ['descripcion' => 'Roppongi Hills Residence', 'precio_total' => 380.00, 'imagen' => 'ShinjukuGranbell.jpg', 'detalle' => 'Alojamiento premium para grupos exigentes.', 'zona' => 'Roppongi', 'servicios_hotel' => 'Piscina · Spa · WiFi', 'estrellas' => 5, 'puntuacion' => 9.0, 'reviews' => 1680],
                ['descripcion' => 'Asakusa Tradition Inn', 'precio_total' => 140.00, 'imagen' => 'AsakusaTradition.jpg', 'detalle' => 'Estilo tradicional con buena relacion calidad-precio.', 'zona' => 'Asakusa', 'servicios_hotel' => 'Desayuno · WiFi · AC', 'estrellas' => 3, 'puntuacion' => 7.8, 'reviews' => 890],
                ['descripcion' => 'Ryumeikan Tokyo', 'precio_total' => 240.00, 'imagen' => 'RyumeikanTokyo.jpg', 'detalle' => 'Elegante y bien situado para estancias cortas.', 'zona' => 'Chuo', 'servicios_hotel' => 'WiFi · Lounge · Desayuno', 'estrellas' => 4, 'puntuacion' => 8.8, 'reviews' => 970],
            ],
            'actividad' => [
                ['descripcion' => 'Tour por el Monte Fuji', 'precio_total' => 80.00, 'imagen' => 'TourMonteFuji.jpg', 'detalle' => 'Excursion guiada de dia completo.', 'duracion' => '9 - 10 horas', 'extras' => 'Grupo reducido · Recogida disponible', 'rating' => 5.0, 'reviews' => 306, 'old_price' => 88.00, 'badge' => 'Nuestra eleccion'],
                ['descripcion' => 'Templos y barrio Asakusa', 'precio_total' => 48.00, 'imagen' => 'TemplosbarrioAsakusa.webp', 'detalle' => 'Visita cultural por el casco historico.', 'duracion' => '3 - 4 horas', 'extras' => 'Sin colas · Guia local', 'rating' => 4.8, 'reviews' => 188, 'old_price' => 56.00, 'badge' => 'Top ventas'],
                ['descripcion' => 'Experiencia Gastronomia Tokio', 'precio_total' => 55.00, 'imagen' => 'ExperienciaGastronomiaTokio.jpg', 'detalle' => 'Ruta de degustacion en Shinjuku.', 'duracion' => '3 horas', 'extras' => 'Grupo reducido · Degustacion', 'rating' => 4.9, 'reviews' => 240, 'old_price' => 61.00, 'badge' => 'Se reserva rapido'],
                ['descripcion' => 'Tour Anime Akihabara', 'precio_total' => 42.00, 'imagen' => 'TourAnimeAkihabara.jpg', 'detalle' => 'Recorrido por tiendas anime y zonas gamer.', 'duracion' => '3 horas', 'extras' => 'Guia local · Grupo reducido', 'rating' => 4.7, 'reviews' => 164, 'old_price' => 49.00, 'badge' => 'Ideal frikis'],
                ['descripcion' => 'Noche panoramica Tokyo Tower', 'precio_total' => 38.00, 'imagen' => 'NochePanoramicaTokyoTower.jpg', 'detalle' => 'Miradores nocturnos y paseo iluminado.', 'duracion' => '2 horas', 'extras' => 'Entrada incluida · Al atardecer', 'rating' => 4.6, 'reviews' => 121, 'old_price' => 44.00, 'badge' => 'Plan nocturno'],
                ['descripcion' => 'Ruta de ramen por Shinjuku', 'precio_total' => 46.00, 'imagen' => 'RutaShinjuku.avif', 'detalle' => 'Paradas en locales emblematicos de ramen.', 'duracion' => '3 horas', 'extras' => 'Degustacion · Grupo reducido', 'rating' => 4.8, 'reviews' => 209, 'old_price' => 53.00, 'badge' => 'Foodie'],
                ['descripcion' => 'Tokyo Bay cruise al atardecer', 'precio_total' => 58.00, 'imagen' => 'TokyoBay.jpg', 'detalle' => 'Paseo panoramico por la bahia de Tokio.', 'duracion' => '2 horas', 'extras' => 'Sin colas · Vista skyline', 'rating' => 4.7, 'reviews' => 141, 'old_price' => 66.00, 'badge' => 'Muy recomendado'],
                ['descripcion' => 'Mercado y street food en Asakusa', 'precio_total' => 44.00, 'imagen' => 'MercadoAsakusa.webp', 'detalle' => 'Ruta entre puestos y sabores tradicionales.', 'duracion' => '2 - 3 horas', 'extras' => 'Degustacion · Recorrido guiado', 'rating' => 4.8, 'reviews' => 172, 'old_price' => 51.00, 'badge' => 'Cultura'],
                ['descripcion' => 'Excursion lagos del Fuji', 'precio_total' => 74.00, 'imagen' => 'TourMonteFuji.jpg', 'detalle' => 'Naturaleza, miradores y pueblos con encanto.', 'duracion' => '8 horas', 'extras' => 'Recogida disponible · Guia', 'rating' => 4.9, 'reviews' => 194, 'old_price' => 82.00, 'badge' => 'Muy reservada'],
            ],
        ],
        // Servicios disponibles para Bali.
        'Bali' => [
            'vuelo' => catalogo_vuelos_destino('Bali'),
            'alojamiento' => [
                ['descripcion' => 'Resort Seminyak Beach', 'precio_total' => 150.00, 'imagen' => 'ResortSeminyakBeach.jpg', 'detalle' => 'Resort junto a la playa con piscina.', 'zona' => 'Seminyak', 'servicios_hotel' => 'Piscina · Frente al mar', 'estrellas' => 5, 'puntuacion' => 8.8, 'reviews' => 945],
                ['descripcion' => 'Ubud Green Boutique', 'precio_total' => 120.00, 'imagen' => 'UbudJungleRetreat.webp', 'detalle' => 'Alojamiento tranquilo rodeado de naturaleza.', 'zona' => 'Ubud', 'servicios_hotel' => 'Desayuno · Piscina · WiFi', 'estrellas' => 4, 'puntuacion' => 8.2, 'reviews' => 611],
                ['descripcion' => 'Nusa Dua Coral Hotel', 'precio_total' => 170.00, 'imagen' => 'TheSeminyakBeachResort.jpg', 'detalle' => 'Hotel comodo para grupos grandes.', 'zona' => 'Nusa Dua', 'servicios_hotel' => 'Playa · Spa · WiFi', 'estrellas' => 4, 'puntuacion' => 8.6, 'reviews' => 702],
                ['descripcion' => 'Legian Sunset Suites', 'precio_total' => 110.00, 'imagen' => 'LegianSunsetSuites.jpg', 'detalle' => 'Alojamiento sencillo para ajustar presupuesto.', 'zona' => 'Legian', 'servicios_hotel' => 'WiFi · Parking · AC', 'estrellas' => 3, 'puntuacion' => 7.9, 'reviews' => 490],
                ['descripcion' => 'Kuta Palm Resort', 'precio_total' => 95.00, 'imagen' => 'KutaPalmResort.jpg', 'detalle' => 'Buena opcion para estancias activas.', 'zona' => 'Kuta', 'servicios_hotel' => 'Piscina · Desayuno', 'estrellas' => 3, 'puntuacion' => 7.7, 'reviews' => 455],
                ['descripcion' => 'Bali Beachfront Villa', 'precio_total' => 350.00, 'imagen' => 'BaliBeachfrontVilla.jpg', 'detalle' => 'Villa premium para una experiencia completa.', 'zona' => 'Seminyak Beach', 'servicios_hotel' => 'Piscina · Spa · WiFi', 'estrellas' => 5, 'puntuacion' => 9.2, 'reviews' => 750],
                ['descripcion' => 'Ubud Jungle Retreat', 'precio_total' => 160.00, 'imagen' => 'UbudGreenBoutique.jpg', 'detalle' => 'Entorno natural y mucha calma.', 'zona' => 'Ubud Jungle', 'servicios_hotel' => 'Desayuno · Piscina · WiFi', 'estrellas' => 4, 'puntuacion' => 8.5, 'reviews' => 420],
                ['descripcion' => 'Canggu Surf House', 'precio_total' => 140.00, 'imagen' => 'CangguSurfHouse.jpg', 'detalle' => 'Ideal para un viaje relajado cerca del mar.', 'zona' => 'Canggu', 'servicios_hotel' => 'WiFi · Surf · Parking', 'estrellas' => 3, 'puntuacion' => 8.0, 'reviews' => 380],
                ['descripcion' => 'The Seminyak Beach Resort', 'precio_total' => 280.00, 'imagen' => 'NusaDuaCoraHotel.jpg', 'detalle' => 'Opcion premium frente al mar.', 'zona' => 'Seminyak', 'servicios_hotel' => 'Piscina · Spa · Vistas', 'estrellas' => 5, 'puntuacion' => 9.0, 'reviews' => 1180],
            ],
            'actividad' => [
                ['descripcion' => 'Excursion templos de Ubud', 'precio_total' => 65.00, 'imagen' => 'ExcursiontemplosUbud.jpg', 'detalle' => 'Ruta cultural con guia local.', 'duracion' => '8 - 9 horas', 'extras' => 'Grupo reducido · Recogida disponible', 'rating' => 5.0, 'reviews' => 306, 'old_price' => 73.00, 'badge' => 'Nuestra eleccion'],
                ['descripcion' => 'Snorkel en Nusa Penida', 'precio_total' => 72.00, 'imagen' => 'SnorkelNusaPenida.avif', 'detalle' => 'Salida en barco con equipo incluido.', 'duracion' => '9 - 11 horas', 'extras' => 'Sin colas · Grupo reducido', 'rating' => 4.9, 'reviews' => 509, 'old_price' => 93.00, 'badge' => 'Se reservo 10 veces ayer'],
                ['descripcion' => 'Clase de cocina balinesa', 'precio_total' => 44.00, 'imagen' => 'Clasecocinabalinesa.jpg', 'detalle' => 'Actividad participativa para el grupo.', 'duracion' => '4 horas', 'extras' => 'Comida incluida · Chef local', 'rating' => 4.8, 'reviews' => 188, 'old_price' => 52.00, 'badge' => 'Autentico'],
                ['descripcion' => 'Atardecer en Tanah Lot', 'precio_total' => 35.00, 'imagen' => 'AtardecerTanahLot.avif', 'detalle' => 'Visita al templo costero al atardecer.', 'duracion' => '3 horas', 'extras' => 'Traslados · Miradores', 'rating' => 4.7, 'reviews' => 241, 'old_price' => 42.00, 'badge' => 'Plan romantico'],
                ['descripcion' => 'Trekking volcan Batur', 'precio_total' => 58.00, 'imagen' => 'TrekkingvolcanBatur.webp', 'detalle' => 'Ascenso guiado para ver el amanecer.', 'duracion' => '7 horas', 'extras' => 'Desayuno · Guia local', 'rating' => 4.8, 'reviews' => 320, 'old_price' => 67.00, 'badge' => 'Aventura'],
                ['descripcion' => 'Tour en yate por Nusa Penida', 'precio_total' => 84.00, 'imagen' => 'TourNusaPenida.jpg', 'detalle' => 'Navegacion premium con snorkel y calas.', 'duracion' => '9 - 10 horas', 'extras' => 'Grupo reducido · Recogida', 'rating' => 5.0, 'reviews' => 306, 'old_price' => 88.00, 'badge' => 'Premium'],
                ['descripcion' => 'Arrozales y columpios de Ubud', 'precio_total' => 48.00, 'imagen' => 'Arrozales.jpg', 'detalle' => 'Ruta fotografica entre arrozales y miradores.', 'duracion' => '5 horas', 'extras' => 'Entrada incluida · Guia', 'rating' => 4.7, 'reviews' => 177, 'old_price' => 56.00, 'badge' => 'Naturaleza'],
                ['descripcion' => 'Masaje balines', 'precio_total' => 39.00, 'imagen' => 'MasajeBalines.webp', 'detalle' => 'Experiencia relajada entre compras y bienestar.', 'duracion' => '4 horas', 'extras' => 'Masaje · Grupo reducido', 'rating' => 4.6, 'reviews' => 143, 'old_price' => 46.00, 'badge' => 'Relax'],
                ['descripcion' => 'Mercados Bali', 'precio_total' => 10.00, 'imagen' => 'MercadosBali.webp', 'detalle' => 'Recorrido por los mejores mercados de la zona.', 'duracion' => '1 horas', 'extras' => 'Conductor · Flexibilidad', 'rating' => 4.3, 'reviews' => 212, 'old_price' => 20.00, 'badge' => 'Muy reservado'],
            ],
        ],
        // Servicios disponibles para Paris.
        'Paris' => [
            'vuelo' => catalogo_vuelos_destino('Paris'),
            'alojamiento' => [
                ['descripcion' => 'Hotel Quartier Latin', 'precio_total' => 130.00, 'imagen' => 'HotelQuartierLatin.jpg', 'detalle' => 'Ubicacion centrica y buena relacion calidad-precio.', 'zona' => 'Quartier Latin', 'servicios_hotel' => 'Desayuno · WiFi', 'estrellas' => 4, 'puntuacion' => 8.3, 'reviews' => 1200],
                ['descripcion' => 'Montmartre View Hotel', 'precio_total' => 145.00, 'imagen' => 'MontmartreViewHotel.jpg', 'detalle' => 'Vistas urbanas y acceso rapido al metro.', 'zona' => 'Montmartre', 'servicios_hotel' => 'WiFi · Vistas ciudad', 'estrellas' => 4, 'puntuacion' => 8.1, 'reviews' => 830],
                ['descripcion' => 'Opera Business Inn', 'precio_total' => 160.00, 'imagen' => 'OperaBusinessInn.jpg', 'detalle' => 'Comodo y funcional para viajes de grupo.', 'zona' => 'Opera', 'servicios_hotel' => 'Gym · WiFi · Recepcion 24h', 'estrellas' => 4, 'puntuacion' => 8.5, 'reviews' => 954],
                ['descripcion' => 'Le Marais Urban Stay', 'precio_total' => 155.00, 'imagen' => 'LeMaraisUrbanStay.jpg', 'detalle' => 'Apartamento urbano bien situado.', 'zona' => 'Le Marais', 'servicios_hotel' => 'WiFi · Cocina', 'estrellas' => 4, 'puntuacion' => 8.4, 'reviews' => 742],
                ['descripcion' => 'Eiffel Riverside Rooms', 'precio_total' => 180.00, 'imagen' => 'EiffelRiversideRooms.jpg', 'detalle' => 'Alojamiento premium cerca del Sena.', 'zona' => 'Eiffel', 'servicios_hotel' => 'Vistas · WiFi · Desayuno', 'estrellas' => 5, 'puntuacion' => 9.0, 'reviews' => 1105],
                ['descripcion' => 'Paris Champs Elysees', 'precio_total' => 450.00, 'imagen' => 'ParisChampsElysees.jpg', 'detalle' => 'Experiencia de lujo en una zona iconica.', 'zona' => 'Champs Elysees', 'servicios_hotel' => 'WiFi · Desayuno · AC', 'estrellas' => 5, 'puntuacion' => 9.3, 'reviews' => 2100],
                ['descripcion' => 'Saint Germain Suite', 'precio_total' => 380.00, 'imagen' => 'SaintGermainSuite.webp', 'detalle' => 'Suite amplia para una estancia premium.', 'zona' => 'Saint Germain', 'servicios_hotel' => 'WiFi · Cocina · Gym', 'estrellas' => 4, 'puntuacion' => 8.8, 'reviews' => 950],
                ['descripcion' => 'Louvre View Apartment', 'precio_total' => 420.00, 'imagen' => 'LouvreViewApartment.jpg', 'detalle' => 'Apartamento elegante con excelente ubicacion.', 'zona' => 'Louvre', 'servicios_hotel' => 'WiFi · Vistas · AC', 'estrellas' => 4, 'puntuacion' => 8.7, 'reviews' => 1100],
                ['descripcion' => 'Hotel des Grands Boulevards', 'precio_total' => 200.00, 'imagen' => 'HotelGrandsBoulevards.jpg', 'detalle' => 'Hotel muy equilibrado para escapadas en grupo.', 'zona' => 'Grands Boulevards', 'servicios_hotel' => 'Bar · WiFi · Desayuno', 'estrellas' => 4, 'puntuacion' => 8.6, 'reviews' => 860],
            ],
            'actividad' => [
                ['descripcion' => 'Visita guiada Louvre', 'precio_total' => 45.00, 'imagen' => 'VisitaLouvre.webp', 'detalle' => 'Entrada con visita guiada resumida.', 'duracion' => '3 horas', 'extras' => 'Sin colas · Guia local', 'rating' => 4.9, 'reviews' => 402, 'old_price' => 54.00, 'badge' => 'Nuestra eleccion'],
                ['descripcion' => 'Crucero por el Sena', 'precio_total' => 40.00, 'imagen' => 'CruceroSena.jpg', 'detalle' => 'Paseo en barco al atardecer.', 'duracion' => '2 horas', 'extras' => 'Sin colas · Vistas panoramicas', 'rating' => 4.8, 'reviews' => 381, 'old_price' => 48.00, 'badge' => 'Muy solicitado'],
                ['descripcion' => 'Subida Torre Eiffel', 'precio_total' => 36.00, 'imagen' => 'SubidaTorreEiffel.avif', 'detalle' => 'Acceso reservado para evitar colas.', 'duracion' => '2 horas', 'extras' => 'Acceso prioritario · Mirador', 'rating' => 4.7, 'reviews' => 520, 'old_price' => 43.00, 'badge' => 'Top iconico'],
                ['descripcion' => 'Tour por el barrio latino', 'precio_total' => 28.00, 'imagen' => 'Tourbarriolatino.jpg', 'detalle' => 'Ruta guiada por calles historicas y plazas.', 'duracion' => '3 horas', 'extras' => 'Guia local · Grupo reducido', 'rating' => 4.7, 'reviews' => 196, 'old_price' => 35.00, 'badge' => 'Cultural'],
                ['descripcion' => 'Ruta de cafeterias historicas', 'precio_total' => 30.00, 'imagen' => 'Rutacafeteriashistoricas.webp', 'detalle' => 'Recorrido entre cafes con mucha historia.', 'duracion' => '3 horas', 'extras' => 'Degustacion · Grupo reducido', 'rating' => 4.8, 'reviews' => 174, 'old_price' => 37.00, 'badge' => 'Foodie'],
                ['descripcion' => 'Paris monumental en barco', 'precio_total' => 42.00, 'imagen' => 'Parismonumentalbarco.jpg', 'detalle' => 'Version premium del paseo por el rio.', 'duracion' => '2 horas', 'extras' => 'Audio guia · Sin colas', 'rating' => 4.6, 'reviews' => 211, 'old_price' => 50.00, 'badge' => 'Panoramico'],
                ['descripcion' => 'DisneyLand Paris', 'precio_total' => 150.00, 'imagen' => 'DisneyLand.webp', 'detalle' => 'Vuelve a sentirte como un niño.', 'duracion' => '12 horas', 'extras' => 'Atracciones · Spots secretos', 'rating' => 5.0, 'reviews' => 500, 'old_price' => 100.00, 'badge' => 'Diversión'],
                ['descripcion' => 'Louvre express por obras clave', 'precio_total' => 39.00, 'imagen' => 'Louvrexpress.jpg', 'detalle' => 'Visita breve centrada en lo imprescindible.', 'duracion' => '2 horas', 'extras' => 'Sin colas · Guia', 'rating' => 4.8, 'reviews' => 257, 'old_price' => 47.00, 'badge' => 'Rapida'],
                ['descripcion' => 'Montmartre y luces de Paris', 'precio_total' => 32.00, 'imagen' => 'MontmartreParis.webp', 'detalle' => 'Paseo al atardecer por calles con encanto.', 'duracion' => '3 horas', 'extras' => 'Grupo reducido · Miradores', 'rating' => 4.7, 'reviews' => 149, 'old_price' => 39.00, 'badge' => 'Atardecer'],
            ],
        ],
        // Servicios disponibles para Nueva York.
        'Nueva York' => [
            'vuelo' => catalogo_vuelos_destino('Nueva York'),
            'alojamiento' => [
                ['descripcion' => 'Hotel Manhattan Central', 'precio_total' => 210.00, 'imagen' => 'CentralParkViewInn.jpg', 'detalle' => 'Base centrica para visitar la ciudad.', 'zona' => 'Manhattan', 'servicios_hotel' => 'WiFi · Gym · Concierge', 'estrellas' => 4, 'puntuacion' => 8.0, 'reviews' => 1766],
                ['descripcion' => 'Soho Loft Boutique', 'precio_total' => 240.00, 'imagen' => 'WallStreetExecutive.jpg', 'detalle' => 'Diseño cuidado y buen ambiente.', 'zona' => 'SoHo', 'servicios_hotel' => 'WiFi · Lounge · Bar', 'estrellas' => 4, 'puntuacion' => 8.2, 'reviews' => 998],
                ['descripcion' => 'Brooklyn Bridge Rooms', 'precio_total' => 175.00, 'imagen' => 'ThePlazaHotel.jpg', 'detalle' => 'Alojamiento comodo con buen acceso.', 'zona' => 'Brooklyn', 'servicios_hotel' => 'WiFi · Parking', 'estrellas' => 3, 'puntuacion' => 7.8, 'reviews' => 670],
                ['descripcion' => 'Times Square Urban', 'precio_total' => 230.00, 'imagen' => 'TimesSquareUrban.jpg', 'detalle' => 'Muy bien ubicado para estancias cortas.', 'zona' => 'Times Square', 'servicios_hotel' => 'WiFi · Gym · Recepcion 24h', 'estrellas' => 4, 'puntuacion' => 8.1, 'reviews' => 1402],
                ['descripcion' => 'Central Park View Inn', 'precio_total' => 260.00, 'imagen' => 'HotelManhattanCentral.jpg', 'detalle' => 'Mejor opcion si buscas vistas y confort.', 'zona' => 'Central Park', 'servicios_hotel' => 'Vistas · WiFi · Desayuno', 'estrellas' => 5, 'puntuacion' => 8.9, 'reviews' => 1193],
                ['descripcion' => 'NYC Fifth Avenue', 'precio_total' => 520.00, 'imagen' => 'NYCFifthAvenue.jpg', 'detalle' => 'Alojamiento de alto nivel en plena Quinta Avenida.', 'zona' => 'Fifth Avenue', 'servicios_hotel' => 'WiFi · Gym · Concierge', 'estrellas' => 5, 'puntuacion' => 9.1, 'reviews' => 3200],
                ['descripcion' => 'Wall Street Executive', 'precio_total' => 400.00, 'imagen' => 'SohoLoftBoutique.jpg', 'detalle' => 'Opcion premium para estancias urbanas.', 'zona' => 'Wall Street', 'servicios_hotel' => 'WiFi · Business · Gym', 'estrellas' => 4, 'puntuacion' => 8.4, 'reviews' => 890],
                ['descripcion' => 'Greenwich Village Inn', 'precio_total' => 320.00, 'imagen' => 'GreenwichVillageInn.jpg', 'detalle' => 'Muy buena atmosfera en una zona con encanto.', 'zona' => 'Greenwich Village', 'servicios_hotel' => 'WiFi · Restaurant · Bar', 'estrellas' => 4, 'puntuacion' => 8.6, 'reviews' => 1200],
                ['descripcion' => 'The Plaza Hotel', 'precio_total' => 680.00, 'imagen' => 'BrooklynBridgeRooms.jpg', 'detalle' => 'Iconico hotel de lujo para una estancia especial.', 'zona' => 'Central Park South', 'servicios_hotel' => 'Spa · Restaurante · WiFi', 'estrellas' => 5, 'puntuacion' => 9.3, 'reviews' => 2800],
            ],
            'actividad' => [
                ['descripcion' => 'Tour contrastes NYC', 'precio_total' => 70.00, 'imagen' => 'TourcontrastesNYC.webp', 'detalle' => 'Recorrido por barrios clave de la ciudad.', 'duracion' => '5 horas', 'extras' => 'Grupo reducido · Guia local', 'rating' => 4.9, 'reviews' => 430, 'old_price' => 79.00, 'badge' => 'Nuestra eleccion'],
                ['descripcion' => 'Entrada Top of the Rock', 'precio_total' => 52.00, 'imagen' => 'EntradaTopRock.webp', 'detalle' => 'Mirador panoramico con acceso programado.', 'duracion' => '2 horas', 'extras' => 'Sin colas · Vistas', 'rating' => 4.8, 'reviews' => 390, 'old_price' => 61.00, 'badge' => 'Top ventas'],
                ['descripcion' => 'Musical en Broadway', 'precio_total' => 95.00, 'imagen' => 'MusicalBroadway.jpg', 'detalle' => 'Entrada para una noche especial en grupo.', 'duracion' => '2.5 horas', 'extras' => 'Butacas seleccionadas · Centro', 'rating' => 4.9, 'reviews' => 612, 'old_price' => 110.00, 'badge' => 'Muy solicitado'],
                ['descripcion' => 'Paseo en ferry Estatua Libertad', 'precio_total' => 34.00, 'imagen' => 'PaseoferryEstatuaLibertad.jpg', 'detalle' => 'Crucero con vistas al skyline y la estatua.', 'duracion' => '3 horas', 'extras' => 'Sin colas · Ferry', 'rating' => 4.7, 'reviews' => 281, 'old_price' => 42.00, 'badge' => 'Clasico'],
                ['descripcion' => 'Tour gastronomico Chelsea', 'precio_total' => 60.00, 'imagen' => 'TourgastronomicoChelsea.jpg', 'detalle' => 'Degustacion guiada por mercados y locales.', 'duracion' => '3 horas', 'extras' => 'Comida incluida · Guia', 'rating' => 4.8, 'reviews' => 232, 'old_price' => 69.00, 'badge' => 'Foodie'],
                ['descripcion' => 'Tour nocturno Times Square', 'precio_total' => 38.00, 'imagen' => 'TimesSquareUrban.jpg', 'detalle' => 'Luces, ambiente y puntos iconicos al anochecer.', 'duracion' => '2 horas', 'extras' => 'Grupo reducido · Guia', 'rating' => 4.6, 'reviews' => 176, 'old_price' => 45.00, 'badge' => 'Nocturno'],
                ['descripcion' => 'Cruise por Manhattan al atardecer', 'precio_total' => 58.00, 'imagen' => 'CruiseManhattan.jpg', 'detalle' => 'Recorrido panoramico en barco al caer el sol.', 'duracion' => '2 horas', 'extras' => 'Sin colas · Vistas skyline', 'rating' => 4.7, 'reviews' => 201, 'old_price' => 66.00, 'badge' => 'Panoramico'],
                ['descripcion' => 'Miradores de Manhattan premium', 'precio_total' => 64.00, 'imagen' => 'MiradoresManhattan.webp', 'detalle' => 'Acceso a mirador con horario premium.', 'duracion' => '2 horas', 'extras' => 'Entrada premium · Sin colas', 'rating' => 4.8, 'reviews' => 154, 'old_price' => 72.00, 'badge' => 'Premium'],
                ['descripcion' => 'Broadway backstage experience', 'precio_total' => 88.00, 'imagen' => 'Broadwaybackstage.webp', 'detalle' => 'Visita guiada centrada en la historia del teatro.', 'duracion' => '3 horas', 'extras' => 'Grupo reducido · Guia experto', 'rating' => 4.9, 'reviews' => 118, 'old_price' => 99.00, 'badge' => 'Especial'],
            ],
        ],
        // Servicios disponibles para Islandia.
        'Islandia' => [
            'vuelo' => catalogo_vuelos_destino('Islandia'),
            'alojamiento' => [
                ['descripcion' => 'Golden Circle Guesthouse', 'precio_total' => 140.00, 'imagen' => 'GoldenCircleGuesthouse.jpg', 'detalle' => 'Guesthouse funcional para explorar la isla.', 'zona' => 'Golden Circle', 'servicios_hotel' => 'WiFi · Cocina', 'estrellas' => 3, 'puntuacion' => 7.8, 'reviews' => 298],
                ['descripcion' => 'Nordic Lights Inn', 'precio_total' => 175.00, 'imagen' => 'NorthernLightsCabin.jpg', 'detalle' => 'Opcion practica para recorrer el sur.', 'zona' => 'Keflavik', 'servicios_hotel' => 'WiFi · Parking · Bar', 'estrellas' => 3, 'puntuacion' => 7.9, 'reviews' => 355],
                ['descripcion' => 'Blue Lagoon Lodge', 'precio_total' => 220.00, 'imagen' => 'BlueLagoonLodge.jpg', 'detalle' => 'Alojamiento especial cerca de las termas.', 'zona' => 'Blue Lagoon', 'servicios_hotel' => 'Spa · Termas · WiFi', 'estrellas' => 5, 'puntuacion' => 9.1, 'reviews' => 400],
                ['descripcion' => 'Cabana Aurora Iceland', 'precio_total' => 190.00, 'imagen' => 'CabanaAuroraIceland.jpg', 'detalle' => 'Cabana acogedora para ver auroras boreales.', 'zona' => 'Akureyri', 'servicios_hotel' => 'Calefaccion · Parking', 'estrellas' => 4, 'puntuacion' => 8.4, 'reviews' => 522],
                ['descripcion' => 'Reykjavik Harbor Hotel', 'precio_total' => 160.00, 'imagen' => 'ReykjavikHarborHotel.jpg', 'detalle' => 'Hotel funcional y centrico.', 'zona' => 'Reikiavik Puerto', 'servicios_hotel' => 'WiFi · Desayuno', 'estrellas' => 4, 'puntuacion' => 8.2, 'reviews' => 610],
                ['descripcion' => 'Iceland Glacier Hotel', 'precio_total' => 280.00, 'imagen' => 'IcelandGlacierHotel.jpg', 'detalle' => 'Muy buena opcion para rutas de aventura.', 'zona' => 'Glacier Region', 'servicios_hotel' => 'WiFi · Spa · Restaurant', 'estrellas' => 4, 'puntuacion' => 8.8, 'reviews' => 450],
                ['descripcion' => 'Northern Lights Cabin', 'precio_total' => 200.00, 'imagen' => 'NordicLightsInn.jpg', 'detalle' => 'Cabin tranquila para grupos pequenos.', 'zona' => 'Northern Lights', 'servicios_hotel' => 'WiFi · Parking · Kitchen', 'estrellas' => 3, 'puntuacion' => 8.1, 'reviews' => 280],
                ['descripcion' => 'Volcano View Lodge', 'precio_total' => 240.00, 'imagen' => 'VolcanoViewLodge.jpg', 'detalle' => 'Alojamiento moderno rodeado de paisaje volcanico.', 'zona' => 'Volcano Area', 'servicios_hotel' => 'WiFi · Parking · Restaurant', 'estrellas' => 4, 'puntuacion' => 8.3, 'reviews' => 320],
                ['descripcion' => 'Hotel Ranga', 'precio_total' => 320.00, 'imagen' => 'HotelRanga.jpg', 'detalle' => 'Hotel premium para una estancia especial.', 'zona' => 'Hella', 'servicios_hotel' => 'Spa · Bar · WiFi', 'estrellas' => 5, 'puntuacion' => 9.2, 'reviews' => 540],
            ],
            'actividad' => [
                ['descripcion' => 'Ruta glaciar en jeep', 'precio_total' => 95.00, 'imagen' => 'Rutaglaciarjeep.jpg', 'detalle' => 'Actividad de aventura con guia.', 'duracion' => '4 horas', 'extras' => 'Grupo reducido · Equipo incluido', 'rating' => 4.9, 'reviews' => 265, 'old_price' => 109.00, 'badge' => 'Nuestra eleccion'],
                ['descripcion' => 'Banio termal Blue Lagoon', 'precio_total' => 68.00, 'imagen' => 'BaniotermalBlueLagoon.jpg', 'detalle' => 'Entrada a termas geotermales.', 'duracion' => '3 horas', 'extras' => 'Sin colas · Toalla incluida', 'rating' => 4.8, 'reviews' => 418, 'old_price' => 79.00, 'badge' => 'Top ventas'],
                ['descripcion' => 'Caza auroras boreales', 'precio_total' => 85.00, 'imagen' => 'Cazaaurorasboreales.jpg', 'detalle' => 'Salida nocturna segun condiciones meteorologicas.', 'duracion' => '5 horas', 'extras' => 'Guia experto · Grupo reducido', 'rating' => 4.9, 'reviews' => 337, 'old_price' => 97.00, 'badge' => 'Muy solicitada'],
                ['descripcion' => 'Cascadas costa sur', 'precio_total' => 52.00, 'imagen' => 'Cascadascostasur.avif', 'detalle' => 'Ruta paisajistica entre cascadas y miradores.', 'duracion' => '6 horas', 'extras' => 'Recogida · Guia local', 'rating' => 4.7, 'reviews' => 194, 'old_price' => 61.00, 'badge' => 'Panoramica'],
                ['descripcion' => 'Senderismo volcanico', 'precio_total' => 58.00, 'imagen' => 'Senderismovolcanico.avif', 'detalle' => 'Caminata guiada por paisajes de lava.', 'duracion' => '5 horas', 'extras' => 'Grupo reducido · Equipo basico', 'rating' => 4.8, 'reviews' => 180, 'old_price' => 67.00, 'badge' => 'Aventura'],
                ['descripcion' => 'Blue Lagoon premium sunset', 'precio_total' => 74.00, 'imagen' => 'BlueLagoon.avif', 'detalle' => 'Acceso al balneario en horario premium.', 'duracion' => '3 horas', 'extras' => 'Bebida incluida · Sunset', 'rating' => 4.8, 'reviews' => 147, 'old_price' => 85.00, 'badge' => 'Premium'],
                ['descripcion' => 'Miradores glaciares', 'precio_total' => 88.00, 'imagen' => 'Miradoresglaciares.jpg', 'detalle' => 'Combinacion de off-road y paisajes helados.', 'duracion' => '5 horas', 'extras' => 'Guia · Grupo reducido', 'rating' => 4.7, 'reviews' => 126, 'old_price' => 98.00, 'badge' => 'Muy reservado'],
                ['descripcion' => 'Ruta fotografica auroras', 'precio_total' => 79.00, 'imagen' => 'Rutafotograficaauroras.jpg', 'detalle' => 'Salida enfocada a capturar las auroras.', 'duracion' => '5 horas', 'extras' => 'Consejos foto · Guia', 'rating' => 4.8, 'reviews' => 132, 'old_price' => 89.00, 'badge' => 'Fotos top'],
                ['descripcion' => 'Visita focas salvajes', 'precio_total' => 62.00, 'imagen' => 'FocasIslandia.jpg', 'detalle' => 'Tour completo con varias paradas iconicas.', 'duracion' => '8 horas', 'extras' => 'Recogida · Paradas clave', 'rating' => 4.9, 'reviews' => 208, 'old_price' => 71.00, 'badge' => 'Completo'],
            ],
        ],
    ];
}
