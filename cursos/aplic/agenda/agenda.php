<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/agenda.php

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
  ARQUIVO : cursos/aplic/agenda/agenda.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("agenda.inc");

  $sock=Conectar("");
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta=1;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;
  $cod_curso = $_GET['cod_curso'];

  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("criarAgenda", 0, 97);

Desconectar($sock);
$sock = Conectar($cod_curso);
  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  /* Fun��es JavaScript */
  echo("    <script type=\"text/javascript\" src=\"../js-css/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");	

  echo("    </script>\n\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Agenda */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1)."</h4>");

  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/			
  echo("          <span class=\"btsNav\" onClick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
    
  /* Tabela Externa */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");	
  /* Agenda Anteriores*/	
  echo("              	<li><a href=\"ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_usuario=".$cod_usuario."\">".RetornaFraseDaLista($lista_frases, 2)."</a></li>\n");
  /* Editar Agenda, caso seja formador*/
  if($usr_formador)
  {
    echo("              	<li><a href=\"ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">".RetornaFraseDaLista($lista_frases, 3)."</a></li>\n");
  }	

  echo("                </ul>\n");	
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* Tabela Interna */	
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /*18 - Titulo */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,18)."</td>\n");		
  echo("                  </tr>\n");	
  /* Conteudo */

  $linha_item=RetornaAgendaAtiva($sock);

  if (isset($linha_item['cod_item']))
  {
    if($usr_formador)
      $titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"ver_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=agenda\">".$linha_item['titulo']."</a>";
    else
      $titulo=$linha_item['titulo'];

    $icone="<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";

    if($linha_item['texto']!="")
      $conteudo = $linha_item['texto'];
    else
    {
      $arquivo_entrada="";
      $dir_name = "agenda";
      $dir_item_temp = CriaLinkVisualizar($sock,$dir_name, $cod_curso, $cod_usuario, $linha_item['cod_item'], $diretorio_arquivos, $diretorio_temp);
      $lista_arq=RetornaArquivosAgendaVer($cod_curso, $dir_item_temp['diretorio']);
      if (count($lista_arq)>0)
      {
        foreach($lista_arq as $cod => $linha1)
        {
          if ($linha1['Status'] && $linha1['Arquivo']!="")
          {
            if(preg_match('/\.php(\.)*/', $linha1['Arquivo'])){  //arquivos php.txt
              $arquivo_entrada = "agenda_entrada.php?cod_curso=".$cod_curso."&entrada=".ConverteUrl2Html($linha1['Arquivo']."&diretorio=".$dir_item_temp['link']);
            }else{
              $arquivo_entrada = ConverteUrl2Html($dir_item_temp['link'].$linha1['Diretorio']."/".$linha1['Arquivo']);
            }
            break;
          }
        }
      }
      if ($arquivo_entrada!="")
      {
        $conteudo = "<iframe id=\"text_".$linha_item['cod_item']."\" name=\"iframe_ArqEntrada\" src=\"".$arquivo_entrada."\" frameBorder=\"0\" scrolling=\"auto\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"0\" vspace=\"0\" hspace=\"0\" style=\"overflow:visible; width:100%; display:visible\"></iframe>";
      }
      else
      {
        $conteudo = "";
      }
    }
          
    echo("                  <tr>\n");
    echo("                    <td align=left>".$icone.$titulo."</td>\n");
    echo("                  </tr>\n");
    if (!empty($conteudo))
    {
      echo("                  <tr class=\"head\">\n");
      /* 94 - Conteudo */
      echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,94)."</td>\n");		
      echo("                  </tr>\n");
      echo("                  <tr>\n");
      echo("                    <td align=left>\n");
      if ($arquivo_entrada!=""){
      		echo($conteudo);
      } else {
      echo("                      <div class=\"divRichText\">".$conteudo."</div>\n");
      }
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }
  }
  else
  {
    /* 4 - Nenhuma agenda adicionada ainda! */
    echo("                  <tr>\n");
    echo("                    <td colspan=5>".RetornaFraseDaLista($lista_frases,4)."</td>\n");
    echo("                  </tr>\n");
  }	

  /*Fim tabela interna*/		
  echo("                </table>\n");
    
  /*Fim tabela externa*/
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("    	    </table>\n");	
  include("../tela2.php");
  echo("  </body>\n");			
  echo("</html>\n");	
  Desconectar($sock);

?>
