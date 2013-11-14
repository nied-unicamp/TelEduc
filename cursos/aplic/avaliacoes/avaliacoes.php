<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/avaliacoes.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/avaliacoes.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include ($bibliotecas . "geral.inc");
  include ("avaliacoes.inc");

  $cod_ferramenta=22;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  if(!isset($tela_avaliacao) || $tela_avaliacao == 'A')
    $cod_pagina_ajuda=1;
  else if($tela_avaliacao == 'P')
    $cod_pagina_ajuda=2;
  else
    $cod_pagina_ajuda=3;


  include("../topo_tela.php");

  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("excluirAvaliacao", 213, 83);


  $data_acesso = PenultimoAcesso($sock, $cod_usuario, "");
  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  // A variavel tela_avaliacao indica quais avaliacoes devem ser listadas: 'P'assadas, 'A'tuais ou 'F'uturas
  if (!isset ($tela_avaliacao) || 
      !in_array($tela_avaliacao, 
                array ('P', 'A', 'F'))
     ) {
    $tela_avaliacao = 'A';
  }

  switch ($tela_avaliacao) {
    case 'P' :
      $lista_avaliacoes = RetornaAvaliacoesAnteriores($sock, $usr_formador);
      // 29 - Avalia��es Passadas
      $frase_avaliacoes = RetornaFraseDaLista($lista_frases, 29);
      $cod_pagina = 2;
      break;
    case 'A' :
      $lista_avaliacoes = RetornaAvaliacoesAtuais($sock, $usr_formador);
      // 32 - Avalia��es Atuais
      $frase_avaliacoes = RetornaFraseDaLista($lista_frases, 32);
      $cod_pagina = 1;
      break;
    case 'F' :
      $lista_avaliacoes = RetornaAvaliacoesFuturas($sock, $usr_formador);
      // 30 - Avalia��es Futuras
      $frase_avaliacoes = RetornaFraseDaLista($lista_frases, 30);
      $cod_pagina = 3;
      break;
  }

  /* Funções javascript */
  echo("  <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
  echo("  <script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>\n");
  echo("  <script type=\"text/javascript\">\n");

  echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("      var Xpos, Ypos;\n");

//  echo("      if (isNav)\n");
//  echo("      {\n");
//  echo("        document.captureEvents(Event.MOUSEMOVE);\n");
//  echo("      }\n");
//  echo("      document.onmousemove = TrataMouse;\n\n");

  /* Verificação do browser sendo usado */
  echo("      if (document.addEventListener) {\n");	/* Caso do FireFox */
  echo("        document.addEventListener('mousemove', TrataMouse, false);\n");
  echo("      } else if (document.attachEvent){\n");	/* Caso do IE */
  echo("        document.attachEvent('onmousemove', TrataMouse);\n");
  echo("      }\n");

  echo("      function TrataMouse(e)\n");
  echo("      {\n");
  echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("      }\n");

  echo("      function getPageScrollY()\n");
  echo("      {\n");
  echo("        if (isNav)\n");
  echo("          return(window.pageYOffset);\n");
  echo("        if (isIE)\n");
  echo("          return(document.body.scrollTop);\n");
  echo("      }\n");

  echo("      function AjustePosMenuIE()\n");
  echo("      {\n");
  echo("        if (isIE)\n");
  echo("          return(getPageScrollY());\n");
  echo("        else\n");
  echo("          return(0);\n");
  echo("      }\n");

  echo("      function TrataMouse(e)\n");
  echo("      {\n");
  echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("      }\n");

  echo("      function getPageScrollY()\n");
  echo("      {\n");
  echo("        if (isNav)\n");
  echo("          return(window.pageYOffset);\n");
  echo("        if (isIE)\n");
  echo("          return(document.body.scrollTop);\n");
  echo("      }\n");

  echo("      function AjustePosMenuIE()\n");
  echo("      {\n");
  echo("        if (isIE)\n");
  echo("          return(getPageScrollY());\n");
  echo("        else\n");
  echo("          return(0);\n");
  echo("      }\n");

  echo("      function startList() {\n");
  echo("        if (document.all && document.getElementById) {\n");
  echo("          nodes = document.getElementsByTagName(\"span\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("          nodes = document.getElementsByTagName(\"li\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        lay_nova_avaliacao = getLayer('layer_nova_avaliacao');\n");
  if (isset($_GET['acao']))
    $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n");
  echo("      \n");

  echo("      function EscondeLayers()\n");
  echo("      {\n");
  echo("        hideLayer(lay_nova_avaliacao);\n");
  echo("      }\n");

  echo("      function MostraLayer(cod_layer, ajuste)\n");
  echo("      {\n");
  echo("        EscondeLayers();\n");
  echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n");

  echo("      function EscondeLayer(cod_layer)\n");
  echo("      {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("      }\n");

  echo("      function VerificaNovoTitulo(textbox, aspas) {\n");
  echo("        texto=textbox.value;\n");
  echo("        if (texto==''){\n");
  echo("          // se nome for vazio, nao pode\n");
  /* 15 - O titulo nao pode ser vazio. */
  echo("          alert(\"".RetornaFraseDaLista($lista_frases,15)."\");\n");
  echo("          textbox.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
  echo("        else if ((texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0)&&(!aspas)) {\n");
  /* 16 - O t�tulo n�o pode conter \\. */
  echo("           alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,16)))."\");\n");
  echo("          textbox.value='';\n");
  echo("          textbox.focus();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n\n");

  echo("      function Ver(id)\n");
  echo("      {\n");
  echo("        document.frmAvaliacao.cod_avaliacao.value = id;\n");
  echo("        document.frmAvaliacao.action = 'ver.php'; \n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("      }\n\n");

  echo("      function VerTelaAvaliacoes(tela)\n");
  echo("      {\n");
  echo("        document.frmAvaliacao.action = 'avaliacoes.php';\n");
  echo("        document.frmAvaliacao.tela_avaliacao.value = tela;\n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("        return false;\n");
  echo("      }\n");

  echo("      function VerTelaNotas()\n");
  echo("      {\n");
  echo("        document.frmAvaliacao.action = 'notas.php';\n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("        return false;\n");
  echo("      }\n");

  echo("    </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  echo("          <h4> ".RetornaFraseDaLista($lista_frases, 1)." - $frase_avaliacoes </h4>");

    // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

/************************CABECALHO ACABA AQUI ****************************/

// Determinamos a cor de cada link (amarelo ou branco) no menu superior
$cor_link1 = array (
  'A' => "",
  'F' => "",
  'P' => ""
);
$cor_link2 = array (
  'A' => "",
  'F' => "",
  'P' => ""
);
$cor_link1[$tela_avaliacao] = "<font color=yellow>";
$cor_link2[$tela_avaliacao] = "</font>";

  echo("          <form name=\"frmAvaliacao\" method=\"get\">\n");
  echo("            <input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\">\n");
  // Passa o cod_avaliacao para executar a�es sobre ela.
  echo("            <input type=\"hidden\" name=\"cod_avaliacao\"  value=\"-1\">\n");
  // tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("            <input type=\"hidden\" name=\"tela_avaliacao\" value=" . $tela_avaliacao . ">\n");
  echo("            <input type=\"hidden\" name=\"origem\"         value=\"avaliacoes\">\n");
  echo("            <input type=\"hidden\" name=\"operacao\"       value=null>\n");
  echo("          </form>\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id =\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("            <!----------------- Botoes de Acao ----------------->\n");
  echo("              <td class=\"btAuxTabs\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 29 - Avalia��es Passadas */
  echo("                  <li><span onClick=\"return(VerTelaAvaliacoes('P'));\">".RetornaFraseDaLista($lista_frases, 29)."</span></li>\n");
  /* 32 - Avalia��es Atuais*/
  echo("                  <li><span onClick=\"return(VerTelaAvaliacoes('A'));\">".RetornaFraseDaLista($lista_frases, 32)."</span></li>\n");
  /* 30 - Avalia��es Futuras*/
  echo("                  <li><span onClick=\"return(VerTelaAvaliacoes('F'));\">".RetornaFraseDaLista($lista_frases, 30)."</span></li>\n");
  /* 31 - Notas dos participantes */
  echo("                  <li><span onClick=\"return(VerTelaNotas());\">".RetornaFraseDaLista($lista_frases, 31)."</span></li>");
  echo("              </ul></td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");
  if ($usr_formador)
  {
    /* 184 - Criar Avalia��o Externa */
    echo ("                 <li><span OnClick='MostraLayer(lay_nova_avaliacao, 0);   document.getElementById(\"nome\").focus();'>".RetornaFraseDaLista($lista_frases,184)."</span></li>\n");
  }
  echo("                </ul>\n");
  echo("              </td>");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("          <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"sortable tabInterna\">\n");
  echo("          <tr class=\"head\">\n");
  echo("                <td width=\"66%\" align=left style=\"cursor:pointer\">$frase_avaliacoes</td>\n");
  /* 113 -Tipo da Avalia��o */
  echo("                <td width=\"14%\" align=\"center\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
  /* 16 - Data de in�cio*/
  echo("                <td width=\"10%\" align=\"center\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  /* 17 - Data de T�rmino */
  echo("                <td width=\"10%\" align=\"center\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases,17)."</td>\n");
  echo("          </tr>\n");
  if (count($lista_avaliacoes) > 0)
  {
    foreach ($lista_avaliacoes as $cod => $linha)
    {
      $data_inicio = UnixTime2Data($linha['Data_inicio']);
      $data_termino = UnixTime2Data($linha['Data_termino']);
      if (!strcmp($linha['Ferramenta'], 'F')) {
        // 145 - Frum de Discuss�
        $ferramenta = RetornaFraseDaLista($lista_frases, 145);
      }
      elseif (!strcmp($linha['Ferramenta'], 'B')) {
        // 146 - Sess� de Bate-Papo
        $ferramenta = RetornaFraseDaLista($lista_frases, 146);
      }
      elseif (!strcmp($linha['Ferramenta'], 'E')) {
        if ($linha['tipo'] == 'I')
          // 176 - Exerc�io Individual
          $ferramenta = RetornaFraseDaLista($lista_frases, 176);
        elseif ($linha['tipo'] == 'G')
          // 174 - Exerc�io em Grupo
          $ferramenta = RetornaFraseDaLista($lista_frases, 174);
      } else {
        if ($linha['Ferramenta'] == 'P') {
          if ($linha['tipo'] == 'G')
            // 162 - Atividade em grupo no Portfolio
            $ferramenta = RetornaFraseDaLista($lista_frases, 162);
          elseif ($linha['tipo'] == 'I')
            // 161 - Atividade individual no Portfolio
            $ferramenta = RetornaFraseDaLista($lista_frases, 161);
        }

        /*Caso seja uma Avalia��o externa*/
        elseif ($linha['Ferramenta'] == 'N') {
            if ($linha['tipo'] == 'I')
              $ferramenta = RetornaFraseDaLista($lista_frases, 185);
            else
              $ferramenta = RetornaFraseDaLista($lista_frases, 186);
          }
      }

      $a1 = "<a href=\"#\" onClick=\"Ver(".$linha['Cod_avaliacao'].");\">";
      $a2 = "</a>";

      echo("                  <tr> \n");
      echo("                    <td align=left>" .$a1 . $linha['Titulo'] . $a2 . "</td>\n");

      // coluna do tipo de avaliacao: "Atividade Individual" ou "Atividade Em Grupo" ou "Sessao de Batepapo" ou "Forum"
      echo("                    <td align=center>" . $ferramenta . "</td>\n");

      // coluna da data de inicio
      echo("                    <td align=center>" . $data_inicio . "</td>\n");

      // coluna da data de termino
      echo("                    <td align=center>" . $data_termino . "</td>\n");
      echo("                  </tr>\n");
    }
  } else {
    switch ($tela_avaliacao) {
      case 'P' :
        // 177 - Nao existem avalia��es passadas
        $nao_existe = RetornaFraseDaLista($lista_frases, 177);
        break;
      case 'A' :
        // 38 - N�o existem avalia��es atuais!
        $nao_existe = RetornaFraseDaLista($lista_frases, 38);
        break;
      case 'F' :
        // 178 - Nao existem avalia��es futuras
        $nao_existe = RetornaFraseDaLista($lista_frases, 178);
        break;
    }
    echo("                  <tr>\n");
    echo("                    <td align=center>" . $nao_existe . "</td>\n");
    echo("                    <td align=center>-</td>\n");
    echo("                    <td align=center>-</td>\n");
    echo("                    <td align=center>-</td>\n");
    echo("                  </tr>\n");
  }
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");


  if($usr_formador)
  {  
    /* Nova Avaliacao */
    echo("          <div id=\"layer_nova_avaliacao\" class=\"popup\">\n");
    echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_avaliacao);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("              <div class=\"int_popup\">\n");
    echo("                <form name=\"form_nova_agenda\" method=\"post\" action=\"acoes.php\" onSubmit='return(VerificaNovoTitulo(document.form_nova_agenda.novo_titulo, 1));'>\n");
    echo("                <div class=\"ulPopup\">\n");
    /* 123 - Titulo: */
    echo("                  ".RetornaFraseDaLista($lista_frases,123)."<br />\n");
    echo("                  <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=150 /><br />\n");
    /* 20 - Tipo da Atividade */
    echo("                  ".RetornaFraseDaLista($lista_frases,20)."<br />\n");
    echo("                  <select name=\"tipo\" class=\"input\">\n");
    /* 21 - Individual */
    echo("                    <option value='I'>".RetornaFraseDaLista($lista_frases,21)."</option>\n");
    /* 22 - Em Grupo */
    echo("                    <option value='G'>".RetornaFraseDaLista($lista_frases,22)."</option>\n");
    echo("                  </select><br>\n");
//     echo("                  Ferramenta de Avaliacao<br />\n");
//     /* Ferramenta da Avaliação */
//     echo("                  <select name=\"ferramenta_avaliacao\" class=\"input\">\n");
//     /* 21 - Individual */
//     echo("                    <option value=\"F\">Forum</option>\n");
//     echo("                    <option value=\"B\">Bate-Papo</option>\n");
//     echo("                    <option value=\"E\">Exercicios</option>\n");
//     echo("                    <option value=\"P\">Portifolio</option>\n");
//     echo("                    <option value=\"N\">Avaliacao Externa</option>\n");
//     echo("                  </select><br>\n");

    echo("                  <input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\" />\n");
    echo("                  <input type=\"hidden\" name=\"acao\"           value=\"criarAvaliacaoExt\" />\n");
    echo("                  <input type=\"hidden\" name=\"cod_usuario\"    value=\"".$cod_usuario."\">\n");
    echo("                  <input type=\"hidden\" name=\"ferramenta\"     value=\"".$linha['Ferramenta']."\" />\n");
    echo("                  <input type=\"hidden\" name=\"tela_avaliacao\" value=\"".$tela_avaliacao."\" />\n");
    /* 18 - Ok (gen) */
    echo("                  <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
    /* 2 - Cancelar (gen) */
    echo("                  &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_avaliacao);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
    echo("                </div>\n");
    echo("                </form>\n");
    echo("              </div>\n");
    echo("            </div>\n\n");
  }

  include("../tela2.php");
  echo ("</body>\n");
  echo ("</html>\n");

Desconectar($sock);
?>
