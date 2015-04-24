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

$cod_curso = $_GET['cod_curso'];
$cod_usuario_portfolio = $_GET['cod_usuario_portfolio'];
$cod_usuario = $_GET['cod_usuario_portfolio'];
$cod_item = $_GET['cod_item'];
$cod_grupo_portfolio = $_GET['cod_grupo_portfolio'];
$cod_topico_raiz = $_GET['cod_topico_raiz'];

require_once $view_administracao.'topo_tela.php';

$sock=AcessoSQL::Conectar("");

$diretorio_arquivos=Portfolio::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Portfolio::RetornaDiretorio($sock,'ArquivosWeb');

AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

Portfolio::ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

$linha_item=Portfolio::RetornaDadosDoItem($sock, $cod_item);

/* Página Principal */

$status_portfolio = Portfolio::RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $linha_item['cod_grupo']);

$dono_portfolio    = $status_portfolio ['dono_portfolio'];
$portfolio_apagado = $status_portfolio ['portfolio_apagado'];
$portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

// 1 - Portfólio
$cabecalho = ("<br /><br /><h4>"._("PORTFOLIO_15"));

// 2 - Portfolio individual
// 3 - Portfolio de grupo
$tipo_portfolio = ($portfolio_grupo ? _("GROUP_PORTFOLIO_15") : _("INDIVIDUAL_PORTFOLIO_15") );

/* 72 - Histórico */
$cabecalho.= (" - ".$tipo_portfolio." / "._("RECORD_OF_CHANGES_-1")."</h4>\n");
echo($cabecalho);
// 3 A's - Muda o Tamanho da fonte
echo("      <div id=\"mudarFonte\" style=\"top: 42px;\">\n");
echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

echo ("<br />\n");

$figura = "".$diretorio_imgs."arquivo_";
$figura.= ($portfolio_grupo   ? "g_" : "i_");
if ($portfolio_apagado)
{
	$figura .= "x.gif";
}
else
{
	if ($dono_portfolio)
		$figura .= "p.gif";
	else
		$figura .= "n.gif";
}

echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("      <tr>\n");
echo("        <td valign=\"top\" colspan=3>\n");
echo("          <ul class=\"btAuxTabs\">\n");
/* 13 - Fechar (ger) */
echo("            <li><span onclick=\"self.close();\">"._("CLOSE_-1")."</span></li>\n");
echo("          </ul>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("      <tr>\n");
echo("        <td colspan=3>\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("            <tr>\n");
echo("              <td  align=right><b>"._("ITEM_-1").":&nbsp;</b></td>\n");
echo("              <td colspan=2>".$linha_item['titulo']."</td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("      <tr>\n");
echo("        <td>\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("            <tr>\n");
/* 89 - Ação */
echo("              <td align=center><b>"._("ACTION_-1")."</b></td>\n");
/* 9 - Data */
echo("              <td align=center><b>"._("DATE_-1")."</b></td>\n");
/* 90 - Usuário */
echo("              <td align=center><b>"._("USER_-3")."</b></td>\n");
echo("            </tr>\n");

$res=Portfolio::RetornaResHistoricoDoItem($sock, $cod_item);

$res2=AcessoSQL::RetornaArrayLinhas($res);

foreach($res2 as $cod => $linha){
	switch ($linha['acao']){

		/* 93 - Criação */
		case ('C'): $acao=_("CREATION_-1"); break;
		/* 94 - Edição Cancelada */
		case ('D'): $acao=_("EDITION_CANCELED_-1"); break;
		/* 54 - Em Edição */
		case ('E'): $acao=_("IN_EDITION_-1"); break;
		/* 91 - Edição Finalizada */
		case ('F'): $acao=_("EDITION_ENDED_-1"); break;
		/* 95 - Movida */
		case ('M'): $acao=_("MOVED_-1"); break;
		/* 96 - Exclusão */
		case ('A'): $acao=_("EXCLUSION_-1"); break;
		/* 97 - Recuperação */
		case ('R'): $acao=_("RETRIEVERING_-1"); break;
		/* 98 - Excluída definitivamente */
		case ('X'): $acao=_("DELETED_PERMANENTLY_-1"); break;
		/* 92 - Desconhecida */
		default: $acao=_("UNKNOWN_-1"); break;
	}

	$data = Data::UnixTime2DataHora($linha['data']);
	$nome_usuario="<a href=\"#\" onclick=\"return(OpenWindowPerfil(".$linha['cod_usuario']."));\">".Usuarios::NomeUsuario($sock, $linha['cod_usuario'], $cod_curso)."</a>";

	echo("            <tr>\n");
	echo("              <td align=center><font class=text>".$acao."</font></td>\n");
	echo("              <td align=center><font class=text>".$data."</font></td>\n");
	echo("              <td align=center><font class=text>".$nome_usuario."</font></td>\n");
	echo("            </tr>\n");
}

echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("    </table>\n");
echo("  </body>\n");
echo("</html>\n");

?>
