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
var lista_frases_geral;
var cod_avaliacao="";
var valor_radios = new Array();
xajax_RetornaFraseDinamic('lista_frases');
xajax_RetornaFraseGeralDinamic('lista_frases_geral');

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
    document.comp.document.form_comp.cod_item.value=js_cod_item;
    var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));
  } else {
    document.form_comp.tipo_comp.value=js_tipo_comp;
    document.form_comp.cod_item.value=js_cod_item;
    var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));
  }
  var imagem="<img src='../imgs/checkmark_blue.gif'>"
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

function EdicaoTexto(codigo, id, valor){

  if (valor=='ok'){
      conteudo=document.getElementById(id+'_text').contentWindow.document.body.innerHTML
      xajax_EditarTexto(cod_curso, codigo, conteudo, cod_usuario, lista_frases.msg49);
    }
  else{
      //Cancela Edi�o
      if (!cancelarTodos)
        xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
  }
  document.getElementById(id).innerHTML=conteudo;
  editaTexto=0;
  cancelarElemento=null;
}

var controle=0;

function EditaTituloEnter(campo, evento, id)
{
  var tecla;
  CheckTAB=true;
  if(navigator.userAgent.indexOf("MSIE")== -1)
  {
    tecla = evento.which;
  }
  else
  {
    tecla = evento.keyCode;
  }
  if ( tecla == 13 )
  {
    EdicaoTitulo(id,'tit_'+id,'ok');
  }
  return true;
}

function EdicaoTitulo(codigo, id, valor){
  if ((valor=='ok')&&(document.getElementById(id+'_text').value!="")){
    conteudo = document.getElementById(id+'_text').value;
    xajax_EditarTitulo(cod_curso, codigo, conteudo, cod_usuario, lista_frases.msg196);
  }else{
    /* 36 - O titulo nao pode ser vazio. */
    if ((valor=='ok')&&(document.getElementById(id+'_text').value==""))
      alert(lista_frases.msg36);

    document.getElementById(id).innerHTML=conteudo;

    if(navigator.appName.match("Opera")){
      document.getElementById('renomear_'+codigo).onclick = AlteraTitulo(codigo);
    }else{
      document.getElementById('renomear_'+codigo).onclick = function(){ AlteraTitulo(codigo); };
    }

    //Cancela Edição
    if (!cancelarTodos)
      xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
  }
  editaTitulo=0;
  cancelarElemento=null;
}

function AlteraTitulo(id){
  if (editaTitulo==0){
    CancelaTodos();

    id_aux = id;

    xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);

    conteudo = document.getElementById('tit_'+id).innerHTML;
    document.getElementById('tit_'+id).className="";
    document.getElementById('tr_'+id).className="";

    createInput = document.createElement('input');
    document.getElementById('tit_'+id).innerHTML='';
    document.getElementById('tit_'+id).onclick=function(){ };

    createInput.setAttribute('type', 'text');
    createInput.setAttribute('style', 'border: 2px solid #9bc');
    createInput.setAttribute('id', 'tit_'+id+'_text');
//     createInput.onkeypress = function(event) {EditaTituloEnter(this, event, id_aux);}
    if (createInput.addEventListener){; //not IE
    createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);
    } else if (createInput.attachEvent){; //IE
    createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});
    };

    document.getElementById('tit_'+id).appendChild(createInput);    
    xajax_DecodificaString('tit_'+id+'_text', conteudo, 'value');

    //cria o elemento 'espaco' e adiciona na pagina
    espaco = document.createElement('span');
    espaco.innerHTML='&nbsp;&nbsp;'
    document.getElementById('tit_'+id).appendChild(espaco);

    createSpan = document.createElement('span');
    createSpan.className='link';
    createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };
    createSpan.setAttribute('id', 'OkEdita');
    createSpan.innerHTML=lista_frases_geral.msg_ger18;
    document.getElementById('tit_'+id).appendChild(createSpan);

    //cria o elemento 'espaco' e adiciona na pagina
    espaco = document.createElement('span');
    espaco.innerHTML='&nbsp;&nbsp;'
    document.getElementById('tit_'+id).appendChild(espaco);

    createSpan = document.createElement('span');
    createSpan.className='link';
    createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };
    createSpan.setAttribute('id', 'CancelaEdita');
    createSpan.innerHTML=lista_frases_geral.msg_ger2;
    document.getElementById('tit_'+id).appendChild(createSpan);

    //cria o elemento 'espaco' e adiciona na pagina
    espaco = document.createElement('span');
    espaco.innerHTML='&nbsp;&nbsp;'
    document.getElementById('tit_'+id).appendChild(espaco);

    startList();
    cancelarElemento=document.getElementById('CancelaEdita');
    document.getElementById('tit_'+id+'_text').select();
    editaTitulo++;
  }
}

function LimparTexto(id)
{
  if (confirm(lista_frases.msg188));
    {
       xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
       document.getElementById('text_'+id).innerHTML='';
       xajax_EditarTexto(cod_curso,cod_item,'',cod_usuario, lista_frases.msg208);
    }
}

function AlteraTexto(id){

  if (editaTexto==0){
    CancelaTodos();

    xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
    conteudo = document.getElementById('text_'+id).innerHTML;
    writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);
    startList();
    document.getElementById('text_'+id+'_text').focus();
    cancelarElemento=document.getElementById('CancelaEdita');
    editaTexto++;
  }
}

  function ArquivoValido(path)
  {
    var file=getfilename(path);
    var n=file.length;
    if (n==0) return (false);
    for(i=0; i<=n; i++) {
      if ((file.charAt(i)=="'") || (file.charAt(i)=="#") || (file.charAt(i)=="%") || (file.charAt(i)=="?") || (file.charAt(i)=="/")) {
        return(false);
      }
    }    return(true);
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
  if ((i==1)&&(ArquivoValido(document.getElementById('input_files').value))){ //OK
    document.formFiles.submit();
  }
  else {
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
    xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);

    cancelarElemento=document.getElementById('cancFile');
}

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
  if (confirm(lista_frases.msg32)){
    xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
    xajax_ExcluirEndereco(cod_curso, cod_endereco, cod_item, cod_usuario, lista_frases.msg197);
  }
}

function Descompactar(){
  checks = document.getElementsByName('chkArq');
  for (i=0; i<checks.length; i++){
    if(checks[i].checked){
      getNumber=checks[i].id.split("_");
      arqZip=document.getElementById('nomeArq_'+getNumber[1]).getAttribute('arqZip');
      if (confirm(lista_frases.msg33+'\n'+lista_frases.msg34+'\n'+lista_frases.msg35)){
        xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
        window.location='acoes.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&cod_topico_raiz='+cod_topico_ant+'&cod_usuario_portfolio='+cod_usuario_portfolio+'&acao=descompactar&arq='+arqZip;
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
    document.getElementById('mArq_mover').className="menuUp";
    document.getElementById('mArq_descomp').className="menuUp";

    document.getElementById('mArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('mArq_ocultar').onclick= function(){  };
    document.getElementById('mArq_mover').onclick= function(){  };
    document.getElementById('mArq_descomp').onclick= function(){  };

  }else if((arqComum==1)||(arqZip>1)){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_ocultar').className="menuUp02"
    document.getElementById('mArq_mover').className="menuUp02"
    document.getElementById('mArq_descomp').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };
    document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };
    document.getElementById('sArq_descomp').onclick= function(){  };
  }else if(arqComum>1){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_ocultar').className="menuUp02"
    document.getElementById('mArq_mover').className="menuUp"
    document.getElementById('mArq_descomp').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };
    document.getElementById('sArq_mover').onclick= function(){  };
    document.getElementById('sArq_descomp').onclick= function(){  };
  }else if(arqZip==1){
    document.getElementById('mArq_apagar').className="menuUp02"
    document.getElementById('mArq_ocultar').className="menuUp02"
    document.getElementById('mArq_mover').className="menuUp02"
    document.getElementById('mArq_descomp').className="menuUp02"

    document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
    document.getElementById('sArq_ocultar').onclick= function(){ Ocultar(); };
    document.getElementById('sArq_mover').onclick= function(){  MostraLayer(cod_mover_arquivo,140); };
    document.getElementById('sArq_descomp').onclick= function(){ Descompactar() };
  }else{
    document.getElementById('mArq_apagar').className="menuUp";
    document.getElementById('mArq_ocultar').className="menuUp";
    document.getElementById('mArq_mover').className="menuUp";
    document.getElementById('mArq_descomp').className="menuUp";

    document.getElementById('sArq_apagar').onclick= function(){  };
    document.getElementById('sArq_ocultar').onclick= function(){  };
    document.getElementById('sArq_mover').onclick= function(){  };
    document.getElementById('sArq_descomp').onclick= function(){  };
  }

  //todos arquivos selecionados sao ocultos
  if ((j==arqOculto)&&(j!=0)) {
      document.getElementById('sArq_ocultar').onclick= function(){ Desocultar(); };

  }

  //Nao foi chamado pela funcao CheckTodos
  if (alpha){
    if (j==checks.length){ document.getElementById('checkMenu').checked=true; }
    else document.getElementById('checkMenu').checked=false;
  }

}

function Apagar(){
  checks = document.getElementsByName('chkArq');
  if (confirm(lista_frases.msg210)){
    xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_raiz);
    for (i=0; i<checks.length; i++){
      if(checks[i].checked){
        getNumber=checks[i].id.split("_");
        nomeArq = document.getElementById("nomeArq_"+getNumber[1]).getAttribute('nomeArq');
        xajax_ExcluirArquivo(getNumber[1], nomeArq, cod_curso, cod_item, cod_usuario, lista_frases.msg198);
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
      xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
      xajax_MoverArquivosDinamic(caminhoOrigem, caminhoDestino, cod_curso, cod_item, cod_usuario, lista_frases_geral.msg_ger63);
    }
  }

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

  document.getElementById('checkMenu').checked=false;
  CheckTodos();
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
    xajax_AssociaAvaliacaoDinamic(cod_curso, cod_usuario, cod_item, cod_avaliacao, lista_frases_geral.msg_ger35, lista_frases.msg212);
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

function DesmarcaRadios(){
  radios = document.getElementsByName('cod_avaliacao');
  for (i=0; i<radios.length; i++){
    radios[i].checked=false;
  }
  cod_avaliacao='';
}

