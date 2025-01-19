@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manual do Usuário</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="accordion" id="manualAccordion">
                        <!-- Introdução -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#introducao">
                                    1. Introdução
                                </button>
                            </h2>
                            <div id="introducao" class="accordion-collapse collapse show" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <p>Bem-vindo ao Sistema de Gestão Pública. Este manual fornece instruções detalhadas sobre como utilizar todas as funcionalidades do sistema.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Categorias -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#categorias">
                                    2. Gerenciamento de Categorias
                                </button>
                            </h2>
                            <div id="categorias" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <h5>2.1. Estrutura Hierárquica</h5>
                                    <p>As categorias seguem uma estrutura hierárquica:</p>
                                    <ul>
                                        <li>Fonte</li>
                                        <li>Bloco</li>
                                        <li>Grupo</li>
                                        <li>Ação</li>
                                    </ul>

                                    <h5>2.2. Criando Categorias</h5>
                                    <p>Para criar uma nova categoria:</p>
                                    <ol>
                                        <li>Acesse o menu "Categorias"</li>
                                        <li>Clique em "Nova Categoria"</li>
                                        <li>Preencha os campos necessários</li>
                                        <li>Selecione o tipo da categoria</li>
                                        <li>Se aplicável, selecione a categoria pai</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Receitas -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#receitas">
                                    3. Gerenciamento de Receitas
                                </button>
                            </h2>
                            <div id="receitas" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <h5>3.1. Registrando Receitas</h5>
                                    <p>Para registrar uma nova receita:</p>
                                    <ol>
                                        <li>Acesse o menu "Receitas"</li>
                                        <li>Clique em "Nova Receita"</li>
                                        <li>Preencha a descrição e o valor</li>
                                        <li>Selecione a data</li>
                                        <li>Escolha as categorias apropriadas</li>
                                        <li>Adicione observações se necessário</li>
                                    </ol>

                                    <h5>3.2. Consultando Receitas</h5>
                                    <p>Você pode visualizar todas as receitas na listagem principal, com opções para editar ou excluir cada registro.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Despesas -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#despesas">
                                    4. Gerenciamento de Despesas
                                </button>
                            </h2>
                            <div id="despesas" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <h5>4.1. Registrando Despesas</h5>
                                    <p>Para registrar uma nova despesa:</p>
                                    <ol>
                                        <li>Acesse o menu "Despesas"</li>
                                        <li>Clique em "Nova Despesa"</li>
                                        <li>Preencha a descrição e o valor</li>
                                        <li>Selecione a data</li>
                                        <li>Escolha a classificação da despesa</li>
                                        <li>Selecione as categorias apropriadas</li>
                                        <li>Adicione observações se necessário</li>
                                    </ol>

                                    <h5>4.2. Consultando Despesas</h5>
                                    <p>Você pode visualizar todas as despesas na listagem principal, com opções para editar ou excluir cada registro.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Relatórios -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#relatorios">
                                    5. Relatórios
                                </button>
                            </h2>
                            <div id="relatorios" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <h5>5.1. Tipos de Relatórios</h5>
                                    <ul>
                                        <li>Balanço Financeiro</li>
                                        <li>Relatório de Receitas</li>
                                        <li>Relatório de Despesas</li>
                                    </ul>

                                    <h5>5.2. Gerando Relatórios</h5>
                                    <p>Para gerar um relatório:</p>
                                    <ol>
                                        <li>Acesse o menu "Relatórios"</li>
                                        <li>Selecione o tipo de relatório</li>
                                        <li>Defina o período desejado</li>
                                        <li>Escolha o formato de saída (PDF, Excel)</li>
                                        <li>Clique em "Gerar Relatório"</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Administração -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#administracao">
                                    6. Administração
                                </button>
                            </h2>
                            <div id="administracao" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <h5>6.1. Gerenciamento de Usuários</h5>
                                    <p>Administradores podem:</p>
                                    <ul>
                                        <li>Criar novos usuários</li>
                                        <li>Editar usuários existentes</li>
                                        <li>Definir permissões (admin/operador)</li>
                                        <li>Desativar usuários</li>
                                    </ul>

                                    <h5>6.2. Configurações da Prefeitura</h5>
                                    <p>Configure informações como:</p>
                                    <ul>
                                        <li>Nome da cidade</li>
                                        <li>Nome da prefeitura</li>
                                        <li>Endereço</li>
                                        <li>Contatos</li>
                                        <li>Nome do prefeito</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Logs de Auditoria -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#auditoria">
                                    7. Logs de Auditoria
                                </button>
                            </h2>
                            <div id="auditoria" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <h5>7.1. Consultando Logs</h5>
                                    <p>Para consultar os logs de auditoria:</p>
                                    <ol>
                                        <li>Acesse o menu "Logs de Auditoria"</li>
                                        <li>Use os filtros disponíveis:
                                            <ul>
                                                <li>Tipo de registro</li>
                                                <li>Ação realizada</li>
                                                <li>Usuário</li>
                                                <li>Período</li>
                                            </ul>
                                        </li>
                                        <li>Clique em "Detalhes" para ver mais informações sobre cada log</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Histórico de Atualizações -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#atualizacoes">
                                    8. Histórico de Atualizações
                                </button>
                            </h2>
                            <div id="atualizacoes" class="accordion-collapse collapse" data-bs-parent="#manualAccordion">
                                <div class="accordion-body">
                                    <h5>8.1. Versão 1.0.0 (15/03/2024)</h5>
                                    <ul>
                                        <li>Lançamento inicial do sistema</li>
                                        <li>Implementação do controle de despesas e receitas</li>
                                        <li>Sistema de categorias hierárquicas</li>
                                        <li>Relatórios financeiros básicos</li>
                                        <li>Sistema de autenticação e autorização</li>
                                        <li>Configurações por município</li>
                                    </ul>

                                    <h5>8.2. Versão 1.1.0 (Atual)</h5>
                                    <ul>
                                        <li>Adição de logs de auditoria</li>
                                        <li>Melhorias na interface do usuário</li>
                                        <li>Implementação de relatórios avançados</li>
                                        <li>Sistema de backup automático</li>
                                        <li>Documentação completa do sistema</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4 text-muted">
                <small>KL Gestor Pub v1.1.0</small>
            </div>
        </main>
    </div>
</div>
@endsection 