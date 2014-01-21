<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/ver_portfolio.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist?cia
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

    Nied - Ncleo de Inform?ica Aplicada ?Educa?o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit?ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/ver_portfolio.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

  $cod_ferramenta = 15;
  $cod_ferramenta_ajuda = 15;
  $cod_pagina_ajuda = 1;
  include("../topo_tela.php");

  /* Necessário para a lixeira. */
  session_register("cod_topico_s");
  unset($cod_topico_s);

  $eformador = EFormador($sock,$cod_curso,$cod_usuario);
  $visitante = EVisitante($sock, $cod_curso, $cod_usuario);
  $colaborador = EColaborador($sock, $cod_curso, $cod_usuario);

  // verificamos se a ferramenta de Avaliacoes estah disponivel
  $ferramenta_avaliacao = TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, 22);
  
  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
    
  if (isset ($exibir)) // Entao estamos vindo de alguma outra pagina, atraves de menu
  {
    if($exibir=="ind")
       $acao_portfolio_s='I';
    /*Considerando o novo modo de exibição inicial -> meus portfolios.*/   
    else if ($exibir=="myp")
       $acao_portfolio_s='M';
    else if ($exibir=="grp")
       $acao_portfolio_s='G';
    else
       $acao_portfolio_s='F';
  }
  else{
    $acao_portfolio_s='I';
  }

  session_register ("ferramenta_grupos_s");
  $ferramenta_grupos_s = StatusFerramentaGrupos($sock);

  // 75 - Portfolios de grupos
  // 74 - Portfolios individuais
  // 174 - Meus Portfolios
  if ($acao_portfolio_s=='M')
  {
     $cod_frase = 174;
     if ($ferramenta_avaliacao)
     // ajuda para meus portfolios sem ferramenta avaliacao
         $cod_pagina = 33;
     else
     // ajuda para portfolios individuais sem ferramenta avaliacao
         $cod_pagina = 33;
  }
  else if ($ferramenta_grupos_s && 'G' == $acao_portfolio_s)
  {
    $cod_frase = 75;
    if ($ferramenta_avaliacao)
      // ajuda para portfolios de grupos com ferramenta avaliacao
      $cod_pagina = 17;
    else
      // ajuda para portfolios de grupos sem ferramenta avaliacao
      $cod_pagina = 2;
  }
  else if ($acao_portfolio_s=='I')
  {
    $cod_frase = 74;
    if ($ferramenta_avaliacao)
      // ajuda para portfolios individuais sem ferramenta avaliacao
      $cod_pagina = 16;
    else
      // ajuda para portfolios individuais sem ferramenta avaliacao
      $cod_pagina = 1;
  }
  else if ($ferramenta_grupos_s && 'F' == $acao_portfolio_s)
  {
      $cod_frase = 177;
      if ($ferramenta_avaliacao)
      // ajuda para portfolios de grupos encerrados com ferramenta avaliacao
         $cod_pagina = 32;
      else
      // ajuda para portfolios de grupos encerrados sem ferramenta avaliacao
         $cod_pagina = 32;
  }

  echo("    <script type='text/javascript' src='../js-css/bib_ajax.js'> </script>\n");
  echo("    <script type='text/javascript'>\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("    var Xpos, Ypos;\n");
  echo("    var js_cod_item, js_cod_topico;\n");
  echo("    var js_nome_topico;\n");
  echo("    var js_tipo_item;\n");
  echo("    var editando=0;\n");
  echo("    var mostrando=0\n");
  echo("    var js_comp = new Array();\n\n");

  echo("    if (isNav)\n");
  echo("    {\n");
  echo("      document.captureEvents(Event.MOUSEMOVE);\n");
  echo("    }\n");
  echo("    document.onmousemove = TrataMouse;\n\n");

  echo("    function TrataMouse(e)\n");
  echo("    {\n");
  echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("    }\n\n");

  echo("    function getPageScrollY()\n");
  echo("    {\n");
  echo("      if (isNav)\n");
  echo("        return(window.pageYOffset);\n");
  echo("      if (isIE){\n");
  echo("        if(document.documentElement.scrollLeft>=0){\n");
  echo("          return document.documentElement.scrollTop;\n");
  echo("        }else if(document.body.scrollLeft>=0){\n");
  echo("          return document.body.scrollTop;\n");
  echo("        }else{\n");
  echo("          return window.pageYOffset;\n");
  echo("        }\n");
  echo("      }\n");
  echo("    }\n");

  echo("    function AjustePosMenuIE()\n");
  echo("    {\n");
  echo("      if (isIE)\n");
  echo("        return(getPageScrollY());\n");
  echo("      else\n");
  echo("        return(0);\n");
  echo("    }\n\n");

  echo("    function MostraLayer(cod_layer, ajuste)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("      if (editando>0){\n");
  echo("          if (editando==2) editando=0;\n");
  echo("      return false;\n");
  echo("      }\n");
  echo("      mostrando=1;\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n");

  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("      mostrando=0;\n");
  echo("    }\n");
  echo(" \n\n");

  echo("  </script>\n");

  include("../menu_principal.php");

  echo("          <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  
  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);
  
  echo("            <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, $cod_frase)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("            <div id=\"mudarFonte\">\n");
  echo("              <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("              <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("              <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  echo("          <ul id=\"legenda\">\n");


  if ('I' == $acao_portfolio_s)
  {
    if (!$colaborador)
    {
      // 130 - Meu Portfólio
      echo("              <li><img src=\"../imgs/icPasta.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,130)."</li>\n");

      // 131 - Portfólios de outros participantes
      $frase_outros_participantes = 131;
    }

    //else = é colaborador
    else
    {
      // 135 - Portfólios dos participates do curso
      $frase_outros_participantes = 135;
    }

    echo("              <li><img src=\"../imgs/icPasta2.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,$frase_outros_participantes)."</li>\n");
    // 121 - Portfólios de ex-alunos
    echo("              <li><img src=\"../imgs/icPasta3.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,121)."</li>\n");

  }
  else if ('G' == $acao_portfolio_s)
  {
    if (!$colaborador)
    {
      // 133 - 'Portfólios dos meus grupos'
      echo("              <li><img src=\"../imgs/icPasta.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,133)."</li>\n");

      // 134 - 'Portf�ios de outros grupos'
      $frase_outros_grupos = 134;
    }
    else
    {
      // 75 - Portf�ios de grupos
      $frase_outros_grupos = 75;
    }

    echo("              <li><img src=\"../imgs/icPasta2.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,$frase_outros_grupos)."</li>\n");
    // 122 - 'Portf�ios de grupos encerrados'
  }
  else if('M' == $acao_portfolio_s)
  {
    echo("              <li><img src=\"../imgs/icPasta.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,130)."</li>\n");
    echo("              <li><img src=\"../imgs/icPasta2.gif\" alt=\"\" border=\"0\" />".RetornaFraseDalista($lista_frases,185)."</li>\n");
    echo("              <li><img src=\"../imgs/icPasta3.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,186)."</li>\n");
  }
  else if('F' == $acao_portfolio_s)
  {
    // 122 - 'Portfólios de grupos encerrados'
    echo("              <li><img src=\"../imgs/icPasta3.gif\" alt=\"\" border=\"0\" />".RetornaFraseDaLista($lista_frases,122)."</li>\n");
  }

  echo("            </ul>\n");
  echo("            <!-- Tabelao -->\n");
  echo("            <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("                <td>\n");
  echo("                  <ul class=\"btAuxTabs\">\n");

   //174 - Meus portfolios 
  echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=myp\">".RetornaFraseDaLista($lista_frases,174)."</a></li>\n");
  // 74 - Portfolios Individuais
  echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=ind\">".RetornaFraseDaLista($lista_frases,74)."</a></li>\n");
  // 75 - Portfolios de Grupos
  if ((isset($ferramenta_grupos_s))&&($ferramenta_grupos_s)){
    echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=grp\">".RetornaFraseDaLista($lista_frases,75)."</a></li>\n");
    // 177 - Portfolios encerrados
    echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=enc\">".RetornaFraseDaLista($lista_frases,177)."</a></li>\n");
  }

  echo("                  </ul>\n");
  echo("                </td>\n");
  echo("              </tr>\n");
  echo("              <tr>\n");
  echo("                <td valign=\"top\">\n");

  // creio q aki ele cria os portfolios de grupos se nao existir
  VerificaPortfolioGrupos($sock,$cod_usuario);

  // a unica maneira de chamar ver_portfolios com esta variavel é através do menu de portfolios
  // se a variavel estiver setada, é que é preciso mudar a variável de sessão
  if (isset ($acao_portfolio))
    $acao_portfolio_s = $acao_portfolio;

  $lista_topicos=RetornaTopicosBase($sock, $cod_usuario, $cod_usuario_portfolio, $eformador, $acao_portfolio_s, $cod_curso);

  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                    <tr class=\"head\">\n");

  /* 8 - Portfolio */
  echo("                      <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,8)."</td>\n");
  /* 9 - Data */
  echo("                      <td width=\"60\" align=\"center\">".RetornaFraseDaLista($lista_frases,9)."</td>\n");
  /* 82 - Itens */
  echo("                      <td width=\"30\" align=\"center\">".RetornaFraseDaLista($lista_frases,82)."</td>\n");
  /* 83 - Itens não comentados */
  echo("                      <td width=\"110\" align=\"center\">".RetornaFraseDaLista($lista_frases,83)."</td>\n");

  if (count($lista_topicos)<1)
  {
    echo("                    </tr>\n");
    echo("                    <tr>\n");
    /* 80 - Não há nenhum portfólio */
    echo("                      <td colspan=\"5\">".RetornaFraseDaLista($lista_frases,80)."</td>\n");
    echo("                    </tr>\n");
    echo("                  </table>\n");
  }
  else
  {
    if ($ferramenta_avaliacao)
    {
      /* 148 - Itens não Avaliados */
      echo("                      <td width=\"110\" align=\"center\">".RetornaFraseDaLista($lista_frases,148)."</td>\n");
    }
    echo("                    </tr>\n");

    foreach ($lista_topicos as $cod_topico => $linha_topico)
    {
      if ($dono_portfolio)
        $max_data=RetornaMaiorDataItemComentario($sock,$cod_topico,'P',$linha_topico['data'],$cod_usuario);
      else if ($eformador)
        $max_data=RetornaMaiorDataItemComentario($sock,$cod_topico,'F',$linha_topico['data'],$cod_usuario);
      else
        $max_data=RetornaMaiorDataItemComentario($sock,$cod_topico,'T',$linha_topico['data'],$cod_usuario);
      $data=UnixTime2Data($max_data);

      if ($data_acesso<$max_data)
      {
        $marcatr="class=\"novoitem\"";
      }
      else
      {
        $marcatr="";
      }

      $span="<span class=\"link\" onclick=\"window.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico."&amp;cod_usuario_portfolio=".$linha_topico['cod_usuario']."&amp;cod_grupo_portfolio=".$linha_topico['cod_grupo']."';\">";

      $arquivo = $linha_topico['figura'];

      if ($ferramenta_avaliacao)
      {
        $num_itens_nao_avaliados=RetornaNumItensNaoAvaliados($sock,$cod_topico,$linha_topico['cod_usuario'],$linha_topico['cod_grupo'],$cod_usuario,$eformador);
      }

      echo("                    <tr ".$marcatr.">\n");
      echo("                      <td class=\"".$arquivo."\">".$span.$linha_topico['topico']."</span></td>\n");
      echo("                      <td>".$marcaib.$data.$marcafb."</td>\n");
      echo("                      <td>".$marcaib.$linha_topico['num_itens'].$marcafb."</td>\n");
      echo("                      <td>".$marcaib.$linha_topico['num_itens_nao_comentados'].$marcafb."</td>\n");
      if ($ferramenta_avaliacao)
      {
        echo("                      <td>".$marcaib.$num_itens_nao_avaliados.$marcafb."</td>\n");
      }

      echo("                    </tr>\n");
    }
    echo("                  </table>\n");
  }

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

?>