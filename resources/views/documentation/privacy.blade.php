@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Política de Privacidade</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>1. Introdução</h3>
                    <p>Esta Política de Privacidade descreve como o Sistema de Gestão Pública coleta, usa, armazena e protege suas informações pessoais.</p>

                    <h3>2. Coleta de Dados</h3>
                    <p>Coletamos os seguintes tipos de informações:</p>
                    <ul>
                        <li>Informações de cadastro (nome, e-mail)</li>
                        <li>Dados de acesso ao sistema</li>
                        <li>Registros de atividades (logs)</li>
                    </ul>

                    <h3>3. Uso dos Dados</h3>
                    <p>Utilizamos seus dados para:</p>
                    <ul>
                        <li>Autenticação e controle de acesso</li>
                        <li>Auditoria de ações realizadas</li>
                        <li>Melhorias no sistema</li>
                        <li>Comunicações importantes sobre o sistema</li>
                    </ul>

                    <h3>4. Proteção de Dados</h3>
                    <p>Implementamos medidas de segurança para proteger suas informações:</p>
                    <ul>
                        <li>Criptografia de dados sensíveis</li>
                        <li>Controle de acesso baseado em funções</li>
                        <li>Monitoramento de atividades suspeitas</li>
                        <li>Backups regulares e seguros</li>
                    </ul>

                    <h3>5. Compartilhamento de Dados</h3>
                    <p>Seus dados podem ser compartilhados:</p>
                    <ul>
                        <li>Entre departamentos autorizados da prefeitura</li>
                        <li>Com órgãos reguladores quando exigido por lei</li>
                        <li>Com prestadores de serviços essenciais ao funcionamento do sistema</li>
                    </ul>

                    <h3>6. Seus Direitos</h3>
                    <p>Você tem direito a:</p>
                    <ul>
                        <li>Acessar seus dados pessoais</li>
                        <li>Solicitar correções de informações incorretas</li>
                        <li>Solicitar a exclusão de seus dados (quando aplicável)</li>
                        <li>Ser informado sobre o uso de seus dados</li>
                    </ul>

                    <h3>7. Contato</h3>
                    <p>Para questões relacionadas à privacidade, entre em contato com o Encarregado de Proteção de Dados (DPO) através do e-mail [email do DPO].</p>

                    <h3>8. Atualizações</h3>
                    <p>Esta política pode ser atualizada periodicamente. A versão mais recente estará sempre disponível no sistema.</p>

                    <div class="text-muted mt-4">
                        <p>Última atualização: Janeiro de 2025</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 