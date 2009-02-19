<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/ver_item_avaliacao.php

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
  ARQUIVO : cursos/aplic/portfolio/ver_item_avaliacao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

  $sock=Conectar("");
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta = 15;
  include("../topo_tela.php");
  
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  /* Verifica��o se o item est� em Edi��o */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edi��o... */
  $linha=RetornaUltimaPosicaoHistorico ($sock, $cod_item);
  if ($linha['acao']=="E")
  {
    /* Est� em edi��o... */
    echo("<script language=javascript>\n");
    echo("  window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("  document.location='ver_itens_aluno.php?cod_curso=".$cod_curso."&cod_item=".$linha_item['cod_item']."&origem=ver&cod_topico=".$cod_topico_raiz."';\n");
    echo("</script>\n");
    exit();
  }

  $eformador=EFormador($sock,$cod_curso,$cod_usuario);

  $dir_item_temp=CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio    = 0; /* Não pode editar já que está associadoo à uma avaliacao */
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

  echo("<script language=JavaScript>\n");

  echo("function WindowOpenVer(id)\n");
  echo("{\n");
  echo("  window.open('".$dir_item_temp['link']."'+id+'?".time()."','Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("}\n");

  echo("function WindowOpenVerURL(end)\n");
  echo("{\n");
  echo("  window.open(end,'PortfolioURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("}\n");

  echo("function AbreJanelaComponentes(cod_grupo)\n");
  echo("{\n");
  echo("  window.open('../grupos/exibir_grupo.php?cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=500,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("  return false;\n");
  echo("}\n");

  echo("function AbrePerfil(cod_usuario)\n");
  echo("{\n");
  echo("  window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("  return(false);\n");
  echo("}\n");

  echo("function OpenWindowComentario(cod_comentario)\n");
  echo("{\n");
  echo("  window.open('comentario.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_comentario='+cod_comentario+'&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."', 'Comentar', 'width=600,height=350,top=100,left=100,status=no,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\n");
  echo("  return false;\n");
  echo("}\n");

  /* Fun��o JScript para chamar p�gina para salvar em arquivo. */
  echo("      function SalvarItemAvaliacao()\n");
  echo("      {\n");
  echo("        document.frmItens.action = \"salvar_ver_item_avaliacao.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("        document.frmItens.submit();\n");
  echo("      }\n\n");

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  /* 51- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n");

  echo("<body>\n");

  // 8 - Portfolio
  $cabecalho = "<b class=titulo>".RetornaFraseDaLista($lista_frases, 8)."</b>";

  /* 73 - Ver Item do Portf�lio */
  $cabecalho.= "<b class=subtitulo> - ".TituloComDistincao ($acao_portfolio_s, $ferramenta_grupos_s, RetornaFraseDaLista($lista_frases, 73) )."\n";

  // $cod_pagina=($acao_portfolio_s=="G" ? 11 : 5)
  if ($acao_portfolio_s=="G")
  {
    $cod_pagina = 30;
  }
  else
  {
    $cod_pagina = 28;
  }
  
  
  //$cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");

  unset($array_params);
  $array_params['cod_topico_raiz']       = $cod_topico_raiz;
  $array_params['cod_item']              = $cod_item;
  $array_params['cod_usuario_portfolio'] = $cod_usuario_portfolio;
  $array_params['cod_grupo_portfolio']   = $cod_grupo_portfolio;

  $EhAvaliacao=RetornaAssociacaoItemAvaliacao($sock,$cod_item);

  if (count($EhAvaliacao)>0)
  {
    $dados=RetornaDadosAvaliacao($sock,$EhAvaliacao['cod_avaliacao']);
    $atividade=RetornaTituloAtividade($sock,$dados['cod_atividade']);
    /* 149 - Item associado a atividade: */
    /*" onClick=\"window.open('../avaliacoes/ver_popup.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&cod_avaliacao=".$cod_avaliacao.".&VeioDePortfolio=1&VeioDaAtividade=1','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');EscondeLayers();return(false);\">"*/
    echo("<br /><br />");
    echo("<h4>".RetornaFraseDaLista($lista_frases,149)." ".$atividade."</h4>\n");
  }
  
  
  
  if ($portfolio_grupo)
  {
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);

    //Figura de Grupo
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";

    /* 84 - Grupo Excluído */
    if ($grupo_apagado && $eformador) $complemento=" <span>(".RetornaFraseDaLista($lista_frases,84).")</span>\n";
    echo("          ".$fig_portfolio." <span class=\"link\" onclick=\"AbreJanelaComponentes(".$cod_grupo_portfolio.");\">".$nome."</span>".$complemento);
  }
  else
  {
    $nome=NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

    // Figura de Perfil
    $fig_portfolio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";

    echo("          ".$fig_portfolio." <span class=\"link\" onclick=\"OpenWindowPerfil(".$cod_usuario_portfolio.");\" > ".$nome."</span>".$complemento);
    //echo("<a href=\"#\" onmousedown=\"js_cod_item='".$cod_item."'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"../imgs/estrutura.gif\" border=\"0\" /></a>");

  }
  

  /* Inicio do Implante */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");


  /* Botões Auxiliares */
  
  /* Extra pra quem quiser sofrer com os Comentários */
  echo("<tr><td><ul class=\"btAuxTabs\">");

  /* 72 - Historico*/
  //echo("                  <li><span onclick=\"window.open('historico.php?cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."','Historico','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false)\">".RetornaFraseDaLista ($lista_frases, 72)."</span></li>\n");

    /* 112 - Comentários */
  //echo("                  <li><a href=\"comentarios.php?cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_usuario=".$cod_usuario."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".RetornaFraseDaLista ($lista_frases, 112)."</a></li>\n");
  



  echo("    <form name=frmItens method=post>\n");

  if (!isset($SalvarEmArquivo))
  {
      /* 50 - Salvar em Arquivo (geral) */
      echo("  <li><span onClick='SalvarItemAvaliacao();'>".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");
      echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
      echo("      <input type=hidden name=cod_usuario_portfolio value=".$cod_usuario_portfolio.">\n");
      echo("      <input type=hidden name=cod_item value=".$cod_item.">\n");
      echo("      <input type=hidden name=cod_topico_raiz value=".$cod_topico_raiz.">\n");
      echo("      <input type=hidden name=cod_grupo_portfolio value=".$cod_grupo_portfolio.">\n");
  }

    /* 14 - Imprimir (geral) */
    echo("  <li><span onClick=ImprimirRelatorio();>".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");

  // 13 - Fechar (gen)
  echo("      <li><span onClick=self.close();>".RetornaFraseDaLista($lista_frases_geral, 13)."</span></li>");
  echo("</form>");


  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  
  
  
  /* Fim Botões aux */




  echo("            <tr>\n");
  echo("              <td>\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 41 - Título */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,41)."</td>\n");

  if ($dono_portfolio){
    // 70 (ger) - Opções
    echo("                    <td width=\"16%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
  }

  /* 119 - Compartilhar */
  echo("                    <td width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases,119)."</td>\n");

  // se a ferramenta Avaliacoes estiver ativada, a tabela com os itens e pastas do portfolio tem 6 colunas, senao sao 5
  if ($ferramenta_avaliacao)
  {
    /* 139 - Avaliação */
    echo("                    <td width=\"8%\" align=\"center\">".RetornaFraseDaLista($lista_frases,139)."</td>\n");
  }

  echo("                  </tr>\n");


  $linha_item=RetornaDadosDoItem($sock, $cod_item);

  $titulo=$linha_item['titulo'];

  $texto="<span id=\"text_".$linha_item['cod_item']."\">".AjustaParagrafo($linha_item['texto'])."</span>";


  /* 209 - Renomear */
  $renomear=RetornaFraseDaLista ($lista_frases, 209);

  /* 184 - Editar texto */
  $editar=RetornaFraseDaLista ($lista_frases, 184);
  /* 187 - Limpar texto */
  $limpar=RetornaFraseDaLista ($lista_frases, 187);

  /* (ger) 25 - Mover */
  $mover=RetornaFraseDaLista ($lista_frases_geral, 25);

    /* 12 - Totalmente Compartilhado */
  if ($linha_item['tipo_compartilhamento']=="T"){
    $compartilhamento=RetornaFraseDaLista($lista_frases,12);
  }
  /* 13 - Compartilhado com Formadores */
  else if ($linha_item['tipo_compartilhamento']=="F"){
    $compartilhamento=RetornaFraseDaLista($lista_frases,13);
  }
  /* 14 - Compartilhado com o Grupo */
  else if (($portfolio_grupo)&&($linha_item['tipo_compartilhamento']=="P")){
    $compartilhamento=RetornaFraseDaLista($lista_frases,14);
  }
  /* 15 - Não compartilhado */
  else if (!$portfolio_grupo){
    $compartilhamento=RetornaFraseDaLista($lista_frases,15);
  }

  // Marca se a linha contém um item 'novo'
  if ($data_acesso<$linha_item['data']) $marcatr=" class=\"novoitem\"";
  else $marcatr="";

  // se a ferramenta Avaliacoes estiver ativa, descobrimos quais avaliacoes estao presas a cada item
  if ($ferramenta_avaliacao) $lista = RetornaAssociacaoItemAvaliacao($sock,$linha_item['cod_item']);
  // senao, passamos uma variavel fake para enganar o codigo abaixo
  else $lista = NULL;

  if ($linha_item['status']=="E"){

    $linha_historico=RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);

    if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario'])
    {
      CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
      if ($dono_portfolio)
      {
        //se existe uma avalia?o ligada ao item
        if (is_array($lista))
        {
           $foiavaliado=ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha_item['cod_item']);
          //talvez arrumar a funcao ItemFoiAvaliado, pois da forma que ta se o item tiver sido avaliado, mas tiver compartilhado so com
          //formadores, o aluno nao sabe que foi avaliado, mas nao consegue editar o item, o que fazer?

          // se foi avaliado não pode editar o material
          if (!$foiavaliado){ //arrumar - não pode mais editar
            $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
            $compartilhamentospan="<span id=\"comp_".$linha_item['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_item']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
            $renomear="<span onclick=\"AlteraTitulo(".$linha_item['cod_item'].");\" id=\"renomear_".$linha_item['cod_item']."\">".$renomear."</span>";
            $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">".$editar."</span>";
            $limpar="<span onclick=\"LimparTexto(".$linha_item['cod_item'].");\">".$limpar."</span>";
            $mover="<span onclick=\"js_cod_item=".$linha_item['cod_item'].";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('".$cod_curso."', '".$cod_item."', '".$cod_usuario."', '".$cod_usuario_portfolio."', '".$cod_grupo_portfolio."', '".$cod_topico_ant."');return(false);\">".$mover."</span>";
          }
        }
        //else = não existe uma avaliação
        else {
          $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
          $compartilhamentospan="<span id=\"comp_".$linha_item['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_item']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
          $renomear="<span onclick=\"AlteraTitulo(".$linha_item['cod_item'].");\" id=\"renomear_".$linha_item['cod_item']."\">".$renomear."</span>";
          $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">".$editar."</span>";
	  $limpar="<span onclick=\"LimparTexto(".$linha_item['cod_item'].");\">".$limpar."</span>";
          $mover="<span onclick=\"js_cod_item=".$linha_item['cod_item'].";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('".$cod_curso."', '".$cod_item."', '".$cod_usuario."', '".$cod_usuario_portfolio."', '".$cod_grupo_portfolio."', '".$cod_topico_ant."');return(false);\">".$mover."</span>";
        }
      }
    }
  }
  //else = item não está sendo editado
//   else if (!(($ferramenta_avaliacao && is_array($lista) && ItemEmAvaliacao($sock,$lista['cod_avaliacao'],$cod_usuario_portfolio) && $dono_portfolio)))
  else if (!(($ferramenta_avaliacao && is_array($lista) && $dono_portfolio)))
  {
    if ($linha_item['status'] != "C")
    {
      if (1)
      {
        if (is_array($lista))
        {
           $foiavaliado = ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha_item['cod_item']);
          if ($foiavaliado)
          { //arrumar - não pode mais editar
            $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
            $compartilhamentospan="<span id=\"comp_".$linha_item['cod_item']."\" >".$compartilhamento."</span>";
            $renomear="<span onclick=\"AlteraTitulo(".$linha_item['cod_item'].");\" id=\"renomear_".$linha_item['cod_item']."\">".$renomear."</span>";
            $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">".$editar."</span>";
  	    $limpar="<span onclick=\"LimparTexto(".$linha_item['cod_item'].");\">".$limpar."</span>";
            $mover="<span onclick=\"js_cod_item=".$linha_item['cod_item'].";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('".$cod_curso."', '".$cod_item."', '".$cod_usuario."', '".$cod_usuario_portfolio."', '".$cod_grupo_portfolio."', '".$cod_topico_ant."');return(false);\">".$mover."</span>";
          }
        }
        else
        {
          $titulo="<span style=\"border:1pt;\" id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";

          $compartilhamentospan="<span id=\"comp_".$linha_item['cod_item']."\"  >".$compartilhamento."</span>";
          $renomear="<span onclick=\"AlteraTitulo(".$linha_item['cod_item'].");\" id=\"renomear_".$linha_item['cod_item']."\">".$renomear."</span>";
          $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">".$editar."</span>";
          $limpar="<span onclick=\"LimparTexto(".$linha_item['cod_item'].");\">".$limpar."</span>";
          $mover="<span onclick=\"js_cod_item=".$linha_item['cod_item'].";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('".$cod_curso."', '".$cod_item."', '".$cod_usuario."', '".$cod_usuario_portfolio."', '".$cod_grupo_portfolio."', '".$cod_topico_ant."');return(false);\">".$mover."</span>";
        }
      }
    }
  }else {
    $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
    $compartilhamentospan="<span id=\"comp_".$linha_item['cod_item']."\"  >".$compartilhamento."</span>";
    $renomear="<span onclick=\"AlteraTitulo(".$linha_item['cod_item'].");\" id=\"renomear_".$linha_item['cod_item']."\">".$renomear."</span>";
    $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">".$editar."</span>";
    $limpar="<span onclick=\"LimparTexto(".$linha_item['cod_item'].");\">".$limpar."</span>";
    $mover="<span onclick=\"js_cod_item=".$linha_item['cod_item'].";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('".$cod_curso."', '".$cod_item."', '".$cod_usuario."', '".$cod_usuario_portfolio."', '".$cod_grupo_portfolio."', '".$cod_topico_ant."');return(false);\">".$mover."</span>";
  }

  echo("                  <tr id='tr_".$linha_item['cod_item']."'>\n");
  echo("                    <td class=\"itens\">".$titulo."</td>\n");

  if ($dono_portfolio){
    echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
    echo("                      <ul>\n");
    if($renomear != null){
      echo("                        <li>".$renomear."</li>\n");
      echo("                        <li>".$editar."</li>\n");
      echo("                        <li>".$limpar."</li>\n");
      echo("                        <li>".$mover."</li>\n");
    // G 1 - Apagar
      echo("                        <li><span onclick=\"CancelaTodos();ApagarItem();\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
    }
    echo("                      </ul>\n");
    echo("                    </td>\n");
  }

  echo("                    <td align=\"center\">".$compartilhamentospan."</td>\n");

  $Sim = "<span id=\"estadoAvaliacao\">".RetornaFraseDaLista($lista_frases_geral, 35)."</span>";

  if ($ferramenta_avaliacao)
  {
    echo("                    <td align=\"center\">");
    if (is_array($lista))
    {
      $foiavaliado=ItemFoiAvaliado($sock,$lista['cod_avaliacao'],$linha_item['cod_item']);
      if ($foiavaliado){
        if ($eformador){
          echo($Sim."</span><span class=\"avaliado\"> (a)</span>\n");
        }
        //else = não é formador
        else{
          $compartilhado=NotaCompartilhadaAluno($sock,$linha_item['cod_item'],$lista['cod_avaliacao'],$cod_grupo_portfolio,$cod_usuario);
          if ($compartilhado){
            echo($Sim."</span><span class=\"avaliado\"> (a)</span>\n");
          }
          //else = não é compartilhado
          else{
            echo($Sim);
          }
        }
       } 
       else{
         echo($Sim);
       }
      }
    //else = não tem avaliação
    else{
      // G 36 - Não
      echo("<span id=\"estadoAvaliacao\">".RetornaFraseDaLista($lista_frases_geral, 36)."</span>\n");
    }
    echo("                    </td>");
  }
  echo("                  </tr>");

  // "<P>&nbsp;</P>" = texto em branco
  // "<br>" = texto em branco
  if ((($linha_item['texto']!="")&&($linha_item['texto']!="<P>&nbsp;</P>")&&($linha_item['texto']!="<br />"))||($dono_portfolio))
  {
    echo("                  <tr class=\"head\">\n");
    /* 42 - Texto  */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,42)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\">\n");
    echo("                      <div class=\"divRichText\">\n");
    echo("                        ".$texto."\n");
    echo("                      </div>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }
  

  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['link']);
  $num_arq_vis = RetornaNumArquivosVisiveis($lista_arq);
  
  if (($num_arq_vis>0)||($dono_portfolio)){
    echo("                  <tr class=\"head\">\n");
    /* 71 - Arquivos */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,71)."</td>\n");
    echo("                  </tr>\n");

    if (count($lista_arq)>0){

      $conta_arq=0;

      echo("                  <tr>\n");
      echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
      // Procuramos na lista de arquivos se existe algum visivel
      $ha_visiveis = false;

      while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis)
      {
        if ($linha[Arquivo] != "")
          $ha_visiveis = !($linha['Status']);
      }

      if (($ha_visiveis) || ($dono_portfolio))
      {
        $nivel_anterior=0;
        $nivel=-1;

        foreach($lista_arq as $cod => $linha)
        {
          if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
            if ((!$linha['Status'])||(($linha['Status'])&&($dono_portfolio)))
            {
              $nivel_anterior=$nivel;
              $espacos="";
              $espacos2="";
              $temp=explode("/",$linha['Diretorio']);
              $nivel=count($temp)-1;
              for ($c=0;$c <= $nivel;$c++){
                $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                $espacos2.="  ";
              }

              $caminho_arquivo = $dir_item_temp['link'].ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);

              if ($linha[Arquivo] != "")
              {

                if ($linha['Diretorio']!=""){
                  $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                  $espacos2.="  ";
                }


                if ($linha['Status']) $arqOculto="arqOculto='sim'";
                else $arqOculto="arqOculto='nao'";

                if (eregi(".zip$",$linha['Arquivo']))
                {
                  // arquivo zip
                  $imagem    = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
                  $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
                }
                else
                {
                  // arquivo comum
                  $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                  $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqOculto.">";
                }

                $tag_fecha = "</span>";

                echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

                if ($dono_portfolio){
                  echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\"/>\n");
                }

                echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha[Tamanho]/1024),2)."Kb)");

                echo("<span id=\"local_oculto_".$conta_arq."\">");
                if ($linha['Status']) 
                  // 118 - Oculto
                    echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style=\"color:red;\">".RetornaFraseDaLista($lista_frases,118)."</span></span>");
                echo("</span>\n");
                echo("                          ".$espacos2."<br />\n");
                echo("                        ".$espacos2."</span>\n");
              }
              else{
                if ($nivel_anterior>=$nivel){
                  $i=$nivel_anterior-$nivel;
                  $j=$i;
                  $espacos3="";
                  do{
                    $espacos3.="  ";
                    $j--;
                  }while($j>=0);
                  do{
                    echo("                      ".$espacos3."</span>\n");
                    $i--;
                  }while($i>=0);
                }
                // pasta
                $imagem    = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
                echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n"); 
                if ($dono_portfolio){
                  echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
                }
                echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
                echo("                        ".$espacos2."<br />\n");
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
          $nivel--;
        }while($nivel>=0);
      }
      echo("                      <script type=\"text/javascript\">js_conta_arq=".$conta_arq.";</script>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }

    if ($dono_portfolio){
      echo("                  <tr>\n");
      echo("                    <td align=\"left\" colspan=\"4\">\n");
      echo("                      <ul>\n");
      echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">Apagar</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_mover\"><span id=\"sArq_mover\">Mover</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">Descompactar</span></li>\n");
      echo("                        <li class=\"menuUp\" id=\"mArq_ocultar\"><span id=\"sArq_ocultar\">Ocultar</span></li>\n");
      echo("                      </ul>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
      echo("                  <tr>\n");
      echo("                    <td align=\"left\" colspan=\"4\">\n");
      echo("                      <form name=\"formFiles\" id=\"formFiles\" action=\"acoes.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
      echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
      echo("                        <input type=\"hidden\" name=\"cod_item\" value=\"".$cod_item."\" />\n");
      echo("                        <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
      echo("                        <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"".$cod_usuario_portfolio."\" />\n");
      echo("                        <input type=\"hidden\" name=\"acao\" value=\"anexar\" />\n");
      echo("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
      echo("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" />\n");
      echo("                          <span class=\"destaque\">".RetornaFraseDaLista ($lista_frases_geral, 26)."</span>\n");
      echo("                          <span> - ".RetornaFraseDaLista ($lista_frases, 59).RetornaFraseDaLista ($lista_frases, 60)."</span>\n");
      echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
      echo("                          <input type=\"file\" id=\"input_files\" name=\"input_files\" class=\"input\" />\n");
      echo("                          &nbsp;&nbsp;\n");
      echo("                          <span onclick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 18)."</span>\n");
      echo("                          &nbsp;&nbsp;\n");
      echo("                          <span onclick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 2)."</span>\n");
      echo("                        </div>\n");
                                    /* 26 - Anexar arquivos (ger) */
      echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">".RetornaFraseDaLista($lista_frases_geral,26)."</span></div>\n");
      echo("                      </form>\n");

    }
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  $lista_url=RetornaEnderecosMaterial($sock, $cod_item);

   if ((is_array($lista_url))||($dono_portfolio)){

    echo("                  <tr class=\"head\">\n");
      /* 44 - Endereços */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,44)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");

    if (count($lista_url)>0)
    {
      foreach ($lista_url as $cod => $linha)
      {
  
        $linha['endereco'] = RetornaURLValida($linha['endereco']);

        echo("                      <span id='end_".$linha['cod_endereco']."'>\n");

        if ($linha['nome']!="")
        {
          echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['nome']."</span>&nbsp;&nbsp;(".$linha['endereco'].")");
        }
        else
        {
          echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['endereco']."</span>");
        }

        if($dono_portfolio){
          /* (gen) 1 - Apagar */
          echo(" - <span class=\"link\" onclick=\"ApagarEndereco('".$cod_curso."', '".$linha['cod_endereco']."');\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span>\n");
        }
        echo("                        <br />\n");
        echo("                      </span>\n");

      }
    }

    echo("                    </td>\n");
    echo("                  </tr>\n");

    if ($dono_portfolio){
      echo("                  <tr>\n");
      echo("                    <td colspan=\"4\" align=\"left\" id=\"tdIncluirEnd\">\n");
      /* 45 - Incluir Endereço */
      echo("                      <div id=\"divEndereco\"><img alt=\"\" src=\"../imgs/url.jpg\" border=\"0\" /> <span id=\"incluiEnd\" class=\"link\" onclick=\"AdicionaInputEndereco();\">".RetornaFraseDaLista($lista_frases,45)."</span></div>\n");
      echo("                      <div id=\"divEnderecoEdit\" class=\"divHidden\">\n");
      echo("                        <img alt=\"\" src=\"../imgs/url.jpg\" border=\"0\" />\n"); 
      echo("                        <span id=\"incluiEndEdit\" class=\"destaque\">".RetornaFraseDaLista($lista_frases,45)."</span>\n");
      echo("                        <span> - ".RetornaFraseDaLista($lista_frases,65)."</span>\n");
      echo("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
      echo("                        <span class=\"destaque\">".RetornaFraseDaLista($lista_frases,41)."</span><br />\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <input type=\"text\" class=\"input\" name=\"novoNomeEnd\" id=\"novoNomeEnd\" size=\"30\" />\n");
      echo("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <span class=\"destaque\">".RetornaFraseDaLista($lista_frases,66)."</span><br />\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <input type=\"text\" class=\"input\" name=\"novoEnd\" id=\"novoEnd\" size=\"30\" />\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <span class=\"link\" onclick=\"EditaEndereco(1);\">".RetornaFraseDaLista($lista_frases_geral,18)."</span>\n");
      echo("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
      echo("                        <span class=\"link\" id=\"cancelaEnd\" onclick=\"EditaEndereco(0);\">".RetornaFraseDaLista($lista_frases_geral,2)."</span><br />\n");
      echo("                      </div>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }
  }

  //Associar a uma avaliação
  if ($dono_portfolio){

    if ($portfolio_grupo){
      //'G'rupo
      $tipo_portfolio='G';
    }else{
      //'I'ndividual
      $tipo_portfolio='I';
    }


    $lista=RetornaAvaliacaoPortfolio($sock,$tipo_portfolio, $cod_curso, $cod_usuario);

    if (is_array($lista)){

      echo("                  <tr class=\"head\">\n");
        /* 139 - Avaliação */
      echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,139)."</td>\n");
      echo("                  </tr>\n");
      echo("                  <tr>\n");
      echo("                    <td colspan=\"4\" class=\"itens\" colspan=\"4\">\n");
      /* 152 - Associar item à Avaliação */
      echo("                      <div id=\"divAvaliacao\"><img alt=\"\" src=\"../imgs/portfolio/lapis.gif\" border=0 /><span id=\"assocAval\" class=\"link\" onclick=\"AssociarAvaliacao();\">".RetornaFraseDaLista($lista_frases,152)."</span></div>\n");
      echo("                      <div id=\"divAvaliacaoEdit\" class=\"divHidden\">\n");
      echo("                        <img alt=\"\" src=\"../imgs/portfolio/lapis.gif\" border=0 /><span class=\"destaque\" >".RetornaFraseDaLista($lista_frases,152)."</span>\n");
      echo("                        <span> - ".RetornaFraseDaLista($lista_frases,153)."</span><br /><br /><br />\n");
      echo("                        <table id=\"tableAvaliacao\" cellspacing=\"0\" cellspading=\"0\">\n");
      echo("                          <tr class=\"head\" align=center>\n");
      echo("                            <td>&nbsp;</td>\n");
      // 168 - Atividades
      echo("                            <td width=\"30%\">".RetornaFraseDaLista($lista_frases, 168)."</td>\n");
      // 163 - Tipo de Atividade
      echo("                            <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 163)."</td>\n");
      // 167 - Valor 
      echo("                            <td width=\"5%\">".RetornaFraseDaLista($lista_frases, 167)."</td>\n");
      // 165 - Data de início
      echo("                            <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 165)."</td>\n");
      // 166 - Data de término
      echo("                            <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 166)."</td>\n");
      echo("                          </tr>\n");
  
      // esta var indica se precisamos colocar uma legenda explicando o que eh o termo "associado" na frente da avaliacao 
      $legenda_associado = false;

      foreach ($lista as $cod => $linha)
      {
        // para que a opção com esta avaliação possa ser escolhida
        if ($cod_avaliacao == $linha['cod_avaliacao'])
          $ha_nao_avaliado = false;
        else
        {
          //somente verifica se existe item nao avaliado se nao for a avaliação que ja estava associada ao item, pois não posso impedir o usuario de associar o item a avaliação ao qual o mesmo ja estava vinculado

          // A funcao ExisteItemNaoAvaliado retorna o numero de itens nao avaliados. Se este numero for != 0, $ha_nao_avaliado = true (ha itens nao avaliados)
          if ($portfolio_grupo)
            $ha_nao_avaliado = (0 != ExisteItemNaoAvaliado($sock,$linha['cod_avaliacao'],$cod_grupo_portfolio,$portfolio_grupo,$cod_topico_raiz_usuario));
          else
            $ha_nao_avaliado = (0 != ExisteItemNaoAvaliado($sock,$linha['cod_avaliacao'],$cod_usuario_portfolio,$portfolio_grupo,$cod_topico_raiz_usuario));
        }

        $atividade=RetornaTituloAtividade($sock,$linha['cod_atividade']);

        if ($ha_nao_avaliado)
        {
          // ha um item associado a esta avaliacao e nao avaliado. Entao nao pode associar este item a esta avaliacao
          $radio = "&nbsp;";
          // Escrevemos na frente da avaliacao que outro item já foi associado a ela
          // 170 - associado
          $assoc = "<font size=-2>"."&nbsp;&nbsp;"."("."<font color=red>".RetornaFraseDaLista($lista_frases, 170)."</font>".")"."</font>";
          // E precisamos colocar a legenda o que esta frase 'associado' significa
          $legenda_associado = true;
        }else{

          if ($cod_avaliacao==$linha['cod_avaliacao']){
            $ch="checked";
          }else{
            $ch="";
          }

          $radio = "<input class=\"g1field\" type=\"radio\" ".$ch." name=\"cod_avaliacao\" value=\"".$linha['cod_avaliacao']."\" onclick=\"cod_avaliacao=".$linha['cod_avaliacao'].";\">";
          // Avaliação livre, não escrevemos nada na frente
          $assoc = "";
        }

        echo("                          <tr>\n");
        echo("                            <td width=\"1%\">".$radio."</td>\n");
        echo("                            <td align=\"left\"><span class=\"link\" onclick=\"WindowOpenVerAvaliacao(".$linha['cod_avaliacao'].");EscondeLayers();return(false);\">".$atividade."</span>".$assoc."</td>\n");
    
        if (!strcmp($tipo_portfolio,'I')){
          // 161 - Individual
          echo("                            <td align=\"center\">".RetornaFraseDaLista($lista_frases, 161)."<br /></td>\n");
        }else{
          // 162 - Em Grupo
          echo("                            <td align=\"center\">".RetornaFraseDaLista($lista_frases, 162)."<br /></td>\n");
        }
        echo("                            <td align=\"center\">".$linha['valor']."<br /></td>\n");
        echo("                            <td align=\"center\">".Unixtime2Data($linha['data_inicio'])."<br /></td>\n");
        echo("                            <td align=\"center\">".Unixtime2Data($linha['data_termino'])."<br /></td>\n");
        echo("                          </tr>\n");
      }
      echo("                        </table>\n");

      // 173 - OBS: Se outro item tiver sido associado a uma avaliacao e nao tiver sido avaliado, nao sera possivel associar este item à mesma.
      $frase_atividades = RetornaFraseDaLista($lista_frases, 173);
      echo("                        ".$frase_atividades);


	  echo("					  <br /><br />");
	  echo("					  <input type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 18)."\" class=\"input\" id=\"OKAval\" onclick=\"EditaAval(1);\"/> ");
	  echo("					  <input type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 2)."\" class=\"input\" id=\"cancAval\" onclick=\"EditaAval(0);\"/> ");
	  echo("					  <input type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases, 160)."\" class=\"input\" onclick=\"xajax_DesassociaAvaliacaoDinamic(".$cod_curso.", ".$cod_usuario.", ".$cod_item.", '".RetornaFraseDaLista($lista_frases, 213)."');\";/> ");
      echo("                      </div>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }
  }

  echo("                </table>\n"); //TabInterna
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n"); //TabExterna

  
  /* Fim do Implante */

  echo("    </form>\n");


  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
