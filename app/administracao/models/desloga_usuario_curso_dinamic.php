<?php
$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';

$cod_usuario = $_POST['cod_usuario'];
$cod_curso = $_POST['cod_curso'];

$sock=AcessoSQL::Conectar($cod_curso);
$query = "update Usuarios_online set logado = 0 where cod_usuario = ".$cod_usuario;
$res=AcessoSQL::Enviar($sock,$query);
AcessoSQL::Desconectar($sock);

?>
