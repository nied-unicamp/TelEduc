<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/selecionar_lingua_conf.php

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
  ARQUIVO : administracao/configurar_solicitacao.php
  ========================================================== */

  /** Entradas:
        * configurar.php: PHPSESSION

      Sa�das:
        * configurar.php: PHPSESSID

        * configurar_solicitacao2.php: PHPSESSID
                                       curso_form
                                       normas
  */


  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"AtualizaConfiguracoesDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\" defer>\n\n");
 
  echo("      function RespostaUsuario(sucesso)\n");
  echo("      {\n");
  echo("        if(sucesso)\n");
  /* 26 - Informa��es alteradas com sucesso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,26)."');\n");
  echo("        else\n");
  /* 190 - Ocorreu um erro na atualiza��o dos requisitos ou na escolha da forma de solicita��o para abertura de curso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,190)."');\n");
  echo("      }\n\n");

  echo("      function Enviar()\n");
  echo("      {\n");
  echo("        var array = document.getElementsByName('curso_form');\n");
  echo("        for(i=0;i<array.length;i++)\n");
  echo("        {\n");
  echo("          if(array[i].checked)\n");
  echo("            curso_form = array[i].value;\n");
  echo("        }\n");
  echo("        var normas = document.frmSolic.normas.value;\n");
  echo("        xajax_AtualizaConfiguracoesDinamic(curso_form,normas);\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
 /* 184 - Configurar forma de solicita��o de curso */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,184)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <form name=\"frmSolic\" action=\"configurar_solicitacao2.php\" onsubmit=\"return(Enviar());\" method=\"post\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 184 - Configurar forma de solicita��o de curso */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,184)."\" onClick=\"document.location='configurar_solicitacao.php'\">".RetornaFraseDaLista($lista_frases,184)."</span></li>\n");
  /* 6 - Configurar dados instituicionais */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,6)."\" onClick=\"document.location='configurar_dados_institucionais.php'\">".RetornaFraseDaLista($lista_frases,6)."</span></li>\n");
  /* 194 - Configurar endere�o para acesso e estrutura de pastas */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,194)."\" onClick=\"document.location='configurar_pastas.php'\">".RetornaFraseDaLista($lista_frases,194)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $curso_form = RetornaConfig("curso_form");
  $normas = RetornaConfig("normas");

  /* 185 - Selecione a forma como o usu�rio poder� solicitar abertura de cursos: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,185)."</td>\n");
  echo("                  </tr>\n");

  $checked[1] = $checked[2] = "";
  if ($curso_form == "nao")
    $checked[1] = " checked";
  else if ($curso_form == "sim")
    $checked[2] = " checked";

  /* 186 - Apenas e-mail de contato para solicitar abertura de cursos. */
  echo("                  <tr>\n");
  echo("                    <td><input type=\"radio\" name=\"curso_form\" value='nao'".$checked[1]." />".RetornaFraseDaLista($lista_frases,186)."</td>\n");
  echo("                  </tr>\n");
  /* 187 - Formul�rio para solicitar abertura de cursos. */
  echo("                  <tr>\n");
  echo("                    <td><input type=\"radio\" name=\"curso_form\" value='sim'".$checked[2]." />".RetornaFraseDaLista($lista_frases,187)."</td>\n");
  echo("                  </tr>\n");
  /* 188 - Editar requisitos necess�rios para abertura de cursos: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,188)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td>\n");
  echo("                      <textarea class=\"input\" name=\"normas\" cols=\"60\" rows=\"5\" style=\"wrap: soft\">".$normas."</textarea><br />\n");
  /* 189 - (Deixe em branco para n�o ser exibido) */
  echo("                      <font size=-1>".RetornaFraseDaLista($lista_frases,189)."</font>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("                <div align=right>\n");
  /* 24 - Alterar (ger) */
  echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,24)."\" type=\"submit\" />\n");
  echo("                </div>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>