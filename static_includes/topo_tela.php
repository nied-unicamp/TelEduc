<?php 
$dir_css = '../../../css/';
$dir_js = '../../../js';

$ctrl_geral = '../../../app/geral/controller/';

require $ctrl_geral.'PapelController.php';
require $ctrl_geral.'PermissaoController.php';

$estilos_css = null; //TODO
$codigos_js = null; //TODO
$SalvarEmArquivo = null; //TODO

$cod_curso = 1;
$cod_usuario = 2;
$cod_ferramenta = 1;

$controlerPermissao = new PermissaoController();

$controlerPapel = new PapelController();

$lista_permissao = $controlerPermissao->retornaPermissaoUsuario($cod_usuario, $cod_ferramenta);

$papel_usuario = $controlerPapel->retornaPapelUsuarioCurso($cod_curso, $cod_usuario);

  /* $bibliotecas="../bibliotecas/";
  include("menu.inc");
  require_once("../xajax_0.5/xajax_core/xajax.inc.php"); */

  /* Se o teleduc naum pegou o cod_curso, pegamos para ele =) */
/*   if (!isset($cod_curso)){
    if (isset($_GET['cod_curso'])){
      $cod_curso = $_GET['cod_curso'];
    } else if (isset($_POST['cod_curso'])){
      $cod_curso = $_POST['cod_curso'];
    }
  }

  $cod_usuario_global = VerificaAutenticacao($cod_curso);
  $sock = Conectar("");

  $lingua_curso = RetornaLinguaCurso($sock,$cod_curso);

  // Se diferente, entao lingua do curso eh diferente da lingua do usuario, atualiza a lista de frases
  if($lingua_curso != $_SESSION['cod_lingua_s']) {
    MudancaDeLingua($sock, $lingua_curso);
  }

  if (!isset($cod_ferramenta))
    $cod_ferramenta = 1; /* Agenda */

  /*$lista_frases_menu  = RetornaListaDeFrases($sock, -4);
  $lista_frases       = RetornaListaDeFrases($sock, $cod_ferramenta);
  $lista_frases_geral = RetornaListaDeFrases($sock, -1);

  $tela_ordem_ferramentas = RetornaOrdemFerramentas($sock);
  $tela_lista_ferramentas = RetornaListaFerramentas($sock);
  $tela_lista_titulos     = RetornaListaTitulos($sock, $_SESSION['cod_lingua_s']);
  $tela_email_suporte     = RetornaConfiguracao($sock,"adm_email");

  $query="select diretorio from Diretorio where item='raiz_www'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $tela_raiz_www = $linha[0];

  $tela_host=RetornaConfiguracao($sock,"host");

  $cod_usuario = RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso);
  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $tela_formador      = EFormador($sock, $cod_curso, $cod_usuario);
  $tela_formadormesmo = EFormadorMesmo($sock, $cod_curso, $cod_usuario);
  // booleano, indica se usuario eh colaborador
  $tela_colaborador   = EColaborador($sock, $cod_curso, $cod_usuario);
  // booleano, indica se usuario eh visitante
  $tela_visitante     = EVisitante($sock, $cod_curso, $cod_usuario);

  $SalvarEmArquivo = (!isset($SalvarEmArquivo) || $SalvarEmArquivo != 1) ? 0 : 1;

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAFerramenta($sock, $cod_curso, $cod_usuario, $cod_ferramenta);
  MarcaAcesso($sock, $cod_usuario, $cod_ferramenta); */

  echo("<!DOCTYPE HTML SYSTEM \"http://teleduc.nied.unicamp.br/~teleduc/loose-custom.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc - Agenda </title>\n"); //TODO: função para retornar nome da ferramenta
  echo("    <meta name=\"robots\" content=\"follow,index\">\n");
  echo("    <meta name=\"description\" content=\"TelEduc\">\n");
  echo("    <meta name=\"keywords\" content=\"TelEduc\">\n");
  echo("    <meta name=\"owner\" content=\"TelEduc\">\n");
  echo("    <meta name=\"copyright\" content=\"TelEduc\">\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n");
  echo("    <link rel=\"shortcut icon\" href=\"../../../favicon.ico\" />\n");

  $estilos_css_default = array($dir_css."ambiente.css",
                               $dir_css."navegacao.css",
                               $dir_css."tabelas.css",
                               $dir_css."dhtmlgoodies_calendar.css");

  $codigos_js_default = array($dir_js."dhtmllib.js",
                              $dir_js."dhtmlgoodies_calendar.js",
                              $dir_js."jscript.js",
                              //"../js-css/chat.js"
                        );

  // Se antes da inclusao de topo_tela.php, a pagina ja indicou 
  // arquivos css ou js, devemos inclui-los no cabecalho da pagina.

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

    array_push($estilos_css, $dir_css."salvaremarquivo.css");
    echo("<style>".RetornaCSSInline($estilos_css)."</style>");

  } else {

    foreach ($estilos_css as $css){
      echo("    <link href=\"".$css."\" rel=\"stylesheet\" type=\"text/css\">\n");
    }

    foreach ($codigos_js as $js){
      echo("    <script type=\"text/javascript\" src=\"".$js."\"></script>\n");
    }

  }
?>