/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/gerenciamento.js

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/administracao/gerenciamento.js
  ========================================================== */

function SelecionouCheckbox()
{
  var elementos = document.gerenc.elements;
  for (i in elementos)
  {
    if (elementos[i].checked)
    {
      return true;
    }
  }
  return false;
}

function SubmitAcao(url, acao, opcao)
{
  if (SelecionouCheckbox())
  {
    if (typeof(url) != 'undefined' && url)
      document.gerenc.action = url;
    if (typeof(acao) != 'undefined' && acao)
      document.gerenc.action_ger.value = acao;
    if (typeof(opcao) != 'undefined' && opcao)
      document.gerenc.opcao.value = opcao;
    document.gerenc.submit();
  }
 /* Se nï¿½o houver nada selecionado */
  else
  {
    /* 114 - Nenhuma pessoa selecionada */
    alert(fraseSemSelecao);
  }
}

function VerificaCheck() {
  var count = 0;
  // Para verificar se o checkbox do
  // coordenador foi selecionado.
  var coordenadorSelecionado = false;
  var Cabecalho = document.getElementById('check_all');
  var cod_itens = getElementsByName_iefix('input','cod_usu[]');
  for (i in cod_itens) {
    if (cod_itens[i].checked) {
      if (typeof(cod_coordenador) != 'undefined' && 
         cod_itens[i].value == cod_coordenador)
        coordenadorSelecionado = true;
      count++;
    }
  }
  if (count == cod_itens.length) Cabecalho.checked = true;
  else                           Cabecalho.checked = false;

  // Habilita ou desabilita os botões de acordo com as seleções dos checkboxes.
  if(count > 0) {
    AdicionaEventos(coordenadorSelecionado);
  }
  if (count <= 0 || coordenadorSelecionado) {
    RemoveEventos((count > 0));
  }
}

function MarcaOuDesmarcaTodos()
{
  var e;
  var CabecalhoMarcado=document.gerenc.check_all.checked;
  for (var i=0; i < document.gerenc.elements.length;i++)
  {
    e = document.gerenc.elements[i];
    if (e.name=='cod_usu[]')
    {
      e.checked=CabecalhoMarcado;
    }
  }
  VerificaCheck();
}

function DesmarcaCabecalho()
{
  document.gerenc.check_all.checked=false;
}

/**
 * Como no IE getElementsByName() nÃ£o funciona, usar a funcao abaixo.
 */  
function getElementsByName_iefix(tag, name) {
  var elem = document.getElementsByTagName(tag);
  var arr = new Array();
  for(var i = 0, iarr = 0; i < elem.length; i++) {
    var att = elem[i].getAttribute('name');
    if(att == name) {
      arr[iarr] = elem[i];
      iarr++;
    }
  }
  return arr;
}