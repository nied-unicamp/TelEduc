<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/inscrever3.php

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
  ARQUIVO : cursos/aplic/administracao/inscrever3.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("administracao.inc");

  $cod_ferramenta=0;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  switch($tipo_usuario){
    case 'z':
    //convidado
    $cod_pagina_ajuda = 14;
    break;
    case 'A':
    //aluno
    $cod_pagina_ajuda = 7;
    break;
  }

  include("../topo_tela.php");
  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /*Funcao JavaScript*/
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var numLogins = 5;\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function verifica_submit(e)\n");
  echo("      {\n");
  echo("        var keynum;\n\n");
  echo("        if(window.event) // IE\n");
  echo("          keynum = e.keyCode;\n");
  echo("        else if(e.which) // Netscape/Firefox/Opera\n");
  echo("          keynum = e.which;\n\n");

  echo("        if(keynum == 13)\n");
  echo("        {\n");
  echo("          buscar();\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n");

  echo("      function buscar()\n");
  echo("      {\n");
  echo("        document.formul.method='post';\n");
  echo("        document.formul.action='".$_SESSION['PHP_SELF']."';\n");
  echo("        document.formul.submit();\n");
  echo("        return true;\n");
  echo("      }\n");

  echo("      function verificar(array_itens)\n");
  echo("      {\n");
  echo("        document.formul.method='post';\n");
  echo("        document.formul.action='acoes.php';\n");
  echo("        document.getElementById('codigos_usu_global').value=array_itens;\n");
  echo("        document.formul.submit();\n");
  echo("      }\n");



  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens = document.getElementsByName('cod_usu_global[]');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i < cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            array_itens[j]=cod_itens[i].value;");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if(j > 0){\n");
  echo("          document.getElementById('inscreve').className=\"menuUp02\";\n");
  echo("          document.getElementById('inscreve').onclick=function(){ verificar(array_itens); };\n");
  echo("        }else{\n");
  echo("          document.getElementById('inscreve').className=\"menuUp\";\n");
  echo("          document.getElementById('inscreve').onclick=function(){ };\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  echo("    <form name=\"formul\" action=\"#\" method=\"post\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=".$cod_curso.">\n");
  echo("      <input type=\"hidden\" name=\"cod_usuario\" value=".$cod_usuario.">\n");
  echo("      <input type=\"hidden\" name=\"cod_ferramenta\" value=".$cod_ferramenta.">\n");
  echo("      <input type=\"hidden\" name=\"tipo_usuario\" value=".$tipo_usuario.">\n");
  echo("      <input type=\"hidden\" id=\"codigos_usu_global\" name=\"codigos_usu_global\" value=''>\n");
  echo("      <input type=\"hidden\" name=\"action\" value='inscrever_cadastrado'>\n");

  // P�gina Principal
  /* 1 - Administra��o */
  $cabecalho = ("          <h4>".RetornaFraseDaLista ($lista_frases, 1)."\n");

  if ($tipo_usuario=="F")
  {
    /* 50 - Inscrever Formadores */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 50)."</h4>";
    $cod_pagina=6;
  }
  else if ($tipo_usuario == 'z')
  {
    // 164 - Inscrever Convidados
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 164)."</h4>";

    // 166 - N� de Convidados:
    $frase_qtde=RetornaFraseDaLista($lista_frases, 166);
    $cod_pagina=14;
  }
  else if ($tipo_usuario == 'V')
  {
    // 164 - Inscrever Visitantes
    $cabecalho .= " - "."[Inscrever Visitantes]"."</h4>";

    // 166 - N� de Visitantes:
    $frase_qtde="N� de Visitantes:";

  }
  else if ($tipo_usuario == 'A')
  {
    /* 51 - Inscrever Alunos */
    $cabecalho .= " - ".RetornaFraseDaLista($lista_frases, 51)."</h4>";
    $tipo_usuario="A";
    $cod_pagina=7;
  }
  else
  {
    echo("Arquivo inscrever.php, tipo_usuario inv�lido, tipo_usuario = [");
    var_dump($tipo_usuario);
    echo("]<br>\n");
    Desconectar($sock);
    die();
  }  

  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 2 - Cancelar (geral)*/
  echo("                  <li><a href=\"administracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;confirma=0\">".RetornaFraseDaLista($lista_frases_geral,2)."</a></li>\n");
  echo("                  <li><a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=".$tipo_usuario."\">Novos usu&aacute;rios"./*RetornaFraseDaLista($lista_frases_geral,2).*/"</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table  cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  /* - */
  echo("                    <td colspan=\"4\">Selecione abaixo os usu&aacute;rios que deseja cadastrar:"/*.RetornaFraseDaLista($lista_frases,58)*/."</td>\n");
  echo("                  </tr>\n"); 
  echo("                  <tr>\n");
  /* - */
  echo("                    <td align=\"left\" valign=\"top\" colspan=\"4\">Nome/E-mail/Login: <input type='text' class='input' name='busca' onkeypress='return verifica_submit(event);' value='".$busca."'>&nbsp;<input type=\"button\" class=\"input\" onclick=\"return(buscar());\" value='Buscar"/*.RetornaFraseDaLista($lista_frases,59)*/."'></td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td width='10px'></td>\n");
  /* 15 - Nome */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,15)."</b></td>\n");
  /* 52 - E-mail */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,52)."</b></td>\n");
  /* 53 - Login */
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,53)."</b></td>\n");
  echo("                  </tr>\n");

  $lista_usuarios = RetornaListaUsuariosGlobal($sock,$cod_curso,$busca);
  $i=0;

  if ((count($lista_usuarios))==0 || $lista_usuarios == "")
  {
    echo("                 <tr>\n");
    /* 279 - Sua pesquisa n&atilde;o retornou resultado. */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,279)."</td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    foreach ($lista_usuarios as $cod_usu => $array_usu)
    {  
      echo("                  <tr>\n");
      echo("                    <td><input type=\"checkbox\" class=\"input\" name=\"cod_usu_global[]\" onclick=\"VerificaCheck();\" value=\"".$array_usu['cod_usuario']."\"></td>\n");
      echo("                    <td>".$array_usu['nome']."</td>\n");
      echo("                    <td>".$array_usu['email']."</td>\n");
      echo("                    <td>".$array_usu['login']."</td>\n");
      echo("                  </tr>\n");

      $i++;
    }
  }

  echo("                </table>\n");

  /* 59 - Inscrever */
//   echo("                <div align=\"right\"><br><input type=\"submit\" id=\"inscreve\" class=\"menuUp\" value='".RetornaFraseDaLista($lista_frases,59)."'></div>\n");
  echo("                <div align=\"right\"><br>\n");
  echo("                  <li id=\"inscreve\" class=\"menuUp\"><span>  ".RetornaFraseDaLista($lista_frases,59)."</span></li></div>\n");
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
