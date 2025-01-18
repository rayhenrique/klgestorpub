@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">LGPD - Lei Geral de Proteção de Dados</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>1. Introdução à LGPD</h3>
                    <p>A Lei Geral de Proteção de Dados (Lei nº 13.709/2018) estabelece regras sobre coleta, armazenamento, tratamento e compartilhamento de dados pessoais.</p>

                    <h3>2. Princípios da LGPD</h3>
                    <p>Nosso sistema segue os princípios fundamentais da LGPD:</p>
                    <ul>
                        <li><strong>Finalidade:</strong> Propósito específico para o tratamento dos dados</li>
                        <li><strong>Adequação:</strong> Compatibilidade com as finalidades informadas</li>
                        <li><strong>Necessidade:</strong> Limitação ao mínimo necessário</li>
                        <li><strong>Livre acesso:</strong> Garantia de consulta facilitada</li>
                        <li><strong>Qualidade dos dados:</strong> Garantia de exatidão</li>
                        <li><strong>Transparência:</strong> Informações claras sobre o tratamento</li>
                        <li><strong>Segurança:</strong> Medidas técnicas e administrativas de proteção</li>
                        <li><strong>Prevenção:</strong> Medidas para prevenir danos</li>
                        <li><strong>Não discriminação:</strong> Impossibilidade de tratamento discriminatório</li>
                        <li><strong>Responsabilização:</strong> Demonstração de medidas eficazes</li>
                    </ul>

                    <h3>3. Bases Legais</h3>
                    <p>O tratamento de dados pessoais é realizado com base em:</p>
                    <ul>
                        <li>Cumprimento de obrigação legal</li>
                        <li>Execução de políticas públicas</li>
                        <li>Interesse legítimo da administração pública</li>
                        <li>Consentimento do titular (quando aplicável)</li>
                    </ul>

                    <h3>4. Direitos do Titular</h3>
                    <p>Em conformidade com a LGPD, garantimos os seguintes direitos:</p>
                    <ul>
                        <li>Confirmação da existência de tratamento</li>
                        <li>Acesso aos dados</li>
                        <li>Correção de dados incompletos ou desatualizados</li>
                        <li>Anonimização ou bloqueio de dados desnecessários</li>
                        <li>Portabilidade dos dados</li>
                        <li>Revogação do consentimento</li>
                    </ul>

                    <h3>5. Medidas de Segurança</h3>
                    <p>Implementamos as seguintes medidas de proteção:</p>
                    <ul>
                        <li>Criptografia de dados sensíveis</li>
                        <li>Controle de acesso rigoroso</li>
                        <li>Registro de atividades (logs)</li>
                        <li>Backup seguro dos dados</li>
                        <li>Treinamento da equipe</li>
                        <li>Políticas de segurança da informação</li>
                    </ul>

                    <h3>6. Compartilhamento de Dados</h3>
                    <p>O compartilhamento de dados ocorre apenas:</p>
                    <ul>
                        <li>Entre órgãos públicos autorizados</li>
                        <li>Para cumprimento de obrigações legais</li>
                        <li>Com prestadores de serviços essenciais</li>
                        <li>Mediante termo de confidencialidade</li>
                    </ul>

                    <h3>7. Incidentes de Segurança</h3>
                    <p>Em caso de incidentes:</p>
                    <ul>
                        <li>Notificação à ANPD quando necessário</li>
                        <li>Comunicação aos titulares afetados</li>
                        <li>Medidas de contenção e correção</li>
                        <li>Registro e documentação do incidente</li>
                    </ul>

                    <h3>8. Contato DPO</h3>
                    <p>O Encarregado de Proteção de Dados (DPO) pode ser contatado através:</p>
                    <ul>
                        <li>E-mail: [email do DPO]</li>
                        <li>Telefone: [telefone do DPO]</li>
                        <li>Endereço: [endereço para correspondência]</li>
                    </ul>

                    <div class="text-muted mt-4">
                        <p>Última atualização: Janeiro de 2025</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 