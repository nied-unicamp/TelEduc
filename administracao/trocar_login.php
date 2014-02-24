<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/trocar_login.php

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
  ARQUIVO : administracao/trocar_login.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"SugerirLoginDinamic");
  $objAjax->register(XAJAX_FUNCTION,"TrocaLoginUsuario");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  echo("    <script type=\"text/javascript\">\n");

  echo("      var flagOnDivSugs=0;");

  echo("      function Iniciar() {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("      function TrocaLogin(login_antigo,novo_login)\n");
  echo("      {\n");
  echo("        if(login_antigo != '' && novo_login != '')\n");
  echo("          xajax_TrocaLoginUsuario(login_antigo,novo_login);\n");
  echo("        else\n");
  //504 - Ambos os campos devem ser preenchidos! 
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,504)."');\n");
  echo("        return false;\n");
  echo("      }\n");

  echo("      function RespostaUsuario(res)\n");
  echo("      {\n");
  //505 - Novo login ja existe.Escolha um login diferente.
  echo("        if(res == 0)\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,505)."');\n");
  echo("          document.getElementById('novo_login').value = '';\n");
  echo("        }\n");
  //506 - Login alterado com sucesso ! 
  echo("        if(res == 2)\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,506)."');\n");
  echo("          document.getElementById('login').value = '';\n");
  echo("          document.getElementById('novo_login').value = '';\n");
  echo("        }\n");
  //507 - Login Atual inexistente.Digite-o novamente ou utilize a sugestao de login (recomendado). 
  echo("        if(res == 1)\n");
  echo("        {\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,507)."');\n");
  echo("          document.getElementById('login').value = '';\n");
  echo("        }\n");
  echo("      }\n");

  echo("    function TesteBlur()");
  echo("    {\n");
  echo("      if(flagOnDivSugs == 0)\n");
  echo("      {\n");
  echo("        document.getElementById('tr_sugs').style.display='none';\n");
  echo("        document.getElementById('divSugs').style.display='none';\n");
  echo("      }\n");
  echo("    }\n");

  echo("    </script>\n");

  $objAjax->printJavascript();
  //   fim do javascript

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  /* Fim da Checagem de novo Patch */
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 8 - Trocar login */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,8)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <form name=\"frmCriar\" action=\"\" method=\"get\" onsubmit=\"return(TrocaLogin(this.login.value,this.novo_login.value));\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span onClick=\"document.location='index.php';\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,8)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>");
  echo("                    <td align=\"center\" style=\"border:none;\">\n");
  echo("                      <table>");
  /* 67 - Login Atual */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases_configurar,67).": </b></td>\n");
  /* 520 - Sugestões */
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo("<input autocomplete=\"off\" type=\"text\" class=\"input\" name=\"login\" id=\"login\" size=\"13\" style=\"maxlenght: 20\" onkeyup=\"xajax_SugerirLoginDinamic(this.value,'".RetornaFraseDaLista($lista_frases, 520)."');\" onblur=\"TesteBlur();\" /></td>\n");
  echo("                        </tr>\n");
  echo("                        <tr id=\"tr_sugs\" style=\"display:none;\">\n");
  echo("                          <td style=\"text-align:right;border:none;\">&nbsp;</td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo("<div id=\"divSugs\" style=\"display:none;background-color:#FFF;position:absolute;border:1pt solid #EEE;padding:5px; margin-top:-22px;\" onmouseover=\"flagOnDivSugs=1;\" onmouseout=\"flagOnDivSugs=0;\">&nbsp;</div></td>\n");
  echo("                        </tr>\n");
  /* 68 - Novo Login */
  echo("                        <tr>\n");
  echo("                          <td style=\"text-align:right;border:none;\"><b>".RetornaFraseDaLista($lista_frases_configurar,68).": </b></td>\n");
  echo("                          <td style=\"text-align:left;border:none;\">\n");
  echo("<input type=\"text\" class=\"input\" name=\"novo_login\" id=\"novo_login\" size=\"13\" style=\"maxlenght: 20\"/></td>\n");
  echo("                        </tr>\n");
  echo("                      </table>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td align=\"right\">\n");
  /* 65 - Alterar Login */
  echo("                <input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_configurar,65)."\">\n");
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