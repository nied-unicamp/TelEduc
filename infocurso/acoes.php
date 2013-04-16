<?php
/*
<!--
-------------------------------------------------------------------------------

    Arquivo : infocurso/acoes.php

    TelEduc - Ambiente de Ensino-Aprendizagem a Distï¿½ncia
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

    Nied - Nï¿½cleo de Informï¿½tica Aplicada ï¿½ Educaï¿½ï¿½o
    Unicamp - Universidade Estadual de Campinas
    Cidade Universitï¿½ria "Zeferino Vaz"
    Bloco V da Reitoria - 2o. Piso
    CEP:13083-970 Campinas - SP - Brasil

    http://www.nied.unicamp.br
    nied@unicamp.br

------------------------------------------------------------------------------
-->
*/

/*==========================================================
  ARQUIVO : infocurso/acoes.php
  ========================================================== */

 $bibliotecas="../cursos/aplic/bibliotecas/";
 include $bibliotecas."/geral.inc";
 include("../administracao/admin.inc");
 include("reenvio.inc");

 VerificaAutenticacaoAdministracao();

 $sock=Conectar("");

 $lista_frases=RetornaListaDeFrases($sock,-5);

  Desconectar($sock);

  switch ($acao){
    case 'email':
      foreach ($cod_curso as $cod_curso_atual){
        var_dump($cod_curso_atual);

        // Conecta-se ao Banco para pegar os dados do formador
        $sock = Conectar("");

        $query="select valor from Config where item='host'";
        $res=Enviar($sock,$query);
        $linha=RetornaLinha($res);
        $host=$linha['valor'];

        $query="select diretorio from Diretorio where item='raiz_www'";
        $res=Enviar($sock,$query);
        $linha=RetornaLinha($res);
        $raiz_www=$linha['diretorio'];


        // Informações sobre o Usuario:
        $query = "select UC.tipo_usuario, UC.cod_usuario_global, UC.cod_curso,  U.nome, U.email, U.login, U.cod_usuario from Usuario_curso as UC, Usuario as U where UC.cod_usuario_global <> -1 and UC.cod_usuario_global = U.cod_usuario and UC.tipo_usuario = 'F' and UC.cod_curso = ".$cod_curso_atual;
        $res = Enviar($sock, $query);
        $info = RetornaLinha($res);

        // Descobre nome do Curso:
        $query = "select cod_curso, nome_curso from Cursos where cod_curso = ".$cod_curso_atual;
        $res = Enviar($sock, $query);
        $info_curso = RetornaLinha($res);

        // Unificando:
        $info['nome_curso'] = $info_curso['nome_curso'];

        // Nova senha para o formador:
        $senha = GeraSenha();
        $senha_crypt = crypt($senha, 'AA');

        // Muda a senha:
        $query = "update Usuario set senha = '".$senha_crypt."' where cod_usuario = ".$info['cod_usuario'];
        $res = Enviar($sock, $query);

        // Monta Email:
        $remetente = RetornaConfig('adm_email'); 
        $destino = $info['email'];
        $assunto = RetornaFraseDaLista($lista_frases,99);
        $endereco=$host.$raiz_www;

        /* 100 - Seu pedido para realiza��o do curso*/
        /* 101 - foi aceito.*/
        /* 102 - Para acessar o curso, a sua Identifica��o �:*/
        /* 103 - e a sua senha �:*/
        /* 104 - O acesso deve ser feito a partir do endereco:*/
        /* 105 - Atenciosamente, Administra��o do Ambiente TelEduc*/

        $mensagem = $info['nome'].",\n\n";
        $mensagem.=RetornaFraseDaLista($lista_frases,100)." ".$info['nome_curso']." ".RetornaFraseDaLista($lista_frases,101)."\n\n";
        $mensagem.=RetornaFraseDaLista($lista_frases,102)." ".$info['login']." ".RetornaFraseDaLista($lista_frases,103)." ".$senha."\n\n";
        $mensagem.=RetornaFraseDaLista($lista_frases,104)."\nhttp://".$endereco."/cursos/aplic/index.php?cod_curso=".$info['cod_curso']."\n\n";
        $mensagem.=RetornaFraseDaLista($lista_frases,105)."\n";

        // Envia
        $mensagem_envio = MontaMsg($host, $raiz_www, $cod_curso_atual, $mensagem, $assunto);
        MandaMsg($remetente,$destino,$assunto,$mensagem_envio);

      }
      header("Location: ../administracao/index.php");
  }
  
?>