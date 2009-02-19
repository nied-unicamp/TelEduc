<?
  $bibliotecas="../cursos/aplic/bibliotecas/";
  include($bibliotecas."geral.inc");
  include("../administracao/admin.inc");
  include("ajuda.inc");

  global $cod_lingua_s;

  $query="select * from Ajuda where cod_ferramenta=15";
  $sock=Conectar("");
  $res=Enviar($sock,$query);
  $array=RetornaArrayLinhas($res);

  $f="'F'";
  for ($i=0;$i<45;$i++)
  {
    $query= "insert into Ajuda (cod_ferramenta,cod_pagina,cod_lingua,tipo_usuario,texto,nome_pagina) values(15,".$array[$i]['cod_pagina'].",".$array[$i]['cod_lingua'].",";
    $query.= $f.",'".$array[$i]['texto']."','".$array[$i]['nome_pagina']."')";
    $foi=Enviar($sock,$query);
  }

  Desconectar($sock);
?>