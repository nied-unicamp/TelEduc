<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/vota_enquete.php

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
  ARQUIVO : cursos/aplic/enquete/vota_enquete.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");

  $cod_ferramenta=24;
  $cod_ferramenta_ajuda = 24;
  $cod_pagina_ajuda = 10;
  
  include("../topo_tela.php");

  /*********************************************************/
  /* in�io - JavaScript */

  echo("  <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script type=\"text/javascript\" language=\"JavaScript\">");  
  echo("    function Iniciar()\n");
  echo("    {\n");
  echo("      startList();\n");
  echo("    }\n");

  echo("    function CancelaVoto()\n");
  echo("    {\n");
  echo("      document.location.href=\"enquete.php?cod_curso=".$cod_curso."\"\n");
  echo("      return(true);\n");
  echo("    }\n\n");

  echo("    var num_respostas;\n");
  
  echo("    function testa_voto()\n");
  echo("    {\n");
  echo("      for (i=0; i<num_respostas; i++) {\n");
  echo("        if (document.getElementById('resposta'+i).checked) {\n");
  echo("          return (true);\n");
  echo("        }\n");
  echo("      }\n");
  // 85 - Selecione uma alternativa
  echo("      alert ('".RetornaFraseDaLista($lista_frases, 85)."');\n");
  echo("      return (false);\n");
  echo("    }\n");
  
  echo("    function testa_campos()\n");
  echo("    {\n");
  echo("      if (testa_voto())\n");
  echo("      {\n");
  echo("        return(true);\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        return(false);\n");
  echo("      }\n");
  echo("    }\n");

  echo("  </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Pega a enquete, seu status, e o tipo do usu�io*/
  $enquete = getEnquete($sock, $idEnquete); 
  $status_enquete = getStatusEnquete($sock, $enquete);
  $ator = getTipoAtor($sock, $cod_curso, $cod_usuario);

  /* 1 - Enquete */
  /* 52- Votar Enquete */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,52)."</h4>\n");

  /*Voltar*/			
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /* SE tem permiss� para votar e a enquete est�em andamento, pode acessar */
  if (($vota = votaEnquete($sock, $ator, $enquete)) && ((strcmp($status_enquete, "ANDAMENTO") == 0)))
  {
    $alternativas = getAlternativas($sock, $enquete['idEnquete']);
    $input_type = getInputType($enquete['num_escolhas']);
    $cont = 0;

    echo("          <form action=\"vota_enquete2.php?cod_curso=".$cod_curso."\" name=\"enquete\" method=\"post\" onsubmit='return(testa_campos());'>\n");
    echo("          <input type=\"hidden\" name=\"idEnquete\" value=\"".$enquete['idEnquete']."\">\n");

    echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");

    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    echo("                <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabInterna\">\n");
    echo("                  <tr class=\"head\">\n");
    /* 9 - Titulo */ 
    echo("                    <td class=\"itens\" colspan=\"2\">".RetornaFraseDaLista($lista_frases,9)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"2\">".$enquete['titulo']."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr class=\"head\">\n");
    /* 10 - Pergunta */ 
    echo("                    <td class=\"itens\" colspan=\"2\">".RetornaFraseDaLista($lista_frases,10)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"2\">".$enquete['pergunta']."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr class=\"head\">\n");
    /* 11 - Alternativas */ 
    echo("                    <td class=\"itens\" colspan=\"2\">".RetornaFraseDaLista($lista_frases,11)."</td>\n");
    echo("                  </tr>\n");

    $total_votos = getTotalVotos($sock, $enquete['idEnquete']);
    $count = 0;
    foreach ($alternativas as $cod => $alternativa)
    {

      echo ("                  <tr>\n");
      echo ("                    <td class=\"itens\">\n");
      echo ("                      <input type=\"".$input_type."\" id=\"resposta".$count."\" name=\"resposta[]\" value=\"".$alternativa['idAlternativa']."\">\n");
      echo ("                      ".$alternativa['texto']."\n");
      echo ("                    </td>\n");
      echo ("                  </tr>\n");

      $count++;
    }

    echo("                  <tr  class=\"altColor0\" style=\"text-align:left\">\n");
    echo("                    <td colspan=\"2\">\n");
    echo("                      <script type=\"text/javascript\" language=\"JavaScript\"> num_respostas = ".($count--)."</script>\n");
    /* 45 - Votar */
    echo("                      <input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,45)."\">\n");
    /* 2 - Cancelar */
    echo("                      <input type=button onclick=\"CancelaVoto();\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\">\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("          </form>\n");
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    /* NAO TEM PERMISSAO*/
  }
  else
  {
    if (empty($enquete))
    {
      /* 118 - Enquete inv&aacute;lida. */
      echo("          <p>".RetornaFraseDaLista($lista_frases, 118)."</p><br/>\n");
    }
    else
    {
      if(!$vota)
      {
        /* 93 - Voc�n� tem permiss� para votar nesta enquete. */
        echo("          <p>".RetornaFraseDaLista($lista_frases, 93)."</p>\n");
      }

      if((strcmp($status_enquete, "ANDAMENTO") != 0))
      {
        /* 94 - A consulta para esta enquete j�terminou.  */
        echo("          <p>".RetornaFraseDaLista($lista_frases, 94)."</p>\n");
      }
    }

    /* 23 - Voltar (gen) */
    echo("          <form><input type=\"button\" value='-->".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=\"history.go(-1);\" class=\"input\" /></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");

  echo("  </body>\n");
  echo("</html>\n");
?>
