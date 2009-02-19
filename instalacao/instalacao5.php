<?
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : instalacao/instalacao5.php

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
  ARQUIVO : instalacao/instalacao5.php
  ========================================================== */
  include "instalacao.inc";
/*
  if (!is_readable($mimetypes))
    Voltar("<font color=red>O arquivo \"mime.types\" não possui permissão para leitura. Altere suas permissões para 644 e reinicie a instalação</font>");
*/
  ExibirCabecalho(5,"Dados da administração do Ambiente");

  session_register("teleduc_login_s");
  session_register("teleduc_senha_s");
  session_register("dbbasegeral_s");
  session_register("dbbasecurso_s");
  session_register("importar_curso_extr_s");
  session_register("tmpteleduc_login_s");
  session_register("tmpteleduc_senha_s");
  session_register("tmpdbbasecurso_s");
  
  $sock=ConectarDB($dbbasegeral_s,$teleduc_login_s,$teleduc_senha_s);

  if ($sock==-1)
    Voltar("Erro inesperado! Reinicie a instalação");

  // Descomentar quando para valer
  EspalharArquivoAuth($ambiente);

  $query="update Diretorio set diretorio='".$ambiente."' where item='Ambiente'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$arquivos."' where item='Arquivos'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$arquivosweb."' where item='ArquivosWeb'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$extracao."' where item='Extracao'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$raiz_www."' where item='raiz_www'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$sendmail."' where item='sendmail'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$mysqldump."' where item='mysqldump'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$mimetypes."' where item='mimetypes'";
  Enviar($sock,$query);
  $query="update Diretorio set diretorio='".$tar."' where item='tar'";
  Enviar($sock,$query);
  if($importar_curso_extr_s)
  {
    $query="update Diretorio set diretorio='".$montagem."' where item='Montagem'";
    Enviar($sock,$query);
    $query="update Config set valor='sim' where item='listarext'";
    Enviar($sock,$query);   
  }    
  $query="update Extracao set valor='".$extracao."/{Cursos_extraidos.codigo}' where item='diretorio'";
  Enviar($sock,$query);


  CriaTelEducConf($ambiente."/teleduc.conf",$dbbasegeral_s,$dbbasecurso_s,$teleduc_login_s,$teleduc_senha_s,$tmpdbbasecurso_s,$tmpteleduc_login_s,$tmpteleduc_senha_s, $importar_curso_extr_s);
  
  
  if (!file_exists($arquivos))
    CriaDiretorio($arquivos);
  if (!file_exists($arquivosweb))
    CriaDiretorio($arquivosweb);
  if (!file_exists($arquivosweb."/tmp"))
    CriaDiretorio($arquivosweb."/tmp");
  if (!file_exists($extracao))
    CriaDiretorio($extracao);
    
  if(($importar_curso_extr_s) && (!file_exists($montagem)))
    CriaDiretorio($montagem);


  echo("
    <script type=\"text/javascript\" language=javascript>
      function Valida()
      {
        var senha_admin=document.inst.senha_admin.value;
        while (senha_admin.search(\" \") != -1)
          senha_admin = senha_admin.replace(/ /, \"\");

        var senha_conf=document.inst.senha_conf.value;
        while (senha_conf.search(\" \") != -1)
          senha_conf = senha_conf.replace(/ /, \"\");

        var host=document.inst.host.value;
        if (host.indexOf('.')< 0) {
	  alert ('Por Favor, coloque o endereço completo no nome da máquina. Ex: www.nied.unicamp.br e não apenas nied');
          return false;
        }
        while (host.search(\" \") != -1)
          host = host.replace(/ /, \"\");

        var nome_admin=document.inst.nome_admin.value;
        while (nome_admin.search(\" \") != -1)
          nome_admin = nome_admin.replace(/ /, \"\");

        var mail_admin=document.inst.mail_admin.value;

        var regras1 = /(@.*@)|(\.{2,})|(@\.)|(\.@)|(^\.)|(\.$)/;
        var regras2 = /^[a-zA-Z0-9\_\-\.]+\@[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})$/;

        if (regras1.test(mail_admin) || !regras2.test(mail_admin))
        {
          alert('email inválido!');
          return(false);
        }

        if (senha_admin=='' || senha_conf=='' || host=='' || nome_admin=='' || mail_admin=='')
        {
          alert('Nenhum campo deve ser deixado em branco.');
          return false;
        }
        if (document.inst.senha_admin.value != document.inst.senha_conf.value)
        {
          alert('A senha não confere.');
          return false;
        }

        return true;
      }
    </script>
  ");



  $nome_da_maquina = $_SERVER['SERVER_NAME'];
  if ($nome_da_maquina == "")
    $nome_da_maquina = $_SERVER['SERVER_ADDR'];

  AbreForm();

  /* <!----------------- Tabelao -----------------> */
  echo("<table cellpadding=\"0\" cellspacing=\"0\" class=\"tabExterna\">\n");
  echo("<tr>\n");
  /* <!----------------- Tabela Interna -----------------> */
  echo("<td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" class=\"tabInterna\">\n");
  echo("<tr><td style=\"padding-left: 150px; padding-top: 15px; padding-right: 150px; padding-bottom: 15px; font-size: small;\" align=\"left\">\n");
  Paragrafo("1 - Diretórios configurados com sucesso!");

  echo("<br/>\n");

  Paragrafo("<b><font size=+1>Preencha os dados da administração abaixo:</font></b>");

  Paragrafo("O ambiente utiliza o usuário '<font color=\"#2a6686\">admtele</font>' para acesso à administração do ambiente (Página Inicial). Especifique uma senha para esse usuário e <b>utilize essas informações</b> para autentificar na Administração do ambiente.");

  CaixaSenha("Senha para a conta de administração do TelEduc (usuário '<font color=\"#2a6686\">admtele</font>'):","senha_admin");
  CaixaSenha("Confirme a senha do Administrador:","senha_conf");
  CaixaTexto("Nome da máquina com TelEduc na rede (Ex. www.nied.unicamp.br): ","host",$nome_da_maquina); // teleduc por padrão
  CaixaTexto("Nome do administrador: ","nome_admin",""); // teleduc por padrão
  CaixaTexto("E-mail do administrador: ","mail_admin",""); // teleduc por padrão

  echo("</td></tr></table>\n");
  echo("</td></tr></table>\n");

  EncerraPagina();
?>

