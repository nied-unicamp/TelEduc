/*
 * StringInfo.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo;

import javax.swing.*;
import java.io.*;

/** Classe que implementa Info para criar objetos que armazenem informação 
 * textual a ser usada por um nó ou uma aresta do grafo.
 */
public class StringInfo implements Info 
{
    
    /** Texto que esta classe armazena.
     */
    public String texto="";

    /** Cria uma nova instância de StringInfo. */
    public StringInfo() {
        texto = "";
    }
    
    /** Cria uma nova instância de StringInfo.
     * @param texto Informação textual (String) a ser armazenada.
     */
    public StringInfo(String texto) {
        this.texto = texto;
    }
    
//    /** Informa se um objeto especificado é igual ao objeto em questão.
//     * Dois objetos StringInfo são iguais se apresentam o mesmo valor da
//     * propriedade texto.
//     * @param obj Objeto a ser comparado.
//     * @return True se o objeto for igual, false caso contrário.
//     */
//    public boolean equals(Object obj) {
//        if (obj.getClass() != this.getClass()) return false;
//        return ((StringInfo)obj).texto.equals(texto);
//    }
    
    /** Retorna string que representa a informação.
     * @return Retorna string que represente a informação.
     */
    public String toString() {
        return texto;
    }
    
    /** Retorna representação visual de informação.
     * @return Retorna um JTextPane contendo a informação armazenada por este objeto.
     */
    public JComponent retornarInformacao() {
        JTextPane pane = new JTextPane();
        pane.setEditable(false);
        pane.setText(texto);
        return pane;
    }
    
}

