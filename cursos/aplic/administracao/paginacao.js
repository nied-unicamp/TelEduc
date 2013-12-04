/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/paginacao.js

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
    Copyright (C) 2001  NIED - Unicamp

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    You could contact us through the following addresses:

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/administracao/paginacao.js
  ========================================================== */

/*fun��o java script que controla paginacao-estou criando a pagina��o nova...depois de mudar o intervalo*/
function Paginacao(status) {
  var tab=document.getElementById('tbgeren');
  var tbody=document.createElement('tbody');
  var td_pagina=document.createElement('td');
  td_pagina.colSpan="5";
  td_pagina.align="right";

  var span_first=document.createElement('span');
  span_first.innerHTML="<<&nbsp;&nbsp;";
  var span_ant=document.createElement('span');
  span_ant.innerHTML="<&nbsp;&nbsp;";
  var span_last=document.createElement('span');
  span_last.innerHTML="&nbsp;&nbsp;>>";
  var span_prox=document.createElement('span');
  span_prox.innerHTML="&nbsp;&nbsp;>";
  
  /*verificando se a pagina��o para voltar esta liberada, ou seja se n�o � a primeira pagina*/
 
  if(status== 'BV'){
    span_first.className="none";
    span_first.onclick="none";
    span_ant.className="none";
    span_ant.onclick="none";
    span_last.className="paginacao";
    span_last.onclick=function(){xajax_MudaDinamic(intervalo,'L',cod_curso,tipo_usuario,ordem);};
    span_prox.className="paginacao";
    span_prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',cod_curso,tipo_usuario,ordem);};
    aux='BV';
  }
  
  if(status== 'LV' || status== 'LF'){
    span_first.className="paginacao";
    span_first.onclick=function(){xajax_MudaDinamic(intervalo,'PR',cod_curso,tipo_usuario,ordem);};
    span_ant.className="paginacao";
    span_ant.onclick=function(){xajax_MudaDinamic(intervalo,'A',cod_curso,tipo_usuario,ordem);};
    span_last.className="paginacao";
    span_last.onclick=function(){xajax_MudaDinamic(intervalo,'L',cod_curso,tipo_usuario,ordem);};
    span_prox.className="paginacao";
    span_prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',cod_curso,tipo_usuario,ordem);};
    aux='LF';
  }
  
  if(status== 'BF'){
    span_first.className="paginacao";
    span_first.onclick=function(){xajax_MudaDinamic(intervalo,'PR',cod_curso,tipo_usuario,ordem);};
    span_ant.className="paginacao";
    span_ant.onclick=function(){xajax_MudaDinamic(intervalo,'A',cod_curso,tipo_usuario,ordem);};
    span_last.className="none";
    span_last.onclick="none";
    span_prox.className="none";
    span_prox.onclick="none";
    aux='BF';
  }
  
  if(status== 'B'){
    span_first.className="none";
    span_first.onclick="none";
    span_ant.className="none";
    span_ant.onclick="none";
    span_last.className="none";
    span_last.onclick="none";
    span_prox.className="none";
    span_prox.onclick="none";
    aux='B';
  }
  
  td_pagina.appendChild(span_first);
  td_pagina.appendChild(span_ant);
  
  /*criando os indices */
  
  for(var i=1;i<=qtdPag;i++){
    var td_span=document.createElement('span');
    ind=(parseInt(i))+(parseInt(intervalo))-1;
    if (ind==atual){
      td_span.className="paginaAtual";
      td_span.onclick="none";
    }
    else{
      td_span.className="paginacao";
      td_span.onclick=function(){xajax_PaginacaoDinamic(aux,intervalo,this.id,cod_curso,tipo_usuario,ordem,frasePortifolioAtivado,frasePortifolioDesativado);};
    }
    td_span.id=ind;
    td_span.innerHTML='<a>[&nbsp;'+ind+'&nbsp;]</a>';
    td_pagina.appendChild(td_span);
  }
  
  /*verificando a pagina��o para frente*/
  
  td_pagina.appendChild(span_prox);
  td_pagina.appendChild(span_last);
  
  var tr_span=document.createElement('tr');
  tr_span.setAttribute('name','germen');
  tr_span.id="control";
  tr_span.appendChild(td_pagina);
  tbody.appendChild(tr_span);
  tab.appendChild(tbody);
}

/*func�o responsavel por apagar os registros da pagina atual
 * se flag==T apaga a pagina inteira
 * caso contrario apaga apenas o navegador da pagina��o
 * � necessario as vezes apagar so o navegador quando paginamos para frente ou para tras
 */

function ApagaPagina(flag)
{
  var tr_ger=getElementsByName_iefix('tr', 'germen');
  var tam=tr_ger.length;
  naveg=document.getElementById('control');
  naveg.parentNode.removeChild(naveg);
  if(flag=='T'){
    for(var i=1; i<tam; i++){
      var ger=document.getElementById('ger');
      ger.parentNode.removeChild(ger);
    }
  }
}

function CriaElementoTab(nome,dtins,dados,cod,mostraPortifolio,frasePort){
  var tab=document.getElementById('tbgeren');
  var tbody=document.createElement('tbody');
  var td_check=document.createElement('td');
  td_check.style.width='2%';
  var check=document.createElement('input');
  check.type="checkbox";
  check.setAttribute('name','cod_usu[]');
  check.setAttribute('value',cod);
  check.onclick=new Function("VerificaCheck()");
  td_check.appendChild(check);
  var td_nome=document.createElement('td');
  td_nome.align="left";
  td_nome.innerHTML=nome;
  var td_data=document.createElement('td');
  td_data.innerHTML=dtins;
  var td_dados=document.createElement('td');
  td_dados.innerHTML="<a href=\"gerenciamento2.php?cod_curso="+cod_curso+"&amp;cod_usuario="+cod_usuario+"&amp;cod_ferramenta="+cod_ferramenta+"&amp;ordem="+ordem+"&amp;action_ger=dados&amp;cod_usu[]="+cod+"\">"+fraseDados+"</a>";
  var tr_ger=document.createElement('tr');
  tr_ger.setAttribute('name','germen');
  tr_ger.id="ger";
  tr_ger.appendChild(td_check);
  tr_ger.appendChild(td_nome);
  tr_ger.appendChild(td_data);
  tr_ger.appendChild(td_dados);

/*caso esteja em inscri��es registradas precisamos criar o campo portifolio*/

  if(mostraPortifolio){
    var td_port=document.createElement('td');
    td_port.innerHTML=frasePort;
    td_port.id='status_port'+cod;
    tr_ger.appendChild(td_port);
  }
  tab.appendChild(tbody);
  tbody.appendChild(tr_ger);
}

/*inicia a pagina��o dinamica, criando os controladores de pagina��o*/

function Inicial(limit,flag){
  if (limit>=1){
    qtdPag=limit;
    inicia=flag;
    var tab=document.getElementById('tbgeren');
    var tbody=document.createElement('tbody');
    var coluna=document.createElement('td');
    coluna.colSpan="5";
    coluna.align="right";

/*
 * criando os span necessarios para a p�gina��o, ir para o primeiro n�o � valido pois estamos na primeira
 * pagina.
 */

    var first=document.createElement('span');
    first.innerHTML="<<&nbsp;&nbsp;";
    coluna.appendChild(first);
    var ant=document.createElement('span');
    ant.innerHTML="<&nbsp;&nbsp;";
    coluna.appendChild(ant);
    var prox=document.createElement('span');
    prox.innerHTML="&nbsp;&nbsp;>";
    var last=document.createElement('span');
    last.innerHTML="&nbsp;&nbsp;>>";
    first.className="none";
    ant.className="none";
    first.onclick="none";
    ant.onclick="none";

///*verificando se ainda existir�o mais pagina��es*/
    if (flag=='L'){
      prox.className="paginacao";
      prox.onclick=function(){xajax_MudaDinamic(intervalo,'P',cod_curso,'A',ordem);};
      last.className="paginacao";
      last.onclick=function(){xajax_MudaDinamic(intervalo,'L',cod_curso,'A',$ordem);};
      aux='BV';
    }
//
    else{
      prox.className="none";
      last.className="none";
      prox.onclick="none";
      last.onclick="none";
      aux= 'B';
    }
//
///*paginando os indices iniciais, at� 5, ou at� o fim das mensagens*/
//
    for(var i=1;i<=limit;i++){
      var GerSpan=document.createElement('span');
      GerSpan.id=i;
      if (atual==i){
        GerSpan.className="paginaAtual";
        GerSpan.onclick="none";
      }
      else{
        GerSpan.className="paginacao";
        GerSpan.onclick = function(){
          if (typeof(frasePortifolioAtivado) == 'undefined')
            frasePortifolioAtivado = '';
          if (typeof(frasePortifolioDesativado) == 'undefined')
            frasePortifolioDesativado = '';
          xajax_PaginacaoDinamic(aux,intervalo,this.id,cod_curso,tipo_usuario,ordem,frasePortifolioAtivado,frasePortifolioDesativado);
        };
      }
      GerSpan.innerHTML='<a>[&nbsp;'+i+'&nbsp;]</a>';
      coluna.appendChild(GerSpan);
    }
    coluna.appendChild(prox);
    coluna.appendChild(last);
    var linha=document.createElement('tr');
    linha.setAttribute('name','germen');
    linha.id="control";
    linha.appendChild(coluna);
    tbody.appendChild(linha);
    tab.appendChild(tbody);
  }
}

function MudaIntervalo(aux){
  intervalo=aux;
}

function MudaAtual(aux){
  atual=aux;
}