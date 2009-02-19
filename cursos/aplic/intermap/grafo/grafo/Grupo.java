/*
 * Grupo.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */
package grafo;

import java.io.*;

/** Essa classe define um grupo de nós de um grafo.
 * Os nós que compõem o grupo não são armazenados nesta classe; ao contrário, o
 * próprio nó sabe a que grupo ele pertence.
 */
public class Grupo 
{

    /** Nome do grupo.
     */
    public String nome;

    /** Cor do grupo.
     */
    public CorNo cor;

    /** Cria uma nova instância da classe Grupo.
     * @param nome Nome do grupo.
     * @param cor Conjunto de cores dos nós desse grupo.
     */
    public Grupo(String nome, CorNo cor) {
        this.nome = nome;
        if (cor==null) {
            this.cor = new CorNo();
        } else {
            this.cor = cor;
        }
    }

    /** Transforma o grupo em uma string. No caso, simplesmente retorna o nome do grupo.
     * @return Retorna o nome do grupo.
     */
    public String toString() {
        return nome;
    }
    
}

