/*
 * Info.java
 * Versão: 2004-07-04
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */
package grafo;

import javax.swing.*;
import java.io.*;

/** Interface que estabelece métodos padrões para gerenciamento de informações
 * sobre nós ou arestas de um grafo.
 */
public interface Info 
{

//    /** Informa se um objeto especificado é igual ao objeto em questão.
//     * @param obj Objeto a ser comparado.
//     * @return True se o objeto for igual, false caso contrário.
//     */
//    public boolean equals(Object obj);
//
//    /** Retorna string que representa a informação.
//     * @return Retorna string que represente a informação.
//     */
//    public String toString();

    /** Retorna componente visual com representação visual de informação.
     * @return Retorna um JComponent que contém a representaçao visual de informação.
     * Se a informação for um texto, pode retornar um panel contendo esse texto, por
     * exemplo. Em outro exemplo, a informação pode ser uma imagem ou um gráfico, e
     * o panel retornado pode representar esses elementos.
     */
    public JComponent retornarInformacao();

}

