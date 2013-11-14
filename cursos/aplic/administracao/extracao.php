<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/extracao.php

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
  ARQUIVO : cursos/aplic/administracao/extracao.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  //$cod_pagina_ajuda = 1;

  include("../topo_tela.php");

   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* Conta quantas ferramentas existem. */
  $total_ferramentas = count($tela_lista_ferramentas);

  if(!EFormador($sock,$cod_curso,$cod_usuario))
  {
    include("../menu_principal.php");

    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

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

  /*Funcao JavaScript*/
  echo("  <script type=\"text/javascript\">\n\n");

  echo("    function Iniciar() {\n");
  echo("      startList();\n");
  echo("    }\n\n");

  echo("    function CancelaExtracao()\n");
  echo("    {\n");
  echo("      document.frmExtracao.action = \"administracao.php?cod_curso=".$cod_curso."\";\n");
  echo("      document.frmExtracao.submit();\n");
  echo("    }\n\n");

  echo("  </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    
  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1));
  /* 212 - Agendar Extra��o do Curso */
  $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 212)."</h4>\n";
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
  
  echo("          <form name=\"frmExtracao\" method=\"post\" action=\"extracao2.php\">\n");
  echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("            <input type=\"hidden\" name=\"action\"    value=\"extrairCurso\">\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=0\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* 214 - Selecione o usu�rio para o qual a extra��o ser� realizada: */
  echo("                    <td colspan=4>".RetornaFraseDaLista($lista_frases,214)."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr class=\"head01\">\n");
  /*239 - Usuario */
  echo("                    <td align=left colspan=4><b>".RetornaFraseDaLista($lista_frases, 239)."</b></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td align=left colspan=4>\n");

  /* Primeiro retornaremos todos os usuarios ativos exceto visitantes e colaboradores */
  $query  = "select UC.cod_usuario, U.nome ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where UC.cod_usuario >= 0 and ";
  $query .=       "UC.cod_curso = '".$cod_curso."' and ";
  $query .=       "(binary UC.tipo_usuario = 'F' or binary UC.tipo_usuario = 'A') ";
  $query .= "order by nome";
  $res=Enviar($sock, $query);

  while ($linha = RetornaLinha($res)) {
    $lista_usuarios[$linha['cod_usuario']]['nome'] = $linha['nome'];
  }

  if (count($lista_usuarios)>0)
  {
    echo("                      <select class=\"input\" name=\"cod_usu[]\">\n");
  
      foreach ($lista_usuarios as $cod_usu => $linha)
        echo("                        <option value=".$cod_usu.">".$linha['nome']."</option>\n");

    echo("                      </select><br>\n");
  }
  else
    /* 104 - Nenhuma pessoa registrada */
    echo("                      ".RetornaFraseDaLista($lista_frases,104)."</font><br>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* 215 - Selecione as ferramentas que ser�o extra�das com o curso: */
  echo("                    <td colspan=4>".RetornaFraseDaLista($lista_frases,215)."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr class=\"head01\">\n");
  /*232 - Ferramentas */
  echo("                    <td align=left colspan=4><b>".RetornaFraseDaLista($lista_frases,232)."</b></td>\n");
  echo("                  </tr>\n");

  /* Lista as ferramentas dispon�veis no curso, ou seja, que foram selecionadas na */
  /* ferramenta 'escolher ferramentas'.                                            */
  $ferramentas_curso = RetornaFerramentasCurso($sock);

  $k=0;
  $total_fer_ordenadas = count($tela_ordem_ferramentas);
  for($i=0;$i<$total_fer_ordenadas;)
  {
    for($count=0;$count<2;$count++)
    {
      $cod_ferramenta = $tela_ordem_ferramentas[$i]['cod_ferramenta'];
      if($cod_ferramenta > 0)
        $status = $ferramentas_curso[$cod_ferramenta]['status'];

      if (($cod_ferramenta > 0) && ($status != 'D'))
      {
        if (($cod_ferramenta != 23) && ($cod_ferramenta != 19) && ($cod_ferramenta != 17))
        {
          if($k==0)
            echo("                  <tr>\n");
          if (($cod_ferramenta == 13) || ($cod_ferramenta == 22))
          {
            echo("                    <td width=\"2%\"><input type=\"checkbox\" name=\"ferramentas[]\" value=".$cod_ferramenta);
            echo(" checked='checked' onMouseOut='this.checked=true'></td>\n");
            echo("                    <td align=left>".RetornaFraseDaLista($lista_frases_menu, $tela_lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])." *</td>\n");
          }
          else
          {
            echo("                    <td width=\"2%\"><input type=\"checkbox\" name=\"ferramentas[]\" value=".$cod_ferramenta);
            echo(" checked></td>\n");

            if ($cod_ferramenta == 18) {
              echo("                    <td align=left>".RetornaFraseDaLista($lista_frases_menu, $tela_lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])." **</td>\n");
            } else {
              echo("                    <td align=left>".RetornaFraseDaLista($lista_frases_menu, $tela_lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])."</td>\n");
            }
          }
          $k++;
          if($k==2)
          {
            echo("                  </tr>\n");
            $k=0;
          }
        }
      }
      $i++;
    }
  }

  if($k != 0)
  {
    echo("                    <td colspan=2></td>");
    echo("                  </tr>\n");
  }
  echo("                </table>\n");

  /* 216 - * S�o necess�rias pois outras ferramentas possuem links que apontam para elas */
  echo("                ".RetornaFraseDaLista($lista_frases, 216)."<br>\n");

  /* 226 - ** Essa ferramenta somente ser� extra�da caso o usu�rio selecionado seja um formador. */
  echo("                ".RetornaFraseDaLista($lista_frases, 226)." (Desabilitada a extra&ccedil;&atilde;o da ferramenta acessos temporariamente)<br><br>\n");
  
  /* 7 - Confirmar */
  echo("                <div align=right><input type=\"submit\" class=\"input\" style=\"width:85px\" value='".RetornaFraseDaLista($lista_frases_geral, 7)."'></div>\n");
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
