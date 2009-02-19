/*
 * Util.java
 * Versão: 2004-07-04
 * Autor: Celmar Guimarães da Silva
 */

package grafo.xgmml;
import java.io.*;
/**
 * Classe contendo métodos estáticos para gerenciar a leitura e escrita de tags
 * XGMML. Essa classe faz a associação entre qual classe do pacote grafo.xgmml
 * conhece as tags de qual classe do pacote grafo.
 */
public class Util {
    
    /** Construtor privado, para que esta classe não tenha subclasses. */
    private Util() {
    }
    
    /**
     * Invoca a classe adequada no pacote xgmml para efetuar a escrita das tags do
     * objeto obj em arquivo.
     * @param o Arquivo no qual as tags devem ser escritas.
     * @param obj Objeto a ser descrito.
     * @return True se a operação de leitura foi bem sucedida, false caso contrário.
     */    
    public static boolean escreverAtributosXGMML(FileWriter o, Object obj) {
        boolean status = false;
        String nomeClasseObj = obj.getClass().getName();
        // Corta "grafo." fora do nome da classe, e substitui por "grafo.xgmml."
        nomeClasseObj = "grafo.xgmml."+nomeClasseObj.substring(6);
        //System.out.println(nomeClasseObj);
        try {
            Class c = Class.forName(nomeClasseObj);
            Object inst = c.newInstance();
            ((Io)inst).escreverAtributosXGMML(o,obj);
        } catch (ClassNotFoundException e) {
            System.out.println ("Classe "+nomeClasseObj+" não encontrada.");
        } catch (Exception e) {
            System.out.println ("Exceção não esperada.");
        }
        return(status);
    }
    
    /**
     * Invoca a classe adequada no pacote xgmml para efetuar a leitura dos atributos do
     * nó DOM (raiz) informado e ajustar o objeto obj.
     * @param raiz Nó DOM a partir do qual serão lidas as tags.
     * @param obj Objeto a ser ajustado.
     * @return True se a operação de leitura foi bem sucedida, false caso contrário.
     */    
    public static boolean lerAtributosXGMML(org.w3c.dom.Node raiz, Object obj) {
        boolean status = false;
        String nomeClasseObj = obj.getClass().getName();
        // Corta "grafo." fora do nome da classe, e substitui por "grafo.xgmml."
        nomeClasseObj = "grafo.xgmml."+nomeClasseObj.substring(6);
        //System.out.println(nomeClasseObj);
        try {
            Class c = Class.forName(nomeClasseObj);
            Object inst = c.newInstance();
            ((Io)inst).lerAtributosXGMML(raiz, obj);
            status=true;
        } catch (ClassNotFoundException e) {
            System.out.println ("Classe "+nomeClasseObj+" não encontrada.");
        } catch (Exception e) {
            System.out.println ("Exceção não esperada.");
        }
        return(status);
    }    
}
