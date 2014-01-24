var isNav = (navigator.appName.indexOf("Netscape") !=-1);
var isMinNS6 = ((navigator.userAgent.indexOf("Gecko") != -1) && (isNav));
var isIE = (navigator.appName.indexOf("Microsoft") !=-1);
var Xpos, Ypos;
var js_cod_item=cod_item, js_cod_topico;
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
var lista_frases;
var lista_frases_geral;
var cod_avaliacao="";
var valor_radios = new Array();
xajax_RetornaFraseDinamic('lista_frases');
xajax_RetornaFraseGeralDinamic('lista_frases_geral');

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

function EdicaoTexto(codigo, id, valor){

  if (valor=='ok'){
	  eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');
      xajax_EditarTexto(cod_curso, codigo, conteudo, cod_usuario, lista_frases.msg9);
  }

  xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario);
  
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

    xajax_AbreEdicao(cod_curso,cod_item,cod_usuario);

    if(iframe == null)	
    	conteudo = span.innerHTML;
    else
    { 	
	span.removeChild(iframe);
	conteudo="";		
    }				
    writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);
    startList();
    document.getElementById('text_'+id+'_text').focus();
    cancelarElemento=document.getElementById('CancelaEdita');
    editaTexto++;
  }
  else{
    if(checks.length > 0)
    {	
	/* 29 - A din�mica n�o pode ter texto e arquivos simultaneamente. */ 	
    	alert(lista_frases.msg29);
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
	alert(lista_frases.msg57);
    document.getElementById('input_files').style.visibility='hidden';
    document.getElementById('input_files').value='';
    document.getElementById('divArquivo').className='';
    document.getElementById('divArquivoEdit').className='divHidden';
    
    //Cancela Edição
    input=0;
    cancelarElemento=null;
  }
}

function AcrescentarBarraFile(apaga){

  if (input==1) return;
  CancelaTodos();

  conteudo = document.getElementById('text_'+cod_item).innerHTML;
  if((conteudo != '')&&(document.getElementById('iframe_ArqEntrada') == null)) {
    /* 29 - A din�mica n�o pode ter texto e arquivos simultaneamente. */
    alert(lista_frases.msg29);
    return false;
  }

  document.getElementById('input_files').style.visibility='visible';
  document.getElementById('divArquivoEdit').className='';
  document.getElementById('divArquivo').className='divHidden';

  cancelarElemento=document.getElementById('cancFile');
}

function CancelaTodos(){
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
      if (confirm(lista_frases.msg22+'\n'+lista_frases.msg23+'\n'+lista_frases.msg24)){
        window.location='acoes_linha.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&acao=descompactar&origem='+origem+'&arq='+arqZip;
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
    	
    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_descomp').onclick= function(){  };
    document.getElementById('sArq_entrada').onclick= function(){  };

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
  if (confirm(lista_frases.msg59)){

    xajax_AbreEdicao(cod_curso,cod_item,cod_usuario);

    for (i=0; i<checks.length; i++){
      if(checks[i].checked){
        getNumber=checks[i].id.split("_");
        nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
        verificaEntrada = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqEntrada');
        if(verificaEntrada == 'sim'){
          iframe = document.getElementById('iframe_ArqEntrada');
          iframe.parentNode.removeChild(iframe);
        }
        xajax_ExcluirArquivo(getNumber[1], nomeArq);
	js_conta_arq--;
      }
    }
    mostraFeedback(lista_frases.msg55, 'true');
    xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario);
    
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
  xajax_SelecionarEntradaDinamic(nomesArqs, cod_curso, cod_item, cod_usuario);	
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

        if(iframe = document.getElementById('iframe_ArqEntrada')){
          iframe.parentNode.removeChild(iframe);
          arqentrada = document.getElementById("arq_entrada_"+getNumber[1]);
          arqentrada.parentNode.removeChild(arqentrada);
          document.getElementById("nomeArq_"+getNumber[1]).setAttribute('arqEntrada','nao');
          LimpaBarraArq();
        }
      }

      break;		
    }
  }

  CheckTodos();
  xajax_RetirarEntradaDinamic(nomeArq, cod_curso, cod_item, cod_usuario, lista_frases.msg56);
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

function LimpaBarraArq(){

  var lista = document.getElementById('listFiles');
  if (!js_conta_arq){
    var pai_lista=lista.parentNode;
    var pai_lista2=pai_lista.parentNode;
    i=3;
    do{
      if(!pai_lista.firstChild) break;
      pai_lista.removeChild(pai_lista.firstChild);
      i--;
    }while(i>0);

  }

  document.getElementById('checkMenu').checked=false;
  CheckTodos();
}

function LimpaTexto(id){

  checks = document.getElementsByName('chkArq');

  if ((editaTexto==0)&&(checks.length==0)){
    // 47 - Você tem certeza que deseja apagar este texto?
    if (confirm(lista_frases.msg47)){
      CancelaTodos();
      document.getElementById('text_'+id).innerHTML='';

      // 48 - Texto excluido com sucesso.
      xajax_EditarTexto(cod_curso, id, '', cod_usuario, lista_frases.msg48);
    }
  }
  else{
    if(checks.length > 0)
    {	
	/* 29 - A din�mica n�o pode ter texto e arquivos simultaneamente. */ 	
    	alert(lista_frases.msg29);
    }	
  }
}
