<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/agenda/ver_linha.php

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
  ARQUIVO : cursos/aplic/agenda/ver_linha.php
  ========================================================== */


$dir_static = '../../../static_includes/';
$origem=null;
$cabecalho=null;
$frase=null;
$caminho=null;
$editar=null;
$renomear=null;
$limpar=null;
$imagem=null;



$ctrl_agenda = '../controller/';

include $ctrl_agenda.'AgendaController.php';

//Adciona o topo tela que contém referencias aos css
include $dir_static.'topo_tela.php';

$cod_ferramenta=31;
$cod_curso= $_GET['cod_curso'];
$cod_item= $_GET['cod_item'];
$id = $cod_item;

echo("    <script type=\"text/javascript\">\n\n");
echo("      var cod_curso='".$cod_curso."';\n");
echo("      var id='".$cod_item."';\n");
echo("      var editaTitulo=0;\n");
echo("		var texto=\"\" ;\n");
echo("      var cancelarElemento=null;");
echo("		var cancelarTodos=0;");
echo("		var cancelaEdita=0;");
echo("      var cod_item='".$cod_item."';\n");

echo("    </script>\n\n");
echo("	<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");
//echo ("    <script type=\"text/javascript\" src=\"" . $diretorio_jscss . "ckeditor/ckeditor.js\"></script>");
///echo ("    <script type=\"text/javascript\" src=\"" . $diretorio_jscss . "ckeditor/ckeditor_biblioteca.js\"></script>");
echo("    <script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"../../../js/dhtmllib.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"../../../js/jscript.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");



include $dir_static.'menu_principal.php';




$usr_formador = true;

$controlerAgenda = new AgendaController();
   
//Imprime o conteúdo da ferrameta
echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  if($origem == "ver_anteriores")
  {
    /* 1 - Agenda */
    /*2 - Agendas Anteriores*/ 
    $cabecalho = "          <h4>\"Agenda\" - \"Agendas Anteriores\"</h4>";
  } else {
    /* 1 - Agenda */
    /* 111 - Editar Agenda*/
    $cabecalho = "          <h4>Agenda - Editar Agenda</h4>";
  }
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../../../img/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../../../img/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../../../img/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
   /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;Voltar&nbsp;</span></li></ul>\n");
  
  /* Tabela Externa */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");




  /*34 - Apagar */
  echo("                  <li><span onClick=\"ApagarItem();\">Agendas Futuras</span></li>\n");
  echo("                  <li><span onClick=\"ApagarItem();\">Historico</span></li>\n");
  echo("                  <li><span onClick=\"ApagarItem();\">Publicar</span></li>\n");
  echo("                  <li><span onClick=\"ApagarItem();\">Apagar</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* Tabela Interna */
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /*18 - Titulo */
  echo("                    <td class=\"alLeft\" align=\"left\">Titulo</td>\n");

  /*Conteudo da Agenda*/

  $linha_item=$controlerAgenda->listaAgenda($cod_item);
  
  //var_dump($linha_item);
  //if(($usr_formador) && ($linha_item['situacao'] != "H"))
  {
    /*70 (gn) - Opcoes */
    echo("                  <td align=center width=\"15%\">Opcoes</td>\n");
  }
  echo("                  </tr>\n");

  $titulo=$linha_item['titulo'];

 // $renomear= renomear_;
        echo("					<tr  id=\"tr_".$cod_item."\">");
        $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
        // 106 - Renomear Título
       $renomear="<span id=\"renomear_".$cod_item."\">Editar Titulo</span>";
     //  $renomear="<span onclick=\"AlteraTitulo('".$linha_item['cod_item']."');\">Editar Titulo</span>";
      /* 91 - Editar texto */
        $editar="<span onclick=\"AlteraTexto(\"\");\">Editar Texto</span>";
      /* 92 - Limpar texto */
        $limpar="<span onclick=\"LimpaTexto(\"\");\">Limpar Texto</span>";


  echo("                  <tr id='tr_".$linha_item['cod_item']."'>\n");
  echo("                    <td class=\"itens\">".$titulo."</td>\n");


      echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
      echo("                      <ul>\n");
      echo("                        <li>".$renomear."</li>\n");
      echo("                        <li>".$editar."</li>\n");
      echo("                        <li>".$limpar."</li>\n");
  
      echo("                      </ul>\n");
      echo("                    </td>\n");


  echo("                  </tr>\n");


  $texto=$linha_item['texto'];
  
  $texto="<span id=\"text_".$linha_item['cod_item']."\">".$linha_item['texto']."</span>";

  
  echo("                  <tr class=\"head\">\n");
  /* 94 - Conteudo  */
  echo("                    <td colspan=\"4\">Texto</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"4\">\n");
  echo("                      <div class=\"divRichText\">\n");
  echo("                        ".$texto."\n");
  echo("                      </div>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  //if ($usr_formador){
    echo("                  <tr class=\"head\">\n");
    /* 57(biblioteca) - Arquivos */
    echo("                    <td colspan=\"4\">Arquivos</td>\n");
    echo("                  </tr>\n");

      echo("                  <tr>\n");
      echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");

                  $imagem    = "<img alt=\"\" src=../imgs/arqzip.gif border=0 />";
               //   $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqEntrada.">";
                
                //else
                {
                  // arquivo comum
                  $imagem    = "<img alt=\"\" src=../imgs/arqp.gif border=0 />";
                 //$tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqEntrada.">";
                }


    echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">Apagar</span></li>\n");
    /*38 - Descompactar (ger)*/
    echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">Descompactar</span></li>\n");
    /*60 - Selecionar Entrada */
    echo("                        <li class=\"menuUp\" id=\"mArq_entrada\"><span id=\"sArq_entrada\">Selecionar Entrada</span></li>\n");
    echo("                      </ul>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td align=left colspan=4>\n");
    echo("                      <form name=\"formFiles\" id=\"formFiles\" action='acoes_linha.php' method='post' enctype=\"multipart/form-data\">\n");
    echo("                        <input type='hidden' name='cod_curso' value='".$cod_curso."' />\n");
    echo("                        <input type='hidden' name='cod_item' value='".$cod_item."' />\n");
    echo("                        <input type='hidden' name='acao' value='anexar' />\n");
    echo("                        <input type='hidden' name='origem' value='".$origem."' />\n");
    echo("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
    echo("                          <img alt=\"\" src=\"../../../img/paperclip.gif\" border=0 />\n");
    echo("                          <span class=\"destaque\">\</span>\n");
    echo("                          <span> - \"gggg\"</span>\n");
    echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
    echo("                          <input type=\"file\" id=\"input_files\" name=\"input_files\" onchange=\"EdicaoArq(1);\" style=\"border:2px solid #9bc\" />\n");
    echo("                          &nbsp;&nbsp;\n");
    //echo("                          <span onClick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 18)."</span>\n");
    //echo("                          &nbsp;&nbsp;\n");
    //echo("                          <span onClick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 2)."</span>\n");
    echo("                        </div>\n");
                                    /* 26 - Anexar arquivo (ger) */
    echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../../../img/paperclip.gif\" border=0 /> <span class=\"link\" id =\"insertFile\" onClick=\"AcrescentarBarraFile(1);\">Anexar Arquivo</span></div>\n");
    echo("                      </form>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  //}

  /*Fim tabela interna*/
  echo("                </table>\n");

//  if($usr_formador)
//  {
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    /* 59 - entrada. */
    /* 20 - Este arquivo sera a entrada da agenda*/
    echo("              <td align=\"left\">(<font color=red>entrada</font>) - Este arquivo sera a entrada da agenda!\n");
    //echo("</td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    /* 44 - Obs.: A agenda devera conter somente texto ou somente arquivos. */
    echo("              <td align=\"left\">Obs.: A agenda devera conter somente texto ou somente arquivos.\n");
    
    /*Fim tabela externa*/
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
   // include("../tela2.php");
    echo("  </body>\n");
    echo("</html>\n");
   // Desconectar($sock);
    ?>
        