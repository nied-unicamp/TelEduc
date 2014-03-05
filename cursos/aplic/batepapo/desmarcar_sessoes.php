<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/desmarcar_sessoes.php

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
  ARQUIVO : cursos/aplic/batepapo/desmarcar_sessoes.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  include("avaliacoes_batepapo.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->register(XAJAX_FUNCTION,"DesmarcaSessaoDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 67 - Sess�es desmarcadas com sucesso.
  // 116 - Erro ao desmarcar a sess�o.
  $feedbackObject->addAction("desmarcar_sessao", 67, 116);

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22); /*verifica se avaliacao est� disponibilizada */

  $e_formador=EFormador($sock,$cod_curso,$cod_usuario);

  echo("    <script type=\"text/javascript\" language=\"javascript\">\n\n");

  if($AcessoAvaliacao){
     echo("      function VerAvaliacao(id)\n");
     echo("      {\n");
     echo("        window.open(\"../avaliacoes/ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+id,\"VerAvaliacao\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
     echo("        return(false);\n");
     echo("      }\n");
  }

  echo("      function Iniciar() \n");
  echo("      {\n");
                $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList(); \n");
  echo("      }\n");

  echo("      function OpenWindow() \n");
  echo("      {\n");
  echo("        window.open(\"entrar_sala.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\",\"Batepapo\",\"width=1000,height=700,top=50,left=50,scrollbars=no,status=yes,toolbar=no,menubar=no,resizable=no\");\n");
  echo("        return(false);\n");
  echo("      }\n");

  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        var Cabecalho = document.getElementById('checkMenu');\n");
  echo("        var array_itens = new Array();\n");
  echo("        for (i=0; i < cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  //echo("            var item = cod_itens[i].id.split('_');\n");
  echo("            array_itens[j]=cod_itens[i].value;\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if(j > 0){\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(array_itens); };\n");
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

  echo("      function ExcluirSelecionados(array_itens){\n");
  /* 73 - Tem certeza que deseja desmarcar as sess�es selecionadas? */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,73)."')){\n");
  echo("          xajax_DesmarcaSessaoDinamic('".$cod_curso."', array_itens, '".$cod_usuario."');\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function Recarregar(){\n");
  echo("        document.location='desmarcar_sessoes.php?cod_curso=".$cod_curso."&acao=desmarcar_sessao&atualizacao=true';");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Bate-Papo */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 63 - Desmarcar sess�es */
  echo(" - ".RetornaFraseDaLista($lista_frases,63)."</h4>");
  $cod_pagina=8;
  if(($AcessoAvaliacao)&&($e_formador))/*Pare exibir a ajuda de avalia��es*/
    $cod_pagina=14;

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("<div id=\"mudarFonte\">\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	<a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("</div>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <ul class=\"btAuxTabs\">\n");
  /* 27 - Ver sess�es realizadas */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 27)."\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 27)."</span></li>\n");
  if ($e_formador)
  {
    /* 47 - Marcar sess�o */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 47)."\" onClick=\"document.location='marcar_sessao.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
    /* 63 - Desmarcar sess�es */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 63)."\" onClick=\"document.location='desmarcar_sessoes.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 63)."</span></li>\n");

    /* 78 - Lixeira */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 78)."\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;lixeira=sim';\">".RetornaFraseDaLista($lista_frases, 78)."</span></li>\n");
  }
  /* 55 - Pr�xima sess�o marcada */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 55)."\" onClick=\"document.location='ver_sessoes_marcadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 55)."</span></li>\n");

  echo("      </ul>\n");

  echo("    </td>\n");
  echo("  </tr>\n");

  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  /* prepara a exibi��o dos campos a serem preenchidos */
  /* 73 - Tem certeza que deseja desmarcar as sess�es selecionadas? */
  echo("      <form action=\"desmarcar_sessoes2.php\" method=\"post\" onSubmit=\"return(confirm('".RetornaFraseDaLista($lista_frases,73)."'));\">\n");
  //echo(RetornaSessionIDInput());
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");

  echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  if ($e_formador)
  {
    echo("          <td width=1%><input class=\"input\" type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  }
  /* 40 - Assunto da Sess�o */
  echo("          <td>".RetornaFraseDaLista($lista_frases,40)."</td>\n");
  /* 41 - Data */
  echo("          <td width=10%>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
  /* 29 - In�cio */
  echo("          <td width=10%>".RetornaFraseDaLista($lista_frases,29)."</td>\n");
  /* 30 - Fim */
  echo("          <td width=10%>".RetornaFraseDaLista($lista_frases,30)."</td>\n");
  if($AcessoAvaliacao)
  {
    /* 88 - Avalia��o */
    echo("          <td width=10%>".RetornaFraseDaLista($lista_frases,88)."</td>\n");
  }
  echo("        </tr>\n");


  $lista=RetornaListaSessoesMarcadas($sock,$d_inicio,$d_fim);

  $i=0;
  if (count($lista)>0 && $lista!="" )
  {
    foreach($lista as $cod => $linha)
    {
      if ($linha['data_inicio']>=time())
      {
        if ($i==0)
          echo("    <tr>\n");
        else
          echo("    <tr>\n");
        $i = ($i + 1) % 2;
        if ($e_formador)
        {
          //echo("      <td><input class=\"input\" type=\"checkbox\" name=\"cod_assunto_desmarcar[]\" value=\"".$linha['cod_assunto']."\" /></td>\n");
          echo("      <td><input class=\"input\" type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha['cod_assunto']."\" onclick='VerificaCheck();' value=\"".$linha['cod_assunto']."\" /></td>\n");
        }
        echo("      <td>".$linha['assunto']."</td>\n");
        echo("      <td>".Unixtime2Data($linha['data_inicio'])."</td>\n");
        echo("      <td>".Unixtime2Hora($linha['data_inicio'])."</td>\n");
        echo("      <td>".Unixtime2Hora($linha['data_fim'])."</td>\n");

        if($AcessoAvaliacao){
          if (BatePapoEhAvaliacao($sock,$linha['assunto'],$linha['data_inicio'],$linha['data_fim']))
          {
            $cod_assunto=RetornaCodAssunto($sock,$linha['assunto'],$linha['data_inicio'],$linha['data_fim']);
            $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_assunto);
            /* 35 - Sim*/
            echo("<td class=\"text\" align=center><a class=\"text\" href=\"#\" onClick='VerAvaliacao(".$cod_avaliacao.");return(false);'>".RetornaFraseDaLista($lista_frases_geral,35)."</a></td>");
          }
          else
            /*36 - N�o*/
            echo("        <td>".RetornaFraseDaLista($lista_frases_geral,36)."</td>\n");
        }
        echo("    </tr>\n");
      }
    }
    // Fim Tabela Interna
    echo("      </table>\n");

    echo("      <ul class=\"btAuxTabs03\">\n");
    /* 2 - Entrar na sala de bate-papo */
    echo("        <li><span title=\"Entrar na sala de bate-papo\" onClick=\"return(OpenWindow());\">".RetornaFraseDaLista($lista_frases, 2)."</span></li>\n");
    echo("      </ul>\n");
  }
  else
  {
    echo("        <tr>\n");
    /* 59 - (N�o existe nenhuma sess�o marcada) */
    echo("          <td colspan=6>".RetornaFraseDaLista($lista_frases,59)."</td>\n");
    echo("        </tr>\n");
    // Fim Tabela Interna
    echo("      </table>\n");

    echo("      <ul class=\"btAuxTabs03\">\n");
    /* 2 - Entrar na sala de bate-papo */
    echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases, 2)."\" onClick=\"return(OpenWindow());\">".RetornaFraseDaLista($lista_frases, 2)."</span></li>\n");
    echo("      </ul>\n");

  }

  echo("      </form>\n");

  if($e_formador)
  {
    echo("<ul>\n");
    /* 64 - Desmarcar sess�es selecionadas */
    echo("  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".RetornaFraseDaLista($lista_frases,64)."</span></li>\n");
    echo("</ul>\n");
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
