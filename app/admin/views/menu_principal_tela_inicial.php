<?php
/**
 * View para menu principal da tela inicial do modulo admin
 *
 */

$ferramenta_geral = 'geral';
$ferramenta_login = 'login';
$ferramenta_admin = 'admin';
$ferramenta_administracao = 'administracao';
$ferramenta_cadastro = 'cadastro';

$model_geral = '../../'.$ferramenta_geral.'/models/';
$ctrl_login = '../../'.$ferramenta_login.'/controllers/';
$view_login = '../../'.$ferramenta_login.'/views/';
$diretorio_imgs  = "../../../web-content/imgs/";
$view_admin = '../../'.$ferramenta_admin.'/views/';
$diretorio_imgs  = "../../../web-content/imgs/";
$view_cadastro = '../../'.$ferramenta_cadastro.'/views/';
$view_administracao = '../../'.$ferramenta_administracao.'/views/';

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
  echo("              <li class=\"visoes\"><a href=\"".$link."equipe.php\">"._("msg10_-3")."</a></li>\n");
  if(empty($_SESSION['login_usuario_s']))
    echo("              <li class=\"visoes\"><a href=\"".$view_login."autenticacao_cadastro.php\">"._("msg157_-3")."</a></li>\n");
  else
    echo("              <li class=\"visoes\"><a href=\"".$ctrl_login."logout.php\">"._("msg161_-3")."</a></li>\n");
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
		echo("          <a href=\"".$view_admin."index_adm.php\" title=\""._("msg17_-3")." ("._("msg18_-3").")\"><img src=\"".$diretorio_imgs."btAdmin.gif\" border=\"0\" alt=\"Admin\" align=\"right\" style=\"position:relative; top:22px;\" /></a>\n");
  echo("          <h3>TelEduc</h3>\n");
  /* 236 - Ocorreu um erro na sua solicitação*/
  echo("          <div id=\"feedback\" class=\"feedback_hidden\"><span id=\"span_feedback\">"._("msg236_-3")."</span></div>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td rowspan=\"2\" style=\"width:200px\" valign=\"top\">\n");
  echo("          <ul id=\"nav\">\n");
  //echo("            <li><a class=\"Cabecalho\" href=\"".$link."teleduc.php\">TelEduc</a></li>\n");
	echo("            <div class=\"navCabecalho\">TelEduc</div>\n");
	/*
	 3 - O que é o TelEduc
	 4 - Estrutura do Ambiente
	 */
	echo("            <li><a href=\"".$link."teleduc.php\">"._("msg3_-3")."</a></li>\n");
  echo("            <li><a href=\"".$link."estrutura.php\">"._("msg4_-3")."</a></li>\n");
  /*Se nao estiver logado, deixa link pra se cadastrar*/
  if(empty($_SESSION['login_usuario_s']))
	if($cod_curso != NULL){
	//179 - Cadastre-se
	echo("            <li><a href=\"".$view_cadastro."cadastro.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."\">"._("msg179_-3")."</a></li>\n");
    }else{
    //179 - Cadastre-se
    echo("            <li><a href=\"".$view_cadastro."cadastro.php\">"._("msg179_-3")."</a></li>\n");
    }
    /*Se estiver logado e nao for o admtele, mostra links para o usuario acessar seus dados e seus cursos*/
    else if($_SESSION['cod_usuario_global_s'] != -1)
    {
    echo("          </ul><ul id=\"nav\">");
    //echo("            <li><a class=\"Cabecalho\" href=\"".$link."exibe_cursos.php\">".Linguas::RetornaFraseDaLista($lista_frases,187)."</a></li>\n");
    /* 187 - Pessoal*/
    echo("            <div class=\"navCabecalho\">"._("msg187_-3")."</div>\n");
    if(Usuarios::PreencheuDadosPessoais($sock))
    	/*
    	 * 132 - Meus cursos
    	 * 1 - Configurar
    	 */
    echo("            <li><a href=\"".$link."exibe_cursos.php\">"._("msg132_-3")."</a></li>\n");
    		echo("            <li><a href=\"".$link."dados.php\">"._("msg1_-7")."</a></li>\n");
    }
  echo("          </ul><ul id=\"nav\">");
  /* 
   * 5 - Cursos
   * 172 - Inscrições Abertas
   * 192 - Não iniciados
   * 171 - Em Andamento
   * 173 - Encerrados
   * 9 - Como criar um curso
   */
  echo("            <div class=\"navCabecalho\">"._("msg5_-3")."</div>\n");
  echo("            <li><a href=\"".$view_administracao."cursos_all.php?tipo_curso=I\">"._("msg172_-3")."</a></li>\n");
  echo("            <li><a href=\"".$view_administracao."cursos_all.php?tipo_curso=N\">"._("msg192_-3")."</a></li>\n");
  echo("            <li><a href=\"".$view_administracao."cursos_all.php?tipo_curso=A\">"._("msg171_-3")."</a></li>\n");
  echo("            <li><a href=\"".$view_administracao."cursos_all.php?tipo_curso=F\">"._("msg173_-3")."</a></li>\n");
  echo("            <li><a href=\"".$view_administracao."criar_curso.php\">"._("msg9_-3")."</a></li>\n");
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  ?>
