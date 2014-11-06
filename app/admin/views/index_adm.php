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
//$feedbackObject =  new FeedbackObject($lista_frases);
//adicionar as acoes possiveis, 1o parametro
//$feedbackObject->addAction("logar", 198, 0);

/* Inicio do JavaScript */
echo("    <script language=\"javascript\"  type=\"text/javascript\">\n");

echo("      function Iniciar() {\n");
//$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n");

echo("    </script>\n");
/* Fim do JavaScript */

require_once $diretorio_views.'menu_principal_tela_inicial.php';

$lista_frases=Linguas::RetornaListaDeFrases($sock,-5);

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/* 1 - Administração */
echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,1)."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

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
		echo("<b>".Linguas::RetornaFraseDaLista($lista_frases,135)."</b><br><br>");

		// 18 - OK
		echo("<form><input type=\"button\" value='".Linguas::RetornaFraseDaLista($lista_frases_geral,18)."' onclick='document.location=\"index.php?\";'></form>");


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

/* 3 - Cria��o de Curso */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,3),"\"".$view_admin."criar_curso.php\"","");

/* 4 - Extra��o de Curso */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,4),"\"../extracao/extrair_curso.php\"","");

/* 141 - Inser��o de Cursos Extra�dos */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,141),"\"inserir_curso.php\"","");

/* 245 - Reutiliza��o de Cursos Encerrados */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,245),"\"resetar_curso.php\"","");

echo("</ul>\n");
echo("</td>\n");

echo("<td>\n");
echo("<ul>\n");

/* 125 - Editar Categorias */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,125),"\"editar_categoria.php\"","");

/* 131 - Selecionar Categoria dos Cursos */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,131),"\"selecionar_categoria.php\"","");

echo("</ul>\n");
echo("</td></tr>\n");

echo("<tr class=\"head\"><td>Reenvio</td><td>Configurar</td></tr>\n");

echo("<tr><td>\n");
echo("<ul>\n");

/* 293 - Reenvio de dados aos coordenadores*/
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,293),"\"../infocurso/reenvio.php\"","");

/* 8 - Trocar login */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,8),"\"trocar_login.php\"","");

/* 9 - Enviar e-mail para usu�rios */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,9),"\"enviar_email.php\"","");

/* 5 - Consulta a Base de Dados */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,5),"\"consultar_base.php\"","");

/* 13 - Contato - NIED - Unicamp */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,13),"\"mailto:equipe.teleduc@gmail.com\"","");

echo("</ul>\n");
echo("</td>\n");

echo("<td>\n");
echo("<ul>\n");

/* 153 - Estat�sticas do Ambiente */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,153),"\"../estatistica/num_cursos.php\"","");

/* 183 - Configurar dados do ambiente */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,183), "\"selecionar_lingua.php\"", "");

/* 11 - Cadastro de L�nguas */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,11),"\"cadastro_linguas.php\"","");

/* 171 - Cadastro de texto da Ajuda */
Admin::PreparaBoldLink(Linguas::RetornaFraseDaLista($lista_frases,171),"\"../ajuda/index.php\"","");

echo("</ul>\n");
echo("</td></tr></table>\n");

/* 12 - Voltar a p�gina inicial */
echo("<div align=\"right\">\n");
echo("  <input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,12)."\" onClick=\"document.location='../pagina_inicial/index.php?'\" type=\"button\"/>\n");
echo("</div>\n");


echo("</td></tr></table>\n");
echo("</td></tr>\n");

require_once $view_admin.'rodape_tela_inicial.php';
echo("</body>\n");
echo("</html>\n");
AcessoSQL::Desconectar($sock);
?>