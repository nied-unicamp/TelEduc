<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/marcar_sessao2.php

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
  ARQUIVO : cursos/aplic/batepapo/marcar_sessao2.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");


  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  
  $tipo="I";
  
  include("../topo_tela.php");

  
  /* Pega a informação da possivel avaliacao */
  $valor = $_POST['ValorAval'];
  $objetivos = $_POST['ObjetivosAval'];
  $criterios = $_POST['CriteriosAval'];
  $com_avaliacao = ($objetivos != NULL && $valor != NULL && $criterios != NULL);
  
  /* tela_topo.php faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); */

  $data_inicio=DataHora2Unixtime($data." ".$hora_inicio.":00");
  $data_fim=DataHora2Unixtime($data." ".$hora_fim.":00");

  $e_formador      = EFormador($sock,$cod_curso,$cod_usuario);

  echo("<script type=\"text/javascript\" language=JavaScript>\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList();\n");
  echo("  } \n");

  echo("  function OpenWindow() \n");
  echo("  {\n");
  echo("    window.open(\"entrar_sala.php?".RetornaSessionID()."&cod_curso=".$cod_curso."\",\"Batepapo\",\"width=1000,height=700,top=50,left=50,scrollbars=no,status=yes,toolbar=no,menubar=no,resizable=no\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("</script>\n");

  if ($data_inicio>$data_fim)
  {
    $data_fim+=24*60*60; /* 1 dia */
  }

  $lista = RetornaListaSessoesMarcadas($sock);
  if (count($lista)>0)
  {
    foreach ($lista as $cod => $linha)
    {
      if (($data_inicio>=$linha['data_inicio'] && $data_inicio<$linha['data_fim'])
        || $data_fim>$linha['data_inicio'] && $data_fim<=$linha['data_fim'])
      {
      	/* sessao_anterior eh a que foi marcada anteriormente */
      	$sessao_anterior = $linha['assunto'];
      	
        include("../menu_principal.php");

        echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

        /* 1 - Bate-Papo */
        echo("<h4>".RetornaFraseDaLista($lista_frases,1));
        /* 47 - Marcar sess�o */
        echo(" - ".RetornaFraseDaLista($lista_frases,47)."</h4>");

        echo("<span class=\"btsNav\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a></span><br/>\n");

        echo("<div id=\"mudarFonte\">\n");
        echo("	<a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
        echo("	<a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
        echo("	<a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
        echo("</div>\n");

        /* <!----------------- Tabelao -----------------> */
        echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");

       echo("  <tr>\n");
       echo("    <td valign=\"top\">\n");

        echo("      <ul class=\"btAuxTabs\">\n");
        /* 27 - Ver sess�es realizadas */
        echo("        <li><span title=\"Ver sess�es realizadas\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 27)."</span></li>\n");
        if ($e_formador)
        {
          /* 47 - Marcar sess�o */
          echo("        <li><span title=\"Marcar sess�o\" onClick=\"document.location='marcar_sessao.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
          /* 63 - Desmarcar sess�es */
          echo("        <li><span title=\"Desmarcar sess�es\" onClick=\"document.location='desmarcar_sessoes.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 63)."</span></li>\n");

          /* 78 - Lixeira */
          echo("        <li><span title=\"Lixeira\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;lixeira=sim';\">".RetornaFraseDaLista($lista_frases, 78)."</span></li>\n");
        }
        /* 55 - Pr�xima sess�o marcada */
        echo("        <li><span title=\"Pr�xima sess�o marcada\" onClick=\"document.location='ver_sessoes_marcadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases, 55)."</span></li>\n");

        echo("      </ul>\n");
        echo("    </td>\n");
        echo("  </tr>\n");
        echo("  <tr>\n");
        echo("    <td valign=\"top\">\n");
        echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
        echo("        <tr class=\"head01\">\n");
        /* 60 - Sess�o n�o marcada */
        echo("          <td>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
        echo("        </tr>\n");

        /* 56 - de */
        /* 57 - a */
        /* 61 - N�o foi poss�vel incluir a sess�o */
        /* 62 - pois j� est� marcada a sess�o */
        echo("        <tr>\n");
        echo("          <td>".RetornaFraseDaLista($lista_frases,61)." \"".LimpaTitulo($assunto)."\" ".RetornaFraseDaLista($lista_frases,56)." ".Unixtime2DataHora($data_inicio)." ".RetornaFraseDaLista($lista_frases,57)." ".Unixtime2DataHora($data_fim).", ".RetornaFraseDaLista($lista_frases,62)." \"".$sessao_anterior."\" ".RetornaFraseDaLista($lista_frases,56)." ".Unixtime2DataHora($linha['data_inicio'])." ".RetornaFraseDaLista($lista_frases,57)." ".Unixtime2DataHora($linha['data_fim']).".</td>\n");
        echo("        </tr>\n");
        // Fim Tabela Interna
        echo("      </table>\n");
        echo("    </td>\n");
        echo("  </tr>\n");
        // Fim Tabel�o
        echo("</table>\n");

        include("../tela2.php");
        exit();
      }
    }
  }
  /* Se o hor�rio de in�cio for anterior ao atual, ent�o exibe uma mensagem informando */
  /* que n�o � poss�vel marcar sess�es em datas/horas passadas.                        */
  if ($data_inicio < time())
  {
    header("Location:marcar_sessao.php?cod_curso=".$cod_curso."&acao=erro_sessao&atualizacao=true");
    Desconectar($sock);
  }
  else
  {
    InsereBPAssuntos($sock, $assunto, $data_inicio, $data_fim);
    AtualizaFerramentasNova($sock,10,'T');

    if ($com_avaliacao)	//sess�o marcada com avalia��o
    {
      $tabela="Avaliacao";
      $cod_assunto=RetornaCodAssuntoSessao($sock,$data_inicio,$data_fim);
      $cod_atividade=$cod_assunto;
      $cod_avaliacao=IniciaCriacaoAvaliacao($sock, $tabela, $cod_atividade, $cod_usuario, 'B', $tipo);
	  AtualizaCadastroAvaliacao($sock, $tabela, $cod_usuario, trim($objetivos), trim($criterios),'B', $valor,$data_inicio,$data_fim,$cod_avaliacao);

      header("Location:ver_sessoes_marcadas.php?cod_curso=".$cod_curso."&acao=sessao_marcada&atualizacao=true");
      Desconectar($sock);
    }
    else
    {
      header("Location:ver_sessoes_marcadas.php?cod_curso=".$cod_curso."&acao=sessao_marcada&atualizacao=true");
      Desconectar($sock);
    }
  }

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
