<?php

/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/material/importar_material.php

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
  ARQUIVO : cursos/aplic/material/importar_material.php
  ========================================================== */
$bibliotecas = "../bibliotecas/";
include ($bibliotecas . "geral.inc");
include ("material.inc");

include ("../topo_tela.php");
Desconectar($sock);

Desconectar($sock);
$sock = Conectar("");

$lista_frases_biblioteca = RetornaListaDeFrases($sock,-2);

Desconectar($sock);

$sock = Conectar($cod_curso);

switch ($cod_ferramenta) {
  case 3 :
    $tabela = "Atividade";
    $dir = "atividades";
    break;
  case 4 :
    $tabela = "Apoio";
    $dir = "apoio";
    break;
  case 5 :
    $tabela = "Leitura";
    $dir = "leituras";
    break;
  case 7 :
    $tabela = "Obrigatoria";
    $dir = "obrigatoria";
    break;
}

$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 1;

$sock = MudarDB($sock, $cod_curso_origem);
    
echo ("    <script type=\"text/javascript\" language=\"javascript\" defer>\n\n");

echo ("      function Iniciar(){\n");
echo ("        startList();\n");
echo ("      }\n\n");

echo ("      function ExibirItem(cod_item)\n");
echo ("       {\n");
echo ("         document.frmImportar.cod_item.value = cod_item;\n");
echo ("         document.frmImportar.action = \"importarItem.php\";");
echo ("         document.frmImportar.submit();\n");
echo ("       }\n\n");

echo ("       function Validacheck()\n");
echo ("       {\n");
echo ("         var cont = false;\n");
echo ("         var nome_var1 = 'cod_topicos_import[]';\n");
echo ("         var nome_var2 = 'cod_itens_import[]';\n");
echo ("         var e;\n");

echo ("         for (i = 0, total = document.getElementsByTagName('input').length; ((i < total) && (cont == false)); i++)\n");
echo ("         {\n");
echo ("           e = document.getElementsByTagName('input')[i];\n");
echo ("           if ((e.type == 'checkbox') && ((e.name == nome_var1) || (e.name == nome_var2)) && (e.checked == true))\n");
echo ("           {\n");
echo ("             cont = true;\n");
echo ("           }\n");
echo ("         }\n");

echo ("         if (cont == true)\n");
echo ("           return true;\n");
echo ("         else\n");
echo ("         {\n");
/*58(biblioteca) - Selecione pelo menos um item*/
echo ("           alert('" . RetornaFraseDaLista($lista_frases_biblioteca, 58) . "');\n");
echo ("           return false;\n");
echo ("         }\n");
echo ("       }\n");

echo ("       function Importar()\n");
echo ("       {\n");
echo ("         if(Validacheck())\n");
echo ("         {\n");
echo ("           document.frmImportar.action = 'acoes.php';\n");
echo ("           document.frmImportar.acao.value = \"importarItem\";\n");
echo ("           document.frmImportar.submit();\n");
echo ("         }\n");
echo ("       }\n\n");

echo ("       function CancelarImportacao()\n");
echo ("       {\n");
echo ("         document.location='material.php?cod_curso=" . $cod_curso . "&cod_usuario=" . $cod_usuario . "&cod_ferramenta=" . $cod_ferramenta . "';\n");
echo ("       }\n\n");

echo("        function CheckAll() {\n");
echo("            var elem = document.frmImportar.elements;\n");    /* Pega todos os elemntos que estão no form 'frmImportar'. */
echo("            var nome_var1 = 'cod_topicos_import[]';\n");
echo("            var nome_var2 = 'cod_itens_import[]';\n");
echo("            var checkAll = document.getElementById('select_all');\n");    /* Pega o checkBox que seleciona totos. */
echo("            var changed = checkAll.checked;\n\n");
echo("            for(var i = 0; i < elem.length; i++) {\n");
echo("                if ((elem[i].name == nome_var1) || (elem[i].name == nome_var2)) {\n");
echo("                    elem[i].checked = changed;\n");    /* Seta o checkbox de acordo com o checkbox 'select_all' */
echo("                }\n");
echo("            }\n");
echo("        }\n\n");

echo ("       function MudarTopico(cod_topico){\n");
echo ("         document.frmImportar.action = \"importar_material.php?cod_curso=".$cod_curso."&cod_ferramenta=".$cod_ferramenta."&cod_assunto_pai=1&cod_topico_raiz=".$cod_topico_raiz."&cod_curso_origem=".$cod_curso_origem."\";\n");
echo ("         document.frmImportar.cod_topico_raiz.value = cod_topico;\n");
echo ("         document.frmImportar.submit();\n");
echo ("       }\n");

echo ("       function ExibirItem(cod_item){\n");
echo ("         document.frmImportar.cod_item.value = cod_item;\n");
echo ("         document.frmImportar.action = \"importar_ver.php?cod_curso=" . $cod_curso . "&cod_ferramenta=" . $cod_ferramenta . "&cod_usuario=" . $cod_usuario . "\";\n");
echo ("         document.frmImportar.submit();\n");
echo ("       }\n");

echo ("     </script>\n\n");

include ("../menu_principal.php");
$sock = MudarDB($sock, $cod_curso_origem);
echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
// 1 - "Material"
$cabecalho = ("          <h4>" . RetornaFraseDaLista($lista_frases, 1));
/*107 - Importando "Material" */
$cabecalho .= (" - " . RetornaFraseDaLista($lista_frases, 107) . "</h4>\n");
echo ($cabecalho);

echo ("          <div id=\"mudarFonte\">\n");
echo ("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
echo ("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
/* 1 - Perguntas Freq�entes */
$cabecalho = "  <b class=\"titulo\">".RetornaFraseDaLista($lista_frases,1)."</b>";

echo ("            <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo ("              <tr>\n");
echo ("                <td valign=\"top\">\n");
echo ("                  <ul class=\"btAuxTabs\">\n");
/* 2 - Cancelar (geral) */
echo ("      <li><span onClick=\"history.go(-1);\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>");
echo ("                </ul>\n");
echo ("                </td>\n");
echo ("              </tr>\n");
echo ("              <tr>\n");
echo ("                <td>\n");
echo ("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo ("                    <tr class=\"head\">\n");
echo ("                      <td align=\"center\" width=\"20px\"><input type=\"checkbox\" id='select_all' name=\"select_all\" onClick=\"CheckAll()\"/></td>\n");
/* 89 - Selecionar todos */
echo ("                      <td class=\"alLeft\" colspan=\"3\">".RetornaFraseDaLista($lista_frases_geral,89)."</td>\n");
echo ("                    </tr>\n");

/*verificar status... confirmar.. e verificar c eh necessario cancelar a edicao!*/

$lista_topicos = RetornaTopicosDoTopico($sock, $tabela, $cod_topico_raiz);
$lista_itens = RetornaItensDoTopico($sock, $tabela, $cod_topico_raiz);
echo("  <form method=\"post\" name=\"frmImportar\">");
echo("    <input type=\"hidden\" name=\"cod_curso\"        value=\"".$cod_curso."\">\n");
echo("    <input type=\"hidden\" name=\"acao\"             value=\"\">\n");
echo("    <input type=\"hidden\" name=\"cod_item\"         value=\"".$cod_item."\">\n");
echo("    <input type=\"hidden\" name=\"cod_topico_raiz\"  value=\"".$cod_topico_raiz."\">\n");
echo("    <input type=\"hidden\" name=\"cod_assunto_pai\"  value=\"".$cod_assunto_pai."\">\n");
echo("    <input type=\"hidden\" name=\"cod_assunto_dest\" value=\"\">\n");
echo("    <input type=\"hidden\" name=\"cod_ferramenta\"   value=\"".$cod_ferramenta."\">\n");
echo("    <input type=\"hidden\" name=\"tabela\"           value=\"".$tabela."\">\n");
echo("    <input type=\"hidden\" name=\"dir\"              value=\"".$dir."\">\n");

if (((count($lista_topicos) < 1) || ($lista_topicos == "")) && ((count($lista_itens) < 1) || ($lista_itens == ""))) {
  echo ("                    <tr>\n");
  /* 15 - 3: Nao ha nenhuma atividade
          4: Nao ha nenhum material de apoio
          5: Nao ha nenhuma leitura
          7: Nao ha nenhuma parada obrigatória
   */
  echo ("                      <td colspan=\"5\" align=\"center\">" . RetornaFraseDaLista($lista_frases, 15) . "</td>\n");
  echo ("                    </tr>\n");
} else {

  $top_index = 0;
  $itens_index = 0;
  for ($i = 0; $i < ((count($lista_topicos)) + (count($lista_itens))); $i++) {
    if ((!isset ($lista_topicos[$top_index]['posicao_topico'])) || (isset ($lista_itens[$itens_index]['posicao_item']) && ($lista_topicos[$top_index]['posicao_topico'] > $lista_itens[$itens_index]['posicao_item']))) {
      $lista_unificada[$i] = $lista_itens[$itens_index];
      $itens_index++;
    } else {
      //este if é para não alterar a estrutura dos portfólios antigos
      if ((isset ($lista_itens[$top_index]['posicao_item'])) && ($lista_topicos[$top_index]['posicao_topico'] == $lista_itens[$itens_index]['posicao_item'])) {
      	$lista_itens[$itens_index]['posicao_item']++;
      }
      $lista_unificada[$i] = $lista_topicos[$top_index];
      $top_index++;
    }
  }

  foreach ($lista_unificada as $cod => $linha) {
    //se é tópico...
    if (isset ($linha['posicao_topico'])) {
      echo ("                    <tr>\n");
      echo ("                      <td>\n");
      echo ("                        <input type=\"checkbox\" id=\"chktop_" . $linha['cod_topico'] . "\" name=\"cod_topicos_import[]\" value=\"" . $linha['cod_topico'] . "\" />\n");
      echo ("                      </td>\n");
      echo ("                      <td width=\"72%\" class=\"alLeft\"><img src=\"../imgs/pasta.gif\" border=\"0\" />&nbsp;&nbsp;<span class=\"link\" onClick=\"MudarTopico('" . $linha['cod_topico'] . "');\">" . $linha['topico'] . "</span></td>\n");
      echo ("                    </tr>\n");
    }
    //é item
    else if (isset ($linha['posicao_item'])) {

      $data = UnixTime2Data($linha['data']);
      if ($linha['tipo_compartilhamento'] == "T") {
        /* 16 - Totalmente Compartilhado */
        $compartilhamento = RetornaFraseDaLista($lista_frases, 16);
      } else {
        /* 17 - Compartilhado com Formadores */
        $compartilhamento = RetornaFraseDaLista($lista_frases, 17);
      }
      if ($data_acesso < $linha['data']) {
        $marcatr = " class=\"novoitem\"";
      } else {
        $marcatr = "";
      }

      if ($linha['status'] == "E") {
        $linha_historico = RetornaUltimaPosicaoHistorico($sock, $tabela, $linha['cod_item']);
        if ($linha['inicio_edicao'] < (time() - 1800) || $cod_usuario == $linha_historico['cod_usuario']) {
          CancelaEdicao($sock, $tabela, $dir, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, $criacao_avaliacao);

          $titulo = "<img src=\"../imgs/arqp.gif\" border=\"0\" />&nbsp;&nbsp;<span class=\"link\" onClick=\"ExibirItem('" . $linha['cod_item'] . "');\">" . $linha['titulo'] . "</span>";
        } else {
          /* 18 - Em Edicao */
          $data = "<span class=\"link\" onClick=\"window.open('em_edicao.php?cod_curso=" . $cod_curso . "&cod_item=" . $linha['cod_item'] . "&origem=material&cod_ferramenta=" . $cod_ferramenta . "','EmEdicao','width=300,height=220,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">" . RetornaFraseDaLista($lista_frases, 18) . "</a>";

          $titulo = "<img src=\"../imgs/arqp.gif\" border=\"0\" />&nbsp;&nbsp;" . $linha['titulo'];
        }
      } else {
        $titulo = "<img src=\"../imgs/arqp.gif\" border=\"0\" />&nbsp;&nbsp;<span class=\"link\" onClick=\"ExibirItem('" . $linha['cod_item'] . "');\">" . $linha['titulo'] . "</span>";
      }
  
      echo ("                    <tr" . $marcatr . " id=\"tr_" . $linha['cod_item'] . "\">\n");
      echo ("                      <td width=\"2%\"><input type=\"checkbox\" id=\"chkitm_" . $linha['cod_item'] . "\" name=\"cod_itens_import[]\" value=\"" . $linha['cod_item'] . "\" /></td>\n");
      echo ("                      <td width=\"72%\" class=\"alLeft\">" . $titulo . "</td>\n");
      echo ("                    </tr>\n");

    } //else
  }

} // else - count(lista_topicos), count(lista_itens)

echo ("                  </table>\n");
echo ("                </td>\n");
echo ("              </tr>\n");
echo ("              <tr>\n");
echo ("                <td valign=\"top\">\n");
echo ("                  <ul class=\"btAuxTabs\">\n");
/* 105 - 3: Importar Atividade
         4: Importar Material de Apoio
         5: Importar Leitura
         7: Importar Parada Obrigat�ria
*/
echo ("                    <li><span onClick=\"Importar()\">" . RetornaFraseDaLista($lista_frases, 105) . "</span></li>\n");
echo ("                  </ul>\n");
echo ("                </td>\n");
echo ("              </tr>\n");
echo ("            </table>\n");
echo ("          </form>\n");
echo ("        </td>\n");
echo ("      </tr>\n");

include ("../tela2.php");

echo ("  </body>\n");
echo ("</html>\n");

Desconectar($sock);
?>
