<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/avaliacoes/medias.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
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

    Nied - Núcleo de Informática Aplicada à Educação
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitária "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : cursos/aplic/avaliacoes/medias.php
  ========================================================== */

  $bibliotecas="../bibliotecas/";
  include($bibliotecas."geral.inc");
  include("avaliacoes.inc");

  $cod_usuario=VerificaAutenticacao($cod_curso);

  $sock=Conectar("");

  $lista_frases=RetornaListaDeFrases($sock,22);
  $lista_frases_geral=RetornaListaDeFrases($sock,-1);

  Desconectar($sock);

  $sock=Conectar($cod_curso);

  VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario);

  $usr_formador=EFormador($sock,$cod_curso,$cod_usuario);

  echo("<html>\n");
  /* 1 - Avaliações  */
  echo("  <head><title>TelEduc - ".RetornaFraseDaLista($lista_frases,1)."</title></head>\n");

  if ($SalvarEmArquivo)
  {
    echo("  <style>\n");
    include "../teleduc.css";
    include "avaliacoes.css";
    echo("  </style>\n");
  }
  else
  {
    echo("  <link rel=stylesheet TYPE=text/css href=../teleduc.css>\n");
    echo("  <link rel=stylesheet TYPE=text/css href=avaliacoes.css>\n");
    echo("\n");

    /* Funções JavaScript */
    echo("<script language=JavaScript>\n");

    $lista_avaliacoes=RetornaAvaliacoes($sock,$usr_formador);
    
    echo("  var expressao;\n");   
    echo("  var expressao_orginal;\n");
    echo("  var media;\n");
    echo("  var norma;");
    echo("  var ok;\n\n");

    echo("  function LoopAvaliarExpressao() {\n");
    echo("    ok=true;\n");
    echo("    expressao=document.frmExpressao.exp.value;\n");
    echo("    norma=document.frmExpressao.norma.value;\n");
    echo("    expressao_original=expressao;\n");
    echo("    CorrigeExpressao();\n"); 
    echo("    AvaliarExpressao();\n");
    echo("  }\n\n");

    echo("  function CorrigeExpressao() {\n");

    // Funções em inglês
    echo("    expressao=expressao.toUpperCase();\n");
    echo("    expressao=expressao.replace(new RegExp(/SQRT\(/g), 'Math.sqrt(');\n");
    echo("    expressao=expressao.replace(new RegExp(/POW\(/g), 'Math.pow(');\n");
    echo("    expressao=expressao.replace(new RegExp(/ABS\(/g), 'Math.abs(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ACOS\(/g), 'Math.acos(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ASIN\(/g), 'Math.asin(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ATAN\(/g), 'Math.atan(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ATAN2\(/g), 'Math.atan2(');\n");
    echo("    expressao=expressao.replace(new RegExp(/CEIL\(/g), 'Math.ceil(');\n");
    echo("    expressao=expressao.replace(new RegExp(/COS\(/g), 'Math.cos(');\n");
    echo("    expressao=expressao.replace(new RegExp(/EXP\(/g), 'Math.exp(');\n");
    echo("    expressao=expressao.replace(new RegExp(/FLOOR\(/g), 'Math.floor(');\n");
    echo("    expressao=expressao.replace(new RegExp(/LOG\(/g), 'Math.log(');\n");
    echo("    expressao=expressao.replace(new RegExp(/MAX\(/g), 'Math.max(');\n");
    echo("    expressao=expressao.replace(new RegExp(/MIN\(/g), 'Math.min(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/RANDOM\(/g), 'Math.random(');\n");
    echo("    expressao=expressao.replace(new RegExp(/ROUND\(/g), 'Math.round(');\n");
    echo("    expressao=expressao.replace(new RegExp(/SIN\(/g), 'Math.sin(');\n");
    echo("    expressao=expressao.replace(new RegExp(/TAN\(/g), 'Math.tan(');\n");
           
    // Funções em português
    echo("    expressao=expressao.replace(new RegExp(/RAIZ\(/g), 'Math.sqrt(');\n");
    echo("    expressao=expressao.replace(new RegExp(/POTÊNCIA\(/g), 'Math.pow(');\n");
    echo("    expressao=expressao.replace(new RegExp(/POTENCIA\(/g), 'Math.pow(');\n");
    echo("    expressao=expressao.replace(new RegExp(/ABS\(/g), 'Math.abs(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ACOS\(/g), 'Math.acos(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ASIN\(/g), 'Math.asin(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ATAN\(/g), 'Math.atan(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ATAN2\(/g), 'Math.atan2(');\n");
    echo("    expressao=expressao.replace(new RegExp(/TETO\(/g), 'Math.ceil(');\n");
    echo("    expressao=expressao.replace(new RegExp(/COS\(/g), 'Math.cos(');\n");
    echo("    expressao=expressao.replace(new RegExp(/EXP\(/g), 'Math.exp(');\n");
    echo("    expressao=expressao.replace(new RegExp(/CHÃO\(/g), 'Math.floor(');\n");
    echo("    expressao=expressao.replace(new RegExp(/CHAO\(/g), 'Math.floor(');\n");
    echo("    expressao=expressao.replace(new RegExp(/LOG\(/g), 'Math.log(');\n");
    echo("    expressao=expressao.replace(new RegExp(/MÁXIMO\(/g), 'Math.max(');\n");
    echo("    expressao=expressao.replace(new RegExp(/MAXIMO\(/g), 'Math.max(');\n");
    echo("    expressao=expressao.replace(new RegExp(/MÍNIMO\(/g), 'Math.min(');\n");
    echo("    expressao=expressao.replace(new RegExp(/MINIMO\(/g), 'Math.min(');\n");
    //echo("    expressao=expressao.replace(new RegExp(/ALEAT\(/g), 'Math.random(');\n");
    echo("    expressao=expressao.replace(new RegExp(/ARRED\(/g), 'Math.round(');\n");
    echo("    expressao=expressao.replace(new RegExp(/SEN\(/g), 'Math.sin(');\n");
    echo("    expressao=expressao.replace(new RegExp(/TAN\(/g), 'Math.tan(');\n");
    
    // Constantes
    echo("    expressao=expressao.replace(new RegExp(/PI/g), 'Math.PI');\n");
    
    echo("  }\n\n");
 
    echo("  function AvaliarExpressao() {\n");
    
    $cont_batepapo=1;
    $cont_forum=1;
    $cont_portfolio=1;
    $cont_exercicio=1;
    $cont_av_ext=1;

    if (count($lista_avaliacoes) > 0) {
       foreach($lista_avaliacoes as $index => $avaliacao) {
          if (!strcmp($avaliacao['Ferramenta'], 'B')) {
             $cont=$cont_batepapo;
             $cont_batepapo++; 
          } elseif (!strcmp($avaliacao['Ferramenta'], 'F')) {
             $cont=$cont_forum;
             $cont_forum++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'P')) {
             $cont=$cont_portfolio;
             $cont_portfolio++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'E')) {
             $cont=$cont_exercicio;
             $cont_exercicio++;
          } elseif (!strcmp($avaliacao['Ferramenta'], 'N')) {
             $cont=$cont_av_ext;
             $cont_av_ext++;
          } 

          echo("    var ".$avaliacao['Ferramenta'].$cont." = 0.00;\n");
       }
    }
    echo("    try {\n");
    echo("      if (expressao != '') {\n");
    echo("        var nota=eval(expressao);\n");
    echo("      } else {\n");
    // 200 - Não definido
    echo("        document.getElementById('expFinal').innerHTML=\"<font class='text'>(".RetornaFraseDaLista($lista_frases, 200).")</font>\";\n");
    echo("        document.getElementById('normaFinal').innerHTML=\"<font class='text'>(".RetornaFraseDaLista($lista_frases, 200).")</font>\";");
    echo("        return;\n");
    echo("      }\n");
    echo("    } catch (e){\n");
    if ($usr_formador) {
    // 191 - Verifique se a sua expressão está correta!
      echo("      alert('".RetornaFraseDaLista($lista_frases, 191)."');\n");
    }
    echo("      ok=false;");
    echo("    }\n");
    
    echo("      if (norma != '') {\n");
    echo("        media=nota;");
    echo("        nota=eval(norma);\n");
    echo("      }\n");
    echo("      if ((isUndefined(nota)) || (nota == 'Infinity') || (media == Infinity)) {\n");
    echo("        if ((nota == 'Infinity') || (media == 'Infinity')) {\n");
    // 198 - A divisão por 0 (zero) não é permitida!
    echo("          alert('".RetornaFraseDaLista($lista_frases, 198)."');\n");
    echo("        }\n");
    echo("        ok=false;\n");
    echo("      }\n");
    
    echo("    if (ok) {\n");
    echo("      document.getElementById('expFinal').innerHTML=expressao_original;\n");
    echo("      document.getElementById('normaFinal').innerHTML=norma;");
    echo("    } else {\n");
    // 199 - Erro
    echo("      document.getElementById('expFinal').innerHTML='".RetornaFraseDaLista($lista_frases, 199)."';\n");
    echo("      document.getElementById('normaFinal').innerHTML='".RetornaFraseDaLista($lista_frases, 199)."';\n");
    echo("    }\n");
    echo("  }\n\n");
   
    echo("  function isUndefined(a) {\n");
    echo("    return typeof a == 'undefined';\n");
    echo("  }\n");

    echo("  function Fechar() {\n");
    echo("    window.opener.location.reload();\n");
    echo("    self.close();\n");
    echo("  }\n");
    
    echo("  function GravarExpressao() {\n");
    echo("    LoopAvaliarExpressao();\n");
    echo("    if (ok) {\n");
    echo("      document.getElementById('exp_gravar').value=document.getElementById('exp').value;\n");
    echo("      document.getElementById('norma_gravar').value=document.getElementById('norma').value;\n");
    echo("      document.frmExpressao.action='medias_popup.php';\n");
    echo("      document.frmExpressao.submit();\n");
    echo("    }\n");
    echo("  }\n");
   
    echo("function AjudaMedia(ajuda, nome_janela)\n");
    echo("{\n");
    $param = "'width=450,height=300,top=150,left=250,status=yes,toolbar=no,menubar=no,resizable=yes,scrollbars=yes'";
    echo("  window_handle = window.open('ajuda_media.php?".RetornaSessionID()."&cod_curso=".$cod_curso."&tela_ajuda=' + ajuda, nome_janela, ".$param.");\n");
    echo("  window_handle.focus(); \n");
    echo("  return false;");
    echo("}\n"); 
    
    echo("  function AdicionarLegenda(l) {\n");
    echo("    document.getElementById('exp').value=document.getElementById('exp').value+l;\n");
    echo("    document.getElementById('exp').focus();\n");
    echo("  }\n");
    
    echo("</script>\n");
  }

  if (EConvidado($sock, $cod_usuario) || EVisitante($sock, $cod_curso, $cod_usuario) || !EFormador($sock,$cod_curso,$cod_usuario))
  {
    echo("  <body link=#0000ff vlink=#0000ff bgcolor=white>\n");
    // 1 - Avaliações
    $cabecalho = "  <b class=titulo>".RetornaFraseDaLista($lista_frases, 1)."</b>";
    // 94 - Usuário sem acesso
    $cabecalho .= "  <b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 94)."</b>";
    echo(PreparaCabecalho($cod_curso, $cabecalho, COD_AVALIACAO, 1));
    echo("    <br>\n");
    /* 23 - Voltar (gen) */
    echo("<form name=frmErro><input class=text type=button name=cmdVoltar value='".RetornaFraseDaLista($lista_frases_geral,23)."' onclick=history.go(-1);></form>\n");

    echo("  </body>\n");
    echo("  </html>\n");
    exit();
  }
  
  echo("<body link=#0000ff vlink=#0000ff bgcolor=white onLoad='LoopAvaliarExpressao();self.focus();'>\n");
  $cabecalho ="<b class=titulo>Avaliações</b>";

  /* 190 - Médias dos participantes */
  $cabecalho.="<b class=subtitulo> - ".RetornaFraseDaLista($lista_frases, 190)."</b>";

  $cod_pagina = 14;
  /* Cabecalho */
  echo(PreparaCabecalho($cod_curso,$cabecalho,COD_AVALIACAO,$cod_pagina));


  echo("    <form name=frmAvaliacao method=post>\n");
  echo(RetornaSessionIDInput());
  echo("      <input type=hidden name=cod_curso value=".$cod_curso.">\n");
  // Passa o cod_avaliacao para executar ações sobre ela.
  echo("      <input type=hidden name=cod_avaliacao value=-1>\n");
  // tela_avaliacao eh a variavel que indica se esta tela deve mostrar avaliacoes 'P'assadas, 'A'tuais ou 'F'uturas
  echo("      <input type=hidden name=tela_avaliacao value=".$tela_avaliacao.">\n");
  echo("    </form>\n");

  if (strcmp($gravar, "sim") == 0) {
     if (GravarExpressaoMedia($sock, $exp_gravar, $norma_gravar, $tipo_compartilhamento_gravar)) {
        // Expressão foi gravada com sucesso
        // echo("<p><font class='text'>Expressão gravada com sucesso: ".$exp_gravar." (Norma: $norma_gravar)</font></p>\n");
        echo("<script>Fechar();</script>");
     } else {
        // 196 - Ocorreu um erro ao gravar a expressão:
        // 195 - Norma
        echo("<p><font class='text'>".RetornaFraseDaLista($lista_frases, 196).": ".$exp_gravar." (".RetornaFraseDaLista($lista_frases, 195).": $norma_gravar)</font></p>\n");
        // 13 - Fechar (ger)
        echo("<input type='button' value='".RetornaFraseDaLista($lista_frases_geral, 13)."' onClick='self.close();'>");
     }
     
     exit();
  }

  if (!$usr_formador) {
    echo("<p><font class='text'>Área Restrita ao Formador.</font></p>\n");
    exit();
  }
  
  $lista_avaliacoes=RetornaAvaliacoes($sock,$usr_formador);

  if (count($lista_avaliacoes)>0)
  {
    $cont_batepapo=1;
    $cont_forum=1;
    $cont_portfolio=1;
    $cont_exercicio=1;
    $cont_av_ext=1;
    $legenda_batepapo = array();
    $legenda_forum = array();
    $legenda_portfolio = array();
    $legenda_exercicio = array();
    $legenda_avaliacao_externa = array();

    foreach ($lista_avaliacoes as $cod => $linha)
    {
      if (!strcmp($linha['Ferramenta'],'F'))
      {
        $leg = $linha['Ferramenta'].$cont_forum++."\n";
        $legenda_forum[] = array(
            // o nome do forum
            'titulo' => $linha['Titulo'],
            // codigo do forum : F#
            'leg' => $leg,
            // O Periodo do Forum
            // 'data' => UnixTime2Data($linha['Data'])
            // 166 - de
            // 167 - a
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
        );
      }
      elseif (!strcmp($linha['Ferramenta'],'B'))
      {
        $leg = $linha['Ferramenta'].$cont_batepapo++."\n";
        $legenda_batepapo[] = array(
            // o titulo da sessao de bate-papo que serviu como avaliacao
            'titulo' => $linha['Titulo'],
            // o codigo: B# onde # eh um numero
            'leg' => $leg,
            // a data da sessao
            'data' => UnixTime2Data($linha['Data'])
        );
      }
      elseif (!strcmp($linha['Ferramenta'],'N'))
      {    
         $leg = $linha['Ferramenta'].$cont_av_ext++."\n";
         $legenda_avaliacao_externa[] = array(
            // o titulo da avaliacao externa
            'titulo' => $linha['Titulo'],
            // o codigo: N# onde # eh um numero
            'leg' => $leg,
            // a data da avaliacao
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
        
         ); 
                     
      }
      elseif (!strcmp($linha['Ferramenta'],'E'))
      {
        $leg = $linha['Ferramenta'].$cont_exercicio++."\n";
        $legenda_exercicio[] = array(
            // o titulo da sessao de bate-papo que serviu como avaliacao
            'titulo' => $linha['Titulo'],
            // o codigo: B# onde # eh um numero
            'leg' => $leg,
            // a data da sessao
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
        );
      }
      elseif (!strcmp($linha['Ferramenta'],'P'))
      {
        $leg = $linha['Ferramenta'].$cont_portfolio++."\n";
        $legenda_portfolio[] = array(
            // titulo da Atividade que foi avaliada
            'titulo' => $linha['Titulo'],
            // codigo da avaliacao: P#
            'leg' => $leg,
            // o periodo em que a atividade estava disponivel
            'data' => RetornaFraseDaLista($lista_frases, 166)." ".
                      UnixTime2Data($linha['Data_inicio'])." ".
                      RetornaFraseDaLista($lista_frases, 167)." ".
                      UnixTime2Data($linha['Data_termino'])
        );
      }
    }
  }
  $media=RetornaInformacoesMedia($sock);

  // 203 - Expressão atual
  echo("<font class='text'>".RetornaFraseDaLista($lista_frases, 203).":&nbsp;</font>\n");
  echo("<font class='text' id='expFinal'>".$media['expressao']."</font><br>\n");
  // 195 - Norma
  echo("<font class='text'>".RetornaFraseDaLista($lista_frases, 195).":&nbsp;</font>\n");
  echo("<font class='text' id='normaFinal'>".$media['norma']."</font><br><br>\n");

  if ($usr_formador) {
    echo("<form name='frmExpressao' method='post'>\n");
    echo(   RetornaSessionIDInput());
    echo("  <input type=hidden name=cod_curso value=".$cod_curso.">\n");
    // Passa o cod_avaliacao para executar ações sobre ela.
    echo("  <input type='hidden' name='cod_avaliacao' value=".$cod_avaliacao.">\n");
    echo("  <input type='hidden' name='origem' value='avaliacoes'>\n");
    echo("  <input type='hidden' name='exp_gravar' id='exp_gravar'>\n");
    echo("  <input type='hidden' name='norma_gravar' id='norma_gravar'>\n");
    echo("  <input type='hidden' name='gravar' value='sim'>\n");

    echo("<table cellspacing=0 cellpadding=0 border=0 width=100%>\n");
    echo("  <tr>\n");
    // 202 - Expressão para cálculo da média
    echo("    <td class=colorfield width=75%><font class='text'>&nbsp;".RetornaFraseDaLista($lista_frases, 202)."</font></td>\n");
    // 201 - Opcional
    // 195 - Norma
    echo("    <td class=colorfield width=25%><font class='text'>&nbsp;(".RetornaFraseDaLista($lista_frases, 201).")&nbsp;".RetornaFraseDaLista($lista_frases, 195)."</font></td>");
    echo("  </tr>\n");
    echo("  <tr style='height: 40px;'>\n");
    echo("    <td class=g1field>\n");
    echo("        &nbsp;&nbsp;<input type='text' name='exp' id='exp' size=45 value='".$media['expressao']."'>
    &nbsp;<a href='#' onClick=\"AjudaMedia('expressao', 'AjudaMediaExpressao')\">?</a>\n");
    echo("    </td>\n");
    echo("    <td class=g1field>&nbsp;&nbsp;<input type='text' name='norma' id='norma' size=5 value=".$media['norma'].">
    &nbsp;<a href='#' onClick=\"AjudaMedia('norma', 'AjudaMediaNorma')\">?</a></td>\n");
    echo("  </tr>\n");

    echo("  <tr>\n");
    // 50 - Compartilhar
    echo("    <td class='colorfield' colspan=2><font class='text'>&nbsp;".RetornaFraseDaLista($lista_frases, 50)."</font><br>\n");
    echo("  </tr>"); 


   if ($media['tipo_compartilhamento']=="F")
    {
      $compf=" checked";
      $comptotal="";
      $compfp="";
      echo("<input type=hidden name=tipo_compartilhamento_gravar value=F>\n");
    }
    else if ($media['tipo_compartilhamento']=="T")
    {
      $compf="";
      $comptotal=" checked";
      $compfp="";
      echo("<input type=hidden name=tipo_compartilhamento_gravar value=T>\n");
    }
    else if ($media['tipo_compartilhamento']=="A")
    {     
      $compf="";
      $comptotal="";
      $compfp=" checked";
      echo("<input type=hidden name=tipo_compartilhamento_gravar value=A>\n");;
    }
    else
    {     
      $compf="";
      $comptotal="";
      $compfp=" checked";
      echo("<input type=hidden name=tipo_compartilhamento_gravar value=A>\n");;
    }   
 
    echo("  <tr>");
    echo("    <td class='g1field' colspan=2>");
    // 51 - Totalmente Compartilhado
    echo("    &nbsp;<input type='radio' name='compartilhamento' id='compartilhamento'".$comptotal." onClick=\"document.frmExpressao.tipo_compartilhamento_gravar.value='T';\"><font class='text'>".RetornaFraseDaLista($lista_frases,51)."</font><br>");
    // 52 - Compartilhado com Formadores
    echo("    &nbsp;<input type='radio' name='compartilhamento' id='compartilhamento'".$compf." onClick=\"document.frmExpressao.tipo_compartilhamento_gravar.value='F';\"><font class='text'>".RetornaFraseDaLista($lista_frases,52)."</font><br>");
    // 54 - Compartilhado com Formadores e Com o Participante
    echo("    &nbsp;<input type='radio' name='compartilhamento' id='compartilhamento'".$compfp." onClick=\"document.frmExpressao.tipo_compartilhamento_gravar.value='A';\"><font class='text'>".RetornaFraseDaLista($lista_frases,54)."</font>");
    echo("    </td>\n");
    echo("  </tr>\n");
                        
    echo("  <tr>\n");
    echo("    <td colspan='2'>\n");
    // 193 - Gravar Expressão
    echo("        <br><input type='button' onClick='GravarExpressao();' value='".RetornaFraseDaLista($lista_frases,193)."'>\n");
    // 2 - Cancelar (ger)
    echo("        <input type='button' onClick='self.close();' value='".RetornaFraseDaLista($lista_frases_geral, 2)."'>\n");
    echo("    </td>\n");
    echo("  </tr>\n");
    echo("</table></form><br>\n");
  }
    
  $apresentar_legenda = (
       (is_array($legenda_batepapo)  && count($legenda_batepapo) > 0)
    || (is_array($legenda_forum)     && count($legenda_forum) > 0)
    || (is_array($legenda_portfolio) && count($legenda_portfolio) > 0)
    || (is_array($legenda_exercicio) && count($legenda_exercicio) > 0)
    || (is_array($legenda_avaliacao_externa) && count($legenda_avaliacao_externa)>0));

  if ($apresentar_legenda)
  {
    echo("<table cellpadding=0 cellspacing=0 border=0 width=80% class=legenda_bg> \n");
    // cabecalho
    echo("<tr class=legenda_bg>\n");
    // celula vazia acima do codigo
    echo("<td class=legenda_bg>&nbsp;</td>\n");
    // 116 - Legenda
    echo("<td class=legenda_bg><font class=text><b>".RetornaFraseDaLista($lista_frases, 116)."</b></font></td>\n");

    if (is_array($legenda_batepapo) && count($legenda_batepapo) > 0)
    {
      // 101 - Data
      $frase_data = RetornaFraseDaLista($lista_frases, 101);
      // Escrevo as datas do forum acima das linhas de legenda do forum e legendas do portfolio
      $linha_data_forum =
         "<tr> \n".
         "<td colspan=2>&nbsp;</td> \n".
         // 168 - Período
         "<td><font class=text><b>".RetornaFraseDaLista($lista_frases, 168)."</b></font></td> \n".
         "</tr> \n";
    }
    else
    {
      // 168 - Período
      $frase_data = RetornaFraseDaLista($lista_frases, 168);
      // nao existem sessoes de bate-papo. Entao nao ponho cabecalho acima do forum e do portfolio, ponho o cabecalho direto na tabela
      $linha_data_forum = "";
    }

    // coluna da data
    echo("<td class=legenda_bg><font class=text><b>".$frase_data."</b></font></td>\n");

    // coluna com a descricao da ferramenta
    echo("<td class=legenda_bg><font class=text>&nbsp;</font></td>\n");
    echo("</tr>\n");

    if (is_array($legenda_batepapo) && count($legenda_batepapo) > 0)
    {
      foreach ($legenda_batepapo as $linha_legenda)
      {
        echo("<tr>\n");
        echo("<td>&nbsp;</td>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("<td><font class=text><b><a href='#' onClick='AdicionarLegenda(\"".$leg[0]."\");'>".$leg[0]."</a></b> - ".$linha_legenda['titulo']."</font></td>\n");
        echo("<td><font class=text>".$linha_legenda['data']."</font></td>\n");
        // 146 - Sessão de Batepapo
        echo("<td><font class=text>".RetornaFraseDaLista($lista_frases, 146)."</font></td>\n");
        echo("</tr>\n");
      }
      echo("<tr><td colspan=4>&nbsp;</td></tr>\n");
    }

    if (is_array($legenda_forum) && count($legenda_forum) > 0)
    {
      echo($linha_data_forum);
      $linha_data_forum = "";
      foreach ($legenda_forum as $linha_legenda)
      {
        echo("<tr>\n");
        echo("<td>&nbsp;</td>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("<td><font class=text><b><a href='#' onClick='AdicionarLegenda(\"".$leg[0]."\");'>".$leg[0]."</a></b> - ".$linha_legenda['titulo']."</font></td>\n");
        echo("<td><font class=text>".$linha_legenda['data']."</font></td>\n");
        // 145 - Fórum de discussão
        echo("<td><font class=text>".RetornaFraseDaLista($lista_frases, 145)."</font></td>\n");
        echo("</tr>\n");
      }
      echo("<tr><td colspan=4>&nbsp;</td></tr>\n");
    }

    if (is_array($legenda_portfolio) && count($legenda_portfolio) > 0)
    {
      echo($linha_data_forum);
      foreach ($legenda_portfolio as $linha_legenda)
      {
        echo("<tr>\n");
        echo("<td>&nbsp;</td>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("<td><font class=text><b><a href='#' onClick='AdicionarLegenda(\"".$leg[0]."\");'>".$leg[0]."</a></b> - ".$linha_legenda['titulo']."</font></td>\n");
        echo("<td><font class=text>".$linha_legenda['data']."</font></td>\n");
        // 14 - Atividade no Portfolio
        echo("<td><font class=text>".RetornaFraseDaLista($lista_frases, 14)."</font></td>\n");
        echo("</tr>\n");
      }
    }

      if (is_array($legenda_exercicio) && count($legenda_exercicio) > 0)
      {
        echo($linha_data_forum);
        foreach ($legenda_exercicio as $linha_exercicio)
        {
          echo("<tr>\n");
          echo("<td>&nbsp;</td>\n");
          $leg=explode("\n",$linha_exercicio['leg']); 
          echo("<td><font class=text><b><a href='#' onClick='AdicionarLegenda(\"".$leg[0]."\");'>".$leg[0]."</a></b> - ".$linha_exercicio['titulo']."</font></td>\n");
          echo("<td><font class=text>".$linha_exercicio['data']."</font></td>\n");
          // 175 - Atividade em Exercícios
          echo("<td><font class=text>".RetornaFraseDaLista($lista_frases, 175)."</font></td>\n");
          echo("</tr>\n");
        }
       }
      
    if (is_array($legenda_avaliacao_externa) && count($legenda_avaliacao_externa) > 0)
    {
      echo($linha_data_forum);
      foreach ($legenda_avaliacao_externa as $linha_legenda)
      {
        echo("<tr>\n");
        echo("<td>&nbsp;</td>\n");
        $leg=explode("\n",$linha_legenda['leg']);
        echo("<td><font class=text><b><a href='#' onClick='AdicionarLegenda(\"".$leg[0]."\");'>".$leg[0]."</a></b> - ".$linha_legenda['titulo']."</font></td>\n");
        echo("<td><font class=text>".$linha_legenda['data']."</font></td>\n");
        // 14 - Atividade no Portfolio
        echo("<td><font class=text>".RetornaFraseDaLista($lista_frases, 187)."
        </font></td>\n");
        echo("</tr>\n");
      }
    }
    echo("</table> \n");
  }
  echo("<br>\n");

  // 2 - Cancelar (ger)
  echo("<p align='right'><input type='button' value='".RetornaFraseDaLista($lista_frases_geral, 2)."' onClick='self.close();'></p>\n");
  
  echo("</body>\n");
  echo("</html>\n");

  Desconectar($sock);

?>
