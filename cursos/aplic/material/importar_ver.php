<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/ver.php

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

    Nied - Ncleo de Inform?tica Aplicada ? Educa??o
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
  ARQUIVO : cursos/aplic/material/ver.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include($bibliotecas."importar.inc");
  include("material.inc");

  /**************** ajax ****************/

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun??es em PHP que voc? quer chamar atrav?s do xajax
  $objAjax->register(XAJAX_FUNCTION,"AbreEdicao");
  $objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CancelaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirArquivo");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirEndereco");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"OcultarArquivosDinamic");
  $objAjax->register(XAJAX_FUNCTION,"DesocultarArquivosDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MoverArquivosDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MoverItensDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  /**************** ajax ****************/

  include("../topo_tela.php");

  // **************** VARI?VEIS DE ENTRADA ****************
  // Recebe de 'importar_curso2.php'
  //    c?digo do curso
  $cod_curso = $_GET['cod_curso'];
  //    c?digo da categoria que estava sendo listada.
  $cod_categoria = $_GET['cod_categoria'];
  //    c?digo do curso do qual itens ser?o importados
  $cod_curso_import = $_GET['cod_curso_import'];
  $cod_ferramenta = $_GET['cod_ferramenta'];
  //    tipo do curso: A(ndamento), I(nscri??es abertas), L(atentes),
  //  E(ncerrados)
  $tipo_curso = $_GET['tipo_curso'];
  if ('E' == $tipo_curso)
  {
    //    per?odo especificado para listar os cursos
    //  encerrados.
    $data_inicio = $_GEt['data_inicio'];
    $data_fim = $_GET['data_fim'];
  }
  //    booleano, se o curso, cujos itens ser?o importados, foi
  //  escolhido na lista de cursos compartilhados.
  $curso_compartilhado = $_GET['curso_compartilhado'];
  //    booleando, se o curso, cujos itens ser?o importados, ? um
  //  curso extra?do.
  $curso_extraido = $_GET['curso_extraido'];
  //    c?digo do t?pico do curso do qual itens ser?o importados.
  $cod_topico_raiz_import = $_GET['cod_topico_raiz_import'];
  // ******************************************************

  Desconectar($sock);
  $sock=Conectar("");

  if ($curso_extraido)
    $diretorio_arquivos=RetornaDiretorio($sock, 'Montagem');
  else
    $diretorio_arquivos=RetornaDiretorio($sock, 'Arquivos');

  $diretorio_temp=RetornaDiretorio($sock, 'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

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

  /* Verificaï¿½ï¿½o se o item estï¿½ em Edi??o */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edi??o... */

  $linha=RetornaUltimaPosicaoHistorico ($sock, $tabela, $cod_item);
  if ($linha['acao']=="E") {
    if($linha['inicio_edicao']>(time()-1800) || $cod_usuario!=$linha['cod_usuario']) {
      /* Est? em edi??o... */
      echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
      echo("       window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_topico_raiz=".$cod_topico_raiz_import."','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\n");
      echo("       document.location='material.php?cod_curso=".$cod_curso."&cod_item=".$linha_item['cod_item']."&origem=ver&cod_topico=".$cod_topico_raiz_import."';\n");
      echo("    </script>\n");
      echo("  </head>\n");
      echo("  <body>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
    else {
      CancelaEdicao($sock, $tabela, $dir, $cod_item, $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp,$criacao_avaliacao);
    }
  }

  echo("    <script type=\"text/javascript\" language=\"JavaScript\">\n");

  echo("      var cod_avaliacao=\"\";\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n");

  echo("      function WindowOpenVerURL(end)\n");
  echo("      {\n");
  echo("         window.open(end,'MaterialURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n");

  echo("       function MudarTopico(cod_topico){\n");
  echo("         document.frmImportar.action = \"importar_material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_usuario=".$cod_usuario."&cod_topico_raiz=\"+cod_topico+\"&cod_topico_raiz_import=".$cod_topico_raiz_import."\";\n");
  echo("         document.frmImportar.cod_topico_raiz_import.value = cod_topico;\n");
  echo("         document.frmImportar.submit();\n");
  echo("       }\n");

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
    //		"<!--<span class=\"btsNav\">".PreparaBusca($cod_curso,$cod_ferramenta)."</span>-->\n");
  $sock = MudarDB($sock, $cod_curso_origem);
  
  $linha_item=RetornaDadosDoItem($sock, $tabela, $cod_item);
  $cod_topico_pai = $linha_item['cod_topico'];

  $lista_topicos_ancestrais=RetornaTopicosAncestrais($sock, $tabela, $cod_topico_pai);
  unset($path);
  foreach ($lista_topicos_ancestrais as $cod => $linha){
    if($cod > 0) $path = " &gt;&gt; ".$path;
    $path= "<span class=\"link\" onClick='MudarTopico(".$linha['cod_topico'].")'>".$linha['topico']."</span> ".$path;

  }
  echo("          ".$path);

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("            <!--  Botoes de Acao  -->\n");
  echo("              <td class=\"btAuxTabs\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  
  /* 23(ger) - Voltar */
  /* Simplificando o voltar de MudarTopico('".$cod_topico_raiz_import."') pra history.go(-1) pra evitar o bug da perda de elementos passados via GET */
  echo("                  <li><span onClick=\"history.go(-1);\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 35 - Titulo */
  echo("                    <td width=\"\" class=\"itens\">".RetornaFraseDaLista($lista_frases,35)."</td>\n");

  /* 13 - Data */
  echo("                    <td width=\"14%\" align=\"center\">".RetornaFraseDaLista($lista_frases,13)."</td>\n");

  /* 14 - Compartilhar */
  echo("                    <td width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases,14)."</td>\n");
//   echo("<td width=\"8%\" align=\"center\">Avalia&ccedil;&atilde;o</td>\n");
  echo("                  </tr>\n");

  if ($linha_item['tipo_compartilhamento']=="T"){
    /* 16 - Totalmente Compartilhado */
    $compartilhamento=RetornaFraseDaLista($lista_frases,16);
  }
  else {
    /* 17 - Compartilhado com Formadores */
    $compartilhamento=RetornaFraseDaLista($lista_frases,17);
  }

  echo("                  <tr id=\"tr_".$linha_item['cod_item']."\">\n");
  echo("                    <td class=\"itens\">\n");

  $titulo="<span id=\"tit_".$linha_item['cod_item']."\" >".$linha_item['titulo']."</span>";

  echo($titulo);

  echo("                    <td align=\"center\">\n");
  echo("                      <span id=data_".$linha_item['cod_item'].">".UnixTime2DataHora($linha_item['data'])."</span>\n");
  echo("                    </td>\n");

  echo("                    <td align=\"center\">".$compartilhamento."</td>\n");

  echo("                  </tr>\n");
  if ($linha_item['texto']!=""){
    echo("                  <tr class=\"head\">\n");
    /* 31 - Texto  */
    echo("                    <td colspan=\"4\" class=\"itens\">".RetornaFraseDaLista($lista_frases,31)."</td>\n");    
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td colspan=\"4\" class=\"itens\">\n");
    $texto="<span id=\"text_".$linha_item['cod_item']."\">".AjustaParagrafo($linha_item['texto'])."</span>";
    echo("                      ".$texto);
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  $diretorio = $diretorio_arquivos."/".$cod_curso."/".$dir."/item/".$cod_item;

  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);

  $num_arq_vis=RetornaNumArquivosVisiveis($lista_arq);

  if ((count($num_arq_vis)>0) && (!empty($lista_arq))){
    echo("                  <tr class=\"head\">\n");
    /* 32 - Arquivos */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,32)."</td>\n");
    echo("                  </tr>\n");

    if (!empty($lista_arq)){

      $conta_arq=0;

      echo("                  <tr>\n");
      echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
      // Procuramos na lista de arquivos se existe algum visivel
      $ha_visiveis = false;

      foreach($lista_arq as $cod => $linha){
        if ($linha['Arquivo'] != "")
          $ha_visiveis = !($linha['Status']);
        if($ha_visiveis) break;
      }

      if ($ha_visiveis){
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
                if ($eformador){
                  $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                  $espacos2.="  ";
                }
                else{
                  $espacos.="";
                  $espacos2.="";
                }
              }

              $caminho_arquivo = $dir_item_temp['link'].ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);

              if ($linha['Arquivo'] != ""){

                if ($linha['Diretorio']!=""){
                  $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                  $espacos2.="  ";
                }


                if ($linha['Status']) $arqOculto="arqOculto='sim'";
                else $arqOculto="arqOculto='nao'";

                if (eregi(".zip$",$linha['Arquivo'])){
                  // arquivo zip
                  $imagem    = "<img alt=\"\" src=\"../imgs/arqzip.gif\" border=\"0\" />";
                  $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVerURL('".$caminho_arquivo."');\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
                }
                else{
                  // arquivo comum
                  $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                  $tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVerURL('".$caminho_arquivo."');\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqOculto.">";
                }

                $tag_fecha = "</span>";

                echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

                echo("                        ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb)");

                echo("<span id=\"local_oculto_".$conta_arq."\">");
                if ($linha['Status']) 
                  // 87 - Oculto
                    echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style='color:red;'>".RetornaFraseDaLista($lista_frases,87)."</span></span>");
                echo("</span>\n");
                echo("                          ".$espacos2."<br />\n");
                echo("                          ".$espacos2."</span>\n");
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
                  echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onClick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\" />\n");
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
      }
      echo("                      <script type=\"text/javascript\" language=\"JavaScript\">js_conta_arq=".$conta_arq.";</script>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }

    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  $lista_url=RetornaEnderecosMaterial($sock, $tabela, $cod_item);

  if (!empty($lista_url)){

    echo("                  <tr class=\"head\">\n");
      /* 33 - Endereï¿½os */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,33)."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");

    foreach ($lista_url as $cod => $linha){
  
      $linha['endereco'] = RetornaURLValida($linha['endereco']);
      echo("                      <span id='end_".$linha['cod_endereco']."'>\n");

      if ($linha['nome']!="")
      {
        echo("                      <span class=\"link\" onClick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['nome']."</span>&nbsp;&nbsp;(".$linha['endereco'].")");
      }
      else
      {
        echo("                      <span class=\"link\" onClick=\"WindowOpenVerURL('".ConverteSpace2Mais($linha['endereco'])."');\">".$linha['endereco']."</span>");
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

  /* Forms */
  echo("    <form method=\"post\" name=\"frmImportar\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_categoria\" value=\"".$cod_categoria."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso_import\" value=\"".$cod_curso_import."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\">\n");
  echo("      <input type=\"hidden\" name=\"cod_topico_raiz_import\" value=\"\">\n");
  echo("      <input type=\"hidden\" name=\"curso_compartilhado\" value=\"".$curso_compartilhado."\">\n");
  echo("      <input type=\"hidden\" name=\"curso_extraido\" value=\"".$curso_extraido."\">\n");
  echo("      <input type=\"hidden\" name=\"tipo_curso\" value=\"".$tipo_curso."\">\n");
  if ('E' == $tipo_curso)
  {
    echo("      <input type=\"hidden\" name=\"data_inicio\" value=\"".$data_inicio."\">\n");
    echo("      <input type=\"hidden\" name=\"data_fim\" value=\"".$data_fim."\">\n");
  }
  echo("    </form>\n");

  echo("  </body>\n");
  echo("</html>\n");
?>

