<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver_lixeira.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver_lixeira.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,COD_AVALIACAO);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario, COD_AVALIACAO);

  if (EConvidado($sock, $cod_usuario) || EVisitante($sock, $cod_curso, $cod_usuario))
  {
    echo("<html>\n");
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

  echo("<html>\n");
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");
  if (isset($SalvarEmArquivo))
  {
    echo("<style>\n");
    include("../teleduc.css");
    include("avaliacoes.css");
    echo("</style>\n");
  }
  else
  {
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
    // funcoes JavaScript
    echo("<script language=JavaScript>\n");

    echo("  function Historico()\n");
    echo("  {\n");
    $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
    $nome_janela = "'AvaliacoesHistorico'";
    echo("    window.open('historico.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."', ".$nome_janela.", ".$param.");\n");
    echo("    return false; \n");
    echo("  }\n");

    echo("  function Editar()\n");
    echo("  {\n");
    echo("    document.frmVer.action = 'alterar_avaliacao.php';\n");
    echo("    document.frmVer.submit();\n");
    echo("  }\n");
    /*
      echo("  function ApagarAvaliacao()\n");
      echo("  {\n");
      echo("    document.frmVer.action = '';\n");
      echo("    document.frmVer.submit();\n");
      echo("  }\n"); */
    echo("  function AvaliarParticipantes()\n");
    echo("  {\n");
    echo("    document.frmVer.action = 'avaliar_participantes.php';\n");
    echo("    document.frmVer.submit();\n");
    echo("  }\n");

    echo("  function VerificarParticipacao()\n");
    echo("  {\n");
    echo("    document.frmVer.action = 'ver_participacao.php';\n");
    echo("    document.frmVer.submit();\n");
    echo("  }\n");

    echo("  function VerNotas()\n");
    echo("  {\n");
    echo("    document.frmVer.action = 'ver_notas.php';\n");
    echo("    document.frmVer.submit();\n");
    echo("  }\n");
    /*
      echo("  function HistoricodoDesempenho()\n");
      echo("  {\n");
      echo("    document.frmVer.action = '';\n");
      echo("    document.frmVer.submit();\n");
      echo("  }\n"); */
    echo("  function SalvarVerAvaliacao()\n");
    echo("  {\n");
    echo("    document.frmSalvar.action = 'salvar_ver_avaliacao.php'; \n");
    echo("    document.frmSalvar.submit();\n");
    echo("  }\n");

    echo("  function ImprimirRelatorio()\n");
    echo("  {\n");
    echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
    echo("    {\n");
    echo("      self.print();\n");
    echo("    }\n");
    echo("    else\n");
    echo("    {\n");
    // 51 (gen)- Infelizmente não foi possível imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
    echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("    }\n");
    echo("  }\n");

    echo("    function VerTelaAvaliacoes(tela)\n");
    echo("    {\n");
    echo("      document.frmAvaliacao.action = 'avaliacoes.php';\n");
    echo("      document.frmAvaliacao.tela_avaliacao.value = tela;\n");
    echo("      document.frmAvaliacao.submit();\n");
    echo("      return false;\n");
    echo("    }\n");

    echo("    function VerTelaNotas()\n");
    echo("    {\n");
    echo("      document.frmAvaliacao.action = 'notas.php';\n");
    echo("      document.frmAvaliacao.submit();\n");
    echo("      return false;\n");
    echo("    }\n");

    echo("    function VerTelaLixeira()\n");
    echo("    {\n");
    echo("      document.frmAvaliacao.action = 'lixeira.php';\n");
    echo("      document.frmAvaliacao.submit();\n");
    echo("      return false;\n");
    echo("    }\n");

    echo("    function Recuperar()\n");
    echo("    {\n");
    /* 118 - Você tem certeza de que deseja recuperar esta avaliação? */
    /* 119 - (Se você também excluiu a atividade a que a avaliação se refere e quiser também recuperá-la é necessário fazê-lo na respectiva ferramenta) */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases,118).RetornaFraseDaLista($lista_frases,119)."'))");
    echo("      {\n");
    echo("        document.frmAvaliacao.operacao.value='recuperar'; \n");
    echo("        document.frmAvaliacao.action = 'excluir_avaliacao.php'; \n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("      }\n");
    echo "      return false;\n";
    echo("    }\n\n");

    echo("    function Excluir()\n");
    echo("    {\n");
    /* 129 - Você tem certeza de que deseja excluir esta avaliação? */
    /* 130 - (a avaliação será excluída definitivamente) */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases,129).RetornaFraseDaLista($lista_frases,130)."'))");
    echo("      {\n");
    echo("        document.frmAvaliacao.operacao.value='excluir'; \n");
    echo("        document.frmAvaliacao.action = 'excluir_avaliacao.php'; \n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("      }\n");
    echo "      return false;\n";
    echo("    }\n\n");

    echo("</script>\n");
  }

  // Verifica se o usuario eh formador.
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);

  // A variavel tela_avaliacao indica quais avaliacoes devem ser listadas: 'P'assadas, 'A'tuais ou 'F'uturas
  if (!isset($tela_avaliacao) || !in_array($tela_avaliacao, array('P', 'A', 'F')))
  {
    $tela_avaliacao = 'A';
  }

  // Determinamos a frase que descreve as avaliacoes e a lista de avaliacoes
  if ($tela_avaliacao == 'P')
    // 29 - Avaliações Passadas
    $lista_avaliacoes = RetornaAvaliacoesAnteriores($sock,$usr_formador);
  elseif ($tela_avaliacao == 'A')
    // 32 - Avaliações Atuais
    $lista_avaliacoes = RetornaAvaliacoesAtuais($sock,$usr_formador);
  elseif ($tela_avaliacao == 'F')
    // 30 - Avaliações Futuras
    $lista_avaliacoes = RetornaAvaliacoesFuturas($sock,$urs_formador);

  // Determinamos a cor de cada link (amarelo ou branco) no menu superior
  $cor_link1 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link2 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link1[$tela_avaliacao] = "<font color=yellow>";
  $cor_link2[$tela_avaliacao] = "</font>";

  // Página Principal
  // 1 - Avaliações
  // G 16 - Lixeira
  $cabecalho ="<b class=titulo>".RetornaFraseDaLista($lista_frases,1)."</b> - <b class=subtitulo>".RetornaFraseDaLista($lista_frases_geral, 16)."</b>\n";
  $cod_pagina=10;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,COD_AVALIACAO,$cod_pagina));

  // menu com links para as outras avaliacoes, notas e lixeira
  echo("  <p>\n");
  echo("  <table border=0 width=100%>\n");
  echo("    <tbody>\n");
  echo("      <tr class=menu>\n");
  // 29 - Avaliações Passadas
  echo("        <td align=center><a href=# onClick=return(VerTelaAvaliacoes('P')) class=menu><b>".RetornaFraseDaLista($lista_frases, 29)."</b></a></td>\n");
  // 32 - Avaliações Atuais
  echo("        <td align=center><a href=# onClick=return(VerTelaAvaliacoes('A')) class=menu><b>".RetornaFraseDaLista($lista_frases, 32)."</b></a></td>\n");
  // 30 - Avaliações Futuras
  echo("        <td align=center><a href=# onClick=return(VerTelaAvaliacoes('F')) class=menu><b>".RetornaFraseDaLista($lista_frases, 30)."</b></a></td>\n");
  // 31 - Notas dos Participantes
  echo("        <td align=center><a href=# onClick=return(VerTelaNotas()) class=menu><b>".RetornaFraseDaLista($lista_frases, 31)."</b></a></td>\n");
  // G 16 - Lixeira
  echo("        <td align=center><a href=# onClick=return(VerTelaLixeira()) class=menu><font color=yellow><b>".RetornaFraseDaLista($lista_frases_geral, 16)."</b></font></a></td>\n");
  echo("      </tr>\n");
  echo("    </tbody>\n");
  echo("  </table>\n");

  echo("<table border=0 width=100%;>\n");
  echo("  <tbody>\n");
  echo("    <tr class=menu3>\n");
  // G 48 - Recuperar
  echo("      <td align=center><a href=# class=menu3 onclick=return(Recuperar())>".RetornaFraseDaLista($lista_frases_geral, 48)."</a></td>\n");
  // G 12 - Excluir
  echo("      <td align=center><a href=# class=menu3 onclick=return(Excluir())>".RetornaFraseDaLista($lista_frases_geral, 12)."</a></td>\n");
  echo("    </tr>\n");
  echo("  </tbody>\n");
  echo("</table>\n");
  echo("    <form name=frmAvaliacao method=post>\n");
  echo(RetornaSessionIDInput());
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  // Passa o cod_avaliacao para executar ações sobre ela.
  echo("      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  // $tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
  echo("      <input type=hidden name=operacao value=-1>\n");
  echo("      <input type=hidden name=origem value=ver_lixeira>\n");
  echo("    </form>\n");

  $dados_avaliacao = RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $titulo = RetornaTituloAvaliacao($sock, $dados_avaliacao['Ferramenta'], $dados_avaliacao['Cod_atividade']);

  $tipo = "";
  // Soh existe o conceito de tipo de avaliacao (individual ou em grupo) se for a avaliacao de uma atividade no portfolio
  if ( $existe_tipo = ($dados_avaliacao['Ferramenta'] == 'P'))
  {
    // 14 - Atividade no Portfólio
    $ferramenta = RetornaFraseDaLista($lista_frases,14);
    if ($dados_avaliacao['Tipo'] == 'I')
      // 161 - Atividade individual no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 161);
    elseif ($dados_avaliacao['Tipo'] == 'G')
      // 162 - Atividade em grupo no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 162);
  }
  else if ($dados_avaliacao['Ferramenta'] == 'F')
    // 145 - Fórum de Discussão
    $tipo = RetornaFraseDaLista($lista_frases,145);
  elseif ($dados_avaliacao['Ferramenta'] == 'B')
    // 146 - Sessão de Bate-Papo
    $tipo = RetornaFraseDaLista($lista_frases,146);

  $valor = FormataNota($dados_avaliacao['Valor']);

  if ($dados_avaliacao['Objetivos'] == '')
  {
    // 157 - Não definidos
    $objetivos=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $objetivos=$dados_avaliacao['Objetivos'];

  if ($dados_avaliacao['Criterios'] == '')
  {
    // 157 - Não definidos
    $criterios=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $criterios=$dados_avaliacao['Criterios'];

  echo("<table border=0 width=100% cellspacing=0>\n");
  echo("  <tbody>\n");
  echo("    <tr>\n");
  echo("      <td width=1% class=colorfield>&nbsp;</td>\n");
  // 123 - Título
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
  /*
    // ?? - Ferramenta
    echo("      <td class=colorfield align=left>&nbsp;&nbsp;"."[Ferramenta]"."</td>\n");
    if ($existe_tipo)
      // ?? - Tipo
       echo("      <td class=colorfield align=left>&nbsp;&nbsp;"."[Tipo]"."</td>\n"); */

  // 113 - Tipo de Avaliação
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
  // 19 - Valor
  echo("      <td class=colorfield align=left>&nbsp;&nbsp;".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
  echo("    </tr>\n");

  echo("    <tr>\n");
  echo("      <td width=1%><img src=../figuras/avaliacao.gif border=0></td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".$titulo."</td>\n");
  /*
    echo("      <td class=text align=left>&nbsp;&nbsp;".$ferramenta."</td>\n");
    if ($existe_tipo)
      echo("      <td class=text align=left>&nbsp;&nbsp;".$tipo."</td>\n"); */
  echo("      <td class=text align=left>&nbsp;&nbsp;".$tipo."</td>\n");
  echo("      <td class=text align=left>&nbsp;&nbsp;".$valor."</td>\n");
  echo("    </tr>\n");
  echo("</table>\n");

  echo("<P>\n");
  echo("<table border=0 width=100% cellspacing=0>\n");
  // 75 - Objetivos
  echo("  <tr><td class=colorfield>".RetornaFraseDaLista($lista_frases,75)."</td></tr>\n");
  echo("  <tr><td><font class=text>".AjustaParagrafo(Enter2Br(LimpaTags($objetivos)))."</font></td></tr>\n");
  echo("</table>\n");
  echo("    <br>\n");

  echo("<table border=0 width=100% cellspacing=0>\n");
  // 23 - Critérios
  echo("  <tr><td class=colorfield>".RetornaFraseDaLista($lista_frases,23)."</td></tr>\n");
  echo("  <tr><td><font class=text>".AjustaParagrafo(Enter2Br(LimpaTags($criterios)))."</font></td></tr>\n");
  echo("</table>\n");
  echo("</P>\n");

  echo("<form name=frmVer>\n");
  echo(RetornaSessionIDInput());
  echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  echo("  <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
  echo("</form>\n");

  echo("<P>\n");
  echo("<div align=right>\n");
  echo("<form name=frmSalvar>\n");
  echo(RetornaSessionIDInput());
  echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("  <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  // G 50 - Salvar em Arquivo
  echo("  <input type=button value='".RetornaFraseDaLista($lista_frases_geral, 50)."' onClick='SalvarVerAvaliacao();'>\n");
  // G 14 - Imprimir
  echo("  <input type=button value='".RetornaFraseDaLista($lista_frases_geral, 14)."' onClick='ImprimirRelatorio();'>\n");
  echo("</form>");
  echo("</div>\n");
  echo("</P>\n");

  Desconectar($sock);
  exit;

?>
