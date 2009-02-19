/*
 * GrafoDisplay.java
 * Versão: 2004-08-24
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo;
import java.awt.*;
import java.awt.event.*;

import java.util.Collection;   // Foram discriminados Collection e
import java.util.Iterator;     // Iterator porque havia conflito se usássemos
import javax.swing.*;          // java.util.* , já que existem java.util.Timer e
import java.io.*;              // javax.swing.Timer.
import javax.imageio.ImageIO;
import java.awt.image.*;

/** Panel em que o Grafo será mostrado. Cuida de todos os cliques efetuados
 * nessa estrutura visual.
 */

public abstract class GrafoDisplay extends JPanel
implements MouseMotionListener, MouseListener, ActionListener, ComponentListener {
    
    /** Textos de menus e diálogos.
     */
    public String[] textos = {
        "Mostrar informação",           // 0
        "Esconder nó",
        "Esconder nós marcados",
        "Esconder nós não marcados",
        "Mostrar nós escondidos",
        "Dados",                        // 5
        "Tipo",
        "Peso", // Peso do nó
        "Nó",
        "Aresta",
        "Elementos interligados",       // 10
        "Informações sobre",
        "Peso" // Peso da aresta
    };
    
    /** Menu popup usado quando se clica em cima de um nó.
     */
    private JPopupMenu nopPopup;
    
    /** Elemento de menu popup.
     */
    private JMenuItem nopEsconder;
    
    /** Elemento de menu popup.
     */
    private JMenuItem nopMostrarInfo;
    
    /** Elemento de menu popup.
     */
    private JMenuItem nopEsconderEscolhidos;
    
    /** Elemento de menu popup.
     */
    private JMenuItem nopEsconderNaoEscolhidos;
    
    /** Elemento de menu popup.
     */
    private JMenuItem nopMostrarEscondidos;
    
    /** Menu popup usado quando se clica no fundo do grafo.
     */
    private JPopupMenu aePopup;
    
    /** Elemento de menu popup.
     */
    private JMenuItem aeEsconderEscolhidos;
    
    /** Elemento de menu popup.
     */
    private JMenuItem aeEsconderNaoEscolhidos;
    
    /** Elemento de menu popup.
     */
    private JMenuItem aeMostrarEscondidos;
    
    /** Janela de diálogo contendo informações sobre um nó ou aresta.
     */
    private JDialog dialogInfo;
    
    /** Botão OK no menu de diálogo citado anteriormente.
     */
    private JButton okInfo;
    
    /** Nó em foco, ou seja, nó sobre o qual está o mouse.
     */
    protected No noFoco; // DEVERIA SER PRIVATE!!!!
    
    /** Abscissa do mouse.
     */
    private int mx;
    
    /** Ordenada do mouse.
     */
    private int my;
    
    /** Nó que foi clicado com o botão direito do mouse para mostrar menu de contexto.
     */
    private No noEmPopup;
    
    /** Nó que está sendo movido pelo usuário ou que está sendo reposicionado pelo
     * próprio sistema.
     */
    //public No noMovendo;
    
    private boolean animacaoHabilitada = true;
    
    /** O grafo em si.
     */
    private Grafo grafo;
    
    /** Cria uma nova instância de GrafoDisplay.
     * @param grafo Grafo a ser desenhado no display. */
    public GrafoDisplay(Grafo grafo) {
        //usarGrafo(grafo);
        this.grafo = grafo;
        
        noFoco = null;
        //noMovendo = null;
        
        // Menu popup
        nopPopup = new JPopupMenu();
        
        /* Frase 0 - Mostrar informação */
        nopMostrarInfo = new JMenuItem(textos[0]);
        nopMostrarInfo.addActionListener(this);
        nopPopup.add(nopMostrarInfo);
        
        /* Frase 1 - Esconder nó */
        nopEsconder = new JMenuItem(textos[1]);
        nopEsconder.addActionListener(this);
        nopPopup.add(nopEsconder);
        
        /* Frase 2 - Esconder nós marcados */
        nopEsconderEscolhidos = new JMenuItem(textos[2]);
        nopEsconderEscolhidos.addActionListener(this);
        nopPopup.add(nopEsconderEscolhidos);
        
        /* Frase 3 - Esconder nós não marcados */
        nopEsconderNaoEscolhidos = new JMenuItem(textos[3]);
        nopEsconderNaoEscolhidos.addActionListener(this);
        nopPopup.add(nopEsconderNaoEscolhidos);
        
        /* Frase 4 - Mostrar nós escondidos */
        nopMostrarEscondidos = new JMenuItem(textos[4]);
        nopMostrarEscondidos.addActionListener(this);
        nopPopup.add(nopMostrarEscondidos);
        
        // Menu popup
        aePopup       = new JPopupMenu();
        
        /* Frase 2 - Esconder nós marcados */
        aeEsconderEscolhidos = new JMenuItem(textos[2]);
        aeEsconderEscolhidos.addActionListener(this);
        aePopup.add(aeEsconderEscolhidos);
        
        /* Frase 3 - Esconder nós não marcados */
        aeEsconderNaoEscolhidos = new JMenuItem(textos[3]);
        aeEsconderNaoEscolhidos.addActionListener(this);
        aePopup.add(aeEsconderNaoEscolhidos);
        
        /* Frase 4 - Mostrar nós escondidos */
        aeMostrarEscondidos = new JMenuItem(textos[4]);
        aeMostrarEscondidos.addActionListener(this);
        aePopup.add(aeMostrarEscondidos);
        
        // Listeners
        addComponentListener(this);
        addMouseMotionListener(this);
        addMouseListener(this);
        
        setCursor(new Cursor(Cursor.CROSSHAIR_CURSOR));
        //setPreferredSize(new Dimension(300,300));
        
    }
    
    /** Desenha o grafo (método padrão do Java).
     * @param g Onde o grafo será desenhado.
     */
    public void paint(Graphics g) {
        desenhar((Graphics2D)g);
    }
    
    /** Desenha o grafo.
     * @param g Onde o grafo será desenhado.
     */
    public void desenhar(Graphics2D g) {
        g.setColor(grafo.corDeFundo);
        //g.fillRect((int) (grafo.centro.x - grafo.largura / 2),
        //(int) (grafo.centro.y - grafo.altura / 2),
        //(int) grafo.largura, (int) grafo.altura);
        g.fillRect(0,0, getWidth(), getHeight());
        g.setColor(Color.black);
        //g.drawRect((int) (grafo.centro.x - grafo.largura / 2),
        //(int) (grafo.centro.y - grafo.altura / 2),
        //(int) grafo.largura, (int) grafo.altura);
        g.drawRect(0,0, getWidth(), getHeight());
        
        desenhaElementosEmSegundoPlano((Graphics2D)g);
        
        grafo.desenhar((Graphics2D)g);
        // borda preta
        g.setColor(Color.black);
        g.drawRect(0, 0, getWidth() - 1 , getHeight() - 1);
        ajustarCursor();
        
    }
    
    /** Desenha elementos que ficarão em segundo plano.
     * @param g Objeto Graphics em que o desenho será feito.
     */
    public abstract void desenhaElementosEmSegundoPlano(Graphics2D g);
    
    /** Retorna um valor informando qual tipo de área do grafo está sob o mouse
     * @return  Retorna AREA_DE_NO - se o ponto está sobre um nó;
     *          AREA_INTERIOR - se está dentro do retângulo mas fora de um nó;
     *          AREA_EXTERIOR - se está fora do retângulo.
     */
    public int areaSobMouse() {
        return(grafo.areaEm(mx, my));
    }
    
    /** Informa qual o nó que está sob o mouse
     * @return  Retorna o nó, ou null se o nó não existir.
     */
    public No noSobMouse() {
        return(grafo.noEm(mx,my));
    }
    
    /** Atualiza variáveis internas que guardam as coordenadas do mouse.
     * Garante que a coordenada armazenada estará dentro dos limites atuais
     * da dimensão do display.
     * @param e Evento do mouse.
     */
    public void atualizarCoordenadasDoMouseInternas(MouseEvent e) {
        mx = e.getX(); my = e.getY();
        Dimension d = getSize();
        if (mx>d.width) {
            mx=d.width;
        }
        if (my>d.height) {
            my=d.height;
        }
        if (mx<0) {
            mx=0;
        }
        if (my<0) {
            my=0;
        }
        
    }
    
    /** Ajusta tipo de cursor, de acordo com a posição do mouse.
     */
    protected void ajustarCursor() { // NAO DEVERIA SER PRIVATE?
        int gc = areaSobMouse();
        switch (gc) {
            case Grafo.AREA_DE_NO:
                setCursor(new Cursor(Cursor.HAND_CURSOR));
                break;
            case Grafo.AREA_EXTERIOR:
            case Grafo.AREA_INTERIOR:
            default:
                setCursor(new Cursor(Cursor.DEFAULT_CURSOR));
                break;
        }
    }
    
    /** Trata dos casos em que o mouse está arrastando algo que começou a
     * ser arrastado dentro do objeto (tipicamente é o caso de um nó.)
     * @param e Evento do mouse.
     */
    public void mouseDragged(MouseEvent e) {
        atualizarCoordenadasDoMouseInternas(e);
        if (grafo.retornarNoMovendo() != null) {
            grafo.moverNoMovendo(mx,my);
            repaint();
        }
    }
    
    /** Trata dos casos em que o mouse está se movendo sobre o objeto.
     * Estabelece qual é o nó sobre o qual o mouse está (noFoco).
     * @param e Evento do mouse.
     */
    public void mouseMoved(MouseEvent e) {
        atualizarCoordenadasDoMouseInternas(e);
        No no = noSobMouse();
        if (noFoco != no) {
            if (noFoco != null) {
                noFoco.desfazerEscolher();
            }
            noFoco = no;
            if (noFoco != null) {
                noFoco.escolher();
            }
            repaint();
        }
        ajustarCursor();
    }
    
    /** Trata dos casos em que o objeto foi clicado.
     * @param e Evento do mouse.
     */
    public void mouseClicked(MouseEvent e) {
        atualizarCoordenadasDoMouseInternas(e);
        int cnt = e.getClickCount(), btn = e.getModifiers();
        
        // Clique em botão principal: Marcar um nó.
        if (btn == InputEvent.BUTTON1_MASK && cnt == 1) {
            boolean ok = marcarNoSobMouse();
            if (ok) {
                mouseMoved(e);
            }
        }
    }
    
    /**
     * Marca o nó que estiver debaixo da última coordenada do mouse verificada.
     * @return Retorna True se a operação teve sucesso, False caso contrário.
     */
    public boolean marcarNoSobMouse() {
        No no = noSobMouse();
        boolean ok = (no!=null);
        if (ok) {
            if (noFoco != null) {
                noFoco.desfazerEscolher();
                noFoco = null;
            }
            if (no.eMarcado()) {
                no.desmarcar();
            } else {
                no.marcar();
            }
        }
        return(ok);
    }
    
    
    /** Não implementado.
     * @param e Evento do mouse.
     */
    public void mouseEntered(MouseEvent e) {
    }
    
    /** Não implementado.
     * @param e Evento do mouse.
     */
    public void mouseExited(MouseEvent e) {
    }
    
    /** Funcao de MouseListener.
     * @param e Evento do mouse.
     */
    public void mousePressed(MouseEvent e) {
        atualizarCoordenadasDoMouseInternas(e);
        int btn = e.getModifiers();
        
        // Botão principal do mouse foi pressionado:
        // Se cursor está dentro da área em que o grafo está desenhado,
        // verifica se está sobre algum nó. Se estiver, o usuário deseja
        // mover esse nó.
        // Se curor não está sobre nenhum nó, verificar se é o caso de
        // mostrar um menu de contexto.
        
        if (btn  == InputEvent.BUTTON1_MASK) {
            if (grafo.areaEm(mx, my) != Grafo.AREA_EXTERIOR) {
                No no = noSobMouse();
                if (no != null) {
                    if (noFoco != null) {
                        noFoco.desfazerEscolher();
                        noFoco = null;
                    }
                    grafo.registrarNoMovendo(no);
                    repaint();
                }
            }
            
        } else {
            // Verifica se deve mostrar menu de contexto
            testarPopup(e);
        }
    }
    
    /** Funcao de MouseListener.
     * @param e Evento do mouse.
     */
    public void mouseReleased(MouseEvent e) {
        int btn = e.getModifiers();
        
        // Se botão principal foi solto
        
        if (btn  == InputEvent.BUTTON1_MASK) {
            
            //if (noMovendo != null) {
            if (grafo.retornarNoMovendo()!=null) {
                grafo.desfazerRegistrarNoMovendo();
                mouseMoved(e);
                repaint();
            }
            
        }
        testarPopup(e);
        
    }
    
    /** Testar se se encontrou um click para mostrar menu de contexto.
     * Se mouse está em um nó, mostra menu de contexto de nó.
     * Se mouse está fora de um nó, mostra menu de contexto geral.
     * @param e Evento do mouse.
     */
    public void testarPopup(MouseEvent e) {
        if (e.isPopupTrigger()) {
            
            // Botão secundário do mouse foi pressionado:
            // Verifica se usuário clicou em área do grafo e que não seja
            // área de nó. Se sim, verificar se usuário clicou em aresta.
            
            int contem = areaSobMouse();
            switch (contem) {
                
                case Grafo.AREA_DE_NO:
                    
                    No no = noSobMouse();
                    if (no != null) {
                        // mostra menu de contexto do nó clicado.
                        noEmPopup = no;
                        nopEsconderEscolhidos.setEnabled(grafo.retornarEscolhidos().size()>0);
                        nopEsconderNaoEscolhidos.setEnabled(grafo.retornarNaoEscolhidos().size()>0);
                        nopMostrarEscondidos.setEnabled(grafo.retornarEscondidos().size()>0);
                        nopPopup.show(e.getComponent(),e.getX(), e.getY());
                    }
                    break;
                    
                case Grafo.AREA_INTERIOR:
                    
                    Collection arestasSobMouse;
                    arestasSobMouse = grafo.retornarConjuntoArestasEm(mx,my);
                    Iterator i = arestasSobMouse.iterator();
                    Aresta aresta;
                    
                    // se existe aresta, mostrar informacao sobre ela.
                    // senao, mostra menu de contexto geral.
                    if (i.hasNext()) {
                        aresta = (Aresta)(i.next());
                        mostrarDialogoInformacaoAresta(aresta);
                    } else {
                        aeEsconderEscolhidos.setEnabled(grafo.retornarEscolhidos().size()>0);
                        aeEsconderNaoEscolhidos.setEnabled(grafo.retornarNaoEscolhidos().size()>0);
                        aeMostrarEscondidos.setEnabled(grafo.retornarEscondidos().size()>0);
                        aePopup.show(e.getComponent(),e.getX(), e.getY());
                        //repaint();
                    }
                    break;
                    
                default:
                    // nao faz nada.
                    
            }
        }
    }
    
    
    
    /** Trata diversas ações referentes a diversos eventos, como
     * @param e Evento do mouse.
     */
    public void actionPerformed(ActionEvent e) {
        Object source = e.getSource();
        if (source == nopEsconder) { // popup menu item
            if (noFoco != null) {
                noFoco.desfazerEscolher();
            }
            noFoco = null;
            grafo.esconderNo(noEmPopup);
            repaint();
        } else
            
            if (source == nopMostrarInfo) {  // popup menu item
                mostrarDialogoInformacaoNo(noEmPopup);
            } else
                
                if (source == nopEsconderEscolhidos || source == aeEsconderEscolhidos) {  // popup menu item
                    if (noFoco != null) {
                        noFoco.desfazerEscolher();
                    }
                    noFoco = null;
                    //            Iterator li = grafo.retornarEscolhidos().iterator();
                    //            while(li.hasNext())
                    //                grafo.esconderNo((No)li.next());
                    grafo.esconderNos(grafo.retornarEscolhidos());
                    repaint();
                } else
                    
                    if (source == nopEsconderNaoEscolhidos || source == aeEsconderNaoEscolhidos) {  // popup menu item
                        if (noFoco != null) {
                            noFoco.desfazerEscolher();
                        }
                        noFoco = null;
                        //Iterator li = grafo.retornarNaoEscolhidos().iterator();
                        //while(li.hasNext())
                        //    grafo.esconderNo((No) li.next());
                        grafo.esconderNos(grafo.retornarNaoEscolhidos());
                        
                        repaint();
                    } else
                        
                        if (source == nopMostrarEscondidos || source == aeMostrarEscondidos) {  // popup menu item
                            if (noFoco != null) {
                                noFoco.desfazerEscolher();
                            }
                            noFoco = null;
                            grafo.mostrarTodosOsNos();
                            repaint();
                        } else
                            
                            if (source == okInfo) {
                                dialogInfo.dispose(); // button de info dialogo
                                repaint();
                            }
    }
    
    /** Mostra janela de informações sobre um nó específico.
     * @param no Nó cujas informações devem ser mostradas.
     */
    private void mostrarDialogoInformacaoNo(No no) {
        if (no!=null) {
            dialogInfo = new JDialog();
            /* Organizaçao de dialogInfo:
             * Norte:  JPanel panelCabecalho contendo
             *          Norte: Cabeçalho (JLabel linhaNomeNo)
             *          Sul: separador (Jpanel)
             * Centro: Conteúdo (JPanel panelCentral), composto de:
             *          Norte:  panelInformacao (se existir)
             *          Centro: JLabel sobre grupo
             *          Sul:    JLabel sobre peso
             * Sul:    JPanel panelBotaoOK, contendo Botão OK.
             */
            int alturaDialogInfo=0;
            
            /* Frase 11 - Informções sobre */
            dialogInfo.setTitle(textos[11] + "...");
            dialogInfo.setModal(true);
            
            dialogInfo.setDefaultCloseOperation(WindowConstants.DISPOSE_ON_CLOSE);
            JPanel conteudo = new JPanel();
            conteudo.setLayout(new BorderLayout());
            dialogInfo.setContentPane(conteudo);
            
            JPanel panelCabecalho = new JPanel(new BorderLayout());
            /* Frase 11 - Informações sobre */
            JLabel linhaNomeNo = new JLabel(textos[11] +" "+ no.nome);
            panelCabecalho.add(linhaNomeNo, BorderLayout.NORTH);
            
            JPanel separador = new JPanel();
            separador.setSize(100, 20);
            panelCabecalho.add(separador, BorderLayout.SOUTH);
            
            conteudo.add(panelCabecalho, BorderLayout.NORTH);
            
            alturaDialogInfo += panelCabecalho.getHeight();
            
            JPanel panelCentral = new JPanel(new BorderLayout());
            if (no.info != null) {
                JPanel panelInformacao = new JPanel(new BorderLayout());
                
                /* Frase 5 - Dados */
                //JLabel labelInformacao=new JLabel(textos[5]+":");
                //labelInformacao.setFont(Font.getFont("Plain"));
                //panelInformacao.add(labelInformacao, BorderLayout.NORTH);
                
                JComponent infoComponente = no.info.retornarInformacao();
                panelInformacao.add(infoComponente, BorderLayout.CENTER);
                
                panelCentral.add(panelInformacao, BorderLayout.NORTH);
            }
            /* Frase 6 - Grupo: */
            if (no.grupo!=null) {
                JLabel labelGrupo = new JLabel(textos[6] +": "+ no.grupo);
                labelGrupo.setFont(Font.getFont("Plain"));
                panelCentral.add(labelGrupo, BorderLayout.CENTER);
            }
            
            if (grafo.nosPossuemPeso()) {
                /* Frase 7 - Peso: */
                JLabel labelPeso = new JLabel(textos[7] +": "+ no.peso);
                labelPeso.setFont(Font.getFont("Plain"));
                panelCentral.add(labelPeso, BorderLayout.SOUTH);
            }
            
            conteudo.add(panelCentral, BorderLayout.CENTER);
            
            alturaDialogInfo += panelCentral.getHeight();
            
            okInfo = new JButton("OK");
            okInfo.setMnemonic(KeyEvent.VK_O);
            okInfo.addActionListener(this);
            dialogInfo.getRootPane().setDefaultButton(okInfo);
            
            JPanel panelBotaoOK = new JPanel();
            panelBotaoOK.add(okInfo);
            
            alturaDialogInfo += panelBotaoOK.getHeight();
            
            conteudo.add(panelBotaoOK, BorderLayout.SOUTH);
            
            dialogInfo.addKeyListener(new KeyAdapter() {
                public void keyPressed(KeyEvent e) {
                    if (e.getKeyCode() == KeyEvent.VK_ESCAPE) {
                        dialogInfo.dispose();
                    }
                }
            } );
            
            Point delta;
            try {
                delta = getLocationOnScreen();
            } catch (IllegalComponentStateException e) {
                delta = new Point(0,0);
            }
            dialogInfo.setBounds(delta.x + mx, delta.y + my, 200, alturaDialogInfo);
            dialogInfo.pack();
            dialogInfo.setVisible(true);
        }
    }
    
    /** Mostra janela de informações sobre uma aresta específica.
     * @param aresta Aresta cujas informações devem ser mostradas.
     */
    private void mostrarDialogoInformacaoAresta(Aresta aresta) {
        
        dialogInfo = new JDialog();
        /* Organizaçao de dialogInfo:
         * Norte:  Cabeçalho (JPanel panelCabecalho), composto de
         *          Norte: JLabel com frase "Aresta interligando os seguintes nós:"
         *          Centro: JPanel panelNos contendo
         *              Norte: JLabel com no1;
         *              Sul: JLabel com no2;
         *          Sul: separador (JPanel)
         * Centro: Conteúdo (JPanel panelCentral), composto de:
         *          Norte:  panelInformacao (se existir)
         *          Sul:    JLabel sobre peso
         * Sul:    JPanel panelBotaoOK, contendo Botão OK.
         */
        int alturaDialogInfo=0;
        
        /* Frase 11 - Informações sobre */
        dialogInfo.setTitle(textos[11] + "...");
        dialogInfo.setModal(true);
        
        dialogInfo.setDefaultCloseOperation(WindowConstants.DISPOSE_ON_CLOSE);
        JPanel conteudo = new JPanel();
        conteudo.setLayout(new BorderLayout());
        dialogInfo.setContentPane(conteudo);
        
        
        JPanel panelCabecalho = new JPanel(new BorderLayout());
        /* Frase 10 - Elementos interligados */
        JLabel linhaNomeAresta = new JLabel(textos[10]+" :");
        panelCabecalho.add(linhaNomeAresta, BorderLayout.NORTH);
        JPanel panelNos = new JPanel(new BorderLayout());
        JLabel linhaNomeNo1 = new JLabel("- "+((No)aresta.no1).nome);
        panelNos.add(linhaNomeNo1, BorderLayout.NORTH);
        JLabel linhaNomeNo2 = new JLabel("- "+((No)aresta.no2).nome);
        panelNos.add(linhaNomeNo2, BorderLayout.SOUTH);
        panelCabecalho.add(panelNos, BorderLayout.CENTER);
        JPanel separador = new JPanel();
        separador.setSize(100, 20);
        panelCabecalho.add(separador, BorderLayout.SOUTH);
        conteudo.add(panelCabecalho, BorderLayout.NORTH);
        
        alturaDialogInfo += panelCabecalho.getHeight();
        
        JPanel panelCentral = new JPanel(new BorderLayout());
        if (aresta.info != null) {
            JPanel panelInformacao = new JPanel(new BorderLayout());
            
            /* Frase 5 - Dados */
            // JLabel labelInformacao=new JLabel(textos[5] + ":");
            // labelInformacao.setFont(Font.getFont("Plain"));
            // panelInformacao.add(labelInformacao, BorderLayout.NORTH);
            
            JComponent infoComponente = aresta.info.retornarInformacao();
            panelInformacao.add(infoComponente, BorderLayout.CENTER);
            
            panelCentral.add(panelInformacao, BorderLayout.NORTH);
        }
        
        /* Frase 12 - Peso: */
        if (grafo.arestasPossuemPeso()) {
            JLabel labelPeso = new JLabel(textos[12] +": "+ aresta.peso);
            labelPeso.setFont(Font.getFont("Plain"));
            panelCentral.add(labelPeso, BorderLayout.SOUTH);
        }
        conteudo.add(panelCentral, BorderLayout.CENTER);
        
        alturaDialogInfo += panelCentral.getHeight();
        
        okInfo = new JButton("OK");
        okInfo.setMnemonic(KeyEvent.VK_O);
        okInfo.addActionListener(this);
        dialogInfo.getRootPane().setDefaultButton(okInfo);
        
        JPanel panelBotaoOK = new JPanel();
        panelBotaoOK.add(okInfo);
        
        alturaDialogInfo += panelBotaoOK.getHeight();
        
        conteudo.add(panelBotaoOK, BorderLayout.SOUTH);
        
        dialogInfo.addKeyListener(new KeyAdapter() {
            public void keyPressed(KeyEvent e) {
                if (e.getKeyCode() == KeyEvent.VK_ESCAPE) {
                    dialogInfo.dispose();
                }
            }
        } );
        
        Point delta;
        try {
            delta = getLocationOnScreen();
        } catch (IllegalComponentStateException e) {
            delta = new Point(0,0);
        }
        dialogInfo.setBounds(delta.x + mx, delta.y + my, 200, alturaDialogInfo);
        dialogInfo.pack();
        dialogInfo.setVisible(true);
        
    }
    
    /** Modifica os textos de menus e diálogos.
     * @param textosNovos Todos os textos que irão substituir os textos atuais.
     */
    public void modificarTextos(java.util.List textosNovos) {
        if (textosNovos.size() == textos.length) {
            textos = (String[])textosNovos.toArray(textos);
            nopMostrarInfo.setText(textos[0]);
            nopEsconder.setText(textos[1]);
            nopEsconderEscolhidos.setText(textos[2]);
            nopEsconderNaoEscolhidos.setText(textos[3]);
            nopMostrarEscondidos.setText(textos[4]);
            aeEsconderEscolhidos.setText(textos[2]);
            aeEsconderNaoEscolhidos.setText(textos[3]);
            aeMostrarEscondidos.setText(textos[4]);
        } else {
            System.out.println("Erro. Não foi possível modificar os textos, porque a lista contendo os novos textos é de tamanho diferente da lista original de textos.");
        }
    }
    
    /** Modifica o nome que descreve o peso dos nós. Inicialmente esse nome é "Peso",
     * mas pode haver interesse em modificá-lo para especificar que esse peso se refere
     * a uma determinada medida (por exemplo, número de mensagens recebidas).
     * Esse nome é independente do nome do peso das arestas.
     * @param nome Novo nome a ser utilizado.
     */
    public void renomearPesoDosNosPara(String nome) {
        textos[7] = nome;
    }
    
    /** Modifica o nome que descreve o peso das arestas. Inicialmente esse nome é "Peso",
     * mas pode haver interesse em modificá-lo para especificar que esse peso se refere
     * a uma determinada medida (por exemplo, número de mensagens representadas pela
     * aresta).
     * Esse nome é independente do nome do peso dos nós.
     * @param nome Novo nome a ser utilizado.
     */
    public void renomearPesoDasArestasPara(String nome) {
        textos[12] = nome;
    }
    
    /** Salva uma imagem do display em um arquivo especificado.
     * @param arquivo Arquivo no qual a imagem será armazenada.
     * @param tipoArquivo Tipo do arquivo a ser gerado. Ao menos os seguintes
     * formatos são suportados para tipoArquivo: jpeg e png.
     * @return True se a operação foi bem sucedida, false caso contrário.
     */
    public boolean salvarImagem(File arquivo, String tipoArquivo) {
        boolean res = false;
        if (arquivo!=null) {
            BufferedImage i = (BufferedImage)createImage(getWidth(), getHeight());
            paint(i.getGraphics());
            //        File arquivo = new File(nomeArquivo);
            try {
                ImageIO.write(i, tipoArquivo, arquivo);
                res = true;
            } catch (IOException ioe) {
                ioe.printStackTrace();
            }
        }
        return res;
    }
    
    /** Não está sendo utilizado.
     * @param componentEvent Evento.
     */
    public void componentHidden(java.awt.event.ComponentEvent componentEvent) {
    }
    
    /** Não está sendo utilizado.
     * @param componentEvent Evento.
     */
    public void componentMoved(java.awt.event.ComponentEvent componentEvent) {
    }
    
    /** Atualiza indicadores de largura e altura quanto o display é redimensionado.
     * @param componentEvent Evento.
     */
    public void componentResized(java.awt.event.ComponentEvent componentEvent) {
        ajustarLimitesDeEspacoDoGrafo();
    }
    
    /** Não está sendo utilizado.
     * @param componentEvent Evento.
     */
    public void componentShown(java.awt.event.ComponentEvent componentEvent) {
        ajustarLimitesDeEspacoDoGrafo();
    }
    
    /** Informa para o grafo quais os novos limites de tela que seus elementos
     * podem utilizar.
     */
    public void ajustarLimitesDeEspacoDoGrafo() {
        Dimension d = new Dimension(getWidth(), getHeight());
        grafo.ajustarDimensaoDaTela(d);
    }

    /** Sorteia a posição dos nós de acordo com a última dimensão registrada
     * pelo grafo.
     */
    public void disporNosAleatoriamente() {
        disporNosAleatoriamente(grafo.retornarDimensaoDaTela());
    }
    
    /** Sorteia a posição dos nós de acordo com uma dimensão especificada.
     * @param d Dimensão dentro da qual as posições dos nós devem ser sorteadas.
     */    
    synchronized public void disporNosAleatoriamente(Dimension d) {
        int margem = 30;
        if (grafo.retornarQuantidadeDeNos()>0) {
            Iterator li = grafo.nos.iterator();
            while(li.hasNext()) {
                No no = (No)li.next();
                no.x = (int)(Math.random()*(d.width-2*margem)+margem);
                no.y = (int)(Math.random()*(d.height-2*margem)+margem);
            }
        }
    }
    
    /** Permite que os nós executem animações.
     */    
    public void habilitarAnimacao() {
        animacaoHabilitada = true;
    }

    /** Não permite que os nós executem animações.
     */
    public void desabilitarAnimacao() {
        animacaoHabilitada = false;
    }

    /** Informa se o display permite ou não que os nós executem animações.
     * @return True se o display atualmente permite animações, false caso 
     * contrário.
     */
    public boolean animacaoEstaHabilitada() {
        return animacaoHabilitada;
    }
}
