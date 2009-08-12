<?
include("voureprovar.inc");
$acao=$_POST['acao'];
if($acao=="Voureprovar"){
	  $cod_curso=$_POST['cod_curso'];
	  $estudo=$_POST['estudo'];
	  $cr=$_POST['cr'];
	  $result=Resultado($estudo,$cr);
	  header("Location: resultado.php?result=".$result."&cod_curso=".$cod_curso);
}
 ?>