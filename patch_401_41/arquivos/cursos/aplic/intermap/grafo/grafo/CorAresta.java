/*
 * CorAresta.java
 * Versão: 2004-07-04
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo;

import java.awt.Color;
import java.io.*;

/** Classe que define cores das arestas.
*/
public class CorAresta 
{
    
    /** Cor da aresta quando ela não está marcada.
     */    
    public Color corNormal  = new Color(0x00008F);
    //public Color corNormal  = new Color(0x000000);
    
    /** Cor da aresta quando ela está marcada.
     */    
    public Color corMarcada = Color.blue;
    //public Color corMarcada = new Color(0xf00000);
    
}