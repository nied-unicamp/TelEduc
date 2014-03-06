<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/dinamica/editar_dinam.php

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
  ARQUIVO : cursos/aplic/dinamica/editar_dinam.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("dinamica.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"EditarTexto");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirArquivo");
  $objAjax->register(XAJAX_FUNCTION,"AbreEdicao");
  $objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"SelecionarEntradaDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RetirarEntradaDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RetornaFraseGeralDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $sock=Conectar("");
  $lista_frases_biblioteca=RetornaListaDeFrases($sock,-2);
  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta=16;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=3;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("anexar", 15, 52);
  $feedbackObject->addAction("descompactar", 53, 54);
  $feedbackObject->addAction("selecionarEntrada", 56, 0);
  $feedbackObject->addAction("tiraEntrada", 15, 0);
  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  if (ExisteDinamica($sock,$cod_curso,$diretorio_arquivos)=='N')
  {
    IniciaCriacaoDinamica($sock,$cod_usuario);
  }

  $dir_name = "dinamica";
  $linha_item = RetornaDadosDinamica($sock);
  $cod_item = $linha_item['cod_dinamica'];
  $dir_item_temp=CriaLinkVisualizar($sock,$dir_name, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);
  /* Verifica se o usuario eh formador. */
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");
  echo("      var cod_ferramenta ='".$cod_ferramenta."';\n");
  echo("      var cod_item       ='".$cod_item."';\n");
  echo("      var cod_curso      ='".$cod_curso."';\n");
  echo("      var cod_usuario    ='".$cod_usuario."';\n");
  echo("      var origem         ='".(isset($origem)?$origem:"")."';\n");
  echo("      var num_apagados   = '0';\n");
  /* (ger) 18 - Ok */
  // Texto do bot�o Ok do ckEditor
  echo("      var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do bot�o Cancelar do ckEditor
  echo("      var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  if (isset($_GET['acao']) && isset($_GET['atualizacao'])) {
    $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  }
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function WindowOpenVer(id)\n");
  echo("      {\n");
  echo("         window.open('".$dir_item_temp['link']."'+id+'?".time()."','Agenda','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n\n");

  echo("      function Voltar()\n");
  echo("      {\n");
  echo("        window.location='dinamica.php?cod_curso=".$cod_curso."';\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  $objAjax->printJavascript();

  echo("    <script type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Impede o acesso a algumas secoes aos usuários que não são formadores. */
  if (!$tela_formador){
    /* 1 - Enquete */
    echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
    /* 73 - Acao exclusiva a formadores. */
    echo("    - ".RetornaFraseDaLista($lista_frases_geral, 76)."</h4>");

    /*Voltar*/
    /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* 23 - Voltar (gen) */
    echo("          <form name=\"frmErro\" action=\"\" method=\"post\">\n");
    echo("            <input class=\"input\" type=\"button\" name=\"cmdVoltar\" value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=\"Voltar();\" />\n");
    echo("          </form>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit;
  }

  /* Verificação se o item está em Edição */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
  if ($linha_item['status']=="E")
  {
    if(($linha_item['data']<(time()-1800)) || ($cod_usuario == $linha_item['cod_usuario'])){
      AcabaEdicaoDinamic($cod_curso, $cod_item, $cod_usuario);
    }else{
      /* Está em edição... */
      echo("          <script language=\"javascript\">\n");
      echo("            window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1&cod_item=".$cod_item."&origem=ver_linha','EmEdicao','width=600,height=280,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("            window.location='dinamica.php?cod_curso=".$cod_curso."';\n");
      echo("          </script>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
  }

  /* Página Principal */

  /* 1 - Din�mica do Curso */
  $cabecalho = "          <h4>".RetornaFraseDaLista($lista_frases, 1);
  if (ExisteDinamica($sock,$cod_curso,$diretorio_arquivos)=='N')
  {
    /* 2 - Incluir Din�mica do Curso */
    $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,2)."</h4>\n";
    $cod_pagina=2;
  }
  else
  {
    /* 3 - Alterar Din�mica do Curso */
    $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,3)."</h4>\n";
    $cod_pagina=3;
  }
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  /*Voltar*/
  /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  /* Tabela Externa */
  echo("          <table id=\"tabelaExterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23(ger) - Voltar */
  echo("                  <li><span onclick='Voltar();'>".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* Tabela Interna */
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 49 - Conteudo  */
  echo("                    <td align=\"left\">".RetornaFraseDaLista($lista_frases,49)."</td>\n");
  if($usr_formador)
  {
    /*51 - Opcoes */
    echo("                  <td align=\"center\" width=\"15%\">".RetornaFraseDaLista($lista_frases,51)."</td>\n");
  }
    /*50 - Data */
  echo("                    <td align=\"center\" width=\"15%\">".RetornaFraseDaLista($lista_frases,50)."</td>\n");
  echo("                  </tr>\n");
  
  /*Conteudo da Dinamica*/

  /*Verifica se ha arquivo de entrada*/
  $arquivo_entrada="";
  $lista_arq=RetornaArquivosDinamicaVer($cod_curso,$dir_item_temp['diretorio']);
  if (count($lista_arq)>0)
    foreach($lista_arq as $cod => $linha1)
      if ($linha1['Status'] && $linha1['Arquivo']!="")
        $arquivo_entrada = $dir_item_temp['link'].ConverteUrl2Html($linha1['Diretorio']."/".$linha1['Arquivo']);

  /*Se houver, cria um iframe para exibi-lo*/
  if(($linha_item['texto']=="")&&($arquivo_entrada!=""))
    $conteudo="<span id=\"text_".$linha_item['cod_dinamica']."\"><iframe id=\"iframe_ArqEntrada\" texto=\"ArqEntrada\" src=\"".$arquivo_entrada."\" width=\"100%\" height=\"400\" frameBorder=\"0\" scrolling=\"auto\"></iframe></span>";
  /*Senaum, exibe o texto da dinamica*/
  else
    $conteudo="<span id=\"text_".$linha_item['cod_dinamica']."\">".AjustaParagrafo($linha_item['texto'])."</span>";

  echo("                  <tr id='tr_".$linha_item['cod_dinamica']."'>\n");
  echo("                    <td class=\"itens\" rowspan=\"2\">\n");
  echo("                      <div class=\"divRichText\">".$conteudo."</div>\n");
  echo("                    </td>\n");
  if($usr_formador)
  {
    /* 45 - Editar Texto */
    $editar=RetornaFraseDaLista($lista_frases, 45);
    /* Limpar Texto */
    $limpar_texto=RetornaFraseDaLista($lista_frases, 46);

    $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_dinamica'].");\">".$editar."</span>";
    $limpar_texto="<span onclick=\"LimpaTexto(".$linha_item['cod_dinamica'].");\">".$limpar_texto."</span>";

    echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\" style=\"height:1px;\">\n");
    echo("                      <ul>\n");
    echo("                        <li>".$editar."</li>\n");
    echo("                        <li>".$limpar_texto."</li>\n");
    echo("                      </ul>\n");
    echo("                    </td>\n");
  }
  echo("                    <td align=\"center\" width=\"15%\">".UnixTime2Data($linha_item['data'])."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  echo("                    <td class=\"itens\" colspan=\"2\">&nbsp;</td>\n");
  echo("                  </tr>\n");
  if ($usr_formador)
  {
    echo("                  <tr class=\"head\">\n");
    /* 57(biblioteca) - Arquivos */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases_biblioteca,57)."</td>\n");
    echo("                  </tr>\n");

    if (count($lista_arq)>0){
      $conta_arq=0;

      echo("                  <tr>\n");
      echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
      // Procuramos na lista de arquivos se existe algum visivel
      $ha_visiveis = true;

        
        $nivel_anterior=0;
        $nivel=-1;

        foreach($lista_arq as $cod => $linha)
        {
          if (function_exists('mb_convert_encoding'))
            $linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
          if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
          {
            $nivel_anterior=$nivel;
            $espacos="";
            $espacos2="";
            $temp=explode("/",$linha['Diretorio']);
            $nivel=count($temp)-1;
            for ($c=0;$c<=$nivel;$c++){
              $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
              $espacos2.="  ";
            }

            $caminho_arquivo = $dir_item_temp['link'].$linha['Diretorio']."/".$linha['Arquivo'];

            if ($linha['Arquivo'] != "")
            {

              if ($linha['Diretorio']!=""){
                $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                $espacos2.="  ";
              }

              if ($linha['Status']) $arqEntrada="arqEntrada='sim'";
              else $arqEntrada="arqEntrada='nao'";

              if (eregi(".zip$",$linha['Arquivo']))
              {
                // arquivo zip
                $imagem    = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
                $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqEntrada.">";
              }
              else
              {
                // arquivo comum
                $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqEntrada.">";
              }

              $tag_fecha = "</a>";

              echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

              if ($usr_formador){
                echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\" />\n");
              }

              echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb)");

              echo("<span id=\"local_entrada_".$conta_arq."\">");
              if ($linha['Status']) 
                // 34 - entrada
                  echo("<span id=\"arq_entrada_".$conta_arq."\">- <span style='color:red;'>".RetornaFraseDaLista($lista_frases,34)."</span></span>");
              echo("</span>\n");
              echo("                          ".$espacos2."<br />\n");
              echo("                        ".$espacos2."</span>\n");
            }
            else{
              if ($nivel_anterior>=$nivel){
                $i=$nivel_anterior-$nivel;
                $j=$i;
                $espacos3="";
                do{
                  $espacos3.="  ";
                  $j--;
                }while($j>=0);
                do{
                  echo("                      ".$espacos3."</span>\n");
                  $i--;
                }while($i>=0);
              }
              // pasta
              $imagem    = "<img alt=\"\" src=\"../imgs/pasta.gif\" border=\"0\" />";
              echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
              echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
              if ($usr_formador){
                echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\" />\n");
              }
              echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
              echo("                        ".$espacos2."<br />\n");
           }

          }
          $conta_arq++;
        }
        do{
          $j=$nivel;
          $espacos3="";
          while($j>0){
            $espacos3.="  ";
            $j--;
          }
          if($j!=$nivel){
            echo("                      ".$espacos3."</span>\n");
          }
          $nivel--;
        }while($nivel>=0);
        
      echo("                      <script type=\"text/javascript\">js_conta_arq=".$conta_arq.";</script>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }
  }

  if ($usr_formador)
  {
    echo("                  <tr>\n");
    echo("                    <td align=\"left\" colspan=\"4\">\n");
    echo("                      <ul>\n");
    echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></span></li>\n");
    /*1 - Apagar (ger) */
    echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");
    /*38 - Descompactar (ger)*/
    echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">".RetornaFraseDaLista($lista_frases_geral,38)."</span></li>\n");
    /*77 - Selecionar Entrada */
    echo("                        <li class=\"menuUp\" id=\"mArq_entrada\"><span id=\"sArq_entrada\">".RetornaFraseDaLista($lista_frases_geral,77)."</span></li>\n");
    echo("                      </ul>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td align=\"left\" colspan=\"4\">\n");
    echo("                      <form name=\"formFiles\" id=\"formFiles\" action='acoes_linha.php' method='post' enctype=\"multipart/form-data\">\n");
    echo("                        <input type='hidden' name='cod_curso' value='".$cod_curso."' />\n");
    echo("                        <input type='hidden' name='cod_item' value='".$cod_item."' />\n");
    echo("                        <input type='hidden' name='acao' value='anexar' />\n");
    echo("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
    echo("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" />\n");
    echo("                          <span class=\"destaque\">".RetornaFraseDaLista ($lista_frases_geral, 26)."</span>\n");
    /* 12: Pressione o bot�o abaixo para selecionar o arquivo a ser anexado. */
    /* 13: (arquivos .ZIP podem ser enviados e descompactados posteriormente) */
    echo("                          <span> - ".RetornaFraseDaLista ($lista_frases, 12).RetornaFraseDaLista ($lista_frases, 13)."</span>\n");
    echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
    echo("                          <input type=\"file\" style=\"border: 2px solid rgb(153, 187, 204); visibility: visible;\" onchange=\"EdicaoArq(1);\" name=\"input_files\" id=\"input_files\">\n");
    echo("                        </div>\n");
                                    /* 26 - Anexar arquivos (ger) */
    echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">".RetornaFraseDaLista($lista_frases_geral,26)."</span></div>\n");
    echo("                      </form>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  /*Fim tabela interna*/
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  
  if($usr_formador)
  {
    echo("            <tr>\n");
    /* 34 - entrada. */
    /* 35 - Este arquivo sera a entrada da dinamica*/
    echo("              <td align=\"left\">(<font color=\"red\">".RetornaFraseDaLista($lista_frases,34)."</font>) - ".RetornaFraseDaLista($lista_frases,35)."</td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    /* 26 - Obs.:
       27 - A din�mica dever� conter somente texto ou somente arquivos. 
    */
    echo("              <td align=\"left\"><b>".RetornaFraseDaLista($lista_frases,26)."</b>".RetornaFraseDaLista($lista_frases,27)."</td>\n");
    echo("            </tr>\n");
  }
  /*Fim tabela externa*/
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
 
