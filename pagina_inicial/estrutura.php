<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/estrutura.php

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
  ARQUIVO : pagina_inicial/estrutura.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  function Item($nome,$descricao,$i) 
  {
    $impar=$i/2; 
    $impar=(int)$impar * 2; 
    $impar=$i - $impar;

    echo("                  <tr class=\"altColor".$impar."\">\n");
    echo("                    <td align=left>\n");
    echo("                      <p style=\"font-weight:bold;\">".$nome."</p>\n");
    echo("                      <p style=\"text-indent:15px;\">".$descricao."</p>\n");
    echo("                    </td>\n");
    echo("                  </tr>");
    
    $i++;
    return($i);
  }

  $pag_atual = "estrutura.php";
  include("../topo_tela_inicial.php");

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 3 - Estrutura do Ambiente */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,4)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">   \n");
  echo("                    <td align=left>".RetornaFraseDaLista($lista_frases,33)."</td>\n");
  echo("                  </tr>   \n");
  echo("                  <tr>   \n");
  /* 34 - Os recursos do ambiente est�o distribu�dos de acordo com o perfil de seus  usu�rios: alunos e formadores */
  echo("                    <td align=left><p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,34)."</p></td>\n");
  echo("                  </tr>   \n");
  echo("                  <tr class=\"head\">   \n");
  /* 35 - Recursos dispon�veis para alunos e formadores */
  echo("                    <td align=left>".RetornaFraseDaLista($lista_frases,35)."</td>\n");
  echo("                  </tr>   \n");
  
  /*********************************************
    Rotina de Mostrar conte�do das Ferramentas... 
  *********************************************/
  $lista_ferramentas=RetornaListaDeFrases($sock,-4);

  $i=0;

  $lista_impressao=RetornaCodigoTextosFerramentas($sock);
 
  foreach ($lista_impressao as $cod=>$linha)
    if ($linha[2]!=19) /* Intermap */
      $i=Item(RetornaFraseDaLista($lista_ferramentas,$linha[0]),RetornaFraseDaLista($lista_ferramentas,$linha[1]),$i);
      
  $impar=$i/2; 
  $impar=(int)$impar * 2; 
  $impar=$i - $impar;

  echo("                  <tr class=\"altColor".$impar."\">\n");
  echo("                    <td align=left>\n");
  echo("                      <p style=\"font-weight:bold;\">".RetornaFraseDaLista($lista_ferramentas,61)."</p>\n");
  echo("                      <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_ferramentas,62)."</p>\n");
  echo("                    </td>\n");
  echo("                  </tr>");

  echo("                  <tr class=\"head\">\n");
  /* 36 - Recursos dispon�veis apenas para formadores */
  echo("                    <td align=left>".RetornaFraseDaLista($lista_frases,36)."</td>\n");
  echo("                  </tr>   \n");

  $i=Item(RetornaFraseDaLista($lista_ferramentas,35),RetornaFraseDaLista($lista_ferramentas,36),$i);
  $i=Item(RetornaFraseDaLista($lista_ferramentas,37),RetornaFraseDaLista($lista_ferramentas,38),$i);
  $i=Item(RetornaFraseDaLista($lista_ferramentas,39),RetornaFraseDaLista($lista_ferramentas,40),$i);

  echo("                  <tr class=\"head\">\n");
  /* 36 - Recursos dispon�veis apenas para formadores */
  echo("                    <td align=left>".RetornaFraseDaLista($lista_ferramentas,63)."</td>\n");
  echo("                  </tr>   \n");
  
  $i=Item(RetornaFraseDaLista($lista_ferramentas,64),RetornaFraseDaLista($lista_ferramentas,65),$i);
  $i=Item(RetornaFraseDaLista($lista_ferramentas,66),RetornaFraseDaLista($lista_ferramentas,67),$i);
  $i=Item(RetornaFraseDaLista($lista_ferramentas,68),RetornaFraseDaLista($lista_ferramentas,69),$i);
  $i=Item(RetornaFraseDaLista($lista_ferramentas,70),RetornaFraseDaLista($lista_ferramentas,71),$i);
  
  echo("                  <tr class=\"head\">\n");
  /* 38 - Autentica��o de acesso */
  echo("                    <td align=left>".RetornaFraseDaLista($lista_frases,38)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  /* 39 - O ambiente possui um esquema de autentica��o de acesso aos cursos. Para que formadores e alunos tenham acesso a um curso s�o necess�rios identifica��o pessoal e senha que lhes s�o solicitadas sempre que tentarem efetuar o acesso. Essas senhas s&atilde;o fornecidas a eles quando se cadastram no ambiente */
  echo("                    <td align=left><p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,39)."</p></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 40 - O TelEduc � um software livre; voc� pode redistribu�-lo e/ou modific�-lo sob os termos da */
  /* 41 - vers�o 2, como publicada pela */
  echo("                    <td align=left>\n");
  echo("                      <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,40)." <a href=../cursos/aplic/estrutura/gpl.txt target=GNU-GPL>GNU General Public License</a> ".RetornaFraseDaLista($lista_frases,41)." <b><i>Free Software Foundation</i></b></p>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>
