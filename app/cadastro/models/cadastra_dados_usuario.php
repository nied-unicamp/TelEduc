<?php

$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';

require_once $model_geral.'geral.inc';
require_once $model_geral.'inicial.inc';

$login            = $_POST['login'];
$senha            = crypt($_POST['senha'],"AA");
$nome_usuario     = $_POST['nome_usuario'];
$endereco         = $_POST['endereco'];
$cidade           = $_POST['cidade'];
$estado           = $_POST['estado'];
$pais             = $_POST['pais'];
$email            = $_POST['email'];
$rg               = $_POST['rg'];
$telefone         = $_POST['telefone'];
$sexo             = $_POST['sexo'];
$local            = $_POST['local'];
$profissao        = $_POST['profissao'];
$informacoes      = $_POST['informacoes'];
$cod_escolaridade = $_POST['cod_escolaridade'];
$data_nascimento  = $_POST['data_nascimento'];

$data=Data::Data2UnixTime($data_nascimento);

$sock=AcessoSQL::Conectar("");

$hash = false;
$resultado = $_POST['resultado'];
if($resultado  != $_SESSION['answer']){
	$hash = true;
}

$flag = 0;
if(Inicial::LoginRepetido($sock,$login)){
	$flag=1;
}else if(Inicial::EmailRepetido($sock,$email)){
	$flag=2;
}else if($hash){
	$flag=3;
}else
{
	$cod_usuario=AcessoSQL::RetornaProximoCodigo($sock,"Usuario");

	//Gera uma sequencia aletoria de 20 caracteres
	$sequencia = Inicial::GeraChave(20);

	$query="insert into Usuario (cod_usuario,login,senha,nome,rg,email,telefone,endereco,cidade,estado,pais,data_nasc,sexo,local_trab,profissao,cod_escolaridade,informacoes,data_inscricao,cod_lingua,confirmacao) values (".ConversorTexto::VerificaNumeroQuery($cod_usuario).",'".ConversorTexto::VerificaStringQuery($login)."','".ConversorTexto::VerificaStringQuery($senha)."','".ConversorTexto::VerificaStringQuery($nome_usuario)."','".ConversorTexto::VerificaStringQuery($rg)."','".ConversorTexto::VerificaStringQuery($email)."','".ConversorTexto::VerificaStringQuery($telefone)."','".ConversorTexto::VerificaStringQuery($endereco)."','".ConversorTexto::VerificaStringQuery($cidade)."','".ConversorTexto::VerificaStringQuery($estado)."','".ConversorTexto::VerificaStringQuery($pais)."','".ConversorTexto::VerificaStringQuery($data)."','".ConversorTexto::VerificaStringQuery($sexo)."','".ConversorTexto::VerificaStringQuery($local)."','".ConversorTexto::VerificaStringQuery($profissao)."','".ConversorTexto::VerificaStringQuery($cod_escolaridade)."','".ConversorTexto::VerificaStringQuery($informacoes)."','".time()."',1,'".ConversorTexto::VerificaStringQuery($sequencia)."')";

	$res=AcessoSQL::Enviar($sock,$query);

	/* Notificacao do Usuario via Email ************************************/
	$sock = AcessoSQL::Conectar('');
	$query="select diretorio from Diretorio where item='raiz_www'";
	$res=AcessoSQL::Enviar($sock,$query);
	$raiz_www_linha=AcessoSQL::RetornaLinha($res);
	$raiz_www = $raiz_www_linha[0];
	$host=Inicial::RetornaConfiguracao($sock,"host");

	$parametros_curso = "";
	if (isset($_GET["cod_curso"]) && !empty($_GET["cod_curso"])) {
		$parametros_curso .= "&c=".$_GET["cod_curso"];
		if (isset($_GET["tipo_curso"]) && !empty($_GET["tipo_curso"]))
			$parametros_curso .= "&t=".$_GET["tipo_curso"];
	}

	//87 - ConfirmaÁ„o de cadastro TelEduc
	$assunto = _("ACCOUNT_CONFIRMATION_-2");
	/* 88 - Se vocÍ se cadastrou no ambiente TelEduc, favor confirmar seu e-mail clicando no link abaixo.
	 * 89 - Caso contr·rio, favor desconsiderar esta mensagem.
	 * 78 - Atenciosamente
	 * 3 - Ambiente de AdministraÁ„o do TelEduc
	 * */
	$mensagem = "<p>"._("IF_REGISTERED_CONFIRM_EMAIL_-2")."</p><p><a href='http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."'>http://".$host.$raiz_www."/pagina_inicial/confirmacao.php?u=".$cod_usuario."&s=".$sequencia.$parametros_curso."</a></p><p>"._("OTHERWISE_DESCONSIDER_MESSAGE_-2")."</p><p>"._("SINCERELY_-2").",</p><p> "._("ADM_TELEDUC_ENVIRONMENT_-2")."</p>";
	/*
	 // 74 - Bem-Vindo ao TelEduc!
	$assunto = RetornaFraseDaLista($lista_frases_email, 74);

	// 75 - Para acessar o ambiente, a sua Identifica√ß√£o √©:
	// 76 - e a sua senha √©:
	// 77 - O acesso deve ser feito a partir do endereco:
	// 78 - Atenciosamente
	// 3 - Ambiente de Administra√ß√£o do TelEduc

	$mensagem = "<p>".RetornaFraseDaLista($lista_frases_email, 75)." <big><em><strong>".$login."</strong></em></big> ".RetornaFraseDaLista($lista_frases_email, 76)."<big><em><strong> ".$dadosForm['senha']."</strong></em></big></p><p>".RetornaFraseDaLista($lista_frases_email, 77)." http://$host$raiz_www/</p><br /><p>".RetornaFraseDaLista($lista_frases_email, 78).",</p><p> ".RetornaFraseDaLista($lista_frases_email, 3)."</p>";

	*/

	// 115 - NAO_RESPONDA
	$remetente = _("DO_NOT_REPLY_11")."@".$host;
	$mensagem_html = Email::MontaMsg($host, $raiz_www, '', $mensagem, $assunto, '', $nome_usuario);
	Email::MandaMsg($remetente,$email,$assunto,$mensagem_html, '');

	// Fim da Notificacao
	//$objResponse->call("mostraFeedback", $texto, 'true');
}


AcessoSQL::Desconectar($sock);

echo json_encode($flag);

?>