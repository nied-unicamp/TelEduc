<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/instalacao2.php

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
  ARQUIVO : instalacao/instalacao2.php
  ========================================================== */

  include "instalacao.inc"; 

  ExibirCabecalho(3,"Criação dos usuários do MySQL para uso da base de dados Importar Materiais do TelEduc");

  session_register("dbbasegeral_s");
  session_register("dbbasecurso_s");
  session_register("teleduc_login_s");
  session_register("teleduc_senha_s");
  session_register("root_mysql_s");
  session_register("root_mysql_senha_s");
  session_register("importar_curso_extr_s");

  $teleduc_login_s=$teleduc_login;
  $teleduc_senha_s=$teleduc_senha;
  $root_mysql_s=$root_mysql;
  $root_mysql_senha_s=$root_mysql_senha;
  $importar_curso_extr_s=$importar_curso_extr;
  
  echo("
    <script type=\"text/javascript\" language=javascript>
      function Valida() 
      {
        if ($importar_curso_extr)
	{
	  var tmpdbbasecurso=document.inst.tmpdbbasecurso.value;
          while (tmpdbbasecurso.search(\" \") != -1)
            tmpdbbasecurso = tmpdbbasecurso.replace(/ /, \"\");

	  var tmpteleduc_login=document.inst.tmpteleduc_login.value;
          while (tmpteleduc_login.search(\" \") != -1)
            tmpteleduc_login = tmpteleduc_login.replace(/ /, \"\");

	  var tmpteleduc_senha=document.inst.tmpteleduc_senha.value;
          while (tmpteleduc_senha.search(\" \") != -1)
            tmpteleduc_senha = tmpteleduc_senha.replace(/ /, \"\");
 
          var tmpteleduc_senha_conf=document.inst.tmpteleduc_senha_conf.value;
          while (tmpteleduc_senha_conf.search(\" \") != -1)
            tmpteleduc_senha_conf = tmpteleduc_senha_conf.replace(/ /, \"\");

          if (tmpdbbasecurso=='' || tmpteleduc_login=='' || tmpteleduc_senha=='' || tmpteleduc_senha_conf=='')
          {
            alert('Nenhum campo deve ser deixado em branco.');
            return false; 
          }
	   
	  if (('$teleduc_login' == tmpteleduc_login) && ('$teleduc_senha' != tmpteleduc_senha ) )
	  {
	    alert('A senha não confere.');
	    return false;
	  }

     	  if (document.inst.tmpteleduc_senha_conf.value != document.inst.tmpteleduc_senha.value)
          {
            alert('A senha não confere.');
            return false;
          }
          return true;
	    }
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

  if ($importar_curso_extr)
  {
    Paragrafo("<b>Será criada uma conta de acesso à base de dados de cursos extraídos do MySQL, dos quais se deseja importar materiais para uso do ambiente TelEduc(somente com acesso local):</b>");

    Paragrafo("<font color=\"#2a6686\"><b>*</b></font> usuário '<b><i>teleduc</i></b>': Terá acesso a todas as tabelas e bases de cursos extraídos que se deseja importar materiais, criadas pelo ambiente, para leitura e escrita;");
    Paragrafo("<font color=\"#2a6686\"><b>*</b></font> Você pode usar nomes de usuários do MySQL distintos para importação de extraídos e acesso ao ambiente. Caso utilize o mesmo usuário <b>(recomendado)</b> digite a mesma do usuário de acesso ao ambiente nos campos de senha abaixo.");
    Paragrafo("<font color=\"#2a6686\"><b>*</b></font> Uma base de dados distinta para cada curso, com as informações do curso em si. O nome da base será uma composição do nome dado abaixo mais o código do curso (Ex.: 'tmpTelEducCurso' + '1' = 'tmpTelEducCurso1').");
    Paragrafo("<font color=\"#2a6686\">* As bases de dados serão temporárias e criadas apenas quando se desejar importar material de algum curso extraído.</font>");
    
    CaixaTexto("Base de dados para cada curso extraído:","tmpdbbasecurso","tmpTelEducCurso");
    CaixaTexto("Nome do usuário para curso extraído '<b><i>teleduc</i></b>': (altere somente se julgar necessário)","tmpteleduc_login",$teleduc_login_s); // teleduc por padrão
    CaixaSenha("Senha para o usuário '<b><i>teleduc</i></b>' de cursos extraídos:","tmpteleduc_senha");
    CaixaSenha("Confirme a senha do usuário '<b><i>teleduc</i></b>' de cursos extraídos:", "tmpteleduc_senha_conf");
  }
  else
  {
    Paragrafo("Você não optou por ter a possibilidade de Importar Cursos Extraídos. Se você desejar ter essa opção mais à frente, instale novamente o Teleduc.");
  }

  echo("</td></tr></table>\n");
  echo("</td></tr></table>\n");

  EncerraPagina();
?>

