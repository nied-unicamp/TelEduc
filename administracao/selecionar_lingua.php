<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/selecionar_lingua.php

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
  ARQUIVO : administracao/selecionar_lingua.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");

  VerificaAutenticacaoAdministracao();

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"AtualizaIdiomaDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();


  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\" defer>\n\n");

  echo("      function Cancela()\n");
  echo("      {\n");
  echo("        document.frmLing.action = \"configurar.php?\";\n");
  echo("        document.frmLing.submit();\n");
  echo("      }\n\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("    </script>\n");
  /* Fim do JavaScript */

  $objAjax->printJavascript();

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 137 - Selecionar Idioma padr�o*/
  echo("          <h4>".RetornaFraseDaLista($lista_frases,137)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 137 - Selecionar Idioma Padr�o */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,137)."\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases,137)."</span></li>\n");
  /* 518 - Configurações */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,518)."\" onClick=\"document.location='configurar_solicitacao.php'\">".RetornaFraseDaLista($lista_frases,518)."</span></li>\n");
  /* 519 - Alterações */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,519)."\" onClick=\"document.location='atualizar_administrador.php'\">".RetornaFraseDaLista($lista_frases,519)."</span></li>\n");
  /* 7 - Atualizar lista de respons�veis pelo Ambiente */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,7)."\" onClick=\"document.location='atualizar_responsaveis.php'\">".RetornaFraseDaLista($lista_frases,7)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  /* 137 - Selecionar l�ngua padr�o*/
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,137)."</td>\n");
  echo("                  </tr>\n");
  /* 138 - Selecione abaixo qual l�ngua deseja que seja a primeira a ser exibida ao ser acessada a p�gina principal */
  echo("                  <tr>\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases,138)."\n");
  echo("                      <form id=\"form_lingua\" name=\"form_lingua\" action=\"selecionar_lingua2.php\" method=\"post\">\n");
  echo("                        <select class=\"input\" name=\"cod_lin\" onchange=\"xajax_AtualizaIdiomaDinamic(this.value);\">\n");

  $sock=Conectar("");
  $lista=ListaLinguas($sock);
  Desconectar($sock);

  foreach ($lista as $cod_lin => $lingua)
  {
    $sel="";
    if ($cod_lin == $cod_lingua_s)
      $sel="selected";
    echo("                            <option ".$sel."  value=\"".$cod_lin."\" >".$lingua."</option>\n");
  }

  echo("                        </select>\n");
  echo("                      </form>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>