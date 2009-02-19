<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/mural/editar_pergunta2.php

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
  ARQUIVO : cursos/aplic/mural/editar_pergunta2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,6);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);

  echo("  <html>\n");
  /* 1 - Perguntas Freq�entes */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");

  if ($origem == "ver_pergunta")
  {
    echo("<script language=JavaScript>\n\n");

    /* $pagprinc especifica qual eh o documento da pagina principal.   */
    /* ATEN�AO: NAO confundir $origem com $pagprinc. $origem refere-se */
    /* ao documento que chamou a a�ao de edi�ao: perguntas.php,        */
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
  /* 1 - Perguntas Freq�entes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 22 - Editar Pergunta */
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,22)."</b>";
//  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,6));
  echo("  <br>\n");


  /* Se a pergunta for editada exibe uma mensagem confirmando o sucesso */
  /* e um botao para voltar aa pagina principal.                        */
  if (EditaPergunta($sock, $cod_pergunta, $pergunta, $resposta))
  {
    AtualizaFerramentasNova($sock,6,'T');

    echo("  <form action=".$origem.".php method=post>\n");

    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


    echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
    /* RePassa a variavel $pagprinc, a qual armazena o nome do documento  */
    /* que estah sendo exibida na pagina principal.                       */
    echo("    <input type=hidden name=pagprinc value=".$pagprinc.">\n");

    if ($origem == "ver_pergunta")
    {
      $CurCheck = explode("_",$RetCheck);
      $totalcheck = count($CurCheck);
      for ($j = 0; $j < $totalcheck; $j++)
        echo("    <input type=hidden name=check[] value=".$CurCheck[$j].">\n");
    }

    /* 23 - Pergunta editada com sucesso. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,23)."</font>\n");
    echo("    <p>\n");
    /* 23 - Voltar */
    echo("    <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("  </form>\n");
  } /* Se a pergunta NAO pode ser editada apresenta uma mensagem de erro */
    /* e oferece a possibilidade de reedita-la.                            */
  else
  {
    echo("  <form name=voltar action=editar_pergunta.php method=post>\n");
//    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


    /* 24 - Erro na edi��o da pergunta. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases,24)."</font>\n");
    echo("    <p>\n");

    echo("    <input type=hidden name=origem value='".$origem."'>\n");
    echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
    echo("    <input type=hidden name=cod_pergunta value=".$cod_pergunta.">\n");

    echo("    <input type=hidden name=check value=".$RetCheck.">\n");

    
    /* 23 - Voltar */
    echo("    <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("  </form>\n");
  }

  if ($origem == "ver_pergunta")
  {
    echo("<script language=JavaScript>\n\n");
    echo("  AtualizaPaginaPrincipal();\n");
    echo("</script>\n\n");
  }

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
