/*
 * GrafoForceDirectedPanel.java
 * Versão: 2004-08-24
 * Autor: Celmar Guimarães da Silva
 */

package grafo.forceDirected;
import grafo.*;
import javax.swing.*;
import java.awt.Dimension;


/** Cria um panel em que o grafo force-directed é desenhado. */

public class GrafoForceDirectedPanel extends grafo.simples.GrafoSimplesPanel 
{

    /** Grafo force-directed */
    private GrafoForceDirected grafoFD; 
    
    /** Botão para ligar ou desligar o auto-ajuste do grafo segundo metodo
     * force-directed. */
    private JToggleButton botaoAutoAjuste;
    
//    /** Textos a serem utilizados no panel */
//    private static String[] textosDefault;

    /** Cria uma nova instância GrafoPanel.
     * @param grafo Grafo a ser mostrado pelo objeto.
     */
    public GrafoForceDirectedPanel(GrafoForceDirected grafo) {
        super(grafo);
        this.grafoFD = grafo;

    }
    
    /**
     * Cria e insere layout e controles (se existirem) no GrafoPanel.
     * @param display Display em que o grafo será mostrado.
     * @param grafo Grafo a ser mostrado.
     */
    public void inserirLayoutEControles(GrafoDisplay display, Grafo grafo) {
        setLayout(new BoxLayout(this, BoxLayout.Y_AXIS));
        add(display);
        
        botaoAutoAjuste = new JToggleButton();
        ajustarTextosNaInterface();
        botaoAutoAjuste.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                botaoAutoAjusteActionPerformed();
            }
        });

        
        JPanel botoesDaDireita = new JPanel();
        botoesDaDireita.setAlignmentX(0.0F);
        botoesDaDireita.setLayout(new java.awt.FlowLayout());
        
        botoesDaDireita.add(botaoAutoAjuste);
        

        JPanel controles = new JPanel();
        controles.setAlignmentX(0.0F);
        controles.setLayout(new java.awt.BorderLayout());
        controles.add(botoesDaDireita, java.awt.BorderLayout.EAST);
        controles.add(new JPanel(), java.awt.BorderLayout.CENTER);
        controles.setMaximumSize(new Dimension(10000,50));
        
        add(controles);

        
    }
    
    /** Modifica os textos de menus e diálogos.
     * @param textosNovos Vetor com todos os textos que irão substituir os 
     * textos atuais, tanto no panel quanto em seu display.
     */
    public void modificarTextos(java.util.List textosNovos) {
        super.modificarTextos(textosNovos);
        ajustarTextosNaInterface();
    }

    /** Ajusta os textos de elementos da interface, segundo os textos atualmente
     * disponíveis.
     */    
    private void ajustarTextosNaInterface() {
        // Textos 0,1,2: Iniciar/Parar auto-ajuste  
        botaoAutoAjuste.setText(textos[0]+"/"+textos[1]+" "+textos[2]);
    }

    /** Método executado quando o botão de auto-ajuste do grafo foi clicado.
     */
    private void botaoAutoAjusteActionPerformed() {
        if (grafoFD.autoAjusteEstaAtivo()) {
            grafoFD.desativarAutoAjuste();
        } else {
            grafoFD.ativarAutoAjuste();
        }
        
    }
    
    /** Define quais textos serão utilizados pelo panel. */
    public void definirTextosDefault() {
        textos = new String[3];
        textos[0] = "Iniciar";
        textos[1] = "Parar";
        textos[2] = "Auto-ajuste";
    }


    /**
     * Cria um display para mostrar o grafo informado.
     * @param grafo Grafo a ser mostrado.
     * @return Display, cujo tipo varia de acordo com o tipo do grafo informado.
     */    
    public GrafoDisplay criarDisplay(Grafo grafo) {
        GrafoDisplay display;
        if (grafo instanceof GrafoForceDirected) {
            display = new GrafoForceDirectedDisplay((GrafoForceDirected)grafo);
        } else {
            display = null;
        }
        return display;
    }

}


    

