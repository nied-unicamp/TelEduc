<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/index.php

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
  ARQUIVO : instalacao/index.php
  ========================================================== */

  include "instalacao.inc"; 

  ExibirCabecalho(0,"Instala��o");

  echo("
    <script type=\"text/javascript\" language=javascript>
      function Valida() {return true;}
    </script>
  ");

  AbreForm();

  switch(phpversion()){
	  case('4.3.7'):
		  $phpok = 0;
		  break;
	  case('4.3.8'):
		  $phpok = 0;
		  break;
	  default:
		  $phpok = 1;
  }

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("<tr><td style=\"padding-left: 150px; padding-top: 15px; padding-right: 150px; padding-bottom: 15px; font-size: small;\" align=\"left\">\n");

 if( ((bool) ini_get('register_globals')) && ($phpok))
  {
    Paragrafo("<font size=+2>Bem vindo � instala��o do ambiente TelEduc!</font>");
    Paragrafo("");  //<br>
    Paragrafo("<font color=\"#2a6686\">Leia atentamente as instru��es contidas em cada passo da instala��o</font>");
    Paragrafo("Nas pr�ximas p�ginas, ser�o pedidas informa��es necess�rias para a instala��o do TelEduc.");
    Paragrafo("Os campos j� preenchidos indicam quais seriam os valores necess�rios ao exemplo (conforme o arquivo <a href=# onClick='window.open(\"Guia_de_Instalacao.pdf\", \"Guia_de_Instalacao\", \"status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes\")'>Guia_de_Instalacao.pdf</a>).");
    Paragrafo("Siga as instru��es para a correta instala��o do ambiente.");
    echo("</td></tr></table>\n");
    echo("</td></tr></table>\n");
    EncerraPagina(0);
  }
  else  {
	if(!(bool)ini_get('register_globals')){
    		Paragrafo("Erro: Aparentemente a op��o register_globals do php est� desligada, a instala��o n�o pode continuar. Edite seu arquivo php.ini e altere a linha:");
    		Paragrafo("<b> register_globals=Off</b> para <b> register_globals=On</b>");
    		Paragrafo("Reinicie o servidor Apache e tente instalar o TelEduc novamente.");
	}
	if(!$phpok){
	    Paragrafo("Erro: Sua vers�o do PHP n�o � compat�vel com o sistema TelEduc, devido um bug na fun��o <b>glob()</b>.");
	    Paragrafo("Para corrigir o problema instale o PHP <b>4.3.9 ou superior</b>.");
	    Paragrafo("Ap�s atualizar o PHP, reinicie o servidor Apache e instale o TelEduc novamente.");
	
	}
    echo("</td></tr></table>\n");
    echo("</td></tr></table>\n");
  }
?>
