<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/imprimir_forum.php

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
  ARQUIVO : cursos/aplic/forum/imprimir_forum.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("forum.inc");
  include("avaliacoes_forum.inc");
  include("../menu.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudarRelevanciaDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MostraMensagemDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 9;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  /* Ajustes necess�rios para independer do topo_tela.php */

  /* Se o teleduc naum pegou o cod_curso, pegamos para ele =) */
  if (!isset($cod_curso)){
    if (isset($_GET['cod_curso'])){
      $cod_curso = $_GET['cod_curso'];
    } else if (isset($_POST['cod_curso'])){
      $cod_curso = $_POST['cod_curso'];
    }
  }
  
  $cod_usuario_global = VerificaAutenticacao($cod_curso);
  $sock=Conectar("");

  $lingua_curso = RetornaLinguaCurso($sock,$cod_curso);

  // Se diferente, ent�o l�ngua do curso � diferente da l�ngua do usu�rio, atualiza a lista de frases
  if($lingua_curso != $_SESSION['cod_lingua_s']) {
    MudancaDeLingua($sock, $lingua_curso);
  }

  $lista_frases_menu=RetornaListaDeFrases($sock,-4);

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);

  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $tela_ordem_ferramentas=RetornaOrdemFerramentas($sock);
  $tela_lista_ferramentas=RetornaListaFerramentas($sock);
  $tela_lista_titulos=RetornaListaTitulos($sock, $_SESSION['cod_lingua_s']);
  $tela_email_suporte=RetornaConfiguracao($sock,"adm_email");

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $tela_raiz_www = $linha[0];

  $tela_host=RetornaConfiguracao($sock,"host");

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $tela_visitante     = EVisitante($sock,$cod_curso,$cod_usuario);

  $tela_formador          = EFormador($sock,$cod_curso,$cod_usuario);
  $tela_formadormesmo     = EFormadorMesmo($sock,$cod_curso,$cod_usuario);

  // booleano, indica se usuario eh colaborador
  $tela_colaborador       = EColaborador($sock, $cod_curso, $cod_usuario);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta);
  MarcaAcesso($sock,$cod_usuario,$cod_ferramenta);

  echo("<!DOCTYPE HTML SYSTEM \"http://teleduc.nied.unicamp.br/~teleduc/loose-custom.dtd\">\n");
  echo("<html lang=\"pt\">\n"); 
  echo("  <head>\n");
  echo("    <title>TelEduc - ".$tela_lista_titulos[$cod_ferramenta]."</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\">\n");
  echo("    <meta name=\"description\" content=\"TelEduc\">\n");
  echo("    <meta name=\"keywords\" content=\"TelEduc\">\n");
  echo("    <meta name=\"owner\" content=\"TelEduc\">\n");
  echo("    <meta name=\"copyright\" content=\"TelEduc\">\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n");
  echo("    <link rel=\"shortcut icon\" href=\"../../../favicon.ico\" />\n");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <link href=\"../js-css/dhtmlgoodies_calendar.css\" rel=\"stylesheet\" type=\"text/css\">\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmlgoodies_calendar.js\"></script>\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>\n");
  $objAjax->printJavascript();
  echo("    <style>body{padding-top:20px;}</style>");
  
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();
  $objAjax->printJavascript();

  /* Fim dos Ajustes necess�rios para independer do topo_tela.php */

  session_register('cod_forum_s');
  session_register('array_mensagens_s');
  session_register('sin_pag_s');
  
  if ($status == 'D')
    $cod_pagina_ajuda=3;
  else
    $cod_pagina_ajuda=6;

  $feedbackObject = new FeedbackObject($lista_frases);
  $feedbackObject->addAction("nova_msg", 17, 18);
  $feedbackObject->addAction("responde_mensagem", 17, 30);

  /* Verifica se o usuario eh formador. */
  $usr_visitante = EVisitante($sock, $cod_curso, $cod_usuario);
  $usr_colaborador = EColaborador($sock, $cod_curso, $cod_usuario);
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);

  /* Obt� o nome e o status do f�um                     */
  $forum_dados = RetornaForum($sock, $cod_forum);

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  /* Nmero de mensagens exibidas por p�ina.             */
  if (!isset($msg_por_pag)) $msg_por_pag = 10;

  /* Se o tipo de ordenacao nao for especificada, usa arvore */
  if ((!isset($_SESSION['forum_ordem']) || $_SESSION['forum_ordem'] == "") && (!isset($_GET['forum_ordem']) || $_GET['forum_ordem'] == "")) {
  	$ordem = 'arvore';
  } else {
  	/* Se o usu�rio tentar atualizar a ordenacao, grava na $_SESSION */
  	if (isset($_GET['forum_ordem']))
  		$_SESSION['forum_ordem'] = $_GET['forum_ordem'];
  	$ordem = $_SESSION['forum_ordem'];
  }

  /* Obt� a data e hora do penltimo acesso para comparar com as datas das   */
  /* mensagens e destacar a que s� mais recentes.                            */
  $penult_acesso = PenultimoAcesso($sock, $cod_usuario, "");

  /* Se a data de altera�o n� estiver setada ent� entrou pela primeira vez */
  /* As p�inas que modificam a base de dados (compor, responder, apagar e    */
  /* excluir mensagens) n� repassam esse valor, pois assumem que houve       */
  /* altera�es.                                                              */
  if ((!isset($data_msg_alt)) || (MensagensAlteradas($sock, $cod_forum, $data_msg_alt)))
  {
    /* Obt� a data atual para posterior compara�o. Esta data �comparada com */
    /* as das mensagens. Se alguma mensagem possuir data superior a            */
    /* $data_msg_alt ent� lista novamente as mensagens, do contr�io utiliza  */
    /* as mensagens armazenadas na sess�.                                     */
    $data_msg_alt = time();

    /* armazena o cod_forum */
    $cod_forum_s = $cod_forum;
    /* limpa as mensagens */
    session_unregister('array_mensagens_s');
    session_unregister('sin_pag_s');

    session_register('array_mensagens_s');
    session_register('sin_pag_s');

    /* Se a ordena�o for por �vore ent� chama a fun�o RetornaMensagens, que */
    /* retorna as mensagens estruturadas.                                       */
    if ($ordem == 'arvore')
      list ($total_mensagens, $array_mensagens, $sin_pag) =
        RetornaMensagens($sock, $cod_forum, $status, $msg_por_pag, $cod_usuario, $penult_acesso);
    else
    {
      /* Se a ordena�o for por data, emissor ou t�ulo da mensagem, chama a      */
      /* RetornaMensagensOrdenadas, que retorna as mensagens de acordo com a      */
      /* ordena�o especificada por� sem estrutura�o (identa�o).               */
      list ($total_mensagens, $array_mensagens, $sin_pag) =
        RetornaMensagensOrdenadas($sock, $cod_curso, $cod_forum, $status, $msg_por_pag, $cod_usuario, $ordem, $penult_acesso);
    }

    $array_mensagens_s = $array_mensagens;
    $sin_pag_s = $sin_pag;
  }


  else
  /* Se n� foram inclu�as/alteradas mensagens ap� a data especificada em     */
  /* $data_msg_alt, utiliza as mensagens armazenadas.  RECOMENTAR               */
  {

    $array_mensagens = $array_mensagens_s;
    $sin_pag = $sin_pag_s;
  }

   /* Se o nmero total de mensagens for superior que o nmero de mensagens por  */
   /* p�ina ent� calcula o total de p�inas. Do contr�io, define o nmero de  */
   /* p�inas para 1.                                                            */
   if ($total_mensagens > $msg_por_pag)
   {
     /* Calcula o nmero de p�inas geradas.                  */
     $total_pag = ceil($total_mensagens / $msg_por_pag);
   }
   else
     $total_pag = 1;

  /* Se a p�ina atual n� estiver setada ent�, por padr�, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p�ina �maior que o total de p�inas, se for */
  /* atribui o valor de $total_pag �$pag_atual.                                    */
   if ((!isset($pag_atual)) || ($pag_atual=="") || ($pag_atual==0))
     $pag_atual =  1;
   else $pag_atual = min($pag_atual, $total_pag);

  $permitido=VerificaPermissao($sock,$cod_usuario,$forum_dados['permissoes']);

  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var pag_atual = ".$pag_atual.";\n\n");
  echo("      var total_pag = ".$total_pag.";\n\n");
  echo("      var totalMsgs = ".$total_mensagens."\n");
  echo("      var respondendoMsg = -1;\n");
  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");
  echo("      var selected_item, mensagens_abertas=0;\n");

  echo("      function startList() {\n");
  echo("        if (document.all && document.getElementById) {\n");
  echo("          nodes = document.getElementsByTagName(\"span\");\n");
  echo("          for (i=0; i<nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("          nodes = document.getElementsByTagName(\"li\");\n");
  echo("          for (i=0; i<nodes.length; i++) {\n");
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

  echo("      function Iniciar()\n");
  echo("      {\n");
  if($usr_formador){
    echo("        relevIni= getLayer(\"relev\");\n");
    echo("        EscondeLayers();\n");
  }

  if ( ($forum_dados['status'] == 'A') || (($forum_dados['status'] == 'G') && ($permitido)) || (($forum_dados['status'] == 'R') && ($permitido)) && (!$usr_visitante) )
  {
    echo("        writeRichTextOnJS('msg_corpo', '', 600, 200, true, false, 'divRTE', true);\n");
  }
  echo("        ExibeMsgPagina(".$pag_atual.");\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");


  
  echo("      function VoltarPaginacao(pagina){\n");
  
  echo("         spans = document.getElementsByTagName('span');\n");
  echo("         for (i=0; i<spans.length; i++){\n");
  echo("           if (spans[i].id.substr(0, 7).match(\"fechar_\")){\n");
  echo("             spans[i].onclick();\n");
  echo("           }\n");
  echo("         }\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        final = tabela.rows.length-1;\n");
  echo("        for (i=1; i<final; i++){\n");
  echo("          if (!tabela.rows[i]) break;\n");
  echo("          tabela.rows[i].style.display=\"none\";\n");
  echo("        }\n\n"); 
     /* 71 - Exibir todas */
  echo("        document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,71)."\";\n");
  echo("        document.getElementById('exibir_paginacao').onclick = function(){ ExibirTodasMsgs(); };\n");
  echo("        ExibeMsgPagina(pagina);\n");
  echo("        mensagens_abertas=0;\n");
  echo("      }\n");
  
  echo("      function ExibeMsgPagina(pagina){\n");
  echo("        if (pagina==-1) return;\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        if(!tabela) return;\n");
  echo("        inicio = (((pag_atual-1)*".$msg_por_pag.")*2)+1;\n");
  echo("        final = (((pag_atual)*".$msg_por_pag.")*2)+1;\n");
  echo("        for (i=inicio; i<final; i++){\n");
  echo("          if (!tabela.rows[i]) break;\n");
  echo("          tabela.rows[i].style.display=\"none\";\n");
  echo("        }\n\n");  
  echo("        var browser=navigator.appName;\n\n");
  
  echo("        inicio = (((pagina-1)*".$msg_por_pag.")*2)+1;\n");
  echo("        final = ((pagina)*".$msg_por_pag.")*2;\n");
  echo("        iTmp = 0; contador=0;\n");
  echo("        for (i=inicio; i<final+1; i++){\n");
  echo("          if (!tabela.rows[i]){ iTmp=1; break;}\n");
  echo("          if(i%2!=0){\n");
  echo("            if (browser==\"Microsoft Internet Explorer\")\n");
  echo("              tabela.rows[i].style.display=\"block\";\n");
  echo("            else\n");
  echo("              tabela.rows[i].style.display=\"table-row\";\n");
  echo("          }\n");
  echo("        }\n\n");
  
  echo("        document.getElementById('prim_msg_index').innerHTML=(inicio-1)/2 + 1;\n"); 
  echo("        if (!iTmp) document.getElementById('ult_msg_index').innerHTML=final/2;\n"); 
  echo("        else document.getElementById('ult_msg_index').innerHTML=(i-2)/2;\n\n"); 
  
  echo("        if (browser==\"Microsoft Internet Explorer\")\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"block\";\n");
  echo("        else\n");
  echo("          tabela.rows[tabela.rows.length-1].style.display=\"table-row\";\n");
  
  echo("        pag_atual=pagina;\n\n");
  
  echo("        if (pag_atual != 1){\n");
  echo("          document.getElementById('paginacao_first').onclick = function(){ ExibeMsgPagina(1); };\n");
  echo("          document.getElementById('paginacao_first').className = \"link\";\n");
  echo("          document.getElementById('paginacao_back').onclick = function(){ ExibeMsgPagina(pag_atual-1); };\n");
  echo("          document.getElementById('paginacao_back').className = \"link\";\n");
  echo("        }else{\n");
  echo("         document.getElementById('paginacao_first').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_first').className = \"\";\n");
  echo("         document.getElementById('paginacao_back').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_back').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_first').innerHTML = \"&lt;&lt;\";\n");
  echo("        document.getElementById('paginacao_back').innerHTML = \"&lt;\";\n");
  echo("        inicio = pag_atual-2;\n");
  echo("        if (inicio<1) inicio=1;\n");
  echo("        fim = pag_atual+2;\n");
  echo("        if (fim>total_pag) fim=total_pag;\n");
  echo("        var controle=1;\n");
  echo("        var vetor= new Array();\n");
  echo("        for (j=inicio; j <= fim; j++){\n");
  echo("          // A página atual Não é exibida com link.\n");
  echo("          if (j == pag_atual){\n");
  echo("             document.getElementById('paginacao_'+controle).innerHTML='<b>['+j+']<\/b>';\n");
  echo("             document.getElementById('paginacao_'+controle).className='';\n");
  echo("             vetor[controle] = -1;\n");
  echo("          }else{\n");
  echo("             document.getElementById('paginacao_'+controle).innerHTML=j;\n");
  echo("             document.getElementById('paginacao_'+controle).className='link';\n");
  echo("             vetor[controle]=j;\n");
  echo("          }\n");
  echo("          controle++;\n");
  echo("        }\n");
  echo("        while (controle<=5){\n");
  echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
  echo("          document.getElementById('paginacao_'+controle).className='';\n");
  echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
  echo("          controle++;\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_1').onclick=function(){ ExibeMsgPagina(vetor[1]); };\n");
  echo("        document.getElementById('paginacao_2').onclick=function(){ ExibeMsgPagina(vetor[2]); };\n");
  echo("        document.getElementById('paginacao_3').onclick=function(){ ExibeMsgPagina(vetor[3]); };\n");
  echo("        document.getElementById('paginacao_4').onclick=function(){ ExibeMsgPagina(vetor[4]); };\n");
  echo("        document.getElementById('paginacao_5').onclick=function(){ ExibeMsgPagina(vetor[5]); };\n\n");

  echo("        /* Se a página atual Não for a última página então cria um   \n");
  echo("           link para a próxima página */\n");
  echo("        if (pag_atual != total_pag){\n");
  echo("         document.getElementById('paginacao_fwd').onclick = function(){ ExibeMsgPagina(pag_atual+1); };\n");
  echo("         document.getElementById('paginacao_fwd').className = \"link\";\n");
  echo("         document.getElementById('paginacao_last').onclick = function(){ ExibeMsgPagina(".$total_pag."); };\n");
  echo("         document.getElementById('paginacao_last').className = \"link\";\n");
  echo("        }\n");
  echo("        else{\n");
  echo("         document.getElementById('paginacao_fwd').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_fwd').className = \"\";\n");
  echo("         document.getElementById('paginacao_last').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_last').className = \"\";\n");
  echo("        }\n");
  echo("        document.getElementById('paginacao_fwd').innerHTML = \"&gt;\";\n");
  echo("        document.getElementById('paginacao_last').innerHTML = \"&gt;&gt;\";\n");
  echo("      }\n");
  
  echo("      function ExibirTodasMsgs(){\n");
  echo("        tabela = document.getElementById('tabelaMsgs');\n");
  echo("        final = tabela.rows.length-1;\n");
  echo("        var browser=navigator.appName;\n\n");
  echo("        contador=0;\n");
  echo("        for (i=0; i<final; i++){\n");
  echo("          if (!tabela.rows[i]) break;\n");
  echo("          if (browser==\"Microsoft Internet Explorer\"){\n");
  echo("            tabela.rows[i].style.display=\"block\";\n");
  echo("          }else{\n");
  echo("            tabela.rows[i].style.display=\"table-row\";\n");
  echo("          }\n");
  echo("          idTemp = tabela.rows[i].id.split('_');\n");
  echo("          if (idTemp[1] !='msg') contador++;\n");
  echo("        }\n\n");

  echo("        document.getElementById('prim_msg_index').innerHTML=1;\n"); 
  echo("        document.getElementById('ult_msg_index').innerHTML=contador-1;\n\n"); 

  echo("        controle=1;\n");
  echo("        while (controle<=5){\n");
  echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
  echo("          document.getElementById('paginacao_'+controle).className='';\n");
  echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
  echo("          controle++;\n");
  echo("        }\n");
  echo("         document.getElementById('paginacao_first').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_first').className = \"\";\n");
  echo("         document.getElementById('paginacao_first').innerHTML = \"\";\n");
  echo("         document.getElementById('paginacao_back').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_back').className = \"\";\n");
  echo("         document.getElementById('paginacao_back').innerHTML = \"\";\n");
  echo("         document.getElementById('paginacao_fwd').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_fwd').className = \"\";\n");
  echo("         document.getElementById('paginacao_fwd').innerHTML = \"\";\n\n");
  echo("         document.getElementById('paginacao_last').onclick = function(){};\n");
  echo("         document.getElementById('paginacao_last').className = \"\";\n");
  echo("         document.getElementById('paginacao_last').innerHTML = \"\";\n\n");
  
  /* 131 - Exibir por páginas */
  echo("         document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,131)."\";\n");
  echo("         document.getElementById('exibir_paginacao').onclick = function(){ VoltarPaginacao(pag_atual); };\n");
  echo("         mensagens_abertas=contador-1;\n");
  echo("      }\n");
    
  /* Se o status do fórum for Ativo (permite leitura e escrita) e se o usuário Não */
  /* for um visitante, cria a função de compor mensagem.                           */
  /* Colaboradores postam mensagens                                            */
  /* Visitantes nao postam mensagens                                      */
  /* Se o status do fórum for G ou R (apenas os usuários permitidos postam), mas o */
  /* usuário não for permitido, não postam mensagens.                              */
//  if ( ($forum_dados['status'] == 'A') && !$usr_visitante )

  if ( (($forum_dados['status'] == 'A') || (($forum_dados['status'] == 'G') && ($permitido)) || (($forum_dados['status'] == 'R') && ($permitido))) && (!$usr_visitante) )
  {

    echo("      function ComporMensagem(){\n");
    echo("        if(!isIE){\n");
    echo("          document.getElementById('divNovaMsg').className=\"\";\n");
    echo("        }else{\n");
    echo("          document.getElementById('trNovaMsg').style.display=\"\"; \n");
    echo("        }\n");
    echo("        document.getElementById('tdNovaMsg').style.background=\"#FFFFFF\";\n");

    echo("        document.getElementById('divNovaMsg').className=\"\";\n");
    echo("        document.getElementById('tdNovaMsg').appendChild(document.getElementById('divNovaMsg'));\n");
    echo("		  writeRichTextOnJS('msg_corpo', '', 600, 200, true, false, 'divRTE', true);\n");
    echo("        document.getElementById('acao').value='nova_msg';\n");
    echo("        document.getElementById('tdNovaMsg').style.width=\"525px\";\n");
    echo("        document.formCompor.msg_titulo.focus();\n");
    echo("        if(respondendoMsg!=-1){\n");
    echo("          respondendoMsg=-1;\n");
    echo("          clearNewRTE('msg_corpo', 'divRTE');\n");
    echo("        }\n");
    echo("      }\n\n");
    
    echo("      function CancelarNovaMsg()\n");
    echo("      {\n");
    echo("        document.getElementById('tdNovaMsg').style.background=\"#DCDCDC\";\n");
    echo("        document.getElementById('divNovaMsg').className=\"divHidden\";");
    echo("        document.formCompor.msg_titulo.value='';\n");
    echo("        document.formCompor.msg_corpo.value='';\n");
    echo("        clearNewRTE('msg_corpo', 'divRTE');\n");
    echo("        if (document.getElementById('spanRespondeMsg')){\n");
    echo("          tdElement = document.getElementById('spanRespondeMsg').parentNode;\n");
    echo("          tdElement.removeChild(document.getElementById('spanRespondeMsg'));\n");
    echo("        }\n");
    echo("        respondendoMsg = -1;\n");
    echo("      }\n\n");

    echo("      function TestaNome(form){\n");
    echo("        updateRTE('msg_corpo');\n");
    /* Elimina os espaços para verificar se o titulo nao eh formado por apenas espaços */
    echo("        Msg_nome = form.msg_titulo.value;\n");
    echo("        Msg_corpo = form.msg_corpo.value;\n");
    echo("        while (Msg_nome.search(\" \") != -1){\n");
    echo("          Msg_nome = Msg_nome.replace(/ /, \"\");\n");
    echo("        }\n");
    echo("        if (Msg_nome == ''){\n");
    /* 7 - A mensagem deve ter um título. */
    echo("          alert('".RetornaFraseDaLista($lista_frases, 15)."');\n");
    echo("          document.formCompor.msg_titulo.focus();\n");
    echo("          return(false);\n");
    echo("        } else {\n");
    echo("          while (Msg_corpo.search(\" \") != -1){\n");
    echo("            Msg_corpo = Msg_corpo.replace(/ /, \"\");\n");
    echo("          }\n");
    echo("          if (Msg_corpo == ''){\n");
    /* 8 - A mensagem deve ter um conteúdo. */
    echo("            alert('".RetornaFraseDaLista($lista_frases, 16)."');\n");
    echo("          document.formCompor.msg_corpo.focus();\n");
    echo("            return(false);\n");  
    echo("          }\n");
    echo("        }\n");
    echo("        return(true);\n");
    echo("      }\n\n");


    if ($total_mensagens > 0)
    {
      echo("      function ResponderMsg(cod_msg){\n");
      echo("        if (respondendoMsg == cod_msg) return;\n");
      echo("        respondendoMsg = cod_msg;\n");
      echo("        spanElement = document.getElementById('spanRespondeMsg');\n");
      echo("        if (spanElement){\n");
      echo("          tdElement = spanElement.parentNode;\n");
      echo("          tdElement.removeChild(spanElement);\n");
      echo("        }\n");

      echo("        document.getElementById('tdNovaMsg').style.background=\"#DCDCDC\";\n");

      echo("        tdElement = document.getElementById('td_msg_'+cod_msg);\n");
      echo("        newSpan = document.createElement('span');\n");
      echo("        newSpan.setAttribute('id', 'spanRespondeMsg');\n");
      echo("        newSpan.setAttribute('name', 'span_resp_'+cod_msg);\n");    
      echo("        newSpan.innerHTML='<br /><br />';\n");

      echo("        tdElement.appendChild(newSpan);\n");
      echo("        tdElement.appendChild(document.getElementById('divNovaMsg'));\n");
      echo("        document.getElementById('divNovaMsg').className=\"\";\n");
      
	  echo("		writeRichTextOnJS('msg_corpo', '', 600, 200, true, false, 'divRTE', true);\n");
      
	  echo("        document.getElementById('acao').value = 'responde_mensagem';\n");
      echo("        document.getElementById('codRespondeMensagem').value = cod_msg;\n");
      echo("        document.getElementById('msg_titulo').value='Re: '+document.getElementById('titulo_'+cod_msg).innerHTML;\n");
      echo("      }\n");
    }
  }

  if($usr_formador){
    echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n\n");
    echo("      if (isNav)\n");
    echo("      {\n");
    echo("        document.captureEvents(Event.MOUSEMOVE);\n");
    echo("      }\n");
    echo("      document.onmousemove = TrataMouse;\n\n");

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
    echo("      }\n\n");

    echo("      function EscondeLayer(cod_layer)\n");
    echo("      {\n");
    echo("        hideLayer(cod_layer);\n");
    echo("      }\n\n");
  
    echo("      function EscondeLayers()\n");
    echo("      {\n");
    echo("        hideLayer(relevIni);\n");
    echo("      }\n\n");
  
    echo("      function MostraLayer(cod_layer,ajuste)\n");
    echo("      {\n");
    echo("        EscondeLayers();\n");
    echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("        showLayer(cod_layer);\n");
    echo("      }\n\n");
  
    echo("      function AlteraCodMsg(cod_msg, n_relev)\n");
    echo("      {\n");
    echo("        document.formRelevancia.cod_msg.value=cod_msg;\n");
    echo("        AlteraRelevLayer(cod_msg, n_relev);\n");
    echo("      }\n\n");
  
    echo("      function AlteraRelevLayer(cod_msg, n_relev)\n");
    echo("      {\n");
    echo("        spans = document.getElementsByTagName('span');\n");
    echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif'>\"\n");  
    echo("        for (i=0; i<spans.length; i++){\n");
    echo("          idTmp = spans[i].id.split('_');\n");
    echo("          if((idTmp[0])=='relevancia')\n");
    echo("            if((idTmp[1])==n_relev){\n");
    echo("              spans[i].innerHTML=imagem;\n");
    echo("            }else{\n");
    echo("              spans[i].innerHTML=\"&nbsp;\";\n");
    echo("            }\n");
    echo("        }\n");
    echo("        if (n_relev==-1) return;\n\n");
  
    echo("      }\n\n");
  }
  
  /* Se houver mensagens neste fórum cria a função de visualizar as demais páginas, */
  /* exibir mensagens e mudar a ordenação.                                          */
  if ($total_mensagens > 0)
  {
    echo("      function ExibirMensagem(cod_msg)\n");
    echo("      {\n");
    echo("        var browser=navigator.appName;\n\n");
    echo("		  var sts = document.getElementById('tr_msg_'+cod_msg).style.display;\n");
    echo("		  if ((sts == 'table-row') || (sts == 'block'))\n");
    echo("		  {\n");
    echo("			mensagens_abertas--;\n");
    echo("         	document.getElementById('tr_msg_'+cod_msg).style.display=\"none\";\n");
    echo("		  }");
    echo("		  else\n");
    echo("		  {\n");
    echo("        	if (browser==\"Microsoft Internet Explorer\")\n");
    echo("          	document.getElementById('tr_msg_'+cod_msg).style.display=\"block\";\n");
    echo("  	    else\n");
    echo("      	    document.getElementById('tr_msg_'+cod_msg).style.display=\"table-row\";\n");
    echo("        mensagens_abertas++;\n");
    echo("		  }");
    echo("        if(totalMsgs <= 10){\n");
    echo("          VerificaAbertas();\n");
    echo("        }\n");
    echo("      } \n");

    echo("      function VerificaAbertas(){\n");
    echo("        tabela = document.getElementById('tabelaMsgs');\n");
    echo("        final = tabela.rows.length-1;\n");
    echo("        var cont = 0\n");
    echo("        var browser=navigator.appName;\n\n");
    // i = 1, para evitar problema com a primeira linha da tabela, que eh o cabecalho
    echo("        for (i=1; i<final; i++){\n");
    echo("          if (!tabela.rows[i]) break;\n");
    echo("          if (browser==\"Microsoft Internet Explorer\"){\n");
    echo("            if (tabela.rows[i].style.display == \"block\"){\n");
    echo("              cont++;\n");
    echo("            }\n");
    echo("          }else{\n");
    echo("            if(tabela.rows[i].style.display == \"table-row\"){\n");
    echo("           cont++;\n");
    echo("             }\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if(cont == (totalMsgs*2)) {\n");

    echo("        controle=1;\n");
    echo("        while (controle<=5){\n");
    echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
    echo("          document.getElementById('paginacao_'+controle).className='';\n");
    echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
    echo("          controle++;\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_first').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_first').className = \"\";\n");
    echo("        document.getElementById('paginacao_first').innerHTML = \"\";\n");
    echo("        document.getElementById('paginacao_back').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_back').className = \"\";\n");
    echo("        document.getElementById('paginacao_back').innerHTML = \"\";\n");
    echo("        document.getElementById('paginacao_fwd').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_fwd').className = \"\";\n");
    echo("        document.getElementById('paginacao_fwd').innerHTML = \"\";\n\n");
    echo("        document.getElementById('paginacao_last').onclick = function(){};\n");
    echo("        document.getElementById('paginacao_last').className = \"\";\n");
    echo("        document.getElementById('paginacao_last').innerHTML = \"\";\n\n");

    /* 131 - Exibir por páginas */
    echo("         document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,131)."\";\n");
    echo("         document.getElementById('exibir_paginacao').onclick = function(){ VoltarPaginacao(pag_atual); };\n");
    echo("         mensagens_abertas=contador-1;\n");
    echo("         }\n");
    echo("      }\n");

    echo("      function FecharMsg(cod_msg){\n");
    echo("        tdElement= document.getElementById('td_msg_'+cod_msg);\n");
    echo("        divElement = document.getElementById('divNovaMsg');\n");
    echo("        if (tdElement.lastChild == divElement){\n");
    echo("          document.getElementById('divNovaMsg').className=\"divHidden\";");
    echo("          tdElement2 = document.getElementById('tdNovaMsg');\n");
    echo("          tdElement2.appendChild(document.getElementById('divNovaMsg'));\n");
    echo("          respondendoMsg = -1;\n");
    echo("        }\n");
    echo("        document.getElementById('tr_msg_'+cod_msg).style.display=\"none\";\n");
    echo("        mensagens_abertas--;\n");

    echo("        if(mensagens_abertas<totalMsgs){\n");
    /* 71 - Exibir todas */
    echo("         document.getElementById('exibir_paginacao').innerHTML = \"".RetornaFraseDaLista($lista_frases,71)."\";\n");
    echo("        document.getElementById('exibir_paginacao').onclick = function(){ ExibirTodasMsgs(); };\n");
    echo("         }\n");

    echo("        if(mensagens_abertas==0 && total_pag==1) VoltarPaginacao(pag_atual);\n");
    echo("      }\n\n");


    
    echo("      function MudaOrdenacao()\n");
    echo("      {\n");
    echo("        elementos = document.getElementById('ordem_foruns');\n");
    echo("        var ordem;\n");
    echo("        for (var i = 0; i < elementos.length; i++)\n");
    echo("        {\n");
    echo("          if (elementos.options[i].selected == true){\n");
    echo("            ordem = elementos.options[i].value;\n");
    echo("            break;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        document.location = 'ver_forum.php?cod_forum=".$cod_forum."&cod_curso=".$cod_curso."&ordem='+ordem;\n");
    echo("      }\n\n");
  }

  echo("    </script>\n\n");

  echo("<body onload=\"ExibirTodasMsgs(); self.print();\">");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

 
  /* 1 - Fóruns de Discussão */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1));
  
  /*********************************************
  Utilizado no cabeçalho
  $cod_pagina=1;
  if(($usr_formador)&&($AcessoAvaliacao))
     $cod_pagina=11;
  *********************************************/   
     
  /* Se estiver visualizando a Lixeira adiciona esta informa�o no cabe�lho. */
  if ($status == 'D')
  {
    /* 16 - Lixeira */
    echo(" - ".RetornaFraseDaLista($lista_frases_geral, 16));
    /********************************************
    $cod_pagina=2;
    if(($usr_formador)&&($AcessoAvaliacao))
       $cod_pagina=12;
    *********************************************/
  }
  
  /* 7 - Ver fórum */
  //echo(" - ".RetornaFraseDaLista($lista_frases, 7)." - ".$forum_dados['nome']);  
  echo(" - ".$forum_dados['nome']);

  if (($forum_dados['status'] == 'L') || (($forum_dados['status'] == 'R') && (! $permitido)) )
  {
    /* 40 - (somente leitura)                                   */
    echo(" <b>".RetornaFraseDaLista($lista_frases, 40)."</b>");
  }

  echo("          </h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  //echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  /* Verifica a permissão de visualização do fórum pelo usuário, caso um usuário não permitido faça acesso ao fórum diretamente pelo link */
  /* 111 - Visualização do fórum não disponível. */
  if (($forum_dados['status'] == 'G') && (!$permitido))
  {
    echo("          <b>".RetornaFraseDaLista($lista_frases, 111)."</b>");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit;
  }

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  echo("                  <li><a href=\"forum.php?cod_curso=".$cod_curso."&amp;status=".$status."\">");
  if (isset($status) && ($status=='D'))
  {
    /* 63 - Retornar à lixeira */
    echo(RetornaFraseDaLista($lista_frases, 63)."</a></li>\n");
  }else { // Quando não há status definido ou status = A
    /* 34 - Retornar à lista de fóruns */
    echo(RetornaFraseDaLista($lista_frases, 34)."</a></li>\n");
  }

  /* checa se o curso terminou ou não */
  $status_curso = RetornaStatusCurso($sock,$cod_curso);

  /* Se o status do fórum for Ativo (permite leitura e escrita) e se o usu�io N� */
  /* for um visitante, exibe um menu para compor mensagens.                        */
  /* Se o status do f�um for G ou R (apenas os usu�ios permitidos postam), mas o */
  /* usu�io n� for permitido, n� postam mensagens.                              */

  //if ( ($forum_dados['status'] == 'A') || (($forum_dados['status'] == 'G') && ($permitido)) || (($forum_dados['status'] == 'R') && ($permitido)) && (!$usr_visitante) )
  /*{

    if (($status_curso != 'E') || ($usr_formador))
    {

      /* 12 - Compor nova mensagem */
    /*  echo("                  <li><span onclick='ComporMensagem();'>".RetornaFraseDaLista($lista_frases, 12)."</span></li>\n");
    }
  }*/

  echo("                </ul>\n");


  if ($total_mensagens > 0)
  {

  }

  echo("              </td>\n");
  echo("            </tr>\n");

  if ( ($forum_dados['status'] == 'A') || (($forum_dados['status'] == 'G') && ($permitido)) || (($forum_dados['status'] == 'R') && ($permitido)) && (!$usr_visitante) ){

    echo("            <tr id=\"trNovaMsg\">\n");
    echo("              <td colspan=\"4\">\n");
    echo("                <table cellspacing=\"0\" class=\"tabInterna\" style=\"border-collapse:collapse;\">\n");

    echo("                  <tr>\n");

    if ($total_mensagens > 0){
      echo("                    <td id=\"tdNovaMsg\" align=\"left\"  style=\"padding: 0 5px 0 5px; background-color:#DCDCDC;\" width=\"70%\">\n");
      echo("                    <div id=\"divNovaMsg\" class=\"divHidden\">\n");
    }else{
      echo("                    <td id=\"tdNovaMsg\" align=\"left\" width=\"70%\" style=\"padding: 0 5px 0 5px;\">\n");
      echo("                      <div id=\"divNovaMsg\">\n");
    }
    
    echo("                        <form id=\"formCompor\" name=\"formCompor\" action=\"acoes.php\" onsubmit=\"return(TestaNome(document.formCompor));\" method=\"post\">\n");
    /* 9 - Título */
    echo("                          <b>".RetornaFraseDaLista($lista_frases,9)."</b><br />\n");
    echo("                          <input type=\"text\" id=\"msg_titulo\" name=\"msg_titulo\" size=40 maxlength=100 value='".$msg_titulo."' style=\"border: 2px solid #9bc;\" /><br /><br />\n");
    /* 14 - Mensagem */
    echo("                          <b>".RetornaFraseDaLista($lista_frases,14)."</b><br />\n");
    echo("                          <div id=\"text_divRTE\"></div>\n");

    echo("                          <br />\n");
    echo("                          <input type=\"hidden\" name=\"acao\" id=\"acao\" value=\"nova_msg\" />\n");
    echo("                          <input type=\"hidden\" name=\"codRespondeMensagem\" id=\"codRespondeMensagem\" value=\"\" />\n");
    echo("                          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  
    echo("                          <input type=\"hidden\" name=\"cod_forum\" value=\"".$cod_forum."\" />\n");  
    /* 18 - Ok */
    echo("                          <input type=\"submit\" class=\"input\" id=\"OKComent\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" style=\"margin-bottom:5px;\" />\n");
    /* 2 - Cancela */
    echo("                          <input type=\"button\" class=\"input\" id=\"cancComent\" onClick=\"CancelarNovaMsg();\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" style=\"margin-bottom:5px;\" />\n");

    echo("                        </form>\n");
    echo("                      </div>\n");
    echo("                    </td>\n");
    echo("                    <td style=\"background-color:#DCDCDC;\"></td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  }
  
  if ($total_mensagens > 0)
  {
    // Calcula o índice da primeira mensagem.
    $prim_msg_index = (($pag_atual - 1) * $msg_por_pag) + 1;
    // Calcula o índice da última mensagem.
    $ult_msg_index = $pag_atual * $msg_por_pag;

    // Se o índice da ltima mensagem for maior que o número de mensagens, então copia este 
    // para o índice da última mensagem.
    if ($ult_msg_index > $total_mensagens)
      $ult_msg_index = $total_mensagens;
    echo("            <tr class=\"head01\">\n");
    echo("              <td style=\"border:none;\">\n");
    /* 19 - Mensagens     */
    echo("                ".RetornaFraseDaLista($lista_frases, 19)." ");
    /* 20 - (             */
    echo(RetornaFraseDaLista($lista_frases, 20)."<span id=\"prim_msg_index\"></span>");
    /* 21 - a             */
    echo(" ".RetornaFraseDaLista($lista_frases, 21)."&nbsp;");
    /* 22 - de            */
    echo("<span id=\"ult_msg_index\"></span> ".RetornaFraseDaLista($lista_frases, 22)." ");
    /* 23 - )             */
    echo($total_mensagens.RetornaFraseDaLista($lista_frases, 23)."\n");
    echo("              </td>\n");
    //echo("              <td style=\"text-align:right;border:none\">\n");
    /* Se houver mensagens exibe a caixa de seleção do método de ordenação.           */
    /* 41 - Ordenar por:  */
    //echo("                ".RetornaFraseDaLista($lista_frases, 41)."\n");
    //echo("                <select name=\"ordem\" id=\"ordem_foruns\" onchange='MudaOrdenacao();' style=\"margin:5px 0 0 0;\">\n");
    /* 43 - Árvore */
    
    //echo("                  <option value='arvore'>".RetornaFraseDaLista($lista_frases, 43)."</option>\n");
//     if (isset($status) && ($status=='A'))
      //if($status != 'D')
      /* 130 - Relevância */
      //echo("                  <option value='relevancia'>".RetornaFraseDaLista($lista_frases, 130)."</option>\n");
    /* 45 - Autor */
    //echo("                  <option value='emissor'>".RetornaFraseDaLista($lista_frases, 45)."</option>\n");
    /* 44 - Data */
    //echo("                  <option value='data'>".RetornaFraseDaLista($lista_frases, 44)."</option>\n");
    ///* 46 - Título */
    //echo("                  <option value='titulo'>".RetornaFraseDaLista($lista_frases, 46)."</option>\n");
    //echo("                </select>\n");

  /* Procura e seleciona a ordenação escolhida. */
    echo("                <script type=\"text/javascript\">\n\n");
    echo("                  elementos = document.getElementById('ordem_foruns');\n");
    echo("                  for (var i = 0; i < elementos.length; i++)\n");
    echo("                  {\n");
    echo("                    if (elementos.options[i].value == '".$ordem."')\n");
    echo("                      elementos.options[i].selected = true;\n");
    echo("                  }\n");
    echo("                </script>\n\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    echo("              <td colspan=\"2\">\n");
    echo("                <table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"cellspadding:0pt;\" class=\"tabInterna\" id=\"tabelaMsgs\">\n");
    echo("                  <thead>\n");
    echo("                    <tr class=\"head\">\n");
    /* 8 - # */
    echo("                      <td width=\"5%\">".RetornaFraseDaLista($lista_frases, 8)."</td>\n");
    /* 9 - T�ulo */
    echo("                      <td class=\"alLeft\" width=\"35%\">".RetornaFraseDaLista($lista_frases, 9)."</td>\n");
    /* 10 - Autor */
    echo("                      <td width=\"25%\">".RetornaFraseDaLista($lista_frases, 10)."</td>\n");
//     if (isset($status) && ($status=='A'))
    if($status!='D')
    /* 130 - Relevancia */
    echo("                      <td width=\"25%\">".RetornaFraseDaLista($lista_frases, 130)."</td>\n");
    /* 11 - Data */
    echo("                      <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 11)."</td>\n");
    echo("                    </tr>\n");
    echo("                  </thead>\n");
    /* Calcula o índice da mensagem com base no nmero da página. */
    $msgidx = 0;
    
    /*115 - N&atilde;o Relevante
      116 - Pouco Relevante
      117 - Relevância Média
      118 - Relevante
      119 - Muito Relevante    */
    $array_rel = array();

    $array_rel_frases[0] = RetornaFraseDaLista($lista_frases, 115);
    $array_rel_frases[1] = RetornaFraseDaLista($lista_frases, 116);
    $array_rel_frases[2] = RetornaFraseDaLista($lista_frases, 117);
    $array_rel_frases[3] = RetornaFraseDaLista($lista_frases, 118);
    $array_rel_frases[4] = RetornaFraseDaLista($lista_frases, 119);

    $query = "SELECT FMR.cod_msg, FMR.relevancia FROM Forum_mensagens_relevancia FMR, Forum_mensagens FM WHERE FM.cod_forum=$cod_forum AND FM.cod_msg=FMR.cod_msg";

    $res = Enviar($sock, $query);
    $tuplas = RetornaArrayLinhas($res);
    //Aqui formamos um array em que o cod_msg é o índice e seu contedo codigo da relevância
    
    $array_relevancia = array();
    if (is_array($tuplas)) {
       foreach ($tuplas as $cod_tupla => $linha)
       {
         $array_relevancia[intval($linha['cod_msg'])] = $linha['relevancia'];
       }
    }
    for($num_paginas=1; $num_paginas<=$total_pag; $num_paginas++){
    
      if ($num_paginas == $pag_atual) $style = "";
      else $style = "display:none";
    
    //neste la� listamos todas as mensagens desta p�ina
      foreach ($array_mensagens[$num_paginas] as $cod_msg => $dados)
      {
        if ($dados['data'] > $penult_acesso)
        {
          $bopen_tag = "<b>";
          $bclose_tag = "</b>";
        }
        else
        {
          $bopen_tag = " ";
          $bclose_tag = " ";
        }
        //124 - Relevância não avaliada
          //Atualizamos aqui a Relevancia no Layer. Caso a relevância não tenha sido avaliada passamos o valor -1 para atribuir false a todos os campos do Layer de relevancia.
        if ($array_relevancia[$cod_msg]==''){
              $array_mensagens['relevancia'] = RetornaFraseDaLista($lista_frases, 124);
              $prop_relevancia = "-1";
          }
        else{
              $array_mensagens['relevancia'] = $array_rel_frases[$array_relevancia[$cod_msg]];
              $prop_relevancia = $array_relevancia[$cod_msg];
          }
  
        echo("                  <tr style=\"".$style."\" id=\"tr_".$cod_msg."\" class=\"altColor".($msgidx%2)."\">\n");
        echo("                    <td  width=\"5%\">".($msgidx + 1).".</td>\n");
        echo("                    <td  width=\"35%\" class=\"alLeft\">");
  
        if ($ordem == 'arvore')
        {
          /* Identa a mensagem de acordo com o nível em que ela se encontra. */
          for ($k = 0; $k < $dados['nivel']; $k++)
            echo("&nbsp;&nbsp;&nbsp;");
        }
  
        echo($bopen_tag."<span id=\"titulo_".$cod_msg."\" class=\"link\" onclick='ExibirMensagem(".$cod_msg.");'>");
        echo($dados['titulo']."</span>".$bclose_tag."</td>\n");
        echo("                    <td  width=\"25%\"><span class=\"link\" onClick='OpenWindowPerfil(".$dados['cod_usuario'].");'>");
        echo($bopen_tag.NomeUsuario($sock, $dados['cod_usuario'], $cod_curso).$bclose_tag."</span>&nbsp;</td>\n");
//         if (isset($status) && ($status=='A')){
          if($status != 'D'){
          if ($usr_formador)
            echo("                    <td width=\"25%\"><span class=\"link\" id=\"msg_".$cod_msg."\" onclick='AlteraCodMsg(".$cod_msg.", ".$prop_relevancia."); MostraLayer(relevIni,0);'>".$bopen_tag.$array_mensagens['relevancia'].$bclose_tag);
          else
            echo("                    <td width=\"25%\">".$bopen_tag.$array_mensagens['relevancia'].$bclose_tag);
            echo("</span>&nbsp;</td>\n");
        }
        echo("                    <td width=\"10%\" id=\"data_msg_".$cod_msg."\">".$bopen_tag.UnixTime2Data($dados['data']).$bclose_tag."</td>\n");
        echo("                  </tr>\n");

        echo("                  <tr style=\"display:none;\" id=\"tr_msg_".$cod_msg."\">");
        echo("                    <td width=\"5%\">&nbsp;</td>\n");
        echo("                    <td style=\"width:50px\" colspan=".($status == 'D'?"2":"3")." id=\"td_msg_".$cod_msg."\" align=\"left\">\n");

        echo("                      <div><b>".RetornaFraseDaLista($lista_frases,14).":</b><br /><br /><div class=\"divRichText\">". PreparaExibicaoMensagem($dados['mensagem'])."</div>\n");
        echo("                      </div></td>\n");
        echo("                    <td width=\"25%\" id=\"td_close".$cod_msg."\">\n");
        //echo("                      <span class=\"link\" id=\"fechar_".$cod_msg."\" onclick=\"FecharMsg(".$cod_msg.");\">Fechar</span><br />\n");


        /* Se o status do fórum for Ativo (permite leitura e escrita) e se o usu�io N� */
        /* for um visitante, exibe um menu para compor mensagens.                        */
        /* Se o status do f�um for G ou R (apenas os usu�ios permitidos postam), mas o */
        /* usu�io n� for permitido, n� postam mensagens.                              */    
        /*if ( (($forum_dados['status'] == 'A') || (($forum_dados['status'] == 'G') && ($permitido)) || (($forum_dados['status'] == 'R') && ($permitido))) && (!$usr_visitante) )
        {
          if (($status_curso != 'E') || ($usr_formador))
          {
            //echo("                      <span class=\"link\" id=\"responder_".$cod_msg."\" onclick=\"ResponderMsg(".$cod_msg.");\">Responder</span><br />\n");
          }
        }*/
        echo("                    </td>\n");
        echo("                  </tr>\n");

        /* Incrementa o contador do índice da mensagem. */
        $msgidx++;
      }

    }
  }else{
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <table border=\"0\" width=\"100%\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                  <tr class=\"head\">\n");
    /* 8 - # */
    echo("                    <td width=\"5%\">".RetornaFraseDaLista($lista_frases, 8)."</td>\n");
    /* 9 - Título */
    echo("                    <td width=\"35%\">".RetornaFraseDaLista($lista_frases, 9)."</td>\n");
    /* 10 - Autor */
    echo("                    <td width=\"25%\">".RetornaFraseDaLista($lista_frases, 10)."</td>\n");
//     if (isset($status) && ($status='A'))
       if($status!='D')
      /* 130 - Relevancia */
      echo("                    <td width=\"25%\">".RetornaFraseDaLista($lista_frases, 130)."</td>\n");
    /* 11 - Data */
    echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 11)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr class=\"altColor1\">\n");
    echo("                    <td colspan=\"6\" align=\"center\">\n");
    /* 31 - Não há mensagens neste fórum. */
    echo("                      ".RetornaFraseDaLista($lista_frases, 31)."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");

  }
  /* Se o número de mensagens for superior ao nmero de mensagens exibidas por */
  /* página, possibilita a criação de links para a página anterior e posterior */
  /* (se existirem) e links para demais páginas.                               */

  echo("                  <tr>\n");  
  echo("                    <td colspan=\"5\" align=\"right\" class=\"paginacao\">\n");
  echo("                      <span id=\"paginacao_first\"></span> <span id=\"paginacao_back\"></span>\n");
  $controle=1;
  while ($controle<=5){
    echo("                      <span id=\"paginacao_".$controle."\"></span>\n");
    $controle++;
  }
  echo("                      <span id=\"paginacao_fwd\"></span> <span id=\"paginacao_last\"></span>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // Se houver mensagens cria o botão para exibir todas as mensagens.
  if ($total_mensagens > 0)
  {
     /* 71 - Exibir todas */
    echo("                  <li><span id=\"exibir_paginacao\" onclick=\"ExibirTodasMsgs();\">".RetornaFraseDaLista($lista_frases, 71)."</span></li>\n");
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");


  //div do Layer de alteração de relevância
  echo("          <div id='relev' class=\"popup\">\n");
  echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(relevIni);\"><img src=\"../imgs/btClose.gif\" alt=\"".RetornaFraseDaLista($lista_frases,138)."\" border=\"0\" /></span></div>\n");
  echo("            <div class=\"int_popup\">\n");
  echo("              <form method=\"post\" id=\"formRelevancia\" name=\"formRelevancia\" action=\"\">\n");
  //  2 - Cancelar
  echo("                <input type=\"hidden\" name=\"cod_msg\" value=\"\" />\n");
  echo("                <input type=\"hidden\" name=\"cod_curso\" value=\"$cod_curso\" />\n");
  echo("                <input type=\"hidden\" name=\"cod_forum\" value=\"$cod_forum\" />\n");
  echo("                <input type=\"hidden\" name=\"nova_relevancia\" id=\"nova_relevancia\" value=\"\" />\n");
  echo("                <input type=\"hidden\" name=\"texto_feedback\" id=\"texto_feedback\" value=\"".RetornaFraseDaLista($lista_frases, 128)."\" />\n");
  echo("              </form>\n");
  //120 - Selecione a nova relevância desejada:
  echo("              ".RetornaFraseDaLista($lista_frases, 120)."\n");
  echo("              <ul class=\"ulPopup\">\n");
  for ($i=0; $i<5; $i++){
    echo("                <li onclick=\"document.formRelevancia.nova_relevancia.value='".$i."'; xajax_MudarRelevanciaDinamic(xajax.getFormValues('formRelevancia'), '".$array_rel_frases[$i]."'); EscondeLayers();\">\n");
    echo("                  <span class=\"check\" id=\"relevancia_".$i."\"></span>\n");
    echo("                  <span>".$array_rel_frases[$i]."</span>\n");
    echo("                </li>\n");
  }
  echo("              </ul>\n");
  echo("            </div>\n");
  echo("          </div>\n");

  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>