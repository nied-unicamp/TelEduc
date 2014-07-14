<?php
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

  $diretorio_models = "../models/";
  $diretorio_ctrlers = "../controllers/";
  $diretorio_views = "../views/";
  $diretorio_imgs = "../../web-content/imgs/";
  $diretorio_jscss = "../../web-content/js-css/";
   
  require_once $diretorio_models.'geral.inc';
 	

  $sock = AcessoSQL::Conectar("");
  $abv_lingua = "pt-br"; //TODO - arrumar
  $lista_frases=Linguas::RetornaListaDeFrases($sock,-3);
  $lista_frases_geral=Linguas::RetornaListaDeFrases($sock,-1);
  $lista_frases_configurar = Linguas::RetornaListaDeFrases($sock,-7);
  
  
  
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
  echo("    <link rel=\"shortcut icon\" href=\"".$diretorio_imgs."favicon.ico\" />\n");

  $estilos_css = array($diretorio_jscss."ambiente.css",
                       $diretorio_jscss."navegacao.css",
                       $diretorio_jscss."tabelas.css",
                       $diretorio_jscss."dhtmlgoodies_calendar.css");

  $codigos_js = array($diretorio_jscss."dhtmllib.js",
                       $diretorio_jscss."dhtmlgoodies_calendar.js",
                       $diretorio_jscss."jscript.js");

  /* Se estamos salvando a pagina em um arquivo, manter o css inline e sem javascript.
   * Caso contrario podemos servi-los normalmente sob a forma de links.
   */
  foreach ($estilos_css as $css){
    echo("    <link href=\"".$css."\" rel=\"stylesheet\" type=\"text/css\">\n");
  }

  foreach ($codigos_js as $js){
    echo("    <script type=\"text/javascript\" src=\"".$js."\"></script>\n");
  }
?>