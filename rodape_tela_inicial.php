<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : rodape_tela_inicial.php

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
  ARQUIVO : pagina_inicial/rodape_tela_inicial.php
  ========================================================== */
  
  /* Rodapï¿½ */

  echo("      <tr>\n");
  echo("        <td valign=\"bottom\" height=\"80\">");
  echo("		</td>\n");
  echo("        <td valign=\"bottom\" id=\"rodape\"> ");
  /*	Para fins de SEO existe um random que alterna o alt e title da imagem do teleduc  */
  if(rand(1,2) == 1){
  	echo("			<a tabindex=\"1\" href=\"http://www.teleduc.org.br\"><img src=\"../imgs/teleduc-EAD.jpg\" alt=\"TelEduc: Ensino à distância\" title=\"TelEduc - Ensino à distância\" border=\"0\" style=\"margin-right:5px;\" /></a>&nbsp;|&nbsp;\n");
  }
  else{
  	echo("			<a tabindex=\"1\" href=\"http://www.teleduc.org.br\"><img src=\"../imgs/teleduc-EAD.jpg\" alt=\"TelEduc: Educação à distância\" title=\"TelEduc - Educação à distância\" border=\"0\" style=\"margin-right:5px;\" /></a>&nbsp;|&nbsp;\n");
  }
  /*Logo do Nied*/
  echo("			<a tabindex=\"2\" href=\"http://www.nied.unicamp.br\"><img src=\"../imgs/logoNied.gif\" acronym title=\"Nucleo de Informatica Aplicada a Educação\" alt=\"nied\" border=\"0\" style=\"margin-right: 8px; margin-bottom: 6px;\" /></a>&nbsp;|&nbsp;");
  /* Logo do IC*/
  echo("		    <a tabindex=\"3\" href=\"http://www.ic.unicamp.br\" title=\"Instituto de Computa&ccedil;&atilde;o\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right: 6px; margin-bottom: -2px;\" /></a>&nbsp;|&nbsp;");
  /* Logo da Unicamp */
  echo("		    <a tabindex=\"4\" href=\"http://www.unicamp.br\" title=\"Unicamp\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" style=\"margin-bottom: 2px;\"border=\"0\" /></a>\n");
  echo("		</td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td valign=\"bottom\">");
  echo("		</td>\n");
  echo("        <td valign=\"bottom\" id=\"rodape\" lang=\"pt\">2010  - TelEduc - Todos os direitos reservados. <em lang=\"en\">All rights reserved</em> - NIED - UNICAMP</td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");

?>