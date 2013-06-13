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

			break;

		case 4:
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

   Entrada: $etapa - Numero da etapa em questao
            $erro  - variável que armazena bit a bit os erros ocorridos durante o passo atual.
   Saida:   void
*/
function exibeErro($etapa,$erro) {

	$content_header = "Não foi possível continuar com a instalação.";
	$console = '';
	$content = '';
	
	switch ($etapa) {
		case 1:
			if ($erro & 1) {
				$console .= "<p class=feedbackp>O módulo php-mysql não foi encontrado. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>O módulo php-mysql não foi encontrado.</p>";
				$content .= "<br />";
				$content .= "<p>A instalação desse módulo deve ser feita de acordo com a distribuição utilizada.</p>";
				$content .= "<br />";
			}
			if ($erro & 2) {
				$console .= "<p class=feedbackp>A versão do PHP está acima da requerida. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>O TelEduc necessita da versão <b>5.3.x</b> ou inferior para funcionar corretamente.</p>";
				$content .= "<br />";
				$content .= "<p>Faça o downgrade do seu PHP se desejar continuar com a instalação.</p>";
				$content .= "<br />";
			}
			if ($erro & 4) {
				$console .= "<p class=feedbackp>A diretiva register_globals está desativada. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>A diretiva <b>register_globals</b> deveria estar ligada.</p>";
				$content .= "<br />";
				$content .= "<pre>register_globals = On</pre>";
				$content .= "<br />";
				$content .= "<p>Para isso, edite o arquivo de configuração do seu PHP.</p>";
				$content .= "<br />";
				$content .= "<p>Se você está instalando o TelEduc em uma hospedagem compartilhada, entre em contato com a sua hospedagem para pedir alteração da diretiva register_globals.</p>";
				$content .= "<br/>";
			}
			if ($erro & 8) {
				$console .= "<p class=feedbackp>A diretiva magic_quotes_gpc está desativada. <img src='../cursos/aplic/imgs/errado.png'></p>";

				$content .= "<p>A diretiva <b>magic_quotes_gpc</b> deveria estar ligada.</p>";
				$content .= "<br />";
				$content .= "<pre>magic_quotes_gpc = On</pre>";
				$content .= "<br />";
				$content .= "<p>Para isso, edite o arquivo de configuração do seu PHP.</p>";
				$content .= "<br />";
				$content .= "<p>Se você está instalando o TelEduc em uma hospedagem compartilhada, entre em contato com a sua hospedagem para pedir alteração da diretiva magic_quotes_gpc.</p>";
				$content .= "<br />";
			}
			break;
		case 2:
			if ($erro & 1) {
				$console .= "<p class=feedbackp>Erro de permissão em <b>cursos/aplic/bibliotecas/</b>. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}
			if ($erro & 2) {
				$console .= "<p class=feedbackp>Não foi possível realizar a conexão com o MySQL. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}
			if ($erro & 4) {
				$console .= "<p class=feedbackp>Não foi possível criar o banco de dados principal. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}
			if ($erro & 8) {
				$console .= "<p class=feedbackp>Não foi possível selecionar o banco de dados principal. <img src='../cursos/aplic/imgs/errado.png'></p>";
			}

			if ($erro & 1) {
				$content .= "<p>O usuário do apache, dependendo da distribuição <b>apache</b> ou <b>www-data</b>, não possui as permissões para escrever e ler no seguinte diretório:</p>";
				$content .= "<pre>".str_replace("instalacao", "cursos/aplic/bibliotecas/", getcwd())."</pre>";
				$content .= "<br />";
				$content .= "<p>Para prosseguir com a instalação é necessário configurar o diretório com permissão <b>775 (drwxrwxr-x)</b> para um dos usuários acima citados.</p>";
				$content .= "<br />";
			}
			else {
				$content .= "<p>Verifique os dados inseridos na etapa anterior. (<b>nome do banco, usuário, senha, servidor etc</b>)</p>";
				$content .= "<br />";
				$content .= "<p>Em caso de instalação em banco de dados remoto, verifique se não há algum firewall impedindo a conexão.</p>";
				$content .= "<br />";
				$content .= "<p>Além disso, veja se foi dada a permissão para a conexão remota.</p>";
				$content .= "<br />";
				$content .= "<p>O erro exibido pelo MySQL foi:</p>";
				$content .= "<pre>".mysql_error()."</pre>";
				$content .= "<br />";
			}
			break;
		case 3:
			if ($erro & 1) {
				$console .= "<p class=feedbackp>Não foi possível criar o diretório de arquivos. <img src='../cursos/aplic/imgs/errado.png'></p>";
				
				$content .= "<p>O usuário do apache, dependendo da distribuição <b>apache</b> ou <b>www-data</b>, não possui as permissões para criar o seguinte diretório:</p>";
				$content .= "<pre>".$_SESSION['arquivos']."/</pre>";
				$content .= "<br />";
				$content .= "<p>Isso impedirá o ambiente de fazer o upload de arquivos.</p>";
				$content .= "<br />";
			}
			if ($erro & 2) {
				$console .= "<p class=feedbackp>Não foi possível fazer links para a diretório de arquivos. <img src='../cursos/aplic/imgs/errado.png'></p>";
			
				$content .= "<p>O usuário do apache, dependendo da distribuição <b>apache</b> ou <b>www-data</b>, não possui as permissões para escrever e ler no seguinte diretório:</p>";
				$content .= "<pre>teleduc4/cursos/diretorio/</pre>";
				$content .= "<br />";
				$content .= "<p>O upload de arquivos é feito para uma pasta não acessível pela web com o objetivo de proteger os arquivos de acessos não autorizados.</p>";
				$content .= "<br />";
				$content .= "<p>Quando um usuário autenticado tenta acessar um dos aos anexos, é feito um link da pasta temporária para a pasta de arquivos.</p>";
				$content .= "<br />";
			}
			$content .= "<p>Para prosseguir com a instalação é necessário criar e configurar o diretório com permissão <b>775 (drwxrwxr-x)</b> para um dos usuários acima citados.</p>";
			$content .= "<br />";
			break;
		case 4:
			break;
	}

	$content .= "<div class=formulario>";
		$content .= "<form method='GET' action='index.php'>";
			if ($etapa == 3) {
				$content .= "<input type='button' class='form' style='margin-left: 105px;' value='Voltar' onClick='history.go(-1)'>";
				$content .= "<input type='button' class='formtn' value='Tentar Novamente' onClick='history.go(0)'>";
				$content .= "<input type='button' class='formtn' value='Pular Verificação' onClick=document.location='index.php?etapa=3&bypass_anexo=1'>";
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
