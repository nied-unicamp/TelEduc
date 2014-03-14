<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/editar_categoria.php

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
  ARQUIVO : administracao/editar_categoria.php
  ========================================================== */


  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");
  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");

  VerificaAutenticacaoAdministracao();

  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  $objAjax->configure('errorHandler', true);
  $objAjax->register(XAJAX_FUNCTION,"EditarTituloDinamic");
  $objAjax->register(XAJAX_FUNCTION,"DecodificaString");
  $objAjax->register(XAJAX_FUNCTION,"ApagarDinamic");
  $objAjax->register(XAJAX_FUNCTION,"InserirCategoriaDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\" type=\"text/javascript\">\n");

?>

function HTMLNovaCategoria(pasta, cod_pasta, frase, frase2){
  var tr_principal = "<tr id=\"tr_" + cod_pasta + "\">";
  var td_1 = "<td><span id=\"titulo_" + cod_pasta + "\">" + pasta + "</span></td>";

  var td_2 = "<td><ul class=\"botao2\"><li><span onclick='Renomear(\"" + cod_pasta + "\");'>" + frase + "</span></li><li><span style=\"href: #\" title=\"Apagar\" onclick='ApagarCategoria(\"" + cod_pasta + "\");'>" + frase2 + "</span></li></ul></td></tr>";

  var principal = document.createElement('tr');
  var coluna_nome = document.createElement('td');
  var coluna_botoes = document.createElement('td');

  coluna_nome.innerHTML = td_1;
  coluna_botoes.innerHTML = td_2;
  principal.innerHTML = tr_principal;
  principal.setAttribute('id', 'tr_'+cod_pasta);
  principal.appendChild(coluna_nome);
  principal.appendChild(coluna_botoes);

  document.getElementById('tr_master').parentNode.appendChild(principal);
}
  
<?php
  echo("      var conteudo;\n\n");

  /* Funcao Edicao Titulo */
  echo("      function EdicaoTitulo(codigo, id, valor){\n");
  echo("        if ((valor=='ok')&&(document.getElementById(id+'_text').value!=\"\")){\n");
  echo("          conteudo = document.getElementById(id+'_text').value;\n");
  echo("          document.getElementById(id).innerHTML='';\n");
  echo("          xajax_EditarTituloDinamic('".$cod_curso."', codigo, conteudo);\n");
  echo("        }else{\n");
  echo("          if ((valor=='ok')&&(document.getElementById(id+'_text').value==\"\"))\n");
  // 513 - O campo Categoria não pode ser deixado em branco
  echo("            alert('".RetornaFraseDaLista($lista_frases_adm,513)."');\n");
  echo("          document.getElementById('titulo_'+codigo).innerHTML=conteudo;\n");
  echo("        }\n");
  echo("        document.getElementById('titulo_'+codigo).style.textDecoration=\"none\";\n");
  echo("      }\n");

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
  echo ("             EdicaoTitulo(id, 'titulo_'+id, 'ok');\n");
  echo ("         }\n\n");
  echo ("         return true;\n");
  echo ("     }\n\n");

  /* Função Renomear */
  echo("      function Renomear(id)\n");
  echo("      {\n");
  echo("        if (document.getElementById('CancelaEdita'))\n");
  echo("          document.getElementById('CancelaEdita').onclick();\n\n");

  echo("        id_aux = id;\n");

  echo("        conteudo = document.getElementById('titulo_'+id).innerHTML;\n");
  echo("        document.getElementById('titulo_'+id).innerHTML='';\n");

  echo("        createInput = document.createElement('input');\n");
  echo("        createInput.setAttribute('type', 'text');\n");
  echo("        createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
  echo("        createInput.setAttribute('id', 'titulo_'+id+'_text');\n\n");
  echo("        createInput.setAttribute('onkeypress', 'EditaTituloEnter(this, event, id_aux)');\n\n");

  echo("        document.getElementById('titulo_'+id).appendChild(createInput); \n");
  echo("        xajax_DecodificaString('titulo_'+id+'_text', conteudo, 'value');\n\n");

  echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('titulo_'+id).appendChild(espaco);\n\n");

  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.className='link';\n");
  echo("        createSpan.onclick= function(){ EdicaoTitulo(id, 'titulo_'+id, 'ok'); };\n");
  echo("        createSpan.setAttribute('id', 'OkEdita');\n");
  echo("        createSpan.innerHTML='OK';\n");
  echo("        document.getElementById('titulo_'+id).appendChild(createSpan);\n\n");

  echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('titulo_'+id).appendChild(espaco);\n\n");

  echo("        createSpan = document.createElement('span');\n");
  echo("        createSpan.className='link';\n");
  echo("        createSpan.onclick=function(){ EdicaoTitulo(id, 'titulo_'+id, 'canc'); };\n");
  echo("        createSpan.setAttribute('id', 'CancelaEdita');\n");
  echo("        createSpan.innerHTML='Cancelar';\n");
  echo("        document.getElementById('titulo_'+id).appendChild(createSpan);\n\n");

  echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
  echo("        espaco = document.createElement('span');\n");
  echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
  echo("        document.getElementById('titulo_'+id).appendChild(espaco);\n\n");

  echo("        startList();\n");
  echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
  echo("        document.getElementById('titulo_'+id+'_text').select();\n");
  echo("      }\n\n");
  
  echo("      function ApagarCategoria(id)\n");
  // 514 - Tem certeza que deseja apagar esta categoria ?
  echo("      {\n");
  echo("        if(confirm('".RetornaFraseDaLista($lista_frases_adm,514)."')){\n");
  echo("          xajax_ApagarDinamic(id);\n");
  echo("        }");
  echo("      }\n\n");

  echo("      function TestaCriar() {\n");
  echo("        nova = document.criar.nova_categ.value;\n");
  echo("        if (nova == '') \n");
  echo("        {\n");
  // 513 - O campo Categoria não pode ser deixado em branco
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,513)."');\n");
  echo("          document.criar.nova_categ.focus();\n");
  echo("          return false;\n");
  echo("        } else {\n");
  // 528 - Categoria criada com sucesso! 
  // 527 - Já existe uma categoria com este nome.
  echo("          xajax_InserirCategoriaDinamic(document.criar.nova_categ.value,'".RetornaFraseDaLista($lista_frases_adm, 528)."','".RetornaFraseDaLista($lista_frases_adm, 527)."');\n");
  echo("          document.criar.nova_categ.value = '';\n");
  echo("        }\n");
  echo("      }\n");
  
  
  echo("function escondetr(valor){\n");
  echo(" if(valor == 1) {\n");
  echo("    document.getElementById('semcategoria').style.display='none';\n");
  echo(" }\n");
  echo(" if(valor == 0){\n");
  echo("    document.getElementById('semcategoria').style.display=\"\";\n");
  echo(" }\n");
  echo("}\n");
  
  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("        document.criar.nova_categ.focus();");
  echo("      }\n");

  echo("    </script>\n");
  /* Fim do JavaScript */

  $objAjax->printJavascript();

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 125 - Editar Categorias */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,125)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\" id=\"tabelaExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='index.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head01 alLeft\">\n");
  /* 124 - Nova categoria: */
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases,124)."\n");
  /* As funções que usam o xajax bugam com o OnSubmit, portanto desabilitamos o Submit.
    O Usuário terá que apertar o botão. */
  echo("                      <form OnSubmit='return false;' name=\"criar\" action='' method=\"get\" >\n");
  echo("                        <input type=\"hidden\" name=\"acao\" value=\"novo\"/>\n");
  echo("                        <input class=\"input\" type=\"text\" name=\"nova_categ\" size=\"33\" style=\"maxlenght: 100\"/>\n");
  /* 8 - Criar */
  echo("                        <input id=\"cria_categ\" class=\"input\" onClick='TestaCriar();' type=\"button\"  value=\"".RetornaFraseDaLista($lista_frases_geral,8)."\"/>\n");
  
  echo("                      </form>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");

  $categ=RetornaCategorias();

    echo("                  <tr id=\"tr_master\" class=\"head\">\n"); 
    /* 130 - Categorias existentes: */
    echo("                    <td width=\"70%\">".RetornaFraseDaLista($lista_frases,130)."</td>\n");
    /* 70 - Opções */
    echo("                    <td width=\"10%\">".RetornaFraseDaLista($lista_frases_geral,70)."</td>\n");
    echo("                  </tr>\n");
    if(!$categ){
    	echo("                  <tr id=\"semcategoria\">\n"); 
		echo("                      <td colspan=2>".RetornaFraseDaLista($lista_frases,132)."</td>");
		echo("					</tr>\n");
    }
	foreach($categ as $cod_pasta => $pasta)
    {
      echo("                  <tr id=\"tr_".$cod_pasta."\">\n");
      echo("                    <td>\n");
      /* Categorias */
      echo("                      <span id=\"titulo_".$cod_pasta."\">".$pasta."</span>");
      echo("                    </td>\n");
      /* 19 - Renomear */
      echo("                    <td>\n");
      echo("                      <ul class=\"botao2\">\n");
      echo("                        <li><span onclick='Renomear(\"".$cod_pasta."\");'>".RetornaFraseDaLista($lista_frases_geral, 19)."</span></li>\n");
      /* 1 - Apagar */
      echo("                        <li><span style=\"href: #\" title=\"Apagar\" onclick='ApagarCategoria(\"".$cod_pasta."\");'>".RetornaFraseDaLista($lista_frases_geral,1)."</span></li>\n");
      echo("                      </ul>\n");
      echo("                    </td>\n");
      echo("                  </tr>\n");
    }



  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>