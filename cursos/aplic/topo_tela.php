<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : topo_tela.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : topo_tela.php
  ========================================================== */

/* ******************************************************************* */
  $bibliotecas="../bibliotecas/";
  include("menu.inc");
  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  /* Se o teleduc naum pegou o cod_curso, pegamos para ele =) */
  if (!isset($cod_curso)){
    if (isset($_GET['cod_curso'])){
      $cod_curso = $_GET['cod_curso'];
    } else if (isset($_POST['cod_curso'])){
      $cod_curso = $_POST['cod_curso'];
    }
  }

  $cod_usuario_global = VerificaAutenticacao($cod_curso);
  $sock = Conectar("");

  $lingua_curso = RetornaLinguaCurso($sock,$cod_curso);

  // Se diferente, entï¿½o lï¿½ngua do curso ï¿½ diferente da lï¿½ngua do usuï¿½rio, atualiza a lista de frases
  if($lingua_curso != $_SESSION['cod_lingua_s']) {
    MudancaDeLingua($sock, $lingua_curso);
  }

  if (!isset($cod_ferramenta))
    $cod_ferramenta = 1; /* Agenda */

  $lista_frases_menu  = RetornaListaDeFrases($sock, -4);
  $lista_frases       = RetornaListaDeFrases($sock, $cod_ferramenta);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  $tela_ordem_ferramentas = RetornaOrdemFerramentas($sock);
  $tela_lista_ferramentas = RetornaListaFerramentas($sock);
  $tela_lista_titulos     = RetornaListaTitulos($sock, $_SESSION['cod_lingua_s']);
  $tela_email_suporte     = RetornaConfiguracao($sock,"adm_email");

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $tela_raiz_www = $linha[0];

  $tela_host=RetornaConfiguracao($sock,"host");

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $tela_formador      = EFormador($sock, $cod_curso, $cod_usuario);
  $tela_formadormesmo = EFormadorMesmo($sock, $cod_curso, $cod_usuario);
  // booleano, indica se usuario eh colaborador
  $tela_colaborador   = EColaborador($sock, $cod_curso, $cod_usuario);
  // booleano, indica se usuario eh visitante
  $tela_visitante     = EVisitante($sock, $cod_curso, $cod_usuario);

  $SalvarEmArquivo = (!isset($SalvarEmArquivo) || $SalvarEmArquivo != 1) ? 0 : 1;

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, $cod_ferramenta);
  MarcaAcesso($sock, $cod_usuario, $cod_ferramenta);

  echo("<!DOCTYPE HTML SYSTEM \"http://teleduc.nied.unicamp.br/~teleduc/loose-custom.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc - ".$tela_lista_titulos[$cod_ferramenta]."</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\">\n");
  echo("    <meta name=\"description\" content=\"TelEduc\">\n");
  echo("    <meta name=\"keywords\" content=\"TelEduc\">\n");
  echo("    <meta name=\"owner\" content=\"TelEduc\">\n");
  echo("    <meta name=\"copyright\" content=\"TelEduc\">\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n");
  echo("    <link rel=\"shortcut icon\" href=\"../../../favicon.ico\" />\n");

  $estilos_css_default = array("../js-css/ambiente.css",
                               "../js-css/navegacao.css",
                               "../js-css/tabelas.css",
                               "../js-css/dhtmlgoodies_calendar.css");

  $codigos_js_default = array("../bibliotecas/dhtmllib.js",
                              "../js-css/dhtmlgoodies_calendar.js",
                              "../js-css/jscript.js",
                              //"../js-css/chat.js"
                        );

  // Se antes da inclusão de topo_tela.php, a página já indicou 
  // arquivos css ou js, devemos incluí-los no cabeçalho da página.

  if (isset($estilos_css) && is_array($estilos_css)) {
    $estilos_css = array_merge($estilos_css_default, $estilos_css);
  }
  else {
    $estilos_css = $estilos_css_default;
  }

  if (isset($codigos_js) && is_array($codigos_js)) {
    $codigos_js = array_merge($codigos_js_default, $codigos_js);
  }
  else {
    $codigos_js = $codigos_js_default;
  }

  /* Se estamos salvando a pagina em um arquivo, manter o css inline e sem javascript.
   * Caso contrario podemos servi-los normalmente sob a forma de links.
   */
  if ($SalvarEmArquivo) {

    array_push($estilos_css, "../js-css/salvaremarquivo.css");
    echo("<style>".RetornaCSSInline($estilos_css)."</style>");

  } else {

    foreach ($estilos_css as $css){
      echo("    <link href=\"".$css."\" rel=\"stylesheet\" type=\"text/css\">\n");
    }

    foreach ($codigos_js as $js){
      echo("    <script type=\"text/javascript\" src=\"".$js."\"></script>\n");
    }

  }
