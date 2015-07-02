<?php
$dir_static = '../../../static_includes/';
$ctrl_agenda = '../controller/';
$dir_img = '../../../img/';
$dir_lib = '../../../lib/';

include $ctrl_agenda.'AgendaController.php';
include $dir_lib.'FeedbackObject.inc.php';

$cod_ferramenta=1;

include $dir_static.'topo_tela.php';

$cod_curso = $_GET['cod_curso'];

$controlerAgenda = new AgendaController();

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject();
// adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("apagarItem", "Item apagado com sucess", 0);
$feedbackObject->addAction("apagarSelecionados", "Item(s) apagado(s) com sucesso", 0);
$feedbackObject->addAction("ativaragenda", "Agenda ativada com sucesso", 0);
$feedbackObject->addAction("importarItem", "Agenda importada com sucesso", 0);

echo("	<script type=\"text/javascript\" src=\"../../../js/agenda.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/dhtmllib.js\"></script>\n");
echo("	<script type=\"text/javascript\" src=\"../../../js/jscript.js\"></script>\n");

echo("    <script type=\"text/javascript\">\n\n");

echo("      function Iniciar()\n");
echo("      {\n");
echo("        lay_nova_agenda = getLayer('layer_nova_agenda');\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n");

echo("      if (isNav)\n");
echo("      {\n");
echo("        document.captureEvents(Event.MOUSEMOVE);\n");
echo("      }\n");
echo("      document.onmousemove = TrataMouse;\n\n");


echo("      function getPageScrollY()\n");
echo("      {\n");
echo("        if (isNav)\n");
echo("          return(window.pageYOffset);\n");
echo("        if (isIE){\n");
echo("          if(document.documentElement.scrollLeft>=0){\n");
echo("            return document.documentElement.scrollTop;\n");
echo("          }else if(document.body.scrollLeft>=0){\n");
echo("            return document.body.scrollTop;\n");
echo("          }else{\n");
echo("            return window.pageYOffset;\n");
echo("          }\n");
echo("        }\n");
echo("      }\n");

echo("    </script>\n\n");

include $dir_static.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* Impede o acesso a algumas secoes aos usuários que não são formadores. */
if (!$controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Visualizar Agendas Futuras')){
	/* 1 - Agenda */
	echo("          <h4>Agenda");
	/* 73- Acao exclusiva a formadores. */
	echo("    - Acao exclusiva a formadores</h4>\n");

	/*Voltar*/
	/* 509 - Voltar */
	echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;Voltar&nbsp;</span></li></ul>\n");

	include $dir_static.'3as.php';

	/* 23 - Voltar (gen) */
	echo("          <form name=\"frmErro\" action=\"\" method=\"post\">\n");
	echo("            <input class=\"input\" type=\"button\" name=\"cmdVoltar\" value='Voltar' onclick=\"Voltar();\" />\n");
	echo("          </form>\n");
	echo("        </td>\n");
	echo("      </tr>\n");
	echo("    </table>\n");
	echo("  </body>\n");
	echo("</html>\n");
	//Desconectar($sock);
	exit;
}

/* 1 - Agenda */
echo("          <h4> Agenda - Agendas Futuras</h4>");

// 3 A's - Muda o Tamanho da fonte
include $dir_static.'3as.php';

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;Voltar&nbsp;</span></li></ul>\n");

/* Tabela Externa */
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");
/*6 - Nova Agenda*/
echo("                      <li><span OnClick='NovaAgenda();'>Nova Agenda</span></li>\n");

if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Importar Agenda')){
	/*61 - Importar Agenda*/
	echo("                      <li><a href=\"importar_curso.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">Importar Agenda</a></li>\n");
}
/*8 - Voltar para Agenda Atual*/
echo("                      <li><a href=\"agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">Voltar para Agenda Atual</a></li>\n");

echo("                </ul>\n");
echo("        	</td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></td>\n");
echo("                    <td class=\"alLeft\">Agenda</td>\n");
echo("                    <td width=\"15%\">Data</td>\n");
/*110 - Situacao*/
echo("                    <td width=\"15%\">Situacao</td>\n");
echo("                  </tr>\n");

//$data_acesso=PenultimoAcesso($sock,$cod_usuario,"");
$lista_agendas = $controlerAgenda->listaAgendasSituacao($cod_curso, 'F');

if ((count($lista_agendas)>0)&&($lista_agendas != null))
{
	foreach ($lista_agendas as $cod => $linha_item)
	{
		$dataC = new Data();
		$data = $dataC->UnixTime2Data($linha_item['data_criacao']);
		$situacao="<a href=\"acoes_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;acao=ativaragenda\" onclick=return(TemCertezaAtivar());>Publicar";
		/* if ($data_acesso<$linha_item['data'])
		{
			$marcaib="<b>";
			$marcafb="</b>";
		}
		else
		{ */
			$marcaib="";
			$marcafb="";
		//}
		if ($linha_item['status']=="E")
		{
			//$linha_historico=RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);
			if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_historico['cod_usuario'])
			{
				$situacao=$marcaib.$situacao."</a>".$marcafb;
				/* if(!CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp))
				{
					//60(geral) -  Houve um erro ao tentar remover arquivos temporarios.
					echo("Erro: ".RetornaFraseDaLista($lista_frases_geral, 60));
					continue;
				} */

				//$situacao=$marcaib.$situacao."</a>".$marcafb;
				$titulo="<a id=\"tit_".$linha_item['cod_item']."\" onclick=\"window.open('ver_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar');\">".$linha_item['titulo']."</a>";
			}
			else
			{
				/* 43 - Em Edicao */
				$situacao="<span class=\"link\" onclick=\"window.open('em_edicao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">Em edicao</span>";
				$titulo=$linha_item['titulo'];
			}
		} else if ($linha_item['situacao'] == "A"){

			/* 23 - Em publicacao */
			$situacao = "Agenda Atual";
			$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"ver_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar\">".$linha_item['titulo']."</a>";
		}
		else
		{
			$situacao=$marcaib.$situacao."</a>".$marcafb;
			$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"ver_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar\">".$linha_item['titulo']."</a>";
		}

		$icone="<img src=\"".$dir_img."arqp.gif\" alt=\"\" border=\"0\" /> ";
		echo("                  <tr class=\"altColor".($cod%2)."\">\n");
		echo("                    <td width=\"2\"><input type=\"checkbox\" name=\"chkItem\" id=\"itm_".$linha_item['cod_item']."\" onclick=\"VerificaCheck();\" value=\"".$linha_item['cod_item']."\" /></td>\n");
		echo("                    <td align=left>".$icone.$titulo."</td>\n");
		echo("                    <td>".$marcaib.$data.$marcafb."</td>\n");
		echo("                    <td>".$situacao."</td>\n");
		echo("                  </tr>\n");

	}
}
else
{
	/* 9 - Nenhuma agenda foi criada! */
	echo("              <tr>\n");
	echo("                <td colspan=\"5\">Nenhuma agenda foi criada!</td>\n");
	echo("              </tr>\n");
}

/*Fim tabela interna*/
echo("                </table>\n");

/* 68 - Excluir Selecionados (ger)*/
if ($controlerPermissao->hasPermission($cod_usuario, $cod_ferramenta, 'Excluir Agenda')){
	echo("                <ul>\n");
	echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">Excluir selecionados</span></li>\n");
	echo("                </ul>\n");
}

/*Fim tabela externa*/
echo("              </td>\n");
echo("            </tr>\n");
echo("    	  </table>\n");
include $dir_static.'tela2.php';


/* Cria a funcao JavaScript que testa o nome da nova agenda e o layer  */
/* nova_agenda, se estiver visualizando as agendas disponieis.         */

/* Novo Item */
echo("    <div id=\"layer_nova_agenda\" class=\"popup\">\n");
echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_agenda);\"><img src=\"".$dir_img."btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
echo("      <div class=\"int_popup\">\n");
echo("        <form name=\"form_nova_agenda\" method=\"post\" action=\"acoes_linha.php\" onSubmit='return(VerificaNovoTitulo(document.form_nova_agenda.novo_titulo, 1));'>\n");
//echo("        ".RetornaSessionIDInput());
echo("          <div class=\"ulPopup\">\n");
/* 18 - Titulo: */
echo("  		  Titulo<br />\n");
echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=\"150\" /><br />\n");
echo("            <input type=\"hidden\" name=\"cod_curso\"   value=\"".$cod_curso."\" />\n");
echo("            <input type=\"hidden\" name=\"acao\"        value=\"criarAgenda\" />\n");
echo("            <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
echo("            <input type=\"hidden\" name=\"origem\"      value=\"ver_editar\" />\n");
/* 18 - Ok (gen) */

echo("            <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"Ok\" />\n");

/* 2 - Cancelar (gen) */
echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_agenda);\" value=\"Cancelar\" />\n");
echo("         </div>\n");
echo("        </form>\n");
echo("      </div>\n");
echo("    </div>\n\n");

echo("    <form name=\"form_dados\" action=\"\" id=\"form_dados\">\n");
echo("      <input type=\"hidden\" name=\"cod_curso\" id=\"cod_curso\"      value=\"".$cod_curso."\" />\n");
echo("      <input type=\"hidden\" name=\"cod_item\"  id=\"cod_item\"       value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"acao\"      id=\"acao_form\"      value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"cod_itens\" id=\"cod_itens_form\" value=\"\" />\n");
echo("      <input type=\"hidden\" name=\"origem\"    value=\"ver_editar\"");
echo("    </form>\n");

echo("  </body>\n");
echo("</html>\n");
?>