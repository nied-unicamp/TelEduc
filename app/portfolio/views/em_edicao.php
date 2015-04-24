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

$cod_curso = $_GET['cod_curso'];
$cod_usuario_portfolio = $_GET['cod_usuario_portfolio'];
$cod_usuario = $_GET['cod_usuario_portfolio'];
$cod_item = $_GET['cod_item'];
$cod_grupo_portfolio = $_GET['cod_grupo_portfolio'];
$cod_topico_raiz = $_GET['cod_topico_raiz'];

$cod_usuario=AcessoPHP::VerificaAutenticacao($cod_curso);

$sock=AcessoSQL::Conectar("");

$diretorio_arquivos=Portfolio::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Portfolio::RetornaDiretorio($sock,'ArquivosWeb');

AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

Usuarios::VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta);

echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n");
echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
echo("<html lang=\"pt\">\n");
/* 1 - Portfólio */
echo("  <head>\n");
echo("    <title>TelEduc - "._("PORTFOLIO_15")."</title>\n");
echo("    <link href=\"".$diretorio_jscss."ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");

Portfolio::ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

echo("  </head>\n");
echo("  <body link=#0000ff vlink=#0000ff onLoad=\"self.focus();\">\n");

/* Página Principal */
$linha_item=Portfolio::RetornaDadosDoItem($sock, $cod_item);

$status_portfolio = Portfolio::RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $linha_item ['cod_grupo']);

$dono_portfolio    = $status_portfolio ['dono_portfolio'];
$portfolio_apagado = $status_portfolio ['portfolio_apagado'];
$portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

/* 1 - Portfólio */
$cabecalho =  "<br /><br /><h4>"._("PORTFOLIO_15");

/* 54 - Em Edição */
$cabecalho.= " - "._("IN_EDITION_-1")."</h4>\n";
echo("    ".$cabecalho);
echo("    <br />\n");

$res=Portfolio::RetornaResHistoricoDoItem($sock, $cod_item);
$num_linhas=AcessoSQL::RetornaNumLinhas($res);

$linha=AcessoSQL::RetornaLinha($res);
$num_linhas--;
$nome_usuario=Usuarios::NomeUsuario($sock, $linha['cod_usuario'], $cod_curso);
$data=Data::UnixTime2DataHora($linha['data']);


echo("    <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
echo("      <tr>\n");
echo("        <td valign=\"top\">\n");
echo("          <ul class=\"btAuxTabs\">\n");

if ($linha['acao']=="E")
{
	/* 52 - Atualizar (ger) */
	echo("            <li><a href=\"em_edicao.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_item=".$cod_item."&amp;origem=".$origem."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">"._("UPDATE_-1")."</a></li>\n");

}
else
{
	echo("            <script language=\"javascript\">\n");
	echo("               opener.document.location='".$origem.".php?".Sessao::RetornaSessionID()."&amp;cod_curso=".$cod_curso."&amp;cod_topico=".$cod_topico_raiz."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_item=".$cod_item."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\n");
	echo("            </script>\n");
	echo("          </ul>\n");
	echo("        </td>\n");
	echo("      </tr>\n");
	echo("    </table>\n");
	echo("  </body>\n");
	echo("</html>\n");
}

/* 13 - Fechar (ger) */
echo("            <li><a href=\"self.close();\">"._("CLOSE_-1")."</a></li>\n");
echo("          </ul>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("      <tr>\n");
echo("        <td>\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("            <tr>\n");
/* 125 - Item */
echo("              <td  align=right><b>"._("ITEM_-1").":&nbsp;</b></td>\n");
echo("              <td>".$linha_item['titulo']."</td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
/* (ger) 53 - Situação */
echo("              <td  align=right><b>"._("SITUATION_-1").":&nbsp;</b></td>\n");

if ($linha['acao']=="E")
	/* (ger) 54 - Em Edição */
	echo("              <td>"._("IN_EDITION_-1")."</td>\n");
else
	/* (ger) 55 - Edição concluí­da */
	echo("              <td>"._("EDITION_CONCLUDED_-1")."</td>\n");

echo("            </tr>\n");
echo("            <tr>\n");
/* 56  - Desde */
echo("              <td align=right><b>"._("SINCE_-1").":&nbsp;</b></td>\n");
echo("              <td>".$data."</td>\n");
echo("            </tr>\n");

if ($linha['acao']=="E")
{
	echo("            <tr>\n");
	/* 57 - Por */
	echo("              <td align=right><b>"._("BY_-1").":&nbsp;</b></td>\n");
	echo("              <td>".$nome_usuario."</td>\n");
	echo("            </tr>\n");
}

echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("    </table>\n");
echo("  </body>\n");
echo("</html>\n");

?>
