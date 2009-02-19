/*
 * ArestaSimples.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.simples;

import java.io.*;
import java.awt.*;
import grafo.*;

/** Classe básica para criar arestas de um grafo.
 */
public class ArestaSimples extends Aresta 
//implements GerenciadorXGMML  
{
    BasicStroke normalStroke = new BasicStroke();
    BasicStroke boldStroke = new BasicStroke(2.0f);
    
    /** Construtor de aresta sem peso (peso = 0) e sem informação (info = null).
     * @param no1 Primeiro nó da aresta
     * @param no2 Segundo nó da aresta
     */
    public ArestaSimples(NoSimples no1, NoSimples no2) {
        this(no1, no2, 0, null);
    }
    
    /** Construtor de aresta sem informação (info = null) e com peso.
     * @param no1 Primeiro nó da aresta.
     * @param no2 Segundo nó da aresta.
     * @param peso Peso da aresta. 
     */
    public ArestaSimples(NoSimples no1, NoSimples no2, int peso) {
        this(no1, no2, peso, null);
    }
    
    /** Construtor de aresta com informação e sem peso (peso = 0).
     * @param no1 Primeiro nó da aresta.
     * @param no2 Segundo nó da aresta.
     * @param info Informação relacionada à aresta.
     */
    public ArestaSimples(NoSimples no1, NoSimples no2, Info info) {
        this(no1, no2, 0, info);
    }
    
    /** Construtor de aresta com informação e peso.
     * @param no1 Primeiro nó da aresta.
     * @param no2 Segundo nó da aresta.
     * @param peso Peso da aresta. 
     * @param info Informação relacionada à aresta.
     */
    public ArestaSimples(NoSimples no1, NoSimples no2, int peso, Info info) {
        super(no1,no2,peso,info);
    }

    /** Calcula a coordenada do centro do círculo que compõem a auto-aresta 
     * de um nó informado.
     * @param no Nó do qual se deseja saber o centro da auto-aresta.
     * @param g Grafo do qual o nó faz parte.
     * @return Retorna o ponto no centro da auto-aresta.
     */
    public Point retornarCentroAutoAresta(No no, Grafo g) {
        return new Point((int)no.x, (int)(no.y-no.getHeight()*3/4));
    }
    
    /** Desenha aresta para o grafo.
     * @param grafo Grafo ao qual a aresta pertence.
     * @param g Objeto em que a aresta será desenhada.
     */

    public void desenhar(Grafo grafo, Graphics g) {
        if (grafo instanceof GrafoSimples) {
            Graphics2D g2 = (Graphics2D) g;
            // Desenhar aresta mais gorda se ela é atingida.
            if (eAtingida()) {
                g2.setStroke(boldStroke);
            }
            g.setColor(retornarCor(grafo));
            if (no1 != no2) {
                g.drawLine((int)no1.x, (int)no1.y, (int)no2.x, (int)no2.y);
            } else {
                desenharArestaParaOProprioNo(grafo, g, no1);
            }
            g2.setStroke(normalStroke);
        }
    }
   
}


