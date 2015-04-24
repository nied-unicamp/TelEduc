<?php
$ferramenta_geral = 'geral';
$ferramenta_portfolio = 'portfolio';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_portfolio = '../../'.$ferramenta_portfolio.'/models/';
$view_portfolio = '../../'.$ferramenta_portfolio.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$diretorio_jscss = '../../../web-content/js-css/';
$diretorio_imgs = '../../../web-content/imgs/';
$ctrl_portfolio = '../../'.$ferramenta_portfolio.'/controllers/';

require_once $model_geral.'geral.inc';
require_once $model_portfolio.'portfolio.inc';

$cod_curso = $_GET['cod_curso'];
$cod_usuario = ((isset($_GET['cod_usuario'])) ? $_GET['cod_usuario'] : $_GET['cod_usuario_portfolio']);
$cod_topico_s = $_GET['cod_topico'];
$cod_topico_raiz = ((isset($_GET['cod_topico_raiz'])) ? $_GET['cod_topico_raiz'] : $_POST['cod_topico_raiz']);
$cod_usuario_portfolio = ((isset($_GET['cod_usuario_portfolio'])) ? $_GET['cod_usuario_portfolio'] : $_POST['cod_usuario_portfolio']);
$cod_grupo_portfolio = ((isset($_GET['cod_grupo_portfolio'])) ? $_GET['cod_grupo_portfolio'] : $_POST['cod_grupo_portfolio']);

$_SESSION['cod_topico_s'] = $cod_topico_s;
unset($cod_topico_s);

$cod_ferramenta = 15;
$cod_ferramenta_ajuda = 15;
$cod_pagina_ajuda = 2;

$sock1 = AcessoSQL::Conectar("");
$diretorio_arquivos_dinamic=Portfolio::RetornaDiretorio($sock1,'Arquivos');
$diretorio_temp_dinamic=Portfolio::RetornaDiretorio($sock1,'ArquivosWeb');
$eformador = Usuarios::EFormador($sock1,$cod_curso,$cod_usuario);
$visitante = Usuarios::EVisitante($sock1, $cod_curso, $cod_usuario);
AcessoSQL::Desconectar($sock1);

require_once $view_administracao.'topo_tela.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject();
//adicionar as acoes possiveis, 1o parametro é a ação, o segundo é o número da frase para ser impressa se for "true", o terceiro caso "false"

/* 194 - Item(ns) apagado(s) com sucesso.
 * 202 - Item(ns) movido(s) com sucesso.
 * 206 - Pasta criada com sucesso.
*/

$feedbackObject->addAction("apagarSelecionados", _("ITEM_DELETED_SUCCESS_-1"), 0);
$feedbackObject->addAction("apagarItem", _("ITEMS_DELETED_SUCCESS_-1"), 0);
$feedbackObject->addAction("moverItens", _("ITEMS_MOVED_SUCCESS_-1"), 0);
$feedbackObject->addAction("criarTopico", _("FOLDER_CREATED_SUCCESS_-1"), 0);

$sock = AcessoSQL::Conectar($cod_curso);

$cod_pasta_raiz = Portfolio::RetornaPastaRaizUsuario($sock, $cod_usuario, $cod_grupo_portfolio);

if (Portfolio::NaoExisteTop($sock, $cod_pasta_raiz, "Material de Apoio", $cod_usuario))
{
	Portfolio::CriarTopico($sock, $cod_pasta_raiz, "Material de Apoio", $cod_usuario, $cod_grupo_portfolio);
}

if (Portfolio::NaoExisteTop($sock, $cod_pasta_raiz, "Leituras", $cod_usuario))
{
	Portfolio::CriarTopico($sock, $cod_pasta_raiz, "Leituras", $cod_usuario, $cod_grupo_portfolio);
}

if (Portfolio::NaoExisteTop($sock, $cod_pasta_raiz, "Atividades", $cod_usuario))
{
	Portfolio::CriarTopico($sock, $cod_pasta_raiz, "Atividades", $cod_usuario, $cod_grupo_portfolio);
}

// cria o diretorio temporario da ferramenta
$dir_tmp_ferramenta = $diretorio_arquivos_dinamic.'/'.$cod_curso.'/portfolio/tmp';
if (!file_exists($dir_tmp_ferramenta)) mkdir($dir_tmp_ferramenta);
$tabela_dinamic="Portfolio";
$nome_ferramenta_dinamic="Portfolio";

// verificamos se a ferramenta de Avaliacoes está disponivel
$ferramenta_avaliacao = Usuarios::TestaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, 22);
/* Apaga links simbolicos que por acaso tenham sobrado daquele usuario */
system ("rm ../../../diretorio/portfolio_".$cod_curso."_*_".$cod_usuario);

$var = $diretorio_temp."/portfolio_".$cod_curso."_*_".$cod_usuario;

foreach (glob($var) as $filename)
{
	if(Arquivos::ExisteArquivo($filename))
		(Arquivos::RemoveArquivo($filename));
}

$data_acesso=Usuarios::PenultimoAcesso($sock,$cod_usuario,"");

$cod_topico_raiz_usuario=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,"");

if (!isset($cod_topico_raiz))
{
	if ($cod_grupo_portfolio!="" && $cod_grupo_portfolio!="NULL")
		$cod_topico_raiz=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,$cod_grupo_portfolio);
	else if ($cod_usuario_portfolio!="")
		$cod_topico_raiz=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario_portfolio,"");
	else
	{

		$cod_topico_raiz=$cod_topico_raiz_usuario;
		$cod_usuario_portfolio=$cod_usuario;

		/* Checagem da existência das pastas dos grupos a que o usuário pertence */
		Portfolio::VerificaPortfolioGrupos($sock,$cod_usuario);

		$cod_topico_raiz_usuario=Portfolio::RetornaPastaRaizUsuario($sock,$cod_usuario,"");

	}

	$cod_topico_raiz=$cod_topico_raiz_usuario;
	$cod_usuario_portfolio=$cod_usuario;

	/* Checagem da existência das pastas dos grupos a que o usuário pertence */
	Portfolio::VerificaPortfolioGrupos($sock,$cod_usuario);
}

if ($cod_topico_raiz=="NULL")
	// nao ha um topico selecionado: redirecionamos o usuario para exibir os portfolios do curso
{
	AcessoSQL::Desconectar($sock);
	header("Location:".$view_portfolio."ver_portfolio.php?cod_curso=".$cod_curso);
	exit;
}

$status_portfolio = Portfolio::RetornaStatusPortfolio ($sock, $cod_curso, $cod_usuario, $cod_usuario_portfolio, $cod_grupo_portfolio);

$dono_portfolio    = $status_portfolio ['dono_portfolio'];
$portfolio_apagado = $status_portfolio ['portfolio_apagado'];
$portfolio_grupo   = $status_portfolio ['portfolio_grupo'];

$ferramenta_grupos_s = Portfolio::StatusFerramentaGrupos($sock);
$_SESSION['ferramenta_grupos_s'] = $ferramenta_grupos_s;

if ($eformador){
	echo("    <script type=\"text/javascript\" language=\"javascript\">\n");
	echo("      function redirecionaDownloadAnexos(url){\n");
	echo("        window.location=url;\n");
	echo("      }\n");
	echo("    </script>\n");
}

if (!$dono_portfolio){
	//JS utilizado para mover as colunas da tabela
	echo("    <script type='text/javascript'>\n");
	echo("      function OpenWindowPerfil(id)\n");
	echo("      {\n");
	echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
	echo("        return(false);\n");
	echo("      }\n");

}else{
	echo("	<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");
	echo("    <script type='text/javascript'>\n");

	echo("      function OpenWindowPerfil(id)\n");
	echo("      {\n");
	echo("         window.open(\"../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]=\"+id,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
	echo("        return(false);\n");
	echo("      }\n");

	echo("      var isNav = (navigator.appName.indexOf(\"Netscape\") !=-1);\n");
	echo("      var isMinNS6 = ((navigator.userAgent.indexOf(\"Gecko\") != -1) && (isNav));\n");
	echo("      var isIE = (navigator.appName.indexOf(\"Microsoft\") !=-1);\n");
	echo("      var Xpos, Ypos;\n");
	echo("      var js_cod_item, js_cod_topico;\n");
	echo("      var js_nome_topico;\n");
	echo("      var js_tipo_item;\n");
	echo("      var editando=0;\n");
	echo("      var mostrando=0\n");
	echo("      var js_comp = new Array();\n");
	echo("      var array_itens;\n");
	echo("      var array_topicos;\n");
	echo("      var nome_topico_atual\n");
	echo("      var table;\n");
	echo("      var tableDnD;\n\n");

	echo("      if (isNav)\n");
	echo("      {\n");
	echo("        document.captureEvents(Event.MOUSEMOVE);\n");
	echo("      }\n");
	echo("      document.onmousemove = TrataMouse;\n\n");

	echo("      function TrataMouse(e)\n");
	echo("      {\n");
	echo("        Ypos = (isMinNS4) ? e.pageY : event.clientY;\n");
	echo("        Xpos = (isMinNS4) ? e.pageX : event.clientX;\n");
	echo("      }\n\n");

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
	echo("      }\n\n");

	echo("      function SoltaMouse(ids){\n");
	echo("			$.post(\"".$model_portfolio."atualiza_posicoes.php\",{cod_curso: ".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_topico: ".$cod_topico_raiz.", tabela: ids}, \n");
	echo("				function(data){\n");
	echo("					var code = $.parseJSON(data);\n");
	echo("					if (code=='true'){\n");
	/* 191 - Item movido com sucesso.*/
	echo("						mostraFeedback('"._("ITEM_MOVED_SUCCESS_-1")."', true)\n");
	echo("					}\n");
	echo("					else{\n");
	/* 211 - Erro interno! Atualize a página e tente mover os itens novamente.*/
	echo("						mostraFeedback('"._("INTERNAL_ERROR_MOVE_AGAIN_-1")."', false)\n");
	echo("					}\n");
	echo("			});\n");
	echo("      }\n");

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
	echo ("             EdicaoNomePasta(id,'ok');\n");
	echo ("         }\n\n");
	echo ("         return true;\n");
	echo ("     }\n\n");

	echo("      function AlterarNomePasta(){\n");
	echo("        id = 'nome_topico_atual';\n");
	echo("        id_aux = id;");
	echo("        document.getElementById(id).onclick = function() { };\n");
	echo("        nome_topico_atual = document.getElementById(id).innerHTML;\n");
	echo("        createInput = document.createElement('input');\n");
	echo("        document.getElementById(id).innerHTML = '';\n");
	echo("        document.getElementById(id).style.fontWeight = '';\n");
	echo("        createInput.setAttribute('type', 'text');\n");
	echo("        createInput.setAttribute('style', 'border: 2px solid #9bc');\n");

	echo("        if (createInput.addEventListener){\n"); //not IE
	echo("          createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);\n");
	echo("        } else if (createInput.attachEvent){\n"); //IE
	echo("          createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});\n");
	echo("        }\n");
	echo("        createInput.setAttribute('id', 'tit_'+id+'_text');\n\n");

	echo("        document.getElementById(id).appendChild(createInput);\n");
	echo("			$.post(\"".$model_geral."decodifica_string.php\",{conteudo:nome_topico_atual}, \n");
	echo("				function(data){\n");
	echo("					var code = $.parseJSON(data);\n");
	echo("					$('#tit_".$id."_text').val(code);\n");
	echo("			});\n");

	echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
	echo("        espaco = document.createElement('span');\n");
	echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
	echo("        document.getElementById(id).appendChild(espaco);\n\n");

	echo("        createSpan = document.createElement('span');\n");
	echo("        createSpan.className='link';\n");
	echo("        createSpan.onclick= function(){ EdicaoNomePasta(id, 'ok'); };\n");
	echo("        createSpan.setAttribute('id', 'OkEdita');\n");
	echo("        createSpan.innerHTML='"._("OK_-1")."';\n");
	echo("        document.getElementById(id).appendChild(createSpan);\n\n");

	echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
	echo("        espaco = document.createElement('span');\n");
	echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
	echo("        document.getElementById(id).appendChild(espaco);\n");

	echo("        createSpan = document.createElement('span');\n");
	echo("        createSpan.className='link';\n");
	echo("        createSpan.onclick= function(){ EdicaoNomePasta(id, 'canc'); };\n");
	echo("        createSpan.setAttribute('id', 'CancelaEdita');\n");
	echo("        createSpan.innerHTML='"._("CANCEL_-1")."';\n");
	echo("        document.getElementById(id).appendChild(createSpan);\n\n");

	echo("        //cria o elemento 'espaco' e adiciona na pagina\n");
	echo("        espaco = document.createElement('span');\n");
	echo("        espaco.innerHTML='&nbsp;&nbsp;'\n");
	echo("        document.getElementById(id).appendChild(espaco);\n");

	echo("        startList();\n");
	echo("        cancelarElemento=document.getElementById('CancelaEdita');\n");
	echo("        document.getElementById('tit_'+id+'_text').select();\n");
	echo("      }\n");

	echo("      function EdicaoNomePasta(id, valor){\n");

	echo("        if ((valor=='ok')&&(document.getElementById('tit_'+id+'_text').value!=\"\")){\n");
	echo("          novo_nome_topico = document.getElementById('tit_'+id+'_text').value;\n");
	echo("          if(novo_nome_topico == nome_topico_atual){\n");
	echo("            document.getElementById(id).innerHTML=nome_topico_atual;\n");
	echo("          }else{\n");
	echo("			$.post(\"".$model_portfolio."renomear_topico.php\",{cod_curso: ".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_topico: ".$cod_topico_raiz.", novo_nome:novo_nome_topico}, \n");
	echo("				function(data){\n");
	echo("					var code = $.parseJSON(data);\n");
	echo("					if (code=='true'){\n");
	echo("						$('nome_topico_atual').html(novo_nome_topico);\n");
	echo("						EscondeLayers();\n");
	/* 196 - Item renomeado com sucesso. */
	echo("						mostraFeedback('"._("ITEM_RENAMED_SUCCESS_-1")."', true)\n");
	echo("					}\n");
	echo("					else{\n");
	echo("						$('#nome_topico_atual').html(nome_topico_atual);\n");
	echo("						EscondeLayers();\n");
	/* 207 - Pasta renomeada com sucesso. */
	echo("						mostraFeedback('"._("FOLDER_RENAMED_SUCCESS_-1")."', false)\n");
	echo("					}\n");
	echo("			});\n");
	
	echo("          }\n");
	echo("        }else{\n");
	/* 36 - O titulo nao pode ser vazio. */
	echo("          if ((valor=='ok')&&(document.getElementById('tit_'+id+'_text').value==\"\"))\n");
	echo("            alert('"._("TITLE_CANNOT_BE_EMPTY_-1")."');\n");

	echo("          document.getElementById(id).innerHTML=nome_topico_atual;\n");
	echo("        }\n");
	echo("        document.getElementById(id).style.fontWeight='bold';\n");
	echo("      }\n");

	echo("      function VerificaNovoItemTitulo(textbox) {\n");
	echo("        texto=textbox.value;\n");
	echo("        if (texto==''){\n");
	echo("          // se nome for vazio, nao pode\n");
	/* 36 - O titulo nao pode ser vazio. */
	echo("          alert(\""._("TITLE_CANNOT_BE_EMPTY_-1")."\");\n");
	echo("          textbox.focus();\n");
	echo("          return false;\n");
	echo("        }\n");
	echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
	echo("        else if (texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0) {\n");
	/* 38 - O titulo do item nao pode conter \\\", \\\', < ou >. */
	echo("          alert(\"".ConversorTexto::ConverteAspas2BarraAspas(ConversorTexto::ConverteHtml2Aspas(_("TITLE_CANNOT_CONTAIN_-1")))."\");\n");
	echo("          textbox.value='';\n");
	echo("          textbox.focus();\n");
	echo("          return false;\n");
	echo("        }\n");
	echo("        return true;\n");
	echo("      }\n\n");

	echo("      function VerificaNovoItemTopico(textbox) {\n");
	echo("        texto=textbox.value;\n");
	echo("        if (texto==''){\n");
	echo("          // se nome for vazio, nao pode\n");
	/* 36 - O titulo nao pode ser vazio. */
	echo("          alert(\""._("TITLE_CANNOT_BE_EMPTY_-1")."\");\n");
	echo("          textbox.focus();\n");
	echo("          return false;\n");
	echo("        }\n");
	echo("        // se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0\n");
	echo("        else if (texto.indexOf(\"\\\\\")>=0 || texto.indexOf(\"\\\"\")>=0 || texto.indexOf(\"'\")>=0 || texto.indexOf(\">\")>=0 || texto.indexOf(\"<\")>=0 || texto.indexOf(\"#\")>=0) {\n");
	echo("           alert(\"".ConversorTexto::ConverteAspas2BarraAspas(ConversorTexto::ConverteHtml2Aspas(_("TITLE_CANNOT_CONTAIN_-1")))."\");\n");
	echo("          textbox.value='';\n");
	echo("          textbox.focus();\n");
	echo("          return false;\n");
	echo("        }\n");
	echo("			$.ajax({\n");
	echo("				url: '".$model_portfolio."cria_topico.php',\n");
	echo("				data: {cod_curso: ".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_grupo_portfolio: '".$cod_grupo_portfolio."', cod_usuario_portfolio:".$cod_usuario_portfolio.", cod_topico_raiz: ".$cod_topico_raiz.", novo_nome:texto}, \n");
	echo("				type: 'POST', \n");
	echo("				dataType: 'json', \n");
	echo("				success: function(data) {\n");
	echo("					if (data.retorno=='true'){\n");
	echo("						window.location='".$view_portfolio."portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz='+data.cod_topico+'&cod_usuario=".$cod_usuario."&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao=criarTopico&atualizacao=true';\n");
	echo("					}\n");
	echo("					else{\n");
	/* 73 - Não foi possível criar a pasta, pois já existe uma com mesmo nome. */
	echo("						mostraFeedback('"._("ERROR_CREATE_FOLDER_-1")."', false)\n");
	echo("					}\n");
	echo("				}\n");
	echo("			});\n");
	echo("        return false;\n");
	echo("      }\n\n");

	echo("      function AtualizaComp(js_tipo_comp)\n");
	echo("      {\n");
	echo("        if ((isNav) && (!isMinNS6)) {\n");
	echo("          document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;\n");
	echo("          document.comp.document.form_comp.cod_item.value=js_cod_item;\n");
	echo("          var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));\n");
	echo("        } else {\n");
	echo("            document.form_comp.tipo_comp.value=js_tipo_comp;\n");
	echo("            document.form_comp.cod_item.value=js_cod_item;\n");
	echo("            var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));\n");
	echo("        }\n");
	echo("        var imagem=\"<img src='".$diretorio_imgs."checkmark_blue.gif' />\"\n");
	echo("        if (js_tipo_comp=='T') {\n");
	echo("          tipo_comp[0].innerHTML=imagem;\n");
	echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
	echo("        } else if (js_tipo_comp=='F') {\n");
	echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[1].innerHTML=imagem;\n");
	echo("          tipo_comp[2].innerHTML=\"&nbsp;\";\n");
	echo("        } else{\n");
	echo("          tipo_comp[0].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[1].innerHTML=\"&nbsp;\";\n");
	echo("          tipo_comp[2].innerHTML=imagem;\n");
	echo("        }\n");
	echo("      }\n\n");

	echo("      function MostraLayer(cod_layer, ajuste, ev){\n");
	echo("        EscondeLayers();\n");
	echo("        ev = ev || window.event;\n");
	echo("        if(ev.pageX || ev.pageY){\n");
	echo("          Xpos = ev.pageX;\n");
	echo("          Ypos = ev.pageY;\n");
	echo("        }else{\n");
	echo("          Xpos = ev.clientX + document.body.scrollLeft - document.body.clientLeft;\n");
	echo("          Ypos = ev.clientY + getPageScrollY();\n");
	echo("        }\n");
	echo("        moveLayerTo(cod_layer,Xpos-100,Ypos);\n");
	echo("        showLayer(cod_layer);\n");
	echo("      }\n\n");

	echo("      function EscondeLayer(cod_layer)\n");
	echo("      {\n");
	echo("        hideLayer(cod_layer);\n");
	echo("        mostrando=0;\n");
	echo("      }\n\n");

	echo("      function VerificaCheck(){\n");
	echo("        var i;\n");
	echo("        var j=0;\n");
	echo("        var k=0;\n");
	echo("        var cod_itens=document.getElementsByName('chkItem');\n");
	echo("        var cod_topicos=document.getElementsByName('chkTopico');\n");
	echo("        var Cabecalho = document.getElementById('checkMenu');\n");
	echo("        array_itens = new Array();\n");
	echo("        array_topicos = new Array();\n");
	echo("        for (i=0; i < cod_itens.length; i++){\n");
	echo("          if (cod_itens[i].checked){\n");
	echo("            var item = cod_itens[i].id.split('_');\n");
	echo("            array_itens[j]=item[1];\n");
	echo("            j++;\n");
	echo("          }\n");
	echo("        }\n");
	echo("        for (i=0; i < cod_topicos.length; i++){\n");
	echo("          if (cod_topicos[i].checked){\n");
	echo("            topico = cod_topicos[i].id.split('_');\n");
	echo("            array_topicos[k]=topico[1];\n");
	echo("            k++;\n");
	echo("          }\n");
	echo("        }\n");
	echo("        if ((k+j)==(cod_topicos.length+cod_itens.length)) Cabecalho.checked=true;\n");
	echo("        else Cabecalho.checked=false;\n");
	echo("        if((k+j)>0){\n");
	echo("          document.getElementById('mExcluir_Selec').className=\"menuUp02\";\n");
	echo("          document.getElementById('mMover_Selec').className=\"menuUp02\";\n");
	echo("          document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };\n");
	echo("          document.getElementById('mMover_Selec').onclick = function(event) { MostraLayer(cod_mover_selec,0, event); };\n");
	echo("        }else{\n");
	echo("          document.getElementById('mExcluir_Selec').className=\"menuUp\";\n");
	echo("          document.getElementById('mMover_Selec').className=\"menuUp\";\n");
	echo("          document.getElementById('mExcluir_Selec').onclick=function(){  };\n");
	echo("          document.getElementById('mMover_Selec').onclick=function(){  };\n");
	echo("        }\n");
	echo("      }\n\n");

	echo("      function CheckTodos(){\n");
	echo("        var e;\n");
	echo("        var i;\n");
	echo("        var CabMarcado = document.getElementById('checkMenu').checked;\n");
	echo("        var cod_itens=document.getElementsByName('chkItem');\n");
	echo("        var cod_topicos=document.getElementsByName('chkTopico');\n");
	echo("        for(i = 0; i < cod_itens.length; i++){\n");
	echo("          e = cod_itens[i];\n");
	echo("          e.checked = CabMarcado;\n");
	echo("        }\n");
	echo("        for(i = 0; i < cod_topicos.length; i++){\n");
	echo("          e = cod_topicos[i];\n");
	echo("          e.checked = CabMarcado;\n");
	echo("        }\n");
	echo("        VerificaCheck();\n");
	echo("      }\n\n");

	echo("      function ExcluirSelecionados(){\n");
	/* 18 - Você tem certeza de que deseja apagar este item? */
	/* 179 - (Os itens serão movidos para a lixeira) */
	echo("        if (confirm('"._("SURE_TO_DELETE_ITEM_-1")."\\n"._("ITEM_MOVE_TO_TRASH_-1")."')){\n");
	echo("          document.getElementById('cod_topicos_form').value=array_topicos;\n");
	echo("          document.getElementById('cod_itens_form').value=array_itens;\n");
	echo("          document.form_dados.action='".$ctrl_portfolio."acoes.php';\n");
	echo("          document.form_dados.method='POST';\n");
	echo("          document.getElementById('acao_form').value='apagarSelecionados';\n");
	echo("          document.form_dados.submit();\n");
	echo("        }\n");
	echo("      }\n\n");

	echo("      function MoverSelecionados(topico_destino){\n");
	echo("			$.post(\"".$model_portfolio."mover_itens.php\",{cod_curso: ".$cod_curso.", cod_usuario: ".$cod_usuario.", cod_topico_raiz: ".$cod_topico_raiz.", cod_topico_novo: topico_destino, cod_topicos: array_topicos, cod_itens: array_itens}, \n");
	echo("				function(data){\n");
	echo("					var code = $.parseJSON(data);\n");
	echo("					if (code == 1){\n");
	/* 28 -  Você não pode mover uma pasta para ela mesma ou para uma subpasta dela.*/
	echo("						mostraFeedback('"._("CANT_MOVE_FOLDER_ITSELF_SUBFOLDER_15")."', false);\n");
	echo("					}\n");
	echo("					else if (code == 2){\n");
	echo("						Redirecionar('".$cod_topico_raiz."', \"moverItens\", \"true\")\n");
	echo("					}\n");
	echo("					else if (code == 3){\n");
	/* 71 - Não foi possível mover a pasta, pois já existe uma pasta com mesmo nome no diretório destino. */
	echo("						mostraFeedback('"._("CANT_MOVE_FOLDER_NAME_-1")."', false);\n");
	echo("					}\n");
	echo("					else if (code == 4){\n");
	echo("						Redirecionar('".$cod_topico_raiz."', \"moverItens\", \"true\")\n");
	echo("					}\n");
	echo("			});\n");
	echo("      }\n\n");

	echo("      function Redirecionar(cod_topico_raiz, acao, atualizacao){\n");
	echo("         window.location='portfolio.php?cod_curso=".$cod_curso."&cod_topico_raiz='+cod_topico_raiz+'&cod_usuario_portfolio=".$cod_usuario_portfolio."&cod_grupo_portfolio=".$cod_grupo_portfolio."&acao='+acao+'&atualizacao='+atualizacao;\n");
	echo("      }\n\n");

	echo("      function EscondeLayers()\n");
	echo("      {\n");
	echo("        hideLayer(cod_comp);\n");
	echo("        hideLayer(cod_mover);\n");
	echo("        hideLayer(cod_mover_selec);\n");
	echo("        hideLayer(cod_novoitem);\n");
	echo("        hideLayer(cod_novapasta);\n");
	echo("      }\n\n");

}
echo("      function Iniciar()\n");
echo("      {\n");
if($dono_portfolio){
	echo("        cod_comp = getLayer(\"comp\");\n");
	echo("        cod_mover = getLayer(\"mover\");\n");
	echo("        cod_mover_selec = getLayer(\"mover_selec\");\n");
	echo("        cod_novoitem = getLayer(\"novoitem\");\n");
	echo("        cod_novapasta = getLayer(\"novapasta\");\n");
	echo("        cod_topicos = getLayer(\"topicos\");\n");
	echo("        EscondeLayers();\n");
	echo("        tableDnD = new TableDnD();\n");
	echo("        table = document.getElementById('tab_interna');\n");
	echo("        if(table) tableDnD.init(table);\n");
}
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n\n");

echo("      function AbreJanelaComponentes(id)\n");
echo("      {\n");
echo("         window.open(\"../grupos/exibir_grupo.php?cod_curso=".$cod_curso."&cod_grupo=\"+id,\"GruposDisplay\",\"width=700,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");\n");
echo("        return(false);\n");
echo("      }\n");

echo("    </script>\n");
echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."tablednd.js\"></script>\n");

require_once $view_administracao.'menu_principal.php';

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

Portfolio::ExpulsaVisitante($sock, $cod_curso, $cod_usuario);

/* P?ina Principal */

if ($ferramenta_avaliacao)
{
	if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
	{
		// 3 - Portfolio de grupo
		$cod_frase  =  _("GROUP_PORTFOLIO_15");
		$cod_pagina = 23;
	}
	else
	{
		// 2 - Portfolio individual
		$cod_frase  =  _("INDIVIDUAL_PORTFOLIO_15");
		$cod_pagina = 18;
	}
}
else
{
	if ($ferramenta_grupos_s && $cod_grupo_portfolio != '')
	{
		// 3 - Portfolio de grupo
		$cod_frase = _("GROUP_PORTFOLIO_15");
		$cod_pagina=10;
	}
	else
	{
		// 2 - Portfolio individual
		$cod_frase = _("INDIVIDUAL_PORTFOLIO_15");
		$cod_pagina=3;
	}
}


echo("          <h4>"._("PORTFOLIO_15")." - ".$cod_frase."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("<div id=\"mudarFonte\">\n");
echo("      <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("      <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");

$lista_topicos_ancestrais=Portfolio::RetornaTopicosAncestrais($sock, $cod_topico_raiz);
unset($path);

foreach ($lista_topicos_ancestrais as $cod => $linha)
{
	if ($cod_topico_raiz!=$linha['cod_topico'])
	{
		$path="<a class=\"text\" href=\"portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."\">".$linha['topico']."</a> &gt;&gt; ".$path;
	}
	else
	{
		$path="<span style=\"font-weight:bold;\" id=\"nome_topico_atual\">".$linha['topico']."</span><br />\n";
	}
}

if ($portfolio_grupo)
{
	$nome=Portfolio::NomeGrupo($sock,$cod_grupo_portfolio);

	//Figura de Grupo
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icGrupo.gif\" border=\"0\" />";

	/* 84 - Grupo Excluído */
	if ($grupo_apagado && $eformador) $complemento=" <span>("._("DELETED_GROUP_15").")</span>\n";


	echo("          ".$fig_portfolio." <span class=\"link\" onclick=\"AbreJanelaComponentes(".$cod_grupo_portfolio.");\">".$nome."</span>".$complemento." - ");
	echo("          <span class=\"link\" onclick=\"MostraLayer(cod_topicos,0,event);\"><img src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\" alt=\"estrutura.gif\"/></span>");
}
else
{
	$nome=Usuarios::NomeUsuario($sock,$cod_usuario_portfolio, $cod_curso);

	// Selecionando qual a figura a ser exibida ao lado do nome
	$fig_portfolio = "<img alt=\"\" src=\"".$diretorio_imgs."icPerfil.gif\" border=\"0\" />";

	/* 85 - Aluno Rejeitado */
	if (Usuarios::RetornaStatusUsuario($sock,$cod_curso,$cod_usuario_portfolio)=="r" && $eformador) $complemento=" <font class=\"textsmall\">("._("REJECTED_STUDENT_0").")</font>\n";

	echo("          ".$fig_portfolio." <span class=\"link\" onclick=\"OpenWindowPerfil(".$cod_usuario_portfolio.");\" > ".$nome."</span>".$complemento." - ");
	echo("<a href=\"#\" onmousedown=\"js_cod_item='".$cod_item."'; MostraLayer(cod_topicos,0,event);return(false);\"><img alt=\"\" src=\"".$diretorio_imgs."estrutura.gif\" border=\"0\" /></a>");
}

echo ($path);

echo("          <!-- Tabelao -->\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <!-- Botoes de Acao -->\n");
echo("              <td valign=\"top\">\n");
echo("                <ul class=\"btAuxTabs\">\n");

//174 - Meus portfolios
echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=myp\">"._("MY_PORTFOLIOS_15")."</a></li>\n");
// 74 - Portfolios Individuais
echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=ind\">"._("INDIVIDUAL_PORTFOLIOS_15")."</a></li>\n");

// 75 - Portfolios de Grupos
if ((isset($ferramenta_grupos_s)) && ($ferramenta_grupos_s)) {
	echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=grp\">"._("GROUP_PORTFOLIOS_15")."</a></li>\n");
	// 177 - Portfolios encerrados
	echo("                  <li><a href=\"ver_portfolio.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&amp;exibir=enc\">"._("ENDED_PORTFOLIOS_15")."</a></li>\n");
}

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <ul class=\"btAuxTabs03\">\n");

// 69 - Atualizar
echo("                  <li> <span onclick=\"window.location.reload();\">"._("UPDATE_-1")."</span></li>\n");

// download de todos os anexos do portfolio de um aluno
// TODO: falta fazer a funcao dinamica para pegar os anexos e montar o zip
//var_dump($cod_topico_raiz);
//if ($eformador)
//echo("                    <li><span onclick=\"xajax_CriaZipDinamic('".$sock."','".$cod_topico_raiz."','".$dir_tmp_ferramenta."',".$cod_curso.",".$cod_ferramenta.",'".$diretorio_arquivos_dinamic."','".$tabela_dinamic."','".$bibliotecas."','".$nome_ferramenta_dinamic."','".$diretorio_temp_dinamic."');\">Baixar todos os anexos</span></li>\n");
// FIXME
//CriaZipDinamic($cod_topico_raiz, $dir_tmp_ferramenta, $cod_curso, $cod_ferramenta, $diretorio_arquivos_dinamic, $tabela_dinamic, $bibliotecas, $nome_ferramenta_dinamic, $diretorio_temp_dinamic);
//$sock1 = Conectar($cod_curso);
//CriaArvorePastasTopico($sock1, $cod_topico_raiz, $dir_tmp_ferramenta, $cod_curso, $cod_ferramenta, $diretorio_arquivos_dinamic);
//Desconectar($sock1);
if ($dono_portfolio)
{
	// 4 - Incluir Novo Item
	echo("                  <li><span onclick=\"MostraLayer(cod_novoitem, 140,event);document.getElementById('titulo').focus();document.getElementById('titulo').value=''\">"._("INCLUDE_NEW_ITEM_-1")."</span></li>\n");
	// 5 - Nova Pasta
	echo("                  <li><span onclick=\"MostraLayer(cod_novapasta, 140,event);document.getElementById('titulopasta').value=''; document.getElementById('titulopasta').focus();\">"._("NEW_FOLDER_-1")."</span></li>\n");

	if($cod_topico_raiz != $cod_topico_raiz_usuario){
		// 183 - Renomear Pasta
		echo("                  <li><span onclick=\"AlterarNomePasta();\" >"._("RENAME_FOLDER_-1")."</span></li>\n");
	}

	// 7 - Lixeira
	echo("                  <li><span onclick=\"window.location='portfolio_lixeira.php?cod_curso=".$cod_curso."&amp;cod_topico=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\" >"._("TRASH_-1")."</span></li>\n");
}

echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr class=\"head\">\n");

if($dono_portfolio){
	echo("                    <td width=\"2\"><input type=\"checkbox\" id=\"checkMenu\" onclick=\"CheckTodos();\" /></td>\n");
}

/* 82 - Itens */
echo("                    <td class=\"alLeft\">"._("ITEMS_-1")."</td>\n");

/* 112 - Coment?ios */
echo("                    <td width=\"110\" align=\"center\">"._("COMMENTS_-1")."</td>\n");

/* 9 - Data */
echo("                    <td width=\"70\" align=\"center\">"._("DATE_-1")."</td>\n");

/* 119 - Compartilharmento */
echo("                    <td width=\"110\" align=\"center\">"._("ACCESS_MODE_-1")."</td>\n");

echo("                  </tr>\n");

$lista_topicos=Portfolio::RetornaTopicosDoTopico($sock, $cod_curso, $cod_topico_raiz,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);
$lista_itens=Portfolio::RetornaItensDoTopico($sock, $cod_curso, $cod_topico_raiz,$cod_usuario,$eformador,$cod_usuario_portfolio,$cod_grupo_portfolio);

if (((count($lista_topicos)<1)||($lista_topicos=="")) && ((count($lista_itens)<1)||($lista_itens=="")))
{
	echo("                  <tr>\n");
	/* 11 - Não há nenhum item neste portfólio */
	echo("                    <td colspan=\"6\">"._("NO_ITEM_IN_THIS_PORTFOLIO_15")."</td>\n");
	echo("                  </tr>\n");
	echo("                </table>\n");
}
//else = existe um topico ou item no portfolio
else
{
	echo("                </table>\n");
	echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\" id=\"tab_interna\">");
	// definindo qual figura para representar pastas ou arquivos (itens)
	$pasta   = "pasta_";
	$arquivo = "arquivo_";

	// aqui, escolho entre a figura para grupo ou individual
	if ($portfolio_grupo) $gi="g_";
	else $gi="i_";
	$pasta  .= $gi;
	$arquivo.= $gi;

	// aqui, escolho entre pessoal, nao-pessoal ou apagado
	if ($dono_portfolio) $pnx="p.gif";
	else if ($portfolio_apagado) $pnx="x.gif";
	else $pnx="n.gif";
	$pasta  .= $pnx;
	$arquivo.= $pnx;


	$top_index = 0;
	$itens_index = 0;
	for($i=0; $i < ((count($lista_topicos))+(count($lista_itens))); $i++){
		if((!isset($lista_topicos[$top_index]['posicao_topico'])) || (isset($lista_itens[$itens_index]['posicao_item']) &&($lista_topicos[$top_index]['posicao_topico'] > $lista_itens[$itens_index]['posicao_item']))) {
			$lista_unificada[$i] = $lista_itens[$itens_index];
			$itens_index++;
		}else{
			//este if é para não alterar a estrutura dos portfólios antigos
			if((isset($lista_itens[$top_index]['posicao_item'])) && ($lista_topicos[$top_index]['posicao_topico'] == $lista_itens[$itens_index]['posicao_item'])) {
				$lista_itens[$itens_index]['posicao_item']++;
			}
			$lista_unificada[$i] = $lista_topicos[$top_index];
			$top_index++;
		}
	}
	
	$codigos_pastas_fixas = Portfolio::verificaPastasFixas($sock, $cod_usuario, $cod_pasta_raiz);
	
	$pasta_material = $codigos_pastas_fixas['cod_material'];
	
	foreach($lista_unificada as $cod => $linha){
		//se é tópico...
		if(isset($linha['posicao_topico']))
		{
			$data=Data::UnixTime2Data($linha['data']);
	
			if ($dono_portfolio) $varTmp="P";
			else if ($eformador) $varTmp="F";
			else $varTmp="T";
	
			$max_data=Portfolio::RetornaMaiorData($sock,$linha['cod_topico'],$varTmp,$linha['data']);
			$num_comentarios=Portfolio::RetornaNumComentariosTopico($sock,$cod_usuario,$linha['cod_topico'],$varTmp,$linha['data'], $cod_curso);
			if ($data_acesso<$max_data) $marcatr=" class=\"novoitem\"";
			else $marcatr="";
	
			echo("<tr ".$marcatr." id=\"tr_top_".$linha['cod_topico']."\">");
	
			if ($dono_portfolio){
				$check = "<td width=\"5\"><input type=\"checkbox\" id=\"chktop_".$linha['cod_topico']."\" name=\"chkTopico\" onclick=\"VerificaCheck()\" value=\"".$linha['cod_topico']."\" /></td>";
			}
			
			if($dono_portfolio) {
				echo("<td width=\"5\"><input type=\"checkbox\" id=\"chktop_".$linha['cod_topico']."\" name=\"chkTopico\" onclick=\"VerificaCheck()\" value=\"".$linha['cod_topico']."\" /></td>");
			}
	
			echo("<td class=\"itens\"><img alt=\"\" src=\"".$diretorio_imgs.$pasta."\" border=\"0\" /> ");
	
			if ($dono_portfolio){
				$titulo_topico  = "<span class=\"link\" onclick=\"window.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;time=".time()."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\">".$linha['topico']."</span>";
			}else{
				$titulo_topico  = "<span class=\"link\" onclick=\"window.location='portfolio.php?cod_curso=".$cod_curso."&amp;cod_topico_raiz=".$linha['cod_topico']."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."';\">".$linha['topico']."</span>";
			}
	
			//         $num_itens = RetornaNumItensTopicoRec($sock, $linha ['cod_topico'], $dono_portfolio, $eformador);
	
			// 125 - Item
			if ($num_itens==1) $frase=_("ITEM_-1");
			// 82 - Itens
			else $frase=_("ITEMS_-1");
	
			$complemento  = " (".$num_itens." ".$frase.")";
	
			if ($num_itens>0) $itens=$complemento;
			else $itens="";
	
			echo($titulo_topico.$itens."</td>");
	
			echo("<td width=\"110\" align=\"center\">&nbsp;");
			if ($num_comentarios['num_comentarios_alunos']>0)
				echo("<span class=\"cAluno\">(c)</span>");
			if ($num_comentarios['num_comentarios_formadores']>0)
				echo("<span class=\"cForm\">(c)</span>");
			if ($num_comentarios['num_comentarios_usuario']>0)
				echo("<span class=\"cMim\">(c)</span>");
			if ($num_comentarios['data_comentarios']>$data_acesso)
				echo("<span class=\"cNovo\">*</span>");
			echo("</td>");
			echo("<td width=\"70\" align=\"center\"><span>".$data."</span></td>");
			echo("<td width=\"110\">&nbsp;</td>");
	
			echo("</tr>");
		}
		// é item...
		else if(isset($linha['cod_item'])){
	
			$data=Data::UnixTime2Data($linha['data']);
			/* 12 - Totalmente Compartilhado */
			if ($linha['tipo_compartilhamento']=="T"){
				$compartilhamento=_("UNRESTRICTED_ACCESS_MODE_-1");
			}
			/* 13 - Compartilhado com Formadores */
			else if ($linha['tipo_compartilhamento']=="F"){
				$compartilhamento=_("INSTRUCTOR_ACCESS_MODE_-1");
			}
			/* 14 - Compartilhado com o Grupo */
			else if (($portfolio_grupo)&&($linha['tipo_compartilhamento']=="P")){
				$compartilhamento=_("GROUP_ACCESS_MODE_-1");
			}
			/* 15 - Não compartilhado */
			else if (!$portfolio_grupo){
				$compartilhamento=_("NOT_ACCESSIBLE_-1");
			}
	
			// Marca se a linha cont? um item 'novo'
			if ($data_acesso<$linha['data']) $marcatr=" class=\"novoitem\"";
			else $marcatr="";
	
			$lista = NULL;
	
			if ($linha['status']=="E"){
	
				$linha_historico=Portfolio::RetornaUltimaPosicaoHistorico($sock, $linha['cod_item']);
	
				if ($linha['inicio_edicao']<(time()-1800) || $cod_usuario == $linha_historico['cod_usuario'])
				{
					Portfolio::CancelaEdicao($sock, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
					if ($dono_portfolio)
					{
						$titulo="<span id=\"tit_".$linha['cod_item']."\" id=\"titulo_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
						$compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."';AtualizaComp('".$linha['tipo_compartilhamento']."');MostraLayer(cod_comp,140,event);return(false);\">".$compartilhamento."</span>";
					}
					//else = não é dono do portfolio
					else
						$titulo="<span id=\"tit_".$linha['cod_item']."\" id=\"titulo_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
				}
				//else = item está sendo editado
				else
				{
					/* 54 - Em Edição */
					$data="<a href=\"#\" class=\"text\" onclick=\"window.open('em_edicao.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;origem=portfolio&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\">"._("IN_EDITION_-1")."</a>";
					$titulo=$linha['titulo'];
					$marcatr="";
				}
			}
			else
			{
				if ($linha['status'] != "C")
				{
					if ($dono_portfolio)
					{
						$titulo="<span id=\"tit_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\" >".$linha['titulo']."</span>";
						$compartilhamento="<span id=\"comp_".$linha['cod_item']."\" class=\"link\" onclick=\"js_cod_item='".$linha['cod_item']."'; AtualizaComp('".$linha['tipo_compartilhamento']."'); MostraLayer(cod_comp,140,event);\">".$compartilhamento."</span>";
					}
					else
					{
						$titulo="<span id=\"tit_".$linha['cod_item']."\" class=\"link\" onclick=\"window.location='ver.php?cod_curso=".$cod_curso."&amp;cod_item=".$linha['cod_item']."&amp;cod_topico_raiz=".$cod_topico_raiz."&amp;cod_usuario_portfolio=".$cod_usuario_portfolio."&amp;cod_grupo_portfolio=".$cod_grupo_portfolio."'\">".$linha['titulo']."</span>";
					}
				}
			}
	
			if ($linha['status']=="C")
			{
				if ($linha['inicio_edicao']<(time()-1800) || $cod_usuario==$linha['cod_usuario'])
				{
					Portfolio::CancelaEdicao($sock, $linha['cod_item'], $cod_usuario, $cod_curso, $diretorio_arquivos, $diretorio_temp, false, false, false);
				}
			}
			else
			{
				echo("<tr".$marcatr." id=\"tr_".$linha['cod_item']."\">");
	
				if($dono_portfolio) {
					echo("<td width=\"2\"><input type=\"checkbox\" id=\"chkitm_".$linha['cod_item']."\" name=\"chkItem\" value=\"".$linha['cod_item']."\" onclick=\"VerificaCheck()\" /></td>");
				}
	
				$icone = "<img alt=\"\" src=\"".$diretorio_imgs.$arquivo."\" border=\"0\" />";
	
				echo("<td class=\"itens\">".$icone." ".$titulo."</td>");
			}
	
			echo("<td width=\"110\">&nbsp;");
	
			if ($linha['num_comentarios_alunos']>0){
				echo("<span class=\"cAluno\">(c)</span>");
			}
			if ($linha['num_comentarios_formadores']>0){
				echo("<span class=\"cForm\">(c)</span>");
			}
			if ($linha['num_comentarios_usuario']>0){
				echo("<span class=\"cMim\">(c)</span>");
			}
			if ($linha['data_comentarios']>$data_acesso){
				echo("<span class=\"cNovo\">*</span>");
			}
			echo("</td>");
			echo("<td width=\"70\"><span id=\"data_".$linha['cod_item']."\">".$data."</span></td>");
			echo("<td width=\"110\"><span>".$compartilhamento."</span></td>");
			echo("</tr>");
		} //fecha foreach
	}
	echo("</table>\n");
	} //fecha else = existem topicos ou pastas
	
	
	/* 113 - Comentário de Aluno */
	/* 114 - Comentário de Formador */
	/* 115 - Comentários postados por mim */
	/* 141 - Item Avaliado */
	echo("                <span class=\"cAluno\">(c)</span> "._("STUDENT_COMMENT_-1")." - \n");
	echo("                <span class=\"cForm\">(c)</span> "._("INSTRUCTOR_COMMENT_-1")." - \n");
	
	if (!Usuarios::EVisitante($sock,$cod_curso,$cod_usuario))
		echo("                <span class=\"cMim\">(c)</span> "._("COMMENTS_SENT_BY_ME_-1")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n");
	
	echo("              </td>\n");
	echo("            </tr>\n");
	echo("          </table>\n");
	if($dono_portfolio){
		echo("          <ul>\n");
		/* 68 - Excluir selecionados
		 * 69 - Mover selecionados
		 */
		echo("            <li id=\"mExcluir_Selec\" class=\"menuUp\"><span id=\"excluirSelec\">"._("DELETE_SELECTED_-1")."</span></li>\n");
		echo("            <li id=\"mMover_Selec\" class=\"menuUp\"><span id=\"moverSelec\">"._("MOVE_SELECTED_-1")."</span></li>\n");
		echo("          </ul>\n");
	}
	echo("        </td>\n");
	echo("      </tr>\n");
	
	require_once $view_administracao.'tela2.php';
	
	if($dono_portfolio){
		include("".$view_portfolio."layer.php");
	}
	
	echo("    <form name=\"form_dados\" action=\"\" id=\"form_dados\">\n");
	
	echo("      <input type=\"hidden\" name=\"cod_curso\" id=\"cod_curso\" value=\"".$cod_curso."\" />\n");
	echo("      <input type=\"hidden\" name=\"cod_topico_raiz\" value=\"".$cod_topico_raiz."\" />\n");
	echo("      <input type=\"hidden\" name=\"cod_item\" id=\"cod_item\" value=\"\" />\n");
	echo("      <input type=\"hidden\" name=\"acao\" id=\"acao_form\" value=\"\" />\n");
	echo("      <input type=\"hidden\" name=\"cod_topico\" value=\"\" />\n");
	echo("      <input type=\"hidden\" name=\"cod_usuario_portfolio\" value=\"".$cod_usuario_portfolio."\" />\n");
	echo("      <input type=\"hidden\" name=\"cod_grupo_portfolio\" value=\"".$cod_grupo_portfolio."\" />\n");
	echo("      <input type=\"hidden\" name=\"cod_topicos\" id=\"cod_topicos_form\" value=\"\" />\n");
	echo("      <input type=\"hidden\" name=\"cod_itens\" id=\"cod_itens_form\" value=\"\" />\n");
	echo("    </form>\n");
	echo("  </body>\n");
	echo("</html>\n");
	AcessoSQL::Desconectar($sock);
	?>

