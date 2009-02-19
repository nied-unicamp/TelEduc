<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/intermap/batepapo_grafico_periodo2.php

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
  ARQUIVO : cursos/aplic/intermap/batepapo_grafico_periodo2.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("intermap.inc");
  include("batepapo.inc");

  $cod_ferramenta=19;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  if (!isset($visualizar))
    $visualizar="todos";

  echo("<script language=javascript>\n");

  echo("  function Iniciar() \n");
  echo("  { \n");
  echo("    startList(); \n");
  echo("  } \n");

  echo("  function OpenWindowPerfil(funcao)\n");
  echo("  {\n");
  echo("    window.open(\"../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("    return(false);\n");
  echo("  }\n");

  echo("  function SalvarEmArquivo()\n");
  echo("  {\n");
  echo("    document.location='salvar_arquivo.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&todos=".$todos."&apresentacao=".$apresentacao."&cod_sessao=".$cod_sessao."&cod_usu=".$cod_usu."&inicio=".$inicio."&fim=".$fim."&agrupar=".$agrupar."&nome_arquivo=batepapo_grafico_participante'");
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
  /* 14 - Bate-papo */
  echo(" - ".RetornaFraseDaLista($lista_frases,14));

  if ($apresentacao=="grafico")
    /* 32 - Gr�fico por Participante */
    echo(" - ".RetornaFraseDaLista($lista_frases,32)."</h4>\n");
  else
    /* 69 - Tabela por Participante */
    echo(" - ".RetornaFraseDaLista($lista_frases,69)."</h4>\n");

  if ($apresentacao=="tabela")
    $cod_pagina=27;
  else
    $cod_pagina=26;

  echo("<br>\n");

  $lista_usuarios        = RetornaTodosUsuarios($sock, $cod_curso);
  $lista_usuarios_sessao = RetornaListaApelidos($sock,$cod_sessao);

  echo("<form name=mapa action=batepapo_grafico_participante2.php target=Intermap method=get>\n");
  //echo(RetornaSessionIDInput()."\n");
  echo("<input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("<input type=hidden name=cod_sessao value='".$cod_sessao."'>\n");
  echo("<input type=hidden name=apresentacao value='".$apresentacao."'>\n");

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("  <tr>\n");
  echo("    <td>\n");
  echo("      <ul class=\"btAuxTabs\">\n");
  // 26 - Fechar
  echo("        <li><span title=\"Fechar\" onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases,26)."</span></li>\n");

  /* 14 - Imprimir (geral) */
  echo("        <li><span title=\"Imprimir\" onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");

  if (!isset($SalvarEmArquivo) && $apresentacao=="tabela")
  {
    /* 22 - Salvar Em Arquivo */
    echo("        <li><span title=\"Salvar em Arquivo\" onClick=\"SalvarEmArquivo();\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>\n");
  }
  echo("      </ul>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  echo("  <tr>\n");
  echo("    <td>\n");

  $sessao=RetornaSessao($sock,$cod_sessao);

  echo("      <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("        <tr class=\"head\">\n");
  // 64 - Sess�o:
  echo("          <td>".RetornaFraseDaLista($lista_frases,64)."</td>\n");
  // 78 - Visualizar:
  echo("          <td>".RetornaFraseDaLista($lista_frases,78)."</td>\n");
  echo("        </tr>\n");

  echo("        <tr>\n");
  echo("          <td>\n");
  echo(UnixTime2Data($sessao['DataInicio']));
  // 16 - das
  echo(" - ".RetornaFraseDaLista($lista_frases,16)." ");
  echo(UnixTime2Hora($sessao['DataInicio']));
  // 8 - as 
  echo(" ".RetornaFraseDaLista($lista_frases,8)." ");
  echo(UnixTime2Hora($sessao['DataFim']));
  echo(" - ");
  echo($sessao['Assunto']);
  echo("          </td>\n");

  $sessao=RetornaSessao($sock,$cod_sessao);

  if (!isset($SalvarEmArquivo))
  {
    echo("          <td>\n");
    // 74 - Todos os participantes do curso
    echo("            <input class=g1field type=radio name=visualizar ".($visualizar=="todos"?"checked":"")." value=todos onClick=\"document.mapa.submit();\">".RetornaFraseDaLista($lista_frases,74)."<br>\n");
    // 67 - Somente os que participaram da sess�o
    echo("            <input class=g1field type=radio name=visualizar ".($visualizar=="sessao"?"checked":"")." value=sessao onClick=\"document.mapa.submit();\">".RetornaFraseDaLista($lista_frases,67)."<br>\n");
  }
  else
  {
    echo("          <td>\n");
    if ($visualizar=="todos")
      // 74 - Todos os participantes do curso
      echo("            ".RetornaFraseDaLista($lista_frases,74)."<br>\n");
    else
      // 67 - Somente os que participaram da sess�o
      echo("            ".RetornaFraseDaLista($lista_frases,67)."<br>\n");
    echo("          </td>\n");
  }
  echo("        </tr>\n");

  $lista_formadores = RetornaListaCodUsuarioFormador($sock, $cod_curso);
  $lista_convidados = RetornaCodUsuarioConvidadoComSemInteracao($sock, $cod_curso);
  $lista_visitantes = RetornaCodUsuarioVisitantes($sock, $cod_curso);

  $msgs_qtde=RetornaQtdeMsgsUsuario($sock,$cod_sessao,$lista_usuarios);

  $max_qtde=0;
  if (count($msgs_qtde)>0)
  {
    foreach($msgs_qtde as $cod => $qtde)
      if ($max_qtde<$qtde)
        $max_qtde=$qtde;
  }

  if (count($lista_usuarios)>0)
  {
    echo("        <tr class=\"head01\">\n");
    // 54 - Participante
    echo("          <td>".RetornaFraseDaLista($lista_frases,54)."</td>\n");
    // 61 - Quantidade de Mensagens Enviadas
    echo("          <td>".RetornaFraseDaLista($lista_frases,61)."</td>\n");
    echo("        </tr>\n");

    foreach($lista_usuarios as $cod_usu => $nome)
    {
      // if ($visualizar=="todos" || $msgs_qtde[$cod_usu]>0)
      if ($visualizar=="todos" || isset($msgs_qtde[$cod_usu]))
      {
        if ($i==1)
          echo("        <tr>\n");
        else
          echo("        <tr>\n");
        $i = ($i + 1) % 2;
        echo("          <td>".MontaLinkPerfil($cod_usu,$nome)."</td>\n");
        if ($apresentacao=="tabela")
        {
          if (isset($lista_usuarios_sessao[$cod_usu]))
          {
            echo("          <td>".(int)$msgs_qtde[$cod_usu]."</td>\n");
          }
          else
          {
            // 2 - (N�o participou da sess�o)
            echo("          <td>".RetornaFraseDaLista($lista_frases,2)."</td>\n");
          }
        }
        else
        {
          if (isset($lista_usuarios_sessao[$cod_usu]))
          {
            if ($lista_formadores[$cod_usu]=$cod_usu)
              $imagem = "formador.gif";
            else if ($lista_convidados[$cod_usu] == $cod_usu)
              $imagem = "convidado.jpeg";
            else if ($lista_visitantes[$cod_usu] == $cod_usu)
              $imagem = "visitante.jpeg";
            else
              $imagem = "aluno.gif";
            if($max_qtde != 0)
              echo("          <td><img src=figuras/".$imagem." border=0 height=5 width=".($msgs_qtde[$cod_usu]*400/$max_qtde).">&nbsp;".(int)$msgs_qtde[$cod_usu]."</td>\n");
            else
              echo("          <td>".(int)$msgs_qtde[$cod_usu]."</td>\n");
          }
          else
          {
            // 2 - (N�o participou da sess�o)
            echo("          <td>".RetornaFraseDaLista($lista_frases,2)."</td>\n");
          }
        }
        echo("        </tr>\n");
      }
    }
  }

  // Fim Tabela Interna
  echo("      </table>\n");
  echo("    </td>\n");
  echo("  </tr>\n");
  // Fim Tabel�o
  echo("</table>\n");
  echo("</form>\n");

  Desconectar($sock);

  echo("</body>\n");
  echo("</html>\n");

?>
