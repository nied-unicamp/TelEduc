/*
 * Util.java
 * Versão: 2004-08-26
 * Autor: Celmar Guimarães da Silva
 */

package grafo;
import java.awt.Color;

/** Classe contendo funções genéricas utilizadas pelas demais classes.
 */
public class Util {
    
    /** Construtor de instância da classe Util. */
    private Util() {
        // Não é utilizado. Derivação impedida.
    }
    
    /** Converte uma string para XML
     * Para isso, faz com que cada caractere especial encontrado no texto seja
     * convertido para sua forma correta em XML.
     * @param texto Texto a ser convertido.
     * @return Retorna o texto convertido.
     */    
    public static String ascii2xml(String texto) {
        String aux = "";
        int o; 
        for (int i=0; i<texto.length(); i++) {
            o = (int)(texto.charAt(i)); // ord
            if (o>127 || o<32) {
                aux+="&#"+Integer.toString(o)+";";
            } else {
                aux+=texto.charAt(i);
            }
        }
        return aux;
    }

    /** Retorna um número hexadecimal de 6 dígitos representando uma cor especificada.
     * @param c Cor.
     * @return Número hexadecimal de 6 digitos representando uma cor no espaço RGB.
     */    
    public static String hexaRGBColor(Color c) {
        String hexa = Integer.toHexString(c.getRGB());
        if (hexa.length()>6) {
            hexa = hexa.substring(hexa.length()-6,hexa.length());
        }
        if (hexa.length()<6) {
            for (int i=0; i< 6 - hexa.length(); i++) {
                hexa = "0" + hexa;
            }
        }
        return hexa;
    }

    /** Deixa um ângulo informado sempre entre 0(inclusive) e 360(exclusive)
     * @param angulo Ângulo a ser normalizado.
     * @return Ângulo normalizado.
     */
    public static double normalizarAngulo(double angulo) {
        double a = angulo;
        if (Math.abs(a) >= 360) { 
            a = a % 360; 
        }
        // agora -360 < a < 360
        // mas quero que 0 <= a < 360
        if (a < 0) { 
            a = a + 360; 
        }
        return a;
    }    
}
