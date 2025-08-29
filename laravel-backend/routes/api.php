<?php

use Illuminate\Support\Facades\Route;

// Normaliza filtros desde query
function filterParams() {
  return [
    'from'    => request('from', now()->subDays(7)->format('Y-m-d')),
    'to'      => request('to', now()->format('Y-m-d')),
    'carrier' => request('carrier'),
    'vehicle' => request('vehicle'),
  ];
}

// 1) Canales visibles (para mostrar Donut de gerencias solo si hay >1)
Route::get('/visible-channels', function () {
  return response()->json(['visibleChannels' => ['Operaciones','Mantenimiento','Transporte']]);
});

// 2) Listas para filtros
Route::get('/contractors', fn() => response()->json([['id'=>1,'name'=>'MineraCo'], ['id'=>2,'name'=>'LogistiCo']]));
Route::get('/vehicles', fn() => response()->json([
  ['id'=>1,'code'=>'TRK-01','plate'=>'ABCJ11'],
  ['id'=>2,'code'=>'TRK-02','plate'=>'KJHG21'],
  ['id'=>3,'code'=>'BUS-10','plate'=>'ZZZZ10'],
]));

// 3) Donut: alertas por gerencia
Route::get('/dashboards/excess/alerts-by-management', function() {
  $f = filterParams();
  return response()->json([
    ['management'=>'Operaciones','count'=>34],
    ['management'=>'Mantenimiento','count'=>12],
    ['management'=>'Transporte','count'=>22],
  ]);
});

// 4) Línea: cantidad de excesos por fecha
Route::get('/dashboards/excess/by-date', function() {
  $f = filterParams();
  return response()->json([
    ['date'=>'2025-08-20','count'=>12],
    ['date'=>'2025-08-21','count'=>18],
    ['date'=>'2025-08-22','count'=>9],
    ['date'=>'2025-08-23','count'=>15],
  ]);
});

// 5) Top 5 peaks (máximas velocidades)
Route::get('/dashboards/excess/top-peaks', fn() => response()->json([
  ['vehicle_plate'=>'ABCJ11','max_speed'=>112],
  ['vehicle_plate'=>'KJHG21','max_speed'=>109],
  ['vehicle_plate'=>'ZZZZ10','max_speed'=>107],
]));

// 6) Top 5 patentes por alertas
Route::get('/dashboards/excess/top-vehicles', fn() => response()->json([
  ['vehicle_plate'=>'ABCJ11','alerts'=>18],
  ['vehicle_plate'=>'KJHG21','alerts'=>16],
  ['vehicle_plate'=>'ZZZZ10','alerts'=>10],
]));

// 7) Tabla detalle (con paginación simple)
Route::get('/dashboards/excess/table', function() {
  $page = max((int)request('page',1),1);
  $per  = max((int)request('per_page',10),1);
  $all = collect([
    ['datetime'=>'2025-08-21 10:10','vehicle_code'=>'TRK-01','vehicle_plate'=>'ABCJ11','speed'=>112,'carrier'=>'MineraCo','lat'=>-33.45,'lng'=>-70.66],
    ['datetime'=>'2025-08-21 11:00','vehicle_code'=>'TRK-02','vehicle_plate'=>'KJHG21','speed'=>109,'carrier'=>'MineraCo','lat'=>-33.47,'lng'=>-70.68],
    // ... agrega más filas para probar
  ]);
  $slice = $all->forPage($page,$per)->values();
  return response()->json(['data'=>$slice,'meta'=>['page'=>$page,'per_page'=>$per,'total'=>$all->count()]]);
});

// 8) Mapa: puntos de exceso
Route::get('/dashboards/excess/map', fn() => response()->json([
  ['lat'=>-33.45,'lng'=>-70.66,'speed'=>112,'vehicle_plate'=>'ABCJ11'],
  ['lat'=>-33.47,'lng'=>-70.68,'speed'=>109,'vehicle_plate'=>'KJHG21'],
]));

// 9) Heatmap (si lo usas)
Route::get('/dashboards/excess/heatmap', fn() => response()->json([
  ['lat'=>-33.45,'lng'=>-70.66,'intensity'=>0.7],
  ['lat'=>-33.46,'lng'=>-70.67,'intensity'=>0.9],
]));
