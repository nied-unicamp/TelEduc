<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacao/componentes.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist?ncia
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

    Nied - N?cleo de Inform?tica Aplicada ? Educa??o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit?ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacao/componentes.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  $cod_curso = $_GET['cod_curso'];

  $sock = Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);  

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

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
  echo("    <script type='text/javascript' src='../bibliotecas/dhtmllib.js'></script>\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmlgoodies_calendar.js\"></script>\n");
  echo("    <link href=\"../js-css/dhtmlgoodies_calendar.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo("    <script type=\"text/javascript\">\n");

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

  echo("      function AbrePerfil(cod_usuario)\n");
  echo("      {\n");
  echo("         window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("        return(false);\n");
  echo("      }\n");

  echo("    </script>\n");
  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("\n");

  /* Pagina Principal */

  // 1 - Avaliacoes
  $cabecalho = ("<br /><br /><h4>".RetornaFraseDaLista ($lista_frases, 1));
  /* 181 - Componentes do grupo */
  $cabecalho.= (" - ".RetornaFraseDaLista($lista_frases,181)."</h4>\n");
  echo($cabecalho);
  echo ("<br />\n");

  $nome=NomeGrupo($sock,$cod_grupo);

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
  /*48 - Grupo*/
  echo("              <td  align=right><b>".RetornaFraseDaLista($lista_frases,48).":&nbsp;</b></td>\n");
  echo("              <td colspan=2>".$nome."</td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("    <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td>\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("            <tr class=\"head\">\n");
  /* 182 - Componentes */
  echo("              <td class>".RetornaFraseDaLista($lista_frases,182)."</td>\n");
  echo("            </tr>\n");

  $sock = MudarDB($sock, $cod_curso);
  $lista_integrantes=RetornaListaIntegrantesMomentoAvaliacao($sock,$cod_curso,$cod_grupo,$cod_avaliacao);
  if (is_array ($lista_integrantes))
  {
    foreach ($lista_integrantes as $cod_usuario => $linha)
    {
      echo("            <tr>\n");
      echo("              <td class=\"whitefield\"><A class=\"text\" href=\"#\" onclick=\"return(AbrePerfil($cod_usuario));\">");
      if ($linha['tipo_usuario']=="F")
        /* 58 - Formador */
        echo(RetornaFraseDaLista($lista_frases_geral,58)." ");
      echo($linha['nome']."</a></td>\n");
      echo("            </tr>\n");
    }
  }

  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
