<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/mural/recuperar_pergunta.php

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
  ARQUIVO : cursos/aplic/mural/recuperar_pergunta.php
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
  /* 1 - Perguntas Freqüentes */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");

  echo("<script language=JavaScript>\n\n");

  echo("  var selected_item = ".$cod_pergunta.";\n");

  echo("  function CancelaRecuperar()\n");
  echo("  {\n");
  echo("    document.frmCancelaRecuperar.submit();\n");
  echo("    return(true);\n");
  echo("  }\n\n");

  echo("  function MoverPergunta(id, destino)\n");
  echo("  {\n");
  /* 47 - Deseja realmente recuperar esta pergunta? */
  echo("    if (confirm(\"".RetornaFraseDaLista($lista_frases, 47)."\"))\n");
  echo("    {\n");
  echo("      document.frmRecuperarPergunta.cod_assunto_dest.value = destino;\n");
  echo("      document.frmRecuperarPergunta.submit();\n");
  echo("    }\n");
  echo("  }\n\n");

  echo("  function AtualizaPaginaPrincipal()\n");
  echo("  {\n");
  echo("    top.opener.location.href=\"perguntas.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."&cod_assunto_pai=".$cod_assunto_pai."&".time()."\";\n");
  echo("    return(false);\n");
  echo("  }\n\n");

  echo("</script>\n\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
  /* 1 - Perguntas Freqüentes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 46 - Recuperar Pergunta */
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 46)."</b>";
  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,1));
  echo("  <br>\n");

  /* Se a pergunta foi apagada (encontra-se na lixeira) entao exiba-a  */
  /* e mostre o layer de mover.                                        */
  if (PerguntaFoiApagada($sock, $cod_pergunta))
  {
    echo("  <form name=frmRecuperarPergunta action=recuperar_pergunta2.php method=post>\n");

    echo(RetornaSessionIDInput());
    echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


    /* Especifica o documento de origem para 'ver_pergunta_lixeira'.     */
    echo("    <input type=hidden name=origem value=ver_pergunta_lixeira>\n");
    /* RePassa o 'cod_assunto_pai', necessario para atualizar a pagina   */
    /* principal.                                                        */
    echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
    /* RePassa o 'cod_pergunta' para recuperar a pergunta.               */
    echo("    <input type=hidden name=cod_pergunta value=".$cod_pergunta.">\n");
    /* Passa o 'cod_assunto_dest', necessario para recuperar a pergunta. */
    echo("    <input type=hidden name=cod_assunto_dest value=-1>\n");
    /* RePassa o 'cod_assunto_anterior', necessario para voltar ao     */
    /* assunto anterior a visualizaçao da lixeira.                     */
    echo("          <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
    /* Especifica o documento da pagina principal, o qual chamou o    */
    /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
    /* eh necessario para voltar ao modo de visualizaçao anterior.    */
    echo("    <input type=hidden name=pag_anterior value=".$pag_anterior.">\n");
    /* Passa 'listacheck', este encontra-se seqüenciada e em formato     */
    /* URL, necessario para listar todas as perguntas visualizadas       */
    /* exceto a pergunta recuperada.                                     */
    echo("    <input type=hidden name=listacheck value=".$listacheck.">\n");
    /* Passa 'Retcheck', este encontra-se sequenciada e em formato  URL, */
    /* necessario para listar todas as perguntas visualizadas caso o     */
    /* usuario aborte a operaçao de recuperar.                           */
    echo("    <input type=hidden name=RetCheck value=".$check.">\n");

    /* Obtem o assunto, pergunta e a resposta. */
    $dados_pergunta = RetornaPergunta($sock, $cod_pergunta);
    /* Obtem o caminho completo da pergunta.   */
    $caminho_pergunta = RetornaCaminhoAssunto($sock, $dados_pergunta['cod_assunto']);
    /* Deixa a pergunta em negrito.            */
    $questao_pergunta = "<b>".$dados_pergunta['pergunta']."</b>";
    $resposta_pergunta = $dados_pergunta['resposta'];

    echo("    <table border=0 width=100% cellpadding=5>\n");
    echo("      <tr class=g1field>\n");
    echo("        <td class=g1field>\n");
    /* Exibe a pergunta, o assunto a qual pertence e sua resposta. */
    echo("          <font class=text>".$questao_pergunta."</font><br>\n");
    echo("          <font class=text>".$resposta_pergunta."</font><br>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");

    echo("    <br>\n");
    echo("    <p>\n");

    echo("    <table width=100% border=0>\n");
    echo("      <tr class=wtfield>\n");
    echo("        <td class=wtfield>\n");
    /* 51 - De: */
    echo("          <font class=text><b>".RetornaFraseDaLista($lista_frases, 51)."</b> ".$caminho_pergunta."</font>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr class=wtfield>\n");
    echo("        <td class=wtfield>\n");
    /* 52 - Para: */
    echo("          <font class=text><b>".RetornaFraseDaLista($lista_frases, 52)."</b></font>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr class=wtfield>\n");
    echo("        <td class=wtfield>\n");
    echo("          ".EstruturaMoverPergunta($sock, $cod_assunto_pai));
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");

    echo("    <br>\n");
    echo("    <p>\n");


    echo("    <div align=right width=100%>\n");
    /* 2 - Cancelar */
    echo("      <input type=button class=text onclick='CancelaRecuperar();' value=".RetornaFraseDaLista($lista_frases_geral,2).">\n");
    echo("    </div>\n");

    echo("  </form>\n");
  }
  else
  {
    /* 49 - Erro ao recuperar a pergunta. */
    echo("    <font class=text>".RetornaFraseDaLista($lista_frases, 49)."</font>\n");
  }

  echo("  <br>\n");
  echo("  <p>\n");


  /* Formulario para cencelar a funçao Recuperar pergunta.    */
  /* Volta para ver_pergunta_lixeira.php.                     */
  echo("  <form name=frmCancelaRecuperar action=ver_pergunta_lixeira.php method=post>\n");

  echo(RetornaSessionIDInput());
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


  /* Deixa 'check' no formato de string, deseqüencializa e    */
  /* coloca no array 'check[]'. Necessario para listar todas  */
  /* as perguntas visualizadas.                               */
  $CurCheck = explode("_",$check);
  $totalcheck = count($CurCheck);
  for ($j = 0; $j < $totalcheck; $j++)
    echo("    <input type=hidden name=check[] value=".$CurCheck[$j].">\n");

  /* Repassa o $cod_assunto_pai para execuçao de outras       */
  /* operaçoes como excluir.                         */
  echo("    <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
  /* RePassa o 'cod_assunto_anterior', necessario para voltar ao     */
  /* assunto anterior a visualizaçao da lixeira.                     */
  echo("    <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
  /* Especifica o documento da pagina principal, o qual chamou o    */
  /* perguntas.php, mas com o cod_assunto_pai = 2 (lixeira). Isto   */
  /* eh necessario para voltar ao modo de visualizaçao anterior.    */
  echo("    <input type=hidden name=pag_anterior value=".$pag_anterior.">\n");

  echo("  </form>\n");


  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
