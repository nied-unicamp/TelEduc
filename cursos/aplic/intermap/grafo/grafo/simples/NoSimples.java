/*
 * NoSimples.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */
package grafo.simples;

import java.util.*;
import java.io.*;
import java.awt.*;

import grafo.*;

/** Classe para armazenar um nó de um grafo.
 * Da forma como está implementada, essa classe não sabe desenhar o nó.
 * Um elemento desta classe não conhece o grafo ao qual pertence, embora
 * saiba quais arestas incidem sobre ele.
 */
public class NoSimples extends No {

    /** Cria uma nova instância de NoSimples, criando um nó sem peso nem informação 
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     */
    public NoSimples(String nome) {
        this(nome, 0, null);
    }

    /** Cria uma nova instância de NoSimples, criando um nó com peso mas sem informação
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     * @param peso Peso do nó.
     */
    public NoSimples(String nome, int peso) {
        this(nome, peso, null);
    }

    /** Cria uma nova instância de NoSimples, criando um nó sem peso mas com informação
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     * @param info Informação associada ao nó.
     */
    public NoSimples(String nome, Info info) {
        this(nome, 0, info);
    }    
    /** Cria uma nova instância de NoSimples, criando um nó com peso e com informação
     * associada.
     * @param nome Nome do nó (nome mostrado no nó).
     * @param peso Peso do nó.
     * @param info Informação associada ao nó.
     */
    public NoSimples(String nome, int peso, Info info) {
        super(nome, peso, info);
    }
        

    
}