<?php

require_once '../controller/CursoController.php';

$cursoController = new CursoController();

$resultado = $cursoController->apagaCursos();

echo $resultado;

?>