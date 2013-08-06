<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/lixeira.php

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
  ARQUIVO : cursos/aplic/material/lixeira.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("material.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objMudarComp = new xajax();
  // Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objMudarComp->registerFunction("ExcluirItensDinamic");
  $objMudarComp->registerFunction("RecuperarItensDinamic");
  // Manda o xajax executar os pedidos acima.
  $objMudarComp->processRequests();

  $cod_ferramenta = $_GET['cod_ferramenta'];
  include("../topo_tela.php");

  /* Registrando c�digo da ferramenta nas vari�veis de sess�o.
     � necess�rio para saber qual ferramenta est� sendo
     utilizada, j� que este arquivo faz parte de quatro
     ferramentas quase distintas.
   */
  session_register("cod_ferramenta_m");
  if (isset($cod_ferramenta))
    $cod_ferramenta_m=$cod_ferramenta;
  else
    $cod_ferramenta=$cod_ferramenta_m;

  if ($cod_ferramenta==3)
    include("avaliacoes_material.inc");

  /* T�pico origem para poder retornar no mesmo lugar. */
  session_register("cod_topico_s");
  if (isset($cod_topico))
    $cod_topico_s=$cod_topico;
  else
    $cod_topico=$cod_topico_s;

  Desconectar($sock);

  $sock=Conectar("");

  $diretorio_arquivos=RetornaDiretorio($sock,'Arquivos');
  $diretorio_temp=RetornaDiretorio($sock,'ArquivosWeb');

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  $AcessoAvaliacao=TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);

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

  $data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
  $cod_topico_raiz=2;

  // Habilita o Ajuda.
  $cod_ferramenta_ajuda = $cod_ferramenta;

  // Lixeiras de ferramentas diferentes
  // tem codigos diferentes
  if ($cod_ferramenta == 3){
    $cod_pagina_ajuda = 8;
  } else {
    $cod_pagina_ajuda = 3;
  }

  echo("    <script type=\"text/javascript\" language=\"JavaScript\">\n");
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n");
  if (EFormador($sock,$cod_curso,$cod_usuario))
  {

  /* Fun��es JavaScript */
  echo("    <script type=\"text/javascript\" language=\"javascript\">\n");

  echo("      function ExcluirSelecionados(){\n");
    /* 6 - Voc� tem certeza de que deseja excluir esta atividade? */
    /* 25 - (a atividade ser� exclu�da definitivamente) */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,25)."')){\n");
  echo("          xajax_ExcluirItensDinamic('".$tabela."', '".$cod_curso."', '".$cod_usuario."', array_itens);\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function RecuperarSelecionados(){\n");
    /* 26 - Voc� tem certeza de que deseja recuperar esta atividade? */
    /* 27 - (a atividade ser� movida para a pasta Raiz e ser� compartilhada com formadores) */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,26)."\\n".RetornaFraseDaLista($lista_frases,27)."')){\n");
  echo("          xajax_RecuperarItensDinamic('".$tabela."', '".$cod_curso."', '".$cod_usuario."','".$cod_grupo_portfolio."', array_itens);\n");
  echo("        }\n");
  echo("      }\n\n");


  echo("      function Recarregar(){\n");
  echo("        window.location='lixeira.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."';\n");
  echo("      }\n\n");


  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        var Cabecalho = document.getElementById('checkMenu');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i<cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            var item = cod_itens[i].id.split('_');\n");
  echo("            array_itens[j]=item[1];\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (j==cod_itens.length) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if(j>0){\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mRecuperar_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
  echo("          document.getElementById('mRecuperar_Selec').onclick=function(){ RecuperarSelecionados(); };\n");
  echo("        }else{\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mRecuperar_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
  echo("          document.getElementById('mRecuperar_Selec').onclick=function(){  };\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        for(i = 0; i < cod_itens.length; i++)\n");
  echo("        {\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");
  echo("    </script>\n");

  $objMudarComp->printJavascript("../xajax_0.2.4/");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Layers */

  /* Página Principal */
  /* 1 - 3: Atividades
          4: Material de Apoio
          5: Leituras
          7: Parada Obrigatória
    */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, 11)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  $cod_pagina=3;
  if(($cod_ferramenta==3) && ($AcessoAvaliacao)&&(EFormador($sock,$cod_curso,$cod_usuario))){
    /*verifica��o se aparecer� ajuda de avalia��es*/
    $cod_pagina=8;
  }

  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <!-- Botoes de Acao -->\n");
  echo("              <td valign=\"top\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // 28 - Voltar para Material de Apoio
  echo("                  <li><a href=\"material.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."\">".RetornaFraseDaLista($lista_frases,28)."</a></li>\n"); 
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");

  /* 12 - 3: Atividade
          4: Material de Apoio
          5: Leitura
          7: Parada Obrigat�ria
    */
  echo("                    <td class=\"alLeft\">".RetornaFraseDaLista($lista_frases,12)."</td>\n");
  /* 13 - Data */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,13)."</td>\n");

  if (($cod_ferramenta==3)&&( $AcessoAvaliacao)){
    /* 90 - Avalia��o */
    echo("                    <td>".RetornaFraseDaLista($lista_frases,90)."</td>\n");
  }

  echo("                  </tr>\n");

  //Retorna Itens da Lixeira
  $lista_itens=RetornaItensDoTopico($sock, $tabela, $cod_topico_raiz);

  if ((empty($lista_topicos)) && (empty($lista_itens))){
    echo("                  <tr>\n");
    /* 15 - 3: Não há nenhuma atividade
            4: Não há nenhum material de apoio
            5: Não há nenhuma leitura
            7: Não há nenhuma parada obrigatória
    */
    echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,15)."</td>\n");
    echo("                  </tr>\n");

  }else{

    if (count($lista_itens)>0){
      foreach ($lista_itens as $cod => $linha_item){
        $data=UnixTime2Data($linha_item['data']);

        if ($data_acesso<$linha_topico['data']){
          $marca="<font color=\"red\" size=\"+1\" class=\"text\">*</font>";
        }else{
          $marca="";
        }

        $titulo="<a href=\"ver_lixeira.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_item=".$linha_item['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha_item['titulo']."</a>";
        $icone="<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" />".$marca;

        echo("                  <tr id=\"tr_".$linha_item['cod_item']."\">\n");
        echo("                    <td width=\"2%\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_item']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_item']."\" /></td>\n");
        echo("                    <td class=\"alLeft\">\n");
        echo("                      ".$icone." ".$titulo."\n");
        echo("                    </td>\n");
        echo("                    <td width=\"10%\">\n");
        echo("                      <span id=\"data_".$linha_item['cod_item']."\">".$data."</span>\n");
        echo("                    </td>\n");
        if (($AcessoAvaliacao)&&($cod_ferramenta==3)){
          if (AtividadeEhAvaliacao($sock,$linha_item['cod_item'])){
            $cod_avaliacao = RetornaCodAvaliacao($sock,$linha_item['cod_item']);
            /* 35 - Sim (ger)*/
            echo("                    <td width=\"10%\">\n");
            echo("                      <span class=\"link\" onclick='VerAvaliacaoLixeira(".$cod_avaliacao.");return(false);'>".RetornaFraseDaLista($lista_frases_geral,35)."</span>\n");
            echo("                    </td>");
          }
          else if (AtividadeEraAvaliacao($sock,$linha_item['cod_item']))
          {
            $cod_avaliacao=RetornaCodAvaliacaoApagada($sock,$linha_item['cod_item']);
            /* 35 - Sim (ger)*/
            echo("                    <td width=\"10%\">\n");
            echo("                      <span class=\"link\" onclick='VerAvaliacaoLixeira(".$cod_avaliacao.");'>".RetornaFraseDaLista($lista_frases_geral,35)."</span>\n");
            echo("                    </td>");
          }
          else
          /* 36 - Não (ger)*/
            echo("                    <td>".RetornaFraseDaLista($lista_frases_geral,36)."</td>\n");
        }
        echo("                  </tr>\n");


      }
    }
  }

  echo("                </table>\n");

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          <ul>\n");
  /* 68 - Excluir selecionados */
  echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".RetornaFraseDaLista($lista_frases_geral,68)."</span></li>\n");
  /* 79 - Recuperar selecionados */
  echo("            <li id=\"mRecuperar_Selec\" class=\"menuUp\"><span id=\"moverSelec\">".RetornaFraseDaLista($lista_frases_geral,79)."</span></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");

  // inclui o rodape
  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
  exit();

/* Fim da P�gina do Formador ***************************/
  }
?>
