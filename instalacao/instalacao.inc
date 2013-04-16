<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/instalacao.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : instalacao/instalacao.inc
  ========================================================== */

session_start();
/*if(strcmp(session_name(), "teleduc4.0") != 0){
  session_name("teleduc4.0");
}*/

function RetornaSessionID()
{
  return SID;
}

function ExibirCabecalho($fase,$titulo)
{
  global $fase_g;
  $fase_g=$fase;
  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("<head>\n");
  echo("<title>TelEduc - Instala&ccedil;&atilde;o</title>\n");
  echo("<meta name=\"robots\" content=\"follow,index\" />\n");
  echo("<meta name=\"description\" content=\"\" />\n");
  echo("<meta name=\"keywords\" content=\"\" />\n");
  echo("<meta name=\"owner\" content=\"\" />\n");
  echo("<meta name=\"copyright\" content=\"\" />\n");
  echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("<link href=\"../cursos/aplic/js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("</head>\n");

  echo("<body>\n");
  echo("  <a name=\"topo\"></a>\n");
  echo("  <h1><a href=\"../pagina_inicial/index.php\"><img src=\"../cursos/aplic/imgs/logo.gif\" alt=\"TelEduc . Educa 	&ccedil;&atilde;o � Dist�ncia\" border=\"0\" /></a></h1>\n");
  echo("  <table id=\"container\" cellpadding=\"0\" cellspacing=\"0\">\n");
  echo("    <tbody><tr><td valign=\"top\">\n");
  echo("          <h3 style=\"margin: 40px 0px 0px 150px;\">".$titulo."</h3>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td id=\"conteudo\" valign=\"top\" width=\"100%\">\n");
  echo("          <h4 style=\"margin-left: 150px;\">Instala&ccedil;&atilde;o - Fase ".$fase." de 6</h4>\n");
  echo("          <br />\n");

}

function AbreForm()
{
  global $fase_g;
  $fase=$fase_g;
  echo("<form action='instalacao".($fase+1).".php' name=inst onSubmit='return(Valida());' method=post>\n");
}

function AbreFormFinal()
{
  global $fase_g;
  $fase=$fase_g;
  echo("<form action='../pagina_inicial/autenticacao_cadastro.php'>\n");
}

function Paragrafo($texto)
{
  echo("<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
  echo($texto);
  echo("\n</p>\n");
}

function EncerraPagina()
{
  echo("<br /><div align=right><input class=input type=submit value='Continuar' /></div>\n");
  echo("</form>\n");
  echo("</td></tr></table>\n");
  echo("</body></html>\n");
}

function EncerraPaginaFinal()
{
  echo("<br /><div align=right><input class=input type=submit value='Entrar no TelEduc' /></div>\n");
  echo("</form>\n");
  echo("</td></tr></table>\n");
  echo("</body></html>\n");
}

function Destaque($texto)
{
  echo("<ul><font face=Verdana,Arial,Helvetica size=-1>\n");
  echo($texto);
  echo("\n</font></ul>\n");
}

function CaixaTexto($texto,$nome_var,$padrao)
{
  if (func_num_args() > 3)
    $arg = func_get_arg(3);
  else
    $arg = "";
  echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=Verdana,Arial,Helvetica size=-1>$texto<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class=input type=text name=".$nome_var." value='".$padrao."' /></font>".$arg."<br/><br/>\n");
}

function Radio($texto,$nome_var,$check,$padrao)
{
  echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=Verdana,Arial,Helvetica size=-1><input type='radio' name=".$nome_var." value='".$padrao."' ".$check." /> $texto</font><br/>\n");
}

function CaixaSenha($texto,$nome_var)
{
  echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face=Verdana,Arial,Helvetica size=-1>$texto<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class=input type=password name=".$nome_var." /></font><br/><br/>\n");
}

/* *******************************************
   ConectarDB - Conecta a base de dados pedida
   Entrada: $base - nome da base de dados
   Saida: socket do servidor de base de dados, se ok.
          interrompe o c�digo e envia alerta caso contrario
*/
function ConectarDB($base,$dbuser,$dbpassword)
{

  if (! $sock = mysql_connect('localhost',$dbuser,$dbpassword))
  {
    return -1;
  }
  else
  {
    if (! mysql_select_db($base, $sock))
    {
      return -1;
    }
    else
    {
      //echo("</div>\n");
      return($sock);
    }
  }
}

function Enviar($sock, $query)
{
  if (!($ms = mysql_query($query, $sock)))
    return -1;
  else
    return($ms);
}

function Voltar($mensagem)
{
  echo("<p><font face=Verdana,Arial,Helvetica size=-1 color=red><b>\n");
  echo($mensagem);
  echo("\n<b></font></p>\n");
  echo("<form><input type=button value='Voltar' onClick=history.go(-1); /></form>\n");
  echo("</body></html>\n");
  die;
}

/*Funcion CriaBase
Descri��o: Preenche a base de dados geral criada pelo TelEduc com as tabelas necess�rias.
entrada:
dbbasegeral - Nome da base criada
teleduc_login - login de acesso ao mysql
teleduc_senha - senha de acesso ao mysql
sa�da:
0 - se a extra��o ocorreu OK
-1 - se ocorreu algum erro nas etapas.
*/

function CriaBase($dbbasegeral,$teleduc_login,$teleduc_senha)
{
if(file_exists("base_geral.table")){
  if(!shell_exec("mysql -u".$teleduc_login." -p".$teleduc_senha." ".$dbbasegeral." < base_geral.table"))
    return true;
  else
    return false;
}
else
  return false;
}

/*Funcion CriaTelEducConf
Descri��o: Cria o arquivo teleduc.conf onde s�o armazenados o usu�rio e senha do MySQL e os nomes da Base Geral e da Base do Curso
$arquivo - nome do arquivo 
$dbbasegeral - Nome da Base Geral
$dbbasecurso - Nome da Base do Curso
$teleduc_login - nome de usu�rio do MySQL usado pelo TelEduc
$teleduc_senha - senha do usu�rio MySQL
$dbtmpbasecurso  - Nome da base tempor�ria
$tmpteleduc_login - Nome de usu�rio MySQL para a base Tempor�ria
$tmpteleduc_senha - Senha do usu�rio MySQL para a base tempor�ria
$importar_curso_extr - Vari�vel que indica se o curso usar� bases tempor�rias
sa�da:
0 - se a cria��o do arquivo ocorreu OK
-1 - se ocorreu algum erro nas etapas.
*/

function CriaTelEducConf($arquivo,$dbbasegeral,$dbbasecurso,$teleduc_login,$teleduc_senha,$dbtmpbasecurso,$tmpteleduc_login,$tmpteleduc_senha, $importar_curso_extr)
{
  if ($fh=fopen($arquivo,"w"))
  {
    fputs($fh,"dbnamebase=".$dbbasegeral."\n");
    fputs($fh,"dbnamecurso=".$dbbasecurso."\n");
    fputs($fh,"dbuser=".$teleduc_login."\n");
    fputs($fh,"dbpassword=".$teleduc_senha."\n");
    fputs($fh,"dbhost=localhost\n");
    fputs($fh,"dbport=3306\n");
/* Se o usu�rio optar por n�o importar itens de cursos extra�dos,
 deixar os campos dbtmpnamecurso, dbtmpuser, dbtmppassword, dbtmphost,
  dbtmpport em branco e n�o realizar grant � conta tempor�ria.*/

// Inserir Valores  
    if ($importar_curso_extr)
    {
      fputs($fh,"dbtmpnamecurso=".$dbtmpbasecurso."\n");
      fputs($fh,"dbtmpuser=".$tmpteleduc_login."\n");
      fputs($fh,"dbtmppassword=".$tmpteleduc_senha."\n");
      fputs($fh,"dbtmphost=localhost\n");
      fputs($fh,"dbtmpport=3306\n");
    }
// Deixar Vazio
    else
    {
      fputs($fh,"dbtmpnamecurso=\n");
      fputs($fh,"dbtmpuser=\n");
      fputs($fh,"dbtmppassword=\n");
      fputs($fh,"dbtmphost=\n");
      fputs($fh,"dbtmpport=\n");
    }
  }
  else
    return -1;

  fclose($fh);
  return 0;
}

function CriaDiretorio($diretorio)
{
  $r1=mkdir ($diretorio, 0777);
  $r2=chmod ($diretorio, 0777);
  return ($r1 & $r2);
}

function CompareRetornaArrayDiretorio($ar1, $ar2)
{
  if ($ar1['Diretorio']<$ar2['Diretorio'])
    return -1;
  else if ($ar1['Diretorio']>$ar2['Diretorio'])
    return 1;
  if ($ar1['Arquivo']<$ar2['Arquivo'])
    return -1;
  else if ($ar1['Arquivo']>$ar2['Arquivo'])
    return 1;
  return 0;
}

function RetornaArrayDiretorio($diretorio)
{
  /* Coloca a barra no final, se n�o tiver */
  $diretorio .="/";
  $diretorio = implode("/",explode("//",$diretorio));

  /* Prepara lista de arquivos */
  clearstatcache();
  $lista = "";
  unset ($lista);
  $fp=popen("find ".$diretorio." -follow","r");

  $cont=0;
  while($s = fgets($fp, 15360))
  {
    /* cortando carro de linha */
    $enter=explode("\n",$s);
    $s=$enter[0];

    $lista[$cont]['Caminho']=$s;
    $quebrando=explode($diretorio,$s);
    $relativo=$quebrando[1];
    if (is_dir($s))
    {
      $lista[$cont]['Diretorio']=$relativo;
      $lista[$cont]['Arquivo']="";
      $lista[$cont]['Tamanho']=0;
    }
    else
    {
      $dir = "";
      unset($dir);
      $partes=explode("/",$relativo);
      for ($c=0;$c<count($partes)-1;$c++)
      {
        $dir.=$partes[$c];
        if ($c<count($partes)-2)
          $dir .="/";
      }
      $lista[$cont]['Diretorio']=$dir;
      $lista[$cont]['Arquivo']=$partes[count($partes)-1];
      $lista[$cont]['Tamanho']=filesize($s);
    }
    $lista[$cont]['Data']=filemtime($s);
    $cont++;
  }
  if (count($lista)>0)
    uasort ($lista, CompareRetornaArrayDiretorio);
  return ($lista);
}

function EspalharArquivoAuth($ambiente)
{
  $lista=RetornaArrayDiretorio("..");

  foreach($lista as $cod=>$linha)
    if ($linha['Arquivo']==".auth")
    {
      $fh=fopen($linha['Caminho'],"w");
      fputs($fh,$ambiente."/");
      fclose($fh);
    }
}

function CriaLinkSimbolico($origem,$destino)
{
  symlink($origem,$destino);
}


?>
