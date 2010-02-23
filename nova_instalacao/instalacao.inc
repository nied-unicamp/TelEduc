<?php

/* ************************************************************************
   VerificaPHPMysql - Retorna verdadeiro se as funcoes do mysql estiverem presentes
   Entrada: void
   Saida: booleano
*/
function VerificaPHPMysql(){
	return function_exists(mysql_connect);
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
   Saida: booleano
*/
function CriaBasePrincipal($dbname, $dbuser, $dbpwd, $dbhost, $dbport){
	
}

/* ************************************************************************
   InicializaBD - Insere os dados do banco de dados principal
   Entrada: $sock - Conexão com o banco de dados principal
   Saida: booleano
*/
function InicializaBD($sock){
	
}

/* ************************************************************************
   CriaTelEducConf - Cria o teleduc.conf para guardar as informacoes do bd
   Entrada: $dbname - Nome do Banco
   			$dbnamecurso - Prefixo do Nome dos Bancos dos Cursos
   			$dbuser - Usuario
   			$dbpwd - Senha (txt)
   			$dbhost - Host
   			$dbport - Porta
   Saida: booleano
*/
function CriaTelEducConf($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost, $dbport){
	
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
	
}

/* ************************************************************************
   RegistraConfigurações - Registra configuracoes de caminho
   Entrada: $host - http://HOST/
   			$www - http://HOST/WWW/pagina_inical/index.php
   			$arquivos - Pasta dos arquivos
   			$sendmail - Caminho do sendmail
   Saida: booleano
*/
function RegistraConfigurações($host, $www, $arquivos, $sendmail){
	
}

/* ************************************************************************
   RegistraDadosAdmtele - Registra configuracoes do admtele
   Entrada: $admtele_email - Email 
   			$admtele_senha - Senha
   Saida: booleano
*/
function RegistraDadosAdmtele($admtele_email, $admtele_senha){
	
}

?>