<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/instalacao2.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
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

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : instalacao/instalacao2.php
  ========================================================== */

  include "instalacao.inc"; 

  ExibirCabecalho(2,"Cria��o dos usu�rios do MySQL para uso do TelEduc");

  session_register("dbbasegeral_s");
  session_register("dbbasecurso_s");

  $dbbasegeral_s=$dbbasegeral;
  $dbbasecurso_s=$dbbasecurso;

  echo("

    <script type=\"text/javascript\" language=javascript>
      function Valida() 
      {
        var root_mysql=document.inst.root_mysql.value;
        while (root_mysql.search(\" \") != -1)
          root_mysql = root_mysql.replace(/ /, \"\");

        var root_mysql_senha=document.inst.root_mysql_senha.value;
        while (root_mysql_senha.search(\" \") != -1)
          root_mysql_senha = root_mysql_senha.replace(/ /, \"\");

        var teleduc_login=document.inst.teleduc_login.value;
        while (teleduc_login.search(\" \") != -1)
          teleduc_login = teleduc_login.replace(/ /, \"\");

        var teleduc_senha=document.inst.teleduc_senha.value;
        while (teleduc_senha.search(\" \") != -1)
          teleduc_senha = teleduc_senha.replace(/ /, \"\");

	var teleduc_senha_conf=document.inst.teleduc_senha_conf.value;
        while (teleduc_senha_conf.search(\" \") != -1)
          teleduc_senha_conf = teleduc_senha_conf.replace(/ /, \"\");

	var importar_curso_extr=document.inst.importar_curso_extr.value;
        
        if (root_mysql=='' || root_mysql_senha=='' || teleduc_login=='' || teleduc_senha=='')
        {
          alert('Nenhum campo deve ser deixado em branco.');
          return false; 
        }

        if (document.inst.teleduc_senha_conf.value != document.inst.teleduc_senha.value)
        {
          alert('A senha n�o confere.');
          return false;
        }
	
        return true;
      }
    </script>
  ");
  AbreForm();

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("<tr><td style=\"padding-left: 150px; padding-top: 15px; padding-right: 150px; padding-bottom: 15px; font-size: small;\" align=\"left\">\n");

  Paragrafo("<b>Ser� criada uma conta de acesso � base de dados do MySQL para uso do ambiente TelEduc (somente com acesso local):</b>");

  Paragrafo("<font color=\"#2a6686\"><b>*</b></font> usu�rio '<b><i>teleduc</i></b>': Ter� acesso a todas as tabelas e bases criadas pelo ambiente, para leitura e escrita;");
  Paragrafo("<b>Para cria��o das contas ser� necess�rio fornecer a senha da <font color=\"#2a6686\">conta de ROOT do MySQL</font> (n�o confundir com a do Linux) e especificar uma senha para a conta '<i>teleduc</i>'</b>");

  Paragrafo("<font color=\"#2a6686\"><b>Obs.:</b></font>");
  Paragrafo("<font color=\"#2a6686\">* O login e senha da conta de ROOT do MySQL n�o ser�o armazenados. Ser�o usados apenas para criar os usu�rios acima, que ser�o usados daqui para frente.</font>");
  Paragrafo("<font color=\"#2a6686\">* Caso o usu�rio de ROOT do MySQL tenha acesso sem senha, interrompa a instala��o e configure o MySQL para permitir somente o acesso ao usu�rio root com login e senha (Veja documenta��o do MySQL para tal). Ap�s isso, reinicie a partir do passo 4 do arquivo <a href=# onClick='window.open(\"LeiaMe.txt\", \"LeiaMe\", \"status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes\")'>LeiaMe.txt</a> da instala��o do TelEduc.</font>");


  CaixaTexto("Login do usu�rio de ROOT do MySQL:","root_mysql","root");
  CaixaSenha("Senha do usu�rio de ROOT do MySQL:","root_mysql_senha");
  CaixaTexto("Nome do usu�rio '<b><i>teleduc</i></b>': (altere somente se julgar necess�rio)","teleduc_login","teleduc"); // teleduc por padr�o
  CaixaSenha("Senha para o usu�rio '<b><i>teleduc</i></b>':","teleduc_senha");
  CaixaSenha("Confirme a senha do usu�rio '<b><i>teleduc</i></b>':", "teleduc_senha_conf");
	
  //Paragrafo("Nessa nova vers�o do Teleduc(3.2.0), foi implementada a fun��o de <b>Importar Material de Cursos Extra�dos</b>.");  
  Paragrafo("Voc� deseja ter a op��o de importar cursos extra�dos?");
  Paragrafo("<font color=\"#2a6686\">Para isso, voc� dever� criar uma nova base de dados. Toda vez que desejar importar material de um curso extra�do, ser� criada uma base tempor�ria com as informa��es do curso extra�do.</font>");
  Radio("SIM ","importar_curso_extr","",1);
  Radio("N�O ","importar_curso_extr","CHECKED",0);

  echo("</td></tr></table>\n");
  echo("</td></tr></table>\n");

  EncerraPagina();
?>

