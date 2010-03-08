<?php

include 'instalacao.inc';

/*
1a Etapa:

Pré-Req: php-mysql
Executar: Informar Nome do Banco, Usuário, Senha e Host tentar criar o banco se não existir e importar o conteúdo para o BD.
Criar o teleduc.conf com as informações acima. Se não for possível pedir ao usuário e dar o conteúdo. */
if (!isset($_POST['etapa'])){
	$etapa = 1;
}


if (!VerificaRegisterGlobals()){
	die("É necessário habilitar o register_globals.");
}

if (!VerificaPHPMysql()){
	die("Não foi encontrado o modulo php-mysql.");
}

/* USER INPUT */
$dbname = "TelEducNova";
$dbnamecurso = "TelEducNova";
$dbuser = "root";
$dbpwd = "root";
$dbhost = "localhost";
$dbport = "3306";

if (!$sock = VerificaExistenciaBD($dbname, $dbuser, $dbpwd, $dbhost, $dbport)){
	if (!CriaBasePrincipal($dbname, $dbuser, $dbpwd, $dbhost, $dbport)){
		die("Não foi possível criar o Banco de Dados: ".mysql_error());
	} else {
		$sock = mysql_connect($dbhost.":".$dbport, $dbuser, $dbpwd);
		mysql_select_db($dbname, $sock);
				
	}
} 

InicializaBD($sock);

if (!VerificaExistenciaArq("../cursos/aplic/bibliotecas/teleduc.inc")){
	$conteudo = CriaTelEducInc($dbname, $dbnamecurso, $dbuser, $dbpwd, $dbhost, $dbport);
	if ($conteudo !== true){
		die("Não foi possível criar o arquivo teleduc.conf, crie manualmente.<br /> Com o conteudo: <br /><br />".str_replace(";", ";<br/>",$conteudo));
		var_dump($conteudo);
	}
}


/*
2a Etapa:
Pré-Req: 1a Etapa
Executar: Escolher pasta para arquivos (?), adivinhar host e caminho pela url.
Configurar os demais diretorios, (rever necessidade de alguns deles). */

/* USER INPUT - Pré-Preenchidas */
$host = "localhost";
$www = "/teleduc4";
$arquivos = "/Users/brunobuccolo/Sites/teleduc4/arquivos";
$sendmail = "/usr/bin/sendmail";

RegistraConfiguracoes($sock, $host, $www, $arquivos, $sendmail);

/*
3a Etapa:
Pré-Req: 2a Etapa
Executar: Pedir ao admin colocar as tarefas do cron, perguntar o email do admtele e a senha para admtele. */

/* USER INPUT */
$admtele_nome = "Bruno Buccolo";
$admtele_email = "admtele@gmail.com";
$admtele_senha = "AA2.FEIabj1C6";

RegistraDadosAdmtele($sock, $admtele_nome, $admtele_email, $admtele_senha);

echo("Não esqueça de configurar o cron.");

/*
4a Etapa:
Pré-Req: 3a Etapa
Executar: Fim? Feedback e botão de entrar. */



?>