<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/remover_extracao.php

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
  ARQUIVO : cursos/aplic/administracao/remover_extracao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  //$cod_pagina_ajuda = 1;

  include("../topo_tela.php");

  /* Conta quantas ferramentas existem. */
  $total_ferramentas = count($tela_lista_ferramentas);

  echo("  <script type=\"text/javascript\">\n\n");

  echo("    function Iniciar() {\n");
  echo("      startList();\n");
  echo("    }\n\n");

  echo("    function CancelaExtracao()\n");
  echo("    {\n");
  echo("      document.frmListExtracao.action = \"administracao.php?".RetornaSessionID());
  echo("&cod_curso=".$cod_curso."\";\n");
  echo("      document.frmListExtracao.submit();\n");
  echo("    }\n\n");

  echo("  </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    /* 1 - Administracao  28 - Area restrita ao formador. */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,28)."</h4>\n");

    /*Voltar*/
    /* 509 - Voltar */
    echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("          <form><input class=\"input\" type=\"button\" value=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onclick=\"history.go(-1);\" /></form>\n");

    echo("        </td>\n");
    echo("      </tr>\n");
  
    include("../tela2.php");
  
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }

  /* 1 - Administração */
  $cabecalho = "          <h4>".RetornaFraseDaLista($lista_frases, 1);
  /* 213 - Listar / Remover Extração do Curso */
  $cabecalho .= "     - ".RetornaFraseDaLista($lista_frases, 213)."</h4>\n";
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $sock2 = Conectar("");
  $sql = "select * from Extracoes_agendadas where cod_curso=".$cod_curso." and extraido=0;";
  $res = Enviar($sock2, $sql);
  $lista_extracoes = RetornaArrayLinhas($res);
  $num_linhas = RetornaNumLinhas($res);
  Desconectar($sock2);

  echo("          <form name=\"frmListExtracao\" method=\"post\" action=\"remover_extracao2.php\">\n");
  echo("            <input type=\"hidden\" name=cod_curso value=".$cod_curso.">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"#\" onclick=\"CancelaExtracao();\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 221 - Usuário(s) para o(s) qual(is) a extração do curso será feita: */
  echo("                    <td>".RetornaFraseDaLista($lista_frases, 221)."</td>\n");
  echo("                  </tr>\n");

  $sock = Conectar($cod_curso);


  if($num_linhas > 0)
  {
    echo("                  <tr class=\"head01\">\n");
    echo("                    <td width=\"2\">&nbsp;</td>\n");
    /* 239 - Usuario */
    echo("                    <td>".RetornaFraseDaLista($lista_frases, 239)."</td>\n");
    /* 45 - Ferramenta */
    echo("                    <td>".RetornaFraseDaLista($lista_frases, 45)."</td>\n");
    echo("                  </tr>\n");
    foreach($lista_extracoes as $lista => $extracao)
    {
      $selecionados = $extracao['cod_usuario'];

      echo("                  <tr>\n");
      echo("                    <td><input type=\"checkbox\" name=\"selecionados[]\" value=\"".$selecionados."\"></td>\n");
      echo("                    <td>".NomeUsuario($sock, $selecionados)."</td>\n");

      $vetor_bd_ferramentas = array("estrutura", "dinamica", "agenda", "avaliacoes", "atividades", "material", "leituras", "perguntas", "exercicios", "parada", "mural", "forum", "batepapo", "correio", "grupos", "perfil", "diario", "portfolio", "acessos", "intermap");
      $vetor_cod_ferramentas = array(17, 16, 1, 22, 3, 4, 5, 6, 23, 7, 8, 9, 10, 11, 12, 13, 14, 15, 18, 19);
      $i = 0;

      echo("                    <td>\n");
      foreach($vetor_bd_ferramentas as $vetor => $ferramenta)
      {
        if($extracao[$ferramenta] == 1)
        {
          echo("                    <li>");
          echo(RetornaFraseDaLista($lista_frases_menu, $tela_lista_ferramentas[$vetor_cod_ferramentas[$i]]['cod_texto_nome'])."\n");
          echo("                    </li>\n");
        }
        $i++;
      }
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }
  
    echo("                </table>\n");
    echo("                <div align=\"right\">\n");
    /* 222 - Remover Extrações Selecionadas */
    echo("                  <input type=\"submit\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases, 222)."\">\n");
    echo("                </div>\n");
    //FIM MIOLO
  }
  else
  {
    echo("                  <tr>\n");
    /* 223 - Não há usuários agendados para extração. */
    echo("                    <td>".RetornaFraseDaLista($lista_frases, 223)."</td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
  }

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
