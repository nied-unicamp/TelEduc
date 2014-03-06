<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver.php
  ========================================================== */
/* TODO - Adicionar feedback do alterar periodo */
  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"EditarTexto");
  $objAjax->register(XAJAX_FUNCTION,"EditarValor");
  $objAjax->register(XAJAX_FUNCTION,"EditarTitulo");
  $objAjax->register(XAJAX_FUNCTION,"AlterarPeriodoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseGeralDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AbreEdicao");
  $objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AlertaFraseFerramenta");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=22;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=5;
  include("../topo_tela.php");

  $lista_frases_biblioteca =RetornaListaDeFrases($sock,-2);
  // Verifica se o usuario eh formador.
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);
  // Guarda dados da avaliaÃ§Ã£o atual
  $dados_avaliacao = RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro Ã© a aÃ§Ã£o, o segundo Ã© o nÃºmero da frase para ser impressa se for "true", o terceiro caso "false"
  /* Frase #212 - Avaliação criada com sucesso */
  $feedbackObject->addAction("criarAvaliacao", 212, 0);
  /* Frase #235 - Exercicio aplicado com sucesso */
  $feedbackObject->addAction("aplicar", RetornaFraseDaLista($lista_frases, 235), 0);
  /* Frase #236 - Exercicio reaplicado com sucesso */
  $feedbackObject->addAction("reaplicar", RetornaFraseDaLista($lista_frases, 236), 0);

  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");

  GeraJSVerificacaoData();
  GeraJSComparacaoDatas();

  echo("    <script type=\"text/javascript\">\n");

  echo("      var cod_curso='".$cod_curso."';\n");
  echo("      var cod_usuario='".$cod_usuario."';\n");
  echo("      var ferramenta='".$dados_avaliacao['Ferramenta']."';\n");
  echo("      var cod_atividade='".$dados_avaliacao['Cod_atividade']."';\n");
  echo("      var cod_avaliacao='".$cod_avaliacao."';\n");
  echo("      var tela_avaliacao='".$tela_avaliacao."';\n");
  /* (ger) 18 - Ok */
  // Texto do botão Ok do ckEditor
  echo("    var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do botão Cancelar do ckEditor
  echo("    var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  echo("      function startList() {\n");
  echo("        if (document.all && document.getElementById) {\n");
  echo("          nodes = document.getElementsByTagName(\"span\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("          nodes = document.getElementsByTagName(\"li\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n\n"); 

  echo("      function Valida()\n");
  echo("      {\n");
  echo("        hoje = '".UnixTime2Data(time())."';\n\n");
  echo("        if (ComparaData(document.getElementById('data_inicio'), document.getElementById('data_fim')) > 0)\n");
  echo("        {\n");
  //31(biblioteca) - PerÃ­odo InvÃ¡lido! 
  echo("          alert('".RetornaFraseDaLista($lista_frases_biblioteca, 31)."!');\n");
  echo("          document.frmAlteraPeriodo.data_inicio.value = document.frmAlteraPeriodo.data_fim.value;\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        CancelaTodos();\n");
  echo("        xajax_AlterarPeriodoDinamic(xajax.getFormValues('frmAlteraPeriodo'));\n");
  echo("      }\n\n");

  if ($SalvarEmArquivo)
  {
    echo("    <style>\n");
    include "../js-css/ambiente.css";
    include "../js-css/tabelas.css";
    include "../js-css/navegacao.css";
    echo("    </style>\n");
  }
  else
  {
    $avaliacao_participante = VerificaAvalicaoParticipantes($sock ,$cod_avaliacao);

    if ($usr_formador)
    {
      echo("      function Historico()\n");
      echo("      {\n");
      $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
      $nome_janela = "'AvaliacoesHistorico'";
      echo("        window.open('historico_avaliacoes.php?&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&tipo_ferramenta=N',".$nome_janela.", ".$param.");\n");
      echo("        return false; \n");
      echo("      }\n");

      echo("      function ExcluirAvaliacao()\n");
      echo("      {\n");
      /* 129 - Vocï¿½ tem certeza de que deseja excluir esta avaliaï¿½ï¿½o? */
      /* 130 - (a avaliaï¿½ï¿½o serï¿½ excluï¿½da definitivamente) */
      echo("        if(confirm('".RetornaFraseDaLista($lista_frases,129).RetornaFraseDaLista($lista_frases,130)."'))\n");
      echo("        {\n");
      echo("          document.frmAvaliacao.action = 'acoes.php';\n");
      echo("          document.frmAvaliacao.acao.value= 'excluirAvaliacao';\n");
      echo("          document.frmAvaliacao.submit();\n");
      echo("        }\n");
      echo("      }\n\n");

    }

    echo("      function AvaliarParticipantes()\n");
    echo("      {\n");
    echo("        document.frmAvaliacao.action = 'avaliar_participantes.php';\n");
    echo("        document.frmAvaliacao.submit();\n");
    //echo("    return false;\n");
    echo("      }\n");

  }

  echo("      function ImprimirRelatorio()\n");
  echo("      {\n");
  echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("        {\n");
  echo("          self.print();\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
    // 51 (gen)- Infelizmente nï¿½o foi possï¿½vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
  echo("          alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("        }\n");
  echo("      }\n");


  // retorna true se a nota contiver digitos estranhos
  // retorna false se a nota estiver no formato adequado
  echo("        function nota_com_digito_estranho(nota) {\n");
  echo("          re_com_virgula = /^[0-9]+(\.|,)?[0-9]+\$/; \n"); // nota com decimal
  echo("          re_somente_numeros = /^[0-9]+\$/; \n"); // somente numeros
  echo("          if (nota == '' || re_com_virgula.test(nota) || re_somente_numeros.test(nota) ) { \n");
  echo("            return false;\n");
  echo("          } else {\n");
  echo("            return true;\n");
  echo("          }\n");
  echo("        }\n");

  echo("        function VerificaNota(tag,nota) \n");
  echo("        { \n");
  // se estiver alterando titulo, naum precisa verificar como se fosse nota
  echo("          if (tag == 'tit')\n");
  echo("            return true; \n");  
  // echo("  var comp = document.avaliado.compartilhamento.value; \n");
  echo("          if (nota == '') { \n");
  // 40 - O campo nota nï¿½o pode ser vazio
  echo("            alert('".RetornaFraseDaLista($lista_frases,40)."'); \n");
  echo("            return false; \n");
  echo("          } \n");
  echo("          if (nota_com_digito_estranho(nota)) { \n");
  // 5 - Vocï¿½ digitou caracteres estranhos nesta nota.
  // 6 - Use apenas dï¿½gitos de 0 a 9 e o ponto ( . ) ou a vï¿½rgula ( , ) para o campo valor (exemplo: 7.5). \n");
  // 7 - Por favor retorne e corrija.
  echo("             alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."'); \n");
  echo("            return false; \n");
  echo("          } \n");
  // verificamos se a nota tem virgula, se tiver, convertemos para ponto
  echo("          nota = nota.replace(/\,/, '.'); \n");
  // 24 - A nota nï¿½o pode ser negativa
  echo("          if (nota < 0) { \n");
  echo("            alert('".RetornaFraseDaLista($lista_frases,24)."'); \n");
  echo("            return false; \n");
  echo("          }  \n");
  echo("          return true;\n");
  echo("        }  \n");
  
  
  echo ("     function EditaTituloEnter(campo, evento, id, tag)\n");
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
  echo ("         if ( tecla == 13 )\n"); //13 = enter
  echo ("         {\n");
  echo ("             EdicaoCampo(id, tag, 'ok');\n");
  echo ("         }\n\n");
  echo ("         return true;\n");
  echo ("     }\n\n");
  
  echo("	function AlteraCampo(tag,id){\n");
  echo("    	if (editaTitulo==0){\n");
  echo("		id_aux = id;\n");
  echo("		tag_aux = tag;\n");
  echo("		CancelaTodos();\n");

  echo("		xajax_AbreEdicao(".$cod_curso.", ".$cod_avaliacao.", ".$cod_usuario.", '".$tela_avaliacao."');\n");

  echo("		conteudo = document.getElementById(tag+'_'+id).innerHTML;\n");
  echo("		document.getElementById(tag+'_'+id).className=\"\";\n");
  echo("		document.getElementById('tr_'+id).className=\"\";\n");

  echo("		createInput = document.createElement('input');\n");
  echo("		document.getElementById(tag+'_'+id).innerHTML='';\n");
  //echo("      document.getElementById(tag+'_'+id).onclick=function(){ };\n");
  echo("		document.getElementById(tag+'_'+id).setAttribute('onclick', '');\n");

  echo("		/*cria o campo para digitar o campo*/\n");
  echo("		createInput.setAttribute('type', 'text');\n");
  echo("		createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
  echo("		createInput.setAttribute('id', tag+'_'+id+'_text');\n");
  echo("		createInput.setAttribute('onkeypress', 'EditaTituloEnter(this, event, id_aux, tag_aux)');\n");
  echo("		createInput.setAttribute('value', conteudo);\n");
  
  echo("		if(tag == 'valor')\n");
  echo("			createInput.setAttribute('size','8%');\n");

  echo("		document.getElementById(tag+'_'+id).appendChild(createInput);\n");

  echo("		/*cria o elemento 'espaco' e adiciona na pagina*/\n");
  echo("		espaco = document.createElement('span');\n");
  echo("		espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("		document.getElementById(tag+'_'+id).appendChild(espaco);\n");

  echo("		createSpan = document.createElement('span');\n");
  echo("		createSpan.className='link';\n");
  echo("		createSpan.onclick= function(){ EdicaoCampo(id, tag, 'ok'); };\n"); //TODO
  echo("		createSpan.setAttribute('id', 'OkEdita');\n");
  echo("		createSpan.innerHTML = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n");
  echo("		document.getElementById(tag+'_'+id).appendChild(createSpan);\n");

  echo("		/*cria o elemento 'espaco' e adiciona na pagina*/\n");
  echo("		espaco = document.createElement('span');\n");
  echo("		espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("		document.getElementById(tag+'_'+id).appendChild(espaco);\n");
  
  echo("		createSpan = document.createElement('span');\n");
  echo("		createSpan.className='link';\n");
  echo("		createSpan.onclick= function(){ EdicaoCampo(id, tag, 'canc'); };\n"); //TODO
  echo("		createSpan.setAttribute('id', 'CancelaEdita');\n");
  echo("		createSpan.innerHTML = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n");
  echo("		document.getElementById(tag+'_'+id).appendChild(createSpan);\n");

  echo("		/*cria o elemento 'espaco' e adiciona na pagina*/\n");
  echo("		espaco = document.createElement('span');\n");
  echo("		espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("		document.getElementById(tag+'_'+id).appendChild(espaco);\n");

  echo("		startList();\n");
  echo("		cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("		document.getElementById(tag+'_'+id+'_text').select();\n");
  echo("		editaTitulo++;\n");
  echo("		}\n");
  echo("	}\n\n");
  
  echo("	function EdicaoCampo(id, tag, valor){\n");
  echo("		if ((valor=='ok')&&(document.getElementById(tag+'_'+id+'_text').value!=\"\")&&(VerificaNota(tag,document.getElementById(tag+'_'+id+'_text').value))){\n");
  echo("			conteudo = document.getElementById(tag+'_'+id+'_text').value;\n");
  echo("			if (tag=='valor'){\n");
  echo("              xajax_EditarValor(".$cod_curso.", conteudo, ".$cod_usuario.", '".$dados_avaliacao['Ferramenta']."', ".$dados_avaliacao['Cod_atividade'].", ".$cod_avaliacao.");\n");
  echo("			  }else{\n");
  echo("			  	xajax_EditarTitulo(".$cod_curso.", conteudo, ".$cod_usuario.", '".$dados_avaliacao['Ferramenta']."', ".$dados_avaliacao['Cod_atividade'].", ".$cod_avaliacao.");\n");
  echo("			  }\n");
  echo("		}else{\n");
  echo("			/* frase #229 - O campo n‹o pode ser vazio. */\n");
  echo("			if ((valor=='ok')&&(document.getElementById(tag+'_'+id+'_text').value==\"\"))\n");
  echo("				xajax_AlertaFraseFerramenta(229,22);\n");

  echo("			document.getElementById(tag+'_'+id).innerHTML=conteudo;\n");
  echo("			document.getElementById(tag+'_'+id).className='';\n");
  
  //echo("		  if(navigator.appName.match("Opera")){\n");
  //echo("          document.getElementById(tag+'_'+id).onclick = AlteraCampo(tag,id);\n");
  //echo("          }else{
  //echo("             document.getElementById(tag+'_'+id).onclick = function(){ AlteraCampo(tag,id); };\n");
  //echo("         }\n");
  
  echo("			document.getElementById(tag+'_'+id);\n");
  echo("			/*Cancela EdiÃ§Ã£o*/\n");
  echo("			if (!cancelarTodos){\n");
  echo("				xajax_AcabaEdicaoDinamic(".$cod_curso.", ".$cod_avaliacao.", ".$cod_usuario.", 0);\n");
  echo("			}\n");
  echo("		}\n");
  echo("		editaTitulo=0;\n");
  echo("		cancelarElemento=null;\n");
  echo("	}\n\n");
  
  echo (" function Iniciar()");
  echo (" {");
    if (isset($_GET['acao']))
      $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo (" startList();");
  echo (" }");
 
  echo("    </script>\n");

  $objAjax->printJavascript();

  echo("    <script type=\"text/javascript\" src=\"jscriptlib.js\"></script>\n");

  // A variavel tela_avaliacao indica quais avaliacoes devem ser listadas: 'P'assadas, 'A'tuais ou 'F'uturas
  if (!isset($tela_avaliacao) || !in_array($tela_avaliacao, array('P', 'A', 'F')))
  {
    $tela_avaliacao = 'A';
  }

  // Determinamos a frase que descreve as avaliacoes e a lista de avaliacoes
  if ($tela_avaliacao == 'P')
    // 29 - Avaliaï¿½ï¿½es Passadas
    $lista_avaliacoes = RetornaAvaliacoesAnteriores($sock,$usr_formador);
  elseif ($tela_avaliacao == 'A')
    // 32 - Avaliaï¿½ï¿½es Atuais
    $lista_avaliacoes = RetornaAvaliacoesAtuais($sock,$usr_formador);
  elseif ($tela_avaliacao == 'F')
    // 30 - Avaliaï¿½ï¿½es Futuras
    $lista_avaliacoes = RetornaAvaliacoesFuturas($sock,$usr_formador);

  include("../menu_principal.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* VerificaÃ§Ã£o se a avaliacao estÃ¡ em EdiÃ§Ã£o */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em EdiÃ§Ã£o... */
  $linha=RetornaStatusAvaliacao($sock, "Avaliacao", $cod_avaliacao);

  if ($linha['status'] == "E")
  {
    if (
      // Se a edição anterior foi iniciada a mais de meia hora atrás ou...
      ($linha['inicio_edicao']<(time()-1800)) ||
      // Se o usuário que solicita ver a avaliação é o mesmo que começou a edição.
      ($cod_usuario == $linha['cod_usuario'])
    ){
      // Cancelar a edição atual em favor da (provável) nova edição do usuario
      // que solicitou a página.
      CancelaEdicaoAvaliacao($sock, "Avaliacao", $cod_avaliacao, $cod_usuario);
    }else{
      // Mostrar ao usuário que esta avaliação ainda está em edição.
      echo("          <script language=\"javascript\">\n");
      echo("            window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=ver','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("            window.location='avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=22&cod_avaliacao=".$cod_avaliacao."&tela_avaliacao=".$tela_avaliacao."&operacao=".$cod_operacao."';\n");
      echo("          </script>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
  }

  // Pï¿½gina Principal
  // 32 - Avaliaï¿½ï¿½es Atuais
  // 120 - Ver Avaliaï¿½ï¿½o
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, 120)."</h4>\n");

    // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  //<!----------------- Cabeï¿½alho Acaba Aqui ----------------->

  //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");

  //<!----------------- Botoes de Acao ----------------->
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // 23 - Voltar (ger)
  echo("                  <li><span onclick=\"window.location='avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&tela_avaliacao=".$tela_avaliacao."&operacao=".$operacao."';\">".RetornaFraseDaLista($lista_frases_geral, 23)."</span></li>\n");
  if ($usr_formador)
  {
    // 99 - Histï¿½rico
    echo("                  <li><span onclick=\"return(Historico());\">".RetornaFraseDaLista($lista_frases, 99)."</span></li>\n");
    // 34 - Avaliar Participantes
    if($avaliacao_participante) /*Se permite avaliar participante*/
    echo("                  <li><span onclick=\"javascript:AvaliarParticipantes();\">".RetornaFraseDaLista($lista_frases, 34)."</span></li>\n");
  }
  else
  {
   // 105 - Histï¿½rico do Desempenho
   echo("                  <li><span onclick=\"javascript:AvaliarParticipantes()\">".RetornaFraseDaLista($lista_frases, 105)."</span></li>\n");
  }
  echo("    <form name=\"frmSalvar\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\"     value=\"".$cod_curso."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_avaliacao\" value=\"".$cod_avaliacao."\">\n");
    // G 209 - Salvar em Arquivo ou Imprimir
  echo("                 <li><span onclick=\"window.open('ver_popup.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_avaliacao=".$cod_avaliacao."&operacao=".$operacao."', 'Salva/Imprime' ,'width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases, 209)."</span></li>\n");
  if ($usr_formador){
    //Frase #1: Apagar
    echo("                 <li><span onClick=\"return(ExcluirAvaliacao());\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
  }
  echo("    </form>\n");

  if ($dados_avaliacao['Ferramenta'] == 'P')
  {
    // 14 - Atividade no Portfï¿½lio
    echo("                  <li><span onclick=\"window.location='../material/material.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=3'\">".RetornaFraseDaLista($lista_frases, 227)."</span></li>\n");
  }
  else if ($dados_avaliacao['Ferramenta'] == 'E')
  {
    // 173 - Exercï¿½cio
    //Quando criar exercicio colocar uma frase no banco de dados pra ele
    //echo("                  <li><span onclick=\"window.location='../portfolio/portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=15&exibir=myp'\">".RetornaFraseDaLista($lista_frases, X)."</span></li>\n");
  }
  else if ($dados_avaliacao['Ferramenta'] == 'F')
  {
    // 145 - Fï¿½rum de Discussï¿½o
    echo("                  <li><span onclick=\"window.location='../forum/forum.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=9'\">".RetornaFraseDaLista($lista_frases, 225)."</span></li>\n");
  }
  else if ($dados_avaliacao['Ferramenta'] == 'B')
  {
    // 146 - Sessï¿½o de Bate-Papo
    if ($tela_avaliacao == 'F')
      echo("                  <li><span onclick=\"window.location='../batepapo/ver_sessoes_marcadas.php?cod_curso=".$cod_curso."'\">".RetornaFraseDaLista($lista_frases, 226)."</span></li>\n");
    else
      echo("                  <li><span onclick=\"window.location='../batepapo/ver_sessoes_realizadas.php?cod_curso=".$cod_curso."'\">".RetornaFraseDaLista($lista_frases, 226)."</span></li>\n");
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  echo("    <form name=\"frmAvaliacao\" method=\"get\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\">\n");
  // Passa o cod_avaliacao para executar aï¿½ï¿½es sobre ela.
  echo("      <input type=\"hidden\" name=\"cod_avaliacao\"  value=\"".$cod_avaliacao."\">\n");
  // $tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=\"hidden\" name=\"tela_avaliacao\" value=\"".$tela_avaliacao."\">\n");
  echo("      <input type=\"hidden\" name=\"origem\"         value=\"ver\">\n");
  echo("      <input type=\"hidden\" name=\"acao\"           value=null>\n"); 
  echo("    </form>\n");

  $tipo = "";
  // Soh existe o conceito de tipo de avaliacao (individual ou em grupo) se for a avaliacao de uma atividade no portfolio ou em exercï¿½cios
  if ($dados_avaliacao['Ferramenta'] == 'P')
  {
    $existe_tipo = true;
    // 14 - Atividade no Portfï¿½lio
    $ferramenta = RetornaFraseDaLista($lista_frases,14);
    if ($dados_avaliacao['Tipo'] == 'I')
      // 161 - Atividade individual no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 161);
    elseif ($dados_avaliacao['Tipo'] == 'G')
      // 162 - Atividade em grupo no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 162);
  }
  else if ($dados_avaliacao['Ferramenta'] == 'E')
  {
    $existe_tipo = true;
    // 173 - Exercï¿½cio
    $ferramenta = RetornaFraseDaLista($lista_frases,173);
    if ($dados_avaliacao['Tipo'] == 'I')
      // 176 - Exercicio individual 
      $tipo = RetornaFraseDaLista($lista_frases, 176);
    elseif ($dados_avaliacao['Tipo'] == 'G')
      // 174 - Exercicio em grupo
      $tipo = RetornaFraseDaLista($lista_frases, 174);
  }
  else if ($dados_avaliacao['Ferramenta'] == 'F')
    // 145 - Fï¿½rum de Discussï¿½o
    $tipo = RetornaFraseDaLista($lista_frases,145);
  elseif ($dados_avaliacao['Ferramenta'] == 'B')
    // 146 - Sessï¿½o de Bate-Papo
    $tipo = RetornaFraseDaLista($lista_frases,146);
  else if($dados_avaliacao['Ferramenta']=='N')
  {
    if($dados_avaliacao['Tipo']=='I')
      $tipo= RetornaFraseDaLista($lista_frases, 185); 
    else
      $tipo= RetornaFraseDaLista($lista_frases, 186);
  }  

  if ($dados_avaliacao['Objetivos'] == '')
  {
    // 157 - Nï¿½o definidos
    $objetivos=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $objetivos=$dados_avaliacao['Objetivos'];

  if ($dados_avaliacao['Criterios'] == '')
  {
    // 157 - Nï¿½o definidos
    $criterios=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $criterios=$dados_avaliacao['Criterios'];

  $titulo = RetornaTituloAvaliacao($sock, $dados_avaliacao['Ferramenta'], $dados_avaliacao['Cod_atividade']);
  if($usr_formador){
    $titulo="<span id=\"tit_".$dados_avaliacao['Cod_atividade']."\" class=\"\" onclick=\"\">".$titulo."</span>";
  }
    $valor = FormataNota($dados_avaliacao['Valor']);
  if($usr_formador){
    $valor="<span id=\"valor_".$dados_avaliacao['Cod_atividade']."\" class=\"\" onclick=\"\">".$valor."</span>";
  }
  $obj = "<span id=\"text_obj\">".AjustaParagrafo($objetivos)."</span>";
  $crt = "<span id=\"text_crt\">".AjustaParagrafo($criterios)."</span>";
  $data_inicio = UnixTime2Data($dados_avaliacao['Data_inicio']);
  $data_fim = UnixTime2Data($dados_avaliacao['Data_termino']);

  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  // 123 - Tï¿½tulo
  echo("                    <td>".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
  if($usr_formador)
    // 70 - OpÃ§oes
    echo("                    <td width=\"15%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral, 70)."</td>\n");
  // 113 - Tipo de Avaliaï¿½ï¿½o
  echo("                    <td width=\"15%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
  // 19 - Valor
  echo("                    <td width=15% align=\"center\">".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr id='tr_".$dados_avaliacao['Cod_atividade']."'>\n");
  echo("                    <td align=left rowspan=\"3\">".$titulo."</td>\n");

  if($usr_formador)
  {
      echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
      echo("                      <ul>\n");
     // 230 - Renomear TÃ­tulo
      echo(  $titulo="<li><span onclick=\"AlteraCampo('tit', ".$dados_avaliacao['Cod_atividade'].");\">".RetornaFraseDaLista($lista_frases,230)."</span></li>");

      // 211 - Editar Criterios
      echo("                        <li><span onClick=\"AlteraTexto('crt');\">".RetornaFraseDaLista($lista_frases,211)."</span></li>\n");

      // 210 - Editar Objetivos
      echo("                        <li><span onClick=\"AlteraTexto('obj');\">".RetornaFraseDaLista($lista_frases,210)."</span></li>\n");

      // 231 - Editar Valor
      echo(  "<li><span onclick=\"AlteraCampo('valor', ".$dados_avaliacao['Cod_atividade'].");\">".RetornaFraseDaLista($lista_frases,231)."</span></li>");

   // G 1 - Apagar
     // echo("                        <li><span onClick=\"return(ExcluirAvaliacao());\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
      echo("                      </ul>\n");
      echo("                    </td>\n");
  }
  echo("                    <td align=\"center\">&nbsp;&nbsp;".$tipo."</td>\n");
  echo("                    <td align=\"center\">".$valor."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  /* 16 - Data de inï¿½cio*/
  echo("                    <td>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  /* 17 - Data de Tï¿½rmino */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,17)."</td>\n");
  if($usr_formador)
    echo("                    <td>&nbsp;</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  if($usr_formador)
  {
    echo("                    <form name=\"frmAlteraPeriodo\" id=\"frmAlteraPeriodo\" method=\"post\" action=\"\" onsubmit=\"Valida(); return false;\">\n");
    echo("                      <input type=\"hidden\" name=\"texto\"          value=\"".RetornaFraseDaLista($lista_frases, 214)."\">\n");
    echo("                      <input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\">\n");
    echo("                      <input type=\"hidden\" name=\"cod_avaliacao\"  value=\"".$cod_avaliacao."\">\n");
    echo("                      <input type=\"hidden\" name=\"cod_usuario\"    value=\"".$cod_usuario."\">\n");
    echo("                      <input type=\"hidden\" name=\"tela_avaliacao\" value=\"".$tela_avaliacao."\">\n");
  }
  /* 16 - Data de inï¿½cio*/
  echo("                    <td>\n");
  if($usr_formador)
    echo("                      <input type=\"text\" id=\"data_inicio\" name=\"data_inicio\" size='10' maxlength='10' value=\"".$data_inicio."\" class=\"input\" /><img src=\"../imgs/ico_calendario.gif\" alt=\"\" onclick=\"displayCalendar(document.getElementById ('data_inicio'),'dd/mm/yyyy',this);\" />\n");
  else
    echo("                      ".$data_inicio);
  echo("                    </td>\n");
  /* 17 - Data de Tï¿½rmino */
  echo("                    <td>\n");
  if($usr_formador)
    echo("                      <input type=\"text\" id=\"data_fim\" name=\"data_fim\" size='10' maxlength='10' value=\"".$data_fim."\" class=\"input\" /><img src=\"../imgs/ico_calendario.gif\" alt=\"\" onclick=\"displayCalendar(document.getElementById ('data_fim'),'dd/mm/yyyy',this);\" />\n");
  else
    echo("                      ".$data_fim);
  echo("                    </td>\n");
  if($usr_formador)
  {
    // 43(biblioteca) - Alterar Perï¿½odo
    echo("                    <td><input type=\"submit\" class=\"input\" style=\"width:120px;\" value=\"".RetornaFraseDaLista($lista_frases_biblioteca,43)."\" /></td>\n");
    echo("                    </form>\n");
  }
  echo("                  </tr>\n");
  // 75 - Objetivos
  echo("                  <tr class=\"head alLeft\">\n");
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,75)."</td></tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens divRichText\" colspan=\"4\" align=left>".$obj."</td></tr>\n");
  // 23 - Criterios
  echo("                  <tr class=\"head alLeft\">");
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,23)."</td></tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens divRichText\" colspan=\"4\" align=left>".$crt."</td></tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  include("../tela2.php");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n"); 
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
  exit;

?>
