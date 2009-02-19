<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/envia_email.php

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
  ARQUIVO : administracao/infocurso/envia_email.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include $bibliotecas."/geral.inc";
  include "../administracao/admin.inc";
  include "reenvio.inc";

  VerificaAutenticacaoAdministracao();

  include "../topo_tela_inicial.php";

  $lista_frases = RetornaListaDeFrases($sock, -5);

  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\" type=\"text/javascript\">\n");

  echo("      function Iniciar() {\n");
  echo("	 startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  /* Fim do JavaScript */

  echo("</head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");
  echo("    <br /><br />\n");
  /*268 - Enviar email*/
  echo("          <h4>".RetornaFraseDaLista($lista_frases,268)."</h4>\n");
  echo("    <br />\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  $total=count($cod_curso);

  echo("<form action=''>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* G 13 - Fechar */
  echo("                  <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  /*283- E-mail com login e senha enviado para:*/
  echo("                  <tr class=\"head\">\n");
  echo("                    <td>".RetornaFraseDaLista($lista_frases, 283)."</td>\n");
  echo("                  </tr>\n");

  if($codigo==0)
  {
    for( $i=0; $i<$total; $i++)
    {
      $email=MandaMsgcomDados($cod_curso[$i]);
      if($email!=false)
      {

        $v = BuscaInfoCursos($cod_curso[$i]);

        /*92 - Nome do curso*/
        echo("                  <tr class=\"head01\">\n");
        echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 92)." ".$v['nome_curso']."</td>\n");
        echo("                  </tr>\n");
        /*281- C�digo do curso: */
        echo("                  <tr>\n");
        echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 281)." ".$v['cod_curso']."</td>\n");
        echo("                  </tr>\n");
        /* 287 - Nome do coordenador:*/
        echo("                  <tr>\n");
        echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 287)." ".$v['nome']."</td>\n");
        echo("                  </tr>\n");
        /* 33 - E-mail: */
        echo("                  <tr>\n");
        echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases, 33)."<b>".$email."</b></td>\n");
        echo("                  </tr>\n");
      }
      else
      {
        /* 168 - E-mail inv�lido.*/
        echo("            <tr>\n");
        echo("              <td>".RetornaFraseDaLista($lista_frases, 164)."</td>\n");
        echo("            </tr>\n");
      }
    }
  }
  else
  {
    $email=MandaMsgcomDados($codigo);
    if($email!=false)
    {
      echo("            <tr>\n");
      echo("              <td>".$email."</td>\n");
      echo("            </tr>\n");
    }
    else
    {
      /* 284 - Erro no envio do e-mail*/
      echo("            <tr>\n");
      echo("              <td>".RetornaFraseDaLista($lista_frases, 284)."</td>\n");
      echo("            </tr>\n");
    }
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          </form>\n");
  echo("  </body>\n");
  echo("</html>\n");
?>