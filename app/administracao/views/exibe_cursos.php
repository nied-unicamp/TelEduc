<?php 

$ferramenta_admin = 'admin';
$ferramenta_geral = 'geral';
$ferramenta_administracao = 'administracao';
$ferramenta_login = 'login';

$view_admin = '../../'.$ferramenta_admin.'/views/';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_administracao = '../../'.$ferramenta_administracao.'/models/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$view_login = '../../'.$ferramenta_login.'/views/';
$ctler_login = '../../'.$ferramenta_login.'/controllers/';



require_once $model_geral.'geral.inc';
require_once $model_geral.'inicial.inc';
require_once $model_administracao.'exibe_cursos.inc';

$sock=AcessoSQL::Conectar("");
/* Caso o usuÔøΩrio nÔøΩo esteja logado, manda para tela de login. */
if (empty ($_SESSION['login_usuario_s']))
{
	AcessoSQL::Desconectar($sock);
	require_once $view_login.'autenticaÁ„o_cadastro.php';
	exit;
}
/* Caso o usuÔøΩrio n√£o tenha preenchido seus dados pessoais, manda para tela de preenchimento. */
else if(!Usuarios::PreencheuDadosPessoais($sock))
{
	AcessoSQL::Desconectar($sock);
	require_once $view_administracao.'preencher_dados.php?acao=preencherDados&atualizacao=true';
	exit;
}
/* Caso o usu√°rio seja o adm, manda para tela dos cursos em andamento. */
else if($_SESSION['cod_usuario_global_s'] == -1)
{
	Desconectar($sock);
	require_once $view_administracaos.'cursos_all.php';/*?tipo_curso=A*/
	exit;
}

require_once $view_admin.'topo_tela_inicial.php';

$lista_frases_autenticaÁ„o = Linguas::RetornaListaDeFrases($sock, 25);

echo("    <script type=\"text/javascript\">\n\n");

echo("      function Iniciar()\n");
echo("      {\n");
echo("        startList();\n");
//$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("      }\n\n");

echo("      function TestaNome(form){\n");
/* Elimina os espa√ßos para verificar se o titulo nao È formado por apenas espa√ßos */
echo("        Campo_login = form.login.value;;\n");
echo("        Campo_senha = form.senha.value;\n");
echo("        while (Campo_login.search(\" \") != -1){\n");
echo("          Campo_login = Campo_login.replace(/ /, \"\");\n");
echo("        }\n");
echo("        if (Campo_login == ''){\n");
/* 4 - Por favor preencha o campo 'Login'. */
echo("          alert('".Linguas::RetornaFraseDaLista($lista_frases_autenticaÁ„o, 4)."');\n");
echo("          document.formAutentica.login.focus();\n");
echo("          return(false);\n");
echo("        } else {\n");
echo("          while (Campo_senha.search(\" \") != -1){\n");
echo("            Campo_senha = Campo_senha.replace(/ /, \"\");\n");
echo("          }\n");
echo("          if (Campo_senha == ''){\n");
/* 5 - Por favor preencha o campo \"Senha\". */
echo("            alert('".Linguas::RetornaFraseDaLista($lista_frases_autenticaÁ„o, 5)."');\n");
echo("          document.formAutentica.senha.focus();\n");
echo("            return(false);\n");
echo("          }\n");
echo("        }\n");
echo("        return(true);\n");
echo("      }\n\n");

echo("    </script>\n\n");

require_once $view_admin.'menu_principal_tela_inicial.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/*162 - Meus Cursos  */
echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,162)."</h4>\n");

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
echo("              <td colspan=4>\n");

echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
/*192 - N√£o iniciados (rec√©m-criados no servidor)*/
echo("                    <td colspan=\"2\">".Linguas::RetornaFraseDaLista($lista_frases,192)."</td>");
echo("                  </tr>\n");
echo("                  <tr class=\"head01\">\n");
/* 5 - Curso */
echo("                    <td class=\"alLeft\">".Linguas::RetornaFraseDaLista($lista_frases,5)."</td>\n");
/* 163 - Tipo usuario */
echo("                    <td width=\"15%\">".Linguas::RetornaFraseDaLista($lista_frases,163)."</td>\n");
echo("                  </tr>\n");

/*Exibe cursos rec√©m aceitos ou cursos que ainda n√£o come√ßaram*/

list ($lista_cursos, $total_cursos) = ExibeCursos::RetornaCursosNaoIniciados($sock, $_SESSION['codigo_usuario_s']);

if (($total_cursos)==0)
{
	echo("                  <tr>\n");
	/* 164 - Voce nao esta cadastrado em nenhum curso */
	echo("                    <td colspan=\"2\">".Linguas::RetornaFraseDaLista($lista_frases,164)."</td>\n");
	echo("                  </tr>\n");
}
else
{
	$num = 0;
	$cor = 0;

	while ($num < $total_cursos)
	{
		$cor++;
		$cor%=2;

		echo("                  <tr>\n");
		echo("                    <td class=\"alLeft\">\n");
		echo("                      <a href=\"".$diretorio_ctrlers."index_curso.php?cod_curso=".$lista_cursos[$num]['cod_curso']."\">".$lista_cursos[$num]['nome_curso']."</a>");
		echo("                    </td>\n");
		echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");

		switch ($lista_cursos[$num]['tipo_usuario'])
		{
			//58 - Formador (geral) // 178 - Usu√°rio
			case "F": echo "".Linguas::RetornaFraseDaLista($lista_frases_geral,58).""; break;
			default: echo "".Linguas::RetornaFraseDaLista($lista_frases,178)."";
		}

		echo("                    </td>\n");
		echo("                  </tr>\n");

		/* Incrementa o contador. */
		$num++;
	}
}

echo("                  <tr class=\"head\">\n");
echo("                    <td colspan=\"2\">".Linguas::RetornaFraseDaLista($lista_frases,171)."</td>");
echo("                  </tr>\n");
echo("                  <tr class=\"head01\">\n");
/* 5 - Curso */
echo("                    <td class=\"alLeft\">".Linguas::RetornaFraseDaLista($lista_frases,5)."</td>\n");
/* 163 - Tipo usuario */
echo("                    <td width=\"15%\">".Linguas::RetornaFraseDaLista($lista_frases,163)."</td>\n");
echo("                  </tr>\n");

/*Exibe cursos em andamento*/

list ($lista_cursos, $total_cursos) = ExibeCursos::RetornaCursosEmAndamento($sock, $_SESSION['codigo_usuario_s']);

if (($total_cursos)==0)
{
	echo("                  <tr>\n");
	/* 164 - Voce nao esta cadastrado em nenhum curso */
	echo("                    <td colspan=\"2\">".Linguas::RetornaFraseDaLista($lista_frases,164)."</td>\n");
	echo("                  </tr>\n");
}
else
{
	$num = 0;
	$cor = 0;

	while ($num < $total_cursos)
	{
		$cor++;
		$cor%=2;

		echo("                  <tr>\n");
		echo("                    <td class=\"alLeft\">\n");
		echo("                      <a href=\"".$diretorio_ctrlers."index_curso.php?cod_curso=".$lista_cursos[$num]['cod_curso']."\">".$lista_cursos[$num]['nome_curso']."</a>");
		echo("                    </td>\n");
		echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");

		switch ($lista_cursos[$num]['tipo_usuario'])
		{
			//58 - Formador (geral) // 178 - Usu√°rio
			case "F": echo "".Linguas::RetornaFraseDaLista($lista_frases_geral,58).""; break;
			default: echo "".Linguas::RetornaFraseDaLista($lista_frases,178)."";
		}

		echo("                    </td>\n");
		echo("                  </tr>\n");

		/* Incrementa o contador. */
		$num++;
	}
}

echo("                  <tr class=\"head\">\n");
echo("                    <td colspan=\"2\">".Linguas::RetornaFraseDaLista($lista_frases,173)."</td>");
echo("                  </tr>\n");
echo("                  <tr class=\"head01\">\n");
/* 5 - Curso */
echo("                    <td class=\"alLeft\">".Linguas::RetornaFraseDaLista($lista_frases,5)."</td>\n");
/* 163 */
echo("                    <td width=\"15%\">".Linguas::RetornaFraseDaLista($lista_frases,163)."</td>\n");
echo("                  </tr>\n");

/*Exibe cursos jah oferecidos*/

list ($lista_cursos, $total_cursos) = ExibeCursos::RetornaCursosPassados($sock, $_SESSION['codigo_usuario_s']);


if (($total_cursos)==0)
{
	echo("                  <tr>\n");
	/* 164 - Voce nao esta cadastrado em nenhum curso */
	echo("                    <td colspan=\"2\">".Linguas::RetornaFraseDaLista($lista_frases,164)."</td>\n");
	echo("                  </tr>\n");
}
else
{
	$num = 0;
	$cor = 0;

	while ($num < $total_cursos)
	{
		$cor++;
		$cor%=2;

		echo("                  <tr>\n");
		echo("                    <td class=\"alLeft\">\n");
		echo("                      <a href=\"".$ctler_login."index_curso.php?cod_curso=".$lista_cursos[$num]['cod_curso']."\">".$lista_cursos[$num]['nome_curso']."</a>");
		echo("                    </td>\n");
		echo("                    <td align=\"center\" valign=\"top\" class=\"botao2\">\n");

		switch ($lista_cursos[$num]['tipo_usuario'])
		{
			//58 - Formador (geral) // 178 - Usu√°rio
			case "F": echo "".Linguas::RetornaFraseDaLista($lista_frases_geral,58).""; break;
			default: echo "".Linguas::RetornaFraseDaLista($lista_frases,178)."";
		}

		echo("                    </td>\n");
		echo("                  </tr>\n");

		/* Incrementa o contador. */
		$num++;
	}
}

echo("                </table>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
require_once $view_admin.'rodape_tela_inicial.php';
AcessoSQL::Desconectar($sock);
echo("  </body>\n");
echo("</html>");
?>