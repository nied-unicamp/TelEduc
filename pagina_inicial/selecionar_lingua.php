<?php 
/* 
<!--  
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/selecionar_lingua.php

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
  ARQUIVO : pagina_inicial/selecionar_lingua.php
  ========================================================== */
    
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"AtualizaIdiomaDinamic");
  
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();
 
  $pag_atual = "selecionar_lingua.php";
  include("../topo_tela_inicial.php");

  /* Caso o usuário naum esteja logado, direciona para páigna de login */
  if (empty($_SESSION['login_usuario_s']))
  {
    /* Obt� a raiz_www */
    //$sock = Conectar("");
    $query = "select diretorio from Diretorio where item = 'raiz_www'";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $raiz_www = $linha[0];

    $caminho = $raiz_www."/pagina_inicial";

    header("Location: {$caminho}/autenticacao_cadastro.php");
    Desconectar($sock);
    exit;
  }
  
  $cod_usuario = $_SESSION['cod_usuario_global_s'];

  $lista=ListaLinguas($sock);

  /*
  ==================
  Fun��o JavaScript
  ==================
  */
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar(){\n");
  echo("        document.form_lingua.cod_lin.focus();\n");
  echo("        startList();\n");
  echo("      }\n\n");
  
  echo("    </script>\n");
  
  $objAjax->printJavascript();

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Configurar *//* 3 - Selecionar Idioma*/
  echo("          <h4>".RetornaFraseDaLista($lista_frases_configurar,1)." - ".RetornaFraseDaLista($lista_frases_configurar,3)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 27 - Alterar dados pessoais*/
  echo("                  <li><a href=\"dados.php\" id=\"alterar_dados\">".RetornaFraseDaLista($lista_frases_configurar, 27)."</a></li>\n");
  /* 65 - Alterar Login */
  echo("                  <li><a href=\"alterar_login.php\" id=\"alterar_login\">".RetornaFraseDaLista($lista_frases_configurar, 65)."</a></li>\n");
  /* 2 - Alterar Senha */
  echo("                  <li><a href=\"alterar_senha.php\" id=\"alterar_senha\">".RetornaFraseDaLista($lista_frases_configurar, 2)."</a></li>\n");
  /* 3 - Alterar Idioma */
  echo("                  <li><a href=\"selecionar_lingua.php\" id=\"selecionar_idioma\">".RetornaFraseDaLista($lista_frases_configurar, 3)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <form name=\"form_lingua\"  id=\"form_lingua\" action=\"\" method=\"post\">\n");
  echo("                  <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                    <tr class=\"head\">\n");
  /* Selecionar Idioma */
  echo("                      <td>".RetornaFraseDaLista($lista_frases_configurar,3)."</td>\n");
  echo("                    </tr>\n");
  echo("                    <tr class=\"tabInterna\">\n");
  echo("                      <td class=\"alLeft\">\n");
  echo("                        <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
  // 18 - Selecione a l�ngua em que deseja visualizar o ambiente: 
  echo("                        <p>&nbsp;&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases_configurar,18)."&nbsp;\n");
  echo("                          <select class=\"input\" name=\"cod_lin\" onchange=\"xajax_AtualizaIdiomaDinamic(xajax.getFormValues('form_lingua'));\">\n");

  $cod_lingua_usuario=RetornaCodLinguaUsuario($sock,$cod_usuario);
 
  foreach ($lista as $cod_lin => $lingua)
  {
    $sel="";
    if ($cod_lin == $cod_lingua_usuario)
      $sel="selected=\"selected\"";
    echo("                            <option ".$sel."  value=\"".$cod_lin."\">".$lingua."</option>\n");
  }

  echo("                          </select>\n");
  echo("                        </p>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                  </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
