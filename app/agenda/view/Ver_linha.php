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
$titulo=null;
$editar=null;
$renomear=null;
$limpar=null;
$conteudo=null;
$imagem=null;
$cod_curso=null;
$cod_usuario=null;
$cod_item=null;
$$linha_item=null;
$ctrl_agenda = '../controller/';

include $ctrl_agenda.'AgendaController.php';

//Adciona o topo tela que contém referencias aos css
include $dir_static.'topo_tela.php';

echo("    <script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"../../../js/dhtmllib.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"../../../js/jscript.js\"></script>\n");
echo("    <script type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");

include $dir_static.'menu_principal.php';

$usr_formador = true;

$controlerAgenda = new AgendaController();
 
 

include("../menu_principal.php");
  
  
//Imprime o conteúdo da ferrameta
echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* Verificação se o item está em Edição */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
 // $linha=RetornaUltimaPosicaoHistorico($sock, $cod_item);

  if ($linha['acao']=="E")
  {
    if (($linha['data']<(time()-1800)) || ($cod_usuario == $linha['cod_usuario'])){
      AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 0);
    }else{
      /* Está em edição... */
  //echo("          <script language=\"javascript\">\n");
  //echo("            window.open('em_edicao.php?\"mmdmdmd\"&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1&cod_item=".$cod_item."&origem=ver_linha','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
  //echo("            window.location='"lll".php?\"mmm\"';\n");
  //echo("          </script>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");
  exit();
    }
  }

  /* Pagina Principal */

  /* Se foi clicado no nome da agenda vindo da pagina de Agendas Anteriores, entao apenas mostra a agenda. Sendo assim ela nao eh editavel. 
   * Assim, o titulo da pagina eh: "Agenda - Agendas Anteriores"
   * 
   * Se n�o, foi clicado em determinada agenda e ela aparece editavel. Neste caso, o titulo da pagina eh: "Agenda - Editar Agenda"
   */
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

  if($origem == "ver_anteriores")
    /*33 - Voltar para Agenda Anteriores*/
   // $frase = RetornaFraseDaLista($lista_frases,33);
 // else if($origem == "ver_editar")
    /*3 - Agendas Futuras*/
    //$frase = RetornaFraseDaLista($lista_frases, 3);
  //else
    /*8 - Voltar para Agenda Atual*/
    //$frase = RetornaFraseDaLista($lista_frases, 8);


  if($origem == "ver_editar")
    $caminho="ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;
  else if($origem == "ver_anteriores")
    $caminho="ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;
  else 
    $caminho="agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;

        
 


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
  //$linha_item = RetornaAgenda($sock, $cod_item);

  //if(($usr_formador) && ($linha_item['situacao'] != "H"))
  {
    /*70 (gn) - Opcoes */
    echo("                  <td align=center width=\"15%\">Opcoes</td>\n");
  }
  echo("                  </tr>\n");

  $titulo=$linha_item['titulo'];

  /* (ger) 9 - Editar */
 // $editar=RetornaFraseDaLista ($lista_frases_geral, 9);
  
//  if ($linha_item['status']=="E")
  {

 //   $linha_historico=RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);

   // if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario'])
   // {
   //   CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp);
     // if($usr_formador)
      {
        $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
        // 106 - Renomear Título
        $renomear="<span onclick=\"AlteraTitulo(\"\");\">Editar Titulo</span>";
      /* 91 - Editar texto */
        $editar="<span onclick=\"AlteraTexto(\"\");\">Editar Texto</span>";
      /* 92 - Limpar texto */
        $limpar="<span onclick=\"LimpaTexto(\"\");\">Limpar Texto</span>";
      }
    }
  //}
 // else = item não está sendo editado
  //else
  //{
   // if($usr_formador)
    {
      $titulo="<span id=\"tit_\>\"\"</span>";
      // 106 - Renomear Títul
      $renomear="<span onclick=\"AlteraTitulo(\"\");\" id=\"renomear_\"\"\">Renomear Titulo</span>";
      /* 91 - Editar texto */
      $editar="<span onclick=\"AlteraTexto(\"\");\">Editar Texto</span>";
      /* 92 - Limpar texto */
      $limpar="<span onclick=\"LimpaTexto(\"\">Limpar texto</span>";
    }
  //}

  echo("                  <tr id='tr_\"retornar\"'>\n");
  echo("                    <td class=\"itens\">".$titulo."</td>\n");

  //if ($linha_item['situacao']!="H")
  //{
  //  if($usr_formador)
  //  {
      echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
      echo("                      <ul>\n");
      echo("                        <li>".$renomear."</li>\n");
      echo("                        <li>".$editar."</li>\n");
      echo("                        <li>".$limpar."</li>\n");
      /* Só pode apagar ou ativar agendas que estão na seção "Editar Agendas" ou que acabaram de ser criadas*/
      /*if($origem == "ver_editar")
      {*/
        /*24 - Ativar*/
        /*echo("                        <li><span onClick=\"Ativar();\">".RetornaFraseDaLista ($lista_frases, 24)."</span></li>\n");
        // G 1 - Apagar
        echo("                        <li><span onClick=\"ApagarItem();\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
      }*/
      echo("                      </ul>\n");
      echo("                    </td>\n");
   // }
//  }

  echo("                  </tr>\n");

  /*Verifica se ha arquivo de entrada*/
//  $arquivo_entrada="";
//  $lista_arq=RetornaArquivosAgendaVer($cod_curso, $dir_item_temp['diretorio']);

  //if (count($lista_arq)>0)
  //  foreach($lista_arq as $cod => $linha1)
  //    if ($linha1['Status'] && $linha1['Arquivo']!=""){
   //     if(preg_match('/\.php(\.)*/', $linha1['Arquivo'])){  //arquivos php.txt

    //      $arquivo_entrada = "agenda_entrada.php?entrada=".ConverteUrl2Html($linha1['Arquivo']."&diretorio=".$dir_item_temp['link']);
    //    }else{
     //     $arquivo_entrada = ConverteUrl2Html($dir_item_temp['link'].$linha1['Diretorio']."/".$linha1['Arquivo']);
     //   }
     //   break;
     // }

  /*Se houver, cria um iframe para exibi-lo*/
 // if(($linha_item['texto']=="")&&($arquivo_entrada!=""))
 //   $conteudo="<span id=\"text_".$linha_item['cod_item']."\"><iframe id=\"iframe_ArqEntrada\" texto=\"ArqEntrada\" src=\"".$arquivo_entrada."\" width=\"100%\" height=\"400\" frameBorder=\"0\" scrolling=\"Auto\"></iframe></span>";
  /*Senaum, exibe o texto da agenda*/
 // else
 // {
 //   $texto = AjustaParagrafo($linha_item['texto']);

   // if(($texto == "<P>&nbsp;</P>") || ($texto == "<br />"))
  //    $texto = "";

  //  $conteudo="<span id=\"text_".$linha_item['cod_item']."\">".$texto."</span>";
 // }

  echo("                  <tr class=\"head\">\n");
  /* 94 - Conteudo  */
  echo("                    <td colspan=\"4\">Conteudo</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"4\">\n");
  echo("                      <div class=\"divRichText\">\n");
  echo("                        ".$conteudo."\n");
  echo("                      </div>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  //if ($usr_formador){
    echo("                  <tr class=\"head\">\n");
    /* 57(biblioteca) - Arquivos */
    echo("                    <td colspan=\"4\">Arquivos</td>\n");
    echo("                  </tr>\n");


   // if (count($lista_arq)>0){
    //  $conta_arq=0;

      echo("                  <tr>\n");
      echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
      // Procuramos na lista de arquivos se existe algum visivel
     // $ha_visiveis = true;

      //  $nivel_anterior=0;
      //  $nivel=-1;

      //  foreach($lista_arq as $cod => $linha)
      //  { 
         // $linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
        //  if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
          {
          /*   $nivel_anterior=$nivel;
            $espacos="";
            $espacos2="";
            $temp=explode("/",$linha['Diretorio']);
            $nivel=count($temp)-1;
            for ($c=0;$c<=$nivel;$c++){
              $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
              $espacos2.="  ";
            } */

            //$caminho_arquivo = $dir_item_temp['link'].$linha['Diretorio']."/".$linha['Arquivo'];

            //if ($linha[Arquivo] != "")
            //{

            //  if ($linha['Diretorio']!=""){
            //    $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
            //    $espacos2.="  ";
            //  }

             // if ($linha['Status']) $arqEntrada="arqEntrada='sim'";
            //    else $arqEntrada="arqEntrada='nao'";

               // if (eregi(".zip$",$linha['Arquivo']))
               // {
                  // arquivo zip
                  $imagem    = "<img alt=\"\" src=../imgs/arqzip.gif border=0 />";
               //   $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqEntrada.">";
                }
                //else
                {
                  // arquivo comum
                  $imagem    = "<img alt=\"\" src=../imgs/arqp.gif border=0 />";
                 //$tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqEntrada.">";
                }

               // $tag_fecha = "</a>";

               // echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

               /*  if(($usr_formador) && ($linha_item['situacao'] != "H")){
                  echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onClick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
                }

                echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha[Tamanho]/1024),2)."Kb) - ".RetornaFraseDaLista($lista_frases,107)." ".UnixTime2Hora($linha["Data"])." ".UnixTime2DataMesAbreviado($linha["Data"])."");

                echo("<span id=\"local_entrada_".$conta_arq."\">");
                if ($linha['Status']) 
                  // 59 - Entrada
                    echo("<span id=\"arq_entrada_".$conta_arq."\">- <span style='color:red;'>".RetornaFraseDaLista($lista_frases,59)."</span></span>");
                echo("</span>\n");
                echo("                          ".$espacos2."<br>\n");
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
                $imagem    = "<img alt=\"\" src=../imgs/pasta.gif border=0 />";
                echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
                if(($usr_formador) && ($linha_item['situacao'] != "H")){
                  echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onClick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
                }
                echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
                echo("                        ".$espacos2."<br>\n");
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

  if(($usr_formador) && ($linha_item['situacao'] != "H"))
  {

    echo("                  <tr>\n");
    echo("                    <td align=\"left\" colspan=\"4\">\n");
    echo("                      <ul>\n");
    echo("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></span></li>\n");
    /*1 - Apagar (ger) */
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
    include("../tela2.php");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    ?>
        