<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ajuda_media.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ajuda_media.php
  ========================================================== */

  /* TODO - incluir topo tela e tela2 */
  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");


  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);


  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);
  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"); echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc . Ensino &agrave; Dist&acirc;ncia</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\" />\n");
  echo("    <meta name=\"description\" content=\"\" />\n");
  echo("    <meta name=\"keywords\" content=\"\" />\n");
  echo("    <meta name=\"owner\" content=\"\" />\n");
  echo("    <meta name=\"copyright\" content=\"\" />\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");

  if ($SalvarEmArquivo)
  {
    echo("    <style>\n");
    include "../teleduc.css";
    include "avaliacoes.css";
    echo("    </style>\n");

    echo("    <script language=\"javascript\">\n");
  }
  else
  {
    echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"../teleduc.css\"> \n");
    echo("    <link rel=\"stylesheet\" type=\"text/css\" href=\"avaliacoes.css\"> \n");
    echo("    <script language=\"javascript\">\n");
  }

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("      function startList() {\n");
  echo("        if (document.all && document.getElementById) {\n");
  echo("          nodes = document.getElementsByTagName(\"span\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("          nodes = document.getElementsByTagName(\"li\");\n");
  echo("          for (i=0; i < nodes.length; i++) {\n");
  echo("            node = nodes[i];\n");
  echo("            node.onmouseover = function() {\n");
  echo("              this.className += \"Hover\";\n");
  echo("            }\n");
  echo("            node.onmouseout = function() {\n");
  echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
  echo("            }\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function ImprimirRelatorio() \n");
  echo("      { \n");
  echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("        { \n");
  echo("          self.print(); \n");
  echo("        } \n");
  echo("        else \n");
  echo("        { \n");
  // 51 (gen)- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
  echo("          alert('".RetornaFraseDaLista($lista_frases,51)."');\n");
  echo("        }\n");
  echo("      }\n");

  echo("    </script>\n");
  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");

  /* Pagina Principal */

  // 1 - Avaliacoes
  $cabecalho = ("<br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1));
  /* 181 - Componentes do grupo */
  $cabecalho.= (" - ".RetornaFraseDaLista($lista_frases,181)."</h4>\n");
  echo($cabecalho);
  echo ("<br />\n");

  echo("    <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\" colspan=3>\n");
  echo("          <ul class=\"btAuxTabs\">\n");
   /* 13 - Fechar (ger) */
  echo("            <li><span onclick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td colspan=3>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  if ($tela_ajuda == "expressao") {
     // 205 (ajuda da f�rmula da express�o da m�dia)
     echo(RetornaFraseDaLista($lista_frases, 205));
  } else if ($tela_ajuda == "norma") {
     // 206 (Ajuda referente a norma) 
     echo(RetornaFraseDaLista($lista_frases, 206));
  }
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
