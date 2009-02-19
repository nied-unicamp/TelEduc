/*
 * AlgoritmoMDSAdaptado.java
 * Versão: 2004-08-17
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.forceDirected;

import java.util.Collection;
import grafo.simples.NoSimples;
import java.awt.geom.Point2D;
import java.util.Iterator;
import java.awt.Dimension;

/** Classe responsável por implementar uma variação do algoritmo force-directed
 * (MDS) de Eades. A adaptação feita faz com que o tamanho das arestas seja 
 * inversamente proporcional aos seus pesos; ou seja, quanto menor o peso da 
 * aresta, maior tende a ser o seu tamanho, e vice-versa.
 *
 * Referência bibliográfica:
 * Fruchterman e Reingold (1991). "Graph Drawing by Force-directed Placement".
 * Software - Practice and Experience, Vol. 21 (11), 1129-1164.
 */
public class AlgoritmoMDSAdaptado implements AlgoritmoMDS {
    
    /** Constante que reduz a distância percorrida por uma única iteração do
     * algoritmo do Intermap. 
     * Obs: O valor originalmente utilizado na ferramenta Intermap do TelEduc é 3.
     */
    private final static double numeroPassos = 3;
    
    /** Peso máximo do conjunto atual de arestas do grafo. */
    private int pesoMaximoArestas;
    
    /** Peso mínimo do conjunto atual de arestas do grafo. */
    private int pesoMinimoArestas;
    
    /** Grafo utilizado pelo algoritmo. */
    private GrafoForceDirected grafo;
    
    /**
     * Cria uma nova instância de AlgoritmoMDSAdaptado
     * @param grafo Grafo a ser utilizado pelo algoritmo.
     */
    public AlgoritmoMDSAdaptado(GrafoForceDirected grafo) {
        this.grafo = grafo;
    }
    
    /** Prepara o algoritmo para ser executado. */
    public void preparar() {
        if (grafo.retornarQuantidadeDeNos() - grafo.retornarEscondidos().size() >1) {
            pesoMaximoArestas = grafo.retornarPesoMaximoArestas();
            pesoMinimoArestas = grafo.retornarPesoMinimoArestas();
        }
    }

    /** Executa um passo do algoritmo. */
    public void executarPasso() {
        if (grafo.retornarQuantidadeDeNos() - grafo.retornarEscondidos().size() >1) {
//            pesoMaximoArestas = grafo.retornarPesoMaximoArestas();
//            pesoMinimoArestas = grafo.retornarPesoMinimoArestas();
            
            calcularForcaDeAtracaoDasArestas(grafo.arestas);
            calcularForcaDeRepulsaoDosNos(grafo.nos);
            aplicarForcasCalculadas(grafo.nos);
        }
    }
    
    
    
    /** Calcula as forças de atração que as arestas informadas exercem no grafo, 
     * e as armazena no hash de deslocamento acumulado.
     * @param arestas Conjunto de arestas cujas forças de atração devem ser 
     * calculadas.
     */    
    void calcularForcaDeAtracaoDasArestas(Collection arestas) {
        calcularForcaDeAtracaoDasArestas(arestas,null);
    }
        
    /** Calcula as forças de atração que as arestas informadas exercem no nó
     * especificado, e as armazena no hash de deslocamento acumulado.
     * @param arestas Conjunto de arestas cujas forças de atração devem ser 
     * calculadas.
     * @param n Nó sobre o qual as forças de atração das arestas devem ser 
     * calculadas. Se n == null, as forças são calculadas para todos os nós.
     */    
    void calcularForcaDeAtracaoDasArestas(Collection arestas, NoSimples n) {
        double distancia, distanciaX, distanciaY;
        double f, fx, fy;
        ArestaForceDirected a;
        Point2D.Double p1, p2;
        for (Iterator it = arestas.iterator(); it.hasNext(); ) {
            a = (ArestaForceDirected)it.next();
            if (a!=null) {
                if (!a.eEscondida() && !a.no1.eEscondido() && !a.no2.eEscondido()) {
                    
                    // v é a distância entre no1 e no2. vx e vy são seus
                    // componentes horizontal e vertical.
                    // v^2 = vx^2 + vy^2
                    distanciaX = a.no2.x - a.no1.x;
                    distanciaY = a.no2.y - a.no1.y;
                    distancia = Math.sqrt(distanciaX * distanciaX + distanciaY * distanciaY);
                    
                    if (distancia!=0) {
                        a.calcularTamanhoDesejado(grafo.tamanhoMaximoAresta, grafo.tamanhoMinimoAresta, pesoMaximoArestas, pesoMinimoArestas);
                        // Na verdade, somente seria preciso calcular isso se houvesse mudança de arestas ou de nós.
                        // É um ponto considerável onde o programa pode ser otimizado.
                        
                        // f é o tamanho que a aresta vai aumentar.
                        f = (a.retornarTamanhoDesejado() - distancia) / numeroPassos;
                        
                        // Fazendo o vetor f se inclinar na direção do vetor v.
                        fx = f * distanciaX / distancia;
                        fy = f * distanciaY / distancia;
                        
                        p1 = (Point2D.Double)grafo.deslocamentoAcumulado.get(a.no1);
                        p2 = (Point2D.Double)grafo.deslocamentoAcumulado.get(a.no2);
                        if (p1!=null && p2!=null) {
                            // O if é necessário porque em situações de concorrência o HashMap pode não conter determinadas arestas.

                            if (n==null) {
                                // p1 := p1 - f
                                p1.setLocation(p1.x - fx/2, p1.y - fy/2);
                                // p2 := p2 + f
                                //p2.setLocation(p2.x + fx/2, p2.y + fy/2);
                                p2.setLocation(p2.x + (fx - fx/2), p2.y + (fy - fy/2));
                            } else if (n==a.no1) {
                                p1.setLocation(p1.x - fx/2, p1.y - fy/2);
                            } else if (n==a.no2) {
                                p2.setLocation(p2.x + (fx - fx/2), p2.y + (fy - fy/2));
                            }
                        }
                        //else {
                        //System.out.print("não estão no hashmap...");
                        //System.out.println(a.no1.nome+"-->"+a.no2.nome);
                        //}
                    }
                }
            }
        }
    }
    
    /** Calcula as forças de repulsão que os nós de um conjunto exercem entre si,
     * e as armazena no hash de deslocamento acumulado.
     * @param nos Conjunto de nós a ser considerado.
     */    
    void calcularForcaDeRepulsaoDosNos(Collection nos) {
        NoSimples n1;
        for (Iterator i = nos.iterator(); i.hasNext(); ) {
            n1 = (NoSimples)i.next();
            calcularForcaDeRepulsaoSofrida(n1, nos);
        }
    }
    
    /** Calcula as forças de repulsão sofridas que um conjunto de nós impõem a
     * um nó específico, e as armazena no hash de deslocamento acumulado.
     * @param n1 Nó sobre o qual as forças atuam.
     * @param nos Conjunto de nós cujas forças de repulsão devem ser 
     * consideradas.
     */    
    void calcularForcaDeRepulsaoSofrida(NoSimples n1, Collection nos) {
        
        double afx, afy;
        NoSimples n2;
        double distancia2;
        double distancia, distanciaX, distanciaY;
        double intensidadeDaRepulsao;
        Point2D.Double p1;
        
        if (!n1.eEscondido() && !n1.eMovendo()) {
            // Definindo o vetor de afastamento af para o nó n1.
            // afx e afy são os componentes horizontal e vertical de af.
            afx = 0;
            afy = 0;
            
            p1 = (Point2D.Double)grafo.deslocamentoAcumulado.get(n1);
            if (p1!=null) {
                
                for (Iterator i = nos.iterator(); i.hasNext();) {
                    n2 = (NoSimples)i.next();
                    if (n1.equals(n2)) {
                        continue;
                    }
                    if (!n2.eEscondido()) {
                        // Calculando direção do afastamento
                        distanciaX = n1.x - n2.x;
                        distanciaY = n1.y - n2.y;
                        distancia2 = distanciaX * distanciaX + distanciaY * distanciaY;
                        distancia = Math.sqrt(distancia2);
                        if (distancia2 == 0) {
                            // Os nós estão na mesma posição. Devem ser afastados em qualquer direção.
                            p1.x += Math.random();
                            p1.y += Math.random();
                        } else {                            
                            intensidadeDaRepulsao = (distancia > grafo.distanciaMinimaEntreNos ? 0 : grafo.distanciaMinimaEntreNos - distancia) / numeroPassos;
                            p1.x += intensidadeDaRepulsao * distanciaX / distancia; // cos alfa = afx/af
                            p1.y += intensidadeDaRepulsao * distanciaY / distancia; // sin alfa = afy/af
                        }
                    }
                }
                
                
            }
        }
        
    }
    
    /** Aplica ao grafo todas as forças calculadas, ou seja, todos os 
     * deslocamentos acumulados pelos cálculos de forças de atração e de 
     * repulsão.
     * Além disso, guarda metade do deslocamento atual para a próxima iteração, 
     * como se tentasse manter a velocidade (por inércia) mas tivesse um atrito
     * causando desaceleração.
     * Também reduz o deslocamento máximo de cada nó a um limite máximo
     * estipulado pela variável deslocamentoMaximo do grafo em questão.
     * @param nos Conjunto de nós nos quais os deslocamentos devem ser 
     * aplicados.
     */    
    void aplicarForcasCalculadas(Collection nos) {
        Point2D.Double p;
        NoSimples n;
        Dimension dimensaoDaTela = grafo.retornarDimensaoDaTela();
        
        
        for (Iterator i = nos.iterator(); i.hasNext();) {
            n = (NoSimples)i.next();
            p = (Point2D.Double)grafo.deslocamentoAcumulado.get(n);
            if (p!=null) {
                if (!n.eMovendo()) {
                    // Controlando deslocamento máximo .
                    n.x += Math.max(-grafo.deslocamentoMaximo, Math.min(grafo.deslocamentoMaximo, p.x));
                    n.y += Math.max(-grafo.deslocamentoMaximo, Math.min(grafo.deslocamentoMaximo, p.y));
                    // Nó deve estar nos limites da tela.
                    grafo.posicionaNoNosLimites(n, dimensaoDaTela);
                    // Guarda metade do deslocamento como "herança" para o próximo movimento
                    p.x /= 2.0;
                    p.y /= 2.0;
                } else {
                    p.x=0;
                    p.y=0;
                }
            }
        }
        
    }
    
}
