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

/* 214 - Um novo email para confirmação do Usuário enviado com sucesso!
 * 180 - Erro de autenticação.Digite o seu login e sua senha novamente
 * 213 - Seu cadastro ainda não foi confirmado, verifique seu email ou faça novo pedido de confirmação abaixo.
 * 210 - Seu cadastro foi efetuado com sucesso. Em instantes você receberá um email com um link para confirmação do cadastro e, em seguida, poderá acessar o ambiente.
 * */
$feedbackObject->addAction("erroAutenticacao", _("CONFIRMATION_EMAIL_SENT_-3"), _("AUTHENTICATION_FAILURE_-3"));
$feedbackObject->addAction("erroConfirmacao", 0, _("ACCOUNT_NOT_CONFIRMED_-3"));
$feedbackObject->addAction("emailConfirmacao", _("ACCOUNT_SUCCESS_-3"), 0);


/* 216 - Digite seu login*/
$fraseLoginPadrao = _("TYPE_YOUR_LOGIN_-3");
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
/* 181 - Por favor, preencha o campo "Login".*/
echo("          alert('".html_entity_decode(_("FILL_LOGIN_FIELD_-3"))."');\n");
echo("          document.formAutentica.login.focus();\n");
echo("          return(false);\n");
echo("        } else {\n");
echo("          while (Campo_senha.search(\" \") != -1){\n");
echo("            Campo_senha = Campo_senha.replace(/ /, \"\");\n");
echo("          }\n");
echo("          if (Campo_senha == ''){\n");
/* 182 - Por favor, preencha o campo "Senha".*/
echo("            alert('".html_entity_decode(_("FILL_PASSWORD_FIELD_-3"))."');\n");
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

/* 183 -  Autenticaçao
 * 159 -  Inscrição
 */
echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
if($destino == "inscricao"){
	echo("          <h4>"._("AUTHENTICATION_-3")." - "._("ENROLLMENT_-3")."</h4>\n");
}else{
	echo("          <h4>"._("AUTHENTICATION_-3")."</h4>\n");
}

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("           <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("           <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("           <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;"._("BACK_-1")."&nbsp;</span></li></ul>\n");
if($destino == "inscricao" && $origem==NULL){
	/*
	 * 219 - Para se inscrever em um curso é preciso estar cadastrado no ambiente. Se você já tem cadastro basta se logar, senão
	 * 220 - cadastre-se!*/
	echo("		  <span class=\"destaque\"><p id=\"feedback\">"._("NEED_TO_BE_REGISTERED_-3")." "._("REGISTER_YOU_-3")."</p></span>");
}
echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td colspan=\"4\">\n");
echo("                <table cellspacing=\"0\" id=\"divide_meio\" class=\"tabInterna\">\n");
echo("                  <tr id=\"caixaAutenticacao\">   \n");
echo("                    <td class=\"divide_meio\" align=\"center\">\n");
/* 165 - Digite seus dados nos campos abaixo para entrar no ambiente.*/
echo("                    "._("TYPE_YOUR_LOGIN_DATA_-3")."\n");


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
echo("                                <b>"._("LOGIN_-3").":</b>\n");
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
echo("                                <b>"._("PASSWORD_-3").":</b>\n");
echo("                              </td>\n");
echo("                              <td style=\"border:none\">\n");
echo("                                    <input type=\"password\" id=\"senha\" name=\"senha\" size=\"25\" maxlength=\"100\" style=\"border: 2px solid #9bc;\" />\n");
echo("                              </td>\n");
echo("                            </tr>\n");
/* Botao Entrar do formulario de login
*/
echo("                            <tr>\n");
echo("                              <td style=\"border:none; text-align:right;\">&nbsp;</td>\n");
echo("                              <td style=\"border:none\">\n");
/* 55 - Entrar */
echo("                              <br /><input type=\"submit\" class=\"input\" id=\"Botao Entrar Login\" onfocus value=\""._("ENTER_-3")."\" />\n");
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
	echo("                    "._("DONT_HAVE_REGISTRATION_-2")." <a href=\"".$view_cadastro."cadastro.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."\">"._("CLICK_HERE_-2")."</a><br />");
}else{
	// 90 - Se nao tiver cadastro,
	// 101 - clique aqui!
	echo("                    "._("DONT_HAVE_REGISTRATION_-2")." <a href='".$view_cadastro."cadastro.php'>"._("CLICK_HERE_-2")."</a><br />");
}
// 67 - Se esqueceu seu login,
// 101 - clique aqui!
echo ("                    <br/>"._("FORGOT_LOGIN_-2")." <a href='esqueci_login.php'>"._("CLICK_HERE_-2")."</a><br/>");

// 24 - Se esqueceu sua senha,
// 101 - clique aqui!
echo ("                    "._("FORGOT_PASSWORD_-2")." <a href='esqueci_senha.php'>"._("CLICK_HERE_-2")."</a><br/>");

// 92 - Se nao recebeu seu email de confirmacao,
// 101 - clique aqui!
echo ("                    "._("DIDNT_RECEIVE_EMAIL_CONFIRMATION_-2")." <a href='reenviar_autenticacao.php'>"._("CLICK_HERE_-2")."</a><br/>");

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