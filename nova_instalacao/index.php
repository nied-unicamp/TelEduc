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

	/* Erro na Instalacao */
	if (!VerificaRegisterGlobals()){

		$content_header = "Erro: Diretiva register_globals desativada.";
		$content .= "<p>É necessário habilitar o register_globals.</p>";
		$content .= "<p>Edite seu /etc/php.ini ou então tutorial dreamhost</p>";
		include 'template_instalacao.php';
		exit();

	} else {
		
		$content .= "<p>A diretiva register_globals está habilitado conforme necessário... OK</p>";
		
	}

	/* Erro na Instalacao */
	if (!VerificaPHPMysql()){

		$content_header = "Erro: Modulo php-mysql não encontrado.";
		$content .= "<p>É necessário instalar o módulo php-mysql.</p>";
		$content .= "<p>yum install php-mysql genericos.</p>";
		include 'template_instalacao.php';
		exit();

	} else {
		
		$content .= "<p>O modulo php-mysql está instalado conforme necessário... OK</p>";
	
	}
	
	$content_header = "Etapa 0 - Banco de Dados e Arquivo de Configuração:";

	$content .= "<form method='POST' action='index.php'>";
	$content .= "<input type=text name=dbname value='TelEduc4'/><br />";
	$content .= "<input type=text name=dbnamecurso value='TelEduc4Curso'/><br />";
	$content .= "<input type=text name=dbuser value='usuario'/><br />";
	$content .= "<input type=password name=dbpwd value='senha'/><br />";
	$content .= "<input type=text name=dbhost value='localhost'/><br />";
	$content .= "<input type=text name=dbport value='3306'/><br />";
	$content .= "<input type=hidden name=etapa value='1'/><br />";
	$content .= "<input type=submit value='Enviar'/><br />";
	$content .= "</form>";

} else if ($etapa == 1){
	
	/* Salva na sessao a informacao do banco de dados */
	$_SESSION['dbname'] = $dbname;
	$_SESSION['dbuser']  = $dbuser;
	$_SESSION['dbpwd'] = $dbpwd;
	$_SESSION['dbhost'] = $dbhost; 
	$_SESSION['dbport'] = $dbport;

	if (!$sock = VerificaExistenciaBD($dbname, $dbuser, $dbpwd, $dbhost, $dbport)){

		/* Erro na Instalacao */
		if (!CriaBasePrincipal($dbname, $dbuser, $dbpwd, $dbhost, $dbport)){
			$content_header = "Erro: Não foi possível criar o banco de dados principal";
			$content .= "Não foi possível criar o Banco de Dados: ".mysql_error();
			$content .= "Verifique os dados, crie na mão";
			include 'template_instalacao.php';
			exit();
		} else {
			$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
			mysql_select_db($dbname, $sock);

		}
	}
	
	$content .= "<p>O banco de dados foi criado com sucesso... OK</p>";

	InicializaBD($sock);
	
	$content .= "<p>O banco de dados foi inicializado com sucesso... OK</p>";
	

	if (!VerificaExistenciaArq("../cursos/aplic/bibliotecas/teleduc.inc")){
		$conteudo = CriaTelEducInc($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost, $dbport);

		/* Erro na Instalacao */
		if ($conteudo !== true){
			$content_header = "Erro: Não foi possível criar o teleduc.inc";
			$content .= "Não foi possível criar o arquivo teleduc.inc, crie manualmente.<br /> Com o conteudo: <br /><br />";
			$content .= "<textarea cols='70' rows='15'>".str_replace(";",";\n",$conteudo)."</textarea>";
			include 'template_instalacao.php';
			exit();
		}
	}
	
	$content .= "<p>O arquivo de configuração teleduc.inc foi criado com sucesso... OK</p>";

	$content_header = "Etapa 1 - Host e Diretórios:";

	$content .= "<form method='POST' action='index.php'>";
	$content .= "<input type=text name=host value='www.dominio.com.br'/><br />";
	$content .= "<input type=text name=www value='/ead/teleduc4'/><br />";
	$content .= "<input type=text name=arquivos value='/home/arquivos'/><br />";
	$content .= "<input type=text name=sendmail value='/usr/sbin/sendmail'/><br />";
	$content .= "<input type=hidden name=etapa value='2'/><br />";
	$content .= "<input type=submit value='Enviar'/><br />";
	$content .= "</form>";

} else if ($etapa == 2){

	$content_header = "Etapa 2 - Configurações do Administrador do Ambiente:";

	/*
	 2a Etapa:
	 Pré-Req: 1a Etapa
	 Executar: Escolher pasta para arquivos (?), adivinhar host e caminho pela url.
	 Configurar os demais diretorios, (rever necessidade de alguns deles). */

	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	mysql_select_db($dbname, $sock);

	RegistraConfiguracoes($sock, $host, $www, $arquivos, $sendmail);
	
	$content .= "<p>As configurações de diretorio foram salvas com sucesso... OK</p>";


	$content .= "<form method='POST' action='index.php'>";
	$content .= "<input type=text name=admtele_nome value='Nome Sobrenome'/><br />";
	$content .= "<input type=text name=admtele_email value='nome@email.com'/><br />";
	$content .= "<input type=password name=admtele_senha value='AA2.FEIabj1C6'/><br />";
	$content .= "<input type=hidden name=etapa value='3'/><br />";
	$content .= "<input type=submit value='Enviar'/><br />";
	$content .= "</form>";



} else if ($etapa == 3){

	/*
	 3a Etapa:
	 Pré-Req: 2a Etapa
	 Executar: Pedir ao admin colocar as tarefas do cron, perguntar o email do admtele e a senha para admtele. */
	
	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	mysql_select_db($dbname, $sock);

	RegistraDadosAdmtele($sock, $admtele_nome, $admtele_email, $admtele_senha);
	
	$content .= "<p>As configurações do administrador do sistema (admtele) foram salvas com sucesso... OK</p>";

	$content_header = "Etapa 3 - Fim ~!:";
	$content = "OK terminou, só falta gravar as coisas no cron";

}

include 'template_instalacao.php';

exit();


?>