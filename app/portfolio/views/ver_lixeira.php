<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$ctrl_portfolio = '../../'.$ferramenta_portfolio.'/controllers/';
$view_portfolio = '../../'.$ferramenta_portfolio.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_ferramenta =15;
$cod_ferramenta_ajuda = 15;
$cod_pagina_ajuda = 6;

$cod_curso = $_GET['cod_curso'];
$cod_usuario_portfolio = $_GET['cod_usuario_portfolio'];
$cod_usuario = $_GET['cod_usuario_portfolio'];
$cod_item = $_GET['cod_item'];
$cod_grupo_portfolio = $_GET['cod_grupo_portfolio'];
$cod_topico_raiz = $_GET['cod_topico_raiz'];

require_once $view_administracao.'topo_tela.php';

$sock1 = AcessoSQL::Conectar("");

$eformador=Usuarios::EFormador($sock1,$cod_curso,$cod_usuario);

AcessoSQL::Desconectar($sock1);

$sock = AcessoSQL::Conectar($cod_curso);

$dir_item_temp=Portfolio::CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

$status_portfolio = Portfolio::RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

$dono_portfolio    = $status_portfolio ['dono_portfolio'];
$portfolio_apagado = $status_portfolio ['portfolio_apagado'];
$portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

if (!$dono_portfolio)
{
	/* 1 - Portfólio */
	$cabecalho = "<br><br><h5>"._("PORTFOLIO_15");
	/* 50- Área restrita ao(s) dono(s) do portfólio */
	$cabecalho .= " - "._("RESTRICTED_AREA_PORTFOLIO_OWNERS_15")."</h5>";
	echo($cabecalho);
	AcessoSQL::Desconectar($sock);
	exit();
}

echo("    <script type=\"text/javascript\">\n");

echo("      function OpenWindowPerfil(id)\n");
echo("      {\n");
echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
echo("        return(false);\n");
echo("      }\n");

echo("    function Excluir(){\n");
/* 120 - Tem certeza de que deseja excluir este item ? */
/* 100 - (o item serÃ¡ excluÃ­do definitivamente) */
echo("      if (confirm('"._("SURE_TO_DELETE_ITEM_-1")."\\n"._("ITEM_DELETED_PERMANENTLY_-1")."')){\n");
echo("			$.post(\"".$model_portfolio."excluir_item.php\",{cod_curso:".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_itens: ".$cod_item."}, \n");
echo("				function(data){\n");
echo("					Recarregar('excluirItens', 'true');\n");
echo("			});\n");
echo("      }\n");
echo("    }\n\n");

echo("    function Recuperar(){\n");
/* 101 - VocÃª tem certeza de que deseja recuperar este item? */
/* 102 - (o item serÃ¡ movida para a pasta Raiz e estarÃ¡ como nÃ£o compartilhado) */
echo("      if (confirm('"._("SURE_TO_RETRIEVE_ITEM_-1")."\\n"._("ITEM_MOVED_ROOT_FOLDER_-1")."')){\n");
echo("			$.post(\"".$model_portfolio."recuperar_item.php\",{cod_curso:".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_grupo_portfolio: '".$cod_grupo_portfolio."', cod_itens: ".$cod_item."}, \n");
echo("				function(data){\n");
echo("					Recarregar('recuperarItens', 'true');\n");
echo("			});\n");
echo("      }\n");
echo("    }\n\n");

echo("    function Recarregar(){\n");
echo("      window.location='portfolio_lixeira.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_usuario_portfolio=".$cod_usuario_portfolio."';\n");
echo("    }\n\n");

echo("      function WindowOpenVer(id)\n");
echo("      {\n");
echo("         window.open('".$dir_item_temp['link']."'+id+'?".time()."','Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
echo("      }\n\n");

echo("      function Iniciar(){\n");
echo("        startList();\n");
echo("      }\n");

echo("    </script>\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/* Página Principal */

if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
{
	// 3 - Portfolios de grupos
	$cod_frase = _("GROUP_PORTFOLIOS_15");
	$cod_pagina=15;
}
else
{
	// 2 - Portfolios individual
	$cod_frase = _("INDIVIDUAL_PORTFOLIOS_15");
	$cod_pagina=9;
}

echo("          <h4>"._("PORTFOLIO_15")." - ".$cod_frase."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("<div id=\"mudarFonte\">\n");
echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");



if ($portfolio_grupo)
{
	$nome=Portfolio::NomeGrupo($sock,$cod_grupo_portfolio);

	//Figura de Grupo
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icGrupo.gif\" border=\"0\" />";


	/* 84 - Grupo Excluído */
	if ($grupo_apagado && $eformador) $complemento=" <span>("._("DELETED_GROUP_15").")</span>\n";

	echo("          <a class=\"text\" href=\"#\" onclick=\"return(AbreJanelaComponentes(".$cod_grupo_portfolio."));\">".$fig_portfolio." ".$nome."</a>".$complemento." - ");
	echo("          <a href=\"#\" onmousedown=\"js_cod_item='".$cod_item."'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"".$diretorio_imgs."estruturag.gif\" border=\"0\" /></a>");
}
else
{
	$nome=Usuarios::NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

	// Selecionando qual a figura a ser exibida ao lado do nome
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icPerfil.gif\" border=\"0\" />";


	echo("          <a class=\"text\" href=\"#\" onclick=\"return(OpenWindowPerfil(".$cod_usuario_portfolio."));\" >".$fig_portfolio." ".$nome."</a>".$complemento." - ");
	echo("<a href=\"#\" onmousedown=\"js_cod_item='".$cod_item."'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\" /></a>");
}

// 7 - Lixeira
echo("<a href=\"portfolio_lixeira.php?&amp;cod_curso=".$cod_curso."&amp;cod_topico=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\" >"._("TRASH_-1")."</a>\n");

echo("          <!-- Tabelao -->\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <!-- Botoes de Acao -->\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");

//174 - Meus portfolios
echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=myp\">"._("MY_PORTFOLIOS_15")."</a></li>\n");
// 74 - Portfolios Individuais
echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=ind\">"._("INDIVIDUAL_PORTFOLIOS_15")."</a></li>\n");
// 75 - Portfolios de Grupos
if ($ferramenta_grupos_s) {
	echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=grp\">"._("GROUP_PORTFOLIOS_15")."</a></li>\n");
	// 177 - Portfolios encerrados
	echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=enc\">"._("ENDED_PORTFOLIOS_15")."</a></li>\n");
}

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <ul class=\"btAuxTabs03\">\n");

$cod_topico_raiz_usuario=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,"");

unset($array_params);
$array_params['cod_topico_raiz']       = $cod_topico_raiz;
$array_params['cod_item']              = $cod_item;
$array_params['cod_usuario_portfolio'] = $cod_usuario_portfolio;
$array_params['cod_grupo_portfolio']   = $cod_grupo_portfolio;

/* 70 - Ver Outros Itens */
echo("                  <li><a href=\"portfolio_lixeira.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">"._("VIEW_OTHER_ITEMS_-1")."</a></li>\n");

$figura = "arquivo_";
$figura.= ( $portfolio_grupo ? "g_" : "i_" );
if ($portfolio_apagado)
{
	$figura .= "x.gif";
}
else
{
	if ($dono_portfolio)
	{
		$figura .= "p.gif";
	}
	else
	{
		$figura .= "n.gif";
	}
}

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/* 41 - Tí­tulo */
echo("                    <td>"._("TITLE_-1")."</td>\n");

// 70 (ger) - Opções
echo("                    <td width=\"14%\" align=\"center\">"._("OPTIONS_0")."</td>\n");


/* 119 - Compartilhamento */
echo("                    <td width=\"10%\" align=\"center\">"._("ACCESS_MODE_-1")."</td>\n");

echo("                  </tr>\n");

$linha_item=Portfolio::RetornaDadosDoItem($sock, $cod_item);

$titulo=$linha_item['titulo'];

$texto="<span id=\"text_".$linha_item['cod_item']."\">".ConversorTexto::AjustaParagrafo($linha_item['texto'])."</span>";

$lista = NULL;

$titulo="<span id=\"tit_".$linha_item['cod_item']."\">".$linha_item['titulo']."</span>";

$editar="<span onclick=\"AlteraTexto(".$linha_item['cod_item'].");\">"._("EDIT_-1")."</span>";



echo("                  <tr id='tr_".$linha_item['cod_item']."'>\n");
echo("                    <td class=\"itens\"><img alt=\"\" src=\"".$diretorio_imgs."".$figura."\" border=\"0\" /> ".$titulo."</td>\n");

echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
echo("                      <ul>\n");
/* 48 - Recuperar (gen) */
echo("                        <li><span onclick=\"Recuperar();\">"._("RETRIEVE_-1")."</span></li>\n");
/* 12 - Excluir (gen) */
echo("                        <li><span onclick=\"Excluir();\">"._("EXCLUDE_-1")."</span></li>\n");
echo("                      </ul>\n");
echo("                    </td>\n");


if ($linha_item['tipo_compartilhamento']=="T"){
	$compartilhamento=_("UNRESTRICTED_ACCESS_MODE_-1");
}
/* 13 - Compartilhado com Formadores */
else if ($linha_item['tipo_compartilhamento']=="F"){
	$compartilhamento=_("INSTRUCTOR_ACCESS_MODE_-1");
}
/* 14 - Compartilhado com o Grupo */
else if (($portfolio_grupo)&&($linha_item['tipo_compartilhamento']=="P")){
	$compartilhamento=_("GROUP_ACCESS_MODE_-1");
}
/* 15 - Não compartilhado */
else if (!$portfolio_grupo){
	$compartilhamento=_("NOT_ACCESSIBLE_-1");
}

echo("                    <td align=\"center\">".$compartilhamento."</td>\n");

echo("                  </tr>");

// "<P>&nbsp;</P>" = texto em branco
// "<br>" = texto em branco
if (($linha_item['texto']!="")&&($linha_item['texto']!="<P>&nbsp;</P>")&&($linha_item['texto']!="<br>"))
{
	echo("                  <tr class=\"head\">\n");
	/* 42 - Texto  */
	echo("                    <td colspan=\"4\">"._("TEXT_-1")."</td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td class=\"itens\" colspan=\"4\">\n");
    echo("                      ".$texto."\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
 }

 $lista_arq=Portfolio::RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['diretorio']);
 $num_arq_vis = Portfolio::RetornaNumArquivosVisiveis($lista_arq);
 
 if (count($lista_arq)>0){
 	echo("                  <tr class=\"head\">\n");
 	/* 71 - Arquivos */
 	echo("                    <td colspan=\"4\">"._("FILES_-1")."</td>\n");
 	echo("                  </tr>\n");
 
 	$conta_arq=0;
 	echo("                  <tr>\n");
 	echo("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
 	// Procuramos na lista de arquivos se existe algum visivel
 	$ha_visiveis = false;
 
 	while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis)
 	{
 		if ($linha[Arquivo] != "")
 			$ha_visiveis = !($linha['Status']);
 	}
 
 	if (($ha_visiveis) || ($dono_portfolio))
 	{
 		$nivel_anterior=0;
 		$nivel=-1;
 
 		foreach($lista_arq as $cod => $linha)
 		{
 			if (!($linha['Arquivo']=="" && $linha['Diretorio']==""))
 			if ((!$linha['Status'])||(($linha['Status'])&&($dono_portfolio)))
 			{
 				$nivel_anterior=$nivel;
 				$espacos="";
 				$espacos2="";
 				$temp=explode("/",$linha['Diretorio']);
 				$nivel=count($temp)-1;
 				for ($c=0;$c<=$nivel;$c++){
 					$espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
 					$espacos2.="  ";
 				}
 
 				$caminho_arquivo = $dir_item_temp['link'].ConversorTexto::ConverteUrl2Html($linha['Diretorio']."/".$linha['Arquivo']);
 
 				if ($linha[Arquivo] != "")
 				{
 
 					if ($linha['Diretorio']!=""){
 						$espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
 						$espacos2.="  ";
 					}
 
 
 					if ($linha['Status']) $arqOculto="arqOculto='sim'";
 					else $arqOculto="arqOculto='nao'";

 					if (eregi(".zip$",$linha['Arquivo']))
 					{
 						// arquivo zip
 						$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqzip.gif\" border=\"0\" />";
 						$tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"zip\" nomeArq=\"".htmlentities($caminho_arquivo)."\" arqZip=\"".$linha['Arquivo']."\" ". $arqOculto.">";
 					}
 					else
 					{
 						// arquivo comum
 						$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqp.gif\" border=\"0\" />";
 						$tag_abre  = "<span class=\"link\" id=\"nomeArq_".$conta_arq."\" onclick=\"WindowOpenVer('".$caminho_arquivo."');\" tipoArq=\"comum\" nomeArq=\"".htmlentities($caminho_arquivo)."\" ".$arqOculto.">";
 					}
 					
 					$tag_fecha = "</span>";
 					
 					echo("                        ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
 					
 					if ($dono_portfolio){
 						echo("                          ".$espacos2."<input class=\"input\" type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
 					}
 					
 					echo("                          ".$espacos2.$espacos.$imagem.$tag_abre.$linha['Arquivo'].$tag_fecha." - (".round(($linha[Tamanho]/1024),2)."Kb)");
 					
 					echo("<span id=\"local_oculto_".$conta_arq."\">");
 					if ($linha['Status'])
 						// 118 - Oculto
 						echo("<span id=\"arq_oculto_".$conta_arq."\"> - <span style=\"color:red;\">"._("HIDDEN_-1")."</span></span>");
 					echo("</span>\n");
 					echo("                          ".$espacos2."<br>\n");
 					echo("                        ".$espacos2."</span>\n");
 					}
 					else{
 						if ($nivel_anterior>=$nivel){
 							$i=$nivel_anterior-$nivel;
 							$j=$i;
 							$espacos3="";
 							do{
 								$espacos3.="  ";
 								$j--;
 							}while($j>=0);
 							do{
 								echo("                      ".$espacos3."</span>\n");
 								$i--;
 							}while($i>=0);
 						}
 						// pasta
 						$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=\"0\" />";
 						echo("                      ".$espacos2."<span id=\"arq_".$conta_arq."\">\n");
 						echo("                        ".$espacos2."<span class=\"link\" id=\"nomeArq_".$conta_arq."\" tipoArq=\"pasta\" nomeArq=\"".htmlentities($caminho_arquivo)."\"></span>\n");
 						if ($dono_portfolio){
 							echo("                        ".$espacos2."<input class=\"input\" type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_".$conta_arq."\">\n");
 						}
 						echo("                        ".$espacos2.$espacos.$imagem.$temp[$nivel]."\n");
 						echo("                        ".$espacos2."<br>\n");
 					}
 					
 					}
 					$conta_arq++;
 					}
 					do{
 						$j=$nivel;
 						$espacos3="";
 						do{
 							$espacos3.="  ";
 							$j--;
 						}while($j>=0);
 						echo("                      ".$espacos3."</span>\n");
 						$nivel--;
 					}while($nivel>=0);
 					
 					echo("                      <script type=\"text/javascript\">js_conta_arq=".$conta_arq.";</script>\n");
 					echo("                    </td>\n");
 					echo("                  </tr>\n");
 	}
 					
 					echo("                    </td>\n");
 					echo("                  </tr>\n");
 	}
 	$lista_url=Portfolio::RetornaEnderecosMaterial($sock, $cod_item);
 	
 	if (is_array($lista_url)){
 	
 		echo("                  <tr class=\"head\">\n");
 		/* 44 - Endereços */
 		echo("                    <td colspan=\"4\">"._("INTERNET_ADDRESSES_-1")."</td>\n");
 		echo("                  </tr>\n");
 		echo("                  <tr>\n");
 		echo("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");
 	
 		if (count($lista_url)>0)
 		{
 			foreach ($lista_url as $cod => $linha)
 			{
 	
 				$linha['endereco'] = ConversorTexto::RetornaURLValida($linha['endereco']);
 	
 				echo("                      <span id='end_".$linha['cod_endereco']."'>\n");
 	
 				if ($linha['nome']!="")
 				{
 					echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConversorTexto::ConverteSpace2Mais($linha['endereco'])."');\">".$linha['nome']."</span>&nbsp;&nbsp;(".$linha['endereco'].")");
 				}
 				else
 				{
 					echo("                      <span class=\"link\" onclick=\"WindowOpenVerURL('".ConversorTexto::ConverteSpace2Mais($linha['endereco'])."');\">".$linha['endereco']."</span>");
 				}
 	
 				if($dono_portfolio){
 					/* (gen) 1 - Apagar */
 					echo(" - <span class=\"link\" onClick=\"ApagarEndereco('".$cod_curso."', '".$linha['cod_endereco']."');\">"._("DELETE_-1")."</span>\n");
 				}
 				echo("                        <br />\n");
 				echo("                      </span>\n");
 	
 			}
 		}
 	
 		echo("                    </td>\n");
 		echo("                  </tr>\n");
 	}
 	
 	echo("                </table>\n");
 	echo("              </td>\n");
 	echo("            </tr>\n");
 	echo("          </table>\n");
 	echo("        </td>\n");
 	echo("      </tr>\n");

 	require_once $view_administracao.'tela2.php';
 	
 	echo("  </body>\n");
 	echo("</html>\n");
 	
 	AcessoSQL::Desconectar($sock);
?>
 	 	
 					
