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

// Se diferente, ent�o l�ngua do curso � diferente da l�ngua do usu�rio, atualiza a lista de frases
if($lingua_curso != $_SESSION['cod_lingua_s']) {
	Linguas::MudancaDeLingua($sock, $lingua_curso);
}

if (!isset($cod_ferramenta))
	$cod_ferramenta = 1; /* Agenda */

$lista_frases_menu  = Linguas::RetornaListaDeFrases($sock, -4);
$lista_frases       = Linguas::RetornaListaDeFrases($sock, $cod_ferramenta);
$lista_frases_geral = Linguas::RetornaListaDeFrases($sock, -1);

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
// booleano, indica se usuario eh colaborador
$tela_colaborador   = Usuarios::EColaborador($sock, $cod_curso, $cod_usuario);
// booleano, indica se usuario eh visitante
$tela_visitante     = Usuarios::EVisitante($sock, $cod_curso, $cod_usuario);

$SalvarEmArquivo = (!isset($SalvarEmArquivo) || $SalvarEmArquivo != 1) ? 0 : 1;

AcessoSQL::Desconectar($sock);

$sock=AcessoSQL::Conectar($cod_curso);

Usuarios::VerificaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, $cod_ferramenta);
Usuarios::MarcaAcesso($sock, $cod_usuario, $cod_ferramenta);

 echo("<!DOCTYPE HTML zSYSTEM \"http://teleduc.nied.unicamp.br/~teleduc/loose-custom.dtd\">\n");
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

  // Se antes da inclus�o de topo_tela.php, a p�gina j� indicou 
  // arquivos css ou js, devemos inclu�-los no cabe�alho da p�gina.

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