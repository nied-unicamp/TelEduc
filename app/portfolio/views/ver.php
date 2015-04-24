<?php

$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$ctrl_portfolio = '../../'.$ferramenta_portfolio.'/controllers/';
$view_portfolio = '../../'.$ferramenta_portfolio.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_ferramenta = 15;
$cod_ferramenta_ajuda = 15;
$cod_pagina_ajuda = 3;

$cod_curso = $_GET['cod_curso'];
$cod_usuario_portfolio = $_GET['cod_usuario_portfolio'];
$cod_usuario = ((isset($_GET['cod_usuario'])) ? $_GET['cod_usuario'] : $_GET['cod_usuario_portfolio']);
$cod_item = $_GET['cod_item'];
$cod_grupo_portfolio = $_GET['cod_grupo_portfolio'];
$cod_topico_raiz = $_GET['cod_topico_raiz'];

// Descobre os diretorios de arquivo, para os portfolios com anexo
$sock2 = AcessoSQL::Conectar("");
$diretorio_arquivos = Portfolio::RetornaDiretorio($sock2, 'Arquivos');
$diretorio_temp = Portfolio::RetornaDiretorio($sock2, 'ArquivosWeb');
AcessoSQL::Desconectar($sock2);

require_once $view_administracao.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject = new FeedbackObject();
//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"

/* 190 - Item criado com sucesso.
 * 191 - Item movido com sucesso.
 * 192 - Arquivo descompactado com sucesso.
 * 193 - Erro ao descompactar arquivo.
 * 62 - Arquivo anexado com sucesso.
 * 189 - Atenção: o arquivo que você anexou não existe ou tem mais de %dMb.
 * 201 - Arquivo(s) movido(s) com sucesso.*/
$feedbackObject->addAction("criarItem", _("ITEM_CREATED_SUCCESS_-1"), 0);
$feedbackObject->addAction("mover", _("ITEM_MOVED_SUCCESS_-1"), 0);
$feedbackObject->addAction("descompactar", _("FILE_EXTRACTED_SUCCESS_-1"), _("ERROR_EXTRACTING_FILE_-1"));
$feedbackObject->addAction("anexar", _("FILE_ATTACHED_SUCCESS_-1"), sprintf(_("ERROR_ATTACHING_FILE_-1"), ((int) ini_get('upload_max_filesize'))));
$feedbackObject->addAction("moverarquivos", _("FILES_MOVED_SUCCESS_-1"), 0);

$sock = AcessoSQL::Conectar($cod_curso);

$eformador = Usuarios::EFormador($sock, $cod_curso, $cod_usuario);

$status_portfolio = Portfolio::RetornaStatusPortfolio($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

$dono_portfolio = $status_portfolio['dono_portfolio'];
$portfolio_apagado = $status_portfolio['portfolio_apagado'];
$portfolio_grupo = $status_portfolio['portfolio_grupo'];

$ferramenta_grupos_s = Portfolio::StatusFerramentaGrupos($sock);
$_SESSION['ferramenta_grupos_s'] = $ferramenta_grupos_s;

$dir_item_temp = Portfolio::CriaLinkVisualizar($sock, $cod_curso, $cod_usuario, $cod_item, $diretorio_arquivos, $diretorio_temp);

$linha_item = Portfolio::RetornaDadosDoItem($sock, $cod_item);

$lista_arq = Portfolio::RetornaArquivosMaterialVer($cod_curso, $dir_item_temp['link']);

if ((count($lista_arq))>0){
	$i=0;
	foreach($lista_arq as $cod=>$linha2){
		if (is_dir($linha2['Caminho'])){
			$lista_diretorios[$i]['Diretorio'] = $linha2['Diretorio'];
			$lista_diretorios[$i]['Caminho'] = $linha2['Caminho'];
			$i++;
		}
	}
}

echo ("    <script type=\"text/javascript\">");
echo ("      function WindowOpenVer(id)\n");
echo ("      {\n");
echo ("         window.open(id+'?".time()."','Portfolio','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');\n");
echo ("      }\n\n");

echo ("</script>");

echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."ckeditor/ckeditor.js\"></script>");
echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."ckeditor/ckeditor_biblioteca.js\"></script>");
echo("	  <script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");
echo("    <script type=\"text/javascript\">\n");

/* (ger) 26 - Anexar Arquivos */
/* 59 - Pressione o botão Browse (ou Procurar) abaixo para selecionar o arquivo a ser anexado; em seguida, pressione OK para prosseguir. */
/* 60 - (arquivos .ZIP podem ser enviados e descompactados posteriormente) */
/* (ger) 63 - Houve um erro ao atualizar o material. */
/* (ger) 18 - Ok  */
/* (ger) 2 - Cancelar  */
/* 65 - Digite abaixo o título e o endereço da internet a ser incluido. */
/* 18 - Você tem certeza de que deseja apagar este item? */
/* 41 - Título */
/* 66 - Endereço */
/* 64 - Pelo menos o endereço deve ser preenchido! */
/* 45 - Incluir Endereço */
/* 30 - Tem certeza que deseja apagar este arquivo? */
/* 31 - Tem certeza que deseja apagar este diret?io? (todos os arquivos dele ser? apagados) */
/* 32 - Tem certeza que deseja apagar este endere?? */
/* 33 - Você tem certeza de que deseja descompactar este arquivo? */
/* 34 - (o arquivo ZIP será apagado) */
/* 35 - importante: não é possível a descompactação de arquivos contendo pastas com espaços no nome. */
/* 36 - O titulo nao pode ser vazio. */
/* 118 - Oculto */
/* 153 - Para que este item possa ser avaliado, associe-o a atividade a qual ele pertence. */
/* 179 - (Os itens serão movidos para a lixeira) */

echo ("      var cod_item='" . $cod_item . "';\n");
echo ("      var cod_topico='" . $cod_topico . "';\n");
echo ("      var cod_curso='" . $cod_curso . "';\n");
echo ("      var cod_usuario='" . $cod_usuario . "';\n");
echo ("      var cod_topico_ant='" . $cod_topico_raiz . "';\n");
echo ("      var cod_topico_raiz='" . $cod_topico_raiz . "';\n");
echo ("      var cod_usuario_portfolio='" . $cod_usuario_portfolio . "';\n");
echo ("      var cod_grupo_portfolio='" . $cod_grupo_portfolio . "';\n");
/* (ger) 18 - Ok */
// Texto do botao Ok do ckEditor
echo("    var textoOk = '"._("OK_-1")."';\n\n");
/* (ger) 2 - Cancelar */
// Texto do botao Cancelar do ckEditor
echo("    var textoCancelar = '"._("CANCEL_-1")."';\n\n");

echo ("      function Iniciar(){\n");
echo ("        cod_comp = getLayer(\"comp\");\n");
echo ("        cod_topicos = getLayer(\"topicos\");\n");
echo ("        cod_mover = getLayer(\"mover\");\n");
echo ("        cod_novapasta = getLayer(\"novapasta\");\n");
echo ("        cod_mover_arquivo = getLayer(\"mover_arquivo\");\n");
echo ("        EscondeLayers();\n");
//echo ("        xajax_RetornaFraseDinamic('lista_frases');");
//echo ("        xajax_RetornaFraseGeralDinamic('lista_frases_geral');");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo ("        startList();\n");
echo ("      }\n");

echo ("     function EditaTituloEnter(campo, evento, id)\n");
echo ("     {\n");
echo ("         var tecla;\n");
echo ("         CheckTAB=true;\n\n");
echo ("         if(navigator.userAgent.indexOf(\"MSIE\")== -1)\n");
echo ("         {\n");
echo ("             tecla = evento.which;\n");
echo ("         }\n");
echo ("         else\n");
echo ("         {\n");
echo ("             tecla = evento.keyCode;\n");
echo ("         }\n\n");
echo ("         if ( tecla == 13 )\n");
echo ("         {\n");
echo ("             EdicaoTitulo(id, 'tit_'+id, 'ok');\n");
echo ("         }\n\n");
echo ("         return true;\n");
echo ("     }\n\n");

echo("		function AbreEdicao(){\n");
echo("			$.post(\"".$model_portfolio."abre_edicao.php\",{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario}, \n");
echo("				function(data){\n");
echo("					var code = $.parseJSON(data);\n");
echo("					if (code==1){\n");
echo("						window.open('".$view_portfolio."em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes')\n");
echo("						document.location='".$view_portfolio."portfolio.php?cod_usuario=".$cod_usuario."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_topico_raiz=".$cod_topico_raiz.";'\n");
echo("					}\n");
echo("			});\n");
echo("		}\n");

echo("      function AlteraTitulo(id){\n");
echo("        var id_aux = id;\n");
echo("        if (editaTitulo==0){\n");
echo("          CancelaTodos();\n");

echo("			$.post(\"".$model_portfolio."abre_edicao.php\",{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario}, \n");
echo("				function(data){\n");
echo("					var code = $.parseJSON(data);\n");
echo("					if (code==1){\n");
echo("						window.open('".$view_portfolio."em_edicao.php?cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes')\n");
echo("						document.location='".$view_portfolio."portfolio.php?cod_usuario=".$cod_usuario."&cod_curso=".$cod_curso."&cod_item=".$cod_item."&origem=ver&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&cod_topico_raiz=".$cod_topico_raiz.";'\n");
echo("					}\n");
echo("			});\n");

echo("          conteudo = document.getElementById('tit_'+id).innerHTML;\n");
echo("          document.getElementById('tr_'+id).className='';\n");
echo("          document.getElementById('tit_'+id).className='';\n");

echo("          createInput = document.createElement('input');\n");
echo("          document.getElementById('tit_'+id).innerHTML='';\n");
echo("          document.getElementById('renomear_'+id).onclick=function(){ };\n\n");
echo("          document.getElementById('renomear_'+id).setAttribute('onclick', '');\n");

echo("          createInput.setAttribute('type', 'text');\n");
echo("          createInput.setAttribute('style', 'border: 2px solid #9bc');\n");
echo("          createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");
echo("          if (createInput.addEventListener){\n"); //not IE
echo("            createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
echo("          } else if (createInput.attachEvent){\n"); //IE
echo("            createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
echo("          }\n");

echo("          document.getElementById('tit_'+id).appendChild(createInput);\n");
echo("			$.post(\"".$model_geral."decodifica_string.php\",{conteudo:conteudo}, \n");
echo("				function(data){\n");
echo("					var code = $.parseJSON(data);\n");
echo("					$('#tit_".$cod_item."_text').val(code);\n");
echo("			});\n");

echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
echo("          espaco = document.createElement('span');\n");
echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
echo("          document.getElementById('tit_'+id).appendChild(espaco);\n");

echo("          createSpan = document.createElement('span');\n");
echo("          createSpan.className='link';\n");
echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };\n");
echo("          createSpan.setAttribute('id', 'OkEdita');\n");
echo("          createSpan.innerHTML='"._("OK_-1")."';\n");
echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
echo("          espaco = document.createElement('span');\n");
echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");

echo("          createSpan = document.createElement('span');\n");
echo("          createSpan.className='link';\n");
echo("          createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };\n");
echo("          createSpan.setAttribute('id', 'CancelaEdita');\n");
echo("          createSpan.innerHTML='"._("CANCEL_-1")."';\n");
echo("          document.getElementById('tit_'+id).appendChild(createSpan);\n\n");

echo("          //cria o elemento 'espaco' e adiciona na pagina\n");
echo("          espaco = document.createElement('span');\n");
echo("          espaco.innerHTML='&nbsp;&nbsp;';\n");
echo("          document.getElementById('tit_'+id).appendChild(espaco);\n\n");

echo("          startList();\n");
echo("          cancelarElemento=document.getElementById('CancelaEdita');\n");
echo("          document.getElementById('tit_'+id+'_text').select();\n");
echo("          editaTitulo++;\n");
echo("        }\n");
echo("      }\n\n");

echo("      function VerificaChkBox(alpha){\n");
echo("         CancelaTodos();\n");
echo("        checks = document.getElementsByName('chkArq');\n");
echo("        var i, j=0;\n");
echo("        var arqComum=0;\n");
echo("        var arqZip=0;\n");
echo("        var arqOculto=0;\n");
echo("        var pasta=0;\n\n");
echo("        var listaDir = '".$lista_diretorios."';\n");
echo("        var haDiretorios = listaDir.length;\n");

echo("        for (i=0; i<checks.length; i++){\n");
echo("          if(checks[i].checked){\n");
echo("            j++;\n");
echo("            getNumber=checks[i].id.split(\"_\");\n");
echo("            tipo = document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('tipoArq');\n");
echo("            switch (tipo){\n");
echo("              case ('pasta'): pasta=1;break;\n");
echo("              case ('comum'): arqComum++;break;\n");
echo("              case ('zip'): arqZip++;break;\n");
echo("            }\n\n");

echo("            if (document.getElementById(\"nomeArq_\"+getNumber[1]).getAttribute('arqOculto')=='sim'){\n");
echo("               arqOculto++;\n");
echo("            }\n\n");

echo("          }\n");
echo("        }\n");

echo("        if (pasta==1){\n");
echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
echo("          document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
echo("          document.getElementById('mArq_mover').className=\"menuUp\";\n");
echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n");

echo("          document.getElementById('mArq_apagar').onclick= function(){ Apagar(); };\n");
echo("          document.getElementById('mArq_ocultar').onclick= function(){  };\n");
echo("          document.getElementById('mArq_mover').onclick= function(){  };\n");
echo("          document.getElementById('mArq_descomp').onclick= function(){  };\n\n");

echo("        }else if((arqComum==1)||(arqZip>1)){\n");
echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
echo("          if (haDiretorios>0){\n");
echo("            document.getElementById('mArq_mover').className=\"menuUp02\";\n");
echo("          }\n");
echo("          else{\n");
echo("            document.getElementById('mArq_mover').className=\"menuUp\";\n");
echo("        }\n");
echo("        document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

echo("        document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
echo("        document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
echo("        if (haDiretorios>0){\n");
echo("          document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };\n");
echo("        }\n");
echo("        else{\n");
echo("          document.getElementById('sArq_mover').onclick= function(){  };\n");
echo("        }\n");
echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n\n");
echo("        }else if(arqComum>1){\n");
echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
echo("          document.getElementById('mArq_mover').className=\"menuUp\";\n");
echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");
echo("          document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
echo("          document.getElementById('sArq_mover').onclick= function(){  };\n");
echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n\n");
echo("        }else if(arqZip==1){\n");
echo("          document.getElementById('mArq_apagar').className=\"menuUp02\";\n");
echo("          document.getElementById('mArq_ocultar').className=\"menuUp02\";\n");
echo("          if (haDiretorios>0){\n");
echo("            document.getElementById('mArq_mover').className=\"menuUp02\";\n");
echo("          }\n");
echo("          else{\n");
echo("            document.getElementById('mArq_mover').className=\"menuUp\";\n");
echo("          }\n");
echo("          document.getElementById('mArq_descomp').className=\"menuUp02\";\n\n");

echo("          document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };\n");
echo("          document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };\n");
echo("          if (haDiretorios>0){\n");
echo("            document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };\n");
echo("          }\n");
echo("          else{\n");
echo("            document.getElementById('sArq_mover').onclick= function(){  };\n");
echo("          }\n");
echo("          document.getElementById('sArq_descomp').onclick= function(){ Descompactar() };\n");
echo("        }else{\n");
echo("          document.getElementById('mArq_apagar').className=\"menuUp\";\n");
echo("          document.getElementById('mArq_ocultar').className=\"menuUp\";\n");
echo("          document.getElementById('mArq_mover').className=\"menuUp\";\n");
echo("          document.getElementById('mArq_descomp').className=\"menuUp\";\n\n");

echo("          document.getElementById('sArq_apagar').onclick= function(){  };\n");
echo("          document.getElementById('sArq_ocultar').onclick= function(){  };\n");
echo("          document.getElementById('sArq_mover').onclick= function(){  };\n");
echo("          document.getElementById('sArq_descomp').onclick= function(){  };\n");
echo("        }\n\n");

echo("        //todos arquivos selecionados sao ocultos\n");
echo("        if ((j==arqOculto)&&(j!=0)) {\n");
echo("            document.getElementById('sArq_ocultar').onclick= function(){ Desocultar(); };\n");
echo("        }\n");

echo("        //Nao foi chamado pela funcao CheckTodos\n");
echo("        if (alpha){\n");
echo("          if (j==checks.length){ document.getElementById('checkMenu').checked=true; }\n");
echo("          else document.getElementById('checkMenu').checked=false;\n");
echo("        }\n");
echo("      }\n\n");
echo ("    </script>\n");

echo("  <script  type=\"text/javascript\" src=\"".$diretorio_jscss."jscriptlib_portfolio.js\"> </script>\n");

require_once $view_administracao.'menu_principal.php';
		
echo ("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* Verificação se o item está em Edição */
/* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
$linha = Portfolio::RetornaUltimaPosicaoHistorico($sock, $cod_item);

if ($linha['acao'] == "E") {
	if (($linha['data'] < (time() - 1800)) || ($cod_usuario == $linha['cod_usuario'])) {
		Portfolio::AcabaEdicao($cod_curso, $cod_item, $cod_usuario, 0);
	} else {
		/* Está em edição... */
		echo ("          <script language=\"javascript\">\n");
		echo ("            window.open('".$view_portfolio."em_edicao.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $cod_item . "&amp;origem=ver&amp;cod_topico_raiz=" . $cod_topico_raiz . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
		echo ("            window.location='".$view_portfolio."portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $linha_item['cod_item'] . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "&amp;cod_topico_raiz=" . $cod_topico_raiz . "';\n");
		echo ("          </script>\n");
		echo ("        </td>\n");
		echo ("      </tr>\n");
		echo ("    </table>\n");
		include ("layer.php");
		echo ("  </body>\n");
		echo ("</html>\n");
		exit ();
	}
}

/* Página Principal */
if ($portfolio_grupo) {
	// ajuda para portfolio de grupos, sem ferramenta avaliacao
	$cod_pagina = 11;
} else {
	// ajuda para portolio individual, sem ferramenta avaliacao
	$cod_pagina = 5;
}

if ($ferramenta_grupos_s) {
	//acao_portfolio_s pode ser G (grupo), F (encerrados), M (pessoal)

	// 3 - Portfolios de grupos
	$cod_frase = _("GROUP_PORTFOLIO_15");

	//meu portfolio individual
	if (($cod_grupo_portfolio == '') && (!$cod_grupo_portfolio)) {
		$cod_frase = _("INDIVIDUAL_PORTFOLIO_15");
	}
} else {
	// 2 - Portfolios individual
	$cod_frase = _("INDIVIDUAL_PORTFOLIO_15");
}

echo ("          <h4>" . _("PORTFOLIO_15") . " - " . $cod_frase . "</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo ("<div id=\"mudarFonte\">\n");
echo ("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo ("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo ("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");

$lista_topicos_ancestrais = Portfolio::RetornaTopicosAncestrais($sock, $cod_topico_raiz);
unset ($path);

foreach ($lista_topicos_ancestrais as $cod => $linha) {
	if ($cod_topico_raiz != $linha['cod_topico']) {
		$path = "<a class=\"text\" href=\"".$view_portfolio."portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_topico_raiz=" . $linha['cod_topico'] . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . $linha['topico'] . "</a> &gt;&gt; " . $path;
	} else {
		$path = "<a class=\"text\" href=\"".$view_portfolio."portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_topico_raiz=" . $linha['cod_topico'] . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . $linha['topico'] . "</a>";
	}
}

if ($portfolio_grupo) {
	$nome = Portfolio::NomeGrupo($sock, $cod_grupo_portfolio);

	//Figura de Grupo
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icGrupo.gif\" border=\"0\" />";

	/* 84 - Grupo Excluído */
	if ($grupo_apagado && $eformador)
		$complemento = " <span>(" . _("DELETED_GROUP_15") . ")</span>\n";

	echo ("          " . $fig_portfolio . " <span class=\"link\" onclick=\"AbreJanelaComponentes(" . $cod_grupo_portfolio . ");\">" . $nome . "</span>" . $complemento . " - ");
	echo ("          <a href=\"#\" onmousedown=\"js_cod_item='" . $cod_item . "'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"".$diretorio_imgs."estrutura.gif\" border=0 /></a>");
} else {
	$nome = Usuarios::NomeUsuario($sock, $cod_usuario_portfolio, $cod_curso);

	// Figura de Perfil
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icPerfil.gif\" border=\"0\" />";

	echo ("          " . $fig_portfolio . " <span class=\"link\" onclick=\"OpenWindowPerfil(" . $cod_usuario_portfolio . ");\" > " . $nome . "</span>" . $complemento . " - ");
	echo ("<a href=\"#\" onmousedown=\"js_cod_item='" . $cod_item . "'; MostraLayer(cod_topicos,0);return(false);\"><img alt=\"\" src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\" /></a>");

}

echo ($path);

echo ("          </span>\n");

echo ("          <!-- Tabelao -->\n");
echo ("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo ("            <tr>\n");
echo ("              <!-- Botoes de Acao -->\n");
echo ("              <td valign=\"top\">\n");
echo ("                <ul class=\"btAuxTabs\">\n");

//174 - Meus portfolios
echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;exibir=myp\">" . _("MY_PORTFOLIOS_15") . "</a></li>\n");
// 74 - Portfolios Individuais
echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;exibir=ind\">" . _("INDIVIDUAL_PORTFOLIOS_15") . "</a></li>\n");
// 75 - Portfolios de Grupos
if ($ferramenta_grupos_s) {
	echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;exibir=grp\">" . _("GROUP_PORTFOLIOS_15") . "</a></li>\n");
	// 177 - Portfolios encerrados
	echo ("                  <li><a href=\"".$view_portfolio."ver_portfolio.php?cod_curso=" . $cod_curso . "&cod_usuario=".$cod_usuario."&amp;exibir=enc\">" . _("ENDED_PORTFOLIOS_15") . "</a></li>\n");
}

echo ("                </ul>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("            <tr>\n");
echo ("              <td>\n");
echo ("                <ul class=\"btAuxTabs03\">\n");

$cod_topico_raiz_usuario = Portfolio::RetornaPastaRaizUsuario($sock, $cod_usuario, "");

unset ($array_params);
$array_params['cod_topico_raiz'] = $cod_topico_raiz;
$array_params['cod_item'] = $cod_item;
$array_params['cod_usuario_portfolio'] = $cod_usuario_portfolio;
$array_params['cod_grupo_portfolio'] = $cod_grupo_portfolio;

/* 70 - Ver Outros Itens */
echo ("                  <li><a href=\"".$view_portfolio."portfolio.php?cod_curso=" . $cod_curso . "&amp;cod_topico_raiz=" . $cod_topico_raiz . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . _("VIEW_OTHER_ITEMS_-1") . "</a></li>\n");

//72 - Historico
echo ("                  <li><span onclick=\"window.open('".$view_portfolio."historico.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $cod_item . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "','Historico','width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes');return(false)\">" . _("RECORD_OF_CHANGES_-1") . "</span></li>\n");

/* 112 - Comentários */
echo ("                  <li><a href=\"".$view_portfolio."comentarios.php?cod_curso=" . $cod_curso . "&amp;cod_item=" . $cod_item . "&amp;cod_topico_raiz=" . $cod_topico_raiz . "&amp;cod_usuario_portfolio=" . $cod_usuario_portfolio . "&amp;cod_usuario=" . $cod_usuario . "&amp;cod_grupo_portfolio=" . $cod_grupo_portfolio . "\">" . _("COMMENTS_-1") . "</a></li>\n");
if ($dono_portfolio) {
	/*Frase #25: Mover*/
	echo("					<li><span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event); AbreEdicao(); return(false);\">" . _("MOVE_-1") . "</span></li>\n");
	/*Frase #1: Apagar*/
	echo("                  <li><span onclick=\"CancelaTodos();ApagarItem();\">" . _("DELETE_-1") . "</span></li>\n");
}

echo ("                </ul>\n");
echo ("              </td>\n");
echo ("            </tr>\n");
echo ("            <tr>\n");
echo ("              <td>\n");
echo ("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo ("                  <tr class=\"head\">\n");
/* 41 - Título */
echo ("                    <td>" . _("TITLE_-1") . "</td>\n");

if ($dono_portfolio) {
	// 235 - Opções
	echo ("                    <td width=\"16%\" align=\"center\">" . _("OPTIONS_0") . "</td>\n");
}

/* 119 - Compartilhar */
echo ("                    <td width=\"10%\" align=\"center\">" . _("SHARE_-1") . "</td>\n");

echo ("                  </tr>\n");

//$linha_item = RetornaDadosDoItem($sock, $cod_item);

$titulo = $linha_item['titulo'];

$texto = "<span id=\"text_" . $linha_item['cod_item'] . "\">" . ConversorTexto::AjustaParagrafo($linha_item['texto']) . "</span>";

/* 209 - Renomear */
$renomear = _("RENAME_-1");

/* 184 - Editar texto */
$editar = _("EDIT_TEXT_-1");
/* 187 - Limpar texto */
$limpar = _("DELETE_TEXT_-1");

/* (ger) 25 - Mover */
$mover = _("MOVE_-1");

/* 12 - Totalmente Compartilhado */
if ($linha_item['tipo_compartilhamento'] == "T") {
	$compartilhamento = _("UNRESTRICTED_ACCESS_MODE_-1");
}
/* 13 - Compartilhado com Formadores */
else
if ($linha_item['tipo_compartilhamento'] == "F") {
	$compartilhamento = _("INSTRUCTOR_ACCESS_MODE_-1");
}
/* 14 - Compartilhado com o Grupo */
else
if (($portfolio_grupo) && ($linha_item['tipo_compartilhamento'] == "P")) {
	$compartilhamento = _("GROUP_ACCESS_MODE_-1");
}
/* 15 - Não compartilhado */
else
if (!$portfolio_grupo) {
	$compartilhamento = _("NOT_ACCESSIBLE_-1");
}

// Marca se a linha contém um item 'novo'
if ($data_acesso < $linha_item['data'])
	$marcatr = " class=\"novoitem\"";
else
	$marcatr = "";

$lista = NULL;

if ($linha_item['status'] == "E") {

	$linha_historico = Portfolio::RetornaUltimaPosicaoHistorico($sock, $linha_item['cod_item']);

	if ($linha_item['inicio_edicao'] < (time() - 1800) || $cod_usuario == $linha_historico['cod_usuario']) {
		Portfolio::CancelaEdicao($sock, $linha_item['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
		if ($dono_portfolio) {
			$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";
			$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
			$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
			$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
			$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
			//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
		}
	}
}
//else = item não está sendo editado
//   else if (!(($ferramenta_avaliacao && is_array($lista) && ItemEmAvaliacao($sock,$lista['cod_avaliacao'],$cod_usuario_portfolio) && $dono_portfolio)))
else
if (!(($ferramenta_avaliacao && is_array($lista) && $dono_portfolio))) {
	if ($linha_item['status'] != "C") {
		if ($dono_portfolio) {
			$titulo = "<span style=\"border:1pt;\" id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";

			$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
			$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
			$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
			$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
			//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
		}
	}
} else {
	$titulo = "<span id=\"tit_" . $linha_item['cod_item'] . "\">" . $linha_item['titulo'] . "</span>";
	$compartilhamentospan = "<span id=\"comp_" . $linha_item['cod_item'] . "\" class=\"link\" onclick=\"js_cod_item='" . $linha_item['cod_item'] . "';AtualizaComp('" . $linha_item['tipo_compartilhamento'] . "');MostraLayer(cod_comp,140,event);return(false);\">" . $compartilhamento . "</span>";
	$renomear = "<span onclick=\"AlteraTitulo(" . $linha_item['cod_item'] . ");\" id=\"renomear_" . $linha_item['cod_item'] . "\">" . $renomear . "</span>";
	$editar = "<span onclick=\"AlteraTexto(" . $linha_item['cod_item'] . ");\">" . $editar . "</span>";
	$limpar = "<span onclick=\"LimparTexto(" . $linha_item['cod_item'] . ");\">" . $limpar . "</span>";
	//$mover = "<span onclick=\"js_cod_item=" . $linha_item['cod_item'] . ";MostraLayer(cod_mover,0,event);xajax_AbreEdicao('" . $cod_curso . "', '" . $cod_item . "', '" . $cod_usuario . "', '" . $cod_usuario_portfolio . "', '" . $cod_grupo_portfolio . "', '" . $cod_topico_ant . "');return(false);\">" . $mover . "</span>";
}

echo ("                  <tr id='tr_" . $linha_item['cod_item'] . "'>\n");
echo ("                    <td class=\"itens\">" . $titulo . "</td>\n");

if ($dono_portfolio) {
	echo ("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
	echo ("                      <ul>\n");
	if ($renomear != null) {
		echo ("                        <li>" . $renomear . "</li>\n");
		echo ("                        <li>" . $editar . "</li>\n");
		echo ("                        <li>" . $limpar . "</li>\n");
		//echo ("                        <li>" . $mover . "</li>\n");
		// G 1 - Apagar
		//echo ("                        <li><span onclick=\"CancelaTodos();ApagarItem();\">" . RetornaFraseDaLista($lista_frases_geral, 1) . "</span></li>\n");
	}
	echo ("                      </ul>\n");
	echo ("                    </td>\n");
}

if (!($dono_portfolio)){
	echo(" <td align=\"center\">".$compartilhamento."</td>\n");
}
else{
	echo ("                    <td align=\"center\">" . $compartilhamentospan . "</td>\n");
}

echo ("                  </tr>");

// "<P>&nbsp;</P>" = texto em branco
// "<br>" = texto em branco
if ((($linha_item['texto'] != "") && ($linha_item['texto'] != "<P>&nbsp;</P>") && ($linha_item['texto'] != "<br />")) || ($dono_portfolio)) {
echo ("                  <tr class=\"head\">\n");
		/* 42 - Texto  */
echo ("                    <td colspan=\"4\">" . _("TEXT_-1") . "</td>\n");
  	echo ("                  </tr>\n");
  	echo ("                  <tr>\n");
  	echo ("                    <td class=\"itens\" colspan=\"4\">\n");
  	echo ("                      <div class=\"divRichText\">\n");
  	echo ("                        " . $texto . "\n");
  	echo ("                      </div>\n");
  	echo ("                    </td>\n");
  	echo ("                  </tr>\n");
  }

 $num_arq_vis = Portfolio::RetornaNumArquivosVisiveis($lista_arq);
 
 if (($num_arq_vis > 0) || ($dono_portfolio)) {
 	echo ("                  <tr class=\"head\">\n");
 	/* 71 - Arquivos */
 	echo ("                    <td colspan=\"4\">" . _("FILES_-2") . "</td>\n");
 	echo ("                  </tr>\n");
 	
 	if(($dono_portfolio) && (count($lista_arq)==0)){
 		echo("                <tr>\n");
 		/* 218 - Diretorio Vazio */
 		echo("                    <td colspan=\"6\">"._("EMPTY_DIRECTORY_-1")."</td>\n");
 		echo("				</tr>\n");
 	}
 	
 	if (is_array($lista_arq) && count($lista_arq)>0){
 	
 		$conta_arq = 0;
 	
 		echo ("                  <tr>\n");
 		echo ("                    <td class=\"itens\" colspan=\"4\" id=\"listFiles\">\n");
 		// Procuramos na lista de arquivos se existe algum visivel
 		$ha_visiveis = $num_arq_vis > 0;
 	
 		if (($ha_visiveis) || ($dono_portfolio)) {
 			$nivel_anterior = 0;
 			$nivel = -1;
 	
 			foreach ($lista_arq as $cod => $linha) {
 				$linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
 				if (!($linha['Arquivo'] == "" && $linha['Diretorio'] == ""))
 				if ((!$linha['Status']) || (($dono_portfolio))) {
 					$nivel_anterior = $nivel;
 					$espacos = "";
 					$espacos2 = "";
 					$temp = explode("/", $linha['Diretorio']);
 					$nivel = count($temp) - 1;
 					for ($c = 0; $c <= $nivel; $c++) {
 						if($dono_portfolio && $pode_editar){
 							$espacos.="&nbsp;&nbsp;&nbsp;&nbsp;";
 							$espacos2.="  ";
 						}
 						else{
 							$espacos.="";
 							$espacos2.="";
 						}
 					}
 	
 					$caminho_arquivo = $dir_item_temp['link'] . ConversorTexto::ConverteUrl2Html($linha['Diretorio'] . "/" . $linha['Arquivo']);
 					//converte o o caminho e o nome do arquivo que vêm do linux em UTF-8 para
 					//ISO-8859-1 para ser exibido corretamente na página.
 					$caminho_arquivo = mb_convert_encoding($caminho_arquivo, "ISO-8859-1", "UTF-8");
 					$linha['Arquivo'] = mb_convert_encoding($linha['Arquivo'], "ISO-8859-1", "UTF-8");
 					
 					
 					if ($linha['Arquivo'] != "") {
 	
 						if ($linha['Diretorio'] != "") {
 							$espacos .= "&nbsp;&nbsp;&nbsp;&nbsp;";
 							$espacos2 .= "  ";
 						}
 	
 						if ($linha['Status'])
 							$arqOculto = "arqOculto='sim'";
 						else
 							$arqOculto = "arqOculto='nao'";
 	
 	
 						if (eregi(".zip$",$linha['Arquivo'])){
 							// arquivo zip
 							$imagem    = "<img src=\"".$diretorio_imgs."arqzip.gif\" border=0 alt=\"\"/>";
 							$tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"zip\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" arqZip=\"" . $linha['Arquivo'] . "\" " . $arqOculto . ">";
 						}
 						else{
 							// arquivo comum
 							//imagem
 							if((eregi(".jpg$",$linha['Arquivo'])) || eregi(".png$",$linha['Arquivo']) || eregi(".gif$",$linha['Arquivo']) || eregi(".jpeg$",$linha['Arquivo'])) {
 								$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqimg.gif\" border=\"0\" />";
 								//doc
 							}else if(eregi(".doc$",$linha['Arquivo'])){
 								$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqdoc.gif\" \"border=\"0\" />";
 								//pdf
 							}else if(eregi(".pdf$",$linha['Arquivo'])){
 								$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqpdf.gif\" border=\"0\" />";
 								//html
 							}else if((eregi(".html$",$linha['Arquivo'])) || (eregi(".htm$",$linha['Arquivo']))){
 								$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqhtml.gif\" border=\"0\" />";
 							}else if((eregi(".mp3$",$linha['Arquivo'])) || (eregi(".mid$",$linha['Arquivo']))) {
 								$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqsnd.gif\" border=\"0\" />";
 							}else{
 								$imagem    = "<img alt=\"\" src=\"".$diretorio_imgs."arqp.gif\" border=\"0\" />";
 							}
 							$tag_abre = "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" onclick=\"WindowOpenVer('" . $caminho_arquivo . "');\" tipoArq=\"comum\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\" " . $arqOculto . ">";
 						}
 	
 						$tag_fecha = "</span>";
 	
 						echo ("                        " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");
 	
 						if ($dono_portfolio){
 							echo ("                          " . $espacos2 . "<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_" . $conta_arq . "\"/>\n");
 						}
 						
 						/* 215 - Última modificação em */
 						echo ("                          " . $espacos2 . $espacos . $imagem . $tag_abre . $linha['Arquivo'] . $tag_fecha . " - (" . round(($linha["Tamanho"] / 1024), 2) . "Kb) - "._("LAST_MODIFICATION_IN_-1")." ".Data::UnixTime2Hora($linha["Data"])." ".Data::UnixTime2DataMesAbreviado($linha["Data"])."");
 	
 						echo ("<span id=\"local_oculto_" . $conta_arq . "\">");
 						if ($linha['Status'])
 							// 118 - oculto
 							echo ("<span id=\"arq_oculto_" . $conta_arq . "\"> - <span style=\"color:red;\">" . _("HIDDEN_-1") . "</span></span>");
 						echo ("</span>\n");
 						echo ("                          " . $espacos2 . "<br />\n");
 						echo ("                        " . $espacos2 . "</span>\n");
 	
 					} else if (($dono_portfolio) || (Arquivos::haArquivosVisiveisDir($linha['Diretorio'], $lista_arq))){
 	
 						if ($nivel_anterior >= $nivel) {
 							$i = $nivel_anterior - $nivel;
 							$j = $i;
 							$espacos3 = "";
 							do {
 								$espacos3 .= "  ";
 								$j--;
 							} while ($j >= 0);
 							do {
 								echo ("                      " . $espacos3 . "</span>\n");
 								$i--;
 							} while ($i >= 0);
 						}
 						// pasta
 						$imagem = "<img alt=\"\" src=\"".$diretorio_imgs."pasta.gif\" border=\"0\" />";
 						echo ("                      " . $espacos2 . "<span id=\"arq_" . $conta_arq . "\">\n");
 						echo ("                        " . $espacos2 . "<span class=\"link\" id=\"nomeArq_" . $conta_arq . "\" tipoArq=\"pasta\" nomeArq=\"" . htmlentities($caminho_arquivo) . "\"></span>\n");
 						if ($dono_portfolio){
 							echo ("                        " . $espacos2 . "<input type=\"checkbox\" name=\"chkArq\" onclick=\"VerificaChkBox(1);\" id=\"chkArq_" . $conta_arq . "\">\n");
 						}
 						echo ("                        " . $espacos2 . $espacos . $imagem . $temp[$nivel] . "\n");
 						echo ("                        " . $espacos2 . "<br />\n");
 					}
 	
 				}
 				$conta_arq++;
 			}
 			do {
 				$j = $nivel;
 				$espacos3 = "";
 				do {
 					$espacos3 .= "  ";
 					$j--;
 				} while ($j >= 0);
 				$nivel--;
 			}
 			while ($nivel >= 0);
 		}
 		
 		echo ("                      <script type=\"text/javascript\">js_conta_arq=" . $conta_arq . ";</script>\n");
 		echo ("                    </td>\n");
 		echo ("                  </tr>\n");
 	}
 	
 	if ($dono_portfolio) {
 		echo ("                  <tr>\n");
 		echo ("                    <td align=\"left\" colspan=\"4\">\n");
 		echo ("                      <ul>\n");
 		echo ("                        <li class=\"checkMenu\"><span><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></span></li>\n");
 		echo ("                        <li class=\"menuUp\" id=\"mArq_apagar\"><span id=\"sArq_apagar\">"._("DELETE_-1")."</span></li>\n");
 		echo ("                        <li class=\"menuUp\" id=\"mArq_mover\"><span id=\"sArq_mover\">"._("MOVE_-1")."</span></li>\n");
 		echo ("                        <li class=\"menuUp\" id=\"mArq_descomp\"><span id=\"sArq_descomp\">"._("EXTRACT_-1")."</span></li>\n");
 		echo ("                        <li class=\"menuUp\" id=\"mArq_ocultar\"><span id=\"sArq_ocultar\">"._("HIDE_-1")."</span></li>\n");
 		echo ("                      </ul>\n");
 		echo ("                    </td>\n");
 		echo ("                  </tr>\n");
 		echo ("                  <tr>\n");
 		echo ("                    <td align=\"left\" colspan=\"4\">\n");
 		echo ("                      <form name=\"formFiles\" id=\"formFiles\" action=\"".$ctrl_portfolio."acoes.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
 		echo ("                        <input type=\"hidden\" name=\"cod_curso\" value=\"" . $cod_curso . "\" />\n");
 		echo ("                        <input type=\"hidden\" name=\"cod_item\" value=\"" . $cod_item . "\" />\n");
 		echo ("                        <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"" . $cod_topico_raiz . "\" />\n");
 		echo ("                        <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"" . $cod_usuario_portfolio . "\" />\n");
 		echo ("                        <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"" . $cod_grupo_portfolio . "\" />\n");
 		echo ("                        <input type=\"hidden\" name=\"acao\" value=\"anexar\" />\n");
 		echo ("                        <div id=\"divArquivoEdit\" class=\"divHidden\">\n");
 		echo ("                          <img alt=\"\" src=\"../imgs/paperclip.gif\" border=\"0\" />\n");
 		echo ("                          <span class=\"destaque\">" . _("ATTACH_FILE_-1") . "</span>\n");
 		echo ("                          <span> - " . _("PRESS_BUTTON_SELECT_ATTACH_FILE_-1") . _("ZIP_FILES_CAN_BE_EXTRACTED_-1") . "</span>\n");
 		echo ("                          <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
 		echo ("                          <input type=\"file\" id=\"input_files\" name=\"input_files\"  class=\"input\" onchange=\"EdicaoArq(1);\">\n");
 		//echo ("                          &nbsp;&nbsp;\n");
 		//echo ("                          <span onclick=\"EdicaoArq(1);\" id=\"OKFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 18) . "</span>\n");
 		//echo ("                          &nbsp;&nbsp;\n");
 		//echo ("                          <span onclick=\"EdicaoArq(0);\" id=\"cancFile\" class=\"link\">" . RetornaFraseDaLista($lista_frases_geral, 2) . "</span>\n");
 		echo ("                        </div>\n");
  		/* 26 - Anexar arquivos (ger) */
 		echo ("                        <div id=\"divArquivo\"><img alt=\"\" src=\"".$diretorio_imgs."paperclip.gif\" border=\"0\" /> <span class=\"link\" id =\"insertFile\" onclick=\"AcrescentarBarraFile(1);\">" . _("ATTACH_FILE_-1") . "</span></div>\n");
  		echo ("                      </form>\n");
 	
  	}
 	  				echo ("                    </td>\n");
  	echo ("                  </tr>\n");
  }
  
  $lista_url = Portfolio::RetornaEnderecosMaterial($sock, $cod_item);
  
  if ((is_array($lista_url)) || ($dono_portfolio)) {
  
  	echo ("                  <tr class=\"head\">\n");
  	/* 44 - Endereços */
  	echo ("                    <td colspan=\"4\">" . _("INTERNET_ADDRESSES_-1") . "</td>\n");
  	echo ("                  </tr>\n");
  	echo ("                  <tr>\n");
  	echo ("                    <td class=\"itens\" colspan=\"4\" id=\"listaEnderecos\">\n");
  
  	if (count($lista_url) > 0) {
  		foreach ($lista_url as $cod => $linha) {
  
  			$linha['endereco'] = ConversorTexto::RetornaURLValida($linha['endereco']);
  
  			echo ("                      <span id='end_" . $linha['cod_endereco'] . "'>\n");
  
  			if ($linha['nome'] != "") {
  				echo ("                      <span class=\"link\" onclick=\"WindowOpenVerURL('" . ConversorTexto::ConverteSpace2Mais($linha['endereco']) . "');\">" . $linha['nome'] . "</span>&nbsp;&nbsp;(" . $linha['endereco'] . ")");
  			} else {
  				echo ("                      <span class=\"link\" onclick=\"WindowOpenVerURL('" . ConversorTexto::ConverteSpace2Mais($linha['endereco']) . "');\">" . $linha['endereco'] . "</span>");
  			}
  
  			if ($dono_portfolio) {
  				/* (gen) 1 - Apagar */
  				echo (" - <span class=\"link\" onclick=\"ApagarEndereco('" . $cod_curso . "', '" . $linha['cod_endereco'] . "');\">" . _("DELETE_-1") . "</span>\n");
  			}
  			echo ("                        <br />\n");
  			echo ("                      </span>\n");
  
  		}
  	}
  
  	echo ("                    </td>\n");
  	echo ("                  </tr>\n");
  
  	if ($dono_portfolio) {
  		echo ("                  <tr>\n");
  		echo ("                    <td colspan=\"4\" align=\"left\" id=\"tdIncluirEnd\">\n");
  		/* 45 - Incluir Endereço */
  		echo ("                      <div id=\"divEndereco\"><img alt=\"\" src=\"".$diretorio_imgs."url.jpg\" border=\"0\" /> <span id=\"incluiEnd\" class=\"link\" onclick=\"AdicionaInputEndereco();\">" . _("INCLUDE_ADDRESS_-1") . "</span></div>\n");
  		echo ("                      <div id=\"divEnderecoEdit\" class=\"divHidden\">\n");
  		echo ("                        <img alt=\"\" src=\"".$diretorio_imgs."url.jpg\" border=\"0\" />\n");
  		echo ("                        <span id=\"incluiEndEdit\" class=\"destaque\">" . _("INCLUDE_ADDRESS_-1") . "</span>\n");
  		/* 65 - Digite abaixo o título e o endereço da internet a ser incluido.*/
  		echo ("                        <span> - " . _("TYPE_ADDRESS_TITLE_-1") . "</span>\n");
  		echo ("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
  		/* Título */
  		echo ("                        <span class=\"destaque\">" . _("TITLE_-1") . "</span><br />\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <input type=\"text\" class=\"input\" name=\"novoNomeEnd\" id=\"novoNomeEnd\" size=\"30\" />\n");
  		echo ("                        <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		/* Endereço*/
  		echo ("                        <span class=\"destaque\">" . _("INTERNET_ADDRESS_-1") . "</span><br />\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <input type=\"text\" class=\"input\" name=\"novoEnd\" id=\"novoEnd\" size=\"30\" />\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <span class=\"link\" onclick=\"EditaEndereco(1);\">" . _("OK_-1") . "</span>\n");
  		echo ("                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
  		echo ("                        <span class=\"link\" id=\"cancelaEnd\" onclick=\"EditaEndereco(0);\">" . _("CANCEL_-1") . "</span><br />\n");
  		echo ("                      </div>\n");
  		echo ("                    </td>\n");
  		echo ("                  </tr>\n");
  	}
  }
  
  echo ("                </table>\n"); //TabInterna
  echo ("              </td>\n");
  echo ("            </tr>\n");
  echo ("          </table>\n"); //TabExterna
  echo ("        </td>\n");
  echo ("      </tr>\n");

  require_once $view_administracao.'tela2.php';
  include ("layer.php");

  echo ("  </body>\n");
  echo ("</html>\n");

  AcessoSQL::Desconectar($sock);
  
?>
