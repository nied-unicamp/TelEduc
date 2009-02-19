<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/estatistica/tam_curso.php

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
  ARQUIVO : administracao/estatistica/tam_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("estat.inc");
  include("../topo_tela_inicial.php");

  VerificaAutenticacaoAdministracao();

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("function Iniciar() {\n");
  echo("	startList();\n");
  echo("}\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $diretorio = RetornaDiretorio('Arquivos');

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,-5);


  echo("    <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 156 - Tamanho dos Arquivos dos Cursos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,156)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("      <!-- Tabelao -->\n");
  echo("      <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("      <td>\n");
  echo("        <ul class=\"btAuxTabs\">\n");





  /* 23 - Voltar (Ger) */
  echo("          <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='../administracao/index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");

  /* 154 - Quantidade de Cursos */
  echo("          <li><span style=\"href: #\" title=\"Quantidade de Cursos\" onClick=\"document.location='num_cursos.php'\">".RetornaFraseDaLista($lista_frases,154)."</span></li>\n");

  /* 155 - Quantidade de Alunos e Formadores por Curso */
  echo("          <li><span style=\"href: #\" title=\"Quantidade de Alunos e Formadores por Curso\" onClick=\"document.location='alunos_curso.php'\">".RetornaFraseDaLista($lista_frases,155)."</span></li>\n");

  /* 156 - Tamanho dos Arquivos dos Cursos */
  echo("          <li><span style=\"href: #\" title=\"Tamanho dos Arquivos dos Cursos\" onClick=\"document.location='tam_curso.php'\">".RetornaFraseDaLista($lista_frases,156)."</span></li>\n");

  echo("        </ul>\n");
  echo("      </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  echo("      <tr>\n");
  echo("        <td colspan=2>\n");
  FraseTipoAgrupamento('tam_curso.php',$agrupar);
  echo("        </td>\n");
  echo("      </tr>\n");

  if (!isset($agrupar) || $agrupar==0)
    ImprimeTamanhoDosCursos($sock,$diretorio,$linha['cod_pasta']);
  else
  {
    $pastas=RetornaPastas($sock);
    if (is_array($pastas))
    {
      foreach ($pastas as $cod => $linha)
      {
        /* 169 - Categoria */
        echo("      <tr class=\"head\">\n");
        echo("        <td colspan=4>".RetornaFraseDaLista($lista_frases,169).": ");
        echo($linha['pasta']."\n");
        echo("        </td>\n");
        echo("      </tr>\n");
        ImprimeTamanhoDosCursos($sock,$diretorio,$linha['cod_pasta']);
      }
    }
    /* 170 - Sem Categoria */
    echo("      <tr class=\"head\">\n");
    echo("        <td colspan=4>".RetornaFraseDaLista($lista_frases,170)."</td>\n");
    echo("      </tr>\n");
    $cod_pasta_null='nenhum';
    ImprimeTamanhoDosCursos($sock,$diretorio,$cod_pasta_null);

  }

  echo("</table>\n");
echo("</table>\n");
include("../rodape_tela_inicial.php");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
