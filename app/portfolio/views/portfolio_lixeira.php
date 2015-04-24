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
$cod_pagina_ajuda = 5;

$cod_curso = $_GET['cod_curso'];
$cod_usuario_portfolio = $_GET['cod_usuario_portfolio'];
$cod_usuario = $_GET['cod_usuario_portfolio'];
$cod_item = $_GET['cod_item'];
$cod_grupo_portfolio = $_GET['cod_grupo_portfolio'];
$cod_topico_raiz = $_GET['cod_topico_raiz'];

if(!isset($_GET['cod_grupo_portfolio'])){
	$cod_grupo_portfolio = NULL;
}

require_once $view_administracao.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject($lista_frases);
//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("excluirItens", _("ITEMS_DELETED_SUCCESS_-1"), 0);
$feedbackObject->addAction("recuperarItens", _("ITEMS_RETRIEVED_SUCESS_-1"), 0);


$sock1 = AcessoSQL::Conectar("");
$eformador   = Usuarios::EFormador($sock1,$cod_curso,$cod_usuario);
$colaborador = Usuarios::EColaborador($sock1, $cod_curso, $cod_usuario);
$diretorio_arquivos=Portfolio::RetornaDiretorio($sock1,'Arquivos');
$diretorio_temp=Portfolio::RetornaDiretorio($sock1,'ArquivosWeb');

system ("rm ../../../diretorio/portfolio_".$cod_curso."_*_".$cod_usuario);

$var = $diretorio_temp."/portfolio_".$cod_curso."_*_".$cod_usuario;

foreach (glob($var) as $filename)
{
	if(Arquivos::ExisteArquivo($filename))
		(Arquivos::RemoveArquivo($filename));
}

AcessoSQL::Desconectar($sock1);

$sock = AcessoSQL::Conectar($cod_curso);

$data_acesso=Usuarios::PenultimoAcesso($sock,$cod_usuario,"");

$cod_topico_raiz_usuario=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,"");

if (!isset($cod_topico_raiz))
{
	if ($cod_grupo_portfolio!="" && $cod_grupo_portfolio!="NULL")
		$cod_topico_raiz=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo_portfolio);
	else if ($cod_usuario_portfolio!="")
		$cod_topico_raiz=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario_portfolio,"");
	else
	{
		$cod_topico_raiz=$cod_topico_raiz_usuario;
		$cod_usuario_portfolio=$cod_usuario;

		/* Checagem da existÃªncia das pastas dos grupos a que o usuÃ¡rio pertence */
		Portfolio::VerificaPortfolioGrupos($sock,$cod_usuario);
	}
}

$status_portfolio = Portfolio::RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

$dono_portfolio    = $status_portfolio ['dono_portfolio'];
$portfolio_apagado = $status_portfolio ['portfolio_apagado'];
$portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

$ferramenta_grupos_s = Portfolio::StatusFerramentaGrupos($sock);
$_SESSION['ferramenta_grupos_s'] = $ferramenta_grupos_s;

echo("    <script type='text/javascript'>\n");

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

echo("    var array_itens;\n\n");

echo("    function VerificaCheck(){\n");
echo("      var i;\n");
echo("      var j=0;\n");
echo("      var cod_itens=document.getElementsByName('chkItem');\n");
echo("      var Cabecalho = document.getElementById('checkMenu');\n");
echo("      array_itens = new Array();\n");
echo("      for (i=0; i < cod_itens.length; i++){\n");
echo("        if (cod_itens[i].checked){\n");
echo("          var item = cod_itens[i].id.split('_');\n");
echo("          array_itens[j]=item[1];\n");
echo("          j++;\n");
echo("        }\n");
echo("      }\n");
echo("      if (j==cod_itens.length) Cabecalho.checked=true;\n");
echo("      else Cabecalho.checked=false;\n");
echo("      if(j>0){\n");
echo("        document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
echo("        document.getElementById('mRecuperar_Selec').className=\"menuUp02\";\n");
echo("        document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
echo("        document.getElementById('mRecuperar_Selec').onclick=function(){ RecuperarSelecionados(); };\n");
echo("      }else{\n");
echo("        document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
echo("        document.getElementById('mRecuperar_Selec').className=\"menuUp\";\n");
echo("        document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
echo("        document.getElementById('mRecuperar_Selec').onclick=function(){  };\n");
echo("      }\n");
echo("    }\n\n");

echo("    function CheckTodos(){\n");
echo("      var e;\n");
echo("      var i;\n");
echo("      var CabMarcado = document.getElementById('checkMenu').checked;\n");
echo("      var cod_itens=document.getElementsByName('chkItem');\n");
echo("      for(i = 0; i < cod_itens.length; i++)\n");
echo("      {\n");
echo("        e = cod_itens[i];\n");
echo("        e.checked = CabMarcado;\n");
echo("      }\n");
echo("      VerificaCheck();\n");
echo("    }\n\n");

echo("    function ExcluirSelecionados(){\n");
/* 120 - Tem certeza de que deseja excluir este item ? */
/* 100 - (o item será excluído definitivamente) */
echo("      if (confirm('"._("SURE_TO_DELETE_ITEM_-1")."\\n"._("ITEM_DELETED_PERMANENTLY_-1")."')){\n");
echo("			$.post(\"".$model_portfolio."excluir_item.php\",{cod_curso:".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_itens: array_itens}, \n");
echo("				function(data){\n");
echo("					Recarregar('excluirItens', 'true');\n");
echo("			});\n");
echo("      }\n");
echo("    }\n\n");

echo("    function RecuperarSelecionados(){\n");
/* 101 - VocÃª tem certeza de que deseja recuperar este item? */
/* 102 - (o item será movida para a pasta Raiz e estará como não compartilhado) */
echo("      if (confirm('"._("SURE_TO_RETRIEVE_ITEM_-1")."\\n"._("ITEM_MOVED_ROOT_FOLDER_-1")."')){\n");
echo("			$.post(\"".$model_portfolio."recuperar_item.php\",{cod_curso:".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_grupo_portfolio: '".$cod_grupo_portfolio."', cod_itens: array_itens}, \n");
echo("				function(data){\n");
echo("					Recarregar('recuperarItens', 'true');\n");
echo("			});\n");
echo("      }\n");
echo("    }\n\n");

echo("    function Recarregar(acao, atualizacao){\n");
echo("      window.location='portfolio_lixeira.php?cod_curso=".$cod_curso."&cod_topico_raiz=".$cod_topico_raiz."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_usuario_portfolio=".$cod_usuario_portfolio."&acao='+acao+'&atualizacao='+atualizacao;\n");
echo("    }\n\n");

echo("    </script>\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/* Página Principal */

if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
{
	// 3 - Portfolios de grupos
	$cod_frase = _("GROUP_PORTFOLIOS_15");
	$cod_pagina=14;
}
else
{
	// 2 - Portfolios individual
	$cod_frase = _("INDIVIDUAL_PORTFOLIOS_15");
	$cod_pagina=8;
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

unset($path);

/* 7 - Lixeira */
$path="        <b>"._("TRASH_-1")."</b>";

if ($portfolio_grupo)
{
	$nome=Portfolio::NomeGrupo($sock,$cod_grupo_portfolio);

	//Figura ao lado do texto
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icGrupo.gif\" border=\"0\" />";

	echo("          <a class=\"text\" href=\"#\" onclick=\"return(AbreJanelaComponentes(".$cod_grupo_portfolio."))\";>".$fig_portfolio." ".$nome."</a>".$complemento." - ");
	echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\"/></a>");


}
else
{
	$nome=Usuarios::NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

	// Selecionando qual a figura a ser exibida ao lado do nome
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icPerfil.gif\" border=\"0\" />";

	/* 85 - Aluno Rejeitado */
	if (Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario_portfolio)=="r" && $eformador) $complemento=" <font class=textsmall>("._("REJECTED_STUDENT_0").")</font>\n";

	echo("          <a href=\"#\" onclick=\"return(OpenWindowPerfil(".$cod_usuario_portfolio."));\" >".$fig_portfolio." ".$nome."</a>".$complemento." - ");
	echo("          <a href=\"#\" onMouseDown=\"MostraLayer(cod_topicos,0);return(false);\"><img src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\"/></a>");
}

echo ($path);

echo("          <!-- Tabelao -->\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <!-- Botoes de Acao -->\n");
echo("                <td valign=\"top\">\n");
echo("                  <ul class=\"btAuxTabs\">\n");

//174 - Meus portfolios
echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=myp\">"._("MY_PORTFOLIOS_15")."</a></li>\n");
// 74 - Portfolios Individuais
echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=ind\">"._("INDIVIDUAL_PORTFOLIOS_15")."</a></li>\n");
// 75 - Portfolios de Grupos
if ($ferramenta_grupos_s) {
	echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=grp\">"._("GROUP_PORTFOLIOS_15")."</a></li>\n");
	// 177 - Portfolios encerrados
	echo("                    <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=enc\">"._("ENDED_PORTFOLIOS_15")."</a></li>\n");
}

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <ul class=\"btAuxTabs03\">\n");

// 69 - Atualizar
echo("		<li> <span onclick=\"window.location.reload();\">"._("UPDATE_-1")."</span></li>\n");


echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\"/></td>\n");

/* 82 - Itens */
echo("                    <td class=\"alLeft\">"._("ITEMS_-1")."</td>\n");
/* 9 - Data */
echo("                    <td width=\"60\" align=\"center\">"._("DATE_-1")."</td>\n");

echo("                  </tr>\n");

$lista_itens=Portfolio::RetornaItensDaLixeira($sock, $cod_usuario,$cod_usuario_portfolio,$cod_grupo_portfolio);

if ((!is_array($lista_itens))||(count($lista_itens)<1))
{
	echo("                  <tr>\n");
	/* 11 - Não há nenhum item neste portfólio */
	echo("                    <td>&nbsp;</td>\n");
	echo("                    <td>"._("NO_ITEM_IN_THIS_PORTFOLIO_15")."</td>\n");
	echo("                    <td>&nbsp;</td>\n");
	echo("                  </tr>\n");
}
//else = existe item(ns) na lixeira
else
{
	// definindo qual figura para representar pastas ou arquivos (itens)
	$arquivo = "arquivo_";

	// aqui, escolho entre a figura para grupo ou individual
	if ($portfolio_grupo) $gi="g_";
	else $gi="i_";

	$arquivo.= $gi;

	// aqui, escolho entre pessoal, nao-pessoal ou apagado
	if ($dono_portfolio) $pnx="p.gif";
	else if ($portfolio_apagado) $pnx="x.gif";
	else $pnx="n.gif";

	$arquivo.= $pnx;

	foreach ($lista_itens as $cod => $linha_item)
	{
		$data=Data::UnixTime2Data($linha_item['data']);
		$titulo="<span class=\"link\" onclick=\"window.location='ver_lixeira.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha_item['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\"> ".$linha_item['titulo']."</span>";

		echo("                  <tr id=\"tr_".$linha_item['cod_item']."\">\n");
		echo("                    <td><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_item']."\" onclick='VerificaCheck();' value=\"".$linha_item['cod_item']."\"/></td>\n");

		$icone = "<img src=\"".$diretorio_imgs."".$arquivo."\" border=\"0\"/>";

		echo("                    <td class=\"itens\">".$icone.$titulo."</td>\n");
		echo("                    <td><span id=\"data_".$linha_item['cod_item']."\">".$data."</span></td>\n");
		echo("                  </tr>\n");
	}
}

echo("                </table>\n");

echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("          <ul>\n");
echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">"._("DELETE_SELECTED_-1")."</span></li>\n");
echo("            <li id=\"mRecuperar_Selec\" class=\"menuUp\"><span id=\"moverSelec\">"._("RETRIEVE_SELECTED_-1")."</span></li>\n");
echo("          </ul>\n");
echo("        </td>\n");
echo("      </tr>\n");
require_once $view_administracao.'tela2.php';
echo("  </body>\n");
echo("</html>\n");
AcessoSQL::Desconectar($sock);
?>