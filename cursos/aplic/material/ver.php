<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/ver.php

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

    Nied - Ncleo de Inform�tica Aplicada � Educa��o
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
  ARQUIVO : cursos/aplic/material/ver.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  session_register("cod_ferramenta_m");
  if (isset($cod_ferramenta))
    $cod_ferramenta_m=$cod_ferramenta;
  else
    $cod_ferramenta=$cod_ferramenta_m;

  if ($cod_ferramenta==3)
    include("avaliacoes_material.inc");

  // Ajuda:
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 5;

  /**************** ajax ****************/

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun��es em PHP que voc� quer chamar atrav�s do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamento");
  $objAjax->register(XAJAX_FUNCTION,"EditarTitulo");
  $objAjax->register(XAJAX_FUNCTION,"EditarTexto");
  $objAjax->register(XAJAX_FUNCTION,"InsereEnderecoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"InsereAvaliacaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ApagaAvaliacaoPortfolioDinamic");
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
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  /**************** ajax ****************/

  include("../topo_tela.php");
  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);

  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("criarItem",	 124, 0);
  $feedbackObject->addAction("criarTopico",  127, 0);
  $feedbackObject->addAction("anexar",		 62, 61);
  $feedbackObject->addAction("nomeAnexo",	 0, 150);
  $feedbackObject->addAction("descompactar", 125, 126);
  $feedbackObject->addAction("moverItem",	 142, 143);
  $feedbackObject->addAction("moverArquivo",	 142, 143);

  Desconectar($sock);

  $sock=Conectar("");

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $AcessoAvaliacaoM = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  switch ($cod_ferramenta) {
    case 3 :
      $tabela="Atividade";
      $dir="atividades";
      break;
    case 4 :
      $tabela="Apoio";
      $dir="apoio";
      break;
    case 5 :
      $tabela="Leitura";
      $dir="leituras";
      break;
    case 7 :
      $tabela="Obrigatoria";
      $dir="obrigatoria";
      break;
  }

  $dir_item_temp = CriaLinkVisualizar($sock,$dir,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp);
  $eformador=EFormador($sock,$cod_curso,$cod_usuario);
  
  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);
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

  $dir_tmp_ferramenta = $diretorio_arquivos.'/'.$cod_curso.'/'.$dir.'/tmp';
  
  if (!file_exists($dir_tmp_ferramenta)) mkdir($dir_tmp_ferramenta);
  
  /* Verificação se o item está em Edi��o */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edi��o... */

  $linha=RetornaUltimaPosicaoHistorico ($sock, $tabela, $cod_item);
  if ($linha['acao']=="E") {
    if($linha['inicio_edicao']>(time()-1800) || $cod_usuario!=$linha['cod_usuario']) {
      /* Est� em edi��o... */
      echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
      echo("       window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_topico_raiz=".$cod_topico_raiz."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\n");
      echo("       document.location='material.php?cod_curso=".$cod_curso."&cod_item=".$linha_item['cod_item']."&origem=ver&cod_topico=".$cod_topico_raiz."';\n");
      echo("    </script>\n");
      echo("  </head>\n");
      echo("  <body>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
    else {
      CancelaEdicao($sock, $tabela, $dir, $cod_item, $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
    }
  }
  GeraJSVerificacaoData();
  GeraJSComparacaoDatas();
  echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");

  echo("      function WindowOpenVerURL(end)\n");
  echo("      {\n");
  echo("         window.open(end,'MaterialURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n");

  echo("      function WindowOpenVer(end)\n");
  echo("      {\n");
  echo("        popup = window.open(end,'MaterialVer','top=50,left=100,width=600,height=400,resizable=yes,menubar=yes,status=yes,toolbar=yes,scrollbars=yes');\n");
  echo("        popup.focus();\n");
  echo("      }\n\n");

  if ($eformador){
    if ($cod_ferramenta==3){
      echo("      function ApagarMaterial(){\n");
      /* 6 - Voce tem certeza de que deseja apagar esta atividade? */
      $msg_confirmacao = RetornaFraseDaLista($lista_frases,6);
      /* 101 - (a atividade sera movida para a lixeira e se houver alguma avaliacao relacionada, a avaliacao tambem sera movida para a lixeira DAS AVALIACOES) */
       if (AtividadeEhAvaliacao($sock,$cod_item))
         $msg_confirmacao.= "\\n".RetornaFraseDaLista($lista_frases,101);

      echo("         if(confirm(\"".$msg_confirmacao."\")){\n");
      echo("           window.location='acoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;cod_ferramenta=3&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&acao=apagarItem';\n");
      echo("      }\n");
      echo("}\n");
    }
    else{
      echo("      function ApagarMaterial()\n");
      echo("      {\n");
      /* 6 - Voc� tem certeza de que deseja apagar esta atividade? */
      /* 7 - (a atividade serï¿½movida para a lixeira) */
      echo("         if(confirm(\"".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."\")){\n");
      echo("           window.location='acoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&cod_item=".$cod_item."&acao=apagarItem';\n");
      echo("        }\n");
      echo("      }\n");
    }

    echo("      var isNav = (navigator.appName.indexOf('Netscape') !=-1);\n");
    echo("      var isMinNS6 = ((navigator.userAgent.indexOf('Gecko') != -1) && (isNav));\n");
    echo("      var isIE = (navigator.appName.indexOf('Microsoft') !=-1);\n");
    echo("      var Xpos, Ypos;\n");
    echo("      var js_cod_item=".$cod_item.", js_cod_topico;\n");
    echo("      var js_nome_topico;\n");
    echo("      var js_tipo_item;\n");
    echo("      var js_conta_arq=0;\n");
    echo("      var mostrando=0;\n");
    echo("      var editando=0;\n");
    echo("      var js_comp = new Array();\n");
    echo("      var editaTitulo=0;\n");
    echo("      var editaTexto=0;\n");
    echo("      var conteudo=\"\";\n");
    echo("      var input=0;\n");
    echo("      var cancelarElemento=null;\n");
    echo("      var cancelarTodos=0;\n");

    echo("      var cod_avaliacao=\"\";\n");
    echo("      var valor_radios = new Array();\n");
    /* (ger) 18 - Ok */
    // Texto do bot�o Ok do ckEditor
    echo("      var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
    /* (ger) 2 - Cancelar */
    // Texto do bot�o Cancelar do ckEditor
    echo("      var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

    //echo("      if (isNav)\n");
    //echo("      {\n");
    //echo("        document.captureEvents(Event.MOUSEMOVE);\n");
    //echo("      }\n");
    //echo("      document.onmousemove = TrataMouse;\n\n");
    
    //echo("      document.attachEvent('onmousemove', TrataMouse);\n");

    /* Verificação do browser sendo usado */
    echo("      if (document.addEventListener) {\n");	/* Caso do FireFox */
    echo("        document.addEventListener('mousemove', TrataMouse, false);\n");
    echo("      } else if (document.attachEvent){\n");	/* Caso do IE */
    echo("        document.attachEvent('onmousemove', TrataMouse);\n");
    echo("      }\n");
    
    
    echo("      function TrataMouse(e)\n");
    echo("      {\n");
    echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("      }\n\n");

    echo("      function getPageScrollY()\n");
    echo("      {\n");
    echo("        if (isNav)\n");
    echo("          return(window.pageYOffset);\n");
    echo("        if (isIE)\n");
    echo("          return(document.body.scrollTop);\n");
    echo("      }\n\n");

    echo("      function AjustePosMenuIE()\n");
    echo("      {\n");
    echo("        if (isIE)\n");
    echo("          return(getPageScrollY());\n");
    echo("        else\n");
    echo("          return(0);\n");
    echo("      }\n");

    echo("      function Iniciar()\n");
    echo("      {\n");
    echo("        startList();\n");
    echo("        cod_comp = getLayer(\"comp\");\n");
    echo("        cod_mover = getLayer(\"mover\");\n");
    echo("        lay_topicos = getLayer(\"topicos\");\n");
    echo("      //   cod_novapasta = getLayer(\"novapasta\");\n");
    echo("        cod_mover_arquivo = getLayer(\"mover_arquivo\");\n");
    echo("        EscondeLayers();\n");

    $acao = $_GET['acao'];
    $statusAcao = $_GET['statusAcao'];

    /* colocar essa linha dentro da funcao Iniciar, ela imprime o comando certo. */
    $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);


//     if(isset($acao)){
//       if(!strcmp($acao, 'criarItem')){
//         if(!strcmp($statusAcao, 'true')){
//           echo("          mostraFeedback('Item criado com sucesso', 'true')\n");
//         }
//       }
//     }

    echo("      }\n");

    echo("      function EscondeLayers()\n");
    echo("      {\n");
    echo("        hideLayer(cod_comp);\n");
    echo("        hideLayer(cod_mover);\n");
    echo("      //   hideLayer(cod_novapasta);\n");
    echo("        hideLayer(cod_mover_arquivo);\n");
    echo("      }\n");

    echo("      function MostraLayer(cod_layer, ajuste)\n");
    echo("      {\n");
    echo("        CancelaTodos();\n");
    echo("        EscondeLayers();\n");
    echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("        mostrando=1;\n");
    echo("        showLayer(cod_layer);\n");
    echo("      }\n");

    echo("      function EscondeLayer(cod_layer)\n");
    echo("      {\n");
    echo("        hideLayer(cod_layer);\n");
    echo("        mostrando=0;\n");
    echo("      }\n");

    echo("      function TestaDatas()\n");
    echo("      {\n");
    echo("        var data_inicio=document.getElementById('DataInicioAval');\n");
    echo("        var data_termino=document.getElementById('DataTerminoAval');\n");
    echo("        if (!DataValidaAux(data_inicio)){\n");
    /* 132 - Data de inicio inválida. */
    //echo("          alert('".RetornaFraseDaLista($lista_frases,132)."');\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        if (!DataValidaAux(data_termino)){\n");
    /* 133 - Data de término inválida. */
    //echo("          alert('".RetornaFraseDaLista($lista_frases,133)."');\n");
    echo("          return false;\n");
    echo("        }\n");
    echo("        if (ComparaData(data_inicio,data_termino) > 0) // (inicio>termino) \n");
    echo("        {\n");
    /* 134 - A data de inicio é posterior à data de termino. */
    echo("         alert('".RetornaFraseDaLista($lista_frases,134)."');\n");
    echo("         return(false);\n");
    echo("        }\n");
    echo("        return true");
    echo("  }\n\n");

    // retorna true se a nota contiver digitos estranhos
    // retorna false se a nota estiver no formato adequado
    echo("        function VerificaDigitosValor(valor) {\n");
    echo("          re_com_virgula = /^[0-9]+(\.|,)?[0-9]+\$/; \n"); // nota com decimal
    echo("          re_somente_numeros = /^[0-9]+\$/; \n"); // somente numeros
    echo("          if (valor == '' || re_com_virgula.test(valor) || re_somente_numeros.test(valor) ) { \n");
    echo("            return false;\n");
    echo("          } else {\n");
    echo("            return true;\n");
    echo("          }\n");
    echo("        }\n");

    echo("        function VerificaValor(valor) \n");
    echo("        {\n");
    echo("          if (valor==''){\n");
    // 128 - O campo Valor não pode ser vazio.
    echo("            alert('".RetornaFraseDaLista($lista_frases,128)."');\n");
    echo("            return false; \n");
    echo("          } \n");
    echo("          if (VerificaDigitosValor(valor)){\n");
    // 129 - Você digitou caracteres estranhos no campo Valor.
    // 130 - Use apenas dígitos de 0 a 9 e o ponto ( . ) ou a vírgula ( , ) para este campo (exemplo: 7.5).
    echo("            alert('".RetornaFraseDaLista($lista_frases,129)."\\n".RetornaFraseDaLista($lista_frases,130)."');\n");
    echo("            return false; \n");
    echo("          } \n");
    // verificamos se o Valor tem virgula, se tiver, convertemos para ponto
    echo("          valor = valor.replace(/\,/, '.'); \n");
    echo("          if (valor < 0) { \n");
    // 131 - A avaliação não pode ter valor negativo.
    echo("            alert('".RetornaFraseDaLista($lista_frases,24)."'); \n");
    echo("            return false; \n");
    echo("          }  \n");
    echo("          return true;\n");
    echo("        }  \n");
    
    echo("      function AtualizaComp(js_tipo_comp) {\n");
    echo("        var tipo_comp;\n");
    echo("        if ((isNav) && (!isMinNS6)) {\n");
    echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
    echo("          document.comp.document.form_comp.cod_pagina.value='';\n");
    echo("          tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'));\n");
    echo("        } else {\n");
    echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("            document.form_comp.cod_item.value=js_cod_item;\n");
    echo("            document.form_comp.cod_pagina.value='';\n");
    echo("            tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'));\n");
    echo("        }\n");
    echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
    echo("        if (js_tipo_comp=='T') {\n");
    echo("          tipo_comp[0].innerHTML=imagem;\n");
    echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
    echo("        } else if (js_tipo_comp=='F') {\n");
    echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[1].innerHTML=imagem;\n");
    echo("        }\n");
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("      }\n");

    echo("      function MoverItem(link,cod_destino)\n");
    echo("      { \n");
    echo("        xajax_MoverItensDinamic('".$tabela."', ".$cod_curso.", ".$cod_ferramenta.", ".$cod_usuario.", ".$cod_topico_raiz.", cod_destino, null, ".$cod_item.", '".RetornaFraseDaLista($lista_frases, 2)."', '".RetornaFraseDaLista($lista_frases, 56)."');\n");
    echo("      }\n");

    echo("      function EdicaoTexto(codigo, id, valor){\n");
    echo("        if (valor=='ok'){\n");
    //echo("          conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
    //echo("          conteudo=CKEDITOR.instances.msg_corpo.getData();");
    echo("          eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');\n");
    echo("          xajax_EditarTexto('".$tabela."', ".$cod_curso.", codigo, conteudo, ".$cod_usuario.");\n");
    echo("          mostraFeedback('".htmlentities(RetornaFraseDaLista($lista_frases, 54))."', true)\n");
    echo("        }\n");
    echo("        else{\n");
    echo("          //Cancela Edição\n");
    echo("          if (!cancelarTodos)\n");
    echo("            mostraFeedback('".htmlentities(RetornaFraseDaLista($lista_frases, 40))."', true)\n");
    echo("            xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("        }\n");
    echo("        document.getElementById(id).innerHTML=conteudo;\n");
    echo("        editaTexto=0;\n");
    echo("        cancelarElemento=null;\n");
    echo("      }\n\n");

    echo("      var controle=0;\n\n");
    // recebe o id, o id do elemento ex: tit_id, e o tipo: ok ou canc
    echo("      function EdicaoTitulo(codigo, id, valor){\n");
    echo("        var novoNome = document.getElementById(id+'_text').value\n");
    
    echo("        if ((valor=='ok')&&(document.getElementById(id+'_text').value!='')){\n");
    echo("          if( !(novoNome.indexOf(\"\\\\\")>=0 || novoNome.indexOf(\"\\\"\")>=0 || novoNome.indexOf(\"'\")>=0 || novoNome.indexOf(\">\")>=0 || novoNome.indexOf(\"<\")>=0) ) {\n");
   
 //   echo("          conteudo = document.getElementById(id+'_text').value;\n");
    echo("            xajax_EditarTitulo('".$tabela."', ".$cod_curso.", codigo, novoNome, ".$cod_usuario.", '".RetornaFraseDaLista($lista_frases,136)."');\n");
    echo("            document.getElementById(id+'_text').value=novoNome;");
    echo("          }else{\n");
    // 77 - O titulo do item nao pode conter \\\", \\\', < ou >. 
    echo("            alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,77)))."\");\n");
    echo("            document.getElementById(id+'_text').value = novoNome\n");
    echo("            document.getElementById(id+'_text').focus();\n");
    echo("            return false;\n");
    echo("          }\n");
    
    echo("        }else{\n");
    /* 76 - O titulo nao pode ser vazio. */
    echo("          if ((valor=='ok')&&(document.getElementById(id+'_text').value==''))	\n");
    echo("            alert('".RetornaFraseDaLista($lista_frases,76)."');\n");

    echo("          document.getElementById(id).innerHTML=conteudo;\n");
	
//    echo("          if(navigator.appName.match('Opera')){\n");
//    echo("            document.getElementById('renomear_'+codigo).onclick = AlteraTitulo(codigo);\n");
//    echo("          }else{\n");
//    echo("            document.getElementById('renomear_'+codigo).onclick = function(){ AlteraTitulo(codigo); };\n");
//    echo("          }\n\n");

    echo("          //Cancela Edição\n");
    echo("          if (!cancelarTodos)\n");
    echo("            xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("        }\n");
    echo("        editaTitulo=0;\n");
    echo("        cancelarElemento=null;\n");
    echo("      }\n");

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
    echo ("             EdicaoTitulo(id, 'tit_'+id, 'ok');\n"); //A fun��o e par�metros s�o os mesmos utilizados na fun��o de edi��o j� utilizada.
    echo ("         }\n\n");
    echo ("         return true;\n");
    echo ("     }\n\n");

    echo("      function AlteraTitulo(id){\n");
    echo("        var id_aux = id;\n");
    echo("        if (editaTitulo==0){\n");
    echo("          CancelaTodos();\n");
    
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    
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

    echo("      function AlteraTexto(id){\n");
    echo("        if (editaTexto==0){\n");
    echo("          CancelaTodos();\n");

    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("          conteudo = document.getElementById('text_'+id).innerHTML;\n");
    echo("          id_aux = id;");
    echo("          newDiv = document.createElement('span');\n");
    echo("          writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
    echo("          startList();\n");
    echo("          document.getElementById('text_'+id).appendChild(newDiv);\n");
    echo("          cancelarElemento=document.getElementById('CancelaEdita');\n");
    echo("          editaTexto++;\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function LimpaTexto(id){\n");
    // 123 - Você tem certeza que deseja apagar este texto?
    echo("        if (confirm('".RetornaFraseDaLista($lista_frases,123)."')){\n");
    echo("          checks = document.getElementsByName('chkArq');\n\n");
    echo("          CancelaTodos();\n");
    echo("          document.getElementById('text_'+id).innerHTML='';\n\n");
    echo("          xajax_EditarTexto('".$tabela."', ".$cod_curso.", id, '', ".$cod_usuario.");\n\n");
    // 122 - Texto excluido com sucesso
    echo("          mostraFeedback('".htmlentities(RetornaFraseDaLista($lista_frases,122))."', true);\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("    function ArquivoValido(path)\n");
    echo("    {\n");
    echo("      var file=getfilename(path);\n");
    echo("      var vet  = file.match(/^[A-Za-z0-9-\.\_\ ]+/);\n");
    // Usando expressão regular para identificar caracteres inválidos
    echo("      if ((file.length == 0) || (vet == null) || (file.length != vet[0].length))\n");
    echo("        return false;\n");
    echo("      return true;\n");
    echo("    }\n");

    echo("      function getfilename(path)\n");
    echo("      {\n");
    echo("        pieces=path.split('\\\\');\n");
    echo("        n=pieces.length;\n");
    echo("        file=pieces[n-1];\n");
    echo("        pieces=file.split('/');\n");
    echo("        n=pieces.length;\n");
    echo("        file=pieces[n-1];\n");
    echo("        return(file);\n");
    echo("      }\n");

    echo("      function EdicaoArq(i, msg){\n");
    echo("        var filename = document.getElementById('input_files').value;\n");
    echo("        filename = filename.replace(\"C:\\\\fakepath\\\\\", \"\");\n");
    echo("        if ((i==1) && ArquivoValido(filename)) { //OK\n");
    echo("          document.formFiles.submit();\n");
    echo("        }\n");
    echo("        else {\n");
    /* 150 - Nome do anexo com acentos ou caracteres inv�lidos! Renomeie o arquivo e tente novamente. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 150)."');\n");
    echo("          document.getElementById('input_files').style.visibility='hidden';\n");
    echo("          document.getElementById('input_files').value='';\n");
    echo("          document.getElementById('divArquivo').className='';\n");
    echo("          document.getElementById('divArquivoEdit').className='divHidden';\n");
    echo("          //Cancela Edição\n");
    echo("          if (!cancelarTodos)\n");
    echo("            xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("          input=0;\n");
    echo("          cancelarElemento=null;\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function AcrescentarBarraFile(apaga){\n");
    echo("          if (input==1) return;\n");
    echo("          CancelaTodos();\n");
    echo("          document.getElementById('input_files').style.visibility='visible';\n");
    echo("          document.getElementById('divArquivoEdit').className='';\n");
    echo("          document.getElementById('divArquivo').className='divHidden';\n");
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("          cancelarElemento=document.getElementById('cancFile');\n");
    echo("      }\n\n");

    echo("      function AdicionaInputEndereco(){\n");
    echo("          CancelaTodos();\n");
    echo("          document.getElementById('novoEnd').style.visibility='visible';\n");
    echo("          document.getElementById('novoNomeEnd').style.visibility='visible';\n");
    echo("          document.getElementById('divEndereco').className='divHidden';\n");
    echo("          document.getElementById('divEnderecoEdit').className='';\n");
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("          cancelarElemento=document.getElementById('cancelaEnd');\n");
    echo("          document.getElementById('novoNomeEnd').focus();\n");
    echo("        }\n\n");

    echo ("     function TestaEnterEndereco(campo, evento)\n");
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
    echo ("             EditaEndereco(1);\n"); //A fun��o e par�metros s�o os mesmos utilizados na fun��o de edi��o j� utilizada.
    echo ("         }\n\n");
    echo ("         return true;\n");
    echo ("     }\n\n");

    echo("      function EditaEndereco(opt){\n");
    echo("          if (opt){\n");
    echo("            if (document.getElementById('novoEnd').value==''){\n");
    echo("              xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("              alert('".RetornaFraseDaLista($lista_frases,83)."');\n");
    echo("              return false;\n");
    echo("            }\n");
    echo("            xajax_InsereEnderecoDinamic('".$tabela."', document.getElementById('novoNomeEnd').value, document.getElementById('novoEnd').value, ".$cod_item.", ".$cod_curso.", ".$cod_usuario.", '".RetornaFraseDaLista($lista_frases,69)."');\n");
    echo("          }else{\n");
    echo("            if (!cancelarTodos)\n");
    echo("              xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("          }\n\n");

    echo("          document.getElementById('novoEnd').style.visibility='hidden';\n");
    echo("          document.getElementById('novoNomeEnd').style.visibility='hidden';\n");
    echo("          document.getElementById('novoEnd').value='';\n");
    echo("          document.getElementById('novoNomeEnd').value='';\n");
    echo("          document.getElementById('divEnderecoEdit').className='divHidden';\n");
    echo("          document.getElementById('divEndereco').className='';\n\n");

    echo("          cancelarElemento=null;\n");
    echo("        }\n\n");
echo("      function AdicionaInputAvaliacao(div_hidden){\n");
    echo("          CancelaTodos();\n");
    echo("          document.getElementById('DataInicioAval').style.visibility='visible';\n");
    echo("          document.getElementById('DataTerminoAval').style.visibility='visible';\n");
    echo("          document.getElementById('ValorAval').style.visibility='visible';\n");
    echo("          document.getElementById('TipoAtividadeAval').style.visibility='visible';\n");
    echo("          document.getElementById('ObjetivosAval').style.visibility='visible';\n");
    echo("          document.getElementById('CriteriosAval').style.visibility='visible';\n");
    echo("          if(div_hidden=='divAvaliacao')\n");
    echo("            document.getElementById('dadosAvaliacao').className='divHidden';\n");
    echo("          document.getElementById(div_hidden).className='divHidden';\n");
    echo("          document.getElementById('divAvaliacaoEdit').className='';\n");
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("          cancelarElemento=document.getElementById('cancelaAval');\n");
    echo("          document.getElementById('DataInicioAval').focus();\n");
    echo("      }\n\n");

    echo ("     function TestaEnterAvaliacao(campo, evento)\n");
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
    echo ("             EditaAvaliacao(1);\n"); //A funcao e parametros sao os mesmos utilizados na funcao de edicao ja utilizada.
    echo ("         }\n\n");
    echo ("         return true;\n");
    echo ("     }\n\n");

    echo("      function EditaAvaliacao(opt){\n");
    echo("          if (opt==1){\n");
    echo("            if (!TestaCamposAval()){\n");
    echo("              xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("              return false;\n");
    echo("            }\n");
    echo("            xajax_InsereAvaliacaoDinamic(document.getElementById('DataInicioAval').value, document.getElementById('DataTerminoAval').value, document.getElementById('ValorAval').value, document.getElementById('TipoAtividadeAval').value, document.getElementById('ObjetivosAval').value, document.getElementById('CriteriosAval').value, ".$cod_item.", ".$cod_curso.", ".$cod_usuario.");\n");
    echo("          }else{\n");
    echo("            if (!cancelarTodos)\n");
    echo("              xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("          }\n\n");

    echo("          document.getElementById('DataInicioAval').style.visibility='hidden';\n");
    echo("          document.getElementById('DataTerminoAval').style.visibility='hidden';\n");
    echo("          document.getElementById('ValorAval').style.visibility='hidden';\n");
    echo("          document.getElementById('TipoAtividadeAval').style.visibility='hidden';\n");
    echo("          document.getElementById('ObjetivosAval').style.visibility='hidden';\n");
    echo("          document.getElementById('CriteriosAval').style.visibility='hidden';\n");
    echo("          document.getElementById('divAvaliacaoEdit').className='divHidden';\n");

    /* Cancelamento de inclusão de avaliação */
    echo("          if (opt==0){\n");
    echo("            document.getElementById('DataInicioAval').value='".$hoje."';\n");
    echo("            document.getElementById('DataTerminoAval').value='".$hoje."';\n");
    echo("            document.getElementById('ValorAval').value='';\n");
    echo("            document.getElementById('TipoAtividadeAval').value='';\n");
    echo("            document.getElementById('ObjetivosAval').value='';\n");
    echo("            document.getElementById('CriteriosAval').value='';\n");
    echo("            document.getElementById('divAvaliacaoAdd').className='';\n\n");
    echo("          }\n");
    echo("          else\n");
    echo("          {\n");
    /* Inclusão de avaliação */
    echo("            if(opt==1)\n");
    echo("            {\n");
    echo("              document.getElementById('span_DataInicioAval').innerHTML=document.getElementById('DataInicioAval').value;\n");
    echo("              document.getElementById('span_DataTerminoAval').innerHTML=document.getElementById('DataTerminoAval').value;\n");
    echo("              document.getElementById('span_ValorAval').innerHTML=document.getElementById('ValorAval').value;\n");
    echo("              if(document.getElementById('TipoAtividadeAval').value == 'I')\n");
    /* 103 - Individual*/
    echo("                document.getElementById('span_TipoAtividadeAval').innerHTML='".RetornaFraseDaLista($lista_frases,103)."';\n");
    echo("              else\n");
    /* 104 - Em Grupo*/
    echo("                document.getElementById('span_TipoAtividadeAval').innerHTML='".RetornaFraseDaLista($lista_frases,104)."';\n");
    echo("              if(document.getElementById('ObjetivosAval').value == '')\n");
        /* 102 - Nao definidos*/
    echo("                document.getElementById('span_ObjetivosAval').innerHTML='".RetornaFraseDaLista($lista_frases,102)."';\n");
    echo("              else\n");
    echo("                document.getElementById('span_ObjetivosAval').innerHTML=document.getElementById('ObjetivosAval').value;\n");

    echo("              if(document.getElementById('CriteriosAval').value == '')\n");
        /* 102 - Nao definidos*/
    echo("                document.getElementById('span_CriteriosAval').innerHTML='".RetornaFraseDaLista($lista_frases,102)."';\n");
    echo("              else\n");
    echo("                document.getElementById('span_CriteriosAval').innerHTML=document.getElementById('CriteriosAval').value;\n");
    echo("            }\n");
    echo("            document.getElementById('divAvaliacao').className='';\n\n");
    echo("            document.getElementById('dadosAvaliacao').className='';\n\n");
    echo("          }\n");

    echo("          cancelarElemento=null;\n");
    echo("        }\n\n");

    echo("      function TestaCamposAval(){\n");
    echo("          return (TestaDatas() && (VerificaValor(document.getElementById('ValorAval').value)));\n");
    echo("      }\n");

    echo("      function ApagaAvaliacao(){\n");
    /* 91 - Deseja realmente apagar esta avaliação? (Esta atividade deixará de ser avaliação*/
    /*      e todos os dados referentes a avaliação realizada ficarão indisponíveis) */
    echo("        if (confirm('".RetornaFraseDaLista($lista_frases,91)."'))\n");
    echo("        {\n");
    echo("          xajax_ApagaAvaliacaoPortfolioDinamic(".$cod_item.",".$cod_curso.",".$cod_usuario.")\n");

    echo("          document.getElementById('DataInicioAval').style.visibility='hidden';\n");
    echo("          document.getElementById('DataTerminoAval').style.visibility='hidden';\n");
    echo("          document.getElementById('ValorAval').style.visibility='hidden';\n");
    echo("          document.getElementById('TipoAtividadeAval').style.visibility='hidden';\n");
    echo("          document.getElementById('ObjetivosAval').style.visibility='hidden';\n");
    echo("          document.getElementById('CriteriosAval').style.visibility='hidden';\n");
    echo("          document.getElementById('DataInicioAval').value='".$hoje."';\n");
    echo("          document.getElementById('DataTerminoAval').value='".$hoje."';\n");
    echo("          document.getElementById('ValorAval').value='';\n");
    echo("          document.getElementById('TipoAtividadeAval').value='I';\n");
    echo("          document.getElementById('ObjetivosAval').value='';\n");
    echo("          document.getElementById('CriteriosAval').value='';\n");
    echo("          document.getElementById('divAvaliacao').className='divHidden';\n");
    echo("          document.getElementById('dadosAvaliacao').className='divHidden';\n");
    echo("          document.getElementById('divAvaliacaoEdit').className='divHidden';\n");
    echo("          document.getElementById('divAvaliacaoAdd').className='';\n\n");

    echo("          cancelarElemento=null;\n");
    echo("        }\n");
    echo("      }\n");


    echo("      function CancelaTodos(){\n");
    echo("          EscondeLayers();\n");
    echo("          cancelarTodos=1;\n");
    echo("          if(cancelarElemento) { \n");
    echo("            cancelarElemento.onclick(); \n");
    echo("            xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", 0);\n");
    echo("          }\n");
    echo("          cancelarTodos=0;\n");
    echo("        }\n");

    echo("      function ApagarArquivo(conta_arq, arquivo){\n");
    echo("        CancelaTodos();\n");
    echo("        if (confirm('colocar texto')){\n");
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("          alert('ok2');xajax_ExcluirArquivo('".$tabela."', conta_arq, arquivo, '".$cod_curso."', '".$cod_item."', '".$cod_usuario."', '".RetornaFraseDaLista($lista_frases, 133)."');\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function ApagarEndereco(cod_curso, cod_endereco){\n");
    echo("        CancelaTodos();\n");
    /* 80 - Tem certeza que deseja apagar este endereço? */
    echo("        if (confirm('".RetornaFraseDaLista($lista_frases,80)."')){\n");
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("          xajax_ExcluirEndereco('".$tabela."', ".$cod_curso.", cod_endereco, ".$cod_item.", ".$cod_usuario.",'".RetornaFraseDaLista($lista_frases, 137)."');\n");
    echo("        }\n");
    echo("      }\n");

    echo("      function Descompactar(){\n");
    echo("        checks = document.getElementsByName('chkArq');\n");
    echo("        for (i=0; i<checks.length; i++){\n");
    echo("          if(checks[i].checked){\n");
    echo("            getNumber=checks[i].id.split(\"_\");\n");
    echo("            arqZip=document.getElementById('nomeArq_'+getNumber[1]).getAttribute('arqZip');\n");
    /* 64 - Você tem certeza de que deseja descompactar este arquivo? */
    /* 65 - (o arquivo ZIP será apagado)*/
    /* 66 - importante: não é possível a descompactação de arquivos contendo pastas com espaços no nome.*/
    echo("            if (confirm('".RetornaFraseDaLista($lista_frases,64)."\\n".RetornaFraseDaLista($lista_frases,65)."\\n".RetornaFraseDaLista($lista_frases,66)."')){\n");
    echo("              xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("              window.location='acoes.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_topico_raiz=".$cod_topico_raiz."&acao=descompactar&arq='+arqZip;\n");
  //   echo("alert(arqZip);\n");
    echo("            }\n");
    echo("          } \n");
    echo("        }\n");
    echo("      }\n");

    echo("      function VerificaChkBox(alpha){\n");
    echo("        CancelaTodos();\n");
    echo("        checks = document.getElementsByName('chkArq');\n");
    echo("        var i, j=0;\n");
    echo("        var arqComum=0;\n");
    echo("        var arqZip=0;\n");
    echo("        var arqOculto=0;\n");
    echo("        var pasta=0;\n\n");
    echo("		  var listaDir = '".$lista_diretorios."';\n");
    echo("		  var haDiretorios = listaDir.length;\n");

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
    echo("			if (haDiretorios>0){\n");
    echo("          	document.getElementById('mArq_mover').className=\"menuUp02\";\n");
    echo("			}\n");
    echo("			else{\n");
    echo("          	document.getElementById('mArq_mover').className=\"menuUp\";\n");
    echo("			}\n");
    echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

    echo("          document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
    echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
    echo("			if (haDiretorios>0){\n");
    echo("          	document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };\n");
    echo("			}\n");
    echo("			else{\n");
    echo("          	document.getElementById('sArq_mover').onclick= function(){  };\n");
    echo("			}\n");
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
    echo("			if (haDiretorios>0){\n");
    echo("          	document.getElementById('mArq_mover').className=\"menuUp02\";\n");
    echo("			}\n");
    echo("			else{\n");
    echo("          	document.getElementById('mArq_mover').className=\"menuUp\";\n");
    echo("			}\n");
    echo("          document.getElementById('mArq_descomp').className=\"menuUp02\";\n\n");

    echo("          document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
    echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
    echo("			if (haDiretorios>0){\n");
    echo("          	document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };\n");
    echo("			}\n");
    echo("			else{\n");
    echo("          	document.getElementById('sArq_mover').onclick= function(){  };\n");
     echo("			}\n");
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

    echo("      function Apagar(){\n");
    echo("        checks = document.getElementsByName('chkArq');\n");
    echo("        if (confirm('".RetornaFraseDaLista($lista_frases, 144)."')){\n");
    echo("          xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("          for (i=0; i<checks.length; i++){\n");
    echo("            if(checks[i].checked){\n");
    echo("              getNumber=checks[i].id.split('_');\n");
    echo("              nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('nomeArq');\n");
    echo("              xajax_ExcluirArquivo('".$tabela."', getNumber[1], nomeArq, ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", '".RetornaFraseDaLista($lista_frases, 133)."');\n");
    //echo("              js_conta_arq--;\n");
    echo("            }\n");
    echo("          }\n");
    echo("          LimpaBarraArq();\n");
    echo("          VerificaChkBox(0);\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function Ocultar(){\n");
    echo("        checks = document.getElementsByName('chkArq');\n");
    echo("        j=0;\n");
    echo("        var nomesArqs = new Array();\n\n");

    echo("        for (i=0; i<checks.length; i++){\n");
    echo("          if(checks[i].checked){\n\n");

    echo("            getNumber=checks[i].id.split(\"_\");\n");
    echo("            if ((document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('arqOculto'))=='nao'){\n");
    echo("              nomesArqs[j] = new Array();\n");

    echo("              nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('nomeArq');\n");
    echo("              nomesArqs[j][0]=nomeArq;\n");
    echo("              nomesArqs[j][1]=getNumber[1];\n");
    echo("              j++;\n");
    echo("            }\n\n");

    echo("          }\n");
    echo("        }\n");

    echo("        xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("        xajax_OcultarArquivosDinamic('".$tabela."', nomesArqs, '".RetornaFraseDaLista($lista_frases,87)."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", '".RetornaFraseDaLista($lista_frases,134)."');\n");
    echo("      }\n\n");

    echo("      function Desocultar(){\n");
    echo("        checks = document.getElementsByName('chkArq');\n");
    echo("        j=0;\n");
    echo("        var nomesArqs = new Array();\n\n");

    echo("        for (i=0; i<checks.length; i++){\n");
    echo("          if(checks[i].checked){\n");
    echo("            getNumber=checks[i].id.split(\"_\");\n");
    echo("            if ((document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('arqOculto'))=='sim'){\n\n");

    echo("              nomesArqs[j] = new Array();\n");
    echo("              nomeArq = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('nomeArq');\n");
    echo("              nomesArqs[j][0]=nomeArq;\n");
    echo("              nomesArqs[j][1]=getNumber[1];\n");
    echo("              j++;\n");
    echo("            }\n");
    echo("          }\n");
    echo("        }\n");
    echo("        xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("        xajax_DesocultarArquivosDinamic('".$tabela."', nomesArqs, ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", '".RetornaFraseDaLista($lista_frases,135)."');\n");
    echo("      }\n");

    echo("      function CheckTodos(){\n");
    echo("        var e;\n");
    echo("        var i;\n");
    echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
    echo("        var checks=document.getElementsByName('chkArq');\n");
    echo("        for(i = 0; i < checks.length; i++)\n");
    echo("        {\n");
    echo("          e = checks[i];\n");
    echo("          e.checked = CabMarcado;\n");
    echo("        }\n\n");
    echo("        VerificaChkBox(0);\n");
    echo("      }\n");

    echo("      function Mover(caminhoDestino){\n");
    echo("        checks = document.getElementsByName('chkArq');\n");
    echo("        for (i=0; i<checks.length; i++){\n");
    echo("          if(checks[i].checked){\n");
    echo("            numeroArq= checks[i].getAttribute('id');\n");
    echo("            numeroArq = numeroArq.split('_');\n");
    echo("            IdArquivo = 'nomeArq_'+numeroArq[1];\n");
    echo("            caminhoOrigem = document.getElementById(IdArquivo).getAttribute('nomeArq');\n");
    echo("            xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("            xajax_MoverArquivosDinamic(caminhoOrigem, caminhoDestino, ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_ferramenta.", ".$cod_topico_raiz.",'".$tabela."');\n");
    echo("          }\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function LimpaBarraArq(){\n");
    echo("        lista = document.getElementById('listFiles');\n");
    echo("        if (!js_conta_arq){\n");
    echo("          pai_lista=lista.parentNode;\n");
    echo("          pai_lista2=pai_lista.parentNode;\n");
    echo("          i=3;\n");
    echo("          do{\n");
    echo("            if (pai_lista.firstChild)\n");
    echo("              pai_lista.removeChild(pai_lista.firstChild);\n");
    echo("            i--;\n");
    echo("          }while(i>0);\n");
    echo("        }\n");
    echo("        document.getElementById('checkMenu').checked=false;\n");
    echo("        CheckTodos();\n");
    echo("      }\n");

    echo("      function AssociarAvaliacao(){\n");
    echo("        CancelaTodos();\n");
    echo("        radios=document.getElementsByName('cod_avaliacao');\n");
    echo("        for (i=0; i<radios.length; i++){\n");
    echo("          valor_radios[i]=radios[i].checked;\n");
    echo("        }\n");
    echo("        document.getElementById('tableAvaliacao').style.visibility='visible';\n");
    echo("        document.getElementById('divAvaliacao').className='divHidden';\n");
    echo("        document.getElementById('divAvaliacaoEdit').className='';\n");

    echo("        xajax_AbreEdicao('".$tabela."', ".$cod_curso.", ".$cod_item.", ".$cod_usuario.", ".$cod_topico_raiz.");\n");
    echo("        cancelarElemento=document.getElementById('cancAval');\n");
    echo("      }\n\n");

    echo("      function DesmarcaRadios(){\n");
    echo("        radios = document.getElementsByName('cod_avaliacao');\n");
    echo("        for (i=0; i<radios.length; i++){\n");
    echo("          radios[i].checked=false;\n");
    echo("        }\n");
    echo("        cod_avaliacao='';\n");
    echo("      }\n");
  }else{
    echo("        function Iniciar(){\n");
    echo("          startList();\n");
    echo("        }\n");
  }

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    /* Pagina Principal */
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigatoria
   */
   /* 84 - Ver Atividade */
  $cabecalho =RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,84);
  
  echo("          <h4>".$cabecalho."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $tabela, $cod_topico_raiz);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha){
    if ($cod_topico_raiz!=$linha['cod_topico']){
      $path="<a href=\"material.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$linha['cod_topico']."\">".$linha['topico']."</a> &gt;&gt; ".$path;
    }
    else{
      $path="<a href=\"material.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$linha['cod_topico']."\">".$linha['topico']."</a><br />\n";
    }
  }
  if($eformador){
    echo("          <span onclick=\"MostraLayer(lay_topicos,0, event);return(false);\"><img src=\"../imgs/estrutura.gif\" border=\"0\" alt=\"\"/></span>");
   echo("          ".$path);
}
 else{
  echo("          <img src=\"../imgs/estrutura.gif\" border=\"0\" alt=\"\"/>");
  echo("          ".$path);
 }
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("            <!--  Botoes de Acao  -->\n");
  echo("              <td class=\"btAuxTabs\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  /* 29 - Atualizar */
  echo("                  <li><a href=\"ver.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_item=".$cod_item."\">".RetornaFraseDaLista($lista_frases,29)."</a></li>\n");

  /* 30 - Ver Outros Itens */
  echo("                  <li><a href=\"material.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$cod_topico_raiz."\">".RetornaFraseDaLista($lista_frases,30)."</a></li>\n");

  if($eformador){
    /* 25 - Mover */
    echo("                  <li><span onclick=\"js_tipo_item='item';MostraLayer(cod_mover,0);return(false);\">".RetornaFraseDaLista($lista_frases_geral,25)."</span></li>\n");
  
    /* 1 - Apagar */
    echo("                  <li><span onclick=\"ApagarMaterial();\">".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");
  }
  
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
    
  /* 35 - Titulo */
  echo("                    <td width=\"\" class=\"itens\">".RetornaFraseDaLista($lista_frases,35)."</td>\n");

  if($eformador){
  /* 70 - Opções */
    echo("                    <td width=\"14%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
  }

  /* 13 - Data */
  echo("                    <td width=\"14%\" align=\"center\">".RetornaFraseDaLista($lista_frases,13)."</td>\n");
  if ($eformador){
    /* 14 - Compartilhar */
    echo("                    <td width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases,14)."</td>\n");
  }
  echo("                  </tr>\n");

  $linha_item=RetornaDadosDoItem($sock, $tabela, $cod_item);

  if ($linha_item['tipo_compartilhamento']=="T") {
    /* 16 - Totalmente Compartilhado */
    $compartilhamento=RetornaFraseDaLista($lista_frases,16);
  }
  else {
    /* 17 - Compartilhado com Formadores */
    $compartilhamento=RetornaFraseDaLista($lista_frases,17);
  }

  $compartilhamento="<span id=\"comp_".$linha_item['cod_item']."\" class=\"link\" onclick=\"js_cod_pagina=2;js_cod_item='".$linha_item['cod_item']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140);\">".$compartilhamento."</span>";

  $titulo=$linha_item['titulo'];

	
  if($eformador) {
    $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
    /* 146 - Renomear titulo */
    $renomear="<span onclick=\"AlteraTitulo(".$linha_item['cod_item'].")\" id=\"renomear_".$linha_item['cod_item']."\">".RetornaFraseDaLista($lista_frases,146)."</span>";
    /* 120 - Editar texto */
    $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].")\">".RetornaFraseDaLista($lista_frases,120)."</span>";
    /* 121 - Limpar texto */
    $limpar="<span onclick=\"LimpaTexto(".$linha_item['cod_item'].");\">".RetornaFraseDaLista ($lista_frases, 121)."</span>";
  }

  echo("                  <tr id='tr_".$linha_item['cod_item']."'>\n");
  echo("                    <td class=\"itens\">".$titulo."</td>\n");
  
  if($eformador) {
  
    echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
    echo("                      <ul>\n");
    echo("                        <li>".$renomear."</li>\n");
    echo("                        <li>".$editar."</li>\n");
    echo("                        <li>".$limpar."</li>\n");
    echo("                      </ul>\n");
    echo("                    </td>\n");
  
  }
  
  echo("                    <td align=\"center\">\n");
  echo("                      <span id=data_".$linha_item['cod_item'].">".UnixTime2DataHora($linha_item['data'])."</span>\n");

  echo("                    </td>\n");
  
  if($eformador){
    echo("                    <td align=\"center\">".$compartilhamento."</td>\n");
  }

  echo("                   </tr>\n");
  if (($eformador) || ($linha_item['texto']!=""))
  {
    echo("                  <tr class=\"head\">\n");
    /* 31 - Texto  */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,31)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td colspan=\"4\" class=\"itens divRichText\">\n");
    $texto="<span id=\"text_".$linha_item['cod_item']."\">".AjustaParagrafo($linha_item['texto'])."</span>";
    echo("                      <div class=\"divRichText\">".$texto."</div>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  //tirei:
  //$diretorio = $diretorio_arquivos."/".$cod_curso."/".$dir."/".$cod_item;
  //$lista_arq=RetornaArquivosMaterialVer($cod_curso, $diretorio);
  //e coloquei:

  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);
  
  $num_arq_vis=RetornaNumArquivosVisiveis($lista_arq);

  if (($num_arq_vis>0) || ($eformador))
  {
    echo("                  <tr class=\"head\">\n");
    /* 32 - Arquivos */
    echo("                    <td colspan=4>".RetornaFraseDaLista($lista_frases,32)."</td>\n");
    echo("                  </tr>\n");

    if (is_array($lista_arq) && count($lista_arq)>0){

      $conta_arq=0;

      echo("                  <tr>\n");
      echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
      // Procuramos na lista de arquivos se existe algum visivel
      $ha_visiveis = $num_arq_vis > 0;


      if (($ha_visiveis) || ($eformador)){

        $nivel_anterior=0;
        $nivel=-1;

        foreach($lista_arq as $cod => $linha){
          $linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
          if (!($linha['Arquivo']=="" && $linha['Diretorio']=="")){
            if ((!$linha['Status']) || ($eformador)){
              $nivel_anterior=$nivel;
              $espacos="";
              $espacos2="";
              $temp=explode("/",$linha['Diretorio']);
              $nivel=count($temp)-1;
              for ($c=0;$c<=$nivel;$c++){
              if($eformador) {
                $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                $espacos2.="  ";
              }
              else{
                $espacos.="";
                $espacos2.="";
              }
            }

              $caminho_arquivo = $dir_item_temp['link'].$linha['Diretorio']."/".$linha['Arquivo'];
              $caminho_arquivo = preg_replace("/\/\//", "/", $caminho_arquivo);
// echo($caminho_arquivo);

              if ($linha['Arquivo'] != ""){
                if ($linha['Diretorio']!=""){
                  $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                  $espacos2.="  ";
                }

                if ($linha['Status']) $arqOculto="arqOculto='sim'";
                else $arqOculto="arqOculto='nao'";

                if (eregi(".zip$",$linha['Arquivo'])){
                  // arquivo zip
                  $imagem    = "<img src=\"../imgs/arqzip.gif\" border=0 alt=\"\"/>";
                  $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."');return false;\" tipoArq=\"zip\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
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
                  $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return false;\" tipoArq=\"comum\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" ".$arqOculto.">";
                }

                $tag_fecha = "</a>";

                echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

                if ($eformador){
                  echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                }

                echo("                          ".$espacos2.$espacos.$imagem." ".$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb)");

                echo("<span id=\"local_oculto_".$conta_arq."\">");
                if ($linha['Status']){
                  // 87 - Oculto
                    echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style='color:red;'>".RetornaFraseDaLista($lista_frases,87)."</span></span>");
                }
                echo("</span>\n");
                echo("                          ".$espacos2."<br />\n");
                echo("                        ".$espacos2."</span>\n");
              }

              else if (($eformador) || (haArquivosVisiveisDir($linha['Diretorio'], $lista_arq))){
                if ($nivel_anterior>=$nivel){
                  $i=$nivel_anterior-$nivel;
                  $j=$i;
                  $espacos3="";
                  do{
                    $espacos3.="  ";
                    $j--;
                  }while($j>=0);

                  while($i>=0){
                    echo("                      ".$espacos3."</span>\n");
                    $i--;
                  }
                }
                // pasta
                $imagem    = "<img src=\"../imgs/pasta.gif\" border=0 alt=\"\" />";
                echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
                if ($eformador){
                  echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                }
                echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
                echo("                        ".$espacos2."<br />\n");
             }
            }
          }
          $conta_arq++;
        }
        do{
          $j=$nivel;
          $espacos3="";
          do{
            $espacos3.="  ";
            $j--;
          }while($j>=0);
          echo("                      ".$espacos3."</span>\n");
          $nivel--;
        }while($nivel>=0);
      }
      echo("                      <script type=\"text/javascript\" language=\"JavaScript\">js_conta_arq=".$conta_arq.";</script>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }

    if ($eformador){
      echo("                  <tr>\n");
      echo("                    <td align=\"left\" colspan=\"4\">\n");
      echo("                      <ul>\n");
      echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_mover\"><span id=\"sArq_mover\">".RetornaFraseDaLista ($lista_frases_geral, 25)."</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">".RetornaFraseDaLista ($lista_frases_geral, 38)."</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_ocultar\"><span id=\"sArq_ocultar\">".RetornaFraseDaLista ($lista_frases_geral, 511)."</span></li>\n");
      echo("                      </ul>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
      echo("                  <tr>\n");
      echo("                    <td align=\"left\" colspan=\"4\">\n");
      echo("                      <form name=\"formFiles\" id=\"formFiles\" action=\"acoes.php\" method=\"post\"  enctype=\"multipart/form-data\">\n");
      echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
      echo("                        <input type=\"hidden\" name=\"cod_item\" value=\"".$cod_item."\" />\n");
      echo("                        <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
      echo("                        <input type=\"hidden\" name=\"acao\" value=\"anexar\" />\n");
      echo("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
      echo("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=0 />\n");
      echo("                          <span class=\"destaque\">".RetornaFraseDaLista ($lista_frases_geral, 26)."</span>\n");
      echo("                          <span> - ".RetornaFraseDaLista ($lista_frases, 59).RetornaFraseDaLista ($lista_frases, 60)."</span>\n");
      echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
      echo("                          <input class=\"input\" type=\"file\" id=\"input_files\" name=\"input_files\" onchange=\"EdicaoArq(1);\" style=\"border:2px solid #9bc\" />\n");
      //echo("                          &nbsp;&nbsp;\n");
      //echo("                          <span onclick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 18)."</span>\n");
      //echo("                          &nbsp;&nbsp;\n");
      //echo("                          <span onclick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 2)."</span>\n");
      echo("                        </div>\n");
                                    /* 26 - Anexar arquivos (ger) */
      echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">".RetornaFraseDaLista($lista_frases_geral,26)."</span></div>\n");
      echo("                      </form>\n");

    }
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  $lista_url=RetornaEnderecosMaterial($sock, $tabela, $cod_item);

   if ((!empty($lista_url))||($eformador)){
    echo("                  <tr class=\"head\">\n");
      /* 33 - Endereços */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,33)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");

    if (!empty($lista_url)>0){
      foreach ($lista_url as $cod => $linha){
        $linha['endereco'] = RetornaURLValida($linha['endereco']);
        echo("                      <span id='end_".$linha['cod_endereco']."'>\n");

        if ($linha['nome']!=""){
          echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['nome']."</span>&nbsp;&nbsp;(".$linha['endereco'].")");
        }
        else{
          echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['endereco']."</span>");
        }

        if($eformador){
          /* (gen) 1 - Apagar */
          echo(" - <span class=\"link\" onclick=\"ApagarEndereco('".$cod_curso."', '".$linha['cod_endereco']."');\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span>\n");
        }
        echo("                        <br />\n");
        echo("                      </span>\n");
      }
    }

    echo("                    </td>\n");
    echo("                  </tr>\n");

    if ($eformador){
      echo("                  <tr>\n");
      echo("                    <td colspan=\"4\" align=\"left\" id=\"tdIncluirEnd\">\n");
      /* 51 - Incluir Endereço */
      echo("                      <div id=\"divEndereco\"><img alt=\"\" src=\"../imgs/url.jpg\" border=0 /> <span id=\"incluiEnd\" class=\"link\" onclick=\"AdicionaInputEndereco();\">".RetornaFraseDaLista($lista_frases,51)."</span></div>\n");
      echo("                      <div id=\"divEnderecoEdit\" class=\"divHidden\">\n");
      echo("                        <img alt=\"\" src=\"../imgs/url.jpg\" border=0 />\n");
      echo("                        <span id=\"incluiEnd\" class=\"destaque\">".RetornaFraseDaLista($lista_frases,51)."</span>\n");
      echo("                        <span> - ".RetornaFraseDaLista($lista_frases,67)."</span>\n");
      echo("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
      echo("                        <span class=\"destaque\">".RetornaFraseDaLista($lista_frases,35)."</span><br />\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <input class=\"input\" type=\"text\" style=\"border:2px solid #9bc\" name=\"novoNomeEnd\" id=\"novoNomeEnd\" onkeypress=\"TestaEnterEndereco(this, event);\" size=30 />\n");
      echo("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <span class=\"destaque\">".RetornaFraseDaLista($lista_frases,68)."</span><br />\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <input class=\"input\" type=\"text\" style=\"border:2px solid #9bc\" name=\"novoEnd\" id=\"novoEnd\" onkeypress=\"TestaEnterEndereco(this, event);\" size=30 />\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <span class=\"link\" onclick=\"EditaEndereco(1);\">".RetornaFraseDaLista($lista_frases_geral,18)."</span>\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <span class=\"link\" id=\"cancelaEnd\" onclick=\"EditaEndereco(0);\">".RetornaFraseDaLista($lista_frases_geral,2)."</span><br />\n");
      echo("                      </div>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
      }
  }

  if (($eformador) && ($cod_ferramenta==3) && ($AcessoAvaliacaoM))
  {
    echo("                  <tr class='head'>\n");
    /* 90 - Avalia�o */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,90)."</td>\n");
    echo("                  </tr>\n");

    $lista=RetornaAvaliacaoPortfolio($sock,'Avaliacao', $cod_item);
    if (count($lista)>0)
    {
      if (!strcmp($lista['tipo'],'G'))
      {
        /* 104 - Em Grupo*/
        $tipo=RetornaFraseDaLista($lista_frases,104);
        $tipo_grupo=true;
      }
      else
      {
        /* 103 - Individual*/
        $tipo=RetornaFraseDaLista($lista_frases,103);
        $tipo_grupo=false;
      }

      if (!strcmp($lista['objetivos'],''))
        /* 102 - Nao definidos*/
        $span_objetivos=RetornaFraseDaLista($lista_frases,102);
      else
        $span_objetivos=$lista['objetivos'];
      if (!strcmp($lista['criterios'],''))
        /* 102 - Nao definidos*/
        $span_criterios=RetornaFraseDaLista($lista_frases,102);
      else
        $span_criterios=$lista['criterios'];

      $opt_cancelar=2;
      $class_avaliacao_add = "class=\"divHidden\"";

      $data_inicio = Unixtime2Data($lista['data_inicio']);
      $data_termino = Unixtime2Data($lista['data_termino']);
    }
    else
    {
      $opt_cancelar=0;
      $class_avaliacao = "class=\"divHidden\"";

      $data_inicio = $hoje;
      $data_termino= $hoje;
    }

    echo("                  <tr ".$class_avaliacao.">\n");
    echo("                    <td align=\"left\" colspan=\"4\" id=\"dadosAvaliacao\">\n");
    echo("                      <ul class=\"btAuxTabs\">\n");
    /* 126 - Alterar Avaliação */
    echo("                        <li><span onclick=\"AdicionaInputAvaliacao('divAvaliacao');\">".RetornaFraseDaLista($lista_frases,92)."</span></li>\n");
    /* 127 - Apagar Avaliação */
    echo("                        <li><span onclick=\"ApagaAvaliacao();\">".RetornaFraseDaLista($lista_frases,93)."</span></li>\n");
    echo("                      </ul>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");

    echo("                  <tr>\n");
    echo("                    <td colspan=\"4\" align=\"left\" >\n");

    echo("                      <div id=\"divAvaliacao\" ".$class_avaliacao.">\n");
    echo("                        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n");
    echo("                          <tr>\n");
    /* 95 - Data de Início */
    echo("                            <td width=\"20%\" align=\"right\"><b>".RetornaFraseDaLista($lista_frases,95)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_DataInicioAval\">".$data_inicio."</span></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
     /* 96 - Data de Término */
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,96)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_DataTerminoAval\">".$data_termino."</span></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 97 - Valor */
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,97)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_ValorAval\">".$lista['valor']."</span></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
     /* 98 - Tipo da Atividade*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,98)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_TipoAtividadeAval\">".$tipo."</span></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 99 - Objetivos*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,99)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_ObjetivosAval\">".$span_objetivos."</span></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 100 - Critérios*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,100)."</b></td>\n");
    echo("                            <td align=\"left\"><span id=\"span_CriteriosAval\">".$span_criterios."</span></td>\n");
    echo("                          </tr>\n");
    echo("                        </table>\n");
    echo("                      </div>\n");

    echo("                      <div id=\"divAvaliacaoAdd\" ".$class_avaliacao_add.">\n");
    /* 94 - Incluir Avaliação */
    echo("                        <img alt=\"\" src=\"../imgs/portfolio/lapis.gif\" border=0 /> <span id=\"incluiAval\" class=\"link\" onclick=\"AdicionaInputAvaliacao('divAvaliacaoAdd');\">".RetornaFraseDaLista($lista_frases,94)."</span>\n");
    echo("                      </div>\n");
    echo("                      <div id=\"divAvaliacaoEdit\" class=\"divHidden\">\n");
    echo("                        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n");
    echo("                          <tr>\n");
    /* 95 - Data de Início */
    echo("                            <td width=\"15%\" align=\"right\"><b>".RetornaFraseDaLista($lista_frases,95)."</b></td>\n");
    echo("                            <td align=\"left\">\n");
    echo("                              <input class=\"input\" type=\"text\" name=\"DataInicioAval\" id=\"DataInicioAval\" onkeypress=\"TestaEnterAvaliacao(this, event);\" size=\"10\" value=\"".$data_inicio."\" />\n");
    echo("                              <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('DataInicioAval'),'dd/mm/yyyy',this);\"/>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 96 - Data de Término */
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,96)."</b></td>\n");
    echo("                            <td align=\"left\">\n");
    echo("                              <input class=\"input\" type=\"text\" name=\"DataTerminoAval\" id=\"DataTerminoAval\" onkeypress=\"TestaEnterAvaliacao(this, event);\" size=\"10\" value=\"".$data_termino."\" />\n");
    echo("                              <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('DataTerminoAval'),'dd/mm/yyyy',this);\"/>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 97 - Valor */
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,97)."</b></td>\n");
    echo("                            <td align=\"left\"><input class=\"input\" type=\"text\" name=\"ValorAval\" id=\"ValorAval\" onkeypress=\"TestaEnterAvaliacao(this, event);\" size=3 value=\"".$lista['valor']."\"/></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 98 - Tipo da Atividade*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,98)."</b></td>\n");
    echo("                            <td align=\"left\">\n");
    echo("                              <select class=\"input\" name=\"TipoAtividadeAval\" id=\"TipoAtividadeAval\" style=\"width:102px;\"/>\n");
    /* 103 - Individual */
    echo("                                <option value=\"I\" ".((!$tipo_grupo) ? "selected=\"selected\"" : "")."/>".RetornaFraseDaLista($lista_frases,103)."</option>\n");
    /* 104 - Em grupo */
    echo("                                <option value=\"G\" ".( ($tipo_grupo) ? "selected=\"selected\"" : "")."/>".RetornaFraseDaLista($lista_frases,104)."</option>\n");
    echo("                              </select/>\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 99 - Objetivos*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,99)."</b></td>\n");
    echo("                            <td align=\"left\"><textarea class=\"input\" name=\"ObjetivosAval\" id=\"ObjetivosAval\" cols=36 rows=5 />".$lista['objetivos']."</textarea></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 100 - Critérios*/
    echo("                            <td align=\"right\"><b>".RetornaFraseDaLista($lista_frases,100)."</b></td>\n");
    echo("                            <td align=\"left\"><textarea class=\"input\" name=\"CriteriosAval\" id=\"CriteriosAval\" cols=36 rows=5 />".$lista['criterios']."</textarea></td>\n");
    echo("                          </tr>\n");
    echo("                          <tr>\n");
    /* 100 - Critérios*/
    echo("                            <td></td>\n");
    echo("                            <td align=\"left\">\n");
    /* 18 (gn) - Ok */
    echo("                              <span class=\"link\" onclick=\"EditaAvaliacao(1);\">".RetornaFraseDaLista($lista_frases_geral,18)."</span>\n");
    echo("                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
    /* 2 (gn) - Cancelar */
    echo("                              <span class=\"link\" id=\"cancelaAval\" onclick=\"EditaAvaliacao(".$opt_cancelar.");\">".RetornaFraseDaLista($lista_frases_geral,2)."</span><br >\n");
    echo("                            </td>\n");
    echo("                          </tr>\n");
    echo("                        </table>\n");
    echo("                      </div>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
    echo("          <br />\n");
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");

  if($eformador) {
    include("layer_material.php");
  }

  echo("  </body>\n");
  echo("</html>\n");
?>

