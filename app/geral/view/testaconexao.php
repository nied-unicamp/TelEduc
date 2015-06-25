<?php

require_once '../controller/CursoController.php';

$cursoController = new CursoController();

$resultado = $cursoController->testaConexao();

echo $resultado;

?>


