<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/dinamica/dinamica.php

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
  ARQUIVO : cursos/aplic/dinamica/dinamica.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("dinamica.inc");

  $sock=Conectar("");
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta=16;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  include("../topo_tela.php");

  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /* Fun��es JavaScript */
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");	

  echo("      function IncAltDinam()\n");
  echo("      {\n");
  echo("        window.location = 'editar_dinam.php?cod_curso=".$cod_curso."';\n");
  echo("      }\n\n");

  echo("      function ImportarDinam()\n");
  echo("      {\n");
  echo("        window.location = 'importar_curso.php?cod_curso=".$cod_curso."';\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal.php");
  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Dinamica do Curso */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  if (ExisteDinamica($sock,$cod_curso,$diretorio_arquivos)=='N')
    /* 2 - Incluir Din�mica do Curso */
    $frase = RetornaFraseDaLista($lista_frases,2);
  else
    /* 3 - Alterar Din�mica do Curso */
    $frase = RetornaFraseDaLista($lista_frases,3);
    
  /* Tabela Externa */
  echo("          <table id=\"tabelaExterna\" cellpadding=\"0\" cellspacing=\"0\"  class=\"tabExterna\">\n");
  if($usr_formador)
  {
    echo("            <tr>\n");
    echo("              <td valign=\"top\">\n");
    echo("                <ul class=\"btAuxTabs\">\n");
    echo("                  <li><span onclick='IncAltDinam();'>".$frase."</span></li>\n");
    /* 36 - Importar Dinamica*/
    echo("                  <li><span onclick='ImportarDinam();'>".RetornaFraseDaLista($lista_frases, 36)."</span></li>\n");	
    echo("                </ul>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  }
  echo("            <tr>\n");
  echo("              <td>\n");
  /* Tabela Interna */
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  /* Conteudo */

  $linha_item=RetornaDadosDinamica($sock);

  if(ExisteDinamica($sock,$cod_curso,$diretorio_arquivos)!='N')
  {
    $align="left";
    if($linha_item['texto']!="")
      $conteudo = "<div class=\"divRichText\">".$linha_item['texto']."</div>";
    else
    {
      $arquivo_entrada="";
      $dir_name = "dinamica";
      $dir_item_temp = CriaLinkVisualizar($sock,$dir_name, $cod_curso, $cod_usuario, $linha_item['cod_dinamica'], $diretorio_arquivos, $diretorio_temp);
      $lista_arq=RetornaArquivosDinamicaVer($cod_curso, $dir_item_temp['diretorio']);
      if (count($lista_arq)>0)
      {
        foreach($lista_arq as $cod => $linha1)
        {
          if ($linha1['Status'] && $linha1['Arquivo']!="")
          {
            $arquivo_entrada = $dir_item_temp['link'].ConverteUrl2Html($linha1['Diretorio']."/".$linha1['Arquivo']);
          }
        }
      }
      if ($arquivo_entrada!="")
      {
        $conteudo = "<iframe id=\"text_".$linha_item['cod_dinamica']."\" name=\"text_".$linha_item['cod_dinamica']."\" src=\"".$arquivo_entrada."\" frameBorder=\"0\" width=\"100%\" height=\"400\" scrolling=\"auto\"></iframe>";
      }
      else
      {
        $conteudo = RetornaFraseDaLista($lista_frases,4);
        $align="center";
      }
    }

    echo("                  <tr>\n");
    echo("                    <td align=\"".$align."\">".$conteudo."</td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    /* 4 - Nenhuma dinamica adicionada ainda! */
    echo("                  <tr>\n");
    echo("                    <td colspan=\"5\">".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    echo("                  </tr>\n");
  }	

  /*Fim tabela interna*/
  echo("                </table>\n");
  
  /*Fim tabela externa*/
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);

?>
