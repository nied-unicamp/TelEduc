<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/exercicio.php

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
  ARQUIVO : cursos/aplic/exercicios/exercicio.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  $cod_ferramenta = 23;
  $agrupamento = $_GET['agrupar'];
  $visualizar = $_GET['visualizar'];
  
  if($visualizar != "I" && $visualizar != "G")
    $visualizar = "I";
    
  if($visualizar == "I")
  {
    if($agrupamento != "A" && $agrupamento != "T")
    {
      $agrupamento = "A";
    }
  }
  else
  {
    if($agrupamento != "G" && $agrupamento != "T")
    {
      $agrupamento = "G";
    }
  }
  

  include("../topo_tela.php");
  
  // aplica ao usuario exercicios aplicados pelos formadores e que ainda nao constam para o mesmo.
  AplicaExerciciosAoUsuario($sock,$cod_curso,$cod_usuario);
  
  $titulos = RetornaListaNomesOuTitulos($sock,$cod_curso,$cod_usuario,$tela_formador,$agrupamento,$visualizar);
  $nsubmetidas = RetornaNumExerciciosNaoEntregues($sock,$cod_usuario,$tela_formador,$agrupamento,$visualizar);
  $ncorrigidas = RetornaNumExerciciosNaoCorrigidos($sock,$cod_usuario,$tela_formador,$agrupamento,$visualizar);

  /*********************************************************/
  /* in�io - JavaScript */
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");
  
  echo("    var lay_agrupar;");
  
  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  echo("      lay_agrupar = getLayer('layer_agrupar');\n");
  echo("      startList();\n");
  echo("    }\n\n");
  
  echo("    function EscondeLayers()\n");
  echo("    {\n");
  echo("      hideLayer(lay_agrupar);\n");
  echo("    }\n");

  echo("    function MostraLayer(cod_layer, ajuste)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n");

  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n");
  
  echo("    function MudaAgrupamento(novo_agrupamento)\n");
  echo("    {\n");
  echo("      window.location='exercicio.php?cod_curso=".$cod_curso."&visualizar=".$visualizar."&agrupar='+novo_agrupamento;\n");
  echo("    }\n");
  
  echo("  </script>\n\n");
  /* fim - JavaScript */
  /*********************************************************/

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador)
  {
    if($visualizar == "I")
    {
	  /* ? - Exercicios - Exercicios Individuais Disponiveis*/
  	  $frase = "Exercicios - Exercicios Individuais Disponiveis";
    }
    else if($visualizar == "G")
    {
	  /* ? - Exercicios - Exercicios em Grupo Disponiveis*/
  	  $frase = "Exercicios - Exercicios em Grupo Disponiveis";
    }
   
	echo("          <h4>".$frase."</h4>\n");
	
  	/*Voltar*/
  	echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span>\n");

  	echo("          <div id=\"mudarFonte\">\n");
  	echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  	echo("          </div>\n");
  	
	echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo("            <tr>\n");
	echo("              <td valign=\"top\">\n");

  	echo("                <ul class=\"btAuxTabs\">\n");
  	
  	/* ? - Exercicios Individuais */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=I&agrupar=A'>Exercicios Individuais</a></li>\n");
    
    /* ? - Exercicios em Grupo */
    echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=G&agrupar=G'>Exercicios em Grupo</a></li>\n");

  	/* ? - Biblioteca de Exercicios */
    echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."&visualizar=E'>Biblioteca de Exercicios</a></li>\n");
  	
    /* ? - Biblioteca de Questoes */
    echo("                  <li><a href='questoes.php?cod_curso=".$cod_curso."&visualizar=Q'>Biblioteca de Questoes</a></li>\n");

  	echo("                </ul>\n");
  	echo("              </td>\n");
  	echo("            </tr>\n");
  	echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <ul class=\"btAuxTabs03\">\n");
    /* ? - Agrupar */
    echo("                  <li><span onclick=\"MostraLayer(lay_agrupar, 0);\">Agrupar</span></li>\n");
    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  	echo("            <tr>\n");
  	echo("              <td valign=\"top\">\n");
	echo("                <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
	echo("                  <tr class=\"head\">\n");
	
	$aux="";
	
    if($agrupamento != "T")	
    {
      if($visualizar == "I")
      {
        // ? - de
        $aux = " de";
      }
      else if($visualizar == "G")
      {
        // ? - do grupo
        $aux = " do grupo";
      }
    }
    
	/* ? - Exercicios */
	echo("                    <td class=\"alLeft\">Exercicios".$aux."</td>\n");
    /* ? - Exercicios nao entregues */
	
	echo("                    <td width=\"15%\">Exercicios nao entregues</td>\n");
    /* ? - Exercicios nao corrigidos*/
	echo("                    <td width=\"15%\">Exercicios nao corrigidos</td>\n");
	echo("                  </tr>\n");

    //Inicializacao de indices
    $i = 0;
	$j = 0;
    $k = 0;
    $ic = 0;
    $total = 0;
    $numlinhas = count($titulos);
    
    if($visualizar == "I" && $agrupamento == "A" && $cod_usuario != -1)
    {
      $nome[$j] = NomeUsuario($sock,$cod_usuario,$cod_curso);
      $cod_user_lista[$j] = $cod_usuario;
      
      while($k<count($nsubmetidas) && $nsubmetidas[$k]["cod"] < $cod_user_lista[$j])
        $k++;

      if($nsubmetidas != null && $cod_user_lista[$j] == $nsubmetidas[$k]["cod"])
        $nao_submetidos[$j]=$nsubmetidas[$k]["num"];
      else
        $nao_submetidos[$j]=0;

      while($ic<count($ncorrigidas) && $ncorrigidas[$ic]["cod"]<$cod_user_lista[$j])
        $ic++;

      if($ncorrigidas != null && $cod_user_lista[$j] == $ncorrigidas[$ic]["cod"])
        $nao_corrigidos[$j]=$ncorrigidas[$ic]["num"];
      else
        $nao_corrigidos[$j]=0;
  
      $j = 1; //o 0 eh reservado para o dono(usuario q esta logado), a não ser que o mesmo seja o admtele
      $total = 1;
      $k = 0;
      $ic = 0;
    }

    for($i = 0;$i < $numlinhas;$i++)
    {
      $nome[$i] = $titulos[$i]["titulo"];
      $cod_user_lista[$i] = $titulos[$i]["cod"];

      while($k<count($nsubmetidas) && $nsubmetidas[$k]["cod"] < $cod_user_lista[$j])
        $k++;

      if($nsubmetidas != null && $cod_user_lista[$i] == $nsubmetidas[$k]["cod"])
        $nao_submetidos[$i]=$nsubmetidas[$k]["num"];
      else
        $nao_submetidos[$i]=0;

      while($ic<count($ncorrigidas) && $ncorrigidas[$ic]["cod"]<$cod_user_lista[$j])
        $ic++;

      if($ncorrigidas != null && $cod_user_lista[$i] == $ncorrigidas[$ic]["cod"])
        $nao_corrigidos[$i]=$ncorrigidas[$ic]["num"];
      else
        $nao_corrigidos[$i]=0;
    }
    
    $total = $numlinhas;
    $i = 0;
	    
    if ($total > 0)
    {
      for($i=0;$i<$total;$i++)
      {
        $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
         
        echo("                  <tr id=\"tr_".$cod_user_lista[$i]."\">\n");
        echo("                    <td align=\"left\">".$icone."<a href=\"ver_exercicios.php?cod_curso=".$cod_curso."&visualizar=".$visualizar."&cod=".$cod_user_lista[$i]."\">".$nome[$i]."</a></td>\n");
        echo("                    <td>".$nao_submetidos[$i]."</td>\n");
        echo("                    <td>".$nao_corrigidos[$i]."</td>\n");
        echo("                  </tr>\n");
      }
    }
    else
    {
      echo("                  <tr>\n");
      /* ? - Nao ha nenhum exericio */
      echo("                    <td colspan=\"7\">Nao ha nenhum exericio</td>\n");
      echo("                  </tr>\n");
    }

	echo("                </table>\n");
	echo("              </td>\n");
  	echo("            </tr>\n");
  	echo("          </table>\n");
    echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");
  }
  else
  {
    //*NAO �FORMADOR*/
	/* 1 - Enquete */
  	/* 37 - Area restrita ao formador. */
  	echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,37)."</h4>\n");
	
        /*Voltar*/
        echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

        echo("          <div id=\"mudarFonte\">\n");
        echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
        echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
        echo("          </div>\n");

    	/* 23 - Voltar (gen) */
    	echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");
  }

  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");
  
  if($tela_formador)
  {
  	/* Agrupar*/
  	echo("    <div id=\"layer_agrupar\" class=popup>\n");
  	echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_agrupar);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  	echo("      <div class=int_popup>\n");
  	echo("        <div class=ulPopup>\n");    
  	/* ? - Agrupar por: */
  	echo("            Agrupar por:<br />\n");
  	echo("            <select class=\"input\" id=\"agrupamento\">");
  	if($visualizar == "I")
  	  echo("              <option value=\"A\" selected>Aluno</option>");
  	else if($visualizar == "G")
  	  echo("              <option value=\"G\" selected>Grupo</option>");
  	echo("              <option value=\"T\">Titulo do Exercicio</option>");
  	echo("            </select><br /><br />");
  	/* 18 - Ok (gen) */
  	echo("            <input type=\"button\" id=\"ok_agrupar\" class=\"input\" onClick=\"MudaAgrupamento(document.getElementById('agrupamento').value);\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  	/* 2 - Cancelar (gen) */
  	echo("            &nbsp; &nbsp;<input type=\"button\" class=\"input\" onClick=\"EscondeLayer(lay_agrupar);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  	echo("      </div>\n");
  	echo("    </div>\n");
  	echo("    </div>\n\n");
  }

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>