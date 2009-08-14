<? 
/* 
<!--  
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/cadastro.php

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
  ARQUIVO : pagina_inicial/cadastro.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("CadastraDadosUsuarioDinamic");
  $objAjax->registerFunction("CadastrarLogar");
  
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $pag_atual = "cadastro.php";
  include("../topo_tela_inicial.php");

  $lista_escolaridade=RetornaListaEscolaridade($sock);
  
  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /*185 - Confirmação*/
  echo("          <h4>".RetornaFraseDaLista($lista_frases,204)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  /* 23 - Voltar (gen) */
  echo("              <ul class=\"btAuxTabs\">\n");
  echo("                <li><a href='autenticacao.php'>".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("              </ul>\n");
  echo("            </tr>");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,211)."</b></td>\n");
  echo("                  </tr>");
  echo("                  <tr>\n");
  echo("                    <td align=\"center\" style=\"padding:25px 5px 25px 5px; text-indent:25px;\">\n");
  
  
  $user = $_GET["u"];
  $seq = $_GET["s"];
  
  if(ConfirmaUsuario($user, $seq)) {
  	//198 - Seu cadastro foi efetivado com sucesso!
  	//199 - Clique
  	//200 - aqui
  	//201 - para efetuar o login.
  	echo(RetornaFraseDaLista($lista_frases,205)." ".RetornaFraseDaLista($lista_frases,206));
  	echo(" <a href='autenticacao.php' alt='Efetuar login'>".RetornaFraseDaLista($lista_frases,207)."</a> ".RetornaFraseDaLista($lista_frases,208));
  }
  else {
  	//202 - Problemas na confirmação do cadastro. Contate o administrador.
  	echo(RetornaFraseDaLista($lista_frases,209));
  }
  
  
  
  echo("                    </td>\n"); 
  echo("                  </tr>\n");
  echo("				</table>");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n"); 
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
  
?>
