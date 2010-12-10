<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/ver.php

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
  ARQUIVO : cursos/aplic/avaliacoes/ver.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  require_once("../xajax_0.2.4/xajax.inc.php");
       
  //Estancia o objeto XAJAX
  $objAjax = new xajax();
  //Registre os nomes das fun?es em PHP que voc?quer chamar atrav? do xajax
  $objAjax->registerFunction("EditarTexto");
  $objAjax->registerFunction("EditarCampo");
  $objAjax->registerFunction("AlterarPeriodoDinamic");
  $objAjax->registerFunction("DecodificaString");
  $objAjax->registerFunction("RetornaFraseGeralDinamic");
  $objAjax->registerFunction("AbreEdicao");
  $objAjax->registerFunction("AcabaEdicaoDinamic");

  //Manda o xajax executar os pedidos acima.
  $objAjax->processRequests();

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,COD_AVALIACAO);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);
  $lista_frases_biblioteca =RetornaListaDeFrases($sock,-2);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);
  VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario, COD_AVALIACAO);

  // Verifica se o usuario eh formador.
  $usr_formador = EFormador($sock, $cod_curso, $cod_usuario);
  $usr_aluno = EAluno($sock, $cod_curso, $cod_usuario);

  // Guarda dados da avaliação atual
  $dados_avaliacao = RetornaAvaliacaoCadastrada($sock,$cod_avaliacao);

  if (EConvidado($sock, $cod_usuario) || EVisitante($sock, $cod_curso, $cod_usuario))
  {
    echo("<html>\n");
    echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    // 1 - Avalia��es
    $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
    // 94 - Usu�rio sem acesso
    $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 94)."</b>";
    echo(PreparaCabecalho($cod_curso, $cabecalho, COD_AVALIACAO, 1));
    echo("    <br>\n");
    echo("    <p>\n");

    echo("  </body>\n");
    echo("  </html>\n");
    exit();
  }

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n"); echo("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html lang=\"pt\">\n");
  echo("  <head>\n");
  echo("    <title>TelEduc . Ensino &agrave; Dist&acirc;ncia</title>\n");
  echo("    <meta name=\"robots\" content=\"follow,index\" />\n");
  echo("    <meta name=\"description\" content=\"\" />\n");
  echo("    <meta name=\"keywords\" content=\"\" />\n");
  echo("    <meta name=\"owner\" content=\"\" />\n");
  echo("    <meta name=\"copyright\" content=\"\" />\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("    <link href=\"../js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
  echo "    <script type=\"text/javascript\" src=\"../bibliotecas/rte/html2xhtml.js\"></script>\n";
  echo "    <script type=\"text/javascript\" src=\"../bibliotecas/rte/richtext.js\"></script>\n";
  echo("    <script type='text/javascript' src='../bibliotecas/dhtmllib.js'></script>\n");
  echo("    <script type=\"text/javascript\">\n");
  echo("      <!--\n");
  //Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML)
  echo "        initRTE(\"../bibliotecas/rte/images/\", \"../bibliotecas/rte/\", \"../bibliotecas/rte/\", true);\n";
  echo "      //-->\n";
  echo "    </script>\n";

  echo("    <script type=\"text/javascript\">\n");

  echo("      var cod_curso='".$cod_curso."';\n");
  echo("      var cod_usuario='".$cod_usuario."';\n");
  echo("      var ferramenta='".$dados_avaliacao['Ferramenta']."';\n");
  echo("      var cod_atividade='".$dados_avaliacao['Cod_atividade']."';\n");
  echo("      var cod_avaliacao='".$cod_avaliacao."';\n");
  echo("      var tela_avaliacao='".$tela_avaliacao."';\n");

  if ($SalvarEmArquivo)
  {
    echo("<style>\n");
    include("../teleduc.css");
    include("avaliacoes.css");
    echo("</style>\n");
  }
  else
  {
    $avaliacao_participante = VerificaAvalicaoParticipantes($sock ,$cod_avaliacao);

    if ($usr_formador)
    {
      echo("  function Historico()\n");
      echo("  {\n");
      $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
      $nome_janela = "'AvaliacoesHistorico'";
      echo("    window.open('historico_avaliacoes.php?&cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."', ".$nome_janela.", ".$param.");\n");
      echo("    return false; \n");
      echo("  }\n");

      echo("    function ExcluirAvaliacao()\n");
      echo("    {\n");
      /* 129 - Voc� tem certeza de que deseja excluir esta avalia��o? */
      /* 130 - (a avalia��o ser� exclu�da definitivamente) */
      echo("      if(confirm('".RetornaFraseDaLista($lista_frases,129).RetornaFraseDaLista($lista_frases,130)."'))\n");
      echo("      {\n");
      echo("        document.frmAvaliacao.action = 'acoes.php'; \n");
      echo("        document.frmAvaliacao.acao.value= 'excluirAvaliacao'; \n");
      echo("        document.frmAvaliacao.submit();\n");
      echo("      }\n");
      echo("    }\n\n");
                                                     
    }
      
    echo("  function AvaliarParticipantes()\n");
    echo("  {\n");
    echo("    document.frmAvaliacao.action = 'avaliar_participantes.php';\n");
    echo("    document.frmAvaliacao.submit();\n");
    //echo("    return false;\n");
    echo("  }\n");

    echo("  function SalvarVerAvaliacao()\n");
    echo("  {\n");
    echo("    document.frmSalvar.action = 'salvar_ver_avaliacao.php'; \n");
    echo("    document.frmSalvar.submit();\n");
    echo("  }\n");

  }

  echo("  function ImprimirRelatorio()\n");
  echo("  {\n");
  echo("    if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape') \n");
  echo("    {\n");
  echo("      self.print();\n");
  echo("    }\n");
  echo("    else\n");
  echo("    {\n");
    // 51 (gen)- Infelizmente n�o foi poss�vel imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir.
  echo("      alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
  echo("    }\n");
  echo("  }\n");

  echo("  function mudafonte(tipo){\n");
  echo("    if ( tipo==0 ){\n");
  echo("      document.getElementById(\"tabelaInterna\").style.fontSize=\"11px\";\n");
  echo("      tipo = '';\n");
  echo("    }\n");
  echo("    if ( tipo==1 ){\n");
  echo("      document.getElementById(\"tabelaInterna\").style.fontSize=\"14px\";\n");
  echo("      tipo = '';\n");
  echo("    }\n");
  echo("    if ( tipo==2 ){\n");
  echo("      document.getElementById(\"tabelaInterna\").style.fontSize=\"16px\";\n");
  echo("      tipo = '';\n");
  echo("    }\n");
  echo("  }\n");

  echo("</script>\n");
  
  $objAjax->printJavascript("../xajax_0.2.4/");  
  echo("    <script type='text/javascript' src='jscriptlib.js'></script>\n");

  echo("  </head>\n");

  // A variavel tela_avaliacao indica quais avaliacoes devem ser listadas: 'P'assadas, 'A'tuais ou 'F'uturas
  if (!isset($tela_avaliacao) || !in_array($tela_avaliacao, array('P', 'A', 'F')))
  {
    $tela_avaliacao = 'A';
  }

  // Determinamos a frase que descreve as avaliacoes e a lista de avaliacoes
  if ($tela_avaliacao == 'P')
    // 29 - Avalia��es Passadas
    $lista_avaliacoes = RetornaAvaliacoesAnteriores($sock,$usr_formador);
  elseif ($tela_avaliacao == 'A')
    // 32 - Avalia��es Atuais
    $lista_avaliacoes = RetornaAvaliacoesAtuais($sock,$usr_formador);
  elseif ($tela_avaliacao == 'F')
    // 30 - Avalia��es Futuras
    $lista_avaliacoes = RetornaAvaliacoesFuturas($sock,$usr_formador);

  // Determinamos a cor de cada link (amarelo ou branco) no menu superior
  $cor_link1 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link2 = array('A' => "", 'F' => "", 'P' => "");
  $cor_link1[$tela_avaliacao] = "<font color=yellow>";
  $cor_link2[$tela_avaliacao] = "</font>";

  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");
  echo("    <h1><a href=\"home.htm\"><img src=\"../imgs/logo.gif\" border=\"0\" alt=\"TelEduc . Educa&ccedil;&atilde;o &agrave; Dist&acirc;ncia\" /></a></h1>\n");
  echo("    <table style=\"cellpadding:0pt;\" cellspacing=\"0\" id=\"container\">\n");
  echo("      <tr>\n");
  echo("        <td></td>\n");
  echo("        <td valign=\"top\">\n");
  echo("          <!-- Navegacao Nivel 3 -->\n");
  echo("          <ul id=\"nav3nivel\">\n");
  echo("            <li class=\"visoes\"><a href=\"#\">Vis&atilde;o do Formador</a></li>\n");
  echo("            <li class=\"visoes\"><a href=\"#\">Vis&atilde;o do Aluno</a></li>\n");
  echo("            <li><a href=\"#\">Configura&ccedil;&atilde;o</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li>\n");
  echo("            <li><a href=\"#\">Suporte</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li>\n");
  echo("            <li><a href=\"#\">Administra&ccedil;&atilde;o</a></li>\n");
  echo("          </ul>\n");
  echo("          <div id=\"btsNivel3\"><span class=\"ajuste1\"><img src=\"../imgs/icAjuda.gif\" border=\"0\" alt=\"Ajuda\" /></span>&nbsp;&nbsp;<a href=\"#\">ajuda</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href=\"#\">X sair</a></div>\n");	
  echo("          <h3>Curso de Teste do Ambiente</h3>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td width=\"140\" valign=\"top\">\n");
  echo("          <!-- Navegacao Principal -->\n");
  echo("          <ul id=\"nav\">\n");
  echo("            <li class=\"topLine\"><a href=\"#\">Din&acirc;mica do Curso</a></li>\n");
  echo("            <li><a href=\"#\">Agenda</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Avalia&ccedil;&otilde;es</a></li>\n");
  echo("            <li><a href=\"#\">Atividades</a></li>\n");
  echo("            <li><a href=\"#\">Material de Apoio</a></li>\n");
  echo("            <li><a href=\"#\">Leituras</a></li>\n");
  echo("            <li><a href=\"#\">Perguntas Frequentes</a></li>\n");
  echo("            <li><a href=\"#\">Exerc&iacute;cios</a></li>\n");
  echo("            <li><a href=\"#\">Parada Obrigat&oacute;ria</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Mural</a></li>\n");
  echo("            <li><a href=\"#\">F&oacute;runs de Discuss&atilde;o</a></li>\n");
  echo("            <li><a href=\"#\">Bate-Papo</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Correio</a></li>\n");
  echo("            <li><a href=\"#\">Grupos</a></li>\n");
  echo("            <li><a href=\"#\">Perfil</a></li>\n");
  echo("            <li><a href=\"#\">Di&aacute;rio de Bordo</a></li>\n");
  echo("            <li class=\"endLine\"><span class=\"link\" onclick=\"window.location='ver_portfolio.php?cod_curso=1&amp;cod_ferramenta=15&amp;exibir=myp';\">Portf&oacute;lio</span></li>\n");
  echo("            <li><a href=\"#\">Acessos</a></li>\n");
  echo("            <li class=\"endLine\"><a href=\"#\">Intermap</a></li>\n");
  echo("          </ul>\n");
  echo("          <p align=\"center\"><a href=\"#\"><img src=\"../imgs/logoNied.gif\" alt=\"Nied\" width=\"28\" height=\"38\" border=\"0\" style=\"margin-right:8px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" width=\"44\" height=\"38\" border=\"0\" style=\"margin-right:6px;\" /></a> <a href=\"#\"><img src=\"../imgs/logoUnicamp.gif\" alt=\"UNICAMP\" width=\"35\" height=\"38\" border=\"0\" /></a></p>\n");
  echo("        </td>\n");
  echo("        <td width=\"100%\" valign=\"top\" id=\"conteudo\">\n");

  /* Verificação se a avaliacao está em Edição */
  /* Se estiver, voltar a tela anterior, e disparar a tela de Em Edição... */
  $linha=RetornaStatusAvaliacao($sock, "Avaliacao", $cod_avaliacao);

  if ($linha['status']=="E")
  {
    if (($linha['data']<(time()-1800)) || ($cod_usuario == $linha['cod_usuario'])){
      CancelaEdicaoAvaliacao($sock, "Avaliacao", $cod_avaliacao,$cod_usuario);
    }else{
      /* Está em edição... */
      echo("          <script language=javascript>\n");
      echo("            window.open('em_edicao.php?cod_curso=".$cod_curso."&cod_avaliacao=".$cod_avaliacao."&origem=ver','EmEdicao','width=400,height=250,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes');\n");
      echo("            window.location='avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=22&cod_avaliacao=".$cod_avaliacao."&tela_avaliacao=".$tela_avaliacao."&operacao=".$cod_operacao."';\n");
      echo("          </script>\n");
      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      echo("  </body>\n");
      echo("</html>\n");
      exit();
    }
  }

  // P�gina Principal
  // 32 - Avalia��es Atuais
  // 120 - Ver Avalia��o
  echo("          <h4>".RetornaFraseDaLista($lista_frases,1)." - ".RetornaFraseDaLista($lista_frases, 120)."</h4>\n");

  echo("          <span class=\"btsNav\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a></span>\n");

  echo("          <div id=\"mudarFonte\">\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(2)\"><img src=\"../imgs/btFont1.gif\" alt=\"Letra tamanho 3\" width=\"17\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(1)\"><img src=\"../imgs/btFont2.gif\" alt=\"Letra tamanho 2\" width=\"15\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("	    <a href=\"#\" onClick=\"mudafonte(0)\"><img src=\"../imgs/btFont3.gif\" alt=\"Letra tamanho 1\" width=\"14\" height=\"15\" border=\"0\" align=\"right\" /></a>\n");
  echo("          </div>\n");
  //<!----------------- Cabe�alho Acaba Aqui ----------------->

  //<!----------------- Tabelao ----------------->
  echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("            <tr>\n");

  //<!----------------- Botoes de Acao ----------------->
  echo("              <td>\n");
  echo("                <ul class=\"btAuxTabs\">\n");
  // 23 - Voltar (ger)
  echo("                  <li><span onclick=\"window.location='avaliacoes.php?cod_curso=".$cod_curso."&cod_usuario=".$cod_usuario."&cod_ferramenta=".$cod_ferramenta."&tela_avaliacao=".$tela_avaliacao."&operacao=".$operacao."';\">".RetornaFraseDaLista($lista_frases_geral, 23)."</span></li>\n");
  if ($usr_formador)
  {
    // 99 - Hist�rico
    echo("                  <li><span onclick=return(Historico())>".RetornaFraseDaLista($lista_frases, 99)."</span></li>\n");
    // 34 - Avaliar Participantes
    if($avaliacao_participante) /*Se permite avaliar participante*/
    echo("                  <li><span onclick=\"javascript:AvaliarParticipantes()\">".RetornaFraseDaLista($lista_frases, 34)."</span></li>\n");
  }
  else
  {
   // 105 - Hist�rico do Desempenho
   echo("                  <li><span onclick=\"javascript:AvaliarParticipantes()\">".RetornaFraseDaLista($lista_frases, 105)."</span></li>\n");
  }
  echo("    <form name=frmSalvar>\n");
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  echo("      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  // G 50 - Salvar em Arquivo
  echo("                  <li><span onClick='SalvarVerAvaliacao();'>".RetornaFraseDaLista($lista_frases_geral, 50)."</span></li>\n");
  // G 14 - Imprimir
  echo("                  <li><span onClick='ImprimirRelatorio();'>".RetornaFraseDaLista($lista_frases_geral, 14)."</span></li>\n");
  echo("    </form>\n");
  echo("                </ul>\n");
  echo("              </td>\n");
  echo("            </tr>\n");

  echo("    <form name=frmAvaliacao method=post>\n");
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  // Passa o cod_avaliacao para executar a��es sobre ela.
  echo("      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
  // $tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
  echo("      <input type=hidden name=origem value=ver>\n");
  echo("      <input type=hidden name=acao value=null>\n"); 
  echo("    </form>\n");

  $tipo = "";
  // Soh existe o conceito de tipo de avaliacao (individual ou em grupo) se for a avaliacao de uma atividade no portfolio ou em exerc�cios
  if ($dados_avaliacao['Ferramenta'] == 'P')
  {
    $existe_tipo = true;
    // 14 - Atividade no Portf�lio
    $ferramenta = RetornaFraseDaLista($lista_frases,14);
    if ($dados_avaliacao['Tipo'] == 'I')
      // 161 - Atividade individual no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 161);
    elseif ($dados_avaliacao['Tipo'] == 'G')
      // 162 - Atividade em grupo no portfolio
      $tipo = RetornaFraseDaLista($lista_frases, 162);
  }
  else if ($dados_avaliacao['Ferramenta'] == 'E')
  {
    $existe_tipo = true;
    // 173 - Exerc�cio
    $ferramenta = RetornaFraseDaLista($lista_frases,173);
    if ($dados_avaliacao['Tipo'] == 'I')
      // 176 - Exercicio individual 
      $tipo = RetornaFraseDaLista($lista_frases, 176);
    elseif ($dados_avaliacao['Tipo'] == 'G')
      // 174 - Exercicio em grupo
      $tipo = RetornaFraseDaLista($lista_frases, 174);
  }
  else if ($dados_avaliacao['Ferramenta'] == 'F')
    // 145 - F�rum de Discuss�o
    $tipo = RetornaFraseDaLista($lista_frases,145);
  elseif ($dados_avaliacao['Ferramenta'] == 'B')
    // 146 - Sess�o de Bate-Papo
    $tipo = RetornaFraseDaLista($lista_frases,146);
  else if($dados_avaliacao['Ferramenta']=='N')
  {
    if($dados_avaliacao['Tipo']=='I')
      $tipo= RetornaFraseDaLista($lista_frases, 185); 
    else
      $tipo= RetornaFraseDaLista($lista_frases, 186);
  }  

  if ($dados_avaliacao['Objetivos'] == '')
  {
    // 157 - N�o definidos
    $objetivos=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $objetivos=$dados_avaliacao['Objetivos'];

  if ($dados_avaliacao['Criterios'] == '')
  {
    // 157 - N�o definidos
    $criterios=RetornaFraseDaLista($lista_frases,157);
  }
  else
    $criterios=$dados_avaliacao['Criterios'];

  $titulo = RetornaTituloAvaliacao($sock, $dados_avaliacao['Ferramenta'], $dados_avaliacao['Cod_atividade']);
  if($usr_formador)
    $titulo="<span id=\"tit_".$dados_avaliacao['Cod_atividade']."\" class=\"linkTexto\" onclick=\"AlteraCampo('tit','".$dados_avaliacao['Cod_atividade']."');\">".$titulo."</span>";
  $valor = FormataNota($dados_avaliacao['Valor']);
  if($usr_formador)
    $valor="<span id=\"valor_".$dados_avaliacao['Cod_atividade']."\" class=\"linkTexto\" onclick=\"AlteraCampo('valor','".$dados_avaliacao['Cod_atividade']."');\">".$valor."</span>";
  $icone="<img src=../figuras/avaliacao.gif border=0> ";
  $obj = "<span id=\"text_obj\">".AjustaParagrafo($objetivos)."</span>";
  $crt = "<span id=\"text_crt\">".AjustaParagrafo($criterios)."</span>";
  $data_inicio = UnixTime2Data($dados_avaliacao['Data_inicio']);
  $data_fim = UnixTime2Data($dados_avaliacao['Data_termino']);

  echo("            <tr>\n");
  echo("              <td valign=\"top\">\n");
  echo("                <table id=\"tabelaInterna\" cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("                  <tr class=\"head alLeft\">\n");
  // 123 - T�tulo
  echo("                    <td>".RetornaFraseDaLista($lista_frases, 123)."</td>\n");
  if($usr_formador)
    // ?? - Opçoes
    echo("                    <td width=\"15%\" align=\"center\">Opcoes</td>\n");
  // 113 - Tipo de Avalia��o
  echo("                    <td width=\"15%\" align=\"center\">".RetornaFraseDaLista($lista_frases, 113)."</td>\n");
  // 19 - Valor
  echo("                    <td width=15% align=\"center\">".RetornaFraseDaLista($lista_frases, 19)."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr id='tr_".$dados_avaliacao['Cod_atividade']."'>\n");
  echo("                    <td align=left rowspan=\"3\">".$icone.$titulo."</td>\n");
  if($usr_formador)
  {
      echo("                    <td align=\"left\" valign=\"top\" class=\"botao2\">\n");
      echo("                      <ul>\n");
      // ? - Editar Objetivos
      echo("                        <li><span onClick=\"AlteraTexto('obj');\">Editar Objetivos</span></li>\n");
      // ? - Editar Criterios
      echo("                        <li><span onClick=\"AlteraTexto('crt');\">Editar Criterios</span></li>\n");
      // G 1 - Apagar
      echo("                        <li><span onClick=\"return(ExcluirAvaliacao());\">".RetornaFraseDaLista ($lista_frases_geral, 1)."</span></li>\n");
      echo("                      </ul>\n");
      echo("                    </td>\n");
  }
  echo("                    <td align=\"center\">&nbsp;&nbsp;".$tipo."</td>\n");
  echo("                    <td align=\"center\">".$valor."</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr class=\"head\">\n");
  /* 16 - Data de in�cio*/
  echo("                    <td>".RetornaFraseDaLista($lista_frases,16)."</td>\n");
  /* 17 - Data de T�rmino */
  echo("                    <td>".RetornaFraseDaLista($lista_frases,17)."</td>\n");
  if($usr_formador)
    echo("                    <td>&nbsp;</td>\n");
  echo("                  </tr>\n");
  echo("                  <tr>\n");
  if($usr_formador)
  {
    echo("                    <form name=\"frmAlteraPeriodo\" id=\"frmAlteraPeriodo\" method=\"post\" action='' onsubmit=\"Valida(); return false;\">\n");
    echo("                      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    echo("                      <input type=hidden name=cod_avaliacao value=".$cod_avaliacao.">\n");
    echo("                      <input type=hidden name=cod_usuario value=".$cod_usuario.">\n");
    echo("                      <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
  }
  /* 16 - Data de in�cio*/
  echo("                    <td>\n");
  if($usr_formador)
    echo("                      <input type='text' id='data_inicio' name='data_inicio' size='10' maxlength='10' value='".$data_inicio."' class='input' /><img src='../imgs/ico_calendario.gif' alt='' onclick=\"displayCalendar(document.getElementById ('data_inicio'),'dd/mm/yyyy',this);\" />\n");
  else
    echo("                      ".$data_inicio);
  echo("                    </td>\n");
  /* 17 - Data de T�rmino */
  echo("                    <td>\n");
  if($usr_formador)
    echo("                      <input type='text' id='data_fim' name='data_fim' size='10' maxlength='10' value='".$data_fim."' class='input' /><img src='../imgs/ico_calendario.gif' alt='' onclick=\"displayCalendar(document.getElementById ('data_fim'),'dd/mm/yyyy',this);\" />\n");
  else
    echo("                      ".$data_fim);
  echo("                    </td>\n");
  if($usr_formador)
  {
    // 43(biblioteca) - Alterar Per�odo
    echo("                    <td><input type=\"submit\" class=\"link\" style=\"width:120px;\" value=\"".RetornaFraseDaLista($lista_frases_biblioteca,43)."\" /></td>\n");
    echo("                    </form>\n");
  }
  echo("                  </tr>\n");
  // 75 - Objetivos
  echo("                  <tr class=\"head alLeft\">\n");
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,75)."</td></tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"4\" align=left>".$obj."</td></tr>\n");
  // 23 - Criterios
  echo("                  <tr class=\"head alLeft\">");
  echo("                    <td colspan=\"4\">".RetornaFraseDaLista($lista_frases,23)."</td></tr>\n");
  echo("                  <tr>\n");
  echo("                    <td class=\"itens\" colspan=\"4\" align=left>".$crt."</td></tr>\n");
  echo("                </table>\n");
  echo("              </td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");

  echo("          <span class=\"btsNavBottom\"><a href=\"javascript:history.back(-1);\"><img src=\"../imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a> <a href=\"#topo\"><img src=\"../imgs/btTopo.gif\" border=\"0\" alt=\"Topo\" /></a></span></td>\n");

  echo("          <table width=\"100%\">\n");
  echo("            <tr>\n");
  echo("              <td>&nbsp;</td>\n");
  echo("              <td valign=\"bottom\" id=\"rodape\">2006 � - TelEduc - Todos os direitos reservados.\n");
  echo("All rights reserved - NIED - UNICAMP</td>\n");
  echo("            </tr>\n");
  echo("          </table>\n");
  echo("        </td>\n");	
  echo("      </tr>\n");
  echo("    </table>\n");
  echo("  </body>\n");			
  echo("</html>\n");	

  Desconectar($sock);
  exit;

?>
