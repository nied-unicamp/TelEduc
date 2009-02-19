<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/instalacao3.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : instalacao/instalacao3.php
  ========================================================== */

  include "instalacao.inc";

  ExibirCabecalho(4,"Criação do arquivo teleduc.conf");

  session_register("teleduc_login_s");
  session_register("teleduc_senha_s");
  session_register("dbbasegeral_s");
  session_register("dbbasecurso_s");
  session_register("importar_curso_extr_s");
  session_register("tmpteleduc_login_s");
  session_register("tmpteleduc_senha_s");
  session_register("tmpdbbasecurso_s");
  session_register('root_mysql_s');
  session_register('root_mysql_senha_s');

  $sock=ConectarDB("mysql",$root_mysql_s,$root_mysql_senha_s);

  $versao_geral = mysql_get_server_info();
  $versao_numero = $versao_geral[0];

  if ($sock==-1)
    Voltar("Não foi possível conectar ao MySQL. Pode ser que o MySQL não esteja corretamente instalado, ou que o Deamon do MySQL não esteja em execução, ou o login e senha de root tenham sido digitados errados.<br><br>Mensagem de erro retornada pelo MySQL: <font color=black>".mysql_error()."</font>");

  $tmpdbbasecurso_s=$tmpdbbasecurso;
  $tmpteleduc_login_s=$tmpteleduc_login;
  $tmpteleduc_senha_s=$tmpteleduc_senha;

#create temporary tables ,lock tables, reload e file foram retirados para manter compatibilidade com versões anteriores à 4.0.2 do MySQL
#  $query1="grant alter, create, create temporary tables, delete, drop, file, index, insert, lock tables, reload, select, update, references privileges on ".$dbbasegeral_s.".* to ".$teleduc_login_s."@localhost identified by '".$teleduc_senha_s."'";

#Estes inserts fazem a mesma coisa que o grant acima, não são necessários.
/*
  if ($versao_numero == 3)
    $query2 = "INSERT INTO user VALUES ('localhost', '".$teleduc_login_s."', PASSWORD('".$teleduc_senha_s."'), 'N','N','N','N','N','N','N','N','N','N','N','N','N','N')";
  else if ($versao_numero == 4)
    $query2 = "INSERT INTO user VALUES ('localhost', '".$teleduc_login_s."', PASSWORD('".$teleduc_senha_s."'), 'N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','','','','',0,0,0)";

  Enviar($sock,$query2);
*/

#Seta privilégios do usuário do teleduc na base dos cursos; A partir da versao 4.1 do MySQl eh necessario ter privilegio de Lock da Base de Dados para poder aplicar um DUMP usado para a extracao de Cursos. No primeiro if colocamos permissao de Lock na Base Geral. E no segundo para todas as Bases de Cursos que forem criadas.
if (mysql_get_server_info()>=4.1)
{
$query1="grant alter, create, delete, drop, index, insert, select, update, lock tables on ".$dbbasegeral_s.".* to ".$teleduc_login_s."@localhost identified by '".$teleduc_senha_s."'";
}
else
{
 $query1="grant alter, create, delete, drop, index, insert, select, update on ".$dbbasegeral_s.".* to ".$teleduc_login_s."@localhost identified by '".$teleduc_senha_s."'";
}
  Enviar($sock,$query1);


if (mysql_get_server_info()>=4.1)
{
	$query3 = "INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, References_priv, Index_priv, Alter_priv, Lock_tables_priv) VALUES ('localhost', '".$dbbasecurso_s."%', '".$teleduc_login_s."', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y')";
}
else
{
	$query3 = "INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', '".$dbbasecurso_s."%', '".$teleduc_login_s."', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y')";
}
Enviar($sock, $query3);

if (mysql_get_server_info()>=4.1)
{
$query4 = "update db set Lock_tables_priv='Y' where Db = '".$dbbasecurso_s."%' and User='".$teleduc_login_s."' and Host='localhost'";
Enviar($sock, $query4);
}

#Caso seja pedido uma base temporária (passo 3 da instalação) seta permissões para um usuário criar tais bases, 
#Usadas durante a importação.

  if ($importar_curso_extr_s)
  {
     if (mysql_get_server_info()>=4.1) {
      $query7 = "INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, References_priv, Index_priv, Alter_priv, Lock_tables_priv) VALUES ('localhost', '".$tmpdbbasecurso_s."%', '".$tmpteleduc_login_s."', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y')";
     } else { 
      $query7 = "INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', '".$tmpdbbasecurso_s."%', '".$tmpteleduc_login_s."', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y')";
     }
      
    Enviar($sock, $query7);
    
    if (mysql_get_server_info()>=4.1)
    { 
       $query4 = "update db set Lock_tables_priv='Y' where Db = '".$tmpdbbasecurso_s."%' and User='".$tmpteleduc_login_s."' and Host='localhost'";
       Enviar($sock, $query4);
    }
    
    if($tmpteleduc_login_s!=$teleduc_login_s) /*temos q inserir na tabela usuário tb.*/
    {
      $query8 = "INSERT INTO user (Host, User, Password) VALUES ('localhost', '$tmpteleduc_login_s', PASSWORD('$tmpteleduc_senha_s'))";
    }
    Enviar($sock, $query8);
  }

#Query desnessária pois seta os mesmos privilégios que o grant logo no início, estava repetido.
/*
  $query4 = "INSERT INTO db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, References_priv, Index_priv, Alter_priv) VALUES ('localhost', '".$dbbasegeral_s."', '".$teleduc_login_s."', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y')";
  Enviar($sock, $query4);
*/

  $query5 = "FLUSH PRIVILEGES";
  Enviar($sock, $query5);


  $query6 = "create database ".$dbbasegeral_s;
  Enviar($sock, $query6);

  mysql_close($sock);

  if (CriaBase($dbbasegeral_s,$teleduc_login_s,$teleduc_senha_s)==false)
    Voltar("Mensagem de erro retornada pelo MySQL: <font color=black>".mysql_error()."</font>");

/*nao existe mais teleducBP"
/*  $sock = ConectarDB("mysql",$root_mysql,$root_mysql_senha);

  $query7 = "grant select on ".$dbbasegeral_s.".Batepapo_sessoes_correntes to teleducBP@localhost identified by 'teleducBP'";
  Enviar($sock, $query7);
  $query8 = "grant select on ".$dbbasegeral_s.".Lingua_textos to teleducBP@localhost identified by 'teleducBP'";
  Enviar($sock, $query8);

  $query9 = "FLUSH PRIVILEGES";
  Enviar($sock, $query9);

  mysql_close($sock);*/

  echo("
    <script type=\"text/javascript\" language=javascript>
      function Valida()
      {
        var dirtele=document.inst.dirtele.value;
        while (dirtele.search(\" \") != -1)
          dirtele = dirtele.replace(/ /, \"\");

        var ambiente=document.inst.ambiente.value;
        while (ambiente.search(\" \") != -1)
          ambiente = ambiente.replace(/ /, \"\");

        var arquivos=document.inst.arquivos.value;
        while (arquivos.search(\" \") != -1)
          arquivos = arquivos.replace(/ /, \"\");

        var arquivosweb=document.inst.arquivosweb.value;  
        while (arquivosweb.search(\" \") != -1)
          arquivosweb = arquivosweb.replace(/ /, \"\");

        var extracao=document.inst.extracao.value;
        while (extracao.search(\" \") != -1)
          extracao = extracao.replace(/ /, \"\");
        

        ");
        if($importar_curso_extr_s)
          echo("var montagem=document.inst.montagem.value;
        while (montagem.search(\" \") != -1)
          montagem = montagem.replace(/ /, \"\");");
	echo("
        var raiz_www=document.inst.raiz_www.value;
        while (raiz_www.search(\" \") != -1)
          raiz_www = raiz_www.replace(/ /, \"\");

        var sendmail=document.inst.sendmail.value;
        while (sendmail.search(\" \") != -1)
          sendmail = sendmail.replace(/ /, \"\");

        var mimetypes=document.inst.mimetypes.value;
        while (mimetypes.search(\" \") != -1)
          mimetypes = mimetypes.replace(/ /, \"\");

        var mysqldump=document.inst.mysqldump.value;
        while (mysqldump.search(\" \") != -1)
          mysqldump = mysqldump.replace(/ /, \"\");

        var tar=document.inst.tar.value;
        while (tar.search(\" \") != -1)
          tar = tar.replace(/ /, \"\");

	");
	if($importar_curso_extr_s)
        {
          echo("
	    if (dirtele=='' || ambiente=='' || arquivos=='' || arquivosweb=='' || extracao=='' || montagem=='' || raiz_www=='' || sendmail=='' || mimetypes=='' || mysqldump=='' || tar=='')
  	  {
	    alert('Nenhum campo deve ser deixado em branco.');
	    return false;
	  }
	  return true;
	  ");
	}
	else
	{
        echo("
	  if (dirtele=='' || ambiente=='' || arquivos=='' || arquivosweb=='' || extracao=='' || montagem=='' || raiz_www=='' || sendmail=='' || mimetypes=='' || mysqldump=='' || tar=='')
  	  {
	    alert('Nenhum campo deve ser deixado em branco.');
	    return false;
	  }
	  return true;
	  ");

	}
	
echo("
      }
     </script>
");

  AbreForm();

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("<tr><td style=\"padding-left: 150px; padding-top: 15px; padding-right: 150px; padding-bottom: 15px; font-size: small;\" align=\"left\">\n");
  Paragrafo("1 - Usuários inseridos com sucesso!");
  echo("<br/>\n");
  Paragrafo("2 - Bases criadas com sucesso!");
  echo("<br/>\n");

  $path=realpath(".");

  $exp=explode("/",$path);

  if (count($exp)>2)
  {
    $ambiente="";
    for ($c=1; $c<count($exp)-2;$c++)
      $ambiente.="/".$exp[$c];
    $subtele=$exp[count($exp)-2];  
    $raiz_www="/~".$exp[count($exp)-3];
  }
  else
  {
    $ambiente="/home/teleduc";
    $subtele="public_html";
    $raiz_www="/~teleduc";
  }
  $dirtele=$ambiente."/".$subtele;
  $arquivosweb=$dirtele."/cursos/diretorio";
  $arquivos=$ambiente."/arquivos";
  $extracao=$ambiente."/extraidos";
  if($importar_curso_extr_s)
  {
    /*seta diretorio onde os cursos serao temporariamente montados.(para importacao)*/
    $montagem=$ambiente."/montagem";
  }
  $sendmail="/usr/sbin/sendmail";
  $mysqldump="/usr/bin/mysqldump";
  $mimetypes="/usr/local/apache/conf/mime.types";
  $tar="/bin/tar";

  Paragrafo("<b><font size=+1>Preencha abaixo os diretórios e caminhos de execução para uso interno do ambiente</font></b>");
  CaixaTexto("Diretório que contém os arquivos do TelEduc (deve ser acessível pela web. <b>Ex.:</b> /home/teleduc/public_html):","dirtele", $dirtele);
  CaixaTexto("Diretório no qual ficará o arquivo de configuração do TelEduc, \"<b><i>teleduc.conf</i></b>\" (deve ficar fora do acesso da web. <b>Ex:</b> /home/teleduc):","ambiente", $ambiente);
  CaixaTexto("Diretório em que ficarão os arquivos anexados a itens das ferramentas do curso (deve ficar fora do acesso da web. <b>Ex:</b> /home/teleduc/arquivos):","arquivos",$arquivos); 
  CaixaTexto("Diretório em que será criados links para exibição dos arquivos pela web (<b>Ex:</b> /home/teleduc/public_html/diretorio):","arquivosweb",$arquivosweb); 
  CaixaTexto("Diretório para o qual serão movidos os cursos extraidos (<b>Ex:</b> /home/teleduc/extraidos):","extracao",$extracao); 
   
  if($importar_curso_extr_s) 
    CaixaTexto("Diretório onde serão montados os cursos extraídos durante a importação (<b>Ex:</b> /home/teleduc/montagem):","montagem",$montagem);
   
  CaixaTexto("Caminho via browser (sem o nome da máquina. <b>Ex:</b> /~teleduc):","raiz_www",$raiz_www); 

  if (file_exists($sendmail))
    CaixaTexto("Caminho do Sendmail (inclusive o executável):","sendmail",$sendmail);
  else
    CaixaTexto("Caminho do Sendmail (inclusive o executável):","sendmail","", "&nbsp;Ex.: ".$sendmail);

  if (file_exists($mysqldump))
    CaixaTexto("Caminho do mysqldump (inclusive o executável):","mysqldump",$mysqldump);
  else
    CaixaTexto("Caminho do mysqldump (inclusive o executável):","mysqldump","","&nbsp;Ex.: ".$mysqldump);

  if (file_exists($mimetypes))
    CaixaTexto("Caminho do arquivo \"mime.types\" do Apache para resolução do arquivos (inclusive o nome do arquivo):","mimetypes",$mimetypes);
  else
    CaixaTexto("Caminho do arquivo \"mime.types\" do Apache para envio de e-mail(inclusive o nome do arquivo):","mimetypes","","&nbsp;Ex.: ".$mimetypes);

  if (file_exists($tar))
    CaixaTexto("Caminho do tar (inclusive o executável):","tar",$tar);
  else
    CaixaTexto("Caminho do tar (inclusive o executável):","tar","", "&nbsp;Ex.: ".$tar);

  echo("</td></tr></table>\n");
  echo("</td></tr></table>\n");

  EncerraPagina();

?>
