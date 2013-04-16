<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/configurar/configurar.php

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
  ARQUIVO : cursos/aplic/configurar/configurar.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("configurar.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax

  $objAjax->registerFunction("AtualizaPlanoNotificacaoDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_ferramenta=-7;
  // variaveis passadas para a funcao PreparaAjuda que eh chamada no menu_principal.php
  $cod_ferramenta_ajuda=-1;
  $cod_pagina_ajuda=4;

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 25 - Plano de notifica��es de novidades alterado com sucesso.
  //  - Erro na altera��o de notificar novidades.
  $feedbackObject->addAction("atualiza_notificacao", 25, "Erro na altera��o de notificar novidades.");

   /*
  ==================
  Fun��es JavaScript
  ==================
  */
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
                $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function Cancelar()\n");
  echo("      {\n");
  echo("        document.frmNotificar.action='configurar.php';\n");
  echo("        document.frmNotificar.submit();\n");
  echo("      }\n\n");

  echo("      function Recarregar(status) \n");
  echo("      { \n");
  echo("        document.location='configurar.php?cod_curso=".$cod_curso."&acao=atualiza_notificacao&atualizacao=true';");
  echo("      } \n");

  echo("    </script>\n\n");

  $objAjax->printJavascript("../xajax_0.2.4/");
  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Obt�m o plano de notifica��es de novidades via e-mail escolhido pelo usu�rio */
  $plano = RetornaPlanoNotificacao($sock, $cod_usuario, $cod_curso);

  $checked[0] = (($plano == 0) ? "checked=\"checked\"" : "");
  $checked[1] = (($plano == 1) ? "checked=\"checked\"" : "");
  $checked[2] = (($plano == 2) ? "checked=\"checked\"" : "");
  $checked[3] = (($plano == 3) ? "checked=\"checked\"" : "");
  

  /* 1 - Configurar */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."\n");
  echo("          </h4>\n");
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");

  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 20 - Notificar Novidades */
  echo("                    <td>".RetornaFrasedaLista($lista_frases,20)."</td>\n");
  echo("                  </tr>\n");
  
  echo("                  <tr class=\"tabInterna\">\n");
  echo("                    <td class=\"alLeft\">\n");
 
  /* 21 - Escolha o plano de envio de notifica��es de novidades para seu e-mail: */
  echo("                      <p>&nbsp;&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases,21)."</p>\n"); 

  echo("                      <form method=\"post\" name=\"frmNotificar\" id=\"frmNotificar\" action=\"\">\n");

  
  echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");

  /* 22 - N�o desejo receber notifica��es de novidades */
  echo("                        <p>\n");
  echo("                          &nbsp;&nbsp;&nbsp;\n");
  echo("                          <input type=\"radio\" name=\"plano\" value=\"0\" ".$checked[0]." onclick=\"xajax_AtualizaPlanoNotificacaoDinamic(xajax.getFormValues('frmNotificar'));\" />".RetornaFraseDaLista($lista_frases,22)."\n");
  echo("                        </p>\n");

  /* 23 - Resumo geral de novidades no final do dia */
  echo("                        <p>\n");
  echo("                          &nbsp;&nbsp;&nbsp;\n");
  echo("                          <input type=\"radio\" name=\"plano\" value=\"1\" ".$checked[1]."onclick=\"xajax_AtualizaPlanoNotificacaoDinamic(xajax.getFormValues('frmNotificar'));\" />".RetornaFraseDaLista($lista_frases,23)."\n");
  echo("                        </p>\n");

  /* 24 - Resumo parcial de novidades duas vezes ao dia */
  echo("                        <p>\n");
  echo("                          &nbsp;&nbsp;&nbsp;\n");
  echo("                          <input type=\"radio\" name=\"plano\" value=\"2\" ".$checked[2]."onclick=\"xajax_AtualizaPlanoNotificacaoDinamic(xajax.getFormValues('frmNotificar'));\" />".RetornaFraseDaLista($lista_frases,24)."\n");
  echo("                        </p>\n");
  
  echo("                      </form>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");
  
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
