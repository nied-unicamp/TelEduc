#!/bin/bash

#Declaração de funções a serem usadas pelo script.

function testa_entrada()
{

  local ent=$1
  case $ent in
    S | s | Y | y | sim | Sim | SIM | yes | Yes | YES) ent="s";;
    N | n | não | Não | NÃO | nao | Nao | NAO | no | No | NO) ent="n";;
    *) ent="";;
  esac

  ret=$ent

  return
}

function reseta_cor()
{

 tput sgr0
}


#Seta vriáveis de cor!
vermelho='\E[0;31m'
verde='\E[0;32m'
azul='\E[0;34m'
cyan='\E[0;36m'
nocolor='\E[0;0m'
am_bold='\E[1;33m'
cy_bold='\E[1;36m'
ve_bold='\E[1;32m'


echo -e "Patch de atualização do TelEduc da versão" "$vermelho""4.0.1" "$nocolor""para" "$vermelho""4.1"
reseta_cor

confirma=""
while [ -z $confirma ]
do
  echo -e "Deseja continuar?" "[""$ve_bold""s""$nocolor"/""$ve_bold""n"$nocolor""]"
  read confirma
  testa_entrada $confirma
  confirma=$ret
done
                                                                                                                            
if [ $confirma != "s" ]
then
  exit 0
fi

echo


echo "Efetuando as Alterações na Base de Dados" 
cp ../.auth ./
php patch_geral.php
rm .auth
echo "Lendo Arquivo de configuração." 

#Le .auth, obtém caminho do teleduc.conf
tele_path=`cat ../.auth`

#Le Config do arquivo teleduc.conf
dbuser=`cat $tele_path/teleduc.conf | grep dbuser | cut -c 8-`
dbname=`cat $tele_path/teleduc.conf | grep dbnamebase | cut -c 12-`
dbpass=`cat $tele_path/teleduc.conf | grep dbpassword | cut -c 12-`

dbhost=`cat $tele_path/teleduc.conf | grep dbhost | cut -c 8-`
dbnamecurso=`cat $tele_path/teleduc.conf | grep dbnamecurso | cut -c 13-`

#Seta os valores-padrão de usuário para acesso às bases e nome da base de dados.
#PROBLEMA: Patch nao cria isso! Instalacao sim! como resolver?!
db_temp=`cat $tele_path/teleduc.conf | grep dbtmpnamecurso | cut -c 16-`
db_temp_user=`cat $tele_path/teleduc.conf | grep dbtmpuser | cut -c 11-`
db_temp_pass=`cat $tele_path/teleduc.conf | grep dbtmppassword | cut -c 15-`

echo 

    echo Atualizando Frases e Ajuda do Ambiente:
    echo "DROP TABLE Lingua_textos" | mysql -u$dbuser -p$dbpass $dbname
    mysql -u$dbuser -p$dbpass $dbname < Lingua_textos_tel_4.1.sql

confirma=""
turno=0

#É preciso verificar se isto esta habilitado com uma consulta à base!!!

importacao="`echo "select valor from Config where item='listarext'" | mysql -s -u$dbuser -p$dbpass $dbname`"
patch="`echo "select patch from Patchs where patch='4.0'" | mysql -s -u$dbuser -p$dbpass $dbname`"

echo 
echo Copiando os arquivos novos

chmod -R 777 ../
cp -r arquivos/* ../
cp -r --preserve=mode ../.auth ../administracao
cp -r --preserve=mode ../.auth ../administracao/base_curso
cp -r --preserve=mode ../.auth ../ajuda
cp -r --preserve=mode ../.auth ../avaliarcurso
cp -r --preserve=mode ../.auth ../cursos
cp -r --preserve=mode ../.auth ../cursos/aplic
cp -r --preserve=mode ../.auth ../cursos/aplic/acessos
cp -r --preserve=mode ../.auth ../cursos/aplic/administracao
cp -r --preserve=mode ../.auth ../cursos/aplic/agenda
cp -r --preserve=mode ../.auth ../cursos/aplic/ajuda
cp -r --preserve=mode ../.auth ../cursos/aplic/bibliotecas
cp -r --preserve=mode ../.auth ../cursos/aplic/bibliotecas/rte/
cp -r --preserve=mode ../.auth ../cursos/aplic/bibliotecas/htmlarea/
cp -r --preserve=mode ../.auth ../cursos/aplic/busca
cp -r --preserve=mode ../.auth ../cursos/aplic/configurar
cp -r --preserve=mode ../.auth ../cursos/aplic/correio
cp -r --preserve=mode ../.auth ../cursos/aplic/diario
cp -r --preserve=mode ../.auth ../cursos/aplic/dinamica
cp -r --preserve=mode ../.auth ../cursos/aplic/enquete
cp -r --preserve=mode ../.auth ../cursos/aplic/extracao
cp -r --preserve=mode ../.auth ../cursos/aplic/estrutura
cp -r --preserve=mode ../.auth ../cursos/aplic/forum
cp -r --preserve=mode ../.auth ../cursos/aplic/grupos
cp -r --preserve=mode ../.auth ../cursos/aplic/imgs
cp -r --preserve=mode ../.auth ../cursos/aplic/js-css
cp -r --preserve=mode ../.auth ../cursos/aplic/material
cp -r --preserve=mode ../.auth ../cursos/aplic/mural
cp -r --preserve=mode ../.auth ../cursos/aplic/perfil
cp -r --preserve=mode ../.auth ../cursos/aplic/portfolio
cp -r --preserve=mode ../.auth ../cursos/aplic/xajax_0.2.4
cp -r --preserve=mode ../.auth ../estatistica
cp -r --preserve=mode ../.auth ../extracao
cp -r --preserve=mode ../.auth ../imgs
cp -r --preserve=mode ../.auth ../infocurso
cp -r --preserve=mode ../.auth ../pagina_inicial
cp -r --preserve=mode ../.auth ../xajax_0.2.4

#cp ../.auth ../cursos/aplic/extracao

reseta_cor

unset dbuser dbname dbpass db_temp db_temp_user db_temp_pass importacao turno tele_path confirma ret
unset vermelho verde azul cyan nocolor am_bold cy_bold ve_bold

echo "Ajustando permissões dos diretórios."
chmod -R 755 ../
chmod -R 777 ../cursos/diretorio

echo "Finalizado o patch para a versão 4.1."