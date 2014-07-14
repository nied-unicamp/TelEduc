<?php
include "geral.inc";
include "nao_existe.php";
$sock = AcessoSQL::Conectar("");
$lista_frase = Linguas::RetornaListaDeFrases($sock, 12);
echo Linguas::RetornaFraseDaLista($lista_frase, 11);

$string = "942112500b";
		
$senha = crypt($string,"AA");

echo $senha;


?>