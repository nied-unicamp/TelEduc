<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : topo_tela.php

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
  ARQUIVO : topo_tela.php
  ========================================================== */

/* ******************************************************************* */
  $bibliotecas="../bibliotecas/";
  include("menu.inc");

  /* Se o teleduc naum pegou o cod_curso, pegamos para ele =) */
  if (!isset($cod_curso)){
  	if (isset($_GET['cod_curso'])){
  		$cod_curso = $_GET['cod_curso'];
  	} else if (isset($_POST['cod_curso'])){
  		$cod_curso = $_POST['cod_curso'];
  	}
  }
  	
  $cod_usuario_global=VerificaAutenticacao($cod_curso);
  $sock=Conectar("");

  $auxiliar = RetornaLinguaCurso($sock,$cod_curso);

  // Se diferente, ent�o l�ngua do curso � diferente da l�ngua do usu�rio, atualiza a lista de frases
  if($auxiliar != $_SESSION['cod_lingua_s']){
  	unset($_SESSION['lista_frases_s']);
  }
  
  
  $lista_frases_menu=RetornaListaDeFrases($sock,-4);
  
  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
   
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $tela_ordem_ferramentas=RetornaOrdemFerramentas($sock);
  $tela_lista_ferramentas=RetornaListaFerramentas($sock);
  $tela_lista_titulos=RetornaListaTitulos($sock, $_SESSION['cod_lingua_s']);
  $tela_email_suporte=RetornaConfiguracao($sock,"adm_email");

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $tela_raiz_www = $linha[0];

  $tela_host=RetornaConfiguracao($sock,"host");

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $tela_visitante     = EVisitante($sock,$cod_curso,$cod_usuario);

  $tela_formador          = EFormador($sock,$cod_curso,$cod_usuario);
  $tela_formadormesmo     = EFormadorMesmo($sock,$cod_curso,$cod_usuario);

  // booleano, indica se usuario eh convidado
  $tela_convidado         = EConvidado ($sock, $cod_usuario, $cod_curso);
  // especifica que tipo de convidado eh
  $tela_convidado_ativo   = EConvidadoAtivo($sock, $cod_usuario, $cod_curso);
  $tela_convidado_passivo = EConvidadoPassivo($sock, $cod_usuario, $cod_curso);
  
  $SalvarEmArquivo = (!isset($SalvarEmArquivo) || $SalvarEmArquivo != 1) ? 0 : 1;
  
  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,0);
  MarcaAcesso($sock,$cod_usuario,$cod_ferramenta);

  if (!isset($cod_ferramenta))
    $cod_ferramenta=1; /* Agenda */

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
  
  $estilos_css = array(	"../js-css/ambiente.css",
  						"../js-css/navegacao.css",
  						"../js-css/tabelas.css",
  						"../js-css/dhtmlgoodies_calendar.css",
                        "../js-css/chat.css");
  
  $codigos_js = array(	"../bibliotecas/dhtmllib.js",
  						"../js-css/dhtmlgoodies_calendar.js",
  						"../js-css/jscript.js",
  						"../js-css/chat.js");
  
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