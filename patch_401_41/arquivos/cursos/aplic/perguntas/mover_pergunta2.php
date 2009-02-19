<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/mural/mover_pergunta2.php

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
  ARQUIVO : cursos/aplic/mural/mover_pergunta2.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, 6);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);

  echo("  <html>\n");
  /* 1 - Perguntas Freqüentes */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");

  if ($origem == "ver_pergunta")
  {
    echo("<script language=JavaScript>\n\n");

    /* $pagprinc especifica qual eh o documento da pagina principal.   */
    /* ATENÇAO: NAO confundir $origem com $pagprinc. $origem refere-se */
    /* ao documento que chamou a açao de mover: perguntas.php,         */
    /* exibir_todas.php ou ver_pergunta.php. Enquanto $pagprinc eh o   */
    /* documento que estah sendo exibido na pagina principal:          */
    /* perguntas.php ou exibir_todas.php.                              */
    echo("  function AtualizaPaginaPrincipal()\n");
    echo("  {\n");
    echo("    top.opener.location.href=\"".$pagprinc.".php?".RetornaSessionID());
    echo("&cod_curso=".$cod_curso."&cod_assunto_pai=".$cod_assunto_pai."&".time()."\";\n");
    echo("    return(true);\n");
    echo("  }\n\n");

    echo("</script>\n\n");
  }


  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Perguntas Freqüentes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 39 - Mover Pergunta */
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 39)."</b>";
  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,1));
  echo("  <br>\n");

  /* Existem tres paginas que chamam 'mover_pergunta2.php': 'perguntas.php',   */
  /* 'exibir_todas.php' e 'ver_pergunta.php'. O nome da pagina esta armazenada */
  /* em $origem.                                                               */

  echo("  <form action=".$origem.".php method=post>\n");

  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  /* RePassa a variavel $pagprinc, a qual armazena o nome do documento  */
  /* que estah sendo exibida na pagina principal.                       */
  echo("    <input type=hidden name=pagprinc value=".$pagprinc.">\n");

  if (MovePergunta($sock, $cod_pergunta, $cod_assunto_dest))
  {
    if ($origem == "ver_pergunta")
    {
      if (isset($listacheck))
      /* Se a variavel contendo todas as perguntas exceto a que foi movida  */
      /* estiver 'setado', entao separa ela em um vetor lcheck */
      /* e cria um array check[] o qual será passado ao ver_pergunta.php.   */
      {
        $lcheck = explode("_",$listacheck);
        $total = count($lcheck);
        for ($i = 0; ($i < $total) && ($lcheck[$i] != ""); $i++)
        /* Enquanto nao chegar ao final e o conteudo do array de indice $i  */
        /* NAO for uma string vazia, crie os input hidden.                  */
        {
          echo("    <input type=hidden name=check[] value=".$lcheck[$i].">\n");
        }
      }
    }

    /* 40 - Pergunta movida com sucesso. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,40)."</font>\n");
    echo("    <p>\n");
    /* 23 - Voltar */
    echo("    <input type=submit class=text value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
  }
  else /* Se a pergunta NAO pode ser movida apresenta uma mensagem de erro */
  {
    /* 41 - Erro ao mover a pergunta. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases, 41)."</font>\n");
    echo("    <p>\n");

    if ($origem == "ver_pergunta")
    {
      /* 23 - Fechar */
      echo("    <input type=button class=text value='".RetornaFraseDaLista($lista_frases_geral, 13)."'");
      echo(" onClick='self.close();'>\n");
    }
    else
      /* 13 - Voltar */
      echo("    <input type=submit class=text value='".RetornaFraseDaLista($lista_frases_geral, 13)."'>\n");
  }
  echo("  </form>\n");

  if ($origem == "ver_pergunta")
  {
    echo("<script language=JavaScript>\n");
    echo("  AtualizaPaginaPrincipal();\n");
    echo("</script>\n\n");
  }

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
