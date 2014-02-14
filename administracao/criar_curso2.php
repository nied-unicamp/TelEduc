<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : administracao/criar_curso2.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Dist�ncia
    Copyright (C) 2001  NIED - Unicamp

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2 as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    You could contact us through the following addresses:

    Nied - N�cleo de Inform�tica Aplicada � Educa��o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universit�ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : administracao/criar_curso2.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("admin.inc");

  VerificaAutenticacaoAdministracao();

  include("../topo_tela_inicial.php");

  /* Inicio do JavaScript */
  echo("  <script type=\"text/javascript\">\n\n");
  
  echo("    function Iniciar() {\n");
  echo("	startList();\n");
  echo("    }\n\n");

  echo("  </script>\n");

  include("../menu_principal_tela_inicial.php");

  $lista_frases=RetornaListaDeFrases($sock,-5);

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /* 3 - Cria��o de Curso */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,3)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

   /* 509 - Voltar */
  echo("                  <ul class=\"btsNav\"><li><span onclick=\"javascript:history.back(-1);\">&nbsp;&lt;&nbsp;".RetornaFraseDaLista($lista_frases_geral,509)."&nbsp;</span></li></ul>\n");
  
  echo("          <!-- Tabelao -->\n");
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><span title=\"".RetornaFraseDaLista($lista_frases_geral,23)."\" onClick=\"document.location='criar_curso.php'\">".RetornaFraseDaLista($lista_frases_geral,23)."</span></li>\n");
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
    $cod_usuario = RetornaCodUsuarioLogin($sock,$login);
  // senao, atribui a cod_usuario -1 para indicar que queremos cadastrar um novo usuario
  else{
  	$verifica = VerificaSeLoginExiste($sock,$login);
  	if(empty($verifica)){
  		$cod_usuario = -1;
  		}
  	else
  		$cod_usuario = RetornaCodUsuarioLogin($sock,$login);
  }
  //var_dump($cod_usuario);
  if($cod_usuario != 0)
  {
    // Criando Base

    $senha=GeraSenha();
    
    if($cod_usuario != -1)
    {
      $arrayNomeEmail = RetornaNomeEmailUsuario($sock,$login);
      //var_dump($arrayNomeEmail);
      $nome_coordenador = $arrayNomeEmail['nome'];
      $email = $arrayNomeEmail['email'];
    }

    if (isset($nova_categ) && $nova_categ!="")
      $cod_pasta=InserirCategoria($nova_categ);

    $cod_curso=CriarBaseDoCurso($nome_curso,$num_alunos,$cod_pasta,$nome_coordenador,$email,$login,$senha,$cod_usuario);

    // Criar Diret�rios

    $diretorio=RetornaDiretorio('Arquivos');

    CriaDiretorio($diretorio."/".$cod_curso);
    CriaDiretorio($diretorio."/".$cod_curso."/dinamica");
    CriaDiretorio($diretorio."/".$cod_curso."/agenda");
    CriaDiretorio($diretorio."/".$cod_curso."/atividades");
    CriaDiretorio($diretorio."/".$cod_curso."/apoio");
    CriaDiretorio($diretorio."/".$cod_curso."/leituras");
    CriaDiretorio($diretorio."/".$cod_curso."/obrigatoria");
    CriaDiretorio($diretorio."/".$cod_curso."/correio");
    CriaDiretorio($diretorio."/".$cod_curso."/perfil");
    CriaDiretorio($diretorio."/".$cod_curso."/portfolio");
    CriaDiretorio($diretorio."/".$cod_curso."/exercicios");
    CriaDiretorio($diretorio."/".$cod_curso."/extracao");

    // Enviar e-mail para o coordenador
    $sock = Conectar("");

    $query="select valor from Config where item = 'host'";
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $host=$linha['valor'];

    $query="select diretorio from Diretorio where item='raiz_www'";
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    $raiz_www=$linha['diretorio'];

    $remetente = RetornaConfig('adm_email'); 
    $destino = $email;
    $nome_aluno = $nome_coordenador;
    $endereco=$host.$raiz_www;

    /* 99 - Informa��es para acesso ao curso no TelEduc */
    $assunto = RetornaFraseDaLista($lista_frases,99);

    /* 100 - Seu pedido para realiza��o do curso*/
    /* 101 - foi aceito.*/
    /* 102 - Para acessar o curso, a sua Identifica��o �:*/
    /* 103 - e a sua senha �:*/
    /* 104 - O acesso deve ser feito a partir do endereco:*/
    /* 105 - Atenciosamente, Administra��o do Ambiente TelEduc*/

    $mensagem ="<p>".$nome_aluno.",</p>\n";
    $mensagem.="<p>".RetornaFraseDaLista($lista_frases,100)." ".$nome_curso." ".RetornaFraseDaLista($lista_frases,101)."</p>\n";
    if($cod_usuario == -1)
    {
      //um novo usuario foi cadastrado, entaum devemos enviar-lhe seus dados para acessar o teleduc
      $mensagem.="<p>".RetornaFraseDaLista($lista_frases,102)." <big><em><strong>".$login."</strong></em></big> ";
      $mensagem.=RetornaFraseDaLista($lista_frases,103)." <big><em><strong>".$senha."</strong></em></big></p>\n";
    }
    $mensagem.="<p>".RetornaFraseDaLista($lista_frases,104)."<br />\n";
    $mensagem.="<a href=\"http://".$endereco."/cursos/aplic/index.php?cod_curso=".$cod_curso."\">http://".$endereco."/cursos/aplic/index.php?cod_curso=".$cod_curso."</a></p>\n\n";    
    $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,105).".</p><br />\n";
    

    $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto);
    MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

    /* 106 - Curso criado corretamente. */
    echo("                      ".RetornaFraseDaLista($lista_frases,106)."<br />\n");
    /* 107 - Um email com as instru��es de acesso ao curso foi enviado ao coordenador cadastrado. */
    echo("                      ".RetornaFraseDaLista($lista_frases,107)."\n");
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
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>\n");
?>
