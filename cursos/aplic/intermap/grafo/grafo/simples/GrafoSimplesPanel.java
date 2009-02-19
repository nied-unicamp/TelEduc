/*
 * GrafoSimplesPanel.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.simples;
import javax.swing.*;
import java.io.*;
import grafo.*;


/** Cria um panel em que o grafo e seus respectivos controles são desenhados. */
public class GrafoSimplesPanel extends GrafoPanel {
    
    /** Grafo a ser desenhado. */
    private GrafoSimples grafoSimples;
    
//    private GrafoSimplesDisplay display = null;
    
    /** Cria uma nova instância GrafoPanel.
     * @param grafo Grafo a ser mostrado pelo objeto.
     */
    public GrafoSimplesPanel(GrafoSimples grafo) {
        super(grafo);
        grafoSimples = grafo;
        
    }
    
    /**
     * Cria um display para mostrar o grafo informado.
     * @param grafo Grafo a ser mostrado.
     * @return Display, cujo tipo varia de acordo com o tipo do grafo informado.
     */    
    public GrafoDisplay criarDisplay(Grafo grafo) {
        GrafoDisplay display;
        if (grafo instanceof GrafoSimples) {
            display = new GrafoSimplesDisplay((GrafoSimples)grafo);
        } else {
            display = null;
        }
        return display;
    }

    /**
     * Cria e insere layout e controles (se existirem) no GrafoPanel.
     * @param display Display em que o grafo será mostrado.
     * @param grafo Grafo a ser mostrado.
     */
    public void inserirLayoutEControles(GrafoDisplay display, Grafo grafo) {
        setLayout(new BoxLayout(this, BoxLayout.X_AXIS));
        add(display);
    }
    
}
