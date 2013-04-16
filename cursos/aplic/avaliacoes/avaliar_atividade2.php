<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/avaliar_atividade2.php

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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/avaliar_atividade2.php
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
  /* Verifica se a pessoa a editar �formador */
  if (!EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("<html>\n");
    /* 1 - Avalia��es */
    echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");


    echo("<body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    /* 1 - Avalia��es */
    echo("<b class=titulo> ".RetornaFraseDaLista($lista_frases,1)."</b>\n");
  /* 8 - �ea restrita ao formador. */
    echo("<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,8)."</b><br>\n");
    /* 23 - Voltar (gen) */
    echo("<form><input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");
    echo("</body></html>\n");
    Desconectar($sock);
    exit;
  }

  echo("<html>\n");
  /* 1 - Avalia��es */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
  echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");

   
  /*CONSISTENCIA PARA N�O DEIXAR O CAMPO NOTA DO BANCO DE 
  DADOS DE AVALIA��O DIFERENTE DO CAMPO NOTA DO BANCO DE DADOS DE EXERCICIOS*/
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $portfolio_grupo = ( ($dados['Ferramenta'] == 'P' || $dados['Ferramenta']=='N') && ($dados['Tipo'] == 'G') )||(($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G'));
    
  if ($ferramenta == 'E')
  {
      if ($portfolio_grupo)
         $array_exercicio = RetornaDadosNotaExercicio($sock, $cod_avaliacao, $cod_grupo,1);         
      else
         $array_exercicio = RetornaDadosNotaExercicio($sock, $cod_avaliacao, $cod_aluno,0);

      if ($nota!=$array_exercicio['nota'])
      {
         $nota=$array_exercicio['nota'];
      }
  }

                                       
  
 $tabela="Avaliacao_notas";
  $virgula = strstr($nota, ",");
  if (strcmp($virgula,""))
  {
    $tmpnota=explode(",",$nota);
    $nota=implode(".", $tmpnota);
  }

  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $portfolio_grupo = ( ($dados['Ferramenta'] == 'P' || $dados['Ferramenta']=='N') && ($dados['Tipo'] == 'G') )||(($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G'));

  $comentario = EliminaScript($comentario);
  $comentario = LimpaConteudo($comentario);
  if ($EhAlteracaoDeNota)
  {
    if ($portfolio_grupo)
    {
      $lista_integrantes=RetornaListaIntegrantesMomentoAvaliacao($sock,$cod_curso,$cod_grupo,$cod_avaliacao);      
      foreach ($lista_integrantes as $cod_aluno => $linha)
      {
        AtualizaAlteracaoAvaliacaoElementodoGrupo($sock, $tabela, $cod_aluno, $cod_avaliacao, trim($comentario), $nota, $compartilhamento);
      }
      $verifica=1;
    }
    else
    {
      AtualizaAlteracaoAvaliacao($sock, $tabela, $cod_aluno, $cod_avaliacao, $cod_nota, trim($comentario), $nota, $compartilhamento);
    }
  }
  else
  {
    if ($portfolio_grupo)
    {
      $lista_integrantes=RetornaListaIntegrantes($sock,$cod_grupo,$cod_avaliacao);
      if ($lista_integrantes)
      {
         foreach ($lista_integrantes as $cod_aluno => $linha)
         {
            AtualizaAvaliacaoElementodoGrupo($sock, $tabela, $cod_aluno, $cod_avaliacao, trim($comentario), $nota, $compartilhamento);
         }
         $verifica=1;
      }   
    }
    else
    {
      AtualizaAvaliacaoAluno($sock, $tabela, $cod_nota, trim($comentario), $nota, $compartilhamento);
    }
  }

  // if ((!strcmp($ferramenta,'P')) && (strcmp($cod_item,'')))
  if ($ferramenta == 'P' && $cod_item != '')
  {
    AtualizaItemDeAvaliacao($sock,$cod_avaliacao,$cod_item,$cod_nota);
  }

  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onload=self.focus();>\n");
  /* 1 - Avalia��es */
  $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b>";
  /* 33 - Avaliar participante */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases,95)."</b>";

  $cod_pagina=7;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,22,$cod_pagina));

  echo("<br>\n");
  echo("<p>\n");

  if ($portfolio_grupo)
  {
     if($verifica)
    /* 55 - Grupo avaliado com sucesso! */
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,55)."<br><br>\n");
    else
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,189).NomeGrupo($sock,$cod_grupo).". <br><br>\n");    
  }
  else
     /* 56 - Participante avaliado com sucesso! */
    echo("<font class=text>".RetornaFraseDaLista($lista_frases,56)."<br><br>\n");

  /* 13 - Fechar (ger) */
  echo("  <form><input class=text type=button value='".RetornaFraseDaLista($lista_frases_geral,13)."' onClick='opener.top.trabalho.direita.location.reload();self.close();'></form>\n");

  echo("</body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
