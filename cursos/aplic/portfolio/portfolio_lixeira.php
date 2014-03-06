<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/portfolio_lixeira.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Ncleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/portfolio/portfolio_lixeira.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"ExcluirItensDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RecuperarItensDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 15;
  $cod_ferramenta_ajuda = 15;
  $cod_pagina_ajuda = 5;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("excluirItens", 203, 0);
  $feedbackObject->addAction("recuperarItens", 204, 0);


  $eformador   = EFormador($sock,$cod_curso,$cod_usuario);
  $colaborador = EColaborador($sock, $cod_curso, $cod_usuario);


  // verificamos se a ferramenta de Avaliacoes estah disponivel
//   $ferramenta_avaliacao = TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, 22);

  $ferramenta_avaliacao = false;


  /* Apaga links simbolicos que por acaso tenham sobrado daquele usuario */
  system ("rm ../../diretorio/portfolio_".$cod_curso."_*_".$cod_usuario);

  $var = $diretorio_temp."/portfolio_".$cod_curso."_*_".$cod_usuario;

  foreach (glob($var) as $filename)
  {
    if(ExisteArquivo($filename))
      (RemoveArquivo($filename));
  }

  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");

  $cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");

  if (!isset($cod_topico_raiz))
  {
    if ($cod_grupo_portfolio!="" && $cod_grupo_portfolio!="NULL")
      $cod_topico_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo_portfolio);
    else if ($cod_usuario_portfolio!="")
      $cod_topico_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario_portfolio,"");
    else
    {
      $cod_topico_raiz=$cod_topico_raiz_usuario;
      $cod_usuario_portfolio=$cod_usuario;

      /* Checagem da existência das pastas dos grupos a que o usuário pertence */
      VerificaPortfolioGrupos($sock,$cod_usuario);
    }
  }

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio    = $status_portfolio ['dono_portfolio'];
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

    //JS utilizado para mover as colunas da tabela

  echo("    <script type='text/javascript'>\n");

  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n");

  

  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    var array_itens;\n\n");

  echo("    function VerificaCheck(){\n");
  echo("      var i;\n");
  echo("      var j=0;\n");
  echo("      var cod_itens=document.getElementsByName('chkItem');\n");
  echo("      var Cabecalho = document.getElementById('checkMenu');\n");
  echo("      array_itens = new Array();\n");
  echo("      for (i=0; i < cod_itens.length; i++){\n");
  echo("        if (cod_itens[i].checked){\n");
  echo("          var item = cod_itens[i].id.split('_');\n");
  echo("          array_itens[j]=item[1];\n");
  echo("          j++;\n");
  echo("        }\n");
  echo("      }\n");
  echo("      if (j==cod_itens.length) Cabecalho.checked=true;\n");
  echo("      else Cabecalho.checked=false;\n");
  echo("      if(j>0){\n");
  echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
  echo("        document.getElementById('mRecuperar_Selec').className=\"menuUp02\";\n");
  echo("        document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
  echo("        document.getElementById('mRecuperar_Selec').onclick=function(){ RecuperarSelecionados(); };\n");
  echo("      }else{\n");
  echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
  echo("        document.getElementById('mRecuperar_Selec').className=\"menuUp\";\n");
  echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
  echo("        document.getElementById('mRecuperar_Selec').onclick=function(){  };\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function CheckTodos(){\n");
  echo("      var e;\n");
  echo("      var i;\n");
  echo("      var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("      var cod_itens=document.getElementsByName('chkItem');\n");
  echo("      for(i = 0; i < cod_itens.length; i++)\n");
  echo("      {\n");
  echo("        e = cod_itens[i];\n");
  echo("        e.checked = CabMarcado;\n");
  echo("      }\n");
  echo("      VerificaCheck();\n");
  echo("    }\n\n");

  echo("    function ExcluirSelecionados(){\n");
      /* 120 - Tem certeza de que deseja excluir este item ? */
      /* 100 - (o item será excluído definitivamente) */
  echo("      if (confirm('".RetornaFraseDaLista($lista_frases,120)."\\n".RetornaFraseDaLista($lista_frases,100)."')){\n");
  echo("        xajax_ExcluirItensDinamic('".$cod_curso."', '".$cod_usuario."', array_itens);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function RecuperarSelecionados(){\n");
  /* 101 - Você tem certeza de que deseja recuperar este item? */
  /* 102 - (o item será movida para a pasta Raiz e estará como não compartilhado) */
  echo("      if (confirm('".RetornaFraseDaLista($lista_frases,101)."\\n".RetornaFraseDaLista($lista_frases,102)."')){\n");
  echo("        xajax_RecuperarItensDinamic('".$cod_curso."', '".$cod_usuario."','".$cod_grupo_portfolio."', array_itens);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function Recarregar(acao, atualizacao){\n");
  echo("      window.location='portfolio_lixeira.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_usuario_portfolio=".$cod_usuario_portfolio."&acao='+acao+'&atualizacao='+atualizacao;\n");
  echo("    }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* P?ina Principal */

  if ($ferramenta_avaliacao)
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase  =  3;
      $cod_pagina = 25;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase  =  2;
      $cod_pagina = 21;
    }
  }
  else
  {
    if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
    {
      // 3 - Portfolios de grupos
      $cod_frase = 3;
      $cod_pagina=14;
    }
    else
    {
      // 2 - Portfolios individual
      $cod_frase = 2;
      $cod_pagina=8;
    }
  }


  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, $cod_frase)."</h4>\n");


  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  unset($path);

  /* 7 - Lixeira */
  $path="        <b>".RetornaFraseDaLista($lista_frases,7)."</b>";

  if ($portfolio_grupo)
  {
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);

    //Figura ao lado do texto
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";

    echo("          <a class=\"text\" href=\"#\" onclick=\"return(AbreJanelaComponentes(".$cod_grupo_portfolio."))\";>".$fig_portfolio." ".$nome."</a>".$complemento." - ");
    echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"../imgs/estrutura.gif\" border=\"0\"/></a>");


  }
  else
  {
    $nome=NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

    // Selecionando qual a figura a ser exibida ao lado do nome
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";

    /* 85 - Aluno Rejeitado */
    if (RetornaStatusUsuario($sock,$cod_curso,$cod_usuario_portfolio)=="r" && $eformador) $complemento=" <font class=textsmall>(".RetornaFraseDaLista($lista_frases,85).")</font>\n";

        echo("          <a href=\"#\" onclick=\"return(OpenWindowPerfil(".$cod_usuario_portfolio."));\" >".$fig_portfolio." ".$nome."</a>".$complemento." - ");
        echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"../imgs/estrutura.gif\" border=\"0\"/></a>");
  }

  echo ($path);

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("                <td valign=\"top\">\n");
  echo("                  <ul class=\"btAuxTabs\">\n");

   //174 - Meus portfolios 
  echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=myp\">".RetornaFraseDaLista($lista_frases,174)."</a></li>\n");    
  // 74 - Portfolios Individuais
  echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=ind\">".RetornaFraseDaLista($lista_frases,74)."</a></li>\n"); 
  // 75 - Portfolios de Grupos
  if ($ferramenta_grupos_s) {
    echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=grp\">".RetornaFraseDaLista($lista_frases,75)."</a></li>\n"); 
    // 177 - Portfolios encerrados
    echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&amp;exibir=enc\">".RetornaFraseDaLista($lista_frases,177)."</a></li>\n"); 
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");

  // 69 - Atualizar
echo("		<li> <span onclick=\"window.location.reload();\">".RetornaFraseDaLista($lista_frases,69)."</span></li>\n");


  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\"/></td>\n");

  /* 82 - Itens */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,82)."</td>\n");
  /* 9 - Data */
  echo("                    <td width=\"60\" align=\"center\">".RetornaFraseDaLista($lista_frases,9)."</td>\n");

  echo("                  </tr>\n");

  $lista_itens=RetornaItensDaLixeira($sock, $cod_usuario,$cod_usuario_portfolio,$cod_grupo_portfolio);

  if ((!is_array($lista_itens))||(count($lista_itens)<1))
  {
    echo("                  <tr>\n");
    /* 11 - Não há nenhum item neste portfólio */
    echo("                    <td>&nbsp;</td>\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,11)."</td>\n");
    echo("                    <td>&nbsp;</td>\n");
    echo("                  </tr>\n");
  }
  //else = existe item(ns) na lixeira
  else
  {
    // definindo qual figura para representar pastas ou arquivos (itens)
    $arquivo = "arquivo_";

    // aqui, escolho entre a figura para grupo ou individual
    if ($portfolio_grupo) $gi="g_";
    else $gi="i_";

    $arquivo.= $gi;

    // aqui, escolho entre pessoal, nao-pessoal ou apagado
    if ($dono_portfolio) $pnx="p.gif";
    else if ($portfolio_apagado) $pnx="x.gif";
    else $pnx="n.gif";

    $arquivo.= $pnx;

    foreach ($lista_itens as $cod => $linha_item)
    {
      $data=UnixTime2Data($linha_item['data']);
      $titulo="<span class=\"link\" onclick=\"window.location='ver_lixeira.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha_item['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\"> ".$linha_item['titulo']."</span>";

      echo("                  <tr id=\"tr_".$linha_item['cod_item']."\">\n");
      echo("                    <td><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_item']."\" onclick='VerificaCheck();' value=\"".$linha_item['cod_item']."\"/></td>\n");

      $icone = "<img src=\"../imgs/".$arquivo."\" border=\"0\"/>";

      echo("                    <td class=\"itens\">".$icone.$titulo."</td>\n");
      echo("                    <td><span id=\"data_".$linha_item['cod_item']."\">".$data."</span></td>\n");
      echo("                  </tr>\n");
    } 
  } 
 
  echo("                </table>\n");

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          <ul>\n");
  echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">Excluir selecionados</span></li>\n");
  echo("            <li id=\"mRecuperar_Selec\" class=\"menuUp\"><span id=\"moverSelec\">Recuperar selecionados</span></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
 include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>