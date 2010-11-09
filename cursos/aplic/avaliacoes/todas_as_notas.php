<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/todas_as_notas.php

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
  ARQUIVO : cursos/aplic/avaliacoes/todas_as_notas.php
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

  /* Funções JavaScript */
  echo("<script language=JavaScript>\n");

  echo("  function HistoricodoDesempenho(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDeTodasNotas=1&cod_aluno_avaliacao=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function HistoricodoDesempenhoPortfolio(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeiodePortfolio=0&VeioDeTodasNotas=1&cod_aluno_avaliacao=\"+funcao,\"HistoricoDesempenho\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
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
  /* 51 - Infelizmente não foi possível imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("      alert('".RetornaFraseDaLista($lista_frases,51)."');\n");
  echo("    }\n");
  echo("  }\n");

   /* Função JvaScript para chamar página para salvar em arquivo. */
  echo("      function SalvarTodasNotas()\n");
  echo("      {\n");
  echo("        document.frmMsg.action = \"salvar_todas_as_notas.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("        document.frmMsg.submit();\n");
  echo("      }\n\n");


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

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad=self.focus();>\n");

  $cabecalho ="<b class=titulo>Avaliações</b>";
    /* 31 - Notas dos participantes */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,31)."</b>";

   $cod_pagina=17;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  echo("<br>");

  if (!$SalvarEmArquivo)
  {
    echo("<table border=0 width=100%>\n");
    echo("  <tr class=menu>\n");
    /* 110 - Ver Avaliações Atuais */
    echo("    <td align=center><a href=\"avaliacoes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=menu><b>".RetornaFraseDaLista($lista_frases,110)."</b></a></td>\n");
    /* 29 - Ver Avaliações passadas */
    echo("    <td align=center><a href=\"ver_avaliacoes_anteriores.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=menu><b>".RetornaFraseDaLista($lista_frases,29)."</b></a></td>\n");
    /* 30 - Ver Avaliações Futuras */
    echo("    <td align=center><a href=\"ver_avaliacoes_futuras.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\" class=menu><b>".RetornaFraseDaLista($lista_frases,30)."</b></a></td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("<br>\n");
  }

  if (!$SalvarEmArquivo)
  {
    echo("<p>\n");
    /* 111 - Para visualizar os objetivos/critérios de uma avaliação, clique sobre o valor da mesma.*/
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,111)."</font><br>\n");
    /* 112 - Para visualizar o histórico de desempenho de um participante em uma atividade, clique sobre a  nota dele.*/
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,112)."</font><br>\n");
    echo("<br>\n");
  }

  $lista_avaliacoes=RetornaAvaliacoes($sock,$usr_formador);

  $reg=count($lista_avaliacoes);
  $width=($reg*50)+150;
  if (count($lista_avaliacoes)>0)
  {
    //Tabela com a lista de alunos do curso, com suas respectivas notas na avaliação realizada
    echo("<table border=0 width=".$width.">\n");
    echo("  <tr class=menu>\n");
    /* 113 - Tipo da Avaliação */
    echo("    <td width=150 class=colorfield align=left>".RetornaFraseDaLista($lista_frases,113)."</td>\n");

    $cont_batepapo=0;
    $cont_forum=0;
    $cont_portfolio=0;

    foreach ($lista_avaliacoes as $cod => $linha)
    {
      if (!strcmp($linha['Ferramenta'],'F'))
        $cont_forum++;
      elseif (!strcmp($linha['Ferramenta'],'B'))
        $cont_batepapo++;
      elseif (!strcmp($linha['Ferramenta'],'P'))
        $cont_portfolio++;

      echo("    <td class=colorfield width=50 align=center>\n");
      if (!strcmp($linha['Ferramenta'],'F'))
        echo($linha['Ferramenta']."".$cont_forum."\n");
      elseif (!strcmp($linha['Ferramenta'],'B'))
        echo($linha['Ferramenta']."".$cont_batepapo."\n");
      elseif (!strcmp($linha['Ferramenta'],'P'))
        echo($linha['Ferramenta']."".$cont_portfolio."\n");

      echo("</td>\n");
    }
    echo ("</tr>\n");

    echo("  <tr class=menu>\n");
    /* 114 - Valor da Avaliação */
    echo("    <td width=150 class=b1field align=left>".RetornaFraseDaLista($lista_frases,114)."</td>\n");

    foreach ($lista_avaliacoes as $cod => $linha)
    {
      echo("    <td class=b1field width=50 align=center>\n");
      if (!$SalvarEmArquivo)
        echo("        <a class=text href=# onClick=\"window.open('ver.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&EhAtalho=1&cod_avaliacao=".$linha['Cod_avaliacao']."','VerAvaliacao','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false);\">".$linha['Valor']."</a>\n");
      else
        echo($linha['Valor']."\n");
      echo("</td>\n");
    }
    echo ("</tr>\n");


    $lista_users=RetornaListaUsuariosAluno($sock);

    if (count($lista_users)>0)
    {
    /* 64 - Alunos*/
      echo("    <td width=150 class=colorfield align=left>".RetornaFraseDaLista($lista_frases,64)."&nbsp;</td>\n");
    /* 155 - Notas*/
      echo("    <td colspan=".$reg." class=colorfield align=left>".RetornaFraseDaLista($lista_frases,155)."&nbsp;</td>\n");
      echo("<tr>\n");

      foreach($lista_users as $cod => $nome)
      {
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


        foreach ($lista_avaliacoes as $cont => $linha)
        {
          $foiavaliado=FoiAvaliado($sock,$linha['Cod_avaliacao'],$cod);
          if ($foiavaliado)             //Ja existe uma nota atribuida
          {
            $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=$dados_nota['nota'];

            if ($usr_formador)
            {
              $marcaib="";
              $marcafb="";
              echo("      <td class=".$field." align=center>");
              if (!$SalvarEmArquivo)
              {
                if (strcmp($linha['Ferramenta'],'P'))
                  echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod.".".$linha['Cod_avaliacao']."));>");
                else
                  echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod.".".$linha['Cod_avaliacao']."));>");
                echo($nota."</a></td>\n");
              }
              else
                echo($nota."</td>\n");
            }
            else           //é aluno
            {
              echo("      <td class=".$field." align=center>");
              if (!strcmp($tipo_compartilhamento,'T'))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($linha['Ferramenta'],'P'))
                    echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod.".".$linha['Cod_avaliacao']."));>");
                  else
                    echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod.".".$linha['Cod_avaliacao']."));>");
                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              elseif ((!strcmp($tipo_compartilhamento,'A')) && ($cod_usuario==$cod))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($linha['Ferramenta'],'P'))
                    echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod.".".$linha['Cod_avaliacao']."));>");
                  else
                    echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod.".".$linha['Cod_avaliacao']."));>");
                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              else //Está compartilhada só com formadores
                echo("&nbsp;</td>\n");
            }
          }
          else // nenhuma nota foi atribuida
            echo("      <td class=".$field." align=center>&nbsp;</td>\n");

    //    $j = ($j + 1) % 2;
        }
        echo("    </tr>\n");
      }
    }

    if ($usr_formador)
    {
    /* 156 - Formador */
      echo("    <td colspan=".($reg+1)." class=colorfield align=left>".RetornaFraseDaLista($lista_frases,156)."</td>\n");
      $lista_users=RetornaListaUsuariosFormador($sock);

      foreach($lista_users as $cod => $nome)
      {
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

        foreach ($lista_avaliacoes as $cont => $linha)
        {
         /* if ($j==0)
            $field="g1field";
          else
            $field="g2field";
           */
          $foiavaliado=FoiAvaliado($sock,$linha['Cod_avaliacao'],$cod);
          if ($foiavaliado)             //Ja existe uma nota atribuida
          {
            $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=$dados_nota['nota'];

            $marcaib="";
            $marcafb="";
            echo("      <td class=".$field." align=center>");
            if (!$SalvarEmArquivo)
            {
              if (strcmp($linha['Ferramenta'],'P'))
                echo("      <a href=# onClick=return(HistoricodoDesempenho(".$cod.".".$linha['Cod_avaliacao']."));>");
              else
                echo("      <a href=# onClick=return(HistoricodoDesempenhoPortfolio(".$cod.".".$linha['Cod_avaliacao']."));>");
              echo($nota."</a></td>\n");
            }
            else
              echo($nota."</td>\n");
          }
          else // nenhuma nota foi atribuida
            echo("      <td class=".$field." align=center>&nbsp;</td>\n");

         // $j = ($j + 1) % 2;
        }
        echo("    </tr>\n");
      }
    }
    echo("</table><br><br>\n");
  }
  else
  {
    /* 115 - Nenhuma avaliação foi criada! */
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,115)."</font>\n");
  }

  echo("    <form name=frmMsg method=post>\n");

  echo("      <div align=right>\n");
  if (!$SalvarEmArquivo)
  {
    /* 50 - Salvar em Arquivo (geral) */
    echo("  <input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,50)."' onClick='SalvarTodasNotas();'>\n");
    echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  }

  /* 14 - Imprimir */
  echo("<input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,14)."' onClick=ImprimirRelatorio();>\n");

  echo("      </div>\n");

  echo("    </form>\n");

  $cont_batepapo=0;
  $cont_forum=0;
  $cont_portfolio=0;

  /* 116 - Legenda */
  echo("<font class=text><b>".RetornaFraseDaLista($lista_frases,116).":</b></font><br>\n");

  foreach ($lista_avaliacoes as $cod => $linha)
  {
    if (!strcmp($linha['Ferramenta'],'F'))
    {
      $cont_forum++;
      /* 145 -Fórum de Discussão*/
      $ferramenta=RetornaFraseDaLista($lista_frases,145);
    }
    elseif (!strcmp($linha['Ferramenta'],'B'))
    {
      $cont_batepapo++;
      /* 146 - Sessão de Bate-Papo*/
      $ferramenta=RetornaFraseDaLista($lista_frases,146);
    }
    elseif (!strcmp($linha['Ferramenta'],'P'))
    {
      $cont_portfolio++;
      /* 14 - Atividade no Portfólio*/
      $ferramenta=RetornaFraseDaLista($lista_frases,14);
    }

    echo("<font class=text><b>");

    if (!strcmp($linha['Ferramenta'],'F'))
      echo($linha['Ferramenta']."".$cont_forum);
    elseif (!strcmp($linha['Ferramenta'],'B'))
      echo($linha['Ferramenta']."".$cont_batepapo);
    elseif (!strcmp($linha['Ferramenta'],'P'))
      echo($linha['Ferramenta']."".$cont_portfolio);

    echo("</b>");

    echo(" - ".$linha['Titulo']."<i>&nbsp;-&nbsp;(".$ferramenta.")</i></font><br>\n");
  }

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
