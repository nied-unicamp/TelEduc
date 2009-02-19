/*
 * GrafoForceDirected.java
 * Versão: 2004-08-25
 * Autor: Celmar Guimarães da Silva
 */

package grafo.forceDirected;
import java.awt.Dimension;
import java.awt.geom.Point2D;
import grafo.simples.NoSimples;
import grafo.No;
import grafo.Info;
import grafo.Aresta;
import java.util.*;

/** Classe que representa grafos force-directed, ou seja, grafos caracterizados
 * por um modelo de simulação de forças em que:
 * a) cada nó se afasta de outro nó com o qual não esteja ligado; e
 * b) nós ligados por uma aresta tendem a se aproximar, com distância
 * proporcional ao peso da aresta.
 */
public class GrafoForceDirected extends grafo.simples.GrafoSimples  {
    
    /** Lista de vetores de deslocamento acumulado para cada nó. */
    Map deslocamentoAcumulado;
    
    /** Constante representando stress impossível de ser alcançado. */
    private static final double STRESS_MUITO_GRANDE = 99999999;
    
    /** Stress do grafo imediatamente antes do atual. */
    private double stressAnterior = STRESS_MUITO_GRANDE ;
    
    /** Medida de stress do grafo */
    private double stress = STRESS_MUITO_GRANDE;
    
    /** Deslocamento máximo de cada nó durante a execução do algoritmo
     * force-directed
     */
    final int deslocamentoMaximo = 10; // valor original = 5
    
    /** Tamanho máximo da aresta. */
    final double tamanhoMaximoAresta = 200;
    
    /** Tamanho mínimo da aresta. */
    final double tamanhoMinimoAresta = 30;
    
    /** Distância mínima entre dois nós que não estão ligados por nenhuma
     * aresta.
     */
    final double distanciaMinimaEntreNos = 30;
    
    // Campos para cálculo de estabilidade do grafo:
    
    /** Fila de elementos para cálculo de média de stress */
    private LinkedList fila = new LinkedList();
    
    /** Constante que define o estado de "fila" como ativa. */
    private static final int FILA_ATIVA = 0;
    
    /** Constante que define o estado de "fila" como desativada. */
    private static final int FILA_DESATIVADA = 1;
    
    /** Constante que define o estado de "fila" como em preparação. */
    private static final int FILA_EM_PREPARACAO = 2;
    
    /** Estado da fila utilizada para detectar estabilidade do stress.
     */
    private int estadoDaFila = FILA_DESATIVADA;
    
    /** Registra se o grafo está ou não estável, ou seja, se as suas medidas
     * de stress estão em média praticamente sem alteração.
     */
    private boolean estavel = false;
    
    /** Diferença mínima entre duas médias consecutivas de stress do grafo
     * para determinar que o grafo está estável.
     */
    private static final double limiteDeDiferencaDasMediasDeStress = 0.05; // empírico
    
    /** Tamanho máximo de "fila". */
    private static final int tamanhoMaximoFila = 20; // valor estipulado não estudado.
    
    /** Média de stress atual do grafo. */
    private double mediaDeStress;
    
    /** Tempo dispendido para o grafo alcançar estabilidade. */
    private double tempoParaAlcancarEstabilidade;
    
    /** Informa se o grafo deve ou não procurar alcançar estabilidade sozinho. */
    private boolean autoAjusteAtivo = false;
    
    /** Define qual o algoritmo force-directed de multidimensional scaling (MDS)
     * que será utilizado no programa para fazer a aproximação dos vértices. 
     */
     private final AlgoritmoMDS mds = new AlgoritmoMDSAdaptado(this);
    //private final AlgoritmoMDS mds = new AlgoritmoMDSChalmers(this);
    
    
     /** Prepara algoritmo force-directed para execução. Deve ser invocado 
      * antes de se fazer a primeira chamada a "executarForceDirected". 
      * A preparação consiste em anular qualquer deslocamento acumulado, 
      * considerar o grafo instável e invocar as rotinas de preparação 
      * específicas do algoritmo.
      */
     public void prepararForceDirected() {
        if (retornarQuantidadeDeNos()>0) {
            zerarDeslocamentoAcumulado();
            estavel = false;
            stressAnterior = STRESS_MUITO_GRANDE;
            stress = STRESS_MUITO_GRANDE;
            estadoDaFila = FILA_DESATIVADA;
            fila.clear();
            mds.preparar();
        }
    }

    /** Executa um passo do algoritmo de force-directed.
     */
    public synchronized void executarForceDirected() {
        if (retornarQuantidadeDeNos()>0) {        
            mds.executarPasso();
        }
    }

    /** Limpa lista de vetores de deslocamento acumulado. */
    private void zerarDeslocamentoAcumulado() {
        NoSimples n;
        Point2D.Double p;
        if (deslocamentoAcumulado==null) {
            deslocamentoAcumulado = new HashMap(retornarQuantidadeDeNos());
        } else {
            deslocamentoAcumulado.clear();
        }
        for (Iterator i = nos.iterator(); i.hasNext(); ) {
            n = (NoSimples)i.next();
            p = new Point2D.Double(0.0,0.0);
            deslocamentoAcumulado.put(n, p);
        }
    }
    
    /** Adiciona um nó ao grafo.
     * @param no Nó a ser adicionado ao grafo.
     */
    synchronized public void adicionarNo(No no) {
        
        // Cuida do HashMap de deslocamento do algortimo de force-directed
        // antes de efetuar a inserção do nó.
        if (deslocamentoAcumulado==null) {
            zerarDeslocamentoAcumulado();
        }
        Point2D.Double p;
        p = new Point2D.Double(0.0,0.0);
        deslocamentoAcumulado.put(no, p);
        
        super.adicionarNo(no);
    }
    
    /** Adiciona uma aresta entre os nós no1 e no2, com peso e informação
     * associados. Se algum dos nós informados não estiver no grafo, insere
     * esses nós automaticamente.
     * Observações importantes:
     * - Não verifica se a aresta ja existia antes da inserção.
     * - Ela substitui Grafo.adicionarAresta para poder usar arestas da classe
     * ArestaForceDirected.
     * @param no1 Primeiro nó atingido pela aresta.
     * @param no2 Segundo nó atingido pela aresta.
     * @param peso Peso da aresta. Para arestas sem peso pode-se usar zero.
     * @param info Informação relacionada à aresta. Pode ser null.
     */
    synchronized public void adicionarAresta(No no1, No no2, int peso, Info info) {
        if (no1 instanceof NoSimples && no2 instanceof NoSimples) {
            ArestaForceDirected aresta = new ArestaForceDirected((NoSimples)no1, (NoSimples)no2, peso, info);
            adicionarAresta(aresta);
        }
    }
    
    /** Adiciona ao grafo uma aresta informada.
     *  Observações
     *  - Não verifica se essa aresta já existe.
     *  - Caso um nó na extremidade da aresta não exista na lista de nós,
     *  insere-o nessa lista.
     * @param aresta Aresta a ser adicionada ao grafo.
     */
    synchronized public void adicionarAresta(Aresta aresta) {
        if (aresta instanceof ArestaForceDirected) {
            super.adicionarAresta(aresta);
        }
    }

    /** Calcula e retorna o stress do grafo.
     * @return Stress do grafo.
     */
    public double retornarStress() {
        calcularStress();
        return stress;
    }
    
    /** Calcula o stress do grafo e o armazena na propriedade stress. O stress é
     * igual a somatorio(erroQuadrado)/somatorio(distanciaQuadrada), onde
     * erroQuadrado é o quadrado da diferença entre o tamanho desejado e o 
     * tamanho real de uma aresta, e 
     * distanciaQuadrada é o quadrado da distância real entre dois nós.
     */
    private void calcularStress() {

        stressAnterior = stress;
        double somatorioErrosQuadrados = 0;
        double somatorioDistanciasQuadradas = 0;
        double tamanhoDesejado = 0;
        double tamanhoAtual = 0;
        ArestaForceDirected a;
        double erro;
        
        double erroMax = -1;
        ArestaForceDirected aErroMax = null;
        
        // Calculando stress para arestas
        for (Iterator it = arestas.iterator(); it.hasNext(); ) {
            a = (ArestaForceDirected)it.next();
            if (!a.eEscondida()) {
                tamanhoDesejado = a.retornarTamanhoDesejado();
                tamanhoAtual = a.retornarTamanhoAresta();
                if (tamanhoDesejado!=ArestaForceDirected.TAMANHO_DESEJADO_NAO_DEFINIDO &&
                tamanhoDesejado!=ArestaForceDirected.TAMANHO_DESEJADO_TANTO_FAZ) {
                    erro = Math.abs(tamanhoAtual - tamanhoDesejado);
                    somatorioErrosQuadrados += Math.pow(erro, 2);
                    erro = Math.abs(erro);
                    if (erro>erroMax) { erroMax = erro; aErroMax = a;}
                }
                somatorioDistanciasQuadradas += tamanhoAtual*tamanhoAtual;
            }
        }
        
        Object[] listaNos = nos.toArray();
        
        double distanciaDesejada, distanciaAtual;
        
        // Calculando stress para pares de nos
        NoSimples no1, no2;
        for (int i=0; i<listaNos.length; i++) {
            no1 = (NoSimples)nos.get(i);
            if (!no1.eEscondido()) {
                for (int j=i+1; j<listaNos.length; j++) {
                    no2 = (NoSimples)nos.get(j);
                    if (!no2.eEscondido()) {
                        // Se nao existir aresta (noi,noj), calcular stress considerando
                        // o tamanho desejado dessa aresta.
                        a = new ArestaForceDirected(no1,no2);
                        if (!conterAresta(a)) {
                            distanciaAtual = Point2D.Double.distance(no1.x,no1.y,no2.x,no2.y);
                            distanciaDesejada = Math.max(distanciaAtual, distanciaMinimaEntreNos);
                            somatorioErrosQuadrados += Math.pow(distanciaAtual - distanciaDesejada, 2);
                            somatorioDistanciasQuadradas += distanciaAtual * distanciaAtual;
                        }
                    }
                }
            }
        }
        
        // Calculando o stress do grafo inteiro.
        stress = somatorioErrosQuadrados / somatorioDistanciasQuadradas;
        
        // DebugMessenger.showMessage("stress calculado = "+stress);
        // DebugMessenger.showMessage("erro máximo = "+erroMax);
        // DebugMessenger.showMessage("aresta de erro máximo = "+aErroMax);
        // if (aErroMax!=null) {
        //     DebugMessenger.showMessage("  tamanhoDesejado= "+aErroMax.retornarTamanhoDesejado());
        //     DebugMessenger.showMessage("  tamanhoAresta= "+aErroMax.retornarTamanhoAresta());
        // }


    }
    
    /** Informa se o grafo está estável, ou seja, se as forças aplicadas ao 
     * grafo alcançaram um estado de equilíbrio estático. Na verdade, informa
     * o último resultado de uma análise de estabilidade feito sobre o grafo,
     * @return True se o grafo está estável e não foi modificado desde então,
     * False caso contrário.
     */
    public boolean estaEstavel() {
        return estavel;
    }

    /** Analisa a estabilidade do grafo, ou seja, se as forças aplicadas a ele
     * alcançaram um estado de equilíbrio. O algoritmo utilizado para isso 
     * funciona da seguinte forma:
     * - A cada execução deste método, verifica se o stress atual é maior que 
     * o stress medido imediatamente antes. Se for, informa que é necessário 
     * "vigiar o stress", porque ele pode ter entrado em um estado de oscilação
     * similar ao de uma onda senoidal.
     * - Para verificar se o stress está oscilando e não caindo, é feito um
     * acompanhamento da média dos últimos valores de stress.
     * - Se essa média tiver uma variação inexpressiva, então o grafo é 
     * considerado estável. Caso contrário, o grafo continua sendo observado na 
     * próxima execução do algoritmo.
     *
     * Caso a execução do método detecte que o grafo alcançou estabilidade, a
     * propriedade estavel é tornada true.
     */
    public void analisarEstabilidade() {
        double ultimoStressDaFila;
        switch (estadoDaFila) {
            case FILA_DESATIVADA:
                if (stress>=stressAnterior && stressAnterior!=STRESS_MUITO_GRANDE) {
                    estadoDaFila = FILA_EM_PREPARACAO;
                }
                break;
                
            case FILA_EM_PREPARACAO:
                fila.addFirst(new Double(stress));
                if (fila.size()>=tamanhoMaximoFila) {
                    estadoDaFila = FILA_ATIVA;
                    // Calculando primeira media de stress
                    mediaDeStress = 0;
                    Iterator it = fila.iterator();
                    while (it.hasNext()) {
                        mediaDeStress += ((Double)it.next()).doubleValue();
                    }
                    mediaDeStress = mediaDeStress / tamanhoMaximoFila;
                }
                break;
                
            case FILA_ATIVA:
                ultimoStressDaFila = ((Double)fila.removeLast()).doubleValue();
                fila.addFirst(new Double(stress));
                double mediaDeStressAnterior = mediaDeStress;
                // atualizando media de stress
                mediaDeStress = mediaDeStress + (stress - ultimoStressDaFila) / tamanhoMaximoFila;
                if (Math.abs(mediaDeStress - mediaDeStressAnterior) < limiteDeDiferencaDasMediasDeStress) {
                    estavel = true;
                    fila.clear();
                    estadoDaFila = FILA_DESATIVADA;
                }
                
                break;
                
            default:
                estadoDaFila = FILA_DESATIVADA;
        }
        
    }

    /** Executa o algoritmo de aproximação de arestas tantas vezes quanto 
     * necessário, até alcançar a estabilidade do grafo.
     */
    synchronized public void procurarEstabilidade() {
        if (retornarQuantidadeDeNos() - retornarEscondidos().size() >1) {
            prepararForceDirected();
            double s;
            long tempo = System.currentTimeMillis();
            long tempo2;
            while (!estaEstavel()) {
                executarForceDirected();
                calcularStress();
                analisarEstabilidade();
            }
            s = retornarStress();
            tempo2 = System.currentTimeMillis();
            tempoParaAlcancarEstabilidade = tempo2-tempo;
            

            //mostrarDistribuicaoDeErrosDasArestas(); // <<<<<<<<<<<<<<<<<<<<<<<<<<< PARA TESTE!!!!!!!
        }
        
    }
    
    /** Informa o tempo que foi necessário para tornar o grafo estável na 
     * última execução do método procurarEstabilidade.
     * @return Tempo (em milissegundos) gasto para tornar o grafo estável.
     */
    public double retornarTempoParaAlcancarEstabilidade() {
        return tempoParaAlcancarEstabilidade;
    }
    
    /** Estabelece que o algoritmo de posicionamento dos nós deve ser executado
     * a cada modificação do grafo.
     */
    public void ativarAutoAjuste() {
        autoAjusteAtivo = true;
        if (!estavel) {
            setChanged();
            notifyObservers();
        }
    }
    
    /** Estabelece que o algoritmo de posicionamento dos nós não deve ser 
     * executado quando o grafo for modificado.
     */
    public void desativarAutoAjuste() {
        autoAjusteAtivo = false;
    }
    
    /** Informa se o algoritmo de posicionamento dos nós vai ou não ser 
     * executado quando o grafo for modificado.
     * @return True se o algoritmo vai ser executado, False caso contrário.
     */
    public boolean autoAjusteEstaAtivo() {
        return autoAjusteAtivo;
    }
    
    /** Registra que o grafo sofreu uma modificação, avisando os observadores do
     * grafo (se o auto-ajuste estiver ativo) e marcando-o como não estável.
     */    
    public void registrarModificacaoDoGrafo() {
        setChanged();
        estavel = false;
        if (autoAjusteAtivo) {
            notifyObservers();
        }
    }
    
    /** Dado um nó e uma dimensão, força o centro do nó a estar dentro dos 
     * limites estipulados por essa dimensão.
     * @param n Nó
     * @param d Limites (dimensão) em que o nó deve estar.
     */
    public void posicionaNoNosLimites(No n, Dimension d) {
        if (d!=null) {
            // Nó deve estar nos limites determinados pela dimensao d.
            if (n.x < n.getWidth()/2) {
                n.x = n.getWidth()/2;
            } else if (n.x > d.width-n.getWidth()/2) {
                n.x = d.width-n.getWidth()/2;
            }
            if (n.y < n.getHeight()/2) {
                n.y = n.getHeight()/2;
            } else if (n.y > d.height-n.getHeight()/2) {
                n.y = d.height-n.getHeight()/2;
            }
        }
    }

    /** Mostra de maneira textual um histograma sobre a distribuição dos stresses
     * em cada aresta, ou seja, da diferença entre o tamanho desejado por uma 
     * aresta e seu tamanho real.
     * Usado para verificar que o algoritmo de force-directed nem sempre 
     * consegue manter as proporções esperadas entre as arestas.
     */
//    public void mostrarDistribuicaoDeErrosDasArestas() { // <<<<<<<<<<<<<<<<<<<<<<< para teste!!!!!!
//        double tamanhoDesejado;
//        double tamanhoAtual;
//        double erro;
//        
//        // Preenchendo histograma vazio
//        int[] histograma = new int[20];
//        for (int i=0; i<20; i++) {
//            histograma[i]=0;
//        }
//        
//        ArestaForceDirected a;
//        for (Iterator it = arestas.iterator(); it.hasNext(); ) {
//            a = (ArestaForceDirected)it.next();
//            if (!a.eEscondida()) {
//                tamanhoDesejado = a.retornarTamanhoDesejado();
//                tamanhoAtual = a.retornarTamanhoAresta();
//                if (tamanhoDesejado!=ArestaForceDirected.TAMANHO_DESEJADO_NAO_DEFINIDO &&
//                tamanhoDesejado!=ArestaForceDirected.TAMANHO_DESEJADO_TANTO_FAZ) {
//                    erro = tamanhoAtual - tamanhoDesejado;
//                    erro = Math.abs(erro);
//                    histograma[(int)Math.floor(erro/10)]++;
//                }
//                System.out.println("Aresta "+a+": peso="+a.peso+"; tam.desejado="+tamanhoDesejado+"; tam.atual="+tamanhoAtual);
//            }
//        }
//        
//        System.out.println("Histograma de erros das arestas:");
//        for (int i=0; i<20; i++) {
//            System.out.println(i*10+" ... "+(i*10+9)+":"+histograma[i]);
//        }
//        System.out.println("");
//
//    }
    
}


