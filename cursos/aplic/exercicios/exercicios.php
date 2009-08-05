<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/exercicios.php

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
  ARQUIVO : cursos/aplic/exercicios/exercicios.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->registerFunction("AlteraStatusExercicioDinamic");
  $objAjax->registerFunction("MudarCompartilhamentoDinamic");
  $objAjax->registerFunction("CancelaAplicacaoExercicioDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();
  
  $cod_ferramenta = 23;
  $visualizar = $_GET['visualizar'];
  $data_atual = time();

  $cod_curso = $_GET['cod_curso'];
  $cod_usuario_global=VerificaAutenticacao($cod_curso);
  
  $sock = Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  
  /* Se for aluno, manda para a pagina de exercicios individuais dele */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  	header("Location: ver_exercicios.php?cod_curso=".$cod_curso."&visualizar=I&cod=".$cod_usuario_global); 
  
  include("../topo_tela.php");
  
  if($visualizar == "E")
  {
    $lista_exercicios = RetornaExercicios($sock);
  }
  else if($visualizar == "L")
  {
  	$lista_exercicios = RetornaExerciciosLixeira($sock);
  }

  /*********************************************************/
  /* in�io - JavaScript */
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");
  echo("  <script  type=\"text/javascript\" language=\"JavaScript\">\n\n");
  
  echo("    var js_cod_item;\n");
  echo("    var js_comp = new Array();\n");
  echo("    var cod_comp;");
  echo("    var numExercicios = ".count($lista_exercicios).";\n\n");
  
  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  if($visualizar == "E")
  {
  	echo("      lay_novo_exercicio = getLayer('layer_novo_exercicio');\n");
  	echo("      cod_comp = getLayer(\"comp\");\n");
  }
  echo("      startList();\n");
  echo("    }\n\n");

  if($visualizar == "E")
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
  	echo("      hideLayer(lay_novo_exercicio);\n");
  	echo("      hideLayer(cod_comp);\n");
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

    echo("    function NovoExercicio()\n");
    echo("    {\n");
    echo("      MostraLayer(lay_novo_exercicio, 0);\n");
    echo("      document.form_novo_exercicio.novo_titulo.value = '';\n");
    echo("      document.getElementById(\"nome\").focus();\n");
    echo("    }\n");
    
    echo("    function AtualizaCampos(id,data,dt_disp,dt_entrega,situacao)\n");
    echo("    {\n");
    echo("      document.getElementById('data_'+id).innerHTML = data;\n");
    echo("      document.getElementById('disp_'+id).innerHTML = dt_disp;\n");
    echo("      document.getElementById('entrega_'+id).innerHTML = dt_entrega;\n");
    echo("      document.getElementById('situacao_'+id+'_A').innerHTML = situacao;\n");
    echo("      document.getElementById('situacao_'+id+'_A').id = 'situacao_'+id+'_C';\n");
    echo("    }\n");
    
    echo("    function CancelarAplicacao()\n");
    echo("    {\n");
    echo("      var i;\n");
    echo("      var getNumber;\n");
    echo("      var cod_itens=document.getElementsByName('chkExercicio');\n");
    echo("      var Cabecalho = document.getElementById('checkMenu');\n");
    echo("      for (i=0; i < cod_itens.length; i++){\n");
    echo("        if (cod_itens[i].checked){\n");
    echo("          getNumber = cod_itens[i].id.split(\"_\");\n");
    echo("          xajax_CancelaAplicacaoExercicioDinamic(".$cod_curso.",getNumber[1]);\n");
    echo("          AtualizaCampos(getNumber[1],'".UnixTime2Data(time())."','-','-','Em criacao');\n");
    echo("        }\n");
    echo("      }\n");
    // ?? - Em criacao
    echo("      mostraFeedback('Aplicacao cancelada.',true);\n");
    echo("    }\n");
    
    echo("    function VerificaCheck(){\n");
    echo("      var i;\n");
    echo("      var j = 0;\n");
    echo("      var getNumber;");
    echo("      var cod_itens=document.getElementsByName('chkExercicio');\n");
    echo("      var Cabecalho = document.getElementById('checkMenu');\n");
    echo("      var flag = 1;\n");
    echo("      for (i=0; i < cod_itens.length; i++){\n");
    echo("        if (cod_itens[i].checked){\n");
    echo("          j++;\n");
    echo("          getNumber = cod_itens[i].id.split(\"_\");\n");
    echo("          if(document.getElementById('situacao_'+getNumber[1]+'_C')){\n");
    echo("            flag = 0;\n");
    echo("          }\n");
    echo("        }\n");
    echo("      }\n");
    echo("      if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
    echo("      else Cabecalho.checked=false;\n");
    echo("      if(j > 0){\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){ TratarSelecionados('L'); };\n");
    echo("        if(flag)\n");
    echo("        {\n");
    echo("          document.getElementById('mCancelarAplic_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mCancelarAplic_Selec').onclick=function(){ CancelarAplicacao(); };\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    echo("          document.getElementById('mCancelarAplic_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mCancelarAplic_Selec').onclick=function(){ };\n");
    echo("        }\n");
    echo("      }else{\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("        document.getElementById('mCancelarAplic_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mCancelarAplic_Selec').onclick=function(){ };\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  else if($visualizar == "L")
  {
  	echo("    function VerificaCheck(){\n");
  	echo("      var i;\n");
  	echo("      var j=0;\n");
  	echo("      var cod_itens=document.getElementsByName('chkExercicio');\n");
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
  	echo("        document.getElementById('mRecup_Selec').className=\"menuUp\";\n");
  	echo("        document.getElementById('mRecup_Selec').onclick=function(){  };\n");
  	echo("      }\n");
  	echo("    }\n\n");
  }

  echo("    function CheckTodos(){\n");
  echo("      var e;\n");
  echo("      var i;\n");
  echo("      var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("      var cod_itens=document.getElementsByName('chkExercicio');\n");
  echo("      for(i = 0; i < cod_itens.length; i++){\n");
  echo("        e = cod_itens[i];\n");
  echo("        e.checked = CabMarcado;\n");
  echo("      }\n");
  echo("      VerificaCheck();\n");
  echo("    }\n\n");
  
  echo("    function DeletarLinhas(deleteArray,j){\n");
  echo("      var i,trExercicio;\n");
  echo("	  for(i=0;i<j;i++)\n");
  echo("      {\n");
  echo("        trExercicio = document.getElementById('trExercicio_'+deleteArray[i]);\n");
  echo("        trExercicio.parentNode.removeChild(trExercicio);\n");
  echo("	  }\n");
  echo("    }\n\n");
  
  echo("    function IntercalaCorLinha(){\n");
  echo("      var checks,i,trExercicio;\n");
  echo("      checks = document.getElementsByName('chkExercicio');\n");
  echo("      corLinha = 0;\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        getNumber=checks[i].id.split('_');\n");
  echo("        trExercicio = document.getElementById('trExercicio_'+getNumber[1]);\n");
  echo("        trExercicio.className = 'altColor'+(i%2);\n");
  echo("      }\n");
  echo("    }\n\n");
  
  echo("    function Confirma(op){\n");
  echo("        if(op == 'X')\n");
  echo("          return confirm('Tem certeza que deseja excluir definitivamente os exercicios selecionadas?');\n");
  echo("        else if(op == 'V')\n");
  echo("          return confirm('Tem certeza que deseja recuperar os exercicios selecionadas?');\n");
  echo("        else if(op == 'L')\n");
  echo("          return confirm('Tem certeza que deseja enviar para lixeira os exercicios selecionadas?');\n");
  echo("    }\n\n");
  
  echo("    function InsereLinhaVazia(){\n");
  echo("	  var table,tr,td;");
  echo("	  table = document.getElementById(\"tabelaInterna\");\n");
  echo("	  tr = document.createElement(\"tr\");\n");
  echo("	  td = document.createElement(\"td\");\n");
  echo("	  td.colSpan = \"7\";\n");
  //?
  echo("	  td.appendChild(document.createTextNode('Nao ha nenhum exericio'));\n");
  echo("	  tr.appendChild(td);\n");
  echo("	  table.appendChild(tr);\n");
  echo("    }\n\n");
  
  echo("      function AtualizaComp(js_tipo_comp)\n");
  echo("      {\n");
  echo("        if ((isNav) && (!isMinNS6)) {\n");
  echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
  echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_N'));\n");
  echo("        } else {\n");
  echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("            document.form_comp.cod_item.value=js_cod_item;\n");
  echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_N'));\n");
  echo("        }\n");
  echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
  echo("        if (js_tipo_comp=='F') {\n");
  echo("          tipo_comp[0].innerHTML=imagem;\n");
  echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
  echo("        } else{\n");
  echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
  echo("          tipo_comp[1].innerHTML=imagem;\n");
  echo("        }\n");
  echo("      }\n\n");
  
  echo("    function RetornaTexto(op){\n");
  echo("        if(op == 'X')\n");
  echo("          return 'Exercicio(s) excluido(s) da lixeira.';\n");
  echo("        else if(op == 'V')\n");
  echo("          return 'Exercicio(s) recuperado(s).';\n");
  echo("        else if(op == 'L')\n");
  echo("          return 'Exercicio(s) enviado(s) para lixeira.';\n");
  echo("    }\n\n");
    
  echo("    function TratarSelecionados(op){\n");
  echo("	  var checks,deleteArray,j;\n");
  echo("      checks = document.getElementsByName('chkExercicio');\n");
  echo("	  deletaArray = new Array();\n");
  echo("      j=0;\n");
  echo("      if(Confirma(op)){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split(\"_\");\n");
  echo("          xajax_AlteraStatusExercicioDinamic(".$cod_curso.",getNumber[1],op);\n");
  echo("          deletaArray[j++] = getNumber[1];\n");
  echo("		  numExercicios--;");
  echo("          }\n");
  echo("        }\n");
  echo("		DeletarLinhas(deletaArray,j);\n");
  echo("		if(numExercicios > 0)\n");
  echo("          IntercalaCorLinha();\n");
  echo("		else\n");
  echo("          InsereLinhaVazia();\n");
  echo("        VerificaCheck();\n");
  echo("		mostraFeedback(RetornaTexto(op),true);\n");
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
	/* ? - Exercicios - Biblioteca de Exercicios*/
  	$frase = "Exercicios - Biblioteca de Exercicios";
    if($visualizar == "L")
  		$frase = $frase." - Lixeira";
  	
	echo("          <h4>".$frase."</h4>\n");
	
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
    if($visualizar == "E")
    {
      // ? - Novo exercicio
      echo("                  <li><span onclick=\"NovoExercicio();\">Novo exercicio</span></li>\n");
      // ? - Lixeira
      echo("                  <li><span onclick=\"document.location='exercicios.php?cod_curso=".$cod_curso."&visualizar=L';\">Lixeira</span></li>\n");
    }
    else if($visualizar == "L")
    {
      /* ? - Exercicios */
      echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."&visualizar=E'>Exercicios</a></li>\n");
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
    /* ? - Data*/
	echo("                    <td width=\"10%\">Data</td>\n");
	if($visualizar == "E")
    {
      /* ? - Disponibilizacao */
	  echo("                    <td width=\"10%\">Disponibilizacao</td>\n");
      /* ? - Limite de entrega*/
	  echo("                    <td width=\"10%\">Limite de entrega</td>\n");
      /* ? - Compartilhamento */
	  echo("                    <td width=\"10%\">Compartilhamento</td>\n");
	  /* ? - Situacao */
	  echo("                    <td width=\"10%\">Situacao</td>\n");
    }
	echo("                  </tr>\n");

   	if ((count($lista_exercicios)>0)&&($lista_exercicios != null))
    {
      foreach ($lista_exercicios as $cod => $linha_item)
      {
        $disponibilizacao = "-";
        $entrega = "-";
      
      	if($linha_item['situacao'] == 'A')
        {
        	$dados_aplicado = RetornaDadosExercicioAplicado($sock,$linha_item['cod_exercicio']);
        	$disponibilizacao = UnixTime2DataHora($dados_aplicado['dt_disponibilizacao']);
        	$entrega = UnixTime2DataHora($dados_aplicado['dt_limite_submissao']);
        }
        
      	$data = "<span id=\"data_".$linha_item['cod_exercicio']."\">".UnixTime2Data($linha_item['data'])."</span>";
        $titulo = $linha_item['titulo'];
        $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
        $situacao = RetornaSituacaoExercicio($linha_item['situacao'],$data_atual,$dados_aplicado['dt_disponibilizacao']);
        
        /* ?? - Compartilhado com Formadores */
        if($linha_item['tipo_compartilhamento'] == "F")
          $compartilhamento = "Compartilhado com Formadores";
        /* ?? - Nao compartilhado */
        else
          $compartilhamento = "Nao compartilhado";
        
        if($cod_usuario == $linha_item['cod_usuario'])
          $compartilhamento = "<span id=\"comp_".$linha_item['cod_exercicio']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_exercicio']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";

        echo("                  <tr id=\"trExercicio_".$linha_item['cod_exercicio']."\">\n");
        echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkExercicio\" id=\"itm_".$linha_item['cod_exercicio']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_exercicio']."\" /></td>\n");
        echo("                    <td align=\"left\">".$icone."<a href=\"editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$linha_item['cod_exercicio']."\">".$titulo."</a></td>\n");
        echo("                    <td id=\"data_".$linha_item['cod_exercicio']."\">".$data."</td>\n");
        if($visualizar == "E")
        {
          echo("                    <td id=\"disp_".$linha_item['cod_exercicio']."\">".$disponibilizacao."</td>\n");
          echo("                    <td id=\"entrega_".$linha_item['cod_exercicio']."\">".$entrega."</td>\n");
          echo("                    <td>".$compartilhamento."</td>\n");
          echo("                    <td id=\"situacao_".$linha_item['cod_exercicio']."_".$linha_item['situacao']."\">".$situacao."</td>\n");
        }
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
	
	if($visualizar == "E")
	{
	  echo("                <ul>\n");
      /* ? - Apagar selecionados */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">Apagar selecionados</span></li>\n");
      echo("                  <li id=\"mCancelarAplic_Selec\" class=\"menuUp\"><span id=\"cancelarAplicSelec\">Cancelar aplicacao dos selecionados</span></li>\n");
      echo("                </ul>\n");
	}
	else if($visualizar == "L")
	{
	  echo("                <ul>\n");
      /* ? - Apagar selecionados */
      echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">Apagar selecionados</span></li>\n");
      /* ? - Recuperar selecionados */
      echo(" 			      <li id=\"mRecup_Selec\" class=\"menuUp\"><span id=\"recuperarSelec\">Recuperar selecionados</span></li>\n");
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

  if($tela_formador && $visualizar == "E")
  {
  	/* Novo Exercicio*/
  	echo("    <div id=\"layer_novo_exercicio\" class=popup>\n");
  	echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_novo_exercicio);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  	echo("      <div class=int_popup>\n");
  	echo("        <form name=\"form_novo_exercicio\" method=\"post\" action=\"acoes.php\" onSubmit='return(VerificaNovoTitulo(document.form_novo_exercicio.novo_titulo, 1));'>\n");
  	echo("          <div class=ulPopup>\n");    
  	/* ? - Titulo: */
  	echo("            Titulo:<br />\n");
  	echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=150 /><br />\n");
  	echo("            <input type=hidden name=cod_curso value=\"".$cod_curso."\" />\n");
  	echo("            <input type=hidden name=acao value=criarExercicio />\n");
  	echo("            <input type=hidden name=cod_usuario value=\"".$cod_usuario."\" />\n");
  	echo("            <input type=hidden name=origem value=exercicios />\n");
  	/* 18 - Ok (gen) */
  	echo("            <input type=\"submit\" id=\"ok_novoexercicio\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  	/* 2 - Cancelar (gen) */
  	echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_novo_exercicio);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  	echo("         </div>\n");
  	echo("        </form>\n");
  	echo("      </div>\n");
  	echo("    </div>\n\n");
  	
  	  /* Mudar Compartilhamento */
  	echo("    <div class=popup id=\"comp\">\n");
  	echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  	echo("      <div class=int_popup>\n");
  	echo("        <script type=\"text/javaScript\">\n");
  	echo("        </script>\n");
  	echo("        <form name=\"form_comp\" action=\"\" id=\"form_comp\">\n");
  	echo("          <input type=hidden name=cod_curso value=\"".$cod_curso."\" />\n");
  	echo("          <input type=hidden name=cod_usuario value=\"".$cod_usuario."\" />\n");
  	echo("          <input type=hidden name=cod_item value=\"\" />\n");
  	echo("          <input type=hidden name=tipo_comp id=tipo_comp value=\"\" />\n");
  	echo("          <input type=hidden name=texto id=texto value=\"Texto\" />\n");
  	echo("          <ul class=ulPopup>\n");
  	echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores', 'E'); EscondeLayers();\">\n");
  	echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
  	/* ?? - Compartilhado com formadores */
  	echo("              <span>Compartilhado com formadores</span>\n");
  	echo("            </li>\n");
  	echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'E'); EscondeLayers();\">\n");
  	echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
  	/* ?? - Nao Compartilhado */
  	echo("              <span>Nao Compartilhado</span>\n");
  	echo("            </li>\n");
 	echo("          </ul>\n");    
  	echo("        </form>\n");
  	echo("      </div>\n");
  	echo("    </div>\n");
  }

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>