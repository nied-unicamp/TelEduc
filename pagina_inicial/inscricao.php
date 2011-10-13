<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : pagina_inicial/inscricao.php

    TelEduc - Ambiente de Ensino-Aprendizagem a DistÔøΩncia
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

    Nied - NÔøΩcleo de InformÔøΩtica Aplicada ÔøΩ EducaÔøΩÔøΩo
    Unicamp - Universidade Estadual de Campinas
    Cidade UniversitÔøΩria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : pagina_inicial/inscricao.php
  ========================================================== */

  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("inicial.inc");

  
  $cod_curso = $_GET["cod_curso"];
  $tipo_curso = $_GET["tipo_curso"];
  $origem = $_GET["origem"];  //origem tera valor de NULL ou 'confirmacao' se a chamada da inscricao.php vier do confirmacao.php
  
  $sock = Conectar("");
  
  $pag_atual = "inscricao.php";
  /* Caso o usu√°rio naum esteja logado, direciona para p√°igna de login */
  if (empty($_SESSION['login_usuario_s']) && $origem == NULL)
  {
    /* ObtÔøΩ a raiz_www */
    $query = "select diretorio from Diretorio where item = 'raiz_www'";
    $res = Enviar($sock,$query);
    $linha = RetornaLinha($res);
    $raiz_www = $linha[0];

    $caminho = $raiz_www."/pagina_inicial";

    header("Location: {$caminho}/autenticacao_cadastro.php?cod_curso=".$cod_curso."&tipo_curso=".$tipo_curso."&destino=inscricao");
    exit;
  }

  $dados_curso=RetornaDadosMostraCurso($sock,$cod_curso);
  Desconectar($sock);
      
  include("../topo_tela_inicial.php");
  echo("    <script type=\"text/javascript\">\n\n");

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("      }\n\n");

  echo("    </script>\n\n");

  include("../menu_principal_tela_inicial.php");

  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");
  /*159 - Inscricao */
  echo("          <h4>".RetornaFraseDaLista($lista_frases,159)."</h4>\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../cursos/aplic/imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../cursos/aplic/imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../cursos/aplic/imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("          <span class=\"btsNav\" onclick=\"javascript:history.back(-1);\"><img src=\"../cursos/aplic/imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></span><br /><br />\n");

  echo("          <table cellpadding=\"0\" cellspacing=\"0\"  id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("            <tr>\n");
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  /* 23 - Voltar (Ger) */
  echo("                  <li><a href=\"mostra_curso.php?cod_curso=".$cod_curso."&amp;tipo_curso=".$tipo_curso."\">".RetornaFraseDaLista($lista_frases_geral,23)."</a></li>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table cellspacing=\"0\" class=\"tabInterna\">\n");

  $hoje=time();
  $ontem=$hoje - 86400;

  if ($dados_curso['inscricao_fim']<$ontem)
  {
    /* 100 - Esse curso jÔøΩ teve suas inscriÔøΩÔøΩes encerradas. */
    echo("                  <tr>\n");
    echo("                    <td>".RetornaFraseDaLista($lista_frases,100)."</td>\n");
    echo("                  </tr>\n");
  }
  else if(!ParticipaDoCurso($cod_curso))
  {
    $sock = Conectar("");
    $cod_usuario = CadastraUsuario($sock,$_SESSION["cod_usuario_global_s"],$cod_curso); 

    $dados_usuario = RetornaDadosUsuario($sock,$_SESSION["cod_usuario_global_s"]);
    /* Tudo ok */
    /* 113 - Inscricao para o curso */
    $linha=DadosCursoParaEmail($sock,$cod_curso);
    $assunto = RetornaFraseDaLista($lista_frases,113)." \"".$dados_curso['nome_curso']."\" ";

    /* 108 - Seu pedido de matricula no curso */
    /* 109 - foi realizado corretamente. */
    /* 110 - O coordenador e os formadores do curso analisarao o seu pedido e em breve entrarao em contato por e-mail com voce aceitando-o ou nao como aluno em sua disciplina.  */
    /* 114 - Atenciosamente, Coordenador(a) */
    $mensagem ="<p>".RetornaFraseDaLista($lista_frases,108)." <b>".$dados_curso['nome_curso']."</b> ".RetornaFraseDaLista($lista_frases,109)."</p>\n";
    $mensagem.="<p>".RetornaFraseDaLista($lista_frases,110)."</p>\n";
    $mensagem.="<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,114)." ".$linha['nome_coordenador']."</p>";

    $nome_destino=$dados_usuario['nome'];
    $destino=$dados_usuario['email'];
    $remetente=$linha['email'];

    $query="select diretorio from Diretorio where item='raiz_www'";
    $res=Enviar($sock,$query);
    $raiz_www_linha=RetornaLinha($res);
    $raiz_www = $raiz_www_linha[0];

    $host=RetornaConfiguracao($sock,"host");

    /* EndereÁo do Link de gerenciamento */
    $link_gerenciamento = "<a href='";
    $link_gerenciamento.= "http://".$host.$raiz_www."/cursos/aplic/administracao/";
 	$link_gerenciamento.= "gerenciamento.php?cod_curso=".$dados_curso['cod_curso'];
 	$link_gerenciamento.= "&cod_usuario=".$dados_curso['cod_coordenador'];
 	$link_gerenciamento.= "&cod_ferramenta=0&acao=N";
 	$link_gerenciamento.= "'> Gerenciamento de InscriÁıes </a>";
    
    /* 188 - Um pedido de matricula no curso */
    /* 189 - foi solicitado. */
    /* 190 - Para ver os dados do aluno, aeita-lo no curso, aceita-lo como visitante ou rejeitar o pedido, acesse o item Administracao e, em seguida, Gerenciamento de Inscricoes desse curso. */
    /* 191 - Atenciosamente, Administracao do Ambiente TelEduc. */
    $mensagem_coord = "<p>".RetornaFraseDaLista($lista_frases,188)." <b>".$dados_curso['nome_curso']."</b> ".RetornaFraseDaLista($lista_frases,189)."</p>\n";
    $mensagem_coord.= "<p>".RetornaFraseDaLista($lista_frases,190)."</p>\n";
    $mensagem_coord.= "<p>".$link_gerenciamento."</p>";
    $mensagem_coord.= "<p style=\"text-align:right;\">".RetornaFraseDaLista($lista_frases,191)."</p>";

    Desconectar($sock);

    $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto, -1, $nome_destino);
    MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

    /* Enviar mensagem para coordenador */
    $destino = $remetente;

    $remetente = RetornaDadosConfig('adm_email'); 
    $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso, $mensagem_coord, $assunto, -1, $linha['nome_coordenador']);
    MandaMsg($linha['email'], $destino,$assunto,$mensagem_envio);
    /* 108 - Seu pedido de matrÔøΩcula no curso */
    /* 109 - foi realizado corretamente. */
    /* 110 - O coordenador e os formadores do curso analisarÔøΩo o seu pedido e em breve entrarÔøΩo em contato por e-mail com vocÔøΩ aceitando-o ou nÔøΩo como aluno em sua disciplina.  */
    /* 111 - Uma mensagem foi enviada para seu e-mail confirmando a inscriÔøΩÔøΩo no curso. Caso nÔøΩo o receba nas prÔøΩximas 24 horas, provavelmente significa que preencheu o e-mail errado. Neste caso, terÔøΩ que fazer a inscriÔøΩÔøΩo novamente. */
    echo("                  <tr>\n");
    echo("                    <td align=left>\n");
    echo("                      <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,108)."&nbsp;<b>".$dados_curso['nome_curso']."</b> ".RetornaFraseDaLista($lista_frases,109)."</p>\n");
    echo("                      <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,110)."</p>\n");
   // echo("                      <p style=\"text-indent:15px;\">".RetornaFraseDaLista($lista_frases,111)."</p>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }
  else
  {
    echo("                  <tr>\n");
    echo("                    <td>\n");
    
    if(RejeitadoDoCurso($cod_curso)){
    	/* 221 - Desculpe-nos, mas você já solicitou inscrição para este curso no dia */
    	/* 222 - e seu pedido foi rejeitado.*/
    	echo("						".RetornaFraseDaLista($lista_frases,221)." ".RetornaDataInscricao($cod_curso)." ".RetornaFraseDaLista($lista_frases,222)."\n");	
    }
    else{
	    // 160 - Usuario ja participa deste curso!
    	echo("                      ".RetornaFraseDaLista($lista_frases,160)."\n");
    }
    echo("                    </td>\n");
    echo("                  </tr>\n");
  }

  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  include("../rodape_tela_inicial.php");
  echo("  </body>\n");
  echo("</html>");
?>
