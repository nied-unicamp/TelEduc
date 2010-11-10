<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver_notas.php

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

    Nied - Núcleo de Informática Aplicada à Educação
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
    ARQUIVO : cursos/aplic/avaliacoes/ver_notas.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);

  if ($acao=="mudarcomp" && $usr_formador)
  {
     if ($portfolio_grupo)
     {
       $cod=RetornaCodAluno($sock,$cod_nota);
       $cod_grupo=RetornaCodGrupoPortfolio($sock,$cod);
       $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo);
       foreach ($lista_integrantes as $cod_aluno => $linha)
       {
         $cod_nota=RetornaCodNota($sock, $cod_aluno, $cod_avaliacao);
         MudarCompartilhamento($sock, $cod_nota, $tipo_comp);
       }
     }
     else
       MudarCompartilhamento($sock, $cod_nota, $tipo_comp);
  }

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  echo("<html>\n");
  /* 1 - Avaliações  */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");

  if (!$SalvarEmArquivo)
  {
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("    <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
    echo("\n");
  }
  else
  {
    echo("  <style>\n");
    include "../teleduc.css";
    include "avaliacoes.css";
    echo("  </style>\n");
  }

  if (!strcmp($dados['Ferramenta'],'P'))
  {
    if (!strcmp($dados['Tipo'],'G'))
      $portfolio_grupo=1;
    else
      $portfolio_grupo=0;
  }

  /* Funções JavaScript */
  echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
  echo("<script language=JavaScript>\n");
  echo("  if ((navigator.appName.indexOf(\"Netscape\") !=-1) && navigator.appVersion.charAt(0) <= '4') {\n");
  echo("      var isNav = true;\n");
  echo("      var isIE  = false;\n");
  echo("  } else if (navigator.appName.indexOf(\"Microsoft Internet Explorer\") != -1) {\n");
  echo("      var isNav = false;\n");
  echo("      var isIE  = true;\n");
  echo("  } else {\n");
  echo("      var isNav = false;\n");
  echo("      var isIE  = false;\n");
  echo("  }\n");
  echo("  var notNav = ! isNav;\n");
  echo("var Xpos, Ypos;\n");
  echo("var js_cod_nota;\n");
  echo("var js_comp = new Array();\n");

  if ($usr_formador)
  {
    echo("if (isNav)\n");
    echo("{\n");

    echo("  document.captureEvents(Event.MOUSEMOVE);\n");
    echo("}\n");
    echo("document.onmousemove = TrataMouse;\n");

    echo("function TrataMouse(e)\n");
    echo("{\n");
    echo("  Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("  Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("}\n");

    echo("function getPageScrollY()\n");
    echo("{\n");
    echo("  if (isNav)\n");
    echo("    return(window.pageYOffset);\n");
    echo("  if (isIE)\n");
    echo("    return(document.body.scrollTop);\n");
    echo("}\n");

    echo("function AjustePosMenuIE()\n");
    echo("{\n");
    echo("  if (isIE)\n");
    echo("    return(getPageScrollY());\n");
    echo("  else\n");
    echo("    return(0);\n");
    echo("}\n");

    echo("function Iniciar()\n");
    echo("{\n");
    echo("  cod_comp = getLayer(\"comp\");\n");
    echo("}\n");
    echo("\n");

    echo("function EscondeLayers()\n");
    echo("{\n");
    echo("  hideLayer(cod_comp);\n");
    echo("}\n");


    echo("function AtualizaComp(js_tipo_comp)\n");
    echo("{\n");
    echo("  if (isNav) {\n");
    echo("    document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("    document.comp.document.form_comp.cod_nota.value=js_cod_nota;\n");
    echo("    if (js_tipo_comp=='T') {\n");
    echo("      document.comp.document.form_comp.tipo_comp[0].checked=true;\n");
    echo("      document.comp.document.form_comp.tipo_comp[1].checked=false;\n");
    echo("      document.comp.document.form_comp.tipo_comp[2].checked=false;\n");
    echo("    } else if (js_tipo_comp=='F') {\n");
    echo("      document.comp.document.form_comp.tipo_comp[0].checked=false;\n");
    echo("      document.comp.document.form_comp.tipo_comp[1].checked=true;\n");
    echo("      document.comp.document.form_comp.tipo_comp[2].checked=false;\n");
    echo("    } else {\n");
    echo("      document.comp.document.form_comp.tipo_comp[0].checked=false;\n");
    echo("      document.comp.document.form_comp.tipo_comp[1].checked=false;\n");
    echo("      document.comp.document.form_comp.tipo_comp[2].checked=true;\n");
    echo("    }\n");
    echo("  } else {\n");
    echo("    if (notNav) {\n");
    echo("      document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("      document.form_comp.cod_nota.value=js_cod_nota;\n");
    echo("      if (js_tipo_comp=='T') {\n");
    echo("        document.form_comp.tipo_comp[0].checked=true;\n");
    echo("        document.form_comp.tipo_comp[1].checked=false;\n");
    echo("        document.form_comp.tipo_comp[2].checked=false;\n");
    echo("      } else if (js_tipo_comp=='F') {\n");
    echo("        document.form_comp.tipo_comp[0].checked=false;\n");
    echo("        document.form_comp.tipo_comp[1].checked=true;\n");
    echo("        document.form_comp.tipo_comp[2].checked=false;\n");
    echo("      } else {\n");
    echo("        document.form_comp.tipo_comp[0].checked=false;\n");
    echo("        document.form_comp.tipo_comp[1].checked=false;\n");
    echo("        document.form_comp.tipo_comp[2].checked=true;\n");
    echo("      }\n");
    echo("    }\n");
    echo("  }\n");
    echo("}\n");


    echo("function MostraLayer(cod_layer, ajuste)\n");
    echo("{\n");
    echo("  EscondeLayers();\n");
    echo("  moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("  showLayer(cod_layer);\n");
    echo("}\n");

    echo("function EscondeLayer(cod_layer)\n");
    echo("{\n");
    echo("  hideLayer(cod_layer);\n");
    echo("}\n");

    echo("  function AvaliarAluno(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_aluno=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");

    echo("  function AvaliarAlunoGrupo(funcao, grupo)\n");
    echo("  {\n");
    echo("    window.open(\"avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&portfolio_grupo=".$portfolio_grupo."&cod_grupo=\"+grupo,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");

    echo("  function AvaliarAlunoPortfolio(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&portfolio_grupo=".$portfolio_grupo."&VeioPeloPortfolio=0&cod_usuario_portfolio=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");
  }

  echo("  function HistoricodoDesempenho(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_atividade=".$dados['Cod_atividade']."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$dados['Ferramenta']."&cod_aluno=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function HistoricodoDesempenhoPortfolio(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeiodePortfolio=0&portfolio_grupo=".$portfolio_grupo."&cod_atividade=".$dados['Cod_atividade']."&ferramenta=".$dados['Ferramenta']."&cod_avaliacao=".$cod_avaliacao."&cod_aluno=\"+funcao,\"HistoricoDesempenho\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function HistoricodoDesempenhoPortfolioGrupo(grupo)\n");
  echo("  {\n");
  echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeiodePortfolio=0&portfolio_grupo=".$portfolio_grupo."&cod_atividade=".$dados['Cod_atividade']."&ferramenta=".$dados['Ferramenta']."&cod_avaliacao=".$cod_avaliacao."&cod_grupo=\"+grupo,\"HistoricoDesempenho\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  // 51 - Infelizmente não foi possível imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  // Função JvaScript para chamar página para salvar em arquivo.
  echo("      function SalvarVerNotas()\n");
  echo("      {\n");
  echo("        document.frmMsg.action = \"salvar_ver_notas.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("        document.frmMsg.submit();\n");
  echo("      }\n\n");
  // Esta funcao mostra a tela com a lista de avaliacoes Passadas, Atuais ou Futuras
  // tela = 'P', 'F' ou 'A'
  echo("    function VerTelaAvaliacoes(tela)\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.action = 'avaliacoes.php';\n");
  echo("      document.frmAvaliacao.tela_avaliacao.value = tela;\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    function VerTelaNotas()\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.action = 'notas.php';\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    function VerTelaLixeira()\n");
  echo("    {\n");
  echo("      document.frmAvaliacao.action = 'lixeira.php';\n");
  echo("      document.frmAvaliacao.submit();\n");
  echo("      return false;\n");
  echo("    }\n");
  echo("    function Ver()\n");
  echo("    {\n");
  echo("        document.frmAvaliacao.cod_avaliacao.value = ".$cod_avaliacao.";\n");
  echo("        document.frmAvaliacao.action = 'ver.php'; \n");
  echo("        document.frmAvaliacao.submit();\n");
  echo("    }\n\n");
  echo("function VerObj()\n");
  echo("{\n");
  $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
  $nome_janela = "'AvaliacoesHistorico'";
  echo("  window.open('ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."', ".$nome_janela.", ".$param.");\n");
  echo("  return false;");
  echo("}\n");

  echo("function AbrePerfil(cod_usuario)\n");
  echo("{\n");
  echo("  window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("  return(false);\n");
  echo("}\n");

   echo("function AbreJanelaComponentes(cod_grupo)\n");
   echo("{\n");
   echo("  window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
   echo("  return false;\n");
   echo("}\n");

  echo("</script>\n");

  if ($usr_formador)
  {
    /*A função Iniciar só existe para formadores*/
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
    $escondelayer="EscondeLayers();";
  }
  else
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  }

  $cabecalho ="<b class=titulo>Avaliações</b>";
  /* 36 - Notas dos participantes */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,36)."</b>";

   $cod_pagina=20;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));
  echo("<br>");


  // Determinamos a cor de cada link (amarelo ou branco) no menu superior
  $cor_link1 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link2 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link1[$tela_avaliacao] = "<font color=yellow>";
  $cor_link2[$tela_avaliacao] = "</font>";
  // Form para navegar entre as listas de avaliacao
  echo("    <form name=frmAvaliacao method=post>\n");
  echo(RetornaSessionIDInput());
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  // Passa o cod_avaliacao para executar ações sobre ela.
  echo("      <input type=hidden name=cod_avaliacao value=-1>\n");
  // tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
  echo("    </form>\n");

  echo("<p>\n");
  // menu com links para as outras avaliacoes, notas e lixeira
  echo("  <table border=0 width=100%>\n");
  echo("    <tbody>\n");
  echo("      <tr class=menu>\n");
  // 29 - Avaliações Passadas
  echo("        <td align=center><a href=# onMouseDown=return(VerTelaAvaliacoes('P')) class=menu>".$cor_link1['P']."<b>".RetornaFraseDaLista($lista_frases, 29)."</b>".$cor_link2['P']."</a></td>\n");
  // 32 - Avaliações Atuais
  echo("        <td align=center><a href=# onMouseDown=return(VerTelaAvaliacoes('A')) class=menu>".$cor_link1['A']."<b>".RetornaFraseDaLista($lista_frases, 32)."</b>".$cor_link2['A']."</a></td>\n");
  // 30 - Avaliações Futuras
  echo("        <td align=center><a href=# onMouseDown=return(VerTelaAvaliacoes('F')) class=menu>".$cor_link1['F']."<b>".RetornaFraseDaLista($lista_frases, 30)."</b>".$cor_link2['F']."</a></td>\n");
  // 31 - Notas dos Participantes
  echo("        <td align=center><a href=# onMouseDown=return(VerTelaNotas()) class=menu><b>".RetornaFraseDaLista($lista_frases, 31)."</b></a></td>\n");
  // G 16 - Lixeira
  echo("        <td align=center><a href=# onMouseDown=return(VerTelaLixeira()) class=menu><b>".RetornaFraseDaLista($lista_frases_geral, 16)."</b></a></td>\n");
  echo("      </tr>\n");
  echo("    </tbody>\n");
  echo("  </table>\n");
  echo("<table border=0 width=100%;>\n");
  echo("  <tbody>\n");
  echo("    <tr class=menu3>\n");
  // 120 - Ver Avaliação
  echo("      <td align=center><a href=# class=menu3 onClick='Ver(); return false;'>".RetornaFraseDaLista($lista_frases, 120)."</a></td>\n");
  // 46 - Ver objetivos/critérios da avaliação
  echo("      <td align=center><a href=# class=menu3 onclick='VerObj();return false;'>".RetornaFraseDaLista($lista_frases, 46)."</a></td>\n");
  echo("    </tr>\n");
  echo("  </tbody>\n");
  echo("</table>\n");
  echo("  </p>\n");

  $titulo=RetornaTituloAvaliacao($sock,$dados['Ferramenta'],$dados['Cod_atividade']);

  if ($dados['Ferramenta'] == 'P')
  {
    $titulo = RetornaAtividade($sock,$dados['Cod_atividade']);
    if ($dados['Tipo'] == 'I')
      // ?? - Atividade individual no portfolio
      $tipo = "[Atividade individual no portfolio]";
    elseif ($dados['Tipo'] == 'G')
      // ?? - Atividade em grupo no portfolio
      $tipo = "[Atividade em grupo no portfolio]";
  }
  else if ($dados['Ferramenta'] == 'F')
  {
    // 145 - Fórum de Discussão
    $tipo = RetornaFraseDaLista($lista_frases,145);
    $titulo = RetornaForum($sock,$dados['Cod_atividade']);
  }
  elseif ($dados['Ferramenta'] == 'B')
  {
    // 146 - Sessão de Bate-Papo
    $tipo = RetornaFraseDaLista($lista_frases,146);
    $titulo = RetornaAssunto($sock,$dados['Cod_atividade']);
  }

  echo("<table border=0 width=100% cellspacing=0>\n");
  echo("  <tbody>\n");
  echo("    <tr>\n");
  // imagem
  echo("      <td width=1% class=colorfield>&nbsp;</td>\n");
  // 123 - Título
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
  // // ?? - Ferramenta
  // echo("      <td class=colorfield align=left>&nbsp;&nbsp;"."[Ferramenta]"."</td>\n");
  // ?? - Tipo
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;"."[Tipo]"."</td>\n");
  // 19 - Valor
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
  echo("    </tr>\n");
  echo("    <tr>\n");
  echo("      <td width=1%><img src=../figuras/avaliacao.gif border=0></td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".$titulo."</td>\n");
  // echo("      <td class=text align=left>&nbsp;&nbsp;".$ferramenta."</td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".$tipo."</td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".FormataNota($dados['Valor'])."</td>\n");
  echo("    </tr>\n");
  echo("</table>\n");
  echo("<br>\n");


  if ((!strcmp($dados['Ferramenta'],'P')) && (!strcmp($dados['Tipo'],'G')))
  {
    $lista_grupos=RetornaListaGrupos($sock);
    $num_grupos=count($lista_grupos);
    if ($num_grupos > 0)
    {
      //Tabela com a lista de alunos do curso, com suas respectivas notas na avaliação realizada
      echo("<table border=0 width=100%>\n");
      echo("  <tr class=menu>\n");
      /* 158 - Grupo */
      echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,158)."</td>\n");

      if ($usr_formador)
      {
        /* 60 - Nota */
        echo("    <td class=colorfield align=center width=20%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
        /* 63 - Compartilhamento */
        echo("    <td class=colorfield align=center width=47%>".RetornaFraseDaLista($lista_frases,63)."</td>\n");
      }
      else
      /* 60 - Nota */
        echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");

      echo("    </tr>\n");

      foreach ($lista_grupos as $cod_grupo => $nome)
      {
        if ($i==0)
          $field="g1field";
        else
          $field="g2field";
        echo("    <tr class=".$field.">\n");

        $i = ($i + 1) % 2;

        echo("      <td class=text>");
        if (!$SalvarEmArquivo)
          echo("<a class=text href=# onClick=return(AbreJanelaComponentes(".$cod_grupo."));>".$nome."</a></td>");
        else
          echo($nome."</td>\n");

        // retorna o codigo de um aluno  que tem mais notas no grupo (caso aconteca) para garantir que retorne todas as avaliações.
        // Isso é necessario porque alguns alunos podem ser inseridos no grupo depois que algumas avaliações ja foram feitas para este grupo
        // E sempre que avalia um grupo, todos os alunos do grupo recebem a mesma avaliaçao
        $foiavaliado=GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo);
        if ($foiavaliado)           //Ja existe uma nota atribuida
        {
          $cod=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo);
          $dados_nota=RetornaDadosNotaGrupo($sock, $cod_grupo, $cod_avaliacao,$cod_usuario,$usr_formador, $cod);
          $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
          $cod_nota=$dados_nota['cod_nota'];
          $nota=FormataNota($dados_nota['nota']);

          if ($usr_formador)
          {
            if (!strcmp($tipo_compartilhamento,'T'))
            /* 51 - Totalmente Compartilhado*/
              $compartilhamento=RetornaFraseDaLista($lista_frases,51);
            elseif (!strcmp($tipo_compartilhamento,'G'))
            /* 53 - Compartilhado com Formadores e com o Grupo*/
              $compartilhamento=RetornaFraseDaLista($lista_frases,53);
            else
            /* 52 - Compartilhado com Formadores*/
              $compartilhamento=RetornaFraseDaLista($lista_frases,52);
            //Notas
            if ($nota=='')
            {
              echo("      <td class=text align=center>");
              if (!$SalvarEmArquivo)
              {
                echo("      <a href=# onClick=return(AvaliarAlunoPortfolio(".$cod."));>"."[Avaliar]"."</a>");
              }
              else
              {
                echo("&nbsp;");
              }
              echo("</td>\n");
              echo("      <td class=text align=center>"."&nbsp;"."</td>\n");
            }
            else
            {
              $marcaib="";
              $marcafb="";
              echo("      <td class=text align=center>");
              if (!$SalvarEmArquivo)
              {
                echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolioGrupo(".$cod_grupo."));>".$nota."</a>\n");
              }
              else
                echo($nota);
              echo("</td>\n");
              if (!$SalvarEmArquivo)
                $compartilhamento=$marcaib."<a class=text href=# onMouseDown=\"js_cod_nota=".$cod_nota.";AtualizaComp('".$tipo_compartilhamento."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>".$marcafb;
              echo("      <td class=text align=center>".$compartilhamento);
              echo("</td>\n");
            }
          }
          else           //é aluno
          {
            if ($nota=='')
              echo("      <td class=text align=center>&nbsp;</td>\n");
            else
            {
              echo("      <td class=text align=center>");
              if (!strcmp($tipo_compartilhamento,'T'))
              {
                if (!$SalvarEmArquivo)
                {
                  echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>");
                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              elseif ((!strcmp($tipo_compartilhamento,'G')) && ($portfolio_grupo))
              {
                $cod_grupo_usuario=RetornaCodGrupoPortfolio($sock,$cod_usuario);         //retorna o codigo do grupo do usuario que esta acessando
                if ($cod_grupo_usuario==$cod_grupo)    //O usuario pertence ao grupo que foi avaliado
                {
                  if (!$SalvarEmArquivo)
                    echo("<a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>".$nota."</a></td>\n");
                  else
                    echo($nota."</td>\n");
                }
                else
                  echo("&nbsp;</td>\n");
              }
              else //Está compartilhada só com formadores
                echo("&nbsp;</td>\n");
            }
          }
        }
        else // nenhuma nota foi atribuida
        {
          $cod=RetornaCodAlunodoGrupo($sock,$cod_avaliacao,$cod_grupo);
          if (($usr_formador)  && (!$SalvarEmArquivo))
          {
            if (!strcmp($dados['Ferramenta'],'P'))
            {
              if($portfolio_grupo)
                echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo."));>");
              else
                echo("      <td class=text align=center><a href=# onClick=return(AvaliarAluno(".$cod."));>");
           }
           else
              echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoPortfolio(".$cod."));>");

            echo("Avaliar</a></td>\n");
            echo("      <td class=text align=center>");
            echo("&nbsp;</td>\n");
          }
          elseif (($usr_formador)  && ($SalvarEmArquivo))
          {
            echo("      <td class=text align=center>");
            echo("&nbsp;</td>\n");
            echo("      <td class=text align=center>");
            echo("&nbsp;</td>\n");
          }
          else
            echo("      <td class=text align=center>&nbsp;</td>\n");
        }
        echo("    </tr>\n");
      }
      echo("</table>\n");
    }
    else
      echo("Não há grupos criados<br>");
  }
  else  // não é portfolio de grupo
  {
    $lista_users=RetornaListaUsuariosAluno($sock);

    if (count($lista_users) > 0)
    {
      //Tabela com a lista de alunos do curso, com suas respectivas notas na avaliação realizada
      echo("<table border=0 width=100%>\n");
      echo("  <tr class=menu>\n");
      /* 64 - Alunos */
      echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,64)."</td>\n");

      if ($usr_formador)
      {
        /* 60 - Nota */
        echo("    <td class=colorfield align=center width=20%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
        /* 63 - Compartilhamento */
        echo("    <td class=colorfield align=center width=47%>".RetornaFraseDaLista($lista_frases,63)."</td>\n");
      }
      else
      /* 60 - Nota */
        echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");

      echo("    </tr>\n");

      foreach($lista_users as $cod => $nome)
      {
        $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod);
        if ($i==0)
          $field="g1field";
        else
          $field="g2field";
        echo("    <tr class=".$field.">\n");

        $i = ($i + 1) % 2;

        echo("      <td class=text>");
        if (!$SalvarEmArquivo)
          echo("<a class=text href=# onClick=return(AbrePerfil(".$cod.")); class=text>".$nome."</a></td>\n");
        else
          echo($nome."</td>\n");

        if ($foiavaliado)             //Ja existe uma nota atribuida
        {
            $dados_nota=RetornaDadosNota($sock, $cod, $cod_avaliacao,$cod_usuario,$usr_formador);
          $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
          $cod_nota=$dados_nota['cod_nota'];
          $nota=FormataNota($dados_nota['nota']);

          if ($usr_formador)
          {
            if (!strcmp($tipo_compartilhamento,'T'))
            /* 51 - Totalmente Compartilhado*/
              $compartilhamento=RetornaFraseDaLista($lista_frases,51);
            elseif (!strcmp($tipo_compartilhamento,'A'))
            /* 54 - Compartilhado com Formadores e com o Participante*/
              $compartilhamento=RetornaFraseDaLista($lista_frases,54);
            else
            /* 52 - Compartilhado com Formadores*/
              $compartilhamento=RetornaFraseDaLista($lista_frases,52);
            //Notas
            if ($nota=='')
            {
              echo("      <td class=text align=center>");
              if (!$SalvarEmArquivo)
              {
                if (strcmp($dados['Ferramenta'],'P'))
                  {
                  if($portfolio_grupo)
                     echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo."));>");
                  else
                     echo("      <a href=# onClick=return(AvaliarAluno(".$cod."));>");
                  }
                else
                  echo("      <a href=# onClick=return(AvaliarAlunoPortfolio(".$cod."));>");

                echo("Avaliar</a></td>\n");
              }
              else
                echo("&nbsp;</td>\n");
              echo("      <td class=text align=center>");
              echo("&nbsp;</td>\n");
            }
            else
            {
              $marcaib="";
              $marcafb="";
              echo("      <td class=text align=center>");
              if (!$SalvarEmArquivo)
              {
                if (strcmp($dados['Ferramenta'],'P'))
                  echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod."));>");
                else
                  echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>");
                echo($nota."</a></td>\n");
              }
              else
                echo($nota."</td>\n");
              if (!$SalvarEmArquivo)
                $compartilhamento=$marcaib."<a class=text href=# onMouseDown=\"js_cod_nota=".$cod_nota.";AtualizaComp('".$tipo_compartilhamento."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>".$marcafb;
              echo("      <td class=text align=center>".$compartilhamento);
              echo("</td>\n");
            }
          }
          else           //é aluno
          {
            if ($nota=='')
              echo("      <td class=text align=center>&nbsp;</td>\n");
            else
            {
              echo("      <td class=text align=center>");
              if (!strcmp($tipo_compartilhamento,'T'))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($dados['Ferramenta'],'P'))
                    echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod."));>");
                  else
                    echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>");
                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              elseif ((!strcmp($tipo_compartilhamento,'A')) && ($cod_usuario==$cod))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($dados['Ferramenta'],'P'))
                    echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod."));>");
                  else
                    echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>");
                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              else //Está compartilhada só com formadores
                echo("&nbsp;</td>\n");
            }
          }
        }
        else // nenhuma nota foi atribuida
        {
          if (($usr_formador)  && (!$SalvarEmArquivo))
          {
            if (strcmp($dados['Ferramenta'],'P'))
             {
             if($portfolio_grupo)
               echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo."));>");
             else
               echo("      <td class=text align=center><a href=# onClick=return(AvaliarAluno(".$cod."));>");
             }
            else
              echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoPortfolio(".$cod."));>");
            echo("Avaliar</a></td>\n");
            echo("      <td class=text align=center>");
            echo("&nbsp;</td>\n");
          }
          elseif (($usr_formador)  && ($SalvarEmArquivo))
          {
            echo("      <td class=text align=center>");
            echo("&nbsp;</td>\n");
            echo("      <td class=text align=center>");
            echo("&nbsp;</td>\n");
          }
          else
            echo("      <td class=text align=center>&nbsp;</td>\n");
        }
        echo("    </tr>\n");
      }
      echo("</table><br><br>\n");
    }

    $lista_users_formador=RetornaListaUsuariosFormador($sock);

    if ((count($lista_users_formador) > 0) && ($usr_formador))
    {
      //Tabela com a lista de alunos do curso, com suas respectivas notas na avaliação realizada
      echo("<table border=0 width=100%>\n");
      echo("  <tr class=menu>\n");
      /* 156 - Formadores */
      echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,156)."</td>\n");

      if ($usr_formador)
      {
        /* 60 - Nota */
        echo("    <td class=colorfield align=center width=20%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
        /* 63 - Compartilhamento */
        echo("    <td class=colorfield align=center width=47%>".RetornaFraseDaLista($lista_frases,63)."</td>\n");
      }
      else
      /* 60 - Nota */
        echo("    <td class=colorfield align=center width=33%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");

      echo("    </tr>\n");

      foreach($lista_users_formador as $cod => $nome)
      {
        $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod);
        //$status_usuario=RetornaStatusUsuario($sock,$cod_curso,$cod);
        //if ($status_usuario != "F")
        //{
          if ($i==0)
            $field="g1field";
          else
            $field="g2field";
          echo("    <tr class=".$field.">\n");

          $i = ($i + 1) % 2;

          echo("      <td class=text>");
          if (!$SalvarEmArquivo)
            echo("<a class=text href=# onClick=return(AbrePerfil(".$cod.")); class=text>".$nome."</a></td>\n");
          else
            echo($nome."</td>\n");

          if ($foiavaliado)             //Ja existe uma nota atribuida
          {
              $dados_nota=RetornaDadosNota($sock, $cod, $cod_avaliacao,$cod_usuario,$usr_formador);
            //$tipo_compartilhamento=RetornaCompartilhamento($sock, $cod, $cod_avaliacao);
            //$cod_nota=RetornaCodNota($sock, $cod, $cod_avaliacao);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=FormataNota($dados_nota['nota']);

            if ($usr_formador)
            {
               if (!strcmp($tipo_compartilhamento,'T'))
               /* 51 - Totalmente Compartilhado*/
                 $compartilhamento=RetornaFraseDaLista($lista_frases,51);
               elseif (!strcmp($tipo_compartilhamento,'A'))
               /* 54 - Compartilhado com Formadores e com o Participante*/
                 $compartilhamento=RetornaFraseDaLista($lista_frases,54);
               else
               /* 52 - Compartilhado com Formadores*/
                 $compartilhamento=RetornaFraseDaLista($lista_frases,52);
              //Notas
              if ($nota=='')
              {
                echo("      <td class=text align=center>");
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($dados['Ferramenta'],'P'))
                   {
                   if($portfolio_grupo)
                     echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo."));>");
                   else
                    echo("      <a href=# onClick=return(AvaliarAluno(".$cod."));>");
                   }
                  else
                  echo("      <a href=# onClick=return(AvaliarAlunoPortfolio(".$cod."));>");
                  echo("Avaliar</a></td>\n");
                }
                else
                  echo("&nbsp;</td>\n");
                echo("      <td class=text align=center>");
                echo("&nbsp;</td>\n");
              }
              else
              {
                $marcaib="";
                $marcafb="";
                echo("      <td class=text align=center>");
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($dados['Ferramenta'],'P'))
                    echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod."));>");
                  else
                    echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>");
                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
                if (!$SalvarEmArquivo)
                  $compartilhamento=$marcaib."<a class=text href=# onMouseDown=\"js_cod_nota=".$cod_nota.";AtualizaComp('".$tipo_compartilhamento."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>".$marcafb;
                echo("      <td class=text align=center>".$compartilhamento);
                echo("</td>\n");
              }
            }
            else           //é aluno
            {
              if ($nota=='')
                echo("      <td class=text align=center>&nbsp;</td>\n");
              else
              {
                echo("      <td class=text align=center>");
                if (!strcmp($tipo_compartilhamento,'T'))
                {
                  if (!$SalvarEmArquivo)
                  {
                    if (strcmp($dados['Ferramenta'],'P'))
                      echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod."));>");
                    else
                      echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>");
                    echo($nota."</a></td>\n");
                  }
                  else
                    echo($nota."</td>\n");
                }
                elseif ((!strcmp($tipo_compartilhamento,'A')) && ($cod_usuario==$cod))
                {
                  if (!$SalvarEmArquivo)
                  {
                    if (strcmp($dados['Ferramenta'],'P'))
                      echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod."));>");
                    else
                      echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod."));>");
                    echo($nota."</a></td>\n");
                  }
                  else
                    echo($nota."</td>\n");
                }
                else //Está compartilhada só com formadores
                  echo("&nbsp;</td>\n");
              }
            }
          }
          else // nenhuma nota foi atribuida
          {
            if (($usr_formador)  && (!$SalvarEmArquivo))
            {
              if (strcmp($dados['Ferramenta'],'P'))
               {
               if($portfolio_grupo)
                 echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo."));>");
               else
                echo("      <td class=text align=center><a href=# onClick=return(AvaliarAluno(".$cod."));>");
               }
              else
                echo("      <td class=text align=center><a href=# onClick=return(AvaliarAlunoPortfolio(".$cod."));>");
              echo("Avaliar</a></td>\n");
              echo("      <td class=text align=center>");
              echo("&nbsp;</td>\n");
            }
            elseif (($usr_formador)  && ($SalvarEmArquivo))
            {
              echo("      <td class=text align=center>");
              echo("&nbsp;</td>\n");
              echo("      <td class=text align=center>");
              echo("&nbsp;</td>\n");
            }
            else
              echo("      <td class=text align=center>&nbsp;</td>\n");
         }
         echo("    </tr>\n");
      }
      echo("</table>\n");
    }
  }

  if (($usr_formador) && (!$SalvarEmArquivo))
  {
    /* Mudar Compartilhamento */
    echo("<div id=comp class=block visibility=hidden onContextMenu=\"return(false);\">\n");
    echo("<form method=post name=form_comp action=ver_notas.php>\n");
    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("  <input type=hidden name=cod_nota value=\"\">\n");
    echo("  <input type=hidden name=acao value=mudarcomp>\n");
    echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    echo("  <input type=hidden name=VeioDaAtividade value=".$VeioDaAtividade.">\n");
    echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
    echo("  <input type=hidden name=origem value=".$origem.">\n");
    echo("<table class=wtfield cellspacing=1 cellpadding=1 border=2>\n");
    echo("  <tr>\n");
    echo("    <td class=bgcolor align=right colspan=2><a href=# onClick=EscondeLayer(cod_comp);return(false);><img src=../figuras/x.gif border=0></a></td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <table class=wtfield border=0>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    echo("            <input type=radio name=tipo_comp value=\"T\" class=wtfield onClick=\"submit();\">\n");
    echo("          </td>\n");
    echo("          <td>\n");
    /* 51 - Totalmente compartilhado */
    echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,51)."</nobr></font>\n");
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    echo("            <input type=radio name=tipo_comp value=\"F\" class=wtfield onClick=\"submit();\">\n");
    echo("          </td>\n");
    echo("          <td>\n");
    /* 52 - Compartilhado com formadores */
    echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,52)."</nobr></font>\n");
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    if ($portfolio_grupo)
    {
      echo("            <input type=radio name=tipo_comp value=\"G\" class=wtfield onClick=\"submit();\">\n");
      echo("          </td>\n");
      echo("          <td>\n");
      /* 53 - Compartilhado com Formadores e com o Grupo */
      echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,53)."</nobr></font>\n");
    }
    else
    {
      echo("            <input type=radio name=tipo_comp value=\"A\" class=wtfield onClick=\"submit();\">\n");
      echo("          </td>\n");
      echo("          <td>\n");
      /* 54 - Compartilhado com Formadores e com o Participante */
      echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,54)."</nobr></font>\n");
    }
    echo("          </td>\n");
    echo("        </tr>\n");

    echo("      </table>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("</form>\n");
    echo("</div>\n");

  }

  echo("    <form name=frmMsg action=".$origem.".php?".RetornaSessionID()." method=post>\n");

  echo("      <div align=right>\n");
  if (!$SalvarEmArquivo)
  {
    /* 50 - Salvar em Arquivo (geral) */
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,50)."' onClick='SalvarVerNotas();'>\n");
    echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  }

  /* 14 - Imprimir */
  echo("<input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,14)."' onClick=ImprimirRelatorio();>\n");

  if (!$SalvarEmArquivo)
  {
    if ($VeioDaAtividade)
    /* 13 - Fechar (ger) */
      echo("  &nbsp;<input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onClick=self.close()>\n");
    // else
    // 23 - Voltar (gen)
      // echo("<input class=text type=submit value='".RetornaFraseDaLista($lista_frases_geral,23)."'>\n");
  }


  echo("      </div>\n");
  echo("      <br>\n");

  echo("    </form>\n");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
