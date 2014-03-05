<?php 
/* 
<!--  
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/cadastro.php

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
  ARQUIVO : pagina_inicial/cadastro.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");
  
  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");
  
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  $objAjax->configure('errorHandler', true);
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"CadastraDadosUsuarioDinamic");
  $objAjax->register(XAJAX_FUNCTION,"CadastrarLogar");
  
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  $pag_atual = "cadastro.php";
  include("../topo_tela_inicial.php");

  echo("    <script type=\"text/javascript\">\n\n");
  echo("      function Iniciar() {\n");
  echo("      }\n\n");
  echo("    </script>\n\n");

  $lista_escolaridade=RetornaListaEscolaridade($sock);

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /*185 - Confirmação*/
  echo("          <h4>".RetornaFraseDaLista($lista_frases,204)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
    
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  /* 23 - Voltar (gen) */
  echo("              <ul class=\"btAuxTabs\">\n");
  echo("                <li><a href='autenticacao_cadastro.php'>".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("              </ul>\n");
  echo("            </tr>");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head\">\n");
  echo("                    <td><b>".RetornaFraseDaLista($lista_frases,211)."</b></td>\n");
  echo("                  </tr>");
  echo("                  <tr>\n");
  echo("                    <td align=\"center\" style=\"padding:25px 5px 25px 5px; text-indent:25px;\">\n");
  
  $user = $_GET["u"];
  $seq = $_GET["s"];
  $cod_curso = $_GET["c"];
  $tipo_curso = $_GET["t"];
 
 
  
  if(ConfirmaUsuario($user, $seq)) {
	  if($cod_curso != NULL){
	  		$sock = Conectar("");
	  		$dados_curso=RetornaDadosMostraCurso($sock,$cod_curso);
  			Desconectar($sock);
	  		echo(RetornaFraseDaLista($lista_frases,217)."&nbsp;<b>".$dados_curso['nome_curso']."</b> ".RetornaFraseDaLista($lista_frases,218));
	  		echo(" <a href=\"autenticacao_cadastro.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."&origem=confirmacao\" alt='Efetuar login'>".RetornaFraseDaLista($lista_frases,207)."!</a>");
	  	}else{
		  	//205 - Seu cadastro foi efetuado com sucesso!
		  	//206 - Clique
		  	//207 - aqui
		  	//208 - para efetuar o login.
		  	echo(RetornaFraseDaLista($lista_frases,205)." ".RetornaFraseDaLista($lista_frases,206));
		  	echo(" <a href='autenticacao_cadastro.php' alt='Efetuar login'>".RetornaFraseDaLista($lista_frases,207)."</a> ".RetornaFraseDaLista($lista_frases,208));
	  	}
  }
  else {
  	//202 - Problemas na confirmação do cadastro. Contate o administrador.
  	echo(RetornaFraseDaLista($lista_frases,209));
  }
  
  
  
  echo("                    </td>\n"); 
  echo("                  </tr>\n");
  echo("				</table>");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
  
?>
