<?php
/**
 * acoes_administacao.php
 *
 * View index_adm
 */
$ferramenta_geral = 'geral';
$ferramenta_admin = 'admin';

$diretorio_jscss = "../../../web-content/js-css/";
$diretorio_imgs  = "../../../web-content/imgs/";
$view_admin = '../../'.$ferramenta_admin.'/views/';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_admin = '../../'.$ferramenta_admin.'/models/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';

require_once $model_geral.'geral.inc';
require_once $model_admin.'admin.inc';

require_once $view_admin.'topo_tela_inicial.php';

AcessoPHP::VerificaAutenticacaoAdministracao();

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject($lista_frases);
//adicionar as acoes possiveis, 1o parametro
/* 198 - Administrador logado com sucesso */
$feedbackObject->addAction("logar", _("ADM_LOGIN_SUCCESS_-3"), 0);

/* Inicio do JavaScript */
echo("    <script language=\"javascript\"  type=\"text/javascript\">\n");

echo("      function Iniciar() {\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n");

echo("    </script>\n");
/* Fim do JavaScript */

require_once $diretorio_views.'menu_principal_tela_inicial.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/* 1 - AdministraÃ§Ã£o */
echo("          <h4>"._("ADMINISTRATION_-5")."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");

echo("<!-- Tabelao -->\n");
echo("<table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("<tr>\n");

echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

/* Realizando checagem de novo Patch */

$lista=Arquivos::RetornaArrayDiretorio("patch");

if (count($lista)>0)
{
	unset($patchs);
	// Existem Patchs no Diretorio
	foreach($lista as $cod =>$linha)
	{
		$query="select * from Patchs where patch='".$linha['Arquivo']."'";
		$res=AcessoSQL::Enviar($sock,$query);
		if (AcessoSQL::eRetornaNumLinhas($res)==0)
			$patchs[$cod]=$linha['Arquivo'];

	}
	 

	if (count($patchs)>0)
	{
		foreach($patchs as $cod => $nome)
		{
			echo("<b>".$nome."</b><br /><br />");

			include("patch/".$nome);

			$query="insert into Patchs (patch) values ('".$nome."')";
			AcessoSQL::Enviar($sock,$query);
		}

		/* 135 - Patch atualizado com sucesso! */
		echo("<b>"._("PATCH_UPDATE_SUCCESS_-5")."</b><br><br>");

		// 18 - OK
		echo("<form><input type=\"button\" value='"._("OK_-1")."' onclick='document.location=\"index.php?\";'></form>");


		echo("</body>\n");
		echo("</html>\n");
		exit();
	}
}

/* Fim da Checagem de novo Patch */


/* X - Dados de Curso */                        /* Y - Categorias */
echo("<tr class=\"head\">\n");
echo("<td>Dados do Curso</td>\n");
echo("<td>Categorias</td>\n");
echo("</tr>\n");
echo("<tr><td>\n");
echo("<ul>\n");

/* 3 - Criação de Curso */
Admin::PreparaBoldLink(_("CREATION_COURSE_-5"),"\"".$view_admin."criar_curso.php\"","");

/* 4 - Extração de Curso */
Admin::PreparaBoldLink(_("REMOTION_COURSE_-5"),"\"../extracao/extrair_curso.php\"","");

/* 141 - Inserção de Cursos Extraídos */
Admin::PreparaBoldLink(_("INSERTION_REMOVED_COURSE_-5"),"\"inserir_curso.php\"","");

/* 245 - Reutilização de Cursos Encerrados */
Admin::PreparaBoldLink(_("REUTILIZATION_COURSE_-5"),"\"resetar_curso.php\"","");

echo("</ul>\n");
echo("</td>\n");

echo("<td>\n");
echo("<ul>\n");

/* 125 - Editar Categorias */
Admin::PreparaBoldLink(_("EDIT_CATEGORIES_-5"),"\"editar_categoria.php\"","");

/* 131 - Selecionar Categoria dos Cursos */
Admin::PreparaBoldLink(_("SELECT_COURSE_CATEGORY_-5"),"\"selecionar_categoria.php\"","");

echo("</ul>\n");
echo("</td></tr>\n");

/* 
 * 535 - Reenvio
 * 536 - Configurar
*/
echo("<tr class=\"head\"><td>"._("RESEND_-5")."</td><td>"._("CONFIGURATE_-5")."</td></tr>\n");

echo("<tr><td>\n");
echo("<ul>\n");

/* 293 - Reenvio de dados aos coordenadores*/
Admin::PreparaBoldLink(_("RESEND_COORD_DATA_-5"),"\"../infocurso/reenvio.php\"","");

/* 8 - Trocar login */
Admin::PreparaBoldLink(_("CHANGE_LOGIN_-5"),"\"trocar_login.php\"","");

/* 9 - Enviar e-mail para usuários */
Admin::PreparaBoldLink(_("SEND_EMAIL_USERS_-5"),"\"enviar_email.php\"","");

/* 5 - Consulta a Base de Dados */
Admin::PreparaBoldLink(_("CONSULT_DATABASE_-5"),"\"consultar_base.php\"","");

/* 13 - Contato - NIED - Unicamp */
Admin::PreparaBoldLink(_("CONTACT_NIED_UNICAMP_-5"),"\"mailto:equipe.teleduc@gmail.com\"","");

echo("</ul>\n");
echo("</td>\n");

echo("<td>\n");
echo("<ul>\n");

/* 153 - Estatísticas do Ambiente */
Admin::PreparaBoldLink(_("ENVIRONMENT_STATISTICS_-5"),"\"../estatistica/num_cursos.php\"","");

/* 183 - Configurar dados do ambiente */
Admin::PreparaBoldLink(_("CONFIGURE_ENVIRONMENT_DATA_-5"), "\"selecionar_lingua.php\"", "");

/* 11 - Cadastro de Línguas */
Admin::PreparaBoldLink(_("LANGUAGE_REGISTER_-5"),"\"cadastro_linguas.php\"","");

/* 171 - Cadastro de texto da Ajuda */
Admin::PreparaBoldLink(_("HELP_TEXT_REGISTER_-5"),"\"../ajuda/index.php\"","");

echo("</ul>\n");
echo("</td></tr></table>\n");

/* 12 - Voltar a página inicial */
echo("<div align=\"right\">\n");
echo("  <input class=\"input\" value=\""._("BACK_HOME_PAGE_-5")."\" onClick=\"document.location='../index.php?'\" type=\"button\"/>\n");
echo("</div>\n");


echo("</td></tr></table>\n");
echo("</td></tr>\n");

require_once $view_admin.'rodape_tela_inicial.php';
echo("</body>\n");
echo("</html>\n");
AcessoSQL::Desconectar($sock);
?>