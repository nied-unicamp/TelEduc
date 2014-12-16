<?php
$ferramenta_geral = 'geral';
$ferramenta_admin = 'admin';
$ferramenta_login = 'login';
$ferramenta_administracao = 'administracao';

$view_admin = '../../'.$ferramenta_admin.'/views/';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$ctrl_login = '../../'.$ferramenta_login.'/controllers/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_imgs  = "../../../web-content/imgs/";

require_once $model_geral.'geral.inc';
require_once $model_geral.'inicial.inc';

$pag_atual = "mostra_curso.php";

require_once $view_admin.'topo_tela_inicial.php';

echo("    <script type=\"text/javascript\">\n\n");

echo("      function Iniciar()\n");
echo("      {\n");
echo("        startList();\n");
echo("      }\n\n");

echo("    </script>\n\n");

require_once $view_admin.'menu_principal_tela_inicial.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

$dados_curso=Inicial::RetornaDadosMostraCurso($sock,$cod_curso);

$dados_email=Cursos::DadosCursoParaEmail($sock,$cod_curso);

AcessoSQL::Desconectar($sock);
$sock=AcessoSQL::Conectar($cod_curso);

echo("          <h4>".$dados_curso['nome_curso']."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <ul class=\"btAuxTabs\">\n");
/* 68 - Voltar para lista de cursos */
echo("                  <li><span onClick=\"document.location='".$view_administracao."cursos_all.php?tipo_curso=".$tipo_curso."';\">".Linguas::RetornaFraseDaLista($lista_frases,68)."</span></li>\n");
if ($dados_curso['acesso_visitante']=="A")
{
	/* 56 - Visitar */
	echo("                <li><span onClick=\"document.location='".$ctrl_login."index_curso.php?cod_curso=".$dados_curso['cod_curso']."&amp;visitante=sim';\">".Linguas::RetornaFraseDaLista($lista_frases,56)."</span></li>\n");
}
echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
echo("                    <td align=\"left\" colspan=\"2\">".$dados_curso['informacoes']."</td>\n");
echo("                  </tr>\n");

/* 60 - Publico Alvo: */
echo("                  <tr>\n");
echo("                    <td align=left colspan=\"2\"><b>".Linguas::RetornaFraseDaLista($lista_frases,60)."</b>".$dados_curso['publico_alvo']."</td>\n");
echo("                  </tr>\n");

/* 61 - Periodo do curso: */
/* 69 - de */
/* 70 - a */
echo("                  <tr>\n");
echo("                    <td align=left colspan=\"2\"><b>".Linguas::RetornaFraseDaLista($lista_frases,61)."</b> ".Linguas::RetornaFraseDaLista($lista_frases,69)." ".Data::UnixTime2Data($dados_curso['curso_inicio'])." ".Linguas::RetornaFraseDaLista($lista_frases,70)." ".Data::UnixTime2Data($dados_curso['curso_fim'])."</td>\n");
echo("                  </tr>\n");

/* 196 - Periodo de inscrição no curso: */
/* 69 - de */
/* 70 - a */
echo("                  <tr>\n");
echo("                    <td align=left colspan=\"2\"><b>".Linguas::RetornaFraseDaLista($lista_frases,196).": </b>".Linguas::RetornaFraseDaLista($lista_frases,69)." ".Data::UnixTime2Data($dados_curso['inscricao_inicio'])." ".Linguas::RetornaFraseDaLista($lista_frases,70)." ".Data::UnixTime2Data($dados_curso['inscricao_fim'])."</td>\n");
echo("                  </tr>\n");

/* 156 - Coordenador do curso: */
echo("                  <tr>\n");
echo("                    <td align=left colspan=\"2\"><b>".Linguas::RetornaFraseDaLista($lista_frases,156)."</b>".$dados_email['nome_coordenador']."</td>\n");
echo("                  </tr>\n");

/* 62 - E-mail para contato: */
echo("                  <tr>\n");
echo("                    <td align=left colspan=\"2\"><b>".Linguas::RetornaFraseDaLista($lista_frases,62)."</b><a href=mailto:".$dados_email['email'].">".$dados_email['email']."</a></td>\n");
echo("                  </tr>\n");

/* 63 - Tipo de inscricao: */
echo("                  <tr>\n");
echo("                    <td align=left colspan=\"2\"><b>".Linguas::RetornaFraseDaLista($lista_frases,63)."</b>".$dados_curso['tipo_inscricao']."</td>\n");
echo("                  </tr>\n");

$hoje=time();
$ontem=$hoje - 86400;

echo("                </table>\n");
echo("              </td>\n");
echo("            </tr>\n");

if ($dados_curso['inscricao_inicio'] <= $hoje  &&
$dados_curso['inscricao_fim']    >= $ontem &&
(
		// Se o aluno nao se inscreveu ou se inscreveu e foi rejeitado.
		!Inicial::ParticipaDoCurso($cod_curso) ||
		Inicial::RejeitadoDoCurso($cod_curso)
)
) {
	echo("            <tr>\n");
	echo("              <td align=right>\n");
	if (!Inicial::ParticipaDoCurso($cod_curso))
		/* 67 - Inscreva-se! */
		echo("                <input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,67)."\" onclick=\"document.location='inscricao.php?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso."';\" type=\"button\" />\n");
	if (Inicial::RejeitadoDoCurso($cod_curso))
		/* 235 - Inscrever-se novamente */
		echo("                <input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,235)."\" onclick=\"document.location='inscricao.php?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso."';\" type=\"button\" />\n");
	echo("              </td>\n");
	echo("            </tr>\n");
}

echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
require_once $view_admin.'rodape_tela_inicial.php';
echo("  </body>\n");
echo("</html>\n");
?>