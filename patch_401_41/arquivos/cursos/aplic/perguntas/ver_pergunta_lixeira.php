<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/ver_pergunta_lixeira.php

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
  ARQUIVO : cursos/aplic/mural/ver_pergunta_lixeira.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,6);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);

  echo("  <html>\n");
  /* 1 - Perguntas Freq�entes */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");

  echo("<script language=javascript>\n\n");

  echo("  function AtualizaPaginaPrincipal()\n");
  echo("  {\n");
  echo("    top.opener.location.href=\"perguntas.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."&cod_assunto_pai=".$cod_assunto_pai."&".time());
  echo("&pag_anterior=perguntas\";\n");
  echo("    return(false);\n");
  echo("  }\n\n");

  echo("  function ExcluirPergunta(id)\n");
  echo("  {\n");
  /* 43 - Deseja excluir definitivamente esta pergunta? */
  echo("    if (confirm(\"".RetornaFraseDaLista($lista_frases, 43)."\"))\n");
  echo("    {\n");
  echo("      eval(\"document.controle\" + id + \".action='excluir_pergunta.php'\");\n");
  echo("      return(true);\n");
  echo("    }\n");
  echo("    else\n");
  echo("      return(false);\n");
  echo("  }\n\n");

  echo("  function RecuperarPergunta(id)\n");
  echo("  {\n");
  echo("    eval(\"document.controle\" + id + \".action='recuperar_pergunta.php'\");\n");
  echo("    return(true);\n");
  echo("  }\n\n");


  echo("</script>\n\n");

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onload='self.focus();'>\n");
  /* 1 - Perguntas Freq�entes */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 18 - Ver Pergunta */
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,18)."</b>";
//  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,1));
  echo("  <br>\n");


  echo("  <table border=0 width=100% cellspacing=0 cellpadding=5>\n");


  /* Se o array $check estiver definido (foi passado) entao      */
  /* conte o numero de elementos.                                */
  /* Isto eh necessario para o procedimento excluir e recuperar, */
  /* nos quais existe a possibilidade de voltar para listar as   */
  /* demais perguntas, quando soh eh listada uma pergunta.       */
  if (isset($cod_pergunta))
    $nchecks = count($cod_pergunta);
  else  /* Do contrario NAO sao listadas as perguntas.            */
  {
    $nchecks = 0;
    /* 50 - N�o h� perguntas selecionadas.                  */
    echo("<font class=text>".RetornaFraseDaLista($lista_frases, 50)."</font>\n");
  }

  for ($i = 0; $i < $nchecks; $i++)
  {
    /* Se a pergunta NAO foi apagada entao atualiza a pagina */
    /* principal.                                            */
    if (!PerguntaFoiApagada($sock, $cod_pergunta[$i]))
    {
      echo("<script language=javascript>\n\n");
      echo("  AtualizaPaginaPrincipal();\n");
      echo("</script>\n");
      $i--;
    }
    else
    {

      /* Obtem o assunto, pergunta e a resposta. */
      $dados_pergunta = RetornaPergunta($sock, $cod_pergunta[$i]);
      /* Obtem o caminho completo da pergunta.   */
      $caminho_pergunta = RetornaCaminhoAssunto($sock, $dados_pergunta['cod_assunto']);
      /* Deixa a pergunta em negrito.            */
      $questao_pergunta = $dados_pergunta['pergunta'];
      $resposta_pergunta = $dados_pergunta['resposta'];

      /* Alterna entre as cores cinza-escuro e cinza-claro se o contador */
      /* for par ou impar, respectivamente.                              */
      if ($i % 2 == 0)
      {
        echo("    <tr class=g1field>\n");
        echo("      <td class=g1field>\n");
      } else if ($i % 2 == 1)
      {
        echo("    <tr class=g2field>\n");
        echo("      <td class=g2field>\n");
      }

      /* Exibe a pergunta e sua resposta. */
      echo("        <font class=text>".($i + 1).": <b>".Space2Nbsp(Enter2Br(LimpaTags($questao_pergunta)))."</b></font><br>\n");
      echo("        <font class=textsmall>".Space2Nbsp(Enter2Br(LimpaTags($caminho_pergunta)))."</font><p>\n");
      echo("        <font class=text>".Space2Nbsp(Enter2Br(LimpaTags($resposta_pergunta)))."</font><br>\n");
      echo("      </td>\n");
      /* Alterna entre as cores cinza-escuro e cinza-claro se o contador */
      /* for par ou impar, respectivamente.                              */
      if ($i % 2 == 0)
        echo("      <td width=60px align=right class=g1field>\n");
      else if ($i % 2 == 1)
        echo("      <td width=60px align=right class=g2field>\n");

      echo("        <form name=controle".$i." method=post>\n");

      echo(RetornaSessionIDInput());
      echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");

      /* Especifica o documento de origem para 'ver_pergunta_lixeira'.   */
      /* Isto eh necessario, pois tanto 'ver_pergunta_lixeira.php' como  */
      /* 'perguntas.php' chamam as fun�oes excluir e restaurar.          */
      echo("          <input type=hidden name=origem value=ver_pergunta_lixeira>\n");
      /* RePassa o 'cod_assunto_pai', necessario para atualizar a pagina */
      /* principal.                                                      */
      echo("          <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
      /* RePassa o 'cod_assunto_anterior', necessario para voltar ao     */
      /* assunto anterior a visualiza�ao da lixeira.                     */
      echo("          <input type=hidden name=cod_assunto_anterior value=".$cod_assunto_anterior.">\n");
      /* RePassa a pagina anterior a visualiza�ao da lixeira.            */
      echo("          <input type=hidden name=pag_anterior value=".$pag_anterior.">\n");
      /* Passa o 'cod_pergunta' para execu�ao das a�oes.                 */
      echo("          <input type=hidden name=cod_pergunta value=".$cod_pergunta[$i].">\n");
      /* Sequencializa o array de todas as perguntas visualizadas e o    */
      /* formata em um vetor. Necessario em caso do usuario abortar a         */
      /* opera�ao de recuperar ou excluir.                               */
      $ThisCheck = implode("_",$cod_pergunta);
      echo("          <input type=hidden name=check value=".$ThisCheck.">\n");

      /* Copia para o array '$aux' todos as perguntas do array '$check'  */
      /* exceto a pergunta atual, que serah apagada, movida ou editada.  */
      $k = 0;
      $total = count($cod_pergunta);
      for ($j = 0; $j < $total; $j++)
      {
        /* Se a for diferente da pergunta que sofrera a a�ao entao copie */
        if ($cod_pergunta[$j] != $cod_pergunta[$i])
          $aux[$k++] = $cod_pergunta[$j];
      }
      /* Se houver mais de 1 elemento defina e passe a variavel '$listacheck'. */
      /* As paginas que executarem as a�oes verificarao se '$listacheck' estah */
      /* definida, se estiver entao define o array '$check' e o envia se       */
      /* clicado em 'Voltar' para 'ver_pergunta_lixeira.php'. Se '$listacheck' */
      /* NAO estiver definida entao NAO define a variavel/array '$check' usada */
      /* no come�o desta pagina para listar as perguntas.                          */
      if ($total > 1)
      {
        /* Seq�encializa o array e o formata com "_" entre os componentes. */
        $lcheck = implode("_",$aux);
        /* Cria um campo com o array sequenciado. */
        echo("          <input type=hidden name=listacheck value=".$lcheck.">\n");
      }

      /* Cria os controles. No evento 'onclick', chama as fun�oes javascript para  */
      /* a a�ao correspondente ('recuperar', 'excluir')                            */

      /* 48 - Recuperar */
      echo("          <input type=submit class=text onclick='return(RecuperarPergunta(".$i."));' width=60 value=\"".RetornaFraseDaLista($lista_frases_geral,48)."\"><br>\n");
      /* 12 - Excluir */
      echo("          <input type=submit class=text onclick='return(ExcluirPergunta(".$i."));' width=60 value=\"   ".RetornaFraseDaLista($lista_frases_geral,12)."  \"><br>\n");
      echo("        </form>\n");
      echo("      </td>\n");
      echo("    </tr>\n");
    }
  }
  echo("  </table>\n");

  /* Exibe um controle para fechar a janela. */
  echo("  <form>\n");
  /* 13 - Fechar */
  echo("    <input type=button value='".RetornaFraseDaLista($lista_frases_geral, 13));
  echo("' onClick='self.close();'>\n");
  echo("  </form>\n");

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
