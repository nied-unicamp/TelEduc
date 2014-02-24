<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/intermap.php

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
  ARQUIVO : cursos/aplic/intermap/intermap.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("intermap.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /*
  ==================
  Funcoes JavaScript
  ==================
  */

  echo("  <script type=\"text/javascript\" language=\"javascript\" src=../bibliotecas/dhtmllib.js></script>\n");

  /* AJAX para o calend�rio */
  //echo("<script src='../bibliotecas/ajax.js'></script>\n");

  echo("  <script type=\"text/javascript\" language=\"javascript\">\n\n");

  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var versao = (navigator.appVersion.substring(0,3));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

  echo("    if (isNav)\n");
  echo("    {\n");
  echo("      document.captureEvents(Event.MOUSEMOVE);\n");
  echo("    }\n");
  echo("    document.onmousemove = TrataMouse;\n\n");

  echo("    function Iniciar() \n");
  echo("    { \n");
  echo("      startList(); \n");
  echo("    } \n");

  echo("    function TrataMouse(e)\n");
  echo("    {\n");
  echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("    }\n\n");

  echo("    function getPageScrollY()\n");
  echo("    {\n");
  echo("      if (isNav)\n");
  echo("        return(window.pageYOffset);\n");
  echo("      if (isIE)\n");
  echo("        return(document.body.scrollTop);\n");
  echo("    }\n\n");
  echo("    function AjustePosMenuIE()\n");
  echo("    {\n");
  echo("      if (isIE)\n");
  echo("        return(getPageScrollY());\n");
  echo("      else\n");
  echo("        return(0);\n");
  echo("    }\n\n");
  
   /* Iniciliza os layers. */
  echo("    function iniciar()\n");
  echo("    {\n");
  echo("      lay_calendario = getLayer('layer_calendario');\n"); 
  echo("    }\n\n");

  // Esconde o layer especificado por cod_layer.
  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n\n");

  /* Esconde todos os layers. Se o usuario for o propriet�rio do di�rio   */
  /* visualizado ent�o esconde o layer para renomear o item.              */
  echo("    function EscondeLayers()\n");
  echo("    {\n");
 	echo("      hideLayer(lay_calendario);\n"); 
  echo("    }\n\n");

  /* Exibe o layer especificado por cod_layer.                            */
  echo("    function MostraLayer(cod_layer)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n\n");

  echo("</script>\n\n");

  include("../menu_principal.php");

  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  if (!isset($menu))
    $menu="Correio";
  if (!isset($submenu))
    $submenu="MapaInteracao";


  if ($menu=="Correio")
  {
    if ($submenu=="MapaInteracao")
    {
      $incluir="correio_mapa_interacao.php";
      $cod_pagina=1;
    }
    else if ($submenu=="GraficoPeriodo")
    {
      $incluir="correio_grafico_periodo.php";
      $cod_pagina=4;
    }
    else if ($submenu=="GraficoParticipante")
    {
      $incluir="correio_grafico_participante.php";
      $cod_pagina=7;
    }
  }

  else if ($menu=="Forum")
  {
    if ($submenu=="MapaInteracao")
    {
      $incluir="forum_mapa_interacao.php";
      $cod_pagina=10;
    }
    else if ($submenu=="GraficoPeriodo")
    {
      $incluir="forum_grafico_periodo.php";
      $cod_pagina=13;
    }
    else if ($submenu=="GraficoParticipante")
    {
      $incluir="forum_grafico_participante.php";
      $cod_pagina=16;
    }
    else if ($submenu=="FluxoConversacao")
    {
      $incluir="forum_fluxo_conversacao.php";
      $cod_pagina=19;
    }
  }

  else if ($menu=="Batepapo")
  {
    if ($submenu=="MapaInteracao")
    {
      $incluir="batepapo_mapa_interacao.php";
      $cod_pagina=22;
    }
    else if ($submenu=="GraficoParticipante")
    {
      $incluir="batepapo_grafico_participante.php";
      $cod_pagina=25;
    }
    else if ($submenu=="FluxoConversacao")
    {
      $incluir="batepapo_fluxo_conversacao.php";
      $cod_pagina=28;
    }
  }

  /* 1 - Intermap */
  echo("<h4>".RetornaFraseDaLista($lista_frases, 1));

  if ($menu=="Correio")
    // 15 - Correio
    echo(" - ".RetornaFraseDaLista($lista_frases, 15));
  else if ($menu=="Forum")
    // 30 - F�rum de Discuss�o
    echo(" - ".RetornaFraseDaLista($lista_frases, 30));
  else if ($menu=="Batepapo")
    // 14 - Batepapo
    echo(" - ".RetornaFraseDaLista($lista_frases, 14));

  if ($submenu=="MapaInteracao")
    // 38 - Mapa de Intera��o
    echo(" - ".RetornaFraseDaLista($lista_frases, 38)."</h4>\n");
  else if ($submenu=="GraficoPeriodo")
    // 33 - Gr�fico por Per�odo
    echo(" - ".RetornaFraseDaLista($lista_frases, 33)."</h4>\n");
  else if ($submenu=="GraficoParticipante")
    // 32 - Gr�fico por Participante
    echo(" - ".RetornaFraseDaLista($lista_frases, 32)."</h4>\n");
  else if ($submenu=="FluxoConversacao")
    // 27 - Fluxo de Conversa��o
    echo(" - ".RetornaFraseDaLista($lista_frases, 27)."</h4>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
  echo("<div id=\"mudarFonte\">\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("</div>\n");

  DesenhaMenus($menu,$submenu,$cod_curso);

  echo("<tr>\n");
  echo("<td valign=\"top\">\n");

  include($incluir);
  Desconectar($sock);

  echo("</td></tr></table>\n");

  include("../tela2.php");

  echo("</body>\n");
  echo("</html>\n");

?>
