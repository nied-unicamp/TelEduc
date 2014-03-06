<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/ver.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Ncleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/material/ver.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");
  
  session_register("cod_ferramenta_m");
  if (isset($cod_ferramenta))
    $cod_ferramenta_m=$cod_ferramenta;
  else
    $cod_ferramenta=$cod_ferramenta_m;

  if ($cod_ferramenta==3)
    include("avaliacoes_material.inc");
  
  /**************** ajax ****************/
  
  require_once("../xajax_0.5/xajax_core/xajax.inc.php");
         
  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das funï¿½ï¿½es em PHP que vocï¿½ quer chamar atravï¿½s do xajax
  $objAjax->register(XAJAX_FUNCTION,"ExcluirItensDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RecuperarItensDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();
  
  /************* fim ajax ****************/
  
  include("../topo_tela.php");

  $cod_ferramenta_ajuda = $cod_ferramenta;
  if ($cod_ferramenta == 3){
    $cod_pagina_ajuda = 9;
  } else {
    $cod_pagina_ajuda = 4;
  }


  Desconectar($sock);
  $sock=Conectar("");

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock = Conectar($cod_curso);

  $AcessoAvaliacao = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

  switch ($cod_ferramenta) {
    case 3 :
      $tabela="Atividade";
      $dir="atividades";
      break;
    case 4 :
      $tabela="Apoio";
      $dir="apoio";
      break;
    case 5 :
      $tabela="Leitura";
      $dir="leituras";
      break;
    case 7 :
      $tabela="Obrigatoria";
      $dir="obrigatoria";
      break;
  }

  $dir_item_temp = CriaLinkVisualizar($sock,$dir,$cod_curso,$cod_usuario,$cod_item,$diretorio_arquivos,$diretorio_temp);

  $eformador=EFormador($sock,$cod_curso,$cod_usuario);

  echo("    <script type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");

  echo("      function WindowOpenVerURL(end)\n");
  echo("      {\n");
  echo("         window.open(end,'MaterialURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n");

  echo("      function Excluir(){\n");
//     6 - Vocï¿½ tem certeza de que deseja excluir esta atividade?
    /* 25 - (a atividade serï¿½ excluï¿½da definitivamente) */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,25)."')){\n");
  echo("          xajax_ExcluirItensDinamic('".$tabela."', '".$cod_curso."', '".$cod_usuario."', '".$cod_item."');\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function Recuperar(){\n");
    /* 26 - Vocï¿½ tem certeza de que deseja recuperar esta atividade? */
    /* 27 - (a atividade serï¿½ movida para a pasta Raiz e serï¿½ compartilhada com formadores) */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,26)."\\n".RetornaFraseDaLista($lista_frases,27)."')){\n");
  echo("          xajax_RecuperarItensDinamic('".$tabela."', '".$cod_curso."', '".$cod_usuario."','".$cod_grupo_portfolio."', '".$cod_item."');\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function Recarregar(){\n");
  echo("        window.location='lixeira.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."';\n");
  echo("      }\n\n");

  echo("      function WindowOpenVerURL(end)\n");
  echo("      {\n");
  echo("         window.open(end,'MaterialURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n");

  echo("      function WindowOpenVer(end)\n");
  echo("      {\n");
  echo("        popup = window.open(end,'MaterialVer','top=50,left=100,width=600,height=400,resizable=yes,menubar=yes,status=yes,toolbar=yes,scrollbars=yes');\n");
  echo("        popup.focus();\n");
  echo("      }\n\n");

  echo("      function Iniciar(){\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    /* Pagina Principal */
  /* 1 - 3: Atividades
         4: Material de Apoio
         5: Leituras
         7: Parada Obrigatoria
   */
   /* 84 - Ver Atividade */
  $cabecalho =RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases,84);
  
  echo("          <h4>".$cabecalho."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("            <!--  Botoes de Acao  -->\n");
  echo("              <td class=\"btAuxTabs\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  
  /* 16(ger) - Lixeira */
  echo("                  <li><a href=\"lixeira.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_topico_raiz=".$cod_topico_raiz."&cod_item=".$cod_item."\">".RetornaFraseDaLista($lista_frases_geral,16)."</a></li>\n");
  
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 35 - Titulo */
  echo("                    <td width=\"\" class=\"itens\">".RetornaFraseDaLista($lista_frases,35)."</td>\n");
  /* 70 - Opcoes */
  echo("                    <td width=\"14%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");

  /* 13 - Data */
  echo("                    <td width=\"14%\" align=\"center\">".RetornaFraseDaLista($lista_frases,13)."</td>\n");
  echo("                  </tr>\n");

  $linha_item=RetornaDadosDoItem($sock, $tabela, $cod_item);
 
  echo("                  <tr id=\"tr_".$linha_item['cod_item']."\">\n");
  echo("                    <td class=\"itens\">\n");
  
  $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";

  
  echo($titulo);
  
  echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
  echo("                      <ul>\n");
  /* 48 - Recuperar (gen) */
  echo("                        <li><span onclick=\"Recuperar()\">".RetornaFraseDaLista($lista_frases_geral,48)."</span></li>\n");
  /* 12 - Excluir (gen) */
  echo("                        <li><span onclick=\"Excluir()\">".RetornaFraseDaLista($lista_frases_geral,12)."</span></li>\n");
  echo("                      </ul>\n");
  echo("                    </td>\n");

  echo("                    <td align=\"center\">\n");
  echo("                      <span id=data_".$linha_item['cod_item'].">".UnixTime2DataHora($linha_item['data'])."</span>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  if ($linha_item['texto']!="")
  {
    echo("                  <tr class=\"head\">\n");
    /* 31 - Texto  */
    echo("                    <td colspan=\"3\" class=\"itens\">".RetornaFraseDaLista($lista_frases,31)."</td>\n");    
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td colspan=\"3\" class=\"itens\">\n");
    $texto="<span id=\"text_".$linha_item['cod_item']."\">".AjustaParagrafo($linha_item['texto'])."</span>";
    echo("                      ".$texto);
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  $diretorio = $diretorio_arquivos."/".$cod_curso."/".$dir."/".$cod_item;

  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $diretorio);

  $num_arq_vis=RetornaNumArquivosVisiveis($lista_arq);

  if ((is_array($lista_arq)>0) && ($lista_arq!="")){
    echo("                  <tr class=\"head\">\n");
    /* 32 - Arquivos */
    echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,32)."</td>\n");
    echo("                  </tr>\n");

    $conta_arq=0;

    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
    // Procuramos na lista de arquivos se existe algum visivel
    $ha_visiveis = false;

    while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis){
      if ($linha['Arquivo'] != "")
        $ha_visiveis = !($linha['Status']);
    }

    $nivel_anterior=0;
    $nivel=-1;

    foreach($lista_arq as $cod => $linha){
      if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
        if ((!$linha['Status'])||(($linha['Status'])&&($eformador))){
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

          if ($linha['Arquivo'] != ""){
            if ($linha['Diretorio']!=""){
              $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
              $espacos2.="  ";
            }

            if ($linha['Status']){
              $arqOculto="arqOculto='sim'";
            }else{
              $arqOculto="arqOculto='nao'";
            }

            if (eregi(".zip$",$linha['Arquivo'])){
              // arquivo zip
              $imagem    = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
              $tag_abre  = "<a href=\"".$caminho_arquivo."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');return false;\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
            }
            else{
              // arquivo comum
              $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
              $tag_abre  = "<a href=\"".$caminho_arquivo."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."'); return false;\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqOculto.">";
            }

            $tag_fecha = "</a>";

            echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

            if ($eformador){
              echo("                          ".$espacos2."\n");
            }

            echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb)");

            echo("<span id=\"local_oculto_".$conta_arq."\">");
            if ($linha['Status']) 
              // 87 - Oculto
                echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style=\"color:red;\">".RetornaFraseDaLista($lista_frases,87)."</span></span>");
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
            if ($eformador){
              echo("                        ".$espacos2."\n");
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
      do{
        $espacos3.="  ";
        $j--;
      }while($j>=0);
      echo("                      ".$espacos3."</span>\n");
      $nivel--;
    }while($nivel>=0);
    echo("                      <script type=\"text/javascript\" language=\"JavaScript\">js_conta_arq=".$conta_arq.";</script>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");

  }

  $lista_url=RetornaEnderecosMaterial($sock, $tabela, $cod_item);

   if (!empty($lista_url)){

    echo("                  <tr class=\"head\">\n");
      /* 33 - EndereÃ§os */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,33)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");

    foreach ($lista_url as $cod => $linha)
    {

      $linha['endereco'] = RetornaURLValida($linha['endereco']);

      echo("                      <span id=\"end_".$linha['cod_endereco']."\">\n");

      if ($linha['nome']!="")
      {
        echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['nome']."</span>&nbsp;&nbsp;(".$linha['endereco'].")");
      }
      else
      {
        echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['endereco']."</span>");
      }
      echo("                        <br />\n");
      echo("                      </span>\n");
    }

    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
    echo("          <br />\n");    
    /* 509 - voltar, 510 - topo */
    echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
    echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
?>