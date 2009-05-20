<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/avaliarcurso/avaliar_curso.php

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
  ARQUIVO : administracao/avaliarcurso/avaliar_curso.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("avaliarcurso.inc");
  
  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");
  
  
  /* Inicio do JavaScript */
  echo("<script type=text/javascript>\n\n");

  /* *********************************************************************
  Funcao Reload - JavaScript. Carrega novamente a p�gina com novos parametros.
    Entrada: pagina - Nome da p�gina a ser recarregada
    Saida:   Boolean, para controle do onClick;
             true, se nao houver erros no formulario,
             false, se houver.
  */
  echo("    function Reload(pagina)\n");
  echo("    {\n");
  echo("      document.cursos.action=pagina;\n");
  echo("      document.cursos.submit();\n");
  echo("      return false;\n");
  echo("    }\n\n");

  echo("    function Cancela()\n");
  echo("    {\n");
  echo("      document.cursos.action = \"../index_criar_curso.php\";\n");
  echo("      document.cursos.submit();\n");
  echo("    }\n\n");

  echo("    function Iniciar()\n"); 
  echo("    {\n");
  echo("	startList();\n");
  echo("    }\n");

  echo("</script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 244 - Avaliar requisi��es para abertura de cursos */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,244)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <form name=\"cursos\" action=\"avaliar_curso2.php\" method=\"post\">\n");
  if (isset($rej))
    echo("            <input type=\"hidden\" name=\"todos\" value=\".$rej.\" />\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"voltar\" onClick=\"document.location='../administracao/index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 98 - Criar Curso */
  echo("                  <li><span style=\"href: #\" title=\"Criar Curso\" onClick=\"document.location='../administracao/criar_curso.php'\">".RetornaFraseDaLista($lista_frases,98)."</span></li>\n");
  /* 244 - Avaliar requisi��es para abertura de cursos */
  echo("                  <li><span style=\"href: #\" title=\"Avaliar requisi��es para abertura de cursos\" onClick=\"document.location='avaliar_curso.php'\">".RetornaFraseDaLista($lista_frases,244)."</span></li>\n");
  echo("                  </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");


  if (isset($rej))
    $cursos=RetornaTodosCursos($sock);
    // $todos!=1 ou !isset($todos) 
  else
    $cursos=RetornaCursosRequisicao($sock);

  if (count($cursos)>0)
  {
    /* 211 - Escolha o curso a ser avaliado:  */
    echo("                  <tr class=\"head\">\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases, 211)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td>\n");
    echo("                      <select size=\"5\" style=\"width: 50%\" name=\"cod\" class=\"input\">\n");

    /* Vari�vel usada para deixar selecionado o primeiro option do select */
    /* Ap�s passar a primeira vez pelo foreach ela vai para 0 e os outros options n�o ser�o selecionados */
    $primeiro=1;
    foreach ($cursos as $cod => $curso)
    {
      if ($curso['avaliado'] == 'R')
        /* 212 -  (rejeitado) */
        $status = RetornaFraseDaLista($lista_frases, 212);
      else
        $status = "";

      if ($primeiro==1)
        echo("                        <option selected value=".$cod.">".$curso['nome_curso'].$status."</option>\n");
      else
        echo("                        <option value=".$cod.">".$curso['nome_curso'].$status."</option>\n");
      $primeiro=0;
    }

    echo("                      </select><br /><br />\n");

    /* 214 - Mostrar cursos rejeitados */
    if (isset($rej))
      echo("                      <input type=\"checkbox\" checked name=\"rej\" onClick=\"Reload('avaliar_curso.php?');\" /> ".RetornaFraseDaLista($lista_frases, 214)."\n");
    else
      echo("                      <input type=\"checkbox\" name=\"rej\" value=1 onClick=\"Reload('avaliar_curso.php?');\" /> ".RetornaFraseDaLista($lista_frases, 214)."\n");

    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            </tr>"); 
    echo("              <td align=\"right\">\n");/* 215 - Avaliar Curso */
    echo("                <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,215)."\" onClick=\"document.cursos.submit();\" type=\"button\" />\n"); 
  }
  else
  {
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    /* 216 - N�o h� nenhum curso a ser avaliado. */
    echo("                ".RetornaFraseDaLista($lista_frases, 216)."\n");
    echo("                <br /><br />\n");

    /* 214 - Mostrar cursos rejeitados */
    if (isset($rej))
      echo("<input type=\"checkbox\" checked name=\"rej\" onClick=\"Reload('avaliar_curso.php');\" /> ".RetornaFraseDaLista($lista_frases, 214)."\n");
    else
      echo("<input type=\"checkbox\" name=\"rej\" value=\"1\" onClick=\"Reload('avaliar_curso.php');\" /> ".RetornaFraseDaLista($lista_frases, 214)."\n");
      
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
  } 
    
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
