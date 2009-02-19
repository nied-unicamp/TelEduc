<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/portfolio/ver_itens_aluno.php

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
  ARQUIVO : cursos/aplic/portfolio/ver_itens_aluno.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("portfolio.inc");
  include("avaliacoes_portfolio.inc");

  /* Necess�rio para a lixeira. */
  session_register("cod_topico_s");
  unset($cod_topico_s);

  $sock=Conectar("");
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta = 15;
  include("../topo_tela.php");

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $eformador = $tela_formador; /* chamado no topo_tela */
  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  

  if (isset($cod_grupo_portfolio))
    $cod_usuario_portfolio=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo_portfolio);
  else
  	$cod_grupo_portfolio = NULL; 

  $cod_topico_raiz_usuario=RetornaPastaRaizUsuario($sock,$cod_usuario,"");

  if (!isset($cod_topico_raiz))
  {
    if ($cod_grupo_portfolio!="" &&  $cod_grupo_portfolio!="NULL")
      $cod_topico_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo_portfolio);
    else if ($cod_usuario_portfolio!="")
      $cod_topico_raiz=RetornaPastaRaizUsuario($sock,$cod_usuario_portfolio,"");
    else
    {
      $cod_topico_raiz=$cod_topico_raiz_usuario;
      $cod_usuario_portfolio=$cod_usuario;

      /* Checagem da exist�ncia das pastas dos grupos a que o usu�rio pertence */
      VerificaPortfolioGrupos($sock,$cod_usuario);
    }
  }

  $status_portfolio = RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

  $dono_portfolio    = $status_portfolio ['dono_portfolio'];
  $portfolio_apagado = $status_portfolio ['portfolio_apagado'];
  $portfolio_grupo   = $status_portfolio ['portfolio_grupo'];


  /* Tem que arrumar daqui para a frente */

  /* Fun��es JavaScript */
  echo("<script language=JavaScript>\n");

  echo("function AbreJanelaComponentes(cod_grupo)\n");
  echo("{\n");
  echo("  window.open('../grupos/exibir_grupo.php?&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=500,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  echo("  return false;\n");
  echo("}\n");

  echo("function AbrePerfil(cod_usuario)\n");
  echo("{\n");
  echo("  window.open('../perfil/exibir_perfis.php?&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("  return(false);\n");
  echo("}\n");
  
  echo("  function VerItem(cod_item) \n");
  echo("  { \n");
  echo("    window_handle = window.open('../portfolio/ver_item_avaliacao.php?&cod_curso=".$cod_curso."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_avaliacao=".$cod_avaliacao."&cod_item='+ cod_item, 'JanelaPortfolio', 'width=600,height=400,top=150,left=150,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'); \n");
  echo("    window_handle.focus(); \n");
  echo("    return false; \n");
  echo("  } \n");

   /* Fun��o JvaScript para chamar p�gina para salvar em arquivo. */
    echo("      function SalvarItensAluno()\n");
    echo("      {\n");
    echo("        document.frmItens.action = \"salvar_ver_itens_aluno.php?".RetornaSessionID());
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

    if ($eformador)
    {
      echo("  function AvaliarAluno(funcao)\n");
      echo("  {\n");
      echo("    window.open(\"../avaliacoes/avaliar_atividade.php?&cod_curso=".$cod_curso."&portfolio_grupo=".$portfolio_grupo."&cod_avaliacao=".$cod_avaliacao."&cod_usuario_portfolio=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
      echo("    return(false);\n");
      echo("  }\n");
    }


  echo("</script>\n");

  echo("<body>\n");
  echo("<br />");
  echo("<br />");
  // Passar pro banco de dados de frases
  echo("<h4>Avaliacoes - Participacoes</h4>");

  /* P�gina Principal */

  // 1 - Portf�lio
  $cabecalho = "<b class=titulo>".RetornaFraseDaLista ($lista_frases, 1)."</b>";

  if ($ferramenta_grupos_s && 'G' == $acao_portfolio_s)
  {
    // 3 - Portfolios de grupos
    $cod_frase = 3;
    $cod_pagina=29;
  }
  else
  {
    // 2 - Portfolios individual
    $cod_frase = 2;
    $cod_pagina=27;
  }
  // 3 - Portfolio de Grupo
  // ?? - Itens de grupo
  // 2 - Portf�lio individual
  // ?? - Itens de Participante
  if ($portfolio_grupo)
    $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 3)." / "."Itens de grupo"."</b>";
  else
    $cabecalho .= "<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 2)." / "."Itens de participante"."</b>";


/* $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $cod_topico_raiz);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha)
  {
    if ($cod_topico_raiz!=$linha['cod_topico'])
    {
      $path="<a class=text href=\"portfolio.php?&cod_curso=".$cod_curso."&cod_topico_raiz=".$linha['cod_topico']."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a> &gt;&gt; ".$path;
    }
    else
    {
      $path="<b class=text>".$linha['topico']."</b><br>\n";
    }
  }
 */

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

  }

  echo("<table cellspacing=\"0\" cellpadding=\"0\" class=\"tabExterna\">"); 
  echo("    <form name=frmItens method=post><tr>\n");
  echo("<td valign=\"top\" colspan=\"3\">");
  echo("<ul class=\"btAuxTabs\">");
  
    /* 14 - Imprimir (geral) */
    echo("  <li><span onClick=ImprimirRelatorio();>".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");

  /* 13 - Fechar (ger) */
  if (!isset($SalvarEmArquivo))
  echo("  <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  
  echo("</td></tr>");
echo("    </form>\n");  
  
  echo("	<tr><td>");
  echo("<table cellspacing=\"0\" cellpadding=\"0\" class=\"tabInterna\">");
  echo("  <tr class=\"head\">\n");
  /* 82 - Itens */
  echo("    <td class=colorfield width=34%>".RetornaFraseDaLista($lista_frases,82)."</td>\n");
  /* 9 - Data */
  echo("    <td class=colorfield align=center width=15%>".RetornaFraseDaLista($lista_frases,9)."</td>\n");
  /* 119 - Compartilharmento */
  echo("    <td class=colorfield align=center width=24%>".RetornaFraseDaLista($lista_frases,119)."</td>\n");
  /* 112 - Coment�rios */
  echo("    <td class=colorfield align=center width=12%>".RetornaFraseDaLista($lista_frases,112)."</td>\n");

  echo("  </tr>\n");


  $lista_itens=RetornaItensDeAvaliacao($sock, $cod_curso, $cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio,$cod_avaliacao);

  if (count($lista_itens)<1)
  {
    echo("  <tr>\n");
    /* 11 - N�o h� nenhuma avalia��o neste portf�lio */
    echo("    <td colspan=5><font class=text>N�o h� nenhuma avalia��o neste portf�lio</font></td><br>\n");
    echo("  </tr>\n");
  }
  else
  {
    // definindo qual figura para representar pastas ou arquivos (itens)
    $pasta   = "pasta_";
    $arquivo = "arquivo_";

    // aqui, escolho entre a figura para grupo ou individual
    $gi      = ($portfolio_grupo ? "g_" : "i_");
    $pasta  .= $gi;
    $arquivo.= $gi;

    // aqui, escolho entre pessoal, nao-pessoal ou apagado
    $pnx = ($dono_portfolio  ? "p.gif" : ( $portfolio_apagado ? "x.gif" : "n.gif") );
    $pasta  .= $pnx;
    $arquivo.= $pnx;

    if (count($lista_itens)>0)
    {
      foreach ($lista_itens as $cod => $linha_item)
      {
        if (((!$portfolio_grupo) && ($linha_item['cod_grupo']=="")) || ($portfolio_grupo))
        {
        $data="<font class=text>".UnixTime2Data($linha_item['data'])."</font>";
        if ($linha_item['tipo_compartilhamento']=="T")
        {
          /* 12 - Totalmente Compartilhado */
          $compartilhamento=RetornaFraseDaLista($lista_frases,12);
        }
        else if ($linha_item['tipo_compartilhamento']=="F")
        {
          /* 13 - Compartilhado com Formadores */
          $compartilhamento=RetornaFraseDaLista($lista_frases,13);
        }
        else
        {
          if (!$portfolio_grupo)
            /* 15 - N�o compartilhado */
            $compartilhamento="<font class=text>".RetornaFraseDaLista($lista_frases,15)."</font>";
          else
            /* 14 - Compartilhado com o Grupo */
            $compartilhamento="<font class=text>".RetornaFraseDaLista($lista_frases,14)."</font>";
        }
        if ($data_acesso<$linha_item['data'])
        {
          $marcaib="<b>";
          $marcafb="</b>";
          $marcatr="";
        }
        else
        {
          $marcaib="";
          $marcafb="";
          $marcatr="";
        }

        if ($linha_item['status']=="E")
        {
          $linha_historico=RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);
          if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_historico['cod_usuario'])
          {
            CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp);

            $compartilhamento=$marcaib.$compartilhamento.$marcafb;
            if (!isset($SalvarEmArquivo))
            {
              $titulo=$marcaib."<a href=# onClick='VerItem(".$linha_item['cod_item'].");return false;'><img src=./figuras/".$arquivo." border=0>".$linha_item['titulo']."</a>".$marcafb;
            }
            else
             $titulo=$marcaib."<img src=./figuras/".$arquivo." border=0>".$linha_item['titulo']."".$marcafb;
        
          }
          else
          {
            /* 54 - Em Edi��o */
            $data="<a href=# class=text onClick=\"window.open('em_edicao.php?&cod_curso=".$cod_curso."&cod_item=".$linha_item['cod_item']."&origem=portfolio&cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".RetornaFraseDaLista($lista_frases_geral,54)."</a>";
            $compartilhamento="<font class=textgray>".$compartilhamento."</font>";
            $titulo="<font class=textgray>".$linha_item['titulo']."</font>";
            $icone="<img src=./figuras/".$arquivo." border=0>";
          }
        }
        else
        {
          if ($linha_item['status']!="C")
          {

              $compartilhamento=$marcaib.$compartilhamento.$marcafb;
              if (!isset($SalvarEmArquivo))
              {
                $titulo=$marcaib."<a href=# onClick='VerItem(".$linha_item['cod_item'].");return false;'><img src=./figuras/".$arquivo." border=0>".$linha_item['titulo']."</a>".$marcafb;
              }
              else
                $titulo=$marcaib."<img src=./figuras/".$arquivo." border=0>".$linha_item['titulo']."".$marcafb;

          }
        }

          echo("  <tr>\n");
          echo("    <td><font class=text>".$titulo."</font></td>\n");
          echo("    <td align=center>".$marcaib.$data.$marcafb."</td>\n");
          echo("    <td align=center><font class=text>".$compartilhamento."</font></td>\n");

          echo("    <td align=center>");
          if ($linha_item['num_comentarios_alunos']>0)
            echo("<img src=../figuras/checked-a.gif border=0>");
          if ($linha_item['num_comentarios_formadores']>0)
            echo("<img src=../figuras/checked-f.gif border=0>");
          if ($linha_item['num_comentarios_usuario']>0)
            echo("<img src=../figuras/checked-d.gif border=0>");
          if ($linha_item['data_comentarios']>$data_acesso)
            echo("<img src=../figuras/asterisco.gif border=0>\n");
          echo("&nbsp;");
          echo("</td>\n");
          echo("  </tr>\n");
      }
      }
    }
  }
  echo("	</table>\n");
  echo("	</td></tr>");
  echo(" 	<tr><td>");
  /* 113 - Coment�rio de Aluno */
  /* 114 - Coment�rio de Formador */
  /* 115 - Coment�rio postados por mim */
  echo("<font class=text><img src=../figuras/checked-a.gif> ".RetornaFraseDaLista($lista_frases,113)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
  echo("<img src=../figuras/checked-f.gif> ".RetornaFraseDaLista($lista_frases,114)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
  if (!EVisitante($sock,$cod_curso,$cod_usuario))
    echo("<img src=../figuras/checked-d.gif> ".RetornaFraseDaLista($lista_frases,115)."\n");

  echo("<br>\n");
  echo("	</td></tr>");
  echo("</table>\n");


  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);
  exit;

?>
