<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/historico.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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
  ARQUIVO : cursos/aplic/material/historico.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  session_register("cod_ferramenta_m");
  if (isset($cod_ferramenta))
    $cod_ferramenta_m=$cod_ferramenta;
  else
    $cod_ferramenta=$cod_ferramenta_m;

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,$cod_ferramenta);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta);

  echo("<html>\n");
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigat�ria
   */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  switch ($cod_ferramenta) {
    case 3 :
      echo("  <link rel=stylesheet TYPE=text/css href=atividades.css>\n");
      $tabela="Atividade";
      break;
    case 4 :
      echo("  <link rel=stylesheet TYPE=text/css href=apoio.css>\n");
      $tabela="Apoio";
      break;
    case 5 :
      echo("  <link rel=stylesheet TYPE=text/css href=leituras.css>\n");
      $tabela="Leitura";
      break;
    case 7 :
      echo("  <link rel=stylesheet TYPE=text/css href=obrigatoria.css>\n");
      $tabela="Obrigatoria";
      break;
  }

  if (EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<script type=\"text/javascript\" language=\"JavaScript\">\n");
    echo("  function OpenWindowPerfil(funcao)\n");
    echo("  {\n");
    echo("    window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("    return(false);\n");
    echo("  }\n");
    echo("</script>\n");
    echo("<body link=#0000ff vlink=#0000ff onLoad=\"self.focus();\">\n");
    echo("\n");

    /* P�gina Principal */
    /* 1 - 3: Atividades
           4: Material de Apoio
           5: Leituras
           7: Parada Obrigat�ria
     */
    echo("<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>");
    /* 34 - Hist�rico */
    echo("<b class=text> - ".RetornaFraseDaLista($lista_frases,34)."</b><br>\n");
    
    $linha_item=RetornaDadosDoItem($sock, $tabela, $cod_item);

    echo("<img src=\"../figuras/arqp.gif\" border=0><font class=text>".$linha_item['titulo']."</font><br>\n");
    echo("<p>\n");
    echo("<table border=0 width=100% cellspacing=0>\n");
    echo("  <tr>\n");
    /* 36 - A��o */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,36)."</td>\n");
    /* 13 - Data */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,13)."</td>\n");
    /* 37 - Usu�rio */
    echo("    <td class=colorfield align=center>".RetornaFraseDaLista($lista_frases,37)."</td>\n");
    echo("  </tr>\n");

    $res=RetornaResHistoricoDoItem($sock, $tabela, $cod_item);
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
      $nome_usuario="<a href=\"#\" onclick=return(OpenWindowPerfil(".$linha['cod_usuario']."));>".NomeUsuario($sock, $linha['cod_usuario'], $cod_curso)."</a>";
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
            /* 38 - Edi��o Finalizada */
            $acao=RetornaFraseDaLista($lista_frases,38);
          }
          else
          {
            /* 39 - Desconhecida */
            $acao=RetornaFraseDaLista($lista_frases,39);
          }
        }
        else
        {
          /* tem que ser cria��o, ent�o */ 
          $linha=RetornaLinha($res);
          $num_linhas--;
          $data=UnixTime2DataHora($linha['data']);
          if ($linha['acao']=="C")
          {
            /* 41 - Cria��o */
            $acao=RetornaFraseDaLista($lista_frases,41);
          }
          else
          {
            /* 39 - Desconhecida */
            $acao=RetornaFraseDaLista($lista_frases,39);
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
              /* 40 - Edi��o Cancelada */
              $acao=RetornaFraseDaLista($lista_frases,40);
            }
            else
            {
              /* 39 - Desconhecida */
              $acao=RetornaFraseDaLista($lista_frases,39);
            }
          }
        }
        else
        {

          if ($linha['acao']=="C")
          {
            /* 41 - Cria��o */
            $acao=RetornaFraseDaLista($lista_frases,41);
          }
          else
          {
            if ($linha['acao']=="M")
            {
              /* 42 - Movida */
              $acao=RetornaFraseDaLista($lista_frases,42);
            }
            else
            {
              if ($linha['acao']=="A")
              {
                /* 43 - Exclus� */
                $acao=RetornaFraseDaLista($lista_frases,43);
              }
              else
              {
                if ($linha['acao']=="R")
                {
                  /* 44 - Recupera��o */
                  $acao=RetornaFraseDaLista($lista_frases,44);
                }
                else
                {
                  if ($linha['acao']=="X")
                  {
                    /* 75 - Exclu�a definitivamente */
                    $acao=RetornaFraseDaLista($lista_frases,75);
                  }
                  else if ($linha['acao']=="E")
                  {
                    /* 18 - Em Edi��o */
                    $acao=RetornaFraseDaLista($lista_frases,18);
                  }
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
