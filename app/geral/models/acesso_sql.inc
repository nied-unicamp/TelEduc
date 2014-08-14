<?php
require_once "teleduc.inc";
class AcessoSQL {

	static function Conectar($cod_curso, $opt = NULL)
	{
		//   global $_SESSION['dbnamecurso'],
		//          $_SESSION['dbtmpnamecurso'];
	
		// 2Session
		//   $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];
	
		if (!isset($_SESSION['dbnamebase']) || !isset($_SESSION['dbtmpnamecurso']))
			AcessoSQL::LeConfig();
		if (!isset($cod_curso) || $cod_curso == "")
		{
			// Conecta-se �base tempor�ia sem selecionar base de dados.
			if (isset($opt) && ($opt == TMPDB))
				$base = "";
			else
				$base = $_SESSION['dbnamebase']; // Conecta-se �base do AMBIENTE
		}
		else
		{
			if (isset($opt) && ($opt == TMPDB))
				$base = $_SESSION['dbtmpnamecurso'].$cod_curso;
			else
				$base = $_SESSION['dbnamecurso'].$cod_curso;
		}
		return(AcessoSQL::ConectarDB($base, $opt));
	}
	
	static function Desconectar($sock)
	{
		if(mysqli_ping($sock))
		if (! mysqli_close($sock))
			die("ERRO : mysqli_close = ".mysqli_errno($sock)."<br />\n");
	}
	
	static function ConectarDB($base, $opt = NULL)
	{
		//   global $_SESSION['dbhostname'],    $_SESSION['dbport'],    $_SESSION['dbuser'],    $_SESSION['dbpassword'],
		//          $_SESSION['dbtmphost'], $$_SESSION['dbtmpport'], $_SESSION['dbtmpuser'], $_SESSION['dbtmppassword'];
	
		if (!isset($_SESSION['dbuser']))
			AcessoSQL::LeConfig();
	
		// Conecta-se �base tempor�ia
		if (isset($opt) && ($opt == TMPDB))
		{
			$host = $_SESSION['dbtmphost'];
			$port = $_SESSION['dbtmpport'];
			$user = $_SESSION['dbtmpuser'];
			$password = $_SESSION['dbtmppassword'];
		}
		else
		{
			$host = $_SESSION['dbhost'];
			$port = $_SESSION['dbport'];
			$user = $_SESSION['dbuser'];
			$password = $_SESSION['dbpassword'];
		}
	
		if (!($sock = mysqli_connect($host, $user, $password,$base)))
			die("Erro de SQL: mysqli_connect(): ".mysqli_connect_errno()."<br />\n");
	
			return($sock);
	}
	
	
	static function Enviar($sock, $query)
	{
		if (!($ms = mysqli_query($sock, $query)))
		{
			$enter = "%OD%0A";
	
			if (eregi("^SELECT",$query))
				$palavra = "pesquisa";
			else
				$palavra = "atualiza&ccedil;&atilde;o";
	
			$ERRMSG = ereg_replace("\"", "", $ERRMSG = mysql_error());
			$ERRMSG = ereg_replace("'", "", $ERRMSG);
	
			// Entra no if se for exibir sem a tela.php
	
			$msgerro = "                      Ocorreu um erro na ".$palavra." solicitada\n";
			$msgerro .= "                      <br />\n";
			$msgerro .= "                      <b>Erro na consulta : </b>".$ERRMSG."<br />\n";
			$msgerro .= "                      <b>Consulta enviada : </b>".$query."<br />\n";
	
	
			ImprimeErroSemTela("de SQL", $msgerro, "S");
		}
		else
			return($ms); 
		//return mysql_query($query,$sock);
	}
	
	static function LeConfig()
	{
	
  /* Verifica se o arquivo de autorizacao existe, se ele pode ser aberto e se nao esta em branco */
  if (file_exists(".auth"))
  	$fp = fopen (".auth", "r");
  else 
  	die("AcessoSQL::LeConfig(): Diret&oacute;rio sem arquivo de autoriza&ccedil;&atilde;o.");
  
  if (!$fp)
    die("AcessoSQL::LeConfig(): Diret&oacute;rio sem arquivo de autoriza&ccedil;&atilde;o.");

  $cam=fgets($fp,256);
  if ($cam == "") die("AcessoSQL::LeConfig(): Arquivo de autoriza&ccedil;&atilde;o em branco. Se voc� est� vendo isso depois da instala��o do ambiente, talvez tenha esquecido de rodar os scripts configure e configure_end. Reveja o manual.");
  $cam1=explode("\n",$cam);
  $caminho=$cam1[0];
  fclose($fp);
  
  print_r($cam);
  
  /* Verifica se o teleduc.conf existe, e se ele pode ser aberto */
  if (file_exists($caminho."teleduc.conf"))
  	$fp=fopen($caminho."teleduc.conf","r");
  else 
  	die("AcessoSQL::LeConfig(): Arquivo de configura&ccedil;&atilde;o do TelEduc n&atilde;o encontrado.");
  
  
  if (!$fp)
    die("AcessoSQL::LeConfig(): Arquivo de configura&ccedil;&atilde;o do TelEduc n&atilde;o encontrado.");

  while (!feof($fp))
  {

    $linha=fgets($fp,256);

    $parte=explode("=",$linha, 2);
    @$conteudo=explode("\n",$parte[1]);
    
    /* Para os campos criticos, verifica se eles nao estao vazios */
    if ($parte[0]=="dbuser")
      ($conteudo[0] != "") ? $_SESSION['dbuser'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informa��o no teleduc.conf");
    if ($parte[0]=="dbpassword")
      ($conteudo[0] != "") ? $_SESSION['dbpassword'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informa��o no teleduc.conf");
    if ($parte[0]=="dbnamebase")
      ($conteudo[0] != "") ? $_SESSION['dbnamebase'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informa��o no teleduc.conf");
    if ($parte[0]=="dbnamecurso")
      ($conteudo[0] != "") ? $_SESSION['dbnamecurso'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informa��o no teleduc.conf");  
    if ($parte[0]=="dbhost")
      ($conteudo[0] != "") ? $_SESSION['dbhost'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informa��o no teleduc.conf");
    if ($parte[0]=="dbport")
      ($conteudo[0] != "") ? $_SESSION['dbport'] = trim($conteudo[0]) : die("AcessoSQL::LeConfig(): Falta informa��o no teleduc.conf");
    if ($parte[0]=="dbtmpuser")
      $_SESSION['dbtmpuser']=trim($conteudo[0]);
    if ($parte[0]=="dbtmppassword")
      $_SESSION['dbtmppassword']=trim($conteudo[0]);
    if ($parte[0]=="dbtmpnamecurso")
      $_SESSION['dbtmpnamecurso']=trim($conteudo[0]);
    if ($parte[0]=="dbtmphost")
      $_SESSION['dbtmphost']=trim($conteudo[0]);
    if ($parte[0]=="dbtmpport")
      $_SESSION['dbtmpport']=trim($conteudo[0]);
  }
  fclose($fp);
}
	static function RetornaLinha($id)
	{
		return(mysqli_fetch_array($id));
	}
	
	/* **********************************************************
	 RetornaUltimoAutoIncrement - Retorna o valor gerado a um campo
	auto_increment, ap� a ltima inser�o.
	Entrada: $sock - Sock da base a ser consultada
	Saida: inteiro
	*/
	static function RetornaUltimoAutoIncrement($sock)
	{
		return(mysqli_insert_id($sock));
	}
	
	/* **********************************************************
	 RetornaNumLinhas - Retorna o nmero de linhas do resultado
	Entrada: $id - apontador de resultado da consulta
	Saida: nmero de linhas
	*/
	static function RetornaNumLinhas($id)
	{
		return(mysqli_num_rows($id));
	}
	
	
	/* **********************************************************
	 RetornaArrayLinhas - Retorna um array com todas as linhas
	Entrada: $id - apontador de resultado da consulta
	Saida: array das linhas
	*/
	static function RetornaArrayLinhas($id, $result_type = MYSQLI_BOTH)
	{
		$num=AcessoSQL::RetornaNumLinhas($id);
		$cont=0;
		$ar=array();
		while ($num>0)
		{
			$num--;
			$ar[$cont]=mysqli_fetch_array($id, $result_type);
			$cont++;
		}
		return($ar);
	}
	
	/* **********************************************************
	 RetornaNumCampos- Retorna nmero de campos do resultado da consulta
	Entrada: $id - apontador de resultado da consulta
	Saida: nmero de campos
	*/
	static function RetornaNumCampos($id)
	{
		$num=mysqli_num_fields($id);
		return ($num);
	}
	
	 /* **********************************************************
	   RetornaCampos- Retorna array com os campos resultados da pesquisa
	   Entrada: $id - apontador de resultado da consulta
	   Saida: array com os campos
	*/
	static function RetornaCampos($id)
	{
	
	  $linha = "";
	  unset($linha);
	
	
	  $num=mysqli_num_fields($id);
	  $c=0;
	  while ($c<$num)
	  {
	    $field=mysqli_fetch_field($id);
	    $linha[$c]=$field->name;
	    $c++;    
	  }  
	  return ($linha);
	}
	
	static function RetornaListaCampos($sock, $tabela)
	{
		$ar="";
		unset($ar);
	
		$query = "select * from ".$tabela." limit 1";
	
		$id = Enviar($sock, $query);
	
		$num = mysqli_num_fields($id);
		$i = 0;
		while ($i < $num)
		{
			$field = mysqli_fetch_field($id);
			$ar[$i] = array("name" => $field->name,
					"table" => $field->table,
					"max_length" => $field->max_length,
					"not_null" => $field->not_null,
					"primary_key" => $field->primary_key,
					"unique_key" => $field->unique_key,
					"multiple_key" => $field->multiple_key,
					"numeric" => $field->numeric,
					"blob" => $field->blob,
					"type" => $field->type,
					"unsigned" => $field->unsigned,
					"zerofill" => $field->zerofill);
			$i++;
		}
		return ($ar);
	}
	
	/* *******************************************
	 MudarDB - Muda a base de dados acessada
	Entrada: $base - nome da base de dados
	Saida: socket do servidor de base de dados, se ok.
	interrompe o c�igo e envia alerta caso contrario
	*/
	static function MudarDB($sock, $cod_curso="", $opt="")
	{
		//   global $_SESSION['dbnamecurso'], $_SESSION['dbtmpnamecurso'];
	
		// 2Session
		//   $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];
		if ($cod_curso!="")
		{
			// Conecta-se a base tempor�ia
			if (isset($opt) && ($opt == TMPDB))
				$base = $_SESSION['dbtmpnamecurso'].$cod_curso;
			else
				$base = $_SESSION['dbnamecurso'].$cod_curso;
		}
		else
			$base=$_SESSION['dbnamebase'];
	
		if (! mysqli_select_db($sock, $base))
			die("Erro de SQL: mysql_select_db = ".mysql_error()."<br />\n");
		else
			return($sock);
	
	}
	
	/* *********************************************************************
	 RetornaProximoCodigo- Retorna o pr�imo nmero a ser usado nessa base
	Entrada: $sock - sock da base em uso
	$tabela - nome da tabela a ser retirada a sequencia
	Saida: pr�imo numero da sequencia
	*/
	static function RetornaProximoCodigo($sock,$tabela,$cod_usuario_tmp='')
	{
		//   global $cod_usuario;
	
		if ($cod_usuario_tmp=="") {
			if (isset($_SESSION['cod_usuario']) && !empty($_SESSION['cod_usuario']))
				$cod_usuario_tmp = $_SESSION['cod_usuario'];
			else
				$cod_usuario_tmp = 0;
		}
	
		$data=time();
		$query="insert into ".$tabela."_sequencia (cod_usuario,data) values (".$cod_usuario_tmp.",".$data.")";
	
		if (!($res=mysqli_query($sock, $query)))
		{
			/* Criar tabela e repetir */
			$query = "CREATE TABLE ".$tabela."_sequencia (cod int PRIMARY KEY AUTO_INCREMENT,cod_usuario INT,data INT)";
			$res=AcessoSQL::Enviar($sock,$query);
			$query="insert into ".$tabela."_sequencia (cod_usuario,data) values (".$cod_usuario_tmp.",".$data.")";
			$res=AcessoSQL::Enviar($sock,$query);
			$query="select cod from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
			$res=AcessoSQL::Enviar($sock,$query);
			$linha=AcessoSQL::RetornaLinha($res);
			$query="delete from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
			$res=AcessoSQL::Enviar($sock,$query);
			return ($linha[0]);
		}
		else
		{
			$query="select cod from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
			$res=AcessoSQL::Enviar($sock,$query);
			$linha=AcessoSQL::RetornaLinha($res);
			$query="delete from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
			$res=AcessoSQL::Enviar($sock,$query);
			return ($linha[0]);
		}
	}
	



	
}