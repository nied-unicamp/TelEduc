<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/enquete/ver_enquete.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�cia
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

    Nied - Ncleo de Inform�ica Aplicada �Educa�o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/enquete/ver_enquete.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("enquete.inc");

  $cod_ferramenta=24;
  $cod_ferramenta_ajuda = 24;
  
  include("../topo_tela.php");
  
  $enquete = getEnquete($sock, $idEnquete);
  $status_enquete = getStatusEnquete($sock, $enquete);
  
    switch($status_enquete)
  {
          /* 39 -  Enquetes n� aplicadas */
          case NAO_APLICADA: $cod_pagina_ajuda = 6; break;
          /* 40 - Enquetes em andamento  */
          case ANDAMENTO: $cod_pagina_ajuda = 7; break;
          /* 41 - Enquetes encerradas */
          case ENCERRADA:$cod_pagina_ajuda = 8; break;
          /* 97 - Lixeira */
          case LIXEIRA: $cod_pagina_ajuda = 9; break;
  }
  
  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  $ator = getTipoAtor($sock, $cod_curso, $cod_usuario);

  if ($tela_formador)
  {
    if ($acao=="adiantar_prorrogar")
    {
      $data_fim=DataHora2Unixtime($data_fim." ".$hora_fim.":00");
      MudaFimEnquete($sock, $idEnquete, $data_fim);
      $categ = getCodStatusEnquete($sock, $idEnquete);
    }
  }




  
  $ator = getTipoAtor($sock, $cod_curso, $cod_usuario); 

  if (empty($enquete))
  {
    /* 1 - Enquete */
    echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

    /*Voltar*/			
   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
    echo("          <div id=\"mudarFonte\">\n");
    echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
    echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
    echo("          </div>\n");

    /* - */
    echo("          <p>"./*.RetornaFraseDaLista($lista_frases, 93).*/"Enquete inv&aacute;lida.</p><br/>\n");
    echo("          <form name=voltar action=\"enquete.php?cod_curso=".$cod_curso."\" method=post>\n");
    /* 23 - Voltar */
    echo("            <input type=submit value='".RetornaFraseDaLista($lista_frases_geral, 23)."'>\n");
    echo("          </form>\n");
    echo("        </td>\n");
    echo("      </tr>\n"); 

    include("../tela2.php");

    echo("  </body>\n");
    echo("</html>\n");
  }

  /*************************************
   Seleciona categoria da enquete 
  *************************************/
  $categ = getCodStatusEnquete($sock, $idEnquete);

  GeraJSVerificacaoData();
  GeraJSComparacaoDatas();
  
  /*********************************************************/
  /* in�io - JavaScript */
  echo("  <script type=\"text/javascript\" language=\"JavaScript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script type=\"text/javascript\" language=\"JavaScript\">\n\n");        
 
  echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("    var versao = (navigator.appVersion.substring(0,3));\n");
  echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

  echo("    var selected_item = $idEnquete;\n");
  echo("    var data_fim = \"".UnixTime2Data($enquete['data_fim'])."\";\n");
  echo("    var hora_fim = \"".UnixTime2Hora($enquete['data_fim'])."\";\n");
  
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
  echo("      startList();\n");
  echo("      lay_adiantar_prorrogar = getLayer('layer_adiantar_prorrogar');\n"); 
  echo("    }\n\n");

  // Esconde o layer especificado por cod_layer.
  echo("    function EscondeLayer(cod_layer)\n");
  echo("    {\n");
  echo("      hideLayer(cod_layer);\n");
  echo("    }\n\n");

  /* Esconde todos os layers. Se o usuario for o propriet�io do di�io   */
  /* visualizado ent� esconde o layer para renomear o item.              */
  echo("    function EscondeLayers()\n");
  echo("    {\n");
  echo("      hideLayer(lay_adiantar_prorrogar);\n"); 
  echo("    }\n\n");

  /* Exibe o layer especificado por cod_layer.                            */
  echo("    function MostraLayer(cod_layer)\n");
  echo("    {\n");
  echo("      EscondeLayers();\n");
  echo("      moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("      showLayer(cod_layer);\n");
  echo("    }\n\n");
 
  /***************************************************************
  JAVASCRIPTS para as a�es do menu
  ***************************************************************/
  
  if ($categ == 1) //enquete em ANDAMENTO
  { 
  /* Vai para a p�ina de vota�o*/
    echo("    function Votar()\n");
    echo("    {\n");
    echo("      document.location.href='vota_enquete.php?cod_curso=$cod_curso&idEnquete='+selected_item\n");
    echo("    }\n\n");
  }

  if (($tela_formador) && ($categ == 0)){ //enquete NAO_REALIZADA na vis� do formador
  /* Vai para a p�ina de edi�o da enquete */
    echo("    function Editar()\n");
    echo("    {\n");
    echo("      document.location.href='editar_enquete.php?cod_curso=$cod_curso&idEnquete='+selected_item\n");
    echo("    }\n\n");

    /* Torna a enquete em andamento*/
    echo("    function Aplicar()\n");
    echo("    {\n");
    /* 86 - Tem certeza que deseja aplicar a enquete? */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 86)."'))");
    echo("        document.location.href='aplicar_enquete.php?cod_curso=$cod_curso&idEnquete='+selected_item\n");
    echo("    }\n\n");
  }
  
  if (($tela_formador) && ($categ == 1)) //enquete em ANDAMENTO na vis� do formador
  {  
    /* Finaliza a enquete*/
    echo("    function Finalizar()\n");
    echo("    {\n");
    /* 42 - Tem certeza que deseja finalizar a enquete? */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 42)."'))");
    echo("        document.location.href='finalizar_enquete.php?cod_curso=".$cod_curso."&idEnquete='+selected_item\n");
    echo("    }\n\n");
  }
  
  if (($tela_formador) && (($categ == 1) || $categ == 2)) //enquete EM ANDAMENTO ou ENCERRADA na visao do formador 
  {
    /* Carrega data e hora final da enquete atual no formulario para  Adiantada/Prorrogada*/
    echo("    function CarregaDataHora()\n");
    echo("    {\n"); 
    echo("      document.form_adiantar_prorrogar.data_fim.value = data_fim;\n");
    echo("      document.form_adiantar_prorrogar.hora_fim.value = hora_fim;\n");
    echo("    }\n\n");
    
    /* Verifica a data de fim da enquete digitada */
    echo("    function PodeAdiantarProrrogar(form)\n");
    echo("    {\n");
    echo("      d_fim=document.form_adiantar_prorrogar.data_fim;\n");
    echo("      d_hoje=document.form_adiantar_prorrogar.data_hoje;\n");
    echo("      h_fim=document.form_adiantar_prorrogar.hora_fim;\n");
    echo("      h_hoje=document.form_adiantar_prorrogar.hora_hoje;\n");
    echo("      if (!DataValidaAux(d_fim))\n");
    echo("        return false;\n");
    echo("      if (! hora_valida(h_fim)){\n");
    /* 110 - Horario de termino invalido. */
    echo("        alert('".RetornaFraseDaLista($lista_frases,110)."');\n");
    echo("        return false;\n");
    echo("      }\n");
    echo("      if (ComparaDataHora(d_hoje,h_hoje,d_fim,h_fim) > 0)// (hoje>fim) \n");
    echo("      {\n");
    /* 95- A nova data final nao pode ser anterior a data de hoje. */
    echo("       alert('".RetornaFraseDaLista($lista_frases,95)."');\n");
    echo("       return(false);\n");
    echo("      }\n");
    echo("      return true");
    echo("    }\n\n");
  }
  
  if (($tela_formador) && ($categ != 3)) //enquete fora da LIXEIRA e �formador
  {
    /* Apaga a enquete , movendo-a para a lixeira */
    echo("    function Apagar()\n");
    echo("    {\n");
    /* 43 - Tem certeza que deseja apagar a enquete? Ela ser�movida para a lixeira. */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 43)."')){");
    echo("        document.location.href='apagar_enquete.php?cod_curso=$cod_curso&idEnquete='+selected_item\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  
  if (($tela_formador) && ($categ == 3)) //enquete na LIXEIRA
  {
    /* Exclui a enquete permanentemente*/
    echo("    function Excluir()\n");
    echo("    {\n");
    /* 104 - Tem certeza que deseja excluir a enquete? Ela ser�excluida permanentemente, sem possibilidade de recupera�o. */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 104)."')){");
    echo("      	document.location.href='excluir_enquete.php?cod_curso=$cod_curso&idEnquete='+selected_item\n");
    echo("      }\n");
    echo("    }\n\n");
    
      /* Recupera a enquete */
    echo("    function Recuperar()\n");
    echo("    {\n");
    /* 105 - Tem certeza que deseja recuperar a enquete? */
    echo("      if(confirm('".RetornaFraseDaLista($lista_frases, 105)."')){");
    echo("      	document.location.href='recuperar_enquete.php?cod_curso=$cod_curso&idEnquete='+selected_item\n");
    echo("      }\n");
    echo("    }\n\n");
  }

  echo("    function Voltar()\n");
  echo("    {\n");
  echo("      window.location='enquete.php?cod_curso=".$cod_curso."';\n");
  echo("    }\n");

  echo("    function abrir(URL, w, h)\n");
  echo("    {\n");
  echo("      var width = w;\n");
  echo("      var height = h;\n");
  echo("      var left = 99;\n");
  echo("      var top = 99;\n");
  echo("      window.open(URL,'', 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');\n");
  echo("    }\n");
  echo("  </script>\n");
  /* fim - JavaScript */
  /*********************************************************/

  if ($tela_formador)
  {
	switch($categ)
	{
		case 0: 
			$lista_enquetes=getTodasEnquetesNaoAplicadas($sock);
			$layer = "lay_naoaplicada";
			break;
		case 1: 
			$lista_enquetes=getTodasEnquetesEmAndamento($sock, $cod_curso);
			$layer = "lay_andamento";
			break;
		case 2: 
			$lista_enquetes=getTodasEnquetesEncerradas($sock);
			$layer = "lay_encerrada";
			break;
                case 3: 
			$lista_enquetes=getTodasEnquetesLixeira($sock);
			$layer = "lay_lixeira";
			break;
	}
  }
  else if ((EAluno($sock,$cod_curso,$cod_usuario)) || (EVisitante($sock,$cod_curso,$cod_usuario)) || ( EConvidado($sock, $cod_usuario, $cod_curso)))
  {
    /* In�io da P�ina do Aluno, Visitante e Convidado ************/

    switch($categ)
    {
      case 1; 
        $lista_enquetes = getEnquetesAndamento($sock, $ator); 
        $layer = "lay_andamento";
        break;
      case 2: 
        $lista_enquetes = getEnquetesEncerradas($sock, $ator);  
        /*******************************************
          N� H�LAYER neste caso, o clique leva direito �pagina de visuliza�o de enquete 
          *******************************************/
        break;
    }
  }

  /* 1 - Enquetes */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

  /*Voltar*/			
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

  /* 23 - Voltar  (gen) */
  echo("                  <li><span onclick='javascript:history.back(-1);'>".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");

  if ($tela_formador)
  {
    /* 3 - Nova Enquete */
    echo("                  <li><a href='nova_enquete.php?cod_curso=".$cod_curso."'>".RetornaFraseDaLista($lista_frases,3)."</a></li>\n");
    /* 39 - Enquetes nao aplicadas */
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=0'>".RetornaFraseDaLista($lista_frases, 39)."</a></li>\n");
  }

  /* 39 - Enquetes em andamento */
  echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=1'>".RetornaFraseDaLista($lista_frases,40)."</a></li>\n");
  /* 41 - Enquetes encerradas  */
  echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=2'>".RetornaFraseDaLista($lista_frases,41)."</a></li>\n");
  if ($tela_formador)
  {
    /* 97 - Lixeira */
    echo("                  <li><a href='enquete.php?cod_curso=".$cod_curso."&amp;categ=3'>".RetornaFraseDaLista($lista_frases,97)."</a></li>\n");
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
//   echo("            <tr>\n");
//   echo("            <!--  Botoes de Acao  -->\n");
//   echo("              <td>\n");
//   echo("                <ul class=\"btAuxTabs03\">\n");
//   
//   if ($tela_formador){
// // if(CriadorEnquete($sock, $cod_curso, $cod_usuario, $idEnquete)){
// echo $cod_curso;
//     /*********************************
//     FORMADOR
//     ********************************/
// 
//     if(strcmp($status_enquete, "NAO_APLICADA") == 0){
//       /*********************************
//       FORMADOR - Enquete NAO REALIZADA
//       ********************************/
//       /* 9 (gen) - Editar */
//       echo("                  <li><a href=\"#\" onClick=\"Editar();\">".RetornaFraseDaLista($lista_frases_geral,9)."</a></li>\n");
//       /* 44 - Aplicar */
//       echo("                  <li><a href=\"#\" onClick=\"Aplicar();\">".RetornaFraseDaLista($lista_frases,44)."</a></li>\n");
//       /* 1 (gen) - Apagar */
//       echo("                  <li><a href=\"#\" onClick=\"Apagar();\">".RetornaFraseDaLista($lista_frases_geral,1)."</a></li>\n");
//     }
//     elseif (strcmp($status_enquete, "ANDAMENTO") == 0)
//     {
//         if (votaEnquete($sock, $ator, $enquete))
//         {
//           /* 45 - Votar */
//           echo("                  <li><a href=\"#\" onClick=\"Votar();\">".RetornaFraseDaLista($lista_frases,45)."</a></li>\n");
//         }
// 
// 	/* 46 - Adiantar / Prorrogar */
// 	echo("                  <li><a href=\"javascript:void(0);\" class=menu2 onClick='CarregaDataHora(); MostraLayer(lay_adiantar_prorrogar)'>".RetornaFraseDaLista($lista_frases,46)."</a></li>\n");
// 	/* 47 - Finalizar */
// 	echo("                  <li><a href=\"javascript:void(0);\" onClick='Finalizar();' class=menu2>".RetornaFraseDaLista($lista_frases,47)."</a></li>\n");
// 	/* 1 - Apagar (gen) */
// 	echo("                  <li><a href=\"javascript:void(0);\" onClick='Apagar();' class=menu2>".RetornaFraseDaLista($lista_frases_geral,1)."</a></li>\n");
//     }
//     elseif (strcmp($status_enquete, "ENCERRADA") == 0)
//     {
//       /* 113 - Prorrogar */
//       echo("                  <li><a class=menu2 href=\"javascript:void(0);\" onClick='data_fim =\"".UnixTime2Data(time())."\" ; hora_fim = \"".UnixTime2Hora(time())."\" ;  CarregaDataHora(); MostraLayer(lay_adiantar_prorrogar)'>".RetornaFraseDaLista($lista_frases, 113)."</a></li>\n");
//       /* 1 - Apagar (gen) */
//       echo("                  <li><a href=\"javascript:void(0);\" onClick=\"Apagar();\">".RetornaFraseDaLista($lista_frases_geral,1)."</a></li>\n");
//     }
//     elseif (strcmp($status_enquete, "LIXEIRA") == 0)
//     {
//       /* 98 - Recuperar */
//       echo("                  <li><a href=\"javascript:void(0);\" onClick=\"Recuperar();\">".RetornaFraseDaLista($lista_frases,98)."</a></li>\n");
//       /* 99 - Excluir */
//       echo("                  <li><a href=\"javascript:void(0);\" onClick=\"Excluir()\">".RetornaFraseDaLista($lista_frases,99)."</a></li>\n");
//     }
//   }
//   else
//   {
//     /*********************************
//     NAO FORMADOR
//     ********************************/
//     if ((votaEnquete($sock, $ator, $enquete)) && ((strcmp($status_enquete, "ANDAMENTO") == 0)))
//     {
//       /* 45 - Votar */
//       echo("                  <li><a href=\"javascript:void(0);\" onClick=\"Votar();\">".RetornaFraseDaLista($lista_frases,45)."</a></li>\n");
//     }
//   }
// 
//   echo("                </ul>\n");
//   echo("              </td>\n");
//   echo("            </tr>\n");

    /***************************** 
    CARREGA FRASES DE CONFIGURA�O 
    *****************************/
  /* 56 - Coordenador */
  $frase_papeis ['R'] = RetornaFraseDaLista($lista_frases,56);
  /* 21 - Formadores */
  $frase_papeis ['F'] = RetornaFraseDaLista($lista_frases,21);
  /* 20 - Alunos */
  $frase_papeis ['A'] = RetornaFraseDaLista($lista_frases,20);
  /* 57 - Visitantes */
  $frase_papeis ['V'] = RetornaFraseDaLista($lista_frases,57);
  /* 58 - Convidados */
  $frase_papeis ['Z'] = RetornaFraseDaLista($lista_frases,58);
  
  /* 59 - Somente uma alternativa */
  $frase_escolhas ['1'] = RetornaFraseDaLista($lista_frases,59);
  /* 60 - Uma ou mais alternativas */
  $frase_escolhas ['N'] = RetornaFraseDaLista($lista_frases,60);
  
  /* 61 - Sim */
  $frase_boolean ['S'] = RetornaFraseDaLista($lista_frases,61);
  /* 62 - N� */
  $frase_boolean ['N'] = RetornaFraseDaLista($lista_frases,62);

  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabInterna\">\n");

  echo("                  <tr class=\"head\">\n");
  /* 9 - Titulo */ 
  echo("                    <td class=\"itens\" width=\"50%\">".RetornaFraseDaLista($lista_frases,9)."</td>\n");
  /* 70 - Opções */
  echo("                    <td width=\"18%\">".RetornaFraseDaLista($lista_frases_geral, 70)."</td>\n");
  /* 28 - Periodo de Consulta */
  echo("                    <td width=\"32%\">".RetornaFraseDaLista($lista_frases,28)."</td>\n");

  echo("                  </tr>\n");

  echo("                  <tr>\n");
  echo("                    <td class=\"itens\">".$enquete['titulo']."</td>\n");
  echo("                    <td align=\"center\" valign=\"top\"  class=\"botao2\">\n");
  echo("                      <ul>\n");
  if($tela_formador){
    /*********************************
    FORMADOR
    ********************************/
    if(strcmp($status_enquete, "NAO_APLICADA") == 0){
      /*********************************
      FORMADOR - Enquete NAO REALIZADA
      ********************************/
//       if(votaEnquete($sock, $ator, $enquete)){
          /* 9(gen) - Editar */
      echo("                        <li><span onclick='Editar();'>".RetornaFraseDaLista($lista_frases_geral, 9)."</span></li>\n");
//       }
      /* 44 - Aplicar*/
      echo("                        <li><span onclick='Aplicar();'>".RetornaFraseDaLista($lista_frases, 44)."</span></li>\n");
        /* 1 (gen) - Apagar */
      echo("                        <li><span onclick='Apagar();'>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
    }
    elseif(strcmp($status_enquete, "ANDAMENTO") == 0){
      if(votaEnquete($sock, $ator, $enquete)){
        /* 45 - Votar*/
        echo("                        <li><span onclick='Votar();'>".RetornaFraseDaLista($lista_frases, 45)."</span></li>\n");
      }
      /* 46 - Adiantar / Prorrogar */
      echo("                        <li><span onclick='CarregaDataHora();MostraLayer(lay_adiantar_prorrogar);'>".RetornaFraseDaLista($lista_frases, 46)."</span></li>\n");
      /* 47 - Finalizar*/
      echo("                        <li><span onclick='Finalizar();'>".RetornaFraseDaLista($lista_frases, 47)."</span></li>\n");
      /* 1 - Apagar (gen) */
      echo("                        <li><span onclick='Apagar();'>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
    }
    elseif(strcmp($status_enquete, "ENCERRADA") == 0){
      /* 113 - Prorrogar */
      echo("                        <li><span onclick='data_fim =\"".UnixTime2Data(time())."\" ; hora_fim = \"".UnixTime2Hora(time())."\" ;  CarregaDataHora(); MostraLayer(lay_adiantar_prorrogar)'>".RetornaFraseDaLista($lista_frases, 113)."</span></li>\n");
      /* 1 - Apagar (gen) */
      echo("                        <li><span onclick=\"Apagar();\">".RetornaFraseDaLista($lista_frases_geral,1)."</a></li>\n");
    }
    elseif(strcmp($status_enquete, "LIXEIRA") == 0){
      /* 98 - Recuperar */
      echo("                        <li><span onclick='Recuperar();'>".RetornaFraseDaLista($lista_frases, 98)."</span></li>\n");
      /* 99 - Excluir */
      echo("                        <li><span onclick='Excluir();'>".RetornaFraseDaLista($lista_frases, 99)."</span></li>\n");

    }else{
    /*********************************
    NAO FORMADOR
    ********************************/
      if ((votaEnquete($sock, $ator, $enquete)) && ((strcmp($status_enquete, "ANDAMENTO") == 0))){
        /* 45 - Votar */
        echo("                  <li><span onclick=\"Votar();\">".RetornaFraseDaLista($lista_frases,45)."</a></li>\n");
      }
    }
  }
  echo("                      </ul>\n");

  echo("                    </td>\n");
  echo("                    <td>\n");
                              /* 48 - Data de inicio */
  echo("                      <b>".RetornaFraseDaLista($lista_frases,48).":</b>\n");
  echo("                      ".UnixTime2DataHora($enquete['data_inicio'])."<br />\n");
  
                              /* 49 - Data de Termino */
  echo("                      <b>".RetornaFraseDaLista($lista_frases,49).":</b>\n");
  echo("                     ".UnixTime2DataHora($enquete['data_fim'])."\n");
  
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  /* 10 - Pergunta */ 
  echo("                    <td class=\"itens\" colspan=\"3\">".RetornaFraseDaLista($lista_frases,10)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"3\">".$enquete['pergunta']."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  /* 11 - Alternativas */ 
  echo("                    <td class=\"center\" colspan=\"3\">".RetornaFraseDaLista($lista_frases,11)."</td>\n");
  echo("                  </tr>\n");

  $alternativas = getAlternativas($sock, $idEnquete);
  $total_votos = getTotalVotos($sock, $idEnquete);
  $cont = 0;

  /*******************************
  Imprime as ALTERNATIVAS, junto com o RESULTADO, SE PERMITIDO
  Mostra tamb� em qual alternativa o usu�io VOTOU, caso j�tenha votado
  *******************************/
  foreach($alternativas as $cod => $alternativa){

    $exibe_resultados = (podeVerResultadoEnquete($sock, $ator, $enquete, CriadorEnquete($sock, $cod_curso, $cod_usuario, $idEnquete))) && (!strcmp($status_enquete, "NAO_APLICADA") == 0);

    echo("               <tr>\n");
    echo("                 <td class=\"itens\" ".((!$exibe_resultados) ? " colspan=\"3\"" : "").">\n");
    echo("                   ".(++$cont).") ".$alternativa['texto']."");

    /* marca ou n� se VOTOU nessa alternativa */
    if(verificaVotoAlternativa($sock, $cod_usuario, $alternativa['idAlternativa'])){
      echo("                 <img src=\"../imgs/arrow_left.jpg\" width=\"15px\" height=\"12px\" alt=\"\">\n");
    }
    echo("                 </td>\n");


    /* mostra ou n� o RESULTADO da enquete */
    if($exibe_resultados){
      echo("                 <td class=\"itens\" colspan=\"2\">\n");
      $tamanho_barra = 300; 
      if ($total_votos == 0)
      { 
        $percentagem = 0;
      }
      else
      { 
        $percentagem = ($alternativa['votos'] / $total_votos) * 100 ;
      }

      echo("                   <img src=\"../imgs/barra.jpg\" width=\"".((($percentagem*$tamanho_barra)/100) + 1)."px\" height=\"10px\" alt=\"\">");
      echo("                   ".(number_format($percentagem, 2))."% (".$alternativa['votos'].")\n");
      echo("                 </td>\n");
    }

    echo("               </tr>\n");
  }

  /* TOTAL de VOTOS */
  if ((podeVerIdentidadeEnquete($sock, $ator, $enquete, CriadorEnquete($sock, $cod_curso, $cod_usuario, $idEnquete))) && (!strcmp($status_enquete, "NAO_APLICADA") ==0)){
    /* 63 - Total de votos: */
    echo("               <tr class=\"altColor0\">\n");
    echo("                 <td>\n");
    echo("                   <strong>".RetornaFraseDaLista($lista_frases,63)."</strong> ".$total_votos."\n");
    echo("                 </td>\n");
    echo("                 <td colspan=\"2\">\n");

    /* 64 - Mostrar votos */
    echo("                   <span class=\"link\" onclick=\"abrir('identidade_enquete.php?cod_curso=".$cod_curso."&amp;idEnquete=".$idEnquete."', '280', '400');\">".RetornaFraseDaLista($lista_frases,64)."</span>");
    echo(" /");
    /* 65 - Ver participantes */
    echo("                   <span class=\"link\" onclick=\"abrir('participantes_enquete.php?cod_curso=".$cod_curso."&amp;idEnquete=".$idEnquete."', '280', '400');\">".RetornaFraseDaLista($lista_frases,65)."</span>\n");
    echo("                 </td>\n");
    echo("               </tr>\n");
  }
  echo("               <tr class=\"head\">\n");
  echo("                 <td class=\"center\" colspan=\"3\">\n");
  /* 31 - Configuracoes */
  echo("                   ".RetornaFraseDaLista($lista_frases,31)."\n");
  echo("                 </td>\n");
  echo("               </tr>\n");
  echo("               <tr>\n");
  echo("                 <td align='right'>\n");
  /* 66 - Aplicacao */
  echo("                   <b>".RetornaFraseDaLista($lista_frases,66)."</b>\n");
  echo("                 </td>\n");
  echo("                 <td align='left' colspan=\"2\">\n");

  /* imprime a quem a enquete ser�aplicada, separado por '\' */
  for ($i = 0; $i < strlen($enquete['aplicacao']); $i++)
  {
    if (strcmp($enquete['aplicacao'][$i], '-') != 0)
    {
      $papeis_aplicacao .= $frase_papeis[$enquete['aplicacao'][$i]]." / ";
    }
  }

  echo("                   ".(substr($papeis_aplicacao, 0, strlen($papeis_aplicacao) - 3))."\n");
  echo("                 </td>\n");
  echo("               </tr>\n");
  echo("               <tr>\n");
  echo("                 <td align='right'>\n");
  /* 67 - Compartilhamento de Resultado */
  echo("                   <b>".RetornaFraseDaLista($lista_frases,67)."</b>\n");
  echo("                 </td>\n");
  echo("                 <td align='left' colspan=\"2\">\n");

  /* imprime a quem o resultado sera mostrado, separado por '\' */
  for ($i = 0; $i < strlen($enquete['resultado']); $i++)
  {
    if (strcmp($enquete['resultado'][$i], '-') != 0)
    {
      $papeis_compartilhamento .= $frase_papeis[$enquete['resultado'][$i]]." / ";
    }
  }

  echo("                   ".(substr($papeis_compartilhamento, 0, strlen($papeis_compartilhamento) - 3))."\n");
  echo("                 </td>\n");
  echo("               </tr>\n");
  echo("               <tr>\n");
  echo("                 <td align='right'>\n");
  /* 68 - Resultado parcial */
  echo("                   <b>".RetornaFraseDaLista($lista_frases,68)."</b>\n");
  echo("                 </td>\n");
  echo("                 <td align='left' colspan=\"2\">\n");
  echo("                   ".$frase_boolean[$enquete['resultado_parcial']]."\n");
  echo("                 </td>\n");
  echo("               </tr>\n");
  echo("               <tr>\n");
  echo("                 <td align='right'>\n");
  /* 69 - Identidade dos votos*/
  echo("                   <b>".RetornaFraseDaLista($lista_frases,69)."</b>\n");
  echo("                 </td>\n");
  echo("                 <td align='left' colspan=\"2\">\n");
  echo("                   ".$frase_boolean[$enquete['identidade_votos']]."\n");
  echo("                 </td>\n");
  echo("               </tr>\n");
  echo("               <tr>\n");
  echo("                 <td align='right'>\n");
  /* 70 - Numero de escolhas */
  echo("                   <b>".RetornaFraseDaLista($lista_frases,70)."</b>\n");
  echo("                 </td>\n");
  echo("                 <td align='left' colspan=\"2\">\n");
  echo("                   ".$frase_escolhas[$enquete['num_escolhas']]."\n");
  echo("                 </td>\n");
  echo("               </tr>\n");

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

  echo("  </body>\n");
  echo("</html>\n");
?>
