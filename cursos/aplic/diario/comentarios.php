<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/diario/comentarios.php

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
  ARQUIVO : cursos/aplic/diario/comentarios.php
  ========================================================== */

  $bibliotecas = "../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("diario.inc");

  require_once("../xajax_0.5/xajax_core/xajax.inc.php");

  // Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../xajax_0.5");
  $objAjax->configure('errorHandler', true);
  // Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"ExcluirComentDinamic");
  // Registra funções para uso de menu_principal.php
  $objAjax->register(XAJAX_FUNCTION,"DeslogaUsuarioCursoDinamic");
  // Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $cod_ferramenta = 14;
  $cod_ferramenta_ajuda = $cod_ferramenta;
  $cod_pagina_ajuda = 4;
  include("../topo_tela.php");

  // instanciar o objeto, passa a lista de frases por parametro
  $feedbackObject =  new FeedbackObject($lista_frases);
  //adicionar as acoes possiveis, 1o parametro Ã© a aÃ§Ã£o, o segundo Ã© o nÃºmero da frase para ser impressa se for "true", o terceiro caso "false"
  $feedbackObject->addAction("comentar", 29, 30);
  $feedbackObject->addAction("excluir_comentario", 72,0);

  /* Verifica se o usuario ï¿½ visitante. */
  $usr_visitante = EVisitante($sock, $cod_curso, $cod_usuario);

  if (isset ($acao) && $acao == "mudarcomp")
  {
    AlteraTipoCompartilhamento ($sock, $cod_item, $tipo_comp);
  }

  /* Se o código do usuário cujo item será exibido não   */
  /* estiver definido, atribui-lhe o código do usuário   */
  /* que está visualizando o item, por padrão.           */
  /* Esta reatribuição é necessária porque as páginas    */
  /* editar_item2 e renomear_item2 não passam a variável */
  /* cod_propriet, visto que essas ações só poderiam ser */
  /* feitas se o usuário for o proprietário do diário e  */
  /* dos itens.                                          */
  if (!isset($cod_propriet))
    $cod_propriet = $cod_usuario;
    
  // verifica se eh dono do diario
  $dono_diario = VerificaDonoDiario ($sock, $cod_curso, $cod_usuario, $cod_propriet);

  /*
 ==================
 Funcoes JavaScript
 ==================
 */
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function VerificaCheck(){\n");
  echo("        var i;\n");
  echo("        var j=0;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        var Cabecalho = document.getElementById('checkMenu');\n");
  echo("        array_itens = new Array();\n");
  echo("        for (i=0; i < cod_itens.length; i++){\n");
  echo("          if (cod_itens[i].checked){\n");
  echo("            var item = cod_itens[i].id.split('_');\n");
  echo("            array_itens[j]=item[1];\n");
  echo("            j++;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        if (j == (cod_itens.length)) Cabecalho.checked=true;\n");
  echo("        else Cabecalho.checked=false;\n");
  echo("        if(j > 0){\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
  echo("        }else{\n");
  echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
  echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
  echo("        }\n");
  echo("      }\n\n");


  echo("      function CheckTodos(){\n");
  echo("        var e;\n");
  echo("        var i;\n");
  echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
  echo("        var cod_itens=document.getElementsByName('chkItem');\n");
  echo("        for(i = 0; i < cod_itens.length; i++){\n");
  echo("          e = cod_itens[i];\n");
  echo("          e.checked = CabMarcado;\n");
  echo("        }\n");
  echo("        VerificaCheck();\n");
  echo("      }\n\n");


  echo("      function ExcluirSelecionados(){\n");
      /* 69 - Tem certeza de que deseja excluir este comentÃ¡rio? */
      /* 70 - (o item serÃ¡ excluÃ­do definitivamente) */
  echo("        if (confirm('".RetornaFraseDaLista($lista_frases,69)."\\n".RetornaFraseDaLista($lista_frases,70)."')){\n");
  echo("          xajax_ExcluirComentDinamic('".$cod_curso."', array_itens,'".$cod_ferramenta."', '".$cod_usuario."');\n");
  echo("        }\n");
  echo("      }\n\n");


  echo("      function Recarregar(acao, atualizacao){\n");
  echo("        window.location='comentarios.php?&cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_item=".$cod_item."&cod_propriet=".$cod_propriet."&acao='+acao+'&atualizacao='+atualizacao;");
  echo("      }\n\n");


  echo("      function Iniciar()\n");
  echo("      {\n");
  $feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
  echo("        startList();\n");
  echo("      }\n\n");


  echo("      function VerComentario(id)\n");
  echo("      {\n");
  echo("        window.open(\"ver_comentario.php?&cod_curso=".$cod_curso);
  echo("&cod_comentario=\" + id + \"&cod_propriet=".$cod_propriet."&cod_item=".$cod_item."\", \"Comentario\",");
  echo("\"width=450,height=300,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,");
  echo("resizable=yes\");\n");
  echo("      return(false);\n");
  echo("    }\n\n");


  echo("      function OpenWindowPerfil(id)\n");
  echo("      {\n");
  echo("        window.open(\"../perfil/exibir_perfis.php?");
  echo("&cod_curso=".$cod_curso."&cod_aluno[]=\" + id, \"PerfilDisplay\",\"width=600,height=400,");
  echo("top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
  echo("        return(false);\n");
  echo("      }\n\n");


  echo("      function EnviarComent(){\n");
  echo("        document.getElementById('OKComent').style.visibility='visible';\n");
  echo("        document.getElementById('cancComent').style.visibility='visible';\n");
  echo("        document.getElementById('textArea_coment').style.visibility='visible';\n");
  echo("        document.getElementById('td_coment').style.background='';\n");
  echo("        document.getElementById('text_coment').className='divShow';\n");
  echo("        document.getElementById('button_coment').className='divShow';\n");
  echo("        elementoDiv = document.getElementById('text_coment');\n");
  echo("        elementoDiv.removeChild(elementoDiv.lastChild);\n");
  echo("        document.getElementById('btnComentar').onclick = function() {};\n");
  echo("      }\n\n");


  echo("      function CancelarComent(){\n");
  echo("        document.getElementById('textArea_coment').value='';\n");
  echo("        document.getElementById('td_coment').style.background='#DCDCDC';\n");
  echo("        document.getElementById('text_coment').className='divHidden';\n");
  echo("        document.getElementById('button_coment').className='divHidden';\n");
  echo("        elementoDiv = document.getElementById('text_coment');\n");
  echo("        createBr = document.createElement('br');\n");
  echo("        elementoDiv.appendChild(createBr);\n");
  echo("        document.getElementById('btnComentar').onclick = function() { EnviarComent(); };\n");
  echo("        element=document.getElementsByName('input_files[]');\n");
  echo("        for (i=0; i < element.length; i++){\n");
  echo("          document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("          document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("          document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("          document.getElementById('text_coment').removeChild(element[i]);\n");
  echo("          i--;\n");
  echo("        }\n");
  echo("      }\n\n");


  echo("      function submitForm(){\n");
  echo("        if(document.getElementById('textArea_coment').value==''){\n");
  /* 106 - Seu comentï¿½io estï¿½ vazio. Para nï¿½o envia-lo, pressione o botao Cancelar. */
  echo("          alert('".RetornaFraseDaLista($lista_frases,106)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        element=document.getElementsByName('input_files[]');\n");
  echo("        for (i=0; i < element.length; i++){\n");
  echo("          if((element[i].value)==\"\"){\n");
  echo("            document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('text_coment').removeChild(element[i].nextSibling);\n");
  echo("            document.getElementById('text_coment').removeChild(element[i]);\n");
  echo("            i--;\n");
  echo("          }\n");
  echo("        }\n");
  echo("        return true;\n");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  include("../menu_principal.php");

  echo("          <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

  /* 1 - Diï¿½rio de Bordo */
  echo("          <h4>".RetornaFraseDaLista($lista_frases, 1));
  /* 12 - Comentï¿½rios */
  echo(" - ".RetornaFraseDaLista($lista_frases, 12)."</h4>\n");

   /* 509 - Voltar */
  echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("            <a href=\"#\" onclick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");

  echo("          <img alt=\"".RetornaFraseDaLista($lista_frases, 1)."\" src=\"../imgs/icPerfil.gif\" border=\"0\" />&nbsp;<a class=\"text\" href=\"#\" onclick=\"OpenWindowPerfil(".$cod_propriet.");return(false);\">".NomeUsuario($sock, $cod_propriet, $cod_curso)."</a>\n");

  /* Obtï¿½m o cod_usuario, titulo, texto e a data do item especificado. */
  $item_dados = RetornaItem($sock, $cod_item);


  /* Obtï¿½m a data do penï¿½ltimo acesso do usuï¿½rio para comparï¿½-la com   */
  /* datas dos comentï¿½rios.                                            */
  $penultac = PenultimoAcesso($sock, $cod_usuario, "");

  /* Obtï¿½m o cod_comentario, cod_comentarist e a data dos comentï¿½rios. */
  $item_coment = RetornaComentariosItem($sock, $cod_item);
  /* Conta quantos comentï¿½rios o item possui.                          */
  $total_coment = count($item_coment);

   //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");

  
  echo("                  <li><a href=\"ver_item.php?&amp;cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;cod_usuario=".$cod_usuario."&amp;cod_propriet=".$cod_propriet."\">".RetornaFraseDaLista($lista_frases_geral, 23)." ao item</a></li>\n");
  
  /* 42 - Voltar ao diï¿½rio */
  echo("                  <li><a href=\"diario.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_propriet."&amp;origem=diario\" >".RetornaFraseDaLista($lista_frases, 42)."</a></li>\n");
  
  /* 5 - Ver outros diï¿½rios */
  echo("                  <li><a href=\"ver_outros.php?&amp;cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."&amp;cod_propriet=".$cod_propriet."&amp;origem=diario\">".RetornaFraseDaLista($lista_frases, 5)."</a></li>\n");
  
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs03\">\n");
  
  // Visitantes podem ver os diários, mas não podem fazer comentários
  if (!$usr_visitante) {
    /* 3 - Comentar (Ger) */
    echo("                  <li><span id=\"btnComentar\" onclick=\"EnviarComent(); document.getElementById('textArea_coment').focus();\">".RetornaFraseDaLista($lista_frases_geral,3)."</span></li>\n");
  }

  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  /* <!----------------- Tabela Interna -----------------> */
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  echo("                  <tr>\n");

  if (count($item_coment) > 0)
    echo("                    <td colspan=\"3\" width=\"70%\" id=\"td_coment\" style=\"background-color:#DCDCDC;\" align=\"left\">\n");
  else
    echo("                    <td colspan=\"3\" width=\"70%\" id=\"td_coment\" align=\"left\">\n");

  echo("                      <form name=\"formFiles\" id=\"formFiles\" action=\"acoes.php\" method=\"post\" enctype=\"multipart/form-data\" onsubmit=\"return(submitForm());\">\n");
  echo("                        <input type=\"hidden\" name=\"cod_propriet\" value=\"".$cod_propriet."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("                        <input type=\"hidden\" name=\"cod_item\" value=\"".$cod_item."\" />\n");
  echo("                        <input type=\"hidden\" name=\"acao\" value=\"comentar\" />\n");

  if (count($item_coment) > 0)
    echo("                        <div id=\"text_coment\" class=\"divHidden\">\n");
  else
    echo("                        <div id=\"text_coment\" class=\"divShow\">\n");

  /* 3 - Comentar (Ger) */
  echo("                          <b>".RetornaFraseDaLista($lista_frases_geral, 3).":</b><br />\n");
  echo("                          <textarea name=\"comentario\" id=\"textArea_coment\" rows=\"8\" cols=\"70\" style=\"border: 2px solid #9bc;\"></textarea><br /><br />\n");
  echo("                        </div>\n");

  if (count($item_coment) > 0)
  {
    echo("                        <div id=\"button_coment\" class=\"divHidden\">\n");
    /* 18 - Ok (Ger) */
    echo("                          <input type=\"submit\" id=\"OKComent\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" class=\"input\" />\n");
    /* 2 - Cancelar (Ger) */
    echo("                          <input type=\"button\" id=\"cancComent\" onclick=\"CancelarComent();\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" class=\"input\" />\n");
    echo("                        </div>\n");
  }
  else
  {
    echo("                        <div id=\"button_coment\" class=\"divShow\">\n");
    /* 18 - Ok (Ger) */
    echo("                          <input type=\"submit\" id=\"OKComent\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" class=\"input\" />\n");
    /* 2 - Cancelar (Ger) */
    echo("                          <input type=\"button\" id=\"cancComent\" onclick=\"window.location='ver_item.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$cod_item."';\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" class=\"input\" />\n");
    echo("                        </div>\n");
  }

  echo("                      </form>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  echo("                  <tr class=\"head\">\n");
  if($dono_diario){
    echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
  }
  
  /* 12 - Comentï¿½rios */
  echo("                    <td>".RetornaFraseDaLista($lista_frases, 12)."</td>\n");
  /* 45 - Autor */
  echo("                    <td width=\"30%\">".RetornaFraseDaLista($lista_frases, 45)."</td>\n");
  echo("                  </tr>\n");

  if (count($item_coment) == 0)
    /* 13 - itens nï¿½o comentados */
    echo("                <tr><td colspan=\"3\">".RetornaFraseDaLista($lista_frases, 13)."</td></tr>\n");

  else
  {
    $i = 0;
    while ($i < $total_coment)
    {
      /* Se a data do comentï¿½rio for maior que a data do penï¿½ltimo       */
      /* acesso do usuï¿½rio, exibe os dados do comentï¿½rio em negrito.     */
      if ($item_coment[$i]['data'] > $penultac)
      {
        $bopentag = "<b>";
        $bclosetag = "</b>";
      }
      else
      {
        $bopentag = "";
        $bclosetag = "";
      }

      /* Extraï¿½ o tï¿½tulo do item a qual o comentï¿½rio pertence, o cï¿½digo do usuï¿½rio */
      /* que fez o comentï¿½rio ($cod_comentarist), o texto e a data do comentï¿½rio.  */
      $coment_dados = RetornaComentario($sock, $item_coment[$i]['cod_comentario']);
      echo("                  <tr>\n");
      if($dono_diario){

        echo("                    <td width=\"2%\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$item_coment[$i]['cod_comentario']."\" onclick='VerificaCheck();' value=\"".$item_coment[$i]['cod_comentario']."\" /></td>\n");
      }
      echo("                    <td align=\"left\">".$coment_dados['comentario']."</td>\n");

      /* Cria um link para o perfil do usuï¿½rio.                              */
      echo("                    <td width=\"30%\">\n");
      echo("                      <a href=\"#\" onclick='OpenWindowPerfil(".$item_coment[$i]['cod_comentarist']);
      echo(");return(false);'>".$bopentag.NomeUsuario($sock, $item_coment[$i]['cod_comentarist'], $cod_curso));
      echo($bclosetag."</a>\n");
      echo("                      <br />\n");
      /* 44 - Comentï¿½rio feito em  */
      echo($bopentag.RetornaFraseDaLista($lista_frases, 44).":<br />");
      echo(UnixTime2Data($item_coment[$i]['data'])." - ");
      echo(UnixTime2Hora($item_coment[$i]['data']).$bclosetag."\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");

      /* Incrementa o contador.                                              */
      $i++;
    }
  }

  /* <!----------------- Fim Tabela Interna -----------------> */
  echo("                </table>\n");
  /* <!----------------- Fim Tabelï¿½o -----------------> */
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  
    if($dono_diario){
    echo("          <ul>\n");
    echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">Excluir selecionados</span></li>\n");
    echo("          </ul>\n");
  }
  
  echo("          <br />\n");    
  /* 509 - voltar, 510 - topo */
  echo("          <ul class=\"btsNavBottom\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span><span><a href=\"#topo\">&nbsp;".RetornaFraseDaLista($lista_frases_geral,510)."&nbsp;&#94;&nbsp;</a></span></li></ul>\n");
      
  echo("        </td>\n"); 
  echo("      </tr>\n");  


  include("../tela2.php");
  echo("</body>\n");
  echo("</html>\n");
?>