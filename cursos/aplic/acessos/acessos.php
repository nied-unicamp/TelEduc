<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/acessos.php

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
  ARQUIVO : cursos/aplic/acessos/acessos.php
  ========================================================== */

/* C�digo principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("acessos.inc");

  $cod_ferramenta = 18;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=1;

  include("../topo_tela.php");

  $lista_frases_ferramentas=$lista_frases_menu;
  $lista_ferramentas=$tela_lista_ferramentas;
  $ordem_ferramentas=$tela_ordem_ferramentas;
   
  Desconectar($sock);
  $sock = Conectar("");
  $ha_convidados = HaConvidados($sock,$cod_curso);
  $ha_visitantes = HaVisitantes($sock);
  Desconectar($sock);
  $sock = Conectar($cod_curso);

  GeraJSComparacaoDatas();
  GeraJSVerificacaoData();
  echo("    <script type=\"text/javascript\">\n");


  echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("      var versao = (navigator.appVersion.substring(0,3));\n");
  echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");

  echo("      if (isNav)\n");
  echo("      {\n");
  echo("        document.captureEvents(Event.MOUSEMOVE);\n");
  echo("      }\n");
  echo("      document.onmousemove = TrataMouse;\n\n");


  echo("      function TrataMouse(e)\n");
  echo("      {\n");
  echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
  echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
  echo("      }\n\n");

  echo("      function getPageScrollY()\n");
  echo("      {\n");
  echo("        if (isNav)\n");
  echo("          return(window.pageYOffset);\n");
  echo("        if (isIE)\n");
  echo("          return(document.body.scrollTop);\n");
  echo("      }\n\n");

  echo("      function AjustePosMenuIE()\n");
  echo("      {\n");
  echo("        if (isIE)\n");
  echo("          return(getPageScrollY());\n");
  echo("        else\n");
  echo("          return(0);\n");
  echo("      }\n\n");
  
   /* Iniciliza os layers. */
  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        lay_calendario = getLayer('layer_calendario');\n"); 
  echo("        startList();\n");
  echo("      }\n\n");

  // Esconde o layer especificado por cod_layer.
  echo("      function EscondeLayer(cod_layer)\n");
  echo("      {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("      }\n\n");

  /* Esconde todos os layers. Se o usuario for o propriet�rio do di�rio   */
  /* visualizado ent�o esconde o layer para renomear o item.              */
  echo("      function EscondeLayers()\n");
  echo("      {\n");
  echo("        hideLayer(lay_calendario);\n"); 
  echo("      }\n\n");

  /* Exibe o layer especificado por cod_layer.                            */
  echo("      function MostraLayer(cod_layer)\n");
  echo("      {\n");
  echo("        EscondeLayers();\n");
  echo("        moveLayerTo(cod_layer, Xpos, Ypos + AjustePosMenuIE());\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n\n");

  echo("      function AlgumaMarcada()\n");
  echo("      {\n");
  echo("        var retorno; \n");
  echo("        retorno = document.formFreq.check_alunos.checked || document.formFreq.check_formadores.checked;\n");

  echo("        retorno = retorno || document.formFreq.check_grupos.checked; \n");

  if ($ha_convidados)
    echo("        retorno = retorno || document.formFreq.check_convidados.checked; \n");
    
  if ($ha_visitantes)
    echo("        retorno = retorno || document.formFreq.check_visitantes.checked; \n");

  echo("        return(retorno);\n");
  echo("      }\n");

  echo("      function EmulaSubmissaoAcessos()\n");
  echo("      {\n");
  echo("        var saida = 'relatorio_acessos.php?cod_curso=".$cod_curso."';\n");
  // Verificando exibição de dados principais
  echo("        if (document.formAcessos.check_ultimos.checked)\n");
  echo("        {\n");
  echo("          saida+='&check_ultimos=1';\n");
  echo("        }\n");
  echo("        if (document.formAcessos.check_qtde.checked)\n");
  echo("        {\n");
  echo("          saida+='&check_qtde=1';\n");
  echo("        }\n");
  // Verificando exibição de dados adicionais
  echo("        if (document.formAcessos.check_local.checked)\n");
  echo("        {\n");
  echo("          saida+='&check_local=1';\n");
  echo("        }\n");
  echo("        if (document.formAcessos.check_cidade.checked)\n");
  echo("        {\n");
  echo("          saida+='&check_cidade=1';\n");
  echo("        }\n");
  echo("        if (document.formAcessos.check_estado.checked)\n");
  echo("        {\n");
  echo("          saida+='&check_estado=1';\n");
  echo("        }\n");
  // Verificando exibição de dados adicionais
  echo("        for (var i=0; i<document.formAcessos.radio_ord.length; i++)\n");
  echo("        {\n");
  echo("          if (document.formAcessos.radio_ord[i].checked)\n");
  echo("            break\n");
  echo("        }\n");
  echo("        var radio_ord = document.formAcessos.radio_ord[i].value;\n");
  echo("        saida+='&radio_ord='+radio_ord;\n");
  echo("        window.open(saida,'RelatorioAcessos','width=600,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("      }\n\n");

  echo("      function VerificaCamposFrequencia()\n");
  echo("      {\n");
  echo("        var campo_i = document.formFreq.data_ini;\n");
  echo("        var campo_f = document.formFreq.data_fim;\n");
  echo("        if (!DataValidaAux(campo_i))\n");
  echo("          return false;\n");
  echo("        if (!DataValidaAux(campo_f))\n");
  echo("          return false;\n");
  echo("        if (ComparaData(campo_i, campo_f) > 0)\n");
  echo("        {\n");
  // 38 - A data inicial n� pode ser posterior �data final no per�do de busca.
  echo("          alert('".RetornaFraseDaLista($lista_frases,38)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        if (!AlgumaMarcada())\n");
  echo("        {\n");
  // 39 - Selecione ao menos uma op�o de exibi�o.
  echo("          alert('".RetornaFraseDaLista($lista_frases,39)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n\n");
  
  echo("      function VerificaCamposFrequenciaIndividual()\n");
  echo("      {\n");
  echo("        var campo_i = document.formFreq.data_ini_i;\n");
  echo("        var campo_f = document.formFreq.data_fim_i;\n");
  echo("        if (!DataValidaAux(campo_i))\n");
  echo("          return false;\n");
  echo("        if (!DataValidaAux(campo_f))\n");
  echo("          return false;\n");
  echo("        if (ComparaData(campo_i, campo_f) > 0)\n");
  echo("        {\n");
  // 38 - A data inicial n�o pode ser posterior � data final no per�odo de busca.
  echo("          alert('".RetornaFraseDaLista($lista_frases,38)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n\n");

  echo("      function EmulaSubmissaoFrequencia()\n");
  echo("      {\n");
  echo("        if (VerificaCamposFrequencia())\n");
  echo("        {\n");
  echo("          var data_i= document.formFreq.data_ini.value;\n");
  echo("          var data_f= document.formFreq.data_fim.value;\n");
  echo("          var saida = 'relatorio_frequencia.php?cod_curso=".$cod_curso."';\n");
  echo("          saida+='&data_ini='+data_i;\n");
  echo("          saida+='&data_fim='+data_f;\n");
  // Verificando exibição de participantes
  echo("          if (document.formFreq.check_alunos.checked)\n");
  echo("          {\n");
  echo("            saida+='&check_alunos=1';\n");
  echo("          }\n");
  echo("          if (document.formFreq.check_formadores.checked)\n");
  echo("          {\n");
  echo("            saida+='&check_formadores=1';\n");
  echo("          }\n");
  // Verificando exibi�o de grupos
  echo("          if (document.formFreq.check_grupos.checked)\n");
  echo("          {\n");
  echo("            saida+='&check_grupos=1';\n");
  echo("          }\n");
  if ($ha_convidados)
  {
    // Verificando exibi�o de convidados
    echo("          if (document.formFreq.check_convidados.checked)\n");
    echo("          {\n");
    echo("            saida+='&check_convidados=1';\n");
    echo("          }\n");
  }
  if ($ha_visitantes)
  {
    // Verificando exibi�o de convidados
    echo("          if (document.formFreq.check_visitantes.checked)\n");
    echo("          {\n");
    echo("            saida+='&check_visitantes=1';\n");
    echo("          }\n");
  }
  echo("          var index=document.formFreq.cod_ferramenta.selectedIndex;\n");
  echo("          if (index > 0)\n");
  echo("          {\n");
  echo("            var cod_fer = document.formFreq.cod_ferramenta[index].value;\n");
  echo("            saida+='&cod_ferramenta_relatorio='+cod_fer;\n");
  echo("          }\n");
  echo("          window.open(saida,'RelatorioFreq','width=750,height=600,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function EmulaSubmissaoFrequenciaIndividual()\n");
  echo("      {\n");
  echo("        if (VerificaCamposFrequenciaIndividual())\n");
  echo("        {\n");
  echo("          var data_i= document.formFreq.data_ini_i.value;\n");
  echo("          var data_f= document.formFreq.data_fim_i.value;\n");
  echo("          var saida = 'relatorio_frequencia.php?cod_curso=".$cod_curso."';\n");
  echo("          saida+='&data_ini='+data_i;\n");
  echo("          saida+='&data_fim='+data_f;\n");
  // Verificando exibição de participantes
  /*echo("          if (document.formFreq.check_alunos.checked)\n");
  echo("          {\n");
  echo("            saida+='&check_alunos=1';\n");
  echo("          }\n");
  echo("          if (document.formFreq.check_formadores.checked)\n");
  echo("          {\n");
  echo("            saida+='&check_formadores=1';\n");
  echo("          }\n");
  // Verificando exibi�o de grupos
  echo("          if (document.formFreq.check_grupos.checked)\n");
  echo("          {\n");
  echo("            saida+='&check_grupos=1';\n");
  echo("          }\n");
  if ($ha_convidados)
  {
    // Verificando exibi�o de convidados
    echo("          if (document.formFreq.check_convidados.checked)\n");
    echo("          {\n");
    echo("            saida+='&check_convidados=1';\n");
    echo("          }\n");
  }
  if ($ha_visitantes)
  {
    // Verificando exibi�o de convidados
    echo("          if (document.formFreq.check_visitantes.checked)\n");
    echo("          {\n");
    echo("            saida+='&check_visitantes=1';\n");
    echo("          }\n");
  }*/
  echo("          var index = document.formFreq.cod_aluno.selectedIndex;\n");
  echo("          if (index > 0)\n");
  echo("          {\n");
  echo("            var cod_alu = document.formFreq.cod_aluno[index].value;\n");
  echo("            saida+='&cod_aluno_relatorio='+cod_alu;\n");
  echo("          }\n");
  echo("          window.open(saida,'RelatorioFreq','width=750,height=600,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
  echo("        }\n");
  echo("      }\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("  </script>\n");

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Fóruns de Discussão */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onClick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <form action=relatorio_acessos.php name=\"formAcessos\" method=get target=\"JanelaAcessos\">\n");
  echo("                  <input type=hidden name=cod_curso value=".$cod_curso." />\n");
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  /* 2 - Relatório de Acessos */
  echo("                    <tr class=\"head\">\n");
  echo("                      <td colspan=6>".RetornaFraseDaLista($lista_frases,2)."</td>\n");
  echo("                    </tr>\n");
  /* 5 - Dados principais: */
  echo("                    <tr>\n");
  echo("                      <td width=15%><b>".RetornaFraseDaLista($lista_frases,5)."</b></td>\n");
  echo("                      <td class=\"alLeft\" style=\"border-right:2pt solid #DCDCDC;\" width=20%>\n");
  /* 6 - Últimos acessos */
  echo("                        <ul>\n");
  echo("                          <li><input type=checkbox name=check_ultimos checked />".RetornaFraseDaLista($lista_frases,6)."</li>\n");
  /* 7 - Quantidade de acessos */
  echo("                          <li><input type=checkbox name=check_qtde />".RetornaFraseDaLista($lista_frases,7)."</li>\n");
  echo("                        </ul>\n");
  echo("                      </td>\n");
  
  /* 8 - Dados adicionais: */
  echo("                      <td width=15%><b>".RetornaFraseDaLista($lista_frases,8)."</b></td>\n");
  echo("                      <td class=\"alLeft\" style=\"border-right:2pt solid #DCDCDC;\" width=15%>\n");
  /* 6 - Últimos acessos */
  echo("                        <ul>\n");
  /* 9 - Local de trabalho */
  echo("                          <li><input type=checkbox name=check_local checked />".RetornaFraseDaLista($lista_frases,9)."</li>\n");
  /* 10 - Cidade */
  echo("                          <li><input type=checkbox name=check_cidade checked />".RetornaFraseDaLista($lista_frases,10)."</li>\n");
  /* 11 - Estado */
  echo("                          <li><input type=checkbox name=check_estado checked />".RetornaFraseDaLista($lista_frases,11)."</li>\n");
  echo("                        </ul>\n");
  echo("                      </td>\n");
  
    /* 12 - Ordenar e agrupar dados por: */
  echo("                      <td width=20%><b>".RetornaFraseDaLista($lista_frases,12)."</b></td>\n");
  /* 13 - Nome */
  echo("                      <td class=\"alLeft\" width=20%>\n");
  echo("                        <ul>\n");
  echo("                          <li><input type=radio name=radio_ord name=\"radio_ord[0]\" value=\"nome\" checked />".RetornaFraseDaLista($lista_frases,13)."</li>\n");
  /* 9 - Local de trabalho */
  echo("                          <li><input type=radio name=radio_ord name=\"radio_ord[1]\" value=\"local\" />".RetornaFraseDaLista($lista_frases,9)."</li>\n");
  /* 10 - Cidade */
  echo("                          <li><input type=radio name=radio_ord name=\"radio_ord[2]\" value=\"cidade\" />".RetornaFraseDaLista($lista_frases,10)."</li>\n");
  /* 11 - Estado */
  echo("                          <li><input type=radio name=radio_ord name=\"radio_ord[3]\" value=\"estado\" />".RetornaFraseDaLista($lista_frases,11)."</li>\n");
  echo("                        </ul>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                    <tr>\n");
  echo("                      <td colspan=6>\n");
  echo("                        <ul class=\"btAuxTabs\">\n");
  echo("                          <li>\n");
  /* 15 - Exibir relatório */
  echo("                            <span onClick=\"EmulaSubmissaoAcessos();\"\">".RetornaFraseDaLista($lista_frases,15)."</span>\n");
  echo("                          </li>\n");
  echo("                        </ul>\n");
  echo("                        <br /><br /><br /><br />\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                  </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <form name=\"formFreq\" method=\"post\" action=\"relatorio_frequencia.php\">\n");  
  echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
   // esta form nao deve levar a lugar nenhum, vc precisa fazer o window open!
  echo("                    <tr class=\"head\">\n");
  /* 3 - Relatório de freqüência */
  echo("                      <td colspan=6>".RetornaFraseDaLista($lista_frases,3)."</td>\n");
  echo("                    </tr>\n");
  echo("                    <tr>\n");
  /* 24 - Período de busca: */
  echo("                      <td><b>".RetornaFraseDaLista($lista_frases,24)."</b></td>\n");
  /* 25 - Início: */
  echo("                      <td width=\"23%\" class=\"alLeft\" style=\"border-right:2pt solid #DCDCDC;\">\n");
  echo("                        <ul>\n");
  echo("                          <li>\n");
  echo("                            <div>\n");
  echo("                              <div style=\"width:50px; padding-top:5px; float:left\">".RetornaFraseDaLista($lista_frases,25)."</div>\n");
  /* a data de inicio da pesquisa há 15 dias contando de hoje */
  echo("                                <input class=\"input\" type=text size=10 maxlength=10 id=\"data_ini\" name=\"data_ini\" value=\"".UnixTime2Data(time()-(15*24*3600))."\" />\n");
  echo("                                <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_ini'),'dd/mm/yyyy',this);\" />\n");
  echo("                            </div>\n");
  echo("                          </li>\n");
  /* 26 - T�mino: */
  echo("                          <li>\n");
  echo("                            <div>\n");
  echo("                              <div style=\"width:50px; padding-top:5px; float:left\">".RetornaFraseDaLista($lista_frases,26)."</div>\n");
  // a busca vai até hoje 
  echo("                                <input class=\"input\" type=text size=10 maxlength=10 id=\"data_fim\" name=\"data_fim\" value=\"".UnixTime2Data(time())."\" />\n");
  echo("                                <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('data_fim'),'dd/mm/yyyy',this);\" />\n");
  echo("                            </div>\n");
  echo("                          </li>\n");
  echo("                        </ul>\n");
  echo("                      </td>\n");
  
  
  /* 27 - Exibir: */
  echo("                      <td><b>".RetornaFraseDaLista($lista_frases,27)."</b></td>\n");
  /* 25 - Início: */
  echo("                      <td class=\"alLeft\" style=\"border-right:2pt solid #DCDCDC;\">\n");
  echo("                        <ul>\n");

  Desconectar($sock);
  $sock = Conectar($cod_curso);
  if(ExistemGrupos($sock))
    // 14 - Grupos
    echo("                          <li><input type=checkbox name=check_grupos />".RetornaFraseDaLista($lista_frases, 14)."</li>\n");
  else
    // 69 - (N� h�grupos)
    echo("                          <li><input type=checkbox name=check_grupos disabled />".RetornaFraseDaLista($lista_frases, 14)." ".RetornaFraseDaLista($lista_frases, 69)."</li>\n");

  Desconectar($sock);
  $sock = Conectar("");
  if(ExistemAlunos($sock, $cod_curso))
    //61 - Alunos
    echo("                          <li><input type=checkbox name=check_alunos checked />".RetornaFraseDaLista($lista_frases, 61)."</li>\n");
  else
  // 70 - (Não há alunos)
    echo("                          <li><input type=checkbox name=check_alunos disabled />".RetornaFraseDaLista($lista_frases, 61)." ".RetornaFraseDaLista($lista_frases, 70)."</li>\n");
  // 62 - Formadores
  echo("                            <li><input type=checkbox name=check_formadores checked />".RetornaFraseDaLista($lista_frases, 62)."</li>\n");

  // 55 - Convidados
  if ($ha_convidados)
    echo("                          <li><input type=checkbox name=check_convidados />".RetornaFraseDaLista($lista_frases, 55)."</li>\n");
  else
  // 63 - (Não há convidados)
    echo("                          <li><input type=checkbox name=check_convidados disabled />".RetornaFraseDaLista($lista_frases, 55)." ".RetornaFraseDaLista($lista_frases, 63)."</li>\n");

  if ($ha_visitantes)
  // 57 - Visitantes
    echo("                          <li><input type=checkbox name=check_visitantes />".RetornaFraseDaLista($lista_frases, 57)."</li>\n");
  else
  // 64 - (N� h�visitantes)
    echo("                          <li><input type=checkbox name=check_visitantes disabled />".RetornaFraseDaLista($lista_frases, 57)." ".RetornaFraseDaLista($lista_frases, 64)."</li>\n");
  
  echo("                        </ul>\n");
  echo("                      </td>\n");
  
  /* 16 - Ferramenta: */
  echo("                      <td><b>".RetornaFraseDaLista($lista_frases,16)."</b></td>\n");
  /* 25 - Início: */
  echo("                      <td>\n");
  echo("                        <select name=cod_ferramenta size=10 style=\"width:300\">\n");
  /* 29 - Entrada no ambiente */
  echo("                          <option selected>".RetornaFraseDaLista($lista_frases,29)."\n");
  foreach ($ordem_ferramentas as $cod=>$linha)
  {
    if (($cod_ferramenta=$linha['cod_ferramenta']) > 0)
      echo("                      <option value=".$cod_ferramenta.">".RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_nome'])."\n");
  }
  echo("                        </select>\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  echo("                    <tr>\n");
  echo("                      <td colspan=6>\n");
  echo("                        <ul class=\"btAuxTabs\">\n");
  echo("                          <li>\n");
  /* 15 - Exibir relatório */
  echo("                            <span onClick=\"EmulaSubmissaoFrequencia();\">".RetornaFraseDaLista($lista_frases,15)."</span>\n");
  echo("                          </li>\n");
  echo("                        </ul>\n");
  echo("                        <br /><br />\n");
  echo("                      </td>\n");
  echo("                    </tr>\n");
  
  echo("                  </table>\n");
  echo("                </form>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  

  
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include ("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");
  Desconectar($sock);
?>
