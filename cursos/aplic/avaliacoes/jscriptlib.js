var isNav = (navigator.appName.indexOf("Netscape") !=-1);
var isMinNS6 = ((navigator.userAgent.indexOf("Gecko") != -1) && (isNav));
var isIE = (navigator.appName.indexOf("Microsoft") !=-1);
var Xpos, Ypos;
var editaTitulo=0;
var editaTexto=0;
var conteudo="";
var input=0;
var cancelarElemento=null;
var cancelarTodos=0;
var lista_frases_geral;
xajax_RetornaFraseGeralDinamic('lista_frases_geral');


function startList() {
  if (document.all && document.getElementById) {
    nodes = document.getElementsByTagName("span");
    for (i=0; i<nodes.length; i++) {
      node = nodes[i];
      node.onmouseover = function() {
        this.className += "Hover";
      }
      node.onmouseout = function() {
        this.className = this.className.replace("Hover", "");
      }
    }
    nodes = document.getElementsByTagName("li");
    for (i=0; i<nodes.length; i++) {
      node = nodes[i];
      node.onmouseover = function() {
        this.className += "Hover";
      }
      node.onmouseout = function() {
        this.className = this.className.replace("Hover", "");
      }
    }
  }
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

function EdicaoTexto(codigo, id, valor){

  if (valor=='ok'){
	  eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');
      xajax_EditarTexto(cod_curso, cod_atividade, conteudo, cod_usuario, cod_avaliacao, id);
    }
  else{
      //Cancela Edição
      if (!cancelarTodos)
        xajax_AcabaEdicaoDinamic(cod_curso, cod_avaliacao, cod_usuario, 0);
  }
  document.getElementById(id).innerHTML=conteudo;
  editaTexto=0;
  cancelarElemento=null;
}

var controle=0;
function EdicaoCampo(id, tag, valor){
  if ((valor=='ok')&&(document.getElementById(tag+'_'+id+'_text').value!="")&&(VerificaNota(tag,document.getElementById(tag+'_'+id+'_text').value))){
    conteudo = document.getElementById(tag+'_'+id+'_text').value;
    xajax_EditarCampo(cod_curso, conteudo, cod_usuario, ferramenta, cod_atividade, cod_avaliacao, tag);
  }else{
    /* frase #229 - O campo n�o pode ser vazio. */
    if ((valor=='ok')&&(document.getElementById(tag+'_'+id+'_text').value=="")){
    	xajax_AlertaFraseFerramenta(229,22);
    }
    
  	document.getElementById(tag+'_'+id).innerHTML=conteudo;
    document.getElementById(tag+'_'+id).className='';

    
    /*if(navigator.appName.match("Opera")){
      document.getElementById(tag+'_'+id).onclick = AlteraCampo(tag,id);
    }else{
      document.getElementById(tag+'_'+id).onclick = function(){ AlteraCampo(tag,id); };
    }*/
    
    document.getElementById(tag+'_'+id);
    //Cancela Edição
    if (!cancelarTodos){
      xajax_AcabaEdicaoDinamic(cod_curso, cod_avaliacao, cod_usuario, 0);
  	}
  }
  editaTitulo=0;
  cancelarElemento=null;
}

//Chama a Funcao AbreEdicao para saber aonde tem que alterar no BD
//E abre o campo para ser editado.
//cria os links Ok E Cancelar
function AlteraCampo(tag,id){
  if (editaTitulo==0){
	 
    id_aux = id;
    tag_aux = tag;  
    CancelaTodos();
    
    xajax_AbreEdicao(cod_curso, cod_avaliacao, cod_usuario, tela_avaliacao);

    conteudo = document.getElementById(tag+'_'+id).innerHTML;
    document.getElementById(tag+'_'+id).className="";
    document.getElementById('tr_'+id).className="";

    createInput = document.createElement('input');
    document.getElementById(tag+'_'+id).innerHTML='';
    //document.getElementById(tag+'_'+id).onclick=function(){ };
    document.getElementById(tag+'_'+id).setAttribute('onclick', '');

    //cria o campo para digitar o campo
    createInput.setAttribute('type', 'text');
    createInput.setAttribute('style', 'border: 2px solid #9bc');
    createInput.setAttribute('id', tag+'_'+id+'_text');
    createInput.setAttribute('onkeypress', 'EditaTituloEnter(this, event, id_aux, tag_aux)');
    createInput.setAttribute('value', conteudo);
    if(tag == 'valor')
      createInput.setAttribute('size','8%');

    document.getElementById(tag+'_'+id).appendChild(createInput); 
   
    //cria o elemento 'espaco' e adiciona na pagina
    espaco = document.createElement('span');
    espaco.innerHTML='&nbsp;&nbsp;'
    document.getElementById(tag+'_'+id).appendChild(espaco);

    createSpan = document.createElement('span');
    createSpan.className='link';
    createSpan.onclick= function(){ EdicaoCampo(id, tag, 'ok'); }; //TODO
    createSpan.setAttribute('id', 'OkEdita');
    createSpan.innerHTML = lista_frases_geral.msg_ger18;
    document.getElementById(tag+'_'+id).appendChild(createSpan);

    //cria o elemento 'espaco' e adiciona na pagina
    espaco = document.createElement('span');
    espaco.innerHTML='&nbsp;&nbsp;'
    document.getElementById(tag+'_'+id).appendChild(espaco);

    createSpan = document.createElement('span');
    createSpan.className='link';
    createSpan.onclick= function(){ EdicaoCampo(id, tag, 'canc'); };
    createSpan.setAttribute('id', 'CancelaEdita');
    createSpan.innerHTML = lista_frases_geral.msg_ger2;
    document.getElementById(tag+'_'+id).appendChild(createSpan);

    //cria o elemento 'espaco' e adiciona na pagina
    espaco = document.createElement('span');
    espaco.innerHTML='&nbsp;&nbsp;'
    document.getElementById(tag+'_'+id).appendChild(espaco);

    startList();
    cancelarElemento=document.getElementById('CancelaEdita');
    document.getElementById(tag+'_'+id+'_text').select();
    editaTitulo++;
  }
}

function AlteraTexto(id){
  if (editaTexto==0){
    CancelaTodos();

    xajax_AbreEdicao(cod_curso, cod_avaliacao, cod_usuario, tela_avaliacao);
   
    conteudo = document.getElementById('text_'+id).innerHTML;
			
    writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);
    startList();
    //document.getElementById('text_'+id+'_text').focus();
    CKEDITOR.on("instanceReady", function(event)
    		{
    			eval('CKEDITOR.instances.text_'+id+'_text'+'.focus();');
    		});
    cancelarElemento=document.getElementById('CancelaEdita');
    editaTexto++;
  }				
}

function CancelaTodos(){
    cancelarTodos=1;
     if(cancelarElemento) { cancelarElemento.onclick(); }
    cancelarTodos=0;
}