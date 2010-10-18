<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/topo_tela_inicial.php

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
  ARQUIVO : pagina_inicial/topo_tela_inicial.php
  ========================================================== */

  $sock=Conectar("");
   	
  if (isset($cod_lin))
     MudancaDeLingua($sock,$cod_lin);
       
  else if(!empty($_SESSION['login_usuario_s']))
  {
  	$cod_lin = RetornaCodLinguaUsuario($sock,$_SESSION['cod_usuario_global_s']);
  	MudancaDeLingua($sock,$cod_lin);
  }

  /*$abv_lingua variavel utilizada para determinar o atributo lang do HTML*/
  $abv_lingua = AbreviaturaLingua($cod_lin);
  
  $lista_frases=RetornaListaDeFrases($sock,-3);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $lista_frases_configurar = RetornaListaDeFrases($sock,-7);

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $tela_raiz_www = $linha[0];

  $query="select valor from Config where item = 'host'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $tela_host=$linha['valor'];

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n");
  echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"$abv_lingua\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\" />\n");
  echo("    <meta name=\"description\" content=\"\" />\n");
  echo("    <meta name=\"keywords\" content=\"\" />\n");
  echo("    <meta name=\"owner\" content=\"\" />\n");
  echo("    <meta name=\"copyright\" content=\"\" />\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("    <link rel=\"shortcut icon\" href=\"../favicon.ico\" />\n");
  echo("    <link href=\"../cursos/aplic/js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <script type=\"text/javascript\" src=\"../cursos/aplic/js-css/jscript.js\"></script>\n");
  echo("    <link href=\"../cursos/aplic/js-css/dhtmlgoodies_calendar.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <script type=\"text/javascript\" src=\"../cursos/aplic/js-css/dhtmlgoodies_calendar.js\"></script>\n");