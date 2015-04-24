<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$view_portfolio = '../../'.$ferramenta_portfolio.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_ferramenta = 15;
$cod_ferramenta_ajuda = 15;
$cod_pagina_ajuda = 1;

$cod_curso = $_GET['cod_curso'];
$cod_usuario = ((isset($_GET['cod_usuario'])) ? $_GET['cod_usuario'] : $_GET['cod_usuario_portfolio']);
$cod_topico_s = $_GET['cod_topico'];

$exibir = ((isset($_GET['exibir'])) ? $_GET['exibir'] : $_POST['exibir']);
$cod_usuario_portfolio = ((isset($_GET['cod_usuario_portfolio'])) ? $_GET['cod_usuario_portfolio'] : $_POST['cod_usuario_portfolio']);

$sock1 = AcessoSQL::Conectar("");

$eformador = Usuarios::EFormador($sock1,$cod_curso,$cod_usuario);
$visitante = Usuarios::EVisitante($sock1, $cod_curso, $cod_usuario);
$colaborador = Usuarios::EColaborador($sock1, $cod_curso, $cod_usuario);

// verificamos se a ferramenta de Avaliacoes estah disponivel
AcessoSQL::Desconectar($sock1);

require_once $view_administracao.'topo_tela.php';

/* Necessário para a lixeira. */
$_SESSION['cod_topico_s'] = $cod_topico_s;
unset($cod_topico_s);

$sock = AcessoSQL::Conectar($cod_curso);

$ferramenta_avaliacao = Usuarios::TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, 22);

$ferramenta_grupos_s = Portfolio::StatusFerramentaGrupos($sock);
$_SESSION['ferramenta_grupos_s'] = $ferramenta_grupos_s;

$data_acesso=Usuarios::PenultimoAcesso($sock,$cod_usuario,"");

if (isset ($exibir)) // Entao estamos vindo de alguma outra pagina, atraves de menu
{
	if($exibir=="ind")
		$acao_portfolio_s='I';
	/*Considerando o novo modo de exibição inicial -> meus portfolios.*/
	else if ($exibir=="myp")
		$acao_portfolio_s='M';
	else if ($exibir=="grp")
		$acao_portfolio_s='G';
	else
		$acao_portfolio_s='F';
}
else{
	$acao_portfolio_s='I';
}

$_SESSION['acao_portfolio_s'] = $acao_portfolio_s;

// 75 - Portfolios de grupos
// 74 - Portfolios individuais
// 174 - Meus Portfolios
if ($acao_portfolio_s=='M')
{
	$cod_frase = _("MY_PORTFOLIOS_15");
	if ($ferramenta_avaliacao)
		// ajuda para meus portfolios sem ferramenta avaliacao
		$cod_pagina = 33;
	else
		// ajuda para portfolios individuais sem ferramenta avaliacao
		$cod_pagina = 33;
}
else if ($ferramenta_grupos_s && 'G' == $acao_portfolio_s)
{
	$cod_frase = _("GROUP_PORTFOLIOS_15");
	if ($ferramenta_avaliacao)
		// ajuda para portfolios de grupos com ferramenta avaliacao
		$cod_pagina = 17;
	else
		// ajuda para portfolios de grupos sem ferramenta avaliacao
		$cod_pagina = 2;
}
else if ($acao_portfolio_s=='I')
{
	$cod_frase = _("INDIVIDUAL_PORTFOLIOS_15");
	if ($ferramenta_avaliacao)
		// ajuda para portfolios individuais sem ferramenta avaliacao
		$cod_pagina = 16;
	else
		// ajuda para portfolios individuais sem ferramenta avaliacao
		$cod_pagina = 1;
}
else if ($ferramenta_grupos_s && 'F' == $acao_portfolio_s)
{
	$cod_frase = _("ENDED_PORTFOLIOS_15");
	if ($ferramenta_avaliacao)
		// ajuda para portfolios de grupos encerrados com ferramenta avaliacao
		$cod_pagina = 32;
	else
		// ajuda para portfolios de grupos encerrados sem ferramenta avaliacao
		$cod_pagina = 32;
}

echo("    <script type='text/javascript' src='".$diretorio_jscss."bib_ajax.js'> </script>\n");
echo("    <script type='text/javascript'>\n");

echo("      function Iniciar()\n");
echo("      {\n");
echo("        startList();\n");
echo("      }\n\n");

echo("    var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
echo("    var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
echo("    var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
echo("    var Xpos, Ypos;\n");
echo("    var js_cod_item, js_cod_topico;\n");
echo("    var js_nome_topico;\n");
echo("    var js_tipo_item;\n");
echo("    var editando=0;\n");
echo("    var mostrando=0\n");
echo("    var js_comp = new Array();\n\n");

echo("    if (isNav)\n");
echo("    {\n");
echo("      document.captureEvents(Event.MOUSEMOVE);\n");
echo("    }\n");
echo("    document.onmousemove = TrataMouse;\n\n");

echo("    function TrataMouse(e)\n");
echo("    {\n");
echo("      Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
echo("      Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
echo("    }\n\n");

echo("    function getPageScrollY()\n");
echo("    {\n");
echo("      if (isNav)\n");
echo("        return(window.pageYOffset);\n");
echo("      if (isIE){\n");
echo("        if(document.documentElement.scrollLeft>=0){\n");
echo("          return document.documentElement.scrollTop;\n");
echo("        }else if(document.body.scrollLeft>=0){\n");
echo("          return document.body.scrollTop;\n");
echo("        }else{\n");
echo("          return window.pageYOffset;\n");
echo("        }\n");
echo("      }\n");
echo("    }\n");

echo("    function AjustePosMenuIE()\n");
echo("    {\n");
echo("      if (isIE)\n");
echo("        return(getPageScrollY());\n");
echo("      else\n");
echo("        return(0);\n");
echo("    }\n\n");

echo("    function MostraLayer(cod_layer, ajuste)\n");
echo("    {\n");
echo("      EscondeLayers();\n");
echo("      moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
echo("      if (editando>0){\n");
echo("          if (editando==2) editando=0;\n");
echo("      return false;\n");
echo("      }\n");
echo("      mostrando=1;\n");
echo("      showLayer(cod_layer);\n");
echo("    }\n");

echo("    function EscondeLayer(cod_layer)\n");
echo("    {\n");
echo("      hideLayer(cod_layer);\n");
echo("      mostrando=0;\n");
echo("    }\n");
echo(" \n\n");

echo("  </script>\n");

require_once $view_administracao.'menu_principal.php';

echo("          <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

Portfolio::ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

echo("            <h4>"._("PORTFOLIO_15")." - ".$cod_frase."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("            <div id=\"mudarFonte\">\n");
echo("              <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("              <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("              <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");
echo("          <ul id=\"legenda\">\n");

if ('I' == $acao_portfolio_s)
{
	if (!$colaborador)
	{
		// 130 - Meu Portfólio
		echo("              <li><img src=\"".$diretorio_imgs."icPasta.gif\" alt=\"\" border=\"0\" />"._("MY_PORTFOLIO_15")."</li>\n");

		// 131 - Portfólios de outros participantes
		$frase_outros_participantes = _("OTHER_PARTICIPANTS_PORTFOLIOS_15");
	}

	//else = é colaborador
	else
	{
		// 135 - Portfólios dos participates do curso
		$frase_outros_participantes = _("PARTICIPANTS_PORTFOLIOS_15");
	}

	echo("              <li><img src=\"".$diretorio_imgs."icPasta2.gif\" alt=\"\" border=\"0\" />".$frase_outros_participantes."</li>\n");
	// 121 - Portfólios de ex-alunos
	echo("              <li><img src=\"".$diretorio_imgs."icPasta3.gif\" alt=\"\" border=\"0\" />"._("REJECTED_STUDENTS_POTFOLIOS_15")."</li>\n");

}
else if ('G' == $acao_portfolio_s)
{
	if (!$colaborador)
	{
		// 133 - 'Portfólios dos meus grupos'
		echo("              <li><img src=\"".$diretorio_imgs."icPasta.gif\" alt=\"\" border=\"0\" />"._("MY_GROUPS_PORTFOLIOS_15")."</li>\n");

		// 134 - 'Portfolios de outros grupos'
		$frase_outros_grupos = _("OTHER_GROUPS_PORTFOLIOS_15");
	}
	else
	{
		// 75 - Portfolios de grupos
		$frase_outros_grupos = _("GROUP_PORTFOLIOS_15");
	}

	echo("              <li><img src=\"".$diretorio_imgs."icPasta2.gif\" alt=\"\" border=\"0\" />".$frase_outros_grupos."</li>\n");
}
else if('M' == $acao_portfolio_s)
{
	/* 185 - Portfólio de grupos que estou participando 
	 * 186 - Portfólio de grupos encerrados que participei*/
	echo("              <li><img src=\"".$diretorio_imgs."icPasta.gif\" alt=\"\" border=\"0\" />"._("MY_PORTFOLIO_15")."</li>\n");
	echo("              <li><img src=\"".$diretorio_imgs."icPasta2.gif\" alt=\"\" border=\"0\" />"._("GROUP_PORTFOLIOS_PARTICIPATING_15")."</li>\n");
	echo("              <li><img src=\"".$diretorio_imgs."icPasta3.gif\" alt=\"\" border=\"0\" />"._("ENDED_GROUPS_PORTFOLIOS_PATICIPATED_15")."</li>\n");
}
else if('F' == $acao_portfolio_s)
{
	// 122 - 'Portfólios de grupos encerrados'
	echo("              <li><img src=\"".$diretorio_imgs."icPasta3.gif\" alt=\"\" border=\"0\" />"._("ENDED_GROUPS_PORTFOLIOS_15")."</li>\n");
}

echo("            </ul>\n");
echo("            <!-- Tabelao -->\n");
echo("            <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("              <tr>\n");
echo("              <!-- Botoes de Acao -->\n");
echo("                <td>\n");
echo("                  <ul class=\"btAuxTabs\">\n");

//174 - Meus portfolios
echo("                    <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=myp\">"._("MY_PORTFOLIOS_15")."</a></li>\n");
// 74 - Portfolios Individuais
echo("                    <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=ind\">"._("INDIVIDUAL_PORTFOLIOS_15")."</a></li>\n");
// 75 - Portfolios de Grupos
if ((isset($ferramenta_grupos_s))&&($ferramenta_grupos_s)){
	echo("                    <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=grp\">"._("GROUP_PORTFOLIOS_15")."</a></li>\n");
	// 177 - Portfolios encerrados
	echo("                    <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=enc\">"._("ENDED_PORTFOLIOS_15")."</a></li>\n");
}

echo("                  </ul>\n");
echo("                </td>\n");
echo("              </tr>\n");
echo("              <tr>\n");
echo("                <td valign=\"top\">\n");

// creio q aki ele cria os portfolios de grupos se nao existir
Portfolio::VerificaPortfolioGrupos($sock,$cod_usuario);

// a unica maneira de chamar ver_portfolios com esta variavel é através do menu de portfolios
// se a variavel estiver setada, é que é preciso mudar a variável de sessão

$acao_portfolio = $_GET['acao_portfolio'];
if (isset ($acao_portfolio))
	$_SESSION['acao_portfolio_s'] = $acao_portfolio;

$lista_topicos=Portfolio::RetornaTopicosBase($sock, $cod_usuario, $cod_usuario_portfolio, $eformador, $acao_portfolio_s, $cod_curso);

echo("                  <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                    <tr class=\"head\">\n");

/* 8 - Portfolio */
echo("                      <td class=\"alLeft\">"._("PORTFOLIO_15")."</td>\n");
/* 9 - Data */
echo("                      <td width=\"60\" align=\"center\">"._("DATE_-1")."</td>\n");
/* 82 - Itens */
echo("                      <td width=\"30\" align=\"center\">"._("ITEMS_-1")."</td>\n");
/* 83 - Itens não comentados */
echo("                      <td width=\"110\" align=\"center\">"._("UNCOMMENTED_ITEMS_-1")."</td>\n");

if (count($lista_topicos)<1)
{
	echo("                    </tr>\n");
	echo("                    <tr>\n");
	/* 80 - Não há nenhum portfólio */
	echo("                      <td colspan=\"5\">"._("NO_PORTFOLIOS_15")."</td>\n");
	echo("                    </tr>\n");
	echo("                  </table>\n");
}

else
{
	echo("                    </tr>\n");

	foreach ($lista_topicos as $cod_topico => $linha_topico)
	{
		if ($dono_portfolio)
			$max_data=Portfolio::RetornaMaiorDataItemComentario($sock,$cod_topico,'P',$linha_topico['data'],$cod_usuario);
		else if ($eformador)
			$max_data=Portfolio::RetornaMaiorDataItemComentario($sock,$cod_topico,'F',$linha_topico['data'],$cod_usuario);
		else
			$max_data=Portfolio::RetornaMaiorDataItemComentario($sock,$cod_topico,'T',$linha_topico['data'],$cod_usuario);
		$data=Data::UnixTime2Data($max_data);

		if ($data_acesso<$max_data)
		{
			$marcaib = "<b>";
			$marcafb = "</b>";
			$marcatr="class=\"novoitem\"";
		}
		else
		{
			$marcaib = "";
			$marcafb = "";
			$marcatr="";
		}

		$span="<span class=\"link\" onclick=\"window.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico."&amp;cod_usuario_portfolio=".$linha_topico['cod_usuario']."&amp;cod_grupo_portfolio=".$linha_topico['cod_grupo']."';\">";

		$arquivo = $linha_topico['figura'];

		echo("                    <tr ".$marcatr.">\n");
		echo("                      <td class=\"".$arquivo."\">".$span.$linha_topico['topico']."</span></td>\n");
		echo("                      <td>".$marcaib.$data.$marcafb."</td>\n");
		echo("                      <td>".$marcaib.$linha_topico['num_itens'].$marcafb."</td>\n");
		echo("                      <td>".$marcaib.$linha_topico['num_itens_nao_comentados'].$marcafb."</td>\n");
		echo("                    </tr>\n");
	}
	echo("                  </table>\n");
}

echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");

require_once $view_administracao.'tela2.php';

AcessoSQL::Desconectar($sock);
echo("  </body>\n");
echo("</html>\n");

?>