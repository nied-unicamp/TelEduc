<?php
/*
 <!--
 -------------------------------------------------------------------------------

 Arquivo : cursos/aplic/exercicios/ver_aplicado_popup.php

 TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
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

 Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
 Unicamp - Universidade Estadual de Campinas
 Cidade Universitï¿½ia "Zeferino Vaz"
 Bloco V da Reitoria - 2o. Piso
 CEP:13083-970 Campinas - SP - Brasil

 http://www.nied.unicamp.br
 nied@unicamp.br

 ------------------------------------------------------------------------------
 -->
 */

/*==========================================================
  ARQUIVO : cursos/aplic/exercicios/ver_aplicado_popup.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");
  include("ver_gabarito.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamentoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AtualizaRespostaDoUsuarioDinamic");
  $objAjax->register(XAJAX_FUNCTION,"EditarRespostaQuestaoDissDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  // Descobre os diretorios de arquivo, para os portfolios com anexo
  $sock = Conectar("");
  $diretorio_arquivos = RetornaDiretorio($sock, 'Arquivos');
  $diretorio_temp = RetornaDiretorio($sock, 'ArquivosWeb');
  Desconectar($sock);

  $cod_ferramenta = 23;
  $cod_resolucao = $_GET['cod_resolucao'];

  include("../topo_tela.php");

  $resolucao = RetornaResolucao($sock,$cod_resolucao);
  $exercicio = RetornaExercicio($sock,$resolucao['cod_exercicio']);
  $questoes = RetornaQuestoesExercicio($sock,$resolucao['cod_exercicio']);
  $aplicado = RetornaDadosExercicioAplicado($sock,$resolucao['cod_exercicio']);
  $ehFormador = EFormador($sock,$cod_curso,$cod_usuario);
  $VerificaCorrigida = VerificaSeCorrigida($cod_curso,$cod_resolucao);

  if($ehFormador || $VerificaCorrigida){

  /*********************************************************/
  /* inï¿½io - JavaScript */
  echo("  <script  type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");

  echo("  <script  type=\"text/javascript\" language=\"javascript\">\n\n");

  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
  echo("    var Xpos, Ypos;\n");
  echo("    var js_cod_item;\n");
  echo("    var js_comp = new Array();\n");
  echo("    var cod_comp;");
  echo("    var editaTexto = 0;\n");
  echo("    var cancelarElemento = null;\n");
  echo("    var cancelarTodos = 0;\n\n");

  echo("    if (isNav)\n");
  echo("    {\n");
  echo("      document.captureEvents(Event.MOUSEMOVE);\n");
  echo("    }\n\n");

  echo("    document.onmousemove = TrataMouse;\n\n");

  echo("    function TrataMouse(e)\n");
  echo("    {\n");
  echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("    }\n\n");

  echo("    function getPageScrollY()\n");
  echo("    {\n");
  echo("      if (isNav)\n");
  echo("        return(window.pageYOffset);\n");
  echo("      if (isIE)\n");
  echo("        return(document.body.scrollTop);\n");
  echo("    }\n\n");

  echo("    function AjustePosMenuIE()\n");
  echo("    {\n");
  echo("      if (isIE)\n");
  echo("        return(getPageScrollY());\n");
  echo("      else\n");
  echo("        return(0);\n");
  echo("    }\n\n");

  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
  if ($tela_formador){
    echo("      cod_comp = getLayer(\"comp\");\n");
    echo("      startList();\n");
  }
  foreach ($questoes as $cod => $linha_item)
    echo("AbreResposta(".$linha_item['cod_questao'].");");
  echo("    }\n\n");

  echo("    function WindowOpenVer(id)\n");
  echo("    {\n");
  echo("      window.open(\"" . $dir_questao_temp['link'] . "\"+id,'Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("    }\n\n");

  echo("    function OpenWindowPerfil(id)\n");
  echo("    {\n");
  echo("      window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("      return(false);\n");
  echo("    }\n");

  if ($tela_formador){
    echo("    function EscondeLayers()\n");
    echo("    {\n");
    echo("      hideLayer(cod_comp);\n");
    echo("    }\n");

    echo("    function MostraLayer(cod_layer, ajuste)\n");
    echo("    {\n");
    echo("      EscondeLayers();\n");
    echo("      moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("      showLayer(cod_layer);\n");
    echo("    }\n");

    echo("    function EscondeLayer(cod_layer)\n");
    echo("    {\n");
    echo("      hideLayer(cod_layer);\n");
    echo("    }\n");

    echo("      function AtualizaComp(js_tipo_comp)\n");
    echo("      {\n");
    echo("        if ((isNav) && (!isMinNS6)) {\n");
    echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
    echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'),document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_N'));\n");
    echo("        } else {\n");
    echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("            document.form_comp.cod_item.value=js_cod_item;\n");
    echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_T'),document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_N'));\n");
    echo("        }\n");
    echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
    echo("        if (js_tipo_comp=='T') {\n");
    echo("          tipo_comp[0].innerHTML=imagem;\n");
    echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
    echo("        }else if (js_tipo_comp=='F'){\n");
    echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[1].innerHTML=imagem;\n");
    echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
    echo("        }else{\n");
    echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
    echo("          tipo_comp[2].innerHTML=imagem;\n");
    echo("        }\n");
    echo("      }\n\n");
  }
  echo("    function AbreResposta(cod_questao)\n");
  echo("    {\n");
  echo("      document.getElementById(\"trResposta_\"+cod_questao).style.display = \"\";\n");
  echo("    }\n");

  echo("    function FechaResposta(cod_questao)\n");
  echo("    {\n");
  echo("      document.getElementById(\"trResposta_\"+cod_questao).style.display = \"none\";\n");
  echo("    }\n");

  echo("    function SelecionaAlternativa(cod_questao,posicao,max)\n");
  echo("    {\n");
  echo("      var resposta,i;\n");
  echo("      resposta=\"\";\n");
  echo("      for(i=0;i<max;i++)\n");
  echo("      {\n");
  echo("        if(i == posicao)\n");
  echo("          resposta = resposta + \"1\";\n");
  echo("        else\n");
  echo("          resposta = resposta + \"0\";\n");
  echo("      }\n");
  /* Frase #171 - Resposta incluída com sucesso */
  /* Frase #167 - Respondida */
  echo("      xajax_AtualizaRespostaDoUsuarioDinamic(".$cod_curso.",".$cod_resolucao.",cod_questao,resposta,\"".RetornaFraseDaLista($lista_frases, 171).".\",\"O\",\"".RetornaFraseDaLista($lista_frases, 167)."\");\n");
  echo("    }\n");

  echo("    function AlteraTexto(id){\n");
  echo("      if (editaTexto==-1 || editaTexto != id){\n");
  if ($tela_formador){
    echo("        CancelaTodos();\n");
  }
  //echo("        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);\n");
  echo("        conteudo = document.getElementById('text_'+id).innerHTML;\n");
  echo("        writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);\n");
  echo("        startList();\n");
  echo("        document.getElementById('text_'+id+'_text').focus();\n");
  echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("        editaTexto = id;\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function EdicaoTexto(codigo, id, valor){\n");
  echo("      var cod;\n");
  echo("      if (valor=='ok'){\n");
  echo("        cod = codigo.split(\"_\");\n");
  echo("        conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML;\n");
  /* Frase #58 - Texto */
  echo("        xajax_EditarRespostaQuestaoDissDinamic(".$cod_curso.",cod[0],cod[1],conteudo,\"".RetornaFraseDaLista($lista_frases, 58)."\");\n");
  echo("      }\n");
  echo("      else{\n");
  // Cancela Ediï¿½o
  //echo("        if (!cancelarTodos)\n");
  //echo("          xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
  echo("      }\n");
  echo("      document.getElementById(id).innerHTML=conteudo;\n");
  echo("      document.getElementById('resp_'+codigo).style.display= '';\n");
  echo("      editaTexto=-1;\n");
  echo("      cancelarElemento=null;\n");
  echo("    }\n\n");

  echo("    function CancelaTodos(){\n");
  echo("      EscondeLayers();\n");
  echo("      cancelarTodos=1;\n");
  echo("      if(cancelarElemento) {\n");
  echo("        cancelarElemento.onclick();\n");
  //echo("        xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);\n");
  echo("      }\n");
  echo("      cancelarTodos=0;\n");
  echo("    }\n");

  echo("    function Responder(id){\n");
  echo("      document.getElementById(\"resp_\"+id).style.display=\"none\";\n");
  echo("      AlteraTexto(id);\n");
  echo("    }\n");

  echo("  </script>\n\n");

  $objAjax->printJavascript();

  /* fim - JavaScript */
  /*********************************************************/

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  echo("<br><br>\n");
  /* Frase #1 - Exercicios */
  /* Frase #169 - Ver resolucao */
  $frase = RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 169);


  echo("          <h4>".$frase."</h4>\n");
  echo("<br>\n");

  /*Voltar*/ 
  //   /* 509 - Voltar */
  //  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  /*
  if($resolucao['cod_grupo'] != null)
  {
    $nome=NomeGrupo($sock,$resolucao['cod_grupo']);
    //Figura de Grupo
    $fig_exercicio = "<img alt=\"\" src=\"../imgs/icGrupo.gif\" border=\"0\" />";

    echo("          ".$fig_exercicio." <span class=\"link\" onclick=\"AbreJanelaComponentes(".$resolucao['cod_grupo'].");\">".$nome."</span>");
  }
  else
  {
    $nome=NomeUsuario($sock,$resolucao['cod_usuario'],$cod_curso);

    // Selecionando qual a figura a ser exibida ao lado do nome
    $fig_exercicio = "<img alt=\"\" src=\"../imgs/icPerfil.gif\" border=\"0\" />";

    echo("          ".$fig_exercicio." <span class=\"link\" onclick=\"OpenWindowPerfil(".$resolucao['cod_usuario'].");\" > ".$nome."</span>");
  }
  */
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  $cod = $resolucao['cod_usuario'];
  if($resolucao['cod_grupo'] != null)
  {
    $cod = $resolucao['cod_grupo'];
  }

  /* Texto #5 - Voltar */
  //echo("                  <li><a href='ver_exercicios.php?cod_curso=".$cod_curso."&visualizar=".$visualizar."&cod=".$cod."'>Voltar</a></li>\n");
  /* Texto #56 - Historico */
  //echo("                  <li><a href=''>Historico</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* Frase #13 - Titulo */
  echo("                    <td colspan=\"3\" class=\"alLeft\">".RetornaFraseDaLista($lista_frases, 13)."</td>\n");
  /* Frase #86 - Limite de entrega */
  echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 86)."</td>\n");
  /* Frase #57 - Compartilhamento */
  echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
  /* Frase #130 - Situacao */
  echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 130)."</td>\n");
  echo("                  </tr>\n");

  /* Frase #6 - Compartilhado com Formadores */
  if($resolucao['compartilhada'] == "F")
  $compartilhamento = RetornaFraseDaLista($lista_frases, 6);
  /* Frase #7 - Totalmente compartilhado */
  else if($resolucao['compartilhada'] == "T")
  $compartilhamento = RetornaFraseDaLista($lista_frases, 7);
  /* Frase #8 - Nao compartilhado */
  else
  $compartilhamento = RetornaFraseDaLista($lista_frases, 8);

  if($cod_usuario == $resolucao['cod_usuario'] || RetornaCodGrupoUsuario($sock,$cod_usuario) == $resolucao['cod_grupo'])
  $compartilhamento = "<span id=\"comp_".$resolucao['cod_resolucao']."\" class=\"link\" onclick=\"js_cod_item=".$resolucao['cod_resolucao'].";AtualizaComp('".$resolucao['compartilhada']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";

  $situacao = "";
  if($resolucao['submetida'] == 'S')
  $situacao .= "<span class=\"\">(e)</span>";
  if($resolucao['corrigida'] == 'S')
  $situacao .= "<span class=\"avaliada\">(a)</span>";

  echo("                  <tr>\n");
  echo("                    <td colspan=\"3\" class=\"alLeft\">".$exercicio['titulo']."</td>\n");
  echo("                    <td width=\"10%\">".UnixTime2DataHora($aplicado['dt_limite_submissao'])."</td>\n");
  echo("                    <td width=\"15%\">".$compartilhamento."</td>\n");
  echo("                    <td width=\"10%\">".$situacao."</td>\n");
  echo("                  </tr>\n");

  echo("                  <tr class=\"head\">\n");
  /* Frase #58 - Texto */
  echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 58)."</td>\n");
  echo("                  </tr>\n");

  $texto = $exercicio['texto'];
  if($texto == "" || $texto == null)
  /* Frase #165 - Nenhum texto foi cadastrado para esse exercicio. */
  $texto = RetornaFraseDaLista($lista_frases, 165);

  echo("                  <tr>\n");
  echo("                    <td colspan=\"6\" class=\"alLeft\">".$texto."</td>\n");
  echo("                  </tr>\n");

  $dir_exercicio_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $resolucao['cod_exercicio'], $diretorio_arquivos, $diretorio_temp,"exercicio");
  $lista_arq = RetornaArquivosQuestao($cod_curso, $dir_exercicio_temp['link']);

  $num_arq_vis = RetornaNumArquivosVisiveis($lista_arq);

      if (is_array($lista_arq) && count($lista_arq)>0){

      echo("                  <tr class=\"head\">\n");
      /* 12 - Arquivos */
      echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases,12)."</td>\n");
      echo("                  </tr>\n");

        $conta_arq=0;

        echo("                  <tr>\n");
        echo("                    <td class=\"itens\" colspan=\"6\" id=\"listFiles\">\n");
        // Procuramos na lista de arquivos se existe algum visivel
        $ha_visiveis = $num_arq_vis > 0;


        if (($ha_visiveis) || ($exercicio['situacao'] == 'C')){

          $nivel_anterior=0;
          $nivel=-1;

          foreach($lista_arq as $cod => $linha){
            $linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
            if (!($linha['Arquivo']=="" && $linha['Diretorio']=="")){
              if ((!$linha['Status']) || ($exercicio['situacao'] == 'C')){
                $nivel_anterior=$nivel;
                $espacos="";
                $espacos2="";
                $temp=explode("/",$linha['Diretorio']);
                $nivel=count($temp)-1;
                for ($c=0;$c<=$nivel;$c++){
                  if($exercicio['situacao']=='C'){
                    $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                    $espacos2.="  ";
                  }
                  else{
                    $espacos.="";
                    $espacos2.="";
                  }
                }

                $caminho_arquivo = $dir_exercicio_temp['link'].$linha['Diretorio']."/".$linha['Arquivo'];
                $caminho_arquivo = preg_replace("/\/\//", "/", $caminho_arquivo);
  // echo($caminho_arquivo);

                if ($linha['Arquivo'] != ""){
                  if ($linha['Diretorio']!=""){
                    $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                    $espacos2.="  ";
                  }

                  if ($linha['Status']) $arqOculto="arqOculto='sim'";
                  else $arqOculto="arqOculto='nao'";

                  if (eregi(".zip$",$linha['Arquivo'])){
                    // arquivo zip
                    $imagem    = "<img src=\"../imgs/arqzip.gif\" border=0 alt=\"\"/>";
                    $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."');return false;\" tipoArq=\"zip\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
                  }
                  else{
                    // arquivo comum
                    //imagem
                    if((eregi(".jpg$",$linha['Arquivo'])) || eregi(".png$",$linha['Arquivo']) || eregi(".gif$",$linha['Arquivo']) || eregi(".jpeg$",$linha['Arquivo'])) {
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqimg.gif\" border=\"0\" />";
                    //doc
                    }else if(eregi(".doc$",$linha['Arquivo'])){
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqdoc.gif\" \"border=\"0\" />";
                    //pdf
                    }else if(eregi(".pdf$",$linha['Arquivo'])){
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqpdf.gif\" border=\"0\" />";
                    //html
                    }else if((eregi(".html$",$linha['Arquivo'])) || (eregi(".htm$",$linha['Arquivo']))){
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqhtml.gif\" border=\"0\" />";
                    }else if((eregi(".mp3$",$linha['Arquivo'])) || (eregi(".mid$",$linha['Arquivo']))) {
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqsnd.gif\" border=\"0\" />";
                    }else{
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                    }
                    $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return false;\" tipoArq=\"comum\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" ".$arqOculto.">";
                  }

                  $tag_fecha = "</a>";

                  echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

                  if ($exercicio['situacao'] == 'C'){
                    echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                  }

                  echo("                          ".$espacos2.$espacos.$imagem." ".$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb)\n");

                  echo("<span id=\"local_oculto_".$conta_arq."\">");
                  if ($linha['Status']){
                    // 70 - Oculto
                      echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style='color:red;'>".RetornaFraseDaLista($lista_frases,70)."</span></span>");
                  }
                  echo("</span>\n");
                  echo("                          ".$espacos2."<br />\n");
                  echo("                        ".$espacos2."</span>\n");
                }

                else if (($exercicio['situacao'] == 'C') || (haArquivosVisiveisDir($linha['Diretorio'], $lista_arq))){
                  if ($nivel_anterior>=$nivel){
                    $i=$nivel_anterior-$nivel;
                    $j=$i;
                    $espacos3="";
                    do{
                      $espacos3.="  ";
                      $j--;
                    }while($j>=0);

                    while($i>=0){
                      echo("                      ".$espacos3."</span>\n");
                      $i--;
                    }
                  }
                  // pasta
                  $imagem    = "<img src=\"../imgs/pasta.gif\" border=0 alt=\"\" />";
                  echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                  echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
                  if ($exercicio['situacao'] == 'C'){
                    echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                  }
                  echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
                  echo("                        ".$espacos2."<br />\n");
               }
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
      }

  echo("                  <tr class=\"head\">\n");
  /* Frase #59 - Questoes */
  echo("                    <td colspan=\"6\">".RetornaFraseDaLista($lista_frases, 59)."</td>\n");
  echo("                  </tr>\n");

  echo("                  <tr class=\"head01\">\n");
  /* Frase #13 - Titulo */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases, 13)."</td>\n");
  /* Frase #14 - Nota */
  echo("                    <td width=\"5%\">".RetornaFraseDaLista($lista_frases, 14)."</td>\n");
  /* Frase #15 - Valor */
  echo("                    <td width=\"5%\">".RetornaFraseDaLista($lista_frases, 15)."</td>\n");
  /* Frase #60 - Tipo */
  echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 60)."</td>\n");
  /* Frase #61 - Topico */
  echo("                    <td width=\"15%\">".RetornaFraseDaLista($lista_frases, 61)."</td>\n");
  /* Frase #16 - Status */
  echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases, 16)."</td>\n");
  echo("                  </tr>\n");

  if ((count($questoes)>0)&&($questoes != null))
  {
    foreach ($questoes as $cod => $linha_item)
    {
      $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
      $icone_correto = " <img src=\"../imgs/certo.png\" alt=\"resposta certa\" border=\"0\" /> ";
      $icone_errado = " <img src=\"../imgs/errado.png\" alt=\"resposta errada\" border=\"0\" /> ";
      $icone_vazio = " <img src=\"../imgs/branco.png\" alt=\"alternativa certa\" border=\"0\" /> ";
      $tipo = $linha_item['tp_questao'];
      if($tipo == 'O'){
        /* 159 - Objetiva */
        $tipo_ext = RetornaFraseDaLista($lista_frases, 159);
      } elseif ($tipo == 'M') {
        /* 212 - Multipla escolha */
        $tipo_ext = RetornaFraseDaLista($lista_frases, 212);
      } elseif($tipo == 'D'){
        /* 160 - Dissertativa */
        $tipo_ext = RetornaFraseDaLista($lista_frases, 160);
      }
      $titulo = $linha_item['titulo'];
      $topico = RetornaNomeTopico($sock,$linha_item['cod_topico']);
      if($tipo == 'O'){
        $nota = VerificaResolucaoO($cod_curso,$linha_item['cod_questao'],$cod_resolucao);
        $respostaCorreta = RetornaGabaritoQuestaoObj($sock,$linha_item['cod_questao']);
      }
      elseif ($tipo == 'M') {
        $nota = VerificaResolucaoM($cod_curso,$linha_item['cod_questao'],$cod_resolucao);
        $respostaCorreta = RetornaGabaritoQuestaoMult($sock,$linha_item['cod_questao']);
      }
      else if($tipo == 'D')
        $nota = VerificaResolucaoD($cod_curso,$linha_item['cod_questao'],$cod_resolucao);
      else
        $nota = "";

      $valor = $linha_item['valor'];
      if($linha_item['tp_questao'] == 'O' || $linha_item['tp_questao'] == 'M'){
        //$sock2 = Conectar($cod_curso);
        //$alternativas = RetornaAlternativas($sock2,$linha_item['cod_questao']);
        $alternativas = RetornaAlternativas($sock,$linha_item['cod_questao']);
        //Desconectar($sock2);
      }

      $resposta = RetornaRespostaQuestao($sock,$cod_resolucao,$linha_item['cod_questao'],$linha_item['tp_questao']);

      $dir_questao_temp = CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $linha_item['cod_questao'], $diretorio_arquivos, $diretorio_temp, "questao");
      $lista_arq = RetornaArquivosQuestao($cod_curso, $dir_questao_temp['link']);

      /* Frase #166 - Nao respondida */
      /* Frase #167 - Respondida */
      if($resposta == null)
        $status = RetornaFraseDaLista($lista_frases, 166);
      else
        $status = RetornaFraseDaLista($lista_frases, 167);

      echo("                  <tr id=\"trQuestao_".$linha_item['cod_questao']."\">\n");
      echo("                    <td align=left>".$icone."<span class=\"link\" onclick=\"AbreResposta(".$linha_item['cod_questao'].");\">".$titulo."</span>");
      /* Se existir nota e for diferente do valor*/
      if( ($linha_item['tp_questao'] == 'O' || $linha_item['tp_questao'] == 'M') && $nota != $valor && $nota != ""){
        echo($icone_errado."</td>\n");
        echo("                <td>".$nota."</td>\n");
      }
      /* Se existir nota e for igual ao valor*/
      else if( ($linha_item['tp_questao'] == 'O' || $linha_item['tp_questao'] == 'M')&& $nota == $valor && $nota != ""){
        echo($icone_correto."</td>\n");
        echo("                <td>".$nota."</td>\n");
      }
      else if($linha_item['tp_questao'] == 'D' && $nota == $valor && $nota != ""){
        echo($icone_correto."</td>\n");
        echo("                <td>".$nota."</td>\n");
      }
      else if($linha_item['tp_questao'] == 'D' && $nota != $valor && $nota != ""){
        echo($icone_errado."</td>\n");
        echo("                <td>".$nota."</td>\n");
      }
      else{
        echo("                </td>\n");
        echo("                <td></td>\n");
      }

      echo("                    <td>".$valor."</td>\n");
      echo("                    <td>".$tipo_ext."</td>\n");
      echo("                    <td>".$topico."</td>\n");
      echo("                    <td>".$status."</td>\n");
      echo("                  </tr>\n");
      echo("                  <tr id=\"trResposta_".$linha_item['cod_questao']."\" style=\"display:none;\">\n");
      echo("                    <td style=\"width:100px\" colspan=\"5\" align=\"left\">\n");
      echo("                      <dl class=\"portlet\">\n");
      /* Frase #17 - Enunciado */
      echo("                        <dt class=\"portletHeader\">".RetornaFraseDaLista($lista_frases, 17)."</dt>\n");
      echo("                          <dd class=\"portletItem\">".$linha_item['enunciado']."</dd>\n");

    if (is_array($lista_arq) && count($lista_arq)>0){
        /* Frase #12 - Arquivos */
        echo("                        <dt class=\"portletHeader\">".RetornaFraseDaLista($lista_frases, 12)."</dt>\n");
        echo("                          <dd class=\"portletItem\">\n");

        $conta_arq=0;

        $ha_visiveis = $num_arq_vis > 0;


        if (($ha_visiveis) || ($exercicio['situacao'] == 'C')){

          $nivel_anterior=0;
          $nivel=-1;

          foreach($lista_arq as $cod => $linha){
            $linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
            if (!($linha['Arquivo']=="" && $linha['Diretorio']=="")){
              if ((!$linha['Status']) || ($exercicio['situacao'] == 'C')){
                $nivel_anterior=$nivel;
                $espacos="";
                $espacos2="";
                $temp=explode("/",$linha['Diretorio']);
                $nivel=count($temp)-1;
                for ($c=0;$c<=$nivel;$c++){
                  if($exercicio['situacao']=='C'){
                    $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                    $espacos2.="  ";
                  }
                  else{
                    $espacos.="";
                    $espacos2.="";
                  }
                }

                $caminho_arquivo = $dir_questao_temp['link'].$linha['Diretorio']."/".$linha['Arquivo'];
                $caminho_arquivo = preg_replace("/\/\//", "/", $caminho_arquivo);
  // echo($caminho_arquivo);

                if ($linha['Arquivo'] != ""){
                  if ($linha['Diretorio']!=""){
                    $espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
                    $espacos2.="  ";
                  }

                  if ($linha['Status']) $arqOculto="arqOculto='sim'";
                  else $arqOculto="arqOculto='nao'";

                  if (eregi(".zip$",$linha['Arquivo'])){
                    // arquivo zip
                    $imagem    = "<img src=\"../imgs/arqzip.gif\" border=0 alt=\"\"/>";
                    $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."');return false;\" tipoArq=\"zip\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
                  }
                  else{
                    // arquivo comum
                    //imagem
                    if((eregi(".jpg$",$linha['Arquivo'])) || eregi(".png$",$linha['Arquivo']) || eregi(".gif$",$linha['Arquivo']) || eregi(".jpeg$",$linha['Arquivo'])) {
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqimg.gif\" border=\"0\" />";
                    //doc
                    }else if(eregi(".doc$",$linha['Arquivo'])){
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqdoc.gif\" \"border=\"0\" />";
                    //pdf
                    }else if(eregi(".pdf$",$linha['Arquivo'])){
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqpdf.gif\" border=\"0\" />";
                    //html
                    }else if((eregi(".html$",$linha['Arquivo'])) || (eregi(".htm$",$linha['Arquivo']))){
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqhtml.gif\" border=\"0\" />";
                    }else if((eregi(".mp3$",$linha['Arquivo'])) || (eregi(".mid$",$linha['Arquivo']))) {
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqsnd.gif\" border=\"0\" />";
                    }else{
                      $imagem    = "<img alt=\"\" src=\"../imgs/arqp.gif\" border=\"0\" />";
                    }
                    $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return false;\" tipoArq=\"comum\" nomeArq=\"".ConverteUrl2Html($caminho_arquivo)."\" ".$arqOculto.">";
                  }

                  $tag_fecha = "</a>";

                  echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

                  if ($exercicio['situacao'] == 'C'){
                    echo("                          ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                  }

                  echo("                          ".$espacos2.$espacos.$imagem." ".$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha['Tamanho']/1024),2)."Kb)\n");

                  echo("<span id=\"local_oculto_".$conta_arq."\">");
                  if ($linha['Status']){
                    // 70 - Oculto
                      echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style='color:red;'>".RetornaFraseDaLista($lista_frases,70)."</span></span>");
                  }
                  echo("</span>\n");
                  echo("                          ".$espacos2."<br />\n");
                  echo("                        ".$espacos2."</span>\n");
                }

                else if (($exercicio['situacao'] == 'C') || (haArquivosVisiveisDir($linha['Diretorio'], $lista_arq))){
                  if ($nivel_anterior>=$nivel){
                    $i=$nivel_anterior-$nivel;
                    $j=$i;
                    $espacos3="";
                    do{
                      $espacos3.="  ";
                      $j--;
                    }while($j>=0);

                    while($i>=0){
                      echo("                      ".$espacos3."</span>\n");
                      $i--;
                    }
                  }
                  // pasta
                  $imagem    = "<img src=\"../imgs/pasta.gif\" border=0 alt=\"\" />";
                  echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
                  echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
                  if ($exercicio['situacao'] == 'C'){
                    echo("                        ".$espacos2."<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBoxArq(1);\" id=\"chkArq_".$conta_arq."\" />\n");
                  }
                  echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
                  echo("                        ".$espacos2."<br />\n");
               }
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
        echo("                          </dd>\n");
      }

      if($linha_item['tp_questao'] == 'O' || $linha_item['tp_questao'] == 'M')
      {
        /* Frase #18 - Alternativas */
        echo("                        <dt class=\"portletHeader\">".RetornaFraseDaLista($lista_frases, 18)."</dt>\n");
        echo("                          <dd class=\"portletItem\">\n");
        foreach ($alternativas as $cod => $linha_alt)
        {
          if($resposta != null && $resposta[$cod] == "1")
          $selected = "checked";
          else
          $selected = "";
          if($respostaCorreta){
            if($respostaCorreta[$cod] == $resposta[$cod]){
              if ($respostaCorreta[$cod] == 1)
                echo($icone_correto);
              else
                echo($icone_vazio);
              if($linha_item['tp_questao'] == 'O')
                echo("                            <input  type=\"radio\" size=\"2\" name=\"resposta_".$linha_item['cod_questao']."\" disabled=\"disabled\" onclick=\"SelecionaAlternativa(".$linha_item['cod_questao'].",".$cod.",".count($alternativas).");\" ".$selected.">&nbsp;&nbsp;&nbsp;".$linha_alt['texto']."\n");
              if($linha_item['tp_questao'] == 'M')
                echo("                            <input  type=\"checkbox\" size=\"2\" name=\"resposta_".$linha_item['cod_questao']."\" disabled=\"disabled\" onclick=\"SelecionaAlternativa(".$linha_item['cod_questao'].",".$cod.",".count($alternativas).");\" ".$selected.">&nbsp;&nbsp;&nbsp;".$linha_alt['texto']."\n");
              echo("                            <br />\n");
            }
            else{
              if ($respostaCorreta[$cod] == 1)
                echo($icone_correto);
              else 
                echo($icone_errado);
              if($linha_item['tp_questao'] == 'O')
                echo("                            <input  type=\"radio\" size=\"2\" name=\"resposta_".$linha_item['cod_questao']."\" disabled=\"disabled\" onclick=\"SelecionaAlternativa(".$linha_item['cod_questao'].",".$cod.",".count($alternativas).");\" ".$selected.">&nbsp;&nbsp;&nbsp;".$linha_alt['texto']."\n");
              if($linha_item['tp_questao'] == 'M')
                echo("                            <input  type=\"checkbox\" size=\"2\" name=\"resposta_".$linha_item['cod_questao']."\" disabled=\"disabled\" onclick=\"SelecionaAlternativa(".$linha_item['cod_questao'].",".$cod.",".count($alternativas).");\" ".$selected.">&nbsp;&nbsp;&nbsp;".$linha_alt['texto']."\n");
              echo("                            <br />\n");
            }
          }
          else{
            if($linha_item['tp_questao'] == 'O')
              echo("                            <input  type=\"radio\" size=\"2\" name=\"resposta_".$linha_item['cod_questao']."\" disabled=\"disabled\" onclick=\"SelecionaAlternativa(".$linha_item['cod_questao'].",".$cod.",".count($alternativas).");\" ".$selected.">&nbsp;&nbsp;&nbsp;".$linha_alt['texto']."\n");
            if($linha_item['tp_questao'] == 'M')
              echo("                            <input  type=\"checkbox\" size=\"2\" name=\"resposta_".$linha_item['cod_questao']."\" disabled=\"disabled\" onclick=\"SelecionaAlternativa(".$linha_item['cod_questao'].",".$cod.",".count($alternativas).");\" ".$selected.">&nbsp;&nbsp;&nbsp;".$linha_alt['texto']."\n");
            echo("                            <br />\n");
          }
        }
        echo("                          </dd>\n");
      }
      else if($linha_item['tp_questao'] == 'D')
      {
        /* Frase #20 - Resposta */
        echo("                        <dt class=\"portletHeader\">".RetornaFraseDaLista($lista_frases, 20)."</dt>\n");
        echo("                          <dd class=\"portletItem\">\n");
        echo("                            <div class=\"divRichText\">\n");
        echo("                              <span id=\"text_".$cod_resolucao."_".$linha_item['cod_questao']."\">");
        if($resposta != null)
        echo($resposta);
        echo("                              </span>\n");
        echo("                            </div>\n");
        $gabarito = VerificaGabaritoD($cod_curso,$linha_item['cod_questao']);
        if($gabarito){
          /* Frase #103 - Gabarito */
          echo("                        <dt class=\"portletHeader\">".RetornaFraseDaLista($lista_frases, 103)."</dt>\n");
          echo("                          <dd class=\"portletItem\">\n");
          echo($gabarito);
          echo("                          </dd>\n");
        }
        $comentario = VerificaComentario($cod_curso,$linha_item['cod_questao'],$cod_resolucao);
        if($comentario){
          /* Frase #168 - Comentario */
          echo("                        <dt class=\"portletHeader\">".RetornaFraseDaLista($lista_frases, 168)."</dt>\n");
          echo("                          <dd class=\"portletItem\">\n");
          echo($comentario);
          echo("                          </dd>\n");
        }
        echo("                          </dd>\n");

        //echo("                          <dd class=\"portletFooter\" id=\"resp_".$cod_resolucao."_".$linha_item['cod_questao']."\"><span class=\"link\" onclick=\"Responder('".$cod_resolucao."_".$linha_item['cod_questao']."');\">Editar resposta</span></dd>\n");
      }
      echo("                      </dl>\n");
      echo("                    </td>\n");
      /* Frase #27 - Fechar */
      echo("                    <td><span class=\"link\" onclick=\"FechaResposta(".$linha_item['cod_questao'].");\">".RetornaFraseDaLista($lista_frases, 27)."</span></td>\n");
      echo("                  </tr>\n");

    }
  }
  echo("                </table>\n");
  if($resolucao['submetida'] == 'N')
  /* TODO: pegar frase do BD: Entregar */
  //echo("                <div align=\"right\"><input type=\"button\" class=\"input\" value='Entregar'></div>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
      echo("          <br />\n");
      /* 509 - voltar, 510 - topo */
      echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");



  echo("        </td>\n");
  echo("      </tr>\n");

  //include("../tela2.php");

  if($tela_formador)
  {
    /* Mudar Compartilhamento */
    echo("    <div class=\"popup\" id=\"comp\">\n");
    /* 13 - Fechar (ger) */
    echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"".RetornaFraseDaLista($lista_frases_geral,13)."\" border=\"0\" /></span></div>\n");
    echo("      <div class=\"int_popup\">\n");
    echo("        <form name=\"form_comp\" action=\"\" id=\"form_comp\">\n");
    echo("          <input type=\"hidden\" name=\"cod_curso\"   value=\"".$cod_curso."\" />\n");
    echo("          <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
    echo("          <input type=\"hidden\" name=\"cod_item\"    value=\"\" />\n");
    echo("          <input type=\"hidden\" name=\"tipo_comp\"   id=\"tipo_comp\" value=\"\" />\n");
    echo("          <input type=\"hidden\" name=\"texto\"       id=\"texto\"     value=\"Texto\" />\n");
    echo("          <ul class=\"ulPopup\">\n");
    echo("            <li onClick=\"document.getElementById('tipo_comp').value='T'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Totalmente Compartilhado', 'R'); EscondeLayers();\">\n");
    echo("              <span id=\"tipo_comp_T\" class=\"check\"></span>\n");
    /* Frase #7 - Totalmente compartilhado */
    echo("              <span>".RetornaFraseDaLista($lista_frases, 7)."</span>\n");
    echo("            </li>\n");
    echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores', 'R'); EscondeLayers();\">\n");
    echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
    /* Frase #6 - Compartilhado com formadores */
    echo("              <span>".RetornaFraseDaLista($lista_frases, 6)."</span>\n");
    echo("            </li>\n");
    echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'R'); EscondeLayers();\">\n");
    echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
    /* Frase #8 - Nao Compartilhado */
    echo("              <span>".RetornaFraseDaLista($lista_frases, 8)."</span>\n");
    echo("            </li>\n");
    echo("          </ul>\n");
    echo("        </form>\n");
    echo("      </div>\n");
    echo("    </div>\n");
  }
  }else{
    header("Location:resolver.php?cod_curso=".$cod_curso."&cod_resolucao=".$cod_resolucao);
  }
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>