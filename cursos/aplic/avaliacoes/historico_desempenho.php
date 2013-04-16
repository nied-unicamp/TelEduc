<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/historico_desempenho.php

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
  ARQUIVO : cursos/aplic/avaliacoes/historico_desempenho.php
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
  $tabela="Avaliacao_notas";

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);

  $usr_aluno=EAluno($sock,$cod_curso,$cod_usuario);

  if ($acao=="mudarcomp" && $usr_formador)
  {
    if ($portfolio_grupo)
    {

      if ($VeiodePortfolio)
         $cod_grupo_mudarcomp=RetornaCodGrupo($sock,$cod_item);
      else if(isset($cod_grupo))
         $cod_grupo_mudarcomp=$cod_grupo;
      else
         $cod_grupo_mudarcomp=RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao);                                
       
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo_mudarcomp);
      $data=RetornaDataNota($sock,$cod_nota);
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {
        $cod_nota=RetornaCod($sock, $cod_aluno, $cod_avaliacao,$data['data']);
        MudarCompartilhamento($sock, $cod_nota, $tipo_comp);
      }
    }
    else
      MudarCompartilhamento($sock, $cod_nota, $tipo_comp);
  }

  if ($acao=="apagar" && $usr_formador)
  {
    if ($portfolio_grupo)
    {

     if ($VeiodePortfolio)
       $cod_grupo_apagar=RetornaCodGrupo($sock,$cod_item);
     else if(isset($cod_grupo))
       $cod_grupo_apagar=$cod_grupo;
     else
       $cod_grupo_apagar=RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao);
 
       
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo_apagar);
      $data=RetornaDataNota($sock,$cod_nota);
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {
        $cod_nota=RetornaCod($sock, $cod_aluno, $cod_avaliacao,$data['data']);
        ApagarNota($sock, $cod_nota, $cod_usuario);
      }
    }
    else
      ApagarNota($sock, $cod_nota, $cod_usuario);
  }

  echo("<html>\n");
  /* 1 - Avalia��es*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");

  if (!$SalvarEmArquivo)
  {
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
  }
  else
  {
    echo("  <style>\n");
    include "../teleduc.css";
    include "avaliacoes.css";
    echo("  </style>\n");
  }

  if(isset($cod_grupo))
    $cod_aluno=RetornaCodAlunodoGrupo($sock,$cod_avaliacao,$cod_grupo);
  
  if ($VeiodePortfolio)
  {
    $lista=RetornaAssociacaoItemAvaliacao($sock,$cod_item);
    if (!$portfolio_grupo)
      $cod_aluno=RetornaCodUsuarioPortfolio($sock,$cod_item);
    $cod_avaliacao=$lista['cod_avaliacao'];
  }

  if ($VeioDeTodasNotas)
  {
    $temp=explode(".",$cod_aluno_avaliacao);
    $cod_aluno=$temp[0];
    $cod_avaliacao=$temp[1];
    $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
    $ferramenta=$dados['Ferramenta'];
    $cod_atividade=$dados['Cod_atividade'];
    if ((!strcmp($ferramenta,'P')) || (!strcmp($ferramenta,'E')))
    {
      if (!strcmp($dados['Tipo'],'G'))
        $portfolio_grupo=1;
      else
        $portfolio_grupo=0;
    }
  }

  if (!isset($cod_atividade))
    $cod_atividade=RetornaCodAtividade($sock,$cod_avaliacao);

  /* Fun��es JavaScript */
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

    echo("  function AvaliarAlunoPortfolio(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&portfolio_grupo=".$portfolio_grupo."&VeioPeloPortfolio=0&cod_avaliacao=".$cod_avaliacao."&cod_usuario_portfolio=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");

  }

  echo("function AbrePerfil(cod_usuario)\n");
  echo("{\n");
  echo("  window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("  return(false);\n");
  echo("}\n");

  echo("function AbreJanelaComponentes(cod_grupo)\n");
  echo("{\n");
  echo("  window.open('componentes.php?".RetornaSessionID()."&cod_avaliacao=".$cod_avaliacao."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("  return false;\n");
  echo("}\n");

  // Fun��o JvaScript para chamar p�gina para salvar em arquivo.
  echo("      function SalvarHistoricoDesempenho()\n");
  echo("      {\n");
  echo("        document.frmHist.action = \"salvar_historico_desempenho.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("        document.frmHist.submit();\n");
  echo("      }\n\n");

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  // 51- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  // echo("function Apagar() \n");
  // echo("{\n");
  // echo("  if(confirm(\"".RetornaFraseDaLista($lista_frases,86)."\\n ".RetornaFraseDaLista($lista_frases,87)."\"))\n");
  // echo("  {\n");
  // echo("    document.location = 'historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_nota=".$cod_nota."&acao=apagar&cod_aluno=".$cod_aluno."&cod_grupo_portfolio=".$cod_grupo."&portfolio_grupo=".$portfolio_grupo."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$ferramenta."';\n");
  // echo("  }\n");
  // echo("}\n");

  echo("function Apagar(cod_nota) \n");
  echo("{\n");
  echo("  if(confirm(\"".RetornaFraseDaLista($lista_frases,86)."\\n ".RetornaFraseDaLista($lista_frases,87)."\"))\n");
  echo("  {\n");
  echo("    document.location = 'historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_nota='+ cod_nota +'&acao=apagar&cod_aluno=".$cod_aluno."&cod_grupo_portfolio=".$cod_grupo."&portfolio_grupo=".$portfolio_grupo."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$ferramenta."';\n");
  echo("  }\n");
  echo("}\n");

  echo("function VerObj()\n");
  echo("{\n");
  $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
  $nome_janela = "'AvaliacoesHistorico'";
  echo("  window.open('ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."', ".$nome_janela.", ".$param.");\n");
  echo("  return false;");
  echo("}\n");

  echo("    </script>\n\n");

  if ($usr_formador)
  {
    // A fun��o iniciar s� existe para formadores
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
    $escondelayer="EscondeLayers();";
  }
  else
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");

  // 1 -  Avalia��es
  $cabecalho = "  <b class=titulo> ".RetornaFraseDaLista($lista_frases,1)."</b>";
  // 103 - Hist�rico do Desempenho do Participante
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,103)."</b>";

  $cod_pagina = 7;
  // Cabecalho
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  echo("<br>\n");
  echo("<p>\n");

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $titulo=RetornaTituloAvaliacao($sock,$ferramenta,$cod_atividade);

  if ($dados['Ferramenta'] == 'P')
  {
    $titulo = RetornaAtividade($sock,$dados['Cod_atividade']);
    if ($dados['Tipo'] == 'I')
      // 161 - Atividade individual no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 161);
    elseif ($dados['Tipo'] == 'G')
      // 162 - Atividade em grupo no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 162);
  }
  if ($dados['Ferramenta'] == 'E')
  {
   $titulo = RetornaExercicio($sock,$dados['Cod_atividade']);
   if ($dados['Tipo'] == 'I')
      // 176 - Exerc�cio individual
      $tipo = RetornaFraseDaLista($lista_frases, 176);
   elseif ($dados['Tipo'] == 'G')
      // 174 - Exerc�cio em grupo
      $tipo = RetornaFraseDaLista($lista_frases, 174);
  }
  else if ($dados['Ferramenta'] == 'F')
  {
    // 145 - F�rum de Discuss�o
    $tipo = RetornaFraseDaLista($lista_frases,145);
    $titulo = RetornaForum($sock,$dados['Cod_atividade']);
  }
  elseif ($dados['Ferramenta'] == 'B')
  {
    // 146 - Sess�o de Bate-Papo
    $tipo = RetornaFraseDaLista($lista_frases,146);
    $titulo = RetornaAssunto($sock,$dados['Cod_atividade']);
  }
  elseif ($dados['Ferramenta']=='N')
  {
     $dados_avaliacao_externa=RetornaDadosDoItemExterna($sock,$dados['Cod_atividade']);
     $titulo=$dados_avaliacao_externa['titulo'];
     if($dados_avaliacao_externa['status']=='I')
     {
        $tipo=RetornaFraseDaLista($lista_frases, 185);
     }
     else
     {
        $tipo= RetornaFraseDaLista($lista_frases, 186);        
     }
  }
  
  if (! $SalvarEmArquivo)
  {
    echo(" <p>\n");
    echo("  <table cellpadding=0 cellspacing=0 border=0  style=\"width: 100%;\" class=colorfield>\n");
    echo("  <tbody>\n");
    echo("    <tr>\n");
    // 46 - Ver objetivos/crit�rios da avalia��o
    echo("      <td><center><a class=colorfield href=# onClick=VerObj();>".RetornaFraseDaLista($lista_frases, 46)."</a></center></td>\n");
    echo("    </tr>\n");
    echo("  </tbody>\n");
    echo("  </table>\n");
    echo(" </p>\n");
  }

  echo("<table border=0 width=100% cellspacing=0>\n");
  echo("  <tbody>\n");
  echo("    <tr>\n");
  // imagem
  echo("      <td width=1% class=colorfield>&nbsp;</td>\n");
  // 123 - T�tulo
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
  // 113 - Tipo da Avalia��o
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
  // 19 - Valor
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
  echo("    </tr>\n");
  echo("    <tr>\n");
  $img = !$SalvarEmArquivo ? "<img src=../figuras/avaliacao.gif border=0>" : "&nbsp;&nbsp;";
  echo("      <td width=1%>".$img."</td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".$titulo."</td>\n");
  // echo("      <td class=text align=left>&nbsp;&nbsp;".$ferramenta."</td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".$tipo."</td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".FormataNota($dados['Valor'])."</td>\n");
  echo("    </tr>\n");
  echo("</table>\n");
  echo("<br>\n");

  // Nome do participante ou grupo
  if ($portfolio_grupo)
  {
    if ($VeiodePortfolio)
      $cod_grupo_portfolio=RetornaCodGrupo($sock,$cod_item);
    else if(isset($cod_grupo))
      $cod_grupo_portfolio=$cod_grupo;
    else
    /*Aqui troquei a fun��o RetornaCodGrupoPortfolioAvaliacao($sock,$cod_aluno,$cod_avaliacao) pela fun��o RetornaCodigoGrupoAvaliacao($sock,$cod,$linha['Cod_avaliacao']);, para pegar o codigo do grupo em que o aluno estava quando a avalia��o foi feita*/
      $cod_grupo_portfolio=RetornaCodigoGrupoAvaliacao($sock,$cod_aluno,$cod_avaliacao);
      
    // $cod_aluno=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo_portfolio);
    
    /*Aqui havia um problema que ele permitia a variavel $cod_grupo_portfolio ficar nula, agora ele n�o deixa mais e exibe a frase 93 - Este grupo ainda n�o foi avaliado nesta atividade!
    */
    if (!$cod_grupo_portfolio)
      {
         $cod_grupo_portfolio=0;
      }    
    $nome=NomeGrupo($sock,$cod_grupo_portfolio);
  }
  else
  {
    $nome=NomeUsuario($sock,$cod_aluno);
  }

  if (!$portfolio_grupo)
  {
    /*
      // 47 - Participante:
      echo("<font class=text>".RetornaFraseDaLista($lista_frases,47).": </font>"); */
    if (!$SalvarEmArquivo)
      $nome = "<a class=text href=# onClick=return(AbrePerfil(".$cod_aluno.")); class=text>".$nome."</a>";
    else
      $nome = "<font class=text>".$nome."</font>";
  }
  else
  {
    /*
      // 48 - Grupo
      echo("<font class=text>".RetornaFraseDaLista($lista_frases,48).": </font>");  */
    if (!$SalvarEmArquivo)
      $nome = "<a class=text href=# onClick=return(AbreJanelaComponentes(".$cod_grupo_portfolio."));>".$nome."</a>";
    else
      $nome = "<font class=text>".$nome."</font>";
  }

  if (!strcmp($ferramenta,'P'))
  {
    if ($portfolio_grupo)
      $cod=$cod_grupo_portfolio;
    else
      $cod=$cod_aluno;

    if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao,$cod,$portfolio_grupo))
    {
      $num_participacoes=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod_aluno);
      if (!$SalvarEmArquivo)
      {
        if($portfolio_grupo)
        {
          // echo("        .$num_participacoes."</a><br><br>\n");
          $num_participacoes =
            "<a class=text href=# onClick=\"window.open('../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_avaliacao=".$cod_avaliacao."','ItensPortfolioParticipante','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">"
            .$num_participacoes
            ."</a>";
        }
        else
        {
          $num_participacoes =
            "<a class=text href=# onClick=\"window.open('../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario_portfolio=".$cod_aluno."&cod_avaliacao=".$cod_avaliacao."','ItensPortfolioParticipante','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">"
            .$num_participacoes
            ."</a>";
        }
      }
      else
      {
        $num_participacoes = "<font class=text>".$num_participacoes."</font>";
      }
    }
    else
      $num_participacoes = "<font class=text>"."0"."</font>";
  }
  else if ($ferramenta=='N')
  {
     $num_participacoes="-";
  }
   else if (!strcmp($ferramenta,'E'))
  {
    if ($portfolio_grupo)
      $cod=$cod_grupo_portfolio;
    else
      $cod=$cod_aluno;
    $modelo=RespondeuExercicio($sock,$cod_avaliacao,$cod,$portfolio_grupo);                                                                                                                        
    $titulo = RetornaExercicio($sock, $modelo);
    if ($modelo!=0)
    {
$param = "'width=600,height=400,top=150,left=150,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'";
      if (!$SalvarEmArquivo)
      {
        if($portfolio_grupo)
        {
          $num_participacoes ="<a class=text href=# onClick=\"window.open('../exercicios/ver_aplicado_popup.php?&origem=avaliacao&cod_dono=".$cod_grupo_portfolio."&cod_resolucao=".$modelo['cod_resolucao']."&cod_curso=".$cod_curso."' ,'ExercicioResolvido',".$param.");".$escondelayer."return(false);\">".$titulo."</a>";
        }
        else
        {
         $num_participacoes ="<a class=text href=# onClick=\"window.open('../exercicios/ver_aplicado_popup.php?&origem=avaliacao&cod_dono=".$cod_aluno."&cod_resolucao=".$modelo['cod_resolucao']."&cod_curso=".$cod_curso."' ,'ExercicioResolvido',".$param.");".$escondelayer."return(false);\">".$titulo."</a>";
        }
      }
      else
      {
        $num_participacoes = "<font class=text>".$titulo."</font>";
      }
    }

  }
  elseif (!strcmp($ferramenta,'B'))
  {

    $nome_bak = $nome;
     
    if (BatePapoExiste($sock,$cod_atividade))
    {
      $lista_usuarios=RetornaUsuarios($sock,$cod_curso);
      $lista_sessoes=RetornaCodSessao($sock,$cod_atividade);
      foreach($lista_sessoes as $cod => $linha)
      {
        $msgs_qtde=RetornaQtdeMsgsUsuario($sock,$linha['Cod_sessao'],$lista_usuarios);
        //para cada aluno incrementar a quantidade de mensagens
        foreach($lista_usuarios as $cod => $nome)
        {
          $msgs_total[$cod]=$msgs_total[$cod]+$msgs_qtde[$cod];
        }
      }

      if ( ($num_participacoes = (int)$msgs_total[$cod_aluno]) == 0 )
      {
        // zero participa��es
        $num_participacoes = "<font class=text>"."0"."</font>";
      }
      else
      {
        if (!$SalvarEmArquivo)
        {
          $num_participacoes =
          "<a class=text href=# onClick=\"window.open('../batepapo/ver_falas_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_aluno."&cod_assunto=".$cod_atividade."&cod_avaliacao=".$cod_avaliacao."','VerParticipacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">"
          .(int)$msgs_total[$cod_aluno]
          ."</a>";
        }
        else
        {
          $num_participacoes = "<font class=text>".(int)$msgs_total[$cod_aluno]."</font>";
        }
      }
    }
    else
      $num_participacoes = "<font class=text>"."0"."</font>";

    $nome = $nome_bak;
      
  }
  elseif (!strcmp($ferramenta,'F'))
  {
    if (ParticipouDoForum($sock,$cod_aluno,$cod_atividade))
    {
      $num_participacoes=RetornaNumMsgsParticipantesForum($sock,$cod_atividade,$cod_aluno);
      if (!$SalvarEmArquivo)
        //echo("        <a class=text href=# onClick=\"window.open('../forum/ver_mensagens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_aluno."&cod_forum=".$cod_atividade."&cod_avaliacao=".$cod_avaliacao."','VerParticipacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">".$num_participacoes."</a><br><br>\n");
        $num_participacoes="<a class=text href=# onClick=\"window.open('../forum/ver_mensagens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_aluno."&cod_forum=".$cod_atividade."&cod_avaliacao=".$cod_avaliacao."','VerParticipacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">".$num_participacoes."</a><br><br>";
      else
        //echo("<font class=text>".$num_participacoes."</font><br><br>\n");
        $num_participacoes="<font class=text>".$num_participacoes."</font><br><br>";
    }
    else
    {
      $num_participacoes=0;
    }
  }
  else
    // 11 - Erro Interno...
    exit(RetornaFraseDaLista($lista_frases,11));

  // tabela com nome do participante e n�mero de participacoes
  echo("<p>");
  echo("<table width=100% border=0 cellpadding=0 cellspacing=0>\n");
  echo("<tr class=colorfield> \n");
  // 107 - Nome
  echo("<td>"."&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 107)."</td>\n");
  // 49 - Participa��es:
  echo("<td>"."&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 49)."</td>\n");
  echo("</tr> \n");

  echo("<tr> \n");
  echo("<td>"."&nbsp;&nbsp;".$nome."</td>\n");
  echo("<td>"."&nbsp;&nbsp;".$num_participacoes."</td>\n");
  echo("</tr> \n");
  echo("</table> \n");
  echo("<p>");


  $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod_aluno);

  if(!$foiavaliado)
  {
    if ($portfolio_grupo)
    {
      if ($cod_grupo_portfolio!=0) 
      // 93 - Este grupo ainda n�o foi avaliado nesta atividade!
         echo("<font class=text>".RetornaFraseDaLista($lista_frases,93)."</font> \n");
      else
         /*183 - Esse aluno n�o fazia parte de nenhum grupo quando a avalia��o foi aplicada. */
         echo("<font class=text>".RetornaFraseDaLista($lista_frases,183)."</font> \n");
      echo("<br>");
    }
    else
    {
      // 98 - Este participante ainda n�o foi avaliado nesta atividade!
      echo("<font class=text>".RetornaFraseDaLista($lista_frases,98)."</font>");
      echo("<br>");
    }
  }
  else
  {
    if ( ($dados['Ferramenta'] == 'P') && $portfolio_grupo)
    {
      $cod=$cod_grupo_portfolio;

      $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo_portfolio);
    }
    
    /*Altera��o que talvez resolva o problema da nota da avaliac��o que � registrada mais de uma vez*/
    else if ($dados['Ferramenta'] == 'E' && $dados['Tipo']=='G')
    {
       $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod);       
    }
    
    else
    {
      $cod=$cod_aluno;
      $avaliacao_atual=RetornarAvaliacaoAluno($sock,$cod_avaliacao,$cod);
    }
    $cont=0;
    if (count($avaliacao_atual)>0)
    {
      // array para alternar as cores de cada tabelinha com notas
      $array_cores = array( false => "g1field", true => "g2field");
      $cor = false;

      foreach ($avaliacao_atual as $cod => $linha)
      {
        if ($usr_formador)   //Formador pode ver qualquer nota
          $podevernota=1;
        elseif ($usr_aluno)   //Aluno so pode ver nota compartilhada com ele
        {
          if (!strcmp($linha['tipo_compartilhamento'],'T'))
            $podevernota=1;
          elseif (!strcmp($linha['tipo_compartilhamento'],'F'))
            $podevernota=0;
          elseif (($portfolio_grupo) && (!strcmp($linha['tipo_compartilhamento'],'G')))
          {
            // � portfolio de grupo e nota compartilhada so com o grupo avaliado
            $cod_grupo_usuario=RetornaCodGrupoPortfolioAvaliacao($sock,$cod_usuario,$cod_avaliacao);         //retorna o codigo do grupo do usuario que esta acessando

            if ($cod_grupo_usuario==$cod_grupo_portfolio)    // O usuario pertence ao grupo que foi avaliado
              $podevernota=1;
            else                   // outro grupo nao pode ver
              $podevernota=0;
          }
          elseif (!strcmp($linha['tipo_compartilhamento'],'A'))
          {
            // nota compartilhada so com o aluno avaliado
            if ($cod_usuario==$cod_aluno)    //O usuario � o aluno que foi avaliado
              $podevernota=1;
            else                   //outro aluno nao pode ver
              $podevernota=0;
          }
        }
        else
        {
          // 94 - Usu�rio sem acesso...
          echo (RetornaFraseDaLista($lista_frases,94));
        }
        if ($podevernota==1)
        {
          $cont++;
          $cod_nota=$linha['cod_nota'];
          if (!strcmp($linha['tipo_compartilhamento'],'T'))
            // 51 - Totalmente Compartilhado
            $compartilhamento=RetornaFraseDaLista($lista_frases,51);
          elseif (!strcmp($linha['tipo_compartilhamento'],'A'))
            // 54 - Compartilhado com Formadores e com o Participante
            $compartilhamento=RetornaFraseDaLista($lista_frases,54);
          elseif (!strcmp($linha['tipo_compartilhamento'],'G'))
            // 53 - Compartilhado com Formadores e com o Grupo
            $compartilhamento=RetornaFraseDaLista($lista_frases,53);
          else
            // 52 - Compartilhado com Formadores
            $compartilhamento=RetornaFraseDaLista($lista_frases,52);

          echo("<p>\n");
          echo("<table border=0 width=100% cellspacing=0>\n");
          echo("  <tr class=menu>\n");
          // 60 - Nota:
          echo("    <td align=center width=20%><font class=colorfield><b>".RetornaFraseDaLista($lista_frases,60)." ".$cont."</b></font></td>\n");
          // 61 - Data da Avalia��o
          echo("    <td align=center width=20%><font class=colorfield><b>".RetornaFraseDaLista($lista_frases,61)."</b></font></td>\n");
          // 68 - Formador que avaliou
          echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases, 68)."</td>\n");

          $num_colunas = 3;
          if ($usr_formador)
          {
            // 50 - Compartilhar
            echo("    <td align=center width=40%><font class=colorfield><b>".RetornaFraseDaLista($lista_frases,50)."</b></font></td>\n");
            $num_colunas ++;

            if (!$SalvarEmArquivo)
            {
              // 1 - Apagar (gen)
              echo("    <td align=center width=20%><a class=colorfield href=# onClick=return(Apagar(".$linha['cod_nota']."));>".RetornaFraseDaLista($lista_frases_geral,1)."</a></td>\n");
              $num_colunas ++;
            }
          }
          echo("  </tr>\n");

          if (! $SalvarEmArquivo)
          {
            $link_perfil1 = "<a href=# onClick='AbrePerfil(".$linha['cod_formador']."); return false;'>";
            $link_perfil2 = "</a>";
          }
          else
          {
            $link_perfil1 = $link_perfil2 = "";
          }

          echo("  <tr>\n");
          // a nota
          echo("    <td align=center class=".$array_cores[$cor]."><font class=text>".FormataNota($linha['nota'])."</font></td>\n");
          // a data da avaliacao
          echo("    <td align=center class=".$array_cores[$cor]."><font class=text>".UnixTime2Data($linha['data'])."</font></td>\n");
          // nome do formador que avaliou

	       if($linha['cod_formador']==-2) // 66 (geral) - Sistema 
	          echo("    <td align=center class=".$array_cores[$cor]."><font class=text>".RetornaFraseDaLista($lista_frases_geral, 66)."</font></td>\n");
	       else
	          echo("    <td align=center class=".$array_cores[$cor]."><font class=text>".$link_perfil1.NomeUsuario($sock,$linha['cod_formador']).$link_perfil2."</font></td>\n");
          if ($usr_formador)
          {
            if (!$SalvarEmArquivo)
              $compartilhamento="<a class=text href=# onMouseDown=\"js_cod_nota=".$cod_nota.";AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>";
            else
              $compartilhamento = "<font class=text>".$compartilhamento."</font>";
            echo("    <td align=center class=".$array_cores[$cor].">".$compartilhamento."</td>\n");

             if (!$SalvarEmArquivo)
               echo("    <td align=center class=".$array_cores[$cor].">&nbsp;</td>\n");
          }
          echo("  </tr>\n");
          // 163 - Justificativa
          echo("  <tr><td class=colorfield colspan=".$num_colunas.">".RetornaFraseDaLista($lista_frases, 163)."</td></tr>\n");
          if (($comentario = Space2Nbsp($linha['comentario'])) == "")
          {
            $comentario = "&nbsp;";
          }
          echo("  <tr><td class=".$array_cores[$cor]." colspan=".$num_colunas."><font class=text>".$comentario."</font></td></tr>\n");
          echo("  </table>\n");
          echo("</p>\n");

          // $cor = ! $cor;

          /*
            echo("    <font class=text>\n");
            // 67 - Coment�rio
            echo("<b>".RetornaFraseDaLista($lista_frases,67).":</b> ".$linha['comentario']."</font><br>\n");
            $formador=NomeUsuario($sock,$linha['cod_formador']);
            echo("    <font class=text>\n");
            // 68 - Formador que avaliou
            echo("<b>".RetornaFraseDaLista($lista_frases,68).":</b> ".$formador."</font><br><br>\n"); */
        }
        if ($cont==0)
        {
          if ($portfolio_grupo)
          {
            // 93 - Este grupo ainda n�o foi avaliado nesta atividade!
            echo("<font class=text>".RetornaFraseDaLista($lista_frases,93)."</font>");
            echo("<br>");
          }
          else
          {
            // 98 - Este participante ainda n�o foi avaliado nesta atividade!
            echo("<font class=text>".RetornaFraseDaLista($lista_frases,98)."</font>");
            echo("<br>");
          }
          echo("<br>");
        }
      }
    }
  }

  // Layer para Mudar Compartilhamento
  if (($usr_formador) && (!$SalvarEmArquivo))
  {
    echo("<div id=comp class=block visibility=hidden onContextMenu=\"return(false);\">\n");
    echo("<form method=post name=form_comp action=historico_desempenho.php>\n");
    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("  <input type=hidden name=cod_nota value=\"\">\n");
    echo("  <input type=hidden name=acao value=mudarcomp>\n");
    echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
    echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    echo("  <input type=hidden name=cod_aluno value=".$cod_aluno.">\n");
    echo("  <input type=hidden name=cod_atividade value=".$cod_atividade.">\n");
    echo("  <input type=hidden name=ferramenta value=".$ferramenta.">\n");

    if ($ferramenta == 'P')
    {
      echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
      if (isset($cod_grupo_portfolio))
        echo("  <input type=hidden name=cod_grupo value=".$cod_grupo_portfolio.">\n");
    }
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
    // 51 - Totalmente compartilhado
    echo("            <font class=text><nobr>".RetornaFraseDaLista($lista_frases,51)."</nobr></font>\n");
    echo("          </td>\n");
    echo("        </tr>\n");
    echo("        <tr>\n");
    echo("          <td>\n");
    echo("            <input type=radio name=tipo_comp value=\"F\" class=wtfield onClick=\"submit();\">\n");
    echo("          </td>\n");
    echo("          <td>\n");
    // 52 - Compartilhado com formadores
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
      // 53 - Compartilhado com Formadores e com o Grupo
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

  echo("    <form name=frmHist method=post>\n");
  echo("      <div align=right>\n");
  /*
    if (($usr_formador) && (!$SalvarEmArquivo))
    {
      if (!strcmp($ferramenta,'P'))
      {
        if ($portfolio_grupo)
        // 104 - Avaliar Grupo
          echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases,104)."' onClick=AvaliarAlunoPortfolio(".$cod_aluno.");>\n");
        else
        // 95 - Avaliar Participante
          echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases,95)."' onClick=AvaliarAlunoPortfolio(".$cod_aluno.");>\n");
      }
      else
      // 95 - Avaliar Participante
        echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases,95)."' onClick=AvaliarAluno(".$cod_aluno.");>\n");

    } */

  if (!$SalvarEmArquivo)
  {
    /* 50 - Salvar em Arquivo (geral) */
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,50)."' onClick='SalvarHistoricoDesempenho();'>\n");
    echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    echo("  <input type=hidden name=cod_atividade value=".$cod_atividade.">\n");
    echo("  <input type=hidden name=ferramenta value=".$ferramenta.">\n");
    echo("  <input type=hidden name=cod_aluno value=".$cod_aluno.">\n");
    // echo("  <input type=hidden name=cod_grupo value=".$cod_grupo.">\n");
    echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
  }

  /* 14 - Imprimir (geral) */
  echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,14)."' onClick=ImprimirRelatorio();>\n");

  // 13 - Fechar (ger)
  if (!$SalvarEmArquivo)
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onClick='window.opener.location.reload(); self.close();')>\n");

  echo("      </div>\n");
  echo("      <br>\n");

  echo("    </form>\n");
  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);

?>
