<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento_usuarios.php

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
  ARQUIVO : cursos/aplic/administracao/gerenciamento_usuarios.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

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

  $cod_ferramenta = 0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro Ã© a aÃ§Ã£o, o segundo Ã© o nÃºmero da frase para ser impressa se for "true", o terceiro caso "false"
  // 255 - Erro na operacao
  // 256 - Transformacao ocorrida com sucesso.
  // 268 - Inscrição realizada com sucesso
  $feedbackObject->addAction("transformar", 256, 255);
  $feedbackObject->addAction("inscrever", 268, 255);
  $feedbackObject->addAction("trocar_coordenador", 256, 255);

  $ecoordenador    = ECoordenador($sock,$cod_curso,$cod_usuario);
  $cod_coordenador = RetornaCodigoCoordenador($sock, $cod_curso);

  if (!isset($tipo_usuario))
  {
    $tipo_usuario = 'A';
  }
  if (!isset($ordem))
  {
    $ordem = "nome";
  }

  // 269 - Portfolio(s) ativado(s) com sucesso.
  $frase1 = RetornaFraseDaLista($lista_frases,269);
  // 270 - Portfolio(s) desativado(s) com sucesso.
  $frase2 = RetornaFraseDaLista($lista_frases,270);
  /* 1 - Administraï¿½ï¿½o */
  $cabecalho = "          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n";

  // Seleciona alguns textos que aparecerão na página
  // de acordo com o tipo de usuário sendo exibido. 
  switch($tipo_usuario) {
    case 'a':
      /* 283 - Gerenciamento de Alunos desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 283);
      /* 284 - No de Alunos desligados: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 284);
      /* 285 - Religar Aluno */
      $frase_religar    = RetornaFraseDaLista($lista_frases, 285);
      $cod_pagina       = 10;
      $cod_pagina_ajuda = 9;
      break;
    case 'A':
      /* 102 - Gerenciamento de Alunos */
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 102);
      /* 109 - No de Alunos: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 109);
      /* 288 - Desligar Aluno */
      $frase_desligar   = RetornaFraseDaLista($lista_frases, 288);
      $cod_pagina       = 9;
      $cod_pagina_ajuda = 9;
      break;
    case 'f':
      /* 258 - Gerenciamento de Formadores desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 258);
      /* 259 - No de Formadores desligados: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 259);
      // Apenas Coordenadores podem Ligar/Desligar Formadores.
      if ($ecoordenador)
        /* 260 - Religar Formador */
        $frase_religar    = RetornaFraseDaLista($lista_frases, 260);
      $cod_pagina       = 10;
      $cod_pagina_ajuda = 10;
      break;
    case 'F':
      /* 103 - Gerenciamento de Formadores */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 103);
      /* 110 - No de Formadores: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 110);
      // Apenas Coordenadores podem Ligar/Desligar Formadores.
      if ($ecoordenador)
        /* 199 - Desligar Formador */
        $frase_desligar   = RetornaFraseDaLista($lista_frases, 199);
      $cod_pagina       = 10;
      $cod_pagina_ajuda = 10;
      break;
    case 'z':
      /* 319 - Gerenciamento de Colaboradores desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 319);
      /* 320 - 'No de Colaboradores desligados: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 320);
      /* 329 - Religar Colaborador */
      $frase_religar    = RetornaFraseDaLista($lista_frases, 329);
      $cod_pagina       = 13;
      $cod_pagina_ajuda = 13;
      break;
    case 'Z':
      /* 165 - Gerenciamento de Colaboradores  */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 165);
      /* 166 - No de Colaboradores: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 166);
      /* 328 - Desligar Colaborador */
      $frase_desligar   = RetornaFraseDaLista($lista_frases, 328);
      $cod_pagina       = 13;
      $cod_pagina_ajuda = 13;
      break;
    case 'v':
      /* 321 - Gerenciamento de Visitantes desligados */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 321);
      /* 322 - Nï¿½ de Visitantes desligados */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 322);
      /* 330 - Religar Visitante */
      $frase_religar    = RetornaFraseDaLista($lista_frases, 330);
      $cod_pagina       = 13;
      $cod_pagina_ajuda = 16;
      break;
    case 'V':
      /* 179 - Gerenciamento de Visitantes */
      $cabecalho       .= " - ".RetornaFraseDaLista($lista_frases, 179);
      /* 191 - Nï¿½ de Visitantes: */
      $frase_qtde       = RetornaFraseDaLista($lista_frases, 191);
      /* 178 - Desligar Visitante */
      $frase_desligar   = RetornaFraseDaLista($lista_frases, 178);
      $cod_pagina       = 13;
      $cod_pagina_ajuda = 16;
      break;
  }

  echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"paginacao.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"gerenciamento.js\"></script>\n");
  if ($ecoordenador) {
    echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"eventos_coordenador.js\"></script>\n");
  }
  else {
    echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"eventos.js\"></script>\n");
  }
  /*Funcões JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var tipo_usuario = '".$tipo_usuario."';\n");
  echo("      var ordem = '".$ordem."';\n");
  echo("      var cod_curso = ".$cod_curso.";\n");
  echo("      var cod_usuario = ".$cod_usuario.";\n");
  echo("      var cod_ferramenta = ".$cod_ferramenta.";\n");
  if ($ecoordenador) {
    echo("      var cod_coordenador = ".$cod_coordenador.";\n");
  }
  /*79 - Dados */
  echo("      var fraseDados = '".RetornaFraseDaLista($lista_frases, 79)."';\n");
  /* 114 - Nenhuma pessoa selecionada */
  echo("      var fraseSemSelecao = '".RetornaFraseDaLista($lista_frases, 114)."';\n");
  echo("      var frasePortifolioAtivado = '".RetornaFraseDaLista($lista_frases, 208)."';\n");
  echo("      var frasePortifolioDesativado = '".RetornaFraseDaLista($lista_frases, 209)."';\n\n");

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

  echo("      function AtivaDesativaPort(tipo)\n");
  echo("      {\n");
  echo("        var k=0;\n");
  echo("        var cod_itens2 = document.getElementsByName('cod_usu[]');\n");
  echo("        cod_usu_array = new Array();\n");
  echo("        for (i=0; i < cod_itens2.length; i++){\n");
  echo("          if (cod_itens2[i].checked){\n");
  echo("            cod_usu_array[k]=cod_itens2[i].value;\n");
  echo("            k++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        xajax_AtivarDesativarPortDinamic(".$cod_curso.",cod_usu_array,tipo,'".$frase1."','".$frase2."');\n");
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
  echo("            <input type=\"hidden\" name=\"action_ger\"   value=\"\">\n");
  echo("            <input type=\"hidden\" name=\"origem\"       value=\"gerenciamento_usuarios.php\">\n");

  // Pï¿½gina Principal
  echo($cabecalho."</h4>");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
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

  switch($tipo_usuario) {
    case 'a':
    case 'A':
      /* 318 - Alunos */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=A&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,318)."</a></li>\n");
      /* 317 - Alunos Desligados */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=a&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,317)."</a></li>\n");
      break;
    case 'f':
    case 'F':
      /* 323 - Formadores */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,323)."</a></li>\n");
      /* 261 - Formadores desligados */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=f&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,261)."</a></li>\n");
      break;
    case 'z':
    case 'Z':
      /* 324 - Colaboradores */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=Z&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,324)."</a></li>\n");
      /* 325 - Colaboradores desligados */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=z&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,325)."</a></li>\n");
      break;
    case 'v':
    case 'V':
      /* 326 - Visitantes */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=V&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,326)."</a></li>\n");
      /* 327 - Visitantes desligados */
      echo("                  <li><a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=v&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,327)."</a></li>\n");
      break;
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  /* Código de montagem do conteúdo a partir daqui */
  $lista_usuarios = RetornaListaUsuarios($sock, $cod_curso, $tipo_usuario, $ordem);

  /* Sistema de Paginacao */

  $num = count($lista_usuarios);
  /* Numero de mensagens exibidas por pagina.*/
  $msg_por_pag = 10;
  // Verifica se estamos gerenciando algum papel desligado.
  $tipo_usuario_desligado = ($tipo_usuario == 'a' || $tipo_usuario == 'f' || $tipo_usuario == 'z' || $tipo_usuario == 'v');
  // Verifica se estamos gerenciando algum papel desligado que tenha portfólio.
  // Se for, temos que dar a opção de ativar ou desativar o portfólio.
  $gerenciar_portfolio    = ($tipo_usuario == 'a' || $tipo_usuario == 'f' || $tipo_usuario == 'z');

  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tbgeren\">\n");
  echo("                  <tr class=\"head01 alLeft\">\n");
  echo("                    <td colspan=\"".($gerenciar_portfolio?"3":"2")."\">");

  /* 109 - No de Alunos: */
  echo("                      ".$frase_qtde." ".$num."\n");
  echo("                    </td>\n");
  echo("                    <td colspan=\"2\" align=\"right\">");
  echo("                      ".RetornaFraseDaLista($lista_frases,146)."\n");
  echo("                      <select name=\"ordem\" onChange=\"document.location='gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."&amp;ordem='+this[this.selectedIndex].value;\" style=\"margin:5px 0 0 0;\">\n");
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
  // 211 - Portfolio
  if($gerenciar_portfolio)
    echo("                    <td align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 211)."</b></td>\n");
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
      if ($msg_por_pag >= 1) {
        echo("                  <tr name=\"germen\" id=\"ger\" style=\"display: table-row;\">\n");
        echo("                    <td width=\"1%\"><input type=\"checkbox\" name=\"cod_usu[]\" onclick=\"VerificaCheck();\" value=".$cod_usuario_l."></td>\n");
        echo("                    <td align=\"left\">".$linha['nome']."</td>\n");
        echo("                    <td>".Unixtime2Data($linha['data_inscricao'])."</td>\n");
        /* 79 - Dados */
        echo("                    <td><a href=\"gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."&amp;ordem=".$ordem."&amp;origem=gerenciamento_usuarios.php&amp;action_ger=dados&amp;cod_usu[]=".$cod_usuario_l."\">".RetornaFraseDaLista($lista_frases,79)."</a></td>\n");
        if($gerenciar_portfolio) {
          echo("                  <td id=\"status_port".$cod_usuario_l."\">");
          if($linha['portfolio'] == "ativado")
            echo RetornaFraseDaLista($lista_frases, 208);// 208 - Ativado
          else
            echo RetornaFraseDaLista($lista_frases, 209);//209 - Desativado
          echo("                    </td>\n");
        }
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
  echo("                  <li id=\"mDados_Selec\" class=\"menuUp\"><span id=\"dados_usu\">".RetornaFraseDaLista($lista_frases,79)."</span></li>\n");

  // Apenas Coordenadores podem trocar papéis de usuários.
  if ($ecoordenador && !$tipo_usuario_desligado) {

    if ($tipo_usuario != 'A')
      // 107 - Transformar em Aluno
      echo("                  <li id=\"mAluno_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,107)."</span></li>\n");

    if ($tipo_usuario != 'F')
      // 108 - Transformar em Formador
      echo("                  <li id=\"mFormador_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,108)."</span></li>\n");

    if ($tipo_usuario != 'Z')
      // 176 - Transformar em Colaborador
      echo("                  <li id=\"mColaborador_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,176)."</span></li>\n");

    if ($tipo_usuario != 'V')
      // 314 - Transformar em visitante
      echo("                  <li id=\"mVisitante_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,314)."</span></li>\n");

    if ($tipo_usuario == 'F')
      // 280 - Transformar em Coordenador
      echo("                  <li id=\"mCoordenador_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,280)."</span></li>\n");

  }

  // Apenas Coordenadores podem Ligar/Desligar Formadores.
  if ((($tipo_usuario != 'F') && ($tipo_usuario != 'f')) || $ecoordenador) {
    if (isset($frase_desligar))
      echo("                  <li id=\"mDesligar_Selec\" class=\"menuUp\"><span>".$frase_desligar."</span></li>\n");
  
    if (isset($frase_religar))
      echo("                  <li id=\"mReligar\" class=\"menuUp\"><span>".$frase_religar."</span></li>\n");
  }

  // Ativar / Desativar Portfolio
  if($gerenciar_portfolio) {
    echo("                  <li id=\"mAtivarPort_Selec\" class=\"menuUp\"><span id=\"ativar_port\">".RetornaFraseDaLista($lista_frases,206)."</span></li>\n");
    echo("                  <li id=\"mDesativarPort_Selec\" class=\"menuUp\"><span id=\"desativar_port\">".RetornaFraseDaLista($lista_frases,207)."</span></li>\n");
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