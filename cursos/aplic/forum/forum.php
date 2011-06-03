<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/forum/forum.php

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
  ARQUIVO : cursos/aplic/forum/forum.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("forum.inc");
  include("avaliacoes_forum.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das funções em PHP que você quer chamar através do xajax
  $objAjax->registerFunction("MudarConfiguracaoDinamic");
  $objAjax->registerFunction("EditarTituloDinamic");
  $objAjax->registerFunction("DecodificaString");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();  

  $cod_ferramenta=9;
  $cod_ferramenta_ajuda = $cod_ferramenta;
 
  if ((isset($status)) && ($status == 'D'))
    $cod_pagina_ajuda=2;
  else
    $cod_pagina_ajuda=1;

  include("../topo_tela.php");
  
  $AcessoAvaliacaoF = TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,22);
//  $AcessoAvaliacaoF = true;

  $feedbackObject =  new FeedbackObject($lista_frases);
  $feedbackObject->addAction("excluir", 65, 66);
  $feedbackObject->addAction("excluirAvaliacao", 65, 88);
  $feedbackObject->addAction("recuperar", 82, 83);
  $feedbackObject->addAction("recuperarAvaliacao", 82, 89);
  $feedbackObject->addAction("apagar", 38, 39);
  $feedbackObject->addAction("novo_forum", 5, 6);

  /* Define ordenação padrão dos fóruns (Data) */
  if ((!isset($ordem_foruns)) || ($ordem_foruns == ""))
    $ordem_foruns = "D";

  /* Verifica se o usuario eh formador. */
  $usr_conv_ativo = EConvidadoAtivo($sock, $cod_usuario, $cod_curso);
  $usr_conv_passivo = EConvidadoPassivo($sock, $cod_usuario, $cod_curso);
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);


  /* Impede o acesso à Lixeira aos usuários que não são formadores. */
  /* status das mensagens: A - Ativo                                */
  /*                       D - Deletado                             */
  /*                       X - Excluído                             */
  if ((!$usr_formador) && (isset($status)) && ($status == 'D'))
  {
    include("../menu_principal.php");
    echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
    /* 1 - Administra�o */
    echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
    /* 61 - A�o exclusiva a formadores. */
    echo("    - ".RetornaFraseDaLista($lista_frases, 61)."</h4>");
    echo("          <br />\n");
    /* 23 - Voltar (gen) */
    echo("          <form name=\"frmErro\" action=\"\" method=\"post\">\n");
    echo("            <input class=\"text\" type=\"button\" name=\"cmdVoltar\" value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=\"history.go(-1);\" />\n");
    echo("          </form>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>\n");
    echo("</html>\n");
    Desconectar($sock);
    exit;
  }

  /* Se o status das mensagens a serem visualizadas não foi setado então   */
  /* lhe atribui o valor 'A' por padrão. Com isso, apenas os fóruns ativos */
  /* serão visualizados, e conseqentemente as mensagens ativas.           */
   if ((!isset($status))||($status != 'D'))
  {
    /* Especifica o status das MENSAGENS a serem listadas: A - Ativo      */
    /*                                                     D - Deletado   */
    /*                                                     X - Excluído   */
    $status = 'A';
  }

  /* Se as mensagens que devem ser listadas forem as ativas, lista os foruns */
  /* ativos. Se forem as deletadas, lista os foruns apagados.                */
  if ($status == 'A')
  {
    /* Lista os f�uns N� deletados: ativo e somente leitura             */
    list ($lista_foruns, $total_por_forum, $total_foruns) = RetornaForuns($sock, $ordem_foruns,$cod_usuario);
  }
  else if ($status == 'D')
  {
    /* Lista os f�uns DELETADOS, apenas os deletados. F�uns exlu�os    */
    /* n� s� listados.                                                  */
    list ($lista_foruns, $total_por_forum, $total_foruns) = RetornaForunsDeletados($sock, $ordem_foruns);
  }

  
    echo("  <script type=\"text/javascript\" src=\"../js-css/sorttable.js\"></script>\n");
  echo("    <script type=\"text/javascript\">\n\n");
  /* 56 - Configuracao alterada com sucesso */
  echo("      var msg_56='".RetornaFraseDaLista($lista_frases, 56)."';\n");
//   /* 57 - Erro ao alterar configuracao */
//   echo("      var msg_57='".RetornaFraseDaLista($lista_frases, 57)."';\n");
  /* 40 - (somente leitura)*/
  echo("      var msg_40='".RetornaFraseDaLista($lista_frases, 40)."';\n");
  echo("      var conteudo=\"\";\n");
  echo("      function MudaOrdenacao(){\n");
  echo("        elementos = document.getElementById('ordem_foruns');\n");
  echo("        var ordem;\n");
  echo("        for (var i = 0; i < elementos.length; i++)\n");
  echo("        {\n");
  echo("          if (elementos.options[i].selected == true){\n");
  echo("            ordem = elementos.options[i].value;\n");
  echo("            break;\n");
  echo("          }\n");
  echo("        }\n");    
  echo("        document.location = 'forum.php?cod_curso=".$cod_curso."&status=".$status."&ordem_foruns='+ordem;\n");
  echo("      }\n\n");

  echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
  echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n\n");

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

  echo("      function AjustePosMenuIE()\n");
  echo("      {\n");
  echo("        if (isIE)\n");
  echo("          return(getPageScrollY());\n");
  echo("        else\n");
  echo("          return(0);\n");
  echo("      }\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  if (($usr_formador)&&($status=='A')){
    echo("        lay_conf = getLayer('layer_conf');\n");
    echo("        lay_novo_forum = getLayer('layer_novo_forum');\n");
  }

  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");

  echo("      function EscondeLayer(cod_layer)\n");
  echo("      {\n");
  echo("        hideLayer(cod_layer);\n");
  echo("      }\n\n");

  echo("      function EscondeLayers()\n");
  echo("      {\n");
  /* Se estiver visualizando os f�uns dispon�eis ent� esconde os layers   */
  /* para cria�o de um novo f�um, para acesso � op�es (Ver, Configurar, */
  /* Renomear e Apagar) de cada f�um e para acesso as op�es (Editar, */
  /* Apagar, Ver Notas, Ver Atividades Entregues e Ver Atividades Pendentes) de avalia�o.                                                     */
  if (($usr_formador)&&($status=='A'))
  {
    echo("        hideLayer(lay_novo_forum);\n");
    echo("        hideLayer(lay_conf);\n");
  }
  echo("      }\n\n");
  
  echo("      function MostraLayer(cod_layer, ajuste){\n");
  echo("        EscondeLayers();\n");
  echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
  echo("        showLayer(cod_layer);\n");
  echo("      }\n\n");

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

  /* Se estiver visualizando os f�uns dispon�eis ent� cria as fun�es JavaScript */
  /* ApagarForum(id), VerLixeira(), Configurar(id), Renomear(id), ApagarAvaliacao(id), AlterarAvaliacao(id) e VerNotas(id).                  */
  if ($usr_formador)
  {
    if ($status == 'A'){

      echo("      function NovoForum(){\n");
      echo("        document.location='acoes.php?cod_curso=".$cod_curso."&acao=novo_forum'\n");
      echo("      }\n");

      echo("      function ApagarForum(id)\n");
      echo("      {\n");
      /* 105 - Deseja realmente apagar este f�um? (Todas perguntas */
      /* pertencentes a ele ser� movidas para a Lixeira. Se o F�um for avalia�o, a avalia�o tamb� ser�movida para a lixeira DAS AVALIA�ES) */
      echo("        if(confirm('".RetornaFraseDaLista($lista_frases, 105)."'))");
      echo("        {\n");
      echo("          document.location='acoes.php?cod_forum='+id+'&cod_curso=".$cod_curso."&acao=apagar&';\n");
      echo("        }\n");
      echo("      }\n\n");



      echo("      function Configurar(id)\n");
      echo("      {\n");
      echo("        document.formConfiguracao.cod_forum.value=id;\n");
      echo("        var item = document.getElementById('forum_'+id);\n");
      echo("        status = item.getAttribute('status_conf');\n");
      echo("        document.getElementById('configuracao_A').innerHTML='&nbsp;';\n");
      echo("        document.getElementById('configuracao_L').innerHTML='&nbsp;';\n");
      echo("        document.getElementById('configuracao_G').innerHTML='&nbsp;';\n");
      echo("        document.getElementById('configuracao_R').innerHTML='&nbsp;';\n");
      echo("        var imagem=\"<img src='../imgs/checkmark_blue.gif' alt='' />\"\n");
      echo("        document.getElementById('configuracao_'+status).innerHTML=imagem;\n");
      echo("        MostraLayer(lay_conf, 0);\n");
      echo("      }\n\n");
            
      echo("      function EdicaoTitulo(codigo, id, valor){\n");
      echo("        if ((valor=='ok')&&(document.getElementById(id+'_text').value!=\"\")){\n");
      echo("          conteudo = document.getElementById(id+'_text').value;\n");
      echo("          document.getElementById(id).innerHTML='';\n");

      echo("        createImg = document.createElement('img');\n");
      echo("        createImg.setAttribute('border', '0');\n");
      echo("        createImg.setAttribute('alt', 'Fóruns de Discussão');\n");
      echo("        createImg.setAttribute('src', '../imgs/icForum.gif');\n");
      echo("        document.getElementById(id).appendChild(createImg);\n\n");

      echo("        espaco = document.createElement('span');\n");
      echo("        espaco.innerHTML='&nbsp;'\n");
      echo("        document.getElementById(id).appendChild(espaco);\n\n");

      echo("        var newLink = document.createElement('a');\n");
      echo("        newLink.setAttribute('id', 'link_'+id);\n");
      echo("        newLink.setAttribute('style', 'text-decoration: underline;');\n");
      echo("        document.getElementById(id).appendChild(newLink);\n");
      echo("        newLink.setAttribute('href', 'ver_forum.php?cod_forum='+codigo+'&cod_curso=".$cod_curso."&status=".$status."');\n");
      echo("        xajax_EditarTituloDinamic('".$cod_curso."', codigo, conteudo, '".RetornaFraseDaLista($lista_frases, 135)."');\n");

      echo("        }else{\n");
              /* 15 - O titulo nao pode ser vazio. */
      echo("          if ((valor=='ok')&&(document.getElementById(id+'_text').value==\"\"))\n");
      echo("            alert('".RetornaFraseDaLista($lista_frases, 15)."');\n");
      echo("          document.getElementById(id).innerHTML='';\n");

      echo("          createImg = document.createElement('img');\n");
      echo("          createImg.setAttribute('border', '0');\n");
      echo("          createImg.setAttribute('alt', 'Fóruns de Discussão');\n");
      echo("          createImg.setAttribute('src', '../imgs/icForum.gif');\n");
      echo("          document.getElementById(id).appendChild(createImg);\n\n");

      echo("          espaco = document.createElement('span');\n");
      echo("          espaco.innerHTML='&nbsp;'\n");
      echo("          document.getElementById(id).appendChild(espaco);\n\n");

      echo("          var newLink = document.createElement('a');\n");
      echo("          newLink.setAttribute('id', 'link_'+id);\n");
      echo("          newLink.innerHTML=conteudo;\n");
      echo("          newLink.setAttribute('href', 'ver_forum.php?cod_forum='+codigo+'&cod_curso=".$cod_curso."&status=".$status."');\n");
      echo("          document.getElementById(id).appendChild(newLink);\n");
      echo("        }\n");
      echo("        document.getElementById('forum_'+codigo).style.textDecoration=\"none\";\n");
      echo("        document.getElementById('forumQtd_'+codigo).style.display=\"\";\n");
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
    echo ("             EdicaoTitulo(id, 'forum_'+id, 'ok');\n"); //A funï¿½ï¿½o e parï¿½metros sï¿½o os mesmos utilizados na funï¿½ï¿½o de ediï¿½ï¿½o jï¿½ utilizada.
    echo ("         }\n\n");
    echo ("         return true;\n");
    echo ("     }\n\n");

      echo("      function Renomear(id){\n");
      echo("        id_aux = id;\n");
      echo("        if (document.getElementById('CancelaEdita'))\n");
      echo("          document.getElementById('CancelaEdita').onclick();\n\n");

      echo("        conteudo = document.getElementById('link_forum_'+id).innerHTML;\n");
      echo("        document.getElementById('forumQtd_'+id).style.fontWeight=\"\";\n\n");

      echo("         document.getElementById('link_forum_'+id).parentNode.removeChild(document.getElementById('link_forum_'+id));\n");

      echo("        createInput = document.createElement('input');\n");
      echo("        createInput.setAttribute('type', 'text');\n");
      echo("        createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
      echo("        createInput.setAttribute('id', 'forum_'+id+'_text');\n\n");
      echo("        if (createInput.addEventListener){\n"); //not IE
      echo("          createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
      echo("        } else if (createInput.attachEvent){\n"); //IE
      echo("          createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
      echo("        }\n");

      echo("        document.getElementById('forum_'+id).appendChild(createInput);\n");
      echo("        xajax_DecodificaString('forum_'+id+'_text', conteudo, 'value');\n\n");

      echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
      echo("        espaco = document.createElement('span');\n");
      echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
      echo("        document.getElementById('forum_'+id).appendChild(espaco);\n\n");

      echo("        createSpan = document.createElement('span');\n");
      echo("        createSpan.className='link';\n");
      echo("        createSpan.onclick= function(){ EdicaoTitulo(id, 'forum_'+id, 'ok'); };\n");
      echo("        createSpan.setAttribute('id', 'OkEdita');\n");
      echo("        createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral, 18)."';\n");
      echo("        document.getElementById('forum_'+id).appendChild(createSpan);\n\n");

      echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
      echo("        espaco = document.createElement('span');\n");
      echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
      echo("        document.getElementById('forum_'+id).appendChild(espaco);\n\n");

      echo("        createSpan = document.createElement('span');\n");
      echo("        createSpan.className='link';\n");
      echo("        createSpan.onclick= function(){ EdicaoTitulo(id, 'forum_'+id, 'canc'); };\n");
      echo("        createSpan.setAttribute('id', 'CancelaEdita');\n");
      echo("        createSpan.innerHTML='".RetornaFraseDaLista($lista_frases_geral, 2)."';\n");
      echo("        document.getElementById('forum_'+id).appendChild(createSpan);\n\n");

      echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
      echo("        espaco = document.createElement('span');\n");
      echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
      echo("        document.getElementById('forum_'+id).appendChild(espaco);\n\n");

      echo("        startList();\n");
      echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
      echo("        document.getElementById('forum_'+id+'_text').select();\n");
      echo("        document.getElementById('forumQtd_'+id).style.display=\"none\";\n");
      echo("      }\n\n");

      echo("      function MudarConfiguracao(status){\n");
      echo("        cod_forum = document.formConfiguracao.cod_forum.value;\n");
      echo("         window.open('configurar_forum.php?cod_curso=".$cod_curso."&cod_forum='+cod_forum+'&status='+status, 'ConfigurarForum', 'height=380,width=660,status=yes,toolbar=no,menubar=no,location=no');\n");
      echo("      }\n");

      if($AcessoAvaliacaoF)
      {
        echo("      function CriarAvaliacao(id,avaliacao)\n");
        echo("      {\n");
        echo("        document.location='../avaliacoes/criar_avaliacao_forum.php?cod_curso=".$cod_curso."&cod_atividade='+id+'';\n");
        echo("      }\n\n");

        /* Abre a janela com a lista de Participantes para ser avaliado */
        echo("      function AvaliarParticipantes(id)\n");
        echo("      {\n");
        echo("        window.open('../avaliacoes/avaliar_participantes.php?cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao='+id,'AvaliarParticipantes','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
        echo("        return false;\n");
        echo("      }\n\n");
      }

    }
    /* Se estiver visualizando a Lixeira ent� cria as fun�es JavaScript ExcluirForum(id) */
    /* SairLixeira().                                                                      */
    else if ($status == 'D')
    {
      echo("      function ExcluirForum(id)\n");
      echo("      {\n");
      /* 91 - Deseja realmente excluir este f�um? N� ser�mais poss�el
       visualiz�lo. Se este f�um for uma avalia�o, a avalia�o tamb� ser�exclu�a.*/
      echo("        if(confirm('".RetornaFraseDaLista($lista_frases, 91)."')){\n");
      echo("          document.location='acoes.php?cod_forum='+id+'&cod_curso=".$cod_curso."&acao=excluir';\n");
      echo("        }\n");
      echo("      }\n\n");

      echo("      function RecuperarForum(id)\n");
      echo("      {\n");
      /* 80 - Deseja realmente recuperar este f�um? Ele ser�configurado para somente leitura. */
      echo("        if(confirm('".RetornaFraseDaLista($lista_frases, 80)."')){\n");
      echo("           document.location='acoes.php?cod_forum='+id+'&cod_curso=".$cod_curso."&acao=recuperar';\n");
      echo("        }\n");
      echo("      }\n\n");



    }
  }

  if ($AcessoAvaliacaoF)
  {
  	echo("      function VerAvaliacao(id) {\n");
    echo("         window.open('../avaliacoes/ver_popup.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&cod_topico=".$cod_topico_raiz."&cod_avaliacao='+id,'VerAvaliacao','width=600,height=450,top=150,left=250,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return(false);\n");
    echo("      }\n");
      
    if ($status == 'A'){
	    echo("      function VerNotas(id){\n");
	    echo("        window.open(\"../avaliacoes/ver_notas.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+id,\"VerNotas\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
	    echo("        return(false);\n");
	    echo("      }\n\n");
	
	    echo("      function VerParticipacao(id){\n");
	    echo("        window.open(\"../avaliacoes/ver_participacao.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+id,\"VerParticipacao\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
	    echo("        return(false);\n");
	    echo("      }\n");
	
	    echo("      function HistoricodoDesempenho(id){\n");
	    echo("         window.open(\"../avaliacoes/historico_desempenho_todos.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&VeioDaAtividade=1&cod_avaliacao=\"+id,\"HistoricoDesempenho\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
	    echo("        return(false);\n");
	    echo("      }\n\n");
    }

  }


  echo("    </script>\n\n");

  $objAjax->printJavascript("../xajax_0.2.4/"); 

  include("../menu_principal.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 1 - Fóruns de Discussão */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1));

  /* Se estiver visualizando a Lixeira adiciona esta informação no cabeçalho. */
  if ($status == 'D')
  {
    /* 16 - Lixeira */
    echo(" - ".RetornaFraseDaLista($lista_frases_geral, 16));
  }
  echo("</h4>\n");
  // 3 A's - Muda o Tamanho da fonte
  echo("<div id=\"mudarFonte\">\n");
  echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  /* Se o usuario FOR Formador entao exibe os controles. */
  if ($usr_formador)
  {
    echo("                <ul class=\"btAuxTabs\">\n");
    /* Se estiver visualizando os fóruns disponíveis então cria um link para o */
    /* layer novo_forum e outro para a função JavaScript VerLixeira().         */
    if ($status == 'A')
    {
      /* 2 - Novo fórum */
      echo("                  <li><span onclick=\"MostraLayer(lay_novo_forum, 0); document.getElementById('nome').focus(); document.getElementById('nome').value='';\">".RetornaFraseDaLista($lista_frases,2)."</span></li>\n");
      /* 22 - Ver Lixeira */
      echo("                  <li><a href='forum.php?cod_curso=".$cod_curso."&amp;status=D&amp;ordem_foruns=".$ordem_foruns."'>".RetornaFraseDaLista($lista_frases_geral, 22)."</a></li>\n");
    }
    /* Se estiver visualizando a Lixeira então cria um link para a função       */
    /* JavaScript SairLixeira().                                                */
    else if ($status == 'D')
    {
      /* 35 - Sair da Lixeira */
      echo("                  <li><a href='forum.php?cod_curso=".$cod_curso."&amp;status=A&amp;ordem_foruns=".$ordem_foruns."'>".RetornaFraseDaLista($lista_frases,35)."</a></li>\n");
    }
    echo("                </ul>\n");
  }

  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr class=\"head01\">\n");
  echo("              <td style=\"text-align:right;\">\n");


  /* Repassa o status da mensagens que ser� selecionadas: A ou D (Lixeira)            */
  /* Se o status for A ent� ser� listadas os f�uns e mensagens ativos, do contr�io */
  /* ficar� vis�eis os f�uns deletados e ser� listadas as mensagens apagadas.      */

  /* 41 - Ordenar por:  */
  echo(RetornaFraseDaLista($lista_frases, 41)."\n");

  if ($ordem_foruns == "N")
    $nome_select = "selected";
  else
    $data_select = "selected";

  echo("                <select name=\"ordem_foruns\" class=\"input\" id=\"ordem_foruns\" onchange='MudaOrdenacao();' style=\"margin:5px 5px 7px 0; \">\n");
  /* 44 - data */
  echo("                  <option value='D' selected=\"".$data_select."\">".RetornaFraseDaLista($lista_frases, 44)."</option>\n");
  /* 46 - título */
  echo("                  <option value='N' ".$nome_select.">".RetornaFraseDaLista($lista_frases, 46)."</option>\n");
  echo("                </select>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"sortable tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  /* 58 - Fórum */
  echo("                    <td style=\"cursor:pointer\" class=\"alLeft\">".RetornaFraseDaLista($lista_frases,58)."</td>\n");

  if($usr_formador){
    /* 70 (ger) - Opções */
    echo("                    <td class=\"sorttable_nosort\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
  }
  /* 11 - Data */
  echo("                    <td style=\"cursor:pointer\" align=\"center\">".RetornaFraseDaLista($lista_frases,11)."</td>\n");
 
  if($AcessoAvaliacaoF){
    /* 92 - Avaliação */
    echo("                    <td style=\"cursor:pointer\" align=\"center\">".RetornaFraseDaLista($lista_frases,92)."</td>\n");
  }

  echo("                  </tr>\n");

  /* 114 - Não há nenhum fórum de discussão*/
  if (($total_foruns)==0) {
    echo("                  <tr>\n");	
    echo("                    <td colspan=\"5\">".RetornaFraseDaLista($lista_frases,114)."</td>\n");
    echo("                  </tr>\n");

  }else{


    $cor = 1;
    $ultimo_acesso = PenultimoAcesso($sock, $cod_usuario, "");
    $icone = "<img src=\"../imgs/icForum.gif\" border=0 alt=\"".RetornaFraseDaLista($lista_frases,1)."\" />";
    foreach($lista_foruns as $num => $linha_lista_foruns)
    {
  
      $permitido=VerificaPermissao($sock, $cod_usuario, $lista_foruns[$num]['permissoes']);
      $cor++;
      $cor%=2;
      
      echo("                  <tr class=\"altColor".$cor."\">\n");
      echo("                    <td width=\"65%\" class=\"alLeft\">\n");

      if ($lista_foruns[$num]['data'] > $ultimo_acesso){
        $style="style=\"text-decoration: underline;\"";
      }else{
        $style="style=\"font-weight:normal;\"";
      }

      echo("                      <span id=\"forum_".$lista_foruns[$num]['cod_forum']."\" status_conf=\"".$lista_foruns[$num]['status']."\">\n");
      echo("                        ".$icone." <a id=\"link_forum_".$lista_foruns[$num]['cod_forum']."\" href='ver_forum.php?cod_forum=".$lista_foruns[$num]['cod_forum']."&amp;cod_curso=".$cod_curso."&amp;status=".$status."' ".$style.">".$lista_foruns[$num]['nome']."</a>\n");
      echo("                      </span>\n");
      echo("                      <span id=\"forumQtd_".$lista_foruns[$num]['cod_forum']."\"> (".$total_por_forum[$num].")</span>\n");


      /* Se o fórum for somente leitura então informa com um texto. 
          Caso o status seja R, e o usuário não estiver na lista de permitidos, o forum é somente leitura.
      */
      echo("                      <span id=\"forum_leitura_".$lista_foruns[$num]['cod_forum']."\">");
      if (($lista_foruns[$num]['status'] == 'L') || (($lista_foruns[$num]['status'] == 'R') && (!$permitido)) || (($lista_foruns[$num]['status'] == 'G') && (!$permitido)))
      {
        /* 40 - (somente leitura)*/
        echo(RetornaFraseDaLista($lista_frases, 40));
      }
      echo("</span>\n");
      echo("                    </td>\n");

      /* Se o usuário for formador então cria links com acesso às opções  */
      /* (Ver, Configurar, etc.), se estiver visualizando os fóruns disponíveis, */
      /* cria liks com acesso às opções (Ver, Excluir) se estiver visualizando a Lixeira. */   
      $EhAvaliacao = ForumEhAvaliacao($sock,$lista_foruns[$num]['cod_forum']);   
      if ($usr_formador){
        echo("                    <td width=\"15%\" align=\"center\" valign=\"top\" class=\"botao2\">\n");
        echo("                      <ul>\n");
        if ($status == 'A')
        {
          /* 6 - Configurar */
          echo("                        <li><span onclick='Configurar(\"".$lista_foruns[$num]['cod_forum']."\");'>".RetornaFraseDaLista($lista_frases_geral, 6)."</span></li>\n");
          /* 19 - Renomear */
          echo("                        <li><span onclick='Renomear(\"".$lista_foruns[$num]['cod_forum']."\");'>".RetornaFraseDaLista($lista_frases_geral, 19)."</span></li>\n");
          /* 1 - Apagar */
          echo("                        <li><span onclick='ApagarForum(\"".$lista_foruns[$num]['cod_forum']."\");'>".RetornaFraseDaLista($lista_frases_geral, 1)."</span></li>\n");
          /* 95 - Criar Avalia�o */
          if($AcessoAvaliacaoF && !$EhAvaliacao)
            echo("                        <li><span onclick='CriarAvaliacao(\"".$lista_foruns[$num]['cod_forum']."\");'>".RetornaFraseDaLista($lista_frases, 95)."</span></li>\n");      
        }
        else if ($status == 'D')
        {
          /* 48 - Recuperar (geral) */
          echo("                        <li><span onclick='RecuperarForum(\"".$lista_foruns[$num]['cod_forum']."\");'>".RetornaFraseDaLista($lista_frases_geral, 48)."</span></li>\n");
          /* 12 - Excluir */
          echo("                        <li><span onclick='ExcluirForum(\"".$lista_foruns[$num]['cod_forum']."\");'>".RetornaFraseDaLista($lista_frases_geral, 12)."</span></li>\n");
        }
        echo("                      </ul>\n");
        echo("                    </td>\n");
      }

      echo("                    <td width=\"10%\" align=\"center\">".UnixTime2Data($lista_foruns[$num]['data'])."</td>\n");

      if($EhAvaliacao){

          $cod_avaliacao=RetornaCodAvaliacao($sock,$lista_foruns[$num]['cod_forum']);
          if ($usr_aluno || $usr_conv_ativo || $usr_conv_passivo){
            // G 35 - Sim
            echo("                    <td  width=\"10%\" align=\"center\"><span class=\"link\" onClick=\"VerAvaliacao(".RetornaCodAvaliacao($sock,$lista_foruns[$num]['cod_forum']).");\">".RetornaFraseDaLista($lista_frases_geral,35)."</span></td>\n");
          }
          else if ($usr_formador){
            if ($status=='A')  //Fórum ativo
            {
              // G 35 - Sim
            	echo("                    <td  width=\"10%\" align=\"center\"><span class=\"link\" onClick=\"VerAvaliacao(".RetornaCodAvaliacao($sock,$lista_foruns[$num]['cod_forum']).");\">".RetornaFraseDaLista($lista_frases_geral,35)."</span></td>\n");
            }
            else
            {
              // G 35 - Sim
              echo("                    <td  width=\"10%\" align=\"center\"><span class=\"link\" onClick=\"VerAvaliacao(".RetornaCodAvaliacao($sock,$lista_foruns[$num]['cod_forum']).");\">".RetornaFraseDaLista($lista_frases_geral,35)."</span></td>\n");
            }
          }
          else
          {
            /*93 - erro interno....*/
            echo("                    <td  width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 93)."</td>\n");
          }
        }
        elseif (($status=='D') && (ForumEraAvaliacao($sock,$lista_foruns[$num]['cod_forum'])))     //Está na lixeira e status da avaliacao=Apagada
        {
          $cod_avaliacao=RetornaCodAvaliacaoDeletada($sock,$lista_foruns[$num]['cod_forum']);
          // G 35 - Sim
          echo("                    <td  width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral,35)."</td>\n");
  
        }
        else
        /* 36 - Não*/
        echo("                    <td width=\"10%\" align=\"center\">".RetornaFraseDaLista($lista_frases_geral, 36)."</td>\n");
      }
      echo("                  </tr>\n");
  
      /* Incrementa o contador. */
//       $num++;
    }
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");


  /* Se o usu�io for formador ent� cria a fun�o JavaScript que testa o nome do    */
  /* novo f�um e o layer novo_forum, se estiver visualizando os f�uns dispon�eis, */
  /* ou cria o layer de acesso � op�es (Ver, Excluir), se estiver visualizando a   */
  /* Lixeira.                                                                        */



  if ($usr_formador)
  {
    if ($status == 'A')
    {
   	  /* layer_novo_forum */
   	  echo("    <script type=\"text/javascript\">\n");
   	  echo("      function testa_nome(form){\n");
   	  /* Elimina os espaços para verificar se o titulo nao eh formado por apenas espaços */
   	  echo("        forum_nome = form.nome.value;\n");
      echo("        while (forum_nome.search(\" \") != -1){\n");
      echo("          forum_nome = forum_nome.replace(/ /, \"\");\n");
      echo("        }\n");
      echo("        if (forum_nome == ''){\n");
      // 4 - O fórum deve ter um nome. 
      echo("          alert('".RetornaFraseDaLista($lista_frases, 4)."');\n");
      echo("          return(false);\n");
      echo("		}\n");
      /*
      echo("		elseif(form.avaliacao.checked == true){\n");
      echo("          <input type=\"hidden\" name=\"avalia\" value=\"true\" />\n");
	  echo("          return(true);\n");
      echo("        }\n");
      */
	  echo("        else {\n");

      echo("          return(true);\n");
      echo("        }\n");
      echo("      }\n\n");
      echo("    </script>\n\n");

      /* Novo Item */
      echo("    <div id=\"layer_novo_forum\" class=\"popup\">\n");
      echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_novo_forum);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
      echo("      <div class=\"int_popup\">\n");
      echo("        <form name=\"form_novo_forum\" method=\"post\" action=\"acoes.php\" onsubmit='return (testa_nome(document.form_novo_forum));'>\n");
      echo("          <div class=\"ulPopup\">\n");    
      /* 132 - Digite o nome do fórum a ser criado aqui: */
      echo("            ".RetornaFraseDaLista($lista_frases,132)."<br />\n");
      echo("            <input class=\"input\" type=\"text\" name=\"nome\" id=\"nome\" value=\"\" maxlength=\"150\" /><br />\n");
	  /* 136 - Criar Avaliação para este Fórum?: */
      echo("			<input type=\"checkbox\" name=\"avaliacao\" value='S'>".RetornaFraseDaLista($lista_frases,136)."\n<br>");
      echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
      echo("            <input type=\"hidden\" name=\"acao\" value=\"novo_forum\" />\n");




      /* 18 - Ok (gen) */
      echo("            <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
      /* 2 - Cancelar (gen) */
      echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\" onclick=\"EscondeLayer(lay_novo_forum);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
      echo("         </div>\n");
	  echo("        </form>\n");

      echo("      </div>\n");
      echo("    </div>\n\n");


      //div do Layer de alteração da configuração
      echo("    <div id='layer_conf' class=\"popup\">\n");
      echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_conf);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
      echo("      <div class=\"int_popup\">\n");
      echo("        <form method=\"post\" id=\"formConfiguracao\" name=\"formConfiguracao\" action=\"\">\n");
      echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"$cod_curso\" />\n");
      echo("          <input type=\"hidden\" name=\"cod_forum\" value=\"\" />\n");
      echo("          <input type=\"hidden\" name=\"nova_configuracao\" id=\"nova_relevancia\" value=\"\" />\n");
      echo("          <input type=\"hidden\" name=\"texto_feedback_sucesso\" id=\"texto_feedback_sucesso\" value=\"".RetornaFraseDaLista($lista_frases, 56)."\" />\n");
      echo("          <input type=\"hidden\" name=\"texto_feedback_falha\" id=\"texto_feedback_falha\" value=\"".RetornaFraseDaLista($lista_frases, 57)."\" />\n");
      echo("        </form>\n");
      echo("        <ul class=\"ulPopup\">\n");
      /* 54 - Escrita e Leitura */
      echo("          <li onclick=\"xajax_MudarConfiguracaoDinamic(xajax.getFormValues('formConfiguracao'), 'A'); EscondeLayers();\">\n");
      echo("            <span class=\"check\" id=\"configuracao_A\"></span>\n");
      echo("            <span>".RetornaFraseDaLista($lista_frases, 54)."</span>\n");
      echo("          </li>\n");
      /* 55 - Somente Leitura */
      echo("          <li onclick=\"xajax_MudarConfiguracaoDinamic(xajax.getFormValues('formConfiguracao'), 'L'); EscondeLayers();\">\n");
      echo("            <span class=\"check\" id=\"configuracao_L\"></span>\n");      
      echo("            <span>".RetornaFraseDaLista($lista_frases, 55)."</span>\n");
      echo("          </li>\n");
      /* 107 - Definir alunos com permissão de Escrita e Leitura */
      echo("          <li onclick=\"MudarConfiguracao('G'); EscondeLayers();\">\n");
      echo("            <span class=\"check\" id=\"configuracao_G\"></span>\n");      
      echo("            <span>".RetornaFraseDaLista($lista_frases, 107)."</span>\n");
      echo("          </li>\n");
      /* 108 - Definir alunos com permissão de Escrita (a leitura é aberta para todos) */
      echo("          <li onclick=\"MudarConfiguracao('R'); EscondeLayers();\">\n");
      echo("            <span class=\"check\" id=\"configuracao_R\"></span>\n");      
      echo("            <span>".RetornaFraseDaLista($lista_frases, 108)."</span>\n");
      echo("          </li>\n");
      echo("        </ul>\n");
      echo("      </div>\n");
      echo("    </div>\n\n");
    }
  }
    
  echo("        </td>\n");
  echo("      </tr>\n"); 

  include("../tela2.php");
  echo("  </body>\n");
  echo("</html>\n");

  Desconectar($sock);
?>

