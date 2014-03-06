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

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("agenda.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"EditarTitulo");
  $objAjax->register(XAJAX_FUNCTION,"EditarTexto");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"AbreEdicao");
  $objAjax->register(XAJAX_FUNCTION,"AcabaEdicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirArquivo");
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

  $cod_ferramenta=1;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=4;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("criarAgenda", 96, 0);
  $feedbackObject->addAction("anexar", 51, 98);
  $feedbackObject->addAction("descompactar", 99, 100);
  $feedbackObject->addAction("selecionar_entrada", 105, 0);
  $feedbackObject->addAction("retirar_entrada", 55, 0);

  $dir_name = "agenda";
  $dir_item_temp=CriaLinkVisualizar($sock,$dir_name, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);
  /* Verifica se o usuario eh formador. */  
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);

  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor.js\"></script>");
  echo("    <script type=\"text/javascript\" src=\"../bibliotecas/ckeditor/ckeditor_biblioteca.js\"></script>");
  echo("    <script type='text/javascript' src='../bibliotecas/dhtmllib.js'></script>\n");

  echo("    <script type=\"text/javascript\">\n\n");

  echo("      var cod_item='".$cod_item."';\n");
  echo("      var cod_curso='".$cod_curso."';\n");
  echo("      var cod_usuario='".$cod_usuario."';\n");
  echo("      var origem='".$origem."';\n");
  echo("      var num_apagados = '0';\n");
  /* (ger) 18 - Ok */
  // Texto do bot�o Ok do ckEditor
  echo("    var textoOk = '".RetornaFraseDaLista($lista_frases_geral, 18)."';\n\n");
  /* (ger) 2 - Cancelar */
  // Texto do bot�o Cancelar do ckEditor
  echo("    var textoCancelar = '".RetornaFraseDaLista($lista_frases_geral, 2)."';\n\n");

  echo("      function TemCertezaAtivar()\n");
  echo("      {\n");
  /* 57 - Tem certeza que deseja ativar esta agenda? */
  /* 58 - (Uma vez ativada, n�o haver� como desativ�-la) */
  echo("        return(confirm(\"".RetornaFraseDaLista($lista_frases,57)."\\n".RetornaFraseDaLista($lista_frases,58)."\"));\n");
  echo("      }\n");

  echo("      function Ativar()\n");
  echo("      {\n");
  echo("        if(TemCertezaAtivar())\n");
  echo("        {\n");
  echo("          window.location='acoes_linha.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1&cod_item=".$cod_item."&acao=ativaragenda';\n");
  echo("        }\n");
  echo("        return false;\n");
  echo("      }\n");

  echo("      function WindowOpenVer(id)\n");
  echo("      {\n");
  echo("         window.open('".$dir_item_temp['link']."'+id+'?".time()."','Agenda','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
  echo("      }\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo ("     function EditaTituloEnter(campo, evento, id)\n");
  echo ("     {\n");
  echo ("         var tecla;\n");
  echo ("         CheckTAB=true;\n\n");
  echo ("         if(navigator.userAgent.indexOf(\"MSIE\")== -1)\n");
  echo ("         {\n");
  echo ("             tecla = evento.which;\n");
  echo ("         }\n");
  echo ("         else\n");
  echo ("         {\n");
  echo ("             tecla = evento.keyCode;\n");
  echo ("         }\n\n");
  echo ("         if ( tecla == 13 )\n");
  echo ("         {\n");
  echo ("             EdicaoTitulo(id, 'tit_'+id, 'ok');\n");
  echo ("         }\n\n");
  echo ("         return true;\n");
  echo ("     }\n\n");
  
  echo("      function AlteraTitulo(id){\n");
  echo("        var id_aux = id;\n");
  echo("        if (editaTitulo==0){\n");
  echo("          CancelaTodos();\n");
    
  echo("          xajax_AbreEdicao(".$cod_curso.", ".$cod_item.", ".$cod_usuario.", origem);\n");
    
  echo("          conteudo = document.getElementById('tit_'+id).innerHTML;\n");
  echo("          document.getElementById('tr_'+id).className='';\n");
  echo("          document.getElementById('tit_'+id).className='';\n");

  echo("          createInput = document.createElement('input');\n");
  echo("          document.getElementById('tit_'+id).innerHTML='';\n");
  //echo("          document.getElementById('renomear_'+id).onclick=function(){ };\n\n");
  //echo("          document.getElementById('renomear_'+id).setAttribute('onclick', '');\n");

  echo("          createInput.setAttribute('type', 'text');\n");
  echo("          createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
  echo("          createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");
  echo("          if (createInput.addEventListener){\n"); //not IE
  echo("            createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
  echo("          } else if (createInput.attachEvent){\n"); //IE
  echo("            createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
  echo("          }\n");

  echo("          document.getElementById('tit_'+id).appendChild(createInput);\n");
  echo("          xajax_DecodificaString('tit_'+id+'_text', conteudo, 'value');\n\n");

  echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("          espaco = document.createElement('span');\n");
  echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
  echo("          document.getElementById('tit_'+id).appendChild(espaco);\n");

  echo("          createSpan = document.createElement('span');\n");
  echo("          createSpan.className='link';\n");
  echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };\n");
  echo("          createSpan.setAttribute('id', 'OkEdita');\n");
  echo("          createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,18)."';\n");
  echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

  echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("          espaco = document.createElement('span');\n");
  echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
  echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");
    
  echo("          createSpan = document.createElement('span');\n");
  echo("          createSpan.className='link';\n");
  echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };\n");
  echo("          createSpan.setAttribute('id', 'CancelaEdita');\n");
  echo("          createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,2)."';\n");
  echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

  echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("          espaco = document.createElement('span');\n");
  echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
  echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");
 
  echo("          startList();\n");
  echo("          cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("          document.getElementById('tit_'+id+'_text').select();\n");
  echo("          editaTitulo++;\n");
  echo("        }\n");
  echo("      }\n\n");
  
  echo("    </script>\n\n");

  $objAjax->printJavascript();

  echo("    <script type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* Verificação se o item está em Edição */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
  $linha=RetornaUltimaPosicaoHistorico($sock, $cod_item);

  if ($linha['acao']=="E")
  {
    if (($linha['data']<(time()-1800)) || ($cod_usuario == $linha['cod_usuario'])){
      AcabaEdicao($sock, $cod_curso, $cod_item, $cod_usuario, 0);
    }else{
      /* Está em edição... */
      echo("          <script language=\"javascript\">\n");
      echo("            window.open('em_edicao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=1&cod_item=".$cod_item."&origem=ver_linha','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("            window.location='".$origem.".php?".RetornaSessionID()."&cod_curso=".$cod_curso."';\n");
      echo("          </script>\n");
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
    $cabecalho = "          <h4>".RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases,2)."</h4>";
  } else {
    /* 1 - Agenda */
    /* 111 - Editar Agenda*/
    $cabecalho = "          <h4>".RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases,111)."</h4>";
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
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  /* Tabela Externa */
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  if($origem == "ver_anteriores")
    /*33 - Voltar para Agenda Anteriores*/
    $frase = RetornaFraseDaLista($lista_frases,33);
  else if($origem == "ver_editar")
    /*3 - Agendas Futuras*/
    $frase = RetornaFraseDaLista($lista_frases, 3);
  else
    /*8 - Voltar para Agenda Atual*/
    $frase = RetornaFraseDaLista($lista_frases, 8);


  if($origem == "ver_editar")
    $caminho="ver_editar.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;
  else if($origem == "ver_anteriores")
    $caminho="ver_anteriores.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;
  else 
    $caminho="agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario;

  echo("                  <li><a href=\"".$caminho."\">".$frase."</a></li>\n");
  /*34 - Histórico */
  echo("                  <li><span onclick=\"window.open('historico_agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."','Historico','width=600,height=400,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');\">".RetornaFraseDaLista($lista_frases, 34)."</span></li>\n");
  if($origem == "ver_editar"){
    /*34 - Ativar */
    echo("                  <li><span onClick=\"Ativar();\">".RetornaFraseDaLista ($lista_frases, 24)."</span></li>\n");
  }
  /*34 - Apagar */
  echo("                  <li><span onClick=\"ApagarItem();\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  /* Tabela Interna */
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /*18 - Titulo */
  echo("                    <td class=\"alLeft\" align=\"left\">".RetornaFraseDaLista($lista_frases, 18)."</td>\n");

  /*Conteudo da Agenda*/
  $linha_item = RetornaAgenda($sock, $cod_item);

  if(($usr_formador) && ($linha_item['situacao'] != "H"))
  {
    /*70 (gn) - Opcoes */
    echo("                  <td align=center width=\"15%\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
  }
  echo("                  </tr>\n");

  $titulo=$linha_item['titulo'];

  /* (ger) 9 - Editar */
  $editar=RetornaFraseDaLista ($lista_frases_geral, 9);
  
  if ($linha_item['status']=="E")
  {

    $linha_historico=RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);

    if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario'])
    {
      CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp);
      if($usr_formador)
      {
        $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
        // 106 - Renomear Título
        $renomear="<span onclick=\"AlteraTitulo('".$linha_item['cod_item']."');\">".RetornaFraseDaLista ($lista_frases, 106)."</span>";
      /* 91 - Editar texto */
        $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">".RetornaFraseDaLista ($lista_frases, 91)."</span>";
      /* 92 - Limpar texto */
        $limpar="<span onclick=\"LimpaTexto(".$linha_item['cod_item'].");\">".RetornaFraseDaLista ($lista_frases, 92)."</span>";
      }
    }
  }
  //else = item não está sendo editado
  else
  {
    if($usr_formador)
    {
      $titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";
      // 106 - Renomear Título
      $renomear="<span onclick=\"AlteraTitulo('".$linha_item['cod_item']."');\" id=\"renomear_".$linha_item['cod_item']."\">".RetornaFraseDaLista ($lista_frases, 106)."</span>";
      /* 91 - Editar texto */
      $editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">".RetornaFraseDaLista ($lista_frases, 91)."</span>";
      /* 92 - Limpar texto */
      $limpar="<span onclick=\"LimpaTexto(".$linha_item['cod_item'].");\">".RetornaFraseDaLista ($lista_frases, 92)."</span>";
    }
  }

  echo("                  <tr id='tr_".$linha_item['cod_item']."'>\n");
  echo("                    <td class=\"itens\">".$titulo."</td>\n");

  if ($linha_item['situacao']!="H")
  {
    if($usr_formador)
    {
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
    }
  }

  echo("                  </tr>\n");

  /*Verifica se ha arquivo de entrada*/
  $arquivo_entrada="";
  $lista_arq=RetornaArquivosAgendaVer($cod_curso, $dir_item_temp['diretorio']);

  if (count($lista_arq)>0)
    foreach($lista_arq as $cod => $linha1)
      if ($linha1['Status'] && $linha1['Arquivo']!=""){
        if(preg_match('/\.php(\.)*/', $linha1['Arquivo'])){  //arquivos php.txt

          $arquivo_entrada = "agenda_entrada.php?entrada=".ConverteUrl2Html($linha1['Arquivo']."&diretorio=".$dir_item_temp['link']);
        }else{
          $arquivo_entrada = ConverteUrl2Html($dir_item_temp['link'].$linha1['Diretorio']."/".$linha1['Arquivo']);
        }
        break;
      }

  /*Se houver, cria um iframe para exibi-lo*/
  if(($linha_item['texto']=="")&&($arquivo_entrada!=""))
    $conteudo="<span id=\"text_".$linha_item['cod_item']."\"><iframe id=\"iframe_ArqEntrada\" texto=\"ArqEntrada\" src=\"".$arquivo_entrada."\" width=\"100%\" height=\"400\" frameBorder=\"0\" scrolling=\"Auto\"></iframe></span>";
  /*Senaum, exibe o texto da agenda*/
  else
  {
    $texto = AjustaParagrafo($linha_item['texto']);

    if(($texto == "<P>&nbsp;</P>") || ($texto == "<br />"))
      $texto = "";

    $conteudo="<span id=\"text_".$linha_item['cod_item']."\">".$texto."</span>";
  }

  echo("                  <tr class=\"head\">\n");
  /* 94 - Conteudo  */
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,94)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"4\">\n");
  echo("                      <div class=\"divRichText\">\n");
  echo("                        ".$conteudo."\n");
  echo("                      </div>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  if ($usr_formador){
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

            if ($linha[Arquivo] != "")
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
                  $imagem    = "<img alt=\"\" src=../imgs/arqzip.gif border=0 />";
                  $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqEntrada.">";
                }
                else
                {
                  // arquivo comum
                  $imagem    = "<img alt=\"\" src=../imgs/arqp.gif border=0 />";
                  $tag_abre  = "<a href=\"".ConverteUrl2Html($caminho_arquivo)."\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".ConverteUrl2Html($caminho_arquivo)."'); return(false);\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqEntrada.">";
                }

                $tag_fecha = "</a>";

                echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");

                if(($usr_formador) && ($linha_item['situacao'] != "H")){
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
    echo("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");
    /*38 - Descompactar (ger)*/
    echo("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">".RetornaFraseDaLista($lista_frases_geral,38)."</span></li>\n");
    /*60 - Selecionar Entrada */
    echo("                        <li class=\"menuUp\" id=\"mArq_entrada\"><span id=\"sArq_entrada\">".RetornaFraseDaLista($lista_frases,60)."</span></li>\n");
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
    echo("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=0 />\n");
    echo("                          <span class=\"destaque\">".RetornaFraseDaLista ($lista_frases_geral, 26)."</span>\n");
    echo("                          <span> - ".RetornaFraseDaLista ($lista_frases, 48).RetornaFraseDaLista ($lista_frases, 49)."</span>\n");
    echo("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
    echo("                          <input type=\"file\" id=\"input_files\" name=\"input_files\" onchange=\"EdicaoArq(1);\" style=\"border:2px solid #9bc\" />\n");
    echo("                          &nbsp;&nbsp;\n");
    //echo("                          <span onClick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 18)."</span>\n");
    //echo("                          &nbsp;&nbsp;\n");
    //echo("                          <span onClick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">".RetornaFraseDaLista ($lista_frases_geral, 2)."</span>\n");
    echo("                        </div>\n");
                                    /* 26 - Anexar arquivo (ger) */
    echo("                        <div id=\"divArquivo\"><img alt=\"\" src=\"../imgs/paperclip.gif\" border=0 /> <span class=\"link\" id =\"insertFile\" onClick=\"AcrescentarBarraFile(1);\">".RetornaFraseDaLista($lista_frases_geral,26)."</span></div>\n");
    echo("                      </form>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  /*Fim tabela interna*/
  echo("                </table>\n");

  if($usr_formador)
  {
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    /* 59 - entrada. */
    /* 20 - Este arquivo sera a entrada da agenda*/
    echo("              <td align=\"left\">(<font color=red>".RetornaFraseDaLista($lista_frases,59)."</font>) - ".RetornaFraseDaLista($lista_frases,20)."</td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    /* 44 - Obs.: A agenda devera conter somente texto ou somente arquivos. */
    echo("              <td align=\"left\">".RetornaFraseDaLista($lista_frases,44)."\n");
  }
  /*Fim tabela externa*/
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
