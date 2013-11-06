<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : layer_material.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist?ncia
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

    Nied - N?cleo de Inform?tica Aplicada ? Educa??o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit?ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : layer_material.php
  ========================================================== */

/* Layers */
  /* Novo Topico */
  echo("    <div id=\"novotop\" class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_novo_top);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup ulPopup\">\n");
  echo("        <form name=\"form_novo_top\" method=\"post\" action=\"acoes.php\" onsubmit=\"return (VerificaNovoItemTopico(document.form_novo_top.novo_nome));\">\n");
  /* 21 - Digite o nome da pasta a ser criada aqui: */
  echo("          ".RetornaFraseDaLista($lista_frases,21)."<br />\n");
  echo("          <input class=\"input\" type=\"text\" name=\"novo_nome\" id=\"nome_novo_topico\" value=\"\" maxlength=\"150\" /><br />\n");
  /* 18 - Ok (gen) */
  echo("          <input class=\"input\" type=\"submit\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("          &nbsp; &nbsp; <input class=\"input\" type=\"button\" onclick=\"EscondeLayer(cod_novo_top);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  /* Mudar Compartilhamento */
  echo("    <div class=\"popup\" id=\"comp\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <form method=\"post\" name=\"form_comp\" id=\"form_comp\" action=''>\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_item\" id=\"cod_item\" value=\"\" />\n");
  echo("          <input type=\"hidden\" name=\"acao\" value=\"mudarcomp\" />\n");
  echo("          <input type=\"hidden\" name=\"tipo_comp\" id=\"tipo_comp\" value=\"\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_pagina\" id=\"cod_pagina\" value=\"\" />\n");
  /* 131 - Compartilhamento alterado com sucesso*/
  echo("          <input type=\"hidden\" name=\"texto\" id=\"texto\" value=\"".RetornaFraseDaLista($lista_frases,131)."\" />\n");
  echo("          <ul class=\"ulPopup\">\n");
  echo("            <li onclick=\"document.getElementById('tipo_comp').value='F'; xajax_MudarCompartilhamento(xajax.getFormValues('form_comp'),'".$tabela."','".RetornaFraseDaLista($lista_frases,17)."'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
  /* 17 - Compartilhado com formadores */
  echo("              <span>".RetornaFraseDaLista($lista_frases,17)."</span>\n");
  echo("            </li>\n");
  echo("            <li onclick=\"document.getElementById('tipo_comp').value='T'; xajax_MudarCompartilhamento(xajax.getFormValues('form_comp'),'".$tabela."','".RetornaFraseDaLista($lista_frases,16)."');EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_T\" class=\"check\"></span>\n");
  /* 16 - Totalmente compartilhado */
  echo("              <span>".RetornaFraseDaLista($lista_frases,16)."</span>\n");
  echo("            </li>\n");
  echo("          </ul>\n");
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");
  
  /* Mover Selecionados*/
  echo("    <div id=\"mover_selec\" class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_mover_selec);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup ulPopup\">\n");
  /* 20 - Escolha a pasta destino: */
  echo("        ".RetornaFraseDaLista($lista_frases,20)."<br />\n");
  $lista_topicos=RetornaListaDeTopicos($sock, $tabela);
  if (count($lista_topicos)>0)
    foreach ($lista_topicos as $cod => $linha_topico)
    {
      if ($cod_topico_raiz==$linha_topico['cod_topico'])
        echo("        ".$linha_topico['espacos']."<img src=\"../imgs/pasta.gif\" alt=\"Pasta\" border=\"0\" />".$linha_topico['topico']."<br />\n");
      else
      // a funcao MoverItem corresponde a implementacao dependente da pagina em que essa acao estah sendo solicitada. Caso for em material.php, a funcao utilizada serah a que se encontra em ../js-css/material_formador.js. Se for em ver.php, a funcao serah do arquivo ../js-css/ver_formador.js. A diferenca entre as funcoes eh que, em material.php, soh eh tratada a movimentacao de pastas e itens selecionados. Jah em ver.php, eh somente o item que estah sendo visualizado.
        echo("        ".$linha_topico['espacos']."<img src=\"../imgs/pasta.gif\" alt=\"Pasta\" border=\"0\" /><span class=\"link\" onclick=\"EscondeLayer(cod_mover_selec);MoverSelecionados('".$linha_topico['cod_topico']."');\">".$linha_topico['topico']."</span><br />\n");
    }

  echo("      </div>\n");
  echo("    </div>\n");

  /* Mover */
  echo("    <div class=\"popup\" id=\"mover\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_mover);xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", js_cod_item, ".$cod_usuario.", 0);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n");    
  /* 17 - Escolha a pasta destino: */
  echo("          ".RetornaFraseDaLista($lista_frases,17)."<br />\n");
  /*
  $lista_topicos=RetornaListaDeTopicos($sock,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);
  */
  if (count($lista_topicos)>0){
    foreach ($lista_topicos as $cod => $linha_topico)
    {
      if ($cod_topico_raiz==$linha_topico['cod_topico'])
        echo("          ".$linha_topico['espacos']."<img alt=\"".$linha_topico['topico']."\" src=\"../imgs/pasta.gif\" border=\"0\" />".$linha_topico['topico']."<br />\n");
      else
        echo("          ".$linha_topico['espacos']."<a href=\"#\" class=\"link\" onclick=\"js_tipo_item='item'; EscondeLayer(cod_mover);MoverItem(this,".$linha_topico['cod_topico'].");\"><img alt=\"".$linha_topico['topico']."\" src=\"../imgs/pasta.gif\" border=\"0\" />".$linha_topico['topico']."</a><br />\n");
    }
  }
  echo("        </div>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  /* Mover arquivo */

  $lista_arq=RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);
  if ((count($lista_arq))>0){
    $i=0;
    foreach($lista_arq as $cod=>$linha2){
      if (is_dir($linha2['Caminho'])){
        $lista_diretorios[$i]['Diretorio'] = $linha2['Diretorio'];
        $lista_diretorios[$i]['Caminho'] = $linha2['Caminho'];
        $i++;
      }
    }
  }

  echo("    <div class=\"popup\" id=\"mover_arquivo\">\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(cod_mover_arquivo);xajax_AcabaEdicaoDinamic('".$tabela."', ".$cod_curso.", js_cod_item, ".$cod_usuario.", 0);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("     <div class=\"int_popup\">\n");

  if (count($lista_diretorios)>0){

  /* 17 - Escolha a pasta destino: */
  echo("          ".RetornaFraseDaLista($lista_frases,17)."<br />\n");

  /* 37 - Pasta Raiz */
  echo("          <span class=\"link\" onclick=\"Mover('".$dir_item_temp['diretorio']."');\"><img src=\"../imgs/pasta.gif\" border=\"0\" alt=\"Pasta\"/>".RetornaFraseDaLista($lista_frases_geral,37)."</span><br />\n");

    foreach ($lista_diretorios as $cod => $linha_dir)
    {

        $caminho=(split('/', $linha_dir['Diretorio']));
        $cont = count($caminho);
        $cont2=$cont;
        echo("          ");
        while ($cont2>0){
            echo("&nbsp;&nbsp;&nbsp;&nbsp;");
            $cont2--;
        }

        echo("         <span class=\"link\" onclick=\"Mover('".$linha_dir['Caminho']."');\"><img src=\"../imgs/pasta.gif\" alt=\"Pasta\" border=\"0\" />".$caminho[$cont-1]."</span><br />\n");
    }
  }
  echo("      </div>\n");
  echo("    </div>\n");




  
  /* Estrutura de Topicos */
  echo("    <div id=\"topicos\" class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(lay_topicos);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <div class=\"ulPopup\">\n");
  $lista_topicos=RetornaListaDeTopicos($sock, $tabela);
  if(!empty($lista_topicos)>0)
    foreach ($lista_topicos as $cod => $linha_topico)
    {
      if ($cod_topico_raiz==$linha_topico['cod_topico'])
        echo("          ".$linha_topico['espacos']."<img src=\"../imgs/pasta.gif\" alt=\"Pasta\" border=\"0\" />".$linha_topico['topico']."&nbsp; <br />\n");
      else
        echo("          ".$linha_topico['espacos']."<img src=\"../imgs/pasta.gif\" alt=\"Pasta\" border=\"0\" /><a class=\"link\" href=\"material.php?cod_curso=".$cod_curso."&amp;cod_ferramenta=".$cod_ferramenta."&amp;cod_topico_raiz=".$linha_topico['cod_topico']."\" onclick=\"EscondeLayer(lay_topicos);\">".$linha_topico['topico']."</a>&nbsp; <br />\n");

    }
  echo("        </div>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  /* Novo Item */
  echo("    <div id=\"novoitem\" class=\"popup\">\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(cod_novoitem);return(false);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <form name=\"form_novo_item\" method=\"post\" action=\"acoes.php\" onsubmit='return (VerificaNovoItemTitulo(document.form_novo_item.novo_nome));'>\n");
  echo("          <div class=\"ulPopup\">\n");    
  /* 22 - Digite o novo título do item: */
  echo("            ".RetornaFraseDaLista($lista_frases,22)."<br />\n");
  echo("            <input class=\"input\" type=\"text\" name=\"novo_nome\" id=\"nome_novo_item\" value=\"\" maxlength=\"150\" /><br />\n");
  echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("            <input type=\"hidden\" name=\"acao\" value=\"criarItem\" />\n");
  echo("            <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
  echo("            <input type=\"hidden\" name=\"cod_ferramenta\" value=\"".$cod_ferramenta."\" />\n");
  /* 18 - Ok (gen) */
  echo("            <input class=\"input\" type=\"submit\" id=\"ok_novoitem\" value=\"".RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input class=\"input\" type=\"button\" onclick=\"EscondeLayer(cod_novoitem);\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
   echo("         </div>\n");    
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

?>