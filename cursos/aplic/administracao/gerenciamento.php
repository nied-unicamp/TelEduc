<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento.php

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
  ARQUIVO : cursos/aplic/administracao/gerenciamento.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objMaterial = new xajax();
  // Registre os nomes das funï¿½ï¿½es em PHP que vocï¿½ quer chamar atravï¿½s do xajax
  $objMaterial->registerFunction("AtivarDesativarPortDinamic");
  $objMaterial->registerFunction("PaginacaoDinamic");
  $objMaterial->registerFunction("MudaGuiaDinamic");
  $objMaterial->registerFunction("MudaDinamic");
  $objMaterial->registerFunction("Paginacao");
  $objMaterial->registerFunction("IniciaPaginacaoDinamic");
  
  // Manda o xajax executar os pedidos acima.
  $objMaterial->processRequests();
   


  switch($acao){
    case 'N':
      $cod_pagina_ajuda = 8;
      break;
    case 'A':
      $cod_pagina_ajuda = 9;
      break;
    case 'F':
      $cod_pagina_ajuda = 10;
      break;
    case 'G':
      $cod_pagina_ajuda = 10;
      break;
    case 'AG':
      $cod_pagina_ajuda = 10;
      break;
      
  }

  $cod_ferramenta = 0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  include("../topo_tela.php");
  

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro Ã© a aÃ§Ã£o, o segundo Ã© o nÃºmero da frase para ser impressa se for "true", o terceiro caso "false"
  // 255 - Erro na operacao
  // 256 - Transformacao ocorrida com sucesso.
  $feedbackObject->addAction("transformar", 256, 255);
  // 268 - InscriÃ§Ã£o realizada com sucesso
  $feedbackObject->addAction("inscrever_cadastrado", 268, 255);
  $feedbackObject->addAction("inscrever", 268, 255);
  $feedbackObject->addAction("trocar_coordenador", 256, 255);

  if ((!isset($pag_atual))or($pag_atual=='')or($pag_atual==0))
    $pag_atual =  1;
  else $pag_atual = min($pag_atual, $total_pag);

  if (!isset($ordem))
  {
    $ordem="nome";
  }

  $ecoordenador = ECoordenador($sock,$cod_curso,$cod_usuario);
  $cod_coordenador = RetornaCodigoCoordenador($sock, $cod_curso);

  // 269 - Portfolio(s) ativado(s) com sucesso.
  $frase1=RetornaFraseDaLista($lista_frases,269);
  // 270 - Portfolio(s) desativado(s) com sucesso.
  $frase2=RetornaFraseDaLista($lista_frases,270); 
    $cabecalho = "          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n";

  if ($acao=="A")
  {
    /* 102 - Gerenciamento de Alunos */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 102);
    $tipo_usuario="A";
    /* 109 - Nï¿½ de Alunos: */
    $frase_qtde=RetornaFraseDaLista($lista_frases,109);
    $cod_pagina=9;
  }
  else if ($acao=="F")
  {
    /* 103 - Gerenciamento de Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 103);

    $tipo_usuario="F";
    /* 110 - Nï¿½ de Formadores: */
    $frase_qtde=RetornaFraseDaLista($lista_frases,110);
    $cod_pagina=10;
  }
  else if ($acao=="G")
  {
    /* 258 - Gerenciamento de Formadores desligados */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 258);

    $tipo_usuario="f";
    /* 259 - Nï¿½ de Formadores desligados: */
    $frase_qtde=RetornaFraseDaLista($lista_frases,259);
    $cod_pagina=10;
  }
  else if ($acao=="AG")
  {
    /* 283 - Gerenciamento de Alunos desligados */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 283);
  	
    $tipo_usuario="a";
    /* 284 - Nï¿½ de Alunos desligados: */
    $frase_qtde=RetornaFraseDaLista($lista_frases, 284);
    $cod_pagina=10;
  }
  
  else if ($acao == 'z')
  {
    // 165 - Gerenciamento de Convidados
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 165);

    // 166 - Nï¿½ de Convidados:
    $frase_qtde=RetornaFraseDaLista($lista_frases, 166);
    $cod_pagina=13;
  }
  else
  {
    /* 74 - Gerenciamento de Inscriï¿½ï¿½es */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 74);
    /* 78 - Nï¿½ de Inscriï¿½ï¿½es: */
    $frase_qtde=RetornaFraseDaLista($lista_frases,78);
    $cod_pagina=8;

    if($acao=="N")
    {
      // 75 - Inscricoes nao avaliadas 
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases,75);
    } 

    else if($acao=="C")
    {
      // 76 - Inscricoes aceitas
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases,76);
    }

    else if($acao=="R")
    {
      // 77 - Inscricoes rejeitadas
      $cabecalho .= " - ".RetornaFraseDaLista($lista_frases,77);
    }  
  }
  if ($acao=="N")
      $tipo_usuario="a";
  else if ($acao=="C")
      $tipo_usuario="A";
  else
      $tipo_usuario="r";
      
  $ativado=RetornaFraseDaLista($lista_frases, 208);
  $desativado=RetornaFraseDaLista($lista_frases, 209);
   
  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var pag_atual = ".$pag_atual.";\n\n"); 
   
  echo("      function Iniciar()\n");
  echo("     {\n");
                $feedbackObject->returnFeedback($_GET['acao_fb'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("	    xajax_IniciaPaginacaoDinamic(".$cod_curso.",'".$tipo_usuario."','".$ordem."');\n");
  echo("     }\n\n");

  echo("      function MarcaOuDesmarcaTodos()\n");
  echo("      {\n");
  echo("        var e;\n");
  echo("        var CabecalhoMarcado=document.gerenc.check_all.checked;\n");
  echo("        for (var i=0;i<document.gerenc.elements.length;i++)\n");
  echo("        {\n");
  echo("          e = document.gerenc.elements[i];\n");
  echo("          if (e.name=='cod_usu[]')\n");
  echo("          {\n");
  echo("            e.checked=CabecalhoMarcado;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n");
  
  

  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var coordenador=false;\n"); //para verificar o checkbox do coordenador 
  echo("        var cod_itens = document.getElementsByName('cod_usu[]');\n");
  echo("        var Cabecalho = document.getElementById('check_all');\n");
  echo("        for (i=0; i < cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            if(cod_itens[i].value == '".$cod_coordenador."')");
  echo("              coordenador=true;");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if ((j)==(cod_itens.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if(j > 0){\n");
  echo("          document.getElementById('mDados_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mDados_Selec').onclick=function(){ Alerta('dados',''); };\n");
  echo("          if(!coordenador){");
//   echo("          document.getElementById('mApagar_Selec').className=\"menuUp02\";\n");
//   echo("          document.getElementById('mApagar_Selec').onclick=function(){ Apagar(); };\n");
  if($acao == "R" || $acao == "N")
  {
    echo("          document.getElementById('mAceitar_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mAceitar_Selec').onclick=function(){ Alerta('aceitar',''); };\n");
    echo("          document.getElementById('mAceitarVis_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mAceitarVis_Selec').onclick=function(){ Alerta('aceitar_vis',''); };\n");
    if($acao == "R")
    {
      echo("          document.getElementById('mAtivarPort_Selec').className=\"menuUp02\";\n");
      echo("          document.getElementById('mAtivarPort_Selec').onclick=function(){ AtivaDesativaPort('ativar_port'); };\n");
      echo("          document.getElementById('mDesativarPort_Selec').className=\"menuUp02\";\n");
      echo("          document.getElementById('mDesativarPort_Selec').onclick=function(){ AtivaDesativaPort('desativar_port'); };\n");
    }
  }
  if($acao == "N")
  {
    echo("          document.getElementById('mRejeitar_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mRejeitar_Selec').onclick=function(){ Alerta('rejeitar',''); };\n");
  }
  if($acao == "C")
  {
    echo("          document.getElementById('mRejeitar2_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mRejeitar2_Selec').onclick=function(){ Alerta('rejeitarVisitantes',''); };\n");
  }
  if($ecoordenador){
    if($acao == 'A' || $acao == 'F')
    {
      echo("          document.getElementById('mConvidado_Selec').className=\"menuUp02\";\n");
      echo("          document.getElementById('mConvidado_Selec').onclick=function(){ Alerta('convidado','transformar'); };\n");
      if($acao == 'A')
      {
        echo("          document.getElementById('mFormador_Selec').className=\"menuUp02\";\n");
        echo("          document.getElementById('mFormador_Selec').onclick=function(){ Alerta('formador','transformar'); };\n");
        echo("          document.getElementById('mDesligar_Selec').className=\"menuUp02\";\n");
        echo("          document.getElementById('mDesligar_Selec').onclick=function(){ Alerta('remover_aluno',''); };\n");
      }
      if($acao == 'F')
      {
        echo("          document.getElementById('mAluno_Selec').className=\"menuUp02\";\n");
        echo("          document.getElementById('mAluno_Selec').onclick=function(){ Alerta('aluno','transformar'); };\n");
        echo("          document.getElementById('mDesligar_Selec').className=\"menuUp02\";\n");
        echo("          document.getElementById('mDesligar_Selec').onclick=function(){ Alerta('remover_form',''); };\n");
        echo("          document.getElementById('mCoordenador_Selec').className=\"menuUp02\";\n");
        echo("          document.getElementById('mCoordenador_Selec').onclick=function(){ Alerta('','trocar_coordenador'); };\n");
      }
    }
    else if($acao == 'G')
      {
        echo("          document.getElementById('Religar').className=\"menuUp02\";\n");
        echo("          document.getElementById('Religar').onclick=function(){ Alerta('religar_form',''); };\n");
      }
    else if($acao == 'AG')
      {
        echo("          document.getElementById('Religar').className=\"menuUp02\";\n");
        echo("          document.getElementById('Religar').onclick=function(){ Alerta('religar_aluno',''); };\n");
      }
      
  }
  echo("          }\n");
  echo("        }if(j <= 0){\n");
  echo("          document.getElementById('mDados_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mDados_Selec').onclick=function(){ };\n");
  echo("         }\n");
  echo("         if((j <= 0) || (coordenador)){");
//   echo("          document.getElementById('mApagar_Selec').className=\"menuUp\";\n");
//   echo("          document.getElementById('mApagar_Selec').onclick=function(){ };\n");
  if($acao == "R" || $acao == "N")
  {
    echo("          document.getElementById('mAceitar_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mAceitar_Selec').onclick=function(){ };\n");
    echo("          document.getElementById('mAceitarVis_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mAceitarVis_Selec').onclick=function(){ };\n");
    if($acao == "R")
    {
      echo("          document.getElementById('mAtivarPort_Selec').className=\"menuUp\";\n");
      echo("          document.getElementById('mAtivarPort_Selec').onclick=function(){ };\n");
      echo("          document.getElementById('mDesativarPort_Selec').className=\"menuUp\";\n");
      echo("          document.getElementById('mDesativarPort_Selec').onclick=function(){ };\n");
    }
  }
  if($acao == "N")
  {
    echo("          document.getElementById('mRejeitar_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mRejeitar_Selec').onclick=function(){ };\n");
  }
  if($acao == "C")
  {
    echo("          document.getElementById('mRejeitar2_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mRejeitar2_Selec').onclick=function(){ };\n");
  }
  if($ecoordenador){
    if($acao == 'A' || $acao == 'F')
    {
      echo("          document.getElementById('mConvidado_Selec').className=\"menuUp\";\n");
      echo("          document.getElementById('mConvidado_Selec').onclick=function(){ };\n");
      if($acao == 'A')
      {
        echo("          document.getElementById('mFormador_Selec').className=\"menuUp\";\n");
        echo("          document.getElementById('mFormador_Selec').onclick=function(){ };\n");
        echo("          document.getElementById('mDesligar_Selec').className=\"menuUp\";\n");
        echo("          document.getElementById('mDesligar_Selec').onclick=function(){ };\n");
      }
      if($acao == 'F')
      {
        echo("          document.getElementById('mAluno_Selec').className=\"menuUp\";\n");
        echo("          document.getElementById('mAluno_Selec').onclick=function(){ };\n");
        echo("          document.getElementById('mDesligar_Selec').className=\"menuUp\";\n");
        echo("          document.getElementById('mDesligar_Selec').onclick=function(){ };\n");
        echo("          document.getElementById('mCoordenador_Selec').className=\"menuUp\";\n");
        echo("          document.getElementById('mCoordenador_Selec').onclick=function(){ };\n");
      }
    }
    else if($acao == 'G')
      {
        echo("          document.getElementById('Religar').className=\"menuUp\";\n");
        echo("          document.getElementById('Religar').onclick=function(){ };\n");
      }
    else if($acao == 'AG')
      {
        echo("          document.getElementById('Religar').className=\"menuUp\";\n");
        echo("          document.getElementById('Religar').onclick=function(){ };\n");
      }
  }
  echo("          }\n"); 
  echo("        }\n");


  echo("      function DesmarcaCabecalho()\n");
  echo("      {\n");
  echo("        document.gerenc.check_all.checked=false;\n");
  echo("      }\n");

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
  echo("      }\n");

  echo("      function Alerta(tipo,action)\n");
  echo("      {\n");
  echo("        var cont=false;\n");
  echo("        var e;\n");
  echo("        for (var i=0; i < document.gerenc.elements.length;i++)\n");
  echo("        {\n");
  echo("          e = document.gerenc.elements[i];\n");
  echo("          if (e.checked==true)\n");
  echo("          {\n");
  echo("            cont=true;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (cont==true)\n");
  echo("        {\n");
  /* Entra na pï¿½gina do tipo de botï¿½o clicado */
  echo("          if (action == ''){\n");
  echo("            document.gerenc.action='gerenciamento2.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."';\n");
  echo("          }else\n");
  echo("            document.gerenc.action='acoes.php';\n");
  echo("          document.gerenc.opcao.value=tipo;\n");
  echo("          document.gerenc.action_ger.value=action;\n");
  echo("          document.gerenc.submit();\n");
  echo("        }\n");
  echo("      }\n");

//   echo("      function ApagarUsuarios(){\n");
//   echo("        var j=0;\n");
//   echo("        var elementos = document.getElementsByName('cod_usu[]')\n");
//   echo("        elementosSelecionados = new Array();\n");
//   echo("          for(i=0 ; i < elementos.length; i++){\n");
//   echo("            if(elementos[i].checked){\n"); 
//   echo("              elementosSelecionados[j] = elementos[i].value\n");   
//   echo("              j++\n"); 
//   echo("            }\n"); 
//   echo("          }\n");
//   echo("        document.location='acoes.php?origem=gerenciamento.php&acao=".$acao."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&action_ger=ApagaUsuario&elementos='+elementosSelecionados+'';\n");
//   echo("      }\n");

  echo("      function SelecionouCheckbox()\n");
  echo("      {\n");
  echo("        var e;");
  echo("        for (var i=0;i<document.gerenc.elements.length;i++)\n");
  echo("        {\n");
  echo("          if (document.gerenc.elements[i].checked==true)\n");
  echo("          {\n");
  echo("            return true;\n");
  echo("          }\n");
  echo("        }\n");
  /* 114 - Nenhuma pessoa selecionada */
  echo("        alert('".RetornaFraseDaLista($lista_frases, 114)."');\n");
  echo("        return false;\n");
  echo("      }\n");
  
  /*funcão responsavel por apagar os registros da pagina atual
   * se flag==T apaga a pagina inteira
   * caso contrario apaga apenas o navegador da paginação
   * é necessario as vezes apagar so o navegador quando paginamos para frente ou para tras
  */
  
  echo("var qtdPag=1;\n");
  echo("var intervalo=1;\n");
  echo("var atual=1;\n");
  echo("var aux='T';\n");
  
  echo("function ApagaPagina(flag)\n");
  echo("{\n");
  echo("var	tr_ger=document.getElementsByName('germen');\n");
  echo("var tam=tr_ger.length;\n");
  echo("	naveg=document.getElementById('control');\n");
  echo("	naveg.parentNode.removeChild(naveg);\n");
  echo("if(flag=='T'){\n");
  echo("for(var i=1; i<tam; i++){\n");
  echo("	ger=document.getElementById('ger');\n");
  echo("	ger.parentNode.removeChild(ger);\n");
  echo("   }\n");
  echo(" }");
  echo("}\n");
  
  echo("function CriaElementoTab(nome,dtins,dados,cod,acao,port){\n");
  echo("	var tab=document.getElementsByClassName('tabInterna');\n");
  echo("	var td_check=document.createElement('td');\n");
  echo("	var check=document.createElement('input');\n");
  echo("	check.type=\"checkbox\";\n");
  echo("	check.setAttribute('name','cod_usu[]');\n");
  echo("	check.setAttribute('value',cod);\n");
  echo("	check.onclick=\"VerificaCheck();\"\n"); 
  echo("	td_check.appendChild(check);\n");
  echo("	var td_nome=document.createElement('td');\n");
  echo("	td_nome.align=\"left\";\n");
  echo("	td_nome.innerHTML=nome;\n");
  echo("	var td_data=document.createElement('td');\n");
  echo("	td_data.innerHTML=dtins;\n");
  echo("	var td_dados=document.createElement('td');\n");
  echo("		td_dados.innerHTML=\"<a href=gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;ordem=".$ordem."&amp;opcao=dados&amp;cod_usu[]=".$cod_usuario_l."\>".RetornaFraseDaLista($lista_frases,79)."</a>\";\n");
  echo("	var tr_ger=document.createElement('tr');\n");
  echo("	tr_ger.setAttribute('name','germen');\n"); 
  echo("	tr_ger.id=\"ger\";\n");
  echo("	tr_ger.appendChild(td_check);\n");
  echo("	tr_ger.appendChild(td_nome);\n");
  echo("	tr_ger.appendChild(td_data);\n");
  echo("	tr_ger.appendChild(td_dados);\n");
  
  /*caso esteja em inscrições registradas precisamos criar o campo portifolio*/
  
  echo("	if(acao== 'R'){\n");
  echo("		var td_port=document.createElement('td');\n");
  echo("		td_port.innerHTML=port;\n");
  echo("		td_port.id='status_port'+cod;\n");
  echo("		tr_ger.appendChild(td_port);\n");
  echo("	}\n");
  echo("	tab[0].appendChild(tr_ger);\n");
  echo("}\n");
  
   /*função java script que controla paginacao-estou criando a paginação nova...depois de mudar o intervalo*/
  echo("function Paginacao(status){\n");
  echo("	var tab=document.getElementsByClassName('tabInterna');\n");
  echo("	var td_pagina=document.createElement('td');\n");
  echo("	td_pagina.colSpan=\"5\";");
  echo("	td_pagina.align=\"right\";");
  
  echo("	var span_first=document.createElement('span');\n");
  echo("	span_first.innerHTML=\"<<&nbsp;\";\n");
  echo("	var span_ant=document.createElement('span');\n");
  echo("	span_ant.innerHTML=\"<&nbsp;\";\n");
  echo("	var span_last=document.createElement('span');\n");
  echo("	span_last.innerHTML=\"&nbsp;>>\";\n");
  echo("	var span_prox=document.createElement('span');\n");
  echo("	span_prox.innerHTML=\"&nbsp;>\";\n");
  
  /*verificando se a paginação para voltar esta liberada, ou seja se não é a primeira pagina*/
 
  echo("if(status== 'BV'){\n");
  echo("		span_first.className=\"none\";\n");
  echo("		span_first.onclick=\"none\";\n");
  echo("		span_ant.className=\"none\";\n");
  echo("		span_ant.onclick=\"none\";\n");
  echo("		span_last.className=\"paginacao\";\n");
  echo("		span_last.onclick=function(){xajax_MudaDinamic(intervalo,'L',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_prox.className=\"paginacao\";\n");
  echo("		span_prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		aux='BV';\n");
  echo("	}\n");
  
  echo("if(status== 'LV' || status== 'LF'){\n");
  echo("		span_first.className=\"paginacao\";\n");
  echo("		span_first.onclick=function(){xajax_MudaDinamic(intervalo,'PR',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_ant.className=\"paginacao\";\n");
  echo("		span_ant.onclick=function(){xajax_MudaDinamic(intervalo,'A',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_last.className=\"paginacao\";\n");
  echo("		span_last.onclick=function(){xajax_MudaDinamic(intervalo,'L',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_prox.className=\"paginacao\";\n");
  echo("		span_prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		aux='LF';\n");	
  echo("	}\n");
  
  echo("if(status== 'BF'){\n");
  echo("		span_first.className=\"paginacao\";\n");
  echo("		span_first.onclick=function(){xajax_MudaDinamic(intervalo,'PR',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_ant.className=\"paginacao\";\n");
  echo("		span_ant.onclick=function(){xajax_MudaDinamic(intervalo,'A',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
  echo("		span_last.className=\"none\";\n");
  echo("		span_last.onclick=\"none\";\n");
  echo("		span_prox.className=\"none\";\n");
  echo("		span_prox.onclick=\"none\";\n");
  echo("		aux='BF';\n");	
  echo("	}");
  
  echo("if(status== 'B'){\n");
  echo("  		span_first.className=\"none\";\n");
  echo("		span_first.onclick=\"none\";\n");
  echo("		span_ant.className=\"none\";\n");
  echo("		span_ant.onclick=\"none\";\n");
  echo("		span_last.className=\"none\";\n");
  echo("		span_last.onclick=\"none\";\n");
  echo("		span_prox.className=\"none\";\n");
  echo("		span_prox.onclick=\"none\";\n");
  echo("		aux='B';\n");	
  echo("	}");
    
  echo("	td_pagina.appendChild(span_first);\n");
  echo("	td_pagina.appendChild(span_ant);\n");
  
  /*criando os indices */
  
  echo("	for(var i=1;i<=qtdPag;i++){\n");
  echo("		var td_span=document.createElement('span');\n");
  echo("		ind=(parseInt(i))+(parseInt(intervalo))-1;\n");
  echo("		if (ind==atual){\n");
  echo("			td_span.className=\"none\";\n");
  echo("			td_span.onclick=\"none\";\n");
  echo("		}\n");
  echo("		else{\n");
  echo("			td_span.className=\"paginacao\";\n");
  echo("			td_span.onclick=function(){xajax_PaginacaoDinamic(aux,intervalo,this.id,".$cod_curso.",'".$tipo_usuario."','".$ordem."','".$acao."','".$ativado."','".$desativado."');};\n");
  echo("		}\n");
  echo("		td_span.id=ind;\n");
  echo("		td_span.innerHTML='[&nbsp;'+ind+'&nbsp;]';\n");
  echo("		td_pagina.appendChild(td_span);\n");
  echo("	}\n");
  
  /*verificando a paginação para frente*/
    
  echo("    td_pagina.appendChild(span_prox);\n");
  echo("	td_pagina.appendChild(span_last);\n");
  
  echo("	var tr_span=document.createElement('tr');\n");
  echo("	tr_span.setAttribute('name','germen');\n"); 
  echo("	tr_span.id=\"control\";\n");
  echo("	tr_span.appendChild(td_pagina);\n");
  echo("	tab[0].appendChild(tr_span);\n");
  
  echo("}\n");
    
  echo("function MudaIntervalo(aux){\n");
  echo("intervalo=aux;\n");
  echo("}\n");
  
  echo("function MudaAtual(aux){\n");
  echo("atual=aux;\n");
  echo("}\n");
  
  
     
  /*inicial a paginação dinamica, criando os controladores de paginação*/
  
  echo("function Inicial(limit,flag){\n");
  echo("if (limit>=1){\n");
  echo("	qtdPag=limit;\n");
  echo("	inicia=flag;\n");
  echo("	var tab=document.getElementsByClassName('tabInterna');\n");
  echo("	var coluna=document.createElement('td');\n");
  echo("	coluna.colSpan=\"5\";\n");
  echo("	coluna.align=\"right\";");
 
  /*
  * criando os span necessarios para a páginação, ir para o primeiro não é valido pois estamos na primeira
  * pagina.
  */
  
  echo("	var first=document.createElement('span');\n");
  echo("	first.innerHTML=\"<<&nbsp;\";\n");
  echo("	coluna.appendChild(first);\n");
  echo("	var ant=document.createElement('span');\n");
  echo("	ant.innerHTML=\"<&nbsp;\";\n");
  echo("	coluna.appendChild(ant);\n");
  echo("	var prox=document.createElement('span');\n");
  echo("	prox.innerHTML=\"&nbsp;>\";\n");
  echo("	var last=document.createElement('span');\n");
  echo("	last.innerHTML=\"&nbsp;>>\";\n");
  echo("	first.className=\"none\";\n");
  echo("	ant.className=\"none\";\n");
  echo("	first.onclick=\"none\";\n");
  echo("	ant.onclick=\"none\";\n");
  
//  /*verificando se ainda existirão mais paginações*/
   echo("if (flag=='L'){\n");
   echo("	prox.className=\"paginacao\";\n");
   echo("	prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
   echo("	last.className=\"paginacao\";\n");
   echo("	last.onclick=function(){xajax_MudaDinamic(intervalo,'L',".$cod_curso.",'".$tipo_usuario."','".$ordem."');};\n");
   echo("	aux='BV';\n");
   echo("}\n");
//  
   echo("else{\n");
   echo("	prox.className=\"none\";\n");
   echo("	last.className=\"none\";\n");
   echo("	prox.onclick=\"none\";\n");
   echo("	last.onclick=\"none\";\n");
   echo("	aux= 'B';\n");	
   echo("}\n");
//  
//  /*paginando os indices iniciais, até 5, ou até o fim das mensagens*/
//  
  echo("for(var i=1;i<=limit;i++){\n");
  echo("	var GerSpan=document.createElement('span');\n");
  echo("	GerSpan.id=i;\n");
  echo("	if (atual==i){\n");
  echo("		GerSpan.className=\"none\";\n");
  echo("		GerSpan.onclick=\"none\";");	
  echo("	}\n");
  echo("	else{\n");
  echo("		GerSpan.className=\"paginacao\";\n");
  echo("		GerSpan.onclick=function(){xajax_PaginacaoDinamic(aux,intervalo,this.id,".$cod_curso.",'".$tipo_usuario."','".$ordem."','".$acao."','".$ativado."','".$desativado."');};\n");
  echo("	}\n");
  echo("	GerSpan.innerHTML='[&nbsp;'+i+'&nbsp;]';\n");
  echo("	coluna.appendChild(GerSpan);\n");
  echo("}\n");
  echo("coluna.appendChild(prox);\n");
  echo("coluna.appendChild(last);\n");
  echo("var linha=document.createElement('tr');\n");
  echo("linha.setAttribute('name','germen');\n"); 
  echo("linha.id=\"control\";\n");
  echo("linha.appendChild(coluna);\n");
  echo("tab[0].appendChild(linha);\n");
  echo("};\n");
  echo("}\n");
 
  
   

//   echo("      function Apagar()\n");
//   echo("      {\n");
//   echo("        if(SelecionouCheckbox() && confirm('Tem certeza disso ?'))\n");
//   echo("          ApagarUsuarios();\n");
//   echo("      }\n");

  echo("    </script>\n\n");
  $objMaterial->printJavascript("../xajax_0.2.4/");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  
  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
  	/* 1 - Administracao  297 - Area restrita ao formador. */
  	echo("<h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");
	
    /*Voltar*/
    echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("<form><input class=\"input\" type=button value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    Desconectar($sock);
    exit();
  }
  
/*Forms*/
  echo("    <form action=\"acoes.php\" name=\"gerenc\" method=\"get\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("      <input type=\"hidden\" name=\"acao\" value=\"".$acao."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\">\n");
  echo("      <input type=\"hidden\" name=\"ordem\" value=\"".$ordem."\">\n");
  echo("      <input type=\"hidden\" name=\"opcao\" value=\"nenhuma\">\n");
  echo("      <input type=\"hidden\" name=\"action_ger\" value=\"nenhuma\">\n");
  echo("      <input type=\"hidden\" name=\"origem\" value=\"gerenciamento\">\n");

  // Pï¿½gina Principal
  /* 1 - Administraï¿½ï¿½o */


  echo($cabecalho."</h4>");
  
  

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  
  echo("          <table id=\"tabelaExterna\" cellpadding=\"0\" cellspacing=\"0\"  class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // 23 - Voltar (geral)
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  if ($acao=="N" || $acao=="C" || $acao=="R")
  {
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <ul class=\"btAuxTabs03\">\n");

    if ($acao=="N")
      $tipo_usuario="a";
    else if ($acao=="C")
      $tipo_usuario="A";
    else
      $tipo_usuario="r";

    /* 75 - Inscriï¿½ï¿½es Nï¿½o Avaliadas */
    echo("                  <li><a href=\"gerenciamento.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=N&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,75)."</a></li>\n");
    /* 76 - Inscriï¿½ï¿½es Aceitas */
    echo("                  <li><a href=\"gerenciamento.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=C&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,76)."</a></li>\n");
    /* 77 - Inscriï¿½ï¿½es Rejeitadas */
    echo("                  <li><a href=\"gerenciamento.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=R&amp;ordem=".$ordem."\">".RetornaFraseDaLista($lista_frases,77)."</a></li>\n");
 
    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  }
  
 
  
   
  $lista_usuarios = RetornaListaUsuariosDoGerenciamento($sock,$cod_curso,$tipo_usuario,$ordem);

  $num=count($lista_usuarios);



  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tbgeren\">\n");
  echo("                  <tr class=\"head01 alLeft\">\n");
  echo("                    <td colspan=\"".(($acao == "R") ? "3" : "2")."\">");
  echo("                      ".$frase_qtde." ".$num."\n");
  echo("                    </td>\n");
  echo("                    <td colspan=\"2\" align=right>");
  echo("                      ".RetornaFraseDaLista($lista_frases,146)." <select name=\"ordem\" onChange=\"document.location='gerenciamento.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;ordem='+this[this.selectedIndex].value;\" style=\"margin:5px 0 0 0;\">\n");
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
  // 132 - Data de inscriï¿½ï¿½o
  echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,132)."</b></td>\n");
  // 79 - Dados
  echo("                    <td align=\"center\" width=\"15%\"><b>".RetornaFraseDaLista($lista_frases,79)."</b></td>\n");
  // 211 - Portfolio
  if($acao == "R")
    echo("                    <td align=\"center\"><b>".RetornaFraseDaLista($lista_frases, 211)."</b></td>\n");
  echo("                  </tr>\n");

  /* Cï¿½digo de montagem do conteï¿½do a partir daqui */

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
  	$cont=10;
    foreach($lista_usuarios as $cod_usuario_l => $linha)
    { 
     if ($cont>=1){
          echo("                  <tr name=\"germen\" id=\"ger\" style=\"display: table-row;\">\n");
          echo("                    <td width=\"1%\"><input type=\"checkbox\" name=\"cod_usu[]\" onclick=\"VerificaCheck();\" value=".$cod_usuario_l."></td>\n");
          echo("                    <td align=\"left\">".$linha['nome']."</td>\n");
          echo("                    <td>".Unixtime2Data($linha['data_inscricao'])."</td>\n");
          /* 79 - Dados */
          echo("                    <td><a href=\"gerenciamento2.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;acao=".$acao."&amp;ordem=".$ordem."&amp;opcao=dados&amp;cod_usu[]=".$cod_usuario_l."\">".RetornaFraseDaLista($lista_frases,79)."</a></td>\n");
	  // Portfolio
	  if($acao == "R"){
	  	echo("                <td id=\"status_port".$cod_usuario_l."\">");
		if($linha['portfolio'] == "ativado")
			echo RetornaFraseDaLista($lista_frases, 208);// 208 - Ativado
		else
			echo RetornaFraseDaLista($lista_frases, 209);//209 - Desativado
	  	echo("                    </td>\n");
	  }
	echo("                  </tr>\n");
     }
    $cont--; 
    }
   echo("                </table>\n");
  }
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul>\n");
  /* 79 - Dados */ 
  echo("                  <li id=\"mDados_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,79)."</span id=\"dados_usu\"></li>\n");
  /* 1 - Apagar */
//   echo("                  <li id=\"mApagar_Selec\" class=\"menuUp\"><span id=\"apagar_usu\">".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
  if ($acao=="N" || $acao=="R")
  {
    /* 80 - Aceitar */
    echo("                  <li id=\"mAceitar_Selec\" class=\"menuUp\"><span id=\"aceitar\">".RetornaFraseDaLista($lista_frases,80)."</span></li>\n");
     
    // 187 - Aceitar como visitante
    echo("                  <li id=\"mAceitarVis_Selec\" class=\"menuUp\"><span id=\"aceitar_vis\">".RetornaFraseDaLista($lista_frases,187)."</span></li>\n");
      
    // Ativar / Desativar Portfolio
    if($acao == "R"){
      echo("                  <li id=\"mAtivarPort_Selec\" class=\"menuUp\"><span id=\"ativar_port\">".RetornaFraseDaLista($lista_frases,206)."</span></li>\n");
      
      echo("                  <li id=\"mDesativarPort_Selec\" class=\"menuUp\"><span id=\"desativar_port\">".RetornaFraseDaLista($lista_frases,207)."</span></li>\n");
       
    }    
  }

  if ($acao=="N"){
    /* 81 - Rejeitar */
    echo("                  <li id=\"mRejeitar_Selec\" class=\"menuUp\"><span id=\"rejeitar\">".RetornaFraseDaLista($lista_frases,81)."</span></li>\n");
  }

  if ($acao=="C"){
    // 81 - Rejeitar
    echo("                  <li id=\"mRejeitar2_Selec\" class=\"menuUp\"><span id=\"rejeitar\">".RetornaFraseDaLista($lista_frases,81)."</span></li>\n");
  }

  if ($ecoordenador)
  {
    if ($acao == 'F')
      // 107 - Transformar em Aluno
      echo("                  <li id=\"mAluno_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,107)."</span></li>\n");
        
    if ($acao == 'A')
      // 108 - Transformar em Formador
      echo("                  <li id=\"mFormador_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,108)."</span></li>\n");
        
    if ($acao == 'A' || $acao == 'F')
      // 176 - Transformar em convidado
      echo("                  <li id=\"mConvidado_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,176)."</span></li>\n");

    if ($acao == 'A')
      // 288 - Desligar Aluno
      echo("                  <li id=\"mDesligar_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,288)."</span></li>\n");

    if ($acao == 'T')
      // 107 - Transformar em Aluno
      echo("                  <li id=\"mFormador_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,107)."</span></li>\n");

    if ($acao == 'F')
      // 280 - Transformar em Coordenador
      echo("                  <li id=\"mCoordenador_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,280)."</span></li>\n");

    if ($acao == 'F')
      // 199 - Desligar Formador
      echo("                  <li id=\"mDesligar_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,199)."</span></li>\n");
   if ($acao == 'G')
      // 260 - Religar Formador
      echo("                  <li id=\"Religar\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,260)."</span></li>\n");
   if ($acao == 'AG')
      // 285 - Religar Aluno
      echo("                  <li id=\"Religar\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases,285)."</span></li>\n");
         
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
