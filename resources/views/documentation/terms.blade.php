@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        @include('layouts.sidebar')
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Termos de Uso</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3>1. Aceitação dos Termos</h3>
                    <p>Ao acessar e utilizar o Sistema de Gestão Pública, você concorda com estes Termos de Uso e todas as leis e regulamentos aplicáveis.</p>

                    <h3>2. Uso do Sistema</h3>
                    <p>O sistema deve ser utilizado exclusivamente para:</p>
                    <ul>
                        <li>Gestão de receitas e despesas públicas</li>
                        <li>Geração de relatórios financeiros</li>
                        <li>Classificação e categorização de transações</li>
                        <li>Atividades administrativas autorizadas</li>
                    </ul>

                    <h3>3. Responsabilidades do Usuário</h3>
                    <p>Ao utilizar o sistema, você concorda em:</p>
                    <ul>
                        <li>Manter a confidencialidade de suas credenciais de acesso</li>
                        <li>Fornecer informações precisas e verdadeiras</li>
                        <li>Não compartilhar seu acesso com terceiros</li>
                        <li>Reportar imediatamente qualquer uso não autorizado</li>
                        <li>Seguir as políticas e procedimentos estabelecidos</li>
                    </ul>

                    <h3>4. Restrições de Uso</h3>
                    <p>É expressamente proibido:</p>
                    <ul>
                        <li>Usar o sistema para fins não autorizados</li>
                        <li>Tentar acessar áreas restritas sem permissão</li>
                        <li>Realizar ações que possam comprometer a segurança</li>
                        <li>Modificar ou adulterar dados sem autorização</li>
                    </ul>

                    <h3>5. Propriedade Intelectual</h3>
                    <p>Todo o conteúdo do sistema, incluindo:</p>
                    <ul>
                        <li>Código-fonte</li>
                        <li>Interface gráfica</li>
                        <li>Documentação</li>
                        <li>Marcas e logotipos</li>
                    </ul>
                    <p>São protegidos por direitos autorais e outras leis de propriedade intelectual.</p>

                    <h3>6. Disponibilidade e Manutenção</h3>
                    <p>O sistema pode estar indisponível ocasionalmente para:</p>
                    <ul>
                        <li>Manutenções programadas</li>
                        <li>Atualizações de segurança</li>
                        <li>Correções de problemas</li>
                        <li>Melhorias no sistema</li>
                    </ul>

                    <h3>7. Responsabilidade Legal</h3>
                    <p>Os usuários são legalmente responsáveis por:</p>
                    <ul>
                        <li>Ações realizadas com suas credenciais</li>
                        <li>Precisão dos dados inseridos</li>
                        <li>Conformidade com leis e regulamentos</li>
                        <li>Uso adequado das informações acessadas</li>
                    </ul>

                    <h3>8. Alterações nos Termos</h3>
                    <p>Estes termos podem ser atualizados periodicamente. Alterações significativas serão comunicadas aos usuários.</p>

                    <div class="text-muted mt-4">
                        <p>Última atualização: Janeiro de 2025</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection 