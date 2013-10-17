/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/administracao/eventos.js

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
  ARQUIVO : cursos/aplic/administracao/eventos.js
  ========================================================== */

/*
 * Este arquivo contém as funções que adicionam os eventos aos botões de ação das
 * páginas de gerenciamento para coordenadores. Como coordenadores possuem mais 
 * ações disponíveis, a mesma função deve ser definida em arquivos diferentes, e
 * arquivo correto deve ser chamado dentro da página dependendo do papel do
 * usuário.
 */

/*
 * Função que adiciona os eventos aos botões de ação das páginas de gerenciamento.
 */
function AdicionaEventos(coordenadorSelecionado) {
  document.getElementById('mDados_Selec').className = "menuUp02";
  document.getElementById('mDados_Selec').onclick   = function() { SubmitAcao('gerenciamento2.php?cod_curso='+cod_curso+'&cod_usuario='+cod_usuario+'&cod_ferramenta='+cod_ferramenta, 'dados', ''); };

  if(!coordenadorSelecionado) {
    if (document.getElementById('mAceitar_Selec') != null) {
      document.getElementById('mAceitar_Selec').className = "menuUp02";
      document.getElementById('mAceitar_Selec').onclick   = function(){ SubmitAcao('gerenciamento2.php?cod_curso='+cod_curso+'&cod_usuario='+cod_usuario+'&cod_ferramenta='+cod_ferramenta, 'aceitar', ''); };
    }
    if (document.getElementById('mRejeitar_Selec') != null) {
      document.getElementById('mRejeitar_Selec').className = "menuUp02";
      document.getElementById('mRejeitar_Selec').onclick   = function(){ SubmitAcao('gerenciamento2.php?cod_curso='+cod_curso+'&cod_usuario='+cod_usuario+'&cod_ferramenta='+cod_ferramenta, 'rejeitar', ''); };
    }
    if (document.getElementById('mDesligar_Selec') != null) {
      document.getElementById('mDesligar_Selec').className = "menuUp02";
      document.getElementById('mDesligar_Selec').onclick   = function(){ SubmitAcao('gerenciamento2.php?cod_curso='+cod_curso+'&cod_usuario='+cod_usuario+'&cod_ferramenta='+cod_ferramenta, 'desligar_usuario', ''); };
    }
    if (document.getElementById('mReligar') != null) {
      document.getElementById('mReligar').className = "menuUp02";
      document.getElementById('mReligar').onclick   = function(){ SubmitAcao('gerenciamento2.php?cod_curso='+cod_curso+'&cod_usuario='+cod_usuario+'&cod_ferramenta='+cod_ferramenta, 'religar_usuario', ''); };
    }
    if (document.getElementById('mAtivarPort_Selec') != null) {
      document.getElementById('mAtivarPort_Selec').className = "menuUp02";
      document.getElementById('mAtivarPort_Selec').onclick   = function(){ AtivaDesativaPort('ativar_port'); };
    }
    if (document.getElementById('mDesativarPort_Selec') != null) {
      document.getElementById('mDesativarPort_Selec').className = "menuUp02";
      document.getElementById('mDesativarPort_Selec').onclick   = function(){ AtivaDesativaPort('desativar_port'); };
    }
  }
}

/*
 * Função que remove os eventos aos botões de ação das páginas de gerenciamento,
 * quando nenhum checkbox for selecionado.
 */
function RemoveEventos(algumSelecionado) {

  if (!algumSelecionado) {
    document.getElementById('mDados_Selec').className = "menuUp";
    document.getElementById('mDados_Selec').onclick   = null;
  }

  if (document.getElementById('mAceitar_Selec') != null) {
    document.getElementById('mAceitar_Selec').className = "menuUp";
    document.getElementById('mAceitar_Selec').onclick   = null;
  }
  if (document.getElementById('mRejeitar_Selec') != null) {
    document.getElementById('mRejeitar_Selec').className = "menuUp";
    document.getElementById('mRejeitar_Selec').onclick   = null;
  }
  if (document.getElementById('mDesligar_Selec') != null) {
    document.getElementById('mDesligar_Selec').className = "menuUp";
    document.getElementById('mDesligar_Selec').onclick   = null;
  }
  if (document.getElementById('mReligar') != null) {
    document.getElementById('mReligar').className = "menuUp";
    document.getElementById('mReligar').onclick   = null;
  }
  if (document.getElementById('mAtivarPort_Selec') != null) {
    document.getElementById('mAtivarPort_Selec').className = "menuUp";
    document.getElementById('mAtivarPort_Selec').onclick   = null;
  }
  if (document.getElementById('mDesativarPort_Selec') != null) {
    document.getElementById('mDesativarPort_Selec').className = "menuUp";
    document.getElementById('mDesativarPort_Selec').onclick   = null;
  }
}