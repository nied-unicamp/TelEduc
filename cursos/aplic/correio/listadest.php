<?php
$checked = "";
  echo("                        <div class=\"bordaBox\" style=\"float:left;\">\n");
//   echo("                    <form id=\"selectDest\" name=\"selectDest\" action=\"naumsei.php\" method=\"Post\" >");

  /* 30 - Todos os Formadores */
  echo("                          <ul>\n");
  echo("                            <li>\n");
  echo("                              <input name=\"chkTodosF\" id=\"chkTodosF\" type=\"checkbox\" value=\"F*\" onClick=\"MarcaOuDesmarcaTodos('F');\" /> ".RetornaFraseDaLista($lista_frases, 30)." <span id=\"mostraF\" class=\"link\" ver=\"nao\" onClick=\"MostraEscondeUsers('F')\">[ + ]</span><br>\n");
  echo("                            </li>\n");
  $cont = count($codFormadores);
  echo("                            <li>\n");
  echo("                              <ul id=\"ulUserF\" class=\"listaDest\" style=\"display:none\">\n");

  if ($acao == 1 && $codMsgAnt != "NULL"){
    /* formata Mensagem e Assunto anterior, para o padrão de resposta */
    $mensagem = EndentarMensagem($mensagem, $dataAnt, $nomeAutorAnt);
    /* 111 - Resp: */
    $cod_msg_assuntp = 111;
  }else if ($acao == 2 && $codMsgAnt != "NULL"){ 
    /* formata Mensagem e Assunto anterior, para o padrão de resposta */
    $mensagem = EndentarMensagem($mensagem, $dataAnt, $nomeAutorAnt);
    /* 111 - Resp: */
    $cod_msg_assuntp = 111;
  }else if($acao == 3 && $codMsgAnt != "NULL"){
    /* formata Mensagem e Assunto anterior, para o padrão de resposta */
    $mensagem = EndentarMensagem($mensagem, $dataAnt, $nomeAutorAnt);
    /* 110 - Red: */
    $cod_msg_assuntp = 110;
  }

  if(is_array($codFormadores) && ($cont > 0)){
    for($i=0 ; $i < $cont; $i++){
    
      if($codFormadores[$i]['cod_usuario']>=0){
  /*exclui o administrador do TelEduc da Lista */  
        echo("                                <li>\n");
        // $acao = 0 - noma mensagem
        // $acao = 1 - Responder 
        // $acao = 2 - Responder a todos os destinatarios
        // $acao = 3 - Redirecionar(arquivos e mensagem)

        if ($acao == 1 && $codMsgAnt != "NULL"){
          // msg estah sendo respondida. Destinatario eh o autor da msg anterior
          
          if ($codUsuarioAutorAnt == $codFormadores[$i]['cod_usuario']){
            $checked = "checked = checked";
          }else{
            $checked = "";
          }
        }

        else if ($acao == 2 && $codMsgAnt != "NULL"){ 

        // msg estah sendo respondida para todos destinatarios. Destinatarios sao destinatarios da msg anterior + autor
          $num=RetornaNumDestinatariosMsg($sock,$codMsgAnt);
          if (count($num)>0){
            $lista=RetornaCategDestinoCodUsuarioMsg($sock,$codMsgAnt);
            foreach($lista as $cod=>$dados){
// echo ("dados['cod_destino'] : ". $dados['cod_destino'] . "<br> CodFormadores: ". $codFormadores[$i]['cod_usuario'] ."<br> codUsuarioAutosAnt: " . $codUsuarioAutorAnt ."<br> cod_usuario: ". $cod_usuario );

              if( //marca como o checkbox SE:
                  (
                    (($dados['cod_destino'] == $codFormadores[$i]['cod_usuario']) && (($dados['cod_destino'] != $cod_usuario) || ($codUsuarioAutorAnt == $cod_usuario)) && ($dados['categ_destino'] == 'U'))
                  ) 
                    //(a pessoa na lista de destinatario corresponde ao checkbox que estah sendo analisado, E se a pessoa na lista de destinatario nao eh a pessoa que estah enviando a mensagem OU se quem enviou a mensagem anterior enviou a mensagem para ela mesma) E a categoria do destinatario eh Usuario.
                    
                    || 

                    (($codUsuarioAutorAnt == $codFormadores[$i]['cod_usuario']) && ($codUsuarioAutorAnt != $cod_usuario)) 
//                     OU se o destinatário da mensagem anterior(que está sendo respondida) eh o mesmo do checkbox que estah sendo analisado E se o destinátario da mensagem anterior, nao eh o autor da mensagem atual(resposta)
                  
                ) {
                $checked = "checked = checked";
                break;
              }else{

                $checked = "";
              }
            }
          } 
        }

        echo("                                  <input name=\"chkF[]\" id=\"chkF\" type=\"checkbox\" value=\"".$codFormadores[$i]['cod_usuario']."\" onclick=\"ControlaChkTodos('F', this)\" ".$checked."/> <span class=\"link\" onclick='OpenWindowPerfil(".$codFormadores[$i]['cod_usuario'].");'> ".
  RetornaNomeUsuarioDeCodigo($sock, $codFormadores[$i]['cod_usuario'],$cod_curso)."</span><br />\n");

        echo("                                </li>\n");
      }
    }
  
    echo("                              </ul>\n");
    echo("                            </li>\n");
  }

  $cont = count($codAlunos);
  $checked = "";
  if(is_array($codAlunos) && ($cont > 0)){

    /* 31 - Todos os alunos*/
    echo("                            <li>\n");
    echo("                              <input name=\"chkTodosA\" id=\"chkTodosA\" type=\"checkbox\" value=\"A*\" onclick=\"MarcaOuDesmarcaTodos('A');\" /> ".RetornaFraseDaLista($lista_frases, 31)." <span id=\"mostraA\" class=\"link\" style=ver:nao onclick=\"MostraEscondeUsers('A')\">[ + ]</span><br />\n");
    echo("                            </li>\n");

    echo("                            <li>\n");
    echo("                              <ul id=\"ulUserA\" class=\"listaDest\" style=\"display:none\">\n");

    for($i=0 ; $i < $cont; $i++){

      if( $codAlunos[$i]['cod_usuario'] >= 0 ){ 
        echo("                                <li>\n");
        if ($acao == 1 && $codMsgAnt != "NULL"){
          // msg estah sendo respondida. Destinatario eh o autor da msg anterior
          if ($codUsuarioAutorAnt == $codAlunos[$i]['cod_usuario']){
            $checked = "checked = checked";
          }else{$checked = "";}
        }

        else if ($acao == 2 && $codMsgAnt != "NULL"){ 
        // msg estah sendo respondida para todos destinatarios. Destinatarios sao destinatarios da msg anterior + autor
          if ($num>0){
            $lista=RetornaCategDestinoCodUsuarioMsg($sock,$codMsgAnt);
            foreach($lista as $cod=>$dados){

              if( //marca como o checkbox SE:
                  (                       
                    (($dados['cod_destino'] == $codAlunos[$i]['cod_usuario']) && (($dados['cod_destino'] != $cod_usuario) || ($codUsuarioAutorAnt == $cod_usuario)) && ($dados['categ_destino'] == 'U'))
                  ) 
                    //(a pessoa na lista de destinatario corresponde ao checkbox que estah sendo analisado, E (se a pessoa na lista de destinatario nao eh a pessoa que estah enviando a mensagem OU se quem enviou a mensagem anterior enviou a mensagem para ela mesma) ) E se a categoria do Destinatario eh Usuario

                      ||

                    (($codUsuarioAutorAnt == $codAlunos[$i]['cod_usuario']) && ($codUsuarioAutorAnt != $cod_usuario)) 
                    //OU se o destinatário da mensagem anterior(que está sendo respondida) eh o mesmo do checkbox que estah sendo analisado E se o destinátario da mensagem anterior, nao eh o autor da mensagem atual(resposta)
                  ) {
                $checked = "checked = checked";
                break;
              }else{
                $checked = "";
              }
            }
          }
        }

        echo("                                  <input name=\"chkA[]\" id=\"chkA\" type=\"checkbox\" value=\"".$codAlunos[$i]['cod_usuario']."\" onclick=\"ControlaChkTodos('A', this)\" ".$checked." /> <span class=\"link\" onclick='OpenWindowPerfil(".$codAlunos[$i]['cod_usuario'].");'> ".
RetornaNomeUsuarioDeCodigo($sock, $codAlunos[$i]['cod_usuario'],$cod_curso)."</span><br />\n");
        echo("                                </li>\n");
      }
    }
  
    echo("                              </ul>\n");
    echo("                            </li>\n");
  }

  $cont = count($codColaboradores);
  $checked = "";

  if(is_array($codColaboradores) && ($cont > 0)){

    /* 117 - Todos os Colaboradores*/
    echo("                            <li>\n");
    echo("                              <input name=\"chkTodosC\" id=\"chkTodosC\" type=\"checkbox\" value=\"C*\" onclick=\"MarcaOuDesmarcaTodos('C');\" /> ".RetornaFraseDaLista($lista_frases, 117)." <span id=\"mostraC\" class=\"link\" style=ver:nao onclick=\"MostraEscondeUsers('C')\">[ + ]</span><br />\n");
    echo("                            </li>\n");
  
    
    echo("                            <li>\n");
    echo("                              <ul id=\"ulUserC\" class=\"listaDest\" style=\"display:none\">\n");
    for($i=0 ; $i < $cont; $i++){
      if( $codColaboradores[$i]['cod_usuario'] >= 0 ){
        echo("                                <li>\n");
        if ($acao == 1 && $codMsgAnt != "NULL"){
          // msg estah sendo respondida. Destinatario eh o autor da msg anterior
          // $linha=RetornaInfosMensagem($sock,$codMsgAnt);
          if ($codUsuarioAutorAnt == $codColaboradores[$i]['cod_usuario']){
            $checked = "checked = checked";
          }else{$checked = "";}
        }

        else if ($acao == 2 && $codMsgAnt != "NULL"){ 
        // msg estah sendo respondida para todos destinatarios. Destinatarios sao destinatarios da msg anterior + autor
          if ($num>0){
            $lista=RetornaCategDestinoCodUsuarioMsg($sock,$codMsgAnt);
            foreach($lista as $cod=>$dados){
              if( //marca como o checkbox SE:
                  (                       
                    (($dados['cod_destino'] == $codColaboradores[$i]['cod_usuario']) && (($dados['cod_destino'] != $cod_usuario) || ($codUsuarioAutorAnt == $cod_usuario)) && ($dados['categ_destino'] == 'U'))
                  ) 
                    //(a pessoa na lista de destinatario corresponde ao checkbox que estah sendo analisado, E se a pessoa na lista de destinatario nao eh a pessoa que estah enviando a mensagem OU se quem enviou a mensagem anterior enviou a mensagem para ela mesma) E se a categoria do Destinatario eh Usuario

                    ||

                    (($codUsuarioAutorAnt == $codColaboradores[$i]['cod_usuario']) && ($codUsuarioAutorAnt != $cod_usuario)) 
                    //OU se o destinatário da mensagem anterior(que está sendo respondida) eh o mesmo do checkbox que estah sendo analisado E se o destinátario da mensagem anterior, nao eh o autor da mensagem atual(resposta)
                  ) {
                $checked = "checked = checked";
                break;
              }else{
                $checked = "";
              }
            }
          }
        }

        echo("                                  <input name=\"chkC[]\" id=\"chkC\" type=\"checkbox\" value=\"".$codColaboradores[$i]['cod_usuario']."\" onclick=\"ControlaChkTodos('C', this)\" ".$checked." /> <span class=\"link\" onclick='OpenWindowPerfil(".$codColaboradores[$i]['cod_usuario'].");'> ".
RetornaNomeUsuarioDeCodigo($sock, $codColaboradores[$i]['cod_usuario'],$cod_curso)."</span><br />\n");
        echo("                                </li>\n");
      }
    }
  
    echo("                              </ul>\n");
    echo("                            </li>\n");
  }


  $cont = count($codGrupos);
  $checked ="";
  if(is_array($codGrupos) && ($cont > 0)){

    /* 32 - Todos os grupos*/
    echo("                            <li>\n");
    echo("                              <input name=\"chkTodosG\" id=\"chkTodosG\" type=\"checkbox\" value=\"G*\" onclick=\"MarcaOuDesmarcaTodos('G');\" /> ".RetornaFraseDaLista($lista_frases, 32)." <span id=\"mostraG\" class=\"link\" style=ver:nao onclick=\"MostraEscondeUsers('G')\">[ + ]</span><br />\n");
    echo("                            </li>\n");
    echo("                            <li>\n");
    echo("                              <ul id=\"ulUserG\" class=\"listaDest\" style=\"display:none\">\n");
  
    for($i=0 ; $i < $cont; $i++){
      if( $codGrupos[$i]['cod_grupo'] >= 0 ){ 
        echo("                                <li>\n");
        if ($acao == 1 && $codMsgAnt != "NULL"){
          // msg estah sendo respondida. Destinatario eh o autor da msg anterior
          if ($linha['cod_destino'] == $codGrupos[$i]['cod_grupo']){
            $checked = "checked = checked";
          }else{$checked = "";}
        }

        else if ($acao == 2 && $codMsgAnt != "NULL"){ 
        // msg estah sendo respondida para todos destinatarios. Destinatarios sao destinatarios da msg anterior + autor

          if ($num>0){
            $lista=RetornaCategDestinoCodUsuarioMsg($sock,$codMsgAnt);
            foreach($lista as $cod=>$dados){
              if ($dados['categ_destino'] == 'g'){
// echo ("dados['cod_destino'] : ". $dados['cod_destino'] . "<br> CodGrupo: ". $codGrupos[$i]['cod_grupo'] ."<br> codUsuarioAutosAnt: " . $codUsuarioAutorAnt ."<br> cod_usuario: ". $cod_usuario );

                if( ($dados['cod_destino'] == $codGrupos[$i]['cod_grupo'])){

//acho que essa parte não é util, depois de testar, apagar!!!
//                 ||
// 
//                 (($codUsuarioAutorAnt == $codGrupos[$i]['cod_grupo']) && ($codUsuarioAutorAnt != $cod_usuario)) ) {
                  $checked = "checked = checked";
                  break;
                }else{
                  $checked = "";
                }
              }
            }
          }
        }
  
        echo("                                  <input name=\"chkG[]\" id=\"chkG\" type=\"checkbox\" value=\"".$codGrupos[$i]['cod_grupo']."\" onclick=\"ControlaChkTodos('G', this)\" ".$checked." /> <span class=\"link\" onclick='OpenWindowGrupo(".$codGrupos[$i]['cod_grupo'].");'> ". RetornaGrupoComCodigo($sock, $codGrupos[$i]['cod_grupo']) ."</span><br />\n");
        echo("                                </li>\n");
      }
    }

    echo("                              </ul>\n");
    echo("                            </li>\n");
  }
  echo("                          </ul>\n");
//   echo("</li>\n");

echo("                      </div>\n");
?>