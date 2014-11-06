<?php
/**
 * View para menu principal da tela inicial do modulo admin
 *
 */


/**
 *
 */

$ferramenta_geral = 'geral';
$ferramenta_login = 'login';
$ferramenta_admin = 'admin';
$ferramenta_cadastro = 'cadastro';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$ctlr_login = '../../'.$ferramenta_login.'/controllers/';
$view_login = '../../'.$ferramenta_login.'/views/';
$diretorio_imgs  = "../../../web-content/imgs/";
$view_admin = '../../'.$ferramenta_admin.'/views/';
$diretorio_imgs  = "../../../web-content/imgs/";
$view_cadastro = '../../'.$ferramenta_cadastro.'/views/';

require_once $model_geral.'geral.inc';
//$_SESSION['cod_usuario_global_s'] = -1;

$sock = AcessoSQL::Conectar("");

$cod_curso = $_GET["cod_curso"];
$tipo_curso = $_GET["tipo_curso"];

if(isset($pag_atual))
	$link = "";
else
	$link = "../pagina_inicial/";

echo("  </head>\n");
echo("  <body link=\"#0000ff\" vlink=\"#0000ff\" bgcolor=\"white\" onload=\"Iniciar();\" >\n");
echo("    <a name=\"topo1\"></a>\n");
echo("    <h1><a href=\"../\" title=\"TelEduc\"><img src=\"".$diretorio_imgs."/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o a Dist&acirc;ncia\" /></a></h1>\n");
echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"container\">\n");
echo("      <tr>\n");
echo("        <td></td>\n");
echo("        <td valign=\"top\" id=\"topo\"><!--NAVEGACAO NIVEL 3-->\n");

/* Menu superior: Equipe Login/Logout
 * Ferramenta: -3
* cod_texto=10: Equipe
* cod_texto=57: Login
* cod_texto=161: Logout
* */
echo("          <ul id=\"nav3nivel\">\n");
  echo("            <map title=\"Menu superior\">\n");
  echo("              <li class=\"visoes\"><a href=\"".$link."equipe.php\">".Linguas::RetornaFraseDaLista($lista_frases,10)."</a></li>\n");
  if(empty($_SESSION['login_usuario_s']))
    echo("              <li class=\"visoes\"><a href=\"".$view_login."autenticação_cadastro.php\">Login</a></li>\n");
  else
    echo("              <li class=\"visoes\"><a href=\"".$ctlr_login."logout.php\">Logout</a></li>\n");
  echo("            </map>\n");
  echo("          </ul>\n");

  /* Menu de Idiomas
* Se nao estiver logado, deixa links para mudar lingua do ambiente
*/
if(empty($_SESSION['login_usuario_s']))
{
echo("          <map title=\"Menu de idiomas\"><ul id=\"nav3nivel\" class=\"PosicaoBandeiras\">\n");
    $lista=Linguas::ListaLinguas($sock);
foreach($lista as $cod_lin => $lingua)
{
$lingua_pais=Linguas::LinguaLocal($sock,$cod_lin);
	echo("            <li>&nbsp;&nbsp;&nbsp;<img src=\"".$diretorio_imgs."/bandeira_".$cod_lin.".png\"style=\"vertical-align:text-bottom;\" />&nbsp;&nbsp;<a href=\"".$pag_atual."?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso."&amp;cod_lin=".$cod_lin."\">".$lingua_pais."</a></li>\n");
    }
			echo("          </ul></map>\n");
  }
  /* FIM nav3nivel */
  echo("          </ul>\n");

	/* Area Restrita
	* Se nao estiver logado ou se for admtele, permite link para a administracao
	* cod_texto=17: Administracao
	* cod_texto=18: Area Restrita
	* */
	if($_SESSION['cod_usuario_global_s'] == -1)
		echo("          <a href=\"".$view_admin."index_adm.php\" title=\"".Linguas::RetornaFraseDaLista($lista_frases,17)." (".Linguas::RetornaFraseDaLista($lista_frases,18).")\"><img src=\"".$diretorio_imgs."btAdmin.gif\" border=\"0\" alt=\"Admin\" align=\"right\" style=\"position:relative; top:22px;\" /></a>\n");
  echo("          <h3>TelEduc</h3>\n");
  echo("          <div id=\"feedback\" class=\"feedback_hidden\"><span id=\"span_feedback\">ocorreu um erro na sua solicitacao</span></div>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td rowspan=\"2\" style=\"width:200px\" valign=\"top\">\n");
  echo("          <ul id=\"nav\">\n");
  //echo("            <li><a class=\"Cabecalho\" href=\"".$link."teleduc.php\">TelEduc</a></li>\n");
	echo("            <div class=\"navCabecalho\">TelEduc</div>\n");
	echo("            <li><a href=\"".$link."teleduc.php\">".Linguas::RetornaFraseDaLista($lista_frases,3)."</a></li>\n");
  echo("            <li><a href=\"".$link."estrutura.php\">".Linguas::RetornaFraseDaLista($lista_frases,4)."</a></li>\n");
  /*Se nao estiver logado, deixa link pra se cadastrar*/
  if(empty($_SESSION['login_usuario_s']))
	if($cod_curso != NULL){
	//179 - Cadastre-se
	echo("            <li><a href=\"".$view_cadastro."cadastro.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."\">".Linguas::RetornaFraseDaLista($lista_frases,179)."</a></li>\n");
    }else{
    //179 - Cadastre-se
    echo("            <li><a href=\"".$view_cadastro."cadastro.php\">".Linguas::RetornaFraseDaLista($lista_frases,179)."</a></li>\n");
    }
    /*Se estiver logado e nao for o admtele, mostra links para o usuario acessar seus dados e seus cursos*/
    else if($_SESSION['cod_usuario_global_s'] != -1)
    {
    echo("          </ul><ul id=\"nav\">");
    //echo("            <li><a class=\"Cabecalho\" href=\"".$link."exibe_cursos.php\">".Linguas::RetornaFraseDaLista($lista_frases,187)."</a></li>\n");
    echo("            <div class=\"navCabecalho\">".Linguas::RetornaFraseDaLista($lista_frases,187)."</div>\n");
    if(Usuarios::PreencheuDadosPessoais($sock))
    echo("            <li><a href=\"".$link."exibe_cursos.php\">".Linguas::RetornaFraseDaLista($lista_frases,132)."</a></li>\n");
    		echo("            <li><a href=\"".$link."dados.php\">".Linguas::RetornaFraseDaLista($lista_frases_configurar,1)."</a></li>\n");
    }
  echo("          </ul><ul id=\"nav\">");
  echo("            <div class=\"navCabecalho\">".Linguas::RetornaFraseDaLista($lista_frases,5)."</div>\n");
  echo("            <li><a href=\"".$link."cursos_all.php?tipo_curso=I\">".Linguas::RetornaFraseDaLista($lista_frases,172)."</a></li>\n");
  echo("            <li><a href=\"".$link."cursos_all.php?tipo_curso=N\">".Linguas::RetornaFraseDaLista($lista_frases,192)."</a></li>\n");
  echo("            <li><a href=\"".$link."cursos_all.php?tipo_curso=A\">".Linguas::RetornaFraseDaLista($lista_frases,171)."</a></li>\n");
  echo("            <li><a href=\"".$link."cursos_all.php?tipo_curso=F\">".Linguas::RetornaFraseDaLista($lista_frases,173)."</a></li>\n");
  echo("            <li><a href=\"".$link."criar_curso.php\">".Linguas::RetornaFraseDaLista($lista_frases,9)."</a></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  ?>
