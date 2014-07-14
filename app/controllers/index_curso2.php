<?php
$diretorio_models = "../models/";
$diretorio_ctrlers = "../controllers/";
$diretorio_views = "../views/";
$diretorio_jscss = "../../web-content/js-css/";
$diretorio_imgs  = "../../web-content/imgs/";

require_once $diretorio_models.'geral.inc';
if (isset($_GET['cod_curso']))
	$cod_curso = $_GET['cod_curso'];
$cod_usuario_global=AcessoPHP::VerificaAutenticacao($cod_curso);

$sock=AcessoSQL::Conectar($cod_curso);

$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

if ($_SESSION['cod_curso_s']!=$cod_curso)
{
	$cod_curso_s=$cod_curso;
	Usuarios::MarcaAcesso($sock,$cod_usuario,"");
}


if (isset($_GET['ativar_visao_aluno']) && $_GET['ativar_visao_aluno']=="sim")
{
	$_SESSION['visao_aluno_s']=true;
}
if (isset($_GET['desativar_visao_aluno']) && $_GET['desativar_visao_aluno']=="sim")
{
	$_SESSION['visao_aluno_s']=false;
}

if (Cursos::CompletarDadosCurso($sock,$cod_curso) && Usuarios::ECoordenador($sock,$cod_curso,$cod_usuario))
{
	
	AcessoSQL::Desconectar($sock);
	echo("<script language=javascript>\n");
	echo("  document.location='".$diretorio_views."alterar_dados_curso.php?cod_curso=".$cod_curso."';\n");
	echo("</script>\n");
}
else
{ 
	AcessoSQL::Desconectar($sock);
	if((!((isset($_GET['prosseguir'])) && ($_GET['prosseguir'] == "true"))) && ((isset($_SERVER['HTTP_REFERER'])) && ($_SERVER['HTTP_REFERER'] != ""))){
		{ echo "oi"; header("Location:".$_SERVER['HTTP_REFERER']); }

	}else{
		header("Location:".$diretorio_views."agenda.php?cod_curso=".$cod_curso);
	}
}
?>