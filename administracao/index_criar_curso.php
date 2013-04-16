<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/index_criar_curso.php

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
  ARQUIVO : administracao/index_criar_curso.php
  ========================================================== */

  $bibliotecas = "../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock,-5);
  $lista_frases_geral = RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");

  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");

  echo("</script>\n");
  /* Fim do JavaScript */

  /* 3 - Cria��o de Curso */
  PreparaCabecalhoOpcao(RetornaFraseDaLista($lista_frases, 3));

  echo("<!-- Tabelao -->\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  echo("<td><ul class=\"btAuxTabs\">\n");

  /* 98 - Criar Curso */
  echo("<li><a href=\"#\" title=\"Criar Curso\" onClick=\"document.location='criar_curso.php'\">".RetornaFraseDaLista($lista_frases,98)."</a></li>\n");


  /* 244 - Avaliar requisi��es para abertura de cursos */
  echo("<li><a href=\"#\" title=\"Avaliar requisi��es para abertura de cursos\" onClick=\"document.location='avaliarcurso/avaliar_curso.php'\">".RetornaFraseDaLista($lista_frases,244)."</a></li>\n");


  echo("<form name=frmIndex action=index.php?>\n");
  /* 23 - Voltar (Ger) */
  echo("<li><a href=\"#\" title=\"voltar\" onCLick=\"document.frmIndex.submit();\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("</form>\n");
  echo("</ul>\n");

  echo("</td></tr></table>\n");

  echo("  <script type=text/javascript defer>\n\n");

  echo("    document.frmIndex.cmdVoltar.focus();\n");

  echo("  </script>\n\n");

  echo("</body>\n");
  echo("</html>\n");
?>
