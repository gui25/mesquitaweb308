<?php

Route::get('/disciplinas',function($a, $r, $b){
    var_dump($a);
    var_dump($r);
    var_dump($b);
});

Route::get('/disciplinas/:i',function($a, $r, $b){
    var_dump($a);
    var_dump($r);
    var_dump($b);
});