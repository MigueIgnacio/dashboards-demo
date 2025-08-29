<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/reports/dashboard-excess-alert-speed')->group(function () {
    Route::get('/getAlertCountByChannel', fn() => response()->json([
        ['channel_name' => 'Operaciones',  'alert_count' => 34],
        ['channel_name' => 'Mantenimiento', 'alert_count' => 12],
        ['channel_name' => 'Transporte',   'alert_count' => 22],
    ]));

    Route::get('/getEventsByDate', fn() => response()->json([
        ['date' => '2025-08-20', 'event_count' => 12],
        ['date' => '2025-08-21', 'event_count' => 18],
        ['date' => '2025-08-22', 'event_count' => 9],
        ['date' => '2025-08-23', 'event_count' => 15],
    ]));

    Route::get('/getTopVehiclesBySpeedExcess', fn() => response()->json([
        ['vehicle_plate' => 'ABCJ11', 'vehicle_code' => 'TRK-01', 'max_speed' => 112],
        ['vehicle_plate' => 'KJHG21', 'vehicle_code' => 'TRK-02', 'max_speed' => 109],
        ['vehicle_plate' => 'ZZZZ10', 'vehicle_code' => 'BUS-10', 'max_speed' => 107],
        ['vehicle_plate' => 'LMNO33', 'vehicle_code' => 'CAR-04', 'max_speed' => 115],
        ['vehicle_plate' => 'PQRS44', 'vehicle_code' => 'TRK-05', 'max_speed' => 111],
    ]));

    Route::get('/topDriversBySpeedExcess', fn() => response()->json([
        ['driver_name' => 'Juan Pérez',     'driver_rut' => '12.345.678-9', 'max_speed' => 112],
        ['driver_name' => 'Ana Díaz',       'driver_rut' => '9.876.543-2',  'max_speed' => 109],
        ['driver_name' => 'Pedro Rojas',    'driver_rut' => '15.234.567-8', 'max_speed' => 115],
        ['driver_name' => 'Carla Soto',     'driver_rut' => '16.987.654-3', 'max_speed' => 111],
        ['driver_name' => 'Luis González',  'driver_rut' => '11.223.344-5', 'max_speed' => 107],
    ]));

    Route::get('/getTopVehiclesByAlerts', fn() => response()->json([
        ['vehicle_plate' => 'ABCJ11', 'vehicle_code' => 'TRK-01', 'alerts' => 18],
        ['vehicle_plate' => 'KJHG21', 'vehicle_code' => 'TRK-02', 'alerts' => 16],
        ['vehicle_plate' => 'ZZZZ10', 'vehicle_code' => 'BUS-10', 'alerts' => 10],
        ['vehicle_plate' => 'LMNO33', 'vehicle_code' => 'CAR-04', 'alerts' => 14],
        ['vehicle_plate' => 'PQRS44', 'vehicle_code' => 'TRK-05', 'alerts' => 12],
    ]));

    Route::get('/topDriversByAlerts', fn() => response()->json([
        ['driver_name' => 'Juan Pérez',     'driver_rut' => '12.345.678-9', 'alerts' => 11],
        ['driver_name' => 'Ana Díaz',       'driver_rut' => '9.876.543-2',  'alerts' => 8],
        ['driver_name' => 'Pedro Rojas',    'driver_rut' => '15.234.567-8', 'alerts' => 10],
        ['driver_name' => 'Carla Soto',     'driver_rut' => '16.987.654-3', 'alerts' => 9],
        ['driver_name' => 'Luis González',  'driver_rut' => '11.223.344-5', 'alerts' => 7],
    ]));

    Route::get('/getAlertDetailsComplete', fn() => response()->json([
        [
            'vehicle_plate'   => 'ABCJ11',
            'vehicle_code'    => 'TRK-01',
            'driver'          => ['driver_name' => 'Juan Pérez', 'driver_phone' => '+56 9 1234 5678'],
            'avg_speed'       => 78.3,
            'max_speed'       => 112,
            'speed_deviation' => 9.2,
            'alert_count'     => 6,
        ],
        [
            'vehicle_plate'   => 'KJHG21',
            'vehicle_code'    => 'TRK-02',
            'driver'          => ['driver_name' => 'Ana Díaz', 'driver_phone' => '+56 9 8765 4321'],
            'avg_speed'       => 70.1,
            'max_speed'       => 109,
            'speed_deviation' => 6.7,
            'alert_count'     => 5,
        ],
        [
            'vehicle_plate'   => 'ZZZZ10',
            'vehicle_code'    => 'BUS-10',
            'driver'          => ['driver_name' => 'Pedro Rojas', 'driver_phone' => '+56 9 2222 1111'],
            'avg_speed'       => 82.5,
            'max_speed'       => 118,
            'speed_deviation' => 8.9,
            'alert_count'     => 7,
        ],
        [
            'vehicle_plate'   => 'LMNO33',
            'vehicle_code'    => 'CAR-04',
            'driver'          => ['driver_name' => 'Carla Soto', 'driver_phone' => '+56 9 3333 4444'],
            'avg_speed'       => 76.0,
            'max_speed'       => 115,
            'speed_deviation' => 7.5,
            'alert_count'     => 8,
        ],
        [
            'vehicle_plate'   => 'PQRS44',
            'vehicle_code'    => 'TRK-05',
            'driver'          => ['driver_name' => 'Luis González', 'driver_phone' => '+56 9 5555 6666'],
            'avg_speed'       => 79.8,
            'max_speed'       => 111,
            'speed_deviation' => 6.2,
            'alert_count'     => 4,
        ],
        [
            'vehicle_plate'   => 'UVWX55',
            'vehicle_code'    => 'SUV-07',
            'driver'          => ['driver_name' => 'Andrea Silva', 'driver_phone' => '+56 9 7777 8888'],
            'avg_speed'       => 85.0,
            'max_speed'       => 120,
            'speed_deviation' => 10.1,
            'alert_count'     => 9,
        ],
        [
            'vehicle_plate'   => 'TTTT88',
            'vehicle_code'    => 'TRK-09',
            'driver'          => ['driver_name' => 'Mario Vargas', 'driver_phone' => '+56 9 9999 0000'],
            'avg_speed'       => 88.2,
            'max_speed'       => 125,
            'speed_deviation' => 11.3,
            'alert_count'     => 10,
        ],
    ]));


    Route::get('/getPoints', fn() => response()->json([
        'data' => [
            ['lat' => -33.45, 'lng' => -70.66, 'vehicle_plate' => 'ABCJ11', 'driver' => 'Juan Pérez', 'fecha' => '2025-08-21 10:30'],
            ['lat' => -33.47, 'lng' => -70.68, 'vehicle_plate' => 'KJHG21', 'driver' => 'Ana Díaz',   'fecha' => '2025-08-21 11:00'],
            ['lat' => -33.46, 'lng' => -70.64, 'vehicle_plate' => 'ZZZZ10', 'driver' => 'Pedro Rojas', 'fecha' => '2025-08-21 11:15'],
            ['lat' => -33.48, 'lng' => -70.70, 'vehicle_plate' => 'LMNO33', 'driver' => 'Carla Soto', 'fecha' => '2025-08-21 11:40'],
            ['lat' => -33.50, 'lng' => -70.72, 'vehicle_plate' => 'PQRS44', 'driver' => 'Luis Gómez', 'fecha' => '2025-08-21 12:05'],
            ['lat' => -33.52, 'lng' => -70.74, 'vehicle_plate' => 'UVWX55', 'driver' => 'Andrea Silva', 'fecha' => '2025-08-21 12:20'],
            ['lat' => -33.54, 'lng' => -70.76, 'vehicle_plate' => 'YYYY99', 'driver' => 'Mario Vargas', 'fecha' => '2025-08-21 12:45'],
            ['lat' => -33.56, 'lng' => -70.78, 'vehicle_plate' => 'TTTT88', 'driver' => 'Fernanda Ruiz', 'fecha' => '2025-08-21 13:10'],
            ['lat' => -33.58, 'lng' => -70.80, 'vehicle_plate' => 'QWER22', 'driver' => 'José Castro', 'fecha' => '2025-08-21 13:35'],
            ['lat' => -33.60, 'lng' => -70.82, 'vehicle_plate' => 'ASDF33', 'driver' => 'Valentina López', 'fecha' => '2025-08-21 14:00'],
        ]
    ]));

    Route::get('/getAllSpeedAlertDetails', fn() => response()->json([
        'data' => [
            [
                'lat' => -33.45,
                'lng' => -70.66,
                'speed' => 112,
                'max' => 120,
                'delta' => 12,
                'vehicle_plate' => 'ABCJ11',
                'driver' => ['driver_name' => 'Juan Pérez'],
                'begin_at' => '2025-08-21 10:30'
            ],
            [
                'lat' => -33.47,
                'lng' => -70.68,
                'speed' => 95,
                'max' => 100,
                'delta' => 5,
                'vehicle_plate' => 'KJHG21',
                'driver' => ['driver_name' => 'Ana Díaz'],
                'begin_at' => '2025-08-21 11:00'
            ],
            [
                'lat' => -33.46,
                'lng' => -70.64,
                'speed' => 105,
                'max' => 110,
                'delta' => 10,
                'vehicle_plate' => 'ZZZZ10',
                'driver' => ['driver_name' => 'Pedro Rojas'],
                'begin_at' => '2025-08-21 11:20'
            ],
            [
                'lat' => -33.48,
                'lng' => -70.70,
                'speed' => 130,
                'max' => 135,
                'delta' => 15,
                'vehicle_plate' => 'LMNO33',
                'driver' => ['driver_name' => 'Carla Soto'],
                'begin_at' => '2025-08-21 11:40'
            ],
            [
                'lat' => -33.50,
                'lng' => -70.72,
                'speed' => 85,
                'max' => 90,
                'delta' => 5,
                'vehicle_plate' => 'PQRS44',
                'driver' => ['driver_name' => 'Luis Gómez'],
                'begin_at' => '2025-08-21 12:00'
            ],
            [
                'lat' => -33.52,
                'lng' => -70.74,
                'speed' => 118,
                'max' => 125,
                'delta' => 7,
                'vehicle_plate' => 'UVWX55',
                'driver' => ['driver_name' => 'Andrea Silva'],
                'begin_at' => '2025-08-21 12:20'
            ],
            [
                'lat' => -33.54,
                'lng' => -70.76,
                'speed' => 101,
                'max' => 105,
                'delta' => 4,
                'vehicle_plate' => 'YYYY99',
                'driver' => ['driver_name' => 'Mario Vargas'],
                'begin_at' => '2025-08-21 12:40'
            ],
            [
                'lat' => -33.56,
                'lng' => -70.78,
                'speed' => 125,
                'max' => 130,
                'delta' => 5,
                'vehicle_plate' => 'TTTT88',
                'driver' => ['driver_name' => 'Fernanda Ruiz'],
                'begin_at' => '2025-08-21 13:00'
            ],
            [
                'lat' => -33.58,
                'lng' => -70.80,
                'speed' => 90,
                'max' => 95,
                'delta' => 5,
                'vehicle_plate' => 'QWER22',
                'driver' => ['driver_name' => 'José Castro'],
                'begin_at' => '2025-08-21 13:20'
            ],
            [
                'lat' => -33.60,
                'lng' => -70.82,
                'speed' => 140,
                'max' => 145,
                'delta' => 10,
                'vehicle_plate' => 'ASDF33',
                'driver' => ['driver_name' => 'Valentina López'],
                'begin_at' => '2025-08-21 13:40'
            ],
        ]
    ]));

    Route::get('/getAllSpeedAlertDetails', fn() => response()->json([
    'data' => [
        [
            'lat' => -33.45,
            'lng' => -70.66,
            'speed' => 112,
            'max' => 120,
            'delta' => 8,
            'vehicle_plate' => 'ABCJ11',
            'driver' => ['driver_name' => 'Juan Pérez'],
            'begin_at' => '2025-08-25 08:30:00',
        ],
        [
            'lat' => -33.47,
            'lng' => -70.68,
            'speed' => 109,
            'max' => 115,
            'delta' => 6,
            'vehicle_plate' => 'KJHG21',
            'driver' => ['driver_name' => 'Ana Díaz'],
            'begin_at' => '2025-08-25 09:10:00',
        ],
        [
            'lat' => -33.44,
            'lng' => -70.65,
            'speed' => 98,
            'max' => 105,
            'delta' => 7,
            'vehicle_plate' => 'XYZB22',
            'driver' => ['driver_name' => 'Carlos Soto'],
            'begin_at' => '2025-08-25 10:05:00',
        ],
        [
            'lat' => -33.46,
            'lng' => -70.70,
            'speed' => 120,
            'max' => 128,
            'delta' => 8,
            'vehicle_plate' => 'LMNP33',
            'driver' => ['driver_name' => 'Patricia Muñoz'],
            'begin_at' => '2025-08-25 10:45:00',
        ],
        [
            'lat' => -33.48,
            'lng' => -70.72,
            'speed' => 115,
            'max' => 123,
            'delta' => 8,
            'vehicle_plate' => 'OPQR44',
            'driver' => ['driver_name' => 'Diego Ramírez'],
            'begin_at' => '2025-08-25 11:20:00',
        ],
        [
            'lat' => -33.50,
            'lng' => -70.73,
            'speed' => 87,
            'max' => 95,
            'delta' => 8,
            'vehicle_plate' => 'TUVX55',
            'driver' => ['driver_name' => 'Sofía Herrera'],
            'begin_at' => '2025-08-25 12:00:00',
        ],
        [
            'lat' => -33.52,
            'lng' => -70.75,
            'speed' => 132,
            'max' => 140,
            'delta' => 8,
            'vehicle_plate' => 'GHJK66',
            'driver' => ['driver_name' => 'Luis González'],
            'begin_at' => '2025-08-25 12:40:00',
        ],
        [
            'lat' => -33.43,
            'lng' => -70.63,
            'speed' => 105,
            'max' => 112,
            'delta' => 7,
            'vehicle_plate' => 'QWER77',
            'driver' => ['driver_name' => 'Fernanda López'],
            'begin_at' => '2025-08-25 13:25:00',
        ],
        [
            'lat' => -33.41,
            'lng' => -70.61,
            'speed' => 99,
            'max' => 107,
            'delta' => 8,
            'vehicle_plate' => 'ASDF88',
            'driver' => ['driver_name' => 'Pedro Morales'],
            'begin_at' => '2025-08-25 14:10:00',
        ],
        [
            'lat' => -33.39,
            'lng' => -70.60,
            'speed' => 110,
            'max' => 118,
            'delta' => 8,
            'vehicle_plate' => 'ZXCV99',
            'driver' => ['driver_name' => 'Marcela Torres'],
            'begin_at' => '2025-08-25 14:55:00',
        ],
    ]
]));
});

Route::get('/fleet/vehicles/all', fn() => response()->json([
    ['id' => 1, 'code' => 'TRK-01', 'plate' => 'ABCJ11'],
    ['id' => 2, 'code' => 'TRK-02', 'plate' => 'KJHG21'],
    ['id' => 3, 'code' => 'BUS-10', 'plate' => 'ZZZZ10'],
]));
Route::get('/company/management/visibleChannels', fn() => response()->json([
    'visibleChannels' => ['Operaciones', 'Mantenimiento', 'Transporte']
]));
Route::get('/fleet/contractors/all', fn() => response()->json([
    ['id' => 1, 'name' => 'MineraCo'],
    ['id' => 2, 'name' => 'LogistiCo']
]));
