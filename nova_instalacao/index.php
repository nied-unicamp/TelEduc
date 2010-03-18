<?php

include 'instalacao.inc';

/* Para simplificar, a instalacao possui um template, para
 * o qual mandaremos o resultado da etapa atual, o controle de
 * etapas sera feito atraves de uma variavel e posts para a mesma
 * pagina. */

session_start("instalacao_teleduc4");

$content = "";
if (!isset($_POST['etapa'])){
	$etapa = 0;
} else {
	$etapa = $_POST['etapa'];
}

if ($etapa == 0){
	$content_header = "Bem-Vindo à Instalação do TelEduc4!";

	$content .= "<p>Le o Manual e tal..</p>";
	$content .= "<br /><br />";
	$content .= "<form method='POST' action='index.php'>";
	$content .= "<input class='form' type=hidden name=etapa value='1'/><br />";
	$content .= "<input type=submit value='Instalar o TelEduc' class='form'/><br />";

} else if ($etapa == 1){

	/* Erro na Instalacao */
	if (!VerificaRegisterGlobals()){

		$content_header = "Erro na Instalação: A diretiva register_globals está desativada.";
		$content .= "<p>É necessário habilitar a diretiva register_globals. <img src='../cursos/aplic/imgs/errado.png'></p>";
		$content .= "<p>Edite seu /etc/php.ini ou então tutorial dreamhost</p>";
		$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'>";
		$content .= "<input type='button' value='Tentar Novamente' class='formtn' onClick='history.go(0)'>";
		include 'template_instalacao.php';
		exit();

	} else {
		
		$content .= "<p class=feedbackp>A diretiva register_globals está habilitada. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
		
	}

	/* Erro na Instalacao */
	if (!VerificaPHPMysql()){

		$content_header = "Erro na Instalação: Módulo php-mysql não encontrado.";
		$content .= "<p>É necessário instalar o módulo php-mysql. <img src='../cursos/aplic/imgs/errado.png'></p>";
		$content .= "<p>yum install php-mysql genericos.</p>";
		$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'>";
		$content .= "<input type='button' value='Tentar Novamente class='formtn' onClick='history.go(0)'>";
		include 'template_instalacao.php';
		exit();

	} else {
		
		$content .= "<p class=feedbackp>O modulo php-mysql está instalado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	
	}
	
	$content_header = "Etapa 1 de 4 - Banco de Dados e Arquivo de Configuração";

	$content .= "<br /><br />";
	$content .= "<form method='POST' action='index.php'>";
	$content .= "<label class='form' for=dbname>Nome do Banco de Dados Principal</label>";
	$content .= "<input class='form' size=25 type=text name=dbname value='".(isset($_SESSION['dbname']) ? $_SESSION['dbname'] : 'TelEduc4')."'/><br />";
	$content .= "<label class='form' for=dbnamecurso>Prefixo dos Bancos de Dados dos Cursos</label>";
	$content .= "<input class='form' size=25 type=text name=dbnamecurso value='".(isset($_SESSION['dbnamecurso']) ? $_SESSION['dbnamecurso'] : 'TelEduc4Curso_')."'/><br />";
	$content .= "<label class='form' for=dbuser>Usuario do MySQL</label>";
	$content .= "<input class='form' size=25 type=text name=dbuser value='".(isset($_SESSION['dbuser']) ? $_SESSION['dbuser'] : 'usuario')."'/><br />";
	$content .= "<label class='form' for=dbpwd>Senha do MySQL</label>";
	$content .= "<input class='form' size=25 type=password name=dbpwd value='".(isset($_SESSION['dbpwd']) ? $_SESSION['dbpwd'] : 'senha')."'/><br />";
	$content .= "<label class='form' for=dbhost>Host do MySQL (link)</label>";
	$content .= "<input class='form' size=25 type=text name=dbhost value='".(isset($_SESSION['dbhost']) ? $_SESSION['dbhost'] : 'localhost')."'/><br />";
	$content .= "<label class='form' for=dbport>Porta do MySQL</label>";
	$content .= "<input class='form' size=25 type=text name=dbport value='".(isset($_SESSION['dbport']) ? $_SESSION['dbport'] : '3306')."'/><br />";
	$content .= "<input class='form' type=hidden name=etapa value='2'/><br />";
	$content .= "<input type=submit value='Prosseguir' class='form'/><br />";
	$content .= "</form>";

} else if ($etapa == 2){
	
	/* Salva na sessao a informacao do banco de dados */
	$_SESSION['dbname'] = $_POST['dbname'];
	$_SESSION['dbnamecurso'] = $_POST['dbnamecurso'];
	$_SESSION['dbuser']  = $_POST['dbuser'];
	$_SESSION['dbpwd'] = $_POST['dbpwd'];
	$_SESSION['dbhost'] = $_POST['dbhost'];
	$_SESSION['dbport'] = $_POST['dbport'];

	if (!$sock = VerificaExistenciaBD($dbname, $dbuser, $dbpwd, $dbhost, $dbport)){

		/* Erro na Instalacao */
		if (!CriaBasePrincipal($dbname, $dbuser, $dbpwd, $dbhost, $dbport)){
			$content_header = "Erro: Não foi possível criar o banco de dados principal.";
			$content .= "<p>Não foi possível criar o banco de dados. <img src='../cursos/aplic/imgs/errado.png'><p/>";
			$content .= "<p>".mysql_error()."</p>";
			$content .= "<p>Verifique os dados, crie na mão ou de permissao</p>";
			$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'>";
			$content .= "<input type='button' value='Tentar Novamente' class='formtn' onClick='history.go(0)'>";
			include 'template_instalacao.php';
			exit();
		} else {
			$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
			mysql_select_db($dbname, $sock);

		}
	}
	
	$content .= "<p class=feedbackp>O banco de dados foi criado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	InicializaBD($sock);
	
	$content .= "<p class=feedbackp>O banco de dados foi inicializado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	

	if (!VerificaExistenciaArq("../cursos/aplic/bibliotecas/teleduc.inc")){
		$conteudo = CriaTelEducInc($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost, $dbport);

		/* Erro na Instalacao */
		if ($conteudo !== true){
			$content_header = "Erro: Não foi possível criar o teleduc.inc";
			$content .= "<p>Não foi possível criar o arquivo teleduc.inc. <img src='../cursos/aplic/imgs/errado.png'></p>";
			$content .= "<p>Devido a uma permissao blabla... pode criar na mão, please?</p>";
			$content .= "<textarea cols='70' rows='15'>".str_replace(";",";\n",$conteudo)."</textarea>";
			$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'>";
			$content .= "<input type='button' value='Tentar Novamente' class='formtn' onClick='history.go(0)'>";
			include 'template_instalacao.php';
			exit();
		}
	}
	
	
	
	$content .= "<p class=feedbackp>O arquivo de configuração teleduc.inc foi criado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	$content_header = "Etapa 2 de 4 - Host e Diretórios";

	$content .= "<br /><br />";
	$content .= "<form method='POST' action='index.php'>";
	$content .= "<label class='form' for=host>Host do TelEduc</label>";
	$content .= "<input type=text size=25 class='form' name=host value='".$_SERVER['SERVER_NAME']."'/><br />";
	$content .= "<label class='form' for=www>Caminho do TelEduc</label>";
	$content .= "<input type=text size=25 class='form' name=www value='".str_replace("nova_instalacao/index.php", "", $_SERVER['PHP_SELF'])."'/><br />";
	$content .= "<label class='form' for=arquivos>Pasta dos Arquivos</label>";
	$content .= "<input type=text size=25 class='form' name=arquivos value='/home/arquivos'/><br />";
	$content .= "<label class='form' for=sendmail>Caminho do Sendmail</label>";
	$content .= "<input type=text size=25 class='form' name=sendmail value='/usr/sbin/sendmail'/><br />";
	$content .= "<input type=hidden name=etapa value='3'/><br />";
	$content .= "<input type=submit value='Prosseguir' class='form'/><br />";
	$content .= "</form>";

} else if ($etapa == 3){

	$content_header = "Etapa 3 de 4 - Administrador do Ambiente";

	/*
	 2a Etapa:
	 Pré-Req: 1a Etapa
	 Executar: Escolher pasta para arquivos (?), adivinhar host e caminho pela url.
	 Configurar os demais diretorios, (rever necessidade de alguns deles). */

	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	mysql_select_db($dbname, $sock);

	RegistraConfiguracoes($sock, $host, $www, $arquivos, $sendmail);
	
	$content .= "<p class=feedbackp>As configurações de diretorio foram salvas. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";


	$content .= "<br /><br />";
	$content .= "<form method='POST' action='index.php'>";
	$content .= "<label class='form' for=admtele_nome>Nome do Administrador do Ambiente</label>";
	$content .= "<input type=text size=25 class='form' name=admtele_nome value='Nome Sobrenome'/><br />";
	$content .= "<label class='form' for=admtele_email>E-Mail do Administrador do Ambiente</label>";
	$content .= "<input type=text size=25 class='form' name=admtele_email value='nome@email.com'/><br />";
	$content .= "<label class='form' for=admtele_senha>Senha do Administrador do Ambiente</label>";
	$content .= "<input type=password size=25 class='form' name=admtele_senha value='AA2.FEIabj1C6'/><br />";
	$content .= "<input type=hidden name=etapa value='4'/><br />";
	$content .= "<input type=submit value='Prosseguir' class='form'/><br />";
	$content .= "</form>";



} else if ($etapa == 4){

	/*
	 3a Etapa:
	 Pré-Req: 2a Etapa
	 Executar: Pedir ao admin colocar as tarefas do cron, perguntar o email do admtele e a senha para admtele. */
	
	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	mysql_select_db($dbname, $sock);

	RegistraDadosAdmtele($sock, $admtele_nome, $admtele_email, $admtele_senha);
	
	$content .= "<p class=feedbackp>As configurações do administrador do sistema (admtele) foram salvas. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	
	$content_header = "Etapa 4 de 4 - Fim da Instalação";
	
	$content .= "<br /><br />";
	$content .= "<p>OK terminou, só falta gravar as coisas no cron</p>";
	$content .= "<p>Dicas apache e php</p>";
	$content .= "<p>botao pra entrar no teleduc e indicacoes pra apagar o nova_instalacao</p>";

}

include 'template_instalacao.php';

exit();


?>