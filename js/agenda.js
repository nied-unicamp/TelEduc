var isNav = (navigator.appName.indexOf("Netscape") !=-1);
var isMinNS6 = ((navigator.userAgent.indexOf("Gecko") != -1) && (isNav));
var isIE = (navigator.appName.indexOf("Microsoft") !=-1);
var Xpos, Ypos;
//var cod_item=cod_item, js_cod_topico;
var cod_item=cod_item;
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
var id_aux=id;
var id = cod_item;
var cancelarElemento=null;
var cancelaEdita=0;
var conteudo='';
/*var id_aux = id;
var id = ".cod_item.";
var id = ".cod_item.";
var cod_curso = ".cod_curso.";*/
//var titulo=0;
//var cod_item=0;
//xajax_RetornaFraseDinamic('lista_frases');
//xajax_RetornaFraseGeralDinamic('lista_frases_geral');
if (isNav)
{
document.addEventListener(Event.MOUSEMOVE);
// document.addEventListener()
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
function AtualizaComp(js_tipo_comp)
{
if ((isNav) && (!isMinNS6)) {
document.comp.document.form_comp.tipo_comp.value=js_tipo_comp;
document.comp.document.form_comp.cod_item.value=js_cod_item;
var tipo_comp = new Array(document.comp.document.getElementById('tipo_comp_T'), document.comp.document.getElementById('tipo_comp_F'), document.comp.document.getElementById('tipo_comp_P'));
} else {
if (isIE || ((isNav)&&(isMinNS6)) ){
document.form_comp.tipo_comp.value=js_tipo_comp;
document.form_comp.cod_item.value=js_cod_item;
var tipo_comp = new Array(document.getElementById('tipo_comp_T'), document.getElementById('tipo_comp_F'), document.getElementById('tipo_comp_P'));
}
}
var imagem="<img src='../imgs/portfolio/checkmark_blue.gif'>"
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
//xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, cod_usuario_portfolio, cod_grupo_portfolio, cod_topico_ant);
}
function WindowOpenVerURL(end)
{
window.open(end,'PortfolioURL','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');
}
/*
function EdicaoTexto(codigo, id, valor){
if (valor=='ok'){
eval('conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');
//xajax_EditarTexto(cod_curso, codigo, conteudo, cod_usuario, lista_frases.msg22);
}
else{
//Cancela Edicao
if (!cancelarTodos)
xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
}
document.getElementById(id).innerHTML=conteudo;
editaTexto=0;
cancelarElemento=null;
}
var controle=0;*/
/*function AlteraTexto(id){;
var conteudo = document.getElementById('text_'+id).innerHTML;
writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);
}
*/
/*function EdicaoTitulo(codigo, id, valor){
//se o tï¿½tulo nï¿½o ï¿½ vazio
if ((valor=='ok')&&(document.getElementById(id+'_text').value != "")){
novoconteudo = document.getElementById(id+'_text').value;
//Edita o tï¿½tulo do item dado, dinï¿½micamente
//xajax_EditarTitulo(cod_curso, codigo, novoconteudo, cod_usuario, lista_frases.msg103);
//else - se o tï¿½tulo for vazio.
}else{
15 - O titulo nao pode ser vazio.
if ((valor=='ok')&&(document.getElementById(id+'_text').value == ""))
alert(lista_frases.msg15);
document.getElementById(id).innerHTML=conteudo;
if(navigator.appName.match("Opera")){
document.getElementById('renomear_'+codigo).onclick = AlteraTitulo(codigo);
}else{
document.getElementById('renomear_'+codigo).onclick = function(){ AlteraTitulo(codigo); };
}
//Cancela Ediï¿½ï¿½o
if (!cancelarTodos)
xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
}
editaTitulo=0;
cancelarElemento=null;
}
*/
/*function AlteraTitulo(id){
var iframe = document.getElementById('iframe_ArqEntrada');
var span = document.getElementById('text_'+id);
checks = document.getElementsByName('chkArq');
if ((editaTexto==0)&&(checks.length==0)){
CancelaTodos();
//xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, origem);
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
}*/
function LimpaTexto(id){
checks = document.getElementsByName('chkArq');
if ((editaTexto==0)&&(checks.length==0)){
// 95 - Vocï¿½ tem certeza que deseja apagar o texto desta agenda?
if (confirm(lista_frases.msg95)){
CancelaTodos();
document.getElementById('text_'+id).innerHTML='';
//xajax_EditarTexto(cod_curso, id, '', cod_usuario, lista_frases.msg93);
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
var vet = file.match(/^[A-Za-z0-9-\.\_\ ]+/);
// Usando expressï¿½o regular para identificar caracteres invï¿½lidos
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
alert(lista_frases.msg109);
document.getElementById('input_files').style.visibility='hidden';
document.getElementById('input_files').value='';
document.getElementById('divArquivo').className='';
document.getElementById('divArquivoEdit').className='divHidden';
//Cancela Edicao
if (!cancelarTodos)
//xajax_AcabaEdicaoDinamic(cod_curso, cod_item, cod_usuario, 0);
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
xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, origem);
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
if (confirm(lista_frases.msg12+''+lista_frases.msg13+''+lista_frases.msg14)){
//xajax_AbreEdicao(cod_curso, cod_item, cod_usuario, origem);
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
document.getElementById('mArq_apagar').onclick= function(){ Apagar(); };
document.getElementById('mArq_descomp').onclick= function(){ };
document.getElementById('mArq_entrada').onclick= function(){ };
}else if((arqComum==1)&&(arqZip==0)&&(arqEntradaCheck==0)){
document.getElementById('mArq_apagar').className="menuUp02"
document.getElementById('mArq_descomp').className="menuUp";
document.getElementById('mArq_entrada').className="menuUp02";
document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
document.getElementById('sArq_descomp').onclick= function(){ };
document.getElementById('sArq_entrada').onclick= function(){ SelecionarEntrada(); };
}else if((arqComum==1)&&(arqZip==0)&&(arqEntradaCheck==1)){
document.getElementById('mArq_apagar').className="menuUp02"
document.getElementById('mArq_descomp').className="menuUp";
document.getElementById('mArq_entrada').className="menuUp02";
document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
document.getElementById('sArq_descomp').onclick= function(){ };
document.getElementById('sArq_entrada').onclick= function(){ RetirarEntrada(); };
}else if((arqComum==1)||(arqZip>1)){
document.getElementById('mArq_apagar').className="menuUp02"
document.getElementById('mArq_descomp').className="menuUp";
document.getElementById('mArq_entrada').className="menuUp";
document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
document.getElementById('sArq_descomp').onclick= function(){ };
document.getElementById('sArq_entrada').onclick= function(){ };
}else if(arqComum>1){
document.getElementById('mArq_apagar').className="menuUp02"
document.getElementById('mArq_descomp').className="menuUp";
document.getElementById('mArq_entrada').className="menuUp";
document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
document.getElementById('sArq_descomp').onclick= function(){ };
document.getElementById('sArq_entrada').onclick= function(){ };
}else if(arqZip==1){
document.getElementById('mArq_apagar').className="menuUp02"
document.getElementById('mArq_descomp').className="menuUp02"
document.getElementById('mArq_entrada').className="menuUp";
document.getElementById('sArq_apagar').onclick= function(){ Apagar(); };
document.getElementById('sArq_descomp').onclick= function(){ Descompactar() };
document.getElementById('sArq_entrada').onclick= function(){ };
}else{
document.getElementById('mArq_apagar').className="menuUp";
document.getElementById('mArq_descomp').className="menuUp";
document.getElementById('mArq_entrada').className="menuUp";
document.getElementById('sArq_apagar').onclick= function(){ };
document.getElementById('sArq_descomp').onclick= function(){ };
document.getElementById('sArq_entrada').onclick= function(){ };
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
//xajax_ExcluirArquivo(getNumber[1], nomeArq, cod_curso, cod_item, cod_usuario, origem);
js_conta_arq--;
}
}
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
//xajax_SelecionarEntradaDinamic(nomesArqs, cod_curso, cod_item, cod_usuario, origem);
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
//xajax_RetirarEntradaDinamic(nomeArq, cod_curso, cod_item, cod_usuario, origem);
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
/*function Mover(caminhoDestino){
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
}*/
function ApagarItem(){
CancelaTodos();
if (confirm(lista_frases.msg29+''+lista_frases.msg30)){
window.location='../agenda/acoes_linha.php?cod_curso='+cod_curso+'&cod_item='+cod_item+'&acao=apagarItem&origem='+origem;
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
function NovaAgenda()
{
MostraLayer(lay_nova_agenda, 0);
document.form_nova_agenda.novo_titulo.value = '';
document.getElementById('nome').focus();
}
/*function Iniciar()
{
lay_nova_agenda = getLayer('layer_nova_agenda');
startList();
}*/
function EscondeLayers()
{
hideLayer(lay_nova_agenda);
}
function MostraLayer(cod_layer, ajuste)
{
EscondeLayers();
moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());
showLayer(cod_layer);
}
function EscondeLayer(cod_layer)
{
hideLayer(cod_layer);
}
function TemCertezaAtivar()
{
/* 57 - Tem certeza que deseja ativar esta agenda? */
/* 58 - (Uma vez ativada, nao havera como desativa-la) */
return(confirm("Tem certeza que deseja ativar esta agenda?"+ "\n" + "Uma vez ativada, nao havera como desativa-la"));
}
function Ativar()
{
if(TemCertezaAtivar())
{
window.location='../../../app/agenda/controller/TrataRequest.php?cod_curso='+cod_curso+'&cod_usuario='+cod_usuario+'&cod_ferramenta=1&cod_item='+cod_item+'&acao=ativaragenda';
}
return false;
}
/*function WindowOpenVer(id)
{
window.open('".$dir_item_temp['link']."'+id+'?".time()."','Agenda','top=50,left=100,width=600,height=400,menubar=yes,status=yes,toolbar=yes,scrollbars=yes,resizable=yes');
}*/
function EditaTituloEnter(campo, evento, id)
{
var tecla;
CheckTAB=true;
if(navigator.userAgent.indexOf('MSIE')== -1)
{
tecla = evento.which;
}
else
{
tecla = evento.keyCode;
}
if ( tecla == 13 )
{
EdicaoTitulo(id, 'tit_'+id, 'ok');
}
return true;
}
//function edicao_titulo(id){
$(document).ready(function(){
$('#renomear_'+id).click(function(){
if (editaTitulo==0){
CancelaTodos();
/*var id_aux = id;
var id = ".cod_item.";
var id = ".cod_item.";
var cod_curso = ".cod_curso.";*/
conteudo = document.getElementById('tit_'+id).innerHTML;
document.getElementById('tr_'+id).className='';
document.getElementById('tit_'+id).className='';
createInput = document.createElement('input');
document.getElementById('tit_'+id).innerHTML='';
createInput.setAttribute('type', 'text');
createInput.setAttribute('style', 'border: 2px solid #9bc');
createInput.setAttribute('id', 'tit_'+id+'_text');
if (createInput.addEventListener){ //not IE
createInput.addEventListener('keypress', function (event) {EditaTituloEnter(this, event, id_aux);}, false);
} else if (createInput.attachEvent){ //IE
createInput.attachEvent('onkeypress', function (event) {EditaTituloEnter(this, event, id_aux);});
}
document.getElementById('tit_'+id).appendChild(createInput);
$.post('../../../lib/DecodificaString.php',{texto:texto},
function(data){
var code = $.parseJSON(data);
$('#tit_'+id+'_text').val(code);
});
//cria o elemento 'espaco' e adiciona na pagina
espaco = document.createElement('span');
espaco.innerHTML='&nbsp;&nbsp;';
document.getElementById('tit_'+id).appendChild(espaco);
createSpan = document.createElement('span');
createSpan.className='link';
createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'ok'); };
createSpan.setAttribute('id', 'OkEdita');
//createSpan.innerHTML='"._("OK_-1")."';
createSpan.innerHTML='OK';
document.getElementById('tit_'+id).appendChild(createSpan);
//cria o elemento 'espaco' e adiciona na pagina
espaco = document.createElement('span');
espaco.innerHTML='&nbsp;&nbsp;';
document.getElementById('tit_'+id).appendChild(espaco);
createSpan = document.createElement('span');
createSpan.className='link';
createSpan.onclick= function(){ EdicaoTitulo(id, 'tit_'+id, 'canc'); };
createSpan.setAttribute('id', 'CancelaEdita');
createSpan.innerHTML='Cancelar';
document.getElementById('tit_'+id).appendChild(createSpan);
//cria o elemento 'espaco' e adiciona na pagina
espaco = document.createElement('span');
espaco.innerHTML='&nbsp;&nbsp;';
document.getElementById('tit_'+id).appendChild(espaco);
startList();
cancelarElemento=document.getElementById('CancelaEdita');
document.getElementById('tit_'+id+'_text').select();
editaTitulo++;
}
});
});
function EdicaoTitulo(codigo, id, valor){
//se o tï¿½tulo nï¿½o ï¿½ vazio");
if ((valor=='ok')&&(document.getElementById(id+'_text').value != '')){
titulo = document.getElementById(id+'_text').value;
//Edita o tï¿½tulo do item dado, dinï¿½micamente");
$.post('../../../app/agenda/dao/DaoAlteraTitulo.php',{titulo: titulo,cod_item: cod_item,cod_curso:cod_curso},
function(data){
$('#tr_'+id).toggleClass('novoitem');
$('#tit_'+id).html(titulo);
/* 103 - Agenda renomeada com sucesso.*/
mostraFeedback('Palavra renomeada com sucesso', 'true');
});
//else - se o titulo for vazio.");
}else{
/* 15 - O titulo nao pode ser vazio. */
if ((valor=='ok')&&(document.getElementById(id+'_text').value == ''))
/* 92 - O tï¿½tulo nï¿½o pode ser vazio.*/
alert('O titulo nao pode ficar vazio');
document.getElementById(id).innerHTML=conteudo;
}
editaTitulo=0;
cancelarElemento=null;
}
function AlteraTexto(id){
var conteudo = document.getElementById('text_'+id).innerHTML;
writeRichTextOnJS('text_'+id+'_text', conteudo, 520, 200, true, false, id);
}
function EdicaoTexto(codigo, id, valor){
eval('var conteudo = CKEDITOR.instances.'+id+'_text'+'.getData();');
if ((valor=='ok')&&(document.getElementById(id+'_text').value != '')){
texto = document.getElementById(id+'_text').value;
$.post('../../../app/agenda/dao/DaoAlteraTexto.php',{ texto: conteudo,cod_item: cod_item, cod_curso:cod_curso},
function(data){
$('#tr_'+id).toggleClass('novoitem');
$('#text_'+id).html(conteudo);
});
}
document.getElementById(id).innerHTML=conteudo;
editaTexto=0;
cancelarElemento=null;
cancelarElemento=document.getElementById('CancelaEdita');
}
function LimpaTexto(id){
checks = document.getElementsByName('chkArq');
if ((editaTexto==0)&&(checks.length==0)){
// 95 - Vocï¿½ tem certeza que deseja apagar o texto desta agenda?
if (confirm('Voce tem certeza que deseja apagar o texto?')){
CancelaTodos();
document.getElementById('text_'+id).innerHTML='';
$.post('../../../app/agenda/dao/DaoAlteraTexto.php',{cod_curso: cod_curso, cod_item: cod_item, texto: ' '},
function(data){
var code = $.parseJSON(data);
$('#tr_'+cod_item).toggleClass('novoitem');
$('#text_'+cod_item).html(code);
mostraFeedback('Apagado com sucesso', 'true');
});
}
}
else{
if(checks.length > 0)
{
// 53 - A agenda nao pode ter texto e arquivos simultaneamente!
alert('A agenda nÃ£o pode ter texto nem titulo simultaneamente.');
}
}
}
function ApagarItem(){
CancelaTodos();
if (confirm('Voce tem certeza de que deseja apagar esta agenda?, Nao havera como recupera-la!')){
window.location='../../../app/agenda/dao/DaoExcluirAgenda.php?cod_curso='+cod_curso+'&cod_item='+cod_item;
}
}
function MostraLayer(cod_layer, ajuste)
{
EscondeLayers();
moveLayerTo(cod_layer,Xpos-ajuste,Ypos+AjustePosMenuIE());
showLayer(cod_layer);
}
function EscondeLayer(cod_layer)
{
hideLayer(cod_layer);
}
function EscondeLayers()
{
hideLayer(lay_nova_agenda);
}
function NovaAgenda()
{
MostraLayer(lay_nova_agenda, 0);
document.form_nova_agenda.novo_titulo.value = '';
document.getElementById('nome').focus();
}
function VerificaCheck(){
var i;
var j=0;
var cod_itens=document.getElementsByName('chkItem');
var Cabecalho = document.getElementById('checkMenu');
array_itens = new Array();
for (i=0; i<cod_itens.length; i++){
if (cod_itens[i].checked){
var item = cod_itens[i].id.split('_');
array_itens[j]=item[1];
j++;
}
}
if ((j)==(cod_itens.length)) Cabecalho.checked=true;
else Cabecalho.checked=false;
if((j)>0){
document.getElementById('mExcluir_Selec').className='menuUp02';
document.getElementById('mExcluir_Selec').onclick=function(){ ExcluirSelecionados(); };
}else{
document.getElementById('mExcluir_Selec').className='menuUp';
document.getElementById('mExcluir_Selec').onclick=function(){ };
}
}
function CheckTodos(){
var e;
var i;
var CabMarcado = document.getElementById('checkMenu').checked;
var cod_itens=document.getElementsByName('chkItem');
if (cod_itens.length == 0){
return;
}
for(i = 0; i < cod_itens.length; i++){
e = cod_itens[i];
e.checked = CabMarcado;
}
VerificaCheck();
}
function ExcluirSelecionados(){
if (TemCertezaApagar()){
document.getElementById('cod_itens_form').value=array_itens;
document.form_dados.action='../../../app/agenda/controller/TrataRequest.php';
document.form_dados.method='POST';
document.getElementById('acao_form').value='apagarSelecionados';
document.form_dados.submit();
}
}
function TemCertezaApagar()
{
/* 29 - Voce tem certeza de que deseja apagar esta agenda? */
/* 30 - (nao havera como recupera-la) */
return(confirm("Voce tem certeza de que deseja apagar esta agenda?"+''+"(nao havera como recupera-la)"));
}
function VerificaNovoTitulo(textbox, aspas) {
texto=textbox.value;
if (texto==''){
// se nome for vazio, nao pode
/* 15 - O titulo nao pode ser vazio. */
alert('O titulo nao pode ser vazio.');
textbox.focus();
return false;
}
// se nome tiver aspas, <, >, nao pode - aspas pode ser 1,0
else if ((texto.indexOf('\\\\')>=0 || texto.indexOf('\\\"')>=0 || texto.indexOf('')>=0 || texto.indexOf('>')>=0 || texto.indexOf('<')>=0)&&(!aspas)) {
/* 16 - O titulo nao pode conter \\. */
alert('O titulo nao pode conter \\.');
textbox.value='';
textbox.focus();
return false;
}
return true;
}
function TemCertezaAtivar()
{
//57 - Tem certeza que deseja publicar esta agenda?
//58 - (Uma vez publicada ela substituira a Agenda Atual)
return(confirm('Tem certeza que deseja publicar esta agenda?'+'\n'+'(Uma vez publicada ela substituira a Agenda Atual)'));
}
function Voltar()
{
window.location='agenda.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."';;
}
/*function OpenWindowPerfil(funcao)
{
window.open(\"../perfil/exibir_perfis.php?"+sessionID+"&cod_curso="+cod_curso+"&cod_aluno[]=\"+funcao,\"PerfilDisplay\",\"width=600,height=400,top=120,left=120,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=yes\");
return(false);
}*/
if (tipo_curso == 'E') {
function CopiaPeriodo()
{
document.frmImpMaterial.data_inicio.value = document.frmAlteraPeriodo.data_inicio.value;
document.frmImpMaterial.data_fim.value = document.frmAlteraPeriodo.data_fim.value;
}
// Se os cursos listados sao do tipo E(ncerrado) entao
// cria funcao de validacao para periodo dos cursos.
// Valida as datas (inicial e final) do periodo
function Valida()
{
if (ComparaData(document.getElementById('data_inicio'), document.getElementById('data_fim')) > 0)
{
//31(biblioteca) - Período Inválido!
alert('Período Inválido!');
document.frmAlteraPeriodo.data_inicio.value = document.frmAlteraPeriodo.data_fim.value;
return false;
}
else if (AnoMesDia(document.getElementById('data_fim').value) > AnoMesDia(hoje))
{
// 31(biblioteca) - Período Inválido!
alert('Período Inválido!');
document.getElementById('data_fim').value = hoje;
return false;
}
var select = document.getElementById('cod_curso_todos');
while(select.length>0){
select.removeChild(select.firstChild);
}
var select = document.getElementById('cod_curso_compart');
while(select.length>0){
select.removeChild(select.firstChild);
}
//xajax_AlterarPeriodoDinamic(xajax.getFormValues('frmAlteraPeriodo'));
}
}
function desmarcaSelect(selectObj)
{
eval('document.getElementById(selectObj).selectedIndex = -1;');
}
function EnviaReq(){
if (tipo_curso == 'E')
CopiaPeriodo();
return(selecionouCurso());
}
function extracheck(obj)
{
return !obj.disabled;
}
function ListarCursos(tipo_curso)
{
document.frmImpMaterial.tipo_curso.value = tipo_curso;
document.frmImpMaterial.action = '../../../app/agenda/view/importar_curso.php?cod_curso='+cod_curso+'&cod_ferramenta='+cod_ferramenta+'&cod_topico_raiz='+cod_topico_raiz;
document.frmImpMaterial.submit();
}
function mudarCategoria()
{
x = document.getElementById('select_categorias');
cod_categoria = x.options[x.selectedIndex].value;
document.frmImpPergunta.cod_categoria;
document.frmImpMaterial.action = '../../../app/agenda/view/importar_curso.php';
document.frmImpMaterial.cod_curso.value = cod_curso;
document.frmImpMaterial.tipo_curso.value = tipo_curso;
document.frmImpMaterial.submit();
}
function mudafonte(tipo) {
if ( tipo == 0 ) {
document.getElementById('tabelaInterna').style.fontSize='1.0em';
tipo='';
}
if ( tipo == 1 ) {
document.getElementById('tabelaInterna').style.fontSize='1.2em';
tipo='';
}
if ( tipo == 2 ) {
document.getElementById('tabelaInterna').style.fontSize='1.4em';
tipo='';
}
}
