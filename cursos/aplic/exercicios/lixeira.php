<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/lixeira.php

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
  ARQUIVO : cursos/aplic/exercicios/lixeira.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->registerFunction("ExcluirExerDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_ferramenta=24;

  $categ = "N";

  include("../topo_tela.php");
  
  /*********************************************************/
  /* in�io - JavaScript */
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\" src='../bibliotecas/dhtmllib.js'></script>\n");
  echo("  <script  type=\"text/javascript\" src='jscriptlib.js'> </script>\n");

  echo("  <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");

  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  //echo("      lay_novo_exercicio = getLayer('layer_novo_exercicio');\n");
  echo("      startList();\n");
  echo("    }\n\n");
  
  /* Mostra perfil de um usuario. */
  echo("    function OpenWindowPerfil(cod_curso,id)\n");
  echo("    {\n");
  echo("       window.open('../perfil/exibir_perfis.php?cod_curso='+cod_curso+'&cod_aluno[]='+id,'PerfilDisplay','width=700,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("      return(false);\n");
  echo("    }\n\n");
  
  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        var Cabecalho = document.getElementById('checkMenu');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i < cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            var item = cod_itens[i].id.split('_');\n");
  echo("            array_itens[j]=item[1];\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if(j > 0){\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
  echo("        }else{\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");
  
  echo("	  function ExcluirSelecionados()\n");
  echo("	  {\n");
  /* ? - Tem certeza que deseja apagar definitvamente as questoes selecionadas?  */
  echo("        if (confirm('Tem certeza que deseja apagar definitvamente as questoes selecionadas?'))\n");
  echo("		{\n");
  echo("          xajax_ExcluirExerDinamic('".$cod_curso."', array_itens);\n");
  echo("        }\n");
  echo("      }\n\n");
  
  echo("      function Recarregar()\n");
  echo("	  {\n");
  echo("        document.location='lixeira.php?cod_curso=".$cod_curso."';");
  echo("      }\n\n");
  
  echo("  </script>\n");
  
  $objAjax->printJavascript("../xajax_0.2.4/");
  
  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador)
  {

	$texto = "<span id=\"text_0\"></span>";	

	/* ? - Exercicios */
	echo("          <h4>Exercicios - Lixeira</h4>\n");
	
  	/*Voltar*/
  	echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  	echo("          <div id=\"mudarFonte\">\n");
  	echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  	echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  	echo("          </div>\n");

	echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo("            <tr>\n");
	echo("              <td valign=\"top\">\n");

  	echo("                <ul class=\"btAuxTabs\">\n");


  	/* 23 - Voltar  (gen) */
  	//echo("                  <li><span onclick='Voltar();'>".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");

  	if ($tela_formador)
  	{
	  /* ? - Exercicios*/
	  echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."'>Exercicios</a></li>\n");
      /* ? - Banco de questoes */
      echo("                  <li><a href='questoes.php?cod_curso=".$cod_curso."'>Banco de questoes</a></li>\n");
  	}

  	echo("                </ul>\n");
  	echo("              </td>\n");
  	echo("            </tr>\n");
  	
  	echo("            <tr>\n");
  	echo("              <td valign=\"top\">\n");
	echo("                <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
	echo("                  <tr class=\"head\">\n");
        echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></td>\n");
	/* ? - T�ulo */
	echo("                    <td class=\"alLeft\">Titulo</td>\n");
        /* ? - Autor */
	echo("                    <td width=\"15%\">Autor</td>\n");
        /* ? - Topico */
	echo("                    <td width=\"15%\">Topico</td>\n");
        /* ? - Tipo*/
	echo("                    <td width=\"12%\">Tipo</td>\n");
        /* ? - Data */
	echo("                    <td width=\"10%\">Data</td>\n");
	echo("                  </tr>\n");

        $lista_questoes = RetornaQuestoesLixeira($sock);
        
        if ((count($lista_questoes)>0)&&($lista_questoes != null))
        {
          foreach ($lista_questoes as $cod => $linha_item)
          {
            $data = UnixTime2Data($linha_item['data']);
            $autor = "<span class=\"link\" onclick=\"OpenWindowPerfil(".$cod_curso.",".$linha_item['cod_usuario'].");\">".NomeUsuario($sock, $linha_item['cod_usuario'], $cod_curso)."</span>";
            $tipo = $linha_item['tp_questao'];
            $titulo = $linha_item['titulo'];
            $topico = RetornaNomeTopico($sock,$linha_item['cod_topico']);
            $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";

            echo("                  <tr class=\"altColor".($cod%2)."\">\n");
            echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_exercicio']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_exercicio']."\" /></td>\n");
            echo("                    <td align=left>".$icone.$titulo."</td>\n");
            echo("                    <td>".$autor."</td>\n");
            echo("                    <td>".$topico."</td>\n");
            echo("                    <td>".$tipo."</td>\n");
            echo("                    <td>".$data."</td>\n");
            echo("                  </tr>\n");
          }
        }
        else
        {
          echo("                  <tr>\n");
          /* ? - Não há nenhuma questao na lixeira. */
          echo("                    <td colspan=\"6\">Nao ha nenhuma questao na lixeira.</td>\n");
          echo("                  </tr>\n");
        }

	echo("                </table>\n");
	
	echo("                <ul>\n");
    /* ? - Apagar selecionadas */
    echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">Apagar Selecionadas</span></li>\n");
    echo("                </ul>\n");
	
	echo("              </td>\n");
  	echo("            </tr>\n");
  	echo("          </table>\n");
        echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span>\n");
  //*NAO �FORMADOR*/
  }
  else
  {
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
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>