/*
 * GrafoPanel.java
 * Versão: 2004-03-16
 * Autor: Celmar Guimarães da Silva
 */

package grafo.forceDirected;
import java.awt.*;
import java.awt.event.*;
import java.awt.geom.Rectangle2D;
import java.util.ListIterator;
import java.util.Collection;
import java.util.Iterator;
import javax.swing.*;
import java.io.*;
import java.lang.*;
import java.util.*;
import java.awt.geom.*;

/* Observação importante: Da forma como está, o GrafoPanel não suportará a 
 * modificação do grafo adequadamente. A thread responsável pela animação não 
 * percebe a inserção e a remoção de nós e arestas, podendo agir de forma 
 * inesperada sob essas situações. Para isso, seria necessário que o GrafoPanel 
 * observasse o Grafo (via Observer) e fosse notificado de quaisquer mudanças
 * ocorridas nele.
 */


/** Cria um panel em que o grafo force-directed e seus respectivos controles são desenhados. */
public class GrafoPanel extends grafo.GrafoPanel implements Runnable {

    /** Grafo force-directed */
    protected GrafoForceDirected grafoFD;
    
    /** Thread responsável pela animação */
    private Thread animador;
    
    /** Indica se a animação está ou não acontecendo. */
    private boolean animacao;
    
    /** Cria uma nova instância GrafoPanel.
     * @param grafo Grafo a ser mostrado pelo objeto.
     */
    
    public GrafoPanel(GrafoForceDirected grafo) {
        super(grafo);
        this.grafoFD = grafo;
    }

    /** Inicia a animação do grafo. */    
    public void iniciarAnimacao() {
        animacao = true;
        if (animador==null) {    
            grafoFD.zerarHashMapDeslocamento();
            animador = new Thread(this);
            animador.start();
        }
    }    

    /** Pára a animação do grafo. */    
    public void pararAnimacao() {
        animacao = false;
    }

    /** Informa se a animação está ou não acontecendo.
     * @return True se a animação está acontecendo, false caso contrário.
     */    
    public boolean animacaoEmExecucao() {
        return (animacao);
    }
    
    // Métodos invocados pela thread
    
    /** Executa a thread responsável pela animação. */    
    public void run() {
        animar();
    }
    
    /** Efetua a iteração responsável pela animação dos nós do grafo. */    
    private void animar() {
        //while (true) {
        while (animacao) {
            if (animacao) {
                grafoFD.ajustarDimensaoDaTela(getSize());
                grafoFD.executarForceDirected();
                repaint();
            }
            try {
                Thread.sleep(100); // 0.1s
            }
            catch (InterruptedException e) {
                //break;
            }
        }
        animador=null;
    }

    //public void init() {
///        System.out.println("Init? pra que init?");
//    }
    
    
/*    
    synchronized void executaForceDirected() {
        pesoMaximoArestas = calculaPesoMaximoArestas();        

        // Parte I - Analisando o deslocamento que a força da aresta causará
        // em cada nó
        
        final double numeroPassos;
        numeroPassos = 3; // valor original
        
        // k = constante que indica qual o tamanho esperado para uma aresta de
        // peso zero no grafo.
        final double k;
        k = 100; // valor original

        for (int i = 0 ; i < grafo.arestas.size(); i++) {
            Point2D.Double p1, p2;
            Aresta a = (Aresta)grafo.arestas.get(i);
            if (!a.eEscondida() && !a.no1.eEscondido() && !a.no2.eEscondido()) {
                // v é a distância entre no1 e no2. vx e vy são seus 
                // componentes horizontal e vertical.
                // v^2 = vx^2 + vy^2
                double vx = a.no2.x - a.no1.x;
                double vy = a.no2.y - a.no1.y;
                double v = Math.sqrt(vx * vx + vy * vy);
                v = (v == 0) ? .0001 : v;

                // f é o tamanho que a aresta vai aumentar.
                double f;
                if (pesoMaximoArestas==0 || a.peso == 0) {
                    // Obs: arestas de peso zero devem se comportar como se não
                    // existissem.
                    f = 0;
                } else {
                    // Calcula o tamanho desejado para a aresta
                    double tamanhoDesejado = (double)((pesoMaximoArestas-a.peso)*k/pesoMaximoArestas);// + 1;
                    // tamanhoDesejado - v = "quanto falta para a aresta ter o tamanho desejado?"
                    f = (tamanhoDesejado - v) / numeroPassos;
                }
                // Fazendo o vetor f se inclinar na direção do vetor v.
                double fx = f * vx / v;
                double fy = f * vy / v;
                
                p1 = (Point2D.Double)d.get(a.no1);
                p2 = (Point2D.Double)d.get(a.no2);
                // p1 := p1 - f
                p1.setLocation(p1.x - fx/2, p1.y - fy/2);
                // p2 := p2 + f
                p2.setLocation(p2.x + fx/2, p2.y + fy/2);
            }
        }

        // Parte II - Afastando os nós uns dos outros
        
        for (int i = 0 ; i < grafo.nos.size(); i++) {
            No n1 = (No)grafo.nos.get(i);
            if (!n1.eEscondido() && !n1.eMovendo()) {
                // Definindo o vetor de afastamento af para o nó n1.
                // afx e afy são os componentes horizontal e vertical de af.
                double afx = 0;
                double afy = 0;
                Point2D.Double p1 = (Point2D.Double)d.get(n1);
                for (int j = 0 ; j < grafo.nos.size(); j++) {
                    if (i == j)
                        continue;   
                    No n2 = (No)grafo.nos.get(j);
                    if (!n2.eEscondido()) {
                        double vx = n1.x - n2.x;
                        double vy = n1.y - n2.y;
                        double v2 = vx * vx + vy * vy;
                        if (v2 == 0) {    
                            afx += Math.random();
                            afy += Math.random();
                        } else if (v2 < 100*100) {
                            afx += vx / v2;
                            afy += vy / v2;
                        }
                    }
                }
                
                double af = afx * afx + afy * afy; // af^2
                if (af > 0) {
                    // af^2 = afx^2 + afy^2
                    af = Math.sqrt(af);
                    // p1 := p1 + 2 af', onde af' é um vetor com a direção de af
                    // mas de tamanho 1.
                    p1.x += afx / af * 2; // cos alfa = afx/af
                    p1.y += afy / af * 2; // sin alfa = afy/af
                    // Ou seja, p1 ganha 2 unidades em tamanho.
                }
            }
        }

        // Parte III - movendo cada nó

        Dimension dim = getSize();
        double ndx, ndy;
        for (int i = 0 ; i < grafo.nos.size(); i++) {
            
            No n = (No)grafo.nos.get(i);
            if (!n.eMovendo()) {
                Point2D.Double p = (Point2D.Double)d.get(n);
                // Deslocamento máximo de 5 unidades.
                n.x += Math.max(-5, Math.min(5, p.x));
                n.y += Math.max(-5, Math.min(5, p.y));
                // Nó deve estar nos limites da tela.
                if (n.x < n.getWidth()/2) {
                    n.x = n.getWidth()/2;
                } else if (n.x > dim.width-n.getWidth()/2) {
                    n.x = dim.width-n.getWidth()/2;
                }
                if (n.y < n.getHeight()/2) {
                    n.y = n.getHeight()/2;
                } else if (n.y > dim.height-n.getHeight()/2) {
                    n.y = dim.height-n.getHeight()/2;
                }
                // Guarda metade do deslocamento como "herança" para o próximo movimento
                p.x /= 2.0;
                p.y /= 2.0;
                //p.x = Math.round(p.x*10000.0)/10000.0;
                //p.y = Math.round(p.y*10000.0)/10000.0;

            }
        }
        repaint();
    }
*/    
}
