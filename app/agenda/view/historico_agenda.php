<?php

$dir_static = '../../../static_includes/';
$dir_lib = '../../../lib/';
$ctrl_agenda = '../controller/';

include $dir_lib.'Sessao.php';
include $dir_lib.'Conexao.php';
include $ctrl_agenda.'HistoricoAgendaController.php';

//$cod_usuario=VerificaAutenticacao($cod_curso);
$cod_usuario = $_GET['cod_usuario'];
$cod_curso = $_GET['cod_curso'];
$cod_item = $_GET['cod_item'];

$controlerHistorico = new HistoricoAgendaController();

$cod_ferramenta = 1;
$sessionID = Sessao::RetornaSessionID();

include $dir_static.'topo_tela.php';

echo("    <script type=\"text/javascript\">\n");
echo("      var sessionID = ".$sessionID."\n");
echo("    </script>\n");

echo("	<script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");

echo("  </head>\n");
echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" onload=\"this.focus();\">\n");

$linha_item=$controlerHistorico->retornaHistoricoDoItem($cod_item);

/* Página Principal */

// 1 - Agenda
$cabecalho = ("<br /><br /><h4>Agenda");

/* 34 - Historico */
$cabecalho.= (" - Histórico</h4>\n");
echo($cabecalho);
echo ("<br />\n");

echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("      <tr>\n");
echo("        <td valign=\"top\" colspan=3>\n");
echo("          <ul class=\"btAuxTabs\">\n");
/* 13 - Fechar (ger) */
echo("            <li><span onclick=\"self.close();\">Fechar</span></li>\n");
echo("          </ul>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("      <tr>\n");
echo("        <td colspan=3>\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("            <tr>\n");
echo("              <td  align=right><b>Título:&nbsp;</b></td>\n");
echo("              <td colspan=2>".$linha_item[0]['titulo']."</td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("    </table>\n");
echo("    <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
echo("      <tr>\n");
echo("        <td>\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("            <tr>\n");
/* 35 - Ação */
echo("              <td><b>Acao</b></td>\n");
/* 7 - Data */
echo("              <td><b>Data</b></td>\n");
/* 36 - Usuário */
echo("              <td><b>Usuário</b></td>\n");
echo("            </tr>\n");

foreach ($linha_item as $cod=>$linha){
	echo("            <tr>\n");
	echo("              <td align=center>".$linha['nome']."</td>\n");
	echo("              <td align=center>".$linha['data']."</td>\n");
	echo("              <td align=center>".$linha['nome_usuario']."</td>\n");
	echo("            </tr>\n");
}









?>