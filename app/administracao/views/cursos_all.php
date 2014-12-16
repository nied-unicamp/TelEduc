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

require_once $view_admin.'topo_tela_inicial.php';

$tipo_curso = $_GET['tipo_curso'];
$cod_pasta = $_GET['cod_pasta'];
$lista_frases_adm = Linguas::RetornaListaDeFrases($sock,-5);

echo("    <script type=\"text/javascript\">\n\n");

echo("      function Iniciar()\n");
echo("      {\n");
echo("        startList();\n");
echo("      }\n\n");

echo("    </script>\n");

require_once $view_admin.'menu_principal_tela_inicial.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

/* Verificar qual o nome a ser exibido...*/
if ($tipo_curso=="N")
	/* 194 - Cursos nao iniciados */
	echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,194)."</h4>\n");
else if ($tipo_curso=="A")
	/* 6 - Cursos em Andamento */
	echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,6)."</h4>\n");
else if ($tipo_curso=="I")
	/* 7 - Cursos com inscricees abertas */
	echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,7)."</h4>\n");
else
	/* 8 - Cursos encerrados */
	echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,8)."</h4>\n");
/*else	/* else if($tipo_curso=="T") */
/* 199 - Todos os cursos */
/*echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,199)."</h4>\n");*/

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

$lista_pastas=Inicial::RetornaPastasDeCursos($sock,$tipo_curso);

if (count($lista_pastas)<2 || isset($cod_pasta))
{
	echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
	echo("            <tr>\n");
	echo("              <td valign=\"top\">\n");
	echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

	if (count($lista_pastas) == 0 || $lista_pastas == "")
	{
		$class = "head";
		$cod_pasta="";
	}
	else
	{
		$class = "head01";
		foreach($lista_pastas as $cod => $linha)
		{
			if(count($lista_pastas) == 1)
			{
				$cod_pasta = $linha['cod_pasta'];
				$nome_pasta=$linha['pasta'];
			}
			else if ($linha['cod_pasta'] == $cod_pasta)
			{
				$nome_pasta=$linha['pasta'];
			}
		}

		//169 - Categoria (adm)
		echo("                  <tr class=\"head\">\n");
		echo("                    <td colspan=\"3\"><b>".Linguas::RetornaFraseDaLista($lista_frases_adm,169).": ".$nome_pasta."</b></td>\n");
		echo("                  </tr>\n");
	}

	echo("                  <tr class=\"".$class."\">\n");
	//265 - Nome do Curso (adm)
	echo("                    <td align=\"left\" width=80%>".Linguas::RetornaFraseDaLista($lista_frases_adm,265)."</td>\n");
	//						    if(empty($_SESSION['login_usuario_s']))      /*caso o usuario nao esteja logado*/
	echo("                    	<td colspan=2 width=\"10%\">&nbsp;</td>\n");
	//							else
	//							{
	//echo("                    	<td width=\"10%\">&nbsp;</td>\n");
		//echo("                    	<td width=\"10%\">&nbsp;</td>\n");
		//							}
		echo("                  </tr>\n");

		$lista=Inicial::RetornaListaDeCursos($sock,$tipo_curso,$cod_pasta);

		if (count($lista)>0 && $lista != "")
		{
			$hoje=time();
			$ontem=$hoje - 86400;

			foreach($lista as $cod => $linha)
			{
				$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $_SESSION['cod_usuario_global_s'], $linha['cod_curso']);
				AcessoSQL::Desconectar($sock);
				$tem_acesso_curso = Inicial::ParticipaDoCurso($linha['cod_curso']);
				$rejeitado_curso = Inicial::RejeitadoDoCurso($linha['cod_curso']);
				$sock=AcessoSQL::Conectar("");

				echo("                  <tr>\n");
				echo("                    <td align=\"left\">".$linha['nome_curso']."</td>\n");
				if ($linha['acesso_visitante']=="A")
				{
					/* 56 - Visitar */
					echo("                    <td><input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,56)."\" onClick=\"document.location='".$ctrl_login."index_curso.php?cod_curso=".$linha['cod_curso']."&amp;visitante=sim';\" type=\"button\" /></td>\n");
				}

				if(!empty($_SESSION['cod_usuario_global_s'])){
					echo("                    <td id=\"status_cel\"> ".Inicial::StatusUsuarioNoCurso($sock, $linha['cod_curso'])." </td>");
				}

				if($tem_acesso_curso) {
					if($rejeitado_curso) {
						/* 223 - Inscrição não aceita */
						echo("                    <td>\n");
						echo("                      ".Linguas::RetornaFraseDaLista($lista_frases,223)."\n");
						/* 235 - Inscrever-se novamente */
						echo("                      <input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,235)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" />\n");
						echo("                    </td>\n");
					}else{
						/* 55 - Entrar */
						echo("                    <td><input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,55)."\" onClick=\"document.location='".$ctrl_login."index_curso.php?cod_curso=".$linha['cod_curso']."';\" type=\"button\" /></td>\n");
					}
				}
				else
				{
					if($tipo_curso == "I")
					{
						/* 54 - Inscricoes */
						echo("                    <td><input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,54)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
					}
					else
					{
						/* 53 - Informacoes */
						echo("                    <td><input class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases,53)."\" onClick=\"document.location='mostra_curso.php?cod_curso=".$linha['cod_curso']."&amp;tipo_curso=".$tipo_curso."&amp;extremos=".$extremos."';\" type=\"button\" /></td>\n");
					}
				}

			}
		}
		else
		{
			/* 195 - curso nao iniciado */
			/* 174 - curso em andamento */
			/* 175 - curso com inscricao aberta */
			/* 176 - Curso encerrado */
			if ($tipo_curso=="N")
				$tela2=Linguas::RetornaFraseDaLista($lista_frases,195);
			else if ($tipo_curso=="A")
				$tela2=Linguas::RetornaFraseDaLista($lista_frases,174);
			else if ($tipo_curso=="I")
				$tela2=Linguas::RetornaFraseDaLista($lista_frases,175);
			else
				$tela2=Linguas::RetornaFraseDaLista($lista_frases,176);
			/* 57 - Nao ha nenhum */
			echo("                  <tr>\n");
			echo("                    <td colspan=3>".Linguas::RetornaFraseDaLista($lista_frases,57)." ".$tela2.".</td>\n");
			echo("                  </tr>\n");
		}

		echo("                </table>\n");
		echo("              </td>\n");
		echo("            </tr>\n");
		echo("          </table>\n");
	}
	else /* Ha mais de uma pasta de cursos com cursos nela, e nao se esta dentro de nenhuma */
	{
		echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
		echo("            <tr>\n");
		echo("              <td valign=\"top\">\n");
		echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
		echo("                  <tr class=\"head\">\n");
		/* 116 - Selecione uma categoria: */
		echo("                    <td colspan=\"3\">".Linguas::RetornaFraseDaLista($lista_frases,116)."</td>\n");
		echo("                  </tr>\n");

		$link="".$view_administracao."cursos_all.php?tipo_curso=".$tipo_curso;
		foreach ($lista_pastas as $cod => $linha)
		{
			echo("                  <tr>\n");
			echo("                    <td colspan=\"3\"><a href=".$link."&amp;cod_pasta=".$linha['cod_pasta'].">".$linha['pasta']." (".$linha['num_cursos'].")</a></td>\n");
			echo("                  </tr>\n");
		}

		echo("                </table>\n");
		echo("              </td>\n");
		echo("            </tr>\n");
		echo("          </table>\n");
	}

	echo("        </td>\n");
	echo("      </tr>\n");
	require_once $view_admin.'rodape_tela_inicial.php';
	echo("  </body>\n");
	echo("</html>");
	AcessoSQL::Desconectar($sock);
?>