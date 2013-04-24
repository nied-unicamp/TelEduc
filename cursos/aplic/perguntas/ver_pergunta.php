<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/perguntas/ver_pergunta.php

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
  ARQUIVO : cursos/aplic/mural/ver_pergunta.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("perguntas.inc");

  $cod_ferramenta = 6;
  include("../topo_tela.php");
/*
  $cod_usuario = VerificaAutenticacao($cod_curso);

  $sock = Conectar("");

  $lista_frases = RetornaListaDeFrases($sock, 6);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  VerificaAcessoAoCurso($sock, $cod_curso, $cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,6);


  echo("  <html>\n");
  // 1 - Perguntas Freq�entes 
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("    <link rel=stylesheet TYPE=text/css href=perguntas.css>\n");
  echo("\n");
*/
  
  echo("<script language=javascript>\n\n");
/*
  echo("  function AtualizaPaginaPrincipal()\n");
  echo("  {\n");
  echo("    top.opener.location.href=\"".$pagprinc.".php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."&cod_assunto_pai=".$cod_assunto_pai."&".time()."\";\n");
  echo("    return(false);\n");
  echo("  }\n\n");
  */
/*  echo("  function ApagarPergunta(id)\n");
  echo("  {\n");
  // 21 - Tem certeza que deseja apagar esta pergunta? 
  echo("    if (confirm(\"".RetornaFraseDaLista($lista_frases, 21)."\"))\n");
  echo("    {\n");
//  echo("      eval(\"document.controle\" + id + \".action='acoes.php\");\n");
  echo("      alert('ok');");
//  echo("      document.Perguntafrm.action = \"acoes.php\";\n");  
//  echo("      document.Perguntafrm.acao.value = apagarItemJanela\";\n");
//  echo("      document.Perguntafrm.submit();\n");
  echo("      return(true);\n");
  echo("      alert('ok1');");
  echo("    }\n");
  echo("    else\n");
  echo("      return(false);\n");
  echo("  }\n\n");
*/
  echo("  function EditarPergunta(id)\n");
  echo("  {\n");
//  echo("    eval(\"document.controle\" + id + \".action='editar_pergunta.php'\");\n");
  echo("    eval(\"document.controle\" + id + \".action='editar_pergunta.php?cod_curso=".$cod_curso."'\");\n");
  echo("    return(true);\n");
  echo("  }\n\n");

  echo("  function MoverPergunta(id)\n");
  echo("  {\n");
  echo("    eval(\"document.controle\" + id + \".action='mover_pergunta.php'\");\n");
  echo("    return(true);\n");
  echo("  }\n\n");

  

  echo("</script>\n\n");

   /*
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onload='self.focus();'>\n");
  // 1 - Perguntas Freq�entes 
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  // 18 - Ver Pergunta 
  $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,18)."</b>";
//  echo(PreparaCabecalho($cod_curso, $cabecalho, 6,5));
  echo("  <br>\n");
*/
  echo("<br><br>");
  echo("          <table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("              <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("                <td class=\"btAuxTabs\">\n");
  echo("                  <ul class=\"btAuxTabs\">\n");

  
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");  
  echo("                    <tr class=\"head\">\n");
  
  /* Se o array $check estiver definido (foi passado) entao   */
  /* conte o numero de elementos.                             */
  /* Isto eh necessario para o procedimento apagar e mover,   */
  /* nos quais existe a possibilidade de voltar para listar   */
  /* as demais perguntas, quando soh eh listada uma pergunta. */
  if (isset($cod_pergunta))
    $nchecks = count($cod_pergunta);
  else
  /* Do contrario NAO sao listadas as perguntas.            */
  {
    $nchecks = 0;
    /* 50 - N�o h� perguntas selecionadas.                  */
    echo("<font class=text>".RetornaFraseDaLista($lista_frases, 50)."</font>\n");
  }

  $eformador = EFormador($sock, $cod_curso, $cod_usuario);

  for ($i = 0; $i < $nchecks; $i++)
  {
    /* Se a pergunta NAO existir entao atualiza a pagina    */
    /* principal.                                           */
    if (!PerguntaExiste($sock, $cod_pergunta[$i]))
    {
/*      echo("<script language=javascript>\n\n");
      echo("  AtualizaPaginaPrincipal();\n");
      echo("</script>\n");
*/      $i--;
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
  /*    if ($i % 2 == 0)
      {
        echo("    <tr class=g1field>\n");
        echo("      <td class=g1field>\n");
      } else if ($i % 2 == 1)
      {
        echo("    <tr class=g2field>\n");
        echo("      <td class=g2field>\n");
      }
*/
      echo("<tr class=\"altColor".($i % 2)."\"><td>\n");
      /* Exibe a pergunta, o assunto a qual pertence e sua resposta. */
      echo("        <font class=text>".($i + 1).": <b>".AjustaParagrafo(Enter2Br(LimpaTags($questao_pergunta)))."</b></font><br>\n");
      /* 55 - Assunto: */
      echo("        <font class=textsmall><b>".RetornaFraseDaLista($lista_frases,55)."</b> ".AjustaParagrafo(Enter2Br(LimpaTags($caminho_pergunta)))."</font><p>\n");
      echo("        <font class=text>".AjustaParagrafo(Enter2Br(LimpaTags($resposta_pergunta)))."</font><br>\n");
      echo("      </td>\n");

      /* Se for formador entao exiba os controles para apagar, mover e editar.  */
      if ($eformador)
      {
        /* Alterna entre as cores cinza-escuro e cinza-claro se o contador */
        /* for par ou impar, respectivamente.                              */
/*        if ($i % 2 == 0)
          echo("      <td width=60px align=right class=g1field>\n");
        else if ($i % 2 == 1)
          echo("      <td width=60px align=right class=g2field>\n");
*/
		echo("       <td width=60px align=right>\n");
//      	echo("        <form name=controle".$i." method=post>\n");
      	echo("        <form method=post name=controle".$i." action=\"acoes.php\">\n");
        echo("          <input type=hidden name=acao value=apagarItemJanela>\n");
      	
      	
//        echo(RetornaSessionIDInput());
        echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");


        /* Especifica o documento de origem para 'ver_pergunta'. Isto eh   */
        /* necessario, pois tanto 'ver_pergunta.php' como 'perguntas.php'  */
        /* chamam as fun�oes apagar, mover e editar.                       */
  //      echo("          <input type=hidden name=origem value=ver_pergunta>\n");
        /* RePassa o 'cod_assunto_pai', necessario para atualizar a pagina */
        /* principal.                                                      */
//        echo("          <input type=hidden name=cod_assunto_pai value=".$cod_assunto_pai.">\n");
        /* Passa o 'cod_pergunta' para execu�ao das a�oes.                 */
        echo("          <input type=hidden name=cod_pergunta value=".$cod_pergunta[$i].">\n");
        /* Especifica o documento da pagina principal, o qual chamou o    */
        /* ver_pergunta.php. Isto eh necessario para atualizar a pagina   */
        /* principal que pode ser perguntas.php ou exibir_todas.php.      */
//        echo("          <input type=hidden name=pagprinc value=".$pagprinc.">\n");
        /* Seq�encializa o array de todas as perguntas visualizadas e o    */
        /* formata em URL. Necessario em caso do usuario abortar a         */
        /* opera�ao de apagar, editar ou mover.                            */
        //$ThisCheck = urlencode(serialize($check));
        $ThisCheck = implode("_" , $cod_pergunta);
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
        /* clicado em 'Voltar' para 'ver_pergunta.php'. Se '$listacheck' NAO     */
        /* estiver definida entao NAO define a variavel/array '$check' usada no  */
        /* come�o desta pagina para listar as perguntas.                         */
        if ($total > 1)
        {
          /* Seq�encializa o array e o formata em URL. */
         // $lcheck = urlencode(serialize($aux));
          $lcheck = implode("_" , $aux);
          /* Cria um campo com o array sequenciado e em formato de URL. */
          echo("          <input type=hidden name=listacheck value=".$lcheck.">\n");
        }

        /* Cria os controles. No evento 'onclick', chama as fun�oes javascript para  */
        /* a a�ao correspondente ('apagar', 'mover', 'editar')                       */
        /* 9 - Editar */
        echo("          <input type=submit class=\"input\" onclick='EditarPergunta(".$i.");' value=\" ".RetornaFraseDaLista($lista_frases_geral,9)." \"><br><br>\n");
        
        /* 25 - Mover */
//        echo("          <input type=submit class=\"input\" onclick='MoverPergunta(".$i.");' value=\" ".RetornaFraseDaLista($lista_frases_geral,25)." \"><br><br>\n");
        /* 1 - Apagar */
//        echo("          <input type=submit class=\"input\" onclick='return(ApagarPergunta(".$i."));' value=".RetornaFraseDaLista($lista_frases_geral,1)."><br><br>\n");
        echo("          <input type=submit class=\"input\" value=".RetornaFraseDaLista($lista_frases_geral,1)."><br><br>\n");
        
        
        echo("        </form>\n");
        echo("      </td>\n");
      }
      echo("    </tr>\n");
    }
  }
  echo("  </table>\n");

  /* Exibe um controle para fechar a janela. */
  echo("  <form>\n");
  /* 13 - Fechar */
  echo("   <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral, 13)."</span></li>\n");

  echo("  </form>\n");
  echo("</tr>");
  echo("</table>");

  echo("  </body>\n");
  echo("  </html>\n");

  Desconectar($sock);
?>
