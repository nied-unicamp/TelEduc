/*
 * AlgoritmoMDSChalmers.java
 * Versão: 2004-08-24
 * Autor: Celmar Guimarães da Silva
 */


package grafo.forceDirected;

import java.util.Map;
import java.util.HashMap;
import java.util.Iterator;
import java.util.ListIterator;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashSet;
import java.awt.geom.Point2D;
import grafo.simples.NoSimples;
import grafo.forceDirected.ArestaForceDirected;

/** Classe que usa o algoritmo de MDS de [Chalmers1996] para implementar o 
 * modelo de aproximação/afastamento de nós baseado em forças.
 * 
 * Referência Bibliográfica:
 * [Chalmers1996] Chalmers, M. "A Linear Iteration Time Layout Algorithm for 
 * Visualising High-Dimensional Data". Proceedings of Visualization '96, 
 * p.127-132, San Francisco, 1996.
 */
public class AlgoritmoMDSChalmers implements AlgoritmoMDS {

    /** Valor vMax do algoritmo de [Chalmers1996]. */
    private int vMax = 5; ///original = 5;
    
    /** Valor sMax do algoritmo de [Chalmers1996]. */
    private int sMax = 5; // original = 10;
    
    /** Mapa em que cada vértice i apresenta uma lista de vértices associados a 
     * ele. Equivale ao conjunto V do algoritmo de [Chalmers1996]
     */
    private Map vizinhos;
    
    /** Para cada lista de arestas armazenada em "vizinhos", armazena o maior 
     * "tamanho desejado" das arestas da lista.
     */
    private Map tamanhoMaximoParaVizinhos;
    
//    /** Contador de iterações para definir quando o stress poderia ser medido. */
//    private int contadorDeIteracoes = 0;
    
    
    /** Peso máximo do conjunto atual de arestas do grafo. */
    private int pesoMaximoArestas;
    
    /** Peso mínimo do conjunto atual de arestas do grafo. */
    private int pesoMinimoArestas;

    /** Instância do algoritmo force-directed básico, do qual o algoritmo de
     * Chalmers se utiliza.
     */
    private AlgoritmoMDSAdaptado mdsAdaptado;
    
    /** Grafo utilizado pelo algoritmo. 
     */
    private GrafoForceDirected grafo;
    
    /**
     * Cria uma nova instância de AlgoritmoMDSChalmers
     * @param grafo Grafo a ser utilizado pelo algoritmo.
     */
    public AlgoritmoMDSChalmers(GrafoForceDirected grafo) {
        this.grafo = grafo;
        mdsAdaptado = new AlgoritmoMDSAdaptado(grafo);        
    }
    
    /** Prepara o algoritmo para ser executado. */
    synchronized public void preparar() {
        mdsAdaptado.preparar();
        // Criar, para cada no, uma entrada nos HashMaps adequados,
        // que representam seus vizinhos (V) e a distância do vizinho mais
        // distante (maxDist);
        vizinhos = new HashMap();
        vizinhos.clear();
        tamanhoMaximoParaVizinhos = new HashMap();
        tamanhoMaximoParaVizinhos.clear();        
        
        for (Iterator i = grafo.nos.iterator(); i.hasNext(); ) {
            NoSimples no = (NoSimples)i.next();
            vizinhos.put(no, new ArrayList());
            tamanhoMaximoParaVizinhos.put(no, null);
        }
        
        pesoMaximoArestas = grafo.retornarPesoMaximoArestas();
        pesoMinimoArestas = grafo.retornarPesoMinimoArestas();
        int numNos = grafo.retornarQuantidadeDeNos();        
        if (numNos<vMax+sMax) {
            vMax = 1;
            sMax = numNos/2;
        }
        

//        contadorDeIteracoes = 0;
    }

    /** Executa um passo do algoritmo. */
    synchronized public void executarPasso() {
        // Executa 1 interaçao do algoritmo, passando por todos os nos.
        // Algoritmo:
        // Para cada nó i,
        // - Iniciar o conjunto S como vazio; (S = secundarios)
        // - Repita, até que S tenha sMax elementos:
        //   - Sortear um nó j do conjunto de vértices (j<>i)
        //   - Calcular a distancia desejada d(i,j) entre os nós.
        //   - Se d(i,j)<maxDist(i), ou se maxDist(i) não estiver definida:
        //     - Inserir j em V(i), de maneira ordenada com relação às distâncias
        //       dos elementos ja inseridos em V.
        //     - Se V(i) tiver mais que vMax elementos, podar V(i) em vMax elementos.
        //     - Atualizar maxDist(i) = d(i,j);
        //   - Caso contrário:
        //     - Inserir j em S.
        // - Calcular as forças de atração e repulsão considerando apenas os
        //   vértices e arestas dos conjuntos V e S.
        // Estando todas as forças calculadas, aplicar as forças.
        
        Collection secundarios = new ArrayList();
        int numNos = grafo.retornarQuantidadeDeNos();
        if (numNos<=1) {
            return; // nao há o que fazer neste caso...
        }
        
        NoSimples i;
        NoSimples j;
        ArestaForceDirected ij;
        Double maxDist = null;
        boolean inserirEmVizinhos;
        Collection arestasDeSecundarios = new HashSet();
        
        double tamanhoDesejado;
        for (int x = 0 ; x < numNos; x++) {
            secundarios.clear();            
            arestasDeSecundarios.clear();
            i = (NoSimples)grafo.nos.get(x);
            
            java.util.List nosJ = new ArrayList();
            nosJ.addAll(grafo.nos);

            nosJ.remove(i);
            // Preenchendo listas vizinhos e secundários de i.
            while (secundarios.size()<sMax && !nosJ.isEmpty()) {
                // Sorteando j dentre todos os nós do grafo
                do {
                    j = (NoSimples)nosJ.get((int)(Math.random()*nosJ.size()));
                } while (j== null || j == i);
                nosJ.remove(j);
                
                // Encontrar a aresta i<-->j, se houver
                
                ij = (ArestaForceDirected)grafo.buscarAresta(i,j);
                // essa rotina precisaria ser transformada em O(1) para não prejudicar o algoritmo.
                
                tamanhoDesejado = distanciaDesejadaEntreNos(i,j,ij);
                
                maxDist = (Double)tamanhoMaximoParaVizinhos.get(i);
                
                inserirEmVizinhos = (maxDist == null || tamanhoDesejado < maxDist.doubleValue());

                if (inserirEmVizinhos) {
                    inserirVizinhoDeI(i, j, ij);
                } else {
                    // Inserindo j em Secundarios
                    if (!secundarios.contains(j)) {
                        secundarios.add(j); // Logo, secundarios é um ArrayList de nós.
                        if (ij!=null) {
                            arestasDeSecundarios.add(ij);
                        }
                    }
                }
                
            }
            
            
            // forcas de repulsao em i: de um lado, o no i; de outro os nos de vizinhos + secundarios
            Collection nosConsiderados = new HashSet();
            nosConsiderados.addAll(secundarios);
            ArrayList vizinhosDeI = (ArrayList)vizinhos.get(i);
            nosConsiderados.addAll(vizinhosDeI);
            mdsAdaptado.calcularForcaDeRepulsaoSofrida(i, nosConsiderados);
            
            // forcas de atraçao em i: todas as arestas de Secundarios mais as que atingem os vertices vizinhos de i.
            Collection arestasConsideradas = new HashSet();
            arestasConsideradas.addAll(arestasDeSecundarios);
            NoSimples n;
            ArestaForceDirected a;
            for (Iterator it = vizinhosDeI.iterator(); it.hasNext(); ) {
                n = (NoSimples)it.next();
                a = (ArestaForceDirected)grafo.buscarAresta(i,n);
                if (a!=null) {
                    arestasConsideradas.add(a);
                }
            }
            mdsAdaptado.calcularForcaDeAtracaoDasArestas(arestasConsideradas,i);
            
        }
        
        //aplicando forcas
        mdsAdaptado.aplicarForcasCalculadas(grafo.nos);
        
    }

    /** Insere nó j como vizinho de i (conjunto V de i) 
     * @param i Nó em cujo conjunto de vizinhos se pretende inserir o nó j.
     * @param j Nó a ser inserido no conjunto de vizinhos de i
     * @param ij Aresta que ligue i e j, se houver.
     */    
    private void inserirVizinhoDeI(NoSimples i, NoSimples j, ArestaForceDirected ij) {
        if (i!=null && j!=null) {
            // Inserindo j como vizinho de i, ou seja, inserindo j no conjunto V.

            ArrayList vizinhosDeI = (ArrayList)vizinhos.get(i);
            if (!vizinhosDeI.contains(j)) {
                double distanciaEntreIeJ = distanciaDesejadaEntreNos(i,j,ij);
                Double maxDist = (Double)tamanhoMaximoParaVizinhos.get(i);
                boolean inseriu = false;                
                if (vizinhosDeI.isEmpty()) {
                    vizinhosDeI.add(j);
                    inseriu = true;
                } else {
                    // Rotina de inserçao
                    ListIterator li = vizinhosDeI.listIterator();
                    ArestaForceDirected a;
                    NoSimples n;
                    while (li.hasNext() && !inseriu) {
                        n = (NoSimples)li.next();
                        a = (ArestaForceDirected)grafo.buscarAresta(i,n);
                        if (distanciaEntreIeJ <= distanciaDesejadaEntreNos(i,n,a)) {
                            li.previous();
                            li.add(j);
                            inseriu = true;
                        }
                    }
                }
                while (vizinhosDeI.size() > vMax) {
                    vizinhosDeI.remove(vMax);
                }
                if (inseriu) {
                    
                    // Pegar o último da lista de VizinhosDeI ---> k
                    // Calcular a distancia desejada entre i e k
                    // maxDist(i) <-- k
                    NoSimples k = (NoSimples)vizinhosDeI.get(vizinhosDeI.size()-1);
                    ArestaForceDirected ik = (ArestaForceDirected)grafo.buscarAresta(i,k);
                    double distanciaEntreIeK = distanciaDesejadaEntreNos(i,k,ik);
                    maxDist = new Double(distanciaEntreIeK);
                    tamanhoMaximoParaVizinhos.put(i, maxDist);
                }
                
            }
            
        }
    }
    
    /** Calcula a distância desejada entre dois nós i e j.
     * Se existir aresta entre i e j, o tamanho desejado dessa aresta é a 
     * distância desejada entre os nós.
     * Caso não exista aresta entre i e j, assume-se como distância desejada
     * a própria distância atual entre eles (ou a distância mínima entre nós, 
     * se esta for maior).
     * Obs: Não confere se i e j são nulos.
     *
     * @param i Nó.
     * @param j Nó.
     * @param ij Aresta que ligue i e j, se houver.
     * @return Distância desejada entre i e j
     */
    private double distanciaDesejadaEntreNos(NoSimples i, NoSimples j, ArestaForceDirected ij) { 
        double distanciaDesejada;
        if (ij == null) {
            // Aresta não existe. Considerar que a distancia desejada é
            // a distancia minima desejada entre os nós pela forca de repulsao,
            // ou a distância atual entre eles (o que for maior).
            // Ou seja, "se estão suficientemente longe um do outro, está ok; 
            // senão, tratem de ficar longe um do outro".
            distanciaDesejada = Math.max(grafo.distanciaMinimaEntreNos, Point2D.distance(i.x,i.y,j.x,j.y));
        } else {
            // Calcular distancia desejada
            ij.calcularTamanhoDesejado(grafo.tamanhoMaximoAresta, grafo.tamanhoMinimoAresta, pesoMaximoArestas, pesoMinimoArestas);
            distanciaDesejada = ij.retornarTamanhoDesejado();
        }
        return(distanciaDesejada);        
    }
}

