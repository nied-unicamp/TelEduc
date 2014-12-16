<?php
$ferramenta_geral = 'geral';
$ferramenta_administacao = 'administracao';
$ferramenta_agenda = 'agenda';

$diretorio_jscss = "../../../web-content/js-css/";
$diretorio_imgs  = "../../../web-content/imgs/";
$view_administracao = '../../'.$ferramenta_administacao.'/views/';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_agenda = '../../'.$ferramenta_agenda.'/models/';
$view_agenda = '../../'.$ferramenta_agenda.'/views/';
$ctrl_agenda = '../../'.$ferramenta_agenda.'/controllers/';

require_once $model_geral.'geral.inc';
require_once $model_agenda.'agenda.inc';

$sock=AcessoSQL::Conectar("");
$diretorio_arquivos=Agenda::RetornaDiretorio($sock,'Arquivos');
$diretorio_temp=Agenda::RetornaDiretorio($sock,'ArquivosWeb');
AcessoSQL::Desconectar($sock);

$cod_ferramenta=1;
$cod_ferramenta_ajuda = $cod_ferramenta;
$cod_pagina_ajuda=3;

require_once $view_administracao.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject($lista_frases);
// adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("apagarItem", 101, 0);
$feedbackObject->addAction("apagarSelecionados", 101, 0);
$feedbackObject->addAction("ativaragenda", 102, 0);
$feedbackObject->addAction("importarItem", 108, 0);

// tipo de usuário
$e_formador    = Usuarios::EFormador($sock,$cod_curso,$cod_usuario);
$e_coordenador = Usuarios::ECoordenador($sock, $cod_curso, $cod_usuario);

echo("    <script type=\"text/javascript\">\n\n");

echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
echo("      var Xpos, Ypos;\n");
echo("      var js_cod_item, js_cod_topico;\n");
echo("      var js_nome_topico;\n");
echo("      var js_tipo_item;\n");
echo("      var js_comp = new Array();\n\n");

echo("      if (isNav)\n");
echo("      {\n");
echo("        document.captureEvents(Event.MOUSEMOVE);\n");
echo("      }\n");
echo("      document.onmousemove = TrataMouse;\n\n");

echo("      function VerificaNovoTitulo(textbox, aspas) {\n");
echo("        texto=textbox.value;\n");
echo("        if (texto==''){\n");
echo("          // se nome for vazio, nao pode\n");
/* 15 - O titulo nao pode ser vazio. */
echo("          alert(\"".Linguas::RetornaFraseDaLista($lista_frases,15)."\");\n");
echo("          textbox.focus();\n");
echo("          return false;\n");
echo("        }\n");
echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
echo("        else if ((texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0)&&(!aspas)) {\n");
/* 16 - O titulo nao pode conter \\. */
echo("           alert(\"".ConversorTexto::ConverteAspas2BarraAspas(ConversorTexto::ConverteHtml2Aspas(Linguas::RetornaFraseDaLista($lista_frases,16)))."\");\n");
echo("          textbox.value='';\n");
echo("          textbox.focus();\n");
echo("          return false;\n");
echo("        }\n");
echo("        return true;\n");
echo("      }\n\n");

echo("      function VerificaCheck(){\n");
echo("        var i;\n");
echo("        var j=0;\n");
echo("        var cod_itens=document.getElementsByName('chkItem');\n");
echo("        var Cabecalho = document.getElementById('checkMenu');\n");
echo("        array_itens = new Array();\n");
echo("        for (i=0; i<cod_itens.length; i++){\n");
echo("          if (cod_itens[i].checked){\n");
echo("            var item = cod_itens[i].id.split('_');\n");
echo("            array_itens[j]=item[1];\n");
echo("            j++;\n");
echo("          }\n");
echo("        }\n");
echo("        if ((j)==(cod_itens.length)) Cabecalho.checked=true;\n");
echo("        else Cabecalho.checked=false;\n");
echo("        if((j)>0){\n");
echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
echo("        }else{\n");
echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
echo("        }\n");
echo("      }\n\n");

echo("      function CheckTodos(){\n");
echo("        var e;\n");
echo("        var i;\n");
echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
echo("        var cod_itens=document.getElementsByName('chkItem');\n");
echo("        if (cod_itens.length == 0){\n");
echo("          return;\n");
echo("        }\n");
echo("        for(i = 0; i < cod_itens.length; i++){\n");
echo("          e = cod_itens[i];\n");
echo("          e.checked = CabMarcado;\n");
echo("        }\n");
echo("        VerificaCheck();\n");
echo("      }\n\n");

echo("      function ExcluirSelecionados(){\n");
echo("        if (TemCertezaApagar()){\n");
echo("          document.getElementById('cod_itens_form').value=array_itens;\n");
echo("          document.form_dados.action='".$ctrl_agenda."acoes_linha.php';\n");
echo("          document.form_dados.method='POST';\n");
echo("          document.getElementById('acao_form').value='apagarSelecionados';\n");
echo("          document.form_dados.submit();\n");
echo("        }\n");
echo("      }\n\n");

echo("      function TrataMouse(e)\n");
echo("      {\n");
echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
echo("      }\n");

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

echo("      function AjustePosMenuIE()\n");
echo("      {\n");
echo("        if (isIE)\n");
echo("          return(getPageScrollY());\n");
echo("        else\n");
echo("          return(0);\n");
echo("      }\n");

echo("      function Iniciar()\n");
echo("      {\n");
echo("        lay_nova_agenda = getLayer('layer_nova_agenda');\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n");
echo("      \n");

echo("      function EscondeLayers()\n");
echo("      {\n");
echo("        hideLayer(lay_nova_agenda);\n");
echo("      }\n");

echo("      function MostraLayer(cod_layer, ajuste)\n");
echo("      {\n");
echo("        EscondeLayers();\n");
echo("        moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());\n");
echo("        showLayer(cod_layer);\n");
echo("      }\n");

echo("      function EscondeLayer(cod_layer)\n");
echo("      {\n");
echo("        hideLayer(cod_layer);\n");
echo("      }\n");

echo("      function TemCertezaApagar()\n");
echo("      {\n");
/* 29 - Voce tem certeza de que deseja apagar esta agenda? */
/* 30 - (nao havera como recupera-la) */
echo("              return(confirm(\"".Linguas::RetornaFraseDaLista($lista_frases,29)."\\n".Linguas::RetornaFraseDaLista($lista_frases,30)."\"));\n");
echo("      }\n");

echo("      function TemCertezaAtivar()\n");
echo("      {\n");
/* 57 - Tem certeza que deseja publicar esta agenda? */
/* 58 - (Uma vez publicada ela substituira a Agenda Atual) */
echo("        return(confirm(\"".Linguas::RetornaFraseDaLista($lista_frases,57)."\\n".Linguas::RetornaFraseDaLista($lista_frases,58)."\"));\n");
echo("      }\n");

echo("      function NovaAgenda()\n");
echo("      {\n");
echo("        MostraLayer(lay_nova_agenda, 0);\n");
echo("        document.form_nova_agenda.novo_titulo.value = '';\n");
echo("        document.getElementById(\"nome\").focus();\n");
echo("      }\n");

echo("      function Voltar()\n");
echo("      {\n");
echo("        window.location='agenda.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."';;\n");
echo("      }\n\n");

//echo("      function Iniciar()\n");
//echo("      {\n");
//$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
//echo("        startList();\n");
//echo("      }\n\n");

echo("    </script>\n\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* Impede o acesso a algumas secoes aos usuários que não são formadores. */
if (!$tela_formador){
	/* 1 - Agenda */
	echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases, 1));
	/* 73- Acao exclusiva a formadores. */
	echo("    - ".Linguas::RetornaFraseDaLista($lista_frases, 73)."</h4>\n");

	/*Voltar*/
	/* 509 - Voltar */
	echo("          <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

	echo("          <div id=\"mudarFonte\">\n");
	echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
	echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
	echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
	echo("          </div>\n");

	/* 23 - Voltar (gen) */
	echo("          <form name=\"frmErro\" action=\"\" method=\"post\">\n");
	echo("            <input class=\"input\" type=\"button\" name=\"cmdVoltar\" value='".Linguas::RetornaFraseDaLista($lista_frases_geral,23)."' onclick=\"Voltar();\" />\n");
	echo("          </form>\n");
	echo("        </td>\n");
	echo("      </tr>\n");
	echo("    </table>\n");
	echo("  </body>\n");
	echo("</html>\n");
	AcessoSQL::Desconectar($sock);
	exit;
}


/* 1 - Agenda */
echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases, 1)." - ".Linguas::RetornaFraseDaLista($lista_frases, 3)."</h4>");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/*Voltar*/
/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

/* Tabela Externa */
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");
/*6 - Nova Agenda*/
echo("                      <li><span OnClick='NovaAgenda();'>".Linguas::RetornaFraseDaLista($lista_frases, 6)."</span></li>\n");
/*61 - Importar Agenda*/
echo("                      <li><a href=\"importar_curso.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">".Linguas::RetornaFraseDaLista($lista_frases, 61)."</a></li>\n");
/*8 - Voltar para Agenda Atual*/
echo("                      <li><a href=\"agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."\">".Linguas::RetornaFraseDaLista($lista_frases, 8)."</a></li>\n");

echo("                </ul>\n");
echo("        	</td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
/* Tabela Interna */
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");
echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onClick=\"CheckTodos();\" /></td>\n");
echo("                    <td class=\"alLeft\">".Linguas::RetornaFraseDaLista($lista_frases,1)."</td>\n");
echo("                    <td width=\"15%\">".Linguas::RetornaFraseDaLista($lista_frases,7)."</td>\n");
/*110 - Situacao*/
echo("                    <td width=\"15%\">".Linguas::RetornaFraseDaLista($lista_frases,110)."</td>\n");
echo("                  </tr>\n");

/*Conteudo*/
/*Listar Agendas*/
$data_acesso=Usuarios::PenultimoAcesso($sock,$cod_usuario,"");
$lista_agendas=Agenda::RetornaItensListaAgendas($sock);
if ((count($lista_agendas)>0)&&($lista_agendas != null))
{
	foreach ($lista_agendas as $cod => $linha_item)
	{
		$data=Data::UnixTime2Data($linha_item['data']);
		$situacao="<a href=\"".$ctrl_agenda."acoes_linha.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;acao=ativaragenda\" onclick=return(TemCertezaAtivar());>".Linguas::RetornaFraseDaLista($lista_frases,24);
		if ($data_acesso<$linha_item['data'])
		{
			$marcaib="<b>";
			$marcafb="</b>";
		}
		else
		{
			$marcaib="";
			$marcafb="";
		}
		if ($linha_item['status']=="E")
		{
			$linha_historico=Agenda::RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);
			if ($linha_item['inicio_edicao']<(time()-1800) || $cod_usuario==$linha_historico['cod_usuario'])
			{
				$situacao=$marcaib.$situacao."</a>".$marcafb;
				if(!Agenda::CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp))
				{
					//60(geral) -  Houve um erro ao tentar remover arquivos temporarios.
					echo("Erro: ".Linguas::RetornaFraseDaLista($lista_frases_geral, 60));
					continue;
				}

				//$situacao=$marcaib.$situacao."</a>".$marcafb;
				$titulo="<a id=\"tit_".$linha_item['cod_item']."\" onclick=\"window.open('".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar');\">".$linha_item['titulo']."</a>";
			}
			else
			{
				/* 43 - Em Edicao */
				$situacao="<span class=\"link\" onclick=\"window.open('".$view_agenda."em_edicao.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">".Linguas::RetornaFraseDaLista($lista_frases,43)."</span>";
				$titulo=$linha_item['titulo'];
			}
		} else if ($linha_item['situacao'] == "A"){

			/* 23 - Em publicacao */
			$situacao = Linguas::RetornaFraseDaLista($lista_frases, 23);
			$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar\">".$linha_item['titulo']."</a>";
		}
		else
		{
			$situacao=$marcaib.$situacao."</a>".$marcafb;
			$titulo="<a id=\"tit_".$linha_item['cod_item']."\" href=\"".$view_agenda."ver_linha_agenda.php?cod_curso=".$cod_curso."&amp;cod_usuario=".$cod_usuario."&amp;cod_item=".$linha_item['cod_item']."&amp;origem=ver_editar\">".$linha_item['titulo']."</a>";
		}

		$icone="<img src=\"".$diretorio_imgs."arqp.gif\" alt=\"\" border=\"0\" /> ";
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
	echo("                <td colspan=\"5\">".Linguas::RetornaFraseDaLista($lista_frases,9)."</td>\n");
	echo("              </tr>\n");
}

/*Fim tabela interna*/
echo("                </table>\n");

/* 68 - Excluir Selecionados (ger)*/
if ($e_formador || $e_coordenador)
{
	echo("                <ul>\n");
	echo("                  <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">".Linguas::RetornaFraseDaLista($lista_frases_geral,68)."</span></li>\n");
	echo("                </ul>\n");
}

/*Fim tabela externa*/
echo("              </td>\n");
echo("            </tr>\n");
echo("    	  </table>\n");

include $view_administracao."tela2.php";

/* Cria a funcao JavaScript que testa o nome da nova agenda e o layer  */
/* nova_agenda, se estiver visualizando as agendas disponieis.         */

/* Novo Item */
echo("    <div id=\"layer_nova_agenda\" class=\"popup\">\n");
echo("     <div class=\"posX\"><span onclick=\"EscondeLayer(lay_nova_agenda);\"><img src=\"../imgs/btClose.gif\" alt=\"Fechar\" border=\"0\" /></span></div>\n");
echo("      <div class=\"int_popup\">\n");
echo("        <form name=\"form_nova_agenda\" method=\"post\" action=\"".$ctrl_agenda."acoes_linha.php\" onSubmit='return(VerificaNovoTitulo(document.form_nova_agenda.novo_titulo, 1));'>\n");
//echo("        ".RetornaSessionIDInput());
echo("          <div class=\"ulPopup\">\n");
/* 18 - Titulo: */
echo("            ".Linguas::RetornaFraseDaLista($lista_frases,18)."<br />\n");
echo("            <input class=\"input\" type=\"text\" name=\"novo_titulo\" id=\"nome\" value=\"\" maxlength=\"150\" /><br />\n");
echo("            <input type=\"hidden\" name=\"cod_curso\"   value=\"".$cod_curso."\" />\n");
echo("            <input type=\"hidden\" name=\"acao\"        value=\"criarAgenda\" />\n");
echo("            <input type=\"hidden\" name=\"cod_usuario\" value=\"".$cod_usuario."\" />\n");
echo("            <input type=\"hidden\" name=\"origem\"      value=\"ver_editar\" />\n");
/* 18 - Ok (gen) */

echo("            <input type=\"submit\" id=\"ok_novoitem\" class=\"input\" value=\"".Linguas::RetornaFraseDaLista($lista_frases_geral,18)."\" />\n");

/* 2 - Cancelar (gen) */
echo("            &nbsp; &nbsp; <input type=\"button\" class=\"input\"  onClick=\"EscondeLayer(lay_nova_agenda);\" value=\"".Linguas::RetornaFraseDaLista($lista_frases_geral,2)."\" />\n");
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
AcessoSQL::Desconectar($sock);


?>
