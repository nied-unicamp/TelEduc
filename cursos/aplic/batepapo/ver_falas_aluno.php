<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/ver_falas_aluno.php

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
  ARQUIVO : cursos/aplic/batepapo/ver_falas_aluno.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* topo_tela.php faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario); */

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);
  $e_formador=EFormador($sock,$cod_curso,$cod_usuario);

  echo("<script type=\"text/javascript\" language=javascript>\n");

  echo("  var window_handle;\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  /* 45- Infelizmente não foi possível imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("      alert('".RetornaFraseDaLista($lista_frases,45)."');\n");
  echo("    }\n");
  echo("  }\n");

  echo("  function OpenWindowLink(funcao)\n");
  echo("  {\n");
  echo("    window_handle = window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("  window_handle.focus();\n");
  echo("  }\n");

   /* Função JvaScript para chamar página para salvar em arquivo. */
  echo("      function SalvarMensagensAluno()\n");
  echo("      {\n");
  echo("        document.frmMsg.action = \"salvar_falas_aluno.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("        document.frmMsg.submit();\n");
  echo("      }\n\n");

  if (($e_formador)&&($AcessoAvaliacao))
  {
    echo("  function AvaliarAluno(funcao)\n");
    echo("  {\n");
    echo("   window_handle = window.open(\"../avaliacoes/avaliar_atividade.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_aluno=\"+funcao,\"AvaliarParticipante\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("  window_handle.focus();\n");
    echo("  }\n");
  }

  echo("</script>\n");

  include("../menu_principal.php");

  echo("<td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* 1 - Bate-Papo */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 43 - Ver Falas de Participante */
  echo(" - Ver Falas de Participantes</h4>\n");

  echo("<br>\n");

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

  echo("      <form name=frmMsg action=\"\" method=post>\n");
  echo("        <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("        <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  echo("        <input type=hidden name=cod_assunto value=".$cod_assunto.">\n");
  echo("        <input type=hidden name=cod_aluno value=".$cod_aluno.">\n");
  //echo("        <input type=hidden name=cod_sessao value=".$cod_sessao.">\n");

  echo("      <ul class=\"btAuxTabs\">\n");

//   if (!isset($SalvarEmArquivo))
//   {
//     /* 13 - Fechar (ger) */
//     echo("        <li><span title=\"Fechar\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
//   }

  /* 27 - Ver Sessões Realizadas */
  echo("        <li><span title=\"Ver Sessões Realizadas\" onClick=\"document.location='ver_sessoes_realizadas.php?".RetornaSessionID()."&amp;cod_curso=".$cod_curso."';\">".RetornaFraseDaLista($lista_frases,27)."</span></li>\n");

  if (!isset($SalvarEmArquivo))
  {
    /* 50 - Salvar em Arquivo (geral) */
    echo("        <li><span title=\"Salvar em Arquivo\" onClick=\"SalvarMensagensAluno();\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");
  }

    /* 14 - Imprimir (geral) */
  echo("        <li><span title=\"Imprimir\" onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
  echo("      </ul>\n");

  echo("      </form>\n");

  echo("    </td>\n");
  echo("  </tr>\n");

  echo("  <tr>\n");
  echo("    <td>\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  /* 40 - Assunto da Sessão */
  echo("          <td>".RetornaFraseDaLista($lista_frases,40)."</td>\n");
  /*93 - Participante */
  echo("          <td>".RetornaFraseDaLista($lista_frases,93)."</td>\n");
  echo("        </tr>\n");
  echo("        <tr>\n");

 // $linha=RetornaSessao($sock,$cod_sessao);
  $assunto=RetornaAssunto($sock,$cod_assunto);

  echo("          <td>".$assunto."</td>\n");

  $nome_aluno=NomeUsuario($sock, $cod_aluno);

  if (!isset($SalvarEmArquivo))
    echo("          <td><a onClick=return(OpenWindowLink(".$cod_aluno."));>".$nome_aluno."</a></td>\n");
  else
    echo("          <td>".$nome_aluno."</td>\n");

  echo("        </tr>\n");

  $lista_falas=RetornaConversaAluno($sock,$cod_assunto,$cod_aluno);

  if (count($lista_falas)>0)
  {
    foreach ($lista_falas as $cod => $linha)
    {
      echo("        <tr>\n");
      echo("          <td>\n");
      echo("<font class=textsmall>(".Unixtime2Hora($linha['Data']).")</font>\n");
      if ($cod_usuario == $linha['cod_usuario'])
      {
        print "<font class=text color=#007700>";
      }
      if ($cod_usuario == $linha['cod_usuario_r'])
      {
        print "<font class=text color=#000099>";
      }
      else
        print "<font class=text>";
      echo("<b>".$linha['Apelido']."</b> ".RetornaFraseDaLista($lista_frases,$linha['cod_texto_fala'])." ");
      if ($linha['cod_texto_fala']>8) /* Não é entrada ou saída... */
        echo("<b>".$linha['Apelido']."</b>: ".$linha['Mensagem']);
      echo("</font>\n");

      echo("          </td>\n");
      echo("        </tr>\n");
    }
  }

  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabelao
  echo("</table>\n");

  include("../tela2.php");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
