<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : bibliotecas/usuarios.inc

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½cia
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

    Nied - Ncleo de Informï¿½ica Aplicada ï¿½Educaï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ia "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : bibliotecas/usuarios.inc
  ========================================================== */

/* *********************************************************************
   RetornaStatusUsuario - Retorna o status do Usuï¿½io
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             BASE DO CURSO         
             $cod_curso - Cï¿½igo do curso 
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: I - aluno invï¿½ido ou inexistente
          i - Aluno inscrito
          a - Aluno desligado
          r - Aluno rejeitado
          A - Aluno Aceito
          f - Formador desligado
          F - Formador
          v - Visitante desligado
          V - Visitante
          z - Colaborador desligado
          Z - Colaborador
   OBS.: Esta funï¿½o retorna o status real do usuï¿½io. 
         Checar se nï¿½ ï¿½um formador querendo ter a visï¿½ do aluno com
         $_SESSION['visao_aluno_s']==true
*/
function RetornaStatusUsuario($sock,$cod_curso,$cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  /* Se administrador, retorna como Formador */
  if ($cod_usuario==-1)
    return ("F");

  $query="select tipo_usuario from ".$dbnamebase.".Usuario_curso where cod_usuario=".VerificaNumeroQuery($cod_usuario)." and cod_curso=".VerificaNumeroQuery($cod_curso);
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    return ($linha[0]);
  }
  else
  {
    return ("I");
  }
}

/* *******************************************************************
   getTipoAtor - retorna sigla do ator
   entrada: $sock - sock de conexao
            $cod_curso- codigo do curso
            $cod_usuario - codigo do usuario
   saida:   'R' se for Coordenador
            'F' se for Formador
            'A' se for Aluno
            'V' se for Visitante
            'Z' se for Colaborador
*/   
function getTipoAtor($sock, $cod_curso, $cod_usuario){

  if (ECoordenador($sock, $cod_curso, $cod_usuario)){
    $sigla= 'R';
  } elseif (EFormador($sock, $cod_curso, $cod_usuario)){
    $sigla= 'F';
  } elseif (EAluno($sock, $cod_curso, $cod_usuario)){
    $sigla= 'A';
  } elseif (EVisitante($sock, $cod_curso, $cod_usuario)){
    $sigla= 'V';
  } elseif (EColaborador($sock, $cod_curso, $cod_usuario)){
    $sigla= 'Z';
  }

  return $sigla;

}

/* *********************************************************************
   VerificaAcessoAoCurso- Barra usuï¿½ios que ainda nï¿½ foram aceitos ou 
                          que foram rejeitados de acessar a pï¿½ina
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
                     BASE EXTERNA
             $cod_curso - Cï¿½igo do curso 
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o                        
   Saida: Envia mensagem de acesso negado e interrompe a execuï¿½o, caso 
          aluno seja i ou r
*/
function VerificaAcessoAoCurso($sock,$cod_curso,$cod_usuario)
{
  if($cod_usuario != "")
    $status=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
  else
    //usuario inexistente
    $status = "I";
  // Definida em 'cursos.inc'
  $status_curso=RetornaStatusCurso($sock,$cod_curso);
  // os usuï¿½ios com status nesse array nï¿½ devem entrar no curso 
  $array_status = array('i', 'r', 'a', 'f', 'z', 'v', 'I');
  //se for administrador, possui acesso ao curso
  if($_SESSION['cod_usuario_global_s'] == -1)
    $adm = true;
  else
    $adm = false;

  if ((!$adm) && (($status_curso=="N" && $status!="F") || in_array($status, $array_status)))
  {
    /* 13 - Acesso negado */
    $mensagemHead = RetornaFrase($sock,13,-2);

    $mensagem="";


    if ($status_curso=="N" && $status!="F")
      // 30 - O curso ainda nï¿½ foi iniciado. 
      $mensagem.=RetornaFrase($sock,30,-2);
    else if ($status == 'i')
      // 14 - Sua inscriï¿½o ainda nï¿½ foi avaliada. Um e-mail lhe serï¿½enviado quando a avaliaï¿½o de sua inscriï¿½o for feita. 
      $mensagem.=RetornaFrase($sock,14,-2);
    else if ($status == 'r' || $status == 'a' || $status == 'f' || $status == 'z' || $status == 'v')
      // 15 - Sua inscriï¿½o nï¿½ foi aceita. Vocï¿½nï¿½ tem acesso a esse curso. 
      $mensagem.=RetornaFrase($sock,15,-2);
    else if ($status=="I")
      // 16 - Usuï¿½io inexistente ou invï¿½ido.
      $mensagem.=RetornaFrase($sock,16,-2);

    echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n");
    echo("<html lang=\"pt\">\n");
    echo("  <head>\n");
    echo("    <title>TelEduc - Erro</title>\n");
    echo("    <link href=\"js-css/ambiente.css\" rel=\"stylesheet\" type=\"text/css\" />\n");
    echo("    <script type=\"text/javascript\">\n");
    echo("      function Iniciar(){\n");
    echo("        document.getElementById('detalhes').style.display='none';\n");
    echo("      }\n");
    echo("      var status='none';\n");
    echo("      function changeStatus(){\n");
    echo("        if(status=='') status = 'none';\n");
    echo("        else status = '';\n");
    echo("        document.getElementById('detalhes').style.display=status;\n");
    echo("      }\n");
    echo("    </script>\n");
    echo("  </head>\n");
    echo("  <body onload=\"Iniciar();\">\n");
    echo("    <br />\n");
    echo("    <table id=\"container\" style=\"width:700px;\">");
    echo("      <tr>\n");
    echo("        <td colspan=\"2\" id=\"conteudo\" valign=\"top\">\n");
    echo("          <h4>".$mensagemHead."</h4>\n");
    echo("          <br />\n");
    echo("          <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
    echo("            <tr>\n");
    echo("              <td>\n");
    echo("                <table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
    echo("                  <tr class=\"head\">\n");
    echo("                    <td width=\"32%\" class=\"alLeft\">\n");
    echo("                      Ocorreu um Erro\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td>\n");
    echo("                      <p>".$mensagem."</p>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr>\n");
    echo("                    <td style=\"text-align:right;\">\n");
    echo("                      <span class=\"link\" onclick=\"changeStatus();\">+ Detalhes do Erro</span>\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                  <tr id=\"detalhes\">\n");
    echo("                    <td class=\"alLeft\" style=\"padding: 10px 50px 10px 50px;\">\n");
    echo("                      &nbsp;\n");
    echo("                    </td>\n");
    echo("                  </tr>\n");
    echo("                </table>\n");
    echo("              </td>\n");
    echo("            </tr>\n");
    echo("          </table>\n");
    echo("          <span class=\"btsNavBottom\">\n");
    echo("            <a href=\"../../../pagina_inicial/exibe_cursos.php\"><img src=\"imgs/btVoltar.gif\" border=\"0\" alt=\"Voltar\" /></a>\n");
    echo("          </span>\n");
    echo("        </td>\n");
    echo("      </tr>\n");
    echo("      <tr>\n");
    echo("        <td valign=\"bottom\" height=\"80\"><a href=\"http://www.nied.unicamp.br\"><img src=\"imgs/logoNied.gif\" alt=\"nied\" border=\"0\" style=\"margin-right:8px;\" /></a><a href=\"http://www.ic.unicamp.br\"><img src=\"imgs/logoInstComp.gif\" alt=\"Instituto de Computa&ccedil;&atilde;o\" border=\"0\" style=\"margin-right:6px;\" /></a><a href=\"http://www.unicamp.br\"><img src=\"imgs/logoUnicamp.gif\" alt=\"UNICAMP\" border=\"0\" /></a></td>\n");
    echo("        <td valign=\"bottom\" id=\"rodape\">2006  - TelEduc - Todos os direitos reservados. All rights reserved - NIED - UNICAMP</td>\n");
    echo("      </tr>\n");
    echo("    </table>\n");
    echo("  </body>");
    echo("</html>");

    exit();
  }
}

/* *********************************************************************
   TestaAcessoAFerramenta - verifica se um usuï¿½ios tem acesso a uma ferramenta
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
                     BASE EXTERNA
             $cod_curso - Cï¿½igo do curso
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
             $cod_ferramenta - Codigo da ferramenta
   Saida: true, se possui acesso, false do contrï¿½io.
*/
function TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta)
{
  $status_usuario = RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);

  $query  = "select status from Curso_ferramentas ";
  $query .= "where cod_ferramenta=".VerificaNumeroQuery($cod_ferramenta);

  $res = Enviar($sock,$query);

  $linha = RetornaLinha($res);

  return (!(
            // Ferramenta não é visivel por ninguém ou
            $linha['status'] == 'D' ||
            // Ferramenta é visível apenas para formadores e 
            // usuário atual não é formador
            ($linha['status'] == 'F' && $status_usuario != 'F')
          )
         );
}

/* *********************************************************************
   VerificaAcessoAFerramenta - Barra usuï¿½ios de entrar em ferramentas
                               desabilitadas
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
                     BASE EXTERNA
             $cod_curso - Cï¿½igo do curso
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
             $cod_ferramenta - Codigo da ferramenta
   Saida: Envia mensagem de acesso negado e interrompe a execuï¿½o, caso a ferramenta 
          esteja desabilitada (checa status de aluno) 
*/
function VerificaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta)
{
  if (!TestaAcessoAFerramenta($sock,$cod_curso,$cod_usuario,$cod_ferramenta))
  {
    // 59 - Ferramenta desabilitada
    die("<b>".RetornaFrase($sock,59,-1)."</b>");
  }
}


/* *********************************************************************
   EFormador - Retorna true se usuï¿½io for formador ou se for administrador do ambiente
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso 
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: true se ï¿½formador e este nï¿½ esta com visao de aluno, false caso contrï¿½io
   OBS.: O coordenador aparece tambï¿½ como formador
*/
function EFormador($sock,$cod_curso,$cod_usuario)
{
  $sock =  Conectar($cod_curso);

  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
  //se for administrador
  
  if(($_SESSION['cod_usuario_global_s'] == -1) && (!isset($_SESSION['visao_aluno_s']) || !($_SESSION['visao_aluno_s'])))
    return true;  
  else if ($estado=="F")
  {
    if (isset($_SESSION['visao_aluno_s']) && $_SESSION['visao_aluno_s'])
      return false;

    return true;
  }
  else
    return false;
}


/* *********************************************************************
   EFormadorMesmo - Retorna true se usuï¿½io for formador ou se for administrador do ambiente
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o

   Saida: true se ï¿½formador, false caso contrï¿½io
   OBS.: O coordenador aparece tambï¿½ como formador
*/
function EFormadorMesmo($sock,$cod_curso,$cod_usuario)
{
  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);

  //se for administrador
  if($_SESSION['cod_usuario_global_s'] == -1)
    return true;
  else if ($estado=="F")
    return true;
  else
    return false;
}


/* *********************************************************************
   EAluno - Retorna true se usuï¿½io for aluno
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso 
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o                        
   Saida: true se ï¿½aluno, false caso contrï¿½io 
*/
function EAluno($sock,$cod_curso,$cod_usuario)
{
//   global $visao_aluno_s;
  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
  if ($estado=="A")
    return true;
  else
  {
    if ($estado=="F" && $_SESSION['visao_aluno_s'])
      return true;
    return false;
  }
}

/* *********************************************************************
   EAlunoMesmo - Retorna true se usuï¿½io for aluno
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: true se ï¿½aluno, false caso contrï¿½io
*/
function EAlunoMesmo($sock,$cod_curso,$cod_usuario)
{
  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);

  if ($estado=="A")
    return true;
  else
    return false;
}


/* *********************************************************************
   ECoordenador - Retorna true se usuï¿½io for coordenador ou se for administrador do ambiente
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso 
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: true se ï¿½coordenador e este nï¿½ esta com visao de aluno, false caso contrï¿½io 
*/
function ECoordenador($sock,$cod_curso,$cod_usuario)
{
//   global $visao_aluno_s;
  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
  //se for administrador
  if($cod_usuario == -1)
    return true;
  else if ($estado=="F")
  {
    $query="select cod_coordenador from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    if ($linha[0]==$cod_usuario)
      if ($_SESSION['visao_aluno_s']){
        return false; 
      } else { return true; }
  }
  return false;
}

/* *********************************************************************
   ECoordenadorMesmo - Retorna true se usuï¿½io for coordenador ou se for
                       administrador do ambiente
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso 
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: true se ï¿½coordenador, false caso contrï¿½io
*/
function ECoordenadorMesmo($sock,$cod_curso,$cod_usuario)
{
  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);

  //se for administrador
  if($cod_usuario == -1)
    return true;
  else if ($estado=="F")
  {
    $query="select cod_coordenador from Cursos where cod_curso=".VerificaNumeroQuery($cod_curso);
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    if ($linha[0]==$cod_usuario)
      return true;
  }
  return false;
}

/* *********************************************************************
   EVisitante - Retorna true se usuario for visitante
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso 
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: true se ï¿½aluno, false caso contrï¿½io 
*/
function EVisitante($sock,$cod_curso,$cod_usuario)
{
  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);
  if ($estado=="V")
    return true;
  else
    return false;
}

/* *********************************************************************
   MarcaAcesso - Marca o acesso a determinada ferramenta pelo aluno
   Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
             $cod_ferramenta - Cï¿½igo da ferramenta. "" se Entrada no 
                               TelEduc
   Saida: nenhuma
*/
function MarcaAcesso($sock,$cod_usuario,$cod_ferramenta)
{
  if ($cod_usuario<0)
    return;
  $agora=time();
  if ($cod_ferramenta!="")
  {
    $query="select cod_ferramenta from Curso_acessos where cod_usuario=".VerificaNumeroQuery($cod_usuario)." order by data desc limit 1";
    $res=Enviar($sock,$query);
    $linha=RetornaLinha($res);
    if ($linha['cod_ferramenta']!=$cod_ferramenta)
    {
      $query="insert into Curso_acessos values (".VerificaNumeroQuery($cod_usuario).",".$agora.",".VerificaNumeroQuery($cod_ferramenta).")";
      Enviar($sock,$query);
    }
  }
  else
  {
    $query="select cod_ferramenta from Curso_acessos where cod_usuario=".VerificaNumeroQuery($cod_usuario)." order by data desc limit 1";
    $res=Enviar($sock,$query);
    $num=RetornaNumLinhas($res);
    $linha=RetornaLinha($res);
    if ($linha['cod_ferramenta']!="" || $num==0)
    {
      $query="insert into Curso_acessos (cod_usuario,data) values (".VerificaNumeroQuery($cod_usuario).",".$agora.")";
      Enviar($sock,$query);
    }
  }
}

/* *********************************************************************
   UltimoAcesso - Retorna o timestamp do ltimo acesso a determinada 
                   ferramenta
   Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
             $cod_ferramenta - Cï¿½igo da ferramenta. "" se Entrada no 
                               TelEduc
   Saida: timestamp do ltimo acesso
*/
function UltimoAcesso($sock,$cod_usuario,$cod_ferramenta)
{
  if ($cod_usuario<0)
    return time();
  if ($cod_ferramenta!="")
    $query="select data from Curso_acessos where cod_usuario=".VerificaNumeroQuery($cod_usuario)." and cod_ferramenta=".VerificaNumeroQuery($cod_ferramenta)." order by data desc limit 1";
  else
    $query="select data from Curso_acessos where cod_usuario=".VerificaNumeroQuery($cod_usuario)." and cod_ferramenta is NULL order by data desc limit 1";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    return $linha[0];
  }
  else
    return 0;
}

/* *********************************************************************
   PenultimoAcesso - Retorna o timestamp do penltimo acesso a determinada 
                   ferramenta
   Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
             $cod_ferramenta - Cï¿½igo da ferramenta. "" se Entrada no 
                               TelEduc
   Saida: timestamp do ltimo acesso
*/
function PenultimoAcesso($sock,$cod_usuario,$cod_ferramenta)
{
  if ($cod_usuario<0)
    return time();
  if ($cod_ferramenta!="")
    $query="select data from Curso_acessos where cod_usuario=".VerificaNumeroQuery($cod_usuario)." and cod_ferramenta=".VerificaNumeroQuery($cod_ferramenta)." order by data desc limit 2";
  else
    $query="select data from Curso_acessos where cod_usuario=".VerificaNumeroQuery($cod_usuario)." and cod_ferramenta is NULL order by data desc limit 2";
  $res=Enviar($sock,$query);
  if (RetornaNumLinhas($res)>1)
  {
    $linha=RetornaLinha($res);
    $linha=RetornaLinha($res);
    return $linha[0];
  }
  else
    return 0;
}

/* *********************************************************************
   AjustaNome - Ajusta o nome do usuario em relaï¿½o as maiusculas e minusculas
   Entradas: $nome_usuario
   Saida: nome com maiusculas e minsculas corrigidas
*/
function AjustaNome($nome_usuario)
{
  $tmp=explode(" ",$nome_usuario);
  if (count($tmp)>0)
  {
    foreach($tmp as $cod => $nome)
    {
      $tmp[$cod]=strtr($nome,"ABCDEFGHIJLKMNOPQRSTUVWXYZï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½","abcdefghijlkmnopqrstuvwxyzï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½");
      $primeira=substr($tmp[$cod],0,1);
      $resto=substr($tmp[$cod],1);
      $primeira=strtr($primeira,"abcdefghijlkmnopqrstuvwxyzï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½","ABCDEFGHIJLKMNOPQRSTUVWXYZï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½ï¿½");
      $tmp[$cod]=$primeira.$resto; 
    }
    $saida=implode(" ",$tmp);
  } 
  return $saida;
}


/* *********************************************************************
   NomeUsuario - Retorna o nome do usuario
   Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
             $cod_curso - Codigo do curso                        
   Saida: nome
*/
function NomeUsuario($sock, $cod_usuario, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($cod_usuario == -2)
    return ("Visitante");

  $cod_usuario = VerificaNumeroQuery($cod_usuario);
  $cod_curso   = VerificaNumeroQuery($cod_curso);
  if ($cod_curso === '' || $cod_usuario === '') return '';

  $query  = "select U.nome ";
  $query .= "from ".$dbnamebase.".Usuario U ";
  $query .= "inner join ".$dbnamebase.".Usuario_curso UC ON (UC.cod_usuario_global=U.cod_usuario AND UC.cod_usuario = ".$cod_usuario." AND UC.cod_curso=".$cod_curso.")";

  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $nome_usuario=$linha[0];
  if ($nome_usuario != AjustaNome($nome_usuario))
  {
    $cod_usuario_global = RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso);
    $nome_usuario=AjustaNome($nome_usuario);
    $query="update ".$dbnamebase.".Usuario set nome='".addslashes($nome_usuario)."' where cod_usuario=".VerificaNumeroQuery($cod_usuario_global);
    Enviar($sock,$query);
  }

  return ($nome_usuario);
}

/* *********************************************************************
   RetornaCodigoUsuarioGlobal - Retorna codugo global do usuario
   Entradas: $sock - Sock aberto para a base
             $cod_usuario - Codigo do usuario no curso especificado
             $cod_curso - Codigo do curso
   Saida: Codigo do usuario no curso especificado
*/
function RetornaCodigoUsuarioGlobal($sock, $cod_usuario, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "SELECT cod_usuario_global FROM ".$dbnamebase.".Usuario_curso WHERE cod_usuario = '".VerificaStringQuery($cod_usuario)."' AND cod_curso = '".VerificaStringQuery($cod_curso)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_usuario_global=$linha[0];
 
  return ($cod_usuario_global);
}

function RetornaCodigoUsuarioGlobalPorEmail($sock, $email)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario WHERE email = '".VerificaStringQuery($email)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_usuario_global=$linha[0];
 
  return ($cod_usuario_global);
}

function RetornaCodigoUsuarioGlobalPorLogin($sock, $login)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario WHERE login = '".VerificaStringQuery($login)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_usuario_global=$linha[0];
 
  return ($cod_usuario_global);
}

/* *********************************************************************
   RetornaCodigoUsuarioCurso - Retorna codigo local do usuario no curso
   Entradas: $sock - Sock aberto para a base
             $cod_usuario_global - Codigo global do usuario 
             $cod_curso - Codigo do curso
   Saida: Codigo do usuario no curso especificado
*/
function RetornaCodigoUsuarioCurso($sock, $cod_usuario_global, $cod_curso)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query = "SELECT cod_usuario FROM ".$dbnamebase.".Usuario_curso WHERE cod_usuario_global = '".VerificaStringQuery($cod_usuario_global)."' AND cod_curso = '".VerificaStringQuery($cod_curso)."'";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  $cod_usuario=$linha[0];
 
  return ($cod_usuario);
}

function AtualizaFerramentasNova($sock,$cod_ferramenta,$tipo_usuario)
{
  $query="replace Ferramentas_nova (cod_ferramenta, tipo_compartilhamento, data) values (".VerificaNumeroQuery($cod_ferramenta)." , '".VerificaStringQuery($tipo_usuario)."' , ".time().")" ;
  //if ($tipo_usuario=="F")
  //  $query.=" and tipo_compartilhamento='F'";
  Enviar($sock,$query);
}


function AtualizaFerramentasNovaUsuario($sock,$cod_ferramenta,$cod_usuario)
{
  $query="replace into Ferramentas_nova_usuarios (tipo, cod, cod_ferramenta, data) values ('U', ".VerificaNumeroQuery($cod_usuario)." , '".VerificaStringQuery($cod_ferramenta)."' , ".time().")" ;
  Enviar($sock,$query);

}

function AtualizaFerramentasNovaUsuarioJS($cod_curso,$cod_ferramenta,$cod_usuario)
{
  $objResponse = new xajaxResponse();
  // Retorna a resposta de XML gerada pelo objeto do xajaxResponse
  
  $sock = Conectar($cod_curso);
  $query="replace into Ferramentas_nova_usuarios (tipo, cod, cod_ferramenta, data) values ('U', ".VerificaNumeroQuery($cod_usuario)." , '".VerificaStringQuery($cod_ferramenta)."' , ".time().")" ;
  Enviar($sock,$query);
  Desconectar($sock);
  return $objResponse;
}

function AtualizaFerramentasNovaGrupo($sock,$cod_ferramenta,$cod_grupo)
{
  $query="replace into Ferramentas_nova_usuarios (tipo, cod, cod_ferramenta, data) values ('G', ".VerificaNumeroQuery($cod_grupo)." , '".VerificaStringQuery($cod_ferramenta)."' , ".time().")" ;
  Enviar($sock,$query);
}

/* *********************************************************************
   HaDadosFaltando - Retorna true se houver algum dado do aluno/formador faltando
   Entradas: $sock - sock aberto para a base do curso (Conectar($cod_curso))
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: true ou false
*/
function HaDadosFaltando($sock,$cod_usuario)
{
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  if ($cod_usuario<0)
    return false;

  $query  = "select UC.tipo_usuario,U.rg,U.data_nasc,U.sexo,U.endereco,U.cidade,U.estado,U.pais ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where UC.cod_usuario='".VerificaStringQuery($cod_usuario)."' AND UC.cod_curso=".$_SESSION['cod_curso_s'];

  $res    = Enviar($sock,$query);
  $linha  = RetornaLinha($res);

  if ($linha['tipo_usuario']=='F' && $linha['rg']=="")
    return true;
  if ($linha['data_nasc']=="" || $linha['sexo']=="" || $linha['endereco']=="" || $linha['cidade']=="" || $linha['estado']=="" || $linha['pais']=="")
    return true;
  return false;
}

/* *********************************************************************
   EFormadorComVisaoDeAluno - Retorna true se usuï¿½io for formador com visao de aluno
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_curso - Cï¿½igo do curso
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: true se ï¿½formador e este esta com visao de aluno, false caso contrï¿½io
   OBS.: O coordenador aparece tambï¿½ como formador
*/
function EFormadorComVisaoDeAluno($sock,$cod_curso,$cod_usuario)
{
//   global $visao_aluno_s;
  $estado=RetornaStatusUsuario($sock,$cod_curso,$cod_usuario);

  if ($estado=="F")
  {
    if ($_SESSION['visao_aluno_s'])
      return true;
    return false;
  }
  else
    return false;
}


/* *********************************************************************
   RetornaLinguaUsuario - Retorna o codigo da lingua selecionada pelo usuario
   Entradas: $sock - Socket da base de dados (retorno do Conectar)
             $cod_usuario - Cï¿½igo do usuï¿½io a buscar a informaï¿½o
   Saida: Codigo da Lingua selecionada pelo usuario 
*/
function RetornaLinguaUsuario($sock,$cod_usuario, $cod_curso)
{
//   global $cod_lingua_s;
   // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];

  $query  = "select U.cod_lingua ";
  $query .= "from ".$dbnamebase.".Usuario_curso UC ";
  $query .= "inner join ".$dbnamebase.".Usuario U ON (U.cod_usuario = UC.cod_usuario_global) ";
  $query .= "where UC.cod_usuario=".VerificaNumeroQuery($cod_usuario)." AND UC.cod_curso=".VerificaStringQuery($cod_curso);

  $res    = Enviar($sock,$query);

  if (RetornaNumLinhas($res)>0)
  {
    $linha=RetornaLinha($res);
    return ($linha[0]);
  }
  else
  {
    // Se nï¿½ tem registro (?!?) retorna a lingua atual
    return ($_SESSION['cod_lingua_s']);
  }
}

/* *********************************************************************
   EColaborador - Retorna se o usuario eh colaborador
   entrada: $sock - sock de conexao
            $cod_usuario - codigo do usuario a verificar
   saida: true - o usuario eh colaborador.
          false - o usuario nao eh colaborador.
*/
function EColaborador($sock, $cod_curso, $cod_usuario)
{

  // 2Session
  $dbnamebase = $_SESSION['dbnamebase'];
  $query = "select tipo_usuario from ".$dbnamebase.".Usuario_curso where cod_usuario=".VerificaNumeroQuery($cod_usuario)." and cod_curso=".VerificaStringQuery($cod_curso);
  $res   = Enviar ($sock, $query);
  $linha = RetornaLinha ($res);
  return ($linha ['tipo_usuario'] == 'Z');
}

function RetornaCodLinguaUsuario($sock,$cod_usuario)
{
  $query="select cod_lingua from Usuario where cod_usuario='".VerificaStringQuery($cod_usuario)."'";
  $res=Enviar($sock, $query);

  $linha=RetornaLinha($res);
  return ($linha[0]);
}

/* *********************************************************************
   PreencheuDadosPessoais - Verifica se o usuario preencheu os dados pessoais
   Entrada: $sock - BASE GERAL
   Saida: retorna true se o usuario preencheu seus dados pessoais;false caso contrario.
*/
function PreencheuDadosPessoais($sock)
{
  $query  = "select cod_usuario from Usuario ";
  $query .= "where cod_usuario = ".$_SESSION['cod_usuario_global_s']." and ";
  $query .= "rg is not NULL and ";
  $query .= "data_nasc is not NULL and ";
  $query .= "endereco is not NULL and ";
  $query .= "cidade is not NULL and ";
  $query .= "estado is not NULL and ";
  $query .= "pais is not NULL";

  $res    = Enviar($sock,$query);
  $res    = RetornaLinha($res);

  if($res != "")
    return true;
  else
    return false;
}

/* *********************************************************************
   RetornaEmailUsuario - Retorna o email do usuario
   Entradas: $sock - sock da base externa
            $cod_usuario - o cod_usuario_global do usuario  
   Saida: email
*/
function RetornaEmailUsuario1($cod_usuario)
{
  $sock = Conectar("");
  $query="select email from Usuario where cod_usuario=".$cod_usuario."";
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);
  //Desconectar($sock);
  return ($linha['email']);
}

/**
 * AtualizaVisita - Atualiza a visita do usuario no curso para verificar os usuarios online
 * @param $sock - sock para o curso
 * @param $cod_usuario - codigo do usuario a ser atualizado
 */
function AtualizaVisita($sock, $cod_usuario) {
  $agora = time();

  // caso do admtele
  if($cod_usuario < 0) {
    return false;
  }

  // pega a data de ultimo acesso do usuario no curso
  $query = "select ultimo_acesso from Usuarios_online where cod_usuario = ".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
  $linha=RetornaLinha($res);

  // se o usuario jah estava inserido na tabela, faz um update, senao faz um insert
  if($linha["ultimo_acesso"]) {
    $query = "update Usuarios_online set ultimo_acesso = ".$agora.", logado = 1 where cod_usuario = ".VerificaNumeroQuery($cod_usuario);
  } else {
    $query = "insert into Usuarios_online values (".VerificaNumeroQuery($cod_usuario).", ".$agora.", 1)";
  }
  Enviar($sock,$query);
}

/**
 * DeslogaUsuarioCursoDinamic - Altera status do usuario para deslogado dinamicamente  
 * @param $cod_curso - codigo do curso que o usuario esta acessando
 * @param $cod_usuario - codigo do usuario a ser deslogado
 */
function DeslogaUsuarioCursoDinamic($cod_curso, $cod_usuario) {
  $objResponse = new xajaxResponse();

  $sock=Conectar($cod_curso);
  $query = "update Usuarios_online set logado = 0 where cod_usuario = ".VerificaNumeroQuery($cod_usuario);
  $res=Enviar($sock,$query);
  Desconectar($sock);

  return $objResponse;
}

/**
 * RetornaUsuariosOnline - retorna os usuarios online de um curso
 * @param $sock - sock para o curso
 * @param $time_out - tempo que o usuario eh considerado online
 */
function RetornaUsuariosOnline($sock, $time_out) {
  $agora=time();

  $query="select cod_usuario from Usuarios_online where (".VerificaNumeroQuery($agora)." - ultimo_acesso < ".VerificaNumeroQuery($time_out).") and logado = 1";
  $res=Enviar($sock,$query);
  $linha=RetornaArrayLinhas($res);

  return $linha;
}

?>