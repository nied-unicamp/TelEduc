<?php


  echo("<!DOCTYPE HTML SYSTEM \"http://teleduc.nied.unicamp.br/~teleduc/loose-custom.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc - "."AgendaN"."</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\">\n");
  echo("    <meta name=\"description\" content=\"TelEduc\">\n");
  echo("    <meta name=\"keywords\" content=\"TelEduc\">\n");
  echo("    <meta name=\"owner\" content=\"TelEduc\">\n");
  echo("    <meta name=\"copyright\" content=\"TelEduc\">\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n");
  echo("    <link rel=\"shortcut icon\" href=\"../../../favicon.ico\" />\n");

 $estilos_css_default = array("../../../css/ambiente.css",
		"../../../css/navegacao.css",
		"../../../css/tabelas.css",
		"../../../css/dhtmlgoodies_calendar.css");

//   $codigos_js_default = array("../bibliotecas/dhtmllib.js",
//                               "../js-css/dhtmlgoodies_calendar.js",
//                               "../js-css/jscript.js",
//                               //"../js-css/chat.js"
//                         );

  // Se antes da inclusão de topo_tela.php, a página já indicou 
  // arquivos css ou js, devemos incluí-los no cabeçalho da página.

  if (isset($estilos_css) && is_array($estilos_css)) {
    $estilos_css = array_merge($estilos_css_default, $estilos_css);
  }
  else {
    $estilos_css = $estilos_css_default;
  }

//   if (isset($codigos_js) && is_array($codigos_js)) {
//     $codigos_js = array_merge($codigos_js_default, $codigos_js);
//   }
//   else {
//     $codigos_js = $codigos_js_default;
//   }

  /* Se estamos salvando a pagina em um arquivo, manter o css inline e sem javascript.
   * Caso contrario podemos servi-los normalmente sob a forma de links.
   */
  if (0) {

    array_push($estilos_css, "../js-css/salvaremarquivo.css");
    echo("<style>".RetornaCSSInline($estilos_css)."</style>");

  } else {

    foreach ($estilos_css as $css){
      echo("    <link href=\"".$css."\" rel=\"stylesheet\" type=\"text/css\">\n");
    }

//     foreach ($codigos_js as $js){
//       echo("    <script type=\"text/javascript\" src=\"".$js."\"></script>\n");
//     }

  }
