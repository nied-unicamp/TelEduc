/*
 * Gerenciador.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;
import java.io.*;
/**
 * Classe com métodos estáticos para leitura e escrita de grafos em XGMML. Esses
 * métodos devem ser invocados por programas externos para fazer a leitura e escrita
 * de XGMML, em vez de invocar seus equivalentes diretamente nas classes derivadas
 * de grafo.xgmml.Grafo.
 */
public class Gerenciador {
    
    /** Construtor privado, impedindo que essa classe tenha subclasses. */
    private Gerenciador() {
    }
    
    /** Efetua a leitura de um arquivo XGMML que representa um grafo, e o insere
     * em um grafo.
     * @param arquivo Nome do arquivo XGMML
     * @param g Grafo a ser preenchido com os elementos definidos no arquivo.
     * @return True se o arquivo foi lido com sucesso, false caso contrário.
     */    
    public static boolean lerXGMML(File arquivo, grafo.Grafo g) {
        boolean status = false;
        String nomeClasseObj = g.getClass().getName();
        // Corta "grafo." fora do nome da classe, e substitui por "grafo.xgmml."
        nomeClasseObj = "grafo.xgmml."+nomeClasseObj.substring(6);
        try {
            Class c = Class.forName(nomeClasseObj);
            Object inst = c.newInstance();
            ((IoGrafo)inst).lerXGMML(arquivo, g);
            status=true;
        } catch (ClassNotFoundException e) {
            System.out.println ("Classe "+nomeClasseObj+" não encontrada.");
        } catch (Exception e) {
            System.out.println ("Exceção não esperada.");
        }
        return(status);
    }

    /**
     * Escreve as tags XGMML que representam um grafo.
     * @param arquivo Instância de FileWriter (arquivo) na qual os atributos serão
     * escritos.
     * @param g Grafo cujas propriedades serão descritas.
     * @return True se a operação foi bem sucedida, false caso contrário.
     */    
    public static boolean escreverXGMML(FileWriter arquivo, grafo.Grafo g) {
        boolean status = false;
        String nomeClasseObj = g.getClass().getName();
        // Corta "grafo." fora do nome da classe, e substitui por "grafo.xgmml."
        nomeClasseObj = "grafo.xgmml."+nomeClasseObj.substring(6);
        try {
            Class c = Class.forName(nomeClasseObj);
            Object inst = c.newInstance();
            ((IoGrafo)inst).escreverXGMML(arquivo, g);
            status=true;
        } catch (ClassNotFoundException e) {
            System.out.println ("Classe "+nomeClasseObj+" não encontrada.");
        } catch (Exception e) {
            System.out.println ("Exceção não esperada.");
        }
        return(status);
    }
    
    
}
