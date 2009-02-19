<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/enviar_email2.php

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
  ARQUIVO : administracao/enviar_email2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

   VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("    <script type=\"text/javascript\">\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("    </script>\n");
  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 9 - Enviar e-mail para usu�rios */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,9)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='enviar_email.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $host = RetornaConfig("host");
  $raiz_www = RetornaDiretorio("raiz_www");

  if ($cod_curso=="Todos")
  {
    $lista=RetornaListaCursosAtivos();
    unset($destinos);
    if (count($lista)>0)
    {
      foreach($lista as $cod => $nome)
      {
        $mensagem_enviar=MontaMsg($host, $raiz_www, $cod, $mensagem, $assunto, -1, '');
        $destinos=EnviarMensagem($cod,$coordenadores,$formadores,$alunos,$assunto,$mensagem_enviar,$destinos);
      }

      echo("                  <tr class=\"head\">\n");
      /* ?? - Mensagem enviada para: */
      echo("                    <td>".RetornaFraseDaLista($lista_frases,53)."</td>\n");
      echo("                  </tr>\n");

      $destinos[0]=" ";
      $destinos=implode(", ",explode(",",$destinos));

      echo("                  <tr>\n");
      echo("                    <td>".$destinos."</td>\n");
      echo("                  </tr>\n");
    }
    else
    {
      /* ?? - N�o existe nenhum curso ativo. */
      echo("                  <tr>\n");
      echo("                    <td>".RetornaFraseDaLista($lista_frases,54)."</td>\n");
      echo("                  </tr>\n");
    }
  }
  else
  {
    $mensagem_enviar=MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto, -1, '');
    $destinos=EnviarMensagem($cod_curso,$coordenadores,$formadores,$alunos,$assunto,$mensagem_enviar,"");
    echo("                  <tr class=\"head\">\n");
    /* ?? - Mensagem enviada para: */
    echo("                    <td>".RetornaFraseDaLista($lista_frases,53)."</td>\n");
    echo("                  </tr>\n");

    $destinos[0]=" ";
    $destinos=implode(", ",explode(",",$destinos));

    echo("                  <tr>\n");
    if($destinos != " ")
      echo("                    <td>".$destinos."</td>\n");
    else
      //?? - Curso(s) sem aluno(s).
      echo("                    <td>Curso(s) sem aluno(s).</td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>