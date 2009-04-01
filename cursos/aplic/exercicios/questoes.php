<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/questoes.php

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
  ARQUIVO : cursos/aplic/exercicios/questoes.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->registerFunction("AlteraStatusQuestaoDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_ferramenta=24;
  $visualizar = $_GET['visualizar'];

  include("../topo_tela.php");
  
  if($visualizar == "Q")
    $lista_questoes = RetornaQuestoes($sock);
  else if($visualizar == "L")
  	$lista_questoes = RetornaQuestoesLixeira($sock);

  /*********************************************************/
  /* in�io - JavaScript */
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");
  
  echo("    var numQuestoes = ".count($lista_questoes).";\n\n");
  

  /* Mostra perfil de um usuario. */
  echo("    function OpenWindowPerfil(cod_curso,id)\n");
  echo("    {\n");
  echo("       window.open('../perfil/exibir_perfis.php?cod_curso='+cod_curso+'&cod_aluno[]='+id,'PerfilDisplay','width=700,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("      return(false);\n");
  echo("    }\n\n");

  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  if($visualizar == "Q")
  	echo("      lay_nova_questao = getLayer('layer_nova_questao');\n");
  echo("      startList();\n");
  echo("    }\n\n");

  if($visualizar == "Q")
  {
  	echo("    function VerificaNovoTitulo(textbox, aspas) {\n");
  	echo("      texto=textbox.value;\n");
  	echo("      if (texto==''){\n");
  	echo("        // se nome for vazio, nao pode\n");
                  /* 15 - O titulo nao pode ser vazio. */
  	echo("        alert(\"".RetornaFraseDaLista($lista_frases,15)."\");\n");
  	echo("        textbox.focus();\n");
  	echo("        return false;\n");
  	echo("      }\n");
  	echo("      // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
  	echo("      else if ((texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0)&&(!aspas)) {\n");
                /* 16 - O t�tulo n�o pode conter \\. */
  	echo("         alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,16)))."\");\n");
  	echo("        textbox.value='';\n");
  	echo("        textbox.focus();\n");
  	echo("        return false;\n");
  	echo("      }\n");
  	echo("      return true;\n");
  	echo("    }\n\n");
  
  	echo("    function EscondeLayers()\n");
  	echo("    {\n");
  	echo("      hideLayer(lay_nova_questao);\n");
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

    echo("    function NovaQuestao()\n");
    echo("    {\n");
    echo("      MostraLayer(lay_nova_questao, 0);\n");
    echo("      document.form_nova_questao.novo_titulo.value = '';\n");
    echo("      document.getElementById(\"nome\").focus();\n");
    echo("    }\n");
    
    echo("    function VerificaCheck(){\n");
    echo("      var i;\n");
    echo("      var j=0;\n");
    echo("      var cod_itens=document.getElementsByName('chkQuestao');\n");
    echo("      var Cabecalho = document.getElementById('checkMenu');\n");
    echo("      for (i=0; i < cod_itens.length; i++){\n");
    echo("        if (cod_itens[i].checked){\n");
    echo("          j++;\n");
    echo("        }\n");
    echo("      }\n");
    echo("      if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
    echo("      else Cabecalho.checked=false;\n");
    echo("      if(j > 0){\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){ TratarSelecionados('L'); };\n");
    echo("      }else{\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  else if($visualizar == "L")
  {
  	echo("    function VerificaCheck(){\n");
  	echo("      var i;\n");
  	echo("      var j=0;\n");
  	echo("      var cod_itens=document.getElementsByName('chkQuestao');\n");
  	echo("      var Cabecalho = document.getElementById('checkMenu');\n");
  	echo("      for (i=0; i < cod_itens.length; i++){\n");
  	echo("        if (cod_itens[i].checked){\n");
  	echo("          var item = cod_itens[i].id.split('_');\n");
  	echo("          j++;\n");
  	echo("        }\n");
  	echo("      }\n");
  	echo("      if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
  	echo("      else Cabecalho.checked=false;\n");
  	echo("      if(j > 0){\n");
  	echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
  	echo("        document.getElementById('mExcluir_Selec').onclick=function(){ TratarSelecionados('X'); };\n");
  	echo("        document.getElementById('mRecup_Selec').className=\"menuUp02\";\n");
  	echo("        document.getElementById('mRecup_Selec').onclick=function(){ TratarSelecionados('V'); };\n");
  	echo("      }else{\n");
  	echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
  	echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
  	echo("        document.getElementById('mRecup_Selec').className=\"menuUp02\";\n");
  	echo("        document.getElementById('mRecup_Selec').onclick=function(){  };\n");
  	echo("      }\n");
  	echo("    }\n\n");
  }

  echo("    function CheckTodos(){\n");
  echo("      var e;\n");
  echo("      var i;\n");
  echo("      var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("      var cod_itens=document.getElementsByName('chkQuestao');\n");
  echo("      for(i = 0; i < cod_itens.length; i++){\n");
  echo("        e = cod_itens[i];\n");
  echo("        e.checked = CabMarcado;\n");
  echo("      }\n");
  echo("      VerificaCheck();\n");
  echo("    }\n\n");
  
  echo("    function DeletarLinhas(deleteArray,j){\n");
  echo("      var i,trQuestao;\n");
  echo("	  for(i=0;i<j;i++)\n");
  echo("      {\n");
  echo("        trQuestao = document.getElementById('trQuestao_'+deleteArray[i]);\n");
  echo("        trQuestao.parentNode.removeChild(trQuestao);\n");
  echo("	  }\n");
  echo("    }\n\n");
  
  echo("    function IntercalaCorLinha(){\n");
  echo("      var checks,i,trQuestao;\n");
  echo("      checks = document.getElementsByName('chkQuestao');\n");
  echo("      corLinha = 0;\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        getNumber=checks[i].id.split('_');\n");
  echo("        trQuestao = document.getElementById('trQuestao_'+getNumber[1]);\n");
  echo("        trQuestao.className = 'altColor'+(i%2);\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function Confirma(op){\n");
  echo("        if(op == 'X')\n");
  echo("          return confirm('Tem certeza que deseja excluir definitivamente as questoes selecionadas?');\n");
  echo("        else if(op == 'V')\n");
  echo("          return confirm('Tem certeza que deseja recuperar os exercicios selecionadas?');\n");
  echo("        else if(op == 'L')\n");
  echo("          return confirm('Tem certeza que deseja enviar para lixeira as questoes selecionadas?');\n");
  echo("    }\n\n");
  
  echo("    function InsereLinhaVazia(){\n");
  echo("	  var table,tr,td;");
  echo("	  table = document.getElementById(\"tabelaInterna\");\n");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("	  td = document.createElement(\"td\");\n");
  echo("	  td.colSpan = \"6\";\n");
  echo("	  td.appendChild(document.createTextNode('Nao ha nenhuma questao'));\n");
  echo("	  tr.appendChild(td);\n");
  echo("	  table.appendChild(tr);\n");
  echo("    }\n\n");
    
  echo("    function TratarSelecionados(op){\n");
  echo("	  var checks,deleteArray,j;\n");
  echo("      checks = document.getElementsByName('chkQuestao');\n");
  echo("	  deletaArray = new Array();\n");
  echo("      j=0;\n");
  echo("      if(Confirma(op)){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split(\"_\");\n");
  echo("          xajax_AlteraStatusQuestaoDinamic(".$cod_curso.",getNumber[1],op);\n");
  echo("          deletaArray[j++] = getNumber[1];\n");
  echo("		  numQuestoes--;");
  echo("          }\n");
  echo("        }\n");
  echo("		DeletarLinhas(deletaArray,j);\n");
  echo("		if(numQuestoes > 0)\n");
  echo("          IntercalaCorLinha();\n");
  echo("		else\n");
  echo("          InsereLinhaVazia();\n");
  echo("        VerificaCheck();\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("\n</script>\n\n");
  /* fim - JavaScript */
  /*********************************************************/

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if ($tela_formador)
  {
	/* ? - Exercicios*/
        /* ? - Banco de questoes*/
	echo("          <h4>Exercicios - Banco de questoes</h4>\n");
	
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

    /* ? - Exercicios */
    echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."'>Exercicios</a></li>\n");

  	echo("                </ul>\n");
  	echo("              </td>\n");
  	echo("            </tr>\n");
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <ul class=\"btAuxTabs03\">\n");
    if($visualizar == "Q")
    {
      // ? - Nova questao
      echo("                  <li><span onclick=\"NovaQuestao();\">Nova questao</span></li>\n");
      // ? - Lixeira
      echo("                  <li><span onclick=\"document.location='questoes.php?cod_curso=".$cod_curso."&visualizar=L';\">Lixeira</span></li>\n");
    }
    else if($visualizar == "L")
    {
      /* ? - Banco de questoes */
      echo("                  <li><a href='questoes.php?cod_curso=".$cod_curso."&visualizar=Q'>Banco de questoes</a></li>\n");
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

        echo("                  <tr class=\"altColor".($cod%2)."\" id=\"trQuestao_".$linha_item['cod_questao']."\">\n");
        echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkQuestao\" id=\"itm_".$linha_item['cod_questao']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_questao']."\" /></td>\n");
        echo("                    <td align=left>".$icone."<a href=\"editar_questao.php?cod_curso=".$cod_curso."&cod_questao=".$linha_item['cod_questao']."\">".$titulo."</a></td>\n");
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
      /* ? - Não há nenhuma questao */
      echo("                    <td colspan=\"6\">Nao ha nenhuma questao</td>\n");
      echo("                  </tr>\n");
    }

	echo("                </table>\n");
	
	if($visualizar == "Q")
	{
	  echo("                <ul>\n");
      /* ? - Apagar selecionadas */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">Apagar Selecionadas</span></li>\n");
      echo("                </ul>\n");
	}
	else if($visualizar == "L")
	{
	  echo("                <ul>\n");
      /* ? - Apagar selecionadas */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">Apagar Selecionadas</span></li>\n");
      /* ? - Recuperar selecionadas */
      echo(" 					<li id=\"mRecup_Selec\" class=\"menuUp\"><span id=\"recuperarSelec\">Recuperar Selecionadas</span></li>\n");
      echo("                </ul>\n");
	}
	
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

  if($tela_formador && $visualizar == "Q")
  {
  	/* Nova Questao */
  	echo("    <div id=\"layer_nova_questao\" class=popup>\n");
  	echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_questao);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  	echo("      <div class=int_popup>\n");
  	echo("        <form name=form_nova_questao method=post action=acoes.php onSubmit='return(VerificaNovoTitulo(document.form_nova_questao.novo_titulo, 1));'>\n");
  	echo("          <div class=ulPopup>\n");    
  	/* ? - Titulo: */
  	echo("            Titulo:<br />\n");
  	echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=150 /><br />\n");
  	/* ? - Tipo da questao: */
  	echo("            Tipo da questao:<br />\n");
  	echo("            <select class=\"input\" name=\"tp_questao\">");
  	echo("              <option value=\"O\" selected>Objetiva</option>");
  	echo("              <option value=\"D\">Dissertativa</option>");
  	echo("            </select><br /><br />");
  	echo("            <input type=hidden name=cod_curso value=\"".$cod_curso."\" />\n");
  	echo("            <input type=hidden name=acao value=criarQuestao />\n");
  	echo("            <input type=hidden name=cod_usuario value=\"".$cod_usuario."\" />\n");
  	echo("            <input type=hidden name=origem value=questoes />\n");
  	/* 18 - Ok (gen) */
  	echo("            <input type=\"submit\" id=\"ok_novaquestao\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  	/* 2 - Cancelar (gen) */
  	echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_questao);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  	echo("         </div>\n");
  	echo("        </form>\n");
  	echo("      </div>\n");
  	echo("    </div>\n\n");
  }

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>