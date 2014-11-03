<?php
$ferramenta_geral = 'geral';
$ferramenta_admin = 'admin';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_admin = '../../'.$ferramenta_admin.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_admin.'admin.inc';

$pal = $_POST['pal'];

$sock = AcessoSQL::Conectar("");
$logins_usuarios = Admin::RetornaListaTodosLogins($sock);
AcessoSQL::Desconectar($sock);

$count=0;
$hint="";
for($i=0; $i<count($logins_usuarios) && $count < 10 && $logins_usuarios != ""; $i++)
{
if($pal != "" && ($pal == substr($logins_usuarios[$i]['login'],0,strlen($pal)) || $pal == substr($logins_usuarios[$i]['email'],0,strlen($pal))))
{
$hint .= "<li><a href=\"#\" onclick=\"javascript:document.getElementById('login').value='".$logins_usuarios[$i]['login']."';document.getElementById('divSugs').style.display = 'none';document.getElementById('tr_sugs').style.display = 'none';\">".$logins_usuarios[$i]['login']."&nbsp;-&nbsp;(".$logins_usuarios[$i]['email'].")</a></li>";
$count++;
}
}

echo json_encode($hint);

?>