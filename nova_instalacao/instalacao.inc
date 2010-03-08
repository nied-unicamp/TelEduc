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
		die("Não foi possível conectar ao MySQL: ".mysql_error());
	} else {
		$sql = 'CREATE DATABASE '.$dbname.' DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;';
		return(mysql_query($sql, $sock));
	}
}

/* ************************************************************************
   InicializaBD - Insere os dados do banco de dados principal
   Entrada: $sock - Conexão com o banco de dados principal
   Saida: booleano
*/
function InicializaBD($sock){
	$arq = "base_geral.table";
	$handle = fopen ($arq, "r");
	$conteudo = fread ($handle, filesize ($arq));
	fclose ($handle);

	$array = explode(";\n", $conteudo);

	foreach ($array as $sql){
		mysql_query($sql, $sock);
	}
}

/* ************************************************************************
   CriaTelEducConf - Cria o teleduc.conf para guardar as informacoes do bd
   Entrada: $dbname - Nome do Banco
   			$dbnamecurso - Prefixo do Nome dos Bancos dos Cursos
   			$dbuser - Usuario
   			$dbpwd - Senha (txt)
   			$dbhost - Host
   			$dbport - Porta
   Saida: true ou o conteudo
*/
function CriaTelEducInc($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost, $dbport){
	$fp = fopen('../cursos/aplic/bibliotecas/teleduc.inc', 'w');
	
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
	
	echo($conf);
	
	if (!fwrite($fp, $conf)){
		fclose($fp);
		return($conf);
	} else {
		fclose($fp);
		return(true);
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
    	die("Não foi possível conectar ao MySQL: ".mysql_error());
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
	$sql = "REPLACE Config values ('host', '".$host."')";
	mysql_query($sql, $sock);
	$sql2 = "REPLACE Diretorio values ('raiz_www', '".$www."')";
	mysql_query($sql2, $sock);
	$sql3 = "REPLACE Diretorio values ('Arquivos', '".$arquivos."')";
	mysql_query($sql3, $sock);
	$sql4 = "REPLACE Diretorio values ('sendmail', '".$sendmail."')";
	mysql_query($sql4, $sock);
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
	$sql3 = "REPLACE Config values ('admtele', '".$adm_senha."')";
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