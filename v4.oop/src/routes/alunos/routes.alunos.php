<?php

require_once "routes.alunos.post.php";
require_once "routes.alunos.id.php";

Route::get('/alunos',function($a, $r, $b){
    try {
      $db = DBMySQL::getInstance();     
      
      $result = $db->query("select * from alunos limit 0,100");
      
      $linhas = array();

      while($linha = $result->fetch_assoc()) {
        $linhas[] = $linha;
      }
      
      $ret = array(
        "status" => "200",
        "message" => "Sucesso",
        "result" => $linhas
      );

      echo json_encode($ret);

    } catch(Exception $e) {

    }
    // var_dump($a);
    // var_dump($r);
    // var_dump($b);
});

