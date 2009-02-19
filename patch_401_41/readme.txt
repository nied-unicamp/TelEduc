Patch de Atualização do TelEduc v3.3.8 para v4.0.1
===============================================================
Campinas, 19 de Junho de 2006.

================================================================================
OBSERVAÇÕES: 1)  Leia atentamente todo o documento, antes de iniciar a aplicação
                 do patch.

             2)  Se desejar manter cópia dos arquivos anteriores, faça
                 backup dos mesmos antes de instalar o patch(RECOMENDADO).

             3)  Se você realizou alterações no código do ambiente, verifique
                 se os arquivos que serão substituídos não sobrescreverão os
                 que foram modificados.

             4)  A lista de arquivos contidos neste patch pode ser obtida do
                 arquivo 'ListaArquivos.txt'

             5)  Atente para mensagens exibidas durante o processo de aplicação
                 do patch.

================================================================================

  1. [[ ATUALIZAÇÕES ]]


  2. [[ INSTALAÇÃO ]]

    2.1 - Para instalação do Patch, descompacte o arquivo .tar.gz baixado no
        diretório base do teleduc (em geral, /home/teleduc/public_html):
            $ cd /home/teleduc/public_html
            $ tar -xvzf patch-teleduc-v3.3.8-v4.0.1.tar.gz

    2.2 - Execute o script aplicar_patch.sh:
            $ cd patch-v3.3.8-v4.0.1/
            $ ./aplicar_patch.sh
          Siga as instrucoes no patch.

    2.3 - Se após execução do script 'aplicar_patch.sh' for exibida a
        mensagem:

            -bash: ./aplicar_patch.sh: /bin/bash: bad interpreter: No such
              file or directory

        será necessário editar o arquivo 'aplicar_patch.sh' alterando a
        primeira linha do arquivo para o caminho correto de seu shell. Por
        padrão utilizamos o shell 'bash' (Bourne Again SHell). Para listar
        os shell's disponíveis em seu sistema digite o comando:

            $ chsh -list

          A primeira linha do arquivo 'aplicar_patch.sh' contém a seguinte
        estrutura:

            #!CAMINHO_DO_SHELL

          No exemplo padrão: #!/bin/bash

    2.4 - Concluída a aplicação do patch, através de um browser, entre na
        administração do ambiente TelEduc. O mesmo deverá exibir a mensagem
        confirmando o sucesso na instalação do Patch.

    2.5 - Execute o script 'finalizar.sh' no diretorio do patch:

            $ ./finalizar_patch.sh
