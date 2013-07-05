<?php
/*
<!--
--------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/criar_avaliacao_exercicio.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
    Copyright (C) 2004  NIED - Unicamp

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
  ARQUIVO : cursos/aplic/avaliacoes/criar_avaliacao_exercicio.php
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
   $tabela="Avaliacao";

    echo("<html>\n");
    /* 1 - Avalia��es*/
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\">\n");

    echo("  <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\">\n");

    GeraJSVerificacaoData();
  /****************** Fun��es JavaScript **************** */

  echo("  <script language=\"javascript\">\n");

  echo("  function Atualiza() {\n");
  echo("    document.atualizar.submit();\n");
  echo("  }\n");

  echo("  </script>\n");

  /* Verifica se a pessoa a editar � formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Avalia��es*/
    echo("<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>\n");
    /* 8 - �rea restrita ao formador. */
    echo("<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,8)."</b><br>\n");
    /* 23 - Voltar (gen) */
    echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\"></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }
  else
  {
    echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus(); >\n");
    /* 1 - Avalia��es */
    $cabecalho ="<b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";
    /* 9 - Cadastro de Avalia��o */
    $cabecalho.="<b class=\"subtitulo\"> - ".RetornaFraseDaLista($lista_frases,9)." </b>";

    $cod_pagina=5;
    /* Cabecalho */
    echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

    echo("<br>\n");
    echo("<p>\n");

    $dados=RetornaAvaliacao($sock,$cod_atividade,'E');
    if ($dados['Cont']>0)
    {

      $linha_hist=RetornaUltimaPosicaoHistoricoAvaliacao($sock, 'Avaliacao_historicos', $dados['Cod_avaliacao']);
      if ($dados['Status']=='C')
      {
        /* algu�m j� est� editando */
        /* Ve se n�o � voc� */
        if ($cod_usuario!=$dados['Cod_usuario'])
        {
          if ($linha_hist['data']>time()-1800)
          {
            /* 78 - A Avalia��o j� est� sendo criada desde */
            echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,78));

            /* 57 - Por */
            echo(Unixtime2DataHora($dados['Data']));

            echo(" ".RetornaFraseDaLista($lista_frases_geral,57)." ".NomeUsuario($sock,$dados['Cod_usuario']).".<br><br>");

            /* 23 - Voltar (gen) */
            echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"location='../exercicios/index_modelo.php?".RetornaSessionID()."&cod_curso=".$cod_curso."';\"></form>\n");

            echo("</body></html>\n");
            Desconectar($sock);
            exit;
          }
          /* Passou o tempo limite, captura a edi��o */
        }
        /* �. Atualiza data e segue em frente. */
        CancelaEdicaoAvaliacao($sock, $tabela, $dados['Cod_avaliacao'], $cod_usuario);
        $cod_avaliacao=IniciaCriacaoAvaliacaoExercicio($sock, $tabela,$cod_atividade, $cod_usuario, 'E');
      }
      elseif (($dados['Status']=='F') || ($dados['Status']=='E'))
      {
        /* 70 - J� existe uma avalia��o criada para esta atividade.*/
        echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,70)."</font><br><br>");

        /* 23 - Voltar (gen) */
        echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"location='../exercicios/index_modelo.php?".RetornaSessionID()."&cod_curso=".$cod_curso."';\"></form>\n");
        echo("</body></html>\n");
        Desconectar($sock);
        exit;
      }
      elseif ($dados['Status']=='A')
      {
        /* 71 - J� existe uma avalia��o criada para esta atividade. Por�m, ela foi apagada.*/
        /* 72 - Se desejar criar outra avalia��o, voc� precisa primeiro excluir definitivamente a avalia��o existente.*/
        echo("<font class=\"text\">".RetornaFraseDaLista($lista_frases,71)."");
        echo(" ".RetornaFraseDaLista($lista_frases,72)."</font><br><br>");

        /* 23 - Voltar (gen) */
        echo("<form><input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"location='../exercicios/index_modelo.php?".RetornaSessionID()."&cod_curso=".$cod_curso."';\"></form>\n");
        echo("</body></html>\n");
        Desconectar($sock);
        exit;
      }
    }
    else
    {
      $cod_avaliacao=IniciaCriacaoAvaliacaoExercicio($sock, "Avaliacao",$cod_atividade, $cod_usuario, 'E');
    }

    $exercicio=RetornaExercicio($sock, $cod_atividade);

    /* 173 - Exerc�cio */
    echo("    <font class=\"text\">".RetornaFraseDaLista($lista_frases,173)."</font>\n");
    echo("    <font class=\"text\"><i>".$exercicio."</i></font>");

    echo("<form name=\"avaliacao\" action=\"criar_avaliacao_exercicio2.php?".RetornaSessionID()."\" method=\"post\">\n");
    /* 79 - Forne�a abaixo os dados que ser�o considerados na avalia��o desta atividade. */
    echo("  <font class=\"text\">".RetornaFraseDaLista($lista_frases,79)."<br>\n");
    echo("    <br>\n");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=\"colorfield\">\n");
    /* 16 - Data de in�cio*/
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,16)."</td>\n");

    /* 17 - Data de T�rmino */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,17)."</td>\n");

    echo("  </tr>\n");
    $data=RetornaDataExercicio($sock, $cod_atividade);
    echo("  <tr class=\"wtfields\">\n");
    echo("<input type=\"hidden\" name=\"data_inicio\"  value=\"".$data['dt_disponibilizacao']."\">");
    echo("<input type=\"hidden\" name=\"data_termino\" value=\"".$data['dt_limite_submissao']."\">");
    echo("    <td class=\"text\">".UnixTime2Data($data['dt_disponibilizacao'])."</td>\n");

    echo("    <td class=\"text\">".UnixTime2Data($data['dt_limite_submissao'])."</td>\n");

    echo("  </tr>\n");

    echo("  <tr class=\"wtfields\">\n");
    /* 18 - dd/mm/aaaa */
    echo("    <td class=\"textsmall\">(".RetornaFraseDaLista($lista_frases,18).")</td>\n");

    /* 18 - dd/mm/aaaa */
    echo("    <td class=\"textsmall\">(".RetornaFraseDaLista($lista_frases,18).")</td>\n");

    echo("  </tr>\n");
    echo("</table>\n");

    echo("    <br>\n");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=\"colorfield\">\n");
    /* 19 - Valor*/
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,19)."</td>\n");
    /* 20 - Tipo da Atividade */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,20)."</td>\n");
    echo("  </tr>\n");
    echo("      <tr class=\"wtfields\">\n");
    echo("        <td class=\"textsmall\">\n");
    $valor=RetornaValorModelo($sock,$cod_atividade);
    echo("<input type=\"hidden\" name=\"valor\" value=".$valor.">");
    echo($valor);  
    echo("        </td>\n");
    echo("        <td class=\"textsmall\">\n");
    $tipo=RetornaTipoModelo($sock,$cod_atividade);
    echo("<input type=\"hidden\" name=\"tipo\" value=\"".$tipo."\">");
    echo($tipo);
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      </table>\n");
    echo("    <br>\n");

        echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=\"colorfield\">\n");
    /* 75 - Objetivos */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,75)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <textarea name=\"objetivos\" rows=4 cols=60 wrap=soft>".stripslashes($objetivos)."</textarea>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");
    echo("    <br>\n");

        echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr class=\"colorfield\">\n");
    /* 23 - Crit�rios */
    echo("    <td class=\"colorfield\">".RetornaFraseDaLista($lista_frases,23)."</td>\n");
    echo("  </tr>\n");
    echo("  <tr>\n");
    echo("    <td>\n");
    echo("      <textarea name=\"criterios\" rows=4 cols=60 wrap=soft>".stripslashes($criterios)."</textarea>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table>\n");

     echo("<input type=\"hidden\" name=\"cod_curso\"      value=".$cod_curso.">\n");
     echo("<input type=\"hidden\" name=\"cod_avaliacao\"  value=".$cod_avaliacao.">\n");
     echo("<input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
     echo("<input type=\"hidden\" name=\"cod_atividade\"  value=".$cod_atividade.">\n");

     echo("<table width=100%><tr><td align=right>");
     /* 11 - Enviar */
     echo("      <input class=\"text\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral, 11)."\">\n");

     /* 2 - Cancelar (ger) */
     echo("<input class=\"text\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" onclick=\"top.trabalho.location='index_avaliacao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_modelo=".$cod_atividade."&cancelar_edicao_avaliacao=sim&origem=../exercicios/exercicios';\">");


     echo("</td></tr></table>\n");
     echo("</form>\n");
     }

  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
