<?php
require_once 'cursos.inc';
require_once 'usuarios.inc';
class Email{
	static function MontaMsg($host, $raiz_www, $cod_curso, $mensagem, $assunto, $cod_usuario_remetente='', $destinatarios=''){
		$socket = AcessoSQL::Conectar("");
		$lista_frases=Linguas::RetornaListaDeFrases($socket,11);
	
	
		if($cod_curso!=''){
			$sock = AcessoSQL::Conectar("");
			$nome_curso = Cursos::NomeCurso($sock, $cod_curso);
			AcessoSQL::Desconectar($sock);
		}
	
	
		if($cod_usuario_remetente!=''){
			$sock = AcessoSQL::Conectar($cod_curso);
			$nome_remetente = Usuarios::NomeUsuario($sock, $cod_usuario_remetente, $cod_curso);
			AcessoSQL::Desconectar($sock);
		}
	
		$html_email="<html>\n";
		$html_email.="\t<head>\n";
		$html_email.="\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
		$html_email.="\t<title>".htmlentities($assunto)."</title>\n";
		$html_email.="\t</head>\n";
		$html_email.="\t<body>\n";
		$html_email.="\t\t<table align=\"center\" width=\"550px\" style=\"font-size:14px;\">\n";
		$html_email.="\t\t\t<tr>\n";
		$html_email.="\t\t\t\t<td style=\"font:0.76em Arial, Verdana, Trebuchet MS; color:#4a4a4a; line-height:16px; margin:0; border-right:1px solid #dcdcdc; border-left:1px solid #dcdcdc;\">\n";
		$html_email.="\t\t\t\t\t<table class=\"tabInterna\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$html_email.="\t\t\t\t\t\t<tr>\n";
		$html_email.="\t\t\t\t\t\t\t<td background=\"http://".$host.$raiz_www."/cursos/aplic/imgs/fdoEmail.gif\" width=\"550px\" style=\"text-align:left; padding:6px 0 0 20px;\" height=\"120px\">\n";
		$html_email.="\t\t\t\t\t\t\t\t<font size=\"2\">\n";
	
		if($cod_curso!=''){
			/* 116 - Nome do curso */
			$html_email.="\t\t\t\t\t\t\t\t\t<strong>".Linguas::RetornaFraseDaLista($lista_frases,116).":</strong> ".$nome_curso."<br />\n";
		}
	
		/* 24 - Data */
		$html_email.="\t\t\t\t\t\t\t\t\t<strong>".Linguas::RetornaFraseDaLista($lista_frases,24).":</strong> ";
		$html_email.=Data::UnixTime2DataHora(time())."<br />\n";
	
		if($cod_usuario_remetente!=''){
			/* 23 - Remetente */
			$html_email.="\t\t\t\t\t\t\t\t\t<strong>".Linguas::RetornaFraseDaLista($lista_frases,23).":</strong> ";
			$html_email.=$nome_remetente."<br />\n";
		}
		if($destinatarios!=''){
			/* 27 - Destinatários */
			$html_email.="\t\t\t\t\t\t\t\t\t<strong>".Linguas::RetornaFraseDaLista($lista_frases,27).":</strong> ";
			$html_email.=htmlentities($destinatarios)."<br />\n";
		}
		$html_email.="\t\t\t\t\t\t\t\t\t<strong>Assunto:</strong> ".htmlentities($assunto)."\n";
		$html_email.="\t\t\t\t\t\t\t\t</font>\n";
		$html_email.="\t\t\t\t\t\t\t</td>\n";
		$html_email.="\t\t\t\t\t\t\t<td width=\"10%\" background=\"http://".$host.$raiz_www."/cursos/aplic/imgs/fdoEmail.gif\" style=\"padding-right:5px;\">\n";
		$html_email.="\t\t\t\t\t\t\t<img src=\"http://".$host.$raiz_www."/cursos/aplic/imgs/logo.gif\" alt=\"TelEduc\">\n";
		$html_email.="\t\t\t\t\t\t\t</td>\n";
		$html_email.="\t\t\t\t\t\t</tr>\n";
		$html_email.="\t\t\t\t\t\t<tr>\n";
		$html_email.="\t\t\t\t\t\t\t<td colspan=\"2\" style=\"text-indent:10px; text-align:left; padding:10px 20px 0px 20px;\">\n";
		$html_email.="\t\t\t\t\t\t\t\t<font size=\"2\">\n";
		$html_email.="<br /><br />\n";
		$html_email.=$mensagem;
		$html_email.="\t\t\t\t\t\t\t\t</font>\n";
		$html_email.="\t\t\t\t\t\t\t</td>\n";
		$html_email.="\t\t\t\t\t\t</tr>\n";
		$html_email.="\t\t\t\t\t</table>\n";
		$html_email.="\t\t\t\t</td>\n";
		$html_email.="\t\t\t</tr>\n";
		$html_email.="\t\t</table>\n";
		$html_email.="\t</body>\n";
		$html_email.="</html>\n";
		//  echo($html_email);exit();
	
		return $html_email;
	}
	

	/* ***********************************************************
	 MandaMsg - Envia uma mensagem usando o sendmail, via Bcc.
	Entrada: $remetente - e-mail do emissor.
	$destino - e-mail(s) do destinat�io (separar por virgulas)
	$assunto - Assunto da mensagem
	$mensagem - Mensagem a ser enviada
	*/
	static function MandaMsg($remetente,$destino,$assunto,$mensagem, $nomearquivo='')
	{
		$caminho_sendmail_s = Email::CaminhoSendmailS();
		/* Verifica se o tamanho do remetente �seguro */
	
		if (strlen($destino)<1024)
		{
	
			if ($nomearquivo!='')
			{
				$fp = popen($caminho_sendmail_s." ".$destino." < ".$nomearquivo,"w");
			}
			else
			{
				if (eregi("<",$remetente))
				{
					$tmp=explode("<",$remetente);
					$tmp=explode(">",$tmp[1]);
					$remetente_email=$tmp[0];
				}
				else
					$remetente_email=$remetente;
	
				$comando = $caminho_sendmail_s." -t ";
				$fp = popen($comando,"w");
	
				$headers = "From: ".$remetente."\n";
				$headers.= "Bcc: ".$destino."\n";
				$headers.= "Subject: ".$assunto."\n";
				$headers.= "X-Mailer: PHP/".phpversion()."\n";
				$headers.= "MIME-Version: 1.0\n";
				$headers.= "Content-Type: text/html; charset=ISO-8859-1\n";
				$headers.= "Content-Transfer-Encoding: 7bit\n";
	
				fputs($fp, $headers);
				fputs($fp, $mensagem);
			}
			pclose($fp);
		}
		else
		{
			Email::MandaMsgMultiplos($remetente,$destino,$assunto,$mensagem,$nomearquivo);
		}
	
		return true;
	}
	
	/* ***********************************************************
	 MandaMsgMultiplos - Envia uma mensagem usando o sendmail, via Bcc, quebrando em peda�s.
	Entrada: $remetente - e-mail do emissor.
	$destino - e-mail(s) do destinat�io (separar por virgulas)
	$assunto - Assunto da mensagem
	$mensagem - Mensagem a ser enviada
	*/
	static function MandaMsgMultiplos($remetente,$destino,$assunto,$mensagem, $nomearquivo='')
	{
		$destinos=explode(",",$destino);
		$destinopequeno = "";
		unset($destinopequeno);
		$destinopequeno="";
	
		if (count($destinos)>0){
			foreach ($destinos as $cod => $linha)
			{
				if (strlen($destinopequeno)+strlen($linha)<1023)
					$destinopequeno.=",".$linha;
				else
				{
					Email::MandaMsg($remetente,$destinopequeno,$assunto,$mensagem, $nomearquivo);
					$destinopequeno = $linha;
				}
			}
		}
		if (isset($destinopequeno)){
			Email::MandaMsg($remetente,$destinopequeno,$assunto,$mensagem,$nomearquivo);
		}
	}
	
	static function CaminhoSendmailS(){
		$sock=AcessoSQL::Conectar("");
		
		/* Se for, envia o e-mail */
		$query="select diretorio from Diretorio where item='sendmail'";
		$res=AcessoSQL::Enviar($sock,$query);
		$linha=AcessoSQL::RetornaLinha($res);
		$caminho_sendmail_s=$linha[0];
		
		return $caminho_sendmail_s;
		
		AcessoSQL::Desconectar($sock);
		
	}
	
	
	
	
	
}