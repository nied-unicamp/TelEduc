<?php
  /* Layers */

  /* Nova Pasta */
  echo("    <div id=\"novapasta\" class=\"popup\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_novapasta);return(false);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <form name=\"form_novo_top\" method=\"post\" action=\"\" onSubmit='return (VerificaNovoItemTopico(document.form_novo_top.novo_nome));'>\n");
  echo("          <div class=\"ulPopup\">\n");    
  /* 24 - Digite o nome da pasta a ser criada aqui: */
  echo("            "._("TYPE_FOLDER_NAME_-1")."<BR />\n");
  echo("            <input id=\"titulopasta\" class=\"input\" type=text name=\"novo_nome\" value=\"\" maxlength=\"150\" /><br />\n");
  /* 18 - Ok (gen) */
  echo("            <input type=\"submit\" id=\"ok_novapasta\" class=\"input\" value=\""._("OK_-1")."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(cod_novapasta);\" value=\""._("CANCEL_-1")."\" />\n");
  echo("          </div>\n");    
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  /* Novo Item */
  echo("    <div id=\"novoitem\" class=\"popup\">\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(cod_novoitem);return(false);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=int_popup>\n");
  echo("        <form name=\"form_novo_item\" method=\"post\" action=\"".$ctrl_portfolio."acoes.php\" onSubmit='return(VerificaNovoItemTitulo(document.form_novo_item.novo_nome));'>\n");
  echo("          <div class=\"ulPopup\">\n");    
  /* 180 - Digite o nome do item a ser criado aqui: */
  echo("            "._("TYPE_ITEM_NAME_-1")."<BR />\n");
  echo("            <input class=\"input\" type=text id=\"titulo\" name=\"novo_nome\" value=\"\" maxlength=150 /><br />\n");
  echo("            <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("            <input type=\"hidden\" name=\"acao\" value=\"criarItem\" />\n");
  echo("            <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
  echo("            <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"".$cod_grupo_portfolio."\" />\n");
  echo("            <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"".$cod_usuario_portfolio."\" />\n");
  /* 18 - Ok (gen) */
  echo("            <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\""._("OK_-1")."\" />\n");
  /* 2 - Cancelar (gen) */
  echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(cod_novoitem);\" value=\""._("CANCEL_-1")."\" />\n");
   echo("         </div>\n");    
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");


  $lista_topicos=Portfolio::RetornaListaDeTopicos($sock,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);

  echo("<script type=\"text/javascript\">\n");
  echo("	function acabaEdicao(){\n");
  echo("			$.post(\"".$model_portfolio."acaba_edicao.php\",{cod_curso: ".$cod_curso.", cod_item: js_cod_item, cod_usuario: ".$cod_usuario.", acao: 0}, \n");
  echo("				function(data){\n");
  echo("					var code = $.parseJSON(data);\n");
  //echo("					alert(code);\n");
  echo("			});\n");
  echo("	}\n");
  echo("</script>");
  echo("    <div class=\"popup\" id=\"mover\">\n");
 echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_mover); acabaEdicao();return(false);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <ul class=\"ulPopup\">\n");    
  /* 17 - Escolha a pasta destino: */
  echo("          <li>"._("SELECT_DESTINATION_FOLDER_-1")."</li>\n");

  if (count($lista_topicos)>0){
    foreach ($lista_topicos as $cod => $linha_topico)
    {
      if ($cod_topico_raiz==$linha_topico['cod_topico'])
        echo("          <li>".$linha_topico['espacos']."<img alt=\"".$linha_topico['topico']."\" src=\"".$diretorio_imgs."pasta.gif\" border=0 />".$linha_topico['topico']."</li>\n");
      else
        echo("          <li>".$linha_topico['espacos']."<span class=\"link\" onClick=\"js_tipo_item='item'; EscondeLayer(cod_mover);MoverItem(this,".$linha_topico['cod_topico'].");\"><img alt=\"".$linha_topico['topico']."\" src=\"".$diretorio_imgs."pasta.gif\" border=0 />".$linha_topico['topico']."</span></li>\n");
    }
  }
  echo("        </ul>\n");
  echo("      </div>\n");
  echo("    </div>\n");

  echo("    <div class=\"popup\" id=\"mover_selec\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_mover_selec);return(false);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <ul class=\"ulPopup\">\n");
  /* 17 - Escolha a pasta destino: */
  echo("          <li>"._("SELECT_DESTINATION_FOLDER_-1")."</li>\n");
  if (count($lista_topicos)>0){
    foreach ($lista_topicos as $cod => $linha_topico)
    {
      if ($cod_topico_raiz==$linha_topico['cod_topico'])
        echo("          <li>".$linha_topico['espacos']."<img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=0 />".$linha_topico['topico']."</li>\n");
      else
        echo("          <li>".$linha_topico['espacos']."<span class=\"link\" onclick=\" EscondeLayer(cod_mover_selec);MoverSelecionados(".$linha_topico['cod_topico'].");\"><img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=0 />".$linha_topico['topico']."</span></li>\n");
    }
  }
  echo("        </ul>\n");
  echo("      </div>\n");
  echo("    </div>\n");


  /* Mover arquivo */

  $lista_arq=Portfolio::RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);
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
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(cod_mover_arquivo); acabaEdicao();return(false);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("     <div class=\"int_popup\">\n");
  echo("        <ul class=\"ulPopup\">\n");

  if (count($lista_diretorios)>0){

  /* 17 - Escolha a pasta destino: */
  echo("          <li>"._("SELECT_DESTINATION_FOLDER_-1")."</li>\n");

  /* 37 - Pasta Raiz */
  echo("          <li><span class=\"link\" onClick=\"Mover('".$dir_item_temp['diretorio']."');\"><img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=0 />"._("ROOT_FOLDER_-1")."</span></li>\n");

    foreach ($lista_diretorios as $cod => $linha_dir)
    {

        $caminho=(split('/', $linha_dir['Diretorio']));
        $cont = count($caminho);
        $cont2=$cont;
        $spaces = "";
        while ($cont2>0){
            $spaces .= "&nbsp;&nbsp;&nbsp;&nbsp;";
            $cont2--;
        }

        echo("         <li>".$spaces."<span class=\"link\" onClick=\"Mover('".$linha_dir['Caminho']."');\"><img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=0 />".$caminho[$cont-1]."</span></li>\n");
    }
  }
  echo("        <ul>\n");
  echo("      </div>\n");
  echo("    </div>\n");



/* Estrutura de topicos */

  $lista_topicos=Portfolio::RetornaListaDeTopicos($sock,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);

  echo("    <div class=popup id=\"topicos\">\n");
  echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(cod_topicos);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("     <div class=\"int_popup ulPopup\">\n");
  echo("       <div class=\"ulPopup\">\n");

  if (count($lista_topicos)>0){

  /* 17 - Escolha a pasta destino: */
  echo("          "._("SELECT_DESTINATION_FOLDER_-1")."<br />\n");

    foreach ($lista_topicos as $cod => $linha_top)
    {

        echo("          ");
        echo($linha_top['espacos']."<img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=\"0\" /> <a href=\"portfolio.php?&amp;cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha_top['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha_top['topico']."</a><br />\n");
    }
  }else{
        echo("         <span>Diret&oacute;rio vazio</span>\n");

  }
  echo("        </div>\n");
  echo("      </div>\n");
  echo("    </div>\n");


  /* Mudar Compartilhamento */
  echo("    <div class=popup id=\"comp\">\n");
  echo("      <div class=\"posX\"><span onclick=\"EscondeLayer(cod_comp); acabaEdicao();return(false);\"><img src=\"".$diretorio_imgs."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
  echo("      <div class=\"int_popup\">\n");
  echo("        <script type=\"text/javascript\">\n");
  echo("		function mudaCompartAtualiza (comp){\n");
  echo("		var cod_curso = document.form_comp.cod_curso.value;\n");
  echo("		var cod_item = document.form_comp.cod_item.value;\n");
  echo("		var cod_usuario = document.form_comp.cod_usuario.value;\n");
  echo("		var tipo_comp = document.form_comp.tipo_comp.value;\n");
  echo("			$.ajax({\n");
  echo("				url: '".$model_portfolio."mudar_compart_atualiza.php',\n");
  echo("				data: {cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario, novo_comp: comp, tipo_comp: tipo_comp}, \n");
  echo("				type: 'POST', \n");
  echo("				success: function(data) {\n");
  echo("					$('#tr_'+cod_item).addClass('novoitem');\n");
  echo("					$('#comp_'+cod_item).html(comp);\n");
  echo("					$('#comp_'+cod_item).click(function(event){\n");
  echo("						js_cod_item=cod_item;\n"); 
  echo("						AtualizaComp(tipo_comp);\n"); 
  echo("						MostraLayer(cod_comp,140, event);\n");
  echo("					});");
  echo("					$('#data_'+cod_item).html('".Data::UnixTime2Data(time())."');\n");
  echo("					mostraFeedback('Compartilhamento atualizado com sucesso', true);\n");
  echo("				}\n");
  echo("			});\n");
  echo("		}");
  echo("        </script>\n");
  echo("        <form name=\"form_comp\" action=\"\" id=\"form_comp\" method=\"post\">\n");
  echo("          <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_item\" id=\"cod_item\"/>\n");
  echo("          <input type=\"hidden\" name=\"acao\" value=\"mudarcomp\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"".$cod_usuario_portfolio."\" />\n");
  echo("          <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"".$cod_grupo_portfolio."\" />\n");
  echo("          <input type=\"hidden\" name=\"tipo_comp\" id=\"tipo_comp\" value=\"\" />\n");
  /* 195 - Compartilhamento alterado com sucesso */
  echo("          <input type=\"hidden\" name=\"texto\" id=\"texto\" value=\""._("ACCESS_MODE_CHANGED_SUCCESS_-1")."\" />\n");
  echo("          <ul class=\"ulPopup\">\n");
  echo("            <li onClick=\"document.getElementById('tipo_comp').value='F'; mudaCompartAtualiza ('"._("INSTRUCTOR_ACCESS_MODE_-1")."'); EscondeLayers();\">\n");
  echo("            <span id=\"tipo_comp_F\" class=\"check\"></span>\n");
  /* 13 - Compartilhado com formadores */
  echo("            <span>"._("INSTRUCTOR_ACCESS_MODE_-1")."</span>\n");
  echo("            </li>\n");
  echo("            <li onClick=\"document.getElementById('tipo_comp').value='T'; mudaCompartAtualiza ('"._("UNRESTRICTED_ACCESS_MODE_-1")."'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_T\" class=\"check\"></span>\n");
  /* 12 - Totalmente compartilhado */
  echo("              <span>"._("UNRESTRICTED_ACCESS_MODE_-1")."</span>\n");
  echo("            </li>\n");

  if (!$portfolio_grupo)
    /* 15 - Não compartilhado */
    $frase_comp = _("NOT_ACCESSIBLE_-1");
  else
    /* 14 - Compartilhado com o Grupo */
    $frase_comp = _("GROUP_ACCESS_MODE_-1");

  echo("            <li onClick=\"document.getElementById('tipo_comp').value='P'; mudaCompartAtualiza ('".$frase_comp."'); EscondeLayers();\">\n");
  echo("              <span id=\"tipo_comp_P\" class=\"check\"></span>\n");
  echo("              <span>".$frase_comp."</span>\n");
  echo("            </li>\n");
  echo("          </ul>\n");    
  echo("        </form>\n");
  echo("      </div>\n");
  echo("    </div>\n");

?>