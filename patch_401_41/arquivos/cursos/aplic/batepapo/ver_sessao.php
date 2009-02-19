<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/batepapo/ver_sessao.php

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
  ARQUIVO : cursos/aplic/batepapo/ver_sessao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("batepapo.inc");
  include("avaliacoes_batepapo.inc");

  $cod_ferramenta=10;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* tela topo faz isso
  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,10);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,10); */

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  $e_formador=EFormador($sock,$cod_curso,$cod_usuario);

  $linha=RetornaSessao($sock,$cod_sessao);

  $assunto=$linha['Assunto'];
  $data_inicio=$linha['DataInicio'];
  $data_fim=$linha['DataFim'];
  $status=$linha['Status'];

  echo("<script type=\"text/javascript\" language=javascript>\n");

  echo("var window_handle;\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  }\n");

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
  echo("    window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  if (($e_formador)&&($AcessoAvaliacao))
  {
    echo("  function AvaliarAlunos(funcao)\n");
    echo("  {\n");
    echo("    window_handle = window.open(\"../avaliacoes/avaliar_participantes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+funcao,\"AvaliarParticipantes\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
    echo("  window_handle.focus();\n");
    echo("  }\n");
  }

  echo("</script>\n");
  echo("</head>\n");
  echo("<body onLoad=\"Iniciar();\">\n");

  echo("<br/><br/>\n");
  /* 1 - Bate-Papo */
  echo("<h4>".RetornaFraseDaLista($lista_frases,1));
  /* 43 - Ver sessão */
  echo(" - ".RetornaFraseDaLista($lista_frases,43)."</h4>");
  echo("<br/>\n");

  $cod_pagina=6;
  if(($AcessoAvaliacao)&&($e_formador))/*Pare exibir a ajuda de avaliações*/
  $cod_pagina=12;

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");

  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");

  echo("      <form action=salvar_sessao.php method=get>\n");
  //echo(RetornaSessionIDInput());
  echo("      <input type=hidden name=cod_curso value=".$cod_curso." />\n");
  echo("      <input type=hidden name=cod_sessao value=".$cod_sessao." />\n");
  echo("      </form>\n");

  if ((!isset($SalvarEmArquivo)) && ($e_formador) && ($status=='A') && ($AcessoAvaliacao))
  {
    // 5 - (Sessão não agendada)
    if (strcmp($assunto, RetornaFraseDaLista($lista_frases,5)) != 0)
    {
      if (BatePapoEhAvaliacao($sock,$assunto,$data_inicio,$data_fim))
      {
        $assuntos=explode("<br/>", $assunto);
        // Caso a sessão tenha mais de um assunto, pode-se acontecer de algumas das sessões sejam avaliadas e outras não.
        // Portanto só permitimos que ele avalie pela tela anterior
        if (count($assuntos)==1) {
           $cod_assunto=RetornaCodAssunto($sock,$assunto,$data_inicio,$data_fim);
           $cod_avaliacao=RetornaCodAvaliacao($sock,$cod_assunto);
           echo("      <ul class=\"btAuxTabs\">\n");
           /* 97 - Avaliar Participantes */
           echo("        <li><span title=\"Avaliar Participantes\" onClick=\"AvaliarAlunos(".$cod_avaliacao.");\">".RetornaFraseDaLista($lista_frases,97)."</span></li>\n");
           echo("      </ul>\n");
        }
      }
    }
  }

  echo("      <ul class=\"btAuxTabs\">\n");
  /* 13 - Fechar (Ger) */
  echo("        <li><span title=\"Fechar\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral, 13)."</span></li>\n");
  if (!isset($SalvarEmArquivo))
  {
    /* 46 - Salvar em Arquivo */
    echo("        <li><span title=\"Salvar em Arquivo\" onClick=\"document.location.href='salvar_sessao.php?cod_curso=".$cod_curso."&amp;cod_sessao=".$cod_sessao."&amp;".RetornaSessionID()."';\">".RetornaFraseDaLista($lista_frases,46)."</span></li>\n");
  }
  /* 14 - Imprimir */
  echo("        <li><span title=\"Imprimir\" onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");

  echo("  <tr>\n");
  echo("    <td valign=\"top\">\n");
  echo("      <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  /* 40 - Assunto da Sessão */
  echo("          <td>".RetornaFraseDaLista($lista_frases,40)."</td>\n");
  /* 29 - Início */
  echo("          <td>".RetornaFraseDaLista($lista_frases,29)."</td>\n");
  /* 30 - Fim */
  echo("          <td>".RetornaFraseDaLista($lista_frases,30)."</td>\n");
  echo("        </tr>\n");

  echo("        <tr>\n");
  echo("          <td><img src=\"../imgs/icForum.gif\" border=\"0\" />".$linha['Assunto']."</td>\n");
  echo("          <td>".Unixtime2DataHora($linha['DataInicio'])."</td>\n");
  echo("          <td>".Unixtime2DataHora($linha['DataFim'])."</td>\n");
  echo("        </tr>\n");

  $lista=RetornaListaApelidos($sock,$cod_sessao);

  echo("        <tr class=\"head\">\n");
  /* 44 - Participantes */
  echo("          <td colspan=3>".RetornaFraseDaLista($lista_frases,44)."</td>\n");
  echo("        </tr>\n");

  if (count($lista)>0)
    foreach ($lista as $cod => $linha)
    {
      echo("        <tr>\n");
      echo("          <td colspan=3 align=left>\n");
      if ($cod>0)
      {
        echo($linha." ('<a href=# onclick=OpenWindowLink(".$cod.");>\n");
        echo(NomeUsuario($sock,$cod)."</a>')\n");
      }
      else
      {
        echo($linha." ('".NomeUsuario($sock,$cod)."')\n");
      }
      echo("          </td>\n");
      echo("        </tr>\n");
    }

  $lista_conversas=RetornaConversaSessao($sock,$cod_sessao);

  echo("        <tr class=\"head\">\n");
  /* X - Mensagens */
  echo("          <td colspan=3>Mensagens</td>\n");
  echo("        </tr>\n");

  if (count($lista_conversas)>0)
  {
    foreach ($lista_conversas as $cod => $linha)
    {
      echo("        <tr>\n");
      echo("          <td colspan=3 align=left>\n");
      echo("(".Unixtime2Hora($linha['Data'])."): \n");
      if ($cod_usuario == $linha['cod_usuario'])
      {
        print "<font color=\"#2a6686\">";
      }
      else if ($cod_usuario == $linha['cod_usuario_r'])
      {
        print "<font color=\"#2a6686\">";
      }
      else
      {
        print "<font color=\"#2a6686\">";
      }
      $apelido  = LimpaConteudo($linha['Apelido']);
      $mensagem = LimpaConteudo($linha['Mensagem']);
      $apelidoR = LimpaConteudo($linha['ApelidoR']);
      echo("<b>".$apelido."</b> ".RetornaFraseDaLista($lista_frases, $linha['cod_texto_fala'])." ");
      if ($linha['cod_texto_fala'] > 8)
        echo("<b>".$apelidoR."</b>: ".$mensagem."\n");
      echo("</font>\n");

      echo("          </td>\n");
      echo("        </tr>\n");
    }
  }
  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabelão
  echo("</table>\n");

  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>

