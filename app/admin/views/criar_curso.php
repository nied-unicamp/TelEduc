<?php
$ferramenta_admin = 'admin';
$ferramenta_geral = 'geral';
$ferramenta_administracao = 'administracao';
$ferramenta_login = 'login';

$view_admin = '../../'.$ferramenta_admin.'/views/';
$model_admin = '../../'.$ferramenta_admin.'/models/';
$model_geral = '../../'.$ferramenta_geral.'/models/';
$model_administracao = '../../'.$ferramenta_administracao.'/models/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$view_login = '../../'.$ferramenta_login.'/views/';
$diretorio_imgs  = "../../../web-content/imgs/";

require_once $model_geral.'geral.inc';
require_once $model_admin.'admin.inc';

require_once $view_admin.'topo_tela_inicial.php';

echo("	<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>");
/* Inicio do JavaScript */
echo("  <script type=\"text/javascript\">\n\n");

echo("		$(document).ready(function(){\n");
echo("			$('#login').keyup(function(){\n");
echo("        		var pal = $(this).val;\n");
/* 520 - Sugest�es*/
echo("    			var frase = '"._("SUGGESTIONS_-5")."';\n\n");
echo("				$.post(\"".$model_admin."sugerir_login.php\",{pal: pal}, \n");
echo("					function(data){\n");
echo("						var hint = $.parseJSON(data);\n");
echo("							if (hint != \"\"){\n");
echo("								hint = \"<ul><li><i>\"+frase+\"</i></li>\"+hint+\"</ul>\";\n");
echo("								$('#divSugs').show();\n");
echo("								$('#tr_sugs').show();\n");
echo("								$('#divSugs').html(hint);\n");
echo("							}\n");
echo("							else{\n");
echo("								$('#divSugs').hide();\n");
echo("								$('#tr_sugs').hide();\n");
echo("							}\n");
echo("				});\n");
echo("			});\n");
echo("			$('#criar').click(function(){\n");
echo("        		var email = $('#email').val;\n");
echo("        		var login = $('#login').val;\n");
echo("        		var optUsu = $('#optUsu').val;\n");
echo("				$.post(\"".$model_admin."existe_login_email.php\",{email: email, login: login, optUsu: optUsu}, \n");
echo("					function(data){\n");
echo("						var res = $.parseJSON(data);\n");
echo("						if(res != \"\" && optUsu == \"nao\" && email != \"\" && login != \"\"){ \n");
echo("							EmailLoginRepetido();\n");
echo("						}\n");
echo("						else{\n");
echo("							TestaForm();\n");
echo("						}\n");
echo("				});\n");
echo("			});\n");
echo("		});\n");

echo("    var flagOnDivSugs=0;");

echo("    function EmailLoginRepetido()\n");
echo("    {\n");
/* 533 - Email e/ou login fornecidos j� existem! Digite valores diferentes (note que os logins existentes aparecem na lista de sugest�es!*/
echo("		alert('"._("REPEATED_LOGIN_EMAIL_-5")."');");
echo("      document.frmCriar.email.value = '';\n");
echo("      document.frmCriar.login.value = '';\n");
echo("      document.frmCriar.email.focus();\n");
echo("    }\n");

echo("    function TestaForm()\n");
echo("    {\n");
echo("      var escolha = document.frmCriar.optUsu.value;\n");
echo("      var nome_curso = document.frmCriar.nome_curso.value;\n");
echo("      while (nome_curso.search(\" \") != -1)\n");
echo("        nome_curso = nome_curso.replace(/ /, \"\");\n\n");

echo("      var num = document.frmCriar.num_alunos.value;\n");
echo("      while (num.search(\" \") != -1)\n");
echo("        num = num.replace(/ /, \"\");\n\n");

echo("      if(escolha == 'nao')\n");
echo("      {");
echo("        var nome_coordenador = document.frmCriar.nome_coordenador.value;\n");
echo("        while (nome_coordenador.search(\" \") != -1)\n");
echo("          nome_coordenador = nome_coordenador.replace(/ /, \"\");\n\n");

echo("        var email = document.frmCriar.email.value;\n");
echo("        while (email.search(\" \") != -1)\n");
echo("          email = email.replace(/ /, \"\");\n\n");
echo("      }\n");

echo("      var login = document.frmCriar.login.value;\n");
echo("      while (login.search(\" \") != -1)\n");
echo("        login = login.replace(/ /, \"\");\n\n");

echo("      if(escolha == 'nao')\n");
echo("      {");
echo("        if(nome_curso == '' || num == '' || nome_coordenador == '' || email == '' || login == '')\n");
echo("        {\n");
/* 166 - Os seguintes campos nao podem ser deixados em branco:\n Nome e numero de alunos do curso;\n Nome, email e login do coordenador */
echo("          alert('"._("BLANK_FIELDS_COURSE_COORD_-5")."');\n");
echo("          return false;\n");
echo("        }\n");
echo("      }\n");
echo("      else\n");
echo("      {");
echo("        if(nome_curso == '' || num == '' || login == '')\n");
echo("        {\n");
/* 512 - Os seguintes campos nao podem ser deixados em branco: Nome e numero de alunos do curso; login do coordenador */
echo("          alert('"._("BLANK_FIELDS_COURSE_LOGIN_-5")."');\n");
echo("          return false;\n");
echo("        }\n");
echo("      }\n");

echo("      var intValue = parseInt(num);\n");
echo("      if ((isNaN(intValue)) || (intValue < 0))\n");
echo("      {\n");
/* 167 - O campo numero de alunos deve ser um inteiro positivo. */
echo("        alert('"._("AMOUNT_POSITIVE_-5")."');\n");
echo("        return false;\n");
echo("      }\n");

echo("      if(escolha == 'nao')\n");
echo("      {");
echo("        var cnt = 0;\n");
echo("        var email = document.frmCriar.email.value;\n");
echo("        while (email.search(\"@\") != -1)\n");
echo("        {\n");
echo("          email = email.replace(/@/, \"\");\n\n");
echo("          cnt++;\n");
echo("        }\n");
echo("        var email = document.frmCriar.email.value;\n");
echo("        var p_arroba = email.indexOf('@');\n");
echo("        var p_u_ponto = email.lastIndexOf('.');\n");
echo("        if ((email.indexOf(' ') >= 0) || (email.charAt(email.length-1)=='@') || (email.indexOf('.@') >= 0) || (email.indexOf('@.') >= 0) || (p_u_ponto==(email.length-1)) || (p_u_ponto < 0) || (p_u_ponto < p_arroba) || (cnt == 0) || (cnt > 1)) \n");
echo("        {\n");
/* 168 - E-mail invalido. */
echo("          alert('"._("INVALID_EMAIL_-5")."');\n");
echo("          return false;\n");
echo("        }\n");
echo("      }\n");

echo("      document.frmCriar.submit();\n");
echo("    }\n\n");

echo("    function Iniciar()\n");
echo("    {\n");
echo("	startList();\n");
echo("    }\n\n");

echo("    function VerificaEscolha()\n");
echo("    {\n");
echo("	var escolha = document.frmCriar.optUsu.value;\n");
echo("        if(escolha == 'sim')\n");
echo("        {\n");
echo("          document.getElementById('tr_nome_coord').style.display = 'none';\n");
echo("          document.getElementById('tr_email_coord').style.display = 'none';\n");
echo("        }\n");
echo("        else\n");
echo("        {\n");
echo("          document.getElementById('tr_nome_coord').style.display = '';\n");
echo("          document.getElementById('tr_email_coord').style.display = '';\n");
echo("        }\n");
echo("    }\n\n");

echo("    function TesteBlur()");
echo("    {\n");
echo("      if(flagOnDivSugs == 0)\n");
echo("      {\n");
echo("        document.getElementById('tr_sugs').style.display='none';\n");
echo("        document.getElementById('divSugs').style.display='none';\n");
echo("      }\n");
echo("    }\n");

echo("  </script>\n");

require_once $view_admin.'menu_principal_tela_inicial.php';

$sock=AcessoSQL::Conectar("");

AcessoPHP::VerificaAutenticacaoAdministracao();

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* 3 - Criacao de Curso */
echo("          <h4>"._("CREATION_COURSE_-5")."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");

echo("          <!-- Tabelao -->\n");
echo("          <form name=\"frmCriar\" action=\"criar_curso2.php\" method=\"post\" onsubmit=\"return(false);\">\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <ul class=\"btAuxTabs\">\n");
/* 509 - Voltar */
echo("                  <li><span title=\""._("BACK_-1")."\" onClick=\"document.location='index.php'\">"._("BACK_-1")."</span></li>\n");

/* 98 - Criar Curso */
echo("                  <li><span title=\""._("CREATE_COURSE_-5")."\" onClick=\"document.location='criar_curso.php'\">"._("CREATE_COURSE_-5")."</span></li>\n");

/* 244 - Avaliar requisicoes para abertura de cursos */
echo("                  <li><span title=\""._("EVALUATE_REQUISITIONS_-5")."\" onClick=\"document.location='../avaliarcurso/avaliar_curso.php'\">"._("EVALUATE_REQUISITIONS_-5")."</span></li>\n");

echo("                </ul>\n");

echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
/* 91 - Dados do Curso */
echo("                  <tr class=\"head\">\n");
echo("                    <td align=\"center\"><b>"._("COURSE_DATA_-5")."</b></td>\n");
echo("                  </tr>\n");
echo("                  <tr>");
echo("                    <td align=\"center\">\n");
echo("                      <table>");

/* 92 - Nome do Curso: */
echo("                        <tr>\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("COURSE_NAME_-5")."</b></td>\n");
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("<input class=\"input\" type=\"text\" name=\"nome_curso\" size=\"33\" style=\"maxlenght: 100\" /></td>\n");
echo("                        </tr>\n");

/* 93 - Numero de Alunos: */
echo("                        <tr>\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("NUMBER_OF_STUDENTS_-5")." </b></td>\n");
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("<input class=\"input\" type=\"text\" name=\"num_alunos\" size=\"6\" style=\"maxlenght: 10\" /></td>\n");
echo("                        </tr>\n");

$categ=Admin::RetornaCategorias();

/* 94 - Categoria: */
echo("                        <tr>\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("CATEGORY_-5")." </b></td>\n");
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("                            <select name=\"cod_pasta\" class=\"input\">\n");
/* 115 - Cursos Gerais*/
echo("                              <option value=\"NULL\">"._("GENERAL_COURSE_-3")."</option>");
if($categ != "")
{
	foreach ($categ as $cod_pasta => $pasta)
		echo("                              <option value=".$cod_pasta.">".$pasta."</option>\n");
}
echo("                            </select>\n");
echo("                          </td>\n");
echo("                        </tr>\n");

echo("                        <tr>\n");
echo("                          <td colspan=\"2\" style=\"text-align:center;border:none;\">");
/* 123 - Se voce deseja criar uma nova categoria, preencha o nome da nova categoria abaixo. A categoria escolhida acima sera desconsiderada. */
echo("                            *"._("CREATE_NEW_CATEGORY_-5")."\n");
echo("                          </td>\n");
echo("                        </tr>\n");

/* 124 - Nova categoria: */
echo("                        <tr>\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("NEW_CATEGORY_-5")." </b></td>\n");
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("<input class=\"input\" type=\"text\" name=\"nova_categ\" size=\"33\" style=\"maxlenght: 100\" /></td>\n");
echo("                        </tr>\n");
echo("                      </table>");
echo("                    </td>");
echo("                  </tr>");
echo("                  <tr class=\"head\">\n");
/* 95 - Dados do Coordenador do Curso*/
echo("                    <td align=\"center\"><b>"._("COORD_DATA_-5")."</b></td>\n");
echo("                  </tr>\n");
echo("                  <tr>");
echo("                    <td align=\"center\" style=\"border:none;\">\n");
echo("                      <table>");

/* 508 - Escolher usuario ja cadastrado? */
echo("                        <tr style=\"display:;\">\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("CHOOSE_REGISTERED_USER_-5")."*</b></td>\n");
// 35 - sim
// 36 - nao
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("                            <select name=\"optUsu\" id=\"optUsu\" class=\"input\" onchange=\"VerificaEscolha();\">\n");
echo("                              <option value=\"sim\">"._("YES_-1")."</option>");
echo("                              <option value=\"nao\">"._("NO_-1")."</option>");
echo("                            </select>\n");
echo("                         </td>\n");
echo("                        </tr>\n");

/* 96 - Nome do Coordenador: */
echo("                        <tr id=\"tr_nome_coord\" style=\"display:none;\">\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("COORD_NAME_-5")."</b></td>\n");
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("<input class=\"input\" type=\"text\" name=\"nome_coordenador\" size=\"33\" style=\"maxlenght: 100\" /></td>\n");
echo("                        </tr>\n");

/* 75 - E-mail: */
echo("                        <tr id=\"tr_email_coord\" style=\"display:none;\">\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("EMAIL_-3")." </b></td>\n");
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("<input class=\"input\" type=\"text\" name=\"email\" id=\"emai\" size=\"33\" style=\"maxlenght: 100\" /></td>\n");
echo("                        </tr>\n");

/* 157 - Login */
echo("                        <tr>\n");
echo("                          <td style=\"text-align:right;border:none;\"><b>"._("LOGIN_-3").":</b></td>\n");
/* 520 - Sugest�es */
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("<input autocomplete=\"off\" class=\"input\" type=\"text\" name=\"login\" id=\"login\" size=\"13\" style=\"maxlenght: 20\" onblur=\"TesteBlur();\"  /></td>\n");
echo("                        </tr>\n");

echo("                        <tr id=\"tr_sugs\" style=\"display:none;\">\n");
echo("                          <td style=\"text-align:right;border:none;\">&nbsp;</td>\n");
echo("                          <td style=\"text-align:left;border:none;\">\n");
echo("						  <div id=\"divSugs\" style=\"display:none;background-color:#FFF;position:absolute;border:1pt solid #EEE;padding:5px; margin-top:-22px;\" onmouseover=\"flagOnDivSugs=1;\" onmouseout=\"flagOnDivSugs=0;\">&nbsp;</div></td>\n");
echo("                        </tr>\n");

echo("                      </table>\n");
echo("                    </td>\n");
echo("                  </tr>\n");
echo("                  <tr>\n");
// 511 - *Caso queira escolher um usu�rio j� cadastrado como coordernador do curso, digite as letras iniciais (case sensitive) de seu login e ent�o escolha o login correspondente na lista de sugest�es que aparecer� abaixo do campo Login.
echo("                    <td style=\"text-align:left;border:none;\">"._("INSTRUCTIONS_CHOOSE_REGISTERED_USER_-5")."</td>\n");
echo("                  </tr>\n");
echo("                  <tr>\n");
echo("                    <td id=\"td_hint\" style=\"text-align:left;border:none;\">&nbsp;</td>\n");
echo("                  </tr>\n");
echo("                </table>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td align=\"right\">\n");
/* 98 - Criar Curso */
echo("                <input class=\"input\" id=\"criar\" value=\""._("CREATE_COURSE_-5")."\" type=\"submit\"/>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("          </form>\n");
echo("        </td>\n");
echo("      </tr>\n");
require_once $view_admin.'rodape_tela_inicial.php';
echo("  </body>\n");
echo("</html>\n");

?>