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