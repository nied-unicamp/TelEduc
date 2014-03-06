<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/notas.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/notas.php
  ========================================================== */
  $bibliotecas="../bibliotecas/";
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
  $objAjax->register(XAJAX_FUNCTION,"MostrarAvaliacoesDinamic");
  $objAjax->register(XAJAX_FUNCTION,"ApagarAvalicaoDinamic");
  $objAjax->register(XAJAX_FUNCTION,"MostrarParticipacoesDinamic");
  $objAjax->register(XAJAX_FUNCTION,"GravarExpressaoDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta=22;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda=4;
  include("../topo_tela.php");

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);

  $sock = MudarDB($sock, $cod_curso);
  $media=RetornaInformacoesMedia($sock);
  // No primeiro acesso desta página, não existe nenhuma
  // expressão ou norma para a média salva no banco. É preciso
  // definir uma média default para evitar comportamentos imprevistos.
  if (empty($media)) {
    $media = array (
      'cod_expressao' => null,
      'expressao' => '',
      'norma' => '',
      'tipo_compartilhamento' => 'F',
      'data' => null,
    );
  }
  $lista_avaliacoes=RetornaAvaliacoes($sock,$usr_formador);
  $expressao = "";
  $norma = "";

  if (count($lista_avaliacoes) > 0)
  {
    $expressao = $media['expressao'];	//Expressao
    $norma = $media['norma'];			//Norma
  }

  if (!$SalvarEmArquivo)
  {
    /* Funï¿½ï¿½es JavaScript */
    echo("    <script language=\"javascript\">\n");

    /* Parte redundante */

    $cont_batepapo=1;
    $cont_forum=1;
    $cont_portfolio=1;
    $cont_av_ext=1;
    $cont_exercicio=1;

    if (count($lista_avaliacoes) > 0) {
      /* Itera na lista de avaliacoes para criar as siglas das avaliacoes
       * Exemplo: B1v, B2v, E1v
       */
      foreach ($lista_avaliacoes as $index => $avaliacao) {
          if (!strcmp($avaliacao['Ferramenta'], 'B')) {
             $cont=$cont_batepapo;
             $cont_batepapo++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'F')) {
             $cont=$cont_forum;
             $cont_forum++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'P')) {
             $cont=$cont_portfolio;
             $cont_portfolio++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'E')) {
             $cont=$cont_exercicio;
             $cont_exercicio++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'N')) {
             $cont=$cont_av_ext;
             $cont_av_ext++;
          }

          echo("      var ".$avaliacao['Ferramenta'].$cont."v = new Array();\n");	/* Cria um array para cada avaliacao. */

          //Coloca a primeira linha(cabecalho) das avaliacoes
          echo("     ".$avaliacao['Ferramenta'].$cont."v[0] = ".$avaliacao['Valor'].";\n");

      }
    }

    $lista_users=RetornaListaUsuariosAluno($cod_curso);
    $sock = MudarDB($sock, $cod_curso);
    $j=1;
    $cont_cod_usr=0;
    echo("      var codv = new Array();\n");

    if (count($lista_users)>0) {
    	/* Itera na lista de usuarios do curso. */
      foreach($lista_users as $cod => $nome) {
        echo("      codv[$cont_cod_usr] = ".$cod.";\n");
        $cont_cod_usr++;
        $cont_batepapo=1;
        $cont_forum=1;
        $cont_portfolio=1;
        $cont_av_ext=1;
        $cont_exercicio=1;
        /* Para cada usuario, volta a iterar na lista de avaliacoes para pegar a nota de cada usuario. */
        foreach ($lista_avaliacoes as $cont => $linha) {
          /*******************************************/
          /*******Pega dados do exercicio*************/
          $grupo=(($linha['tipo']=='G') && (($linha['Ferramenta']=='E') || ($linha['Ferramenta']=='N')));
          if ($grupo) {
             $codigo=RetornaCodGrupoPortfolioAvaliacao($sock,$cod,$linha['Cod_avaliacao']);
             if ($codigo) {
                $foiavaliado=GrupoFoiAvaliado($sock,$linha['Cod_avaliacao'],$codigo);
             } else {
                $grupo=0;
                $codigo=$cod;
             }
          } else {
             $codigo=$cod;
             $foiavaliado=FoiAvaliado($sock,$linha['Cod_avaliacao'],$cod);
          }

          //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $linha['Cod_avaliacao'], $codigo, $grupo);
          if ($foiavaliado && $linha['Ferramenta']!='E') {
            $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=FormataNota($dados_nota['nota']);

            if (!strcmp($linha['Ferramenta'], 'B')) {
               $contador=$cont_batepapo;
               $cont_batepapo++;
            } elseif (!strcmp($linha['Ferramenta'], 'F')) {
               $contador=$cont_forum;
               $cont_forum++;
            } elseif (!strcmp($linha['Ferramenta'], 'P')) {
               $contador=$cont_portfolio;
               $cont_portfolio++;
            } elseif (!strcmp($linha['Ferramenta'], 'E')) {
               $contador=$cont_exercicio;
               $cont_exercicio++;
            } elseif (!strcmp($linha['Ferramenta'], 'N')) {
               $contador=$cont_av_ext;
               $cont_av_ext++;
            }

            echo("      ".$linha['Ferramenta'].$contador."v[".$j."] = ".$nota.";\n");

          } elseif($foiavaliado && $linha['Ferramenta']=='E') { //Exercicios
            $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=FormataNota($dados_nota['nota']);

            $contador=$cont_exercicio;
            $cont_exercicio++;

            echo("      ".$linha['Ferramenta'].$contador."v[".$j."] = ".$nota.";\n");

          } else { // nenhuma nota foi atribuida
             //if ( ( $linha['Ferramenta']=='E' && $linha['Data_termino']<=time() ) || ($linha['Ferramenta']=='N' || $linha['Ferramenta']=='P' || $linha['Ferramenta']=='F' || $linha['Ferramenta']=='B') ) {
               if (!strcmp($linha['Ferramenta'], 'B')) {
                  $contador=$cont_batepapo;
                  $cont_batepapo++;
               } elseif (!strcmp($linha['Ferramenta'], 'F')) {
                  $contador=$cont_forum;
                  $cont_forum++;
               } elseif (!strcmp($linha['Ferramenta'], 'P')) {
                  $contador=$cont_portfolio;
                  $cont_portfolio++;
               } elseif (!strcmp($linha['Ferramenta'], 'E')) {
                  $contador=$cont_exercicio;
                  $cont_exercicio++;
               } elseif (!strcmp($linha['Ferramenta'], 'N')) {
                  $contador=$cont_av_ext;
                  $cont_av_ext++;
               }

               echo("     ".$linha['Ferramenta'].$contador."v[".$j."] = 0.00;\n");	/* Se nao tem nota atribuida, deixa zero. */

             //}
          }

        }
        $j++;
      }
    }

    if ($usr_formador) {
      $lista_users=RetornaListaUsuariosFormador($cod_curso);
      $sock = MudarDB($sock, $cod_curso);
      /* Itera na lista de usuarios do curso. */ 
      foreach($lista_users as $cod => $nome) {
        echo("      codv[$cont_cod_usr] = ".$cod.";\n");

        $cont_cod_usr++;
        $cont_batepapo=1;
        $cont_forum=1;
        $cont_portfolio=1;
        $cont_av_ext=1;
        $cont_exercicio=1;

        /* Volta a iterar na lista de avaliacoes de cada usuario. */
        foreach ($lista_avaliacoes as $cont => $linha) {
           $grupo=(($linha['tipo']=='G') && (($linha['Ferramenta']=='E') || ($linha['Ferramenta']=='N')));
           //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $linha['Cod_avaliacao'], $cod, $grupo);

           if ($grupo) {
              $codigo=RetornaCodGrupoPortfolioAvaliacao($sock,$cod,$linha['Cod_avaliacao']);
              if ($codigo) {
                 $foiavaliado=GrupoFoiAvaliado($sock,$linha['Cod_avaliacao'],$codigo);
              } else {
                 $codigo=$cod;
                 $grupo=0;
              }
           } else {
               $codigo=$cod;
               $foiavaliado=FoiAvaliado($sock,$linha['Cod_avaliacao'],$cod);
		   }  
            //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $linha['Cod_avaliacao'], $codigo, $grupo);

         if ( $foiavaliado && $linha['Ferramenta']!='E' ) {
            $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=FormataNota($dados_nota['nota']);

            if (!strcmp($linha['Ferramenta'], 'B')) {
               $contador=$cont_batepapo;
               $cont_batepapo++;
            } elseif (!strcmp($linha['Ferramenta'], 'F')) {
               $contador=$cont_forum;
               $cont_forum++;
            } elseif (!strcmp($linha['Ferramenta'], 'P')) {
               $contador=$cont_portfolio;
               $cont_portfolio++;
            } elseif (!strcmp($linha['Ferramenta'], 'E')) {
               $contador=$cont_exercicio;
               $cont_exercicio++;
            } elseif (!strcmp($linha['Ferramenta'], 'N')) {
               $contador=$cont_av_ext;
               $cont_av_ext++;
            }

            echo("      ".$linha['Ferramenta'].$contador."v[".$j."] = ".$nota.";\n");

         } elseif($foiavaliado && $linha['Ferramenta']=='E') {//Exercï¿½cio
          	$dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=FormataNota($dados_nota['nota']);

            $contador=$cont_exercicio;
            $cont_exercicio++;

            echo("      ".$linha['Ferramenta'].$contador."v[".$j."] = ".$nota.";\n");

          } else { // nenhuma nota foi atribuida
            //if ( ( $linha['Ferramenta']=='E' && $linha['Data_termino']<=time() ) || ($linha['Ferramenta']=='N' || $linha['Ferramenta']=='P' || $linha['Ferramenta']=='F' || $linha['Ferramenta']=='B')) {
              if (!strcmp($linha['Ferramenta'], 'B')) {
                $contador=$cont_batepapo;
                $cont_batepapo++;
              } elseif (!strcmp($linha['Ferramenta'], 'F')) {
                $contador=$cont_forum;
                $cont_forum++;
              } elseif (!strcmp($linha['Ferramenta'], 'P')) {
                $contador=$cont_portfolio;
                $cont_portfolio++;
              } elseif (!strcmp($linha['Ferramenta'], 'E')) {
                $contador=$cont_exercicio;
                $cont_exercicio++;
              } elseif (!strcmp($linha['Ferramenta'], 'N')) {
                $contador=$cont_av_ext;
                $cont_av_ext++;
              }

              echo("      ".$linha['Ferramenta'].$contador."v[".$j."] = 0.00;\n");

            //}
         }

//         if ($j == 1) { 
//           echo("     ".$linha['Ferramenta'].$contador."v[0] = ".$linha['Valor'].";\n");
//         }

        }//Fim: foreach ($lista_avaliacoes as $cont => $linha)
        $j++;
      }//Fim: foreach($lista_users as $cod => $nome)
    }

    $j--;
    /* Fim da parte redundante */

    echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
    echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
    echo("      var Xpos, Ypos;\n");
    echo("      var expressao;\n");
    echo("      var expressao_orginal;\n");
    echo("      var media;\n");
    echo("      var norma;");
    echo("      var ok;\n");
    echo("      var alterandoExp;\n");
    echo("      var js_colSpan = ".count($lista_avaliacoes)."+1;\n");
    echo("      var js_cod_avaliacao;\n");
    echo("      var js_cod_aluno;\n");
    echo("      var js_cod_grupo;\n");
    echo("      var js_cod_nota;\n");
    echo("      var js_tipoComp;\n");
    echo("      var js_arrayConteudo;\n");
    echo("      var js_arrayCab = new Array('".RetornaFraseDaLista($lista_frases,60)."','".RetornaFraseDaLista($lista_frases,61)."','".RetornaFraseDaLista($lista_frases,68)."','".RetornaFraseDaLista($lista_frases,50)."','".RetornaFraseDaLista($lista_frases_geral,1)."');\n");


/*************************************************************************************************************************/

  echo("        function CriarSpanSimples(frase,classe)\n");
  echo("        {\n");
  echo("              newSpan = document.createElement('span');\n");
  echo("              newSpan.setAttribute(\"class\",classe);\n");
  echo("              newSpan.innerHTML = frase;\n");
  echo("              return newSpan;\n");
  echo("        }\n");

  echo("        function AbrePerfilHisDes(id) \n");
  echo("        {\n");
  echo("          var brokenId = id.split(\"_\");\n");
  echo("          AbrePerfil(parseInt(brokenId[0]));\n");
  echo("        }\n");

  if($usr_formador)
  {

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

    echo("        function MudaSpanCompartilhamento(spanName,novoComp,tipoComp,codNota,codGrupo,codAluno)\n");
    echo("        {\n");
    echo("          spanElements = document.getElementsByName(spanName);\n");
    echo("          for(i=0;i<spanElements.length;i++)\n");
    echo("          {\n");
    echo("            spanElements[i].innerHTML = novoComp;\n");
    echo("            spanElements[i].onclick = function() { js_cod_nota=codNota;js_cod_grupo=codGrupo;js_cod_aluno=codAluno;AtualizaComp(tipoComp,spanName);MostraLayer(cod_comp,100); }\n");
    echo("          }\n");
    echo("        }\n");
  }

  if($usr_formador)
  {  
    echo("        function MudaCompartilhamentoHisDes(spanId,codAvaliacao) \n");
    echo("        {\n");
    echo("          var i,spanName,brokenId = spanId.split(\"_\");\n");
    echo("          js_cod_nota = parseInt(brokenId[1]);\n");
    echo("          js_cod_aluno = parseInt(brokenId[3]);\n");
    echo("          js_cod_grupo = parseInt(brokenId[4]);\n");
    echo("          js_cod_avaliacao = codAvaliacao;\n");
    echo("          i = parseInt(brokenId[6]);\n");
    echo("          spanName = 'span_'+codAvaliacao+'_'+js_cod_grupo+'_'+i;\n");
    echo("          AtualizaComp(brokenId[2],spanName);\n");
    echo("          MostraLayer(cod_comp,140);\n");
    echo("        }\n");
  }

  if($usr_formador)
  {
    echo("        function ApagarHisDes(id,trPaiId,codAvaliacao) \n");
    echo("        {\n");
    echo("          var brokenId,codNota,i,codAluno,codGrupo,numTables,tableElements,tableElement,tdElement,arrayId;\n");
    echo("          if(confirm(\"".RetornaFraseDaLista($lista_frases,86)."\\n ".RetornaFraseDaLista($lista_frases,87)."\"))\n");
    echo("          {\n");
    echo("            brokenId = id.split(\"_\");\n");
    echo("            codNota = parseInt(brokenId[0]);\n");
    echo("            i = parseInt(brokenId[1]);\n");
    echo("            codAluno = parseInt(brokenId[2]);\n");
    echo("            codGrupo = parseInt(brokenId[3]);\n");
    echo("            xajax_ApagarAvalicaoDinamic(".$cod_curso.",codAvaliacao,codNota,codAluno,codGrupo,'',trPaiId);");
    echo("            if(codGrupo != -1)\n");
    echo("            {\n");
    echo("              tableElements = document.getElementsByName('table_'+codAvaliacao+'_'+codGrupo+'_'+i);\n");
    echo("              numTables = tableElements.length;\n");
    echo("              arrayId = new Array();\n");
    echo("              for(i=0;i<numTables;i++)\n");
    echo("                arrayId[i] = tableElements[i].id;\n");
    echo("              for(i=0;i<numTables;i++)\n");
    echo("              {\n");
    echo("                tableElement = document.getElementById(arrayId[i]);\n");
    echo("                tdElement = tableElement.parentNode;\n");
    echo("                tdElement.removeChild(tableElement.nextSibling);\n");//remove <br>
    echo("                tdElement.removeChild(tableElement);\n");
    echo("              }\n");
    echo("            }\n");
    echo("            else\n");
    echo("            {\n");
    echo("              tableElement = document.getElementById('table_'+codNota+'_'+i);\n");
    echo("              tdElement = tableElement.parentNode;\n");
    echo("              tdElement.removeChild(tableElement.nextSibling);\n");//remove <br>
    echo("              tdElement.removeChild(tableElement);\n");
    echo("            }\n");
    echo("          }\n");
    echo("        }\n");
  }

  echo("        function CriarBotaoParticipacao(codCurso,codAvaliacao,codAluno,codGrupo) \n");
  echo("        {\n");
  echo("          newUl = document.createElement('ul');\n");
  //echo("          newUl.setAttribute(\"class\",\"btAuxTabs\");\n");
  echo("		  newUl.className=\"btAuxTabs\";");
  echo("          newLi = document.createElement('li');\n");
  // 49 - Participaï¿½ï¿½es
  echo("          newSpan = CriarSpanSimples(\"".RetornaFraseDaLista($lista_frases,49)."\",\"\");\n");
  echo("          newSpan.onclick = function(){ xajax_MostrarParticipacoesDinamic(codCurso,codAvaliacao,codAluno,codGrupo); };\n");
  echo("          newLi.appendChild(newSpan);\n");
  echo("          newUl.appendChild(newLi);\n");
  echo("          return newUl;\n");
  echo("        }\n");

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

  echo("        function DesabilitaTr(id)\n");
  echo("        {\n");
  echo("          trElement = document.getElementById(id);\n");
  echo("          tableElement = trElement.parentNode;\n");
  echo("          tableElement.removeChild(trElement);\n");
  echo("        }\n\n");

  echo("        function HabilitaHistorico(arrayConteudo,tamTabela,id,codAvaliacao,codAluno,codGrupo)\n");
  echo("        {\n");
  echo("          var i,j,codNota,codFormador,tipoComp;\n");
  echo("          if(document.getElementById(id+'_hist') != null)\n");
  echo("            DesabilitaTr(id+'_hist');\n");
  echo("          if(document.getElementById(id+'_hist') == null)\n");
  echo("          {\n");
  echo("            trElement = document.getElementById(id);\n");
  echo("            trElement = trElement.nextSibling;\n");
  echo("            tableElement = trElement.parentNode;\n");
  echo("            newTrConteiner = document.createElement('tr');\n");
  echo("            newTrConteiner.setAttribute('id', id+'_hist');\n");
  //echo("            newTrConteiner.setAttribute(\"class\", \"altColor0\");\n");
  echo("            newTrConteiner.className=\"altColor0\";");
  echo("            newTdConteiner = document.createElement('td');\n");
  echo("            newTdConteiner.colSpan = js_colSpan;\n");
  echo("            newTdConteiner.appendChild(CriarBotaoParticipacao(".$cod_curso.",codAvaliacao,codAluno,codGrupo));\n");
  echo("            newBr = document.createElement('br');\n");
  echo("            newTdConteiner.appendChild(newBr);\n");
  echo("            newBr = document.createElement('br');\n");
  echo("            newTdConteiner.appendChild(newBr);\n");
  echo("            newTdConteiner.appendChild(CriarDivCabecalhoHistoricoDesempenho());\n");
  echo("            newBr = document.createElement('br');\n");
  echo("            newTdConteiner.appendChild(newBr);\n");
  echo("            for(i=0;i<tamTabela;i++)\n");
  echo("            {\n");
  echo("              codFormador = arrayConteudo[i][5];\n");
  echo("              codNota = arrayConteudo[i][6];\n");
  echo("              tipoComp = arrayConteudo[i][7];\n");
  echo("              newTable = document.createElement('table');\n");
  echo("              var tbody = document.createElement('tbody');\n");
  echo("              newTable.width = '100%';\n");
  echo("              newTable.setAttribute(\"id\", 'table_'+codNota+'_'+i);\n");
  echo("              newTable.setAttribute(\"name\", 'table_'+codAvaliacao+'_'+codGrupo+'_'+i);\n");
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
    echo("                if(j != 4)\n");
  }
  echo("                newTd.innerHTML=js_arrayCab[j];\n");
  if($usr_formador)
  {
    echo("                else\n");
    echo("                {\n");
    // BotÃ£o Apagar do HistÃ³rico do Desempenho do Participante
    echo("                  newSpan = document.createElement('span');\n");
    echo("                  newSpan.innerHTML=js_arrayCab[j];\n");
    echo("                  newSpan.className='link';\n");
    echo("                  newSpan.setAttribute(\"id\", codNota+'_'+i+'_'+codAluno+'_'+codGrupo);\n");
    echo("                  newSpan.onclick = function(){ ApagarHisDes(this.id,id,codAvaliacao); };\n");
    echo("                  newTd.rowSpan = 2;\n");
    echo("                  newTd.appendChild(newSpan);\n");
    echo("                }\n");
  }
  echo("                newTrCab.appendChild(newTd);\n");
  echo("              }\n");
  echo("              newTrMid = document.createElement('tr');\n");
  //echo("              newTrMid.setAttribute(\"class\", \"altColor1\");\n");
  echo("              newTrMid.className=\"altColor1\";");
  echo("              newTrMid.setAttribute('id', 'tr_mid_'+i);\n");
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
  echo("                  newSpan.setAttribute(\"id\", codFormador+'_'+codNota+'_'+tipoComp+'_'+codAluno+'_'+codGrupo+'_'+j+'_'+i);\n");
  echo("                  if(j == 2)\n");
  echo("                    newSpan.onclick = function(){ AbrePerfilHisDes(this.id); };\n");
  echo("                  else\n");
  echo("                  {\n");
  echo("                    newSpan.setAttribute(\"name\", 'span_'+codAvaliacao+'_'+codGrupo+'_'+i);\n");
  echo("                    newSpan.onclick = function(){ MudaCompartilhamentoHisDes(this.id,codAvaliacao); };\n");
  echo("                  }\n");
  echo("                newTd.appendChild(newSpan);\n");
  echo("                }\n");
  echo("                else\n");
  echo("                  newTd.innerHTML=arrayConteudo[i][j];\n");
  echo("                newTrMid.appendChild(newTd);\n");
  echo("              }\n");
  echo("              newTrJust = document.createElement('tr');\n");
  echo("              newTrJust.className = 'head01';\n");
  // Justificativa
  echo("              newTd = document.createElement('td');\n");
  echo("              newTd.colSpan = 5;\n");
  echo("              newTd.align = 'left';\n");
  echo("              newTd.innerHTML='".RetornaFraseDaLista($lista_frases,163)."';\n");
  echo("              newTrJust.appendChild(newTd);\n");
  // Botao de Resposta	
  // Ticket 254 - Tozo nao terminou antes de sair do NiED.
  //echo("              newTd = document.createElement('td');\n");
  //echo("              newTd.colSpan = 1;\n");
  //echo("              newTd.rowSpan = 2;\n");
  //echo("              newSpan = document.createElement('span');\n");
  //echo("              newSpan.innerHTML='Responder';\n"); /* TODO: HARDCODED -> Passar pro banco de dados */
  //echo("              newSpan.className='link';\n");
  //echo("							spam_id =  'comentar_'+codNota+'_'+i+'_'+codAluno+'_'+codGrupo;");
  //echo("              newSpan.setAttribute('id', spam_id);\n");
  //echo("              newSpan.onclick = function(){ respondeComentario(newTable, spam_id); };\n"); /* TODO: De chat function */
  //echo("							newTd.appendChild(newSpan);\n");
  //echo("              newTrJust.appendChild(newTd);\n");
	// Justificativa do formador
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
  echo("			  newTrJustMid.className=\"altColoro1\";");
	// Adicionando itens a tabela
//  echo("              newTable.appendChild(newTrCab);\n");
//  echo("              newTable.appendChild(newTrMid);\n");
//  echo("              newTable.appendChild(newTrJust);\n");
//  echo("              newTable.appendChild(newTrJustMid);\n");
//  echo("              newTdConteiner.appendChild(newTable);\n");
  echo("              tbody.appendChild(newTrCab);\n");
  echo("              tbody.appendChild(newTrMid);\n");
  echo("              tbody.appendChild(newTrJust);\n");
  echo("              tbody.appendChild(newTrJustMid);\n");
  echo("              newTable.appendChild(tbody);");
  echo("              newTdConteiner.appendChild(newTable);\n");
  echo("              newBr = document.createElement('br');\n");
  echo("              newTdConteiner.appendChild(newBr);\n");
  echo("            }\n");
  echo("            if(i == 0)\n");
  // 98 - Este participante ainda nï¿½o foi avaliado nesta atividade!
  echo("              newTdConteiner.appendChild(CriarSpanSimples('".RetornaFraseDaLista($lista_frases,98)."'));\n");
  echo("            newTrConteiner.appendChild(newTdConteiner);\n");
  echo("            newTrConteiner.appendChild(CriarTdFechar(id+'_hist'));\n");
  echo("            tableElement.insertBefore(newTrConteiner,trElement);\n");
  echo("          }\n\n");
  echo("        }\n\n");

// Ticket 254 - Tozo nao terminou antes de sair do NiED.
//echo("
//function respondeComentario(tr_pai, span_id) 
//{
//
//	// CabeÃ§alho: Nome+':'
//	cabecalhoTR = document.createElement('tr');
//	cabecalhoTR.className = 'head01';
//	infoTD = document.createElement('td');
//	infoTD.colSpan = 4;
//	infoTD.align = 'left';
//	infoTD.innerHTML = 'Victor Tozo:';
//	cabecalhoTR.appendChild(infoTD);
//
//	// Comentario + Data
//	whoChatTR = document.createElement('tr');
//	whoChatTR.className = 'altColor1';
//
//	// Comentario
//	chatTD = document.createElement('td');
//	chatTD.colSpan = 3;
//	chatTD.align = 'left';
//	chatTD.innerHTML = 'Poxa professora, me esforcei de mais pra ganhar essa nota !'; /* TODO: HARDCODED */
//	whoChatTR.appendChild(chatTD);
//
//	// Data
//	chatTD = document.createElement('td');
//	chatTD.colSpan = 1;
//	chatTD.align = 'center';
//	chatTD.innerHTML = 'Data: 19:55 20/08/2010'; /* TODO: HARDCODED */
//	whoChatTR.appendChild(chatTD);
//
//	// Encerra
//	tr_pai.appendChild(cabecalhoTR);
//	tr_pai.appendChild(whoChatTR);
//}
//");


  echo("        function AtualizaTr(id,codNota,nota,data,comp)\n");
  echo("        {\n");
  echo("          VerTelaNotas();\n");
  echo("        }\n\n");

  if($usr_formador)
  { 
    echo("        function AtualizaComp(js_tipo_comp,spanName)\n");
    echo("        {\n");
    echo("          if(js_cod_grupo != -1)\n");
    echo("            js_tipoComp = 'G';\n");
    echo("          else\n");
    echo("            js_tipoComp = 'A';\n");
    echo("          if ((isNav) && (!isMinNS6)) {\n");
    echo("            document.comp.document.getElementById('tipo_comp_frase').innerHTML = RetornaFraseComp(js_tipo_comp);\n");
    echo("            document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("            document.comp.document.form_comp.cod_nota.value=js_cod_nota;\n");
    echo("            document.comp.document.form_comp.cod_aluno.value=js_cod_aluno;\n");
    echo("            document.comp.document.form_comp.cod_grupo.value=js_cod_grupo;\n");
    echo("            document.comp.document.form_comp.cod_avaliacao.value=js_cod_avaliacao;\n");
    echo("            document.comp.document.form_comp.spanName.value=spanName;\n");
    echo("            var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));\n");
    echo("          } else {\n");
    echo("              document.getElementById('tipo_comp_frase').innerHTML = RetornaFraseComp(js_tipoComp);\n");
    echo("              document.form_comp.tipo_comp.value=js_tipo_comp;\n");
    echo("              document.form_comp.cod_nota.value=js_cod_nota;\n");
    echo("              document.form_comp.cod_aluno.value=js_cod_aluno;\n");
    echo("              document.form_comp.cod_grupo.value=js_cod_grupo;\n");
    echo("              document.form_comp.cod_avaliacao.value=js_cod_avaliacao;\n");
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
  }

/***************************************************************************************************************************/

/* captureEvents esta deprecated. */
//    echo("      if (isNav)\n");
//    echo("      {\n");
//    echo("        document.captureEvents(Event.MOUSEMOVE);\n");
//    echo("      }\n");
//    echo("      document.onmousemove = TrataMouse;\n\n");

    /* VerificaÃ§Ã£o do browser sendo usado */
    echo("      if (document.addEventListener) {\n");	/* Caso do FireFox */
    echo("        document.addEventListener('mousemove', TrataMouse, false);\n");
    echo("      } else if (document.attachEvent){\n");	/* Caso do IE */
    echo("        document.attachEvent('onmousemove', TrataMouse);\n");
    echo("      }\n");

    echo("      function TrataMouse(e)\n");
    echo("      {\n");
    echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("      }\n");

    echo("      function getPageScrollY()\n");
    echo("      {\n");
    echo("        if (isNav)\n");
    echo("          return(window.pageYOffset);\n");
    echo("        if (isIE)\n");
    echo("          return(document.documentElement.scrollTop);\n");
    echo("      }\n");

    echo("      function AjustePosMenuIE()\n");
    echo("      {\n");
    echo("        if (isIE)\n");
    echo("          return(getPageScrollY());\n");
    echo("        else\n");
    echo("          return(0);\n");
    echo("      }\n");

    echo("      function TrataMouse(e)\n");
    echo("      {\n");
    echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
    echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
    echo("      }\n");

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

    echo("      function Iniciar()\n");
    echo("      {\n");
    echo("        lay_muda_expressao = getLayer('layer_muda_expressao');\n");
    echo("        cod_comp = getLayer(\"comp\");\n");
    echo("        LoopAvaliarExpressao(".$j.",'".$expressao."','".$norma."');\n");
    echo("        startList();\n");
    echo("      }\n");


    echo("      function EscondeLayers()\n");
    echo("      {\n");
    echo("        hideLayer(cod_comp);\n");
    echo("        hideLayer(lay_muda_expressao);\n");
    echo("        alterandoExp = 0;\n");
    echo("      }\n");

    echo("      function MostraLayer(cod_layer, ajuste)\n");
    echo("      {\n");
    echo("        EscondeLayers();\n");
    echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
    echo("        showLayer(cod_layer);\n");
    echo("        alterandoExp = 1;\n");
    echo("      }\n");

    echo("      function EscondeLayer(cod_layer)\n");
    echo("      {\n");
    echo("        hideLayer(cod_layer);\n");
    echo("        alterandoExp = 0;\n");
    echo("      }\n");

    echo("      function AdicionarLegenda(l,id)\n");
    echo("      {\n");
    echo("        if(alterandoExp)\n");
    echo("        {\n");
    echo("          document.getElementById('nova_expressao').value=document.getElementById('nova_expressao').value+l;\n");
    echo("          document.getElementById('nova_expressao').focus();\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    echo("          VerObj(id);\n");
    echo("        }\n");
    echo("      }\n");

    echo("      function GravarExpressao()\n");
    echo("      {\n");
    echo("        expressao = document.form_muda_expressao.nova_expressao.value;\n");
    echo("        norma = document.form_muda_expressao.nova_norma.value;\n");
    echo("        LoopAvaliarExpressao(".$j.",expressao,norma);\n");
    echo("        if (ok) {\n");
    echo("          xajax_GravarExpressaoDinamic(xajax.getFormValues('form_muda_expressao'));\n");
    echo("          EscondeLayers();\n");
    echo("        }\n");
    echo("        return false;\n");
    echo("      }\n");

    echo("      function LoopAvaliarExpressao(j,exp,norm) {\n");
    echo("        ok=true;\n");
    echo("        expressao=exp;\n");
    // verificamos se a norma tem virgula, se tiver, convertemos para ponto
    echo("        norma=norm.replace(/\,/, '.');\n");
    echo("        expressao_original=expressao;\n");
    echo("        CorrigeExpressao();\n");
    echo("        for (var i = 0; i <= j; i++) {\n");
    echo("          AvaliarExpressao(i);\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function CorrigeExpressao() {\n");
    // Funï¿½ï¿½es em inglï¿½s
    echo("        expressao=expressao.toUpperCase();\n");
    echo("        expressao=expressao.replace(new RegExp(/SQRT\(/g), 'Math.sqrt(');\n");
    echo("        expressao=expressao.replace(new RegExp(/POW\(/g), 'Math.pow(');\n");
    echo("        expressao=expressao.replace(new RegExp(/ABS\(/g), 'Math.abs(');\n");
    echo("        expressao=expressao.replace(new RegExp(/CEIL\(/g), 'Math.ceil(');\n");
    echo("        expressao=expressao.replace(new RegExp(/COS\(/g), 'Math.cos(');\n");
    echo("        expressao=expressao.replace(new RegExp(/EXP\(/g), 'Math.exp(');\n");
    echo("        expressao=expressao.replace(new RegExp(/FLOOR\(/g), 'Math.floor(');\n");
    echo("        expressao=expressao.replace(new RegExp(/LOG\(/g), 'Math.log(');\n");
    echo("        expressao=expressao.replace(new RegExp(/MAX\(/g), 'Math.max(');\n");
    echo("        expressao=expressao.replace(new RegExp(/MIN\(/g), 'Math.min(');\n");
    echo("        expressao=expressao.replace(new RegExp(/ROUND\(/g), 'Math.round(');\n");
    echo("        expressao=expressao.replace(new RegExp(/SIN\(/g), 'Math.sin(');\n");
    echo("        expressao=expressao.replace(new RegExp(/TAN\(/g), 'Math.tan(');\n");
    // Funï¿½ï¿½es em portuguï¿½s
    echo("        expressao=expressao.replace(new RegExp(/RAIZ\(/g), 'Math.sqrt(');\n");
    echo("        expressao=expressao.replace(new RegExp(/POTï¿½NCIA\(/g), 'Math.pow(');\n");
    echo("        expressao=expressao.replace(new RegExp(/POTENCIA\(/g), 'Math.pow(');\n");
    echo("        expressao=expressao.replace(new RegExp(/ABS\(/g), 'Math.abs(');\n");
    echo("        expressao=expressao.replace(new RegExp(/TETO\(/g), 'Math.ceil(');\n");
    echo("        expressao=expressao.replace(new RegExp(/COS\(/g), 'Math.cos(');\n");
    echo("        expressao=expressao.replace(new RegExp(/EXP\(/g), 'Math.exp(');\n");
    echo("        expressao=expressao.replace(new RegExp(/CHï¿½O\(/g), 'Math.floor(');\n");
    echo("        expressao=expressao.replace(new RegExp(/CHAO\(/g), 'Math.floor(');\n");
    echo("        expressao=expressao.replace(new RegExp(/LOG\(/g), 'Math.log(');\n");
    echo("        expressao=expressao.replace(new RegExp(/Mï¿½XIMO\(/g), 'Math.max(');\n");
    echo("        expressao=expressao.replace(new RegExp(/MAXIMO\(/g), 'Math.max(');\n");
    echo("        expressao=expressao.replace(new RegExp(/Mï¿½NIMO\(/g), 'Math.min(');\n");
    echo("        expressao=expressao.replace(new RegExp(/MINIMO\(/g), 'Math.min(');\n");
    echo("        expressao=expressao.replace(new RegExp(/ARRED\(/g), 'Math.round(');\n");
    echo("        expressao=expressao.replace(new RegExp(/SEN\(/g), 'Math.sin(');\n");
    echo("        expressao=expressao.replace(new RegExp(/TAN\(/g), 'Math.tan(');\n");
     // Constantes
    echo("        expressao=expressao.replace(new RegExp(/PI/g), 'Math.PI');\n");
    echo("      }\n\n");

    // retorna true se a norma contiver digitos estranhos
    // retorna false se a norma estiver no formato adequado
    echo("        function norma_com_digito_estranho(norma) {\n");
    echo("          re_com_virgula = /^[0-9]+(\.|,)?[0-9]+\$/; \n"); // nota com decimal
    echo("          re_somente_numeros = /^[0-9]+\$/; \n"); // somente numeros
    echo("          if (norma == '' || re_com_virgula.test(norma) || re_somente_numeros.test(norma) ) { \n");
    echo("            return false;\n");
    echo("          } else {\n");
    echo("            return true;\n");
    echo("          }\n");
    echo("        }\n");

    echo("      function AvaliarExpressao(i) {\n");

    $cont_batepapo=1;
    $cont_forum=1;
    $cont_portfolio=1;
    $cont_exercicio=1;
    $cont_av_ext=1;

    if (count($lista_avaliacoes) > 0) {
       foreach ($lista_avaliacoes as $index => $avaliacao) {
          if (!strcmp($avaliacao['Ferramenta'], 'B')) {
             $cont=$cont_batepapo;
             $cont_batepapo++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'F')) {
             $cont=$cont_forum;
             $cont_forum++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'P')) {
             $cont=$cont_portfolio;
             $cont_portfolio++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'E')) {
             $cont=$cont_exercicio;
             $cont_exercicio++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'N')) {
             $cont=$cont_av_ext;
             $cont_av_ext++;
          } 

          echo("        var ".$avaliacao['Ferramenta'].$cont." = ".$avaliacao['Ferramenta'].$cont."v[i];\n");
       }
    }
    echo("        try {\n");
    echo("          if (expressao != '') {\n");
    echo("            var nota=eval(expressao);\n");
    echo("          } else { return; }\n");
    echo("        } catch (e){\n");
    echo("          if (i == 0) {\n");
    if ($usr_formador) {
    // 191 - Verifique se a sua expressï¿½o estï¿½ correta!
      //echo("        alert('Verifique se sua expressï¿½o estï¿½ correta!\\nErro = ' + e.message);\n");
      echo("          alert('".RetornaFraseDaLista($lista_frases, 191)."');\n");
    }
    echo("            ok=false;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if (i != 0) {\n");
    if (($media['tipo_compartilhamento'] == 'T')                  ||
        ($media['tipo_compartilhamento'] == 'F' && $usr_formador) ||
        ($media['tipo_compartilhamento'] == 'A')) {
      if ($media['tipo_compartilhamento'] == 'A' && !$usr_formador) {
        echo("          if (codv[i-1] == $cod_usuario) {\n");
      }
      echo("            if (ok && norma != '') {\n");
      echo("              var normalizacao=nota*(10/norma);\n");
      echo("            } else {\n");
      echo("              var normalizacao=nota;\n");
      echo("            }\n");
      echo("            if (ok && (!isUndefined(normalizacao)) && (!isNaN(normalizacao))) {\n");
      echo("              document.getElementById('media' + i).innerHTML=normalizacao.toFixed(2);\n");
      echo("            }else {\n");
      echo("              document.getElementById('media' + i).innerHTML='0.00';\n");
      echo("            }\n");
      if ($media['tipo_compartilhamento'] == 'A' && !$usr_formador) {
        echo("          }\n");
      }
    }
    echo("        } else {\n");
    echo("          if (norma != '' && !norma_com_digito_estranho(norma)) {\n");
    echo("            media=nota;\n");
    echo("            nota=media*(10/norma);\n");
    echo("          }\n");
    echo("          if(norma_com_digito_estranho(norma)) {\n");
    echo("            ok=false;\n");
    // ?? - Verifique se a norma estï¿½ correta!
    echo("            alert('Verifique se a norma esta correta');\n");
    echo("          }\n");
    echo("          if (ok && (!isUndefined(nota)) && (nota != 'Infinity') && (media != Infinity)) {\n");
    echo("            document.getElementById('maxMedia').innerHTML=nota.toFixed(2);\n");
    echo("          } else {\n");
    echo("            if ((nota == 'Infinity') || (media == 'Infinity')) {\n");
    // 198 - A divisï¿½o por 0 (zero) nï¿½o ï¿½ permitida!
    echo("              alert('".RetornaFraseDaLista($lista_frases, 198)."');\n");
    echo("            }\n");
    echo("            document.getElementById('maxMedia').innerHTML='0.00'\n");
    echo("            ok=false;\n");
    echo("          }\n");
    echo("        }\n");
    echo("        if (ok) {\n");
    echo("          document.getElementById('expFinal').innerHTML=expressao_original;\n");
    echo("          if (norma != '') \n");
    echo("            document.getElementById('normaFinal').innerHTML=norma;\n");
    echo("          else \n");
    echo("            document.getElementById('normaFinal').innerHTML='".RetornaFraseDaLista($lista_frases, 228)."';\n");
    echo("        }else {\n");
    // 199 - Erro
    echo("          document.getElementById('expFinal').innerHTML='".RetornaFraseDaLista($lista_frases, 199)."';\n");
    echo("          if (norma != '') \n");
    echo("            document.getElementById('normaFinal').innerHTML='".RetornaFraseDaLista($lista_frases, 199)."';\n");
    echo("          else \n");
    echo("            document.getElementById('normaFinal').innerHTML='".RetornaFraseDaLista($lista_frases, 228)."';\n");
    echo("        }\n");
    echo("      }\n\n");

    echo("      function isUndefined(a) {\n");
    echo("        return typeof a == 'undefined';\n");
    echo("      }\n\n");

    echo("      function AjudaMedia(ajuda, nome_janela)\n");
    echo("      {\n");
    $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
    echo("        window_handle = window.open('ajuda_media.php?cod_curso=".$cod_curso."&tela_ajuda=' + ajuda, nome_janela, ".$param.");\n");
    echo("        window_handle.focus(); \n");
    echo("        return false;");
    echo("      }\n\n");

    echo("        function HistoricodoDesempenho(funcao,codAvaliacao,id,codNota)\n");
    echo("        {\n");
    echo("          xajax_MostrarAvaliacoesDinamic(".$cod_curso.",codAvaliacao,funcao,'','',id,codNota);\n");
    echo("          js_cod_aluno = funcao;\n");
    echo("        }\n");

    echo("        function HistoricodoDesempenhoPortfolio(funcao,codAvaliacao,id,codNota)\n");
    echo("        {\n");
    echo("          xajax_MostrarAvaliacoesDinamic(".$cod_curso.",codAvaliacao,funcao,'','',id,codNota);\n");
    echo("          js_cod_aluno = funcao;\n");
    echo("        }\n");

    echo("      function ImprimirRelatorio()\n");
    echo("      {\n");
    echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
    echo("        {\n");
    echo("          self.print();\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n\n");
    /* 51 - Infelizmente nï¿½o foi possï¿½vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
    echo("          alert('".RetornaFraseDaLista($lista_frases,51)."');\n");
    echo("        }\n");
    echo("      }\n\n");

    /* Funï¿½ï¿½o JvaScript para chamar pï¿½gina para salvar em arquivo. */
    echo("      function SalvarTodasNotas()\n");
    echo("      {\n");
    echo("        document.frmMsg.action = \"salvar_todas_as_notas.php\";");
    echo("        document.frmMsg.submit();\n");
    echo("      }\n\n");

    echo("      function AbrePerfil(cod_usuario)\n");
    echo("      {\n");
    echo("        window.open('../perfil/exibir_perfis.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=60,left=60,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return(false);\n");
    echo("      }\n\n");

    echo("      function AbreJanelaComponentes(cod_grupo)\n");
    echo("      {\n\n");
    echo("        window.open('componentes.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&cod_grupo='+cod_grupo,'Componentes','width=400,height=300,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
    echo("        return false;\n");
    echo("      }\n\n");

    // Ver objetivos e critï¿½rios da avaliacao
    echo("      function VerObj( id )\n");
    echo("      {\n");
    $param = "'width=600,height=400,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
    $nome_janela = "'AvaliacoesHistorico'";
    echo("        window_handle = window.open('ver_popup.php?cod_curso=".$cod_curso."&cod_avaliacao=' + id, ".$nome_janela.", ".$param.");\n");
    echo("        window_handle.focus(); \n");
    echo("        return false;");
    echo("      }\n\n");

    // Esta funcao mostra a tela com a lista de avaliacoes Passadas, Atuais ou Futuras
    // tela = 'P', 'F' ou 'A'
    echo("      function VerTelaAvaliacoes(tela)\n");
    echo("      {\n");
    echo("        document.frmAvaliacao.tela_avaliacao.value = tela;\n");
    echo("        document.frmAvaliacao.action = 'avaliacoes.php';\n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("        return false;\n");
    echo("      }\n\n");

    echo("      function VerTelaNotas()\n");
    echo("      {\n");
    echo("        document.frmAvaliacao.action = 'notas.php';\n");
    echo("        document.frmAvaliacao.submit();\n");
    echo("        return false;\n");
    echo("      }\n\n");

    echo("      function ExibirLegenda()\n");
    echo("      {\n");
    echo("        spanElement = document.getElementById('span_legenda');\n");
    /* 232 - Ocultar Legenda */
    echo("        spanElement.innerHTML = \"".RetornaFraseDaLista($lista_frases,232)."\";\n");
    echo("        spanElement.onclick = function(){ OcultarLegenda(); };\n");
    echo("        tableElement = document.getElementById('table_legenda');\n");
    echo("        tableElement.style.display = \"\";\n");
    echo("      }\n\n");

    echo("      function OcultarLegenda()\n");
    echo("      {\n");
    echo("        spanElement = document.getElementById('span_legenda');\n");
    /* 207 - Exibir Legenda */ 
    echo("        spanElement.innerHTML = \"".RetornaFraseDaLista($lista_frases,207)."\";\n");
    echo("        spanElement.onclick = function(){ ExibirLegenda(); };\n");
    echo("        tableElement = document.getElementById('table_legenda');\n");
    echo("        tableElement.style.display = \"none\";\n");
    echo("      }\n\n");
  }

  echo("    </script>\n");
  echo("    <script type=\"text/javascript\" src=\"../js-css/jscript.js\"></script>");

  $objAjax->printJavascript();

  $sock = MudarDB($sock, $cod_curso);
  include("../menu_principal.php");

  echo("    <form name=\"frmAvaliacao\" method=\"get\">\n");
  echo("      <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  // Passa o cod_avaliacao para executar aï¿½ï¿½es sobre ela.
  echo("      <input type=\"hidden\" name=\"cod_avaliacao\" value=\"-1\">\n");
  // tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=\"hidden\" name=\"tela_avaliacao\" value=\"".$tela_avaliacao."\">\n");
  echo("    </form>\n");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  //1 - Avaliacoes
  //31 - Notas dos Participantes
  echo("          <h4> ".RetornaFraseDaLista($lista_frases, 1)." - ".RetornaFraseDaLista($lista_frases, 31)." </h4>");

    // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  //<!----------------- Cabeï¿½alho Acaba Aqui ----------------->

  //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");

  //<!----------------- Botoes de Acao ----------------->
  echo("              <td class=\"btAuxTabs\">\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 29 - Avaliaï¿½ï¿½es Passadas */
  echo("                  <li><a href=\"#\" title=\"".RetornaFraseDaLista($lista_frases, 29)."\" onClick=\"return(VerTelaAvaliacoes('P'));\">".RetornaFraseDaLista($lista_frases, 29)."</a></li>\n");
  /* 32 - Avaliaï¿½ï¿½es Atuais*/
  echo("                  <li><a href=\"#\" title=\"".RetornaFraseDaLista($lista_frases, 32)."\" onClick=\"return(VerTelaAvaliacoes('A'));\">".RetornaFraseDaLista($lista_frases, 32)."</a></li>\n");
  /* 30 - Avaliaï¿½ï¿½es Futuras*/
  echo("                  <li><a href=\"#\" title=\"".RetornaFraseDaLista($lista_frases, 30)."\" onClick=\"return(VerTelaAvaliacoes('F'));\">".RetornaFraseDaLista($lista_frases, 30)."</a></li>\n");
  /* 31 - Notas dos participantes */
  echo("	          <li><span onClick='return(VerTelaNotas());'>".RetornaFraseDaLista($lista_frases, 31)."</span></li>");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $reg=count($lista_avaliacoes);
  $width=($reg*50)+150;

  if (count($lista_avaliacoes)>0)
  {
    //Tabela com a lista de alunos do curso, com suas respectivas notas na avaliaï¿½ï¿½o realizada

    echo("                <tr class=\"head\">\n");
    echo("                  <td style=\"width:20%\">".RetornaFraseDaLista($lista_frases,1)."</td>\n");

    $cont_batepapo=1;
    $cont = 1;
    $cont_forum=1;
    $cont_portfolio=1;
    $cont_exercicio=1;
    $cont_av_ext=1;
    $legenda_batepapo = array();
    $legenda_forum = array();
    $legenda_portfolio = array();
    $legenda_exercicio = array();
    $legenda_avaliacao_externa = array();

    foreach ($lista_avaliacoes as $cod => $linha)
    {
      if (!$SalvarEmArquivo)
      {
        $a1 = "<a href=\"#\" onClick=\"AdicionarLegenda('".$linha['Ferramenta'].$cont++."',".$linha['Cod_avaliacao'].");\">";
        $a2 = "</a>";
      }
      else
      {
        $a1 = $a2 = "";
      }

      if (!strcmp($linha['Ferramenta'],'F'))
      {
        $leg = $linha['Ferramenta'].$cont_forum++;
        $legenda_forum[] = array(
            // o nome do forum
            'titulo' => $linha['Titulo'],
            // codigo do forum : F#
            'leg' => $leg,
            //codigo de avaliacao
            'cod_avaliacao' => $linha['Cod_avaliacao'],
            // O Periodo do Forum
            // 'data' => UnixTime2Data($linha['Data'])
            // 166 - de
            // 167 - a
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
        );
      }
      elseif (!strcmp($linha['Ferramenta'],'B'))
      {
        $leg = $linha['Ferramenta'].$cont_batepapo++;
        $legenda_batepapo[] = array(
            // o titulo da sessao de bate-papo que serviu como avaliacao
            'titulo' => $linha['Titulo'],
            // o codigo: B# onde # eh um numero
            'leg' => $leg,
            //codigo de avaliacao
            'cod_avaliacao' => $linha['Cod_avaliacao'],
            // a data da sessao
            'data' => UnixTime2Data($linha['Data'])
        );
      }
      elseif (!strcmp($linha['Ferramenta'],'N'))
      {    
         $leg = $linha['Ferramenta'].$cont_av_ext++;
         $legenda_avaliacao_externa[] = array(
            // o titulo da avaliacao externa
            'titulo' => $linha['Titulo'],
            // o codigo: N# onde # eh um numero
            'leg' => $leg,
            //codigo de avaliacao
            'cod_avaliacao' => $linha['Cod_avaliacao'],
            // a data da avaliacao
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
         );

      }
      elseif (!strcmp($linha['Ferramenta'],'E'))
      {
        $leg = $linha['Ferramenta'].$cont_exercicio++;
        $legenda_exercicio[] = array(
            // o titulo da sessao de bate-papo que serviu como avaliacao
            'titulo' => $linha['Titulo'],
            // o codigo: B# onde # eh um numero
            'leg' => $leg,
            //codigo de avaliacao
            'cod_avaliacao' => $linha['Cod_avaliacao'],
            // a data da sessao
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
        );
      }
      elseif (!strcmp($linha['Ferramenta'],'P'))
      {
        $leg = $linha['Ferramenta'].$cont_portfolio++;
        $legenda_portfolio[] = array(
            // titulo da Atividade que foi avaliada
            'titulo' => $linha['Titulo'],
            // codigo da avaliacao: P#
            'leg' => $leg,
            //codigo de avaliacao
            'cod_avaliacao' => $linha['Cod_avaliacao'],
            // o periodo em que a atividade estava disponivel
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
        );
      }

      echo("                    <td style=\"width:50\">".$a1.$leg.$a2."</td>\n");
    }

    if($usr_formador)
    {
      $a1 = "<a class=\"menu\" href=\"#\" onClick=\"MostraLayer(lay_muda_expressao, 400); document.getElementById('nova_expressao').focus();\">";
      $a2 = "</a>";
    } 
    else
      $a1=$a2="";

    // 197 - Mï¿½dia Final
    echo("                    <td style=\"width=100\" align='center'>".$a1.RetornaFraseDaLista($lista_frases,197).$a2."</a></td>\n");
    echo("                  </tr>\n");

    // 114 - Valor da Avaliaï¿½ï¿½o
    echo("                  <tr class=\"head01\">\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,114)."</td>\n");

    foreach ($lista_avaliacoes as $cod => $linha)
    {
      echo("                    <td id=\"media\" align=center>".FormataNota($linha['Valor'])."</td>\n");
    }

    echo("                    <td id=\"maxMedia\" align='center'>0.00</td>");
    echo("                  </tr>\n");

    $lista_users=RetornaListaUsuariosAluno($cod_curso);
    $j=0;

    /* Notas dos Alunos */
    if (count($lista_users)>0)
    {
      // 64 - Alunos
      echo("                  <tr class=\"head\">\n");
      echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases,64)."</td>\n");
      // 155 - Notas
      echo("                    <td colspan=".($reg+1).">".RetornaFraseDaLista($lista_frases,155)."</td>\n");
      echo("                  </tr>\n");
      $sock = MudarDB($sock, $cod_curso);
      foreach($lista_users as $cod => $nome)
      {

        echo("                  <tr id=\"tr_aluno_".$cod."\">\n");
        echo("                    <td align=left>"."&nbsp;&nbsp;");
        if (!$SalvarEmArquivo)
          echo("<a href=\"#\" onClick=\"return(AbrePerfil(".$cod."));\">".$nome."</a></td>\n");
        else
          echo($nome."</td>\n");


        foreach ($lista_avaliacoes as $cont => $linha)
        {
          /*******************************************/
          /*******Pega dados do exercicio*************/
          $grupo=(($linha['tipo']=='G') && (($linha['Ferramenta']=='E') || ($linha['Ferramenta']=='N')));
          if ($grupo)  
          {
            $codigo=RetornaCodigoGrupoAvaliacao($sock,$cod,$linha['Cod_avaliacao']);
            if ($codigo)
              $foiavaliado=GrupoFoiAvaliado($sock,$linha['Cod_avaliacao'],$codigo);
            else
            {
              $grupo=0;
              $codigo=$cod;
            }
          }
          else
          {
            $codigo=$cod;
            $foiavaliado=FoiAvaliado($sock,$linha['Cod_avaliacao'],$cod);
          }

          //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $linha['Cod_avaliacao'], $codigo, $grupo);                    /**********************************************/

          if ($foiavaliado && $linha['Ferramenta']!='E')  
          {
            $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=FormataNota($dados_nota['nota']);

            if ($usr_formador)
            {
              $marcaib="";
              $marcafb="";
              echo("                    <td align=center>");
              if (!$SalvarEmArquivo)
              {
                if (strcmp($linha['Ferramenta'],'P'))
                  echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                else
                  echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                echo($nota."</a></td>\n");
              }
              else
                echo($nota."</td>\n");
            }
            else       //ï¿½ ALUNO
            {
              echo("                    <td align=center>");
              if (!strcmp($tipo_compartilhamento,'T'))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($linha['Ferramenta'],'P'))
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                  else
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");

                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              elseif (((!strcmp($tipo_compartilhamento,'A')) || (!strcmp($tipo_compartilhamento,'G'))) && ($cod_usuario==$cod))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($linha['Ferramenta'],'P'))
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                  else
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");

                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              else //Estï¿½ compartilhada sï¿½ com formadores
                echo("&nbsp;</td>\n");
            }
          }//Exercï¿½cio
          elseif($foiavaliado && $linha['Ferramenta']=='E') {
            $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
            $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
            $cod_nota=$dados_nota['cod_nota'];
            $nota=FormataNota($dados_nota['nota']);

            if ($usr_formador)
            {
              $marcaib="";
              $marcafb="";
              echo("                    <td align=center>");
              if (!$SalvarEmArquivo)
              {
                if (strcmp($linha['Ferramenta'],'P'))
                  echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                else
                  echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                echo($nota."</a></td>\n");
              }
              else
                echo($nota."</td>\n");
            }
            else       //ï¿½ ALUNO
            {
              echo("                    <td align=center>");
              if (!strcmp($tipo_compartilhamento,'T'))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($linha['Ferramenta'],'P'))
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                  else
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");

                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              elseif (((!strcmp($tipo_compartilhamento,'A')) || (!strcmp($tipo_compartilhamento,'G'))) && ($cod_usuario==$cod))
              {
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($linha['Ferramenta'],'P'))
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                  else
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");

                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              else //Estï¿½ compartilhada sï¿½ com formadores
                echo("&nbsp;</td>\n");
	            }
          }
          else // nenhuma nota foi atribuida
          {
             if ($linha['Ferramenta']=='E' && $linha['Data_termino']<=time() )  
             {
               echo("      <td align=center>\n");
               echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
               $nota=" 0.00 ";
               echo($nota."</a></td>\n");
             }
             else
               echo("                   <td align=center>&nbsp;</td>\n");
          }
        }
        $j=$j+1;
        echo("                    <td align=center><font id=\"media".$j."\"></font></td>\n");
        echo("                  </tr>\n");
      }
    }

    /* Notas dos Formadores (SÃ³ sÃ£o vistas por formadores) */
    if ($usr_formador)
      {
        /* 156 - Formador */
        echo("                  <tr class=\"head\">\n");
        echo("                    <td align=\"center\">".RetornaFraseDaLista($lista_frases,156)."</td>\n");
        // 155 - Notas
        echo("                    <td colspan=".($reg+1).">".RetornaFraseDaLista($lista_frases,155)."</td>\n");
        echo("                  </tr>\n");

        $lista_users=RetornaListaUsuariosFormador($cod_curso);

        foreach($lista_users as $cod => $nome)
        {
          echo("                  <tr id=\"tr_formador_".$cod."\">\n");
          echo("                    <td align=left>"."&nbsp;&nbsp;");
          if (!$SalvarEmArquivo)
            echo("<a class=\"text\" href=\"#\" onClick=\"return(AbrePerfil(".$cod."));\">".$nome."</a></td>\n");
          else
            echo($nome."</td>\n");

          foreach ($lista_avaliacoes as $cont => $linha)
          {
            /*******************************************/
            /*******Pega dados do exercicio*************/
            $sock = MudarDB($sock, $cod_curso);
            $grupo=(($linha['tipo']=='G') && (($linha['Ferramenta']=='E') || ($linha['Ferramenta']=='N')));
            //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $linha['Cod_avaliacao'], $cod, $grupo);

            if($grupo)
            {
              $codigo=RetornaCodigoGrupoAvaliacao($sock,$cod,$linha['Cod_avaliacao']);
              if($codigo)
                $foiavaliado=GrupoFoiAvaliado($sock,$linha['Cod_avaliacao'],$codigo);
              else
              {
                $codigo=$cod;
                $grupo=0;
              }
            }
            else
            {
              $codigo=$cod;
              $foiavaliado=FoiAvaliado($sock,$linha['Cod_avaliacao'],$cod);
            }
            $sock = MudarDB($sock, $cod_curso);
            //$DadosExercicios=RetornaDadosExercicioAvaliado($sock, $linha['Cod_avaliacao'], $codigo, $grupo);

            /*******************************************/
            if ( $foiavaliado && $linha['Ferramenta']!='E' )
            //Ja existe uma nota atribuida
            {
              $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
              $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
              $cod_nota=$dados_nota['cod_nota'];
              $nota=FormataNota($dados_nota['nota']);

              $marcaib="";
              $marcafb="";
              echo("                    <td  align=center>");
              if (!$SalvarEmArquivo)
              {
                if (strcmp($linha['Ferramenta'],'P'))
                  echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_formador_".$cod."','".$cod_nota."'));\">");
                else
                  echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_formador_".$cod."','".$cod_nota."'));\">");

                echo($nota."</a></td>\n");
              }
              else
                echo($nota."</td>\n");
            }//Exercï¿½cio
            elseif($foiavaliado && $linha['Ferramenta']=='E') {
              $dados_nota=RetornaDadosNota($sock, $cod, $linha['Cod_avaliacao'],$cod_usuario,$usr_formador);
              $tipo_compartilhamento=$dados_nota['tipo_compartilhamento'];
              $cod_nota=$dados_nota['cod_nota'];
              $nota=FormataNota($dados_nota['nota']);

              if ($usr_formador)
              {
                $marcaib="";
                $marcafb="";
                echo("                    <td align=center>");
                if (!$SalvarEmArquivo)
                {
                  if (strcmp($linha['Ferramenta'],'P'))
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                  else
                    echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                  echo($nota."</a></td>\n");
                }
                else
                  echo($nota."</td>\n");
              }
              else       //ï¿½ ALUNO
              {
                echo("                    <td align=center>");
                if (!strcmp($tipo_compartilhamento,'T'))
                {
                  if (!$SalvarEmArquivo)
                  {
                    if (strcmp($linha['Ferramenta'],'P'))
                      echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                    else
                      echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");

                    echo($nota."</a></td>\n");
                  }
                  else
                    echo($nota."</td>\n");
                }
                elseif (((!strcmp($tipo_compartilhamento,'A')) || (!strcmp($tipo_compartilhamento,'G'))) && ($cod_usuario==$cod))
                {
                  if (!$SalvarEmArquivo)
                  {
                    if (strcmp($linha['Ferramenta'],'P'))
                      echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");
                    else
                      echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenhoPortfolio(".$cod.",".$linha['Cod_avaliacao'].",'tr_aluno_".$cod."','".$cod_nota."'));\">");

                    echo($nota."</a></td>\n");
                  }
                  else
                    echo($nota."</td>\n");
                }
                else //Estï¿½ compartilhada sï¿½ com formadores
                  echo("&nbsp;</td>\n");
              }
            }
            else // nenhuma nota foi atribuida
            {
              if ($linha['Ferramenta']=='E' && $linha['Data_termino']<=time() )
              {
                echo("                    <td align=center>\n");
                echo("<a href=\"#\" onClick=\"return(HistoricodoDesempenho(".$cod.",".$linha['Cod_avaliacao'].",'tr_formador_".$cod."','".$cod_nota."'));\">");
                $nota=" 0.00 ";
                echo($nota."</a></td>\n");
              }
              else
                echo("                    <td align=center>&nbsp;</td>\n");
            }
          }
          $j = $j+1;
          echo("                    <td  align=center><font id=\"media".$j."\"></font></td>");
          echo("                  </tr>\n");
        }
      }
  }
  else
  {
    // 115 - Nenhuma avaliaï¿½ï¿½o foi criada !
    echo("                  <tr>\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,115)."</td>\n");
    echo("                  </tr>\n");
  }
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td class=\"btAuxTabs\">\n");
  echo("                <form name=\"frmMsg\" method=\"post\">\n");
  echo("                  <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\">\n");
  echo("                <ul class=\"btAuxTabs03\">\n");
  if ($usr_formador)
    /* 192 - Alterar Expressï¿½o */
    echo("                  <li><span onClick=\"MostraLayer(lay_muda_expressao, 0); document.getElementById('nova_expressao').focus();\">".RetornaFraseDaLista($lista_frases, 192)."</span></li>\n");
  /* 207 - Exibir Legenda */
  echo("                  <li><span id=\"span_legenda\" onClick=\"ExibirLegenda();\">".RetornaFraseDaLista($lista_frases, 207)."</span></li>");
  /* 208 - Salvar em arquivo */
  echo("                  <li><span onClick=\"SalvarTodasNotas();\">".RetornaFraseDaLista($lista_frases, 208)."</span></li>\n");
  /* 209 - Imprimir */
  echo("                  <li><span onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases, 209)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");

  echo("          <br>\n");
  $sock = MudarDB($sock, $cod_curso);
  $media=RetornaInformacoesMedia($sock);

  if ($media['expressao'] == "") {
     // 200 - Nï¿½o definido
     $media['expressao'] = "(".RetornaFraseDaLista($lista_frases, 200).")";
  }

  if ($media['norma'] == "") {
     // 200 - Nï¿½o definido
     $media['norma'] = "(".RetornaFraseDaLista($lista_frases, 200).")";
  }

  if (count($lista_avaliacoes) > 0) 
  {
    // 194 - Expressï¿½o avaliada 
    echo("          <font class=\"text\"><b>".RetornaFraseDaLista($lista_frases, 194).":</b>&nbsp;</font>\n");
    echo("          <font class=\"text\" id=\"expFinal\">".$media['expressao']."</font><font class=\"text\">&nbsp;&nbsp;<a href=\"#\" onClick=\"AjudaMedia('expressao', 'AjudaMediaExpressao')\"><img src=\"../imgs/icAjuda.gif\" alt=\"Ajuda\" border=\"0\" align=\"absmiddle\" /></a></font><br>\n");
    // 195 - Norma
    echo("          <font class=\"text\"><b>".RetornaFraseDaLista($lista_frases, 195).":</b>&nbsp;</font>\n");
    echo("          <font class=\"text\" id=\"normaFinal\">".$media['norma']."</font><font class=\"text\">&nbsp;&nbsp;<a href=\"#\" onClick=\"AjudaMedia('norma', 'AjudaMediaNorma')\"><img src=\"../imgs/icAjuda.gif\" alt=\"Ajuda\" border=\"0\" align=\"absmiddle\"/></a></font><br><br>\n");
  }

  $apresentar_legenda = (
       (is_array($legenda_batepapo)  && count($legenda_batepapo) > 0)
    || (is_array($legenda_forum)     && count($legenda_forum) > 0)
    || (is_array($legenda_portfolio) && count($legenda_portfolio) > 0)
    || (is_array($legenda_exercicio) && count($legenda_exercicio) > 0)
    || (is_array($legenda_avaliacao_externa) && count($legenda_avaliacao_externa)>0));

  $mostraLegenda = ($SalvarEmArquivo) ? 'table' : 'none';
  if ($apresentar_legenda)
  {
    echo("          <table id=\"table_legenda\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" bgcolor=\"#DCDCDC\" style=\"display: ".$mostraLegenda.";\">\n");
    echo("            <tr>\n");
    /* 116 - Legenda */
    echo("              <td bgcolor=\"#f1f1f1\"><b>".RetornaFraseDaLista($lista_frases,116)."</b></td>\n");
    /* 168 -  PerÃ­odo */
    echo("              <td bgcolor=\"#f1f1f1\"><b>".RetornaFraseDaLista($lista_frases,168)."</b></td>\n");
    // 113 - Tipo de Avaliaï¿½ï¿½o
    echo("              <td align=center bgcolor=\"#f1f1f1\"><b>".RetornaFraseDaLista($lista_frases, 113)."</b></td>\n");
    echo("            </tr>\n");

    if (is_array($legenda_batepapo) && count($legenda_batepapo) > 0)
    {
      // 101 - Data
      $frase_data = RetornaFraseDaLista($lista_frases, 101);
      // Escrevo as datas do forum acima das linhas de legenda do forum e legendas do portfolio
      $linha_data_forum =
         "            <tr> \n".
         "              <td colspan=2>&nbsp;</td> \n".
         // 168 - Perï¿½odo
         "              <td><b>".RetornaFraseDaLista($lista_frases, 168)."</b></td> \n".
         "            </tr> \n";
    }

    if (is_array($legenda_batepapo) && count($legenda_batepapo) > 0)
    {
      foreach ($legenda_batepapo as $linha_legenda)
      {
        echo("            <tr>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("              <td bgcolor=\"#f1f1f1\"><b><a href=\"#\" onClick=\"AdicionarLegenda('".$leg[0]."',".$linha_legenda['cod_avalicao'].");\">".$linha_legenda['leg']."</a></b> - ".$linha_legenda['titulo']."</td>\n");
        echo("              <td bgcolor=\"#f1f1f1\">".$linha_legenda['data']."</td>\n");
        // 146 - Sessï¿½o de Batepapo
        echo("              <td bgcolor=\"#f1f1f1\">".RetornaFraseDaLista($lista_frases, 146)."</td>\n");
        echo("            </tr>\n");
      }
      echo("            <tr><td colspan=4>&nbsp;</td></tr>\n");
    }

    if (is_array($legenda_forum) && count($legenda_forum) > 0)
    {
      echo($linha_data_forum);
      $linha_data_forum = "";
      foreach ($legenda_forum as $linha_legenda)
      {
        echo("            <tr>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("              <td bgcolor=\"#f1f1f1\"><b><a href=\"#\" onClick=\"AdicionarLegenda('".$leg[0]."',".$linha_legenda['cod_avaliacao'].");\">".$linha_legenda['leg']."</a></b> - ".$linha_legenda['titulo']."</td>\n");
        echo("              <td bgcolor=\"#f1f1f1\">".$linha_legenda['data']."</td>\n");
        // 145 - Fï¿½rum de discussï¿½o
        echo("              <td bgcolor=\"#f1f1f1\">".RetornaFraseDaLista($lista_frases, 145)."</td>\n");
        echo("            </tr>\n");
      }
      echo("            <tr><td colspan=4>&nbsp;</td></tr>\n");
    }

    if (is_array($legenda_portfolio) && count($legenda_portfolio) > 0)
    {
      echo($linha_data_forum);
      foreach ($legenda_portfolio as $linha_legenda)
      {
        echo("            <tr>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("              <td bgcolor=\"#f1f1f1\"><b><a href=\"#\" onClick=\"AdicionarLegenda('".$leg[0]."',".$linha_legenda['cod_avaliacao'].");\">".$linha_legenda['leg']."</a></b> - ".$linha_legenda['titulo']."</td>\n");
        echo("              <td bgcolor=\"#f1f1f1\">".$linha_legenda['data']."</td>\n");
        // 14 - Atividade no Portfolio
        echo("              <td bgcolor=\"#f1f1f1\">".RetornaFraseDaLista($lista_frases, 14)."</td>\n");
        echo("            </tr>\n");
      }
    }

    if (is_array($legenda_exercicio) && count($legenda_exercicio) > 0)
    {
      echo($linha_data_forum);
      foreach ($legenda_exercicio as $linha_exercicio)
      {
        echo("            <tr>\n");
        $leg=explode("\n",$linha_exercicio['leg']);
        echo("              <td bgcolor=\"#f1f1f1\"><b><a href=\"#\" onClick=\"AdicionarLegenda('".$leg[0]."',".$linha_exercicio['cod_avaliacao'].");\">".$linha_exercicio['leg']."</a></b> - ".$linha_exercicio['titulo']."</td>\n");
        echo("              <td bgcolor=\"#f1f1f1\">".$linha_exercicio['data']."</td>\n");
        // 175 - Atividade em Exercï¿½cios
        echo("              <td bgcolor=\"#f1f1f1\">".RetornaFraseDaLista($lista_frases, 175)."</td>\n");
        echo("            </tr>\n");
      }
     }

    if (is_array($legenda_avaliacao_externa) && count($legenda_avaliacao_externa) > 0)
    {
      echo($linha_data_forum);
      foreach ($legenda_avaliacao_externa as $linha_legenda)
      {
        echo("            <tr>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("              <td bgcolor=\"#f1f1f1\"><b><a href=\"#\" onClick=\"AdicionarLegenda('".$leg[0]."',".$linha_legenda['cod_avaliacao'].");\">".$linha_legenda['leg']."</a></b> - ".$linha_legenda['titulo']."</td>\n");
        echo("              <td bgcolor=\"#f1f1f1\">".$linha_legenda['data']."</td>\n");
        // 14 - Atividade no Portfolio
        echo("              <td bgcolor=\"#f1f1f1\">".RetornaFraseDaLista($lista_frases, 187)."</td>\n");
        echo("            </tr>\n");
      }
    }

    echo("          </table>\n");
  } else {
	/* Nenhuma avaliacao, portanto nÃ£o hÃ¡ legenda. */
  	echo("          <table id=\"table_legenda\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" bgcolor=\"#DCDCDC\" style=\"display:".$mostraLegenda.";\">\n");
  	echo("            <tr>\n");
    /*116 - Legenda */
    echo("              <td bgcolor=\"#f1f1f1\"><b>".RetornaFraseDaLista($lista_frases,116)."</b></td>\n");
    /* 168 -  PerÃ­odo */
    echo("              <td bgcolor=\"#f1f1f1\"><b>".RetornaFraseDaLista($lista_frases,168)."</b></td>\n");
    // 113 - Tipo de Avaliaï¿½ï¿½o
    echo("              <td align=center bgcolor=\"#f1f1f1\"><b>".RetornaFraseDaLista($lista_frases, 113)."</b></td>\n");
    echo("            </tr>\n");
    echo("            <tr>\n");
    /*115 - Nenhuma avaliacao foi criada! */
    echo("              <td bgcolor=\"#f1f1f1\" colspan=\"3\">".RetornaFraseDaLista($lista_frases,115)."</td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");

  }
  echo("          </form>\n");

  if (($usr_formador) && (!$SalvarEmArquivo))
  {
    /* Altera Expressao */
    $sock = MudarDB($sock, $cod_curso);
    $media=RetornaInformacoesMedia($sock);
    echo("          <div id=\"layer_muda_expressao\" class=popup>\n");
    echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(lay_muda_expressao);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("              <div class=\"int_popup\">\n");
    echo("                <form name=\"form_muda_expressao\" id=\"form_muda_expressao\" method=\"post\" action=\"acoes.php\" onsubmit=\"return(GravarExpressao());\">\n");
    echo("                <div class=\"ulPopup\">\n");
    // 202 - Expressï¿½o para cï¿½lculo da mï¿½dia
    echo("                <b>".RetornaFraseDaLista($lista_frases,202)."</b><br />\n");
    echo("                  <input class=\"input\" type=\"text\" name=\"nova_expressao\" id=\"nova_expressao\" value=\"".$media['expressao']."\" maxlength=\"100\" size=\"25\" /><a href='#' onClick=\"AjudaMedia('expressao', 'AjudaMediaExpressao')\"><img src=\"../imgs/icAjuda.gif\" alt=\"Ajuda\" border=\"0\" align=\"absmiddle\" /></a><br /><br />\n");
    // 201 - Opcional
    // 195 - Norma
    echo("                <b>".RetornaFraseDaLista($lista_frases,195)."(".RetornaFraseDaLista($lista_frases,201).")</b><br />\n");
    echo("                  <input class=\"input\" type=\"text\" name=\"nova_norma\" id=\"nova_norma\" value=\"".$media['norma']."\" maxlength=100 size=5 /><a href=\"#\" onClick=\"AjudaMedia('norma', 'AjudaMediaNorma')\"><img src=\"../imgs/icAjuda.gif\" alt=\"Ajuda\" border=\"0\" align=\"absmiddle\"/></a><br /><br />\n");
    // 50 - Compartilhar
    echo("                <b>".RetornaFraseDaLista($lista_frases,50)."</b><br />\n");
    if ($media['tipo_compartilhamento']=="F")
    {
      $compf=" checked";
      $comptotal="";
      $compfp="";
      echo("                  <input type=\"hidden\" name=\"tipo_compartilhamento\" value='F'>\n");
    }
    else if ($media['tipo_compartilhamento']=="T")
    {
      $compf="";
      $comptotal=" checked";
      $compfp="";
      echo("                  <input type=\"hidden\" name=\"tipo_compartilhamento\" value='T'>\n");
    }
    else if ($media['tipo_compartilhamento']=="A")
    {     
      $compf="";
      $comptotal="";
      $compfp=" checked";
      echo("                  <input type=\"hidden\" name=\"tipo_compartilhamento\" value='A'>\n");
    }
    else
    {     
      $compf="";
      $comptotal="";
      $compfp=" checked";
      echo("                  <input type=\"hidden\" name=\"tipo_compartilhamento\" value='A'>\n");
    }
    // 51 - Totalmente Compartilhado
    echo("                  <input type=\"radio\" name=\"compartilhamento\" id=\"compartilhamento\"".$comptotal." onClick=\"document.form_muda_expressao.tipo_compartilhamento.value='T';\">".RetornaFraseDaLista($lista_frases,51)."<br>");
    // 52 - Compartilhado com Formadores
    echo("                  <input type=\"radio\" name=\"compartilhamento\" id=\"compartilhamento\"".$compf." onClick=\"document.form_muda_expressao.tipo_compartilhamento.value='F';\">".RetornaFraseDaLista($lista_frases,52)."<br>");
    // 54 - Compartilhado com Formadores e Com o Participante
    echo("                  <input type=\"radio\"  name=\"compartilhamento\" id=\"compartilhamento\"".$compfp." onClick=\"document.form_muda_expressao.tipo_compartilhamento.value='A';\">".RetornaFraseDaLista($lista_frases,54)."<br><br />");
    echo("                  <input type=\"hidden\" name=\"cod_curso\"        value=\"".$cod_curso."\" />\n");
    echo("                  <input type=\"hidden\" name=\"action\"           value=\"alterarExpressao\" />\n");
    echo("                  <input type=\"hidden\" name=\"cod_usuario\"      value=\"".$cod_usuario."\">\n");
    echo("                  <input type=\"hidden\" name=\"tela_avaliacao\"   value=\"".$tela_avaliacao."\" />\n");
    /* 18 - Ok (gen) */
    echo("                  <input type=\"button\" id=\"ok_novaexpressao\" class=\"input\" onClick=\"EscondeLayer(lay_muda_expressao);GravarExpressao();\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\">\n");
    /* 2 - Cancelar (gen) */
    echo("                  &nbsp;&nbsp;<input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_muda_expressao);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
    echo("                </div>\n");
    echo("                </form>\n");
    echo("              </div>\n");
    echo("            </div>\n");

    // Mudar Compartilhamento
    echo("          <div class=\"popup\" id=\"comp\" visibility=hidden onContextMenu='return(false);'>\n");
    echo("            <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
    echo("            <div class=\"int_popup\">\n");
    echo("            <form name=\"form_comp\" id=\"form_comp\">\n");
    echo("              <input type=\"hidden\" name=\"cod_curso\"       value=\"".$cod_curso."\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_nota\"        value=\"\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_aluno\"       value=\"\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_grupo\"       value=\"\" />\n");
    echo("              <input type=\"hidden\" name=\"cod_avaliacao\"   value=\"\" />\n");
    echo("              <input type=\"hidden\" name=\"portfolio_grupo\" value=\"\" />\n");
    echo("              <input type=\"hidden\" name=\"spanName\"        value=\"\" />\n");
    echo("              <input type=\"hidden\" name=\"tipo_comp\"       value=\"\" id=\"tipo_comp\" />\n");
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
    echo("                <li onClick=\"document.getElementById('tipo_comp').value=js_tipoComp; xajax_MudarCompartilhamentoDinamic(xajax.getFormValues('form_comp'), RetornaFraseComp(js_tipoComp)); EscondeLayers();\">\n");
    echo("                  <span id=\"tipo_comp_P\" class=\"check\"></span>\n");
    echo("                  <span id=\"tipo_comp_frase\"></span>\n");
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
