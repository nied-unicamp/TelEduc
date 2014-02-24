<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/configurar_pastas.php

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
  ARQUIVO : administracao/configurar_pastas.php
  ========================================================== */

  /** Entradas:
        * configurar.php: PHPSESSION

      Sa�das:
        * configurar.php: PHPSESSID

        * configurar_pastas2.php: PHPSESSID
                                  host
                                  raizwww
                                  arquivos
                                  arquivosweb
                                  extraidos
                                  sendmail
                                  mysqldump
                                  (mimetypes)
  */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  require_once("../cursos/aplic/xajax_0.5/xajax_core/xajax.inc.php");

  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  $objAjax->configure("characterEncoding", 'ISO-8859-1');
  $objAjax->setFlag("decodeUTF8Input",true);
  $objAjax->configure('javascript URI', "../cursos/aplic/xajax_0.5");
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->register(XAJAX_FUNCTION,"AtualizaPastasDinamic");
  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequest();

  include("../topo_tela_inicial.php");

  $lista_frases_adm=RetornaListaDeFrases($sock,-5);

  /* Inicio do JavaScript */
  echo("    <script language=\"javascript\" type=\"text/javascript\">\n\n");

  echo("      function Confirma()\n");
  echo("      {\n");
  echo("        if(document.frmConfig.chkConf.checked == true)\n");
  echo("        {\n");
  echo("          var host = document.frmConfig.host.value;\n");
  echo("          var arquivos = document.frmConfig.arquivos.value;\n");
  echo("          var arquivosweb = document.frmConfig.arquivosweb.value;\n");
  echo("          var extraidos = document.frmConfig.extraidos.value;\n");
  echo("          var raizwww = document.frmConfig.raizwww.value;\n");
  echo("          var sendmail = document.frmConfig.sendmail.value;\n");
  echo("          var mysqldump = document.frmConfig.mysqldump.value;\n");
  echo("          var mimetypes = document.frmConfig.mimetypes.value;\n");
  echo("          xajax_AtualizaPastasDinamic(host,arquivos,arquivosweb,extraidos,raizwww,sendmail,mysqldump,mimetypes);\n");
  echo("        }\n");
  echo("        else\n");
  echo("        {\n");
  /* 195 - Por favor confirme as altera��es. */
  echo("          alert(\"".RetornaFraseDaLista($lista_frases_adm, 195)."\");\n");
  echo("        }\n");
  echo("        return false;\n");
  echo("      }\n\n");

  echo("      function Restaura()\n");
  echo("      {\n");
  echo("        document.frmConfig.action = \"configurar_pastas.php\";\n");
  echo("        document.frmConfig.submit();\n");
  echo("      }\n\n");

  echo("      function RespostaUsuario(sucesso)\n");
  echo("      {\n");
  echo("        if(sucesso)\n");
  /* 26 - Informa��es alteradas com sucesso. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,26)."');\n");
  echo("        else\n");
  /* 210 - Ocorreu um erro na atualiza��o do endere�o para acesso ou da estrutura de pastas. */
  echo("          alert('".RetornaFraseDaLista($lista_frases_adm,210)."');\n");
  echo("      }\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("	startList();\n");
  echo("      }\n");

  echo("    </script>\n");

  $objAjax->printJavascript();

  /* Fim do JavaScript */

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);
  Desconectar($sock);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 194 - Configurar endere�o para acesso e estrutura de pastas */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,194)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <form name=\"frmConfig\" action=\"configurar_pastas2.php\" method=\"post\" onSubmit=\"return(Confirma());\">\n");  
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");

  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span style=\"href: #\" title=\"Voltar\" onClick=\"document.location='selecionar_lingua.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
  /* 184 - Configurar forma de solicita��o de curso */
  echo("                  <li><span style=\"href: #\" title=\"Configurar forma de solicita��o de curso\" onClick=\"document.location='configurar_solicitacao.php'\">".RetornaFraseDaLista($lista_frases,184)."</span></li>\n");
  /* 6 - Configurar dados instituicionais */
  echo("                  <li><span style=\"href: #\" title=\"Configurar dados instituicionais\" onClick=\"document.location='configurar_dados_institucionais.php'\">".RetornaFraseDaLista($lista_frases,6)."</span></li>\n");
  /* 194 - Configurar endere�o para acesso e estrutura de pastas */
  echo("                  <li><span style=\"href: #\" title=\"Configurar endere�o para acesso e estrutura de pastas\" onClick=\"document.location='configurar_pastas.php'\">".RetornaFraseDaLista($lista_frases,194)."</span></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");

  $host = RetornaConfig("host");
  $diretorios = RetornaTodosDiretorios();

  /* 196 - Endere�o para acesso: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases, 196)."</td>\n");
  echo("                  </tr>\n");
  /* 197 - Hostname ou endere�o IP do servidor: */
  echo("                  <tr>\n");
  echo("                    <td width=\"60%\">".RetornaFraseDaLista($lista_frases, 197)."</td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"host\" size=\"40\" value='".ConverteAspas2Html($host)."' /></td>\n");
  echo("                  </tr>\n");
  /* 198 - Caminho via browser (sem o nome da m�quina. Ex: /~teleduc): */
  echo("                  <tr>\n");
  echo("                    <td width=\"60%\">".RetornaFraseDaLista($lista_frases, 198)."</td>\n");
  echo("                    <td>\n");
  echo("                      <input class=\"input\" type=\"text\" name=\"raizwww\" size=\"40\" value='".ConverteAspas2Html($diretorios['raiz_www'])."' /><br />\n");
  /* 199 - O acesso ser� feito atrav�s da URL: http://hostname/~teleduc */
  echo("                      <font size=\"-1\">".RetornaFraseDaLista($lista_frases, 199)."</font>\n");
  echo("                    </td>\n");
  echo("                  </tr>\n");
  /* 200 - Estrutura de Pastas: */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases, 200)."</td>\n");
  echo("                  </tr>\n");
  /* 201 - Diret�rio em que ficar�o os arquivos anexados as ferramentas do curso (deve ficar fora do acesso da web. Ex: /home/teleduc/arquivos): */
  echo("                  <tr>\n");
  echo("                    <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 201)."</td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"arquivos\" size=\"40\" value='".ConverteAspas2Html($diretorios['Arquivos'])."' /></td>\n");
  echo("                  </tr>\n");
  /* 202 - Diret�rio em que ser� criados links para exibi��o dos arquivos pela web (Ex: /home/teleduc/public_html/diretorio): */
  echo("                  <tr>\n");
  echo("                    <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 202)."</td>\n");
  echo("<td><input class=\"input\" type=\"text\" name=\"arquivosweb\" size=\"40\" value='".ConverteAspas2Html($diretorios['ArquivosWeb'])."' /></td>\n");
  echo("                  </tr>\n");
  /* 203 - Diret�rio para o qual ser�o movidos os cursos extraidos (Ex: /home/teleduc/extraidos): */
  echo("                  <tr>\n");
  echo("                    <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 203)."</td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"extraidos\" size=\"40\" value='".ConverteAspas2Html($diretorios['Extracao'])."' /></td>\n");
  echo("                  </tr>\n");
  /* 204 - Caminho do Sendmail (inclusive o execut�vel): */
  echo("                  <tr>\n");
  echo("                    <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 204)."</td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"sendmail\" size=\"40\" value='".ConverteAspas2Html($diretorios['sendmail'])."' /></td>\n");
  echo("                  </tr>\n");
  /* 205 - Caminho do mysqldump (inclusive o execut�vel): */
  echo("                  <tr>\n");
  echo("                    <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 205)."</td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"mysqldump\" size=\"40\" value='".ConverteAspas2Html($diretorios['mysqldump'])."' /></td>\n");
  echo("                  </tr>\n");
  /* 206 - Caminho do arquivo "mime.types" do Apache para resolu��o do arquivos (inclusive o nome do arquivo): */
  echo("                  <tr>\n");
  echo("                    <td width=\"20%\">".RetornaFraseDaLista($lista_frases, 206)."</td>\n");
  echo("                    <td><input class=\"input\" type=\"text\" name=\"mimetypes\" size=\"40\" value='".ConverteAspas2Html($diretorios['mimetypes'])."' /></td>\n");
  echo("                  </tr>\n");
  /* 207 - Modifica��es nas configura��es do ambiente devem ser feitas apenas se refletirem mudan�as na estrutura de arquivos ou configura��o para acesso ao ambiente. */
  echo("                  <tr class=\"head\">\n");
  echo("                    <td colspan=\"2\">".RetornaFraseDaLista($lista_frases, 207)."</td>\n");
  echo("                  </tr>\n");
  /* 208 - Estou ciente que os dados aqui fornecidos refletem a estrutura de arquivos e configura��o do meu ambiente. */
  echo("                  <tr>\n");
  echo("                    <td colspan=\"2\"><input class=\"input\" type=\"checkbox\" name=\"chkConf\" value=1 />".RetornaFraseDaLista($lista_frases, 208)."</td>\n");
  echo("                  </tr>\n");
  echo("                </table>\n");
  echo("                <div align=\"right\">\n");
  /* 209 - Restaurar */
  echo("                      <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases,209)."\" onClick=\"Restaura();\" type=\"button\" />\n");
  /* 24 - Alterar (ger) */
  echo("                      <input class=\"input\" value=\"".RetornaFraseDaLista($lista_frases_geral,24)."\" type=\"submit\" />\n");
  echo("                </div>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </form>\n");
  echo("      </td>\n");
  echo("    </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>