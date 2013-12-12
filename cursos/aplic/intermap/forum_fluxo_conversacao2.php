<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/forum_fluxo_conversacao2.php

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
  ARQUIVO : cursos/aplic/intermap/forum_fluxo_conversacao2.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("intermap.inc");
  include("forum.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  echo("<script language=\"javascript\">\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

//  echo("  window.resizeTo(640,480);\n");
  echo("  function OpenWindowPerfil(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function AbreMensagem(cod_msg)\n");
  echo("  {\n");
  echo("    window.open(\"forum_ver_mensagem.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_forum=".$cod_forum."&cod_msg=\"+cod_msg,\"DisplayMensagem\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
  /* 51- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n");

  echo("<body onLoad=\"Iniciar();\">\n");
  echo("<br><br>\n");

  /* 1 - Intermap */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 28 - F�rum de Discuss�o */
  echo(" - ".RetornaFraseDaLista($lista_frases,28));
  /* 27 - Fluxo de Conversa��o */
  echo(" - ".RetornaFraseDaLista($lista_frases,27)."</h4>\n");

  if ($exibir=="intervencao")
    $cod_pagina=21;
  else
    $cod_pagina=20;

  echo("<br>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  /* 13 - Fechar (geral) */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  /* 14 - Imprimir (geral) */
  echo("        <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,14)."\" onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $fluxo=RetornaFluxoConversacao($sock,$cod_forum);

  if (count($fluxo)>0)
  {
    $lista_usuarios   = RetornaAlunosFormadoresCodUsuarioNome($sock, $cod_curso);
    $lista_formadores = RetornaListaCodUsuarioFormador($sock, $cod_curso);

    if ($exibir=="estrutura")
    {
      echo("        <tr class=\"head\">\n");
      // 9 - Assunto das mensagens
      echo("          <td>".RetornaFraseDaLista($lista_frases,9)."</td>\n");
      foreach($lista_usuarios as $cod_usu => $nome)
      {
        if ($lista_formadores[$cod_usu]==$cod_usu)
          $cor="#2a6686";
        else
          $cor="FFE0C0";
        echo("          <td><a href=\"#\" onClick=\"return(OpenWindowPerfil(".$cod_usu."));\"><font color=\"".$cor."\">".$nome."</font></a></td>\n");
      }
      echo("        </tr>\n");
      foreach ($fluxo as $cod_msg => $linha)
      {
        echo("        <tr>\n");
        echo("          <td>".Space2Nbsp($linha['titulo'])."</td>\n");
        $respostas=AgrupaArrayRespostasPorUsuario($linha['respostas']);
        foreach($lista_usuarios as $cod_usu => $nome)
        {
          echo("          <td>");
          if ($cod_usu == $linha['cod_usuario']) {
            echo("            <a href=\"#\" onclick=\"return(AbreMensagem(".$cod_msg."));\">\n");
            echo("              <img src=\"figuras/poucas.gif\" width=\"7\" height=\"4\" border=\"0\">\n");
            echo("            </a>\n");
          }
          if (count($respostas[$cod_usu])>0) {
            foreach($respostas[$cod_usu] as $cod_msg => $data) {
              echo("            <a href=\"#\" onclick=\"return(AbreMensagem(".$cod_msg."));\">\n");
              echo("              <img src=\"figuras/muitas.gif\" width=\"7\" height=\"4\" border=\"0\">\n");
              echo("            </a>\n");
            }
          }
          echo("          </td>\n");
        }
        echo("        </tr>\n");
      }
    }
    else // $exibir=="intervencao"
    {
      $lista_usuarios=RetornaListaCodUsuarioNome($sock, $cod_curso);
      $lista_formadores=RetornaListaCodUsuarioFormador($sock, $cod_curso);

      echo("        <tr class=\"head\">\n");
      // 10 - Assuntos
      echo("          <td>".RetornaFraseDaLista($lista_frases,10)."</td>\n");
      // 41 - Mensagens
      echo("          <td>".RetornaFraseDaLista($lista_frases,41)."</td>\n");
      echo("        </tr>\n");
      foreach ($fluxo as $cod_msg => $linha)
      {
        echo("        <tr>\n");
        echo("          <td>".Space2Nbsp($linha['titulo'])."</td>\n");

        $respostas=AgrupaArrayRespostasPorData($linha['respostas']);
        echo("          <td>\n"); 
        if ($lista_formadores[$linha['cod_usuario']]==$linha['cod_usuario']) {
          echo("            <a href=\"#\" onclick=\"return(AbreMensagem(".$cod_msg."));\">\n");
          echo("              <img src=\"figuras/formador.gif\" width=\"7\" height=\"4\" border=\"0\">\n");
          echo("            </a>\n");
        }
        else {
          echo("            <a href=\"#\" onclick=\"return(AbreMensagem(".$cod_msg."));\">\n");
          echo("              <img src=\"figuras/aluno.gif\" width=\"7\" height=\"4\" border=\"0\">\n");
          echo("            </a>\n");
        }
        if (count($respostas)>0)
        {
          foreach($respostas as $data => $linha1)
          {
            if ($lista_formadores[$linha1['cod_usuario']]==$linha1['cod_usuario']) {
              echo("            <a href=\"#\" onclick=\"return(AbreMensagem(".$linha1['cod_msg']."));\">\n");
              echo("              <img src=\"figuras/formador.gif\" width=\"7\" height=\"4\" border=\"0\">\n");
              echo("            </a>\n");
            }
            else {
              echo("            <a href=\"#\" onclick=\"return(AbreMensagem(".$linha1['cod_msg']."));\">\n");
              echo("              <img src=\"figuras/aluno.gif\" width=\"7\" height=\"4\" border=\"0\">\n");
              echo("            </a>\n");
            }
          }
        }
        echo("          </td>\n");
        echo("        </tr>\n");
      }
    }
  }
  else
  {
    echo("        <tr>\n");
    // 48 - N�o h� nenhuma mensagem neste f�rum
    echo("          <td>".RetornaFraseDaLista($lista_frases,48)."</td>\n");
    echo("        </tr>\n");
  }

  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");

  if ($exibir=="estrutura")
  {
    // 36 - Legenda:
    echo("      <b>".RetornaFraseDaLista($lista_frases,36)."</b>\n");
    // 40 - Mensagem inicial de um assunto
    echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=\"figuras/poucas.gif\" width=\"7\" height=\"4\" border=\"0\"> ".RetornaFraseDaLista($lista_frases,40));
    // 62 - Resposta de mensagens iniciais
    echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=\"figuras/muitas.gif\" width=\"7\" height=\"4\" border=\"0\"> ".RetornaFraseDaLista($lista_frases,62));
  }
  else
  {
    // 36 - Legenda:
    echo("      <b>".RetornaFraseDaLista($lista_frases,36)."</b>\n");
    // 45 - Mensagens de formadores
    echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=\"figuras/formador.gif\" width=\"7\" height=\"4\" border=\"0\"> ".RetornaFraseDaLista($lista_frases,45));
    // 44 - Mensagens de alunos
    echo("      &nbsp;&nbsp;&nbsp;&nbsp;<img src=\"figuras/aluno.gif\" width=\"7\" height=\"4\" border=\"0\"> ".RetornaFraseDaLista($lista_frases,44));
  }

  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>
