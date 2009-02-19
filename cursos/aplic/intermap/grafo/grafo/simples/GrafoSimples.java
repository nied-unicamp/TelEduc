/*
 * GrafoSimples.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.simples;

import grafo.*;
import java.util.Iterator;
import java.awt.Dimension;

/** Classe genérica para representar grafos.
 */
public class GrafoSimples extends Grafo 
{
    
//    /** Cria uma nova instância de Grafo.
//     */
//    public GrafoSimples() {
//        super();
//    }
 
    /**
     * Adiciona ao grafo uma aresta com peso e informação.
     * @param no1 Primeiro nó da aresta a ser adicionada.
     * @param no2 Segundo nó da aresta a ser adicionada.
     * @param peso Peso da aresta a ser adicionada.
     * @param info Informação sobre a aresta a ser adicionada.
     * @param registrarModificacao Informa se a ação de adicionar a aresta deve ser considerada como modificação
     * do grafo ou não.
     */
    synchronized public void adicionarAresta(No no1, No no2, int peso, Info info, boolean registrarModificacao) {
        if (no1 instanceof NoSimples && no2 instanceof NoSimples) {
            ArestaSimples aresta = new ArestaSimples((NoSimples)no1, (NoSimples)no2, peso, info);
            adicionarAresta(aresta, registrarModificacao);
        }
    }

    /** Adiciona ao grafo uma aresta informada.
     * @param aresta Aresta a ser adicionada ao grafo.
     * @param registrarModificacao Informa se a ação de adicionar a aresta deve ser considerada como modificação
     * do grafo ou não.
     */
    synchronized public void adicionarAresta(Aresta aresta, boolean registrarModificacao) {
        if (aresta instanceof ArestaSimples) {
            super.adicionarAresta(aresta, registrarModificacao);
        }
    }
    
    /**
     * Adiciona ao grafo um nó especificado.
     * @param registrarModificacao Informa se a ação de adicionar o nó deve ser considerada como modificação do
     * grafo ou não.
     * @param no Nó a ser adicionado.
     */
    synchronized public void adicionarNo(No no, boolean registrarModificacao) {
        if (no instanceof NoSimples) {
            super.adicionarNo(no, registrarModificacao);
        }
    }
    /** Centraliza o grafo segundo a dimensão atual da tela registrada no grafo.
     */
    public void centralizarGrafo() {
        centralizarGrafo(retornarDimensaoDaTela());
    }
    
    /**
     * Centraliza o grafo segundo a dimensão informada.
     * @param d Dimensão a ser considerada para centralização.
     */
    public void centralizarGrafo(Dimension d) {
        
        if (d!= null) {
            // Descobre quais as abscissas e ordenadas máximas e mínimas ocupadas
            // pelo grafo.
            double maxX=0,maxY=0,minX=0,minY=0;
            boolean primeiro = true;
            for (Iterator i = nos.iterator(); i.hasNext(); ) {
                NoSimples no = (NoSimples)i.next();
                if (!no.eEscondido()) {
                    if (primeiro) {
                        maxX = no.x;
                        maxY = no.y;
                        minX = no.x;
                        minY = no.y;
                        primeiro = false;
                    } else {
                        maxX = (no.x>maxX ? no.x : maxX);
                        maxY = (no.y>maxY ? no.y : maxY);
                        minX = (no.x<minX ? no.x : minX);
                        minY = (no.y<minY ? no.y : minY);
                    }
                }
            }
            
            // Propõe um deslocamento dos nós na horizontal e na vertical
            
            int tamanhoX = (int)(maxX-minX);
            int tamanhoY = (int)(maxY-minY);
            int deltaX = (int)((d.width - tamanhoX)/2 - minX);
            int deltaY = (int)((d.height - tamanhoY)/2 - minY);
            
            // Efetua o deslocamento proposto
            
            for (Iterator i = nos.iterator(); i.hasNext(); ) {
                NoSimples no = (NoSimples)i.next();
                if (!no.eEscondido()) {
                    no.x += deltaX;
                    no.y += deltaY;
                }
            }
            
        } else {
            System.out.println("Não foi possível centralizar o grafo.");
        }
    }
    
}