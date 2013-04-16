<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/ver_sessoes_realizadas.php

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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
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
  ARQUIVO : cursos/aplic/batepapo/ver_sessoes_realizadas.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  include("avaliacoes_batepapo.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->registerFunction("MudarStatusDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  
  if ($lixeira == "sim")
  	$cod_pagina_ajuda=5;
  else
  	$cod_pagina_ajuda=4;
  	
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 82 - Sess�es recuperadas com sucesso.
  // 104 - Erro na recupera��o das sess�es.
  $feedbackObject->addAction("recuperar_sessao", 82, 104);
  // 105 - Sess�es apagadas com sucesso. As sess�es foram movidas para a lixeira.
  // 106 - Erro ao mover as sess�es para a lixeira.
  $feedbackObject->addAction("apagar_sessao", 76, 106);
  // 76 - Sess�es apagadas com sucesso.
  // 107 - Erro ao apagar as sess�es.
  $feedbackObject->addAction("excluir_sessao", 76, 107);

  /* tela_topo.php faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); */

  /* Encerra sess�o anterior, se n�o tiver ningu�m online */
  $cod_sessao=RetornaSessaoCorrente($sock);
  if (VerificaRetiradaOnline($sock))
  {
    LimpaOnline($sock,$cod_curso, 90);
  }

  if (!VerificaOnline($sock))
  {
    /* Todas as pessoas foram retiradas. Encerramos a sessao ent�o */
    EncerraSessao($sock,$cod_curso,$cod_sessao);
    $cod_sessao=RetornaSessaoCorrente($sock);
  }

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  /***********************
    Codigo Javascript
   ***********************/
  // echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
  echo("<script type=\"text/javascript\" language=JavaScript>\n\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
            $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("    startList(); \n");
  echo("  } \n");

  /* Abre a janela de exibi��o da sess�o escolhida */
  echo("  function AbreSessao(cod_ses)\n");
  echo("  {\n");
  echo("    window.open('ver_sessao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_sessao='+cod_ses,'Sessao','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("    return false;\n");
  echo("  }\n");

  if ($lixeira!="sim")
  {
    // Se estiver visualizando as sess�es de bate_bapo realizadas ent�o obt�m o layer para avalia��o
    echo("  function iniciar()\n");
    echo("  {\n");
    echo("    lay_avaliacao = getLayer(\"layer_avaliacao\");\n");
    echo("    EscondeLayers(); \n");
    echo("  }\n");
  }

  $e_formador       = EFormador($sock,$cod_curso,$cod_usuario);
  $e_aluno          = EAluno($sock, $cod_curso, $cod_usuario);
  $usr_conv_ativo   = EConvidadoAtivo($sock, $cod_usuario, $cod_curso);
  $usr_conv_passivo = EConvidadoPassivo($sock, $cod_usuario, $cod_curso);

  if(($AcessoAvaliacao)&&($lixeira!="sim"))
  {

    /* Se estiver visualizando as sess�es de bate_bapo realizadas ent�o cria as fun��es JavaScript */
    /* ApagarAvaliacao(id), AlterarAvaliacao(id) e VerNotas(id).                                   */
      if ($lixeira!="sim")
      {
        if ($e_formador)
        {
          echo("    function CriarAvaliacao(id)\n");
          echo("    {\n");
          echo("      document.frmSessao.cod_sessao.value = id;\n");
          echo("      document.frmSessao.action = \"../avaliacoes/criar_avaliacao_batepapo.php?".RetornaSessionID());
          echo("&origem=../batepapo/ver_sessoes_realizadas\";\n");
          echo("      document.frmSessao.submit();\n");
          echo("    }\n\n");

          // Abre a janela com a lista de Participantes para ser avaliado
          echo("  function AvaliarParticipantes(id)\n");
          echo("  {\n");
          echo("    window.open('../avaliacoes/avaliar_participantes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao='+id,'AvaliarParticipantes','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
          echo("    return false;\n");
          echo("  }\n");
        }

        echo("  function VerNotas(id)\n");
        echo("  {\n");
        echo("    window.open(\"../avaliacoes/ver_notas.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+id,\"VerNotas\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
        echo("    return(false);\n");
        echo("  }\n");

        echo("  function VerificarParticipacao(id)\n");
        echo("  {\n");
        echo("    window.open(\"../avaliacoes/ver_participacao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&origem=ver&VeioDaAtividade=1&cod_avaliacao=\"+id,\"VerParticipacao\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
        echo("    return(false);\n");
        echo("  }\n");

        echo("  function HistoricodoDesempenho(id)\n");
        echo("  {\n");
        echo("    window.open(\"../avaliacoes/historico_desempenho_todos.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+id,\"HistoricoDesempenho\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
        echo("    return(false);\n");
        echo("  }\n");

        echo("  function Ver(id)\n");
        echo("  {\n");
        echo("    window.open(\"../avaliacoes/ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&EhAluno=".$e_aluno."&cod_avaliacao=\"+id,\"VerAvaliacao\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
        echo("    return(false);\n");
        echo("  }\n");
      }
  }
  echo("  function VerSessao(id)\n");
  echo("  {\n");
  echo("    window.open('ver_sessao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_sessao='+id,'Sessao','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("    return false;\n");
  echo("  }\n\n");

  echo("  function VerAvaliacaoLixeira(id)\n");
  echo("  {\n");
  echo("    window.open('../avaliacoes/ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&EhLixeiraDaAtividade=1&cod_avaliacao='+id,'VerAvaliacao','width=450,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function OpenWindow() \n");
  echo("  {\n");
  echo("    window.open(\"entrar_sala.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\",\"Batepapo\",\"width=1000,height=700,top=50,left=50,scrollbars=no,status=yes,toolbar=no,menubar=no,resizable=no\");\n");
  echo("  }\n");

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
  if ($lixeira=="sim")
  {
    echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
    echo("          document.getElementById('mRecup_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mRecup_Selec').onclick=function(){ RecuperarSelecionados(); };\n");
  }
  else
  {
    echo("          document.getElementById('mMov_Lix_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mMov_Lix_Selec').onclick=function(){ MoverLixeiraSelecionados(); };\n");
  }
  echo("        }else{\n");
  if ($lixeira=="sim")
  {
    echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mExcluir_Selec').onclick=function(){};\n");
    echo("          document.getElementById('mRecup_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mRecup_Selec').onclick=function(){};\n");
  }
  else
  {
    echo("          document.getElementById('mMov_Lix_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mMov_Lix_Selec').onclick=function(){};\n");
  }
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
  
  echo("      function ExcluirSelecionados(){\n");
  /* 79 - Tem certeza que deseja apagar definitvamente as sess�es selecionadas?  */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,79)."')){\n");
  echo("          xajax_MudarStatusDinamic('".$cod_curso."', array_itens, 'X');\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function RecuperarSelecionados(){\n");
  /* 80 - Tem certeza que deseja recuperar as sess�es selecionadas? */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,80)."')){\n");
  echo("          xajax_MudarStatusDinamic('".$cod_curso."', array_itens, 'A');\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function MoverLixeiraSelecionados(){\n");
  /* 75 - Tem certeza que deseja apagar as sess�es selecionadas? (as sess�es ser�o movidas para a lixeira.) */
  /* 101 - (Se houver avalia��es relacionadas, elas tamb�m ser�o movida para a lixeira DAS AVALIA��ES)*/
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,75)." (".RetornaFraseDaLista($lista_frases,101).")')){\n");
  echo("          xajax_MudarStatusDinamic('".$cod_curso."', array_itens, 'L');\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    function Recarregar(status){\n");
  echo("      if(status=='A') \n");
  echo("        document.location='ver_sessoes_realizadas.php?cod_curso=".$cod_curso."&acao=recuperar_sessao&atualizacao=true';");
  echo("      if(status=='X') \n");
  echo("        document.location='ver_sessoes_realizadas.php?cod_curso=".$cod_curso."&lixeira=sim&acao=excluir_sessao&atualizacao=true';");
  echo("      if(status=='L') \n");
  echo("        document.location='ver_sessoes_realizadas.php?cod_curso=".$cod_curso."&acao=apagar_sessao&atualizacao=true';");
  echo("    }\n\n");

  echo("</script>\n");

  $objAjax->printJavascript("../xajax_0.2.4/");

  include("../menu_principal.php");

  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Bate-Papo */
  echo("<h4>".RetornaFraseDaLista($lista_frases, 1));

  if ($lixeira=="sim")
  {
    /* 27 - Ver sess�es realizadas */
    /* 78 - Lixeira */
    echo(" - ".RetornaFraseDaLista($lista_frases,78)."</h4>\n");
    $cod_pagina=5;
    if(($AcessoAvaliacao)&&($e_formador))/*Pare exibir a ajuda de avalia��es*/
      $cod_pagina=11;
  }
  else
  {
    /* 27 - Ver sess�es realizadas */
    echo(" - ".RetornaFraseDaLista($lista_frases,27)."</h4>\n");
    $cod_pagina=4;
    if(($AcessoAvaliacao)&&($e_formador))/*Pare exibir a ajuda de avalia��es*/
       $cod_pagina=10;
  }

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<div id=\"mudarFonte\">\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("</div>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");

//   /* ------------------------------------------
//      Verifica��o de Sess�o Marcadas e Em andamento
//      ------------------------------------------ */
//   $sessoes=RetornaListaSessoesMarcadas($sock);
//   if (count($sessoes)>0)
//   {
//     echo("  <tr>\n");
//     echo("    <td valign=\"top\">\n");
//     /* <!----------------- Tabela Interna -----------------> */
//     echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
//     echo("        <tr class=\"head01\">\n");
//     if ($sessoes[0]['data_inicio']>=time())
//       /* 55 - Pr�xima sess�o marcada */
//       echo("          <td colspan=6>".RetornaFraseDaLista($lista_frases,55).": \n");
//     else
//       /* 72 - Sess�o em Andamento */
//       echo("          <td colspan=6>".RetornaFraseDaLista($lista_frases,72).": \n");
// 
//     /* 56 - de */
//     echo("            \"".$sessoes[0]['assunto']."\", ".RetornaFraseDaLista($lista_frases,56)." ".Unixtime2DataHora($sessoes[0]['data_inicio']));
//     /* 57 - a */
//     echo("            ".RetornaFraseDaLista($lista_frases,57)." ".Unixtime2DataHora($sessoes[0]['data_fim'])." - \n");
// 
//     if (count($sessoes)>1)
//     {
//       /* 58 - Ver pr�ximas sess�es marcadas */
//       echo("          <a href=ver_sessoes_marcadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso.">(".RetornaFraseDaLista($lista_frases,58).")</a>\n");
//     }
//     echo("          </td>\n");
//     echo("        </tr>\n");
//     // Fim Tabela Interna
//     echo("      </table>\n");
//     echo("    </td>\n");
//     echo("  </tr>\n");
//   }

  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <ul class=\"btAuxTabs\">\n");
  /* 27 - Ver sess�es realizadas */
  echo("        <li><span onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 27)."</span></li>\n");
  if ($e_formador)
  {
    /* 47 - Marcar sess�o */
    echo("        <li><span onClick=\"document.location='marcar_sessao.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
    /* 63 - Desmarcar sess�es */
    echo("        <li><span onClick=\"document.location='desmarcar_sessoes.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 63)."</span></li>\n");

    /* 78 - Lixeira */
    echo("        <li><span onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;lixeira=sim';\">".RetornaFraseDaLista($lista_frases, 78)."</span></li>\n");
  }
  /* 55 - Pr�xima sess�o marcada */
  echo("        <li><span onClick=\"document.location='ver_sessoes_marcadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 55)."</span></li>\n");

  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <form name=frmSessao action=\"\" method=post>\n");
  //echo(RetornaSessionIDInput());
  echo("        <input type=hidden name=cod_curso value=".$cod_curso." />\n");
  /* Passa o cod_avaliacao para executar a��es sobre ela.       */
  echo("        <input type=hidden name=cod_avaliacao value=-1 />\n");
  echo("        <input type=hidden name=cod_sessao value=-1 />\n");
  echo("        <input type=hidden name=VeiodeSessoesRealizadas value=1 />\n");
  echo("      </form>\n");

  /* <!----------------- Tabela Interna -----------------> */
  echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  if ($e_formador)
  {
    echo("          <td><input class=\"input\" type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  }
  /* 40 - Assunto da Sess�o */
  echo("          <td width=30%>".RetornaFraseDaLista($lista_frases,40)."</td>\n");
  /* 41 - Data */
  echo("          <td width=20%>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
  /* 29 - In�cio */
  echo("          <td width=20%>".RetornaFraseDaLista($lista_frases,29)."</td>\n");
  /* 30 - Fim */
  echo("          <td width=20%>".RetornaFraseDaLista($lista_frases,30)."</td>\n");
  /* 88 - Avalia��o */
  if($AcessoAvaliacao)
    echo("          <td width=10%>".RetornaFraseDaLista($lista_frases,88)."</td>\n");

  echo("        </tr>\n");

  if ($lixeira=="sim")
    $lista=RetornaListaSessoes($sock,'L');
  else
    $lista=RetornaListaSessoes($sock,'A');

  $i=0;
  $reg=count($lista);   //numero de sessoes a serem listadas
  $num=0;       // contador para registros listados
  if (count($lista)>0)
  {
    foreach($lista as $cod => $linha)
    {
      $num++;
      echo("    <tr>\n");

      $i = ($i + 1) % 2;
      if ($e_formador)
        echo("      <td><input class=\"input\" type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha['cod_sessao']."\" onclick='VerificaCheck();' value=\"".$cod_item."\" /></td>\n");

      if ($lixeira=="sim")
      {
        echo("      <td><a href=# onClick=return(AbreSessao(".$linha['cod_sessao']."));>");
        echo("<img src=../imgs/trash.gif border=0 />");
        echo($linha['Assunto']."</a></td>\n");
      }
      else
      {
        echo("      <td><a href=# onClick=return(AbreSessao(".$linha['cod_sessao']."));>");
        echo("<img src=../imgs/icForum.gif border=0 />");
        echo($linha['Assunto']."</a></td>\n");
      }

      echo("      <td class=text align=center>".Unixtime2Data($linha['DataInicio'])."</td>\n");
      echo("      <td class=text align=center>".Unixtime2Hora($linha['DataInicio'])."</td>\n");
      echo("      <td class=text align=center>".Unixtime2Hora($linha['DataFim'])."</td>\n");
       /*5 - (Sess�o n�o agendada)*/
      if($AcessoAvaliacao)
      {
        /* Pode ocorrer de uma sess�o de bate-papo fazer parte de mais de um assunto 
	   Desse modo, devemos determinar se h� uma avalia��o para cada um desses assuntos
	 */
	/* Os assuntos s�o montados na fun��o RetornaListaSessoes, e se houver mais de um, eles s�o
           separados por um <br/>	 
	 */
	$assunto_unico = explode("<br/>", $linha['Assunto']); 
        echo("      <td class=text align=center>");

	foreach($assunto_unico as $assunto_u){ 	
		if (strcmp($linha['Assunto'], RetornaFraseDaLista($lista_frases,5)))
		{
			if (BatePapoEhAvaliacao($sock,$assunto_u,$linha['DataInicio'],$linha['DataFim']))
			{
				$cod_assunto=RetornaCodAssunto($sock,$assunto_u,$linha['DataInicio'],$linha['DataFim']);
				$cod_avaliacao=RetornaCodAvaliacao($sock,$cod_assunto);
				$foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod_usuario);
				
				if ($e_aluno || $usr_conv_ativo || $usr_conv_passivo)
				{
					// G 35 - Sim
					echo(RetornaFraseDaLista($lista_frases_geral,35)."<br/>");
				}
				elseif ($e_formador)
				{
						// G 35 - Sim
						echo("<a class=text href=# onClick='VerAvaliacaoLixeira(".$cod_avaliacao.");return(false);'>".RetornaFraseDaLista($lista_frases_geral,35)."</a><br/>");	
				}else
				{
				/* 95 - erro interno... */
				echo (RetornaFraseDaLista($lista_frases,95));
				}
			}
			elseif (($lixeira=="sim") && (BatePapoEraAvaliacao($sock,$assunto_u,$linha['DataInicio'],$linha['DataFim'])))
			{
				$cod_assunto=RetornaCodAssunto($sock,$assunto_u,$linha['DataInicio'],$linha['DataFim']);
				$cod_avaliacao=RetornaCodAvaliacaoApagada($sock,$cod_assunto);
				/* 35 - Sim */
				echo("<a class=text href=# onClick='VerAvaliacaoLixeira(".$cod_avaliacao.");return(false);'>".RetornaFraseDaLista($lista_frases_geral,35)."</a><br/>");
			}
			else
			/* 36 - N�o */
			echo("<font class=text>".RetornaFraseDaLista($lista_frases_geral,36)."</font><br/>");
		}
		else
		/* 36 - N�o */
		echo("<font class=text>".RetornaFraseDaLista($lista_frases_geral,36)."</font><br/>");
		}
		
	}//fim � do foreach
	echo("</td>\n");
	echo("    </tr>\n");
    }//fim do acesso avalia��o
  }
  else
  {
    echo("        <tr>\n");
    /* 42 - (N�o existe nenhuma sess�o realizada neste per�odo) */
    echo("          <td colspan=6>".RetornaFraseDaLista($lista_frases,42)."</td>\n");
    echo("        </tr>\n");
  }

  // Fim Tabela Interna
  echo("      </table>\n");

  echo("      <ul class=\"btAuxTabs03\">\n");
  /* 2 - Entrar na sala de bate-papo */
  echo("        <li><span onClick=\"OpenWindow();\">".RetornaFraseDaLista($lista_frases, 2)."</span></li>\n");
  echo("      </ul>\n");

  if ($e_formador)
  {
    if ($lixeira=="sim")
    {
      echo("<ul>\n");
      /* 74 - Apagar selecionadas */
      echo("  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".RetornaFraseDaLista($lista_frases,74)."</span></li>\n");
      /* 81 - Recuperar selecionadas */
      echo("  <li id=\"mRecup_Selec\" class=\"menuUp\"><span id=\"recuperarSelec\">".RetornaFraseDaLista($lista_frases,81)."</span></li>\n");
      echo("</ul>\n");
    }
    else
    {
      echo("<ul>\n");
      /* 74 - Apagar selecionadas */
      echo("  <li id=\"mMov_Lix_Selec\" class=\"menuUp\"><span id=\"emoverSelec\">".RetornaFraseDaLista($lista_frases,74)."</span></li>\n");
      echo("</ul>\n");
    }
  }

  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");

  include("../tela2.php");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>