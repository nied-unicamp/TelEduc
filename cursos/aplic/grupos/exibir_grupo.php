<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/grupo/exibir_grupo.php

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
  ARQUIVO : cursos/aplic/grupo/exibir_grupo.php
  ========================================================== */

  /*
  ==================
  Programa Principal
  ==================
  */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include ("grupos.inc");

  $cod_ferramenta = 12;
  include("../topo_tela.php");

  echo(" <script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n");

  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n");

  echo("    </script>\n");
  
  /*
  =============================
  Retorno ao programa principal
  =============================
  */
  
  echo("  </head>\n");
  echo("  <body bgcolor=\"white\" onload=\"startList(); self.focus();\">\n");
  echo("    <a name=\"topo\"></a>\n");
  
  if (EVisitante($sock, $cod_curso, $cod_usuario))
  {
    /* 1 - Grupos */
    $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
    /* 504 - �ea restrita a alunos e formadores */
    $cabecalho.=" - ".RetornaFraseDaLista($lista_frases_geral, 504)."</h4>";
    echo("    <br /><br />".$cabecalho."\n");
    echo("    <br />\n");
    echo("    <ul class=\"btAuxTabs\">\n");
    echo("      <li>\n");
    /* G 13 - Fechar */
    echo("        <span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span>\n");
    echo("      </li>\n");
    echo("    </ul>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit();
  }

  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
  echo("      <tr>\n");
  echo("        <td></td>\n");
  echo("        <td id=\"tabelaInterna\" valign=\"top\">\n");
  /* 1 - Perfil */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
  /* 12 - Componentes do Grupo */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,12)." ".RetornaNomeGrupo($sock, $cod_grupo)."</h4>";

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\" style=\"top:42px;\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <br /><br />".$cabecalho);
  echo("          <br />\n");
  echo("          <table border=\"0\" width=\"100%\" cellspacing=\"2\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td align=\"center\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 13(ger) - Fechar */
  echo("                  <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 68 - Componente */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,68)."</td>\n");
  echo("                    <td width=\"15%\">\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  if($cod_grupo!=-1){
    $lista = RetornaUsuariosNoGrupo($sock,$cod_curso,$cod_grupo);
  }else{
    $lista = RetornaUsuariosSemGrupo($sock, $cod_curso);
  }

  if($lista==""){
    /* 31 - Não há Componentes neste grupo.*/
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                ".RetornaFraseDaLista($lista_frases,31));
    echo("              </td>\n");
    echo("            </tr>\n");
  }else{

    $i = 0;

    foreach($lista as $cod => $linha){

      if ($linha['tipo_usuario'] == 'A')
        /* 18 - Aluno */
        $tmp = RetornaFraseDaLista($lista_frases,18);
      else if ($linha['tipo_usuario'] == 'F')
        /* 19 - Formador */
        $tmp = RetornaFraseDaLista($lista_frases,19);
      else
        $tmp = "Erro";  //usuários desligados

      if(($linha['tipo_usuario'] == 'A') || ($linha['tipo_usuario'] == 'F')){ 
        echo("            <tr class=\"altColor".(($i++)%2)."\">\n");
        echo("              <td class=\"alLeft\">\n");
        echo("                <span>".$tmp." ".$linha['nome']."</span>\n");
        echo("              </td>\n");
        echo("              <td class=\"botao2\">\n");
        echo("                <ul>\n");
        /* 70 - Ver Perfil */
        echo("                  <li><span onclick=\"OpenWindowPerfil(".$linha['cod_usuario'].")\">".RetornaFraseDaLista($lista_frases,70)."</span></li>\n");
        echo("                </ul>\n");
        echo("              </td>\n");
        echo("            </tr>\n");
      }
    }
  }
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>