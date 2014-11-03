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
$ctler_login = '../../'.$ferramenta_login.'/controllers/';
$diretorio_imgs  = "../../../web-content/imgs/";

require_once $model_geral.'geral.inc';
require_once $model_admin.'admin.inc';

$optUsu = $_POST['optUsu'];
$nome_curso = $_POST['nome_curso'];
$num_alunos = $_POST['num_alunos'];
$cod_pasta = $_POST['cod_pasta'];
$nome_coordenador = $_POST['nome_coordenador'];
$email = $_POST['email'];
$login = $_POST['login'];

AcessoPHP::VerificaAutenticacaoAdministracao();

require_once $view_admin.'topo_tela_inicial.php';

/* Inicio do JavaScript */
echo("  <script type=\"text/javascript\">\n\n");

echo("    function Iniciar() {\n");
echo("	startList();\n");
echo("    }\n\n");

echo("  </script>\n");

require_once $view_admin.'menu_principal_tela_inicial.php';

$lista_frases=Linguas::RetornaListaDeFrases($sock,-5);

echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
/* 3 - Criacao de Curso */
echo("          <h4>".Linguas::RetornaFraseDaLista($lista_frases,3)."</h4>\n");

// 3 A's - Muda o Tamanho da fonte
echo("          <div id=\"mudarFonte\">\n");
echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"".$diretorio_imgs."btFont1.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"".$diretorio_imgs."btFont2.gif\"/></a>\n");
echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"".$diretorio_imgs."btFont3.gif\"/></a>\n");
echo("          </div>\n");

/* 509 - Voltar */
echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".Linguas::RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");

echo("          <!-- Tabelao -->\n");
echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
echo("            <tr>\n");
echo("              <td>\n");
echo("                <ul class=\"btAuxTabs\">\n");
/* 23 - Voltar (Ger) */
echo("                  <li><span title=\"".Linguas::RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='criar_curso.php'\">".Linguas::RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
echo("                </ul>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("            <tr>\n");
echo("              <td valign=\"top\">\n");
echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
echo("                  <tr>\n");
echo("                    <td>\n");

//caso o coordenador do curso seja um usuario cadastrado e realmente exista, cod_usuario recebe o codigo de usuario global desse usuario; recebe 0 caso contrario
if($optUsu == "sim")
	$cod_usuario = Admin::RetornaCodUsuarioLogin($sock,$login);
// senao, atribui a cod_usuario -1 para indicar que queremos cadastrar um novo usuario
else{
	$verifica = Admin::VerificaSeLoginExiste($sock,$login);
	if(empty($verifica)){
		$cod_usuario = -1;
	}
	else
		$cod_usuario = Admin::RetornaCodUsuarioLogin($sock,$login);
}
//var_dump($cod_usuario);
if($cod_usuario != 0)
{
	// Criando Base

	$senha=AcessoPHP::GeraSenha();

	if($cod_usuario != -1)
	{
		$arrayNomeEmail = Admin::RetornaNomeEmailUsuario($sock,$login);
		//var_dump($arrayNomeEmail);
		$nome_coordenador = $arrayNomeEmail['nome'];
		$email = $arrayNomeEmail['email'];
	}

	if (isset($nova_categ) && $nova_categ!="")
		$cod_pasta=Admin::InserirCategoria($nova_categ);

	$cod_curso=Admin::CriarBaseDoCurso($nome_curso,$num_alunos,$cod_pasta,$nome_coordenador,$email,$login,$senha,$cod_usuario);

	// Criar Diretorios

	$diretorio=Admin::RetornaDiretorio('Arquivos');

	Arquivos::CriaDiretorio($diretorio."/".$cod_curso);
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/dinamica");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/agenda");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/atividades");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/apoio");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/leituras");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/obrigatoria");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/correio");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/perfil");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/portfolio");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/exercicios");
	Arquivos::CriaDiretorio($diretorio."/".$cod_curso."/extracao");

	// Enviar e-mail para o coordenador
	$sock = AcessoSQL::Conectar("");

	$host = Admin::RetornaConfiguracao($sock, 'host');
	
	$raiz_www = Admin::RetornaDiretorio('raiz_www');
	
	$remetente = Admin::RetornaConfig('adm_email');
	$destino = $email;
	$nome_aluno = $nome_coordenador;
	$endereco=$host.$raiz_www;

	/* 99 - Informacoes para acesso ao curso no TelEduc */
	$assunto = Linguas::RetornaFraseDaLista($lista_frases,99);

	/* 100 - Seu pedido para realizacao do curso*/
	/* 101 - foi aceito.*/
	/* 102 - Para acessar o curso, a sua Identificacao e:*/
	/* 103 - e a sua senha e:*/
	/* 104 - O acesso deve ser feito a partir do endereco:*/
	/* 105 - Atenciosamente, Administracao do Ambiente TelEduc*/

	$mensagem ="<p>".$nome_aluno.",</p>\n";
	$mensagem.="<p>".Linguas::RetornaFraseDaLista($lista_frases,100)." ".$nome_curso." ".Linguas::RetornaFraseDaLista($lista_frases,101)."</p>\n";
	if($cod_usuario == -1)
	{
		//um novo usuario foi cadastrado, entaum devemos enviar-lhe seus dados para acessar o teleduc
		$mensagem.="<p>".Linguas::RetornaFraseDaLista($lista_frases,102)." <big><em><strong>".$login."</strong></em></big> ";
		$mensagem.=Linguas::RetornaFraseDaLista($lista_frases,103)." <big><em><strong>".$senha."</strong></em></big></p>\n";
	}
	$mensagem.="<p>".Linguas::RetornaFraseDaLista($lista_frases,104)."<br />\n";
	$mensagem.="<a href=\"http://".$endereco.$ctler_login."index_curso.php?cod_curso=".$cod_curso."\">http://".$endereco.$ctler_login."index_curso.php?cod_curso=".$cod_curso."</a></p>\n\n";
	$mensagem.="<p style=\"text-align:right;\">".Linguas::RetornaFraseDaLista($lista_frases,105).".</p><br />\n";


	$mensagem_envio = Email::MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto);
	Email::MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

	/* 106 - Curso criado corretamente. */
	echo("                      ".Linguas::RetornaFraseDaLista($lista_frases,106)."<br />\n");
	/* 107 - Um email com as instrucoes de acesso ao curso foi enviado ao coordenador cadastrado. */
	echo("                      ".Linguas::RetornaFraseDaLista($lista_frases,107)."\n");
}
else
{
	/* ?? - . */
	echo("                      Login inexistente! Volte e digite o login novamente ou escolha a opcao de cadastrar o coordenador no ambiente.\n");
}

echo("                    </td>\n");
echo("                  </tr>\n");
echo("                </table>\n");
echo("              </td>\n");
echo("            </tr>\n");
echo("          </table>\n");
echo("        </td>\n");
echo("      </tr>\n");
require_once $view_admin.'rodape_tela_inicial.php';
echo("  </body>\n");
echo("</html>\n");
?>

