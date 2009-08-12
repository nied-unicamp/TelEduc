<?
$bibliotecas="../bibliotecas/";
include($bibliotecas."geral.inc");
function Resultado($estudo,$cr){
	            if($estudo>=3 && $cr<=0.5){
	                         $situacao=64;
	                         return($situacao);
	            }
	            if($estudo<=3 && $cr<=0.5){
	            	         $situacao=66;
	            	         return($situacao);
	            }
	            if($estudo>=1 && $cr>0.5){
	            	         $situacao=65;
	            	         return($situacao);
	            }
	            if($estudo<1 && $cr>8){
	                         $situacao=65;
	                         return($situacao);
	            }
	            
       }

?>