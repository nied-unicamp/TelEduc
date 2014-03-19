<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : cursos/aplic/configurar/notificar.php

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
  ARQUIVO : cursos/aplic/configurar/notificar.php
  ========================================================== */

  set_time_limit(0);

  $bibliotecas="../cursos/aplic/bibliotecas/";

  session_start();
  include($bibliotecas."teleduc.inc");
  include($bibliotecas."acesso_sql.inc");
  include($bibliotecas."conversor_texto.inc");
  include($bibliotecas."linguas.inc");
  include($bibliotecas."email.inc");
  include($bibliotecas."data.inc");
  include($bibliotecas."usuarios.inc");
  include($bibliotecas."cursos.inc");

  include("notificar.inc");

  $sock = Conectar("");

  // Pegamos todas as linguas disponiveis ja que a notificacao de novidades deve funcionar para cada lingua
  $query = "select cod_lingua from Lingua_textos group by cod_lingua";
  $res = Enviar($sock, $query);
  $linha = RetornaArrayLinhas($res);

  for ($k = 1; $k <= count($linha); $k++) {
     $lista_frases_total[$k] = RetornaListaDeFrasesNotificar($sock, -8, $k);
  }
  
  //$lista_frases = RetornaListaDeFrases($sock, -8, 1);

  echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
  echo("<html xmlns=\"http://www.w3.org/1999/xhtml\">\n");

  echo("  <head>\n");
  // 1 - Notifica��o de novidades
  echo("    <title>TelEduc - ".RetornaFraseDaListaNotificar($lista_frases_total[1], 1)."</title>\n");
  echo("    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n");
  echo("  </head>\n");
  echo("  <body>\n");
  echo("    <pre>\n");

  $L_host = $_SERVER['SERVER_NAME'];
  $R_host = $_SERVER['REMOTE_ADDR'];

  // Verifica se � um IP v�lido
  if (ereg('^([0-9]{1,3}\.){3}[0-9]{1,3}$', $L_host))
  {
    $L_host = gethostbyaddr($L_host);
  }
  else
  {
    $L_host = gethostbyname($L_host);
  }

  // Impede que outro usu�rio execute arbitrariamente esse script.
  if (strcmp($L_host, $R_host) != 0)
  {
    // 2 - Este script n�o pode ser executado remotamente.
    echo("<br /><font color=\"tomato\" size=+1>".RetornaFraseDaListaNotificar($lista_frases_total[1], 2)."</font><br />");
    echo("    </pre>\n");
    echo("  </body>\n");
    echo("</html>\n");
    exit(); // Executado remotamente sa�.
  }

  // Verifica se a vari�vel que determina a forma de envio foi setada e se ela � valida. 
  // Note que o teste de validez inclui o teste <= 0, pois para esses usu�rios o script  
  // n�o processa.                                                                       
  if ( (!isset($notificar_email)) || ($notificar_email <= 0) || ($notificar_email > 3) )
  {
    // 3 - A vari�vel notificar_email deve ser passada.
    echo("\n".RetornaFraseDaListaNotificar($lista_frases_total[1], 3)."\n");
    // 4 - Ex.: notificar.php?&notificar_email=2
    echo(RetornaFraseDaListaNotificar($lista_frases_total[1], 4)."\n\n");
    // 5 - 0 - n�o receber notifica��es de atualiza��es
    echo(RetornaFraseDaListaNotificar($lista_frases_total[1], 5)."\n");
    // 6 - 1 - resumo di�rio
    echo(RetornaFraseDaListaNotificar($lista_frases_total[1], 6)."\n");
    // 7 - 2 - resumo parcial duas vezes ao dia 
    echo(RetornaFraseDaListaNotificar($lista_frases_total[1], 7)."\n");

    echo("    </pre>\n");
    echo("  </body>\n");
    echo("</html>\n");
    exit();
  }

  // Obt�m a lista de ferramentas dispon�veis no ambiente.
  $lista_ferramentas = RetornaListaFerramentas($sock);

  // Calcula o UnixTime do dia anterior para obter cursos ainda n�o encerrados.
  $ontem = time() - (24 * 60 * 60);

  // Obt�m o hostname da m�quina e a raiz_www para criar o link de acesso ao curso
  $query = "select valor from Config where item='host'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  $host = $linha['valor'];

  unset($linha);
  $query = "select diretorio from Diretorio where item='raiz_www'";
  $res = Enviar($sock, $query);
  $linha = RetornaLinha($res);
  $raiz_www = $linha['diretorio'];
  unset($linha);

  // Lista os cursos n�o encerrados
  $query = "select cod_curso from Cursos where curso_fim > ".$ontem;
  $res = Enviar($sock, $query);
  $lista = RetornaArrayLinhas($res);

  $total_cursos = count($lista);
  // Para cada curso lista os usu�rios e envia o e-mail de notifica��o se eles o requiseram.
  for ($i = 0; $i < $total_cursos; $i++)
  {
    // Alterna para base de dados principal
    MudarDB($sock, "");

    // Obt�m dados do usu�rio e a data do �ltimo envio de notifica��o.
    $query  = "SELECT nome, email, curso.cod_usuario cod_usuario, cod_lingua, config.notificar_email ";
    $query .= "FROM `Usuario` as user, `Usuario_config` as config, `Usuario_curso` as curso ";
    $query .= "WHERE (user.cod_usuario = curso.cod_usuario_global) ";
    $query .= "and (curso.cod_usuario = config.cod_usuario) ";
    $query .= "and (curso.cod_curso = ".$lista[$i]['cod_curso'].") ";
    $query .= "and (config.cod_curso = curso.cod_curso)";
    $query .= "and (config.notificar_email != 0)";
   
    $res = Enviar($sock, $query);
    $linha = RetornaArrayLinhas($res);

    // Alterna para a base de dados do curso.
    MudarDB($sock, $lista[$i]['cod_curso']);

    // Obt�m os dados do curso para o envio do e-mail.
    $dados_curso = DadosCursoParaEmail($sock, $lista[$i]['cod_curso']);
    
    // 8 - Nome do curso:
    echo(RetornaFraseDaListaNotificar($lista_frases_total[1], 8).$dados_curso['nome_curso']."<br />\n");

    // Determina o assunto do e-mail.
    // 1 - Notifica��o de novidades
    $assunto = "TelEduc: - ".$dados_curso['nome_curso']." - ".RetornaFraseDaListaNotificar($lista_frases_total[1], 1);

    $url_acesso = "<a href='";
    $url_acesso.= "http://".$host.$raiz_www."/cursos/aplic/index.php?cod_curso=".$lista[$i]['cod_curso'];
    $url_acesso.= " '> ".$dados_curso['nome_curso']." </a>";
    $total_usuarios = count($linha);
    // Para cada usu�rio lista as novidades nas ferramentas e se estas houver, envia e-mail.

    for ($j = 0; $j < $total_usuarios; $j++)
    {

      $notificar_email_usuario = $linha[$j]['notificar_email'];
      // Caso o usu�rio n�o queira ser notificado (notificar_email == 0)
      if (($notificar_email_usuario > 0) && ($notificar_email_usuario < 3))
      {
        // notificar_email = 2, recebe email 2x por dia (sempre)
        // notificar_email = 1, recebe 1 email s� (no momento em que for passado 1 de parametro)
        if ((($notificar_email == 1) && ($notificar_email_usuario == 1)) ||
            ($notificar_email_usuario == 2))
        {
          $curso_ferramentas = RetornaFerramentasCursoNotificar($sock);
          $novidade_ferramentas = RetornaNovidadeFerramentasNotificar($sock, $lista[$i]['cod_curso'], $linha[$j]['cod_usuario']);

          // Obt�m o timestamp do �ltimo acesso ao ambiente (esse timestamp conta o acesso �s ferramentas). 
          $ultimo_acesso = UltimoAcessoAmbiente($sock, $linha[$j]['cod_usuario']);

          // Soma um tempo m�dio estipulado que o usu�rio gasta em uma ferramenta para 
          // determinar se ele ainda se encontra online. Neste caso 25 minutos.   
          $comp_acesso = $ultimo_acesso + (25 * 60);

          $frase = "";
          $novo_flag = false;

          // Se foram retornadas novidades ent�o envia e-mail.
          if ((is_array($novidade_ferramentas)) && (is_array($curso_ferramentas)))
          {
            foreach($novidade_ferramentas as $cod_ferr => $dados_ferr)
            {
              // Se o compartilhamento da ferramenta for para formadores e o usu�rio for um formador
              // ou o compartilhamento da ferramenta for para todos e a data de novidades for maior
              // que a data base de compara��o e n�o foi o usu�rio quem postou a novidade, ent�o
              // lista as ferramentas onde h� novidades.                                             
              if ((((($curso_ferramentas[$cod_ferr]['status'] == 'F') || ($cod_ferr == 0)) && ($linha[$j]['tipo_usuario'] == 'F')) ||
                   ($curso_ferramentas[$cod_ferr]['status'] == 'A')) &&
                  ($comp_acesso < $dados_ferr['data']) && 
                  ($linha[$j]['cod_usuario'] != $dados_ferr['cod_usuario']))
              {
                $frase .= $lista_ferramentas[($linha[$j]['cod_lingua'])][$cod_ferr]['texto']."<br />";

                $novo_flag = true;
              }

            }

            // Se houver novidades monta a mensagem e envia ao usu�rio.
            if ($novo_flag)
            {
              // 12 - Verifica��o feita at�: 
              $frase_12 = RetornaFraseDaListaNotificar($lista_frases_total[($linha[$j]['cod_lingua'])], 12);

              // 9 - Curso:
              $mensagem = "<br />".(str_pad(RetornaFraseDaListaNotificar($lista_frases_total[($linha[$j]['cod_lingua'])], 9), strlen($frase_12)))." ".$dados_curso['nome_curso']."<br />";
              // 12 - Verifica��o feita at�:
              $mensagem .= ($frase_12)." ".UnixTime2DataHora(time())."<br /><br />";

              // 10 - Ol�  
              // 11 - , 
              $mensagem .= RetornaFraseDaListaNotificar($lista_frases_total[($linha[$j]['cod_lingua'])], 10)." ".$linha[$j]['nome']." ".RetornaFraseDaListaNotificar($lista_frases_total[($linha[$j]['cod_lingua'])], 11)."<br /><br />";


              // 13 - H� novidades na(s) ferramenta(s):
              $mensagem .= RetornaFraseDaListaNotificar($lista_frases_total[($linha[$j]['cod_lingua'])], 13)."<br /><br />";

              $mensagem .= $frase;
              // 14 - Acesse seu curso atrav�s do endere�o:
              $mensagem .= "\n".RetornaFraseDaListaNotificar($lista_frases_total[($linha[$j]['cod_lingua'])], 14)."<br />";
              $mensagem .= $url_acesso."<br />";
              // 15 - Para n�o receber mais notifica��es do ambiente, entre em seu curso e desative a op��o na ferramenta Configurar.
              $mensagem .= RetornaFraseDaListaNotificar($lista_frases_total[($linha[$j]['cod_lingua'])], 15);

              $emissor = $dados_curso['nome_curso']." <NAO_RESPONDA@".$host.">";
              echo($mensagem);
//              echo("host = ".$host."\n raiz = ".$raiz_www."\n cod_curso = ".$lista[$i]['cod_curso']."\n mensagem = ".$mensagem."\n assunto = ".$assunto."\n");

              // MontaMsg cria o corpo do e-mail, padr�o do TelEduc.
              $mensagem = MontaMsg($host, $raiz_www, $lista[$i]['cod_curso'], $mensagem, $assunto);

              // MontaMsg() destroi o sock, � necess�rio reconectar.
              Desconectar($sock);
              $sock = Conectar($lista[$i]['cod_curso']);

              MandaMsg($emissor, $linha[$j]['email'], $assunto, $mensagem);
            } // END IF
          }   // END IF
        }     // END IF
      }       // END IF
    }         // END FOR - usuarios
  }           // END FOR - cursos
  Desconectar($sock);

  echo("    </pre>\n");
  echo("  </body>\n");
  echo("</html>\n");
  
?>
