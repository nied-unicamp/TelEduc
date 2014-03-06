<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/configurar_dados_institucionais.php

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
  ARQUIVO : administracao/configurar_dados_institucionais.php
  ========================================================== */

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
  $objAjax->register(XAJAX_FUNCTION,"AtualizaDadosInstDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\" type=\"text/javascript\">\n\n");

  echo("      function RespostaUsuario()\n");
  echo("      {\n");
  /* 26 - Informa��es alteradas com sucesso. */
  echo("        alert('".RetornaFraseDaLista($lista_frases_adm,26)."');\n");
  echo("      }\n\n");

  echo("      function Enviar()\n");
  echo("      {\n");
  echo("        var nome = document.frmInst.nome.value\n;");
  echo("        var informacoes = document.frmInst.informacoes.value;\n");
  echo("        var link = document.frmInst.link.value;\n");
  echo("        xajax_AtualizaDadosInstDinamic(nome,informacoes,link);\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function Iniciar() {\n");
  echo("	 startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 6 - Configurar dados institucionais */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,6)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <form name=\"frmInst\" action=\"configurar_dados_institucionais2.php\" method=\"post\" onsubmit=\"return(Enviar());\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 184 - Configurar forma de solicita��o de curso */
  echo("                  <li><span style=\"href: #\" title=\"Configurar forma de solicita��o de curso\" onClick=\"document.location='configurar_solicitacao.php'\">".RetornaFraseDaLista($lista_frases,184)."</span></li>\n");
  /* 6 - Configurar dados instituicionais */
  echo("                  <li><span style=\"href: #\" title=\"Configurar dados instituicionais\" onClick=\"document.location='configurar_dados_institucionais.php'\">".RetornaFraseDaLista($lista_frases,6)."</span></li>\n");
  /* 194 - Configurar endere�o para acesso e estrutura de pastas */
  echo("                  <li><span style=\"href: #\" title=\"Configurar endere�o para acesso e estrutura de pastas\" onClick=\"document.location='configurar_pastas.php'\">".RetornaFraseDaLista($lista_frases,194)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $lista=RetornaDadosIntituicao();

  /* 23 - Nome: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,23)."</td>\n");
  /* 25 - Endere�o na Internet (URL): (exemplo: http://www.nied.unicamp.br) */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,25)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"nome\" size=\"40\" value='".ConverteAspas2Html($lista['nome'])."' /></td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"link\" size=\"40\" value='".ConverteAspas2Html($lista['link'])."' /></td>\n");
  echo("                  </tr>\n");
  /* 24 - Informa��es: (endere�o, telefone, email) */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,24)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td colspan=\"2\"><textarea class=\"input\" name=\"informacoes\" cols=\"80\" rows=\"10\">".$lista['informacoes']."</textarea></td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("                <div align=\"right\">\n");
  /* 11 - Enviar (ger) */
  echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\"  type=\"submit\" />\n");
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