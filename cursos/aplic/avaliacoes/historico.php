<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/historico.php

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
  ARQUIVO : cursos/aplic/avaliacoes/historico.php
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

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  $tabela="Avaliacao_historicos";
  echo("<html>\n");
  /* 1 - Avaliações  */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n"); 
  echo("\n");

  if (! EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    // 1 - Avaliações
    $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
    // 94 - Usuário sem acesso
    $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 94)."</b>";
    echo(PreparaCabecalho($cod_curso, $cabecalho, COD_AVALIACAO, 1));
    echo("    <br>\n");
    echo("    <p>\n");

    echo("  </body>\n");
    echo("  </html>\n");
    exit();
  }
  else
  {
    echo("<script language=javascript>\n");
    echo("  function OpenWindowPerfil(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");
    echo("</script>\n");
    echo("<body link=#0000ff vlink=#0000ff onLoad=\"self.focus();\">\n");
    echo("\n");

    /* Página Principal */
    /* 1 - Avaliações  */
    echo("<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>");
    /* 99 - Histórico */
    echo("<b class=text> - ".RetornaFraseDaLista($lista_frases,99)."</b><br>\n");

    $tmp = RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
    $cod_atividade = $tmp[Cod_atividade];
    $titulo=RetornaTituloAvaliacao($sock,$tipo_ferramenta,$cod_atividade);
    $dados = RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

    if (!strcmp($dados['Ferramenta'],'F'))
    /* 145 - Fórum de Discussão*/
      $ferramenta=RetornaFraseDaLista($lista_frases,145);
    elseif (!strcmp($dados['Ferramenta'],'B'))
    /* 146 - Sessão de Bate-Papo*/
      $ferramenta=RetornaFraseDaLista($lista_frases,146);
    elseif (!strcmp($dados['Ferramenta'],'E'))
    /* 174 - Exercício em grupo*/
      $ferramenta=RetornaFraseDaLista($lista_frases,174);
    else
    /* 14 - Atividade no Portfólio*/
      $ferramenta=RetornaFraseDaLista($lista_frases,14);


    echo("<img src=\"../figuras/avaliacao.gif\" border=0><font class=text>".$titulo."<font class=text> (".$ferramenta.")</font></font><br>\n");
    echo("<p>\n");
    echo("<table border=0 width=100% cellspacing=0>\n");
    echo("  <tr>\n");
    /* 100 - Ação */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,100)."</td>\n");
    /* 101 - Data */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,101)."</td>\n");
    /* 102 - Usuário */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,102)."</td>\n");
    echo("  </tr>\n");

    $res=RetornaResHistoricoDaAvaliacao($sock, $tabela, $cod_avaliacao);
    $num_linhas=RetornaNumLinhas($res);
    $class="g2field";

    while ($num_linhas>0)
    {
      if ($class=="g1field")
        $class="g2field";
      else
        $class="g1field";

      $linha=RetornaLinha($res);
      $num_linhas--;
      $nome_usuario="<a href=# onclick=return(OpenWindowPerfil(".$linha['cod_usuario']."));>".NomeUsuario($sock, $linha['cod_usuario'])."</a>";
      $data=UnixTime2DataHora($linha['data']);

      if ($linha['acao']=="F")
      {
        if ($num_linhas>1)
        {
          $linha=RetornaLinha($res);
          $num_linhas--;
          $data=UnixTime2DataHora($linha['data']);
          if ($linha['acao']=="E")
          {
          /* 147 - Edição Finalizada*/
            $acao=RetornaFraseDaLista($lista_frases,147);
          }
          else
          {
          /* 148 - Desconhecida*/
            $acao=RetornaFraseDaLista($lista_frases,148);
          }
        }
        else
        {
          /* tem que ser criação, então */
          $linha=RetornaLinha($res);
          $num_linhas--;
          $data=UnixTime2DataHora($linha['data']);
          if ($linha['acao']=="C")
          {
          /* 149 - Criação*/
            $acao=RetornaFraseDaLista($lista_frases,149);
          }
          else
          {
          /* 148 - Desconhecida*/
            $acao=RetornaFraseDaLista($lista_frases,148);
          }
        }
      }
      else
      {
        if ($linha['acao']=="D")
        {
          if ($num_linhas>1)
          {
            $linha=RetornaLinha($res);
            $num_linhas--;
            $data=UnixTime2DataHora($linha['data']);
            if ($linha['acao']=="E")
            {
            /* 150 - Edição Cancelada*/
              $acao=RetornaFraseDaLista($lista_frases,150);
            }
            else
            {
             /* 148 - Desconhecida*/
              $acao=RetornaFraseDaLista($lista_frases,148);
            }
          }
        }
        else
        {

          if ($linha['acao']=="C")
          {
          /* 149 - Criação*/
            $acao=RetornaFraseDaLista($lista_frases,149);
          }
          else
          {
            if ($linha['acao']=="A")
            {
            /* 151 - Exclusão*/
              $acao=RetornaFraseDaLista($lista_frases,151);
            }
            else
            {
              if ($linha['acao']=="R")
              {
              /* 152 - Recuperação*/
                $acao=RetornaFraseDaLista($lista_frases,152);
              }
              else
              {
                if ($linha['acao']=="X")
                {
                /* 153 - Excluída definitivamente*/
                  $acao=RetornaFraseDaLista($lista_frases,153);
                }
                else if ($linha['acao']=="E")
                {
                 /* 154 - Em Edição*/
                  $acao=RetornaFraseDaLista($lista_frases,154);
                }
              }
            }
          }

        }
      }

      echo("  <tr class=".$class.">\n");
      echo("    <td align=center><font class=text>".$acao."</font></td>\n");
      echo("    <td align=center><font class=text>".$data."</font></td>\n");
      echo("    <td align=center><font class=text>".$nome_usuario."</font></td>\n");
      echo("  </tr>\n");

    }

    echo("</table>\n");

    // 13 - Fechar
    echo("<form><input type=button onClick=self.close(); value=".RetornaFraseDaLista($lista_frases_geral,13)."></form>\n");

    echo("</body>\n");
    echo("</html>\n");

  }

?>
