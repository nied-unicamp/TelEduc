/*
 * GrafoForceDirectedDisplay.java
 * Versão: 2004-08-24
 * Autor: Celmar Guimarães da Silva 
 */

package grafo.forceDirected;
import java.awt.Graphics2D;
import java.awt.event.*;
import grafo.simples.GrafoSimplesDisplay;
import grafo.*;
import java.awt.geom.Point2D;
import java.util.Iterator;
import java.util.Map;
import java.util.HashMap;
import java.util.Observer;

/** Panel em que o Grafo será mostrado. Cuida de todos os cliques efetuados
 * nessa estrutura visual.
 */

public class GrafoForceDirectedDisplay extends GrafoSimplesDisplay 
implements MouseMotionListener, MouseListener, ActionListener, ComponentListener, Observer {
    
    /** Grafo que será mostrado no display. */
    private GrafoForceDirected grafoFD;
    
    /** Mapa com as posições anteriores de cada nó do grafo. 
     * Usado para possibilitar a animação do grafo para um estado estável.
     */
    private Map posicoesAnteriores = new HashMap();
    
    /** Cria uma nova instância de GrafoDisplay.
     * @param grafo Grafo a ser desenhado no display.
     */
    public GrafoForceDirectedDisplay(GrafoForceDirected grafo) {
        super(grafo);
        grafoFD = grafo;
        grafoFD.addObserver(this);
    }
    
    /** Desenha elementos que ficarão em segundo plano.
     * @param g Objeto Graphics em que o desenho será feito.
     */
    public void desenhaElementosEmSegundoPlano(Graphics2D g) {
        // Nao existem elementos de segundo plano a serem desenhados...
    }

    /** Calcula quais as posições de cada nó que farão o grafo ficar estável
     * novamente, e faz uma animação dos nós para que alcancem essas posições.
     */
    synchronized public void animarGrafoParaEstadoEstavel() {

        if (grafoFD.autoAjusteEstaAtivo()) {
            posicoesAnteriores.clear();
            
            // Alimentar hashmap com nós e suas posições atuais
            // (que serão tidas como iniciais para o movimento)
            No n;
            PontoDuplo p;
            Iterator i;
            boolean devoAnimar = animacaoEstaHabilitada();
            
            
            if (devoAnimar) {
                i = grafoFD.nos.iterator();
                while (i.hasNext()) {
                    n = (No)i.next();
                    p = new PontoDuplo(new Point2D.Double(n.x, n.y), null);
                    posicoesAnteriores.put(n, p);
                }
            }
            
            // Procurando estabilidade
            grafoFD.procurarEstabilidade();
            grafoFD.centralizarGrafo();
            
            if (devoAnimar) {
                // Alimentando hashmap com posições finais calculadas pelo
                // algoritmo de aproximaçao de vértices, e movendo vértices
                // para suas posições antigas.
                i = grafoFD.nos.iterator();
                while (i.hasNext()) {
                    n = (No)i.next();
                    p = (PontoDuplo)posicoesAnteriores.get(n);
                    p.fim = new Point2D.Double(n.x,n.y);
                    n.x = p.inicio.x;
                    n.y = p.inicio.y;
                }
                
                int quantidadeDePassos = 25;
                long tempoDeEspera = 600/quantidadeDePassos;
                double angulo = Math.PI;
                double deltaAngulo = Math.PI/quantidadeDePassos;
                for (int passo=0; passo<quantidadeDePassos; passo++) {
                    angulo += deltaAngulo;
                    for (int indiceNo=0; indiceNo<grafoFD.nos.size(); indiceNo++) {
                        n = (No)grafoFD.nos.get(indiceNo);
                        p = (PontoDuplo)posicoesAnteriores.get(n);
                        //                    n.x += (p.fim.x - p.inicio.x)/quantidadeDePassos;
                        //                    n.y += (p.fim.y - p.inicio.y)/quantidadeDePassos;
                        n.x = p.inicio.x + (1 + Math.cos(angulo))*(p.fim.x - p.inicio.x)/2;
                        n.y = p.inicio.y + (1 + Math.cos(angulo))*(p.fim.y - p.inicio.y)/2;
                    }
                    //repaint();
                    paintImmediately(0,0, getWidth(), getHeight());
                    try {
                        Thread.sleep(tempoDeEspera);
                    } catch (InterruptedException e) {
                        System.out.println("Espera interrompida.");
                    }
                }
            } 
            
        }
    }

    /** Método que observa o grafo. Se ele for modificado, dispara o método de
     * animação dos nós.
     * @param o Elemento observado. Não é necessário neste caso.
     * @param arg Argumento passado pelo elemento observado. Não é necessário 
     * neste caso.
     */
    public void update(java.util.Observable o, Object arg) {
        animarGrafoParaEstadoEstavel();
    }

}

/** Usado nesta classe para representar um conjunto de dois pontos com 
 * coordenadas x e y do tipo double.
 */
class PontoDuplo {
    /** Ponto inicial */
    Point2D.Double inicio = null;
    
    /** Ponto final */
    Point2D.Double fim = null;
    
    /** Cria uma nova instância de PontoDuplo.
     * @param inicio Ponto inicial.
     * @param fim Ponto final.
     */
    public PontoDuplo(Point2D.Double inicio, Point2D.Double fim) {
        this.inicio = inicio;
        this.fim = fim;
    }
}