@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manual do Usuário</h1>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2>Relatórios</h2>

                    <h3>Gerando Relatórios</h3>
                    <ol>
                        <li>Acesse o menu "Relatórios"</li>
                        <li>Selecione o tipo de relatório:
                            <ul>
                                <li><strong>Receitas</strong>: Visualize todas as receitas do período</li>
                                <li><strong>Despesas</strong>: Analise todas as despesas do período</li>
                                <li><strong>Balanço</strong>: Compare receitas e despesas</li>
                                <li><strong>Classificação de Despesas</strong>: Analise despesas por classificação</li>
                            </ul>
                        </li>
                        <li>Escolha o período desejado</li>
                        <li>Selecione o agrupamento (Diário, Mensal ou Anual)</li>
                        <li>Aplique os filtros desejados (opcional):
                            <ul>
                                <li>Fonte</li>
                                <li>Bloco</li>
                                <li>Grupo</li>
                                <li>Ação</li>
                                <li>Classificação de Despesa (apenas para relatórios de despesas)</li>
                            </ul>
                        </li>
                        <li>Escolha o formato de saída:
                            <ul>
                                <li>Visualizar na tela</li>
                                <li>Exportar para PDF</li>
                                <li>Exportar para Excel</li>
                            </ul>
                        </li>
                    </ol>

                    <h3>Relatórios em Excel</h3>
                    <ul>
                        <li>Os relatórios em Excel mostram o valor total logo abaixo do título</li>
                        <li>Para relatórios de balanço, são exibidos os totais de:
                            <ul>
                                <li>Receitas</li>
                                <li>Despesas</li>
                                <li>Saldo</li>
                            </ul>
                        </li>
                        <li>Os valores são formatados no padrão monetário brasileiro (R$)</li>
                        <li>É possível visualizar os filtros aplicados ao final do relatório</li>
                    </ul>

                    <h2>Histórico de Versões</h2>

                    <h3>Versão 1.0.0 (08/01/2024)</h3>
                    <ul>
                        <li>Lançamento inicial do sistema</li>
                        <li>Implementação do controle de despesas e receitas</li>
                        <li>Sistema de categorias hierárquicas</li>
                        <li>Relatórios financeiros básicos</li>
                        <li>Sistema de autenticação e autorização</li>
                        <li>Configurações por município</li>
                    </ul>

                    <h3>Versão 1.1.0 (25/01/2024)</h3>
                    <ul>
                        <li>Adição de logs de auditoria</li>
                        <li>Melhorias na interface do usuário</li>
                        <li>Implementação de relatórios avançados</li>
                        <li>Sistema de backup automático</li>
                        <li>Documentação completa do sistema</li>
                    </ul>

                    <h3>Versão 1.2.0 (10/02/2024)</h3>
                    <ul>
                        <li>Simplificação do sistema de relatórios</li>
                        <li>Remoção dos relatórios por categoria e personalizados</li>
                        <li>Foco em relatórios essenciais: Receitas, Despesas, Balanço e Classificação de Despesas</li>
                        <li>Melhorias na performance dos relatórios</li>
                        <li>Otimização da interface de usuário</li>
                    </ul>

                    <h3>Versão 1.3.0 (26/08/2025)</h3>
                    <ul>
                        <li>Melhorias na organização do código</li>
                        <li>Refatoração do ReportController para utilizar serviços</li>
                        <li>Otimizações de performance</li>
                        <li>Melhorias na interface do usuário</li>
                        <li>Atualização da documentação</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection