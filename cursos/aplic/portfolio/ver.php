<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/ver.php

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
  ARQUIVO : cursos/aplic/portfolio/ver.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamentoEAtualiza");
  $objAjax->register(XAJAX_FUNCTION,"EditarTitulo");
  $objAjax->register(XAJAX_FUNCTION,"EditarTexto");
  $objAjax->register(XAJAX_FUNCTION,"InsereEnderecoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AbreEdicao");
  $objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CancelaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirArquivo");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirEndereco");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"OcultarArquivosDinamic");
  $objAjax->register(XAJAX_FUNCTION,"DesocultarArquivosDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MoverArquivosDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MoverItensDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseGeralDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AssociaAvaliacaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"VerificaSePodeDesassociar");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 15;
  $cod_ferramenta_ajuda = 15;
  $cod_pagina_ajuda = 3;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject = new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("criarItem", 190, 0);
  $feedbackObject->addAction("mover", 191, 0);
  $feedbackObject->addAction("descompactar", 192, 193);
  $feedbackObject->addAction("anexar", 62, sprintf(RetornaFraseDaLista($lista_frases, 189), ((int) ini_get('upload_max_filesize'))));
  $feedbackObject->addAction("moverarquivos", 201, 0);

  $eformador = EFormador($sock, $cod_curso, $cod_usuario);

  $status_portfolio = RetornaStatusPortfolio($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio = $status_portfolio['dono_portfolio'];
  $portfolio_apagado = $status_portfolio['portfolio_apagado'];
  $portfolio_grupo = $status_portfolio['portfolio_grupo'];

  // Descobre os diretorios de arquivo, para os portfolios com anexo
  $sock2 = Conectar("");
  $diretorio_arquivos = RetornaDiretorio($sock, 'Arquivos');
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');
  Desconectar($sock2);

  $sock = Conectar($cod_curso);

  $dir_item_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  $linha_item = RetornaDadosDoItem($sock, $cod_item);

  $lista_arq = RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['link']);

  $pode_editar = PodeAlterarPortfolio($cod_curso, $cod_usuario, $cod_item);

  $tem_avaliacao = TemAvaliacao($cod_curso, $cod_item);

  if ((count($lista_arq))>0){
      $i=0;
      foreach($lista_arq as $cod=>$linha2){
        if (is_dir($linha2['Caminho'])){
          $lista_diretorios[$i]['Diretorio'] = $linha2['Diretorio'];
          $lista_diretorios[$i]['Caminho'] = $linha2['Caminho'];
          $i++;
        }
      }
    }

  echo ("    <script type=\"text/javascript\">");
  echo ("      function WindowOpenVer(id)\n");
  echo ("      {\n");
  echo ("         window.open(\"" . $dir_item_temp['link'] . "\"+id,'Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo ("      }\n\n");

  echo ("      function WindowOpenAvalia(id)\n");
  echo ("      {\n");
  echo ("         window.open('../avaliacoes/ver_popup.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_avaliacao='+id,'VerAvaliacao','width=620,height=450,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo ("        return(false);\n");
  echo ("      }\n");
  echo ("</script>");

  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\">\n");

  /* (ger) 26 - Anexar Arquivos */
  /* 59 - Pressione o botão Browse (ou Procurar) abaixo para selecionar o arquivo a ser anexado; em seguida, pressione OK para prosseguir. */
  /* 60 - (arquivos .ZIP podem ser enviados e descompactados posteriormente) */
  /* (ger) 63 - Houve um erro ao atualizar o material. */
  /* (ger) 18 - Ok  */
  /* (ger) 2 - Cancelar  */
  /* 65 - Digite abaixo o título e o endereço da internet a ser incluido. */
  /* 18 - Você tem certeza de que deseja apagar este item? */
  /* 41 - Título */
  /* 66 - Endereço */
  /* 64 - Pelo menos o endereço deve ser preenchido! */
  /* 45 - Incluir Endereço */
  /* 30 - Tem certeza que deseja apagar este arquivo? */
  /* 31 - Tem certeza que deseja apagar este diret?io? (todos os arquivos dele ser? apagados) */
  /* 32 - Tem certeza que deseja apagar este endere?? */
  /* 33 - Você tem certeza de que deseja descompactar este arquivo? */
  /* 34 - (o arquivo ZIP será apagado) */
  /* 35 - importante: não é possível a descompactação de arquivos contendo pastas com espaços no nome. */
  /* 36 - O titulo nao pode ser vazio. */
  /* 118 - Oculto */
  /* 153 - Para que este item possa ser avaliado, associe-o a atividade a qual ele pertence. */
  /* 179 - (Os itens serão movidos para a lixeira) */

  echo ("      var cod_item='" . $cod_item . "';\n");
  echo ("      var cod_topico='" . $cod_topico . "';\n");
  echo ("      var cod_curso='" . $cod_curso . "';\n");
  echo ("      var cod_usuario='" . $cod_usuario . "';\n");
  echo ("      var cod_topico_ant='" . $cod_topico_raiz . "';\n");
  echo ("      var cod_topico_raiz='" . $cod_topico_raiz . "';\n");
  echo ("      var cod_usuario_portfolio='" . $cod_usuario_portfolio . "';\n");
  echo ("      var cod_grupo_portfolio='" . $cod_grupo_portfolio . "';\n");
  /* (ger) 18 - Ok */
  // Texto do bot�o Ok do ckEditor
  echo("    var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do bot�o Cancelar do ckEditor
  echo("    var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  /*
   * Funcao que atualiza as listas de avitidades a serem associadas ao Portolio.
   * Varre os radioboxes e muda a visibilidade do radio que foi marcada para ser associado.
   */
  echo ("function AtualizaAssociacaoForm(cod_avaliacao){\n");
  echo ("  var radioboxes = document.getElementsByName(\"cod_avaliacao\");\n");
  echo ("  for(var i = 0; i < radioboxes.length; i++) {\n");
  echo ("    if(radioboxes[i].value==cod_avaliacao) {\n");
  echo ("      radioboxes[i].style.visibility = 'hidden';\n");
  echo ("      VisibilidadeSpanAssociado(i, true);\n");
  echo ("    }\n");
  echo ("  }\n");
  echo ("}\n");

  echo ("function VisibilidadeSpanAssociado(j, b){\n");
  echo ("	spans = document.getElementsByName(\"associado\");\n");
  echo ("	var i = 0;\n");
  echo ("	(b) ? mostra = 'visible': mostra = 'hidden';\n");
  echo ("	for(i = 0; i < spans.length; i++){\n");
  echo ("		if (i == j){\n");
  echo ("			spans[i].style.visibility = mostra;\n");
  echo ("		}\n");
  echo ("	}\n");
  echo ("}\n");

  /*
   * Funcao que atualiza as listas de atividades a serem associadas ao Portfolio.
   * Varre os radioboxes e muda a visibilidade da avaliacao que esta associadad ao item.
   */
  echo ("function AtualizaDesassociacaoForm(cod_avaliacao){\n");
  echo ("  var radioboxes = document.getElementsByName(\"cod_avaliacao\");\n");
  echo ("  for(var i = 0; i < radioboxes.length; i++){\n");
  echo ("    if (radioboxes[i].value == cod_avaliacao){\n");
  echo ("      radioboxes[i].style.visibility = 'visible';\n");
  echo ("      VisibilidadeSpanAssociado(i, false);\n");
  echo ("    }\n");
  echo ("  }\n");
  echo ("}\n");

  echo ("function FechaDivAvaliacoes(){\n");
  echo ("	document.getElementById('divAvaliacaoEdit').className = \"divHidden\";\n");
  echo ("}\n");

  echo ("      function Iniciar(){\n");
  echo ("        cod_comp = getLayer(\"comp\");\n");
  echo ("        cod_topicos = getLayer(\"topicos\");\n");
  echo ("        cod_mover = getLayer(\"mover\");\n");
  echo ("        cod_novapasta = getLayer(\"novapasta\");\n");
  echo ("        cod_mover_arquivo = getLayer(\"mover_arquivo\");\n");
  echo ("        EscondeLayers();\n");
  echo ("        xajax_RetornaFraseDinamic('lista_frases');");
  echo ("        xajax_RetornaFraseGeralDinamic('lista_frases_geral');");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo ("        startList();\n");
  echo ("      }\n");

  echo ("function WindowOpenVerAvaliacao(cod_avaliacao)\n");
  echo ("{\n");
  echo ("  window_handle = window.open('../avaliacoes/ver_popup.php?" . RetornaSessionID() . "&cod_curso=" . $cod_curso . "&VeioDaAtividade=1&cod_avaliacao=' + cod_avaliacao,'VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes')\n");
  echo ("  window_handle.focus();\n");
  echo ("}\n");

  echo ("     function EditaTituloEnter(campo, evento, id)\n");
  echo ("     {\n");
  echo ("         var tecla;\n");
  echo ("         CheckTAB=true;\n\n");
  echo ("         if(navigator.userAgent.indexOf(\"MSIE\")== -1)\n");
  echo ("         {\n");
  echo ("             tecla = evento.which;\n");
  echo ("         }\n");
  echo ("         else\n");
  echo ("         {\n");
  echo ("             tecla = evento.keyCode;\n");
  echo ("         }\n\n");
  echo ("         if ( tecla == 13 )\n");
  echo ("         {\n");
  echo ("             EdicaoTitulo(id, 'tit_'+id, 'ok');\n");
  echo ("         }\n\n");
  echo ("         return true;\n");
  echo ("     }\n\n");

  echo("      function AlteraTitulo(id){\n");
  echo("        var id_aux = id;\n");
  echo("        if (editaTitulo==0){\n");
  echo("          CancelaTodos();\n");

  echo("          xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");

  echo("          conteudo = document.getElementById('tit_'+id).innerHTML;\n");
  echo("          document.getElementById('tr_'+id).className='';\n");
  echo("          document.getElementById('tit_'+id).className='';\n");

  echo("          createInput = document.createElement('input');\n");
  echo("          document.getElementById('tit_'+id).innerHTML='';\n");
  //echo("          document.getElementById('renomear_'+id).onclick=function(){ };\n\n");
  //echo("          document.getElementById('renomear_'+id).setAttribute('onclick', '');\n");

  echo("          createInput.setAttribute('type', 'text');\n");
  echo("          createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
  echo("          createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");
  echo("          if (createInput.addEventListener){\n"); //not IE
  echo("            createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
  echo("          } else if (createInput.attachEvent){\n"); //IE
  echo("            createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
  echo("          }\n");

  echo("          document.getElementById('tit_'+id).appendChild(createInput);\n");
  echo("          xajax_DecodificaString('tit_'+id+'_text', conteudo, 'value');\n\n");

  echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("          espaco = document.createElement('span');\n");
  echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
  echo("          document.getElementById('tit_'+id).appendChild(espaco);\n");

  echo("          createSpan = document.createElement('span');\n");
  echo("          createSpan.className='link';\n");
  echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };\n");
  echo("          createSpan.setAttribute('id', 'OkEdita');\n");
  echo("          createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,18)."';\n");
  echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

  echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("          espaco = document.createElement('span');\n");
  echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
  echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");

  echo("          createSpan = document.createElement('span');\n");
  echo("          createSpan.className='link';\n");
  echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };\n");
  echo("          createSpan.setAttribute('id', 'CancelaEdita');\n");
  echo("          createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,2)."';\n");
  echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

  echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("          espaco = document.createElement('span');\n");
  echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
  echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");

  echo("          startList();\n");
  echo("          cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("          document.getElementById('tit_'+id+'_text').select();\n");
  echo("          editaTitulo++;\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function VerificaChkBox(alpha){\n");
  echo("         CancelaTodos();\n");
  echo("        checks = document.getElementsByName('chkArq');\n");
  echo("        var i, j=0;\n");
  echo("        var arqComum=0;\n");
  echo("        var arqZip=0;\n");
  echo("        var arqOculto=0;\n");
  echo("        var pasta=0;\n\n");
  echo("        var listaDir = '".$lista_diretorios."';\n");
  echo("        var haDiretorios = listaDir.length;\n");

  echo("        for (i=0; i<checks.length; i++){\n");
  echo("          if(checks[i].checked){\n");
  echo("            j++;\n");
  echo("            getNumber=checks[i].id.split(\"_\");\n");
  echo("            tipo = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('tipoArq');\n");
  echo("            switch (tipo){\n");
  echo("              case ('pasta'): pasta=1;break;\n");
  echo("              case ('comum'): arqComum++;break;\n");
  echo("              case ('zip'): arqZip++;break;\n");
  echo("            }\n\n");

  echo("            if (document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('arqOculto')=='sim'){\n");
  echo("               arqOculto++;\n");
  echo("            }\n\n");

  echo("          }\n");
  echo("        }\n");

  echo("        if (pasta==1){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_mover').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n");

  echo("          document.getElementById('mArq_apagar').onclick= function(){ Apagar(); };\n");
  echo("          document.getElementById('mArq_ocultar').onclick= function(){  };\n");
  echo("          document.getElementById('mArq_mover').onclick= function(){  };\n");
  echo("          document.getElementById('mArq_descomp').onclick= function(){  };\n\n");

  echo("        }else if((arqComum==1)||(arqZip>1)){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
  echo("          if (haDiretorios>0){\n");
  echo("            document.getElementById('mArq_mover').className=\"menuUp02\";\n");
  echo("          }\n");
  echo("          else{\n");
  echo("            document.getElementById('mArq_mover').className=\"menuUp\";\n");
  echo("        }\n");
  echo("        document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

  echo("        document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
  echo("        document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
  echo("        if (haDiretorios>0){\n");
  echo("          document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };\n");
  echo("        }\n");
  echo("        else{\n");
  echo("          document.getElementById('sArq_mover').onclick= function(){  };\n");
  echo("        }\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n\n");
  echo("        }else if(arqComum>1){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_mover').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");
  echo("          document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
  echo("          document.getElementById('sArq_mover').onclick= function(){  };\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n\n");
  echo("        }else if(arqZip==1){\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
  echo("          if (haDiretorios>0){\n");
  echo("            document.getElementById('mArq_mover').className=\"menuUp02\";\n");
  echo("          }\n");
  echo("          else{\n");
  echo("            document.getElementById('mArq_mover').className=\"menuUp\";\n");
  echo("          }\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp02\";\n\n");

  echo("          document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
  echo("          if (haDiretorios>0){\n");
  echo("            document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };\n");
  echo("          }\n");
  echo("          else{\n");
  echo("            document.getElementById('sArq_mover').onclick= function(){  };\n");
  echo("          }\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){ Descompactar() };\n");
  echo("        }else{\n");
  echo("          document.getElementById('mArq_apagar').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_mover').className=\"menuUp\";\n");
  echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

  echo("          document.getElementById('sArq_apagar').onclick= function(){  };\n");
  echo("          document.getElementById('sArq_ocultar').onclick= function(){  };\n");
  echo("          document.getElementById('sArq_mover').onclick= function(){  };\n");
  echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n");
  echo("        }\n\n");

  echo("        //todos arquivos selecionados sao ocultos\n");
  echo("        if ((j==arqOculto)&&(j!=0)) {\n");
  echo("            document.getElementById('sArq_ocultar').onclick= function(){ Desocultar(); };\n");
  echo("        }\n");

  echo("        //Nao foi chamado pela funcao CheckTodos\n");
  echo("        if (alpha){\n");
  echo("          if (j==checks.length){ document.getElementById('checkMenu').checked=true; }\n");
  echo("          else document.getElementById('checkMenu').checked=false;\n");
  echo("        }\n");
  echo("      }\n\n");
  echo ("    </script>\n");

  $objAjax->printJavascript();
  
  echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");

  include ("../menu_principal.php");

  echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* Verificação se o item está em Edição */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
  $linha = RetornaUltimaPosicaoHistorico($sock, $cod_item);

  if ($linha['acao'] == "E") {
  	if (($linha['data'] < (time() - 1800)) || ($cod_usuario == $linha['cod_usuario'])) {
  		AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 0);
  	} else {
  		/* Está em edição... */
  		echo ("          <script language=\"javascript\">\n");
  		echo ("            window.open('em_edicao.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $cod_item . "&amp;origem=ver&amp;cod_topico_raiz=" . $cod_topico_raiz . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  		echo ("            window.location='portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $linha_item['cod_item'] . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "&amp;cod_topico_raiz=" . $cod_topico_raiz . "';\n");
  		echo ("          </script>\n");
  		echo ("        </td>\n");
  		echo ("      </tr>\n");
  		echo ("    </table>\n");
  		include ("layer.php");
  		echo ("  </body>\n");
  		echo ("</html>\n");
  		exit ();
  	}
  }

  /* Página Principal */
  $ferramenta_avaliacao = TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, COD_AVALIACAO);

  if ($ferramenta_avaliacao) {
  	if ($portfolio_grupo) {
  		// ajuda para portfolio de grupos, ferramenta avaliacao ativada
  		$cod_pagina = 24;
  	} else {
  		// ajuda para portfolio individual, ferramenta avaliacao ativada
  		$cod_pagina = 20;
  	}
  } else {
  	if ($portfolio_grupo) {
  		// ajuda para portfolio de grupos, sem ferramenta avaliacao
  		$cod_pagina = 11;
  	} else {
  		// ajuda para portolio individual, sem ferramenta avaliacao
  		$cod_pagina = 5;
  	}
  }

  if ($ferramenta_avaliacao) {

  	if ($ferramenta_grupos_s) {
  		//acao_portfolio_s pode ser G (grupo), F (encerrados), M (pessoal)

  		// 3 - Portfolios de grupos
  		$cod_frase = 3;

  		//meu portfolio individual

  		if (($cod_grupo_portfolio == '') && (!$cod_grupo_portfolio)) {
  			$cod_frase = 2;
  		}
  	} else {
  		// 2 - Portfolios individual
  		$cod_frase = 2;
  	}
  } else {
  	if ($ferramenta_grupos_s) {
  		//acao_portfolio_s pode ser G (grupo), F (encerrados), M (pessoal)

  		// 3 - Portfolios de grupos
  		$cod_frase = 3;

  		//meu portfolio individual
  		if (($cod_grupo_portfolio == '') && (!$cod_grupo_portfolio)) {
  			$cod_frase = 2;
  		}
  	} else {
  		// 2 - Portfolios individual
  		$cod_frase = 2;
  	}
  }

  echo ("          <h4>" . RetornaFraseDaLista($lista_frases, 1) . " - " . RetornaFraseDaLista($lista_frases, $cod_frase) . "</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo ("<div id=\"mudarFonte\">\n");
  echo ("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo ("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo ("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo ("          </div>\n");

     /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  $lista_topicos_ancestrais = RetornaTopicosAncestrais($sock, $cod_topico_raiz);
  unset ($path);

  foreach ($lista_topicos_ancestrais as $cod => $linha) {
  	if ($cod_topico_raiz != $linha['cod_topico']) {
  		$path = "<a class=\"text\" href=\"portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_topico_raiz=" . $linha['cod_topico'] . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . $linha['topico'] . "</a> &gt;&gt; " . $path;
  	} else {
  		$path = "<a class=\"text\" href=\"portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_topico_raiz=" . $linha['cod_topico'] . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . $linha['topico'] . "</a>";
  	}
  }

  if ($portfolio_grupo) {
  	$nome = NomeGrupo($sock, $cod_grupo_portfolio);

  	//Figura de Grupo
  	$fig_portfolio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";

  	/* 84 - Grupo Excluído */
  	if ($grupo_apagado && $eformador)
  		$complemento = " <span>(" . RetornaFraseDaLista($lista_frases, 84) . ")</span>\n";

  	echo ("          " . $fig_portfolio . " <span class=\"link\" onclick=\"AbreJanelaComponentes(" . $cod_grupo_portfolio . ");\">" . $nome . "</span>" . $complemento . " - ");
  	echo ("          <a href=\"#\" onmousedown=\"js_cod_item='" . $cod_item . "'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"../imgs/estrutura.gif\" border=0 /></a>");
  } else {
  	$nome = NomeUsuario($sock, $cod_usuario_portfolio, $cod_curso);

  	// Figura de Perfil
  	$fig_portfolio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";

  	echo ("          " . $fig_portfolio . " <span class=\"link\" onclick=\"OpenWindowPerfil(" . $cod_usuario_portfolio . ");\" > " . $nome . "</span>" . $complemento . " - ");
  	echo ("<a href=\"#\" onmousedown=\"js_cod_item='" . $cod_item . "'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"../imgs/estrutura.gif\" border=\"0\" /></a>");

  }

  echo ($path);

  //   $EhAvaliacao=RetornaAssociacaoItemAvaliacao($sock,$cod_item);
  $EhAvaliacao = false;

  echo ("          <span id=\"associadoItem\">\n");
  if (($EhAvaliacao != false) && (count($EhAvaliacao) > 0)) {
  	$dados = RetornaDadosAvaliacao($sock, $EhAvaliacao['cod_avaliacao']);
  	$atividade = RetornaTituloAtividade($sock, $dados['cod_atividade']);
  	/* 149 - Item associado a atividade: */
  	echo ("            <br /><br />" . RetornaFraseDaLista($lista_frases, 149) . " <a class=\"text\" href=\"#\" onclick=\"window.open('../avaliacoes/ver_popup.php?&amp;cod_curso=" . $cod_curso . "&amp;cod_avaliacao=" . $EhAvaliacao['cod_avaliacao'] . "','VerAvaliacao','width=450,height=450,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');EscondeLayers();return(false);\">" . $atividade . "</a><br />");

  	$cod_avaliacao = $EhAvaliacao['cod_avaliacao'];

  }
  echo ("          </span>\n");

  echo ("          <!-- Tabelao -->\n");
  echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo ("            <tr>\n");
  echo ("              <!-- Botoes de Acao -->\n");
  echo ("              <td valign=\"top\">\n");
  echo ("                <ul class=\"btAuxTabs\">\n");

  //174 - Meus portfolios 
  echo ("                  <li><a href=\"ver_portfolio.php?cod_curso=" . $cod_curso . "&amp;exibir=myp\">" . RetornaFraseDaLista($lista_frases, 174) . "</a></li>\n");
  // 74 - Portfolios Individuais
  echo ("                  <li><a href=\"ver_portfolio.php?cod_curso=" . $cod_curso . "&amp;exibir=ind\">" . RetornaFraseDaLista($lista_frases, 74) . "</a></li>\n");
  // 75 - Portfolios de Grupos
  if ($ferramenta_grupos_s) {
  	echo ("                  <li><a href=\"ver_portfolio.php?cod_curso=" . $cod_curso . "&amp;exibir=grp\">" . RetornaFraseDaLista($lista_frases, 75) . "</a></li>\n");
  	// 177 - Portfolios encerrados
  	echo ("                  <li><a href=\"ver_portfolio.php?cod_curso=" . $cod_curso . "&amp;exibir=enc\">" . RetornaFraseDaLista($lista_frases, 177) . "</a></li>\n");
  }

  echo ("                </ul>\n");
  echo ("              </td>\n");
  echo ("            </tr>\n");
  echo ("            <tr>\n");
  echo ("              <td>\n");
  echo ("                <ul class=\"btAuxTabs03\">\n");

  $cod_topico_raiz_usuario = RetornaPastaRaizUsuario($sock, $cod_usuario, "");

  unset ($array_params);
  $array_params['cod_topico_raiz'] = $cod_topico_raiz;
  $array_params['cod_item'] = $cod_item;
  $array_params['cod_usuario_portfolio'] = $cod_usuario_portfolio;
  $array_params['cod_grupo_portfolio'] = $cod_grupo_portfolio;

  /* 70 - Ver Outros Itens */
  echo ("                  <li><a href=\"portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_topico_raiz=" . $cod_topico_raiz . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . RetornaFraseDaLista($lista_frases, 70) . "</a></li>\n");

  //72 - Historico
  echo ("                  <li><span onclick=\"window.open('historico.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $cod_item . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "','Historico','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false)\">" . RetornaFraseDaLista($lista_frases, 72) . "</span></li>\n");

  /* 112 - Comentários */
  echo ("                  <li><a href=\"comentarios.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $cod_item . "&amp;cod_topico_raiz=" . $cod_topico_raiz . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_usuario=" . $cod_usuario . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . RetornaFraseDaLista($lista_frases, 112) . "</a></li>\n");
  if ($dono_portfolio) {
  	/*Frase #25: Mover*/
  	echo("					<li><span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . RetornaFraseDaLista($lista_frases_geral, 25) . "</span></li>\n");
  	/*Frase #1: Apagar*/
  	echo("                  <li><span onclick=\"CancelaTodos();ApagarItem();\">" . RetornaFraseDaLista($lista_frases_geral, 1) . "</span></li>\n");
  }
  echo ("                </ul>\n");
  echo ("              </td>\n");
  echo ("            </tr>\n");
  echo ("            <tr>\n");
  echo ("              <td>\n");
  echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo ("                  <tr class=\"head\">\n");
  /* 41 - Título */
  echo ("                    <td>" . RetornaFraseDaLista($lista_frases, 41) . "</td>\n");

  if ($dono_portfolio) {
  	// 70 (ger) - Opções
  	echo ("                    <td width=\"16%\" align=\"center\">" . RetornaFraseDaLista($lista_frases_geral, 70) . "</td>\n");
  }

  /* 119 - Compartilhar */
  echo ("                    <td width=\"10%\" align=\"center\">" . RetornaFraseDaLista($lista_frases, 119) . "</td>\n");

  // se a ferramenta Avaliacoes estiver ativada, a tabela com os itens e pastas do portfolio tem 6 colunas, senao sao 5
  if ($ferramenta_avaliacao) {
  	/* 139 - Avaliação */
  	echo ("                    <td width=\"12%\" align=\"center\">" . RetornaFraseDaLista($lista_frases, 139) . "</td>\n");
  }

  echo ("                  </tr>\n");

  //$linha_item = RetornaDadosDoItem($sock, $cod_item);

  $titulo = $linha_item['titulo'];

  $texto = "<span id=\"text_" . $linha_item['cod_item'] . "\">" . AjustaParagrafo($linha_item['texto']) . "</span>";

  /* 209 - Renomear */
  $renomear = RetornaFraseDaLista($lista_frases, 209);

  /* 184 - Editar texto */
  $editar = RetornaFraseDaLista($lista_frases, 184);
  /* 187 - Limpar texto */
  $limpar = RetornaFraseDaLista($lista_frases, 187);

  /* (ger) 25 - Mover */
  $mover = RetornaFraseDaLista($lista_frases_geral, 25);

  /* 12 - Totalmente Compartilhado */
  if ($linha_item['tipo_compartilhamento'] == "T") {
  	$compartilhamento = RetornaFraseDaLista($lista_frases, 12);
  }
  /* 13 - Compartilhado com Formadores */
  else
  	if ($linha_item['tipo_compartilhamento'] == "F") {
  		$compartilhamento = RetornaFraseDaLista($lista_frases, 13);
  	}
  /* 14 - Compartilhado com o Grupo */
  else
  	if (($portfolio_grupo) && ($linha_item['tipo_compartilhamento'] == "P")) {
  		$compartilhamento = RetornaFraseDaLista($lista_frases, 14);
  	}
  /* 15 - Não compartilhado */
  else
  	if (!$portfolio_grupo) {
  		$compartilhamento = RetornaFraseDaLista($lista_frases, 15);
  	}

  // Marca se a linha contém um item 'novo'
  if ($data_acesso < $linha_item['data'])
  	$marcatr = " class=\"novoitem\"";
  else
  	$marcatr = "";

  // se a ferramenta Avaliacoes estiver ativa, descobrimos quais avaliacoes estao presas a cada item
  if ($ferramenta_avaliacao)
  	$lista = RetornaAssociacaoItemAvaliacao($sock, $linha_item['cod_item']);
  // senao, passamos uma variavel fake para enganar o codigo abaixo
  else
  	$lista = NULL;

  if ($linha_item['status'] == "E") {

  	$linha_historico = RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);

  	if ($linha_item['inicio_edicao'] < (time() - 1800) || $cod_usuario == $linha_historico['cod_usuario']) {
  		CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
  		if ($dono_portfolio) {
  			//se existe uma avalia?o ligada ao item
  			if (is_array($lista)) {
  				//$foiavaliado = ItemFoiAvaliado($sock, $lista['cod_avaliacao'], $linha_item['cod_item']);
  				$foiavaliado=FoiAvaliado($sock,$lista['cod_avaliacao'],$linha['cod_usuario']);
  				//talvez arrumar a funcao ItemFoiAvaliado, pois da forma que ta se o item tiver sido avaliado, mas tiver compartilhado so com
  				//formadores, o aluno nao sabe que foi avaliado, mas nao consegue editar o item, o que fazer?

  				// se foi avaliado não pode editar o material
  				if (!$foiavaliado) { //arrumar - não pode mais editar
  					$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";
  					$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
  					$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
  					$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
  					$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
  					//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
  				}
  			}
  			//else = não existe uma avaliação
  			else {
  				$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";
  				$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
  				$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
  				$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
  				$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
  				//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
  			}
  		}
  	}
  }
  //else = item não está sendo editado
  //   else if (!(($ferramenta_avaliacao && is_array($lista) && ItemEmAvaliacao($sock,$lista['cod_avaliacao'],$cod_usuario_portfolio) && $dono_portfolio)))
  else
  	if (!(($ferramenta_avaliacao && is_array($lista) && $dono_portfolio))) {
  		if ($linha_item['status'] != "C") {
  			if ($dono_portfolio) {
  				if (is_array($lista)) {
  					//$foiavaliado = ItemFoiAvaliado($sock, $lista['cod_avaliacao'], $linha_item['cod_item']);
  					$foiavaliado=FoiAvaliado($sock,$lista['cod_avaliacao'],$linha['cod_usuario']);
  				  if ($foiavaliado) { //arrumar - não pode mais editar
  						$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";
  						$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
  						$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
  						$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
  						$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
  						//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
  					}
  				} else {
  					$titulo = "<span style=\"border:1pt;\" id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";

  					$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
  					$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
  					$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
  					$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
  					//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
  				}
  			}
  		}
  	} else {
  		$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";
  		$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
  		$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
  		$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
  		$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
  		//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
  	}

  echo ("                  <tr id='tr_" . $linha_item['cod_item'] . "'>\n");
  echo ("                    <td class=\"itens\">" . $titulo . "</td>\n");

  if ($dono_portfolio) {
  	echo ("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
  	echo ("                      <ul>\n");
  	if ($renomear != null) {
  		echo ("                        <li>" . $renomear . "</li>\n");
  		echo ("                        <li>" . $editar . "</li>\n");
  		echo ("                        <li>" . $limpar . "</li>\n");
  		//echo ("                        <li>" . $mover . "</li>\n");
  		// G 1 - Apagar
  		//echo ("                        <li><span onclick=\"CancelaTodos();ApagarItem();\">" . RetornaFraseDaLista($lista_frases_geral, 1) . "</span></li>\n");
  	}
  	echo ("                      </ul>\n");
  	echo ("                    </td>\n");
  }

  if (!($dono_portfolio)){
    echo(" <td align=\"center\">".$compartilhamento."</td>\n");
  }
  else{
    echo ("                    <td align=\"center\">" . $compartilhamentospan . "</td>\n");
  }

    $tituloAvaliacao = RetornaTituloAvaliacaoDoItem($sock, $linha_item['cod_item']);

    if($tituloAvaliacao!= ""){
      $tituloavalia = "<span id=\"estadoAvaliacao\" class=\"link\" onclick=\"WindowOpenAvalia(".$lista['cod_avaliacao']."); return false;\" >" . $tituloAvaliacao . "</span>";
    }
    else{
      //Frase Ger #36 - N�o
      $tituloavalia = "<span id=\"estadoAvaliacao\">" . RetornaFraseDaLista($lista_frases_geral, 36) . "</span>";
    }

    if ($ferramenta_avaliacao) {
      echo ("                    <td align=\"center\"><span>");
  	  if (is_array($lista)) {
  		 //$foiavaliado = ItemFoiAvaliado($sock, $lista['cod_avaliacao'], $linha_item['cod_item']);
  		 $foiavaliado=FoiAvaliado($sock,$lista['cod_avaliacao'],$linha['cod_usuario']);
  		if ($foiavaliado) {
  			if ($eformador) {
  				echo ($tituloavalia . "</span><span class=\"avaliado\"> (a)\n");
  			}
  			//else = não é formador
  			else {
  				$compartilhado = NotaCompartilhadaAluno($sock, $linha_item['cod_item'], $lista['cod_avaliacao'], $cod_grupo_portfolio, $cod_usuario);
  				if ($compartilhado) {
  					echo ($tituloavalia . "</span><span class=\"avaliado\"> (a)\n");
  				}
  				//else = não é compartilhado
  				else {
  					echo ($tituloavalia);
  				}
  			}
  		} 
  		else {
  			echo ($tituloavalia);
  		}
  	}
  	//else = não tem avaliação
  	else {
  		// G 36 - Não
  		echo ($tituloavalia);
  	}
  	echo ("                    </span>");
  	echo ("                    </td>");
  }
  echo ("                  </tr>");

  // "<P>&nbsp;</P>" = texto em branco
  // "<br>" = texto em branco
  if ((($linha_item['texto'] != "") && ($linha_item['texto'] != "<P>&nbsp;</P>") && ($linha_item['texto'] != "<br />")) || ($dono_portfolio)) {
  	echo ("                  <tr class=\"head\">\n");
  	/* 42 - Texto  */
  	echo ("                    <td colspan=\"4\">" . RetornaFraseDaLista($lista_frases, 42) . "</td>\n");
  	echo ("                  </tr>\n");
  	echo ("                  <tr>\n");
  	echo ("                    <td class=\"itens\" colspan=\"4\">\n");
  	echo ("                      <div class=\"divRichText\">\n");
  	echo ("                        " . $texto . "\n");
  	echo ("                      </div>\n");
  	echo ("                    </td>\n");
  	echo ("                  </tr>\n");
  }

  $num_arq_vis = RetornaNumArquivosVisiveis($lista_arq);


  if (($num_arq_vis > 0) || ($dono_portfolio)) {
  	echo ("                  <tr class=\"head\">\n");
  	/* 71 - Arquivos */
  	echo ("                    <td colspan=\"4\">" . RetornaFraseDaLista($lista_frases, 71) . "</td>\n");
  	echo ("                  </tr>\n");

  	if((($dono_portfolio) && (!$pode_editar) && ($tem_avaliacao)) && (count($lista_arq)==0)){
  	  echo("                <tr>\n");
  	  /* Frase #218 - Diret�rio Vazio */
        echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 218)."</td>\n");
        echo("				</tr>\n");
  	}

  	if (is_array($lista_arq) && count($lista_arq)>0){

  		$conta_arq = 0;

  		echo ("                  <tr>\n");
  		echo ("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
  		// Procuramos na lista de arquivos se existe algum visivel
  		$ha_visiveis = $num_arq_vis > 0;

  		if (($ha_visiveis) || ($dono_portfolio)) {
  			$nivel_anterior = 0;
  			$nivel = -1;

  			foreach ($lista_arq as $cod => $linha) {
  				$linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
  				if (!($linha['Arquivo'] == "" && $linha['Diretorio'] == ""))
  					if ((!$linha['Status']) || (($dono_portfolio))) {
  						$nivel_anterior = $nivel;
  						$espacos = "";
  						$espacos2 = "";
  						$temp = explode("/", $linha['Diretorio']);
  						$nivel = count($temp) - 1;
  						for ($c = 0; $c <= $nivel; $c++) {
  						    if($dono_portfolio && $pode_editar){
                              $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                              $espacos2.="  ";
                            }
                            else{
                              $espacos.="";
                              $espacos2.="";
                            }
  						}

  						$caminho_arquivo = $dir_item_temp['link'] . ConverteUrl2Html($linha['Diretorio'] . "/" . $linha['Arquivo']);
  						//converte o o caminho e o nome do arquivo que vêm do linux em UTF-8 para 
  						//ISO-8859-1 para ser exibido corretamente na página.
  						$caminho_arquivo = mb_convert_encoding($caminho_arquivo, "ISO-8859-1", "UTF-8");
  						$linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
  						if ($linha['Arquivo'] != "") {

  							if ($linha['Diretorio'] != "") {
  								$espacos .= "&nbsp;&nbsp;&nbsp;&nbsp;";
  								$espacos2 .= "  ";
  							}

  							if ($linha['Status'])
  								$arqOculto = "arqOculto='sim'";
  							else
  								$arqOculto = "arqOculto='nao'";


                              if (eregi(".zip$",$linha['Arquivo'])){
                              // arquivo zip
                              $imagem    = "<img src=\"../imgs/arqzip.gif\" border=0 alt=\"\"/>";
  						    $tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"zip\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" arqZip=\"" . $linha['Arquivo'] . "\" " . $arqOculto . ">";
  							} 
  						    else{
                              // arquivo comum
                              //imagem
                              if((eregi(".jpg$",$linha['Arquivo'])) || eregi(".png$",$linha['Arquivo']) || eregi(".gif$",$linha['Arquivo']) || eregi(".jpeg$",$linha['Arquivo'])) {
                              $imagem    = "<img alt=\"\" src=\"../imgs/arqimg.gif\" border=\"0\" />";
                              //doc
                              }else if(eregi(".doc$",$linha['Arquivo'])){
                              $imagem    = "<img alt=\"\" src=\"../imgs/arqdoc.gif\" \"border=\"0\" />";
                              //pdf
                              }else if(eregi(".pdf$",$linha['Arquivo'])){
                              $imagem    = "<img alt=\"\" src=\"../imgs/arqpdf.gif\" border=\"0\" />";
                              //html
                              }else if((eregi(".html$",$linha['Arquivo'])) || (eregi(".htm$",$linha['Arquivo']))){
                              $imagem    = "<img alt=\"\" src=\"../imgs/arqhtml.gif\" border=\"0\" />";
                              }else if((eregi(".mp3$",$linha['Arquivo'])) || (eregi(".mid$",$linha['Arquivo']))) {
                              $imagem    = "<img alt=\"\" src=\"../imgs/arqsnd.gif\" border=\"0\" />";
                              }else{
                              $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                             }
                             $tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"comum\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" " . $arqOculto . ">";
                             }

  						   $tag_fecha = "</span>";

  							echo ("                        " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");

  							if ((($dono_portfolio) && ($pode_editar)) || (($dono_portfolio) && (!$tem_avaliacao))){
  								echo ("                          " . $espacos2 . "<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_" . $conta_arq . "\"/>\n");
  							}

  							echo ("                          " . $espacos2 . $espacos . $imagem . $tag_abre . $linha['Arquivo'] . $tag_fecha . " - (" . round(($linha["Tamanho"] / 1024), 2) . "Kb) - ".RetornaFraseDaLista($lista_frases,215)." ".UnixTime2Hora($linha["Data"])." ".UnixTime2DataMesAbreviado($linha["Data"])."");

  							echo ("<span id=\"local_oculto_" . $conta_arq . "\">");
  							if ($linha['Status'])
  								// 118 - Oculto
  								echo ("<span id=\"arq_oculto_" . $conta_arq . "\"> - <span style=\"color:red;\">" . RetornaFraseDaLista($lista_frases, 118) . "</span></span>");
  							echo ("</span>\n");
  							echo ("                          " . $espacos2 . "<br />\n");
  							echo ("                        " . $espacos2 . "</span>\n");

  						} else if (($dono_portfolio) || (haArquivosVisiveisDir($linha['Diretorio'], $lista_arq))){

  							if ($nivel_anterior >= $nivel) {
  								$i = $nivel_anterior - $nivel;
  								$j = $i;
  								$espacos3 = "";
  								do {
  									$espacos3 .= "  ";
  									$j--;
  								} while ($j >= 0);
  								do {
  									echo ("                      " . $espacos3 . "</span>\n");
  									$i--;
  								} while ($i >= 0);
  							}
  							// pasta
  							$imagem = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
  							echo ("                      " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");
  							echo ("                        " . $espacos2 . "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" tipoArq=\"pasta\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\"></span>\n");
  							if ((($dono_portfolio) && ($pode_editar)) || (($dono_portfolio) && (!$tem_avaliacao))){
  								echo ("                        " . $espacos2 . "<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_" . $conta_arq . "\">\n");
  							}
  							echo ("                        " . $espacos2 . $espacos . $imagem . $temp[$nivel] . "\n");
  							echo ("                        " . $espacos2 . "<br />\n");
  						}

  					}
  				$conta_arq++;
  			}
  			do {
  				$j = $nivel;
  				$espacos3 = "";
  				do {
  					$espacos3 .= "  ";
  					$j--;
  				} while ($j >= 0);
  				$nivel--;
  			}
  			while ($nivel >= 0);
  		}
  		if((($dono_portfolio) && (!$pode_editar)) && ($tem_avaliacao)){
  		  /*N�o � poss�vel editar a sess�o de arquivos caso o item estiver associado a uma avalia��o e o
  		   * mesmo j� tiver sido avaliado ou o prazo da atividade relacionada j� estiver terminado.*/
  		  echo("<br>".RetornaFraseDaLista($lista_frases, 219)."\n");
  		}		
  		echo ("                      <script type=\"text/javascript\">js_conta_arq=" . $conta_arq . ";</script>\n");
  		echo ("                    </td>\n");
  		echo ("                  </tr>\n");
  	}
  	
  	if ((($dono_portfolio) && ($pode_editar)) || (($dono_portfolio) && (!$tem_avaliacao))) {
  		echo ("                  <tr>\n");
  		echo ("                    <td align=\"left\" colspan=\"4\">\n");
  		echo ("                      <ul>\n");
  		echo ("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></span></li>\n");
  		echo ("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
  		echo ("                        <li class=\"menuUp\" id=\"mArq_mover\"><span id=\"sArq_mover\">".RetornaFraseDaLista($lista_frases_geral, 25)."</span></li>\n");
  		echo ("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">".RetornaFraseDaLista($lista_frases_geral, 38)."</span></li>\n");
  		echo ("                        <li class=\"menuUp\" id=\"mArq_ocultar\"><span id=\"sArq_ocultar\">".RetornaFraseDaLista($lista_frases_geral, 511)."</span></li>\n");
  		echo ("                      </ul>\n");
  		echo ("                    </td>\n");
  		echo ("                  </tr>\n");
  		echo ("                  <tr>\n");
  		echo ("                    <td align=\"left\" colspan=\"4\">\n");
  		echo ("                      <form name=\"formFiles\" id=\"formFiles\" action=\"acoes.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
  		echo ("                        <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\" />\n");
  		echo ("                        <input type=\"hidden\" name=\"cod_item\" value=\"" . $cod_item . "\" />\n");
  		echo ("                        <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"" . $cod_topico_raiz . "\" />\n");
  		echo ("                        <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"" . $cod_usuario_portfolio . "\" />\n");
  		echo ("                        <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"" . $cod_grupo_portfolio . "\" />\n");
  		echo ("                        <input type=\"hidden\" name=\"acao\" value=\"anexar\" />\n");
  		echo ("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
  		echo ("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" />\n");
  		echo ("                          <span class=\"destaque\">" . RetornaFraseDaLista($lista_frases_geral, 26) . "</span>\n");
  		echo ("                          <span> - " . RetornaFraseDaLista($lista_frases, 59) . RetornaFraseDaLista($lista_frases, 60) . "</span>\n");
  		echo ("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
  		echo ("                          <input type=\"file\" id=\"input_files\" name=\"input_files\"  class=\"input\" onchange=\"EdicaoArq(1);\">\n");
  		//echo ("                          &nbsp;&nbsp;\n");
  		//echo ("                          <span onclick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 18) . "</span>\n");
  		//echo ("                          &nbsp;&nbsp;\n");
  		//echo ("                          <span onclick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span>\n");
  		echo ("                        </div>\n");
  		/* 26 - Anexar arquivos (ger) */
  		echo ("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">" . RetornaFraseDaLista($lista_frases_geral, 26) . "</span></div>\n");
  		echo ("                      </form>\n");

  	}
  	echo ("                    </td>\n");
  	echo ("                  </tr>\n");
  }

  $lista_url = RetornaEnderecosMaterial($sock, $cod_item);

  if ((is_array($lista_url)) || ($dono_portfolio)) {

  	echo ("                  <tr class=\"head\">\n");
  	/* 44 - Endereços */
  	echo ("                    <td colspan=\"4\">" . RetornaFraseDaLista($lista_frases, 44) . "</td>\n");
  	echo ("                  </tr>\n");
  	echo ("                  <tr>\n");
  	echo ("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");

  	if (count($lista_url) > 0) {
  		foreach ($lista_url as $cod => $linha) {

  			$linha['endereco'] = RetornaURLValida($linha['endereco']);

  			echo ("                      <span id='end_" . $linha['cod_endereco'] . "'>\n");

  			if ($linha['nome'] != "") {
  				echo ("                      <span class=\"link\" onclick=\"WindowOpenVerURL('" . ConverteSpace2Mais($linha['endereco']) . "');\">" . $linha['nome'] . "</span>&nbsp;&nbsp;(" . $linha['endereco'] . ")");
  			} else {
  				echo ("                      <span class=\"link\" onclick=\"WindowOpenVerURL('" . ConverteSpace2Mais($linha['endereco']) . "');\">" . $linha['endereco'] . "</span>");
  			}

  			if ($dono_portfolio) {
  				/* (gen) 1 - Apagar */
  				echo (" - <span class=\"link\" onclick=\"ApagarEndereco('" . $cod_curso . "', '" . $linha['cod_endereco'] . "');\">" . RetornaFraseDaLista($lista_frases_geral, 1) . "</span>\n");
  			}
  			echo ("                        <br />\n");
  			echo ("                      </span>\n");

  		}
  	}

  	echo ("                    </td>\n");
  	echo ("                  </tr>\n");

  	if ($dono_portfolio) {
  		echo ("                  <tr>\n");
  		echo ("                    <td colspan=\"4\" align=\"left\" id=\"tdIncluirEnd\">\n");
  		/* 45 - Incluir Endereço */
  		echo ("                      <div id=\"divEndereco\"><img alt=\"\" src=\"../imgs/url.jpg\" border=\"0\" /> <span id=\"incluiEnd\" class=\"link\" onclick=\"AdicionaInputEndereco();\">" . RetornaFraseDaLista($lista_frases, 45) . "</span></div>\n");
  		echo ("                      <div id=\"divEnderecoEdit\" class=\"divHidden\">\n");
  		echo ("                        <img alt=\"\" src=\"../imgs/url.jpg\" border=\"0\" />\n");
  		echo ("                        <span id=\"incluiEndEdit\" class=\"destaque\">" . RetornaFraseDaLista($lista_frases, 45) . "</span>\n");
  		echo ("                        <span> - " . RetornaFraseDaLista($lista_frases, 65) . "</span>\n");
  		echo ("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
  		echo ("                        <span class=\"destaque\">" . RetornaFraseDaLista($lista_frases, 41) . "</span><br />\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <input type=\"text\" class=\"input\" name=\"novoNomeEnd\" id=\"novoNomeEnd\" size=\"30\" />\n");
  		echo ("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <span class=\"destaque\">" . RetornaFraseDaLista($lista_frases, 66) . "</span><br />\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <input type=\"text\" class=\"input\" name=\"novoEnd\" id=\"novoEnd\" size=\"30\" />\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <span class=\"link\" onclick=\"EditaEndereco(1);\">" . RetornaFraseDaLista($lista_frases_geral, 18) . "</span>\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <span class=\"link\" id=\"cancelaEnd\" onclick=\"EditaEndereco(0);\">" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span><br />\n");
  		echo ("                      </div>\n");
  		echo ("                    </td>\n");
  		echo ("                  </tr>\n");
  	}
  }

  //Associar a uma avaliação
  if ($dono_portfolio) {

  	if ($portfolio_grupo) {
  		//'G'rupo
  		$tipo_portfolio = 'G';
  	} else {
  		//'I'ndividual
  		$tipo_portfolio = 'I';
  	}

  	$lista = RetornaAvaliacaoPortfolio($sock, $tipo_portfolio, $cod_curso, $cod_usuario);

  	if (is_array($lista)) {

  		echo ("                  <tr class=\"head\">\n");
  		/* 139 - Avaliação */
  		echo ("                    <td colspan=\"4\">" . RetornaFraseDaLista($lista_frases, 139) . "</td>\n");
  		echo ("                  </tr>\n");
  		echo ("                  <tr>\n");
  		echo ("                    <td colspan=\"4\" class=\"itens\" colspan=\"4\">\n");
  		/* 152 - Associar item à Avaliação */
  		echo ("                      <div id=\"divAvaliacao\"><img alt=\"\" src=\"../imgs/portfolio/lapis.gif\" border=0 /><span id=\"assocAval\" class=\"link\" onclick=\"AssociarAvaliacao();\">" . RetornaFraseDaLista($lista_frases, 152) . "</span></div>\n");
  		echo ("                      <div id=\"divAvaliacaoEdit\" class=\"divHidden\">\n");
  		echo ("                        <img alt=\"\" src=\"../imgs/portfolio/lapis.gif\" border=\"0\" /><span class=\"destaque\" >" . RetornaFraseDaLista($lista_frases, 152) . "</span>\n");
  		echo ("                        <span> - " . RetornaFraseDaLista($lista_frases, 153) . "</span><br /><br /><br />\n");
  		echo ("                        <table id=\"tableAvaliacao\" cellspacing=\"0\" cellspading=\"0\">\n");
  		echo ("                          <tr class=\"head\" align=\"center\">\n");
  		echo ("                            <td>&nbsp;</td>\n");
  		// 168 - Atividades
  		echo ("                            <td width=\"30%\">" . RetornaFraseDaLista($lista_frases, 168) . "</td>\n");
  		// 163 - Tipo de Atividade
  		echo ("                            <td width=\"15%\">" . RetornaFraseDaLista($lista_frases, 163) . "</td>\n");
  		// 167 - Valor 
  		echo ("                            <td width=\"5%\">" . RetornaFraseDaLista($lista_frases, 167) . "</td>\n");
  		// 165 - Data de início
  		echo ("                            <td width=\"20%\">" . RetornaFraseDaLista($lista_frases, 165) . "</td>\n");
  		// 166 - Data de término
  		echo ("                            <td width=\"20%\">" . RetornaFraseDaLista($lista_frases, 166) . "</td>\n");
  		echo ("                          </tr>\n");

  		// esta var indica se precisamos colocar uma legenda explicando o que eh o termo "associado" na frente da avaliacao 
  		$legenda_associado = false;
  		if (count($lista)) {
  			foreach ($lista as $cod => $linha) {
  				// para que a opção com esta avaliação possa ser escolhida
  				if ($cod_avaliacao == $linha['cod_avaliacao'])
  					$ha_nao_avaliado = false;
  				else {
  					//somente verifica se existe item nao avaliado se nao for a avaliação que ja estava associada ao item, pois não posso impedir o usuario de associar o item a avaliação ao qual o mesmo ja estava vinculado

  					// A funcao ExisteItemNaoAvaliado retorna o numero de itens nao avaliados. Se este numero for != 0, $ha_nao_avaliado = true (ha itens nao avaliados)
  					if ($portfolio_grupo)
  						$ha_nao_avaliado = (0 != ExisteItemNaoAvaliado($sock, $linha['cod_avaliacao'], $cod_grupo_portfolio, $portfolio_grupo, $cod_topico_raiz_usuario));
  					else
  						$ha_nao_avaliado = (0 != ExisteItemNaoAvaliado($sock, $linha['cod_avaliacao'], $cod_usuario_portfolio, $portfolio_grupo, $cod_topico_raiz_usuario));
  				}

  				$atividade = RetornaTituloAtividade($sock, $linha['cod_atividade']);

  				if ($ha_nao_avaliado) {
  					// ha um item associado a esta avaliacao e nao avaliado. Entao nao pode associar este item a esta avaliacao
  					$radio = "<input class=\"g1field\" type=\"radio\" " . $ch . " style=\"visibility: hidden;\" name=\"cod_avaliacao\" value=\"" . $linha['cod_avaliacao'] . "\" onclick=\"cod_avaliacao=" . $linha['cod_avaliacao'] . ";\">";
  					// Escrevemos na frente da avaliacao que outro item já foi associado a ela
  					// 170 - associado
  					$assoc = "<span name=\"associado\" style=\"font-size: 0.9em;color: #ff0000;visibility: visible;\">" . "&nbsp;&nbsp;" . "(" . RetornaFraseDaLista($lista_frases, 170) . ")" . "</span>";
  					// E precisamos colocar a legenda o que esta frase 'associado' significa
  					$legenda_associado = true;
  				} else {

  					if ($cod_avaliacao == $linha['cod_avaliacao']) {
  						$ch = "checked";
  					} else {
  						$ch = "";
  					}

  					$radio = "<input class=\"g1field\" type=\"radio\" " . $ch . " style=\"visibility: visible;\" name=\"cod_avaliacao\" value=\"" . $linha['cod_avaliacao'] . "\" onclick=\"cod_avaliacao=" . $linha['cod_avaliacao'] . ";\">";
  					// Avaliação livre, não escrevemos nada na frente
  					$assoc = "<span name=\"associado\" style=\"font-size: 0.9em;color: #ff0000;visibility: hidden;\">" . "&nbsp;&nbsp;" . "(" . RetornaFraseDaLista($lista_frases, 170) . ")" . "</span>";
  				}

  				echo ("                          <tr>\n");
  				echo ("                            <td width=\"1%\">" . $radio . "</td>\n");
  				echo ("                            <td align=\"left\"><span class=\"link\" onclick=\"WindowOpenVerAvaliacao(" . $linha['cod_avaliacao'] . ");EscondeLayers();return(false);\">" . $atividade . "</span>" . $assoc . "</td>\n");

  				if (!strcmp($tipo_portfolio, 'I')) {
  					// 161 - Individual
  					echo ("                            <td align=\"center\">" . RetornaFraseDaLista($lista_frases, 161) . "<br /></td>\n");
  				} else {
  					// 162 - Em Grupo
  					echo ("                            <td align=\"center\">" . RetornaFraseDaLista($lista_frases, 162) . "<br /></td>\n");
  				}
  				echo ("                            <td align=\"center\">" . $linha['valor'] . "<br /></td>\n");
  				echo ("                            <td align=\"center\">" . Unixtime2Data($linha['data_inicio']) . "<br /></td>\n");
  				echo ("                            <td align=\"center\">" . Unixtime2Data($linha['data_termino']) . "<br /></td>\n");
  				echo ("                          </tr>\n");
  			}
  		} else {
  				echo ("                          <tr>\n");
  				echo ("                            <td colspan=\"6\" align=\"center\">" . RetornaFraseDaLista($lista_frases, 214) . "<br /></td>\n");
  				echo ("                          </tr>\n");
  		}


  		echo ("                        </table>\n");

  		// 173 - OBS: Se outro item tiver sido associado a uma avaliacao e nao tiver sido avaliado, nao sera possivel associar este item à mesma.
  		$frase_atividades = RetornaFraseDaLista($lista_frases, 173);
  		echo ("                        " . $frase_atividades);

  		echo ("					  <br /><br />");
  		echo ("					  <input type=\"submit\" value=\"" . RetornaFraseDaLista($lista_frases_geral, 18) . "\" class=\"input\" id=\"OKAval\" onclick=\"EditaAval(1);\"/> ");
  		echo ("					  <input type=\"submit\" value=\"" . RetornaFraseDaLista($lista_frases_geral, 2) . "\" class=\"input\" id=\"cancAval\" onclick=\"EditaAval(0);FechaDivAvaliacoes();\"/> ");
  		echo ("					  <input type=\"submit\" value=\"" . RetornaFraseDaLista($lista_frases, 160) . "\" class=\"input\" onclick=\"xajax_VerificaSePodeDesassociar(" . $cod_curso . ", " . $cod_usuario . ", " . $cod_item . ", '" . RetornaFraseDaLista($lista_frases_geral, 36) . "', '" . RetornaFraseDaLista($lista_frases, 213) . "', '" . RetornaFraseDaLista($lista_frases, 221) . "');\";/> ");
  		echo ("                      </div>\n");
  		echo ("                    </td>\n");
  		echo ("                  </tr>\n");
  	}
  }

  echo ("                </table>\n"); //TabInterna
  echo ("              </td>\n");
  echo ("            </tr>\n");
  echo ("          </table>\n"); //TabExterna
  echo ("        </td>\n");
  echo ("      </tr>\n");

  include ("../tela2.php");
  include ("layer.php");

  echo ("  </body>\n");
  echo ("</html>\n");

  Desconectar($sock);
?>
