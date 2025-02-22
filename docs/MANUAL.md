# Manual do Usuário - KL Gestor Pub

## Relatórios

### Gerando Relatórios
1. Acesse o menu "Relatórios"
2. Selecione o tipo de relatório:
   - **Receitas**: Visualize todas as receitas do período
   - **Despesas**: Analise todas as despesas do período
   - **Balanço**: Compare receitas e despesas
   - **Classificação de Despesas**: Analise despesas por classificação
3. Escolha o período desejado
4. Selecione o agrupamento (Diário, Mensal ou Anual)
5. Aplique os filtros desejados (opcional):
   - Fonte
   - Bloco
   - Grupo
   - Ação
   - Classificação de Despesa (apenas para relatórios de despesas)
6. Escolha o formato de saída:
   - Visualizar na tela
   - Exportar para PDF
   - Exportar para Excel

### Relatórios em Excel
- Os relatórios em Excel mostram o valor total logo abaixo do título
- Para relatórios de balanço, são exibidos os totais de:
  - Receitas
  - Despesas
  - Saldo
- Os valores são formatados no padrão monetário brasileiro (R$)
- É possível visualizar os filtros aplicados ao final do relatório

### Relatórios em PDF
- Os relatórios em PDF agora incluem informações detalhadas:
  - Fonte
  - Bloco
  - Grupo
  - Ação
  - Valores formatados no padrão monetário brasileiro
  - Datas no formato brasileiro (dd/mm/yyyy)
- O layout foi otimizado para melhor legibilidade
- Totalizadores são exibidos ao final do relatório
- Filtros aplicados são mostrados no cabeçalho

## Histórico de Versões

### Versão 1.0.0 (08/01/2024)
- Lançamento inicial do sistema
- Implementação do controle de despesas e receitas
- Sistema de categorias hierárquicas
- Relatórios financeiros básicos
- Sistema de autenticação e autorização
- Configurações por município

### Versão 1.1.0 (25/01/2024)
- Adição de logs de auditoria
- Melhorias na interface do usuário
- Implementação de relatórios avançados
- Sistema de backup automático
- Documentação completa do sistema

### Versão 1.2.0 (10/02/2024)
- Simplificação do sistema de relatórios
- Remoção dos relatórios por categoria e personalizados
- Foco em relatórios essenciais: Receitas, Despesas, Balanço e Classificação de Despesas
- Melhorias na performance dos relatórios
- Otimização da interface de usuário

### Versão 1.2.0 (11/02/2025)
- Melhorias nos relatórios em PDF
  - Adição das colunas: Fonte, Bloco, Grupo e Ação
  - Formatação de datas no padrão brasileiro
  - Layout otimizado para melhor visualização
- Padronização da formatação de datas:
  - Diário: dd/mm/yyyy (exemplo: 11/02/2025)
  - Mensal: mm/yyyy (exemplo: 02/2025)
  - Anual: yyyy (exemplo: 2025)