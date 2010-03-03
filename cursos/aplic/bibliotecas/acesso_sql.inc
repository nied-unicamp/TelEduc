<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/acesso_sql.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
    Copyright (C) 2001  NIED - Unicamp

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    You could contact us through the following addresses:

    Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bibliotecas/acesso_sql.inc
  ========================================================== */

if (!defined("TMPDB"))
  define("TMPDB", "tmp");



/**************************************************************************
  EnviaEmailComErro - envia um e-mail com os dados do erro para o e-mail do 
                      suporte
*/
function EnviaEmailComErro($erro, $cod){

  $server_remetente = $_SERVER['SERVER_NAME'];
  $headers = "From: <teleduc@".$server_remetente.">\n";
  $headers.= "X-Mailer: PHP/".phpversion()."\n";
  $headers .= "MIME-Version: 1.0\n"; 
  $headers.= "Content-Type: text/html; charset=ISO-8859-1\n";
  $headers.= "Content-Transfer-Encoding: 7bit\n";

  $destinatario = "Suporte TelEduc <curso@nied.unicamp.br>";

  $assunto = "[TelEduc Error] ".$cod;

  $mensagem = "<html>\n";
  $mensagem.= "\t<head>\n";
  $mensagem.= "\t\t<title>".$assunto."</title>\n";
  $mensagem.= "\t</head>\n";
  $mensagem.= "\t<body>\n";
  $mensagem.= $erro;
  $mensagem.= "\t\t<strong>Referer:</strong> ".@$_SERVER['HTTP_REFERER']."<br />";
  $mensagem.= "\t\t<strong>Server:</strong> ".$_SERVER['SERVER_NAME']."<br />";
  $mensagem.= "\t\t<strong>URL:</strong> ".$_SERVER['REQUEST_URI']."<br />";
  $mensagem.= "\t</body>\n";
  $mensagem.= "</html>\n";

  return mail($destinatario, $assunto, $mensagem, $headers);

}

/* **********************************************************************
    ImprimeErroSemTela - Para imprimir erros em que tela.php
    (menu, header, etc..) nÃ£o pode ser carregado.
*/
function ImprimeErroSemTela($tipo_erro, $erro, $prefixo)
{
    $cod = rand(0, 268435454);
    $cod_hex = dechex($cod);
    $cod_fim = strtoupper($prefixo.str_pad($cod_hex, 7, "0", STR_PAD_LEFT));

    /* Obtï¿½ a raiz_www */
    $sock = Conectar("");
    $query = "select diretorio from Diretorio where item = 'raiz_www'";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $raiz_www = $linha[0];
    Desconectar($sock);

    $caminho_jscss = $raiz_www."/cursos/aplic/js-css";

    echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
    echo("<html lang=\"pt\">\n");
    echo("  <head>\n");
    echo("    <title>TelEduc - Erro</title>\n");
    echo("    <link href=\"".$caminho_jscss."/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
    echo("    <script type=\"text/javascript\">\n");
    echo("      function Iniciar(){\n");
    echo("        document.getElementById('detalhes').style.display='none';\n");
    echo("      }\n");
    echo("      var status='none';\n");
    echo("      function changeStatus(){\n");
    echo("        if(status=='') status = 'none';\n");
    echo("        else status = '';\n");
    echo("        document.getElementById('detalhes').style.display=status;\n");
    echo("      }\n");
    echo("    </script>\n");
    echo("  </head>\n");
    echo("  <body onload=\"Iniciar();\">\n");
    echo("    <br />\n");
    echo("    <table id=\"container\" style=\"width:700px;\">");
    echo("      <tr>\n");
    echo("        <td colspan=\"2\" id=\"conteudo\" valign=\"top\">\n");
    echo("          <h4>Erro ".$tipo_erro."</h4>\n");
    echo("          <br />\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                  <tr class=\"head\">\n");
    echo("                    <td width=\"32%\" class=\"alLeft\">\n");
    echo("                      Ocorreu um Erro\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td>\n");
    echo("                      <p>Infelizmente o TelEduc comportou-se de forma inesperada. Houve um erro ".$tipo_erro." que impediu que a navega&ccedil;&atilde;o prosseguisse.</p>\n");
    echo("                      <p>As informa&ccedil;&otilde;es foram enviadas automaticamente &agrave; Equipe do TelEduc para que as provid&ecirc;ncias necess&aacute;rias sejam tomadas.</p>\n");
    echo("                      <p>Caso queira saber mais detalhes sobre o erro, entre em contato com a Equipe do TelEduc e informe o c&oacute;digo <strong>".$cod_fim."</strong>.</p>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td style=\"text-align:right;\">\n");
    echo("                      <span class=\"link\" onclick=\"changeStatus();\">+ Detalhes do Erro</span>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr id=\"detalhes\">\n");
    echo("                    <td class=\"alLeft\" style=\"padding: 10px 50px 10px 50px;\">\n");
    echo("                      ".$erro."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("          <span class=\"btsNavBottom\">\n");
    echo("            <a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a>\n");
    echo("          </span>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td valign=\"bottom\" height=\"80\"><a href=\"http://www.nied.unicamp.br\"><img src=\"../imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right:8px;\" /></a><a href=\"http://www.ic.unicamp.br\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right:6px;\" /></a><a href=\"http://www.unicamp.br\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" border=\"0\" /></a></td>\n");
    echo("        <td valign=\"bottom\" id=\"rodape\">2006  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>");
    echo("</html>");

    EnviaEmailComErro($erro, $cod_fim);

    exit();

}

/* ************************************************************************
   LeConfig - DEPRECIADA: futuras versoes do teleduc vao extrair as informacoes
   			  do teleduc.inc localizado na pasta de bibliotecas, sem a necessidade
   			  do teleduc.conf.
   			  
   			  Le arquivo de configuraï¿½o a partir do arquivo teleduc.conf
              Todo diretorio deve conter o arquivo ".auth", com o caminho
              para teleduc.con
   Sem entrada ou saida. Afeta globais de uso de acesso_sql.inc
*/
function LeConfig()
{
	
  /* Verifica se o arquivo de autorizacao existe, se ele pode ser aberto e se nao esta em branco */
  if (file_exists(".auth"))
  	$fp = fopen (".auth", "r");
  else 
  	die("LeConfig(): Diret&oacute;rio sem arquivo de autoriza&ccedil;&atilde;o.");
  
  if (!$fp)
    die("LeConfig(): Diret&oacute;rio sem arquivo de autoriza&ccedil;&atilde;o.");

  $cam=fgets($fp,256);
  if ($cam == "") die("LeConfig(): Arquivo de autoriza&ccedil;&atilde;o em branco. Se você está vendo isso depois da instalação do ambiente, talvez tenha esquecido de rodar os scripts configure e configure_end. Reveja o manual.");
  $cam1=explode("\n",$cam);
  $caminho=$cam1[0];
  fclose($fp);
  
  /* Verifica se o teleduc.conf existe, e se ele pode ser aberto */
  if (file_exists($caminho."teleduc.conf"))
  	$fp=fopen($caminho."teleduc.conf","r");
  else 
  	die("LeConfig(): Arquivo de configura&ccedil;&atilde;o do TelEduc n&atilde;o encontrado.");
  
  
  if (!$fp)
    die("LeConfig(): Arquivo de configura&ccedil;&atilde;o do TelEduc n&atilde;o encontrado.");

  while (!feof($fp))
  {

    $linha=fgets($fp,256);

    $parte=explode("=",$linha, 2);
    @$conteudo=explode("\n",$parte[1]);
    
    /* Para os campos criticos, verifica se eles nao estao vazios */
    if ($parte[0]=="dbuser")
      ($conteudo[0] != "") ? $_SESSION['dbuser'] = trim($conteudo[0]) : die("LeConfig(): Falta informação no teleduc.conf");
    if ($parte[0]=="dbpassword")
      ($conteudo[0] != "") ? $_SESSION['dbpassword'] = trim($conteudo[0]) : die("LeConfig(): Falta informação no teleduc.conf");
    if ($parte[0]=="dbnamebase")
      ($conteudo[0] != "") ? $_SESSION['dbnamebase'] = trim($conteudo[0]) : die("LeConfig(): Falta informação no teleduc.conf");
    if ($parte[0]=="dbnamecurso")
      ($conteudo[0] != "") ? $_SESSION['dbnamecurso'] = trim($conteudo[0]) : die("LeConfig(): Falta informação no teleduc.conf");  
    if ($parte[0]=="dbhost")
      ($conteudo[0] != "") ? $_SESSION['dbhost'] = trim($conteudo[0]) : die("LeConfig(): Falta informação no teleduc.conf");
    if ($parte[0]=="dbport")
      ($conteudo[0] != "") ? $_SESSION['dbport'] = trim($conteudo[0]) : die("LeConfig(): Falta informação no teleduc.conf");
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


/* *******************************************
   RetornaDBName - Retorna o Nome da base de dados
     a qual estï¿½conectada.
   Entrada: $sock - socket da base de dados.
   Saida: nome da base de dados a qual estï¿½conectada.
*/
function RetornaDBName($sock)
{
  $query = "select DATABASE()";
  $id = Enviar($sock, $query);
  $linha = RetornaLinha($id);
  return ($linha[0]);

}

/* *******************************************
   ConectarDB - Conecta a base de dados pedida
   Entrada: $base - nome da base de dados
   Saida: socket do servidor de base de dados, se ok.
          interrompe o cï¿½igo e envia alerta caso contrario
*/
function ConectarDB($base, $opt = NULL)
{
//   global $_SESSION['dbhostname'],    $_SESSION['dbport'],    $_SESSION['dbuser'],    $_SESSION['dbpassword'],
//          $_SESSION['dbtmphost'], $$_SESSION['dbtmpport'], $_SESSION['dbtmpuser'], $_SESSION['dbtmppassword'];

  if (!isset($_SESSION['dbuser']))
    LeConfig();

  // Conecta-se ï¿½base temporï¿½ia
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

  if (! $sock = mysql_connect($host.":".$port, $user, $password))
    die("Erro de SQL: mysql_connect(): ".mysql_error()."<br />\n");

  else
  {
    if ((isset($base)) && ($base != ""))
    {
      if (!mysql_select_db($base, $sock))
        die("Erro de SQL: mysql_select_db(): ".mysql_error()."<br />\n");
      else
        return($sock);
    }
    else
      return($sock);
  }
}

/* *******************************************
   MudarDB - Muda a base de dados acessada
   Entrada: $base - nome da base de dados
   Saida: socket do servidor de base de dados, se ok.
          interrompe o cï¿½igo e envia alerta caso contrario
*/
function MudarDB($sock, $cod_curso="", $opt="")
{
//   global $_SESSION['dbnamecurso'], $_SESSION['dbtmpnamecurso'];

   // 2Session
//   $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];

  if ($cod_curso!="")
  {
    // Conecta-se a base temporï¿½ia
    if (isset($opt) && ($opt == TMPDB))
      $base = $_SESSION['dbtmpnamecurso'].$cod_curso;
    else
      $base = $_SESSION['dbnamecurso'].$cod_curso;
  }
  else
    $base=$_SESSION['dbnamebase'];

  if (! mysql_select_db($base, $sock))
    die("Erro de SQL: mysql_select_db = ".mysql_error()."<br />\n");
  else
    return($sock);

}


/* *****************************************************
   Conectar - Funï¿½o de conexï¿½ as bases de uso continuo
   Entradas: $cod_curso - para acessar a base do curso correspondente
                        - "" para base geral
   Saida: socket do servidor de base de dados, se ok.
          interrompe o cï¿½igo e envia alerta caso contrario
*/  
function Conectar($cod_curso, $opt = NULL)
{
//   global $_SESSION['dbnamecurso'],
//          $_SESSION['dbtmpnamecurso'];

   // 2Session
//   $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];

  if (!isset($_SESSION['dbnamebase']) || !isset($_SESSION['dbtmpnamecurso']))
    LeConfig();
  if (!isset($cod_curso) || $cod_curso == "")
  {
    // Conecta-se ï¿½base temporï¿½ia sem selecionar base de dados.
    if (isset($opt) && ($opt == TMPDB))
      $base = "";
    else
      $base = $_SESSION['dbnamebase']; // Conecta-se ï¿½base do AMBIENTE
  }
  else
  {
    if (isset($opt) && ($opt == TMPDB))
      $base = $_SESSION['dbtmpnamecurso'].$cod_curso;
    else
      $base = $_SESSION['dbnamecurso'].$cod_curso;
  }

  return(ConectarDB($base, $opt));
}

/* **************************************************
   Desconectar - Funï¿½o de desconexï¿½ as bases em uso
   Entradas: $sock - Sock da base a ser liberado
*/  
function Desconectar($sock)
{
	if(mysql_ping($sock))
	  if (! mysql_close($sock))
	    die("ERRO : mysql_close = ".mysql_error()."<br />\n");
}

/* **********************************************
   Enviar - Funï¿½o de execuï¿½o da query requerida
   Entradas: $sock - Sock da base a ser consultada
             $query - consulta a ser realizada
   Saida: apontador de resultado da consulta
*/  
function Enviar($sock, $query)
{
  if (!($ms = mysql_query($query, $sock)))
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
}

/* **********************************************
   ErroSQL - Funï¿½o que retorna o cï¿½igo do ltimo
     erro SQL.
   Entradas: $sock - Sock da base consultada
   Saida: cï¿½igo do erro SQL
*/
function ErroSQL($sock)
{
  return(mysql_errno());
}



/* **********************************************************
   RetornaUltimoAutoIncrement - Retorna o valor gerado a um campo
     auto_increment, apï¿½ a ltima inserï¿½o.
   Entrada: $sock - Sock da base a ser consultada
   Saida: inteiro
*/
function RetornaUltimoAutoIncrement($sock)
{
  return(mysql_insert_id($sock));
}

/* **********************************************************
   RetornaNumLinhas - Retorna o nmero de linhas do resultado
   Entrada: $id - apontador de resultado da consulta
   Saida: nmero de linhas          
*/
function RetornaNumLinhas($id)
{
  return(mysql_num_rows($id));
}

/* **********************************************************
   RetornaLinha - Retorna a linha corrente do resultado
   Entrada: $id - apontador de resultado da consulta
   Saida: array com todos os campos da linha
*/
function RetornaLinha($id)
{
  return(mysql_fetch_array($id));
}

/* **********************************************************
   RetornaArrayLinhas - Retorna um array com todas as linhas
   Entrada: $id - apontador de resultado da consulta
   Saida: array das linhas 
*/
function RetornaArrayLinhas($id)
{
  $num=RetornaNumLinhas($id);
  $cont=0;
  $ar=array();
  while ($num>0)
  {
    $num--;
    $ar[$cont]=mysql_fetch_array($id); 
    $cont++;
  }
  return($ar);
}

/* **********************************************************
   RetornaNumCampos- Retorna nmero de campos do resultado da consulta
   Entrada: $id - apontador de resultado da consulta
   Saida: nmero de campos
*/
function RetornaNumCampos($id)
{
  $num=mysql_num_fields($id);
  return ($num);
}

/* **********************************************************
   RetornaCampos- Retorna array com os campos resultados da pesquisa
   Entrada: $id - apontador de resultado da consulta
   Saida: array com os campos
*/
function RetornaCampos($id)
{

  $linha = "";
  unset($linha);


  $num=mysql_num_fields($id);
  $c=0;
  while ($c<$num)
  {
    $field=mysql_fetch_field($id);
    $linha[$c]=$field->name;
    $c++;    
  }  
  return ($linha);
}

/* **********************************************************
   RetornaListaCampos- Retorna dados dos campos de uma
     tabela
   Entrada: $sock - sock da base em uso
            $tabela - nome da tabela
   Saida: array com dados dos campos da tabela
*/
function RetornaListaCampos($sock, $tabela)
{
  $ar="";
  unset($ar);

  $query = "select * from ".$tabela." limit 1";

  $id = Enviar($sock, $query);

  $num = mysql_num_fields($id);
  $i = 0;
  while ($i < $num)
  {
    $field = mysql_fetch_field($id);
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

/* **********************************************************
   RetornaListaBases- Retorna nmero de campos do resultado da consulta
   Entrada: $sock - sock da base em uso
   Saida: array com o nome das bases da tabela
*/
function RetornaListaBases($sock)
{
//   global $_SESSION['dbnamecurso'], $_SESSION['dbtmpnamecurso'];

   // 2Session
  $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];


  $lista = "";
  unset($lista);


  $db_list = mysql_list_dbs($sock);
  $c = 0;
  while ($row = mysql_fetch_object($db_list))
  {

    $tmp = $row->Database;

    if ((ereg("^".$_SESSION['dbnamebase']."$", $tmp)) || (ereg("^".$_SESSION['dbnamecurso']."[0-9]+$", $tmp)) || (ereg("^".$_SESSION['dbtmpnamecurso']."[0-9]+$", $tmp)))
    {
      $lista[$c] = $tmp;
      $c++;
    }
  }
  return ($lista);
}

/* *********************************************************************
   RetornaProximoCodigo- Retorna o prï¿½imo nmero a ser usado nessa base
   Entrada: $sock - sock da base em uso
            $tabela - nome da tabela a ser retirada a sequencia
   Saida: prï¿½imo numero da sequencia
*/
function RetornaProximoCodigo($sock,$tabela,$cod_usuario_tmp='')
{
//   global $cod_usuario;
  
  if ($cod_usuario_tmp==""){
  	$cod_usuario_tmp=$_SESSION['cod_usuario'];
  	if ($cod_usuario_tmp=="")
  		$cod_usuario_tmp=0;
  }

  $data=time();
  $query="insert into ".$tabela."_sequencia (cod_usuario,data) values (".$cod_usuario_tmp.",".$data.")";

  if (!($res=mysql_query($query, $sock)))
  {
    /* Criar tabela e repetir */
    $query = "CREATE TABLE ".$tabela."_sequencia (cod int PRIMARY KEY AUTO_INCREMENT,cod_usuario INT,data INT)";
    $res=Enviar($sock,$query);
    $query="insert into ".$tabela."_sequencia (cod_usuario,data) values (".$cod_usuario_tmp.",".$data.")";
    $res=Enviar($sock,$query);
    $query="select cod from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $query="delete from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
    $res=Enviar($sock,$query);
    return ($linha[0]);
  }
  else
  {
    $query="select cod from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $query="delete from ".$tabela."_sequencia where cod_usuario=".$cod_usuario_tmp." and data=".$data;
    $res=Enviar($sock,$query);
    return ($linha[0]);     
  }
}

/*RetornaProximoCodigoUsuarioCurso - Retorna o prï¿½ximo cod_usuario olhando apenas para a tabela Usuario_curso
Entradas:
   $cod_curso - Curso a ser verificado
Saida:
   Proximo cod_usuario;
*/
function RetornaProximoCodigoUsuarioCurso($sock,$cod_curso)
{
   // 2Session
  $_SESSION['dbnamebase'] = $_SESSION['dbnamebase'];

   $query="select MAX(cod_usuario) from ".$_SESSION['dbnamebase'].".Usuario_curso where cod_curso='".VerificaStringQuery($cod_curso)."'";
   $res=Enviar($sock,$query);
   $dados=RetornaLinha($res);
   $prox_cod=$dados[0];
   $prox_cod++;
   return $prox_cod;
}

/* *********************************************************************
   PegaSemaforo- Pega semaforo para ferramenta
   Entrada: $sock - sock da base em uso
            $ferramenta - nome da Ferramenta
   Saï¿½a: Quando consegue o semaforo, retorna true; false caso contrÃ¡rio.
*/
function PegaSemaforo($sock,$ferramenta)
{
  $cont=0;
  $tempo_comparacao = time() - 60;

  $query = "select * from Semaforo_".$ferramenta." where data < ".$tempo_comparacao."";
  $res = mysql_query($query);
  if($res)
  	$rows = mysql_num_rows($res);
  else
  	$rows=0;	

  //se existe um semaforo e ele foi criado ha mais de 1 minuto, entao o liberamos
  if($res && $rows != 0)
    LiberaSemaforo($sock,$ferramenta);

  $query="CREATE TABLE Semaforo_".$ferramenta." (cod int,data int)";
  while (!mysql_query($query, $sock))
  {
    if ($cont>10)
        return false;

    $cont++;
    sleep(1);
  }

  $query="insert into Semaforo_".$ferramenta." (cod,data) values (0,".time().")";
  mysql_query($query, $sock);

  return true;
}

/* *********************************************************************
   LiberaSemaforo- Pega semaforo para ferramenta
   Entrada: $sock - sock da base em uso
            $ferramenta - nome da Ferramenta
   Saï¿½a: Nenhuma.
*/
function LiberaSemaforo($sock,$ferramenta)
{
   $query="DROP TABLE Semaforo_".$ferramenta;
   mysql_query($query,$sock);
}

/* *********************************************************************
   LiberaTodosSemaforos- Pega semaforo para ferramenta
   Entrada: $sock - sock da base em uso
   Saï¿½a: Nenhuma
*/
function LiberaTodosSemaforos($sock)
{
  $query="Show tables";
  $res=Enviar($sock,$query);
  $lista=RetornaArrayLinhas($res);
  foreach($lista as $cod => $linha)
  {
    if (eregi("^Semaforo",$linha[0]))
    {
      $query="DROP TABLE ".$linha[0];
      msql_query($query,$sock);
    }
  }
}

/* **********************************************
   EnviarNC - Funï¿½o de execuï¿½o da query requerida (no check)
   Entradas: $sock - Sock da base a ser consultada
             $query - consulta a ser realizada
   Saida: apontador de resultado da consulta
*/  
function EnviarNC($sock, $query)
{
  $ms = mysql_query($query, $sock);
  return($ms);
}

?>