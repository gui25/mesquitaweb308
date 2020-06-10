<?php

Route::post('/alunos',function($a, $r, $b){

    $db = DBMySQL::getInstance();     
      
    // echo "nome: ", $b->aluno_nome;
    // echo "idade: ", $b->aluno_idade;
    
    $sql = "insert into alunos (nome, idade) values ('" . $b->aluno_nome . "', " . $b->aluno_idade . ") ";
    
    if ($db->query($sql)) {
        $ret = array(
            "status" => 200,
            "message" => Route::$statusCodes[200],
            "result" => "Aluno incluido com sucesso."
         ); 
    } else {

        $ret = array(
            "status" => 400,
            "message" => Route::$statusCodes[400]
         );  
    }

    echo json_encode($ret);

    
});