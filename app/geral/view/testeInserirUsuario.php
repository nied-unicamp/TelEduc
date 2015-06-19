<?php

require_once '../controller/UsuarioController.php';

$usarioController = new UsuarioController();

$resultado = $usarioController->insereUsuario();

echo $resultado;