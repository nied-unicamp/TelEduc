<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/finalizarEnquete.php

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
  ARQUIVO : cursos/aplic/enquete/finalizarEnquete.php
  ========================================================== */
  
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");
  
  $cod_ferramenta=24;

  include("../topo_tela.php");

  /* INICIO - JavaScript */
  echo("<script type=\"text/javascript\" language=\"javascript\">\n\n");
  echo("  function Iniciar()\n");
  echo("  {\n");
  echo("    startList();\n");
  echo("  }\n\n");

  /* Volta a Pagina de edicao desta Enquete */
  echo("  function VoltaPaginaPrincipal(atualizacao)\n");
  echo("  {\n");
  echo("    document.location.href='enquete.php?cod_curso=".$cod_curso."&categ=2&acao=finalizarEnquete&atualizacao='+atualizacao;\n");
  echo("    return(true);\n");
  echo("  }\n\n");
  echo("</script>\n\n");
  /* FIM - JavaScript */

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  $enquete = getEnquete($sock, $idEnquete);
  
  if ($tela_formador || $tela_colaborador)
  { 
    if (finalizarEnquete($sock, $idEnquete))
    {
      $atualizacao="true";
      Desconectar($sock);
      echo("  <script type=\"text/javascript\" language=\"javascript\">VoltaPaginaPrincipal('".$atualizacao."');</script>");
      exit;
    }
    else
    {
      $atualizacao="false";
      Desconectar($sock);
      echo("  <script type=\"text/javascript\" language=\"javascript\">VoltaPaginaPrincipal('".$atualizacao."');</script>");
      exit;
    }
  }
  else
  {
    /* 1 - Enquete */
    /* 37 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,37)."</h4>\n");
    /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
    /* 23 - Voltar (gen) */
    echo("          <form><input type=\"button\" class=\"input\" value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n"); 
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
  exit;
?>