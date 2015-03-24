<?php
$ferramenta_admin = 'admin';
$ferramenta_geral = 'geral';
$ferramenta_administracao = 'administracao';
$ferramenta_login = 'login';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_administracao = '../../'.$ferramenta_administracao.'/models/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_administracao.'administracao.inc';

$cod_ferramenta=0;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda = 1;

$cod_curso = $_GET['cod_curso'];
$cod_usuario = $_GET['cod_usuario'];

require_once $view_administracao.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject();

//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
// 255 - Erro na operacao
// 137 - Senha(s) enviada(s) por email.
$feedbackObject->addAction("enviarSenha", _("SENT_PASSWORD_EMAIL_0"), _("ERROR_IN_OPERATION_0"));
//273 - Ferramentas do curso escolhidas com sucesso.
$feedbackObject->addAction("escolherFerramentas", _("TOOL_CHOSEN_SUCCESS_0"), _("ERROR_IN_OPERATION_0"));
//274 - Ferramentas compartilhadas com sucesso.
$feedbackObject->addAction("compartilharFerramentas", _("TOOL_SHARED_SUCCESS_0"), _("ERROR_IN_OPERATION_0"));
//277 - Ferramentas destacadas com sucesso. As ferramentas destacadas aparecem em vermelho.
$feedbackObject->addAction("marcarFerramentas", _("TOOL_HIGHLIGHTED_SUCCESS_0"), _("ERROR_IN_OPERATION_0"));
//27 -Dados alterados com sucesso.
$feedbackObject->addAction("alterarDadosCurso", _("DATA_UPDATE_SUCCESS_0"), _("ERROR_IN_OPERATION_0"));
//276 -Cronograma alterado com sucesso.
$feedbackObject->addAction("alterarCronograma", _("SCHEDULE_UPDATE_SUCCESS_0"), _("ERROR_IN_OPERATION_0"));

echo("        <script type=\"text/javascript\">\n");
echo("          function Iniciar()\n");
echo("          {\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("            startList();\n");
echo("          }\n\n");
echo("        </script>\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
// Pagina Principal
/* 1 - Administracao */
$cabecalho = ("          <h4>"._("ADMINISTRATION_-5")."</h4>\n");
echo($cabecalho);

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");

echo("         <div id=\"mudarFonte\">\n");
echo("           <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("           <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("           <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaInterna\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/* 231 - Dados do Curso*/
echo("                    <td width=\"50%\" align=\"center\"><b>"._("COURSE_DATA_-5")."</b></td>\n");
/* 232 - Ferramentas */
echo("                    <td width=\"50%\" align=\"center\"><b>"._("TOOLS_0")."</b></td>\n");
echo("                  </tr>\n");
echo("                  <tr>\n");
echo("                    <td align=\"left\">\n");
echo("                      <a href=\"alterar_dados_curso.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");

if ($ecoordenador = Usuarios::ECoordenadorMesmo($sock, $cod_curso, $cod_usuario))
{
	/* 2 - Visualizar / Alterar Dados do Curso */
	echo(_("VIEW_CHANGE_COURSE_DATA_0")."</a><br />\n");
}
else
{
	/* 49 - Visualizar Dados do Curso */
	echo(_("VIEW_COURSE_DATA_0")."</a><br />\n");
}

$bold_tag = array(array("", ""), array("<b>", "</b>"));

$ferr_alt = Administracao::HouveAlteracoes($sock,$cod_curso,$cod_usuario);

echo("                      <a href=\"alterar_cronograma.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");

/* 31 - Visualizar / Alterar Cronograma do Curso */
echo($bold_tag[$ferr_alt[0]][0]._("VIEW_CHANGE_COURSE_SCHEDULE_0").$bold_tag[$ferr_alt[0]][1]."</a><br />\n");
echo("                    </td>\n");
echo("                    <td align=\"left\">\n");
echo("                      <a href=\"escolher_ferramentas.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
/* 40 - Escolher Ferramentas do Curso */
echo($bold_tag[$ferr_alt[1]][0]._("CHOOSE_COURSE_TOOLS_0").$bold_tag[$ferr_alt[1]][1]."</a><br />\n");
echo("                      <a href=\"compartilhar_ferramentas.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");
/* 202 - Compartilhar Ferramentas */
echo($bold_tag[$ferr_alt[2]][0]._("SHARE_COURSE_TOOLS_0").$bold_tag[$ferr_alt[2]][1]."</a><br />\n");
echo("                      <a href=\"marcar_ferramentas.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");
/* 141 - Marcar Ferramentas */
echo($bold_tag[$ferr_alt[3]][0]._("HIGHLIGHT_TOOLS_0").$bold_tag[$ferr_alt[3]][1]."</a><br />\n");
echo("                    </td>\n");
echo("                  </tr>\n");
echo("                  <tr class=\"head\">\n");
/* 233 - Inscricao*/
echo("                    <td align=\"center\"><b>"._("ENROLLMENT_-3")."</b></td>\n");
/* 234 - Gerenciamento */
echo("                    <td align=\"center\"><b>"._("MANAGEMENT_0")."</b></td>\n");
echo("                  </tr>\n");
echo("                  <tr>\n");
echo("                    <td align=\"left\">\n");
if ($ecoordenador)
{
	echo("                      <a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");

	/* 50 - Inscrever Formadores */
	echo(_("ENROLL_INSTRUCTORS_0")."</a><br />\n");
}

echo("                      <a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=A\">");

/* 51 - Inscrever Alunos */
echo(_("ENROLL_STUDENTS_0")."</a><br />\n");

// 164 - Inscrever Colaboradores
echo("                      <a href=\"inscrever.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=Z\">"._("ENROLL_COLLABORATORS_0")."</a><br />\n");
// aqui, a variavel origem indica que a proxima pagina veio de administracao.php

// 182 - Inscrever visitantes
echo("    <a href=\"inscrever.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=0&tipo_usuario=V\">"._("ENROLL_VISITORS_0")."</a><br /><br />\n");

echo("                    </td>\n");
echo("                    <td align=\"left\">\n");

echo("                      <a href=\"gerenciamento_inscricoes.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=i\">");
/* 74 - Gerenciamento de Inscricoes */
echo($bold_tag[$ferr_alt[3]][0]._("ENROLLMENT_MANAGEMENT_0").$bold_tag[$ferr_alt[3]][1]."</a><br />\n");

echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=A\">");
/* 102 - Gerenciamento de Alunos */
echo(_("STUDENT_MANAGEMENT_0"));
echo("</a><br />\n");

echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=F\">");
// 103 - Gerenciamento de Formadores
echo(_("INSTRUCTOR_MANAGEMENT_0"));
echo("</a><br />\n");

// 165 - Gerenciamento de Colaboradores
echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=Z\">");
echo(_("COLLABORATOR_MANAGEMENT_0"));
echo("</a><br />\n");

// 179 - Gerenciamento de Visitantes
echo("                      <a href=\"gerenciamento_usuarios.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."&amp;tipo_usuario=V\">");
echo(_("VISITOR_MANAGEMENT_0"));
echo("</a><br />\n");

echo("                    </td>\n");
echo("                  </tr>\n");
echo("                  <tr class=\"head\">\n");
/* 235 - Opcoes*/
echo("                    <td align=\"center\"><b>"._("OPTIONS_0")."</b></td>\n");
/* 236 - Extracao*/
echo("                    <td align=\"center\"><b>"._("EXTRACTION_0")."</b></td>\n");
echo("                  </tr>\n");
echo("                  <tr>\n");
echo("                    <td align=\"left\">\n");
if ($ecoordenador)
{
	echo("                      <a href=\"alterar_nomenclatura.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
	/* 149 - Alterar nomenclatura do coordenador */
	echo(_("CHANGE_COORD_NOMENCLATURE_0"));
	echo("</a><br />\n");
}

echo("                      <a href=\"enviar_senha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
/* 133 - Enviar Senha */
echo(_("SEND_PASSWORD_0")."</a><br />\n");
echo("                    </td>\n");
echo("                    <td align=\"left\">\n");

AcessoSQL::Desconectar($sock);
$sock = AcessoSQL::Conectar("");

$extrator = 'nao';

$extrator = Administracao::RetornaExtrator($sock);

if ($extrator == 'sim') {
	if ($ecoordenador) {
		echo("                      <a href=\"extracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
		/* 212 - Agendar Extracao do Curso */
		echo(_("SCHEDULE_COURSE_EXTRACTION_0")."</a><br />\n");

		echo("                      <a href=\"remover_extracao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_ferramenta=".$cod_ferramenta."\">");
		/* 213 - Listar / Remover Extracao do Curso */
		echo(_("LIST_REMOVE_COURSE_EXTRACTION_0")."</a><br />\n");
	}
	else
		/*237 - Secao permitada somente a coordenadores*/
		echo("                      "._("SECTION_ALLOWED_COORD_0")."\n");
}
else
	/*238 - Secao atualmente inexistente*/
	echo("                      "._("SECTION_NONEXISTENT_0")."\n");

echo("                    </td>\n");
echo("                  </tr>\n");
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