<?php

/* ************************************************************************
   VerificaPHPMysql - Retorna verdadeiro se as funcoes do mysql estiverem presentes
   Entrada: void
   Saida: booleano
*/
function VerificaPHPMysql(){
	return function_exists("mysql_connect");
}

/* ************************************************************************
   VerificaRegisterGlobals - Retorna verdadeiro se a diretiva register globals 
   estiver ligada.
   Entrada: void
   Saida: booleano
*/
function VerificaRegisterGlobals(){
	return (bool) ini_get('register_globals');
}

/* ************************************************************************
   CriaBasePrincipal - Cria o banco de dados principal
   Entrada: $dbname - Nome do Banco
   			$dbuser - Usuario
   			$dbpwd - Senha (txt)
   			$dbhost - Host
   			$dbport - Porta
   Saida: true se sucesso
*/
function CriaBasePrincipal($dbname, $dbuser, $dbpwd, $dbhost, $dbport){
	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	if (!$sock){
		return false;
	} else {
		$sql = 'CREATE DATABASE '.$dbname.' DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;';
		return(mysql_query($sql, $sock));
	}
}

/* ************************************************************************
   InicializaBD - Insere os dados no banco de dados principal
   Entrada: $sock - Conexão com o banco de dados principal
   Saida: void
*/
function InicializaBD($sock){
	InsereArqBD($sock, "base_geral.table");
	InsereArqBD($sock, "base_textos.table");
}

/* ************************************************************************
   InsereArqBD - Dado um arquivo de dump do mysql, insere-o no banco do sock, linha por linha
   Entrada: $sock - Conexão com o banco de dados principal
   			$arq - Arquivo com o dump de mysql
   Saida: void
*/
function InsereArqBD($sock, $arq){
	$handle = fopen ($arq, "r");
	$conteudo = fread ($handle, filesize ($arq));
	fclose ($handle);

	$array = explode(";\n", $conteudo);

	foreach ($array as $sql){
		mysql_query($sql, $sock);
	}
}

/* ************************************************************************
   CriaTelEducInc - Cria o teleduc.inc para guardar as informacoes do bd
   Entrada: $dbname - Nome do Banco
   			$dbnamecurso - Prefixo do Nome dos Bancos dos Cursos
   			$dbuser - Usuario
   			$dbpwd - Senha (txt)
   			$dbhost - Host
   			$dbport - Porta
   			$opt - se for false, apenas retorna o teleduc.inc
   Saida: true ou o conteudo
*/
function CriaTelEducInc($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost, $dbport, $opt = true){
	
	
	$conf = '<?php ';
	$conf .= '$_SESSION[\'dbnamebase\']=\''.$dbname.'\';';
	$conf .= '$_SESSION[\'dbnamecurso\']=\''.$dbnamecurso.'\';';
	$conf .= '$_SESSION[\'dbuser\']=\''.$dbuser.'\';';
	$conf .= '$_SESSION[\'dbpassword\']=\''.$dbpwd.'\';';
	$conf .= '$_SESSION[\'dbhost\']=\''.$dbhost.'\';';
	$conf .= '$_SESSION[\'dbport\']=\''.$dbport.'\';';
	$conf .= '$_SESSION[\'dbtmpnamecurso\']=\''.$dbname.'tmp\';';
	$conf .= '$_SESSION[\'dbtmpuser\']=\''.$dbuser.'\';';
	$conf .= '$_SESSION[\'dbtmppassword\']=\''.$dbpwd.'\';';
	$conf .= '$_SESSION[\'dbtmphost\']=\''.$dbhost.'\';';
	$conf .= '$_SESSION[\'dbtmpport\']=\''.$dbport.'\';';
	$conf .= '?>';
	
	if (!$opt)
		return $conf;
	
	$fp = fopen('../cursos/aplic/bibliotecas/teleduc.inc', 'w');
	
	if(!$fp){
		return($conf);
	} else if (!fwrite($fp, $conf)){
		fclose($fp);
		return($conf);
	} else {
		fclose($fp);
		return(true);
	}
}

/* ************************************************************************
   CriaLinkSimbolico - Cria link simbolico
   Entrada: $origem - diretï¿½rio de origem
            $destino - caminho e nome do link
   Saida: true - Se tudo ok, false - se ocorreu algum erro
*/
function CriaLinkSimbolico($origem,$destino)
{
  return(symlink($origem, $destino));
}

/* ************************************************************************
   TestaAnexoArquivos - Tenta simular um upload na pasta, para evitar que reclamem que nao ta anexando.
   Entrada: $arquivos - /home/arquivos/
   Saida: 0 - Anexou e linkou, sucesso.
         -1 - Nao conseguiu anexar.
         -2 - Nao conseguiu linkar.
*/
function TestaAnexoArquivos($arquivos){
	
	$fp = fopen($arquivos.'/teste.txt', 'w');
	$teste = "... testando pasta de arquivos. EOF";
	
	/* Anexa e depois faz link para a pasta diretorio/ */
	if($fp && fwrite($fp, $teste) && fclose($fp)){
		if(is_link('../cursos/diretorio/123.txt')){
			return(0); 
		}
		
		if(symlink($arquivos.'/teste.txt', '../cursos/diretorio/123.txt')){
			return(0);
		} else {
			return(-2);
		}
		
	} else {
		return(-1);
	}
}

/* ************************************************************************
   VerificaExistenciaBD - Se o bd existir, devolve uma conexao, caso contrario
   devolve falso
   Entrada: $dbname - Nome do Banco
   			$dbuser - Usuario
   			$dbpwd - Senha (txt)
   			$dbhost - Host
   			$dbport - Porta
   Saida: $sock ou false
*/
function VerificaExistenciaBD($dbname, $dbuser, $dbpwd, $dbhost, $dbport){
	$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
	if (!$sock) {
    	return false;
	}

	$db = mysql_select_db($dbname, $sock);
	return(($db) ? $sock : false);
}

/* ************************************************************************
   RegistraConfigurações - Registra configuracoes de caminho
   Entrada: $host - http://HOST/
   			$www - http://HOST/WWW/pagina_inical/index.php
   			$arquivos - Pasta dos arquivos
   			$sendmail - Caminho do sendmail
   Saida: booleano
*/
function RegistraConfiguracoes($sock, $host, $www, $arquivos, $sendmail){
	
	/* O que foi pego no formulario, caminho, host, pasta arquivos e sendmail */
	$sql = "REPLACE Config values ('host', '".$host."')";
	mysql_query($sql, $sock);
	$sql = "REPLACE Diretorio values ('raiz_www', '".$www."')";
	mysql_query($sql, $sock);
	$sql = "REPLACE Diretorio values ('Arquivos', '".$arquivos."')";
	mysql_query($sql, $sock);
	$sql = "REPLACE Diretorio values ('sendmail', '".$sendmail."')";
	mysql_query($sql, $sock);
	
	/* Somente as ferramentas fazem anexos, portanto a pasta diretorio pode ser fixa */
	$sql = "REPLACE Diretorio values ('ArquivosWeb', '../../diretorio')";
	mysql_query($sql, $sock);
	
	/* Nova versao: */
	$sql = "REPLACE Config values ('versao', '4.2.2')";
	mysql_query($sql, $sock);
	
	/* Formulario para abertura de cursos */
	$sql = "REPLACE Config values ('curso_form', 'nao')";
	mysql_query($sql, $sock);
	$sql = "REPLACE Config values ('normas', ' ')";
	mysql_query($sql, $sock);
	
	/* Resquicios da instalacao antiga, mantendo por compatibilidade, apenas. */
	$sql = "REPLACE Config values ('extrator', 'nao')";
	mysql_query($sql, $sock);
	$sql = "REPLACE Config values ('listarext', 'sim')";
	mysql_query($sql, $sock);
}

/* ************************************************************************
   RegistraDadosAdmtele - Registra configuracoes do admtele
   Entrada: $admtele_email - Email 
   			$admtele_senha - Senha
   Saida: booleano
*/
function RegistraDadosAdmtele($sock, $adm_nome, $adm_email, $adm_senha){
	$sql = "REPLACE Config values ('adm_nome', '".$adm_nome."')";
	mysql_query($sql, $sock);
	$sql2 = "REPLACE Config values ('adm_email', '".$adm_email."')";
	mysql_query($sql2, $sock);
	$sql3 = "REPLACE Config values ('admtele', '".crypt($adm_senha,"AA")."')";
	mysql_query($sql3, $sock);
}

/* ************************************************************************
   VerificaExistenciaArq - Verifica se o arquivo existe
   Entrada: $arq - /home/arquivos/teleduc.conf
   Saida: $sock ou false
*/
function VerificaExistenciaArq($arq){
	return(file_exists($arq) && filesize($arq) > 0);
}

?>