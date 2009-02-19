/*
 * AlgoritmoMDS.java
 * Versão: 2004-08-17
 * Autor: Celmar Guimarães da Silva
 */

package grafo.forceDirected;

/** Interface que representa os algoritmos de MDS utilizados com os grafos
 * (algoritmos force-directed)
 */
public interface AlgoritmoMDS {
    
    /** Prepara o algoritmo para ser executado. */
    public void preparar();
    
    /** Executa um passo do algoritmo. */
    public void executarPasso();
    
}
