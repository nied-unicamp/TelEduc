/*
 * GrafoPolarPanel.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.polar;

import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
import java.util.Observer;
import java.util.Observable;
import java.io.*;
import grafo.*;

/** Cria um panel em que o grafo polar e seus respectivos controles são desenhados. */
public class GrafoPolarPanel extends GrafoPanel implements Observer {
    
//    /** Panel contendo todos os controles de tamanho dos anéis e da área proibida. */    
//    private JPanel panelControles;
    
    /** Grafo a ser desenhado. */    
    private GrafoPolar grafoPolar;
    
    /** Display em que o grafo será desenhado. */
    private GrafoPolarDisplay displayPolar;
    
    /** Controle de tamanho do anel periférico. */
    private Barra barraPeriferica;
    
    /** Controle de tamanho do anel central. */
    private Barra barraCentral;
    
    /** Controle de tamanho da área proibida. */    
    private Barra barraAreaProibida;
    
    /** Seletor duplo de ângulos para controlar ângulos iniciais do anel central e do
     * anel periférico.
     */    
    private SeletorDuploDeAngulos seletor;
    
    /** Cria uma nova instância GrafoPolarPanel.
     * @param grafo Grafo a ser mostrado pelo objeto.
     */
    public GrafoPolarPanel(GrafoPolar grafo) {
        super(grafo);
        grafoPolar = grafo;
    }
    
    /**
     * Cria e insere layout e controles (se existirem) no GrafoPanel.
     * @param display Display em que o grafo será mostrado.
     * @param grafo Grafo a ser mostrado.
     */    
    public void inserirLayoutEControles(GrafoDisplay display, Grafo grafo) {
        GrafoPolar grafoPolar = (GrafoPolar)grafo;
        // definindo a interface
        setLayout(new java.awt.GridBagLayout());
        java.awt.GridBagConstraints gridBagConstraints;
        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 0;
        gridBagConstraints.gridy = 0;
        gridBagConstraints.gridheight = 1;
        gridBagConstraints.weightx = 1.0;
        gridBagConstraints.weighty = 1.0;
        gridBagConstraints.fill = java.awt.GridBagConstraints.BOTH;
        gridBagConstraints.anchor = java.awt.GridBagConstraints.NORTHEAST;
        add(display, gridBagConstraints);
        
        setSize(400,400); // tamanho inicial do Panel
        
        grafoPolar.ajustarRaioPeriferico(150);
        grafoPolar.ajustarRaioCentral(50);
        grafoPolar.ajustarRaioAreaProibida(100);
        
        barraPeriferica = new Barra(getHeight()/2, grafoPolar.raioPeriferico, grafoPolar.corAreaPeriferica);
        barraCentral = new Barra(getHeight()/2,  grafoPolar.raioCentral, grafoPolar.corAreaCentral);
        barraAreaProibida = new Barra(getHeight()/2, grafoPolar.raioAreaProibida, grafoPolar.corAreaProibida);
        
        // Modificar linha abaixo. A barra da area proibida só deve ficar 
        // habilitada se houver arestas periféricas no grafo => Essa propriedade
        // do grafo deve ser ObservableBoolean, e a barra da area proibida deve
        // observá-la.
        barraAreaProibida.ativado = true;
        
        seletor = new SeletorDuploDeAngulos(grafoPolar.corAreaCentral, grafoPolar.corAreaPeriferica, grafoPolar.anguloInicialNosCentrais, grafoPolar.anguloInicialNosPerifericos);
        
        
        JPanel panelControles = new JPanel();
        panelControles.setLayout(new javax.swing.BoxLayout(panelControles, javax.swing.BoxLayout.X_AXIS));
        panelControles.setAlignmentX(Box.LEFT_ALIGNMENT);
        panelControles.add(barraPeriferica);
        panelControles.add(Box.createRigidArea(new Dimension(1,0)));
        panelControles.add(barraCentral);
        panelControles.add(Box.createRigidArea(new Dimension(1,0)));
        panelControles.add(barraAreaProibida);

        int larguraBarras = seletor.largura - (barraPeriferica.larguraTotal + barraCentral.larguraTotal + barraAreaProibida.larguraTotal + 2);
        panelControles.add(Box.createRigidArea(new Dimension(larguraBarras,0)));
        
        JPanel panelElementos = new JPanel();
        panelElementos.setLayout(new java.awt.GridLayout(2,1));
        panelElementos.add(panelControles);
        panelElementos.add(seletor);
        
        gridBagConstraints = new java.awt.GridBagConstraints();
        gridBagConstraints.gridx = 1;
        gridBagConstraints.gridy = 0;
        gridBagConstraints.gridheight = 2;
        gridBagConstraints.weightx = 0.0;
        gridBagConstraints.weighty = 0.0;
        gridBagConstraints.fill = java.awt.GridBagConstraints.VERTICAL;
        gridBagConstraints.anchor = java.awt.GridBagConstraints.EAST;
        add(panelElementos, gridBagConstraints);
        
        // Fazer display prestar atenção às propriedades do grafo
        // para se modificar de acordo com elas
        grafoPolar.raioPeriferico.addObserver(displayPolar);
        grafoPolar.raioCentral.addObserver(displayPolar);
        grafoPolar.raioAreaProibida.addObserver(displayPolar);
        grafoPolar.anguloInicialNosCentrais.addObserver(displayPolar);
        grafoPolar.anguloInicialNosPerifericos.addObserver(displayPolar);
        
        // Fazer este panel prestar atencão às barras e ao seletor de ângulos,
        // para mostrar ou não círculos de acordo com a presença/ausência do
        // mouse sobre um desses controles.
        barraPeriferica.mouseDentroDoControle.addObserver(this);
        barraCentral.mouseDentroDoControle.addObserver(this);
        barraAreaProibida.mouseDentroDoControle.addObserver(this);
        seletor.mouseDentroDoControle.addObserver(this);
        grafoPolar.numeroNosCentrais.addObserver(this);
        grafoPolar.numeroNosPerifericos.addObserver(this);
    }
    
    /** Atualiza o estado (ativado ou desativado) dos controles de tamanho e de rotação,
     * de acordo com o número de nós em cada anel do grafo.
     */    
    public void atualizarEstadoControles() {
        if (grafoPolar.numeroNosCentrais.getValue() < 2) {
            seletor.desabilitar(seletor.SELETOR_INTERNO);
            barraCentral.desabilitar();
        } else {
            seletor.habilitar(seletor.SELETOR_INTERNO);
            barraCentral.habilitar();
        }
        if (grafoPolar.numeroNosPerifericos.getValue() == 0) {
            seletor.desabilitar(seletor.SELETOR_EXTERNO);
            barraPeriferica.desabilitar();
        } else {
            seletor.habilitar(seletor.SELETOR_EXTERNO);
            barraPeriferica.habilitar();
        }
    }
    
    /** Este método é chamado sempre que um objeto observado pelo objeto atual é
     * modificado. Uma aplicação chama o método <code>notifyObservers</code> de um
     * objeto <tt>Observable</tt> para notificar todos os observadores sobre a mudança
     * efetuada.
     * @param o O objeto sendo observado.
     * @param arg Um argumento passado para o método <code>notifyObservers</code>.
     */
    public void update(Observable o, Object arg) {
        if (displayEstaDefinido() && barraAreaProibida!=null && barraCentral!= null && barraPeriferica!=null && grafoPolar!=null) {
            if (o.equals(grafoPolar.numeroNosCentrais) || o.equals(grafoPolar.numeroNosPerifericos)) {
                atualizarEstadoControles();
            } else {
                boolean mouseEmBarraAreaProibida = barraAreaProibida.mouseDentroDoControle.getBoolean();
                boolean mouseEmBarraCentral = barraCentral.mouseDentroDoControle.getBoolean();
                boolean mouseEmBarraPeriferica = barraPeriferica.mouseDentroDoControle.getBoolean();
                boolean mouseEmSeletor = seletor.mouseDentroDoControle.getValue();
                displayPolar.mostrarCirculoAreaProibida = mouseEmBarraAreaProibida;
                displayPolar.mostrarCirculoCentral = mouseEmBarraCentral || mouseEmSeletor;
                displayPolar.mostrarCirculoPeriferico = mouseEmBarraPeriferica || mouseEmSeletor;
                displayPolar.mostrarLinhaAuxiliarDoAnelCentral = mouseEmBarraCentral;
                displayPolar.mostrarLinhaAuxiliarDoAnelPeriferico = mouseEmBarraPeriferica;
                displayPolar.mostrarCirculoPreview = mouseEmBarraCentral || mouseEmBarraPeriferica || mouseEmBarraAreaProibida;
                if (mouseEmBarraCentral) {
                    displayPolar.raioCirculoPreview = (int)grafoPolar.centro.y - barraCentral.mouseDentroDoControle.getY();
                } else if (mouseEmBarraPeriferica) {
                    displayPolar.raioCirculoPreview = (int)grafoPolar.centro.y - barraPeriferica.mouseDentroDoControle.getY();
                } else if (mouseEmBarraAreaProibida) {
                    displayPolar.raioCirculoPreview = (int)grafoPolar.centro.y - barraAreaProibida.mouseDentroDoControle.getY();
                }
                displayPolar.mostrarAlcaDoAnelCentral = mouseEmSeletor;
                displayPolar.mostrarAlcaDoAnelPeriferico = displayPolar.mostrarAlcaDoAnelCentral;
                displayPolar.repaint();
            }
        }
    }

    /**
     * Cria um display para mostrar o grafo informado.
     * @param grafo Grafo a ser mostrado.
     * @return Display, cujo tipo varia de acordo com o tipo do grafo informado.
     */   
    public GrafoDisplay criarDisplay(Grafo grafo) {
        if (grafo instanceof GrafoPolar) {
            displayPolar = new GrafoPolarDisplay((GrafoPolar)grafo);
        } else {
            displayPolar = null;
        }
        return displayPolar;
    }
    

    
}
