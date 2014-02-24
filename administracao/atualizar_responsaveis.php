<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/atualizar_responsaveis.php

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
  ARQUIVO : administracao/atualizar_responsaveis.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"CadastraResponsavelDinamic");
  $objAjax->register(XAJAX_FUNCTION,"RemoveResponsavelDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */ 
  echo("    <script type=\"text/javascript\">\n");
  
  echo("      function Remover(nome)\n");
  echo("      {\n");
  /* 28 - Tem certeza que deseja remov�-lo? */
  echo("        if(confirm('".RetornaFraseDaLista($lista_frases_adm,28)."'))\n");
  echo("        {\n");
  echo("          xajax_RemoveResponsavelDinamic(nome);\n");
  echo("          document.getElementById('tr_'+nome).style.display = 'none';");
  echo("        }\n");
  echo("      }\n");

  echo("      function Iniciar() {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("      function VerificaCampos()\n");
  echo("      {\n");
  echo("        var nome = document.frmResp.nome.value;\n");
  echo("        while (nome.search(\" \") != -1)\n");
  echo("          nome = nome.replace(/ /, \"\");\n\n");
  echo("        var email = document.frmResp.email.value;\n");
  echo("        if ((email.indexOf('@')< 0) || (email.indexOf('.')< 0))\n");
  echo("        {\n");
  /* 168 - E-mail inv�lido. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm, 168)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        while (email.search(\" \") != -1)\n");
  echo("          email = email.replace(/ /, \"\");\n\n");
  echo("        if ((email == '') || (nome == ''))\n");
  echo("        {\n");
  /* 17 - Algum campo n�o foi preenchido! */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm, 17)."');\n");
  echo("          return false;\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  echo("          nome = document.frmResp.nome.value;\n");
  echo("          email = document.frmResp.email.value;\n");
  echo("          var status = '';");
  echo("          if(document.frmResp.status.checked)\n");
  echo("            status = document.frmResp.status.value;\n");
  echo("          xajax_CadastraResponsavelDinamic(nome,email,status);\n");
  echo("        }\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function RespostaUsuario(sucesso)\n");
  echo("      {\n");
  echo("        if(sucesso)\n");
  echo("        {\n");
  echO("          window.location = 'atualizar_responsaveis.php';");
  echo("        }\n");
  /* 193 - Ocorreu um erro na atualiza��o dos dados do administrador. */
  echo("        else\n");
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,193)."');\n");
  echo("      }\n\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 7 - Atualizar lista de respons�veis pelo Ambiente */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,7)."</h4>\n");

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
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 137 - Selecionar Idioma Padr�o */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,137)."\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases,137)."</span></li>\n");
  /* 518 - Configurações */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,518)."\" onClick=\"document.location='configurar_solicitacao.php'\">".RetornaFraseDaLista($lista_frases,518)."</span></li>\n");
  /* 519 - Alteracoes */
    echo("                <li><span title=\"".RetornaFraseDaLista($lista_frases,519)."\" onClick=\"document.location='atualizar_administrador.php'\">".RetornaFraseDaLista($lista_frases,519)."</span></li>\n");
  /* 7 - Atualizar lista de respons�veis pelo Ambiente */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases,7)."\" onClick=\"document.location='atualizar_responsaveis.php'\">".RetornaFraseDaLista($lista_frases,7)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  
  echo("                  <tr id=\"tr_addResp\" style=\"display:none;\">\n");
  echo("                    <td colspan=\"3\" align=\"center\">\n");
  echo("                      <form name=\"frmResp\" action=\"\" onsubmit=\"return(VerificaCampos());\">\n");
  echo("                      <table>\n");
  /* 32 - Nome do respons�vel: */
  echo("                        <tr>\n");
  echo("                          <td style=\"border:none;text-align:right;\">".RetornaFraseDaLista($lista_frases,32)."</td>");
  echo("                          <td style=\"border:none;text-align:left;\"><input class=\"input\" type=\"text\" name=\"nome\" maxlength=\"100\" size=\"30\" /></td>\n");
  echo("                        </tr>\n");
  /* 33 - E-mail: */
  echo("                        <tr>\n");
  echo("                          <td style=\"border:none;text-align:right;\">".RetornaFraseDaLista($lista_frases,33)."</td>\n");
  echo("                          <td style=\"border:none;text-align:left;\"><input class=\"input\" type=\"text\" name=\"email\" size=\"30\" /></td>\n");
  echo("                        </tr>\n");
  /* 34 - Esta pessoa deve fazer parte da lista de pessoas a serem contatadas para autorizar a cria��o de um curso. */
  echo("                        <tr>\n");
  echo("                          <td style=\"border:none;text-align:center;\" colspan=\"2\"><input type=\"checkbox\" name=\"status\" value='C' checked />".RetornaFraseDaLista($lista_frases,34)."</td> \n");
  echo("                        </tr>\n");
  echo("                        <tr>\n");
  /* 11 - Enviar (Ger) */
  /* 2 - Cancelar (ger)*/
  echo("                          <td colspan=\"2\" style=\"border:none;text-align:center;\"><br /><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,11)."\" type=\"submit\" /> <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,2)."\" type=\"button\" onclick=\"document.getElementById('tr_addResp').style.display='none';\" /></td>");
  echo("                        </tr>\n");
  echo("                      </table>\n");
  echO("                      </form>");
  echo("                    </td>");
  echo("                  </tr>");

  /* 27 - A lista abaixo cont�m os respons�veis pelo ambiente na sua institui��o (como, por exemplo, o administrador e as pessoas a serem contatadas para autorizarem a cria��o de um curso). Estes nomes aparecem nas p�ginas <b>Contatos</b> e <b>Como Criar um Curso</b>. */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,27)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head01\">\n");
  /* 29 - Lista de Respons�veis */
  echo("                    <td colspan=\"3\">".RetornaFraseDaLista($lista_frases,29)."</td>\n");
  echo("                  </tr>\n");

  $lista=RetornaContatos();

  if (count($lista)>0 && $lista != "")
  {
    foreach($lista as $cod => $linha)
    {
      echo("                  <tr id=\"tr_".$linha['nome']."\">\n");
      if ($linha['status']=='C')
        echo("                    <td>".$linha['nome']." *</td>\n");
      else
        echo("                    <td>".$linha['nome']."</td>\n");

      echo("                    <td><a href=mailto:".$linha['email'].">".$linha['email']."</a></td>\n");
      /* 38 - Remover */
      echo("                    <td><input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,38)."\" onClick=\"Remover('".$linha['nome']."');\" type=\"button\" /></td>\n");
      echo("                  </tr>\n");
    }

    echo("                  <tr>\n");
    echo("                    <td colspan=\"3\">\n");
    /* 30 - Aparecer� na lista de pessoas a serem contatadas para se criar um curso. */
    echo("                      * <font size=\"-3\">".RetornaFraseDaLista($lista_frases,30)."</font>\n");
    echo("                    </td>");
    echo("                  </tr>");
  }
  else
  {
    /* 37 - Nenhum respons�vel cadastrado */
    echo("                  <tr>\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,37)."</td>\n");
    echo("                  </tr>");
  }

  echo("                </table>\n");
  echo("                <div align=\"right\">\n");
  /* 31 - Adicionar respons�veis � lista */
  echo("                  <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,31)."\" onClick=\"document.getElementById('tr_addResp').style.display='';\" type=\"button\" />\n");
  echo("                </div>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>