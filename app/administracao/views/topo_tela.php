<?php

$ferramenta_geral = 'geral';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$diretorio_jscss = "../../../web-content/js-css/";


require_once $model_geral.'geral.inc';
require_once $model_geral.'menu.inc';
require_once $model_geral.'inicial.inc';

/* Se o teleduc naum pegou o cod_curso, pegamos para ele =) */
if (!isset($cod_curso)){
	if (isset($_GET['cod_curso'])){
		$cod_curso = $_GET['cod_curso'];
	} else if (isset($_POST['cod_curso'])){
		$cod_curso = $_POST['cod_curso'];
	}

}

$cod_usuario_global = AcessoPHP::VerificaAutenticacao($cod_curso);
$sock = AcessoSQL::Conectar("");

$lingua_curso = Menu::RetornaLinguaCurso($sock,$cod_curso);

// Se diferente, então língua do curso é diferente da língua do usuário, atualiza a lista de frases
if($lingua_curso != $_SESSION['cod_lingua_s']) {
	$lingua = $lingua_curso;
	
	if($lingua == 1){
		$locale = "pt_BR";
	}
	else if($lingua == 3){
		$locale = "en_US";
	}
	else if($lingua == 4){
		$locale = "pt_PT";
	}
}
else{
	$lingua = $_SESSION['cod_lingua_s'];
	
	if($lingua == 1){
		$locale = "pt_BR";
	}
	else if($lingua == 3){
		$locale = "en_US";
	}
	else if($lingua == 4){
		$locale = "pt_PT";
	}
}

if (!isset($cod_ferramenta))
	$cod_ferramenta = 1; /* Agenda */

$tela_ordem_ferramentas = Menu::RetornaOrdemFerramentas($sock);
$tela_lista_ferramentas = Menu::RetornaListaFerramentas($sock);
$tela_lista_titulos     = Menu::RetornaListaTitulos($sock, $_SESSION['cod_lingua_s']);
$tela_email_suporte     = Menu::RetornaConfiguracao($sock,"adm_email");

$tela_raiz_www = Menu::RetornaDiretorio($sock);

$tela_host = Menu::RetornaConfiguracao($sock,"host");

$cod_usuario = Usuarios::RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
Usuarios::VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

$tela_formador      = Usuarios::EFormador($sock, $cod_curso, $cod_usuario);
$tela_formadormesmo = Usuarios::EFormadorMesmo($sock, $cod_curso, $cod_usuario);
// booleano, indica se usuario é colaborador
$tela_colaborador   = Usuarios::EColaborador($sock, $cod_curso, $cod_usuario);
// booleano, indica se usuario é visitante
$tela_visitante     = Usuarios::EVisitante($sock, $cod_curso, $cod_usuario);

$SalvarEmArquivo = (!isset($SalvarEmArquivo) || $SalvarEmArquivo != 1) ? 0 : 1;

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("TelEduc", "../../../gettext/i18n");
textdomain("TelEduc");

AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

Usuarios::VerificaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, $cod_ferramenta);
Usuarios::MarcaAcesso($sock, $cod_usuario, $cod_ferramenta);

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n");
  echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc - ".$tela_lista_titulos[$cod_ferramenta]."</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\">\n");
  echo("    <meta name=\"description\" content=\"TelEduc\">\n");
  echo("    <meta name=\"keywords\" content=\"TelEduc\">\n");
  echo("    <meta name=\"owner\" content=\"TelEduc\">\n");
  echo("    <meta name=\"copyright\" content=\"TelEduc\">\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n");
  echo("    <link rel=\"shortcut icon\" href=\"../../../favicon.ico\" />\n");

  $estilos_css_default = array($diretorio_jscss."ambiente.css",
                               $diretorio_jscss."navegacao.css",
                               $diretorio_jscss."tabelas.css",
                               $diretorio_jscss."dhtmlgoodies_calendar.css");

  $codigos_js_default = array($diretorio_jscss."dhtmllib.js",
                              $diretorio_jscss."dhtmlgoodies_calendar.js",
                              $diretorio_jscss."jscript.js",
                              //"../js-css/chat.js"
                        );

  // Se antes da inclusï¿½o de topo_tela.php, a pï¿½gina jï¿½ indicou 
  // arquivos css ou js, devemos incluï¿½-los no cabeï¿½alho da pï¿½gina.

  if (isset($estilos_css) && is_array($estilos_css)) {
    $estilos_css = array_merge($estilos_css_default, $estilos_css);
  }
  else {
    $estilos_css = $estilos_css_default;
  }

  if (isset($codigos_js) && is_array($codigos_js)) {
    $codigos_js = array_merge($codigos_js_default, $codigos_js);
  }
  else {
    $codigos_js = $codigos_js_default;
  }

  /* Se estamos salvando a pagina em um arquivo, manter o css inline e sem javascript.
   * Caso contrario podemos servi-los normalmente sob a forma de links.
   */
  if ($SalvarEmArquivo) {

    array_push($estilos_css, $diretorio_jscss."salvaremarquivo.css");
    echo("<style>".Menu::RetornaCSSInline($estilos_css)."</style>");

  } else {

    foreach ($estilos_css as $css){
      echo("    <link href=\"".$css."\" rel=\"stylesheet\" type=\"text/css\">\n");
    }

    foreach ($codigos_js as $js){
      echo("    <script type=\"text/javascript\" src=\"".$js."\"></script>\n");
    }

  }
?>
