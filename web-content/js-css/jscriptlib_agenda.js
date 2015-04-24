var isNav = (navigator.appName.indexOf("Netscape") !=-1);
var isMinNS6 = ((navigator.userAgent.indexOf("Gecko") != -1) && (isNav));
var isIE = (navigator.appName.indexOf("Microsoft") !=-1);
var Xpos, Ypos;
var js_cod_item=cod_item;
var js_cod_topico;
var js_nome_topico;
var js_tipo_item;
var js_total_exc=0;
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
var cod_avaliacao="";
var valor_radios = new Array();
var lista_frases;

$.ajax({
	type: 'post',
	url: '../../../app/geral/models/retorna_frase_dinamic.php',
	async: false,
	success: function(data){
		var lista = $.parseJSON(data);
		lista_frases = lista;
		}
});

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

function WindowOpenVerURL(end)
{
  window.open(end,'PortfolioURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');
}

function EdicaoTexto(codigo, id, valor){
	if (valor=='ok'){
		eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');
	    $.post('../../../app/agenda/models/editar_texto.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, novo_texto:conteudo}, 
	    		function(data){
	    			var code = $.parseJSON(data);
	    		    $('#tr_'+cod_item).toggleClass('novoitem');
	    		    $('#text_'+cod_item).html(code);
	    		    /* 22 - Agenda editada com sucesso.*/
	    		    mostraFeedback(lista_frases.msg22, 'true');
	      });
	 }
	 else{
	      //Cancela Edicao
	      if (!cancelarTodos)
	    	  $.post('../../../app/agenda/models/acaba_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, acao: 0}, 
	    			  function(data){
		    		  	var code = $.parseJSON(data);
		      });
	  }
document.getElementById(id).innerHTML=conteudo;
editaTexto=0;
cancelarElemento=null;
}
	
var controle=0;
	
	
function AlteraTexto(id){
	var iframe = document.getElementById('iframe_ArqEntrada');	
	var span = document.getElementById('text_'+id);	
	
	checks = document.getElementsByName('chkArq');
	
	if ((editaTexto==0)&&(checks.length==0)){
		CancelaTodos();
	
		$.post('../../../app/agenda/models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem:origem}, 
				function(data){
		    		var code = $.parseJSON(data);
		});
		
		if(iframe == null)	
			conteudo = span.innerHTML;
		else
		{ 	
			span.removeChild(iframe);
			conteudo="";		
		}				
		writeRichTextOnJSButtons('text_'+id+'_text', conteudo, 520, 200, true, false, id);
	    startList();
		//document.getElementById('text_'+id+'_text').focus();
		CKEDITOR.on("instanceReady", function(event)
		{
			eval('CKEDITOR.instances.text_'+id+'_text'+'.focus();');
		});
		
		cancelarElemento=document.getElementById('CancelaEdita');
		editaTexto++;
	}
	else{
		if(checks.length > 0)
	    {	
		// 53 - A agenda nao pode ter texto e arquivos simultaneamente! 	
	    	alert(lista_frases.msg53);
	    }	
	}
}
	
function LimpaTexto(id){
	
	checks = document.getElementsByName('chkArq');
	
	if ((editaTexto==0)&&(checks.length==0)){
		// 95 - Você tem certeza que deseja apagar o texto desta agenda?
	    if (confirm(lista_frases.msg95)){
	      CancelaTodos();
	      
	      document.getElementById('text_'+id).innerHTML='';
	
	      $.post('../../../app/agenda/models/editar_texto.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, novo_texto: ' '}, 
	    		  function(data){
	    		    	var code = $.parseJSON(data);
	    		    	$('#tr_'+cod_item).toggleClass('novoitem');
	    		    	$('#text_'+cod_item).html(code);
	    		    	/* Texto excluído com sucesso. */
	    		    	mostraFeedback(lista_frases.msg93, 'true');
	      });
	    }
	  }
	  else{
	    if(checks.length > 0)
	    {	
		// 53 - A agenda nao pode ter texto e arquivos simultaneamente!
	    	alert(lista_frases.msg53);
	    }	
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

function EdicaoArq(i){
  if ((i==1)&&(ArquivoValido(document.getElementById('input_files').value))){ //OK
    document.formFiles.submit();
  }
  else {
	/* Nome do anexo com acentos ou caracteres inválidos! Renomeie o arquivo e tente novamente. */
	alert(lista_frases.msg109);
    document.getElementById('input_files').style.visibility='hidden';
    document.getElementById('input_files').value='';
    document.getElementById('divArquivo').className='';
    document.getElementById('divArquivoEdit').className='divHidden';
    //Cancela Edição
    if (!cancelarTodos)
    	$.post('../../../app/agenda/models/acaba_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, acao: 0}, 
    		    function(data){
    		    	var code = $.parseJSON(data);
      });
    input=0;
    cancelarElemento=null;
  }
}

function AcrescentarBarraFile(apaga){
    
    if (input==1) return;
    CancelaTodos();
    	
    conteudo = document.getElementById('text_'+cod_item).innerHTML;
    if((conteudo != '')&&(document.getElementById('iframe_ArqEntrada') == null)) {
	// 53 - A agenda nao pode ter texto e arquivos simultaneamente! 	
	alert(lista_frases.msg53);
	return false;
    }
						
    document.getElementById('input_files').style.visibility='visible';
    document.getElementById('divArquivoEdit').className='';
    document.getElementById('divArquivo').className='divHidden';
    
    $.post('../../../app/agenda/models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem:origem}, 
    	    function(data){
    	    	var code = $.parseJSON(data);
    });

    cancelarElemento=document.getElementById('cancFile');
}

function CancelaTodos(){
    //EscondeLayers();
    cancelarTodos=1;
     if(cancelarElemento) { cancelarElemento.onclick(); }
    cancelarTodos=0;
  }

function Descompactar(){
  checks = document.getElementsByName('chkArq');
  for (i=0; i<checks.length; i++){
    if(checks[i].checked){
      getNumber=checks[i].id.split("_");
      arqZip=document.getElementById('nomeArq_'+getNumber[1]).getAttribute('arqZip');
      if (confirm(lista_frases.msg12+'\n'+lista_frases.msg13+'\n'+lista_frases.msg14)){
    	  $.post('../../../app/agenda/models/abre_edicao.php',{cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem:origem}, 
    	  function(data){
    		  var code = $.parseJSON(data);
    	  });
        window.location='../../../app/agenda/controllers/acoes_linha.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&acao=descompactar&origem='+origem+'&arq='+arqZip;
      }
    } 
  }

}

function VerificaChkBox(alpha){
  CancelaTodos();
  checks = document.getElementsByName('chkArq');
  var i, j=0;
  var arqComum=0;
  var arqZip=0;
  var arqEntradaCheck=0;
  var arqEntrada=0;
  var pasta=0;

  for (i=0; i<checks.length; i++){
    
    getNumber=checks[i].id.split("_");

    if (document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqEntrada')=='sim'){
         arqEntrada=1;
    }

    if(checks[i].checked){
      j++;
      tipo = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('tipoArq');
      switch (tipo){
        case ('pasta'): pasta=1;break;
        case ('comum'): arqComum++;break;
        case ('zip'): arqZip++;break;
      }
      
      if (document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqEntrada')=='sim'){
         arqEntradaCheck=1;
      }
    }

  }

  if (pasta==1){
    document.getElementById('mArq_apagar').className="menuUp02";
    document.getElementById('mArq_descomp').className="menuUp";
    document.getElementById('mArq_entrada').className="menuUp";
    	
    document.getElementById('mArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('mArq_descomp').onclick= function(){  };
    document.getElementById('mArq_entrada').onclick= function(){  };

  }else if((arqComum==1)&&(arqZip==0)&&(arqEntradaCheck==0)){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_descomp').className="menuUp";
    document.getElementById('mArq_entrada').className="menuUp02";

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_descomp').onclick= function(){  };
    document.getElementById('sArq_entrada').onclick= function(){ SelecionarEntrada(); };
    
  }else if((arqComum==1)&&(arqZip==0)&&(arqEntradaCheck==1)){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_descomp').className="menuUp";
    document.getElementById('mArq_entrada').className="menuUp02";

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_descomp').onclick= function(){  };
    document.getElementById('sArq_entrada').onclick= function(){ RetirarEntrada(); };
    
  }else if((arqComum==1)||(arqZip>1)){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_descomp').className="menuUp";
    document.getElementById('mArq_entrada').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_descomp').onclick= function(){  };
    document.getElementById('sArq_entrada').onclick= function(){  };
    
  }else if(arqComum>1){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_descomp').className="menuUp";
    document.getElementById('mArq_entrada').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_descomp').onclick= function(){  };
    document.getElementById('sArq_entrada').onclick= function(){  };
    
  }else if(arqZip==1){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_descomp').className="menuUp02"
    document.getElementById('mArq_entrada').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_descomp').onclick= function(){ Descompactar() };
    document.getElementById('sArq_entrada').onclick= function(){  };
    
  }else{
    document.getElementById('mArq_apagar').className="menuUp";
    document.getElementById('mArq_descomp').className="menuUp";
    document.getElementById('mArq_entrada').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){  };
    document.getElementById('sArq_descomp').onclick= function(){  };
    document.getElementById('sArq_entrada').onclick= function(){  };
  }

  //Nao foi chamado pela funcao CheckTodos
  if (alpha){
    if (j==checks.length){ document.getElementById('checkMenu').checked=true; }
    else document.getElementById('checkMenu').checked=false;
  }

}

function Apagar(){
  checks = document.getElementsByName('chkArq');
  if (confirm('Deseja realmente apagar o(s) arquivo(s) e/ou a(s) pasta(s) selecionado(s)?')){
    for (i=0; i<checks.length; i++){
      if(checks[i].checked){
        getNumber=checks[i].id.split("_");
        nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
        
        $.post('../../../app/agenda/models/excluir_arquivo.php',{numero: getNumber[1], arq: nomeArq, cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem: origem}, 
    		    function(data){
    		    	$('#arq_'+getNumber[1]).remove();
        });
	js_conta_arq--;
      }
    }
    /* Arquivo(s) excluído(s) com sucesso.*/
    mostraFeedback(lista_frases.msg104, 'true');
  }

  if(document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqEntrada') == 'sim')
  {
    document.getElementById('text_'+cod_item).innerHTML='';
  }

  LimpaBarraArq();
}

function SelecionarEntrada(){
  checks = document.getElementsByName('chkArq');
  var nomesArqs = new Array();

  for (i=0; i<checks.length; i++)
  {
    nomesArqs[i] = new Array();
	
    getNumber=checks[i].id.split("_");	
    nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
    nomesArqs[i][0]=nomeArq;
    
    if(checks[i].checked)
      nomesArqs[i][1]=1; 
    else
      nomesArqs[i][1]=0;
  }

  CheckTodos();
  $.post('../../../app/agenda/models/selecionar_entrada.php',{nomes_arquivos: nomesArqs, cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem: origem}, 
		  function(data){
	  	  	var caminho = $.parseJSON(data);
	  		window.location = caminho;
  });	
}

function RetirarEntrada(){
  checks = document.getElementsByName('chkArq');
  j=0;
  var nomeArq;

  for (i=0; i<checks.length; i++){
    if(checks[i].checked){
      getNumber=checks[i].id.split("_");
      if ((document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqEntrada'))=='sim'){
        nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
      }
// 
      break;
    }
  }

  CheckTodos();
  $.post('../../../app/agenda/models/retirar_entrada.php',{nome_arquivo: nomeArq, cod_curso: cod_curso, cod_item: cod_item, cod_usuario:cod_usuario, origem: origem}, 
		    function(data){
	  			var caminho = $.parseJSON(data);
	  			window.location = caminho;
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

function ApagarItem(){
  CancelaTodos();

  /* Você tem certeza de que deseja apagar esta agenda?
   * (não haverá como recuperá-la!)*/
  if (confirm(lista_frases.msg29+'\n'+lista_frases.msg30)){
        window.location='../../../app/agenda/controllers/acoes_linha.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&acao=apagarItem&origem='+origem;
  }
}

function LimpaBarraArq(){

  lista = document.getElementById('listFiles');
  if (!js_conta_arq){
    pai_lista=lista.parentNode;
    pai_lista2=pai_lista.parentNode;
    i=3;
    do{
      pai_lista.removeChild(pai_lista.firstChild);
      i--;
    }while(i>0);

  }

  document.getElementById('checkMenu').checked=false;
  CheckTodos();
}