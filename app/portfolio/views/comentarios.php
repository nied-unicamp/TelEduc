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

$cod_ferramenta = 15;
$cod_ferramenta_ajuda = 15;
$cod_pagina_ajuda = 4;

$cod_curso = $_GET['cod_curso'];
$cod_usuario_portfolio = $_GET['cod_usuario_portfolio'];
$cod_usuario = ((isset($_GET['cod_usuario'])) ? $_GET['cod_usuario'] : $_GET['cod_usuario_portfolio']);
$cod_item = $_GET['cod_item'];
$cod_grupo_portfolio = $_GET['cod_grupo_portfolio'];
$cod_topico_raiz = $_GET['cod_topico_raiz'];

$sock1 = AcessoSQL::Conectar("");
$eformador=Usuarios::EFormador($sock1,$cod_curso,$cod_usuario);
$diretorio_arquivos=Portfolio::RetornaDiretorio($sock1,'Arquivos');
$diretorio_temp=Portfolio::RetornaDiretorio($sock1,'ArquivosWeb');
AcessoSQL::Desconectar($sock1);

require_once $view_administracao.'topo_tela.php';

$sock = AcessoSQL::Conectar($cod_curso);

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject();
//adicionar as acoes possiveis, 1o parametro È a aÁ„o, o segundo È o n˙mero da frase para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("comentar", _("COMMENT_INSERT_SUCCESS"), sprintf(_("ERROR_ATTACHING_FILE_-1"), ((int) ini_get('upload_max_filesize'))));

/* 1 - Portffolio */
echo("    <script type=\"text/javascript\">\n");

echo("      function OpenWindowPerfil(id)\n");
echo("      {\n");
echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
echo("        return(false);\n");
echo("      }\n");


echo("      function Iniciar()\n");
echo("      {\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n\n");

echo("      function WindowOpenVer(id)\n");
echo("      {\n");
echo("         window.open('".$dir_item_temp['link']."'+id+'?".time()."','Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
echo("      }\n\n");

echo("      var many_arqs=0;\n\n");
echo("      function removeInputFile(numero){\n");
echo("        elementoDiv = document.getElementById('text_coment');\n");
echo("        elementoDiv.removeChild(document.getElementById('br_'+numero));\n");
echo("        elementoDiv.removeChild(document.getElementById('remover_arquivo_'+numero));\n");
echo("        elementoDiv.removeChild(document.getElementById('space_'+numero));\n");
echo("        elementoDiv.removeChild(document.getElementById('input_file_'+numero));\n");
echo("      }\n\n");

echo("      function addInputFile(){\n");
echo("        var num = many_arqs;\n");
echo("        elementoDiv = document.getElementById('text_coment');\n");
echo("        while(elementoDiv.lastChild.tagName!=\"IMG\")\n");
echo("          elementoDiv.removeChild(elementoDiv.lastChild);\n");
echo("        elementoDiv.removeChild(elementoDiv.lastChild);\n");
echo("        inputFile=document.createElement('input');\n");
echo("        inputFile.setAttribute(\"type\", \"file\");\n");
echo("        inputFile.setAttribute(\"size\", \"40\");\n");
echo("        inputFile.setAttribute(\"name\", \"input_files[]\");\n");
echo("        inputFile.setAttribute(\"id\", \"input_file_\"+many_arqs);\n");
echo("        inputFile.setAttribute(\"style\", \"border:2px solid #9bc\");\n\n");
echo("        createSpace=document.createElement('span');\n");
echo("        createSpace.setAttribute(\"id\", \"space_\"+many_arqs);\n");
echo("        createSpace.innerHTML=\"&nbsp;&nbsp;&nbsp;\"\n");
echo("        createSpan = document.createElement('span');\n");
echo("        createSpan.onclick = function() { removeInputFile(num); };\n");
echo("        createSpan.setAttribute(\"id\", \"remover_arquivo_\"+many_arqs);\n");
echo("        createSpan.className=\"link\";\n");
echo("        createSpan.innerHTML=\""._("REMOVE_-1")."\";\n\n");
echo("        createBr = document.createElement('br');\n");
echo("        createBr.setAttribute(\"id\", \"br_\"+many_arqs);\n\n");
echo("        createImg = document.createElement('img');\n");
echo("        createImg.setAttribute(\"src\", \"".$diretorio_imgs."paperclip.gif\");\n");
echo("        createImg.setAttribute(\"border\", \"0\");\n");
echo("        createSpan2 = document.createElement('span');\n");
echo("        createSpan2.className=\"link\";\n");
echo("        createSpan2.onclick = function (){ addInputFile(); };\n");
echo("        createSpan2.setAttribute(\"id\", \"anexar_arquivo\");\n");
echo("        createSpan2.innerHTML=\""._("ATTACH_FILE_-1")."\";\n\n");
echo("        elementoDiv.appendChild(inputFile);\n");
echo("        elementoDiv.appendChild(createSpace);\n");
echo("        elementoDiv.appendChild(createSpan);\n");
echo("        elementoDiv.appendChild(createBr);\n");
echo("        elementoDiv.appendChild(createImg);\n\n");
echo("        elementoDiv.appendChild(createSpan2);\n\n");
echo("        many_arqs++;\n");
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
/* 106 - Seu coment·rio est· vazio. Para n„o envi·-lo, pressione o botao Cancelar. */
echo("          alert('"._("EMPTY_COMMENT_15")."');\n");
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

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");


/* Verifica√ß√£o se o item est√° em Edi√ß√£o */
/* Se estiver, voltar a tela anterior, e disparar a tela de Em Edi√ß√£o... */
$linha=Portfolio::RetornaUltimaPosicaoHistorico ($sock, $cod_item);
if ($linha['acao']=="E")
{
	if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario']){
		Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario);
	}else{
		/* Est√° em edi√ß√£o... */
		echo("          <script type=\"text/javascript\">\n");
		echo("            window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&amp;cod_topico_raiz=".$cod_topico_raiz."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
		echo("            document.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha_item['cod_item']."&origem=ver&cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."&amp;cod_topico_raiz=".$cod_topico_raiz."';\n");
		echo("          </script>\n");
		echo("        </td>\n");
		echo("      </tr>\n");
		echo("    </table>\n");
		echo("  </body>\n");
		echo("</html>\n");
		exit();
	}
}


$status_portfolio = Portfolio::RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

$dono_portfolio    = $status_portfolio ['dono_portfolio'];
$portfolio_apagado = $status_portfolio ['portfolio_apagado'];
$portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

$ferramenta_grupos_s = Portfolio::StatusFerramentaGrupos($sock);
$_SESSION['ferramenta_grupos_s'] = $ferramenta_grupos_s;

if ($acao=="mudarcomp" && $dono_portfolio)
{
	Portfolio::MudarCompartilhamento($cod_item, $tipo_comp); //REVER
}

if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
{
	// 3 - Portfolios de grupos
	$cod_frase = _("GROUP_PORTFOLIOS_15");
	$cod_pagina=11;
}
else
{
	// 2 - Portfolios individual
	$cod_frase = _("INDIVIDUAL_PORTFOLIOS_15");
	$cod_pagina=5;
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

$lista_topicos_ancestrais=Portfolio::RetornaTopicosAncestrais($sock, $cod_topico_raiz);
unset($path);
foreach ($lista_topicos_ancestrais as $cod => $linha)
{
	if ($cod_topico_raiz!=$linha['cod_topico'])
	{
		$path="<a href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a> &gt;&gt; ".$path;
	}
	else
	{
		$path="<a href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a>";
	}
}

if ($portfolio_grupo)
{
	$nome=Portfolio::NomeGrupo($sock,$cod_grupo_portfolio);

	//Figura de Grupo
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icGrupo.gif\" border=\"0\" />";


	/* 84 - Grupo ExcluÌdo */
	if ($grupo_apagado && $eformador) $complemento=" <span>("._("DELETED_GROUP_15").")</span>\n";

	echo("          <a href=\"#\" onclick=\"return(AbreJanelaComponentes(".$cod_grupo_portfolio."))\";>".$fig_portfolio." ".$nome."</a>".$complemento." - ");
	echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img  alt=\"\" src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\" /></a>");
}
else
{
	$nome=Usuarios::NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

	// Selecionando qual a figura a ser exibida ao lado do nome
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icPerfil.gif\" border=\"0\" />";

	echo("          <a href=\"#\" onclick=\"return(OpenWindowPerfil(".$cod_usuario_portfolio."));\" >".$fig_portfolio." ".$nome."</a>".$complemento." - ");
	echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img  alt=\"\" src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\" /></a>");
}

echo("          ".$path);

echo ("          </span>\n");

echo ("          <!-- Tabelao -->\n");
echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo ("            <tr>\n");
echo ("              <!-- Botoes de Acao -->\n");
echo ("              <td valign=\"top\">\n");
echo ("                <ul class=\"btAuxTabs\">\n");

//174 - Meus portfolios
echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;cod_usuario_portfolio=".$cod_usuario."&amp;exibir=myp\">" . _("MY_PORTFOLIOS_15") . "</a></li>\n");
// 74 - Portfolios Individuais
echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;exibir=ind\">" . _("INDIVIDUAL_PORTFOLIOS_15") . "</a></li>\n");
// 75 - Portfolios de Grupos
if ($ferramenta_grupos_s) {
	echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;exibir=grp\">" . _("GROUP_PORTFOLIOS_15") . "</a></li>\n");
	// 177 - Portfolios encerrados
	echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;exibir=enc\">" . _("ENDED_PORTFOLIOS_15") . "</a></li>\n");
}

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <ul class=\"btAuxTabs03\">\n");

$cod_topico_raiz_usuario=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,"");
$ultimo_acesso=Usuarios::PenultimoAcesso($sock,$cod_usuario,"");
$lista_comentario=Portfolio::RetornaComentariosDoItem($sock, $cod_item);

/* 23 (ger) - Voltar */
echo("                  <li><a href=\"ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">"._("BACK_-1")."</a></li>\n");
/* 70 - Ver Outros Itens */
echo("                  <li><a href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">". _("VIEW_OTHER_ITEMS_-1") ."</a></li>\n");
/* 3 (ger) - Comentar */
if ((count($lista_comentario)>0)&&($lista_comentario!=""))
	echo("                  <li><span id=\"btnComentar\" onclick=\"EnviarComent();\">"._("TO_COMMENT_-1")."</span></li>\n");
else
	echo("                  <li><span id=\"btnComentar\">"._("COMMENT_-1")."</span></li>\n");

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr>\n");
if ((count($lista_comentario)>0)&&($lista_comentario!=""))
	echo("                    <td colspan=\"3\" width=\"70%\" id=\"td_coment\" style=\"background-color:#DCDCDC;\" align=\"left\">\n");
else
	echo("                    <td colspan=\"3\" width=\"70%\" id=\"td_coment\" align=\"left\">\n");

echo("                      <form name=\"formFiles\" id=\"formFiles\" action=\"".$ctrl_portfolio."acoes.php\" method=\"post\" enctype=\"multipart/form-data\" onsubmit=\"return(submitForm());\">\n");
echo("                        <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
echo("                        <input type=\"hidden\" name=\"cod_curso\" value=\"".$cod_curso."\" />\n");
echo("                        <input type=\"hidden\" name=\"cod_item\" value=\"".$cod_item."\" />\n");
echo("                        <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
echo("                        <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"".$cod_usuario_portfolio."\" />\n");
echo("                        <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"".$cod_grupo_portfolio."\" />\n");
echo("                        <input type=\"hidden\" name=\"acao\" value=\"comentar\" />\n");


if ((count($lista_comentario)>0)&&($lista_comentario!=""))
	echo("                        <div id=\"text_coment\" class=\"divHidden\">\n");
else
	echo("                        <div id=\"text_coment\" class=\"divShow\">\n");

echo("                          <b>"._("COMMENT_-1").":</b><br />\n");
echo("                          <textarea name=\"comentario\" id=\"textArea_coment\" rows=\"8\" cols=\"70\" style=\"border: 2px solid #9bc;\"></textarea><br /><br />\n");
echo("                          <img alt=\"\" src=\"".$diretorio_imgs."paperclip.gif\" border=\"0\" /><span id=\"anexar_arquivo\" onclick=\"addInputFile();\" class=\"link\">"._("ATTACH_FILE_-1")."</span>\n");
echo("                        </div>\n");

if ((count($lista_comentario)>0)&&($lista_comentario!="")){
	echo("                        <div id=\"button_coment\" class=\"divHidden\">\n");
	echo("                          <br />\n");
	echo("                          <input type=\"submit\" id=\"OKComent\" value=\""._("OK_-1")."\" class=\"input\" />\n");
	echo("                          <input type=\"button\" id=\"cancComent\" onclick=\"CancelarComent();\" value=\""._("CANCEL_-1")."\" class=\"input\" />\n");

	echo("                        </div>\n");
}else{
	echo("                        <div id=\"button_coment\" class=\"divShow\">\n");
	echo("                          <br />\n");
	echo("                          <input class=\"input\" type=\"submit\" id=\"OKComent\" value=\""._("OK_-1")."\" />\n");
	echo("                          <input class=\"input\" type=\"button\" id=\"cancComent\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$cod_item."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."';\" value=\""._("CANCEL_-1")."\" />\n");

	echo("                        </div>\n");
}

echo("                      </form>\n");
echo("                    </td>\n");
echo("                    <td style=\"background-color:#DCDCDC; border:0;\">\n");
echo("                    </td>\n");
echo("                  </tr>\n");

if ((count($lista_comentario)>0)&&($lista_comentario!=""))
{
	echo("                  <tr class=\"head\">\n");
	/* 105 - Coment·rio */
	echo("                    <td colspan=\"3\" width=\"70%\">"._("COMMENT_-1")."</td>\n");
	/* 109 - Emissor */
	echo("                    <td colspan=\"1\">"._("SENDER_-1")."</td>\n");
	echo("                  </tr>\n");
	$i=1;
	foreach ($lista_comentario as $cod => $linha)
	{
		echo("                  <tr class=\"altColor".($i%2)."\">\n");
		$i++;

		$cod_autor = $linha['cod_comentarista'];
		$data_coment=Data::UnixTime2DataHora($linha['data']);

		$bstt="";
		$bend="";
		if ($linha['data']>$ultimo_acesso)
		{
			$bstt="<b>";
			$bend="</b>";
		}


		$dir_item_temp=Portfolio::CriaLinkVisualizarComentar($sock, $cod_curso, $cod_usuario, $linha['cod_comentario'], $diretorio_arquivos, $diretorio_temp);
		//listagem dos arquivos
		
		$lista_arq=Portfolio::RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['link']);
		echo("                    <td colspan=\"3\" align=\"left\">\n");
		echo("                      ".ConversorTexto::IndentarComentario(ConversorTexto::Enter2Br($linha['comentario']),"                      ")."<br /><br />\n");
		
		if (count($lista_arq)>0)
		{
			// Procuramos na lista de arquivos se existe algum visivel
			$ha_visiveis = false;
		
			reset ($lista_arq);
			while (( list($cod, $linha) = each($lista_arq) ) && !$ha_visiveis)
			{
				$ha_visiveis = !($linha['Status']);
			}
		
			if ($ha_visiveis)
			{
				/* 71 - Arquivos */
				echo("                      <b>"._("FILES_-2").": </b> ");
				$dir_atual="";
				$c = 0;
		
				foreach($lista_arq as $cod_arq => $linha_arq)
				{
					if (!$linha_arq['Status'])
					{
						//Vamos exibir todos os arquivos em um mesmo nivel, como se nao houvesse pastas
						$linha_arq['Arquivo'] = mb_convert_encoding($linha_arq['Arquivo'], "ISO-8859-1", "UTF-8");
						if ($linha_arq['Arquivo'] != "")
						{
							$caminho_arquivo = $dir_item_temp['link'].ConversorTexto::ConverteUrl2Html($linha['Diretorio']."/".$linha_arq['Arquivo']);
							$tag_abre  = "<a href=".$caminho_arquivo." onclick=\"WindowOpenVer('".$caminho_arquivo."');return(false);\">";
							$tag_fecha = "</a>";
							echo($tag_abre.$imagem.$linha_arq['Arquivo'].$tag_fecha);
						}
						if ($cod_arq<count($lista_arq)-1)
							echo(" | ");
						else
							echo("\n");
					}
				}
			}
		}
		//fim da listagem dos arquivos
		
		echo("                    </td>\n");
		echo("                    <td>\n");
		echo("                      <a href=# onclick=\"OpenWindowPerfil(".$cod_autor.");return(false);\">".Usuarios::NomeUsuario($sock,$cod_autor, $cod_curso)."</a><br />"._("COMMENT_DONE_IN_-1")." <br />".$data_coment."\n");
		echo("                    </td>\n");
		echo("                  </tr>\n");
		}
		}
		echo("                </table>\n");//TabInterna
		echo("              </td>\n");
		echo("            </tr>\n");
		echo("          </table>\n");//TabExterna
		echo("        </td>\n");
		echo("      </tr>\n");
		
		
		require_once $view_administracao.'tela2.php';
		echo("  </body>\n");
		echo("</html>\n");
		
		AcessoSQL::Desconectar($sock);
		
		?>
				
