<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/correio_grafico_periodo.php

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
  ARQUIVO : cursos/aplic/intermap/correio_grafico_periodo.php
  ========================================================== */

  include("correio.inc");

  $linha_curso=RetornaDadosCurso($sock,$cod_curso);

  GeraJSVerificacaoData();
  GeraJSComparacaoDatas();

  echo("<script type=\"text/javascript\">\n");
  echo("  function Valida()\n");
  echo("  {\n");
  echo("    if (ComparaData(document.mapa.inicio,document.mapa.fim)>0)\n");
  echo("    {\n");

  // 57 - Per�odo Inv�lido
  echo("      alert('".RetornaFraseDaLista($lista_frases,57)."!');\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    else\n");
  echo("      return true;\n");
  echo("  }\n");
  echo("</script>\n");

  echo("<form name=\"mapa\" action=\"correio_grafico_periodo2.php\" target=\"Intermap\" method=\"get\" onsubmit=\"return(Valida());\">\n");

  //echo(RetornaSessionIDInput()."\n");
  echo("<input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("<input type=\"hidden\" name=\"todos\"     value=\"sim\" />\n");

  echo("<table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("  <tr class=\"head\">\n");
  // 7 - Apresenta��o:
  echo("    <td width=\"50%\">".RetornaFraseDaLista($lista_frases,7)."</td>\n");
  // 58 - Per�odo:
  echo("    <td>".RetornaFraseDaLista($lista_frases,58)."</td>\n");
  echo("  </tr>\n");

  echo("  <tr>\n");
  echo("    <td>\n");
  // 31 - Gr�fico
  echo("      <input type=\"radio\" checked class=\"g1field\" name=\"apresentacao\" value=\"grafico\" />".RetornaFraseDaLista($lista_frases,31));
  // 68 - Tabela
  echo("      <br/><input type=\"radio\" class=\"g1field\" name=\"apresentacao\" value=\"tabela\" />".RetornaFraseDaLista($lista_frases,68));
  echo("    </td>\n");

  echo("    <td>\n");
  // 19 - De:
  echo("      ".RetornaFraseDaLista($lista_frases,19)."&nbsp; \n");
  echo("      <input class=\"input\" size=\"10\" maxlength=\"10\" id=\"data_ini\" name=\"inicio\" value=\"".UnixTime2Data($linha_curso['curso_inicio'])."\" type=\"text\" />\n");
  echo("      <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_ini'),'dd/mm/yyyy',this);\" />\n");
  if ($linha_curso['curso_fim']>time())
  {
    $linha_curso['curso_fim']=time();
  }
  // 12 - At�:
  echo("      <br/>".RetornaFraseDaLista($lista_frases,12)." \n");
  echo("      <input class=\"input\" size=\"10\" maxlength=\"10\" id=\"data_ini2\" name=\"fim\" value=\"".UnixTime2Data($linha_curso['curso_fim'])."\" type=\"text\" />\n");
  echo("      <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_ini2'),'dd/mm/yyyy',this);\" />\n");
  echo("    </td>\n");
  echo("  </tr>\n");

  echo("  <tr class=\"head\">\n");
  // 6 - Agrupar por:
  echo("    <td>".RetornaFraseDaLista($lista_frases,6)."</td>\n");
  // 25 - Exibir:
  echo("    <td>".RetornaFraseDaLista($lista_frases,25)."</td>\n");
  echo("  </tr>\n");

  echo("  <tr>\n");
  echo("    <td>\n");
  // 21 - Dia
  echo("      <input type=\"radio\" checked class=\"g1field\" name=\"agrupar\" value=\"dia\" />".RetornaFraseDaLista($lista_frases,21)."<br/>\n");
  // 63 - Semana
  echo("      <input type=\"radio\" class=\"g1field\" name=\"agrupar\" value=\"semana\" />".RetornaFraseDaLista($lista_frases,63)."<br/>\n");
  // 46 - M�s
  echo("      <input type=\"radio\" class=\"g1field\" name=\"agrupar\" value=\"mes\" />".RetornaFraseDaLista($lista_frases,46)."<br/>\n");
  echo("    </td>\n");

  echo("    <td>\n");
  // 73 - Todos os participantes
  echo("      <input type=\"radio\" checked class=\"g1field\" name=\"exibir\" value=\"todos\" />".RetornaFraseDaLista($lista_frases,73)."<br/>\n");
  // 66 - Somente Formadores
  echo("      <input type=\"radio\" class=\"g1field\" name=\"exibir\" value=\"formador\" />".RetornaFraseDaLista($lista_frases,66)."<br/>\n");
  // 65 - Somente Alunos
  echo("      <input type=\"radio\" class=\"g1field\" name=\"exibir\" value=\"aluno\" />".RetornaFraseDaLista($lista_frases,65)."<br/>\n");
  // 84 - Somente Colaboradores
  //echo("      <input type=\"radio\" class=\"g1field\" name=\"exibir\" value=\"colaboradores\">".RetornaFraseDaLista($lista_frases,84)."<br/>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("</table>\n");

  echo("<div align=\"right\">\n");
  // 53 - Ok
  echo("  <input class=\"input\" type=\"submit\" value='".RetornaFraseDaLista($lista_frases,53)."' />\n");
  echo("</div>\n");

  echo("</form>\n");

?>
