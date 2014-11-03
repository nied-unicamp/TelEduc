<?php
$ferramenta_geral = 'geral';
$ferramenta_agenda = 'agenda';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$cod_curso = $_GET['cod_curso'];
$cod_item = $_GET['cod_item'];

$cod_usuario=AcessoPHP::VerificaAutenticacao($cod_curso);

$sock=AcessoSQL::Conectar("");

$diretorio_arquivos=Agenda::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Agenda::RetornaDiretorio($sock,'ArquivosWeb');

AcessoSQL::Desconectar($sock);

$cod_ferramenta = 1;

require_once $view_administracao.'topo_tela.php';

echo("    <script type=\"text/javascript\">\n");
echo("      function OpenWindowPerfil(funcao)\n");
echo("      {\n");
echo("         window.open(\"../perfil/exibir_perfis.php?".Sessao::RetornaSessionID()."&cod_curso=".$cod_curso."&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
echo("        return(false);\n");
echo("      }\n");
echo("    </script>\n");
echo("  </head>\n");
echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" onload=\"this.focus();\">\n");

$linha_item=Agenda::RetornaDadosDoItem($sock, $cod_item);

/* Página Principal */

// 1 - Agenda
$cabecalho = ("<br /><br /><h4>".Linguas::RetornaFraseDaLista ($lista_frases, 1));

/* 34 - Historico */
$cabecalho.= (" - ".Linguas::RetornaFraseDaLista($lista_frases,34)."</h4>\n");
echo($cabecalho);
echo ("<br />\n");

echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("      <tr>\n");
echo("        <td valign=\"top\" colspan=3>\n");
echo("          <ul class=\"btAuxTabs\">\n");
/* 13 - Fechar (ger) */
echo("            <li><span onclick=\"self.close();\">".Linguas::RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
echo("          </ul>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("      <tr>\n");
echo("        <td colspan=3>\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("            <tr>\n");
echo("              <td  align=right><b>".Linguas::RetornaFraseDaLista($lista_frases,18).":&nbsp;</b></td>\n");
echo("              <td colspan=2>".$linha_item['titulo']."</td>\n");
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
echo("              <td><b>".Linguas::RetornaFraseDaLista($lista_frases,35)."</b></td>\n");
/* 7 - Data */
echo("              <td><b>".Linguas::RetornaFraseDaLista($lista_frases,7)."</b></td>\n");
/* 36 - Usuário */
echo("              <td><b>".Linguas::RetornaFraseDaLista($lista_frases,36)."</b></td>\n");
echo("            </tr>\n");

$res=Agenda::RetornaResHistoricoDoItem($sock, $cod_item);
$num_linhas=AcessoSQL::RetornaNumLinhas($res);

while ($num_linhas>0)
{
	$linha=AcessoSQL::RetornaLinha($res);
	$num_linhas--;
	$nome_usuario="<span class=\"link\" onclick=\"OpenWindowPerfil(".$linha['cod_usuario'].");\">".Usuarios::NomeUsuario($sock, $linha['cod_usuario'], $cod_curso)."</span>";
	$data=Data::UnixTime2DataHora($linha['data']);

	switch ($linha['acao']){

		/* 40 - Criacao */
		case ('C'): $acao=Linguas::RetornaFraseDaLista($lista_frases,40); break;
		/* 39 - Edicao Cancelada */
		case ('D'): $acao=Linguas::RetornaFraseDaLista($lista_frases,39); break;
		/* 43 - Em Edicao */
		case ('E'): $acao=Linguas::RetornaFraseDaLista($lista_frases,43); break;
		/* 37 - Edicao Finalizada */
		case ('F'): $acao=Linguas::RetornaFraseDaLista($lista_frases,37); break;
		/* 42 - Movida para histórico */
		case ('H'): $acao=Linguas::RetornaFraseDaLista($lista_frases,42); break;
		/* 41 - Ativada */
		case ('A'): $acao=Linguas::RetornaFraseDaLista($lista_frases,41); break;
		/* 38 - Desconhecida */
		default: $acao=Linguas::RetornaFraseDaLista($lista_frases,38); break;
	}

	echo("            <tr>\n");
	echo("              <td align=center>".$acao."</td>\n");
	echo("              <td align=center>".$data."</td>\n");
	echo("              <td align=center>".$nome_usuario."</td>\n");
	echo("            </tr>\n");

}

echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
echo("    </table>\n");
echo("  </body>\n");
echo("</html>\n");
AcessoSQL::Desconectar($sock);

?>


