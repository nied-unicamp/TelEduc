<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : index_novo.php

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
  ARQUIVO : index_novo.php
  ========================================================== */

  $bibliotecas="bibliotecas/";
  include($bibliotecas."geral.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  $eformador   = EFormador($sock,$cod_curso,$cod_usuario);
  $colaborador = EColaborador($sock, $cod_usuario, $cod_curso);

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n");
  echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc . Ensino &agrave; Dist&acirc;ncia</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\" />\n");
  echo("    <meta name=\"description\" content=\"\" />\n");
  echo("    <meta name=\"keywords\" content=\"\" />\n");
  echo("    <meta name=\"owner\" content=\"\" />\n");
  echo("    <meta name=\"copyright\" content=\"\" />\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("    <link href=\"js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");

  echo("    <script type='text/javascript' src='bibliotecas/dhtmllib.js'></script>\n");  
  echo("    <script type='text/javascript' src='js-css/jscriptlib.js'> </script>\n");
  echo("    <script type='text/javascript' src='js-css/rounded_corners_lite.inc.js'> </script>\n");

  echo("  </head>\n");
  echo("  <body>\n");
  echo("    <a name=\"topo\"></a>\n");
  echo("    <h1><a href=\"home.htm\"><img src=\"imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o &agrave; Dist&acirc;ncia\" /></a></h1>\n");
  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("      <tr>\n");
  echo("        <td></td>\n");
  echo("        <td valign=\"top\">\n");
  echo("          <!-- Navegacao Nivel 3 -->\n");
  echo("          <ul id=\"nav3nivel\">\n");
  echo("            <li class=\"visoes\"><a href=\"#\">Vis&atilde;o do Formador</a></li>\n");
  echo("            <li class=\"visoes\"><a href=\"#\">Vis&atilde;o do Aluno</a></li>\n");
  echo("            <li><a href=\"#\">Configura&ccedil;&atilde;o</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li>\n");
  echo("            <li><a href=\"#\">Suporte</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li>\n");
  echo("            <li><a href=\"#\">Administra&ccedil;&atilde;o</a></li>\n");
  echo("          </ul>\n");
  echo("          <div id=\"btsNivel3\"><span class=\"ajuste1\"><img src=\"imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" /></span>&nbsp;&nbsp;<a href=\"#\">ajuda</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"#\">X sair</a></div>\n");
  echo("          <h3>".NomeCurso($sock, $cod_curso)."</h3>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td width=\"140\" valign=\"top\">\n");
  echo("          <!-- Navegacao Principal -->\n");
  echo("          <ul id=\"nav\">\n");
  echo("            <li class=\"topLine\"><a href=\"#\">Din&acirc;mica do Curso</a></li>\n");
  echo("            <li><a href=\"#\">Agenda</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Avalia&ccedil;&otilde;es</a></li>\n");
  echo("            <li><a href=\"#\">Atividades</a></li>\n");
  echo("            <li><a href=\"#\">Material de Apoio</a></li>\n");
  echo("            <li><a href=\"#\">Leituras</a></li>\n");
  echo("            <li><a href=\"#\">Perguntas Frequentes</a></li>\n");
  echo("            <li><a href=\"#\">Exerc&iacute;cios</a></li>\n");
  echo("            <li><a href=\"#\">Parada Obrigat&oacute;ria</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Mural</a></li>\n");
  echo("            <li><a href=\"#\">F&oacute;uns de Discuss&atilde;o</a></li>\n");
  echo("            <li><a href=\"#\">Bate-Papo</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Correio</a></li>\n");
  echo("            <li><a href=\"#\">Grupos</a></li>\n");
  echo("            <li><a href=\"#\">Perfil</a></li>\n");
  echo("            <li><a href=\"#\">Di&aacute;rio de Bordo</a></li>\n");
  echo("            <li class=\"endLine\"><a href='ver_portfolio.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."5&amp;exibir=myp';\">Portf&oacute;lio</a></li>\n");
  echo("            <li><a href=\"#\">Acessos</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Intermap</a></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  //conteudo

  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td valign=\"bottom\" height=\"80\"><img src=\"imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right:8px;\" /> <img src=\"imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right:6px;\" /> <img src=\"imgs/logoUnicamp.gif\" alt=\"UNICAMP\" border=\"0\" /></td>\n");
  echo("        <td valign=\"bottom\" id=\"rodape\">2006  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
  echo("      </tr>\n");
  echo("    </table>\n"); //Container
  echo("  </body>\n");
  echo("</html>\n");
?>