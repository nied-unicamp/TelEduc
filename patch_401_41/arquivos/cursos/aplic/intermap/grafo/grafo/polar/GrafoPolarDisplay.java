/*
 * GrafoPolarDisplay.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.polar;

import java.awt.*;
import java.awt.event.*;
import java.awt.geom.Rectangle2D;
import javax.swing.*;          
import java.util.Observer;
import java.util.Observable;
import java.awt.geom.Point2D;
import java.io.*;
import java.awt.image.*;
import grafo.*;


/** Panel em que o Grafo Polar será mostrado. Cuida de todos os cliques efetuados
 * nessa estrutura visual.
 */

public class GrafoPolarDisplay extends GrafoDisplay
implements ComponentListener, MouseMotionListener, MouseListener, ActionListener, Observer {
    
    /** Pausa em animação (milissegundos). */
    public final static int DELAY = 50;
    
    /** Razão de movimentação para efetuar movimentação de um no (noMovendo)
     * (tem que ser >2). 
     */
    public final static int RAZAO = 5;

    /** Timer usado para animação de movimentação de um nó, quando ele retorna para sua
     * posição correta.
     */
    private Timer timer;
    
    /** Raio de um nó, usado na sua movimentação para retornar para sua posição correta.
     */
    private double raioF;
    
    /** Ângulo de um nó, usado na sua movimentação para retornar para sua posição
     * correta.
     */
    private double anguloF;
    
    /** Abscissa de um nó, usada na sua movimentação para retornar para sua posição
     * correta.
     */
    private int fx;
    
    /** Ordenada de um nó, usada na sua movimentação para retornar para sua posição
     * correta.
     */
    private int fy;

    /** Indica se a animação está sendo executada ou não.
     */
    private boolean anim;
    
    /** Indica se o disco que representa a área proibida deve ou não ser mostrado na
     * tela. Tipicamente deve ser ajustado como True ou False quando o usuário estiver
     * ou não sobre um controle externo que ajuste o tamanho da área proibida.
     * Se True, desenha também uma linha do topo do disco até a margem direita do
     * objeto.
     */    
    public boolean mostrarCirculoAreaProibida;

    /** Indica se o círculo que representa o anel central deve ou não ser mostrado na
     * tela. Tipicamente deve ser ajustado como True ou False quando o usuário estiver
     * ou não sobre um controle externo que ajuste o tamanho ou a rotação do anel.
     */    
    public boolean mostrarCirculoCentral;

    /** Indica se o círculo que representa o anel periférico deve ou não ser mostrado na
     * tela. Tipicamente deve ser ajustado como True ou False quando o usuário estiver
     * ou não sobre um controle externo que ajuste o tamanho ou a rotação do anel.
     */    
    public boolean mostrarCirculoPeriferico;
    
    /** Indica se linha horizontal que sai do topo do círculo do anel central e vai até
     * a margem direta deve ou não ser mostrada na tela. Tipicamente deve ser ajustada
     * como True ou False quando o usuário estiver ou não sobre um controle externo que
     * ajuste o tamanho do anel central.
     */    
    public boolean mostrarLinhaAuxiliarDoAnelCentral;

    /** Indica se linha horizontal que sai do topo do círculo do anel periférico e vai
     * até a margem direta deve ou não ser mostrada na tela. Tipicamente deve ser
     * ajustada como True ou False quando o usuário estiver ou não sobre um controle
     * externo que ajuste o tamanho do anel periférico.
     */    
    public boolean mostrarLinhaAuxiliarDoAnelPeriferico;

    /** Indica se deve ou não ser mostrada uma linha que indique o ângulo inicial do
     * anel central. Tipicamente deve ser ajustada como True ou False quando o usuário
     * estiver ou não sobre um controle externo que ajuste a rotação do anel central.
     */    
    public boolean mostrarAlcaDoAnelCentral;

    /** Indica se deve ou não ser mostrada uma linha que indique o ângulo inicial do
     * anel periférico. Tipicamente deve ser ajustada como True ou False quando o
     * usuário  estiver ou não sobre um controle externo que ajuste a rotação do anel
     * periférico.
     */    
    public boolean mostrarAlcaDoAnelPeriferico;
    
    /** Indica se deve ou não ser mostrado um círculo "preview" que indica qual o
     * tamanho de um anel (ou da área proibida) ao se modificar um controle.
     * Tipicamente deve ser ajustado como True ou False quando o usuário estiver ou não
     * sobre um controle externo que ajuste o tamanho de um dos anéis ou da área
     * proibida. Ao ser desenhado, também será traçada uma linha que parte de seu topo
     * e se dirige à margem direita do objeto.
     */    
    public boolean mostrarCirculoPreview;
    
    /** Raio do círculo "preview".
     */    
    public int raioCirculoPreview = 0;
    
    /** O grafo
     */
    private GrafoPolar grafoPolar;
    
    /** Cria uma nova instância de GrafoPolarDisplay.
     * @param grafo Grafo a ser desenhado no display.
     */
    public GrafoPolarDisplay(GrafoPolar grafo) {
        super(grafo);
        grafoPolar = grafo;
        //this.grafo = grafo;
        
        // Listeners
        /*
        addComponentListener(this);
        addMouseMotionListener(this);
        addMouseListener(this);
        
        setCursor(new Cursor(Cursor.CROSSHAIR_CURSOR));
        setPreferredSize(new Dimension(300,300));
         */

        // Elementos auxiliadores de manipulação de tamanho e orientação dos
        // anéis
        mostrarCirculoAreaProibida = false;
        mostrarCirculoCentral = false;
        mostrarCirculoPeriferico = false;
        mostrarLinhaAuxiliarDoAnelCentral = false;
        mostrarLinhaAuxiliarDoAnelPeriferico = false;
        mostrarAlcaDoAnelCentral = false;
        mostrarAlcaDoAnelPeriferico = false;
        mostrarCirculoPreview = false;

        timer = new Timer(DELAY, this);
        timer.setInitialDelay(0);
        timer.setCoalesce(true);
        anim = false;
        
    }
    


    
    /**
     * Desenha elementos que ficarão em segundo plano.
     * No Grafo Polar, esses elementos são
     * - os círculos da área proibida, do anel central, do anel periférico e de
     * prévia
     * - as "alças" (linhas retas) que ligam as barras de controle de tamanho aos seus
     * respectivos círculos.
     * @param g Objeto Graphics em que esses elementos serão desenhados.
     */
    public void desenhaElementosEmSegundoPlano(Graphics2D g) {
        Shape shape = g.getClip();
        Dimension d = grafoPolar.retornarDimensaoDaTela();

        Rectangle2D rect = new Rectangle2D.Float();
        rect.setRect((int) grafoPolar.centro.x - d.width / 2 + 1, 
        (int) grafoPolar.centro.y - d.height / 2 + 1,
        d.width - 1, d.height - 1);
        g.setClip(rect);
        
        int by;
        int raio;
        if (mostrarCirculoAreaProibida) {
            raio = grafoPolar.raioAreaProibida.getValue();
            g.setColor(grafoPolar.corAreaProibida);
            g.fillOval((int)grafoPolar.centro.x - raio, (int)grafoPolar.centro.y - raio,
            2 * raio, 2 * raio);
            g.setColor(Color.black);
            by = (int) (grafoPolar.centro.y - raio);
            g.drawLine((int) (grafoPolar.centro.x), by, (int) (grafoPolar.centro.x + d.width/ 2), by);
            g.drawOval((int) (grafoPolar.centro.x - (grafoPolar.centro.y - by)), by, 
            (int) (2 * (grafoPolar.centro.y - by)), (int) (2 * (grafoPolar.centro.y - by)));
        }
        if (mostrarCirculoCentral) {
            raio = grafoPolar.raioCentral.getValue();
            g.setColor(grafoPolar.corAreaCentral);
            by = (int) (grafoPolar.centro.y - raio);
            if (mostrarLinhaAuxiliarDoAnelCentral) {
                g.drawLine((int) (grafoPolar.centro.x), by,
                (int) (grafoPolar.centro.x + d.width / 2), by);
            }
            g.drawOval((int) (grafoPolar.centro.x - (grafoPolar.centro.y - by)), by,
            (int) (2 * (grafoPolar.centro.y - by)), (int) (2 * (grafoPolar.centro.y - by)));
            g.setStroke(new BasicStroke(4.0f));
            g.drawOval((int) (grafoPolar.centro.x - raio), (int) (grafoPolar.centro.y - raio),
            2 * raio, 2 * raio);
            g.setStroke(new BasicStroke());
        }
        if (mostrarCirculoPeriferico) {
            raio = grafoPolar.raioPeriferico.getValue();
            g.setColor(grafoPolar.corAreaPeriferica);
            by = (int) (grafoPolar.centro.y - raio);
            if (mostrarLinhaAuxiliarDoAnelPeriferico) {
                g.drawLine((int) (grafoPolar.centro.x), by, 
                (int) (grafoPolar.centro.x + d.width / 2), by);
            }
            g.drawOval((int) (grafoPolar.centro.x - (grafoPolar.centro.y - by)), by,
            (int) (2 * (grafoPolar.centro.y - by)), (int) (2 * (grafoPolar.centro.y - by)));
            g.setStroke(new BasicStroke(4.0f));
            g.drawOval((int) (grafoPolar.centro.x - raio), (int) (grafoPolar.centro.y - raio), 
            2 * raio, 2 * raio);
            g.setStroke(new BasicStroke());
        }
        if (mostrarCirculoPreview) {
            g.setColor(Color.lightGray);
            g.drawOval((int)grafoPolar.centro.x - raioCirculoPreview, 
            (int)grafoPolar.centro.y-raioCirculoPreview,
            2 * raioCirculoPreview, 2 * raioCirculoPreview);
            g.drawLine((int)grafoPolar.centro.x, (int)grafoPolar.centro.y-raioCirculoPreview, 
            (int)grafoPolar.centro.x + d.width / 2,
            (int)grafoPolar.centro.y-raioCirculoPreview);
        }
        if (mostrarAlcaDoAnelCentral && grafoPolar.numeroNosCentrais.getValue()>1) {
            desenharAlca(g, grafoPolar.anguloInicialNosCentrais.getValue(), grafoPolar.raioCentral.getValue(), grafoPolar.corAreaCentral);
            desenharAlca(g, grafoPolar.anguloInicialNosCentrais.getValue() + 180, grafoPolar.raioCentral.getValue(), grafoPolar.corAreaCentral);            
        }
        if (mostrarAlcaDoAnelPeriferico && grafoPolar.numeroNosPerifericos.getValue()>0) {
            desenharAlca(g, grafoPolar.anguloInicialNosPerifericos.getValue(), grafoPolar.raioPeriferico.getValue(), grafoPolar.corAreaPeriferica);
            desenharAlca(g, grafoPolar.anguloInicialNosPerifericos.getValue() + 180, grafoPolar.raioPeriferico.getValue(), grafoPolar.corAreaPeriferica);            
        }
        
        g.setClip(shape);        
    }
    
    /** Desenha a alça relacionada à rotação dos anéis central e periférico.
     * @param g Onde o grafo será desenhado.
     * @param angulo Ângulo em que a alça deve ser desenhada.
     * @param raioInicial Raio a partir do qual a alça deve ser desenhada.
     * @param cor Cor com a qual a alça deve ser desenhada.
     */    
    public void desenharAlca(Graphics2D g, double angulo, int raioInicial, Color cor) {
        Point2D.Double p1,p2,p3;
        int tamanhoAlca = 30;
        int raioInicioAlca = Math.max(grafoPolar.raioCentral.getValue(), grafoPolar.raioPeriferico.getValue())+50;
        int raioFimAlca = raioInicioAlca+tamanhoAlca;
        g.setStroke(new BasicStroke(4.0f));
        p1 = PolarCartesiano.polar2Cartesiano(raioInicial, angulo, grafoPolar.centro);
        p2 = PolarCartesiano.polar2Cartesiano(raioFimAlca, angulo, grafoPolar.centro);
        p3 = PolarCartesiano.polar2Cartesiano(raioInicioAlca, angulo, grafoPolar.centro);        
        g.setColor(cor);
        g.drawLine((int)p1.x,(int)p1.y,(int)p2.x,(int)p2.y);

        g.setStroke(new BasicStroke(6.0f));        
        g.setColor(Color.black);
        g.drawLine((int)p3.x,(int)p3.y,(int)p2.x,(int)p2.y);        
        
        g.setStroke(new BasicStroke());
    }
    
    /** Função de ComponentListener.
     * @param e Evento do mouse.
     */
    public void componentResized(ComponentEvent e) {
        super.componentResized(e);
        grafoPolar.centro.x = getWidth() / 2;
        grafoPolar.centro.y = getHeight() / 2;
    }
    
   /** Trata dos casos em que o mouse está se movendo sobre o objeto.
     * Estabelece qual é o nó sobre o qual o mouse está (noFoco).
     * @param e Evento do mouse.
     */
    public void mouseMoved(MouseEvent e) {
        if (!anim) { // não faz nada durante animacao
            super.mouseMoved(e);
        }
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
        
        // Clique em botão central: mover nó para outro anel
        if (!anim && btn  == InputEvent.BUTTON2_MASK && cnt == 1) {
            NoPolar no = (NoPolar)noSobMouse();
            if (no != null) {
                if (no.eCentral()) {
                    grafoPolar.moverParaAnelPeriferico(no);
                } else {
                    grafoPolar.moverParaAnelCentral(no);
                }
                repaint();
            }
        }
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
        if (!anim) { // não faz nada durante animacao
            super.mousePressed(e);
        }
    }
    
    /** Funcao de MouseListener.
     * @param e Evento do mouse.
     */
    public void mouseReleased(MouseEvent e) {
        if (!anim) { // não faz nada durante animacao
            int btn = e.getModifiers();
            
            // Se botão principal foi solto
            
            if (btn  == InputEvent.BUTTON1_MASK) {
                if (grafoPolar.retornarNoMovendo() != null) {
                    NoPolar noMovendoPolar = (NoPolar)grafoPolar.retornarNoMovendo();
                    double raio   = noMovendoPolar.raio;   // salvar posicao original
                    
                    double angulo = noMovendoPolar.angulo;
                    grafoPolar.posicionarCorretamenteNoMovendo();
                    
                    raioF   = noMovendoPolar.raio;
                    anguloF = noMovendoPolar.angulo;
                    fx = (int)PolarCartesiano.polar2Cartesiano_x(noMovendoPolar.raio, noMovendoPolar.angulo, grafoPolar.centro);
                    fy = (int)PolarCartesiano.polar2Cartesiano_y(noMovendoPolar.raio, noMovendoPolar.angulo, grafoPolar.centro);
                    
                    noMovendoPolar.raio   = raio;
                    noMovendoPolar.angulo = angulo;
                    
                    startAnimation();
                } else {
                    repaint();
                }
            }
            testarPopup(e);
        }
    }
    
//    public void mouseDragged(MouseEvent e) {
//        super.mouseDragged(e);
//    }
    
    /** Invoca animação que faz a movimentação de um nó para sua posição correta. */
    public synchronized void startAnimation() {
        anim = true;
        //Inicia a animação.
        if (!timer.isRunning()) {
            timer.start();
        }
    }
    
    /** Pára a animacao que faz a movimentação de um nó para sua posição correta. */
    public synchronized void stopAnimation() {
        anim = false;
        //Pára a thread de animação.
        if (timer.isRunning()) {
            timer.stop();
        }
    }
    
    /** Trata diversas ações referentes a diversos eventos, como
     * @param e Evento do mouse.
     */
    public void actionPerformed(ActionEvent e) {
        Object source = e.getSource();
        if (source == timer) { // ação de timer de animação -> mover no para nova posição
            NoPolar noMovendoPolar = (NoPolar)grafoPolar.retornarNoMovendo();
            int ax = (int) PolarCartesiano.polar2Cartesiano_x(noMovendoPolar.raio, noMovendoPolar.angulo, grafoPolar.centro);
            int ay = (int) PolarCartesiano.polar2Cartesiano_y(noMovendoPolar.raio, noMovendoPolar.angulo, grafoPolar.centro);
            
            int passox = Math.abs(ax - fx);
            int passoy = Math.abs(ay - fy);
            if (passox < 5 && passoy < 5) { // parar animação.
                noMovendoPolar.raio = raioF;
                noMovendoPolar.angulo = anguloF;
                stopAnimation();
                grafoPolar.desfazerRegistrarNoMovendo();
                noMovendoPolar = null;
               
                NoPolar no = (NoPolar)noSobMouse();
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
                
                

            } else {
                passox = passox / RAZAO + 1;
                passoy = passoy / RAZAO + 1;
                if (fx < ax) {
                    ax -= passox; 
                } else {
                    ax += passox;
                }
                if (fy < ay) {
                    ay -= passoy; 
                } else {
                    ay += passoy;
                }
                noMovendoPolar.raio   = PolarCartesiano.cartesiano2Polar_raio(ax, ay, grafoPolar.centro);
                noMovendoPolar.angulo = PolarCartesiano.cartesiano2Polar_angulo(ax, ay, grafoPolar.centro);
            }
            repaint();
        } else {
            super.actionPerformed(e);
        }
    }
    
    /** Este método é chamado sempre que os objetos observados por este objeto são
     * modificados. Uma aplicação chama o método <code>nofifyObservers</code> de um
     * objeto <tt>Observable</tt>, para notificar todos os objetos sobre a mudança.
     * @param o O objeto observado (Observable).
     * @param arg Um argumento passado pelo método <code>notifyObservers</code>.
     * Neste caso, este argumento está sendo ignorado.
     */
    public void update(Observable o, Object arg) {
        grafoPolar.reposicionarNos();
        repaint();
    }
  
}
