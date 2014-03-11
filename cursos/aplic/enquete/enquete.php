<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/enquete.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distâcia
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

    Nied - Nucleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/enquete/enquete.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=24;
  $cod_ferramenta_ajuda = 24;

  if(!isset($categ)) $categ = 1;

  switch($categ)
  {
    /* 39 - Enquetes não aplicadas */
    case 0: $cod_pagina_ajuda = 1; break;
    /* 40 - Enquetes em andamento  */
    case 1: $cod_pagina_ajuda = 2; break;
    /* 41 - Enquetes encerradas */
    case 2: $cod_pagina_ajuda = 3; break;
    /* 97 - Lixeira */
    case 3: $cod_pagina_ajuda = 4; break;
  }
  
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
  // 14 - Enquete criada com sucesso.
  // 15 - Erro na criação da Enquete.
  $feedbackObject->addAction("criarEnquete", 14, 15);
  // 102 - Enquete recuperada com sucesso.
  // 103 - Erro na recuperação da enquete.
  $feedbackObject->addAction("recuperarEnquete", 102, 103);
  // 79 - Enquete finalizada com sucesso.
  // 80 - Erro na finalização da Enquete.
  $feedbackObject->addAction("finalizarEnquete", 79, 80);
  // 101 - Enquete apagada com sucesso. A enquete foi movida para a lixeira.
  // 100 - Erro ao apagar a enquete.
  $feedbackObject->addAction("apagarEnquete", 101, 100);
  // 73 - Enquete aplicada com sucesso. Agora ela se encontra em andamento.
  // 74 - Erro na aplicação da Enquete.
  $feedbackObject->addAction("aplicarEnquete", 73, 74);
  // 91 - Voto inserido com sucesso.
  // 92 - Erro na inserção do voto.
  $feedbackObject->addAction("votarEnquete", 91, 92);
  // 76 - Enquete excluida com sucesso.
  // 77 - Erro ao excluir enquete.
  $feedbackObject->addAction("excluirEnquete", 76, 77);
 // 117 - Enquete prorrogada com sucesso.
  $feedbackObject->addAction("prorrogarEnquete", 117, 0);

  $objAjax->printJavascript();

  $ator = getTipoAtor($sock, $cod_curso, $cod_usuario);

  if ($tela_formador || $tela_colaborador)
  {
    if ($acao=="adiantar_prorrogar")
    {
      $data_fim=DataHora2Unixtime($data_fim." ".$hora_fim.":00");
      MudaFimEnquete($sock, $idEnquete, $data_fim);
      $categ = getCodStatusEnquete($sock, $idEnquete);
      $_GET['acao'] = "prorrogarEnquete";
      $_GET['atualizacao'] = "true";
    }
  }

  $tabela="Enquete";
  $dir="enquete";

  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  if (!isset($cod_topico_raiz))
    $cod_topico_raiz=1;

  /*******************************************
  CATEGORIAS 
  0 - não aplicadas
  1 - em andamento (default)
  2 - encerradas
  ********************************************/
  if(!isset($categ)) $categ = 1;

  switch($categ)
  {
    /* 39 - Enquetes não aplicadas */
    case 0: $categoria = RetornaFraseDaLista($lista_frases,39); break;
    /* 40 - Enquetes em andamento  */
    case 1: $categoria = RetornaFraseDaLista($lista_frases,40) ; break;
    /* 41 - Enquetes encerradas */
    case 2: $categoria = RetornaFraseDaLista($lista_frases,41); break;
    /* 97 - Lixeira */
    case 3: $categoria = RetornaFraseDaLista($lista_frases,97); break;
  } 

  /* Impede o acesso a algumas secoes aos usuários que não são formadores. */
  if ((!$tela_formador && !$tela_colaborador) && ($categ != '1') && ($categ != '2'))
  {

    include("../menu_principal.php");

    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

    /* 1 - Enquete */
    echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
    /* 114 - Acao exclusiva a formadores. */
    echo("    - ".RetornaFraseDaLista($lista_frases, 114)."</h4>");

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
    echo("            <input class=\"input\" type=\"button\" name=\"cmdVoltar\" value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=\"history.go(-1);\" />\n");
    echo("          </form>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit;
  }


  GeraJSVerificacaoData();
  GeraJSComparacaoDatas();
  
  /*********************************************************/
  /* início - JavaScript */
  echo("  <script type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script type=\"text/javascript\" language=\"javascript\">\n\n");

  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var versao = (navigator.appVersion.substring(0,3));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

  echo("    var selected_item;\n");
  echo("    var data_fim;\n");
  echo("    var hora_fim;\n");
  echo("    var vota;\n");
  
  echo("    if (isNav)\n");
  echo("    {\n");
  echo("      document.captureEvents(Event.MOUSEMOVE);\n");
  echo("    }\n");
  echo("    document.onmousemove = TrataMouse;\n\n");

  echo("    function TrataMouse(e)\n");
  echo("    {\n");
  echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("    }\n\n");

  echo("    var selected_item;\n");

  echo("      function getPageScrollY()\n");
  echo("      {\n");
  echo("        if (isNav)\n");
  echo("          return(window.pageYOffset);\n");
  echo("        if (isIE){\n");
  echo("          if(document.documentElement.scrollLeft>=0){\n");
  echo("            return document.documentElement.scrollTop;\n");
  echo("          }else if(document.body.scrollLeft>=0){\n");
  echo("            return document.body.scrollTop;\n");
  echo("          }else{\n");
  echo("            return window.pageYOffset;\n");
  echo("          }\n");
  echo("        }\n");
  echo("      }\n");

  /* Iniciliza os layers. */
  echo("    function Iniciar()\n");
  echo("    {\n");
              $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("      startList();\n");
  echo("      lay_nova_enquete = getLayer('layer_nova_enquete');\n");
  switch($categ)
  {
    //case 0: echo("      lay_naoaplicada = getLayer('layer_naoaplicada');\n"); break;
    case 1: //echo("      lay_andamento = getLayer('layer_andamento');\n"); 
                //echo("      lay_andamento_vota = getLayer('layer_andamento_vota');\n");
                if ($tela_formador || $tela_colaborador) echo("      lay_adiantar_prorrogar = getLayer('layer_adiantar_prorrogar');\n");
                if ($tela_formador || $tela_colaborador) echo("      lay_calendario = getLayer('layer_calendario');\n");
                break;
    case 2: //if ($tela_formador || $tela_colaborador) echo("      lay_encerrada = getLayer('layer_encerrada');\n");
                if ($tela_formador || $tela_colaborador) echo("      lay_adiantar_prorrogar = getLayer('layer_adiantar_prorrogar');\n"); 
                if ($tela_formador || $tela_colaborador) echo("      lay_calendario = getLayer('layer_calendario');\n");
                break;
    //case 3: echo("      lay_lixeira = getLayer('layer_lixeira');\n"); break;
  }
  echo("    }\n\n");

  echo("      function MostraLayer(cod_layer, ajuste, ev){\n");
  echo("        EscondeLayers();\n");
  echo("        ev = ev || window.event;\n");
  echo("        if(ev.pageX || ev.pageY){\n");
  echo("          Xpos = ev.pageX;\n");
  echo("          Ypos = ev.pageY;\n");
  echo("        }else{\n");
  echo("          Xpos = ev.clientX + document.body.scrollLeft - document.body.clientLeft;\n");
  echo("          Ypos = ev.clientY + getPageScrollY();\n");
  echo("        }\n");
  echo("        moveLayerTo(cod_layer,Xpos-100,Ypos);\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n\n");

  // Esconde o layer especificado por cod_layer.
  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("      mostrando=0;\n");
  echo("    }\n\n");

  /* Esconde todos os layers. Se o usuario for o proprietário do diï¿½io   */
  /* visualizado então esconde o layer para renomear o item.              */
  echo("    function EscondeLayers()\n");
  echo("    {\n");
  switch($categ)
  {
    //case 0: echo("      hideLayer(lay_naoaplicada);\n"); break;
    case 1: //echo("      hideLayer(lay_andamento);\n");
                //echo("      hideLayer(lay_andamento_vota);\n"); 
                if ($tela_formador || $tela_colaborador) echo("      hideLayer(lay_adiantar_prorrogar);\n");
                if ($tela_formador || $tela_colaborador) echo("      lay_calendario = getLayer('layer_calendario');\n");
                break;
    case 2: //if ($tela_formador || $tela_colaborador) echo("      hideLayer(lay_encerrada);\n"); 
                if ($tela_formador || $tela_colaborador) echo("      hideLayer(lay_adiantar_prorrogar);\n");
                if ($tela_formador || $tela_colaborador) echo("      lay_calendario = getLayer('layer_calendario');\n");
                break;
    //case 3: echo("      hideLayer(lay_lixeira);\n"); break;
  }
  echo("    }\n\n");


  /***************************************************************
  JAVASCRIPTS para as ações dos layers
  ***************************************************************/
  
  /* Visualiza uma enquete */
  echo("    function Ver()\n");
  echo("    {\n");
  echo("      document.location.href='ver_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
  echo("    }\n\n");

  if ($categ == 1) //enquete em ANDAMENTO
  {  
    // Vai para a página de votação
    echo("    function Votar()\n");
    echo("    {\n");
    echo("      document.location.href='vota_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("    }\n\n");
  }

  if (($tela_formador || $tela_colaborador) && ($categ == 0)) // enquete NAO_REALIZADA na visï¿½o do formador
  {
    // Vai para a página de edição da enquete
    echo("    function Editar()\n");
    echo("    {\n");
    echo("      document.location.href='editar_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("    }\n\n");

    // Torna a enquete em andamento
    echo("    function Aplicar()\n");
    echo("    {\n");
    // 86 - Tem certeza que deseja aplicar a enquete?
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 86)."')){");
    echo("        document.location.href='aplicar_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  
  if (($tela_formador || $tela_colaborador) && ($categ == 1)) // enquete em ANDAMENTO na visï¿½o do formador
  {
    // Finaliza a enquete
    echo("    function Finalizar()\n");
    echo("    {\n");
    // 42 - Tem certeza que deseja finalizar a enquete? 
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 42)."')){");
    echo("        document.location.href='finalizar_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  
  if (($tela_formador || $tela_colaborador) && (($categ == 1) || $categ == 2)) //enquete EM ANDAMENTO ou ENCERRADA na visão do formador 
  {
    // Carrega data e hora final da enquete atual no formulário para  Adiantada/Prorrogada
    echo("    function CarregaDataHora()\n");
    echo("    {\n"); 
    echo("      document.form_adiantar_prorrogar.data_fim.value = data_fim;\n");
    echo("      document.form_adiantar_prorrogar.hora_fim.value = hora_fim;\n");
    echo("    }\n\n");
    
    // Verifica a data de fim da enquete digitada 
    echo("    function PodeAdiantarProrrogar(form)\n");
    echo("    {\n");
    echo("      d_fim=document.form_adiantar_prorrogar.data_fim;\n");
    echo("      d_hoje=document.form_adiantar_prorrogar.data_hoje;\n");
    echo("      h_fim=document.form_adiantar_prorrogar.hora_fim;\n");
    echo("      h_hoje=document.form_adiantar_prorrogar.hora_hoje;\n");
    echo("      if (!DataValidaAux(d_fim))\n");
    echo("        return false;\n");
    echo("      if (! hora_valida(h_fim)){\n");
    // 110 - Horário de término inválido.
    echo("        alert('".RetornaFraseDaLista($lista_frases,110)."');\n");
    echo("        return false;\n");
    echo("      }\n");
    echo("      if (ComparaDataHora(d_hoje,h_hoje,d_fim,h_fim) > 0)// (hoje>fim) \n");
    echo("      {\n");
    // 95- A nova data final nao  pode ser anterior a data de hoje.
    echo("       alert('".RetornaFraseDaLista($lista_frases,95)."');\n");
    echo("       return(false);\n");
    echo("      }\n");
    echo("      return true");
    echo("    }\n\n");
  }
  
  if (($tela_formador || $tela_colaborador) && ($categ != 3)) // enquete fora da LIXEIRA e é formador
  {
    // Apaga a enquete , movendo-a para a lixeira
    echo("    function Apagar()\n");
    echo("    {\n");
    // 43 - Tem certeza que deseja apagar a enquete? Ela sera movida para a lixeira.
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 43)."')){");
    echo("      	document.location.href='apagar_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  
  if (($tela_formador || $tela_colaborador) && ($categ == 3)) // enquete na LIXEIRA
  {
    // Exclui a enquete permanentemente
    echo("    function Excluir()\n");
    echo("    {\n");
    // 104 - Tem certeza que deseja excluir a enquete? Ela sera excluida permanentemente, sem possibilidade de recuperacao.
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 104)."')){");
    echo("        document.location.href='excluir_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("      }\n");
    echo("    }\n\n");
 
    // Recupera a enquete
    echo("    function Recuperar()\n");
    echo("    {\n");
    // 105 - Tem certeza que deseja recuperar a enquete?
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 105)."')){");
    echo("        document.location.href='recuperar_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("    }\n");
    echo("    }\n\n");
  }

  echo("  </script>\n\n");
  /* fim - JavaScript */
  /*********************************************************/

  // Inicialmente, seta status de Enquetes que foram recentemente alteradas
  setStatusEnquetesRecentes($sock);
  
  if ($tela_formador || $tela_colaborador)
  {
    switch($categ)
    {
      case 0:
        $lista_enquetes=getTodasEnquetesNaoAplicadas($sock);
        break;
      case 1:
        $lista_enquetes=getTodasEnquetesEmAndamento($sock, $cod_curso);
        break;
      case 2:
        $lista_enquetes=getTodasEnquetesEncerradas($sock);
        break;
      case 3:
        $lista_enquetes=getTodasEnquetesLixeira($sock);
        break;
    }
  }
  else if ((EAluno($sock,$cod_curso,$cod_usuario)) || (EVisitante($sock,$cod_curso,$cod_usuario)))
  {
    // Início da Página do Aluno, Visitante e Colaborador
    switch($categ)
    {
      case 1:
        $lista_enquetes = getEnquetesAndamento($sock, $ator);
        break;
      case 2:
        $lista_enquetes = getEnquetesEncerradas($sock, $ator);
        /*******************************************
        Nao Ha LAYER neste caso, o clique leva direito a pagina de visulizacao de enquete 
        *******************************************/
        break;
    }
  }

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  // 1 - Enquete
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".$categoria."</h4>\n");

  // Voltar
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");

  echo("                <ul class=\"btAuxTabs\">\n");

  if ($tela_formador || $tela_colaborador)
  {
    // 3 - Nova Enquete
    echo("                  <li><a href='nova_enquete.php?cod_curso=".$cod_curso."'>".RetornaFraseDaLista($lista_frases,3)."</a></li>\n");
    // 39 - Enquetes nao aplicadas 
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=0'>".RetornaFraseDaLista($lista_frases, 39)."</a></li>\n");
  }

  // 39 - Enquetes em andamento
  echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=1'>".RetornaFraseDaLista($lista_frases,40)."</a></li>\n");
  // 41 - Enquetes encerradas
  echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=2'>".RetornaFraseDaLista($lista_frases,41)."</a></li>\n");

  if ($tela_formador || $tela_colaborador)
  {
    // 97 - Lixeira /
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=3'>".RetornaFraseDaLista($lista_frases,97)."</a></li>\n");
  }

  echo("                </ul>\n");


  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");

  // 09 - Titulo
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,9)."</td>\n");

  if (($tela_formador || $tela_colaborador) || ($categ == 1))
  {
    // 116 - Opções
    echo("                    <td width=\"140px\">".RetornaFraseDaLista($lista_frases,116)."</td>\n");
  }

  // 48 - Data Inicio
  echo("                    <td class=\"alLeft\" width=\"100px\">".RetornaFraseDaLista($lista_frases,48)."</td>\n");
  // 49 - Data Termino
  echo("                    <td align=\"center\" width=\"100px\">".RetornaFraseDaLista($lista_frases,49)."</td>\n");
  // 115 - Votada
  echo("                    <td align=\"center\" width=\"50px\">".RetornaFraseDaLista($lista_frases,115)."</td>\n");
 
  echo("                  </tr>\n");

  if (empty($lista_enquetes))
  {
    echo("                  <tr>\n");
    // 51 - Nao ha 
    echo("                    <td colspan=\"5\">".RetornaFraseDaLista($lista_frases,51)."</td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    $num = 0;
    $cor = 0;

    $ultimo_acesso = PenultimoAcesso($sock, $cod_usuario, "");
    while ($num < count($lista_enquetes))
    {
      $lay_vota = "";
      $vota = votaEnquete($sock, $ator, $lista_enquetes[$num]);
      $votou = jaVotouEnquete($sock, $cod_usuario, $lista_enquetes[$num]['idEnquete']);
      if(($vota) && ($categ == 1)) $lay_vota = "_vota";
      
      /* DESTACAR ENQUETES QUE DEVE VOTAR */
      $bi = "antigo";
      $tr = "";
      
      if ((!$votou) && ($vota) && (strcmp(getStatusEnquete($sock, $lista_enquetes[$num]), "ANDAMENTO") == 0))
      {
        $bi = "novo";
      } 
      /***************************************/

      $titulo=$lista_enquetes[$num]['titulo'];

      echo("                  <tr class=\"altColor".($cor)."\">\n");
      echo("                    <td class=\"alLeft\"><a class=\"".$bi."\" id=\"link_enquete_".$lista_enquetes[$num]['idEnquete']."\" href='ver_enquete.php?cod_curso=".$cod_curso."&amp;idEnquete=".$lista_enquetes[$num]['idEnquete']."' >".$titulo."</a></td>\n");

      // Se o usuário for formador então cria links com acesso as opções
      if ($tela_formador || $tela_colaborador)
      {
        switch($categ)
        {
          case 0:
            echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");
            echo("                      <ul>\n");
            // 21 - Ver
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Ver();'>".RetornaFraseDaLista($lista_frases_geral, 21)."</span></li>\n");
            // 9 - Editar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Editar();'>".RetornaFraseDaLista($lista_frases_geral, 9)."</span></li>\n");
            // 44 - Aplicar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Aplicar();'>".RetornaFraseDaLista($lista_frases, 44)."</span></li>\n");
            // 1 (gen) - Apagar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Apagar();'>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
            echo("                      </ul>\n");
            echo("                    </td>\n");
            break;

          case 1:
            echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");
            echo("                      <ul>\n");
            // 21 - Ver
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Ver();'>".RetornaFraseDaLista($lista_frases_geral, 21)."</span></li>\n");
            // 45 - Votar
            echo("                        <li><span ".(($vota) ? "onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Votar();'" : "style=\"color:#AAAAAA; cursor:default;\"") .">".RetornaFraseDaLista($lista_frases, 45)."</span></li>\n");
            // 46 - Adiantar / Prorrogar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; data_fim = \"".UnixTime2Data($lista_enquetes[$num]['data_fim'])."\" ; hora_fim = \"".UnixTime2Hora($lista_enquetes[$num]['data_fim'])."\" ; CarregaDataHora(); MostraLayer(lay_adiantar_prorrogar, 0, event)'>".RetornaFraseDaLista($lista_frases, 46)."</span></li>\n");
            // 47 - Finalizar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Finalizar();'>".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
            // 1 (gen) - Apagar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Apagar();'>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
            echo("                      </ul>\n");
            echo("                    </td>\n");
            break;

          case 2:
            echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");
            echo("                      <ul>\n");
            // 21 - Ver
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Ver();'>".RetornaFraseDaLista($lista_frases_geral, 21)."</span></li>\n");
            // 113 - Prorrogar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; data_fim = \"".UnixTime2Data(time())."\" ; hora_fim = \"".UnixTime2Hora(time())."\" ; CarregaDataHora(); MostraLayer(lay_adiantar_prorrogar, 0, event)'>".RetornaFraseDaLista($lista_frases, 113)."</span></li>\n");
            // 1 (gen) - Apagar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Apagar();'>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
            echo("                      </ul>\n");
            echo("                    </td>\n");
            break;

                case 3:
            echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");
            echo("                      <ul>\n");
            // 21 - Ver
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Ver();'>".RetornaFraseDaLista($lista_frases_geral, 21)."</span></li>\n");
            // 98- Recuperar
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Recuperar();'>".RetornaFraseDaLista($lista_frases, 98)."</span></li>\n");
            // 99 - Excluir
            echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Excluir();'>".RetornaFraseDaLista($lista_frases, 99)."</span></li>\n");
            echo("                      </ul>\n");
            echo("                    </td>\n");
            break;
        }
      }
      else if ($categ == 1) //Não formadores
      {
        echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");
        echo("                      <ul>\n");
        // 21 - Ver
        echo("                        <li><span onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Ver();'>".RetornaFraseDaLista($lista_frases_geral, 21)."</span></li>\n");
        // 45 - Votar
        echo("                        <li><span ".(($vota) ? "onclick='selected_item=".$lista_enquetes[$num]['idEnquete']."; Votar();'" : "style=\"color:#AAAAAA; cursor:default;\"") .">".RetornaFraseDaLista($lista_frases, 45)."</span></li>\n");
        echo("                      </ul>\n");
        echo("                    </td>\n");
      }

      echo("                    <td align=\"center\">".UnixTime2Data($lista_enquetes[$num]['data_inicio'])."</td>\n");
      echo("                    <td align=\"center\">".UnixTime2Data($lista_enquetes[$num]['data_fim'])."</td>\n");
      /* (ger) 35 - Sim */
      /* (ger) 36 - Não */
      echo("                    <td align=\"center\">".($votou ? RetornaFraseDaLista($lista_frases_geral,35) : RetornaFraseDaLista($lista_frases_geral,36))."</td>\n");
      echo("                  </tr>\n");

      // Incrementa o contador.
      $num++;
      $cor++;
      $cor%=2;
    }
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

  include("layer.php");

  echo("      <script type=\"text/javascript\" language=\"javascript\">\n");
  echo("        Iniciar();\n");
  echo("      </script>\n");

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>