<?php
$diretorio_models = "../models/";
$diretorio_ctrlers = "../controllers/";
$diretorio_views = "../views/";
$diretorio_jscss = "../../web-content/js-css/";
$diretorio_imgs  = "../../web-content/imgs/";

require_once $diretorio_models.'geral.inc';
if (isset($_GET['cod_curso']))
	$cod_curso = $_GET['cod_curso'];
//echo $cod_curso;
if (empty($cod_curso)){
	header("Location: ../../");
	exit;
}
//Ver da onde vem isso
/*if ($visitante=="sim")
	$_SESSION['visitante_s']="sim";
else
	$_SESSION['visitante_s']="nao";*/

$cod_usuario_global=AcessoPHP::VerificaAutenticacao($cod_curso);
$sock = AcessoSQL::Conectar("");


if(!Usuarios::PreencheuDadosPessoais($sock))
{
	AcessoSQL::Desconectar($sock);
	//header("Location:{$diretorio_views}/preencher_dados.php?cod_curso=".$cod_curso."&acao=preencherDados&atualizacao=true");
}

AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);


$cod_curso_s=$cod_curso;
Usuarios::MarcaAcesso($sock,$cod_usuario,"");
AcessoSQL::Desconectar($sock);


header('Location:'.$diretorio_ctrlers.'index_curso2.php?cod_curso='.$cod_curso.'&prosseguir=true') 

?>