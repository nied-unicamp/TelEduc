<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/consultar_base.php

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
  ARQUIVO : administracao/consultar_base.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("  }\n");

  echo(" function Verifica() {\n");
  echo("    query = document.frmEnviar.query.value;\n");
  echo("    if (query == '') {\n");
  /* 17 - Algum campo não foi preenchido! */
  echo("      alert('".RetornaFraseDaLista($lista_frases_adm,17)."');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista=RetornaListaBases($sock);

  Desconectar($sock);

  session_register("consulta_s");
  session_register("base_s");
  session_register("anotacoes_s");

  if (isset($apagar_anotacoes))
    $anotacoes_s="";
  elseif (isset($apagar_consulta))
  	$consulta_s="";
  else
    $anotacoes_s=implode(" ",explode("\n",$consulta_s))."\n".$anotacoes_s;

  $anotacoes=$anotacoes_s;

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 5 - Consulta a Base de Dados */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,5)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<!-- Tabelao -->\n");
  echo("<form name=\"frmEnviar\" action=\"consultar_base2.php\" method=\"post\" onSubmit=\"return(Verifica());\">\n");
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  echo("<td><ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("<li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 71 - Apagar anota��es */
  echo("<li><a href=\"#\" title=\"".RetornaFraseDaLista($lista_frases,71)."\" onClick=\"document.location='consultar_base.php?apagar_anotacoes=sim'\">".RetornaFraseDaLista($lista_frases,71)."</a></li>\n");
  /* 532 - Apagar Consulta */
  echo("<li><a href=\"#\" title=\"".RetornaFraseDaLista($lista_frases,532)."\" onClick=\"document.location='consultar_base.php?apagar_consulta=sim'\">".RetornaFraseDaLista($lista_frases,532)."</a></li>\n");
  echo("</ul></td></tr>\n");
  echo("<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");


  /* 40 - Base de Dados: */
  echo("<tr class=\"head\"><td colspan=\"2\">".RetornaFraseDaLista($lista_frases,40)."</td></tr>\n");

  echo("<tr><td colspan=\"2\"><select name=\"base\" class=\"input\">\n");

  /* Selecionar Bases aqui... */
  if (isset($base_s))
    echo(" <option value='".$base_s."'>".$base_s."\n");

  /* 67 - Todas os Cursos */
  echo(" <option value='".RetornaFraseDaLista($lista_frases,67)."'>".RetornaFraseDaLista($lista_frases,67)."\n");

  foreach($lista as $cod => $linha)
  {
    echo(" <option value='".$linha."'>".$linha."\n");
  }
  echo("</select</td></tr>\n");

  /* 39 - Consulta SQL: */
  echo("<tr class=\"head01\"><td>".RetornaFraseDaLista($lista_frases,39)."</td>\n");
  /* 41 - Anota��es: */
  echo("<td>".RetornaFraseDaLista($lista_frases,41)."</td></tr>\n");

  echo("<tr><td><textarea class=\"input\" name=\"consulta\" cols=\"50\" rows=\"7\">".$consulta_s."</textarea></td>\n");

  echo("<td><textarea class=\"input\" name=\"anotacoes\" cols=\"50\" rows=\"7\">".$anotacoes."</textarea></td></tr>\n");

  echo("</table>\n");

  echo("<div align=\"right\">\n");
  /* 11 - Enviar (Ger) */
  echo("<input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\" type=\"submit\" />\n");
  echo("</div>\n");

  echo("</table></form></td></tr>\n");
  include("../rodape_tela_inicial.php");
  echo("</body>\n");
  echo("</html>\n");
?>