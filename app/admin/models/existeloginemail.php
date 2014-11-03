<?php
$ferramenta_geral = 'geral';
$ferramenta_admin = 'admin';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_admin = '../../'.$ferramenta_admin.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_admin.'admin.inc';

$login = $_POST['login'];
$email = $_POST['email'];

$sock=AcessoSQL::Conectar("");

$query = "select * from Usuario where login='".$login."' or email = '".$email."'" ;
$res = AcessoSQL::Enviar($sock,$query);

AcessoSQL::Desconectar($sock);

$res = AcessoSQL::RetornaLinha($res);

echo json_encode($res);