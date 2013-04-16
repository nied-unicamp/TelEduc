<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/resetar_curso.php

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
  ARQUIVO : administracao/resetar_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");
  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  VerificaAutenticacaoAdministracao();

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 245 - Reutiliza��o de Cursos Encerrados */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,245)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<form name=\"frmReset\" action=\"resetar_curso2.php?\" method=\"get\">\n");
  echo("<!-- Tabelao -->\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  echo("<td>\n");
  echo("<ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("<li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("</ul>\n");
  echo("</td>\n");
  echo("</tr>\n");
  echo("<tr>\n");
  echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  /* 246 - Selecione abaixo o curso a ser reutilizado* no ambiente TelEduc: */
  echo("<tr class=\"head\">\n");
  echo("<td>".RetornaFraseDaLista($lista_frases,246)."</td>\n");
  echo("</tr>\n");

  echo("<tr>\n");
  echo("<td>&nbsp;&nbsp;\n");

  $lista=RetornaCursosExtraiveis();

  if (count($lista)>0)
  {
    echo("<select name=\"cod_curso\" class=\"input\">\n");
    foreach ($lista as $cod_curso => $nome)
      echo("<option value=".$cod_curso.">".$nome."</option>\n");
    echo("</select>\n");
  }
  else
    /* 118 - Nenhum curso dispon�vel. */
    echo(RetornaFraseDaLista($lista_frases,118)."\n");

  echo("</td>\n");
  echo("</tr>\n");
  echo("</table>\n");
  echo("<div align=\"right\">\n");

  if (count($lista)>0)
    /* 247 - Reutilizar */
    echo("<input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,247)."\" onClick=\"document.frmReset.submit();\" type=\"button\" />\n");

  echo("</div>\n");

  echo("</td>\n");
  echo("</tr></table>\n");

  echo("</form>\n");

  echo("</td></tr>\n");
  include("../rodape_tela_inicial.php");
  echo("</body>\n");
  echo("</html>\n");
?>
