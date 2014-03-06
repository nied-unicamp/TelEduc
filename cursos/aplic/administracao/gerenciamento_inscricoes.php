<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento_inscricoes.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/administracao/gerenciamento_inscricoes.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");
  
  if($_SESSION['login_existente']==true){
    $sock=Conectar($cod_curso);
    foreach ($_SESSION['dados'] as $cod => $linha){
      if($linha['status_login']==1){
        $linha['login']=$linha['novo_login'];
        $sock=CadastrarUsuario($sock,$cod_curso,$linha,$_SESSION['lista_frases'],$cod_usuario);
      }
    }
    Desconectar($sock);
    unset($_SESSION['login_existente']);
  }

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das funï¿½ï¿½es em PHP que vocï¿½ quer chamar atravï¿½s do xajax
  $objAjax->register(XAJAX_FUNCTION,"AtivarDesativarPortDinamic");
  $objAjax->register(XAJAX_FUNCTION,"PaginacaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MudaGuiaDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MudaDinamic");
  $objAjax->register(XAJAX_FUNCTION,"Paginacao");
  $objAjax->register(XAJAX_FUNCTION,"IniciaPaginacaoDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta       = 0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro Ã© a aÃ§Ã£o, o segundo Ã© o nÃºmero da frase para ser impressa se for "true", o terceiro caso "false"
  // 255 - Erro na operacao
  // 268 - InscriÃ§Ã£o realizada com sucesso
  $feedbackObject->addAction("inscrever_cadastrado", 268, 255);
  $feedbackObject->addAction("inscrever", 268, 255);

  $ecoordenador = ECoordenador($sock,$cod_curso,$cod_usuario);
  $cod_coordenador = RetornaCodigoCoordenador($sock, $cod_curso);

  if (!isset($tipo_usuario))
  {
    $tipo_usuario = 'i';
  }
  if (!isset($ordem))
  {
    $ordem = "nome";
  }

  /* 1 - Administraï¿½ï¿½o */
  $cabecalho = "          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n";

  switch ($tipo_usuario){
    case "i":
      /* 74 - Gerenciamento de Inscricoes */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 74);
      /* 75 - Inscricoes nao avaliadas */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases,75);
      /* 78 - No de Inscricoes: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases,78);
      $cod_pagina       = 8;
      $cod_pagina_ajuda = 8;
      break;
    /* Mas pode isso arnaldo? */
    case "A":
      /* 74 - Gerenciamento de Inscricoes */
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 74);
      /* 76 - Inscricoes aceitas */
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases,76);
      /* 78 - No de Inscricoes: */
      $frase_qtde = RetornaFraseDaLista($lista_frases,78);
      break;

    case "r":
      /* 74 - Gerenciamento de Inscricoes */
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 74);
      /* 77 - Inscricoes rejeitadas */
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases,77);
      /* 78 - No de Inscricoes: */
      $frase_qtde = RetornaFraseDaLista($lista_frases,78);
      break;

  }
  
  $ativado=RetornaFraseDaLista($lista_frases, 208);
  $desativado=RetornaFraseDaLista($lista_frases, 209);
  
  echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"paginacao.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"gerenciamento.js\"></script>\n");
  if ($ecoordenador) {
    echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"eventos_coordenador.js\"></script>\n");
  }
  else {
    echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"eventos.js\"></script>\n");
  }
  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var tipo_usuario = '".$tipo_usuario."';\n");
  echo("      var ordem = '".$ordem."';\n");
  echo("      var cod_curso = ".$cod_curso.";\n");
  echo("      var cod_usuario = ".$cod_usuario.";\n");
  echo("      var cod_ferramenta = ".$cod_ferramenta.";\n");
  /*79 - Dados */
  echo("      var fraseDados = '".RetornaFraseDaLista($lista_frases, 79)."';\n");
  /* 114 - Nenhuma pessoa selecionada */
  echo("      var fraseSemSelecao = '".RetornaFraseDaLista($lista_frases, 114)."';\n\n");
  
  echo("      var qtdPag    = 1;\n");
  echo("      var intervalo = 1;\n");
  echo("      var atual     = 1;\n");
  echo("      var aux       = 'T';\n\n");
  
  echo("      function Iniciar()\n");
  echo("      {\n");
                $feedbackObject->returnFeedback($_GET['acao_fb'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("        xajax_IniciaPaginacaoDinamic(".$cod_curso.",'".$tipo_usuario."','".$ordem."');\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    /* 1 - Administracao  28 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("          <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    echo("        </td>\n");
    echo("      </tr>\n");

    include("../tela2.php");

    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }
  
/*Forms*/
  echo("          <form action=\"acoes.php\" name=\"gerenc\" method=\"get\">\n");
  echo("            <input type=\"hidden\" name=\"cod_curso\"    value=\"".$cod_curso."\">\n");
  // variavel que indica o tipo de usuário.
  echo("            <input type=\"hidden\" name=\"tipo_usuario\" value=\"".$tipo_usuario."\">\n");
  echo("            <input type=\"hidden\" name=\"ordem\"        value=\"".$ordem."\">\n");
  echo("            <input type=\"hidden\" name=\"opcao\"        value=\"\">\n");
  echo("            <input type=\"hidden\" name=\"action_ger\"   value=\"nenhuma\">\n");
  echo("            <input type=\"hidden\" name=\"origem\"       value=\"gerenciamento_inscricoes.php\">\n");

  // Pï¿½gina Principal
  echo($cabecalho."</h4>");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("           <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("           <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("           <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table id=\"tabelaExterna\" cellpadding=\"0\" cellspacing=\"0\"  class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // 23 - Voltar (geral)
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");

  /* 75 - Inscriï¿½ï¿½es Nï¿½o Avaliadas */
  echo("                  <li><a href=\"gerenciamento_inscricoes.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=i&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,75)."</a></li>\n");
  /* 76 - Inscriï¿½ï¿½es Aceitas */
  echo("                  <li><a href=\"gerenciamento_inscricoes.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=A&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,76)."</a></li>\n");
  /* 77 - Inscriï¿½ï¿½es Rejeitadas */
  echo("                  <li><a href=\"gerenciamento_inscricoes.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=r&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,77)."</a></li>\n");
 
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  /* Código de montagem do conteúdo a partir daqui */
  $lista_usuarios = RetornaListaUsuarios($sock, $cod_curso, $tipo_usuario, $ordem);

  /* Sistema de Paginacao */
  
  $num=count($lista_usuarios);
  /* Numero de mensagens exibidas por pagina.*/
  $msg_por_pag=10;

  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tbgeren\">\n");
  echo("                  <tr class=\"head01 alLeft\">\n");
  echo("                    <td colspan=\"2\">");
  echo("                      ".$frase_qtde." ".$num."\n");
  echo("                    </td>\n");
  echo("                    <td colspan=\"2\" align=right>");
  echo("                      ".RetornaFraseDaLista($lista_frases,146)." <select name=\"ordem\" onChange=\"document.location='gerenciamento_inscricoes.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."&amp;ordem='+this[this.selectedIndex].value;\" style=\"margin:5px 0 0 0;\">\n");
  $tmp = ($ordem == "nome" ? " selected" : "");
  // 147 - nome
  echo("                        <option value=\"nome\"".$tmp.">".RetornaFraseDaLista($lista_frases,147)."\n");
  $tmp = ($ordem == "data" ? " selected" : "");
  // 155 - data de inscriï¿½ï¿½o
  echo("                        <option value=\"data\"".$tmp.">".RetornaFraseDaLista($lista_frases,155)."\n");
  echo("                      </select>\n");
  echo("                    </td>\n");
  echo("                  </tr>");
  echo("                  <tr class=\"head alLeft\">\n");
  echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"check_all\" id=\"check_all\" onclick=\"MarcaOuDesmarcaTodos();\"></td>\n");
  // 119 - Nome
  echo("                    <td align=\"left\"><b>".RetornaFraseDaLista($lista_frases,119)."</b></td>\n");
  // 132 - Data de inscricao
  echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,132)."</b></td>\n");
  // 79 - Dados
  echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,79)."</b></td>\n");
  echo("                  </tr>\n");

  if ($num==0)
  {
    echo("                  <tr>\n");
    echo("                    <td colspan=\"5\">\n");
    /* 104 - Nenhuma pessoa registrada. */
    echo("                      ".RetornaFraseDaLista($lista_frases,104)."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
  }
  else
  {
    foreach($lista_usuarios as $cod_usuario_l => $linha)
    { 
      if ($msg_por_pag>=1){
        echo("                  <tr name=\"germen\" id=\"ger\" style=\"display: table-row;\">\n");
        echo("                    <td width=\"1%\"><input type=\"checkbox\" name=\"cod_usu[]\" onclick=\"VerificaCheck();\" value=".$cod_usuario_l."></td>\n");
        echo("                    <td align=\"left\">".$linha['nome']."</td>\n");
        echo("                    <td>".Unixtime2Data($linha['data_inscricao'])."</td>\n");
        /* 79 - Dados */
        echo("                    <td><a href=\"gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."&amp;ordem=".$ordem."&amp;origem=gerenciamento_inscricoes.php&amp;action_ger=dados&amp;cod_usu[]=".$cod_usuario_l."\">".RetornaFraseDaLista($lista_frases,79)."</a></td>\n");
        echo("                  </tr>\n");
      }
      $msg_por_pag--;
    }
    echo("                </table>\n");
  }
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul>\n");
  /* 79 - Dados */ 
  echo("                  <li id=\"mDados_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,79)."</span id=\"dados_usu\"></li>\n");

  if ($tipo_usuario == 'i' || $tipo_usuario == 'r')
  {
    /* 80 - Aceitar */
    echo("                  <li id=\"mAceitar_Selec\" class=\"menuUp\"><span id=\"aceitar\">".RetornaFraseDaLista($lista_frases,80)."</span></li>\n");
  }

  if ($tipo_usuario == 'i')
  {
    /* 81 - Rejeitar */
    echo("                  <li id=\"mRejeitar_Selec\" class=\"menuUp\"><span id=\"rejeitar\">".RetornaFraseDaLista($lista_frases,81)."</span></li>\n");
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
