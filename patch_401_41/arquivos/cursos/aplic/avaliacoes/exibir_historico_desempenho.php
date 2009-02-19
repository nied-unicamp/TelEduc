<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/exibir_historico_desempenho.php

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
  ARQUIVO : cursos/aplic/avaliacoes/exibir_historico_desempenho.php
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
      $cod=RetornaCodAluno($sock,$cod_nota);
      $cod_grupo=RetornaCodGrupoPortfolio($sock,$cod);
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo);
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
      $cod=RetornaCodAluno($sock,$cod_nota);
      $cod_grupo=RetornaCodGrupoPortfolio($sock,$cod);
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo);
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

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  if (!strcmp($dados['Ferramenta'],'P'))
  {
    if (!strcmp($dados['Tipo'],'G'))
      $portfolio_grupo=1;
    else
      $portfolio_grupo=0;
  }


  echo("<html>\n");
 /* 1 - Avaliações*/
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");

  if (!isset($SalvarEmArquivo))
  {
    echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("    <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
  }
  else
  {
    echo("  <style>\n");
    include "../teleduc.css";
    include "avaliacoes.css";
    echo("  </style>\n");
  }
   /* Funções JavaScript */
  echo("<script language=JavaScript src=../bibliotecas/dhtmllib.js></script>\n");
  echo("<script language=JavaScript>\n");
  echo("var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
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
    echo("    if (isIE) {\n");
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
    echo("    window.open(\"avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&portfolio_grupo=".$portfolio_grupo."&VeioPeloPortfolio=0&cod_usuario_portfolio=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
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
    echo("  window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("  return false;\n");
    echo("}\n");

    /* Função JvaScript para chamar página para salvar em arquivo. */
    echo("      function SalvarHistoricoDesempenho()\n");
    echo("      {\n");
    echo("        document.frmHist.action = \"salvar_exibir_historico_desempenho.php?".RetornaSessionID());
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
    /* 51- Infelizmente não foi possível imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
    echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("    }\n");
    echo("  }\n");

    echo("function TemCertezaApagar() {\n");
    /* 86 - Você tem certeza de que deseja apagar a avaliação desse participante? */
    /* 87 - (a avaliação selecionada será apagada definitivamente) */
    echo("  return(confirm(\"".RetornaFraseDaLista($lista_frases,86)."\\n (".RetornaFraseDaLista($lista_frases,87).")\"));\n");
    echo("}\n");

    echo("    </script>\n\n");

   if ($usr_formador){
  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
   $escondelayer="EscondeLayers();";
  }
  else
  echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");

  /* 1 - Avaliações */
  $cabecalho = "  <b class=titulo> ".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 88 - Histórico do Desempenho dos Participantes*/
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,88)."</b>";
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,16));

  echo("<br>\n");
  echo("<p>\n");

  $titulo=RetornaTituloAvaliacao($sock,$dados['Ferramenta'],$dados['Cod_atividade']);

  if (!strcmp($dados['Ferramenta'],'F')) //Avaliacao no Forum
  {
    echo("    <font class=text>Fórum:</font>\n");
    echo("    <font class=text> ".$titulo."</font>");
  }
  elseif (!strcmp($dados['Ferramenta'],'B')) //Avaliacao no Bate-Papo
  {
    echo("    <font class=text>Assunto da Sessão:</font>\n");
    echo("    <font class=text> ".$titulo."</font>");
  }
  else //Avaliacao no portfolio
  {
  /* 14 - Atividade no Portfólio*/
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,14).":</font>\n");
    echo("    <font class=text> ".$titulo."</font><br>");
    /* 20 - Tipo da Atividade*/
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,20).":</font>\n");
    if (!strcmp($dados['Tipo'],'I'))
    /* 21 - Individual*/
      echo("    <font class=text> ".RetornaFraseDaLista($lista_frases,21)."</font>");
    else
    /* 22 - Em Grupo*/
      echo("    <font class=text> ".RetornaFraseDaLista($lista_frases,22)."</font>");
  }


  echo("<br>");
  /* 58 - Valor da Atividade*/
  echo("<font class=text>".RetornaFraseDaLista($lista_frases,58).": ".$dados['Valor']."</font><br><br>\n");


  if (!isset($SalvarEmArquivo))
  /* 46- Ver objetivos/critérios da avaliação */
    echo("        <a class=text href=# onClick=\"window.open('ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&EhAtalho=1&cod_avaliacao=".$cod_avaliacao."','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">".RetornaFraseDaLista($lista_frases,46)."</a><br><br>\n");


  echo("<hr>\n");

  if ((!strcmp($dados['Ferramenta'],'P')) && (!strcmp($dados['Tipo'],'G')))
  {
    if (isset($grupocod))
    {
      unset($cod_grupo);
      if (isset($grupocod))
        $cod_grupo = explode("_", $grupocod);

    //$cod_grupo_portfolio=RetornaCodGrupoPortfolio($sock,$cod_aluno);
   // $nome=NomeGrupo($sock,$cod_grupo_portfolio);
    }
    $i=0;
    unset ($hist_Cod_grupo);
    if (count($cod_grupo)>0)
      foreach($cod_grupo as $cod => $valor)
    $hist_Cod_grupo[$i++]=$valor;
    if (!is_array($hist_Cod_grupo))
    {
      /* 89 - Nenhum grupo selecionado! */
      echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,89)."</b></font>");
      echo("<br>");
      /* 92 - Selecione o grupo de quem você deseja ver o histórico de desempenho clicando sobre o nome do mesmo, ou selecionando vários grupos através das caixas de seleção e pressionando o botão "Mostrar Selecionados." */
      echo("<font class=text>".RetornaFraseDaLista($lista_frases,92)."</font>\n");
      /* G 13 - Fechar */
      echo("  <form>\n");
      echo("    <input class=text type=button value=".RetornaFraseDaLista($lista_frases_geral,13)." onClick=self.close();>\n");
      echo("  </form>\n");
    }
    else
    {
      if (is_array($cod_grupo))
      {
        $grupocod = implode("_",$cod_grupo);
      }
      $num=0;
      while($num < count($hist_Cod_grupo))
      {
        $cod_grupo_ficha = $hist_Cod_grupo[$num];
        $num++;
        $nome_grupo=NomeGrupo($sock,$cod_grupo_ficha);

        /* 48 - Grupo*/
        echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,48).": </font></b>");
        if (!isset($SalvarEmArquivo))
          echo("<a class=text href=# onClick=return(AbreJanelaComponentes(".$cod_grupo_ficha.")); class=text>".$nome_grupo."</a><br><p>\n");
        else
          echo("<font class=text>".$nome_grupo."</font><br><p>\n");

        $cod=$cod_grupo_ficha;

        /* 49 - Participações:*/
        echo("<font class=text>".RetornaFraseDaLista($lista_frases,49).": </font>");

        if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao,$cod,$portfolio_grupo))
        {
          $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,"");
          if (!isset($SalvarEmArquivo))
           echo("        <a class=text href=# onClick=\"window.open('../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo_portfolio=".$cod."&cod_avaliacao=".$cod_avaliacao."','ItensPortfolioParticipante','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">".$num_itens."</a><br><br>\n");
          else
            echo("<font class=text>".$num_itens."</font><br><br>\n");
        }
        else
          echo("<font class=text>0</font><br><br>\n");

        $foiavaliado=GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo_ficha);
        if(!$foiavaliado)
        {
          $cod_aluno=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo_ficha);
          /* 93 - Este grupo ainda não foi avaliado nesta atividade! */
          echo("<font class=text>".RetornaFraseDaLista($lista_frases,93)."</font>");
          echo("<br>");
        }
        else
        {
          $cod_aluno=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo_ficha);
/*teste*/
    if ((!strcmp($dados['Ferramenta'],'P'))&&($portfolio_grupo))
      {
      $cod=$cod_grupo_portfolio;
      $avaliacao_atual=RetornarAvaliacaoGrupo($sock,$cod_avaliacao,$cod_grupo_ficha);
      }
    else
      {
      $cod=$cod_aluno;
      $avaliacao_atual=RetornarAvaliacaoAluno($sock,$cod_avaliacao,$cod);
      }
      $cont=0;

          if(is_array($avaliacao_atual))
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
              elseif (!strcmp($linha['tipo_compartilhamento'],'G'))     //é portfolio de grupo e nota compartilhada so com o grupo avaliado
              {
                $cod_grupo_usuario=RetornaCodGrupoPortfolio($sock,$cod_usuario);         //retorna o codigo do grupo do usuario que esta acessando
                if ($cod_grupo_usuario==$cod_grupo)    //O usuario pertence ao grupo que foi avaliado
                  $podevernota=1;
                else                   //outro grupo nao pode ver
                  $podevernota=0;
              }
            }
            else
            {
            /* 94 - Usuário sem acesso...*/
              echo (RetornaFraseDaLista($lista_frases,94));
            }
            if ($podevernota==1)
            {
              $cont++;
              $cod_nota=$linha['cod_nota'];
              if (!strcmp($linha['tipo_compartilhamento'],'T'))
              /* 51 - Totalmente Compartilhado*/
                $compartilhamento=RetornaFraseDaLista($lista_frases,51);
              elseif (!strcmp($linha['tipo_compartilhamento'],'G'))
              /* 53 - Compartilhado com Formadores e com o Grupo*/
                $compartilhamento=RetornaFraseDaLista($lista_frases,53);
              else
              /* 52 - Compartilhado com Formadores*/
                $compartilhamento=RetornaFraseDaLista($lista_frases,52);

              echo("<table border=0 width=100% cellspacing=2>\n");
              echo("  <tr class=menu>\n");
              echo("    <td align=center width=20%>\n");
              echo("       <font class=colorfield>");
              /* 60 - Nota:  */
              echo("<b>".RetornaFraseDaLista($lista_frases,60)." ".$cont."</b></font>\n");
              echo("    </td>\n");
              echo("    <td align=center width=20%>\n");
              /* 61 - Data da Avaliação */
              echo("       <font class=colorfield>");
              echo("<b>".RetornaFraseDaLista($lista_frases,61)."</b></font>\n");
              echo("    </td>\n");
              if ($usr_formador)
              {
                echo("    <td align=center width=40%>\n");
                /* 50 - Compartilhar */
                echo("       <font class=colorfield>");
                echo("<b>".RetornaFraseDaLista($lista_frases,50)."</b></font>\n");
                echo("    </td>\n");

                if (!isset($SalvarEmArquivo))
               /* 1 - Apagar */
                  echo("    <td align=center width=20%><a class=colorfield href=exibir_historico_desempenho.php onClick=\"this.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_nota=".$cod_nota."&acao=apagar&grupocod=".$grupocod."&portfolio_grupo=".$portfolio_grupo."&cod_avaliacao=".$cod_avaliacao."';return(TemCertezaApagar());\">".RetornaFraseDaLista($lista_frases_geral,1)."</a></td>\n");

              }
              echo("  </tr>\n");
              echo("  <tr>\n");
              echo("    <td align=center><font class=text>".$linha['nota']."</font></td>\n");
              echo("    <td align=center><font class=text>".UnixTime2Data($linha['data'])."</font></td>\n");
              if ($usr_formador)
              {
                if (!isset($SalvarEmArquivo))
                  $compartilhamento="<a class=text href=# onMouseDown=\"js_cod_nota=".$cod_nota.";AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>";
                echo("    <td align=center>".$compartilhamento."</td>\n");

                if (!isset($SalvarEmArquivo))
                  echo("    <td align=center>&nbsp;</td>\n");
              }
              echo("  </tr>\n");
              echo("</table><br>");
              echo("    <font class=text>\n");
              /* 67 - Comentário*/
              echo("<b>".RetornaFraseDaLista($lista_frases,67).":</b> ".$linha['comentario']."</font><br>\n");
              $formador=NomeUsuario($sock,$linha['cod_formador']);
              echo("    <font class=text>\n");
              /* 68 - Formador que avaliou*/
              echo("<b>".RetornaFraseDaLista($lista_frases,68).":</b> ".$formador."</font><br><br>\n");
            }
          }
          if ($cont==0)
          {
            /* 93 - Este grupo ainda não foi avaliado nesta atividade! */
            echo("<font class=text>".RetornaFraseDaLista($lista_frases,93)."</font>");
            echo("<br>");
          }
        }
        echo("    <form name=frmAvaliar method=post>\n");

        echo("      <div align=right>\n");

        if (($usr_formador) && (!isset($SalvarEmArquivo)))
        /* 95 - Avaliar Grupo */
          echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases,104)."' onClick=AvaliarAlunoPortfolio(".$cod_aluno.");>\n");

        /* 13 - Fechar (ger) */
        if (!isset($SalvarEmArquivo))
          echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onClick=self.close()>\n");

        echo("      </div>\n");
        echo("      <br>\n");

        echo("    </form>\n");

        echo("<br>\n");
        echo("<hr>\n");
      }

    }
  }
  else
  {
    if (isset($alunocod))
    {
      unset($cod_aluno);
      if (isset($alunocod))
        $cod_aluno = explode("_", $alunocod);
    }

    $i=0;
    unset ($hist_Cod_usuario);
    if (count($cod_aluno)>0)
      foreach($cod_aluno as $cod => $valor)
    $hist_Cod_usuario[$i++]=$valor;

    if (!is_array($hist_Cod_usuario))
    {
      /* 96 - Nenhuma pessoa selecionada! */
      echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,96)."</b></font>");
      echo("<br>");
      /* 91 - Selecione o participante de quem você deseja ver o histórico de desempenho clicando sobre o nome do mesmo, ou selecionando vários participantes através das caixas de seleção e pressionando o botão "Mostrar Selecionados." */
      echo("<font class=text>".RetornaFraseDaLista($lista_frases,91)."</font>\n");
      /* G 13 - Fechar */
      echo("  <form>\n");
      echo("    <input class=text type=button value=".RetornaFraseDaLista($lista_frases_geral,13)." onClick=self.close();>\n");
      echo("  </form>\n");
    }
    else
    {
      if (is_array($cod_aluno))
      {
        $alunocod = implode("_",$cod_aluno);
      }
      $num=0;

      if (!strcmp($dados['Ferramenta'],'B'))
      {
        if (BatePapoExiste($sock,$dados['Cod_atividade']))
        {
          $batepaponaoexiste=0;
          $lista_usuarios=RetornaUsuarios($sock);
          $lista_sessoes=RetornaCodSessao($sock,$dados['Cod_atividade']);
          foreach($lista_sessoes as $cod => $linha)
          {
            $msgs_qtde=RetornaQtdeMsgsUsuario($sock,$linha['Cod_sessao'],$lista_usuarios);
            //para cada aluno incrementar a quantidade de mensagens
            foreach($lista_usuarios as $cod => $nome)
            {
              $msgs_total[$cod]=$msgs_total[$cod]+$msgs_qtde[$cod];
            }
          }
        }
        else
          $batepaponaoexiste=1;
      }

      while($num < count($hist_Cod_usuario))
      {
        $cod_aluno_ficha = $hist_Cod_usuario[$num];
        $num++;
        $nome_aluno=NomeUsuario($sock,$cod_aluno_ficha);

        /* 47 - Participante */
        echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,47).": </b></font>");
        if (!isset($SalvarEmArquivo))
          echo("<a class=text href=# onClick=return(AbrePerfil(".$cod_aluno_ficha.")); class=text>".$nome_aluno."</a><br><p>\n");
        else
          echo("<font class=text>".$nome_aluno."</font><br><p>\n");

        $cod=$cod_aluno_ficha;

        /* 49 - Participações:*/
        echo("<font class=text>".RetornaFraseDaLista($lista_frases,49).": </font>");

        if (!strcmp($dados['Ferramenta'],'B'))
        {
          if ($batepaponaoexiste)
          {
            echo("<font class=text>0</font><br><br>\n");
          }
          else
          {
            if ((int)$msgs_total[$cod_aluno_ficha]==0)
              echo("<font class=text>".(int)$msgs_total[$cod_aluno_ficha]."</font><br><br>\n");
            else
            {
              if (!isset($SalvarEmArquivo))
                echo("        <a class=text href=# onClick=\"window.open('../batepapo/ver_falas_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_aluno_ficha."&cod_assunto=".$dados['Cod_atividade']."&cod_avaliacao=".$cod_avaliacao."','VerParticipacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">".(int)$msgs_total[$cod_aluno_ficha]."</a><br><br>\n");
              else
                echo("<font class=text>".(int)$msgs_total[$cod_aluno_ficha]."</font><br><br>\n");
            }
          }
        }
        elseif (!strcmp($dados['Ferramenta'],'F'))
        {
          if (ParticipouDoForum($sock,$cod_aluno_ficha,$dados['Cod_atividade']))
          {
            $num_participacoes=RetornaNumMsgsParticipantesForum($sock,$dados['Cod_atividade'],$cod_aluno_ficha);
            if (!isset($SalvarEmArquivo))
              echo("        <a class=text href=# onClick=\"window.open('../forum/ver_mensagens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno=".$cod_aluno_ficha."&cod_forum=".$dados['Cod_atividade']."&cod_avaliacao=".$cod_avaliacao."','VerParticipacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">".$num_participacoes."</a><br><br>\n");
            else
              echo("<font class=text>".$num_participacoes."</font><br><br>\n");
          }
          else
          {
            $num_participacoes=0;
            echo("<font class=text>".$num_participacoes."</font><br><br>\n");
          }
        }
        else
        {
          if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao,$cod,$portfolio_grupo))
          {
            $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod_aluno_ficha);
            if (!isset($SalvarEmArquivo))
              echo("        <a class=text href=# onClick=\"window.open('../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario_portfolio=".$cod."&cod_avaliacao=".$cod_avaliacao."','ItensPortfolioParticipante','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');".$escondelayer."return(false);\">".$num_itens."</a><br><br>\n");
            else
              echo("<font class=text>".$num_itens."</font><br><br>\n");
          }
          else
            echo("<font class=text>0</font><br><br>\n");
        }


        $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod_aluno_ficha);
        if(!$foiavaliado)
        {
          /* 98 - Este participante ainda não foi avaliado nesta atividade! */
          echo("<font class=text>".RetornaFraseDaLista($lista_frases,98)."</font>");
          echo("<br>");
        }
        else
        {
          $avaliacao_atual=RetornarAvaliacaoAluno($sock,$cod_avaliacao,$cod_aluno_ficha);
          $cont=0;

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
              elseif (!strcmp($linha['tipo_compartilhamento'],'A'))     //nota compartilhada so com o aluno avaliado
              {
                if ($cod_usuario==$cod_aluno_ficha)    //O usuario é o aluno que foi avaliado
                  $podevernota=1;
                else                   //outro aluno nao pode ver
                  $podevernota=0;
              }
            }
            else
            {
              echo ("usuário sem acesso....");
            }
            if ($podevernota==1)
            {
              $cont++;
              $cod_nota=$linha['cod_nota'];
              if (!strcmp($linha['tipo_compartilhamento'],'T'))
                $compartilhamento='Totalmente Compartilhado';
              elseif (!strcmp($linha['tipo_compartilhamento'],'A'))
                $compartilhamento='Compartilhado com Formadores e com o Participante';
              else
                $compartilhamento='Compartilhado com Formadores';

              echo("<table border=0 width=100% cellspacing=2>\n");
              echo("  <tr class=menu>\n");
              echo("    <td align=center width=20%>\n");
              echo("       <font class=colorfield>");
              /* 60 - Nota:  */
              echo("<b>".RetornaFraseDaLista($lista_frases,60)." ".$cont."</b></font>\n");
              echo("    </td>\n");
              echo("    <td align=center width=20%>\n");
              /* 61 - Data da Avaliação */
              echo("       <font class=colorfield>");
              echo("<b>".RetornaFraseDaLista($lista_frases,61)."</b></font>\n");
              echo("    </td>\n");
              if ($usr_formador)
              {
                echo("    <td align=center width=40%>\n");
                /* 50 - Compartilhar */
                echo("       <font class=colorfield>");
                echo("<b>".RetornaFraseDaLista($lista_frases,50)."</b></font>\n");
                echo("    </td>\n");

                if (!isset($SalvarEmArquivo))
                /* 1 - Apagar */
                   echo("    <td align=center width=20%><a class=colorfield href=exibir_historico_desempenho.php onClick=\"this.search='?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_nota=".$cod_nota."&acao=apagar&alunocod=".$alunocod."&portfolio_grupo=".$portfolio_grupo."&cod_avaliacao=".$cod_avaliacao."';return(TemCertezaApagar());\">".RetornaFraseDaLista($lista_frases_geral,1)."</a></td>\n");

              }
              echo("  </tr>\n");
              echo("  <tr>\n");
              echo("    <td align=center><font class=text>".$linha['nota']."</font></td>\n");
              echo("    <td align=center><font class=text>".UnixTime2Data($linha['data'])."</font></td>\n");
              if ($usr_formador)
              {
                if (!isset($SalvarEmArquivo))
                  $compartilhamento="<a class=text href=# onMouseDown=\"js_cod_nota=".$cod_nota.";AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140);return(false);\">".$compartilhamento."</a>";
                echo("    <td align=center>".$compartilhamento."</td>\n");

                if (!isset($SalvarEmArquivo))
                  echo("    <td align=center>&nbsp;</td>\n");

              }
              echo("  </tr></table><br>\n");
              echo("    <font class=text>\n");
              /* 67 - Comentário*/
              echo("<b>".RetornaFraseDaLista($lista_frases,67).":</b> ".$linha['comentario']."</font>\n");
              echo("  <br>\n");
              $formador=NomeUsuario($sock,$linha['cod_formador']);
              /* 68 - Formador que avaliou*/
              echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,68).":</b> ".$formador."</font><br><br>\n");
            }
          }
          if ($cont==0)
          {
            /* 98 - Este participante ainda não foi avaliado nesta atividade! */
            echo("<font class=text>".RetornaFraseDaLista($lista_frases,98)."</font>");
            echo("<br>");
          }
          echo("</table>\n");
        }
        echo("    <form name=frmAvaliar method=post>\n");

        echo("      <div align=right>\n");

        if (($usr_formador) && (!isset($SalvarEmArquivo)))
        {
          /* 95 - Avaliar Participante */
          if (strcmp($dados['Ferramenta'],'P'))
            echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases,95)."' onClick=AvaliarAluno(".$cod_aluno_ficha.");>\n");
          else
          /* 95 - Avaliar Participante */
            echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases,95)."' onClick=AvaliarAlunoPortfolio(".$cod_aluno_ficha.");>\n");
        }
        /* 13 - Fechar (ger) */
        if (!isset($SalvarEmArquivo))
          echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onClick=self.close()>\n");

        echo("      </div>\n");
        echo("      <br>\n");

        echo("    </form>\n");

        echo("<br>\n");
        echo("<hr>\n");
      }
    }
  }

  if (($usr_formador) && (!isset($SalvarEmArquivo)))
  {
      /* Mudar Compartilhamento */
      echo("<div id=comp class=block visibility=hidden onContextMenu=\"return(false);\">\n");
      echo("<form method=post name=form_comp action=exibir_historico_desempenho.php>\n");
      echo(RetornaSessionIDInput());
      echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
      echo("  <input type=hidden name=cod_nota value=\"\">\n");
      echo("  <input type=hidden name=acao value=mudarcomp>\n");
      echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  //  echo("  <input type=hidden name=cod_forum value=".$cod_forum.">\n");
      if (!strcmp($dados['Tipo'],'G'))
        echo("    <input type=hidden name=grupocod value='".$grupocod."'>\n");
      else
        echo("    <input type=hidden name=alunocod value='".$alunocod."'>\n");
      echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
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
  echo("    <form name=frmHist method=post>\n");

  echo("      <div align=right>\n");

  if (!isset($SalvarEmArquivo))
  {
    /* 50 - Salvar em Arquivo (geral) */
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,50)."' onClick='SalvarHistoricoDesempenho();'>\n");
    echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
   //  echo("      <input type=hidden name=cod_forum value=".$cod_forum.">\n");
    echo("      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    if (!strcmp($dados['Tipo'],'G'))
      echo("    <input type=hidden name=grupocod value='".$grupocod."'>\n");
    else
      echo("    <input type=hidden name=alunocod value='".$alunocod."'>\n");
    echo("  <input type=hidden name=portfolio_grupo value=".$portfolio_grupo.">\n");
  }

  /* 14 - Imprimir (geral) */
  echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,14)."' onClick=ImprimirRelatorio();>\n");

  echo("      </div>\n");
  echo("      <br>\n");

  echo("    </form>\n");
  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
