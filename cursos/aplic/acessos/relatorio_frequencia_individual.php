<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/acessos/relatorio_frequencia.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distâcia
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

    Nied - Ncleo de Informáica Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitáia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/acessos/relatorio_frequencia.php
  ========================================================== */

/* Código principal */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("acessos.inc");
  //include("acessos_aux.inc");

  // Correção provisória para exibição das datas
  if(isset($data_ini))
    $data_iniUT = Data2UnixTime($data_ini);
  if(isset($data_ini))
    $data_fimUT = Data2UnixTime($data_fim) + 24 * 3600 - 1;

  $dig=$data_invertida_g;
  $data_invertida_g=false;

  if(isset($data_iniUT))
    $data_ini = Unixtime2Data($data_iniUT);
  if(isset($data_fimUT))
    $data_fim = Unixtime2Data($data_fimUT);
  // Fim da correção

  $cod_ferramenta = 18;
  include("../topo_tela.php");

  ExpulsaVisitante($sock, $cod_curso, $cod_usuario, true);

  $cod_ferramenta = $_GET['cod_ferramenta_relatorio'];
  $lista_frases_ferramentas=$lista_frases_menu;
  $lista_ferramentas=$tela_lista_ferramentas;
  $ordem_ferramentas=$tela_ordem_ferramentas;

  if (!$SalvarEmArquivo)
  {
    echo("    <script type=\"text/javascript\">\n");
    echo("      function AbrePerfil(cod_usuario)\n");
    echo("      {\n");
    echo("         window.open('../perfil/exibir_perfis.php?cod_curso=".$cod_curso."&cod_aluno[]='+cod_usuario,'PerfilDisplay','width=620,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("        return(false);\n");
    echo("      }\n");

    echo("      function AbreGrupo(cod_grupo)\n");
    echo("      {\n");
    echo("          window.open('../grupos/exibir_grupo.php?cod_curso=".$cod_curso."&cod_grupo='+cod_grupo+'&esconder_barra=1','MostrarComponentes','width=500,height=300,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("      }\n");

    // opcoes de visualizacao
    // 1 - 1 usuario, 1 dia
    // 2 - 1 usuario, periodo
    // 3 - varios usuarios, 1 dia
    echo("      function AbreRelatorioUsuario(usuario,diaUT,data_iniUT,data_fimUT,opcao,cod_ferramenta_relatorio)\n");
    // variavel opcao: 1 = tratar 1 usuario, em 1 dia inteiro, das zero as 24 horas do dia diaUT
    //                 2 = tratar 1 usuario em 1 periodo limitado pelas vars data_iniUT e data_fimUT
    //                 3 = tratar varios usuarios em um unico dia diaUT  
    echo("      {\n");
    echo("         window.open('relatorio_frequencia2.php?cod_curso=".$cod_curso."&usuario='+usuario+'&diaUT='+diaUT+'&data_iniUT='+data_iniUT+'&data_fimUT='+data_fimUT+'&cod_ferramenta_relatorio='+cod_ferramenta_relatorio+'&opcao='+opcao,'JanelaFreq','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("      }\n");

    // opcoes de visualizacao
    // 1 - 1 grupo, 1 dia
    // 2 - 1 grupo, periodo
    echo("      function AbreRelatorioGrupo(cod_grupo,diaUT,data_iniUT,data_fimUT,opcao)\n");
    // variavel opcao: 1 = tratar 1 grupo, em 1 dia inteiro, das zero as 24 horas do dia diaUT
    //                 2 = tratar 1 grupo, em um periodo limitado pelas vars data_iniUT e data_fimUT
    echo("      {\n");
    echo("         window.open('relatorio_frequencia2.php?cod_curso=".$cod_curso."&cod_grupo='+cod_grupo+'&diaUT='+diaUT+'&data_iniUT='+data_iniUT+'&data_fimUT='+data_fimUT+'&cod_ferramenta_relatorio=".$cod_ferramenta_relatorio."&opcao='+opcao,'JanelaFreq','width=600,height=400,top=100,left=100,scrollbars=yes,status=yes,toolbar=no,menubar=no,resizable=no');\n");
    echo("      }\n");

    echo("      function ImprimirRelatorio()\n");
    echo("      {\n");
    echo("        if ((navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.indexOf('5.')>=0) || navigator.appName == 'Netscape')\n");
    echo("        {\n");
    echo("          self.print();\n");
    echo("        }\n");
    echo("        else\n");
    echo("        {\n");
    /* 51- Infelizmente não foi possível imprimir automaticamente esse documento. Mantenha a tecla <Ctrl> pressionada enquanto pressiona a tecla <p> para imprimir. */
    echo("          alert('".RetornaFraseDaLista($lista_frases_geral,51)."');\n");
    echo("        }\n");
    echo("      }\n");
  }else{
    echo("    <style>\n");
    include "../js-css/ambiente.css";
    include "../js-css/tabelas.css";
    include "../js-css/navegacao.css";
    echo("    </style>\n");
    echo("    <script type=\"text/javascript\">\n");
  }

  echo("      function Iniciar()\n");
  echo("      {\n");
  echo("        startList();\n");
  echo("        self.focus();\n");
  echo("      }\n\n");
  echo("    </script>\n");

  echo("  </head>\n");
  echo("  <body link=#0000ff vlink=#0000ff bgcolor=white onLoad=\"Iniciar();\">\n");
  echo("    <a name=\"topo\"></a>\n");

  if (isset($cod_ferramenta_relatorio))
    $nome_ferramenta = RetornaFraseDaLista($lista_frases_menu,$tela_lista_ferramentas[$cod_ferramenta_relatorio]['cod_texto_nome']);
  else
    /* 29 - Entrada no ambiente */
    $nome_ferramenta = RetornaFraseDaLista($lista_frases,29);

  /* 1 - Acessos */
  $cabecalho ="<h4>".RetornaFraseDaLista($lista_frases,1);
  /* 54 - Exibir Relatório de Freqüência */
  $cabecalho.=" - ".RetornaFraseDaLista($lista_frases,54)." - ".$nome_ferramenta."</h4>";
  echo("    <br /><br />".$cabecalho."\n");
  echo("    <br />\n");

  // 3 A's - Muda o Tamanho da fonte
  echo("          <div id=\"mudarFonte\">\n");
  echo("            <a onclick=\"mudafonte(2)\" href=\"#\"><img width=\"17\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 3\" src=\"../imgs/btFont1.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(1)\" href=\"#\"><img width=\"15\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 2\" src=\"../imgs/btFont2.gif\"/></a>\n");
  echo("            <a onclick=\"mudafonte(0)\" href=\"#\"><img width=\"14\" height=\"15\" border=\"0\" align=\"right\" alt=\"Letra tamanho 1\" src=\"../imgs/btFont3.gif\"/></a>\n");
  echo("          </div>\n");

  echo("    <table cellpadding=\"0\" cellspacing=\"0\" id=\"tabelaExterna\" class=\"tabExterna\">\n");
  echo("      <tr>\n");
  echo("        <td valign=\"top\">\n");

  $exibir_grupos        = false;//isset ($check_grupos);
  $exibir_alunos        = isset ($check_alunos);
  $exibir_formadores    = isset ($check_formadores);
  $exibir_colaboradores = isset ($check_colaboradores);
  $exibir_visitantes    = isset ($check_visitantes);

/*  if (! $SalvarEmArquivo)
  {  

    echo("          <form action=\"salvar_arquivo.php\" name=\"formSalvar\">\n");
    echo("            <input type=\"hidden\" name=\"cod_curso\"    value=\"".$cod_curso."\" />\n");
    echo("            <input type=\"hidden\" name=\"origem\"       value=\"freq\" />\n");
    echo("            <input type=\"hidden\" name=\"nome_arquivo\" value=\"relatorio_frequencia.html\" />");
    /*if (isset($cod_ferramenta))
      echo("            <input type=\"hidden\" name=\"cod_ferramenta\"       value=\"".$cod_ferramenta."\" />\n");*/
/*    if(isset($data_ini))
      echo("            <input type=\"hidden\" name=\"data_ini\"             value=\"".$data_ini."\" />\n");
    if(isset($data_fim))
      echo("            <input type=\"hidden\" name=\"data_fim\"             value=\"".$data_fim."\" />\n");
    if (isset($check_part) && $check_part)
      echo("            <input type=\"hidden\" name=\"check_part\"           value=\"1\" />\n");
    if (isset($check_grupos) && $check_grupos)
      echo("            <input type=\"hidden\" name=\"check_grupos\"         value=\"1\" />\n");
    if (isset($exibir_colaboradores) && $exibir_colaboradores)
      echo("            <input type=\"hidden\" name=\"exibir_colaboradores\" value=\"1\" />\n");
    if (isset($check_formadores) && $check_formadores)
      echo("            <input type=\"hidden\" name=\"check_formadores\"     value=\"1\" />\n");  
    if (isset($check_alunos) && $check_alunos)
      echo("            <input type=\"hidden\" name=\"check_alunos\"         value=\"1\" />\n");  
    if (isset($exibir_visitantes) && $exibir_visitantes)
      echo("            <input type=\"hidden\" name=\"check_visitantes\"     value=\"1\" />\n");
    echo("          </form>\n");
*/
    echo("          <ul class=\"btAuxTabs\">\n");  
    /* 22 - Salvar Em Arquivo */
/*    echo("            <li><span onClick=\"document.formSalvar.submit();\">".RetornaFraseDaLista($lista_frases_geral,50)."</span></li>");
    /* G 14 - Imprimir */
    echo("            <li><span onClick=\"ImprimirRelatorio();\">".RetornaFraseDaLista($lista_frases_geral,14)."</span></li>\n");
    /* G 13 - Fechar */
/*    echo("            <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");    
  }
  else
  {
*/    echo("          <ul class=\"btAuxTabs\">\n");  
    /* G 13 - Fechar */
    echo("            <li><span onClick=\"self.close();\">".RetornaFraseDaLista($lista_frases_geral,13)."</span></li>\n");
//  }
  echo("          </ul>\n");
  echo("        </td>\n");
  echo("      </tr>\n");
  echo("      <tr>\n");
  echo("        <td>\n");

  /* ******************************************************************************
     Gerando tabelas com os acessos dos usuarios
  ****************************************************************************** */

  Desconectar($sock);
  $sock = Conectar("");

  $lista_grupos   = RetornaGrupos($sock, false, $exibir_alunos, $exibir_formadores, $exibir_colaboradores, $exibir_visitantes, $cod_curso);
  //$exibir_so_grupos=(!(($exibir_grupos)||($exibir_alunos)||($exibir_formadores)));
  $lista_nomes    = RetornaNomesUsuarios($sock,$cod_curso);
  Desconectar($sock);

  foreach ($ordem_ferramentas as $cod=>$linha)
  {
    if(($cod_ferramenta=$linha['cod_ferramenta']) > 0)
    {

      $sock = Conectar($cod_curso);
      $acessos_users  = RetornaUsuariosAcessos($sock, $cod_ferramenta, $data_iniUT, $data_fimUT);
      $totais_users   = RetornaTotaisUsuarios( $acessos_users );

      Desconectar($sock);
      $sock = Conectar("");
      $totais_diarios = RetornaAcessosDiarios($sock, $cod_ferramenta, $data_iniUT, $data_fimUT, $exibir_alunos, $exibir_formadores, $exibir_colaboradores, $exibir_visitantes, $exibir_grupos, $cod_curso);

      // definindo o numero de dias em uma tabela como uma constante
      define("NUM_DIAS_TABELA", 14, FALSE);
      // numero de dias entre a data inicial e a data final do periodo de exibicao - muda a cada passagem do loop mais externo
      $num_dias = intval(ceil(($data_fimUT - $data_iniUT) / 86400));
      // numero de tabelas a exibir - nao muda. Exibimos tabelas de no maximo 20 dias
      $num_tabelas = intval(ceil($num_dias / NUM_DIAS_TABELA));
      // tamanho de cada tabela menor - muda a cada passagem de loop. Exemplo: 29 dias = 1 tabela de 15 + 1 tabela de 14
      $tam_tabela = ($num_dias > NUM_DIAS_TABELA ? ceil($num_dias / $num_tabelas) : $num_dias);
      // variavel para percorrer o loop numerando as colunas
      $diaUT = $data_iniUT;


      while ($num_tabelas-- > 0)
      {
        // numero de dias entre a data atualmente tratada pelo loop e o fim do periodo
        $num_dias = ceil(($data_fimUT - $diaUT) / 86400);
        // uma das tabelas vai ser menor que as outras
        $tam_tabela = ($tam_tabela <= $num_dias ? $tam_tabela : $num_dias);

        // determina a cor da linha
        $cor_linha = 0;
        CriaTabela();

        // num_tabelas == 0 indica que esta eh a ultima tabela a ser gerada
        $sock = Conectar("");
        $titulo = RetornaFraseDaLista($lista_frases_ferramentas,$lista_ferramentas[$cod_ferramenta]['cod_texto_nome']);

        ExibeCabecalhoIndividual($tam_tabela, $num_tabelas, $diaUT, $SalvarEmArquivo, ($coluna_total = (0 == $num_tabelas)),$titulo,$cod_ferramenta);

        if (is_array ($lista_grupos))
        {
          foreach ($lista_grupos as $cod_grupo => $linha_grupo)
          {
                // exibe linha com os acessos diarios do grupo
                ExibeLinhaGrupo($cod_grupo, $acessos_grupos[ $cod_grupo ], $tam_tabela, $diaUT, $SalvarEmArquivo, $coluna_total, $nomes_grupos[$cod_grupo], $exibir_alunos || $exibir_formadores, ($cod_grupo+1), $data_iniUT);
                if (is_array ($linha_grupo))
                {
                  // listamos os integrantes do grupo
                  foreach ($linha_grupo as $cod_usuario)
                  {
                      //foreach($ferramenta_do_sistema as $key => $value){
                          if($cod_usuario == $_GET['cod_aluno_relatorio']){
                            // Exibe linha com os acessos do usuario em cada dia
                            //echo("cod_usuario:".."\n Código Certo:".$_POST['cod_aluno_relatorio']);
                            ExibeLinhaUsuario($cod_usuario, $lista_nomes[$cod_usuario], $acessos_users[$cod_usuario], $tam_tabela, $diaUT, $SalvarEmArquivo, $coluna_total, $cor_linha, $totais_users[$cod_usuario], $data_iniUT, $data_fimUT, $cod_ferramenta);
                            $cor_linha++;
                            break;
                          }
                      //}
                  }
                }
                else
                {
              // se nao for detalhar os participantes dos grupos, precisa ainda tratar as mudancas de cor na linha
                  // CUIDADO !!! Não caia na tentação de juntar esse comando com a mudanca de cor na linha acima, os tratamentos sao diferentes !
                  $cor_linha++;
                }

          }
        }

        ExibeLinhaTotaisDiarios($tam_tabela, $diaUT, $coluna_total, $totais_diarios);
        FechaTabela();

        /* avancando a variavel $diaUT. */
        $diaST = explode("/",UnixTime2Data($diaUT));
        $diaUT = Data2UnixTime(($tam_tabela + $diaST[0])."/".$diaST[1]."/".$diaST[2]);
      }

      /* ****************************************************************************** 
         Gerando tabelas - FIM
      ****************************************************************************** */
      if ($exibir_grupos)
      {
        /* 45 - Obs.: O número total de acessos não corresponderão à soma da coluna caso haja algum aluno em mais de um grupo. Cada Aluno é contado somente uma vez no total. */
        echo("          <b>".RetornaFraseDaLista($lista_frases,45)."</b>\n");
      }

      echo("        </td>\n");
      echo("      </tr>\n");
      echo("    </table>\n");
      Desconectar($sock);
      }
  }
  echo("  </body>\n");
  echo("</html>");

  // Correção provisória para exibição das datas
  $data_invertida_g=$dig;

?>