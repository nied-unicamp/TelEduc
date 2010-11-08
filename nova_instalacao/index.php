<?php

include 'instalacao.inc';

/* Para simplificar, a instalacao possui um template, para
 * o qual mandaremos o resultado da etapa atual, o controle de
 * etapas sera feito atraves de uma variavel e posts para a mesma
 * pagina. */

session_start("instalacao_teleduc4");

$console = "";
$content = "";
if (!isset($_POST['etapa'])){
	$etapa = 0;
} else {
	$etapa = $_POST['etapa'];
}

/* Monta o console com as mensagens de acordo com as etapas concluidas */
if ($etapa > 1){
	$console .= "<p class=feedbackp>A diretiva register_globals está habilitada. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>O módulo php-mysql está instalado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
}
if ($etapa > 2){
	$console .= "<p class=feedbackp>O banco de dados foi criado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>O banco de dados foi inicializado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	$console .= "<p class=feedbackp>O arquivo de configuração teleduc.inc foi criado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
}
	
if ($etapa > 3){
	$console .= "<p class=feedbackp>As configurações de diretorio foram salvas. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
}	
	

if ($etapa == 0){
	$content_header = "Bem-Vindo à Instalação do TelEduc4!";

	$content .= "<p>Recomendamos a leitura do manual de instalação(link) antes de começar.</p>";
	$content .= "<p>Nele estão cobertos todos os tipos de instalação possíveis para o ambiente.</p>";
	$content .= "<p>Nas próximas etapas serão pedidas informações à respeito do servidor onde está sendo instalado o ambiente.</p>";
	
	$content .= "<br /><br />";
	$content .= "<div class=formulario>";
	$content .= "<form method='POST' action='index.php'>";
	$content .= "<input class='form' type=hidden name=etapa value='1'/><br />";
	$content .= "<input type=submit value='Instalar o TelEduc' class='form'/><br />";
	$content .= "</div>";

} else if ($etapa == 1){

	/* Erro na Instalacao */
	if (!VerificaRegisterGlobals()){

		$content_header = "Não foi possível continuar com a instalação.";
		
		$console .= "<p class=feedbackp>A diretiva register_globals está desativada. <img src='../cursos/aplic/imgs/errado.png'></p>";

		$content .= "<p>Para o funcionamento correto, o TelEduc4 necessita que a diretiva register_globals esteja ligada.</p>";
		$content .= "<p>Para isso, edite o arquivo de configuração do php (/etc/php.ini normalmente) e mude para:</p>";
		$content .= "<p>register_globals = On</p>";

		$content .= "<p>Se você está instalando o TelEduc em uma hospedagem compartilhada, contate o seu host sobre</p>";
		$content .= "<p>a possibilidade da alteração da diretiva register_globals.</p>";
		
		$content .= "<div class=formulario>";
		$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'>";
		$content .= "<input type='button' value='Tentar Novamente' class='form' onClick='history.go(0)'>";
		$content .= "</div>";
		include 'template_instalacao.php';
		exit();

	} else {
		
		$console .= "<p class=feedbackp>A diretiva register_globals está habilitada. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
		
	}

	/* Erro na Instalacao */
	if (!VerificaPHPMysql()){

		$content_header = "Não foi possível continuar com a instalação.";
		
		$console .= "<p class=feedbackp>O módulo php-mysql não foi encontrado. <img src='../cursos/aplic/imgs/errado.png'></p>";
		
		$content .= "<p>A instalação pode ser feita através da linha de comando:</p>";

		$content .= "<p>yum install php-mysql # No caso do Fedora ou</p>";
		$content .= "<p>apt-get install php5-mysql # No caso do Debian</p>";

		$content .= "<p>Ou a ação equivalente na distribuição utilizada no servidor.</p>";
		
		$content .= "<div class=formulario>";
		$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'><br />";
		$content .= "<input type='button' value='Tentar Novamente' class='form' onClick='history.go(0)'>";
		$content .= "</div>";
		include 'template_instalacao.php';
		exit();

	} else {
		
		$console .= "<p class=feedbackp>O módulo php-mysql está instalado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	
	}
	
	$content_header = "Banco de Dados e Arquivo de Configuração <span class=etapa>Etapa 1 de 4</span>";

	$content .= "<p>O TelEduc 4 utiliza um banco de dados principal para manter as configurações, dados dos cursos e dos usuáios, e</p>";
	$content .= "<p>também um banco de dados para cada curso. O nome dos bancos de dados para os curso é composto da seguinte maneira:</p>";
	$content .= "<p>BancoDeDadosCurso+Cod_Curso, por exemplo, TelEduc4Curso1 seria o banco de dados do curso 1.</p>";

	$content .= "<p>Para poder acessar os bancos de dados, é necessário que o ambiente grave estas configurações em um arquivo:</p>";
	$content .= "<p>cursos/aplic/bibliotecas/teleduc.inc, que será criado a seguir. Certifique-se que as permissões desse arquivo</p>";
	$content .= "<p>estão corretas antes de colocar o ambiente em produção.</p>";
	
	$content .= "<br /><br />";
	$content .= "<div class=formulario>";
	$content .= "<form method='POST' action='index.php'>";
	$content .= "<label class='form' for=dbname>Nome do Banco de Dados Principal</label>";
	$content .= "<input class='form' size=25 type=text name=dbname value='".(isset($_SESSION['dbname']) ? $_SESSION['dbname'] : 'TelEduc4')."'/><br />";
	$content .= "<label class='form' for=dbnamecurso>Prefixo dos Bancos de Dados dos Cursos</label>";
	$content .= "<input class='form' size=25 type=text name=dbnamecurso value='".(isset($_SESSION['dbnamecurso']) ? $_SESSION['dbnamecurso'] : 'TelEduc4Curso_')."'/><br />";
	$content .= "<label class='form' for=dbuser>Usuario do MySQL</label>";
	$content .= "<input class='form' size=25 type=text name=dbuser value='".(isset($_SESSION['dbuser']) ? $_SESSION['dbuser'] : 'usuario')."'/><br />";
	$content .= "<label class='form' for=dbpwd>Senha do MySQL</label>";
	$content .= "<input class='form' size=25 type=password name=dbpwd value='".(isset($_SESSION['dbpwd']) ? $_SESSION['dbpwd'] : 'senha')."'/><br />";
	$content .= "<label class='form' for=dbhost>Servidor do MySQL</label>";
	$content .= "<input class='form' size=25 type=text name=dbhost value='".(isset($_SESSION['dbhost']) ? $_SESSION['dbhost'] : 'localhost')."'/><br />";
	$content .= "<label class='form' for=dbport>Porta do MySQL</label>";
	$content .= "<input class='form' size=25 type=text name=dbport value='".(isset($_SESSION['dbport']) ? $_SESSION['dbport'] : '3306')."'/><br />";
	$content .= "<input class='form' type=hidden name=etapa value='2'/><br />";
	$content .= "<input type=submit value='Prosseguir' class='form'/><br />";
	$content .= "</form>";
	$content .= "</div>";

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
			
			$content_header = "Não foi possível continuar com a instalação.";
		
			$console .= "<p class=feedbackp>Não foi possível criar o banco de dados principal. <img src='../cursos/aplic/imgs/errado.png'></p>";
			
			$content .= "<p>Não foi possível criar o banco de dados principal. Verifique o nome de usuário e senha do banco de dados.</p>";
			$content .= "<p>Em caso de instalação com banco de dados remoto, verifique se não há algum firewall impedindo a conexão,</p>";
			$content .= "<p>e se foi dada a permissão correta para a conexão remota. O erro exibido pelo MySQL foi:</p>";
			$content .= "<pre>".mysql_error()."</pre>";
			
			$content .= "<div class=formulario>";
			$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'>";
			$content .= "<input type='button' value='Tentar Novamente' class='formtn' onClick='history.go(0)'>";
			$content .= "</div>";
			
			include 'template_instalacao.php';
			exit();
		} else {
			$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
			mysql_select_db($dbname, $sock);

		}
	}
	
	$console .= "<p class=feedbackp>O banco de dados foi criado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	InicializaBD($sock);
	
	$console .= "<p class=feedbackp>O banco de dados foi inicializado com sucesso. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";
	

	if (!VerificaExistenciaArq("../cursos/aplic/bibliotecas/teleduc.inc")){
		$conteudo = CriaTelEducInc($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost, $dbport);

		/* Erro na Instalacao */
		if ($conteudo !== true){
			
			$content_header = "Não foi possível continuar com a instalação.";
		
			$console .= "<p class=feedbackp>Não foi possível criar o arquivo de configuração. <img src='../cursos/aplic/imgs/errado.png'></p>";
			
			$content .= "<p>Não foi possível criar o arquivo de configuração teleduc.inc <img src='../cursos/aplic/imgs/errado.png'></p>";
			$content .= "<p>Corrija as permissões do diretório cursos/aplic/bibliotecas ou então crie manualmente</p>";
			$content .= "<p>o arquivo teleduc.inc na pasta cursos/aplic/bibliotecas com o seguinte conteúdo:</p>";
			
			$content .= "<div class=formulario>";
			$content .= "<textarea cols='70' rows='15'>".str_replace(";",";\n",$conteudo)."</textarea>";
			$content .= "<input type='button' value='Voltar' class='form' onClick='history.go(-1)'>";
			$content .= "<input type='button' value='Tentar Novamente' class='formtn' onClick='history.go(0)'>";
			$content .= "</div>";
			include 'template_instalacao.php';
			exit();
		}
	}
	
	
	
	$console .= "<p class=feedbackp>O arquivo de configuração teleduc.inc foi criado. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	$content_header = "Servidor e Diretórios <span class=etapa>Etapa 2 de 4</span>";

	$content .= "<p>Nesta etapa será necessário informar o nome do servidor e o caminho do teleduc,</p>";
	$content .= "<p>que podem ser verificados no próprio endereço: http://nome-do-servidor/caminho/do/teleduc/instalacao</p>";
	$content .= "<p>O TelEduc precisa de uma pasta para armazenar os arquivos dos usuários, certifique-se de</p>";
	$content .= "<p>que o servidor web tem as permissões necessárias para escrever nessa pasta.</p>";
	$content .= "<p>O caminho para o executável do sendmail é que permite que o TelEduc envie emails</p>";
	$content .= "<p>para o correio externo dos usuários.</p>";

	$content .= "<br /><br />";
	$content .= "<div class=formulario>";
	$content .= "<form method='POST' action='index.php'>";
	$content .= "<label class='form' for=host>Servidor do TelEduc</label>";
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
	$content .= "</div>";

} else if ($etapa == 3){

	$content_header = "Administrador do Ambiente <span class=etapa>Etapa 3 de 4</span>";

	/*
	 2a Etapa:
	 Pré-Req: 1a Etapa
	 Executar: Escolher pasta para arquivos (?), adivinhar host e caminho pela url.
	 Configurar os demais diretorios, (rever necessidade de alguns deles). */

	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	mysql_select_db($dbname, $sock);

	RegistraConfiguracoes($sock, $host, $www, $arquivos, $sendmail);
	
	$console .= "<p class=feedbackp>As configurações de diretorio foram salvas. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	$content .= "<p>A conta do Administrador do Ambiente (nome de usuário: admtele) será utilizada para gerenciar a abertura</p>";
	$content .= "<p>de cursos e o ambiente em geral. O Administrador tem acesso irrestrito a todos</p>";
	$content .= "<p>os cursos, pode inscrever e remover usuários do Ambiente entre outras funcionalidades.</p>";

	$content .= "<br /><br />";
	$content .= "<div class=formulario>";
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
	$content .= "</div>"; 



} else if ($etapa == 4){

	/*
	 3a Etapa:
	 Pré-Req: 2a Etapa
	 Executar: Pedir ao admin colocar as tarefas do cron, perguntar o email do admtele e a senha para admtele. */
	
	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	mysql_select_db($dbname, $sock);

	RegistraDadosAdmtele($sock, $admtele_nome, $admtele_email, $admtele_senha);
	
	$console .= "<p class=feedbackp>As configurações do administrador do sistema (admtele) foram salvas. <img src='../cursos/aplic/imgs/certo.png' alt='com sucesso'></p>";

	
	$content_header = "Fim da Instalação <span class=etapa>Etapa 4 de 4</span>";
	
	$content .= "<br /><br />";
	$content .= "<p>OK terminou, só falta gravar as coisas no cron</p>";
	$content .= "<p>Dicas apache e php</p>";
	$content .= "<p>botao pra entrar no teleduc e indicacoes pra apagar o nova_instalacao</p>";

}

include 'template_instalacao.php';

exit();


?>