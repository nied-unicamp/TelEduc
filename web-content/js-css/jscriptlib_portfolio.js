var isNav = (navigator.appName.indexOf("Netscape") !=-1);
var isMinNS6 = ((navigator.userAgent.indexOf("Gecko") != -1) && (isNav));
var isIE = (navigator.appName.indexOf("Microsoft") !=-1);
var Xpos, Ypos;
var js_cod_item=cod_item, js_cod_topico;
var js_nome_topico;
var js_tipo_item;
var js_conta_arq=0;
var mostrando=0;
var editando=0;
var js_comp = new Array();
var editaTitulo=0;
var editaTexto=0;
var conteudo="";
var input=0;
var cancelarElemento=null;
var cancelarTodos=0;
var lista_frases;
var cod_avaliacao="";
var valor_radios = new Array();

$.ajax({
	type: 'post',
	url: '../../../app/geral/models/retorna_frase_dinamic.php',
	async: false,
	success: function(data){
		var lista = $.parseJSON(data);
		lista_frases = lista;
		}
});

function OpenWindowPerfil(id)
{
  window.open("../perfil/exibir_perfis.php?cod_curso="+cod_curso+"&cod_aluno[]="+id,"PerfilDisplay","width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes");
  return(false);
 }

function AbreJanelaComponentes(id)
{
  window.open("../grupos/exibir_grupo.php?cod_curso="+cod_curso+"&cod_grupo="+id,"GruposDisplay","width=700,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes");
  return(false);
}


if (isNav)
{
  document.captureEvents(Event.MOUSEMOVE);
}
document.onmousemove = TrataMouse;

function TrataMouse(e)
{
  Ypos = (isMinNS4) ? e.pageY : event.clientY;
  Xpos = (isMinNS4) ? e.pageX : event.clientX;
}

function getPageScrollY()
{
  if (isNav)
    return(window.pageYOffset);
  if (isIE)
    return(document.body.scrollTop);
}

function AjustePosMenuIE()
{
  if (isIE)
    return(getPageScrollY());
  else
    return(0);
}

function EscondeLayers()
{
  hideLayer(cod_comp);
  hideLayer(cod_topicos);
  hideLayer(cod_mover);
  hideLayer(cod_novapasta);
  hideLayer(cod_mover_arquivo);
}

function MostraLayer(cod_layer, ajuste)
{
  CancelaTodos();
  EscondeLayers();
  moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());
  mostrando=1;
  showLayer(cod_layer);
}

function EscondeLayer(cod_layer)
{
  hideLayer(cod_layer);
  mostrando=0;
}

function AtualizaComp(js_tipo_comp)
{
  if ((isNav) && (!isMinNS6)) {
    document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;
    document.comp.document.form_comp.cod_item.value=cod_item;
    var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));
  } else {
    document.form_comp.tipo_comp.value=js_tipo_comp;
    document.form_comp.cod_item.value=cod_item;
    var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));
  }
  var imagem="<img src='../../../web-content/imgs/checkmark_blue.gif'>"
  if (js_tipo_comp=='T') {
    tipo_comp[0].innerHTML=imagem;
    tipo_comp[1].innerHTML="&nbsp;";
    tipo_comp[2].innerHTML="&nbsp;";
  } else if (js_tipo_comp=='F') {
    tipo_comp[0].innerHTML="&nbsp;";
    tipo_comp[1].innerHTML=imagem;
    tipo_comp[2].innerHTML="&nbsp;";
  } else{
    tipo_comp[0].innerHTML="&nbsp;";
    tipo_comp[1].innerHTML="&nbsp;";
    tipo_comp[2].innerHTML=imagem;
  }

  $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
		  function(data){
	  		var code = $.parseJSON(data);
	  		if (code==1){
	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
	  		}
  });
}

function MoverItem(link,cod_destino)
{ 
	$.post('../models/mover_itens.php',{cod_curso: cod_curso, cod_usuario: cod_usuario, cod_topico_raiz: cod_topico_ant, cod_topico_novo: cod_destino, cod_topicos: null, cod_itens: cod_item},
			function(data){
				var code = $.parseJSON(data);
				if (code == 1){
					/* 28 -  Você não pode mover uma pasta para ela mesma ou para uma subpasta dela.*/
					mostraFeedback(lista_frases.msg28, false);
				}
				else if (code == 2){
					Redirecionar(cod_topico_ant, true);
				}
				else if (code == 3){
					/* 71 - Não foi possível mover a pasta, pois já existe uma pasta com mesmo nome no diretório destino. */
					mostraFeedback(lista_frases.msg71, false);
				}
				else if (code == 4){
					Redirecionar(cod_topico_ant, true);
				}
	});
	$.post('../models/acaba_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario, acao: 0},
			  function(data){
		  	  	var code = $.parseJSON(data);
	  });

  if (js_tipo_item=='item')
  {
    window.location='ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_destino+'&cod_topico_ant='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&cod_grupo_portfolio='+cod_grupo_portfolio+'&acao=mover&atualizacao=true';
    return true;
  }
  else
  {

    return false;

  }
}


function WindowOpenVerURL(end)
{
  window.open(end,'PortfolioURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');
}

function EdicaoTexto(codigo, id, valor){

  if (valor=='ok'){
	  eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');
	  $.post('../models/editar_texto.php',{cod_curso: cod_curso, cod_item: codigo, cod_usuario: cod_usuario, novo_nome: conteudo},
			  function(data){
		  	  	$('#tr_'+codigo).addClass('novoitem');
		  	  	$('#text_'+codigo).html(conteudo);
		  	  	/* Item editado com sucesso. */
		  	  	mostraFeedback(lista_frases.msg49, true);
	  });
    }
  else{
      //Cancela Edicao
      if (!cancelarTodos)
    	  $.post('../models/acaba_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario, acao: 0},
    			  function(data){
    		  	  	var code = $.parseJSON(data);
    	  });
  }
  document.getElementById(id).innerHTML=conteudo;
  editaTexto=0;
  cancelarElemento=null;
}

var controle=0;

function EdicaoTitulo(codigo, id, valor){
  if ((valor=='ok')&&(document.getElementById(id+'_text').value!="")){
    conteudo = document.getElementById(id+'_text').value;
    
    $.post('../models/editar_titulo.php',{cod_curso: cod_curso, cod_item: codigo, cod_usuario: cod_usuario, novo_nome: conteudo},
			  function(data){
		  	  	$('#tr_'+codigo).addClass('novoitem');
		  	  	$('#tit_'+codigo).html(conteudo);
		  	  	$('#renomear_'+codigo).click(function (){
		  	  		AlteraTitulo(codigo);
		  	  	});
		  	  	/* Item renomeado com sucesso. */
		  	  	mostraFeedback(lista_frases.msg196, true);
	  });
  }else{
    /* 36 - O titulo nao pode ser vazio. */
    if ((valor=='ok')&&(document.getElementById(id+'_text').value==""))
      /* O título não pode ser vazio.*/	
      alert(lista_frases.msg36);

    document.getElementById(id).innerHTML=conteudo;

    if(navigator.appName.match("Opera")){
      document.getElementById('renomear_'+codigo).onclick = AlteraTitulo(codigo);
    }else{
      document.getElementById('renomear_'+codigo).onclick = function(){ AlteraTitulo(codigo); };
    }

    //Cancela Edição
    if (!cancelarTodos)
    	$.post('../models/acaba_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario, acao: 0},
  			  function(data){
  		  	  	var code = $.parseJSON(data);
  	  });
  }
  editaTitulo=0;
  cancelarElemento=null;
}


function LimparTexto(id)
{
  /* Você tem certeza de que deseja apagar o conteúdo do texto?*/
  if (confirm(lista_frases.msg188))
    {
	  $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
			  function(data){
		  		var code = $.parseJSON(data);
		  		if (code==1){
		  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
		  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
		  		}
	  }); 
	  
      document.getElementById('text_'+id).innerHTML='';
      $.post('../models/editar_texto.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario, novo_nome: ''},
 			  function(data){
 		  	  	$('#tr_'+cod_item).addClass('novoitem');
 		  	  	$('#text_'+cod_item).html('');
 		  	  	/* Texto excluído com sucesso. */
 		  	  	mostraFeedback(lista_frases.msg208, true);
 	  });
    }
}

function AlteraTexto(id){

  if (editaTexto==0){
    CancelaTodos();

    $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
  		  function(data){
  	  		var code = $.parseJSON(data);
  	  		if (code==1){
  	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
  	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
  	  		}
    });
    conteudo = document.getElementById('text_'+id).innerHTML;
    writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);
    startList();
    //document.getElementById('text_'+id+'_text').focus();
    cancelarElemento=document.getElementById('CancelaEdita');
    editaTexto++;
  }
}

function ArquivoValido(path)
{
	var file = getfilename(path);
	var vet  = file.match(/^[A-Za-z0-9-\.\_\ ]+/);

	// Usando expressão regular para identificar caracteres inválidos
	if ((file.length == 0) || (vet == null) || (file.length != vet[0].length))
		return false;
	return true;
}

function getfilename(path)
{
  pieces=path.split('\\');
  n=pieces.length;
  file=pieces[n-1];
  pieces=file.split('/');
  n=pieces.length;
  file=pieces[n-1];
  return(file);
}

function EdicaoArq(i, msg){
  var filename = document.getElementById('input_files').value;
  filename = filename.replace("C:\\fakepath\\", "");
  if ((i==1) && ArquivoValido(filename)) { //OK
    document.formFiles.submit();
  }
  else {
	/* Nome do anexo com acentos ou caracteres inválidos! Renomeie o arquivo e tente novamente.*/
    alert(lista_frases.msg216);
    document.getElementById('input_files').style.visibility='hidden';
    document.getElementById('input_files').value='';
    document.getElementById('divArquivo').className='';
    document.getElementById('divArquivoEdit').className='divHidden';
    //Cancela Edição
    if (!cancelarTodos)
      xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
    input=0;
    cancelarElemento=null;
  }
}

function AcrescentarBarraFile(apaga){
    if (input==1) return;
    CancelaTodos();
    document.getElementById('input_files').style.visibility='visible';
    document.getElementById('divArquivoEdit').className='';
    document.getElementById('divArquivo').className='divHidden';
    $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
  		  function(data){
  	  		var code = $.parseJSON(data);
  	  		if (code==1){
  	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
  	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
  	  		}
    });

    cancelarElemento=document.getElementById('cancFile');
}

  function AdicionaInputEndereco(){
    CancelaTodos();
    document.getElementById('novoEnd').style.visibility='visible';
    document.getElementById('novoNomeEnd').style.visibility='visible';
    document.getElementById('divEndereco').className='divHidden';
    document.getElementById('divEnderecoEdit').className='';
    $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
    		  function(data){
    	  		var code = $.parseJSON(data);
    	  		if (code==1){
    	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
    	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
    	  		}
      });
    cancelarElemento=document.getElementById('cancelaEnd');
  }

function EditaEndereco(opt){
    if (opt){
      if (document.getElementById('novoEnd').value==''){
    	  $.post('../models/acaba_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario, acao: 0},
    			  function(data){
    		  	  	var code = $.parseJSON(data);
    	  });
    	/* Pelo menos o endereço deve ser preenchido!*/
        alert(lista_frases.msg64);
        return false;
      }
      $.ajax({
    	  type: 'post',
    	  async: false,
    	  url: '../models/insere_endereco.php',
    	  data: {nome: document.getElementById('novoNomeEnd').value, endereco: document.getElementById('novoEnd').value, cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
    	  success: function(data) {
    		 var lista = $.parseJSON(data);
			  		$('#listaEnderecos').append('<span id=end_'+lista.cod_endereco+">");
			  		$('#end_'+lista.cod_endereco).append('<span id=link_'+lista.cod_endereco+">");
			  		$('#link_'+lista.cod_endereco).toggleClass('link');
			  		
			  		if (lista.num==0){
			  			if ((document.getElementById('novoNomeEnd').value) != ""){
	    	  				$('#link_'+lista.cod_endereco).html(document.getElementById('novoNomeEnd').value);
	    	  				$('<span id=endEndereco_'+lista.cod_endereco+">").insertAfter('#link_'+lista.cod_endereco);
	    	  				$('#endEndereco_'+lista.cod_endereco).html("&nbsp;&nbsp;("+lista.url_valida+") - ");
			  			}else{
			  				$('#link_'+lista.cod_endereco).html(lista.url_valida);
			  			}
			  		}
    	  			$('#link_'+lista.cod_endereco).click(function(){
    	  				WindowOpenVerURL(lista.url_valida_space);
    	  				return(false);
    	  			});
    	  			$('#end_'+lista.cod_endereco).append('<span id=endApagar_'+lista.cod_endereco+">");
    	  			$('#endApagar_'+lista.cod_endereco).addClass('link');
    	  			$('#endApagar_'+lista.cod_endereco).html('Apagar<br>');
    	  			$('#endApagar_'+lista.cod_endereco).click(function(){
    	  					ApagarEndereco(cod_curso, lista.cod_endereco);
    	  			});
    	  			
    	  			mostraFeedback(lista_frases.msg67, true);
    	  	}

	});
    }else{
      if (!cancelarTodos)
    	  $.post('../models/acaba_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario, acao: 0},
    			  function(data){
    		  	  	var code = $.parseJSON(data);
    	  });
    }

    document.getElementById('novoEnd').style.visibility='hidden';
    document.getElementById('novoNomeEnd').style.visibility='hidden';
    document.getElementById('novoEnd').value='';
    document.getElementById('novoNomeEnd').value='';
    document.getElementById('divEnderecoEdit').className='divHidden';
    document.getElementById('divEndereco').className='';

    cancelarElemento=null;
  }

function CancelaTodos(){
    EscondeLayers();
    cancelarTodos=1;
    if(cancelarElemento) { 
      cancelarElemento.onclick(); 
      xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
    }
    cancelarTodos=0;
  }

function ApagarEndereco(cod_curso, cod_endereco){
  CancelaTodos();
  /* Tem certeza que deseja apagar este endereço? */
  if (confirm(lista_frases.msg32)){
	  $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
	  		  function(data){
	  	  		var code = $.parseJSON(data);
	  	  		if (code==1){
	  	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
	  	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
	  	  		}
	  });
	  
	  $.post('../models/excluir_endereco.php',{cod_curso: cod_curso, cod_endereco: cod_endereco,cod_item: cod_item, cod_usuario: cod_usuario},
	  		  function(data){
		  		$("#end_"+cod_endereco).remove();
		  		
		  		/* Endereço removido com sucesso. */
		  		mostraFeedback(lista_frases.msg197, true);
	});
  }
}

function Descompactar(){
  checks = document.getElementsByName('chkArq');
  for (i=0; i<checks.length; i++){
    if(checks[i].checked){
      getNumber=checks[i].id.split("_");
      arqZip=document.getElementById('nomeArq_'+getNumber[1]).getAttribute('arqZip');
      if (confirm(lista_frases.msg33+'\n'+lista_frases.msg34+'\n'+lista_frases.msg35)){
    	  $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
    	  		  function(data){
    	  	  		var code = $.parseJSON(data);
    	  	  		if (code==1){
    	  	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
    	  	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
    	  	  		}
    	    });
        window.location='../controllers/acoes.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=descompactar&arq='+arqZip;
      }
    } 
  }

}

function Apagar(){
  checks = document.getElementsByName('chkArq');
  /* Deseja realmente apagar o(s) arquivo(s) e/ou a(s) pasta(s) selecionado(s)?*/
  if (confirm(lista_frases.msg210)){
	  $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
	  		  function(data){
	  	  		var code = $.parseJSON(data);
	  	  		if (code==1){
	  	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
	  	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
	  	  		}
	  });
    for (i=0; i<checks.length; i++){
      if(checks[i].checked){
        getNumber=checks[i].id.split("_");
        nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
        $.post('../models/excluir_arquivo.php',{numero: getNumber[1], arq: nomeArq, cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
  	  		  function(data){
  	  	  			$('#arq_'+getNumber[1]).remove();
  	  	  			/* Arquivo(s) apagado(s) com sucesso. */
  	  	  			mostraFeedback(lista_frases.msg198, 'true');
  	    });
  
        js_conta_arq--;
      }
    }
    LimpaBarraArq();
    VerificaChkBox(0);
  }
}

function Ocultar(){
  checks = document.getElementsByName('chkArq');
  j=0;
  var nomesArqs = new Array();

  for (i=0; i<checks.length; i++){
    if(checks[i].checked){

      getNumber=checks[i].id.split("_");
      if ((document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqOculto'))=='nao'){
        nomesArqs[j] = new Array();
  
        nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
        nomesArqs[j][0]=nomeArq;
        nomesArqs[j][1]=getNumber[1];
        j++;
      }

    }
  }
  
  $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
  		  function(data){
  	  		var code = $.parseJSON(data);
  	  		if (code==1){
  	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
  	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
  	  		}
  });
  
  $.post('../models/ocultar_arquivos.php',{nomes_arquivos: nomesArqs, cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
	  		  function(data){
		  		  $.each(nomesArqs, function(key, value){
		  			$('#local_oculto_'+value[1]).append('<span id=arq_oculto_'+value[1]+">");
		  			$('#arq_oculto_'+value[1]).append('<span id=arq_oculto_in1_'+value[1]+">");
		  			$('#arq_oculto_in1_'+value[1]).html('&nbsp;- ');
		  			
		  			$('#arq_oculto_'+value[1]).append('<span id=arq_oculto_in2_'+value[1]+">");
		  			/* oculto */
		  			$('#arq_oculto_in2_'+value[1]).html(lista_frases.msg118);
		  			$('#arq_oculto_in2_'+value[1]).addClass('arqOculto');
		  			
		  			$('#nomeArq_'+value[1]).addClass('arqOculto = sim');
		  		  });
	  		  
	  		  $('#sArq_ocultar').on("click", function() {
	  			Desocultar();
	  		  });
	  		  
	  		  LimpaBarraArq();
	  		
	  		  /* Arquivo(s) ocultado(s) com sucesso. */
	  		  mostraFeedback(lista_frases.msg199, true);
	});
  }
  
function Desocultar(){
  checks = document.getElementsByName('chkArq');
  j=0;
  var nomesArqs = new Array();

  for (i=0; i<checks.length; i++){
    if(checks[i].checked){
      getNumber=checks[i].id.split("_");
      if ((document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqOculto'))=='sim'){

        nomesArqs[j] = new Array();
        nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
        nomesArqs[j][0]=nomeArq;
        nomesArqs[j][1]=getNumber[1];
        j++;
      }
    }
  }
  $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
  		  function(data){
  	  		var code = $.parseJSON(data);
  	  		if (code==1){
  	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
  	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
  	  		}
  });
  
  $.post('../models/desocultar_arquivos.php',{nomes_arquivos: nomesArqs, cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
  		  function(data){
	  		  $.each(nomesArqs, function(key, value){
	  			$('#arq_oculto_'+value[1]).remove();
	  			$('#nomeArq_'+value[1]).addClass('arqOculto = nao');
	  		  });
  		  
  		  $('#sArq_ocultar').on("click", function() {
  			Ocultar();
  		  });
  		  
  		LimpaBarraArq();
  		
  		mostraFeedback(lista_frases.msg200, true);
  });
}

function CheckTodos(){
  var e;
  var i;
  var CabMarcado = document.getElementById('checkMenu').checked;
  var checks=document.getElementsByName('chkArq');
  for(i = 0; i < checks.length; i++)
  {
    e = checks[i];
    e.checked = CabMarcado;
  }

  VerificaChkBox(0);
}

function Mover(caminhoDestino){

  checks = document.getElementsByName('chkArq');
  for (i=0; i<checks.length; i++){
    if(checks[i].checked){
      numeroArq= checks[i].getAttribute('id');
      numeroArq = numeroArq.split('_');
      IdArquivo = 'nomeArq_'+numeroArq[1];
      caminhoOrigem = document.getElementById(IdArquivo).getAttribute('nomeArq');
      $.post('../models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
      		  function(data){
      	  		var code = $.parseJSON(data);
      	  		if (code==1){
      	  			window.open('em_edicao.php?cod_curso=cod_curso&cod_item=cod_item&origem=ver','EmEdicao','width=300,height=240,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');
      	  			document.location='portfolio.php?cod_usuario=cod_usuario&cod_curso=cod_curso&cod_item=cod_item&origem=ver&cod_usuario_portfolio=cod_usuario_portfolio&cod_grupo_portfolio=cod_grupo_portfolio&cod_topico_raiz=cod_topico_raiz';
      	  		}
        });
      
      $.post('../models/mover_arquivos.php',{origem: caminhoOrigem, destino: caminhoDestino, cod_curso: cod_curso, cod_item: cod_item, cod_usuario: cod_usuario},
  			function(data){
  				var code = $.parseJSON(data);
  				if (code == 1){
  					EscondeLayers();
  					/* Houve um erro ao atualizar o material.*/
  					mostraFeedback(lista_frases.msg63, false);
  				}
  				else if (code == 2){
  					EscondeLayers();
  					window.location='../views/ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_raiz+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=moverarquivos&atualizacao=true';
  				}
  				window.location='../views/ver.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_raiz+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=moverarquivos&atualizacao=true';
  	  });
    }
  }

}

function ApagarItem(){
  CancelaTodos();
  /* Você tem certeza de que deseja apagar este item?
   * (Os itens serão movidos para a lixeira)*/
  if (confirm(lista_frases.msg18+'\n'+lista_frases.msg179)){
        window.location='acoes.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=apagarItem';
  }
}

function LimpaBarraArq(){

  lista = document.getElementById("listFiles");
  if (!js_conta_arq){
    pai_lista=lista.parentNode;
    pai_lista2=pai_lista.parentNode;
    i=3;
    do{
      if (pai_lista.firstChild)
        pai_lista.removeChild(pai_lista.firstChild);
      i--;
    }while(i>0);

  }

  document.getElementById('checkMenu').checked=false;
  CheckTodos();
}

function DesmarcaRadios(){
  radios = document.getElementsByName('cod_avaliacao');
  for (i=0; i<radios.length; i++){
    radios[i].checked=false;
  }
  cod_avaliacao='';
}

