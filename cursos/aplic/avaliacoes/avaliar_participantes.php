<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/avaliar_participantes.php

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
  ARQUIVO : cursos/aplic/avaliacoes/avaliar_participantes.php
  ========================================================== */

  // o script avaliar_participantes eh a tela 'Avaliar Participantes' para o formador e
  // eh a tela 'Histrico do Desempenho' para o Aluno

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"MudarCompartilhamentoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RegistrarAvaliacaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MostrarAvaliacoesDinamic");
  $objAjax->register(XAJAX_FUNCTION,"AvaliarDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ApagarAvalicaoDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=22;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=6;
  include("../topo_tela.php");
  
  session_register("visao_aluno_s");

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);
  $VeioDaAtividade = ($VeioDaAtividade == 1);
  $dados=RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);
  $avaliacao_participante = VerificaAvalicaoParticipantes($sock, $cod_avaliacao);


  if($dados['Ferramenta']=='E'){
    /* #233 - Justificar */
    $textoReavaliarOuJustificar = RetornaFraseDaLista($lista_frases,233);
    /* #234 - Avaliar/Justificar */
    $textoHead = RetornaFraseDaLista($lista_frases,234);
  }
  else{
    /* #66 - Reavaliar */
    $textoReavaliarOuJustificar = RetornaFraseDaLista($lista_frases,66);
    /* #62 - Avaliar/Reavaliar */
    $textoHead = RetornaFraseDaLista($lista_frases,62);
  }


  if($dados['Tipo'] == "G")
    $portfolio_grupo = true;

  if ( (!strcmp($dados['Ferramenta'],'P') ) || (!strcmp($dados['Ferramenta'],'E')) || (!strcmp($dados['Ferramenta'],'N') ) )
  {
    if (!strcmp($dados['Tipo'],'G'))
    $portfolio_grupo=1;
    else
    $portfolio_grupo=0;
  }

  if(($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G'))
    $exercicio_grupo = 1;
  else
    $exercicio_grupo = 0;

  if (!strcmp($dados['Ferramenta'],'B'))
  {
    $assunto_sessao = RetornaAssunto($sock,$dados['Cod_atividade']);
  }

  if ((!$portfolio_grupo)&&(!$exercicio_grupo))
    $cod_aluno_grupo = $cod_aluno;
  else
    $cod_aluno_grupo = $cod_grupo;

  /* Funï¿½ï¿½es JavaScript */
  if (!$SalvarEmArquivo)
  {
    echo("    <script language=\"javascript\" src=\"../bibliotecas/dhtmllib.js\"></script>\n");
    echo("    <script language=\"javascript\">\n");
    echo("        if ((navigator.appName.indexOf(\"Netscape\") !=-1) && navigator.appVersion.charAt(0) <= '4') {\n");
    echo("          var isNav = true;\n");
    echo("          var isIE  = false;\n");
    echo("        } else if (navigator.appName.indexOf(\"Microsoft Internet Explorer\") != -1) {\n");
    echo("          var isNav = false;\n");
    echo("          var isIE  = true;\n");
    echo("        } else {\n");
    echo("          var isNav = false;\n");
    echo("          var isIE  = false;\n");
    echo("        }\n");
    echo("        var notNav = ! isNav;\n");
    echo("        var Xpos, Ypos;\n");
    echo("        var js_cod_aluno;\n");
    echo("        var js_cod_grupo;\n");
    echo("        var js_cod_nota;\n");
    echo("        var js_comp = new Array();\n");
    echo("        var js_arrayConteudo;\n");
    echo("        var js_arrayCab = new Array('".RetornaFraseDaLista($lista_frases,60)."','".RetornaFraseDaLista($lista_frases,61)."','".RetornaFraseDaLista($lista_frases,68)."','".RetornaFraseDaLista($lista_frases,50)."','".RetornaFraseDaLista($lista_frases_geral,1)."');\n");

    // parametros para a funcao window.open
    $nome_janela = "'AvaliarAtividade'";
    $param = "'width=600,height=400,top=170,left=170,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'";

    echo("        function Iniciar()\n");
    echo("        {\n");
    echo("          cod_comp = getLayer(\"comp\");\n");
    echo("          startList();\n");
    echo("        }\n");
    echo("\n");

    if ($usr_formador)
    {
      echo("        if (isNav)\n");
      echo("        {\n");
      echo("          document.captureEvents(Event.MOUSEMOVE);\n");
      echo("        }\n");
      echo("        document.onmousemove = TrataMouse;\n");

      echo("        function TrataMouse(e)\n");
      echo("        {\n");
      echo("          Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
      echo("          Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
      echo("        }\n");

      echo("        function getPageScrollY()\n");
      echo("        {\n");
      echo("          if (isNav)\n");
      echo("            return(window.pageYOffset);\n");
      echo("          if (isIE)\n");
      echo("            return(document.documentElement.scrollTop);\n");
      echo("        }\n");

      echo("        function AjustePosMenuIE()\n");
      echo("        {\n");
      echo("          if (isIE)\n");
      echo("            return(getPageScrollY());\n");
      echo("          else\n");
      echo("            return(0);\n");
      echo("        }\n");

      echo("      function startList() {\n");
      echo("        if (document.all && document.getElementById) {\n");
      echo("          nodes = document.getElementsByTagName(\"span\");\n");
      echo("          for (i=0; i < nodes.length; i++) {\n");
      echo("            node = nodes[i];\n");
      echo("            node.onmouseover = function() {\n");
      echo("              this.className += \"Hover\";\n");
      echo("            }\n");
      echo("            node.onmouseout = function() {\n");
      echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
      echo("            }\n");
      echo("          }\n");
      echo("          nodes = document.getElementsByTagName(\"li\");\n");
      echo("          for (i=0; i < nodes.length; i++) {\n");
      echo("            node = nodes[i];\n");
      echo("            node.onmouseover = function() {\n");
      echo("              this.className += \"Hover\";\n");
      echo("            }\n");
      echo("            node.onmouseout = function() {\n");
      echo("              this.className = this.className.replace(\"Hover\", \"\");\n");
      echo("            }\n");
      echo("          }\n");
      echo("        }\n");
      echo("      }\n\n");

      echo("        function EscondeLayers()\n");
      echo("        {\n");
      echo("          hideLayer(cod_comp);\n");
      echo("        }\n");

      echo("        function AtualizaComp(js_tipo_comp,spanName)\n");
      echo("        {\n");
      echo("          if ((isNav) && (!isMinNS6)) {\n");
      echo("            document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
      echo("            document.comp.document.form_comp.cod_nota.value=js_cod_nota;\n");
      echo("            document.comp.document.form_comp.cod_grupo.value=parseInt(js_cod_grupo);\n");
      echo("            document.comp.document.form_comp.cod_aluno.value=js_cod_aluno;\n");
      echo("            document.comp.document.form_comp.spanName.value=spanName;\n");
      echo("            var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));\n");
      echo("          } else {\n");
      echo("              document.form_comp.tipo_comp.value=js_tipo_comp;\n");
      echo("              document.form_comp.cod_nota.value=js_cod_nota;\n");
      echo("              document.form_comp.cod_grupo.value=parseInt(js_cod_grupo);\n");
      echo("              document.form_comp.cod_aluno.value=js_cod_aluno;\n");
      echo("              document.form_comp.spanName.value=spanName;\n");
      echo("              var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));\n");
      echo("          }\n");
      echo("          var imagem=\"<img src='../imgs/checkmark_blue.gif' />\"\n");
      echo("          if (js_tipo_comp=='F') {\n");
      echo("            tipo_comp[0].innerHTML=imagem;\n");
      echo("            tipo_comp[1].innerHTML=\"&nbsp;\";\n");
      echo("            tipo_comp[2].innerHTML=\"&nbsp;\";\n");
      echo("          } else if (js_tipo_comp=='T') {\n");
      echo("            tipo_comp[0].innerHTML=\"&nbsp;\";\n");
      echo("            tipo_comp[1].innerHTML=imagem;\n");
      echo("            tipo_comp[2].innerHTML=\"&nbsp;\";\n");
      echo("          } else{\n");
      echo("            tipo_comp[0].innerHTML=\"&nbsp;\";\n");
      echo("            tipo_comp[1].innerHTML=\"&nbsp;\";\n");
      echo("            tipo_comp[2].innerHTML=imagem;\n");
      echo("          }\n");
      echo("        }\n\n");

      echo("        function MostraLayer(cod_layer, ajuste)\n");
      echo("        {\n");
      echo("          EscondeLayers();\n");
      echo("          moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
      echo("          showLayer(cod_layer);\n");
      echo("        }\n");

      echo("        function EscondeLayer(cod_layer)\n");
      echo("        {\n");
      echo("          hideLayer(cod_layer);\n");
      echo("        }\n");

      echo("        function AvaliarAluno(funcao, nome, id)\n");
      echo("        {\n");
      echo("          xajax_AvaliarDinamic(".$cod_curso.", ".$cod_avaliacao.", funcao, ".$exercicio_grupo.", id);\n");
      echo("          return(false);\n");
      echo("        }\n");

      echo("        function AvaliarAlunoGrupo(funcao, grupo, nome, id)\n");
      echo("        {\n");
      echo("          xajax_AvaliarDinamic(".$cod_curso.", ".$cod_avaliacao.", grupo, ".$exercicio_grupo.", id);\n");
      echo("          return(false);\n");
      echo("        }\n");

      echo("        function AvaliarAlunoPortfolio(funcao, nome, id)\n");
      echo("        { \n");
      echo("          return AvaliarAluno(funcao, nome, id); \n");
      echo("        } \n");
    }

    /* Se o portfolio nao for em grupo, portfolio_grupo = NULL, mas para passar como parametro pro javascript
     * precisamos converter NULL pra ''
     */
    $portfolio_grupo_js = ($portfolio_grupo != NULL) ? $portfolio_grupo : "''";

    echo("        function HistoricodoDesempenho(funcao,id)\n");
    echo("        {\n");
    /*echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_atividade=".$dados['Cod_atividade']."&cod_avaliacao=".$cod_avaliacao."&ferramenta=".$dados['Ferramenta']."&cod_aluno=\"+funcao,\"AvaliarParticipante\",".$param.");\n");
     echo("    return(false);\n");*/
    echo("          xajax_MostrarAvaliacoesDinamic(".$cod_curso.",".$cod_avaliacao.",funcao,'',".$portfolio_grupo_js.",id,'');\n");
    echo("          js_cod_aluno = funcao;\n");
    echo("          js_cod_grupo = '';\n");
    echo("        }\n");

    echo("        function HistoricodoDesempenhoPortfolio(funcao,id)\n");
    echo("        {\n");
    /*echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeiodePortfolio=0&portfolio_grupo=".$portfolio_grupo."&cod_atividade=".$dados['Cod_atividade']."&ferramenta=".$dados['Ferramenta']."&cod_avaliacao=".$cod_avaliacao."&cod_aluno=\"+funcao,\"HistoricoDesempenho\",".$param.");\n");
     echo("    return(false);\n");*/
    echo("          xajax_MostrarAvaliacoesDinamic(".$cod_curso.",".$cod_avaliacao.",funcao,'',".$portfolio_grupo_js.",id,'');\n");
    echo("          js_cod_aluno = funcao;\n");
    echo("          js_cod_grupo = '';\n");
    echo("        }\n");

    echo("        function HistoricodoDesempenhoPortfolioGrupo(grupo,id)\n");
    echo("        {\n");
    /*echo("    window.open(\"historico_desempenho.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeiodePortfolio=0&portfolio_grupo=".$portfolio_grupo."&cod_atividade=".$dados['Cod_atividade']."&ferramenta=".$dados['Ferramenta']."&cod_avaliacao=".$cod_avaliacao."&cod_grupo=\"+grupo,\"HistoricoDesempenho\",".$param.");\n");    echo("    return(false);\n");*/
    echo("          xajax_MostrarAvaliacoesDinamic(".$cod_curso.",".$cod_avaliacao.",'',grupo,".$portfolio_grupo_js.",id,'');\n");
    echo("          js_cod_aluno = '';\n");
    echo("          js_cod_grupo = grupo;\n");
    echo("        }\n");

    echo("        function RetornaMensagensAluno(funcao)\n");
    echo("        {\n");
    echo("            window.open(\"../forum/ver_mensagens_aluno.php?ordem=data&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_forum=".$dados['Cod_atividade']."&cod_aluno=\"+funcao,\"MensagensParticipante\",".$param.");\n");
    echo("        return(false);\n");
    echo("        }\n");

    echo("        function RetornaFalasAluno(funcao)\n");
    echo("        {\n");
    echo("            window.open(\"../batepapo/ver_falas_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_assunto=".$dados['Cod_atividade']."&cod_aluno=\"+funcao,\"FalasParticipante\",".$param.");\n");
    echo("          return(false);\n");
    echo("        }\n");

    echo("        function RetornaItensAluno(funcao)\n");
    echo("        {\n");
    echo("           window.open(\"../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_usuario_portfolio=\"+funcao,\"ItensPortfolioParticipante\",".$param.");\n");
    echo("          return(false);\n");
    echo("        }\n");

    echo("        function RetornaItensGrupo(funcao)\n");
    echo("        {\n");
    echo("           window.open(\"../portfolio/ver_itens_aluno.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&cod_grupo_portfolio=\"+funcao,\"ItensPortfolioParticipante\",".$param.");\n");
    echo("          return(false);\n");
    echo("        }\n");

    // Funï¿½ï¿½o JvaScript para chamar pï¿½gina para salvar em arquivo.
    echo("        function SalvarAvaliarParticipantes()\n");
    echo("        {\n");
    echo("          document.frmAvaliar.action = \"salvar_avaliar_participantes.php?".RetornaSessionID());
    echo("&cod_curso=".$cod_curso."\";\n");
    echo("          document.frmAvaliar.submit();\n");
    echo("        }\n\n");

    echo("        function Ver()\n");
    echo("        {\n");
    echo("          document.frmAvaliacao.cod_avaliacao.value = ".$cod_avaliacao.";\n");
    echo("          document.frmAvaliacao.action = 'ver.php'; \n");
    echo("          document.frmAvaliacao.submit();\n");
    echo("        }\n\n");

    echo("        function VerObj()\n");
    echo("        {\n");
    $param = "'width=600,height=400,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
    $nome_janela = "'AvaliacoesHistorico'";
    echo("          window.open('ver_popup.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."', ".$nome_janela.", ".$param.");\n");
    echo("          return false;");
    echo("}\n");

    echo ("        function VerTelaAvaliacoes(tela)\n");
    echo ("        {\n");
    echo ("          document.frmAvaliacao.action = 'avaliacoes.php';\n");
    echo ("          document.frmAvaliacao.tela_avaliacao.value = tela;\n");
    echo ("          document.frmAvaliacao.submit();\n");
    echo ("          return false;\n");
    echo ("        }\n");

    echo ("        function VerTelaNotas()\n");
    echo ("        {\n");
    echo ("          document.frmAvaliacao.action = 'notas.php';\n");
    echo ("          document.frmAvaliacao.submit();\n");
    echo ("          return false;\n");
    echo ("        }\n");

    echo("        function AbrePerfil(cod_usuario)\n");
    echo("        {\n");
    echo("           window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("          return(false);\n");
    echo("}\n");

    if(strcmp($dados['Ferramenta'],'E'))
    {
      echo("        function AbreJanelaComponentes(cod_grupo,cod_avaliacao)\n");
      echo("        {\n");
      echo("           window.open('componentes.php?cod_curso=".$cod_curso."&cod_grupo='+cod_grupo+'&cod_avaliacao='+cod_avaliacao,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("          return false;\n");
      echo("        }\n");
    }
    elseif(!strcmp($dados['Ferramenta'],'E'))
    {
      echo("        function AbreJanelaComponentes(cod_grupo,cod_avaliacao)\n");
      echo("        {\n");
      echo("           window.open('componentes.php?cod_curso=".$cod_curso."&cod_grupo='+cod_grupo+'&cod_avaliacao='+cod_avaliacao,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("          return false;\n");
      echo("}\n");

      echo("        function VerExercicio(cod_exercicio, cod_resolucao, cod_dono) \n");
      echo("        { \n");
      $param = "'width=600,height=400,top=150,left=150,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes'";
      echo("           window.open('../exercicios/ver_aplicado_popup.php?&origem=avaliacao&cod='+cod_dono+'&cod_resolucao='+cod_resolucao+'&cod_curso=".$cod_curso."' ,'ExercicioResolvido',".$param."); \n");
      echo("          return(false);\n");
      echo("} \n");
    }

    echo("        function ImprimirRelatorio()\n");
    echo("        {\n");
    echo("          if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
    echo("          {\n");
    echo("            self.print();\n");
    echo("          }\n");
    echo("          else\n");
    echo("          {\n");
    // 51 - Infelizmente nï¿½o foi possï¿½vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
    echo("            alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("          }\n");
    echo("        }\n");

    echo("        function Corrigir(cod_modelo)\n");
    echo("        {\n");
    echo("           window.open('../exercicios/correcao.php?cod_curso=".$cod_curso."&cod_dono=".$cod_usuario."&cod_modelo='+cod_modelo,'Corrigir','width=600,height=400,top=170,left=170,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("        }\n");

    /**
     * Funcao para setar o style de um elemento.
     */
    echo("    function setStyle(elemento, styleText){\n");
    echo("      if(elemento.style.setAttribute)\n");
    echo("        elemento.style.setAttribute(\"cssText\", styleText );\n");  //IE
    echo("      else\n");
    echo("        elemento.setAttribute(\"style\", styleText );\n");  //FF
    echo("    }\n");

    // retorna true se a nota contiver digitos estranhos
    // retorna false se a nota estiver no formato adequado
    echo("        function nota_com_digito_estranho(nota) {\n");
    echo("          re_com_virgula = /^[0-9]+(\.|,)?[0-9]+\$/; \n"); // nota com decimal
    echo("          re_somente_numeros = /^[0-9]+\$/; \n"); // somente numeros
    echo("          if (nota == '' || re_com_virgula.test(nota) || re_somente_numeros.test(nota) ) { \n");
    echo("            return false;\n");
    echo("          } else {\n");
    echo("            return true;\n");
    echo("          }\n");
    echo("        }\n");

    echo("        function VerificaCampos(nota,compartilhamento) \n");
    echo("        { \n");
    // echo("  var comp = document.avaliado.compartilhamento.value; \n");
    echo("          if (nota == '') { \n");
    // 40 - O campo nota nï¿½o pode ser vazio
    echo("            alert('".RetornaFraseDaLista($lista_frases,40)."'); \n");
    echo("            return false; \n");
    echo("          } \n");
    echo("          if (nota_com_digito_estranho(nota)) { \n");
    // 5 - Vocï¿½ digitou caracteres estranhos nesta nota.
    // 6 - Use apenas dï¿½gitos de 0 a 9 e o ponto ( . ) ou a vï¿½rgula ( , ) para o campo valor (exemplo: 7.5). \n");
    // 7 - Por favor retorne e corrija.
    echo("             alert('".RetornaFraseDaLista($lista_frases,5)."\\n".RetornaFraseDaLista($lista_frases,6)."\\n".RetornaFraseDaLista($lista_frases,7)."'); \n");
    echo("            return(false); \n");
    echo("          } \n");
    // verificamos se a nota tem virgula, se tiver, convertemos para ponto
    echo("          nota = nota.replace(/\,/, '.'); \n");
    echo("          if (nota > ".$dados['Valor'].") { \n");
    // 169 - O valor mï¿½nimo para a nota ï¿½
    echo("            alert('".RetornaFraseDaLista($lista_frases, 169)." ".$dados['Valor']."'); \n");
    echo("            return(false); \n");
    echo("          } \n");
    // 24 - A nota nï¿½o pode ser negativa
    echo("          if (nota < 0) { \n");
    echo("            alert('".RetornaFraseDaLista($lista_frases,24)."'); \n");
    echo("            return false; \n");
    echo("          }  \n");
    echo("          if ( ! (compartilhamento == 'T'    \n");
    echo("            || compartilhamento == 'F' \n");
    echo("            || compartilhamento == 'G' \n");
    echo("            || compartilhamento == 'A')\n");
    echo("            ) { \n");
    // 42 - Voce nï¿½o selecionou o modo de compartilhamento !
    echo("            alert('".RetornaFraseDaLista($lista_frases, 42)."'); \n");
    echo("            return false; \n");
    echo("          } \n");
    echo("\n");
    echo("          return true; \n");
    echo("        } \n");

    echo("        function EnviarAvalicao(id,cod)\n");
    echo("        {\n");
    echo("          var i;");
    echo("          var nota = document.getElementById(id+'_avaliar_nota').value;");
    echo("          var just = document.getElementById(id+'_avaliar_just').value;");
    echo("          var comp = '';");
    echo("          compRadio = document.getElementsByName(id+'_avaliar_comp');");
    echo("          for(i=0;i<3;i++)\n");
    echo("          {\n");
    echo("            if(compRadio[i].checked == true)\n");
    echo("            comp = compRadio[i].value;\n");
    echo("          }\n");
    echo("          if(VerificaCampos(nota,comp))");
    echo("            xajax_RegistrarAvaliacaoDinamic(".$cod_curso.",".$cod_avaliacao.",cod,'','',nota,just,comp,id);");
    echo("        }\n");

    echo("        function RetornaFraseComp(comp)\n");
    echo("        {\n");
    echo("             if(comp == 'T')\n");
    // 51 - Totalmente Compartilhado
    echo("               return '".RetornaFraseDaLista($lista_frases,51)."'\n");
    echo("             else if(comp == 'F')\n");
    // 52 - Compartilhado com Formadores
    echo("               return '".RetornaFraseDaLista($lista_frases,52)."'\n");
    echo("             else if(comp == 'G')\n");
    // 53 - Compartilhado com Formadores e Com o Grupo
    echo("               return '".RetornaFraseDaLista($lista_frases,53)."'\n");
    echo("             else if(comp == 'A')\n");
    // 54 - Compartilhado com Formadores e Com o Participante
    echo("               return '".RetornaFraseDaLista($lista_frases,54)."'\n");
    echo("             else\n");
    echo("               return '';\n");
    echo("        }\n\n");

    echo("        function CriarSpanSimples(frase,classe)\n");
    echo("        {\n");
    echo("              newSpan = document.createElement('span');\n");
    echo("              newSpan.setAttribute(\"class\",classe);\n");
    echo("              newSpan.innerHTML = frase;\n");
    echo("              return newSpan;\n");
    echo("        }\n\n");

    if($usr_formador)
    {
      echo("        function CriarInputText(name,id,size,classe,value)\n");
      echo("        {\n");
      echo("              newInput = document.createElement('input');\n");
      echo("              newInput.setAttribute(\"type\",\"text\");\n");
      echo("              newInput.setAttribute(\"name\",name);\n");
      echo("              newInput.setAttribute(\"id\",id);\n");
      echo("              newInput.setAttribute(\"size\",size);\n");
      echo("              newInput.setAttribute(\"class\",classe);\n");
      echo("              newInput.setAttribute(\"value\",value);\n");
      echo("              return newInput;\n");
      echo("        }\n\n");

      echo("        function CriarInputRadio(name,id,tipoComp)\n");
      echo("        {\n");
      echo("      try{\n");
      echo("            newInput = document.createElement('<input type=\"radio\" name=\"name\" />');\n");
      echo("        }catch(err){\n");
      echo("           newInput = document.createElement('input');\n");
      echo("        }\n");
      //Codigo abaixo comentado pois nao funciona no IE
      //echo("      newInput.setAttribute(\"type\",'radio');\n");
      echo("      newInput.type='radio';\n");
      echo("      newInput.setAttribute(\"name\",name);\n");
      echo("      newInput.setAttribute(\"id\",id);\n");
      echo("      newInput.setAttribute(\"value\",tipoComp);\n");
      echo("      return newInput;\n");
      echo("        }\n\n");

      echo("        function CriarInputHidden(name,value)\n");
      echo("        {\n");
      echo("              newInput = document.createElement('input');\n");
      echo("              newInput.setAttribute(\"type\",\"hidden\");\n");
      echo("              newInput.setAttribute(\"name\",name);\n");
      echo("              newInput.setAttribute(\"id\",name);\n");
      echo("              newInput.setAttribute(\"value\",value);\n");
      echo("              return newInput;\n");
      echo("        }\n\n");

      echo("        function CriarTextArea(name,id,rows,cols)\n");
      echo("        {\n");
      echo("              newTextArea = document.createElement('textarea');\n");
      echo("              newTextArea.setAttribute(\"name\",name);\n");
      echo("              newTextArea.setAttribute(\"id\",id);\n");
      echo("              newTextArea.setAttribute(\"rows\",rows);\n");
      echo("              newTextArea.setAttribute(\"cols\",cols);\n");
      echo("              newTextArea.setAttribute(\"style\",\"border: 2px solid #9bc;\");\n");
      echo("              return newTextArea;\n");
      echo("        }\n\n");

      echo("        function CriarBotaoEnviar(id,cod)\n");
      echo("        {\n");
      echo("              newButton = document.createElement('input');\n");
      echo("              newButton.setAttribute(\"type\",\"button\");\n");
      //echo("              newButton.setAttribute(\"class\",\"input\");\n");
      echo("        newButton.className=\"input\";\n");
      // 11 - Enviar
      echo("              newButton.setAttribute(\"value\",\"".RetornaFraseDaLista($lista_frases_geral, 11)."\");\n");
      echo("              newButton.onclick = function(){ EnviarAvalicao(id,cod); };\n");
      echo("              return newButton;\n");
      echo("        }\n\n");

      echo("        function CriarTrNota(id,op,valueInput,codModelo)\n");
      echo("        {\n");
      echo("              newTr = document.createElement('tr');\n");
      echo("              newTd1 = document.createElement('td');\n");
      //echo("              newTd1.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd1,\"border:0pt\");\n");
      echo("              newTd1.setAttribute(\"align\",\"right\");\n");
      // 60 - Nota
      echo("              newTd1.innerHTML = '<b>".RetornaFraseDaLista($lista_frases, 60).":</b>';\n");
      echo("              newTd2 = document.createElement('td');\n");
      //echo("              newTd2.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd2,\"border:0pt\");\n");
      echo("              newTd2.setAttribute(\"align\",\"left\");\n");
      echo("              if(op == 1 || op == 2)\n");
      echo("              {\n");
      echo("                newTd2.appendChild(CriarSpanSimples('".RetornaFraseDaLista($lista_frases, 179)."',\"\"));\n");
      echo("                newTd2.appendChild(CriarInputHidden(id+'_avaliar_nota',0))\n");
      echo("                if(op == 2)\n");
      echo("                {\n");
      echo("                  newSpan = CriarSpanSimples('  ".RetornaFraseDaLista($lista_frases, 180)."',\"link\");\n");
      echo("                  newSpan.onclick = function(){ Corrigir(codModelo); };\n");
      echo("                  newTd2.appendChild(newSpan);\n");
      echo("                }\n");
      echo("              }\n");
      echo("              if(op == 3)\n");
      echo("              {\n");
      echo("                newTd2.appendChild(CriarSpanSimples(valueInput,\"\"));\n");
      echo("                newTd2.appendChild(CriarInputHidden(id+'_avaliar_nota',valueInput));\n");
      echo("              }\n");
      echo("              if(op == 4)\n");
      echo("                newTd2.appendChild(CriarInputText(id+'_avaliar_nota',id+'_avaliar_nota',\"5\",\"input\",\"\"));\n");
      echo("              newTr.appendChild(newTd1);\n");
      echo("              newTr.appendChild(newTd2);\n");
      echo("              return newTr;\n");
      echo("        }\n\n");

      echo("        function CriarTrJustificativa(id,op)\n");
      echo("        {\n");
      echo("              newTr = document.createElement('tr');\n");
      echo("              newTd1 = document.createElement('td');\n");
      //echo("              newTd1.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd1,\"border:0pt\");\n");
      echo("              newTd1.setAttribute(\"align\",\"right\");\n");
      // 163 - Justificativa
      echo("              newTd1.innerHTML = '<b>".RetornaFraseDaLista($lista_frases, 163).":</b>';\n");
      echo("              newTd2 = document.createElement('td');\n");
      //echo("              newTd2.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd2,\"border:0pt\");\n");
      echo("              newTd2.setAttribute(\"align\",\"left\");\n");
      echo("              if(op == 1)\n");
      echo("              {\n");
      echo("                newTd2.appendChild(CriarSpanSimples('".RetornaFraseDaLista($lista_frases, 179)."',\"\"));\n");
      echo("                newTd2.appendChild(CriarInputHidden(id+'_avaliar_just',\"\"));\n");
      echo("              }\n");
      echo("              if(op == 2)\n");
      echo("                newTd2.appendChild(CriarTextArea(id+'_avaliar_just',id+'_avaliar_just',\"5\",\"60\"));\n");
      echo("              newTr.appendChild(newTd1);\n");
      echo("              newTr.appendChild(newTd2);\n");
      echo("              return newTr;\n");
      echo("        }\n\n");

      echo("        function CriarDivOpComp(id,op)\n");
      echo("        {\n");
      echo("              newDiv = document.createElement('div');\n");
      echo("              if(op == 1)\n");
      echo("                newDiv.appendChild(CriarInputRadio(id+'_avaliar_comp',id+'_avaliar_comp','T'));\n");
      // 51 - Totalmente Compartilhado
      echo("              newDiv.appendChild(CriarSpanSimples(\"".RetornaFraseDaLista($lista_frases,51)."\",\"\"));\n");
      echo("              newDiv.appendChild(document.createElement('br'));\n");
      echo("              if(op == 1)\n");
      echo("                newDiv.appendChild(CriarInputRadio(id+'_avaliar_comp',id+'_avaliar_comp','F'));\n");
      // 52 - Compartilhado com Formadores
      echo("              newDiv.appendChild(CriarSpanSimples(\"".RetornaFraseDaLista($lista_frases,52)."\",\"\"));\n");
      echo("              newDiv.appendChild(document.createElement('br'));\n");
      echo("              if(op == 1)\n");
      if(($portfolio_grupo)||($exercicio_grupo))
      {
        echo("                newDiv.appendChild(CriarInputRadio(id+'_avaliar_comp',id+'_avaliar_comp','G'));\n");
        // 53 - Compartilhado com Formadores e Com o Grupo
        echo("              newDiv.appendChild(CriarSpanSimples(\"".RetornaFraseDaLista($lista_frases,53)."\",\"\"));\n");
      }
      else
      {
        echo("                newDiv.appendChild(CriarInputRadio(id+'_avaliar_comp',id+'_avaliar_comp','A'));\n");
        // 54 - Compartilhado com Formadores e Com o Participante
        echo("              newDiv.appendChild(CriarSpanSimples(\"".RetornaFraseDaLista($lista_frases,54)."\",\"\"));\n");
      }
      echo("              return newDiv;\n");
      echo("        }\n\n");

      echo("        function CriarTrComp(id,op)\n");
      echo("        {\n");
      echo("              newTr = document.createElement('tr');\n");
      echo("              newTd1 = document.createElement('td');\n");
      //echo("              newTd1.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd1,\"border:0pt\");\n");
      echo("              newTd1.setAttribute(\"align\",\"right\");\n");
      // 50 - Compartilhar
      echo("              newTd1.innerHTML = '<b>".RetornaFraseDaLista($lista_frases, 50).":</b>';\n");
      echo("              newTd2 = document.createElement('td');\n");
      //echo("              newTd2.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd2,\"border:0pt\");\n");
      echo("              newTd2.setAttribute(\"align\",\"left\");\n");
      echo("              newTd2.appendChild(CriarDivOpComp(id,op));\n");
      echo("              newTr.appendChild(newTd1);\n");
      echo("              newTr.appendChild(newTd2);\n");
      echo("              return newTr;\n");
      echo("        }\n\n");

      echo("        function CriarTrBotoes(id,cod)\n");
      echo("        {\n");
      echo("              newTr = document.createElement('tr');\n");
      echo("              newTd1 = document.createElement('td');\n");
      //echo("              newTd1.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd1,\"border:0pt\");\n");
      echo("              newTd1.setAttribute(\"align\",\"right\");\n");
      echo("              newTd1.innerHTML = '&nbsp';\n");
      echo("              newTd2 = document.createElement('td');\n");
      //echo("              newTd2.setAttribute(\"style\",\"border:0pt;\");\n");
      echo("        setStyle(newTd2,\"border:0pt\");\n");
      echo("              newTd2.setAttribute(\"align\",\"left\");\n");
      echo("              newTd2.appendChild(CriarBotaoEnviar(id,cod));\n");
      echo("              newTr.appendChild(newTd1);\n");
      echo("              newTr.appendChild(newTd2);\n");
      echo("              return newTr;\n");
      echo("        }\n\n");

      echo("        function CriarTabelaAvaliar(id,opNota,opJust,opComp,valueInputNota,codModelo,opEnviar,cod)\n");
      echo("        {\n");
      echo("        var tbody = document.createElement('tbody');\n");
      echo("              newTable = document.createElement('table');\n");
      echo("              tbody.appendChild(CriarTrNota(id,opNota,valueInputNota,codModelo));\n");
      echo("              tbody.appendChild(CriarTrJustificativa(id,opJust));\n");
      echo("              tbody.appendChild(CriarTrComp(id,opComp));\n");
      echO("              if(opEnviar == 1)\n");
      echo("                tbody.appendChild(CriarTrBotoes(id,cod));\n");
      echo("        newTable.appendChild(tbody);");
      echo("              return newTable;\n");
      echo("        }\n\n");

      echo("        function HabilitaAvaliar(id,opNota,opJust,opComp,valueInputNota,codModelo,opEnviar,cod)\n");
      echo("        {\n");
      echo("          if(document.getElementById(id+'_hist') != null)\n");
      echo("            DesabilitaTr(id+'_hist');\n");
      echo("          if(document.getElementById(id+'_avaliar') == null)\n");
      echo("          {\n");
      echo("            trElement = document.getElementById(id);\n");
      echo("            trElement = trElement.nextSibling;\n");
      echo("            tableElement = trElement.parentNode;\n");
      echo("            newTrConteiner = document.createElement('tr');\n");
      echo("            newTrConteiner.setAttribute('id', id+'_avaliar');\n");
      //echo("            newTrConteiner.setAttribute(\"class\", \"altColor0\");\n");
      echo("        newTrConteiner.className=\"altColor0\";\n");
      echo("            newTdConteiner = document.createElement('td');\n");
      echo("            newTdConteiner.colSpan = 5;\n");
      echo("            newTdConteiner.appendChild(CriarTabelaAvaliar(id,opNota,opJust,opComp,valueInputNota,codModelo,opEnviar,cod));\n");
      echo("            newBr = document.createElement('br');\n");
      echo("            newTdConteiner.appendChild(newBr);\n");
      echo("            newTrConteiner.appendChild(newTdConteiner);\n");
      echo("            newTrConteiner.appendChild(CriarTdFechar(id+'_avaliar'));\n");
      echo("            tableElement.insertBefore(newTrConteiner,trElement);\n");
      echo("          }\n");
      echo("        }\n");
    }

    echo("        function AbrePerfilHisDes(id) \n");
    echo("        {\n");
    echo("          var brokenId = id.split(\"_\");\n");
    echo("          AbrePerfil(parseInt(brokenId[0]));\n");
    echo("        }\n");

    /**
     * Como no IE getElementsByName() nÃ£o funciona, usar a funcao abaixo.
     */
    echo("    function getElementsByName_iefix(tag, name) {\n");
    echo("      var elem = document.getElementsByTagName(tag);\n");
    echo("      var arr = new Array();\n");
    echo("      for(var i = 0, iarr = 0; i < elem.length; i++) {\n");
    echo("        var att = elem[i].getAttribute('name');\n");
    echo("        if(att == name) {\n");
    echo("          arr[iarr] = elem[i];\n");
    echo("          iarr++;\n");
    echo("        }\n");
    echo("      }\n");
    echo("      return arr;\n");
    echo("    }\n");

    echo("        function MudaSpanCompartilhamento(spanName,novoComp,tipoComp,codNota,codGrupo,codAluno)\n");
    echo("        {\n");
    echo("          spanElements = getElementsByName_iefix('span',spanName);\n");
    echo("          for(i=0;i<spanElements.length;i++)\n");
    echo("          {\n");
    echo("            spanElements[i].innerHTML = novoComp;\n");
    echo("            spanElements[i].onclick = function() { js_cod_nota=codNota;js_cod_grupo=codGrupo;js_cod_aluno=codAluno;AtualizaComp(tipoComp,spanName);MostraLayer(cod_comp,100); }\n");
    echo("          }\n");
    echo("        }\n");

    if($usr_formador)
    {
      echo("        function MudaCompartilhamentoHisDes(id) \n");
      echo("        {\n");
      echo("          var brokenId = id.split(\"_\");\n");
      echo("          js_cod_nota = parseInt(brokenId[1]);\n");
      echo("          js_cod_aluno = parseInt(brokenId[3]);\n");
      echo("          js_cod_grupo = brokenId[4];\n");
      echo("          spanName = 'comp_'+js_cod_nota;\n");
      echo("          AtualizaComp(brokenId[2],spanName);\n");
      echo("          MostraLayer(cod_comp,140);\n");
      echo("        }\n");

      echo("        function ApagarHisDes(id,trPaiId) \n");
      echo("        {\n");
      echo("          var brokenId,codNota,i,codAluno,codGrupo;\n");
      echo("          if(confirm(\"".RetornaFraseDaLista($lista_frases,86)."\\n ".RetornaFraseDaLista($lista_frases,87)."\"))\n");
      echo("          {\n");
      echo("            brokenId = id.split(\"_\");\n");
      echo("            codNota = parseInt(brokenId[0]);\n");
      echo("            i = parseInt(brokenId[1]);\n");
      echo("            codAluno = parseInt(brokenId[2]);\n");
      echo("            codGrupo = parseInt(brokenId[3]);\n");
      echo("            xajax_ApagarAvalicaoDinamic(".$cod_curso.",".$cod_avaliacao.",codNota,codAluno,codGrupo,".$portfolio_grupo_js.",trPaiId);");
      echo("            tableElement = document.getElementById('table_'+codNota+'_'+i);\n");
      echo("            tdElement = tableElement.parentNode;\n");
      echo("            tdElement.removeChild(tableElement.nextSibling);\n");//remove <br>
      echo("            tdElement.removeChild(tableElement);\n");
      echo("          }\n");
      echo("        }\n");
    }

    echo("        function CriarDivCabecalhoHistoricoDesempenho() \n");
    echo("        {\n");
    echo("          newDivCab = document.createElement('div');\n");
    echo("          newDivCab.align = 'left';\n");
    // 103 - Histï¿½rico do Desempenho do Participante
    echo("          newDivCab.innerHTML = '<b>".RetornaFraseDaLista($lista_frases,103)."</b>';\n");
    echo("          return newDivCab;\n");
    echo("        }\n");

    echo("        function CriarTdFechar(id) \n");
    echo("        {\n");
    echo("          newTd = document.createElement('td');\n");
    echo("          newSpan = document.createElement('span');\n");
    // 13 - Fechar (ger)
    echo("          newSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral,13)."';\n");
    echo("          newSpan.className='link';\n");
    echo("          newSpan.onclick = function(){ DesabilitaTr(id); };\n");
    echo("          newTd.appendChild(newSpan);\n");
    echo("          return newTd;\n");
    echo("        }\n");

    echo("        function HabilitaHistorico(arrayConteudo,tamTabela,id)\n");
    echo("        {\n");
    echo("          var i,j,codNota,codFormador,tipoComp;\n");
    echo("          if(document.getElementById(id+'_avaliar') != null)\n");
    echo("            DesabilitaTr(id+'_avaliar');\n");
    echo("          if(document.getElementById(id+'_hist') == null)\n");
    echo("          {\n");
    echo("            trElement = document.getElementById(id);\n");
    echo("            trElement = trElement.nextSibling;\n");
    echo("            tableElement = trElement.parentNode;\n");
    echo("            newTrConteiner = document.createElement('tr');\n");
    echo("            newTrConteiner.setAttribute('id', id+'_hist');\n");
    //echo("            newTrConteiner.setAttribute(\"class\", \"altColor0\");\n");
    echo("        newTrConteiner.className=\"altColor0\";\n");
    echo("            newTdConteiner = document.createElement('td');\n");
    if($usr_formador)
    echo("            newTdConteiner.colSpan = 5;\n");
    else
    echo("            newTdConteiner.colSpan = 3;\n");
    echo("            newTdConteiner.appendChild(CriarDivCabecalhoHistoricoDesempenho());\n");
    echo("            newBr = document.createElement('br');\n");
    echo("            newTdConteiner.appendChild(newBr);\n");
    echo("            for(i=0;i<tamTabela;i++)\n");
    echo("            {\n");
    echo("              codFormador = arrayConteudo[i][5];\n");
    echo("              codNota = arrayConteudo[i][6];\n");
    echo("              tipoComp = arrayConteudo[i][7];\n");
    echo("              newTable = document.createElement('table');\n");
    echo("        var tbody = document.createElement('tbody');\n");
    echo("              newTable.width = '100%';\n");
    echo("              newTable.setAttribute(\"id\", 'table_'+codNota+'_'+i);\n");
    echo("              newTable.setAttribute(\"cellpadding\", \"0\");\n");
    echo("              newTable.setAttribute(\"cellspacing\",\"0\");\n");
    echo("              newTrCab = document.createElement('tr');\n");
    echo("              newTrCab.className = 'head01';\n");
    echo("              newTrCab.setAttribute('id', 'tr_cab_'+i);\n");
    if($usr_formador)
    echo("              for(j=0;j<5;j++)\n");
    else
    echo("              for(j=0;j<3;j++)\n");
    echo("              {\n");
    echo("                newTd = document.createElement('td');\n");
    echo("                if(j == 3)\n");// Compartilhamento
    echo("                  newTd.width = '20%';\n");
    if($usr_formador)
    {
      echo("                else if( j == 4)\n");
      echo("                  newTd.width = '10%';\n");
      echo("                if(j != 4)\n  ");
    }
    echo("                  newTd.innerHTML=js_arrayCab[j];\n");
    if($usr_formador)
    {
      echo("                else\n");
      echo("                {\n");
      echo("                  newSpan = document.createElement('span');\n");
      echo("                  newSpan.innerHTML=js_arrayCab[j];\n");
      echo("                  newSpan.className='link';\n");
      echo("                  newSpan.setAttribute(\"id\", codNota+'_'+i+'_'+js_cod_aluno+'_'+js_cod_grupo);\n");
      echo("                  newSpan.onclick = function(){ ApagarHisDes(this.id,id); };\n");
      echo("                  newTd.rowSpan = 2;\n");
      echo("                  newTd.appendChild(newSpan);\n");
      echo("                }\n");
    }
    echo("                newTrCab.appendChild(newTd);\n");
    echo("              }\n");
    echo("              newTrMid = document.createElement('tr');\n");
    echo("              newTrMid.setAttribute('id', 'tr_mid_'+i);\n");
    //echo("              newTrMid.setAttribute(\"class\", \"altColor1\");\n");
    echo("        newTrMid.className=\"altColor1\";");
    if($usr_formador)
    echo("              for(j=0;j<4;j++)\n");
    else
    echo("              for(j=0;j<3;j++)\n");
    echo("              {\n");
    echo("                newTd = document.createElement('td');\n");
    echo("                if(j == 2 || j == 3)\n");
    echo("                {\n");
    echo("                  newSpan = document.createElement('span');\n");
    echo("                  newSpan.innerHTML=arrayConteudo[i][j];\n");
    echo("                  newSpan.className='link';\n");
    echo("                  newSpan.setAttribute(\"id\", codFormador+'_'+codNota+'_'+tipoComp+'_'+js_cod_aluno+'_'+js_cod_grupo+'_'+j);\n");
    echo("                  if(j == 2)\n");
    echo("                    newSpan.onclick = function(){ AbrePerfilHisDes(this.id); };\n");
    echo("                  else\n");
    echo("                  {\n");
    echo("                    newSpan.setAttribute(\"name\",'comp_'+codNota);\n");
    echo("                    newSpan.onclick = function(){ MudaCompartilhamentoHisDes(this.id); };\n");
    echo("                  }\n");
    echo("                newTd.appendChild(newSpan);\n");
    echo("                }\n");
    echo("                else\n");
    echo("                  newTd.innerHTML=arrayConteudo[i][j];\n");
    echo("                newTrMid.appendChild(newTd);\n");
    echo("              }\n");
    echo("              newTrJust = document.createElement('tr');\n");
    echo("              newTrJust.className = 'head01';\n");
    echo("              newTd = document.createElement('td');\n");
    echo("              newTd.colSpan = 5;\n");
    echo("              newTd.align = 'left';\n");
    echo("              newTd.innerHTML='".RetornaFraseDaLista($lista_frases,163)."';\n");
    echo("              newTrJust.appendChild(newTd);\n");
    echo("              newTrJustMid = document.createElement('tr');\n");
    echo("              newTd = document.createElement('td');\n");
    echo("              newTd.colSpan = 5;\n");
    echo("              newTd.align = 'left';\n");
    if($usr_formador)
    echo("              newTd.innerHTML=arrayConteudo[i][j];\n");
    else
    echo("              newTd.innerHTML=arrayConteudo[i][4];\n");
    echo("              newTrJustMid.appendChild(newTd);\n");
    //echo("              newTrJustMid.setAttribute(\"class\", \"altColor1\");\n");
    echo("        newTrJustMid.className=\"altColor1\";");
    echo("              tbody.appendChild(newTrCab);\n");
    echo("              tbody.appendChild(newTrMid);\n");
    echo("              tbody.appendChild(newTrJust);\n");
    echo("              tbody.appendChild(newTrJustMid);\n");
    echo("        newTable.appendChild(tbody);");
    echo("              newTdConteiner.appendChild(newTable);\n");
    echo("              newBr = document.createElement('br');\n");
    echo("              newTdConteiner.appendChild(newBr);\n");
    echo("            }\n");
    echo("            newTrConteiner.appendChild(newTdConteiner);\n");
    echo("            newTrConteiner.appendChild(CriarTdFechar(id+'_hist'));\n");
    echo("            tableElement.insertBefore(newTrConteiner,trElement);\n");
    echo("          }\n\n");
    echo("        }\n\n");

    echo("        function DesabilitaTr(id)\n");
    echo("        {\n");
    echo("          trElement = document.getElementById(id);\n");
    echo("          tableElement = trElement.parentNode;\n");
    echo("          tableElement.removeChild(trElement);\n");
    echo("        }\n\n");

    if($usr_formador)
    {
      echo("        function MudaSpanComp(SpanComp,codNota,comp,codAluno,codGrupo)\n");
      echo("        {\n");
      echo("              SpanComp.innerHTML = RetornaFraseComp(comp);\n");
      echo("              SpanComp.setAttribute(\"name\",'comp_'+codNota);\n");
      //echo("              SpanComp.setAttribute(\"class\",\"link\");\n");
      echo("        SpanComp.className=\"link\";\n");
      echo("              SpanComp.onclick = function(){ js_cod_nota=codNota;js_cod_grupo=codGrupo;js_cod_aluno=codAluno;AtualizaComp(comp,'comp_'+codNota);MostraLayer(cod_comp,140); };\n");
      echo("        }\n\n");

      echo("        function AtualizaTr(id,codNota,nota,data,comp,codAluno,codGrupo)\n");
      echo("        {\n");
      echo("          var i=0;\n");
      echo("          trElement = document.getElementById(id);\n");
      echo("          childElement = trElement.firstChild;\n");
      echo("          while(childElement != null)\n");
      echo("          {\n");
      //  echo("alert('i: '+i+' : childElement.firstChild: '+childElement.firstChild);");
      echo("            if(i == 2 && isIE)\n");  //nota no IE
      echo("              childElement.firstChild.innerHTML = nota+'&nbsp;';\n");
      echo("            if(i == 5 && !isIE)\n");  //nota no FF
      echo("              childElement.firstChild.innerHTML = nota;\n");

      echo("            if(i == 3 && isIE)\n");  //data no IE
      echo("              childElement.innerHTML = data+'&nbsp;';\n");
      echo("            if(i == 7 && !isIE)\n");  //data no FF
      echo("              childElement.innerHTML = data;\n");

      echo("            if(i == 4 && isIE) {\n");//Avaliar/Reavaliar no IE
      echo("              if(codNota == -1)\n");
      //65 - Avaliar
      echo("                childElement.firstChild.innerHTML = '".RetornaFraseDaLista($lista_frases,65)."';\n");
      echo("              else\n");
      //66 - Reavaliar
      echo("                childElement.firstChild.innerHTML = $textoReavaliarOuJustificar;\n");
      echo("            }\n");
      echo("            if(i == 9 && !isIE) {\n");//Avaliar/Reavaliar no FF
      echo("              if(codNota == -1)\n");
      //65 - Avaliar
      echo("                childElement.firstChild.innerHTML = '".RetornaFraseDaLista($lista_frases,65)."';\n");
      echo("              else\n");
      //66 - Reavaliar
      echo("                childElement.firstChild.innerHTML = $textoReavaliarOuJustificar;\n");
      echo("            }\n");

      echo("            if(i == 5 && isIE)\n");      //span compartilhamento no IE
      echo("              MudaSpanComp(childElement.firstChild,codNota,comp,codAluno,codGrupo);\n");
      echo("            if(i == 11 && !isIE)\n");      //span compartilhamento no FF
      echo("              MudaSpanComp(childElement.firstChild,codNota,comp,codAluno,codGrupo);\n");

      echo("            childElement = childElement.nextSibling;\n");
      echo("            i++;\n");
      echo("          }\n");
      echo("        }\n\n");
    }

    echo("    </script>\n\n");
  }

  $objAjax->printJavascript();

  include("../menu_principal.php");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">");

  // A variavel tela_avaliacao indica quais avaliacoes devem ser listadas: 'P'assadas, 'A'tuais ou 'F'uturas
  if (!isset($tela_avaliacao) || !in_array($tela_avaliacao, array('P', 'A', 'F')))
  {
    $tela_avaliacao = 'A';
  }
  switch ($tela_avaliacao)
  {
    case 'P' :
      $lista_avaliacoes = RetornaAvaliacoesAnteriores($sock,$usr_formador);
      // 29 - Avaliaï¿½ï¿½es Passadas
      $frase_avaliacoes = RetornaFraseDaLista($lista_frases, 29);
      break;
    case 'A' :
      $lista_avaliacoes = RetornaAvaliacoesAtuais($sock,$usr_formador);
      // 32 - Avaliaï¿½ï¿½es Atuai
      $frase_avaliacoes = RetornaFraseDaLista($lista_frases, 32);
      break;
    case 'F' :
      $lista_avaliacoes = RetornaAvaliacoesFuturas($sock,$usr_formador);
      // 30 - Avaliaï¿½ï¿½es Futuras
      $frase_avaliacoes = RetornaFraseDaLista($lista_frases, 30);
      break;
  }

  // Pï¿½gina Principal
  /* 1 - Avaliaï¿½ï¿½es */
  $cod_pagina = 6;
  $cabecalho = "          <h4>".RetornaFraseDaLista($lista_frases,1);

  if ($usr_formador)
  {
    // para o formador, esta ï¿½a tela em que ele avalia os alunos

    // 34 - Avaliar participantes
    $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,34)."</h4>";
  }
  else
  {
    // para o aluno, esta ï¿½a tela com os histricos de desempenho

    // 105 - Historico de desempenho dos participantes
    $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,105)."</h4>";
  }
  echo($cabecalho);

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

     /* 509 - Voltar */
    echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  if (!$SalvarEmArquivo)
  {
    if ( ! $VeioDaAtividade)
    {
      echo("          <form name=\"frmAvaliacao\" method=\"get\">\n");
      echo("            <input type=\"hidden\" name=\"cod_curso\"      value=\"".$cod_curso."\">\n");
      // Passa o cod_avaliacao para executar aï¿½es sobre ela.
      echo("            <input type=\"hidden\" name=\"cod_avaliacao\"  value=\"-1\">\n");
      // tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
      echo("            <input type=\"hidden\" name=\"tela_avaliacao\" value=\"".$tela_avaliacao."\"s>\n");
      echo("          </form>\n");
    }
  }

  if (!strcmp($dados['Ferramenta'],'B'))
  {
    if (BatePapoExiste($sock,$dados['Cod_atividade']))
    {
      $batepapo_nao_existe = 0;
      $lista_usuarios = RetornaUsuarios($sock,$cod_curso);
      $lista_sessoes = RetornaCodSessao($sock,$dados['Cod_atividade']);

      foreach ($lista_sessoes as $cod => $linha)
      {
        $msgs_qtde=RetornaQtdeMsgsUsuario($sock,$linha['Cod_sessao'],$lista_usuarios);
        //para cada aluno incrementar a quantidade de mensagens
        foreach($lista_usuarios as $cod => $nome)
        {
          $msgs_total[$cod] = $msgs_total[$cod] + $msgs_qtde[$cod];
        }
      }
    }
    else
    {
      $batepapo_nao_existe = 1;
    }
  }

  echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo ("           <tr>\n");
  echo ("           <!----------------- Botoes de Acao ----------------->\n");
  echo ("             <td class=\"btAuxTabs\">\n");
  echo ("               <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar */
  echo("                  <li><span OnClick='Ver();'>".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 29 - Avaliaï¿½ï¿½es Passadas */
  echo ("      <li><span onClick=return(VerTelaAvaliacoes('P'))>".RetornaFraseDaLista($lista_frases, 29)."</span></li>\n");
  /* 32 - Avaliaï¿½ï¿½es Atuais*/
  echo ("      <li><span onClick=return(VerTelaAvaliacoes('A'))>".RetornaFraseDaLista($lista_frases, 32)."</span></li>\n");
  /* 30 - Avaliaï¿½ï¿½es Futuras*/
  echo ("      <li><span onClick=return(VerTelaAvaliacoes('F'))>".RetornaFraseDaLista($lista_frases, 30)."</span></li>\n");
  /* 31 - Notas dos participantes */
  echo ("            <li><span onClick='return(VerTelaNotas());'>".RetornaFraseDaLista($lista_frases, 31)."</span></li>");
  echo ("          </ul></td>\n");
  echo ("           </tr>\n");
  echo ("           <tr>\n");
  echo ("             <td>\n");
  echo ("               <ul class=\"btAuxTabs03\">\n");
  /*120 - Ver avaliacao*/
  echo("                  <li><span OnClick='VerObj();'>".RetornaFraseDaLista($lista_frases, 120)."</span></li>\n");
  echo("                  <form name=\"frmAvaliar\" method=\"post\">\n");
  echo("                    <input type=\"hidden\" name=\"cod_curso\"     value=\"".$cod_curso."\">\n");
  echo("                    <input type=\"hidden\" name=\"cod_avaliacao\" value=\"".$cod_avaliacao."\">\n");
  // G 50 - Salvar em Arquivo
  echo("                  <li><span onClick=\"SalvarAvaliarParticipantes();\">".RetornaFraseDaLista($lista_frases_geral, 50)."</span></li>\n");
  echo("                  </form>\n");
  // G 14 - Imprimir
  echo("                  <li><span onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral, 14)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  // ajuste para trazer o layer de compartilhamento mais para a esquerda. Veja a funcao MostraLayer
  $ajuste = 100;
  if ( ( ($dados['Ferramenta'] == 'P') || ($dados['Ferramenta']=='N')) && ($dados['Tipo'] == 'G') )
  {
    // avaliacao de atividade em grupo, no portfolio
    $lista_grupos=RetornaListaGrupos($sock);
    $num_grupos=count($lista_grupos);
    if ($num_grupos > 0)
    {
      //Tabela com a lista de alunos do curso, com suas respectivas notas na avaliaï¿½o realizada
      echo ("                 <tr class=\"head\">\n");
      // 48 - Grupo
      echo("                    <td align=\"center\" width=\"25%\">".RetornaFraseDaLista($lista_frases,48)."</td>\n");

      // 49 - Participaï¿½es
      echo("                    <td align=\"center\" width=\"12%\">".RetornaFraseDaLista($lista_frases,49)."</td>\n");
      // 60 - Nota
      echo("                    <td align=\"center\" width=\"11%\">".RetornaFraseDaLista($lista_frases,60)."</td>\n");
      // 61 - Data da Avaliaï¿½o
      echo("                    <td align=\"center\" width=\"11%\">".RetornaFraseDaLista($lista_frases,61)."</td>\n");
      if ($usr_formador)
      {
        if($avaliacao_participante)
        {
          // 62 - Avaliar/Reavaliar
          echo("                    <td align=\"center\" width=\"21%\">".$textoHead."</td>\n");
        }
        // 63 - Compartilhamento
        echo("                    <td align=\"center\" width=\"20%\">".RetornaFraseDaLista($lista_frases,63)."</td>\n");
      }
      echo("                  </tr>\n");

      foreach ($lista_grupos as $cod_grupo => $nome)
      {
        echo("                  <tr id=\"tr_grupos_".$cod_grupo."\">\n");

        echo("                    <td>");
        if (!$SalvarEmArquivo)
        echo("<span class=\"link\" onClick=\"return(AbreJanelaComponentes(".$cod_grupo.",".$cod_avaliacao."));\">".$nome."</span></td>");
        else
        echo($nome."</td>\n");

        // retorna o codigo de um aluno que tem mais notas no grupo (caso aconteca) para garantir que retorne todas as avaliaï¿½ï¿½es.
        // Isso ï¿½ necessario porque alguns alunos podem ser inseridos no grupo depois que algumas avaliaï¿½ï¿½es ja foram feitas para este grupo
        // E sempre que avalia um grupo, todos os alunos do grupo recebem a mesma avaliaï¿½o

        $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod_grupo,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,"");
        if ($num_itens > 0)
        {
          echo("                    <td align=\"center\">");
          if (!$SalvarEmArquivo)
          echo("<a href=\"#\" onClick=\"return(RetornaItensGrupo(".$cod_grupo."));\">".$num_itens."</a></td>\n");
          else
          echo($num_itens."</td>\n");
        }
        else
        {
          // Nï¿½ Participou
          echo("                    <td align=\"center\">0</td>\n");
        }

        $foiavaliado=GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo);
        $grupo=( (($dados['Ferramenta']=='P') || ($dados['Ferramenta'] == 'E')|| ($dados['Ferramenta']=='N') ) && ($dados['Tipo'] == 'G'));
        //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $cod_avaliacao, $cod_grupo, $grupo);

        if ($foiavaliado /*&& $dados['Ferramenta']!='E'*/)
        {
          // Modificado. Antes retornava o aluno que mais tem notas no grupo.
          // Agora retorna o cod_aluno de um aluno do grupo que recebeu a ultima avaliacao
          $cod=RetornaCodAlunoMaisNotasnoGrupo($sock,$cod_avaliacao,$cod_grupo);
          $dados_nota=RetornaDadosNotaGrupoStatusF($sock,$cod_grupo,$cod_avaliacao,$usr_formador);

          $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
          $cod_nota=$dados_nota['cod_nota'];
          $nota=$dados_nota['nota'];
          //$nota=RetornaNotaExercicio($sock, $cod_avaliacao, $cod_usuario);

          // booleano que indica se a nota estah vazia
          $nota_vazia = ($nota == '');
          // escrevemos a nota no formato "float", ou seja, com uma casa atras da virgula
          $nota = FormataNota($nota);

          if (!strcmp($tipo_compartilhamento,'T'))
          // 51 - Totalmente Compartilhado
          $compartilhamento=RetornaFraseDaLista($lista_frases,51);
          elseif (!strcmp($tipo_compartilhamento,'G'))
          // 53 - Compartilhado com Formadores e com o Grupo
          $compartilhamento=RetornaFraseDaLista($lista_frases,53);
          else
          // 52 - Compartilhado com Formadores
          $compartilhamento=RetornaFraseDaLista($lista_frases,52);
          // Notas
          if ($nota_vazia)
          {
            // coluna de nota
            echo("                    <td align=\"center\">");
            if ($usr_formador && !$SalvarEmArquivo)
            echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolioGrupo(".$cod_grupo.",'tr_grupos_".$cod_grupo."'));\">&nbsp;</span></td>\n");
            else
            echo("&nbsp;</td>\n");

            // coluna da data da avaliaï¿½o
            echo("                    <td align=\"center\">");
            echo("&nbsp;</td>\n");

            if ($usr_formador)
            {
              // coluna de avaliacao
              if (!$SalvarEmArquivo)
              {
                if (! is_numeric($cod))
                {
                  $cod = 0;
                }

                if($avaliacao_participante)
                {
                  echo("                    <td align=\"center\">");
                  if($portfolio_grupo)
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  else
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  // 65 - Avaliar
                  echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                }
              }
              else
              {
                echo("                    <td align=\"center\">");
                echo("&nbsp;</td>\n");
              }
              // coluna do compartilhamento
              echo("                    <td align=\"center\"><span>&nbsp</span></td>\n");
            }
          }
          else
          {
            $marcaib="";
            $marcafb="";
            echo("                    <td align=center>");
            if (!$SalvarEmArquivo)
            {
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolioGrupo(".$cod_grupo.",'tr_grupos_".$cod_grupo."'));\">");

              echo($nota."</span></td>\n");

            }
            else
            echo($nota."</td>\n");

            // coluna da data da avaliaï¿½o
            echo("                    <td align=\"center\">");
            echo(UnixTime2Data($dados_nota['data'])."</td>\n");

            if ($usr_formador)
            {
              //coluna de avaliacao
              // 66 - Reavaliar
              if (!$SalvarEmArquivo)
              {
                if (! is_numeric($cod))
                {
                  $cod = 0;
                }
                if($avaliacao_participante)
                {
                  echo("                    <td align=\"center\">");
                  if($portfolio_grupo)
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  else
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  echo($textoReavaliarOuJustificar."</span></td>\n");
                }
              }
              else if($avaliacao_participante)
              {
                echo("                   <td align=\"center\">");
                // 66 - Reavaliar
                echo($textoReavaliarOuJustificar."</td>\n");
              }
              if (!$SalvarEmArquivo)
              $compartilhamento=$marcaib."<span name=\"comp_".$cod_nota."\" class=\"link\" onMouseDown=\"js_cod_nota=".$cod_nota.";js_cod_grupo='".$cod_grupo."';js_cod_aluno='".$cod."';AtualizaComp('".$tipo_compartilhamento."','comp_".$cod_nota."');MostraLayer(cod_comp,".$ajuste.");return(false);\">".$compartilhamento."</span>".$marcafb;
              echo("                    <td align=\"center\">".$compartilhamento."</td>\n");
            }
          }
        }
        else // nenhuma nota foi atribuida
        {
          $cod=RetornaCodAlunodoGrupo($sock,$cod_avaliacao,$cod_grupo);
          //coluna de nota
          echo("                    <td align=\"center\">");
          if ($usr_formador && !$SalvarEmArquivo)
          echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolioGrupo(".$cod_grupo.",'tr_grupos_".$cod_grupo."'));\">&nbsp;</span></td>\n");
          else
          echo("&nbsp;</td>\n");

          //coluna da data da avaliaï¿½o
          echo("                    <td align=\"center\">");
          echo("&nbsp;</td>\n");

          if ($usr_formador)
          {
            //coluna de avaliacao
            if (!$SalvarEmArquivo)
            {
              if (! is_numeric($cod))
              {
                $cod = 0;
              }
              if($avaliacao_participante)
              {
                if($portfolio_grupo)
                echo("                    <td align=\"center\"><span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                else
                echo("                    <td align=\"center\"><span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                // 65 - Avaliar
                echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                echo("                    <td align=\"center\">");
                echo("<span></span></td>\n");
              }
            }
            else
            {
              // coluna 'avaliar'
              echo("                    <td align=\"center\">&nbsp;</td>\n");
              // coluna do compartilhamento
              echo("                    <td align=\"center\"><span>&nbs</span></td>\n");
            }
          }
        }
        echo("                  </tr>\n");
      }
      //       echo("</table>\n");
    }
    else
    {
      // 77 - Nï¿½ hï¿½grupos criados
      echo("                  <tr>\n");
      echo("                    <td>".RetornaFraseDaLista($lista_frases,77)."</td>\n");
      echo("                  </tr>\n");
    }
  }
  elseif(($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G'))
  {
    // avaliacao de exercicio em grupo
    $lista_grupos=RetornaListaGrupos($sock);
    $num_grupos=count($lista_grupos);
    if ($num_grupos > 0)
    {
      //Tabela com a lista de alunos do curso, com suas respectivas notas na avaliaï¿½o realizada
      echo ("                 <tr class=\"head\">\n");
      // 48 - Grupo
      echo("                    <td align=\"center\" width=\"25%\">".RetornaFraseDaLista($lista_frases,48)."</td>\n");
      // 49 - Participacoes
      echo("                    <td align=\"center\" width=\"12%\">".RetornaFraseDaLista($lista_frases,59)."</td>\n");
      // 60 - Nota
      echo("                    <td align=\"center\" width=\"11%\">".RetornaFraseDaLista($lista_frases,60)."</td>\n");
      // 61 - Data da Avaliaï¿½o
      echo("                    <td align=\"center\" width=\"11%\">".RetornaFraseDaLista($lista_frases,61)."</td>\n");
      if ($usr_formador)
      {
        if($avaliacao_participante)
        {
          // 62 - Avaliar/Reavaliar
          echo("                    <td align=\"center\" width=\"21%\">".$textoHead."</td>\n");
        }
        // 63 - Compartilhamento
        echo("                    <td align=\"center\" width=\"20%\">".RetornaFraseDaLista($lista_frases,63)."</td>\n");
      }
      echo("                  </tr>\n");
      foreach ($lista_grupos as $cod_grupo => $nome)
      {
        echo("                  <tr id=\"tr_grupos_".$cod_grupo."\">\n");
        echo("                    <td>");
        if (!$SalvarEmArquivo)
        echo("<span class=\"link\" onClick=\"return(AbreJanelaComponentes(".$cod_grupo.",".$cod_avaliacao."));\">".$nome."</span></td>");
        else
        echo($nome."</td>\n");

        // retorna o codigo de um aluno que tem mais notas no grupo (caso aconteca) para garantir que retorne todas as avaliaï¿½ï¿½es.
        // Isso ï¿½ necessario porque alguns alunos podem ser inseridos no grupo depois que algumas avaliaï¿½ï¿½es ja foram feitas para este grupo
        // E sempre que avalia um grupo, todos os alunos do grupo recebem a mesma avaliaï¿½o
        $num_itens=RetornaExercicioResolvido($sock,$cod_avaliacao,$cod_grupo);

        if ($num_itens > 0)
        {
          echo("                    <td align=\"center\">");
          if (!$SalvarEmArquivo)
          {
            $cod_resolucao = RetornaCodResolucaoExercicio($sock, $cod_avaliacao, $cod_grupo);
            echo("<a href=\"#\" onClick=\"return(VerExercicio(0,".$cod_resolucao.",".$cod_grupo."));\">".$num_itens."</a></td>\n");
          }
          else
          echo($num_itens."</td>\n");
        }
        else
        {
          // Nï¿½ Participou
          echo("                    <td align=\"center\">0</td>\n");
        }

        $foiavaliado=GrupoFoiAvaliado($sock,$cod_avaliacao,$cod_grupo);
        $grupo=(($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G'));

        if ($foiavaliado)
        {
          $dados_nota=RetornaDadosNotaGrupoStatusF($sock, $cod_grupo, $cod_avaliacao,$usr_formador);

          $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
          $cod_nota=$dados_nota['cod_nota'];

          $nota=RetornaNotaExercicioGrupo($sock, $cod_avaliacao, $cod_grupo);

          // booleano que indica se a nota estah vazia
          $nota_vazia = ($nota == '');
          // escrevemos a nota no formato "float", ou seja, com uma casa atras da virgula
          $nota = FormataNota($nota);

          if (!strcmp($tipo_compartilhamento,'T'))
          // 51 - Totalmente Compartilhado
          $compartilhamento=RetornaFraseDaLista($lista_frases,51);
          elseif (!strcmp($tipo_compartilhamento,'G'))
          // 53 - Compartilhado com Formadores e com o Grupo
          $compartilhamento=RetornaFraseDaLista($lista_frases,53);
          else
          // 52 - Compartilhado com Formadores
          $compartilhamento=RetornaFraseDaLista($lista_frases,52);
          // Notas
          if ($nota_vazia)
          {
            // coluna de nota
            echo("                    <td align=\"center\">");
            if($usr_formador && !$SalvarEmArquivo)
            echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolioGrupo(".$cod_grupo.",'tr_grupos_".$cod_grupo."'));\"></span></td>");
            else
            echo("&nbsp;</td>\n");

            // coluna da data da avaliaï¿½o
            echo("                    <td class=\"text\" align=\"center\">");
            echo("&nbsp;</td>\n");

            if ($usr_formador)
            {
              // coluna de avaliacao
              if (!$SalvarEmArquivo)
              {
                if (! is_numeric($cod))
                {
                  $cod = 0;
                }

                if($avaliacao_participante)
                {
                  echo("                    <td align=\"center\">");
                  if($portfolio_grupo)
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  else
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  // 65 - Avaliar
                  echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                }
              }
              else
              {
                echo("                    <td align=\"center\">");
                echo("&nbsp;</td>\n");
              }
              // coluna do compartilhamento
              echo("                    <td align=\"center\"><span>&nbsp;</span></td>\n");
            }
          }
          else
          {
            $marcaib="";
            $marcafb="";
            echo("                    <td align=\"center\">");
            if (!$SalvarEmArquivo)
            {
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolioGrupo(".$cod_grupo.",'tr_grupos_".$cod_grupo."'));\">");

              /*if($dados['Ferramenta']=='E')
               echo($nota."<font class=\"text\"><sup>*C</sup></font></span></td>\n");
               else*/
              echo($nota."</span></td>\n");

            }
            else
            echo($nota."</td>\n");

            // coluna da data da avaliaï¿½o
            echo("                    <td align=\"center\">");
            echo(UnixTime2Data($dados_nota['data'])."</td>\n");

            if ($usr_formador)
            {
              //coluna de avaliacao
              // 66 - Reavaliar
              if (!$SalvarEmArquivo)
              {
                if (! is_numeric($cod))
                {
                  $cod = 0;
                }
                if($avaliacao_participante)
                {
                  echo("                    <td align=\"center\">");
                  if($portfolio_grupo)
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  else
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                  echo($textoReavaliarOuJustificar."</span></td>\n");
                }
              }
              else if($avaliacao_participante)
              {
                echo("                   <td align=center>");
                // 66 - Reavaliar
                echo($textoReavaliarOuJustificar."</td>\n");
              }
              if (!$SalvarEmArquivo)
              $compartilhamento=$marcaib."<span name=\"comp_".$cod_nota."\" class=\"link\" onMouseDown=\"js_cod_nota=".$cod_nota.";js_cod_grupo='".$cod_grupo."';js_cod_aluno='".$cod."';AtualizaComp('".$tipo_compartilhamento."','comp_".$cod_nota."');MostraLayer(cod_comp,".$ajuste.");return(false);\">".$compartilhamento."</span>".$marcafb;
              echo("                    <td align=center>".$compartilhamento."</td>\n");
            }
          }
        }
        else // nenhuma nota foi atribuida
        {
          $cod=RetornaCodAlunodoGrupo($sock,$cod_avaliacao,$cod_grupo);
          //coluna de nota
          echo("                    <td align=center>");
          if($usr_formador && !$SalvarEmArquivo)
          echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolioGrupo(".$cod_grupo.",'tr_grupos_".$cod_grupo."'));\"></span></td>");
          else
          echo("&nbsp;</td>\n");

          //coluna da data da avaliaï¿½o
          echo("                    <td align=center>");
          echo("&nbsp;</td>\n");

          if ($usr_formador)
          {
            //coluna de avaliacao
            if (!$SalvarEmArquivo)
            {
              if (! is_numeric($cod))
              {
                $cod = 0;
              }
              if($avaliacao_participante)
              {
                if($portfolio_grupo)
                echo("                    <td align=center><span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                else
                echo("                    <td align=center><span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_grupos_".$cod_grupo."'));\">");
                // 65 - Avaliar
                echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
              }
              echo("                    <td align=center>");
              echo("<span></span></td>\n");
            }
            else
            {
              // coluna 'avaliar'
              if($avaliacao_participante)
              echo("                    <td align=center>&nbsp;</td>\n");
              // coluna do compartilhamento
              echo("                    <td align=center><span>&nbsp;</span></td>\n");
            }
          }
        }
        echo("                  </tr>\n");
      }
      //       echo("</table>\n");
    }
    else
    {
      // 77 - Nï¿½ hï¿½grupos criados
      echo("                  <tr>\n");
      echo("                    <td>".RetornaFraseDaLista($lista_frases,77)."</td>\n");
      echo("                  </tr>\n");
    }
  }
  else
  {
    // nï¿½ ï¿½portfolio de grupo
    $lista_users=RetornaListaUsuariosAluno($cod_curso);
    $sock = MudarDB($sock, $cod_curso);
    if (count($lista_users) > 0)
    {
      // Tabela com a lista de alunos do curso, com suas respectivas notas na avaliaï¿½o realizada
      echo("                  <tr class=\"head\">\n");
      // 64 - Alunos
      echo("                    <td align=center width=25%>".RetornaFraseDaLista($lista_frases,64)."</td>\n");

      // 49 - Participaï¿½es
      echo("                    <td align=center width=12%>".RetornaFraseDaLista($lista_frases,49)."</td>\n");
      // 60 - Nota
      echo("                    <td align=center width=11%>".RetornaFraseDaLista($lista_frases,60)."</td>\n");
      // 61 - Data da Avaliaï¿½o
      echo("                    <td align=center width=11%>".RetornaFraseDaLista($lista_frases,61)."</td>\n");

      if ($usr_formador)
      {
        if($avaliacao_participante)
        /* 62 - Avaliar/Reavaliar */
        echo("                    <td align=center width=21%><b> ".$textoHead."</td>\n");
        /* 63 - Compartilhamento */
        echo("                    <td align=center width=20%><b> ".RetornaFraseDaLista($lista_frases,63)."</td>\n");
      }

      echo("                  </tr>\n");

      foreach($lista_users as $cod => $nome)
      {
        $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod);
        $grupo=(($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G'));
        //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $cod_avaliacao, $cod, $grupo);

        echo("                  <tr id=\"tr_users_".$cod."\">\n");
        // coluna com o nome do participante
        echo("                    <td>");
        if (!$SalvarEmArquivo)
        echo("<span class=\"link text\" onClick=\"return(AbrePerfil(".$cod."));\">".$nome."</span></td>\n");
        else
        echo($nome."</td>\n");

        // coluna com qtde de participacoes
        if (!strcmp($dados['Ferramenta'],'B'))
        {
          if ($batepapo_nao_existe)
          {
            echo("                    <td align=center>0</td>\n");
          }
          else
          {
            if (ParticipouDaSessao($sock,$cod,$dados['Cod_atividade']))
            {
              if ((int)$msgs_total[$cod]==0)
              echo("                    <td align=center>0</td>\n");
              else
              {
                echo("                    <td align=center>");
                if (!$SalvarEmArquivo)
                echo("      <a href=\"#\" onClick=\"return(RetornaFalasAluno(".$cod."));\">".(int)$msgs_total[$cod]."</a></td>\n");
                else
                echo((int)$msgs_total[$cod]."</td>\n");
              }
            }
            else
            {
              /* Nï¿½ Participou */
              echo("                    <td align=center>0</td>\n");
            }
          }
        }
        elseif($dados['Ferramenta']=='N')
        {
          echo("                   <td align=center>-</td>\n");
        }
        elseif (!strcmp($dados['Ferramenta'],'F'))
        {
          if (ParticipouDoForum($sock,$cod,$dados['Cod_atividade']))
          {
            $num_mensagens = RetornaNumMsgsParticipantesForum($sock,$dados['Cod_atividade'],$cod);
            echo("                    <td align=center>");
            if (!$SalvarEmArquivo)
            echo("<a href=\"#\" onClick=\"return(RetornaMensagensAluno(".$cod."));\">".$num_mensagens."</a></td>\n");
            else
            echo($num_mensagens."</td>\n");
          }
          else
          /* Nï¿½ Participou */
          echo("                    <td align=center>0</td>\n");
        }
        elseif(!strcmp($dados['Ferramenta'],'E'))
        {
          $exercicio=RespondeuExercicio($sock,$cod_avaliacao,$cod,$exercicio_grupo);
          if($exercicio['cod_exercicio']!=0)
          {
        if (!$SalvarEmArquivo)
        echo("                   <td align=center><a href=\"#\" onClick=\"return(VerExercicio(".$exercicio['cod_exercicio'].",".$exercicio['cod_resolucao'].",".$cod."));\">1</td>\n");
        else
        echo("                   <td align=center>1</td>\n");
          }
          else
          echo("                   <td align=center>0</td>\n");
        }
        else
        {
          /* Verificando se o usuario fez e compartilhou a atividade no portifolio */
          $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod);
          if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao, $cod,$portfolio_grupo) && $num_itens > 0 && $num_itens != NULL)
          {

            echo("                    <td align=center>");
            if (!$SalvarEmArquivo)
            echo("<a href=\"#\" onClick=\"return(RetornaItensAluno(".$cod."));\">".$num_itens."</a></td>\n");
            else
            echo($num_itens."</td>\n");
          }
          else
          {
            /* Nï¿½ Pairticipou */
            echo("                    <td align=center>0</td>\n");
          }
        }

        // coluna com a nota, coluna com data da avaliaï¿½o, coluna com opcao avaliar e coluna do compartilhamento
        //if ($foiavaliado )             //Ja existe uma nota atribuida

        if ($foiavaliado /*&& $dados['Ferramenta']!='E'*/)
        {
          $dados_nota=RetornaDadosNota($sock, $cod, $cod_avaliacao, $cod_usuario, $usr_formador);
          $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
          $cod_nota=$dados_nota['cod_nota'];
          //$nota=RetornaNotaExercicio($sock, $cod_avaliacao, $cod_usuario);
          $nota=$dados_nota['nota'];

          // booleano que indica se a nota estah vazia
          $nota_vazia = ($nota == '');
          // escrevemos a nota no formato "float", ou seja, com uma casa atras da virgula
          $nota = FormataNota($nota);

          if (!strcmp($tipo_compartilhamento,'T'))
          // 51 - Totalmente Compartilhado
          $compartilhamento=RetornaFraseDaLista($lista_frases,51);
          elseif (!strcmp($tipo_compartilhamento,'A'))
          // 54 - Compartilhado com Formadores e com o Participante
          $compartilhamento=RetornaFraseDaLista($lista_frases,54);
          else
          // 52 - Compartilhado com Formadores
          $compartilhamento=RetornaFraseDaLista($lista_frases,52);
          //Notas
          if ($nota_vazia)
          {
            //coluna de nota
            echo("                  <td align=center>");
            if($usr_formador && !$SalvarEmArquivo)
            {
              if (strcmp($dados['Ferramenta'],'P'))
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenho(".$cod.",'tr_users_".$cod."'));\">&nbsp;</span></td>\n");
              else
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",'tr_users_".$cod."'));\">&nbsp;</span></td>\n");
            }
            else
            echo("&nbsp;</td>\n");

            //coluna da data da avaliaï¿½o
            echo("                  <td align=center>&nbsp;</td>\n");

            if ($usr_formador)
            {
              //coluna de com link para avaliar / reavaliar
              if (!$SalvarEmArquivo)
              {
                if ((!strcmp($dados['Ferramenta'],'P'))&&(!strcmp($dados['Ferramenta'],'E')))
                {
                  if (! is_numeric($cod))
                  {
                    $cod = 0;
                  }
                  echo("                   <td align=center>");
                  if($portfolio_grupo)
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                  else
                  echo("<span class=\"link\" onClick=\"return(AvaliarAluno(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                  echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                }
                else if($avaliacao_participante)
                {
                  echo("                   <td align=center>");
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                  // 65 - Avaliar
                  echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                }
              }
              else
              echo("                    <td align=center>&nbsp;</td>\n");

              // coluna do compartilhamento
              echo("                    <td align=center><span>&nbsp;</span></td>\n");
            }
          }
          else
          {
            $marcaib="";
            $marcafb="";
            echo("                    <td align=center>");
            if (!$SalvarEmArquivo)
            {
              if (strcmp($dados['Ferramenta'],'P'))
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenho(".$cod.",'tr_users_".$cod."'));\">");
              else
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",'tr_users_".$cod."'));\">");

              /*if($dados['Ferramenta']=='E')
               echo($nota."<font><sup>*C</sup></font></span></td>\n");
               else*/
              echo($nota."</span></td>\n");
            }
            else
            echo($nota."</td>\n");

            // coluna da data da avaliaï¿½o
            echo("                    <td align=center>".UnixTime2Data($dados_nota['data'])."</td>\n");

            if ($usr_formador)
            {
              // coluna de avaliar / reavaliar
              if (!$SalvarEmArquivo)
              {
                if (!strcmp($dados['Ferramenta'],'P'))
                {
                  if (! is_numeric($cod))
                  {
                    $cod = 0;
                  }
                  echo("                    <td align=\"center\">");
                  if($portfolio_grupo)
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                  else
                  echo("<span class=\"link\" onClick=\"return(AvaliarAluno(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                if($exercicio['Ferramenta']=='E'){
                    echo($textoReavaliarOuJustificar."</span></td>\n");
                  }
                  else{
                    echo($textoReavaliarOuJustificar."</span></td>\n");
                  }
                }
                else if($avaliacao_participante)
                {
                  echo("                    <td align=\"center\">");
                  echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                  // 66 - Reavaliar
                  echo($textoReavaliarOuJustificar."</span></td>\n");
                }
              }
              else
              echo("&nbsp;</td>\n");

              // coluna do compartilhamento
              if (!$SalvarEmArquivo)
                $compartilhamento=$marcaib."<span name=\"comp_".$cod_nota."\" class=\"link\" onMouseDown=\"js_cod_nota=".$cod_nota.";js_cod_grupo='".$cod_grupo."';js_cod_aluno='".$cod."';AtualizaComp('".$tipo_compartilhamento."','comp_".$cod_nota."');MostraLayer(cod_comp,".$ajuste.");return(false);\">".$compartilhamento."</span>".$marcafb;
              echo("                    <td align=center>".$compartilhamento."</td>\n");
            }
          }
        }
        else // nenhuma nota foi atribuida
        {
          // coluna de nota
          echo("                    <td align=center>");
          if($usr_formador && !$SalvarEmArquivo)
          {
            if (strcmp($dados['Ferramenta'],'P'))
            echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenho(".$cod.",'tr_users_".$cod."'));\">&nbsp;</span></td>\n");
            else
            echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",'tr_users_".$cod."'));\">&nbsp;</span></td>\n");
          }
          else
          echo("&nbsp;</td>\n");

          // coluna da data da avaliaï¿½o
          echo("                    <td align=center>&nbsp;</td>\n");

          if ($usr_formador)
          {
            // coluna de avaliacao
            if (!$SalvarEmArquivo)
            {
              if (!strcmp($dados['Ferramenta'],'P'))
              {
                if (! is_numeric($cod))
                {
                  $cod = 0;
                }
                if($portfolio_grupo)
                echo("                   <td align=center><span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                else
                echo("                   <td align=center><span class=\"link\" onClick=\"return(AvaliarAluno(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
              }
              else if($avaliacao_participante)
              {
                echo("                    <td align=center><span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_users_".$cod."'));\">");
                // 65 - Avaliar
                echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
              }
              // coluna do compartilhamento
              echo("                    <td align=center><span>&nbsp;</span></td>\n");
            }
            else
            {
              // coluna 'avaliar'
              echo("                    <td align=center>&nbsp;</td>\n");
              // coluna do compartilhamento
              echo("                    <td align=center><span>&nbsp;</span></td>\n");
            }
          }
        }
        echo("                  </tr>\n");
      }
    }

    if ($usr_formador)
    {
      $lista_users_formador=RetornaListaUsuariosFormador($cod_curso);
      $sock = MudarDB($sock, $cod_curso);

      if (count($lista_users_formador) > 0)
      {
        // Tabela com a lista de formadores do curso, com suas respectivas notas na avaliaï¿½o realizada
        echo ("                <tr class=\"head\">\n");
        // 156 - Formadores
        echo("                    <td align=center width=25%>".RetornaFraseDaLista($lista_frases,156)."</td>\n");

        // 59 - Participaï¿½es
        echo("                    <td align=center width=12%><b> ".RetornaFraseDaLista($lista_frases,49)."</b></td>\n");
        // 60 - Nota
        echo("                    <td align=center width=11%><b> ".RetornaFraseDaLista($lista_frases,60)."</b></td>\n");
        // 61 - Data da Avaliaï¿½o
        echo("                    <td align=center width=11%><b> ".RetornaFraseDaLista($lista_frases,61)."</b></td>\n");
        if ($usr_formador)
        {
          if($avaliacao_participante)
          echo("                    <td align=center width=21%><b> ".$textoHead."</b></td>\n");
          // 63 - Compartilhamento
          echo("                    <td align=center width=20%><b> ".RetornaFraseDaLista($lista_frases,63)."</b></td>\n");
        }

        echo("                  </tr>\n");

        foreach($lista_users_formador as $cod => $nome)
        {
          $foiavaliado=FoiAvaliado($sock,$cod_avaliacao,$cod);
          $grupo=(
          (($dados['Ferramenta'] == 'E') && ($dados['Tipo'] == 'G')) || ($dados['Ferramenta']=='N' && $dados['Tipo']=='G'));
          //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $cod_avaliacao, $cod, $grupo);

          echo("                  <tr id=\"tr_formadores_".$cod."\">\n");
          echo("                    <td >");
          if (!$SalvarEmArquivo)
          echo("<span class=\"link text\" onClick=\"return(AbrePerfil(".$cod."));\">".$nome."</span></td>\n");
          else
          echo($nome."</td>\n");

          if (!strcmp($dados['Ferramenta'],'B'))
          {
            if ($batepapo_nao_existe)
            {
              echo("                    <td align=center>0</td>\n");
            }
            else
            {
              if (ParticipouDaSessao($sock,$cod,$dados['Cod_atividade']))
              {
                if ((int)$msgs_total[$cod]==0)
                echo("                    <td align=center>0</td>\n");
                else
                {
                  echo("                    <td align=center>");
                  if (!$SalvarEmArquivo)
                  echo("<a href=\"#\" onClick=\"return(RetornaFalasAluno(".$cod."));\">".(int)$msgs_total[$cod]."</a></td>\n");
                  else
                  echo((int)$msgs_total[$cod]."</td>\n");
                }
              }
              else
              // Nï¿½ Participou
              echo("                    <td align=center>0</td>\n");
            }
          }
          elseif($dados['Ferramenta']=='N')
          {
            echo("                    <td align=center>-</td>\n");
          }

          elseif(!strcmp($dados['Ferramenta'],'E'))
          {
            $exercicio=RespondeuExercicio($sock,$cod_avaliacao,$cod,$exercicio_grupo);
            if($exercicio['cod_exercicio']!=0)
            {
              if (!$SalvarEmArquivo)
              echo("                    <td align=center><a href=\"#\" onClick=\"return(VerExercicio(".$exercicio['cod_exercicio'].",".$exercicio['cod_resolucao'].",".$cod."));\">1</td>\n");
              else
              echo("                    <td align=center>1</td>\n");
            }
            else
            echo("                   <td align=center>0</td>\n");
          }
          elseif (!strcmp($dados['Ferramenta'],'F'))
          {
            if (ParticipouDoForum($sock,$cod,$dados['Cod_atividade']))
            {
              $num_mensagens=RetornaNumMsgsParticipantesForum($sock,$dados['Cod_atividade'],$cod);
              echo("                    <td align=center>");
              if (!$SalvarEmArquivo)
              echo("<a href=\"#\" onClick=\"return(RetornaMensagensAluno(".$cod."));\">".$num_mensagens."</a></td>\n");
              else
              echo($num_mensagens."</td>\n");
            }
            else
            //  Nï¿½ Participou
            echo("                    <td align=center>0</td>\n");
          }
          else
          {
            /* Verificando se o usuario fez e compartilhou a atividade no portifolio */
            $num_itens=RetornaNumItensPortfolioAvaliacao($sock,$cod,$cod_avaliacao,$portfolio_grupo,$cod_usuario,$usr_formador,$cod);
            if (RealizouAtividadeNoPortfolio($sock,$cod_avaliacao, $cod,$portfolio_grupo) && $num_itens > 0 && $num_itens != NULL)
            {
              echo("                    <td align=center>");
              if (!$SalvarEmArquivo)
              echo("<a href=\"#\" onClick=return(RetornaItensAluno(".$cod."));>".$num_itens."</a></td>\n");
              else
              echo($num_itens."</td>\n");
            }//tipo_comp
            else
            {
              // Nï¿½ Participou
              echo("                    <td align=center>"."0"."</td>\n");
            }
          }

          if ($foiavaliado /*&& $dados['Ferramenta']!='E'*/)
          {
            $dados_nota=RetornaDadosNota($sock, $cod, $cod_avaliacao,$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=$dados_nota['nota'];
            //$nota=RetornaNotaExercicio($sock, $cod_avaliacao, $cod_usuario);

            // booleano que indica se a nota estah vazia
            $nota_vazia = ($nota == '');
            // escrevemos a nota no formato "float", ou seja, com uma casa atras da virgula
            $nota = FormataNota($nota);

            if (!strcmp($tipo_compartilhamento,'T'))
            // 51 - Totalmente Compartilhado
            $compartilhamento=RetornaFraseDaLista($lista_frases,51);
            elseif (!strcmp($tipo_compartilhamento,'A'))
            // 54 - Compartilhado com Formadores e com o Participante
            $compartilhamento=RetornaFraseDaLista($lista_frases,54);
            else
            // 52 - Compartilhado com Formadores
            $compartilhamento=RetornaFraseDaLista($lista_frases,52);
            // Notas
            if ($nota_vazia)
            {
              //coluna de nota
              echo("                    <td align=center>");
              if ($usr_formador && !$SalvarEmArquivo)
              {
                if (strcmp($dados['Ferramenta'],'P'))
                echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenho(".$cod.",'tr_formadores_".$cod."'));\">");
                else
                echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",'tr_formadores_".$cod."'));\">");
                echo("&nbsp;</span></td>\n");
              }
              else
              echo("&nbsp;</td>\n");

              //coluna da data da avaliaï¿½o
              echo("                    <td align=center>");
              echo("&nbsp;</td>\n");

              if ($usr_formador)
              {
                //coluna de avaliacao
                if (!$SalvarEmArquivo)
                {
                  if (!strcmp($dados['Ferramenta'],'P'))
                  {
                    if (! is_numeric($cod))
                    {
                      $cod = 0;
                    }
                    echo("                    <td align=center>");
                    if($portfolio_grupo)
                    echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                    else
                    echo("<span class=\"link\" onClick=\"return(AvaliarAluno(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                    echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                  }
                  else if($avaliacao_participante)
                  {
                    echo("                    <td align=center>");
                    echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                    // 65 - Avaliar
                    echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                  }
                }
                else
                {
                  echo("                    <td align=center>");
                  echo("&nbsp;</td>\n");
                }
                // coluna do compartilhamento
                echo("                    <td align=center><span>&nbsp;</span></td>\n");
              }
            }
            else
            {
              $marcaib="";
              $marcafb="";
              echo("                    <td align=center>");
              if (!$SalvarEmArquivo)
              {
                if (strcmp($dados['Ferramenta'],'P'))
                echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenho(".$cod.",'tr_formadores_".$cod."'));\">");
                else
                echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",'tr_formadores_".$cod."'));\">");
                /*if($dados['Ferramenta']=='E')
                 echo($nota."<font class=\"text\"><sup>*C</sup></font></span></td>\n");
                 else*/
                echo($nota."</span></td>\n");

              }
              else
              echo($nota."</td>\n");

              //coluna da data da avaliaï¿½o
              echo("                    <td align=center>");
              echo(UnixTime2Data($dados_nota['data'])."</td>\n");

              if ($usr_formador)
              {
                //coluna de avaliacao
                if (!$SalvarEmArquivo)
                {
                  if (!strcmp($dados['Ferramenta'],'P'))
                  {
                    if (! is_numeric($cod))
                    {
                      $cod = 0;
                    }
                    echo("                    <td align=\"center\">");
                    if($portfolio_grupo)
                    echo("<span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                    else
                    echo("<span class=\"link\" onClick=\"return(AvaliarAluno(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                    echo($textoReavaliarOuJustificar."</span></td>\n");
                  }
                  else if($avaliacao_participante)
                  {
                    echo("                    <td align=\"center\">");
                    echo("<span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                    // 66 - Reavaliar
                    echo($textoReavaliarOuJustificar."</span></td>\n");
                  }
                }
                else
                {
                  echo("                    <td align=\"center\">");
                  echo("&nbsp;</td>\n");
                }
                if (!$SalvarEmArquivo)
                $compartilhamento=$marcaib."<span name=\"comp_".$cod_nota."\" class=\"link\" onMouseDown=\"js_cod_nota=".$cod_nota.";js_cod_grupo='".$cod_grupo."';js_cod_aluno='".$cod."';AtualizaComp('".$tipo_compartilhamento."','comp_".$cod_nota."');MostraLayer(cod_comp,".$ajuste.");return(false);\">".$compartilhamento."</span>".$marcafb;
                // coluna do compartilhamento
                echo("                    <td align=\"center\">".$compartilhamento."</td>\n");
              }
            }
          }
          else // nenhuma nota foi atribuida
          {
            //coluna de nota
            echo("                    <td align=\"center\">");
            if ($usr_formador && !$SalvarEmArquivo)
            {
              if (strcmp($dados['Ferramenta'],'P'))
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenho(".$cod.",'tr_formadores_".$cod."'));\">");
              else
              echo("<span class=\"link\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",'tr_formadores_".$cod."'));\">");
              echo("&nbsp;</span></td>\n");
            }
            else
            echo("&nbsp;</td>\n");

            //coluna da data da avaliaï¿½o
            echo("                    <td align=\"center\">");
            echo("&nbsp;</td>\n");

            if ($usr_formador)
            {
              //coluna de avaliacao
              if (!$SalvarEmArquivo)
              {
                if (!strcmp($dados['Ferramenta'],'P'))
                {
                  if (! is_numeric($cod))
                  {
                    $cod = 0;
                  }
                  if($portfolio_grupo)
                  echo("                   <td align=\"center\"><span class=\"link\" onClick=\"return(AvaliarAlunoGrupo(".$cod.",".$cod_grupo.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                  else
                  echo("                   <td align=\"center\"><span class=\"link\" onClick=\"return(AvaliarAluno(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                  echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                }
                else if($avaliacao_participante)
                {
                  echo("                    <td align=\"center\"><span class=\"link\" onClick=\"return(AvaliarAlunoPortfolio(".$cod.",'".VerificaStringQuery(trim($nome))."','tr_formadores_".$cod."'));\">");
                  // 65 - Avaliar
                  echo(RetornaFraseDaLista($lista_frases,65)."</span></td>\n");
                }
                echo("                    <td align=\"center\">");
                echo("<span></span></td>\n");
              }
              else
              {
                // coluna 'avaliar'
                echo("                    <td align=\"center\">&nbsp;</td>\n");
                // coluna do compartilhamento
                echo("                    <td align=\"center\"><span>&nbsp;</span></td>\n");
              }
            }
          }
          echo("                  </tr>\n");
        }
        //         echo("</table>\n");
      }
    }
  }
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");

  if (($usr_formador) && (!$SalvarEmArquivo))
  {
    // Mudar Compartilhamento
    echo("          <div class=\"popup\" id=\"comp\" visibility=hidden onContextMenu='return(false);'>\n");
    echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("            <div class=\"int_popup\">\n");
    echo("            <form name=\"form_comp\" id=\"form_comp\">\n");
    echo("              <input type=\"hidden\" name=\"cod_curso\"       value=\"".$cod_curso."\">\n");
    echo("              <input type=\"hidden\" name=\"cod_nota\"        value=\"\">\n");
    echo("              <input type=\"hidden\" name=\"cod_grupo\"       value=\"\">\n");
    echo("              <input type=\"hidden\" name=\"cod_aluno\"       value=\"\">\n");
    echo("              <input type=\"hidden\" name=\"cod_avaliacao\"   value=\"".$cod_avaliacao."\">\n");
    echo("              <input type=\"hidden\" name=\"portfolio_grupo\" value=\"".$portfolio_grupo."\">\n");
    echo("              <input type=\"hidden\" name=\"spanName\"        value=\"\">\n");
    echo("              <input type=\"hidden\" name=\"tipo_comp\" id=\"tipo_comp\" value=\"\" />\n");
    echo("              <ul class=\"ulPopup\">\n");
    echo("                <li onClick=\"document.getElementById('tipo_comp').value='T'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,51)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
    /* 51 - Totalmente compartilhado */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,51)."</span>\n");
    echo("                </li>\n");
    echo("               <li onClick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), '".RetornaFraseDaLista($lista_frases,52)."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_T\" class=\"check\"></span>\n");
    /* 52 - Compartilhado com formadores */
    echo("                  <span>".RetornaFraseDaLista($lista_frases,52)."</span>\n");
    echo("                </li>\n");

    if ($portfolio_grupo)
    {
      /* 53 - Compartilhado com Formadores e com o Grupo */
      $frase_comp = RetornaFraseDaLista($lista_frases,53);
      $val = 'G';
    }
    else
    {
      /* 54 - Compartilhado com Formadores e com o Participante */
      $frase_comp = RetornaFraseDaLista($lista_frases,54);
      $val = 'A';
    }

    echo("                <li onClick=\"document.getElementById('tipo_comp').value='".$val."'; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), '".$frase_comp."'); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_P\" class=\"check\"></span>\n");
    echo("                  <span>".$frase_comp."</span>\n");
    echo("                </li>\n");
    echo("              </ul>\n");
    echo("            </form>\n");
    echo("            </div>\n");
    echo("          </div>\n");
  }
  include("../tela2.php");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>