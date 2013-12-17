var isNav = (navigator.appName.indexOf("Netscape") !=-1);
var isMinNS6 = ((navigator.userAgent.indexOf("Gecko") != -1) && (isNav));
var isIE = (navigator.appName.indexOf("Microsoft") !=-1);
var Xpos, Ypos;
var cod_curso = 1, cod_questao = 3;
var js_nome_topico;
var js_tipo_item;
var js_conta_arq=0;
var mostrando=0;
var editando=0;
var js_comp = new Array();
var lista_frases;
var lista_frases_geral;
var cod_avaliacao="";
var valor_radios = new Array();
//xajax_RetornaFraseDinamic('lista_frases');
//xajax_RetornaFraseGeralDinamic('lista_frases_geral');

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

function AtualizaComp(js_tipo_comp)
{
  if ((isNav) && (!isMinNS6)) {
    document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;
    document.comp.document.form_comp.cod_item.value=js_cod_item;
    var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));
  } else {
    document.form_comp.tipo_comp.value=js_tipo_comp;
    document.form_comp.cod_item.value=js_cod_item;
    var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));
  }
  var imagem="<img src='../imgs/checkmark_blue.gif'>";
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

  xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
}

function MoverItem(link,cod_destino)
{ 
  xajax_MoverItensDinamic(cod_curso, cod_usuario, cod_topico_ant, cod_destino, null, cod_item);
  xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 1);

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

var controle=0;

  function AdicionaInputEndereco(){
    CancelaTodos();
    document.getElementById('novoEnd').style.visibility='visible';
    document.getElementById('novoNomeEnd').style.visibility='visible';
    document.getElementById('divEndereco').className='divHidden';
    document.getElementById('divEnderecoEdit').className='';
    xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
    cancelarElemento=document.getElementById('cancelaEnd');
  }

function EditaEndereco(opt){
    if (opt){
      if (document.getElementById('novoEnd').value==''){
        xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
        alert(lista_frases.msg64);
        return false;
      }
      xajax_InsereEnderecoDinamic(document.getElementById('novoNomeEnd').value, document.getElementById('novoEnd').value, cod_item, cod_curso, cod_usuario, lista_frases.msg67);
    }else{
      if (!cancelarTodos)
        xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
    }

    document.getElementById('novoEnd').style.visibility='hidden';
    document.getElementById('novoNomeEnd').style.visibility='hidden';
    document.getElementById('novoEnd').value='';
    document.getElementById('novoNomeEnd').value='';
    document.getElementById('divEnderecoEdit').className='divHidden';
    document.getElementById('divEndereco').className='';

    cancelarElemento=null;
  }

function ApagarEndereco(cod_curso, cod_endereco){
  CancelaTodos();
  if (confirm(lista_frases.msg32)){
    xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
    xajax_ExcluirEndereco(cod_curso, cod_endereco, cod_item, cod_usuario, lista_frases.msg197);
  }
}

function VerificaChkBoxArq(alpha){
  CancelaTodos();
  checks = document.getElementsByName('chkArq');
  var i, j=0;
  var arqComum=0;
  var arqZip=0;
  var arqOculto=0;
  var pasta=0;

  for (i=0; i<checks.length; i++){
    if(checks[i].checked){
      j++;
      getNumber=checks[i].id.split("_");
      tipo = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('tipoArq');
      switch (tipo){
        case ('pasta'): pasta=1;break;
        case ('comum'): arqComum++;break;
        case ('zip'): arqZip++;break;
      }

      if (document.getElementById("nomeArq_"+getNumber[1]).getAttribute('arqOculto')=='sim'){
         arqOculto++;
      }

    }
  }

  if (pasta==1){
    document.getElementById('mArq_apagar').className="menuUp02";
    document.getElementById('mArq_ocultar').className="menuUp";

    document.getElementById('mArq_apagar').onclick= function(){ ApagarArq(); };
    document.getElementById('mArq_ocultar').onclick= function(){  };

  }else if((arqComum==1)||(arqZip>1)){
    document.getElementById('mArq_apagar').className="menuUp02";
    document.getElementById('mArq_ocultar').className="menuUp02";

    document.getElementById('sArq_apagar').onclick= function(){ ApagarArq(); };
    document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };
  }else if(arqComum>1){
    document.getElementById('mArq_apagar').className="menuUp02";
    document.getElementById('mArq_ocultar').className="menuUp02";

    document.getElementById('sArq_apagar').onclick= function(){ ApagarArq(); };
    document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };
  }else if(arqZip==1){
    document.getElementById('mArq_apagar').className="menuUp02";
    document.getElementById('mArq_ocultar').className="menuUp02";

    document.getElementById('sArq_apagar').onclick= function(){ ApagarArq(); };
    document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };
  }else{
    document.getElementById('mArq_apagar').className="menuUp";
    document.getElementById('mArq_ocultar').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){  };
    document.getElementById('sArq_ocultar').onclick= function(){  };
  }

  //todos arquivos selecionados sao ocultos
  if ((j==arqOculto)&&(j!=0)) {
      document.getElementById('sArq_ocultar').onclick= function(){ Desocultar(); };

  }

  //Nao foi chamado pela funcao CheckTodos
  if (alpha){
    if (j==checks.length){ document.getElementById('checkMenuArq').checked=true; }
    else document.getElementById('checkMenuArq').checked=false;
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

  xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
  xajax_OcultarArquivosDinamic(nomesArqs, lista_frases.msg118, cod_curso, cod_item, cod_usuario, lista_frases.msg199);
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
  xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
  xajax_DesocultarArquivosDinamic(nomesArqs, cod_curso, cod_item, cod_usuario, lista_frases.msg200);
}

function ApagarItem(){
  CancelaTodos();
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

  document.getElementById('checkMenuArq').checked=false;
  CheckTodos(1);
}

function AssociarAvaliacao(){
  CancelaTodos();
  radios=document.getElementsByName('cod_avaliacao');
  for (i=0; i<radios.length; i++){
    valor_radios[i]=radios[i].checked;
  }
  document.getElementById('tableAvaliacao').style.visibility='visible';
  document.getElementById('divAvaliacao').className='divHidden';
  document.getElementById('divAvaliacaoEdit').className='';

  xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);

  cancelarElemento=document.getElementById('cancAval');

}

function EditaAval(opt){
  //document.getElementById('tableAvaliacao').style.visibility='hidden';
  document.getElementById('divAvaliacao').className='';
  //document.getElementById('divAvaliacaoEdit').className='divHidden';
  if (opt){
    xajax_AssociaAvaliacaoDinamic(cod_curso, cod_usuario, cod_item, cod_avaliacao, lista_frases_geral.msg_ger35, lista_frases.msg212, lista_frases.msg220);
  }else{
    radios = document.getElementsByName('cod_avaliacao');
    for (i=0; i<radios.length; i++){
      radios[i].checked=valor_radios[i].checked;
    }
    if(!cancelarTodos)
      xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
  }

  cancelarElemento=null;
}