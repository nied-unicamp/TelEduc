<?php
$diretorio_models = "../models/";
$diretorio_ctrlers = "../controllers/";
$diretorio_views = "../views/";
$diretorio_jscss = "../../web-content/js-css/";
$diretorio_imgs  = "../../web-content/imgs/";

require_once $diretorio_models.'geral.inc';
require_once $diretorio_models.'agenda.inc';
$cod_curso = $_GET['cod_curso'];
$acao = $_GET['acao'];
$cod_usuario_global=AcessoPHP::VerificaAutenticacao($cod_curso);

$sock=AcessoSQL::Conectar("");

$lista_frases=Linguas::RetornaListaDeFrases($sock,1);

$diretorio_arquivos=Agenda::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Agenda::RetornaDiretorio($sock,'ArquivosWeb');

AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

$dir_name = "agenda";
$dir_item_temp=Agenda::CriaLinkVisualizar($sock,$dir_name,$cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

/* ação = Criar Nova Agenda - origem = ver_editar.php */
if ($acao=="criarAgenda")
{
	
	$atualizacao = "true";

	$cod_item = Agenda::IniciaCriacao($sock, $cod_usuario, $cod_curso, $diretorio_temp, $novo_titulo, $novo_texto);
	if($cod_item == -1) //erro na criacao! algum parametro da func. esta vazio
	{
		echo "chegou aqui\n";
		$atualizacao="false";
		AcessoSQL::Desconectar($sock);
		header("Location:agenda.php?cod_curso=".$cod_curso."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
		exit();
	}

	AcessoSQL::Desconectar($sock);
	header("Location:../views/ver_linha_agenda.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=".$origem."&acao=".$acao."&atualizacao=".$atualizacao);
}
?>