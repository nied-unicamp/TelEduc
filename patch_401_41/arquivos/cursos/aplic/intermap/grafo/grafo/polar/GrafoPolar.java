/*
 * GrafoPolar.java
 * Versão: 2004-08-26
 * Autores: Celmar Guimarães da Silva e Petr Stukjunger
 */

package grafo.polar;

import java.util.*;
import java.awt.*;
import java.awt.geom.Point2D;
import grafo.*;

/** Classe que armazena um grafo como a estrutura de um Grafo Polar.
 * Um Grafo Polar apresenta dois anéis concêntricos em que os nós de um grafo podem
 * ser dispostos.
 */
public class GrafoPolar extends Grafo implements Aneis {
    
    /** Cor do círculo periférico.
     */
    public Color corAreaPeriferica;
    
    /** Cor do círculo central.
     */
    public Color corAreaCentral;
    
    /** Cor da área proibida.
     */
    public Color corAreaProibida;
    
    /** Hash map de arestas.
     */
    private Map arestasHash = new HashMap();
    
    /** Raio do anel periférico.
     */
    public ObservableInt raioPeriferico;
    
    /** Raio do anel central.
     */
    public ObservableInt raioCentral;
    
    /** Raio da área proibida.
     */
    public ObservableInt raioAreaProibida;
    
    /** Ângulo usando para girar anel central. É o ângulo que orienta os demais
     * nós do anel.
     */
    public ObservableAngle anguloInicialNosCentrais;
    
    /** Ângulo usando para girar anel periférico. É o ângulo que orienta os
     * demais nós do anel.
     */
    public ObservableAngle anguloInicialNosPerifericos;
    
    /** Coordenada cartesiana do centro do grafo.
     */
    public Point2D.Double centro;
    
    /** Quantidade de nós centrais (tamanho do anel central).
     */
    public ObservableInt numeroNosCentrais;
    
    /** Quantidade de nós periféricos (tamanho do anel periférico).
     */
    public ObservableInt numeroNosPerifericos;
    
    /** Nó que está sendo movido no momento.
     */    
    private NoPolar noMovendoPolar;

    // Observação sobre implementaçao do ArrayList "nos":
    // Nem sempre os nós de um mesmo anel em "nos" está ordenado.
    // Pode ocorrer situações como : 120 180 240 300 0 60.
    // Tomar cuidado com isso ao elaborar novos algoritmos.
    
    /** Cria uma nova instância de GrafoPolar.
     */
    public GrafoPolar() {
        super();
        corAreaPeriferica = new Color(0x009999);
        corAreaCentral = new Color(0xaba56a);
        corAreaProibida = new Color(0xCFa0a0);
        
        raioPeriferico = new ObservableInt(0);
        raioCentral = new ObservableInt(0);
        raioAreaProibida = new ObservableInt(0);
        
        anguloInicialNosCentrais = new ObservableAngle(90.0);
        anguloInicialNosPerifericos = new ObservableAngle(0.0);
        
        centro = new Point2D.Double(0,0);
        
        numeroNosCentrais = new ObservableInt(0);
        numeroNosPerifericos = new ObservableInt(0);
    }
    
    
    /**
     * Adiciona um nó ao grafo
     * Observação: Sobrescreve grafo.adicionarNo para atualizar número de nós centrais.
     * @param no Nó a ser adicionado.
     * @param registrarModificacao Informa se a ação de adicionar o nó deve ser considerada como modificação
     * do grafo ou não.
     */
    synchronized public void adicionarNo(No no, boolean registrarModificacao) {
        NoPolar noPolar = (NoPolar)no;
        if (!noPolar.eEscondido()) {
            if (noPolar.eCentral()) {
                numeroNosCentrais.add(1);
                //System.out.println("Novo número de nós centrais = "+numeroNosCentrais.getValue());
            } else {
                // É periferico
                numeroNosPerifericos.add(1);
                //System.out.println("Novo número de nós Perifericos = "+numeroNosPerifericos.getValue());
            }
        }
        super.adicionarNo(noPolar, registrarModificacao);
        //nos.add(no);
        reposicionarNos();
    }
    
    /**
     * Adiciona uma aresta entre os nós no1 e no2, com peso e informação
     * associados. Se algum dos nós informados não estiver no grafo, insere
     * esses nós automaticamente.
     * Observações importantes:
     * - Não verifica se a aresta ja existia antes da inserção.
     * - Ela substitui Grafo.adicionarAresta para poder usar arestas da classe
     * ArestaPolar.
     * @param no1 Primeiro nó atingido pela aresta.
     * @param no2 Segundo nó atingido pela aresta.
     * @param peso Peso da aresta. Para arestas sem peso pode-se usar zero.
     * @param info Informação relacionada à aresta. Pode ser null.
     * @param registrarModificacao Informa se a ação de adicionar a aresta deve ser considerada como modificação
     * do grafo ou não.
     */
    synchronized public void adicionarAresta(No no1, No no2, int peso, Info info, boolean registrarModificacao) {
        if (no1 instanceof NoPolar && no2 instanceof NoPolar) {
            ArestaPolar aresta = new ArestaPolar((NoPolar)no1, (NoPolar)no2, peso, info);
            adicionarAresta(aresta, registrarModificacao);
        }
    }
    
    /**
     * Adiciona ao grafo uma aresta informada.
     *  Observações
     *  - Não verifica se essa aresta já existe.
     *  - Caso um nó na extremidade da aresta não exista na lista de nós,
     *  insere-o nessa lista.
     * @param aresta Aresta a ser adicionada ao grafo.
     * @param registrarModificacao Informa se a ação de adicionar a aresta deve ser considerada como modificação
     * do grafo ou não.
     */
    synchronized public void adicionarAresta(Aresta aresta, boolean registrarModificacao) {
        if (aresta instanceof ArestaPolar) {
            super.adicionarAresta(aresta, registrarModificacao);
        }
    }
    
    /**
     * Remove um nó do grafo.
     * Observações:
     * - Sobrescreve grafo.removerNo para atualizar número de nós centrais.
     * - Não verifica se nó existia em algum anel antes de fazer sua remoção.
     * @param no Nó a ser removido.
     * @param registrarModificacao Informa se a ação de remover o nó deve ser considerada como modificação
     * do grafo ou não.
     */
    synchronized public void removerNo(No no, boolean registrarModificacao) {
        if (no instanceof NoPolar) {
            NoPolar noPolar = (NoPolar)no;
            if (!noPolar.eEscondido()) {
                if (noPolar.eCentral()) {
                    numeroNosCentrais.sub(1);
                } else {
                    // É periferico
                    numeroNosPerifericos.sub(1);
                }
            }
            super.removerNo(noPolar, registrarModificacao);
            reposicionarNos();
        }
    }
    
    /**
     * Esconde um nó do grafo.
     * @param no Nó a ser escondido.
     * @param registrarModificacao Informa se a ação de esconder o nó deve ser considerada como modificação
     * do grafo ou não.
     */
    public void esconderNo(No no, boolean registrarModificacao) {
        if (no instanceof NoPolar) {
            NoPolar noPolar = (NoPolar)no;
            if (noPolar.eMovendo()) {
                return; // é muito dificil para esconder nó que está em movimento
            }
            
            // remover nó de lista em grafo
            int indexNo = nos.indexOf(noPolar);
            if (indexNo!=-1 && !no.eEscondido()) { // se nó pertence à lista de nós, então posso escondê-lo.
                if (noPolar.eCentral()) {
                    numeroNosCentrais.sub(1);
                } else {
                    // É periferico
                    numeroNosPerifericos.sub(1);
                }
                super.esconderNo(no, registrarModificacao);
//                if (noPolar.eMarcado()) {
//                    noPolar.desmarcar();
//                }
//                if (noPolar.eEscolhido()) {
//                    noPolar.desfazerEscolher();
//                }
//                noPolar.esconder();
            }
            reposicionarNos();
        }
    }
    
    /**
     * Torna visível um nó que havia sido escondido.
     * @param no Nó a ser mostrado.
     * @param registrarModificacao Informa se a ação de mostrar o nó deve ser considerada como modificação
     * do grafo ou não.
     */
    public void mostrarNo(No no, boolean registrarModificacao) {
        if (no instanceof NoPolar) {
            NoPolar noPolar = (NoPolar)no;
            int indice=nos.indexOf(noPolar);
            if (indice!=-1 && no.eEscondido()) {
                if (noPolar.eCentral()) {
                    numeroNosCentrais.add(1);
                } else {
                    // É periferico
                    numeroNosPerifericos.add(1);
                }
                super.mostrarNo(noPolar, registrarModificacao);
                reposicionarNos();
            }
        }
    }
    
    /** Calcula a distância angular fixa existente entre os nós de um dos dois anéis.
     * @param anel Anel a ser considerado.
     * @return Distância angular fixa entre os nós do anel informado.
     */
    public double distanciaEntreNos(boolean anel) {
        double resposta;
        int tamanho;
        if (anel==NoPolar.ANEL_CENTRAL) {
            tamanho = numeroNosCentrais.getValue();
        } else {
            tamanho = numeroNosPerifericos.getValue();
        }
        if (tamanho > 0) {
            resposta = 360.0/tamanho;
        } else {
            resposta = 0;
        }
        return resposta;
    }
    
    /** Verifica qual o nó sob um determinado ponto (x,y).
     * @param x Abscissa do ponto.
     * @param y Ordenada do ponto.
     * @return  Retorna o nó, ou null se o nó não existir.
     */
    protected NoPolar noPolarEm(int x, int y) {
        No no = noEm(x,y);
        NoPolar nop;
        if (!(no instanceof NoPolar)) {
            nop = null;
        } else {
            nop = (NoPolar)no;
        }
        return nop;
    }
    
    
    /** Reposiciona todos os nós eqüidistantemente uns dos outros em cada anel,
     * exceto noMovendo, que continua na mesma posição em que está.
     */
    protected void reposicionarNos() {
        double distanciaNosCentrais    = distanciaEntreNos(ANEL_CENTRAL);
        double distanciaNosPerifericos = distanciaEntreNos(ANEL_PERIFERICO);
        
        double raioC = raioCentral.getValue();
        if (numeroNosCentrais.getValue() < 2) {
            raioC = 0.0;
        }
        
        // Calculando novos ângulos para todos os nós.
        // Todos os nós centrais estão ordenados por ângulo entre si em "nós".
        // Todos os nós periféricos estão ordenados por ângulo entre si em "nós".
        // O unico nó que não é ajustado é o noMovendoPolar.
        ListIterator li = nos.listIterator();
        int i = 0, j = 0;
        while (li.hasNext()) {
            NoPolar no = (NoPolar) li.next();
            if (!no.eEscondido()) {
                if (no.eCentral()) {
                    if (no != noMovendoPolar) {
                        no.raio   = raioC;
                        no.angulo = (anguloInicialNosCentrais.getValue() + (i*distanciaNosCentrais)) % 360.0;
                        if (no.angulo < 0) {
                            no.angulo += 360.0;
                        }
                    }
                    i++;
                } else {
                    if (no != noMovendoPolar) {
                        no.raio   = raioPeriferico.getValue();
                        no.angulo = (anguloInicialNosPerifericos.getValue() + (j*distanciaNosPerifericos)) % 360.0;
                        if (no.angulo < 0) {
                            no.angulo += 360.0;
                        }
                    }
                    j++;
                }
            }
        }
        atualizarHashArestas();
    }
    
    /** Atualiza HashMap de arestas, que é usado no algoritmo de seleção de
     * arestas.
     */
    private void atualizarHashArestas() {
        arestasHash.clear();
        Iterator it = arestas.iterator();
        while(it.hasNext()) {
            ArestaPolar a = (ArestaPolar)it.next();
            arestasHash.put(a.retornarChave(), a);
        }
    }
    
    /** Move um nó para o anel periférico.
     * Observação: Não verifica se o nó pertence à lista de nós do grafo.
     * @param no Nó a ser movido.
     */
    public void moverParaAnelPeriferico(NoPolar no) {
        if (no.eCentral()) {
            girarAnelParaEfetuarRemocao(no, NoPolar.ANEL_CENTRAL);
            posicionarCorretamenteNaListaDeNos(no, NoPolar.ANEL_PERIFERICO);
            no.moverParaAnelPeriferico();
            numeroNosCentrais.sub(1);
            numeroNosPerifericos.add(1);
            girarAnelParaEfetuarInsercao(no, NoPolar.ANEL_PERIFERICO);
            reposicionarNos();
        }
    }
    
    /** Move um nó para o anel central.
     * Observação: Não verifica se o nó pertence à lista de nós do grafo.
     * @param no No a ser movido.
     */
    public void moverParaAnelCentral(NoPolar no) {
        if (no.ePeriferico()) {
            girarAnelParaEfetuarRemocao(no, NoPolar.ANEL_PERIFERICO);
            posicionarCorretamenteNaListaDeNos(no, NoPolar.ANEL_CENTRAL);
            no.moverParaAnelCentral();
            numeroNosCentrais.add(1);
            numeroNosPerifericos.sub(1);
            girarAnelParaEfetuarInsercao(no, NoPolar.ANEL_CENTRAL);
            reposicionarNos();
        }
    }
    
    
    /** Modifica o ângulo inicial do anel para o qual um nó será movido, de 
     * modo que o nó a ser inserido esteja em uma posição angular equidistante
     * de seus nós vizinhos. Ou seja, faz com que o buraco que vai ser aberto
     * para este nó no anel tenha exatamente o mesmo ângulo que o nó no momento
     * da inserção.
     * @param no Nó a ser inserido.
     * @param anel Anel a ser girado.
     */    
    public void girarAnelParaEfetuarInsercao(NoPolar no, boolean anel) {
        double anguloInsercao = no.angulo;
        double anguloInicial, novoAnguloInicial, delta;
        if (anel == NoPolar.ANEL_CENTRAL) {
            anguloInicial=anguloInicialNosCentrais.getValue();
        } else {
            anguloInicial=anguloInicialNosPerifericos.getValue();
        }
        novoAnguloInicial = anguloInsercao - (retornarPosicaoNoAnel(no)) * distanciaEntreNos(anel);
        // Linha acima pode estar com problema. retornarPosicaoNoAnel não faz o que se pensa que ela faz.
        delta = novoAnguloInicial - anguloInicial;
        
        girarAnel(anel, delta);
    }

    /** Retorna posição do nó informado em seu anel, considerando como posição zero o
     * nó que ocupa (ou que deveria estar ocupando) a posição indicada pelo ângulo
     * inicial do nó.
     * @param no Nó cuja posição deve ser calculada.
     * @return Posição do nó no anel, como explicado anteriormente.
     */    
    private int retornarPosicaoEfetivaNoAnel(NoPolar no) {
        // Retorna posicao no anel, tendo por base o nó que tem angulo inicial.
        NoPolar aux;
        double anguloInicial;
        int posInicial = -1;
        int posNo = -1;
        int posRealNo = -1;
        if (no.retornarAnel() == NoPolar.ANEL_CENTRAL) {
            anguloInicial=anguloInicialNosCentrais.getValue();
        } else {
            anguloInicial=anguloInicialNosPerifericos.getValue();
        }
        for (int i=0; i<nos.size(); i++) {
            aux = (NoPolar)nos.get(i);
            if (aux.retornarAnel() == no.retornarAnel() && !aux.eEscondido()) {
                if (Math.abs(aux.angulo - anguloInicial)<0.01) { // praticamente iguais
                    posInicial = i;
                }
                if (Math.abs(aux.angulo - no.angulo)<0.01) { // praticamente iguais
                    posNo = i;
                }
            }
        }
        if (posInicial == -1 && nos.contains(no)) {
            posInicial = posNo;
        }
        if (posInicial != -1 && posNo != -1) {
            if (posNo >= posInicial) {
                posRealNo = posNo - posInicial;
            } else {
                posRealNo = nos.size() - posInicial + posNo;
            }
        }
        return posRealNo;
    }
    
    /** Modifica o ângulo inicial do anel do qual um nó foi movido, de modo a 
     * parecer que ambos os ex-vizinhos desse nó caminharam a mesma distância 
     * angular durante o reposicionamento dos nós no anel. Dessa forma, os
     * n/2 nós que estão à direita do buraco se comportam da mesma forma que os
     * n/2 nós restantes no reposicionamento: os que estavam mais pertos do 
     * buraco parecem ter andado mais, e os que estavam mais longe parecem ter
     * andado menos.
     * @param no Nó a ser removido do anel.
     * @param anel Anel do qual o nó será removido.
     */    
    public void girarAnelParaEfetuarRemocao(NoPolar no, boolean anel) {
        double anguloInicial, novoAnguloInicial, delta;
        if (anel == NoPolar.ANEL_CENTRAL) {
            anguloInicial=anguloInicialNosCentrais.getValue();
        } else {
            anguloInicial=anguloInicialNosPerifericos.getValue();
        }

        if (retornarTamanhoAnel(anel)>2) {
            double k = retornarPosicaoEfetivaNoAnel(no);
            double anguloBuraco = anguloInicial + k * distanciaEntreNos(anel);
            double novaDistanciaEntreNos = 360 / ((double)retornarTamanhoAnel(anel) - 1);
            novoAnguloInicial = Util.normalizarAngulo(anguloBuraco - novaDistanciaEntreNos * ( k - 0.5 ));
            delta = novoAnguloInicial - anguloInicial;
            girarAnel(anel, delta);
        }
        
        
    }
    
    
    /** Verifica qual a posição correta que o nó informado deve ocupar em nos.
     * Se estiver na posição errada, insere-o na posição correta.
     * @param no Nó a ser posicionado.
     * @param anel Anel em que o nó será posicionado.
     */
    public void posicionarCorretamenteNaListaDeNos(NoPolar no, boolean anel) {
        int tamanhoAnel;
        double anguloInicial;
        
        
        if (anel == NoPolar.ANEL_CENTRAL) {
            tamanhoAnel = numeroNosCentrais.getValue();
            anguloInicial=anguloInicialNosCentrais.getValue();
        } else {
            tamanhoAnel = numeroNosPerifericos.getValue();
            anguloInicial=anguloInicialNosPerifericos.getValue();
        }
        
        
        if (tamanhoAnel >0) {
            nos.remove(no);
            NoPolar noAnterior = null;
            NoPolar noSeguinte = null;
            int i = -1;
            do {
                i++;
                noAnterior = (NoPolar)nos.get(i);
            } while (noAnterior.retornarAnel()!=anel);
            // noAnterior tem agora o primeiro nó do anel.
            boolean acheiPosInsercao = false;
            //i--;
            double aAnterior, aSeguinte;
            while (!acheiPosInsercao) {
                do {
                    i=(i+1) % nos.size();
                    noSeguinte = (NoPolar)nos.get(i);
                } while (noSeguinte.retornarAnel()!=anel);
                aAnterior = noAnterior.angulo % 360;
                aSeguinte = noSeguinte.angulo % 360;
                if (aSeguinte<aAnterior) { aSeguinte+=360; }
                acheiPosInsercao = ((aAnterior <= no.angulo) && (no.angulo < aSeguinte)) ||
                ((aAnterior <= no.angulo+360) && (no.angulo + 360 < aSeguinte)) ||
                (aAnterior == aSeguinte);
                if (aAnterior == aSeguinte) {
                    if (PolarCartesiano.menorDistanciaEntreAngulos(no.angulo, anguloInicial) < 90) {
                        i=0;
                    } else {
                        i=nos.size();
                    }
                }
                
                if (!acheiPosInsercao) { noAnterior = noSeguinte; }
            }
            // achei posicao de insercao ==> antes de noSeguinte.
            
            nos.add(i,no);
            
        } // else {
            //Anel so tem 1 elemento. Não precisa posicionar.
        //}
        
    }
    
    /** Ajusta o raio periférico para um valor específico.
     * @param novoRaio Novo raio periférico.
     */
    public void ajustarRaioPeriferico(int novoRaio) {
        if (novoRaio > 0) {
            raioPeriferico.setValue(novoRaio);
            reposicionarNos();
        }
    }
    
    /** Ajusta o raio central para um valor específico.
     * @param novoRaio Novo raio central.
     */
    public void ajustarRaioCentral(int novoRaio) {
        if (novoRaio > 0) {
            raioCentral.setValue(novoRaio);
            reposicionarNos();
        }
    }
    
    /** Ajusta o raio da área proibida para um valor específico.
     * @param novoRaio Novo raio da área proibida.
     */
    public void ajustarRaioAreaProibida(int novoRaio) {
        if (novoRaio > 0) {
            raioAreaProibida.setValue(novoRaio);
        }
    }
    
    /** Retorna o valor do raio periférico.
     * @return Valor do raio periférico.
     */
    public double retornarRaioPeriferico() {
        return raioPeriferico.getValue();
    }
    
    /** Retorna o valor do raio central.
     * @return Valor do raio central.
     */
    public double retornarRaioCentral() {
        return raioCentral.getValue();
    }
    
    /** Retorna o valor do raio da área proibida.
     * @return Valor do raio da área proibida.
     */
    public double retornarRaioAreaProibida() {
        return raioAreaProibida.getValue();
    }
    
    /** Gira o anel especificado, adicionando ao ângulo inicial do anel o
     * ângulo informado.
     * @param anel Anel a ser rotacionado.
     * @param angulo Valor a ser adicionado aos ângulos dos nós.
     */
    public void girarAnel(boolean anel, double angulo) {
        if (anel==NoPolar.ANEL_CENTRAL) {
            anguloInicialNosCentrais.setValue(anguloInicialNosCentrais.getValue() + angulo);
        } else if (anel==NoPolar.ANEL_PERIFERICO) {
            anguloInicialNosPerifericos.setValue(anguloInicialNosPerifericos.getValue() + angulo);
        }
        reposicionarNos();
    }
    
    /** Testa se o ponto (x,y) se encontra no retângulo em que o grafo é desenhado.
     * @param x Abscissa do ponto.
     * @param y Ordenada do ponto.
     * @return  Retorna AREA_DE_NO - se o ponto está sobre um nó;
     *          AREA_INTERIOR - se está dentro do retângulo mas fora de um nó;
     *          AREA_EXTERIOR - se está fora do retângulo.
     */
    
/*    public int areaEm(int x, int y) {
        int tx = (int) (centro.x - largura / 2);
        int ty = (int) (centro.y - altura / 2);
        
        if  (x >= tx && y>= ty && (x-tx) < largura && (y-ty) < altura) {
            ListIterator li = nos.listIterator();
            while (li.hasNext()) {
                NoPolar no = (NoPolar) li.next();
                if (no.areaEm(x,y) == NoPolar.AREA_INTERIOR && !no.eEscondido()) return AREA_DE_NO;
            }
            return AREA_INTERIOR;
        }
        return AREA_EXTERIOR;
    }
  */  
    
    /** Registra qual nó está em movimento.
     * @param no Nó que está em movimento.
     */
    public void registrarNoMovendo(No no) {
        super.registrarNoMovendo(no);
        noMovendoPolar = (NoPolar)no;
    }
    
        /** Desfaz o registro de qual nó está em movimento.
     */
    public void desfazerRegistrarNoMovendo() {
        super.desfazerRegistrarNoMovendo();
        noMovendoPolar = null;
    }
    
    /** Move o nó que está em movimento para a posição correta do anel ao qual
     * ele pertence.
     */
    public void posicionarCorretamenteNoMovendo() {
        if (noMovendoPolar != null) {
            NoPolar no = noMovendoPolar;  // Normalmente reposicionarNos nao muda
            noMovendoPolar = null;        // posicao de noMovendo, por isso é preciso
            reposicionarNos();       // "desfazer" noMovendo para executar
            noMovendoPolar = no;          // essa função
        }
    }
    
    /** Mover nó que está em movimento para a posição nova (x,y).
     * @param x Abscissa da nova posição.
     * @param y Ordenada da nova posição.
     */
    public void moverNoMovendo(int x, int y) {
        boolean troqueiDeAnel = false;
        Dimension d = retornarDimensaoDaTela();
        // ajusta para que o nó não possa ser movido para fora do retângulo do
        // grafo
        int tx = (int) (centro.x - d.width/ 2);  // canto superior do retângulo
        int ty = (int) (centro.y - d.height / 2);   // que enquadra o grafo.
        int nx = x, ny = y; // posição do mouse
        if (x < tx) {
            nx = tx;
        }
        if (x > (tx + d.width)) {
            nx = tx + (int) d.width;
        }
        if (y < ty) {
            ny = ty;
        }
        if (y > (ty + d.height)) {
            ny = ty + (int) d.height;
        }
        // aqui nx e ny já tem a posição do mouse, ajustada para estar dentro
        // do retângulo
        
        // ajustando posição do noMovendo
        noMovendoPolar.raio = PolarCartesiano.cartesiano2Polar_raio(nx,ny,centro);
        noMovendoPolar.angulo = PolarCartesiano.cartesiano2Polar_angulo(nx,ny,centro);
        noMovendoPolar.x = nx;
        noMovendoPolar.y = ny;
        
        double distOrig = distanciaEntreNos(noMovendoPolar.retornarAnel());
        double angulo = noMovendoPolar.angulo;
        
        // Comparar raio de noMovendo com raio central e periférico, para
        // descobrir se é necessário mover o nó para outro anel
        double rnp = Math.abs(noMovendoPolar.raio - raioPeriferico.getValue());
        double rnc = Math.abs(noMovendoPolar.raio - raioCentral.getValue());
        if (rnp < rnc && noMovendoPolar.eCentral()) { // mover para anel periferico
            moverParaAnelPeriferico(noMovendoPolar);
            troqueiDeAnel = true;
        } else if (rnp > rnc && noMovendoPolar.ePeriferico()) { // mover para anel central
            moverParaAnelCentral(noMovendoPolar);
            troqueiDeAnel = true;
        }
        
        if (!troqueiDeAnel) {
            double anguloInicial;
            int tamanhoAnel;
            if (noMovendoPolar.retornarAnel()==NoPolar.ANEL_CENTRAL) {
                anguloInicial=anguloInicialNosCentrais.getValue();
                tamanhoAnel=numeroNosCentrais.getValue();
            } else {
                anguloInicial=anguloInicialNosPerifericos.getValue();
                tamanhoAnel=numeroNosPerifericos.getValue();
            }
            if (tamanhoAnel>1) {
                int posNoMovendo = retornarPosicaoNoAnel(noMovendoPolar);
                double anguloCorreto = posNoMovendo * distOrig + anguloInicial;
                anguloCorreto = Util.normalizarAngulo(anguloCorreto);
                if (PolarCartesiano.menorDistanciaEntreAngulos(angulo, anguloCorreto) > distOrig) {
                    double a1 = Math.floor((angulo-anguloInicial) / distOrig) * distOrig + anguloInicial;
                    double a2 = Math.ceil((angulo-anguloInicial) / distOrig) * distOrig + anguloInicial;
                    a1=Util.normalizarAngulo(a1);
                    a2=Util.normalizarAngulo(a2);
                    NoPolar no = null;
                    if (PolarCartesiano.menorDistanciaEntreAngulos(angulo, a1)<PolarCartesiano.menorDistanciaEntreAngulos(angulo,a2)) {
                        // entao noMovendo deve ser trocado de posicao
                        // com o no de angulo a1.
                        no = retornarNo(a1,noMovendoPolar.retornarAnel());
                        
                    } else {
                        // senao noMovendo deve ser trocado de posicao
                        // com o no de angulo a2
                        no = retornarNo(a2,noMovendoPolar.retornarAnel());
                    }
                    // trocarNos(noMovendo, no);  // Pode ser que nó e nóMovendo nao sejam vizinhos.
                    // Por isso precisei fazer uma rotina de rotação de um pedaço de lista.
                    rotacionarSecaoAnel(noMovendoPolar, no); // nóMovendo deve entrar no lugar de nó.
                    reposicionarNos();
                } // else we need to do anything.
            } //else {
            // Nao preciso fazer nada se anel tiver apenas 1 elemento.
            //}
            
            
            
        }
        
    }

    /** Rotaciona os elementos da lista de anéis entre um intervalo de nós especificado.
     * @param noASerReposicionado Nó que é retirado de uma extremidade do intervalo para ser posicionado na outra.
     * @param noDestino A outra extremidade do intervalo.
     */    
    private void rotacionarSecaoAnel(NoPolar noASerReposicionado, NoPolar noDestino) {
        boolean anel = noASerReposicionado.retornarAnel();
        if (anel == noDestino.retornarAnel()) {
            // noDestino vai ceder lugar ao noASerReposicionado, fazendo um "shift" no array.
            int iRealDestino = nos.indexOf(noDestino);
            int iRealRepos = nos.indexOf(noASerReposicionado);
            int iDestino = retornarPosicaoNoAnel(noDestino);
            int iRepos = retornarPosicaoNoAnel(noASerReposicionado);
            if (iDestino == retornarTamanhoAnel(anel)-1 && iRepos == 0) {
                nos.remove(noASerReposicionado);
                nos.remove(noDestino);
                nos.add(0, noDestino);
                nos.add(iRealDestino, noASerReposicionado);
            } else if (iRepos==retornarTamanhoAnel(anel)-1 && iDestino == 0) {
                nos.remove(noASerReposicionado);
                nos.remove(noDestino);
                nos.add(0, noASerReposicionado);
                nos.add(iRealRepos, noDestino);
            } else {
               /* Para efetuar a rotação, primeiramente um nó é retirado; em seguida, os demais
                * nós do intervalo se movem na lista na direção do vazio deixado pelo nó retirado;
                * por fim o nó retirado é recolocado no novo espaço vazio que aparece na lista
                * após o deslocamento de todos os elementos do intervalo.
                */
                nos.remove(noASerReposicionado);
                nos.add(iRealDestino, noASerReposicionado);
            }
        }
        /* Este método ainda não está perfeito. Pode vir a dar problemas no futuro. (Celmar - 02/12/2003) */
    }

    
    /** Retorna o nó que apresenta o ângulo informado em um anel determinado.
     * Observação: Precisão do ângulo = 0.01
     * @param angulo Ângulo do nó a ser procurado.
     * @param anel Anel em que o nó deve ser procurado.
     * @return Retorna o nó, se existir, ou null caso contrário.
     */
    private NoPolar retornarNo(double angulo, boolean anel) {
        ListIterator li = nos.listIterator();
        double a1= Math.rint(angulo*100)/100;
        boolean achei = false;
        NoPolar no = null;
        while (li.hasNext() && !achei) {
            no = (NoPolar)li.next();
            achei = (no.retornarAnel() == anel && Math.rint(no.angulo*100)/100 == a1 && !no.eEscondido());
        }
        if (!achei) {
            no = null;
        }
        return no;
    }
    
    /** Retorna a posição de um nó na lista de nos do programa,
     * com relação aos nós do anel ao qual pertence.
     * Pode acontecer de os nós no início da lista não serem os nós de menor ângulo.
     * A primeira posição é zero.
     * Ignora nós escondidos.
     * @param no Nó a ser procurado.
     * @return Posição do nó, com relação aos demais nós de seu anel, na lista de nós "nos".
     */
    private int retornarPosicaoNoAnel(NoPolar no) {
        ListIterator li = nos.listIterator();
        int i = -1;
        boolean achei = false;
        NoPolar no2;
        while (li.hasNext() && !achei) {
            no2 = (NoPolar)li.next();
            if (no2.retornarAnel() == no.retornarAnel() && !no2.eEscondido()) {
                i++;
                achei = (no == no2);
            }
        }
        return i;
        
    }
    
//    /** Troca dois nós de posição na lista de nós.
//     * @param no1 Um dos nós a ser trocado de posição.
//     * @param no2 O outro nó a ser trocado de posição.
//     */
//    private void trocarNos(NoPolar no1, NoPolar no2) {
//        int i1 = nos.indexOf(no1);
//        int i2 = nos.indexOf(no2);
//        if (i1>=0 && i2>=0) {
//            nos.set(i1,no2);
//            nos.set(i2,no1);
//        } // else { ... um dos nós não existe na lista de nós do grafo. Não fazer troca. }
//    }
    
//    /** Informa qual nó vem antes do nó informado, considerando a seqüência de nós do
//     * anel ao qual ele pertence.
//     * @param no Nó cujo nó anterior deve ser buscado.
//     * @return Nó anterior ao nó informado. Se houver apenas 1 nó no anel, retorna o próprio
//     * nó. Se falhar, retorna null.
//     */
//    private NoPolar noAnterior(NoPolar no) {
//        NoPolar anterior = null;
//        switch (retornarTamanhoAnel(no.retornarAnel())) {
//            case 0:
//                anterior = null; // o que seria estranho, porque pelo menos "no" deve estar no anel.
//                break;
//            case 1:
//                if (!no.eEscondido()) {
//                    anterior = no; // se só há 1 nó no anel, entao o anterior é o próprio "no"
//                }
//                break;
//            default:
//                int i = nos.indexOf(no);
//                // Corrigir. Pode acontecer em casos extremos que trave o programa.
//                int limite = nos.size();
//                do {
//                    i = (nos.size() + i - 1) % nos.size();
//                    anterior = (NoPolar)nos.get(i);
//                    limite--;
//                } while ((anterior.retornarAnel() != no.retornarAnel() || (anterior.eEscondido()) ) && limite > 0);
//                if (anterior.retornarAnel() != no.retornarAnel() || (anterior.eEscondido()) ) {
//                    anterior = null;
//                }
//        }
//        
//        return anterior;
//    }
    
//    /** Informa qual nó vem após o nó informado, considerando a seqüência de nós do anel
//     * ao qual ele pertence.
//     * @param no Nó cujo nó posterior deve ser buscado.
//     * @return Nó posterior ao nó informado. Se houver apenas 1 nó no anel, retorna o próprio
//     * nó. Se falhar, retorna null.
//     */
//    private NoPolar noPosterior(NoPolar no) {
//        NoPolar posterior = null;
//        switch (retornarTamanhoAnel(no.retornarAnel())) {
//            case 0:
//                posterior = null; // o que seria estranho, porque pelo menos "no" deve estar no anel.
//                break;
//            case 1:
//                posterior = no; // se só há 1 nó no anel, entao o posterior é o próprio "no"
//                break;
//            default:
//                int i = nos.indexOf(no);
//                int limite = nos.size();
//                do {
//                    i = (i + 1) % nos.size();
//                    posterior = (NoPolar)nos.get(i);
//                    limite --;
//                } while ((posterior.retornarAnel() != no.retornarAnel() || posterior.eEscondido()) && limite>0);
//                if (posterior.retornarAnel() != no.retornarAnel() || posterior.eEscondido()) {
//                    posterior = null;
//                }
//        }
//        return posterior;
//    }
    
    
    /** Informa o tamanho do anel especificado.
     * @param anel Anel cujo tamanho deve ser retornado..
     * @return Tamanho do anel.
     */
    private int retornarTamanhoAnel(boolean anel) {
        int tamanhoAnel;
        if (anel==NoPolar.ANEL_CENTRAL) {
            tamanhoAnel=numeroNosCentrais.getValue();
        } else {
            tamanhoAnel=numeroNosPerifericos.getValue();
        }
        return tamanhoAnel;
    }
    
    // Métodos para seleção de arestas retas
    
    /* Dado um nó, um ponto M=(mx,my) e um anel, descobre a linha que passaria
     * por esses três elementos. A seguir, descobre o ângulo alfa do nó mais
     * próximo dessa linha no anel informado. 0<=alfa<360
     */
    /** Dado um nó noA, um ponto M=(xm,ym) e um anel A do grafo polar, verifica qual o
     * ângulo do ponto em A atingido pela reta que passa por M e noA.
     * @param noA Nó pelo qual passa a reta.
     * @param xm Abscissa do ponto pelo qual passa a reta.
     * @param ym Ordenada do ponto pelo qual passa a reta.
     * @param anel Anel atingido pela reta.
     * @return Ângulo do ponto atingido pela reta no anel especificado.
     */
    public double anguloAtingidoPelaRetaNoAnel(NoPolar noA, int xm, int ym, boolean anel) {
        
        double fiB1, fiB2, fiB, fiM, a, c, d1, d2, rm, rb, dam, xa, ya,
        dmb1, dab1, xb1, yb1, dmb2, dab2, xb2, yb2, anguloEscolhido, xb, yb ;
        double resposta = -1;
        
        if (noA !=null) {
            rm = PolarCartesiano.cartesiano2Polar_raio(xm, ym, centro);
            fiM = PolarCartesiano.cartesiano2Polar_angulo(xm, ym, centro);
            if (rm <= Math.max(raioPeriferico.getValue(), raioCentral.getValue())) {
                if (anel == NoPolar.ANEL_CENTRAL) {
                    if (numeroNosCentrais.getValue() == 1) {
                        rb = 0;
                    } else {
                        rb = raioCentral.getValue();
                    }
                } else {
                    rb = raioPeriferico.getValue();
                }
                
                if (rb==0) {
                    anguloEscolhido = 0; /// caso não tratado.
                    xb = centro.x;
                    yb = centro.y;
                    xa = PolarCartesiano.polar2Cartesiano_x(noA.raio, noA.angulo, centro);
                    ya = PolarCartesiano.polar2Cartesiano_y(noA.raio, noA.angulo, centro);
                    
                } else {
                    
                    if (noA.eCentral() && numeroNosCentrais.getValue()==1) {
                        fiB = fiM;
                        xa = centro.x;
                        ya = centro.y;
                    } else {
                        a = fiM - noA.angulo;
                        xa = PolarCartesiano.polar2Cartesiano_x(noA.raio, noA.angulo, centro);
                        ya = PolarCartesiano.polar2Cartesiano_y(noA.raio, noA.angulo, centro);
                        
                        dam =  PolarCartesiano.distanciaEntrePontos(xa,ya,xm,ym);
                        c = Math.toDegrees(Math.asin(rm / dam * Math.sin(Math.toRadians(a) ) ) );
                        
                        if (noA.raio - rm * Math.cos(Math.toRadians(a)) < 0) {
                            c = 180 - c;
                        }
                        
                        d1 = Math.toDegrees(Math.asin(rm / rb * Math.sin(Math.toRadians(a + c))));
                        d2 = 180 - d1;
                        fiB1 = 180 - c - d1 + noA.angulo;
                        fiB2 = 180 - c - d2 + noA.angulo;
                        
                        xb1 = PolarCartesiano.polar2Cartesiano_x(rb, fiB1, centro);
                        yb1 = PolarCartesiano.polar2Cartesiano_y(rb, fiB1, centro);
                        dmb1 = PolarCartesiano.distanciaEntrePontos(xm,ym,xb1,yb1);
                        dab1 = PolarCartesiano.distanciaEntrePontos(xa,ya,xb1,yb1);
                        xb2 = PolarCartesiano.polar2Cartesiano_x(rb, fiB2, centro);
                        yb2 = PolarCartesiano.polar2Cartesiano_y(rb, fiB2, centro);
                        dmb2 = PolarCartesiano.distanciaEntrePontos(xm,ym,xb2,yb2);
                        dab2 = PolarCartesiano.distanciaEntrePontos(xa,ya,xb2,yb2);
                        
                        // Teoricamente, dam + dmb1 - dab1 = 0 OU dam + dmb2 - dab2 = 0
                        // No entanto, isso não está acontecendo por problemas com precisão.
                        // Assim, para efetuar a escolha entre 1 ou 2, quem tiver o menor erro ganha.
                        if (dam + dmb1 - dab1 < dam + dmb2 - dab2) {
                            fiB = fiB1;
                        } else {
                            fiB = fiB2;
                        }
                    }
                    
                    double[] v;
                    v = retornarAngulosVizinhos(fiB, anel);
                    
                    double dv0,dv1;
                    dv0=PolarCartesiano.menorDistanciaEntreAngulos(fiB,v[0]);
                    dv1=PolarCartesiano.menorDistanciaEntreAngulos(fiB,v[1]);
                    
                    if (dv0<=dv1) {
                        anguloEscolhido=v[0];
                    } else {
                        anguloEscolhido=v[1];
                    }
                    
                }
                // Mouse deve estar suficientemente próximo da aresta em questão.
                xb = PolarCartesiano.polar2Cartesiano_x(rb, anguloEscolhido, centro);
                yb = PolarCartesiano.polar2Cartesiano_y(rb, anguloEscolhido, centro);
                
                
                boolean existe = pontoEstaProximoDaAresta(xm,ym,xa,ya,xb,yb) &&
                (anguloEscolhido!=noA.angulo || noA.retornarAnel() != anel);
                
                if (existe) {
                    resposta = anguloEscolhido;
                }
            }
        }
        
        return(resposta);
    }
    
    
    
    /** Dado um ângulo e um anel, retorna quais são os ângulos dos nós
     * imediatamente vizinhos deste ângulo no anel.
     * @param angulo Ângulo do qual se deve descobrir os vizinhos.
     * @param anel Anel no qual se deve descobrir os ângulos vizinhos.
     * @return array contendo os dois ângulos vizinhos ao ângulo informado.
     */
    public double[] retornarAngulosVizinhos(double angulo, boolean anel) {
        
        double anguloInicial;
        double distancia;
        
        double[] r = new double[2];
        
        double anguloNormalizado = Util.normalizarAngulo(angulo);
        
        if (anel==NoPolar.ANEL_CENTRAL) {
            anguloInicial=anguloInicialNosCentrais.getValue();
        } else {
            anguloInicial=anguloInicialNosPerifericos.getValue();
        }
        distancia=distanciaEntreNos(anel);
        
        int i;

        i = (int)((anguloNormalizado-anguloInicial) / distancia);
        r[0] = i * distancia + anguloInicial;
        r[1] = r[0] + distancia;
        r[0] = Util.normalizarAngulo(r[0]);
        r[1] = Util.normalizarAngulo(r[1]);
        return (r);
    }
    

    
    
    
    /** Dado um ponto M=(mx,my), calcula quais seriam as arestas de um grafo polar
     * completo que começariam no anel 1, passariam por esse ponto e atingiriam
     * o anel 2. Como nem todo grafo é completo, as arestas retornadas formam um
     * conjunto de arestas que possivelmente existem no grafo atual.
     * @param mx Abscissa de M.
     * @param my Ordenada de M.
     * @param anel1 Anel origem.
     * @param anel2 Anel destino.
     * @return Retorna um conjunto de chaves de possíveis arestas que sairiam de 
     * anel1, passariam por M e chegariam a anel2.
     */
    public Set retornarAngulosDePossiveisArestasEm(int mx, int my, boolean anel1, boolean anel2) {
        // anel1 - anel que será percorrido.
        // anel2 - anel onde o nó-destino será procurado
        // mx,my - mouse
        
        NoPolar n;
        Set s = new HashSet();
        
        //ParDeAngulos p;
        ChaveDeAresta ch;
        
        double angulo;
        for (int i = 0; i<nos.size(); i++) {
            n = (NoPolar)nos.get(i);
            if (n.retornarAnel() == anel1 && !n.eEscondido()) {
                angulo = anguloAtingidoPelaRetaNoAnel(n, mx, my, anel2);
                if (angulo != -1) {
                    //p = new ParDeAngulos(n.angulo, angulo);
                    ch = new ChaveDeAresta(n.angulo, anel1, angulo, anel2);
                    //s.add(p);
                    s.add(ch);
                }
            }
        }
        return (s);
        
    }
    
    
    /** Algoritmo linear ( O(n), n = número de nós ) para descobrir o conjunto de
     * arestas que passam por um ponto M=(xm,ym) no grafo polar.
     * @param xm Abscissa de M.
     * @param ym Ordenada de M.
     * @return Retorna o conjunto de arestas do grafo que passam pelo ponto M.
     */
    public Set retornarConjuntoArestasEmLinear(int xm, int ym) {
        Set s = new HashSet();
        Set conjuntoArestas = new HashSet();
        ArestaPolar a;
        ChaveDeAresta ch1, ch2;
        boolean anel1, anel2;
        
        for (int i=0; i<=3; i++) {
            switch (i) {
                case 0:
                    anel1 = NoPolar.ANEL_PERIFERICO;
                    anel2 = NoPolar.ANEL_PERIFERICO;
                    break;
                case 1:
                    anel1 = NoPolar.ANEL_CENTRAL;
                    anel2 = NoPolar.ANEL_PERIFERICO;
                    break;
                case 2:
                    anel1 = NoPolar.ANEL_CENTRAL;
                    anel2 = NoPolar.ANEL_CENTRAL;
                    break;
                case 3: // talvez desnecessario
                    anel1 = NoPolar.ANEL_PERIFERICO;
                    anel2 = NoPolar.ANEL_CENTRAL;
                    break;
                default:
                    anel1 = NoPolar.ANEL_PERIFERICO;
                    anel2 = NoPolar.ANEL_PERIFERICO;
            }
            s = retornarAngulosDePossiveisArestasEm(xm, ym, anel1, anel2);
            
            // Obs: Talvez possa ser simplificado... uma chave somente ja bastaria...
            for (Iterator si = s.iterator(); si.hasNext(); ) {
                ch1 = (ChaveDeAresta)si.next();
                ch2 = new ChaveDeAresta(ch1.angulo2, ch1.anel2, ch1.angulo1, ch1.anel1);
                a = (ArestaPolar)arestasHash.get(ch1);
                if (a!=null) {
                    if (a.eAtingida() && !a.eEscondida()) {
                        conjuntoArestas.add(a);
                    }
                } else {
                    a = (ArestaPolar)arestasHash.get(ch2);
                    if (a!=null) {
                        if (a.eAtingida() && !a.eEscondida()) {
                            conjuntoArestas.add(a);
                        }
                    }
                }
            }
        }
        conjuntoArestas.addAll(retornarConjuntoAutoArestasEm(xm,ym));
        return(conjuntoArestas);
    }
    
    /** Algoritmo quadrático ( O(n^2), n = número de nós ) para descobrir o conjunto de
     * arestas que passam por um ponto M=(xm,ym) no grafo.
     * @param xm Abscissa de M.
     * @param ym Ordenada de M.
     * @return Retorna o conjunto de arestas do grafo que passam pelo ponto M.
     */
    private Collection retornarConjuntoArestasEmQuadratico(int xm, int ym) {
        double xa,xb,ya,yb;
        HashSet conjuntoArestas = new HashSet();
        ArestaPolar a;
        NoPolar no1, no2;
        for (Iterator i = arestas.iterator(); i.hasNext();) {
            a = (ArestaPolar)i.next();
            if (!a.eEscondida()) {
                no1 = (NoPolar)(a.no1);
                no2 = (NoPolar)(a.no2);
                xa = PolarCartesiano.polar2Cartesiano_x(no1.raio, no1.angulo, centro);
                ya = PolarCartesiano.polar2Cartesiano_y(no1.raio, no1.angulo, centro);
                xb = PolarCartesiano.polar2Cartesiano_x(no2.raio, no2.angulo, centro);
                yb = PolarCartesiano.polar2Cartesiano_y(no2.raio, no2.angulo, centro);
                if (a.eAtingida() && pontoEstaProximoDaAresta(xm,ym,xa,ya,xb,yb)) {
                    conjuntoArestas.add(a);
                }
            }
        }
        return (conjuntoArestas);
    }
    
    /** Algoritmo linear (O(n), n = número de nós) para descobrir o conjunto de
     * auto-arestas (arestas cujas extremidades incidem sobre o mesmo nó) que passam
     * pelo ponto M=(xm,ym).
     * @param xm Abscissa de M.
     * @param ym Ordenada de M.
     * @return Retorna o conjunto de auto-arestas do grafo que passam pelo ponto M.
     */
    public Collection retornarConjuntoAutoArestasEm(int xm, int ym) {
        HashSet conjuntoArestas = new HashSet();
        NoPolar no;
        ArestaPolar aresta;
        ChaveDeAresta ch;
        double x, y, r, rm;
        final int delta = 3;
        for (int i = 0; i<nos.size(); i++) {
            no = (NoPolar)nos.get(i);
            if (!no.eEscondido() ) {
                r = no.raio + ArestaPolar.raioArestaCircular;
                x = PolarCartesiano.polar2Cartesiano_x(r,no.angulo, centro);
                y = PolarCartesiano.polar2Cartesiano_y(r,no.angulo, centro);
                rm = PolarCartesiano.distanciaEntrePontos(x,y,xm,ym);
                if (ArestaPolar.raioArestaCircular-delta <= rm && rm <=ArestaPolar.raioArestaCircular+delta) {
                    ch = new ChaveDeAresta(no.angulo, no.retornarAnel(),no.angulo, no.retornarAnel());
                    aresta = (ArestaPolar)arestasHash.get(ch);
                    if (aresta!=null) {
                        if (!aresta.eEscondida() && aresta.eAtingida()) {
                            conjuntoArestas.add(aresta);
                        }
                    }
                }
            }
        }
        return (conjuntoArestas);
    }
    
    /** Retorna o conjunto de arestas em um ponto M = (xm,ym)
     * @param xm Abscissa do ponto M.
     * @param ym Ordenada do ponto M.
     * @return Conjunto de arestas que passam pelo ponto M.
     */
    public Collection retornarConjuntoArestasEm(int xm, int ym) {
        Collection resposta;
        if (nos.size()<arestas.size()) {
            resposta = retornarConjuntoArestasEmLinear(xm, ym);
        } else {
            resposta = retornarConjuntoArestasEmQuadratico(xm, ym);
        }
        resposta.addAll(retornarConjuntoAutoArestasEm(xm,ym));
        return(resposta);
    }
    
    /** Limpa o grafo, removendo arestas, nós e grupos.
     */
    public void limparGrafo() {
        super.limparGrafo();
        numeroNosCentrais.setValue(0);
        numeroNosPerifericos.setValue(0);
    }
    
}