<?php

include 'instalacao.inc';

/*
1a Etapa:

Pré-Req: php-mysql
Executar: Informar Nome do Banco, Usuário, Senha e Host tentar criar o banco se não existir e importar o conteúdo para o BD.
Criar o teleduc.conf com as informações acima. Se não for possível pedir ao usuário e dar o conteúdo. */

if (!VerificaRegisterGlobals()){
	die("É necessário habilitar o register_globals.");
}

if (!VerificaPHPMysql()){
	die("Não foi encontrado o modulo php-mysql.");
}

/* USER INPUT */
$dbname = "TelEduc";
$dbnamecurso = "TelEducCurso_";
$dbuser = "root";
$dbpwd = "Myqui80n";
$dbhost = "localhost";
$dbport = "3306";

if (!$sock = VerificaExistenciaBD($dbname, $dbuser, $dbpwd, $dbhost, $dbport)){
	if (!CriaBasePrincipal($sock)){
		die("Não foi possível criar o BD, crie manualmente.");
	}
} 

InicializaBD($sock);

if (!CriaTelEducConf($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost)){
	die("Não foi possível criar o arquivo teleduc.conf, crie manualmente.");
}

/*
2a Etapa:
Pré-Req: 1a Etapa
Executar: Escolher pasta para arquivos (?), adivinhar host e caminho pela url.
Configurar os demais diretorios, (rever necessidade de alguns deles). */

/* USER INPUT - Pré-Preenchidas */
$host = "quimera.nied.unicamp.br";
$www = "/~bruno/teleduc4";
$arquivos = "/home/bruno/arquivos";
$sendmail = "/usr/bin/sendmail";

RegistraConfigurações($host, $www, $arquivos, $sendmail);

/*
3a Etapa:
Pré-Req: 2a Etapa
Executar: Pedir ao admin colocar as tarefas do cron, perguntar o email do admtele e a senha para admtele. */

/* USER INPUT */
$admtele_email = "admtele@gmail.com";
$admtele_senha = "AAf2dfh9";

RegistraDadosAdmtele($admtele_email, $admtele_senha);

echo("Não esqueça de configurar o cron.");

/*
4a Etapa:
Pré-Req: 3a Etapa
Executar: Fim? Feedback e botão de entrar. */



?>