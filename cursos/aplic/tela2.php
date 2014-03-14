<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : tela2.php

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
  ARQUIVO : tela2.php
  ========================================================== */
  
  /* Rodap� */
  echo("      <tr>\n");
  
  if(!$SalvarEmArquivo){
    echo("        <td valign=\"bottom\" height=\"80\">\n");
    echo("        </td>\n");
  }
  
  echo("        <td valign=\"bottom\" class=\"rodape\">\n");
  /*	Para fins de SEO existe um random que alterna o alt e title da imagem do teleduc  */
  if(rand(1,2) == 1){
    echo("          <a href=\"http://www.teleduc.org.br\"><img src=\"../../../imgs/teleduc-EAD.jpg\" alt=\"TelEduc - Ensino &agrave; dist&acirc;ncia\" title=\"TelEduc - Ensino &agrave; dist&acirc;ncia\" border=\"0\" style=\"margin-right:5px;\" /></a>\n");
  }
  else{
    echo("          <a href=\"http://www.teleduc.org.br\"><img src=\"../../../imgs/teleduc-EAD.jpg\" alt=\"TelEduc - Educa&ccedil;&atilde;o &agrave; dist&acirc;ncia\" title=\"TelEduc - Educa&ccedil;&atilde;o &agrave; dist&acirc;ncia\" border=\"0\" style=\"margin-right:5px;\" /></a>\n");
  }
  echo("          <a href=\"http://www.nied.unicamp.br\"><img src=\"../imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right: 8px; margin-bottom: 6px;\" /></a><a href=\"http://www.ic.unicamp.br\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right: 6px; margin-bottom: -2px;\" /></a><a href=\"http://www.unicamp.br\" title=\"Unicamp\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" style=\"margin-bottom: 2px;\"border=\"0\" /></a>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  
  if(!$SalvarEmArquivo){
    echo("        <td valign=\"bottom\">\n");
    echo("        </td>\n");
  }
  
  echo("        <td valign=\"bottom\" class=\"rodape\">2010  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");

?>