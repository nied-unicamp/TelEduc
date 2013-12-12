/*
 * GrafoApplet.java
 * Versão: 2004-08-25
 * Autor: Celmar Guimarães da Silva
-------------------------------------------------------------------------------
 
    TelEduc - Ambiente de Ensino-Aprendizagem a Distância
    Copyright (C) 2001-2004  NIED - Unicamp
 
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
 */

package applet;

import javax.swing.JApplet;
import java.awt.*;
import java.util.*;
import java.io.*;
import javax.swing.JPanel;
import javax.swing.BoxLayout.*;
import javax.swing.*;
import grafo.simples.*;
import grafo.polar.*;
import grafo.forceDirected.*;
import grafo.*;
import applet.*;
import javax.swing.event.ChangeListener;
import java.util.HashMap;

/** Applet que mostra grafos para o Intermap (TelEduc).
 *
 *  Recebe os seguintes parâmetros via HTML:
 *  - codigo_nome: lista no formato "cod1:nome1/cod2:nome2/cod3:nome3/ ... /codN:nomeN",
 *  sendo nomeX o nome de um participante de curso e codX seu respectivo código.
 *  Os valores dos códigos não precisam ser contíguos.
 *
 *  - formador: lista no formato "cod1/cod2/cod3/ ... /codN" contendo a lista dos
 *  códigos dos formadores.
 *
 *  - colaborador: lista no formato "cod1/cod2/cod3/ ... /codN" contendo a lista dos
 *  códigos dos colaboradores. Deve ser definido pelo HTML apenas quando a ferramenta
 *  cujos dados estão sendo visualizados apresentar suporte a colaboradores.
 *
 *  - visitante: lista no formato "cod1/cod2/cod3/ ... /codN" contendo a lista dos
 *  códigos dos visitantes. Deve ser definido pelo HTML apenas quando a ferramenta
 *  cujos dados estão sendo visualizados apresentar suporte a visitantes.
 *
 *  - usuarios_sessao: lista no formato "cod1/cod2/cod3/ ... /codN" contendo a lista dos
 *  códigos dos usuários que participaram da sessão de bate-papo em questão. Deve ser
 *  definido pelo HTML apenas quando os dados pertencerem à ferramenta Bate-papo.
 *
 *  - arestas_com_no_todos e arestas_sem_no_todos: listas no formato
 * "codOrigemA:codDestinoA1,tamA1.codDestinoA2,tamA2.codDestinoA3,tamA3. ...
 *  codDestinoAN,tamAN/codOrigemB:codDestinoB1,tamB1.codDestinoB2,tamB2. ...",
 *  sendo (codOrigemL, codDestinoLX) uma aresta de peso tamAX. A primeira lista
 *  define quais arestas devem ser mostradas quando o nó "todos" estiver sendo
 *  considerado; a segunda, quando ele não estiver sendo considerado.
 *
 *  - textos: lista no formato "texto1/texto2/texto3/ ... /textoN" contendo a lista dos
 *  textos a serem utilizados na ferramenta. Usado para especificar frases diferentes
 *  para usos em outros idiomas. Caso não seja especificado serão utilizadas frases
 *  em português.
 *
 *  - cod_curso: código do curso atual. Necessário para que se possa acessar a
 *  página contendo o perfil de um participante.
 *
 * - mostrar_link_perfil: indica se deve ou não ser mostrado um link para a 
 * ferramenta Perfil quando se pede mais informações sobre um vértice (participante).
 * Se "sim", mostra o link; caso contrário, não mostra. Com isso, o programa 
 * que chama o applet pode primeiro verificar se a ferramenta Perfil está ativada 
 * no curso em questão, para então permitir sua invocação pelo applet.
 *
 */

public class GrafoApplet extends JApplet implements ChangeListener {
    
    private final static String COD_NO_TODOS = "0";
    
    /** Gerenciador de abas.
     */
    private javax.swing.JTabbedPane panelComAbas;
    
    /** Textos a serem utilizados pelo applet, por padrão.
     */
    protected String[] textos = {
        "Grafo Simples", // 0
        "Grafo Polar",
        "Legenda",
        "Perfil", 
        "Visitante",
        "Todos", //5
        "Alunos",
        "Formadores",
        "Colaboradores",
        "Visitantes",
        "Aguarde enquanto o sistema prepara suas representações gráficas.", //10
        "Carregando programas de gerenciamento de grafos...",
        "Criando grafo simples...",
        "Criando grafo polar...",
        "Preparando janelas...",
        "Fim!", //15
        "Mostrar todos os participantes do curso",
        "Mostrar apenas os participantes da sessão",
        "Mostrar apenas os participantes que enviaram ou receberam mensagens",
        "Exibir separadamente mensagens enviadas para todos os participantes",
        "Aluno", //20
        "Formador",
        "Colaborador"
    };
    
    /** Quantidade de textos utilizada especificamente pelo applet.
     */
    private int quantidadeTextosApplet = textos.length;
    
    /** Panel para grafo polar.
     */
    private GrafoPolarPanel panelPolar;
    
    /** Panel para grafo simples (force directed).
     */
    private GrafoForceDirectedPanel panelSimples;
    
    /** Panel para inserir grafo polar.
     */
    private JPanel tabPanelPolar;
    
    /** Panel para inserir grafo simples.
     */
    private JPanel tabPanelSimples;
    
    /** O grafo polar.
     */
    private GrafoPolar grPolar;
    
    /** O grafo simples.
     */
    private GrafoForceDirected grSimples;
    
    /** Controle para que o sistema considere todos os nós do grafo.
     */
    private javax.swing.JRadioButton jRadioButtonTodos;
    
    /** Controle para que o sistema considere apenas os nós da sessão de bate-papo em
     * questão (ativo apenas para dados da ferramenta Bate-papo).
     */
    private javax.swing.JRadioButton jRadioButtonSessao;
    
    /** Controle para que o sistema considere apenas os nós de participantes que
     * enviaram ou receberam mensagens.
     */
    private javax.swing.JRadioButton jRadioButtonMensagem;
    
    /** Agrupador de botões de filtro formato radio button.
     */
    private javax.swing.ButtonGroup buttonGroup1;
    
    /** Controle para que o sistema considere ou não o nó "Todos", que chama para si
     * todas as arestas que representam mensagens enviadas para todas as pessoas.
     */
    private javax.swing.JCheckBox jCheckBoxNoTodos;
    
    /** Arestas do grafo simples para quando o nó "Todos" estiver habilitado.
     */
    private Collection arestasSimplesComNoTodos;
    /** Arestas do grafo simples para quando o nó "Todos" estiver desabilitado.
     */
    private Collection arestasSimplesSemNoTodos;
    /** Arestas do grafo polar para quando o nó "Todos" estiver habilitado.
     */
    private Collection arestasPolaresComNoTodos;
    /** Arestas do grafo polar para quando o nó "Todos" estiver desabilitado.
     */
    private Collection arestasPolaresSemNoTodos;
    
    /** No grafo simples, nós de participantes que participaram da sessão de bate-papo.
     */
    private Collection nosSimplesParticipantesDaSessao;
    /** No grafo simples, nós de participantes que enviaram ou receberam mensagens
     * quando o nó "Todos" está habilitado.
     */
    private Collection nosSimplesParticipantesQueEnviaramOuReceberamMsgsComNoTodos;
    /** No grafo simples, nós de participantes que enviaram ou receberam mensagens
     * quando o nó "Todos" está desabilitado.
     */
    private Collection nosSimplesParticipantesQueEnviaramOuReceberamMsgsSemNoTodos;
    /** No grafo polar, nós de participantes que participaram da sessão de bate-papo.
     */
    private Collection nosPolaresParticipantesDaSessao;
    /** No grafo polar, nós de participantes que enviaram ou receberam mensagens
     * quando o nó "Todos" está habilitado.
     */
    private Collection nosPolaresParticipantesQueEnviaramOuReceberamMsgsComNoTodos;
    /** No grafo polar, nós de participantes que enviaram ou receberam mensagens
     * quando o nó "Todos" está habilitado.
     */
    private Collection nosPolaresParticipantesQueEnviaramOuReceberamMsgsSemNoTodos;
    
    /** Nós simples utilizados.
     */
    private Map nosSimples;
    
    /** Nós polares utilizados.
     */
    private Map nosPolares;
    
    /** Nós simples não considerados para o grafo (ou seja, nós que foram retirados do
     * grafo pelos filtros de radio button).
     */
    private Collection nosSimplesNaoConsiderados;
    
    /** Nós polares não considerados para o grafo (ou seja, nós que foram retirados do
     * grafo pelos filtros de radio button).
     */
    private Collection nosPolaresNaoConsiderados;
    
    /** Cor do nó do formador quando não estiver marcado.
     */
    private Color formadorCorNormal  = new Color(0xA4DDF4);
    /** Cor do nó do formador quando estiver marcado.
     */
    private Color formadorCorMarcado = new Color(0x69D3FF);
    /** Cor do nó do aluno quando não estiver marcado.
     */
    private Color estudanteCorNormal   = new Color(0xFED782);
    /** Cor do nó do aluno quando estiver marcado.
     */
    private Color estudanteCorMarcado  = new Color(0xFFB351);
    /** Cor do nó do colaborador quando não estiver marcado.
     */
    private Color colaboradorCorNormal  = new Color(0xE3BAF4);
    /** Cor do nó do colaborador quando estiver marcado.
     */
    private Color colaboradorCorMarcado = new Color(0xD97DFF);
    /** Cor do nó do visitante quando não estiver marcado.
     */
    private Color visitanteCorNormal  = new Color(0xA7F498);
    /** Cor do nó do visitante quando estiver marcado.
     */
    private Color visitanteCorMarcado = new Color(0x72FF51);
    
    /** Constante a ser usada para indicar o uso do filtro que considera todos os
     * participantes do curso.
     */
    private final int filtroTodosOsParticipantes = 0;
    /** Constante a ser usada para indicar o uso do filtro que considera apenas os
     * participantes de uma sessão de bate-papo.
     */
    private final int filtroParticipantesDaSessao = 1;
    /** Constante a ser usada para indicar o uso do filtro que considera apenas os
     * participantes que enviaram ou receberam mensagens.
     */
    private final int filtroParticipantesQueEnviaramOuReceberamMensagens = 2;
    
    private final int filtroNenhum = -1;
    
    /** Indica qual filtro está ativo atualmente. Pode assumir os valores:
     * - filtroTodosOsParticipantes
     * - filtroParticipantesDaSessao
     * - filtroParticipantesQueEnviaramOuReceberamMensagens
     * - filtroNenhum, se nenhum filtro tiver sido escolhido até o momento.
     */
    private int filtroAtivo = filtroNenhum;
    
    /** Indica se existem parâmetros considerados essenciais para o funcionamento do
     * applet e que não tenham sido definidos.
     */
    private boolean parametrosEssenciaisExistem = false;
    
    /** Construtor de Grafo Applet. (Evita mensagem de erro desnecessaria em Java 1.1.)
     */
    public GrafoApplet() {
        getRootPane().putClientProperty("defeatSystemEventQueueCheck",
        Boolean.TRUE);
    }
    
    /** Inicia o applet, criando duas abas para mostrar o grafo simples (force
     * directed) e o polar.
     */
    public void init() {
        
        Collection listaParametrosFaltantes = retornaListaParametrosEssenciaisFaltantes();
        parametrosEssenciaisExistem = listaParametrosFaltantes.isEmpty();
        
        if (parametrosEssenciaisExistem) {
            
            /* Chamando rotina de Barra de Progresso */
            
            arestasSimplesComNoTodos = new ArrayList();
            arestasSimplesSemNoTodos = new ArrayList();
            arestasPolaresComNoTodos = new ArrayList();
            arestasPolaresSemNoTodos = new ArrayList();
            
            nosSimplesParticipantesQueEnviaramOuReceberamMsgsComNoTodos = new ArrayList();
            nosSimplesParticipantesQueEnviaramOuReceberamMsgsSemNoTodos = new ArrayList();
            nosSimplesParticipantesDaSessao = new ArrayList(); // Caso da ferramenta Bate-Papo
            nosPolaresParticipantesQueEnviaramOuReceberamMsgsComNoTodos = new ArrayList();
            nosPolaresParticipantesQueEnviaramOuReceberamMsgsSemNoTodos = new ArrayList();
            nosPolaresParticipantesDaSessao = new ArrayList(); // Caso da ferramenta Bate-Papo
            
            nosSimplesNaoConsiderados = new ArrayList();
            nosPolaresNaoConsiderados = new ArrayList();
            
            getContentPane().setLayout(new java.awt.FlowLayout());
            obterTextosDoApplet();
            
            JPanel inicial = new JPanel();
            inicial.setLayout(new java.awt.FlowLayout());
            
            PreparandoApplet progresso = new PreparandoApplet();
            progresso.ajustarFraseDeIntroducao(textos[10]);
            progresso.ajustarNomeDaFase(textos[11]);
            progresso.ajustarValor(0);
            progresso.setMaximumSize(new java.awt.Dimension(2147483647, 80));
            progresso.setMinimumSize(new java.awt.Dimension(395, 80));
            progresso.setPreferredSize(new java.awt.Dimension(395, 80));
            
            JPanel superior = new JPanel();
            superior.setMinimumSize(new java.awt.Dimension(32767, 10));
            superior.setPreferredSize(new java.awt.Dimension(32767, 150));
            
            JPanel inferior = new JPanel();
            inferior.setMinimumSize(new java.awt.Dimension(32767, 10));
            inferior.setPreferredSize(new java.awt.Dimension(32767, 150));
            
            inicial.add(superior);
            inicial.add(progresso);
            inicial.add(inferior);
            
            getContentPane().add(inicial);
            
            setVisible(true);
            
            /* Fase 0: Carregando classes */
            progresso.ajustarNomeDaFase(textos[11]);
            carregarClasses(progresso);
            // Termina com barra de progresso em 72.
            
            /* Fase 1: Criando grafo simples (force-directed) */
            
            progresso.ajustarNomeDaFase(textos[12]);
            grSimples = new GrafoForceDirected();
            grSimples.considerarPesosDasArestas();
            grSimples.desconsiderarPesosDosNos();
            progresso.ajustarValor(74);
            
            panelSimples = new grafo.forceDirected.GrafoForceDirectedPanel(grSimples);
            progresso.ajustarValor(76);
            
            /* Fase 2: Criando grafo polar */
            progresso.ajustarNomeDaFase(textos[13]);
            grPolar = new GrafoPolar();
            grPolar.considerarPesosDasArestas();
            grPolar.desconsiderarPesosDosNos();
            progresso.ajustarValor(78);
            
            panelPolar = new GrafoPolarPanel(grPolar);
            grPolar.ajustarRaioCentral(panelPolar.getHeight()/6);
            grPolar.ajustarRaioPeriferico(panelPolar.getHeight()/2-10);
            grPolar.ajustarRaioAreaProibida(panelPolar.getHeight()/3);
            progresso.ajustarValor(80);
            
            
            //getContentPane().add(panelPolar, BorderLayout.CENTER);
            
            /* Fase 3: Preparando janelas */
            progresso.ajustarNomeDaFase(textos[14]);
            modificarTextos(panelPolar, panelSimples);

            
            // Preparando panel da aba do grafo simples (force-directed)
            
            tabPanelSimples = new JPanel();
            tabPanelSimples.setLayout(new javax.swing.BoxLayout(tabPanelSimples, javax.swing.BoxLayout.Y_AXIS));
            tabPanelSimples.add(panelSimples);
            progresso.ajustarValor(90);
            
            // Preparando panel da aba do grafo polar
            
            tabPanelPolar = new JPanel();
            tabPanelPolar.setLayout(new javax.swing.BoxLayout(tabPanelPolar, javax.swing.BoxLayout.Y_AXIS));
            tabPanelPolar.add(panelPolar);
            
            panelComAbas = new javax.swing.JTabbedPane();
            // Texto 0: Grafo Polar
            panelComAbas.addTab(textos[0], tabPanelSimples);
            // Texto 1: Grafo Simples
            panelComAbas.addTab(textos[1], tabPanelPolar);

            panelComAbas.addChangeListener(this);
            
            buttonGroup1 = new ButtonGroup();
            JPanel controlesSuperiores = new JPanel();
            controlesSuperiores.setBackground(new Color(240,240,240));
            
            controlesSuperiores.setLayout(new javax.swing.BoxLayout(controlesSuperiores, javax.swing.BoxLayout.X_AXIS));
            controlesSuperiores.setAlignmentX(Box.BOTTOM_ALIGNMENT);
            
            JPanel controlesSuperioresEsquerda = new JPanel();
            controlesSuperioresEsquerda.setLayout(new javax.swing.BoxLayout(controlesSuperioresEsquerda, javax.swing.BoxLayout.Y_AXIS));
            controlesSuperioresEsquerda.setAlignmentX(Box.LEFT_ALIGNMENT);
            controlesSuperioresEsquerda.setBackground(new Color(240,240,240));
            
            jRadioButtonTodos = new JRadioButton();
            jRadioButtonTodos.setFont(new java.awt.Font("Dialog", 0, 12));
            jRadioButtonTodos.setSelected(true);
            // Texto 16: Mostrar todos os participantes do curso
            jRadioButtonTodos.setText(textos[16]);
            jRadioButtonTodos.setBackground(new Color(240,240,240));
            buttonGroup1.add(jRadioButtonTodos);
            jRadioButtonTodos.addActionListener(new java.awt.event.ActionListener() {
                public void actionPerformed(java.awt.event.ActionEvent evt) {
                    //jRadioButtonTodosActionPerformed(evt);
                    considerarTodosOsParticipantesDoCurso(true);
                }
            });
            controlesSuperioresEsquerda.add(jRadioButtonTodos);
            
            progresso.ajustarValor(94);
            
            // O radiobutton de sessão somente deve aparecer quando o parâmetro
            // usuarios_sessao estiver definido, caso que acontece apenas na
            // ferramenta Bate-papo.
            if (getParameter("usuarios_sessao")!=null) {
                jRadioButtonSessao = new JRadioButton();
                jRadioButtonSessao.setFont(new java.awt.Font("Dialog", 0, 12));
                jRadioButtonSessao.setSelected(true);
                // Texto 17: Mostrar apenas os participantes da sessão
                jRadioButtonSessao.setText(textos[17]);
                jRadioButtonSessao.setBackground(new Color(240,240,240));
                buttonGroup1.add(jRadioButtonSessao);
                jRadioButtonSessao.addActionListener(new java.awt.event.ActionListener() {
                    public void actionPerformed(java.awt.event.ActionEvent evt) {
                        considerarApenasOsParticipantesDaSessao(true);
                    }
                });
                controlesSuperioresEsquerda.add(jRadioButtonSessao);
            }
            
            jRadioButtonMensagem = new JRadioButton();
            jRadioButtonMensagem.setFont(new java.awt.Font("Dialog", 0, 12));
            jRadioButtonMensagem.setSelected(true);
            // Texto 18: Mostrar apenas os participantes que enviaram ou receberam mensagens
            jRadioButtonMensagem.setText(textos[18]);
            jRadioButtonMensagem.setBackground(new Color(240,240,240));
            buttonGroup1.add(jRadioButtonMensagem);
            jRadioButtonMensagem.addActionListener(new java.awt.event.ActionListener() {
                public void actionPerformed(java.awt.event.ActionEvent evt) {
                    //RadioButtonSessaoActionPerformed(evt);
                    considerarApenasOsParticipantesComMensagensRelacionadas(true);
                }
            });
            controlesSuperioresEsquerda.add(jRadioButtonMensagem);
            
            
            controlesSuperiores.add(controlesSuperioresEsquerda);
            
            
            progresso.ajustarValor(97);
            
            // Texto 19: Exibir separadamente mensagens enviadas para todos os participantes
            jCheckBoxNoTodos = new JCheckBox(textos[19]);
            jCheckBoxNoTodos.setFont(new java.awt.Font("Dialog", 0, 12));
            jCheckBoxNoTodos.setBackground(new Color(240,240,240));

            jCheckBoxNoTodos.addActionListener(new java.awt.event.ActionListener() {
                public void actionPerformed(java.awt.event.ActionEvent evt) {
                    if (jCheckBoxNoTodos.isSelected()) {
                        usarNoTodos();
                    } else {
                        naoUsarNoTodos();
                    }
                }
            });
            controlesSuperiores.add(jCheckBoxNoTodos);
            
            
            JPanel legenda = new JPanel();
            legenda.setBackground(new Color(240,240,240));
            // Texto 2: Legenda
            legenda.add(new JLabel(textos[2]+":  "));
            
            JPanel quadradoFormador = new JPanel();
            quadradoFormador.setMaximumSize(new java.awt.Dimension(10, 10));
            quadradoFormador.setBackground(formadorCorNormal);
            quadradoFormador.setBorder(new javax.swing.border.LineBorder(new java.awt.Color(0, 0, 0)));
            // Texto 21: Formador
            JLabel labelFormador = new JLabel("- "+textos[21]+"   ");
            labelFormador.setFont(new java.awt.Font("Dialog", 0, 12));
            legenda.add(quadradoFormador);
            legenda.add(labelFormador);
            
            JPanel quadradoEstudante = new JPanel();
            quadradoEstudante.setMaximumSize(new java.awt.Dimension(10, 10));
            quadradoEstudante.setBackground(estudanteCorNormal);
            quadradoEstudante.setBorder(new javax.swing.border.LineBorder(new java.awt.Color(0, 0, 0)));
            // Texto 20: Aluno
            JLabel labelEstudante = new JLabel("- "+textos[20]+"   ");
            labelEstudante.setFont(new java.awt.Font("Dialog", 0, 12));
            legenda.add(quadradoEstudante);
            legenda.add(labelEstudante);
            
            if (getParameter("colaborador")!=null) {
                JPanel quadradoColaborador = new JPanel();
                quadradoColaborador.setMaximumSize(new java.awt.Dimension(10, 10));
                quadradoColaborador.setBackground(colaboradorCorNormal);
                quadradoColaborador.setBorder(new javax.swing.border.LineBorder(new java.awt.Color(0, 0, 0)));
                // Texto 22: Colaborador
                JLabel labelColaborador = new JLabel("- "+textos[22]+"   ");
                labelColaborador.setFont(new java.awt.Font("Dialog", 0, 12));
                legenda.add(quadradoColaborador);
                legenda.add(labelColaborador);
            }
            
            if (getParameter("visitante")!=null) {
                JPanel quadradoVisitante = new JPanel();
                quadradoVisitante.setMaximumSize(new java.awt.Dimension(10, 10));
                quadradoVisitante.setBackground(visitanteCorNormal);
                quadradoVisitante.setBorder(new javax.swing.border.LineBorder(new java.awt.Color(0, 0, 0)));
                // Texto 4: Visitante
                JLabel labelVisitante = new JLabel("- "+textos[4]+"   ");
                labelVisitante.setFont(new java.awt.Font("Dialog", 0, 12));
                legenda.add(quadradoVisitante);
                legenda.add(labelVisitante);
            }
            
            progresso.ajustarNomeDaFase(textos[15]);
            progresso.ajustarValor(100);
            
            /* Fase 4: Fim! */
            
            getContentPane().remove(inicial);

            getContentPane().setLayout(new java.awt.BorderLayout());
            
            getContentPane().add(panelComAbas, java.awt.BorderLayout.CENTER);
            getContentPane().add(controlesSuperiores, java.awt.BorderLayout.NORTH);
            getContentPane().add(legenda, java.awt.BorderLayout.SOUTH);
            
            grSimples.ajustarDimensaoDaTela(panelSimples.getSize());
            getContentPane().setVisible(false);
            
            grSimples.desativarAutoAjuste();
            preencherGrafos(grSimples, grPolar);
            
            if (nosSimples.get(COD_NO_TODOS)==null || nosPolares.get(COD_NO_TODOS)==null) {
                jCheckBoxNoTodos.setVisible(false);
            }

        } else {
            
            // Parametros Essenciais não existem
            ArrayList msg = new ArrayList();
            msg.add("ERRO:  O código HTML não está informando os seguintes parâmetros essenciais:");
            //msg.add("ERROR: The HTML code is not informing the following essential parameters:");
            
            for (Iterator i= listaParametrosFaltantes.iterator(); i.hasNext(); ) {
                msg.add("- "+(String)i.next());
            }
            msg.add("");
            msg.add("O aplicativo não pôde ser iniciado.");
            //msg.add("The application could not be started.");
            getContentPane().setLayout(new javax.swing.BoxLayout(getContentPane(), javax.swing.BoxLayout.Y_AXIS));
            
            String linha;
            String texto = "";
            for (Iterator i = msg.iterator(); i.hasNext();) {
                linha = (String)i.next();
                texto += linha + "\n";
                System.out.println(linha);
            }
            
            JTextArea textArea = new JTextArea(texto);
            textArea.setEditable(false);
            getContentPane().add(textArea);
            
        }
    }
    
    /** Preenche o grafo simples e o polar com os parâmetros obtidos junto ao
     * HTML que invocou o applet.
     * @param grSimples Grafo simples.
     * @param grPolar Grafo polar.
     */
    private void preencherGrafos(GrafoForceDirected grSimples, GrafoPolar grPolar) {
        
        // Criando grupos
        
        int maxGr=2;
        Grupo[] estudantes    = new Grupo[maxGr];
        Grupo[] formadores    = new Grupo[maxGr];
        Grupo[] colaboradores = new Grupo[maxGr];
        Grupo[] visitantes    = new Grupo[maxGr];
        Grupo[] todos         = new Grupo[maxGr];
        
        for (int i=0; i<maxGr; i++) {
            Grafo gr;
            if (i==0) { gr = grSimples; } else { gr = grPolar; }
            // Texto 6: Estudantes
            estudantes[i] = new Grupo(textos[6], new CorNo(gr.defaultCorNo));
            // Texto 7: Formadores
            formadores[i] = new Grupo(textos[7], new CorNo(gr.defaultCorNo));
            // Texto 8: Colaboradores
            colaboradores[i] = new Grupo(textos[8], new CorNo(gr.defaultCorNo));
            // Texto 9: Visitantes
            visitantes[i] = new Grupo(textos[9], new CorNo(gr.defaultCorNo));
            // Texto 5: Todos
            todos[i] = new Grupo(textos[5], new CorNo(gr.defaultCorNo));
            definirCoresDosGrupos(estudantes[i], formadores[i], colaboradores[i], visitantes[i], todos[i]);
            gr.adicionarGrupo(estudantes[i]);
            gr.adicionarGrupo(formadores[i]);
            gr.adicionarGrupo(colaboradores[i]);
            gr.adicionarGrupo(visitantes[i]);
            gr.adicionarGrupo(todos[i]);
        }
        
        // Ler parametros entrados
        
        // - Obtem lista com nomes de participantes e respectivos códigos.
        // - Cria nos para cada participante e move-os inicialmente para o grupo
        // estudantes (alguns serão mudados de grupo posteriormente).
        String lista_codigo_nome = getParameter("codigo_nome");
        
        Map nomes = new HashMap();
        nosSimples = new HashMap();
        nosPolares = new HashMap();
        
        // Preenchendo lista de codigos e nomes de participantes
        
        String str, nome, link;
        LinkInfo info;
        StringTokenizer tkCodNome;
        String codigo;
        NoSimples noSimples;
        NoPolar noPolar;
        for (StringTokenizer tkNomes = new StringTokenizer(lista_codigo_nome, "/") ; tkNomes.hasMoreTokens() ; ) {
            str = tkNomes.nextToken();
            tkCodNome = new StringTokenizer(str, ":");
            codigo=tkCodNome.nextToken();
            nome=tkCodNome.nextToken();
            nomes.put(codigo, nome);

            noSimples = new NoSimples(nome);
            noSimples.ajustarId(codigo);
            nosSimples.put(codigo, noSimples);
            noSimples.moverParaGrupo(estudantes[0]);
            
            noPolar = new NoPolar(nome, grPolar.ANEL_PERIFERICO);
            noPolar.ajustarId(codigo);            
            nosPolares.put(codigo, noPolar);
            noPolar.moverParaGrupo(estudantes[1]);

            if (getParameter("mostrar_link_perfil").equals("sim") && !codigo.equals(COD_NO_TODOS)) {
                // Texto 3: Perfil
                info = new LinkInfo(textos[3], getCodeBase()+"../exibir_perfil.php?&cod_curso="+getParameter("cod_curso")+"&cod_usuario_perfil="+codigo, this, "invisivel");
                noSimples.ajustarInfo(info);
                noPolar.ajustarInfo(info);
            }
            
        }
        
        nosSimplesParticipantesDaSessao.clear();
        nosPolaresParticipantesDaSessao.clear();

        // Preenchendo lista de participantes da sessao
        String lista_usuarios_sessao= getParameter("usuarios_sessao");
        if (lista_usuarios_sessao!=null) {
            StringTokenizer tkUsuariosSessao=new StringTokenizer(lista_usuarios_sessao, "/");
            boolean tkUsuariosSessaoNaoVazio = (tkUsuariosSessao.countTokens()>0);
            while (tkUsuariosSessao.hasMoreTokens()) {
                codigo=tkUsuariosSessao.nextToken();
                noSimples = (NoSimples)nosSimples.get(codigo);
                nosSimplesParticipantesDaSessao.add(noSimples);
                noPolar = (NoPolar)nosPolares.get(codigo);
                nosPolaresParticipantesDaSessao.add(noPolar);
            }
        }
        
        // - Adiciona aos grafos TODOS os nós criados
        for (Iterator i=nosSimples.values().iterator(); i.hasNext(); ) {
            noSimples = (NoSimples)i.next();
            grSimples.adicionarNo(noSimples);
        }
        for (Iterator i=nosPolares.values().iterator(); i.hasNext(); ) {
            noPolar = (NoPolar)i.next();
            grPolar.adicionarNo(noPolar);
        }
        
        // - Lê os dados referentes a arestas, criando-as e adicionando-as aos
        // grafos.
        
        for (int i=0; i<2; i++) { // duas iteraçoes, uma para arestas com no todos, outra para arestas sem no todos
            String edges;
            if (i==0) {
                edges = getParameter("arestas_com_no_todos");
            } else {
                edges = getParameter("arestas_sem_no_todos");
            }
            if (edges!=null) {
                
                for (StringTokenizer tkEdges = new StringTokenizer(edges, "/") ; tkEdges.hasMoreTokens() ; ) {
                    str = tkEdges.nextToken();
                    // str contem uma lista do tipo: cod_origem:cod_destino,tam.cod_destino,tam....
                    
                    StringTokenizer tkEdgesCods = new StringTokenizer(str, ":");
                    String cod_origem = tkEdgesCods.nextToken();
                    String cod_destino_tam=tkEdgesCods.nextToken();
                    
                    if (i==0) {
                        nosSimplesParticipantesQueEnviaramOuReceberamMsgsComNoTodos.add((NoSimples)nosSimples.get(cod_origem));
                        nosPolaresParticipantesQueEnviaramOuReceberamMsgsComNoTodos.add((NoPolar)nosPolares.get(cod_origem));                        
                    } else {
                        nosSimplesParticipantesQueEnviaramOuReceberamMsgsSemNoTodos.add((NoSimples)nosSimples.get(cod_origem));
                        nosPolaresParticipantesQueEnviaramOuReceberamMsgsSemNoTodos.add((NoPolar)nosPolares.get(cod_origem));                        
                    }
                    
                    // cod_origem contem agora o código do usuario de origem da aresta
                    // cod_destino_tam tem uma lista do tipo cod_destino,tam.cod_destino,tam...
                    
                    for (StringTokenizer tkEdgesDestino = new StringTokenizer(cod_destino_tam, ".") ; tkEdgesDestino.hasMoreTokens() ; ) {
                        String str1 = tkEdgesDestino.nextToken();
                        StringTokenizer tkEdgesCodsDestino = new StringTokenizer(str1, ",");
                        String cod_destino=tkEdgesCodsDestino.nextToken();
                        int valor=Integer.valueOf(tkEdgesCodsDestino.nextToken()).intValue();
                        
                        if (i==0) {
                            nosSimplesParticipantesQueEnviaramOuReceberamMsgsComNoTodos.add((NoSimples)nosSimples.get(cod_destino));
                            nosPolaresParticipantesQueEnviaramOuReceberamMsgsComNoTodos.add((NoPolar)nosPolares.get(cod_destino));                        
                        } else {
                            nosSimplesParticipantesQueEnviaramOuReceberamMsgsSemNoTodos.add((NoSimples)nosSimples.get(cod_destino));
                            nosPolaresParticipantesQueEnviaramOuReceberamMsgsSemNoTodos.add((NoPolar)nosPolares.get(cod_destino));                        
                        }
                        
                        // Verifica se a aresta já existe no grafo.
                        // Se existe, soma a quantidade indicada à que ela já apresenta.
                        // Se não existe, cria-a.
                        ArestaForceDirected arestaSimples;
                        ArestaPolar arestaPolar;
                        
                        arestaSimples = new ArestaForceDirected((NoSimples)nosSimples.get(cod_origem), (NoSimples)nosSimples.get(cod_destino), valor);
                        arestaPolar = new ArestaPolar((NoPolar)nosPolares.get(cod_origem), (NoPolar)nosPolares.get(cod_destino), valor);
                        if (i==0) {
                            adicionaArestaOuSomaValor(arestasSimplesComNoTodos,arestaSimples);
                            adicionaArestaOuSomaValor(arestasPolaresComNoTodos,arestaPolar);
                        } else {
                            adicionaArestaOuSomaValor(arestasSimplesSemNoTodos,arestaSimples);
                            adicionaArestaOuSomaValor(arestasPolaresSemNoTodos,arestaPolar);
                        }

                    }
                }
            }
            
        }
        
        moverParaGrupo(getParameter("formador"),formadores);
        moverParaGrupo(getParameter("colaborador"),colaboradores);
        moverParaGrupo(getParameter("visitante"),visitantes);
        moverParaGrupo(COD_NO_TODOS, todos);
    }

    
    private void moverParaGrupo(String codigos, Grupo[] grupo) {
        if (codigos != null) {
            NoSimples noSimples;
            NoPolar noPolar;
            String cod_no;
            for (StringTokenizer tkCodigos = new StringTokenizer(codigos, "/"); tkCodigos.hasMoreTokens(); ) {
                cod_no = tkCodigos.nextToken();
                noSimples = (NoSimples)nosSimples.get(cod_no);
                if (noSimples!=null) {
                    noSimples.moverParaGrupo(grupo[0]);
                }
                noPolar = (NoPolar)nosPolares.get(cod_no);
                if (noPolar!=null) {
                    noPolar.moverParaGrupo(grupo[1]);
                }
            }
        }
    }
    
    /** Tenta adicionar uma aresta A a uma lista de arestas L informada. Caso essa
     * aresta já exista em L, o peso de A é somado a ela, e A não é adicionada a L.
     * Vale notar que, como estamos manipulando grafos não orientados, uma aresta (x,y)
     * é considerada igual a uma aresta (y,x).
     * @param listaArestas Lista de arestas na qual a aresta deve tentar ser adicionada.
     * @param aresta Aresta a ser adicionada.
     * @param ePolar True se aresta é polar, false caso contrário.
     */
    private void adicionaArestaOuSomaValor(Collection listaArestas, Aresta aresta) {
        Iterator i = listaArestas.iterator();
        Aresta arestaEmAnalise = null;
        boolean achei = false;
        while (i.hasNext() && !achei) {
            arestaEmAnalise = (Aresta)i.next();
            if ((arestaEmAnalise.no1 == aresta.no1 && arestaEmAnalise.no2 == aresta.no2) ||
            (arestaEmAnalise.no1 == aresta.no2 && arestaEmAnalise.no2 == aresta.no1)) {
                achei=true;
            }
        }
        if (achei) {
            // Somo o valor da aresta informada ao da aresta encontrada e nao adiciono a
            // aresta informada à lista de arestas
            
            arestaEmAnalise.peso += aresta.peso;
        } else {
            // Adiciono a aresta informada à lista de arestas.
            listaArestas.add(aresta);
        }
        
    }
    
    /** Modifica os textos dos grafos, de acordo com o parâmetro
     * "textos" lido do HTML.
     * @param panelPolar Panel do grafo polar.
     * @param panelSimples Panel do grafo simples.
     */
    public void modificarTextos(GrafoPolarPanel panelPolar, GrafoForceDirectedPanel panelSimples) {
        boolean ok = false;
        // Ajustar textos.
        String texts = getParameter("textos");
        java.util.List textosParaPanels = new ArrayList();
        if (texts != null && texts !="") {
            StringTokenizer tkTexts = new StringTokenizer(texts, "/");
            int tamNovo = tkTexts.countTokens()-quantidadeTextosApplet;
            if (tamNovo>0) {
                
                for (int i = 0 ; tkTexts.hasMoreTokens() ; i++) {
                    if (i>=quantidadeTextosApplet) {
                        // texto é de panelPolar e de panelSimples
                        textosParaPanels.add(tkTexts.nextToken());
                    } else {
                        tkTexts.nextToken();
                    }
                    
                }
                
                // O panelSimples recebe todas as frases. O panelPolar não 
                // precisa das 3 primeiras, que são do botao de auto-ajuste do
                // panelSimples.
                panelSimples.modificarTextos(textosParaPanels);
                panelPolar.modificarTextos(textosParaPanels.subList(3,textosParaPanels.size()));
                
                ok = true;
            }
            
        }
        if (!ok) {
            System.out.println("ERRO: Não foi possível modificar os textos usados pelos grafos.");
            //System.out.println("ERROR: The system couldn't modify the texts used by graphs.");
        }
    }
    
    /** Modifica os textos do applet, de acordo com o parâmetro
     * "textos" lido do HTML.
     */
    public void obterTextosDoApplet() {
        
        String texts = getParameter("textos");
        if (texts != null) {
            StringTokenizer tkTexts = new StringTokenizer(texts, "/");
            for (int i = 0 ; tkTexts.hasMoreTokens() && i<quantidadeTextosApplet; i++) {
                textos[i] = tkTexts.nextToken();
            }
        }
        
    }
    
    /** Define as cores dos grupos de participantes da seguinte forma:
     * estudantes: azul;
     * formadores: laranja;
     * colaboradores: roxo;
     * visitantes: verde;
     * todos: rosa claro.
     * @param estudantes Grupo contendo nós que representam estudantes.
     * @param formadores Grupo contendo nós que representam formadores.
     * @param colaboradores Grupo contendo nós que representam colaboradores.
     * @param visitantes Grupo contendo nós que representam visitantes.
     * @param todos Grupo contendo o nó "Todos".
     */
    private void definirCoresDosGrupos(Grupo estudantes, Grupo formadores, Grupo colaboradores, Grupo visitantes, Grupo todos)  {
        Color fonteVermelha = new Color(0xDA1C1C);
        
        estudantes.cor.corDeFundoMarcado = estudanteCorMarcado; //new Color(0x69D3FF);
        estudantes.cor.corDeFundoNormal  = estudanteCorNormal; //new Color(0xA4DDF4);
        estudantes.cor.corMarcado = fonteVermelha;
        
        formadores.cor.corDeFundoMarcado = formadorCorMarcado; //new Color(0xFFB351);
        formadores.cor.corDeFundoNormal  = formadorCorNormal; //new Color(0xFED782);
        formadores.cor.corMarcado = fonteVermelha;
        
        colaboradores.cor.corDeFundoMarcado = colaboradorCorMarcado; //new Color(0xD97DFF);
        colaboradores.cor.corDeFundoNormal = colaboradorCorNormal; //new Color(0xE3BAF4);
        colaboradores.cor.corMarcado = fonteVermelha;
        
        visitantes.cor.corDeFundoMarcado = visitanteCorMarcado; //new Color(0x72FF51);
        visitantes.cor.corDeFundoNormal = visitanteCorNormal; //new Color(0xA7F498);
        visitantes.cor.corMarcado = fonteVermelha;
        
        todos.cor.corDeFundoMarcado = new Color(0xFFA5A3);
        todos.cor.corDeFundoNormal = new Color(0xFFC2C1);
        todos.cor.corMarcado = fonteVermelha;
    }
    
    /** Considera nos grafos polar e simples todos os nós dos participantes do curso e
     * suas respectivas arestas.
     */
    private void considerarTodosOsParticipantesDoCurso(boolean registrarModificacao) {
        grSimples.adicionarNos(nosSimplesNaoConsiderados, false);
        grPolar.adicionarNos(nosPolaresNaoConsiderados, false);
        nosSimplesNaoConsiderados.clear();
        nosPolaresNaoConsiderados.clear();
        // Força mostrar nós escondidos
        grSimples.mostrarTodosOsNos(registrarModificacao);
        grPolar.mostrarTodosOsNos(registrarModificacao);
        redesenharPanelAtivo();
        filtroAtivo = filtroTodosOsParticipantes;
    }
    
    /** Considera nos grafos polar e simples apenas os nós referentes à sessão de
     * bate-papo, removendo os demais.
     * Nenhuma aresta é removida, pois os nós removidos não possuem arestas. (Se
     * possuíssem, teriam participado da sessão, e logo não seriam removidos.)
     */
    private void considerarApenasOsParticipantesDaSessao(boolean registrarModificacao) {
        aplicarFiltro(nosSimplesParticipantesDaSessao, nosPolaresParticipantesDaSessao, registrarModificacao);
        
        // Força mostrar nós escondidos
        grSimples.mostrarTodosOsNos(registrarModificacao);
        grPolar.mostrarTodosOsNos(registrarModificacao);
        filtroAtivo = filtroParticipantesDaSessao;
    }
    
    /** Considera nos grafos polar e simples apenas os nós de participantes que enviaram
     * ou receberam mensagens, removendo os demais.
     * Nenhuma aresta é removida, pois os nós removidos não possuem arestas. (Se
     * possuíssem, teriam enviado ou recebido mensagens, e logo não seriam removidos.)
     */
    private void considerarApenasOsParticipantesComMensagensRelacionadas(boolean registrarModificacao) {
        if (jCheckBoxNoTodos.isSelected()) {
            aplicarFiltro(nosSimplesParticipantesQueEnviaramOuReceberamMsgsComNoTodos, nosPolaresParticipantesQueEnviaramOuReceberamMsgsComNoTodos, registrarModificacao);
        } else {
            aplicarFiltro(nosSimplesParticipantesQueEnviaramOuReceberamMsgsSemNoTodos, nosPolaresParticipantesQueEnviaramOuReceberamMsgsSemNoTodos, registrarModificacao);
        }
        
        // Força mostrar nós escondidos
        grSimples.mostrarTodosOsNos(registrarModificacao);
        grPolar.mostrarTodosOsNos(registrarModificacao);
        filtroAtivo = filtroParticipantesQueEnviaramOuReceberamMensagens;
    }

    private void aplicarFiltro(Collection nosSimplesConsiderados, Collection nosPolaresConsiderados, boolean registrarModificacao) {
        aplicarFiltroNos(grSimples, nosSimplesConsiderados, nosSimplesNaoConsiderados, registrarModificacao);
        aplicarFiltroNos(grPolar, nosPolaresConsiderados, nosPolaresNaoConsiderados, registrarModificacao);
        redesenharPanelAtivo();
    }
    
    private void aplicarFiltroNos(Grafo gr, Collection nosConsiderados, Collection nosNaoConsiderados, boolean registrarModificacao) {
        Collection aMoverParaConsiderados = new ArrayList();
        Collection aMoverParaNaoConsiderados = new ArrayList();
        
        No n;
        for (Iterator i = gr.nos.iterator(); i.hasNext();) {
            n = (No)i.next();
            if (!nosConsiderados.contains(n)) {
                aMoverParaNaoConsiderados.add(n);
            }
        }
        for (Iterator i = nosNaoConsiderados.iterator(); i.hasNext(); ) {
            n = (No)i.next();
            if (nosConsiderados.contains(n)) {
                aMoverParaConsiderados.add(n);
            }
        }
        // Movendo nós entre conjuntos
        gr.adicionarNos(aMoverParaConsiderados, false);
        nosNaoConsiderados.removeAll(aMoverParaConsiderados);
        gr.mostrarNos(aMoverParaConsiderados, false); 
        gr.removerNos(aMoverParaNaoConsiderados, registrarModificacao);
        nosNaoConsiderados.addAll(aMoverParaNaoConsiderados);
    }
    
    /** Adiciona aos grafos polar e simples o nó "Todos", trocando o conjunto de arestas.
     */
    private void usarNoTodos() {
        reaplicarFiltroParticipantes(false);
        
        modificarGrafoParaUsarNoTodos(grSimples, nosSimples, arestasSimplesComNoTodos);
        modificarGrafoParaUsarNoTodos(grPolar, nosPolares, arestasPolaresComNoTodos);

        redesenharPanelAtivo();
    }
    
    private void modificarGrafoParaUsarNoTodos(Grafo gr, Map mapaDeNos, Collection arestasComNoTodos) {
        No noTodos = (No)mapaDeNos.get(COD_NO_TODOS);
        if (noTodos!=null) {
            gr.removerTodasAsArestas(false);
            if (!gr.nos.contains(noTodos)) {
                gr.adicionarNo(noTodos, false);
            }
            noTodos.mostrar();
            Aresta a;
            for (Iterator i = arestasComNoTodos.iterator(); i.hasNext();) {
                a = (Aresta)i.next();
                a.mostrar();
            }
            gr.adicionarArestas(arestasComNoTodos, true);
        }
    }
    
    /** Remove dos grafos polar e simples o nó "Todos", trocando o conjunto de arestas.
     */
    private void naoUsarNoTodos() {
        reaplicarFiltroParticipantes(false);
        
        modificandoGrafoParaNaoUsarNoTodos(grSimples, nosSimples, arestasSimplesSemNoTodos, nosSimplesNaoConsiderados);
        modificandoGrafoParaNaoUsarNoTodos(grPolar, nosPolares, arestasPolaresSemNoTodos, nosPolaresNaoConsiderados);
        
        redesenharPanelAtivo();
        
    }

    private void modificandoGrafoParaNaoUsarNoTodos(Grafo gr, Map mapaDeNos, Collection arestasSemNoTodos, Collection nosNaoConsiderados) {
        No noTodos = (No)mapaDeNos.get(COD_NO_TODOS);
        if (noTodos !=null) {
            if (noTodos.eMarcado()) {
                noTodos.desmarcar();
            }
            if (noTodos.eEscolhido()) {
                noTodos.desfazerEscolher();
            }
        }
        gr.removerTodasAsArestas(false);
        if (noTodos!=null) {
            if (gr.nos.contains(noTodos)) {
                gr.removerNo(noTodos, false);
            }
            if (nosNaoConsiderados.contains(noTodos)) {
                nosNaoConsiderados.remove(noTodos);
            }
        }        
        Aresta a;
        for (Iterator i = arestasSemNoTodos.iterator(); i.hasNext(); ) {
            a = (Aresta)i.next();
            a.mostrar();
        }
        gr.adicionarArestas(arestasSemNoTodos, true);    
    }
    
    /** Verifica qual o filtro atualmente ativo e o reaplica ao conjunto de
     * vértices dos grafos.
     */
    private void reaplicarFiltroParticipantes(boolean registrarModificacao) {
        switch (filtroAtivo) {
            case filtroTodosOsParticipantes:
                considerarTodosOsParticipantesDoCurso(registrarModificacao);
                break;
            case filtroParticipantesDaSessao:
                considerarApenasOsParticipantesDaSessao(registrarModificacao);
                break;
            case filtroParticipantesQueEnviaramOuReceberamMensagens:
                considerarApenasOsParticipantesComMensagensRelacionadas(registrarModificacao);
                break;
        }
    }
    
    /** Efetua um acesso a cada tipo de classe a ser usada pelo programa.
     * @param progresso Objeto PreparandoApplet que exibe uma barra de progresso
     * sobre a preparação do applet.
     */
    private void carregarClasses(PreparandoApplet progresso) {
        String[] lista = {
            "grafo.Aresta",
            "grafo.CorAresta",
            "grafo.CorNo",
            "grafo.Grafo",
            "grafo.GrafoDisplay",
            "grafo.GrafoPanel",
            "grafo.Grupo",
            "grafo.Info",
            "grafo.No",
            "grafo.LinkInfo",
            "grafo.Util",
            "grafo.simples.ArestaSimples",
            "grafo.simples.GrafoSimples",
            "grafo.simples.GrafoSimplesDisplay",
            "grafo.simples.GrafoSimplesPanel",
            "grafo.simples.NoSimples",
            "grafo.forceDirected.AlgoritmoMDS",
            "grafo.forceDirected.AlgoritmoMDSAdaptado",
            //"grafo.forceDirected.AlgoritmoMDSChalmers", // Nao é usado.
            "grafo.forceDirected.ArestaForceDirected",
            "grafo.forceDirected.GrafoForceDirected",
            "grafo.forceDirected.GrafoForceDirectedDisplay",
            "grafo.forceDirected.GrafoForceDirectedPanel",
            "grafo.forceDirected.PontoDuplo",
            "grafo.polar.ObservableAngle",
            "grafo.polar.ObservableBoolean",
            "grafo.polar.ObservableBooleanXY",
            "grafo.polar.ObservableInt",
            "grafo.polar.PolarCartesiano",
            "grafo.polar.Aneis",
            "grafo.polar.ChaveDeAresta",
            "grafo.polar.ArestaPolar",
            "grafo.polar.NoPolar",
            "grafo.polar.Barra",
            "grafo.polar.GrafoPolar",
            "grafo.polar.GrafoPolarDisplay",
            "grafo.polar.GrafoPolarPanel",
            "grafo.polar.SeletorDuploDeAngulos"
        };
        //DebugMessenger.showMessage("Carregando classes ... / Downloading classes ...");
        for (int i = 0 ; i<lista.length; i++) {
            carregarClasse(lista[i]);
            progresso.ajustarValor(progresso.obterValor()+2);
        }
        //DebugMessenger.showMessage("Fim do carregamento de classes. / End of class download.");
    }
    
    /** Faz um acesso a uma classe especificada, forçando seu carregamento.
     * @param classe Classe a ser carregada.
     * @return True se o carregamento foi bem sucedido, false caso contrário.
     */
    public static boolean carregarClasse(String classe) {
        boolean ok = false;
        try {
            Class.forName(classe);
            //DebugMessenger.showMessage( "Classe carregada / Class downloaded  : '"+classe+"'");
            ok = true;
        } catch (Exception e) {
            //DebugMessenger.showMessage( "Exceção / Exception :" + e.getMessage());
            throw new NullPointerException("O aplicativo não conseguiu encontrar o arquivo "+classe+", necessário para sua execução.");
        }
        return ok;
    }
    
    /** Preenche os grafos e dispõe aleatoriamente no espaço os nós.
     */
    public void start() {
        if (parametrosEssenciaisExistem) {
            
//            getContentPane().setVisible(true);
            grSimples.desativarAutoAjuste();
            Dimension d = panelSimples.getSize();
            getContentPane().setVisible(false);
            
            //DebugMessenger.showMessage("meu ajuste:");
            grSimples.ajustarDimensaoDaTela(d);
            
            //DebugMessenger.showMessage("size do panelSimples"+panelSimples.getSize());
            
            // Sorteio de posicoes. Deve levar em conta o No Todos, mesmo que
            // ele nao esteja inicialmente no grafo.
            
            //DebugMessenger.showMessage("Sorteando novas posicoes");

            NoSimples noTodosSimples = (NoSimples)nosSimples.get(COD_NO_TODOS);
            if (noTodosSimples!=null) {
                grSimples.adicionarNo(noTodosSimples);
            }
            
            //DebugMessenger.showMessage("size do panelSimples"+panelSimples.getSize());
            panelSimples.disporNosAleatoriamente(d);
            //DebugMessenger.showMessage("size do panelSimples"+panelSimples.getSize());

            if (noTodosSimples!=null) {
                grSimples.removerNo(noTodosSimples);
            }            
            //DebugMessenger.showMessage("Fim do Sorteando novas posicoes");
            
            considerarTodosOsParticipantesDoCurso(false);
            if (getParameter("arestas_com_no_todos")!=null) {
                jCheckBoxNoTodos.setSelected(true);
                usarNoTodos();
            } else {
                jCheckBoxNoTodos.setSelected(false);
                naoUsarNoTodos();
            }

            grSimples.procurarEstabilidade();
            grSimples.centralizarGrafo();

            getContentPane().setVisible(true);
            repaint();
            
        }
    }
    
    /** Verifica se os parâmetros essenciais para o funcionamento do applet
     * foram informados pelo HTML. São eles: codigo_nome, arestas_sem_no_todos, 
     * formador, cod_curso e mostrar_link_perfil.
     * @return True se todos os parâmetros essenciais estão presentes, false
     * caso contrário.
     */
    private Collection retornaListaParametrosEssenciaisFaltantes() {
        String[] parametrosEssenciais = {
            "codigo_nome",
            "arestas_sem_no_todos",
            "formador",
            "cod_curso",
            "mostrar_link_perfil"
        };

        /* Justificativa para outros parametros nao serem necessarios:
         * - arestas_com_no_todos: Podem não existir arestas do nó Todos, pois nem sempre esse nó existe.
         * - colaboradores e visitantes: Podem não existir colaboradores nem visitantes, dependendo da ferramenta
         * cujos dados estão sendo mostrados.
         * - usuarios_sessao: Podem não existir sessões, se a ferramenta não for o Bate-papo.
         * - textos: Se não houver textos (ou se houver algum problema com os textos 
         * informados), o programa tem textos em Português.
         */
        
        Collection lista= new ArrayList();
        lista.clear();
        boolean ok = true;
        for (int i=0; i<parametrosEssenciais.length; i++) {
            ok = getParameter(parametrosEssenciais[i])!=null;
            if (!ok) {
                lista.add(parametrosEssenciais[i]);
            }
        }
        return(lista);
    }
    
    public void stateChanged(javax.swing.event.ChangeEvent e) {
        Object o = e.getSource();
        if (o.equals(panelComAbas)) {
            ajustarHabilitacaoGrafoForceDirected();
        }
    }

    private void ajustarHabilitacaoGrafoForceDirected() {
        if (panelComAbas.getSelectedComponent() == tabPanelSimples && tabPanelSimples != null) {
            panelSimples.habilitarAnimacao();
        } else {
            panelSimples.desabilitarAnimacao();
        }
    }
    
    private void redesenharPanelAtivo() {
        Component c = panelComAbas.getSelectedComponent();
        if (c==null) {
            //throw new NullPointerException("Exceção: nenhuma aba (tab) selecionada");
        } else if (c == tabPanelSimples) {
            panelSimples.repaint();
        } else if (c == tabPanelPolar) {
            panelPolar.repaint();
        }
    }
}