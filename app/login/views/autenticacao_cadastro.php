<?php

$ferramenta_geral = 'geral';
$ferramenta_admin = 'admin';
$ferramenta_login = 'login';
$ferramenta_cadastro = 'cadastro';
$ferramenta_administracao = 'administracao';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$view_admin = '../../'.$ferramenta_admin.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';
$ctrl_login = '../../'.$ferramenta_login.'/controllers/';
$diretorio_imgs = '../../../web-content/imgs/';
$view_cadastro = '../../'.$ferramenta_cadastro.'/views/';

require_once $model_geral.'geral.inc';
require_once $model_geral.'inicial.inc';

$cod_curso = $_GET["cod_curso"];
$tipo_curso = $_GET["tipo_curso"];
$destino = $_GET["destino"];
$origem = $_GET["origem"];

if(!isset($ordem))
	$ordem="";

if(!isset($todas_abertas))
	$todas_abertas="";

if(!isset($erro_autenticacao))
	$erro_autenticacao="";

if (!empty ($_SESSION['login_usuario_s']))
{
	header("Location: ".$view_administracao."exibe_cursos.php");
}

$pag_atual = "autenticacao_cadastro.php";

require_once $view_admin.'topo_tela_inicial.php';

// instanciar o objeto, passa a lista de frases por parametro
$feedbackObject =  new FeedbackObject($lista_frases);
// adicionar as acoes possiveis, 1o parametro eh a acao, o segundo eh o numero da frase
// para ser impressa se for "true", o terceiro caso "false"
$feedbackObject->addAction("erroAutenticacao", _("msg214_-3"), _("msg180_-3"));
$feedbackObject->addAction("erroConfirmacao", 0, _("msg213_-3"));
$feedbackObject->addAction("emailConfirmacao", _("msg210_-3"), 0);

$fraseLoginPadrao = _("msg216_-3");
if(!isset($login)){
	$login = $fraseLoginPadrao;
}

echo("    <script type=\"text/javascript\" src=\"".$diretorio_jscss."dhtmllib.js\"></script>\n");
echo("    <script type=\"text/javascript\">\n\n");
echo("      function Iniciar()\n");
echo("      {\n");
$feedbackObject->returnFeedback($_GET['acao'], $_GET['atualizacao']);
echo("        startList();\n");
echo("      }\n\n");

echo("     function TestaNome(form){\n");
echo("          Campo_login = form.login.value;\n");
echo("          Campo_senha = form.senha.value;\n");
echo("          while (Campo_login.search(\" \") != -1){\n");
echo("          Campo_login = Campo_login.replace(/ /, \"\");\n");
echo("        }\n");
echo("        if (Campo_login == ''){\n");
echo("          alert('".html_entity_decode(_("msg181_-3"))."');\n");
echo("          document.formAutentica.login.focus();\n");
echo("          return(false);\n");
echo("        } else {\n");
echo("          while (Campo_senha.search(\" \") != -1){\n");
echo("            Campo_senha = Campo_senha.replace(/ /, \"\");\n");
echo("          }\n");
echo("          if (Campo_senha == ''){\n");
echo("            alert('".html_entity_decode(_("msg182_-3"))."');\n");
echo("          document.formAutentica.senha.focus();\n");
echo("            return(false);\n");
echo("          }\n");
echo("        }\n");
echo("        return(true);\n");
echo("      }\n\n");

/*on focus do Login*/
echo("      function Login_onfocus(obj){\n");
echo("         obj.className='estiloPadrao';\n");
echo("         if (obj.value==\"$fraseLoginPadrao\"){\n");
echo("           obj.value=\"\";\n");
echo("         }\n");
echo("      }\n\n");

/*on blur (perde o foco) do Login*/
echo("      function Login_onblur(obj){\n");
echo("         if (obj.value==\"\"){\n");
echo("           obj.className='valorExemplo';\n");
echo("           obj.value=\"$fraseLoginPadrao\";\n");
echo("         }\n");
echo("      }\n\n");

echo("    </script>\n\n");

require_once $view_admin.'menu_principal_tela_inicial.php';

// 183 -  Autenticacao
echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
if($destino == "inscricao"){
	echo("          <h4>"._("msg183_-3")." - "._("msg159_-3")."</h4>\n");
}else{
	echo("          <h4>"._("msg183_-3")."</h4>\n");
}

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("           <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("           <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("           <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("msg509_-1")."&nbsp;</span></li></ul>\n");
if($destino == "inscricao" && $origem==NULL){
	echo("		  <span class=\"destaque\"><p id=\"feedback\">"._("msg219_-3")." "._("msg220_-3")."</p></span>");
}
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td colspan=\"4\">\n");
echo("                <table cellspacing=\"0\" id=\"divide_meio\" class=\"tabInterna\">\n");
echo("                  <tr id=\"caixaAutenticacao\">   \n");
echo("                    <td class=\"divide_meio\" align=\"center\">\n");
echo("                    "._("msg165_-3")."\n");


/*
 * === Formulario de Autenticacao (login) ===
*/
echo("                        <form id=\"formAutentica\" name=\"formAutentica\" action=\"".$ctrl_login."autenticacao_cadastro.php\" onSubmit=\"return(TestaNome(document.formAutentica));\" method=\"post\" >\n");
echo("                          <input type=\"hidden\" name=\"acao\" id=\"acao\" value=\"autenticar\" />\n");
//echo("                          <input type=\"hidden\" name=\"cod_curso\" value=\"".$_GET['cod_curso']."\" />\n");
echo("                          <input type=\"hidden\" name=\"cod_curso\" value=\"\" />\n");
echo("                          <input type=\"hidden\" name=\"cod_lingua\" value=\"".$_SESSION['cod_lingua_s']."\" />\n");
//echo("                          <input type=\"hidden\" name=\"cod_lingua\" value=\"1\" />\n");
if(isset($tipo_curso))
	echo("                          <input type=\"hidden\" name=\"tipo_curso\" value=\"".$tipo_curso."\" />\n");
if(isset($destino))
	echo("                          <input type=\"hidden\" name=\"destino\" value=\"".$destino."\" />\n");

echo("                          <table>\n");
echo("                            <tr>\n");
echo("                              <td style=\"border:none; text-align:right;\">\n");
/* Frase cod_texto=157 e cod_ferramenta=-3: Login */
echo("                                <b>"._("msg157_-3")."</b>\n");
echo("                              </td>\n");
echo("                              <td style=\"border:none\">\n");
echo("                                <input class=\"valorExemplo\" type=\"text\" id=\"login\" name=\"login\" size=\"25\" maxlength=\"100\" value='".$login."' onfocus=Login_onfocus(document.formAutentica.login); onblur=Login_onblur(document.formAutentica.login);>\n");
echo("                              </td>\n");
echo("                            </tr>\n");
/* Senha
 * Frase cod_texto=158 e cod_ferramenta=-3: Senha
*/
echo("                            <tr>\n");
echo("                              <td style=\"border:none; text-align:right;\">\n");
echo("                                <b>"._("msg158_-3")."</b>\n");
echo("                              </td>\n");
echo("                              <td style=\"border:none\">\n");
echo("                                    <input type=\"password\" id=\"senha\" name=\"senha\" size=\"25\" maxlength=\"100\" style=\"border: 2px solid #9bc;\" />\n");
echo("                              </td>\n");
echo("                            </tr>\n");
/* Botao Entrar do formulario de login
 * Frase cod_texto=18 e cod_ferramenta=-3: ?
*/
echo("                            <tr>\n");
echo("                              <td style=\"border:none; text-align:right;\">&nbsp;</td>\n");
echo("                              <td style=\"border:none\">\n");
echo("                              <br /><input type=\"submit\" class=\"input\" id=\"Botao Entrar Login\" onfocus value=\""._("msg55_-3")."\" />\n");
//echo("                              <br /><input type=\"submit\" class=\"input\" id=\"Botao OK Login\" onfocus value=\"Login\" />\n");
echo("                              </td>\n");
echo("                            </tr>\n");
echo("                          </table>\n");
echo("                        </form>\n");
/*=== FIM - Formulario de Autenticacao (login) ===*/
/*
 // 67 - Se esqueceu seu login, siga o link:
// 68 - Esqueci meu login!
echo ("                    <br/>".RetornaFrase($sock, 67, -2)." <a href='esqueci_login.php'>".RetornaFrase($sock, 68, -2)."</a><br/>");

// 24 - Caso tenha esquecido sua senha siga o link:
// 23 - Esqueci minha senha!
echo ("                    ".RetornaFrase($sock, 24, -2)." <a href='esqueci_senha.php'>".RetornaFrase($sock, 23, -2)."</a><br/>");

// 92 - Se seu cadastro ainda nao foi autenticado, siga o link:
// 93 - Autenticar meu login!
echo ("                    ".RetornaFrase($sock, 92, -2)." <a href='reenviar_autenticacao.php'>".RetornaFrase($sock, 93, -2)."</a><br/>");
*/

echo("                    </td>\n");

echo("                    <td class=\"divide_meio\">\n");
if($cod_curso != NULL){
	// 90 - Se nao tiver cadastro,
	// 101 - clique aqui!
	echo("                    "._("msg90_-2")." <a href=\"".$view_cadastro."cadastro.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."\">"._("msg101_-2")."</a><br />");
}else{
	// 90 - Se nao tiver cadastro,
	// 101 - clique aqui!
	echo("                    "._("msg90_-2")." <a href='".$view_cadastro."cadastro.php'>"._("msg101_-2")."</a><br />");
}
// 67 - Se esqueceu seu login,
// 101 - clique aqui!
echo ("                    <br/>"._("msg67_-2")." <a href='esqueci_login.php'>"._("msg101_-2")."</a><br/>");

// 24 - Se esqueceu sua senha,
// 101 - clique aqui!
echo ("                    "._("msg24_-2")." <a href='esqueci_senha.php'>"._("msg101_-2")."</a><br/>");

// 92 - Se nao recebeu seu email de confirmacao,
// 101 - clique aqui!
echo ("                    "._("msg92_-2")." <a href='reenviar_autenticacao.php'>"._("msg101_-2")."</a><br/>");

echo("                    </td>\n");

echo("                  </tr>\n");
echo("                </table>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");

require_once $view_admin.'rodape_tela_inicial.php';

AcessoSQL::Desconectar($sock);
?>