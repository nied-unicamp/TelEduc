<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/instalacao1.php

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
  ARQUIVO : instalacao/instalacao1.php
  ========================================================== */

  include "instalacao.inc"; 

  ExibirCabecalho(1,"Defini��o dos nomes das bases de dados do TelEduc");

  echo("
    <script type=\"text/javascript\" language=javascript>
      function Valida() 
      {
        var dbbasegeral=document.inst.dbbasegeral.value;
        var dbbasecurso=document.inst.dbbasecurso.value;
     
        if (dbbasegeral=='' || dbbasecurso=='')
        {
          alert('Nenhum campo deve ser deixado em branco.');
          return false; 
        }
        return true;
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

  Paragrafo("O ambiente TelEduc utiliza uma base de dados para manter configura��es do ambiente e uma base de dados para cada curso:");
  Paragrafo("<font color=\"#2a6686\"><b>*</b></font> Uma base de dados comum a todos os cursos, que cont�m informa��es de configura��es do ambiente;");
  Paragrafo("<font color=\"#2a6686\"><b>*</b></font> Uma base de dados distinta para cada curso, com as informa��es do curso em si. O nome da base ser� uma composi��o do nome dado abaixo mais o c�digo do curso (Ex.: 'TelEducCurso' + '1' = 'TelEducCurso1').");
  Paragrafo("Obs.: Altere o nome das bases somente se julgar necess�rio.");

  CaixaTexto("Base de dados comum a todos os cursos:","dbbasegeral","TelEduc"); // Deveria ser TelEduc
  CaixaTexto("Base de dados de cada curso:","dbbasecurso","TelEducCurso"); // Deveria ser TelEducCurso

  echo("</td></tr></table>\n");
  echo("</td></tr></table>\n");

  EncerraPagina();
?>

