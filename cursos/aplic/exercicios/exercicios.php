<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/exercicios/exercicios.php

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
  ARQUIVO : cursos/aplic/exercicios/exercicios.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("exercicios.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das funcoes em PHP que voce quer chamar atraves do xajax
  $objAjax->register(XAJAX_FUNCTION,"AlteraStatusExercicioDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamentoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CancelaAplicacaoExercicioDinamic");
  $objAjax->register(XAJAX_FUNCTION,"VerificaNotas");
  $objAjax->register(XAJAX_FUNCTION,"AplicaExercicioDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ExcluirExercicioDinamic");
  // Registra fun��es para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 23;
  $cod_ferramenta_ajuda = $cod_ferramenta;

  $visualizar = $_GET['visualizar'];
  if(!isset($visualizar))
  $visualizar = "E";

  if($visualizar == "L") {
    $defColspan = "colspan=\"4\"";
  } else {
    $defColspan="";
  }

  if($visualizar == "L"){
    $cod_pagina_ajuda=5;
  }
  else{
    $cod_pagina_ajuda=1;
  }

  $data_atual = time();

  $cod_curso = $_GET['cod_curso'];
  $cod_usuario_global=VerificaAutenticacao($cod_curso);

  // Se h� exerc�cios com apenas quest�es objetivas
  // e data limite de entrega j� passou, entrega gabaritos.
  EntregaCorrecaoExsObjetivosEMultiplaEscolha($cod_curso);

  $sock = Conectar("");
  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);

  /* Se for aluno, manda para a pagina de exercicios individuais dele */
  if (EAluno($sock,$cod_curso,$cod_usuario))
    header("Location: ver_exercicios.php?cod_curso=".$cod_curso."&visualizar=I&cod=".$cod_usuario);
  /* Se for colaborador, manda para a pagina que lista entregas de exercicios */
  if (EColaborador($sock,$cod_curso,$cod_usuario))
    header("Location: exercicio.php?cod_curso=".$cod_curso."&agrupar=A");

  include("../topo_tela.php");

  if($visualizar == "E")
  {
    $lista_exercicios = RetornaExercicios($sock);
  }
  else if($visualizar == "L")
  {
    $lista_exercicios = RetornaExerciciosLixeira($sock);
  }

  if(is_array($lista_exercicios))
    $totalExercicios = count($lista_exercicios);
  else
    $totalExercicios = 0;

  /* Nmero de questoes exibidas por p�ina.             */
  if (!isset($exerciciosPorPag)) $exerciciosPorPag = 10;

  /* Se o nmero total de questoes for superior que o nmero de questoes por  */
  /* p�ina ent� calcula o total de p�inas. Do contr�io, define o nmero de     */
  /* p�inas para 1.                                                           */

  /* Calcula o nmero de p�inas geradas.    */
  if($totalExercicios > $exerciciosPorPag)
    $totalPag = ceil($totalExercicios / $exerciciosPorPag);
  else
    $totalPag = 1;

  /* Se a p�ina atual n� estiver setada ent�, por padr�, atribui-lhe o valor 1. */
  /* Se estiver setada, verifica se a p�ina �maior que o total de p�inas, se for */
  /* atribui o valor de $total_pag �$pagAtual.                                    */
  if ((!isset($pagAtual))or($pagAtual=='')or($pagAtual==0))
    $pagAtual =  1;
  else $pagAtual = min($pagAtual, $totalPag);

  GeraJSComparacaoDatas();
  GeraJSVerificacaoData();

  /*********************************************************/
  /* in�io - JavaScript */
  echo("  <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
  echo("  <script  type=\"text/javascript\" language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
  echo("  <script  type=\"text/javascript\" src=\"jscriptlib.js\"> </script>\n");
  echo("  <script  type=\"text/javascript\" language=\"javascript\">\n\n");

  echo("    var js_cod_item;\n");
  echo("    var js_comp = new Array();\n");
  echo("    var pagAtual = ".$pagAtual.";\n");
  echo("    var totalExercicios = ".$totalExercicios.";\n");
  echo("    var exerciciosPorPag = ".$exerciciosPorPag.";\n");
  echo("    var totalPag = ".$totalPag.";\n");
  echo("    var topico = 'T';\n");
//  echo("    var tp_questao = 'T';\n");
//  echo("    var dificuldade = 'T';\n");
  echo("    var window_handle;\n");
  echo("    var t;\n");
  echo("    this.name = 'principal';\n\n");
  echo("    var cod_comp;");
  echo("    var totalExercicios = ".count($lista_exercicios).";\n\n");

  echo("    if (document.addEventListener) {\n");/* Caso do FireFox */
  echo("      document.addEventListener('mousemove', TrataMouse, false);\n");
  echo("    } else if (document.attachEvent){\n");/* Caso do IE */
  echo("      document.attachEvent('onmousemove', TrataMouse);\n");
  echo("    }\n");

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
  echo("        return(document.documentElement.scrollTop);\n");
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
  if($visualizar == "E")
  {
    echo("      lay_novo_exercicio = getLayer('layer_novo_exercicio');\n");
    echo("      cod_comp = getLayer(\"comp\");\n");
    echo("      lay_aplicar = getLayer(\"layer_aplicar\");\n");
  }
  echo("      startList();\n");
  if (count($lista_exercicios) > 0){
    echo("      ExibeMsgPagina(".$pagAtual.");\n");
  }
  echo("    }\n\n");

  if (count($lista_exercicios) > 0){
    echo("      function ExibeMsgPagina(pagina){\n");
    echo("        var i = 0;\n");
    echo("        if (pagina < 1) return;\n");
    echo("        document.getElementById(\"checkMenu\").checked=false;\n");
    echo("        tabela = document.getElementById('tabelaInterna');\n");
    echo("        if(!tabela) return;\n");
    echo("        inicio = 1;\n");
    echo("        final = ((totalPag)*".$exerciciosPorPag.")+1;\n");
    echo("        for (i=inicio; i < final; i++){\n");
    echo("          if (!tabela.rows[i]) break;\n");
    echo("          tabela.rows[i].style.display=\"none\";\n");
    echo("        }\n");

    echo("        var browser=navigator.appName;\n\n");
    echo("        inicio = ((pagina-1)*".$exerciciosPorPag.")+1;\n");
    echo("        final = ((pagina)*".$exerciciosPorPag.");\n");
    echo("        for (i=inicio; i < final+1; i++){\n");
    echo("          if (!tabela.rows[i+1] || i > totalExercicios){break;}\n");
    echo("          if (browser==\"Microsoft Internet Explorer\")\n");
    echo("            tabela.rows[i].style.display=\"block\";\n");
    echo("          else\n");
    echo("            tabela.rows[i].style.display=\"table-row\";\n");
    echo("          tabela.rows[i].className = 'altColor'+((i+1)%2);");
    echo("        }\n\n");
    echo("        document.getElementById('primQuestaoIndex').innerHTML=inicio;\n");
    echo("        document.getElementById('ultQuestaoIndex').innerHTML=(i-1);\n\n");

    echo("        if (browser==\"Microsoft Internet Explorer\")\n");
    echo("          tabela.rows[tabela.rows.length-1].style.display=\"block\";\n");
    echo("        else\n");
    echo("          tabela.rows[tabela.rows.length-1].style.display=\"table-row\";\n");

    echo("        pagAtual=pagina;\n\n");

    echo("        if (pagAtual != 1){\n");
    echo("          document.getElementById('paginacao_first').onclick = function(){ ExibeMsgPagina(1); };\n");
    echo("          document.getElementById('paginacao_first').className = \"link\";\n");
    echo("          document.getElementById('paginacao_back').onclick = function(){ ExibeMsgPagina(pagAtual-1); };\n");
    echo("          document.getElementById('paginacao_back').className = \"link\";\n");
    echo("        }else{\n");
    echo("          document.getElementById('paginacao_first').onclick = function(){};\n");
    echo("          document.getElementById('paginacao_first').className = \"\";\n");
    echo("          document.getElementById('paginacao_back').onclick = function(){};\n");
    echo("          document.getElementById('paginacao_back').className = \"\";\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_first').innerHTML = \"&lt;&lt;\";\n");
    echo("        document.getElementById('paginacao_back').innerHTML = \"&lt;\";\n");
    echo("        inicio = pagAtual-2;\n");
    echo("        if (inicio < 1) inicio=1;\n");
    echo("        fim = pagAtual+2;\n");
    echo("        if (fim > totalPag) fim=totalPag;\n");
    echo("        var controle=1;\n");
    echo("        var vetor= new Array();\n");
    echo("        for (j=inicio; j <= fim; j++){\n");
    echo("          // A página atual Não é exibida com link.\n");
    echo("          if (j == pagAtual){\n");
    echo("             document.getElementById('paginacao_'+controle).innerHTML='<b>['+j+']<\/b>';\n");
    echo("             document.getElementById('paginacao_'+controle).className='';\n");
    echo("             vetor[controle] = -1;\n");
    echo("          }else{\n");
    echo("             document.getElementById('paginacao_'+controle).innerHTML=j;\n");
    echo("             document.getElementById('paginacao_'+controle).className='link';\n");
    echo("             vetor[controle]=j;\n");
    echo("          }\n");
    echo("          controle++;\n");
    echo("        }\n");
    echo("        while (controle<=5){\n");
    echo("          document.getElementById('paginacao_'+controle).innerHTML='';\n");
    echo("          document.getElementById('paginacao_'+controle).className='';\n");
    echo("          document.getElementById('paginacao_'+controle).onclick= function() { };\n");
    echo("          controle++;\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_1').onclick=function(){ ExibeMsgPagina(vetor[1]); };\n");
    echo("        document.getElementById('paginacao_2').onclick=function(){ ExibeMsgPagina(vetor[2]); };\n");
    echo("        document.getElementById('paginacao_3').onclick=function(){ ExibeMsgPagina(vetor[3]); };\n");
    echo("        document.getElementById('paginacao_4').onclick=function(){ ExibeMsgPagina(vetor[4]); };\n");
    echo("        document.getElementById('paginacao_5').onclick=function(){ ExibeMsgPagina(vetor[5]); };\n\n");

    echo("        /* Se a página atual Não for a última página então cria um   \n");
    echo("           link para a próxima página */\n");
    echo("        if (pagAtual != totalPag){\n");
    echo("          document.getElementById('paginacao_fwd').onclick = function(){ ExibeMsgPagina(pagAtual+1); };\n");
    echo("          document.getElementById('paginacao_fwd').className = \"link\";\n");
    echo("          document.getElementById('paginacao_last').onclick = function(){ ExibeMsgPagina(totalPag); };\n");
    echo("          document.getElementById('paginacao_last').className = \"link\";\n");
    echo("        }\n");
    echo("        else{\n");
    echo("          document.getElementById('paginacao_fwd').onclick = function(){};\n");
    echo("          document.getElementById('paginacao_fwd').className = \"\";\n");
    echo("          document.getElementById('paginacao_last').onclick = function(){};\n");
    echo("          document.getElementById('paginacao_last').className = \"\";\n");
    echo("        }\n");
    echo("        document.getElementById('paginacao_fwd').innerHTML = \"&gt;\";\n");
    echo("        document.getElementById('paginacao_last').innerHTML = \"&gt;&gt;\";\n");
    //echo("        ControlaSelecao();\n");
    echo("      }\n\n");
  }

  if($visualizar == "E")
  {
    echo("    function VerificaNovoTitulo(textbox, aspas) {\n");
    echo("      texto=textbox.value;\n");
    echo("      if (texto==''){\n");
    echo("        // se nome for vazio, nao pode\n");
                  /* Frase #34 - O titulo nao pode ser vazio. */
    echo("        alert(\"".RetornaFraseDaLista($lista_frases,34)."\");\n");
    echo("        textbox.focus();\n");
    echo("        return false;\n");
    echo("      }\n");
    echo("      // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
    echo("      else if ((texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0)&&(!aspas)) {\n");
                   /* Frase #120 - O titulo deve conter apenas numeros, letras e espacos */
    echo("         alert(\"".ConverteAspas2BarraAspas(ConverteHtml2Aspas(RetornaFraseDaLista($lista_frases,120)))."\");\n");
    echo("        textbox.value='';\n");
    echo("        textbox.focus();\n");
    echo("        return false;\n");
    echo("      }\n");
    echo("      return true;\n");
    echo("    }\n\n");

    echo("    function  ExibirAgendamento(value)\n");
    echo("    {\n");
    echo("       if(value == \"I\")\n");
    echo("         document.getElementById(\"div_disp\").style.display = \"none\";\n");
    echo("       if(value == \"A\")\n");
    echo("         document.getElementById(\"div_disp\").style.display = \"\";\n");
    echo("    }\n\n");


    echo("    function EscondeLayers()\n");
    echo("    {\n");
    echo("      hideLayer(lay_novo_exercicio);\n");
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

    echo("    function NovoExercicio()\n");
    echo("    {\n");
    echo("      MostraLayer(lay_novo_exercicio, 0);\n");
    echo("      document.form_novo_exercicio.novo_titulo.value = '';\n");
    echo("      document.getElementById(\"nome\").focus();\n");
    echo("    }\n");

    echo("    function VerificaEnv(){\n");
    echo("      j=0;\n");
    echo("      check = document.getElementsByName('chkExercicio');\n");
    echo("      for(i=0;i<check.length;i++){\n");
    echo("        if(check[i].checked){\n");
    echo("          j++;\n");
    echo("          getCodExercicio=check[i].id.split('_');\n");
    echo("        }\n");
    echo("      }\n");
    echo("      if(j == 1){\n");
    echo("        MostraLayer(lay_aplicar,0);\n");
    echo("      }else{\n");
    echo("        alert('selecione apenas um exercicio');\n");
    echo("      }");
    echo("    }\n");

    echo("    function EnvAplica(){\n");
    echo("      j=0;\n");
    echo("      check = document.getElementsByName('chkExercicio');\n");
    echo("      for(i=0;i<check.length;i++){\n");
    echo("        if(check[i].checked){\n");
    echo("          getCodExercicio=check[i].id.split('_');\n");
    echo("          xajax_VerificaNotas(getCodExercicio[1],".$cod_curso.");\n");
    echo("        }\n");
    echo("      }\n");
    echo("    }\n");

    echo("    function ExercicioAplicado(avaliacao,cod_avaliacao)\n");
    echo("    {\n");
    echo("      window.location='exercicios.php?cod_curso=".$cod_curso."&visualizar=E';\n");
    echo("    }\n\n");

    echo("    function verifica_notas(flag,codex)\n");
    echo("    {\n");
    echo("      if(flag == '0'){\n");
    //188 - Existem quest�es com valores iguais a 0, Deseja continuar?
    echo("        if(confirm('".RetornaFraseDaLista($lista_frases, 188)."'))\n");
    echo("          AplicarExercicio(codex);\n");
    echo("        else\n");
    echo("          EscondeLayer(lay_aplicar);\n");
    echo("      } else if (flag == '1'){\n");
    echo("        AplicarExercicio(codex);\n");
    echo("      } else if (flag == '2'){\n ");
    /* Frase #193 - Nao e possivel aplicar um exercicio vazio. Adicione ao menos uma questao. */
    echo("        alert('".RetornaFraseDaLista($lista_frases, 193)."');");
    echo("      }\n");
    echo("    }\n");

    echo("    function AplicarExercicio(codex)\n");
    echo("    {\n");
    echo("        if(document.getElementById(\"disponibilizacaoa\").checked)\n");
    echo("        {\n");
    echo("          if(verifica_intervalos()){\n");
    echo("            dt_disp = document.getElementById(\"dt_disponibilizacao\").value;\n");
    echo("            hr_disp = document.getElementById(\"hora_disponibilizacao\").value;\n");
    echo("            min_disp = document.getElementById(\"minuto_disponibilizacao\").value;\n");
    echo("            horario_disp = hr_disp+':'+min_disp+':00';\n");
    echo("          }\n");
    echo("          else{\n");
    echo("            return 0;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    echo("            dt_disp = \"".UnixTime2Data($data_atual)."\";\n");
    echo("            horario_disp = \"".UnixTime2Hora($data_atual)."\";\n");
    echo("        }\n");
    echo("        limite_entrega = document.getElementById(\"limite_entrega\");\n");
    echo("        dt_disponibilizacao = document.getElementById(\"dt_disponibilizacao\");\n");
    echo("        dt_entrega = document.getElementById(\"limite_entrega\").value;\n");
    echo("        hr_entrega = document.getElementById(\"hora_limite_entrega\").value;\n");
    echo("        min_entrega = document.getElementById(\"minuto_limite_entrega\").value;\n");
    echo("        horario_entrega = hr_entrega+':'+min_entrega+':00';\n");
    echo("        tp_aplicacao = (document.getElementById(\"tp_aplicacaoi\").checked) ? 'I' : 'G';\n");
    echo("        disp_gabarito = (document.getElementById(\"disp_gabaritos\").checked) ? 'S' : 'N';\n");
    echo("        avaliacao = (document.getElementById(\"avaliacaos\").checked) ? 'S' : 'N';\n");
    echo("        if(document.getElementById(\"disponibilizacaoi\").checked)\n");
    echo("        {\n");
    echo("          if (ComparaDataHora(dt_disponibilizacao,RetornaHorarioDisponibilizacao(),limite_entrega,RetornaHorarioEntrega()) > 0 )\n");
    echo("          {\n");
    /* Frase #48 - O limite de entrega deve ser posterior a disponibilizacao do exercicio. */
    echo("            alert('".RetornaFraseDaLista($lista_frases, 48)."');\n");
    echo("            return(false);\n");
    echo("          }\n");
    echo("        }\n");
    echo("      xajax_AplicaExercicioDinamic(".$cod_curso.",codex,".$cod_usuario.",dt_disp,horario_disp,dt_entrega,horario_entrega,tp_aplicacao,disp_gabarito,avaliacao,0);");
    echo("    }\n\n");

    echo("    function ExercicioAplicado(avaliacao,cod_avaliacao)\n");
    echo("    {\n");
    echo("      if(avaliacao == 'N')\n");
    echo("        window.location='exercicios.php?cod_curso=".$cod_curso."&visualizar=E';\n");
    echo("      else\n");
    echo("        window.location='../avaliacoes/ver.php?cod_curso=".$cod_curso."&cod_avaliacao='+cod_avaliacao+'&origem=exercicios&operacao=null&acao=aplicar&atualizacao=true';\n");
    echo("    }\n\n");

    echo("    function RetornaDataAtual()\n");
    echo("    {\n");
    echo("       var input;\n");
    echo("       input = document.createElement(\"input\");\n");
    echo("       input.setAttribute(\"value\",\"".UnixTime2Data($data_atual)."\")\n");
    echo("       return input;\n");
    echo("    }\n\n");

    echo("    function RetornaHoraAtual()\n");
    echo("    {\n");
    echo("       var input;\n");
    echo("       input = document.createElement(\"input\");\n");
    echo("       input.setAttribute(\"value\",\"".UnixTime2Hora($data_atual)."\")\n");
    echo("       return input;\n");
    echo("    }\n\n");

    echo("    function RetornaHorarioEntrega()\n");
    echo("    {\n");
    echo("      hr_entrega = document.getElementById(\"hora_limite_entrega\").value;\n");
    echo("      min_entrega = document.getElementById(\"minuto_limite_entrega\").value;\n");
    echo("      horario_entrega = hr_entrega+':'+min_entrega;\n");
    echo("      var docHorario_entrega = document.createElement('input'); \n");
    echo("      docHorario_entrega.setAttribute('value',horario_entrega);\n");
    echo("      return(docHorario_entrega);\n");
    echo("    }\n");

    echo("    function RetornaHorarioDisponibilizacao()\n");
    echo("    {\n");
    echo("      hr_disp = document.getElementById(\"hora_disponibilizacao\").value;\n");
    echo("      min_disp = document.getElementById(\"minuto_disponibilizacao\").value;\n");
    echo("      horario_disp = hr_disp+':'+min_disp;\n");
    echo("      var docHorario_disp = document.createElement('input'); \n");
    echo("      docHorario_disp.setAttribute('value',horario_disp);\n");
    echo("      return(docHorario_disp);\n");
    echo("    }\n");


    echo("    function verifica_intervalos()\n");
    echo("    {\n");
    echo("      var dt_disponibilizacao,limite_entrega,hora_disponibilizacao,hora_limite_entrega,data_atual,hora_atual;\n");
    echo("      dt_disponibilizacao = document.getElementById(\"dt_disponibilizacao\");\n");
    echo("      limite_entrega = document.getElementById(\"limite_entrega\");\n");
    echo("      data_atual = RetornaDataAtual();\n");
    echo("      hora_limite_entrega = RetornaHorarioEntrega();\n");
    echo("      hora_disponibilizacao = RetornaHorarioDisponibilizacao();\n");
    echo("      hora_atual = RetornaHoraAtual();\n");
    echo("      if (!DataValidaAux(dt_disponibilizacao) || !DataValidaAux(limite_entrega))\n");
    echo("        return (false);\n");
    echo("      if (!hora_valida(hora_disponibilizacao))\n");
    echo("      {\n");
    /* Frase #45 - Hora de disponibilizacao invalida. Por favor volte e corrija. */
    echo("        alert('".RetornaFraseDaLista($lista_frases, 45)."');\n");
    echo("        return(false);\n");
    echo("      }\n");
    echo("      if (!hora_valida(hora_limite_entrega))\n");
    echo("      {\n");
   /* Frase #46 - Hora de limite de entrega invalida. Por favor volte e corrija. */
    echo("        alert('".RetornaFraseDaLista($lista_frases, 46)."');\n");
    echo("        return(false);\n");
    echo("      }\n");
    echo("      if (ComparaDataHora(data_atual,hora_atual,dt_disponibilizacao,hora_disponibilizacao) > 0 )\n");
    echo("      {\n");
    /* Frase #47 - A disponibilizacao do exercicio deve ser posterior a data atual. */
    echo("        alert('".RetornaFraseDaLista($lista_frases, 47)."');\n");
    echo("        return(false);\n");
    echo("      }\n");
    echo("      if (ComparaDataHora(dt_disponibilizacao,hora_disponibilizacao,limite_entrega,hora_limite_entrega) > 0 )\n");
    echo("      {\n");
    /* Frase #48 - O limite de entrega deve ser posterior a disponibilizacao do exercicio. */
    echo("        alert('".RetornaFraseDaLista($lista_frases, 48)."');\n");
    echo("        return(false);\n");
    echo("      }\n");
    echo("      return(true);\n");
    echo("    }\n");


    echo("    function AtualizaCampos(id,data,dt_disp,dt_entrega,situacao)\n");
    echo("    {\n");
    echo("      document.getElementById('data_'+id).innerHTML = data;\n");
    echo("      document.getElementById('disp_'+id).innerHTML = dt_disp;\n");
    echo("      document.getElementById('entrega_'+id).innerHTML = dt_entrega;\n");
    echo("      document.getElementById('situacao_'+id+'_A').innerHTML = situacao;\n");
    echo("      document.getElementById('situacao_'+id+'_A').id = 'situacao_'+id+'_C';\n");
    echo("    }\n");

    echo("    function CancelarAplicacao()\n");
    echo("    {\n");
    /* Frase #199 - Voce realmente deseja cancelar aplica��o dos selecionados? */
    echo("      if(confirm(\"".RetornaFraseDaLista($lista_frases, 199)."\"))\n");
    echo("      {\n");
    echo("        var i;\n");
    echo("        var getNumber;\n");
    echo("        var cod_itens=document.getElementsByName('chkExercicio');\n");
    echo("        var Cabecalho = document.getElementById('checkMenu');\n");
    echo("        for (i=0; i < cod_itens.length; i++){\n");
    echo("            if (cod_itens[i].checked){\n");
    echo("              getNumber = cod_itens[i].id.split(\"_\");\n");
    echo("              xajax_CancelaAplicacaoExercicioDinamic(".$cod_curso.",".$cod_usuario.",getNumber[1]);\n");
    echo("              AtualizaCampos(getNumber[1],'".UnixTime2Data(time())."','-','-','Em criacao');\n");
    echo("            }\n");
    echo("        }\n");
    /* Frase #121 - Aplicacao cancelada com sucesso. */
    echo("        mostraFeedback('".RetornaFraseDaLista($lista_frases, 121)."',true);\n");
    echo("      }\n");
    echo("    }\n");

    echo("    function VerificaCheck(){\n");
    echo("      var i;\n");
    echo("      var j = 0;\n");
    echo("      var getNumber;");
    echo("      var cod_itens=document.getElementsByName('chkExercicio');\n");
    echo("      var Cabecalho = document.getElementById('checkMenu');\n");
    echo("      var flag = 1;\n");
    echo("      for (i=0; i < cod_itens.length; i++){\n");
    echo("        if (cod_itens[i].checked){\n");
    echo("          j++;\n");
    echo("          getNumber = cod_itens[i].id.split(\"_\");\n");
    echo("          if(document.getElementById('situacao_'+getNumber[1]+'_C')){\n");
    echo("            flag = 0;\n");
    echo("          }\n");
    echo("        }\n");
    echo("      }\n");
    echo("      if(j==1){\n");
    echo("        document.getElementById('mAplicar_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mAplicar_Selec').onclick=function(){ MostraLayer(lay_aplicar,0); };\n");
    echo("      }else{\n");
    echo("        document.getElementById('mAplicar_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mAplicar_Selec').onclick=function(){  };\n");
    echo("      }\n");
    echo("      if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
    echo("      else Cabecalho.checked=false;\n");
    echo("      if(j > 0){\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){ TratarSelecionados('L'); };\n");
    echo("        document.getElementById('mAplicar_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mAplicar_Selec').onclick=function(){ MostraLayer(lay_aplicar,0); };\n");

    echo("        if(flag)\n");
    echo("        {\n");
    echo("          document.getElementById('mCancelarAplic_Selec').className=\"menuUp02\";\n");
    echo("          document.getElementById('mCancelarAplic_Selec').onclick=function(){ CancelarAplicacao(); };\n");
    echo("          document.getElementById('mAplicar_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mAplicar_Selec').onclick=function(){ };\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    echo("          document.getElementById('mCancelarAplic_Selec').className=\"menuUp\";\n");
    echo("          document.getElementById('mCancelarAplic_Selec').onclick=function(){ };\n");
    echo("        }\n");
    echo("      }else{\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("        document.getElementById('mAplicar_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mAplicar_Selec').onclick=function(){  };\n");
    echo("        document.getElementById('mCancelarAplic_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mCancelarAplic_Selec').onclick=function(){ };\n");
    echo("      }\n");
    echo("    }\n\n");
  }
  else if($visualizar == "L")
  {
    echo("    function VerificaCheck(){\n");
    echo("      var i;\n");
    echo("      var j=0;\n");
    echo("      var cod_itens=document.getElementsByName('chkExercicio');\n");
    echo("      var Cabecalho = document.getElementById('checkMenu');\n");
    echo("      for (i=0; i < cod_itens.length; i++){\n");
    echo("        if (cod_itens[i].checked){\n");
    echo("          var item = cod_itens[i].id.split('_');\n");
    echo("          j++;\n");
    echo("        }\n");
    echo("      }\n");
    echo("      if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
    echo("      else Cabecalho.checked=false;\n");
    echo("      if(j > 0){\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){ TratarSelecionados('X'); };\n");
    echo("        document.getElementById('mRecup_Selec').className=\"menuUp02\";\n");
    echo("        document.getElementById('mRecup_Selec').onclick=function(){ TratarSelecionados('V'); };\n");
    echo("      }else{\n");
    echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
    echo("        document.getElementById('mRecup_Selec').className=\"menuUp\";\n");
    echo("        document.getElementById('mRecup_Selec').onclick=function(){  };\n");
    echo("      }\n");
    echo("    }\n\n");
  }


  echo("      function MarcaOuDesmarcaTodos(pagAtual){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var inicio;\n");
  echo("        var final;\n");
  echo("        var elementos = document.getElementsByName('chkExercicio')\n");
  echo("        inicio = ((pagAtual-1)*".$exerciciosPorPag.");\n");
  echo("        final = ((pagAtual)*".$exerciciosPorPag.");\n");
  echo("        controle = (pagAtual-1)*".$exerciciosPorPag.";\n");
  echo("        controle = elementos.length - controle;\n");
  echo("        if(controle < final) {final = inicio + controle;}\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        for(i = inicio; i < final; i++){\n");
  echo("          e = document.getElementsByName('chkExercicio')[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("      VerificaCheck();\n");
  echo("      }\n\n");


  echo("    function DeletarLinhas(deleteArray,j){\n");
  echo("      var i,trExercicio;\n");
  echo("      for(i=0;i<j;i++)\n");
  echo("      {\n");
  echo("        trExercicio = document.getElementById('trExercicio_'+deleteArray[i]);\n");
  echo("        trExercicio.parentNode.removeChild(trExercicio);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function IntercalaCorLinha(){\n");
  echo("      var checks,i,trExercicio;\n");
  echo("      checks = document.getElementsByName('chkExercicio');\n");
  echo("      corLinha = 0;\n");
  echo("      for (i=0; i<checks.length; i++){\n");
  echo("        getNumber=checks[i].id.split('_');\n");
  echo("        trExercicio = document.getElementById('trExercicio_'+getNumber[1]);\n");
  echo("        trExercicio.className = 'altColor'+(i%2);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function Confirma(op){\n");
  echo("        if(op == 'X')\n");
                  /* Frase #122 - Tem certeza que deseja excluir definitivamente os exercicios selecionadas? */
  echo("          return confirm('".RetornaFraseDaLista($lista_frases, 122)."');\n");
  echo("        else if(op == 'V')\n");
                  /* Frase #123 - Tem certeza que deseja recuperar os exercicios selecionadas? */
  echo("          return confirm('".RetornaFraseDaLista($lista_frases, 123)."');\n");
  echo("        else if(op == 'L')\n");
                  /* Frase #124 - Tem certeza que deseja enviar para lixeira os exercicios selecionadas? */
  echo("          return confirm('".RetornaFraseDaLista($lista_frases, 124)."');\n");
  echo("    }\n\n");

  echo("    function InsereLinhaVazia(){\n");
  echo("      var table,tr,td;");
  echo("      table = document.getElementById(\"tabelaInterna\");\n");
  echo("      tr = document.createElement(\"tr\");\n");
  echo("      td = document.createElement(\"td\");\n");
  echo("      td.colSpan = \"7\";\n");
  /* Frase #118 - Nao ha nenhum exercicio */
  echo("      td.appendChild(document.createTextNode('".RetornaFraseDaLista($lista_frases, 118)."'));\n");
  echo("      tr.appendChild(td);\n");
  echo("      table.appendChild(tr);\n");
  echo("    }\n\n");

  echo("      function AtualizaComp(js_tipo_comp)\n");
  echo("      {\n");
  echo("        if ((isNav) && (!isMinNS6)) {\n");
  echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
  echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_N'));\n");
  echo("        } else {\n");
  echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
  echo("            document.form_comp.cod_item.value=js_cod_item;\n");
  echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_N'));\n");
  echo("        }\n");
  echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
  echo("        if (js_tipo_comp=='F') {\n");
  echo("          tipo_comp[0].innerHTML=imagem;\n");
  echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
  echo("        } else{\n");
  echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
  echo("          tipo_comp[1].innerHTML=imagem;\n");
  echo("        }\n");
  echo("      }\n\n");

  echo("    function RetornaTexto(op){\n");
  echo("        if(op == 'X')\n");
  /* Frase #125 - Exercicio(s) excluido(s) da lixeira. */
  echo("          return '".RetornaFraseDaLista($lista_frases, 125)."';\n");
  echo("        else if(op == 'V')\n");
  /* Frase #126 - Exercicio(s) recuperado(s). */
  echo("          return '".RetornaFraseDaLista($lista_frases, 126)."';\n");
  echo("        else if(op == 'L')\n");
  /* Frase #127 - Exercicio(s) enviado(s) para lixeira. */
  echo("          return '".RetornaFraseDaLista($lista_frases, 127)."';\n");
  echo("    }\n\n");

  echo("    function AtualizaEstadoPaginacao(pagina){\n");
  echo("      var trVazia;");
  echo("      totalPag = Math.ceil(totalExercicios/".$exerciciosPorPag.");\n");
  echo("      document.getElementById(\"totalExercicios\").innerHTML = totalExercicios;\n");
  echo("      ExibeMsgPagina(pagina);\n");
  echo("      trVazia = document.getElementById(\"trVazia\");");
  echo("      if(totalExercicios == 0)\n");
  echo("      {\n");
  echo("        if(!trVazia)");
  echo("          InsereLinhaVazia();\n");
  echo("        document.getElementById(\"trIndicaEstadoPag\").style.display = \"none\";\n");
  echo("        document.getElementById(\"trIndicePag\").style.display = \"none\";\n");
  echo("      }\n");
  echo("      else\n");
  echo("      {\n");
  echo("        if(trVazia)");
  echo("          trVazia.parentNode.removeChild(trVazia);\n");
  echo("        document.getElementById(\"trIndicaEstadoPag\").style.display = \"\";\n");
  echo("        document.getElementById(\"trIndicePag\").style.display = \"\";\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("    function VerificaPaginacao(){\n");
  echo("      if(Math.ceil(totalExercicios/exerciciosPorPag) < pagAtual)\n");
  echo("        AtualizaEstadoPaginacao(pagAtual-1)\n");
  echo("      else\n");
  echo("        AtualizaEstadoPaginacao(pagAtual)\n");
  echo("}\n");


  echo("    function TratarSelecionados(op){\n");
  echo("      var checks,deleteArray,j;\n");
  echo("      checks = document.getElementsByName('chkExercicio');\n");
  echo("      deletaArray = new Array();\n");
  echo("      j=0;\n");
  echo("      if(Confirma(op)){\n");
  //echo("      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);\n");
  echo("        for (i=0; i<checks.length; i++){\n");
  echo("        if(checks[i].checked){\n");
  echo("          getNumber=checks[i].id.split(\"_\");\n");
  echo("          xajax_AlteraStatusExercicioDinamic(".$cod_usuario.",".$cod_curso.",getNumber[1],op);\n");
  echo("          if(op == \"L\")\n");
  echo("            xajax_ExcluirExercicioDinamic(".$cod_curso.",".$cod_usuario.",getNumber[1]);\n");
  echo("          deletaArray[j++] = getNumber[1];\n");
  echo("          totalExercicios--;");
  echo("          }\n");
  echo("        }\n");
  echo("        DeletarLinhas(deletaArray,j);\n");
  echo("        if(totalExercicios > 0)\n");
  echo("          IntercalaCorLinha();\n");
  echo("        else\n");
  echo("          InsereLinhaVazia();\n");
  echo("        VerificaPaginacao();\n");
  echo("        VerificaCheck();\n");
  echo("        mostraFeedback(RetornaTexto(op),true);\n");
  echo("      }\n");
  echo("    }\n\n");

  echo("\n</script>\n\n");

  $objAjax->printJavascript();

  /* fim - JavaScript */
  /*********************************************************/

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  /* Frase #1 - Exercicios */
  /* Frase #111 - Biblioteca de Exercicios */
  $frase = RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 111);
  if($visualizar == "L")
    /* Frase #128 - Lixeira */
    $frase = $frase." - ".RetornaFraseDaLista($lista_frases, 128);

  echo("          <h4>".$frase."</h4>\n");

  /*Frase #5 - Voltar */
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

  /* Frase #109 - Exercicios Individuais */
  echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=I&agrupar=A'>".RetornaFraseDaLista($lista_frases, 109)."</a></li>\n");

  /* Frase #110 - Exercicios em Grupo */
  echo("                  <li><a href='exercicio.php?cod_curso=".$cod_curso."&visualizar=G&agrupar=G'>".RetornaFraseDaLista($lista_frases, 110)."</a></li>\n");

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");
  if($visualizar == "E")
  {
    /* Frase #129 - Novo exercicio */
    echo("                  <li><span onclick=\"NovoExercicio();\">".RetornaFraseDaLista($lista_frases, 129)."</span></li>\n");
    /* Frase #111 - Biblioteca de Exercicios */
    echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."&visualizar=E'>".RetornaFraseDaLista($lista_frases, 111)."</a></li>\n");
    /* Frase #112 - Biblioteca de Questoes */
    echo("                  <li><a href='questoes.php?cod_curso=".$cod_curso."&visualizar=Q'>".RetornaFraseDaLista($lista_frases, 112)."</a></li>\n");
    /* Frase #128 - Lixeira */
    echo("                  <li><span onclick=\"document.location='exercicios.php?cod_curso=".$cod_curso."&visualizar=L';\">".RetornaFraseDaLista($lista_frases, 128)."</span></li>\n");
  }
  else if($visualizar == "L")
  {
    /* Frase #1 - Exercicios */
    echo("                  <li><a href='exercicios.php?cod_curso=".$cod_curso."&visualizar=E'>".RetornaFraseDaLista($lista_frases, 1)."</a></li>\n");
  }
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  if($totalExercicios > 0){
    // Calcula o índice da primeira mensagem.
    $primQuestaoIndex = (($pagAtual - 1) * $exerciciosPorPag) + 1;
    // Calcula o índice da última mensagem.
    $ultQuestaoIndex = $pagAtual * $exerciciosPorPag;

    // Se o índice da ultima mensagem for maior que o número de mensagens, então copia este 
    // para o índice da última mensagem.
    if ($ultQuestaoIndex > ($totalExercicios))
      $ultQuestaoIndex = ($totalExercicios);
    echo("            <tr class=\"head01\" id=\"trIndicaEstadoPag\">\n");
    echo("              <td colspan=\"6\">\n");
    /* Frase #1 - Exercicios     */
    echo("                ".RetornaFraseDaLista($lista_frases, 1)." ");
    echo("(<span id=\"primQuestaoIndex\"></span>");
    /* Frase #221 - a             */
    echo(" ".RetornaFraseDaLista($lista_frases, 221)."&nbsp;");
    /* Frase #222 - de            */
    echo("<span id=\"ultQuestaoIndex\"></span> ".RetornaFraseDaLista($lista_frases, 222)." ");
    echo("<span id=\"totalExercicios\">".($totalExercicios)."</span>)\n");
    echo("              </td>\n");
    echo("            </tr>\n");
  }

  echo("            <tr>\n");

  echo("              <td valign=\"top\">\n");
  echo("                <table border=0 width=\"100%\" cellspacing=0 id=\"tabelaInterna\" class=\"sortable tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td width=\"2%\" class=\"sorttable_nosort\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"MarcaOuDesmarcaTodos(pagAtual);\" /></td>\n");
  /* Frase #13 - Titulo */
  echo("                    <td class=\"alLeft\" ".$defColspan." style=\"cursor:pointer\" >".RetornaFraseDaLista($lista_frases, 13)."</td>\n");
  /* Frase #69 - Data */
  echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 69)."</td>\n");
  if($visualizar == "E")
  {
    /* Frase #82 - Disponibilizacao */
    echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 82)."</td>\n");
    /* Frase #86 - Limite de entrega */
    echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 86)."</td>\n");
    /* Frase #57 - Compartilhamento */
    echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 57)."</td>\n");
    /* Frase #130 - Situacao */
    echo("                    <td width=\"10%\" style=\"cursor:pointer\">".RetornaFraseDaLista($lista_frases, 130)."</td>\n");
  }
  echo("                  </tr>\n");

  if ((count($lista_exercicios)>0)&&($lista_exercicios != null))
  {

    foreach ($lista_exercicios as $cod => $linha_item)
    {
      $disponibilizacao = "-";
      $entrega = "-";
      $dados_aplicado = RetornaDadosExercicioAplicado($sock,$linha_item['cod_exercicio']);

      if($linha_item['situacao'] == 'A')
      {
        $disponibilizacao = UnixTime2DataHora($dados_aplicado['dt_disponibilizacao']);
        $entrega = UnixTime2DataHora($dados_aplicado['dt_limite_submissao']);
      }

      $data = "<span id=\"data_".$linha_item['cod_exercicio']."\">".UnixTime2Data($linha_item['data'])."</span>";

      $titulo = $linha_item['titulo'];
      $icone = "<img src=\"../imgs/arqp.gif\" alt=\"\" border=\"0\" /> ";
      $situacao = RetornaSituacaoExercicio($linha_item['situacao'],$data_atual,$dados_aplicado['dt_disponibilizacao']);

      /* Frase #6 - Compartilhado com Formadores */
      if($linha_item['tipo_compartilhamento'] == "F")
        $compartilhamento = RetornaFraseDaLista($lista_frases, 6);
      /* Frase #8 - Nao compartilhado */
      else
        $compartilhamento = RetornaFraseDaLista($lista_frases, 8);

      if($cod_usuario == $linha_item['cod_usuario'])
        $compartilhamento = "<span id=\"comp_".$linha_item['cod_exercicio']."\" class=\"link\" onclick=\"js_cod_item='".$linha_item['cod_exercicio']."';AtualizaComp('".$linha_item['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
      if($cod_usuario == $linha_item['cod_usuario'] || $linha_item['tipo_compartilhamento'] == "F"){
        echo("                  <tr id=\"trExercicio_".$linha_item['cod_exercicio']."\">\n");
        echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkExercicio\" id=\"itm_".$linha_item['cod_exercicio']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_exercicio']."\" /></td>\n");
        echo("                    <td ".$defColspan." align=\"left\">".$icone."<a href=\"editar_exercicio.php?cod_curso=".$cod_curso."&cod_exercicio=".$linha_item['cod_exercicio']."\">".$titulo."</a></td>\n");
        echo("                    <td id=\"data_".$linha_item['cod_exercicio']."\">".$data."</td>\n");
        if($visualizar == "E")
        {
          echo("                    <td id=\"disp_".$linha_item['cod_exercicio']."\">".$disponibilizacao."</td>\n");
          echo("                    <td id=\"entrega_".$linha_item['cod_exercicio']."\">".$entrega."</td>\n");
          echo("                    <td>".$compartilhamento."</td>\n");
          echo("                    <td id=\"situacao_".$linha_item['cod_exercicio']."_".$linha_item['situacao']."\">".$situacao."</td>\n");
        }
        echo("                  </tr>\n");
      }
    }
  }
  else
  {
    echo("                  <tr>\n");
    /* Frase #118 - Nao ha nenhum exericio */
    echo("                    <td colspan=\"7\">".RetornaFraseDaLista($lista_frases, 118)."</td>\n");
    echo("                  </tr>\n");
  }

  echo("                <tfoot>\n");
  echo("                  <tr id=\"trIndicePag\">\n");
  echo("                    <td colspan=\"3\" align=\"left\" style=\"border-right:none\">\n");
  if($totalExercicios>1)
    /* Frase #130 - clique no cabecalho para ordenar os exercicios */
    echo("                      *".RetornaFraseDaLista($lista_frases, 130)."\n");
  echo("                    </td>\n");

  echo("                    <td colspan=\"4\" align=\"right\">\n");
  echo("                    <span id=\"paginacao_first\"></span> <span id=\"paginacao_back\"></span>\n");
  $controle=1;
  while($controle<=5){
    echo("                      <span id=\"paginacao_".$controle."\"></span>\n");
    $controle++;
  }
  echo("                    <span id=\"paginacao_fwd\"></span> <span id=\"paginacao_last\"></span>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  echo("                </tfoot>\n");

  echo("                </table>\n");

  if($visualizar == "E")
  {
    echo("                <ul>\n");
    /* Frase #131 - Apagar selecionados */
    echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">".RetornaFraseDaLista($lista_frases, 131)."</span></li>\n");
    /* Frase #53 - Aplicar */
    echo("                  <li id=\"mAplicar_Selec\" class=\"menuUp\"><span>".RetornaFraseDaLista($lista_frases, 53)."</span></li>\n");
    /* Frase #132 - Cancelar aplicacao dos selecionados */
    echo("                  <li id=\"mCancelarAplic_Selec\" class=\"menuUp\"><span id=\"cancelarAplicSelec\">".RetornaFraseDaLista($lista_frases, 132)."</span></li>\n");
    echo("                </ul>\n");
  }
  else if($visualizar == "L")
  {
    echo("                <ul>\n");
    /* Frase #131 - Apagar selecionados */
    echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"eapagarrSelec\">".RetornaFraseDaLista($lista_frases, 131)."</span></li>\n");
    /* Frase #133 - Recuperar selecionados */
    echo("                  <li id=\"mRecup_Selec\" class=\"menuUp\"><span id=\"recuperarSelec\">".RetornaFraseDaLista($lista_frases, 133)."</span></li>\n");
    echo("                </ul>\n");
  }

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("          <br />\n");
  /* 509 - voltar, 510 - topo */
  echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");

  echo("        </td>\n");
  echo("      </tr>\n");

  include("../tela2.php");

  if($visualizar == "E")
  {
    /* Novo Exercicio*/
    echo("    <div id=\"layer_novo_exercicio\" class=\"popup\">\n");
    echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_novo_exercicio);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("      <div class=\"int_popup\">\n");
    echo("        <form name=\"form_novo_exercicio\" method=\"post\" action=\"acoes.php\" onSubmit='return(VerificaNovoTitulo(document.form_novo_exercicio.novo_titulo, 1));'>\n");
    echo("          <div class=\"ulPopup\">\n");
    /* Frase #13 - Titulo */
    echo("            ".RetornaFraseDaLista($lista_frases, 13).":<br />\n");
    echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=150 /><br />\n");
    echo("            <input type=\"hidden\" name=cod_curso value=\"".$cod_curso."\" />\n");
    echo("            <input type=\"hidden\" name=acao value=criarExercicio />\n");
    echo("            <input type=\"hidden\" name=cod_usuario value=\"".$cod_usuario."\" />\n");
    echo("            <input type=\"hidden\" name=origem value=exercicios />\n");
    /* 18 - Ok (gen) */
    echo("            <input type=\"submit\" id=\"ok_novoexercicio\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
    /* 2 - Cancelar (gen) */
    echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_novo_exercicio);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
    echo("         </div>\n");
    echo("        </form>\n");
    echo("      </div>\n");
    echo("    </div>\n\n");

    /* Mudar Compartilhamento */
    echo("    <div class=\"popup\" id=\"comp\">\n");
    echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("      <div class=\"int_popup\">\n");
    echo("        <form name=\"form_comp\" action=\"\" id=\"form_comp\">\n");
    echo("          <input type=\"hidden\" name=cod_curso value=\"".$cod_curso."\" />\n");
    echo("          <input type=\"hidden\" name=cod_usuario value=\"".$cod_usuario."\" />\n");
    echo("          <input type=\"hidden\" name=cod_item value=\"\" />\n");
    echo("          <input type=\"hidden\" name=tipo_comp id=tipo_comp value=\"\" />\n");
    echo("          <input type=\"hidden\" name=texto id=texto value='".RetornaFraseDaLista($lista_frases,192)."'/>\n");
    echo("          <ul class=\"ulPopup\">\n");
    echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Compartilhado com formadores', 'E'); EscondeLayers();\">\n");
    echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
    /* Frase #6 - Compartilhado com formadores */
    echo("              <span>".RetornaFraseDaLista($lista_frases, 6)."</span>\n");
    echo("            </li>\n");
    echo("            <li onClick=\"document.getElementById('tipo_comp').value='N'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), 'Nao Compartilhado', 'E'); EscondeLayers();\">\n");
    echo("              <span id=\"tipo_comp_N\" class=\"check\"></span>\n");
    /* Frase #8 - Nao Compartilhado */
    echo("              <span>".RetornaFraseDaLista($lista_frases, 8)."</span>\n");
    echo("            </li>\n");
    echo("          </ul>\n");
    echo("        </form>\n");
    echo("      </div>\n");
    echo("    </div>\n");

  echo("    <div id=\"layer_aplicar\" class=\"popup\">\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_aplicar);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n");
  /* Frase #75 - Associar a avaliacao */
  echo("          ".RetornaFraseDaLista($lista_frases, 75).": <br />");
  /* Frase #76 - Sim */
  echo("		  <input type=\"radio\" name=\"avaliacao\" id=\"avaliacaos\" value=\"S\">".RetornaFraseDaLista($lista_frases, 76)."\n");
  /* Frase #77 - Nao */
  echo("		  <input type=\"radio\" name=\"avaliacao\" id=\"avaliacaon\" value=\"N\">".RetornaFraseDaLista($lista_frases, 77)."\n");
  echo("		  <br /><br />\n");
  /* Frase #78 - Disponibilizar gabarito com a correcao */
  echo("          ".RetornaFraseDaLista($lista_frases, 78).": <br />");
  echo("		    <input type=\"radio\" name=\"disp_gabarito\" id=\"disp_gabaritos\" value=\"S\">".RetornaFraseDaLista($lista_frases, 76)."\n");
  echo("		    <input type=\"radio\" name=\"disp_gabarito\" id=\"disp_gabariton\" value=\"N\">".RetornaFraseDaLista($lista_frases, 77)."\n");
  echo("		  <br /><br />\n");
  /* Frase #79 - Tipo de aplicacao */
  echo("          ".RetornaFraseDaLista($lista_frases, 79).": <br />");
  /* Frase #80 - Individual */
  echo("		    <input type=\"radio\" name=\"tp_aplicacao\" id=\"tp_aplicacaoi\" value=\"I\">".RetornaFraseDaLista($lista_frases, 80)."\n");
  /* Frase #81 - Em Grupo */
  echo("		    <input type=\"radio\" name=\"tp_aplicacao\" id=\"tp_aplicacaog\" value=\"G\">".RetornaFraseDaLista($lista_frases, 81)."\n");
  echo("		  <br /><br />\n");
  /* Frase #82 - Disponibilizacao */
  echo("          ".RetornaFraseDaLista($lista_frases, 82).": <br />");
  /* Frase #83 - Imediata */
  echo("		    <input type=\"radio\" onChange=\"ExibirAgendamento(this.value);\" name=\"disponibilizacao\" id=\"disponibilizacaoi\" value=\"I\">".RetornaFraseDaLista($lista_frases, 83)."\n");
  /* Frase #84 - Agendar */
  echo("		    <input type=\"radio\" onChange=\"ExibirAgendamento(this.value);\" name=\"disponibilizacao\" id=\"disponibilizacaoa\" value=\"A\">".RetornaFraseDaLista($lista_frases, 84)."\n");
  echo("		  <br /><br />\n");
  echo("          <div id=\"div_disp\" style=\"display:none;\">\n");
  /* Frase #69 - Data */
  echo("            ".RetornaFraseDaLista($lista_frases, 69).": <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".UnixTime2Data($data_atual)."\" id=\"dt_disponibilizacao\" name=\"dt_disponibilizacao\" />\n");
  echo("            <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('dt_disponibilizacao'),'dd/mm/yyyy',this);\" />\n");
  /* Frase #85 - Horario */
  $horario = explode(":",UnixTime2Hora($data_atual));
  echo("            <br /><br />".RetornaFraseDaLista($lista_frases, 85).": <select id=\"hora_disponibilizacao\" class=\"input\">\n");

  for($i=0;$i<24;$i++){
    if($i<10) $i="0$i";
    if($i == $horario[0]) $selected = "selected=\"selected\"";
    else $selected = "";
    echo("<option ".$selected." value=".$i.">".$i."</option>\n");
  }
  echo("</select><b> : </b><select id=\"minuto_disponibilizacao\" class=\"input\">\n");
  for($j=0;$j<60;$j++){
    if($j<10) $j="0$j";
    if($j == $horario[1]) $selected = "selected=\"selected\"";
    else $selected = "";
    echo("<option ".$selected." value=".$j.">".$j."</option>\n");
  }
  echo("</select>\n");
  echo("          </div><br />\n");
  /* Frase #86 - Limite de entrega: */
  echo("          ".RetornaFraseDaLista($lista_frases, 86).": <br /><br />");
  echo("          <div>\n");
  /* Frase #69 - Data */
  echo("            ".RetornaFraseDaLista($lista_frases, 69).": <input class=\"input\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".UnixTime2Data($data_atual)."\" id=\"limite_entrega\" name=\"limite_entrega\" />\n");
  echo("            <img src=\"../imgs/ico_calendario.gif\" alt=\"calendario\" onclick=\"displayCalendar(document.getElementById('limite_entrega'),'dd/mm/yyyy',this);\" />\n");
  /* Frase #85 - Horario */
  echo("            <br /><br />".RetornaFraseDaLista($lista_frases, 85).": <select id=\"hora_limite_entrega\" class=\"input\">\n");
  for($i=0;$i<24;$i++){
    if($i<10) $i="0$i";
    echo("<option value=".$i.">".$i."</option>\n");
  }
  echo("</select><b> : </b><select id=\"minuto_limite_entrega\" class=\"input\">\n");
  for($j=0;$j<60;$j++){
    if($j<10) $j="0$j";
    echo("<option  value=".$j.">".$j."</option>\n");
  }
  echo("</select>\n");
  echo("          </div><br /><br />\n");
  /* 18 - Ok (gen) */
  echo("            <input type=\"button\" class=\"input\" onClick=\"EnvAplica();\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");

  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\" onClick=\"EscondeLayer(lay_aplicar);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("        </div>\n");
  echo("      </div>\n");
  echo("    </div>\n\n");

  }

  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>