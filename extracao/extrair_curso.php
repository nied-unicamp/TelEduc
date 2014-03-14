<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/extracao/extrair_curso.php

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
  ARQUIVO : administracao/extracao/extrair_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("extracao.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("<script language=\"javascript\" type=\"text/javascript\">\n");
  echo("  function Iniciar() {\n");
  echo("	startList();\n");
  echo("  }\n");
  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  Desconectar($sock);


  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  echo("        <!-- Tabelao -->\n");
  /* 4 - Extra��o de Curso */
  echo("        <h4>".RetornaFraseDaLista($lista_frases,4)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <form name=\"frmExtrair\" action=\"extrair_curso2.php?\" method=\"get\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\" id=\"tabelaExterna\">\n");

  echo("          <tr>\n");

  echo("            <td>\n");
  echo("              <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='../administracao/index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("              </ul>\n");
  echo("            </td>\n");
  echo("          </tr>\n");
  echo("          <tr>\n");
  echo("            <td valign=\"top\">\n");
  echo("              <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("              <tr class=\"head\">\n");
  echo("                <td colspan=\"2\">\n");
  /* 108 - Selecione abaixo o curso a ser extra�do* do ambiente TelEduc: */
  echo(RetornaFraseDaLista($lista_frases,108)."\n");
  echo("                </td>\n");
  echo("              </tr>\n");

  $lista=RetornaCursosExtraiveis();

  if (count($lista)>0)
  {
    echo("              <tr>\n");
    echo("                <td width=\"45%\" align=\"right\">\n");
    echo("                  <select class=\"input\" name=\"cod_curso\">\n");
    foreach ($lista as $cod_curso => $nome)
      echo("                    <option value=\"".$cod_curso."\">".$nome."</option>\n");
    echo("                  </select>\n");
    echo("                </td>\n");
    echo("                <td align=\"left\">\n");
    /* COLOQUEI */
    /* 526 - Remover o curso da Base de Dados.*/
    echo("                  <input type=\"checkbox\" name=\"check_remover\" checked />".RetornaFraseDaLista($lista_frases,526)."</td></tr>");
  }
  else
    /* 118 - Nenhum curso dispon�vel. */
    echo("<tr><td>".RetornaFraseDaLista($lista_frases,118)."</td></tr>\n");

  /* 333 - A Remocao de Curso eh uma ferramenta criada para salvar um curso exatamente no estado em que ele esta, e assim podendo transferir para outro servidor ou para usar posteriormente como um backup. Essa remocao extrai todos os dados e arquivos armazenados, salvando-os na pasta extraidos na home do TelEduc.*/
  /* 334 - Voce possui a opcao de Remover o curso da Base de Dados. Com essa opcao selecionada alem de extrair todos os dados e arquivos do curso, esse curso sera removido da lista de cursos do seu TelEduc. Caso voce queira salvar os dados do curso, porem nao gostaria que esse curso fosse removido da lista de cursos, deixe esta opcao desmarcada.*/
  echo("<tr><td colspan=\"2\" align=\"left\"><p style=\"text-indent:15px\">* ".RetornaFraseDaLista($lista_frases,344)."</p>\n");
  echo("<p style=\"text-indent:15px\">".RetornaFraseDaLista($lista_frases,345)."</p></td></tr>\n\n");

  echo("              </table>\n");

  echo("              <div align=right>\n");

  if (count($lista)>0)
    /* 110 - Extrair */
    echo("                <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,110)."\" onClick=\"document.frmExtrair.submit();\" type=\"button\" />\n");

  echo("              </div>\n");
  
  echo("          </table>\n");
  echo("      </form>\n"); 
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../cursos/aplic/tela2.php");
  echo("</body>\n");
  echo("</html>\n");
?>
