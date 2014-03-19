<?php
/* ************************************************************************
   verificaRequisitos - Verifica se os pre-requisitos das etapas estao devidamente instalados.

   Entrada: $etapa - Numero da etapa em questao
            $flag  - variável que armazena bit a bit os erros ocorridos durante o passo atual.
   Saida:   $flag  - Variável que armazena bit a bit os erros ocorridos durante o passo atual.
*/
function &verificaRequisitos($etapa) {
	$flag = 0;
	switch ($etapa) {
		case 1:
			if (!function_exists('mysql_connect')) {
				$flag |= 1;
			}
			$versao = explode('.',phpversion());
			if ($versao[0] == 5 && $versao[1] > 3) {
				$flag |= 2;
			}
			if (!(bool) ini_get('register_globals')) {
				$flag |= 4;
			}
			if (!(bool) ini_get('magic_quotes_gpc')) {
				$flag |= 8;
			}

			break;

		case 2:
			clearstatcache();

			if (!is_writable('../cursos/aplic/bibliotecas/')) {
				$flag |= 1;
			}

			// Vamos exibir apenas um erro de mysql de cada vez, pois a função mysql_error retorna o erro do último comando de sql.
			if (!$sock = @mysql_connect($_SESSION['dbhost'].':'.$_SESSION['dbport'], $_SESSION['dbuser'], $_SESSION['dbpwd'])) {
				$flag |= 2;
				break;
			}
			if (!$query = @mysql_query('CREATE DATABASE '.$_SESSION['dbname'].' DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;', $sock)) {
				$flag |= 4;
				break;
			}
			if (!$db = @mysql_select_db($_SESSION['dbname'], $sock)) {
				$flag |= 8;
				break;
			}

			// Se não deu erro de mysql, podemos escrever o arquivo de configuração.
			$comando = 'mysql -h ' .$_SESSION['dbhost']. ' -u ' .$_SESSION['dbuser']. ' -p' .$_SESSION['dbpwd']. ' ' .$_SESSION['dbname']. ' < base_geral.sql';
			exec($comando, $saida = array(), $sucesso);
			$comando = 'mysql -h ' .$_SESSION['dbhost']. ' -u ' .$_SESSION['dbuser']. ' -p' .$_SESSION['dbpwd']. ' ' .$_SESSION['dbname']. ' < textos.sql';
			exec($comando, $saida = array(), $sucesso);
			
			$conteudo = "<?php\n";
			$conteudo .= '$_SESSION[\'dbnamebase\']=\''.$_SESSION['dbname']."';\n";
			$conteudo .= '$_SESSION[\'dbnamecurso\']=\''.$_SESSION['dbnamecurso']."';\n";
			$conteudo .= '$_SESSION[\'dbuser\']=\''.$_SESSION['dbuser']."';\n";
			$conteudo .= '$_SESSION[\'dbpassword\']=\''.$_SESSION['dbpwd']."';\n";
			$conteudo .= '$_SESSION[\'dbhost\']=\''.$_SESSION['dbhost']."';\n";
			$conteudo .= '$_SESSION[\'dbport\']=\''.$_SESSION['dbport']."';\n";
			$conteudo .= '$_SESSION[\'dbtmpnamecurso\']=\''.$_SESSION['dbname']."tmp';\n";
			$conteudo .= '$_SESSION[\'dbtmpuser\']=\''.$_SESSION['dbuser']."';\n";
			$conteudo .= '$_SESSION[\'dbtmppassword\']=\''.$_SESSION['dbpwd']."';\n";
			$conteudo .= '$_SESSION[\'dbtmphost\']=\''.$_SESSION['dbhost']."';\n";
			$conteudo .= '$_SESSION[\'dbtmpport\']=\''.$_SESSION['dbport']."';\n";
			$conteudo .= "?>\n";

			$arquivo = fopen('../cursos/aplic/bibliotecas/teleduc.inc', "w");
			fwrite($arquivo, $conteudo);
			fclose($arquivo);

			break;

		case 3:
			clearstatcache();

			if (!is_dir($_SESSION['arquivos']) && !is_writable($_SESSION['arquivos'].'/')) {
				if (!mkdir($_SESSION['arquivos'], 0775)) {
					$flag |= 1;
					break;
				}
			}

			// Se o diretório existe, pode ser modificado e temos permissão,
			// tentamos criar um link simbólico para ele para testar.
			$arquivo = fopen($_SESSION['arquivos'].'/teste.txt', "w");
			fwrite($arquivo, '... testando pasta de arquivos. EOF\n');
			fclose($arquivo);

			if (!is_link('../cursos/diretorio/123.txt')) {
				if (!symlink($_SESSION['arquivos'].'/teste.txt', '../cursos/diretorio/123.txt')) {
					$flag |= 2;
					break;
				}
			}

			// Verifica se o nome de host é válido. 
			if (preg_match("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/", $_SESSION['host']) === 0) {
				$flag |= 4;
				break;
			}

			// Se conseguimos criar um link simbólico
			// de teste, então o apagamos.
			unlink($_SESSION['arquivos'].'/teste.txt');
			unlink('../cursos/diretorio/123.txt');

			$sock = mysql_quick_connect();
	
			mysql_query("TRUNCATE Config;");
			mysql_query("TRUNCATE Diretorio;");

			mysql_query("INSERT INTO Config VALUES ('versao', '".VERSAO."');", $sock);
			mysql_query("INSERT INTO Config VALUES ('listarext', 'sim');", $sock);
			mysql_query("INSERT INTO Config VALUES ('host', '".$_SESSION['host']."');", $sock);
			mysql_query("INSERT INTO Config VALUES ('curso_form', 'nao');", $sock);
			mysql_query("INSERT INTO Config VALUES ('normas', ' ');", $sock);
			mysql_query("INSERT INTO Config VALUES ('extrator', 'nao');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('raiz_www', '".$_SESSION['www']."');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('Arquivos', '".$_SESSION['arquivos']."');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('sendmail', '".$_SESSION['sendmail']."');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('ArquivosWeb', '../../diretorio');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('Extracao', '');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('tar', '');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('mysqldump', '');", $sock);
			mysql_query("INSERT INTO Diretorio VALUES ('mimetypes', '');", $sock);
			mysql_query("INSERT INTO Extracao VALUES ('diretorio', '{Cursos_extraidos.codigo}');", $sock);
			mysql_query("INSERT INTO Extracao VALUES ('select_query','select codigo from Cursos_extraidos where codigo = \']codigo[\'');", $sock);
			mysql_query("INSERT INTO Extracao VALUES ('compactar','S');", $sock);

			break;

		case 4:

			// Verifica se o email é válido.
			if (preg_match("/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/", $_SESSION['admtele_email'], $_SESSION['admtele_email']) === 0) {
				$flag |= 1;
				break;
			}

			$sock = mysql_quick_connect();
			
			mysql_query("TRUNCATE Usuario;");
			
			mysql_query("INSERT INTO Config VALUES ('adm_nome', '".$_SESSION['admtele_nome']."');", $sock);
			mysql_query("INSERT INTO Config VALUES ('adm_email', '".$_SESSION['admtele_email']."');", $sock);
			mysql_query("INSERT INTO Config VALUES ('admtele', '".crypt($_SESSION['admtele_senha'],"AA")."');", $sock);

			mysql_query("INSERT INTO Usuario VALUES (-1,'admtele','".crypt($_SESSION['admtele_senha'],"AA")."','".$_SESSION['admtele_nome']."','','".$_SESSION['admtele_email']."','','','','','',0,'M','','',3,'',0,1,NULL);", $sock);

			break;
	}

	return $flag;
}

/* ************************************************************************
   exibeErro - Exibe a mensagem de erro / falha na instalacao de acordo com a etapa e flag de erro.

   Entrada: $etapa - Numero da etapa em questão
            $erro  - variável que armazena bit a bit os erros ocorridos durante o passo atual.
   Saida:   void
*/
function exibeErro($etapa,$erro) {

	$content_header = "N&atilde;o foi poss&iacute;vel continuar com a instala&ccedil;&atilde;o.";
	$console = '';
	$content = '';
	
	switch ($etapa) {
		case 1:
			if ($erro & 1) {
				$console .= "<p class=\"feedbackp\">O m&oacute;dulo php-mysql n&atilde;o foi encontrado. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>O m&oacute;dulo php-mysql n&atilde;o foi encontrado.</p>";
				$content .= "<br />";
				$content .= "<p>A instala&ccedil;&atilde;o desse m&oacute;dulo deve ser feita de acordo com a distribui&ccedil;&atilde;o utilizada.</p>";
				$content .= "<br />";
			}
			if ($erro & 2) {
				$console .= "<p class=\"feedbackp\">A vers&atilde;o do PHP est&aacute; acima da requerida. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>O TelEduc necessita da vers&atilde;o <b>5.3.x</b> ou inferior para funcionar corretamente.</p>";
				$content .= "<br />";
				$content .= "<p>Fa&ccedil;a o downgrade do seu PHP se desejar continuar com a instala&ccedil;&atilde;o.</p>";
				$content .= "<br />";
			}
			if ($erro & 4) {
				$console .= "<p class='feedbackp'>A diretiva register_globals est&aacute; desativada. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>A diretiva <b>register_globals</b> deveria estar ligada.</p>";
				$content .= "<br />";
				$content .= "<pre>register_globals = On</pre>";
				$content .= "<br />";
				$content .= "<p>Para isso, edite o arquivo de configura&ccedil;&atilde;o do seu PHP.</p>";
				$content .= "<br />";
				$content .= "<p>Se voc&ecirc; est&aacute; instalando o TelEduc em uma hospedagem compartilhada, entre em contato com a sua hospedagem para pedir altera&ccedil;&atilde;o da diretiva register_globals.</p>";
				$content .= "<br/>";
			}
			if ($erro & 8) {
				$console .= "<p class='feedbackp'>A diretiva magic_quotes_gpc est&aacute; desativada. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>A diretiva <b>magic_quotes_gpc</b> deveria estar ligada.</p>";
				$content .= "<br />";
				$content .= "<pre>magic_quotes_gpc = On</pre>";
				$content .= "<br />";
				$content .= "<p>Para isso, edite o arquivo de configura&ccedil;&atilde;o do seu PHP.</p>";
				$content .= "<br />";
				$content .= "<p>Se voc&ecirc; est&aacute; instalando o TelEduc em uma hospedagem compartilhada, entre em contato com a sua hospedagem para pedir altera&ccedil;&atilde;o da diretiva magic_quotes_gpc.</p>";
				$content .= "<br />";
			}
			break;
		case 2:
			if ($erro & 1) {
				$console .= "<p class='feedbackp'>Erro de permiss&atilde;o em <b>cursos/aplic/bibliotecas/</b>. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}
			if ($erro & 2) {
				$console .= "<p class='feedbackp'>N&atilde;o foi poss&iacute;vel realizar a conex&atilde;o com o MySQL. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}
			if ($erro & 4) {
				$console .= "<p class='feedbackp'>N&atilde;o foi poss&iacute;­vel criar o banco de dados principal. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}
			if ($erro & 8) {
				$console .= "<p class='feedbackp'>N&atilde;o foi poss&iacute;vel selecionar o banco de dados principal. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}

			if ($erro & 1) {
				$content .= "<p>O usu&aacute;rio do apache, dependendo da distribui&ccedil;&atilde;o <b>apache</b> ou <b>www-data</b>, n&atilde;o possui as permiss&otilde;es para escrever e ler no seguinte diret&oacute;rio:</p>";
				$content .= "<pre>".str_replace("instalacao", "cursos/aplic/bibliotecas/", getcwd())."</pre>";
				$content .= "<br />";
				$content .= "<p>Para prosseguir com a instala&ccedil;&atilde;o &eacute; necess&aacute;rio configurar o diret&oacute;rio com permiss&atilde;o <b>775 (drwxrwxr-x)</b> para um dos usu&aacute;rios acima citados.</p>";
				$content .= "<br />";
			}
			else {
				$content .= "<p>Verifique os dados inseridos na etapa anterior. (<b>nome do banco, usu&aacute;rio, senha, servidor etc</b>)</p>";
				$content .= "<br />";
				$content .= "<p>Em caso de instala&ccedil;&atilde;o em banco de dados remoto, verifique se n&atilde;o h&aacute; algum firewall impedindo a conex&atilde;o.</p>";
				$content .= "<br />";
				$content .= "<p>Al&eacute;m disso, veja se foi dada a permiss&atilde;o para a conex&atilde;o remota.</p>";
				$content .= "<br />";
				$content .= "<p>O erro exibido pelo MySQL foi:</p>";
				$content .= "<pre>".mysql_error()."</pre>";
				$content .= "<br />";
			}
			break;
		case 3:
			if ($erro & 1) {
				$console .= "<p class='feedbackp'>N&atilde;o foi poss&iacute;­vel criar o diret&oacute;rio de arquivos. <img src='../cursos/aplic/imgs/errado.png'></p>";
				
				$content .= "<p>O usu&aacute;rio do apache, dependendo da distribui&ccedil;&atilde;o <b>apache</b> ou <b>www-data</b>, n&atilde;o possui as permiss&otilde;es para criar o seguinte diret&oacute;rio:</p>";
				$content .= "<pre>".$_SESSION['arquivos']."/</pre>";
				$content .= "<br />";
				$content .= "<p>Isso impedir&aacute; o ambiente de fazer o upload de arquivos.</p>";
				$content .= "<br />";

				$content .= "<p>Para prosseguir com a instala&ccedil;&atilde;o &eacute; necess&aacute;rio criar e configurar o diret&oacute;rio com permiss&atilde;o <b>775 (drwxrwxr-x)</b> para um dos usu&aacute;rios acima citados.</p>";
				$content .= "<br />";
			}
			if ($erro & 2) {
				$console .= "<p class='feedbackp'>N&atilde;o foi poss&iacute;­vel fazer links para a diret&oacute;rio de arquivos. <img src='../cursos/aplic/imgs/errado.png'></p>";
			
				$content .= "<p>O usu&aacute;rio do apache, dependendo da distribui&ccedil;&atilde;o <b>apache</b> ou <b>www-data</b>, n&atilde;o possui as permiss&otilde;es para escrever e ler no seguinte diret&oacute;rio:</p>";
				$content .= "<pre>".$_SESSION['www']."/cursos/diretorio/</pre>";
				$content .= "<br />";
				$content .= "<p>O upload de arquivos &eacute; feito para uma pasta n&atilde;o acess&iacute;­vel pela web com o objetivo de proteger os arquivos de acessos n&atilde;o autorizados.</p>";
				$content .= "<br />";
				$content .= "<p>Quando um usu&aacute;rio autenticado tenta acessar um dos aos anexos, &eacute; feito um link da pasta tempor&aacute;ria para a pasta de arquivos.</p>";
				$content .= "<br />";

				$content .= "<p>Para prosseguir com a instala&ccedil;&atilde;o &eacute; necess&aacute;rio criar e configurar o diret&oacute;rio com permiss&atilde;o <b>775 (drwxrwxr-x)</b> para um dos usu&aacute;rios acima citados.</p>";
				$content .= "<br />";
			}
			if ($erro & 4) {
				$console .= "<p class='feedbackp'>O nome do servidor &eacute; inv&aacute;lido. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>Para prosseguir com a instala&ccedil;&atilde;o, digite um nome de host v&aacute;lido.</p>";
				$content .= "<br />";
			}

			break;
		case 4:
			if ($erro & 1) {
				$console .= "<p class='feedbackp'>O endere&ccedil;o de email &eacute; inv&aacute;lido. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>Para prosseguir com a instala&ccedil;&atilde;o, digite um email v&aacute;lido.</p>";
				$content .= "<br />";
			}
			break;
	}

	$content .= "<div class=\"formulario\">";
		$content .= "<form method='GET' action='index.php'>";
			if ($etapa == 3) {
				$content .= "<input type='button' class='form' style='margin-left: 105px;' value='Voltar' onClick='history.go(-1)'>";
				$content .= "<input type='button' class='formtn' value='Tentar Novamente' onClick='history.go(0)'>";
				$content .= "<input type='button' class='formtn' value='Pular Verifica&ccedil;&atilde;o' onClick=document.location='index.php?etapa=3&bypass_anexo=1'>";
			}
			else {
				$content .= "<input type='button' class='form' style='margin-left: 195px;' value='Voltar' onClick='history.go(-1)'>";
				$content .= "<input type='button' class='formtn' value='Tentar Novamente' onClick='history.go(0)'>";
			}
		$content .= "</form>";
	$content .= "</div>";

	include 'template_instalacao.php';
	exit();
}

// For faster database connection use this "macro" that returns $sock on success and FALSE on failure.
function mysql_quick_connect() {
	$sock = mysql_connect($_SESSION['dbhost'].':'.$_SESSION['dbport'], $_SESSION['dbuser'], $_SESSION['dbpwd']);
	mysql_select_db($_SESSION['dbname'], $sock);
	return ($sock ? $sock : false);
}
?>
