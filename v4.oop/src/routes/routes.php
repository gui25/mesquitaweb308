<?php

require_once "alunos/routes.alunos.php";
require_once "disciplinas/routes.disciplinas.php";


// home
Route::get('/',function($a, $r, $b){
    http_response_code(200);
    echo 'API v4.0';
});


Route::get('/connect',function($a, $r, $b){
    http_response_code(200);
    $db = DBMySQL::getInstance();
    var_dump($db);
});

Route::get('/info',function($a, $r, $b){
    http_response_code(200);
    echo "<pre>";
    //print_r($_SERVER);
    echo "</pre>";
});



Route::put('/login',function($a, $r, $b){
    var_dump($a);
    var_dump($r);
    var_dump($b);
    echo Route::$statusCodes[404];
});

// Rota estática
Route::get('/teste.html',function($a, $r, $b){
    http_response_code(200);
    echo '<p>Simulação de pagina virtual estatica';
});