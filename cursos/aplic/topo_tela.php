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

  $cod_curso = $_GET['cod_curso'];
  $cod_usuario_global=VerificaAutenticacao($cod_curso);
  $sock=Conectar("");
  
  $_SESSION['cod_lingua_s'] = RetornaLinguaCurso($sock,$cod_curso);

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
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <link href=\"../js-css/dhtmlgoodies_calendar.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmlgoodies_calendar.js\"></script>\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>\n");