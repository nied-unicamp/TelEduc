<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/contato.php

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
  ARQUIVO : pagina_inicial/contato.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");
  
  $pag_atual = "contato.php";
  include("../topo_tela_inicial.php");

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 10 - Contato */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,10)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 29 - Lista de Responsáveis */
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases_adm,29)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 32 - Nome do responsável: */
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases_adm,32)."</td>\n");
  /* 43 - E-mail */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,43)."</td>\n");
  echo("                  </tr>\n");

  $query = "select nome, email from Contatos";

  $res=Enviar($sock,$query);
  $num=RetornaNumLinhas($res);
  $lista=RetornaArrayLinhas($res);

  if ($num>0)
  {
    foreach($lista as $cod => $linha)
    {
        echo("                  <tr>\n");
        echo("                    <td colspan=\"2\"><b>".$linha[0]."</b></td>\n");
        echo("                    <td ><a href=mailto:".$linha[1].">".$linha[1]."</a></td>\n");
        echo("                  </tr>\n");
    }
  }
  else
  {
    /* 44- Não há nenhum responsável cadastrado. */
    echo("                  <tr>\n");
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,44)."</td>\n");
    echo("                  </tr>\n");
  }

  echo("                  <tr class=\"head\">\n");
  //169 - Instituicao
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,169)."</td>\n");
  echo("                  </tr>\n");

  $query = "select nome, informacoes, link from Instituicao";
  $res = Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $lista = RetornaArrayLinhas($res);
    foreach($lista as $cod => $linha)
    {
      $linha[1]=Enter2BR($linha[1]);
      echo("                  <tr class=\"head01\">\n");
      //32 - Nome (configurar)
      echo("                    <td width=\"30%\">".RetornaFraseDaLista($lista_frases_configurar,32)."</td>\n");
      //53 - Informações
      echo("                    <td width=\"40%\">".RetornaFraseDaLista($lista_frases,53)."</td>\n");
      //170 -Link
      echo("                    <td width=\"30%\">".RetornaFraseDaLista($lista_frases,170)."</td>\n");
      echo("                  </tr>\n");
      echo("                  <tr>\n");
      echo("                    <td><b>".$linha[0]."</b></td>\n");
      echo("                    <td>".$linha[1]."</td>\n");

      if ($linha[2] != "") 
      {
        if(preg_match("/^http:\/\/(.*)/", $linha[2]))
        {
          echo("                    <td><a href=\"".$linha[2]."\">".$linha[2]."</a></td>\n");
          echo("                  </tr>\n");
        } else
        {
          echo("                    <td><a href=\"http://".$linha[2]."\">http://".$linha[2]."</a></td>\n");
          echo("                  </tr>\n");
        }
     }
     else
      echo("<td>-</td>\n");
      echo("</tr>\n");
    }
  }
  else
  {
    /* 45 - Os dados da instituição não foram cadastrados ainda */
    echo("                  <tr>\n");
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,45)."</td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("         </td>\n");
  echo("       </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>");
  Desconectar($sock);
?>