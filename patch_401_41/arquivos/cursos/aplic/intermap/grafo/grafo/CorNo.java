/*
 * Grafo.java
 * Versão: 2004-07-04
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo;

import java.awt.Color;
import java.io.*;

/** Classe que define as cores de um nó.
 */
public class CorNo 
{

    /** Cor do texto do nó que não está marcado.
     */    
    public Color corNormal             = Color.black;
    /** Cor do texto do nó que está marcado.
     */    
    public Color corMarcado            = Color.black;
    /** Cor de fundo do nó que não está marcado.
     */    
        
    public Color corDeFundoNormal    = new Color(0xA7F498);    //new Color(0x008F00);
    /** Cor de fundo do nó que está marcado.
     */    
    public Color corDeFundoMarcado   = Color.green; //new Color(0x72FF51);

    /** Cria uma nova instância de CorNo.
     */    
    public CorNo() {}

    /** Cria uma nova instância de CorNo, usando as cores de frente definidas no
     * parâmetro cor.
     * @param cor Define as cores de frente que serão usadas nesta instância de CorNo.
     */    
    public CorNo(CorNo cor) {
        corNormal  = cor.corNormal;
        corMarcado = cor.corMarcado;
   }
    
}

