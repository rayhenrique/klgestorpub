# Manual do Usuário - KL Gestor Pub v1.4.0

## 📖 Índice

1. [Introdução](#introdução)
2. [Primeiros Passos](#primeiros-passos)
3. [Dashboard](#dashboard)
4. [Gestão de Receitas](#gestão-de-receitas)
5. [Gestão de Despesas](#gestão-de-despesas)
6. [Sistema de Categorias](#sistema-de-categorias)
7. [Relatórios](#relatórios)
8. [Auditoria](#auditoria)
9. [Configurações](#configurações)
10. [FAQ e Troubleshooting](#faq-e-troubleshooting)

---

## 🎯 Introdução

O **KL Gestor Pub** é um sistema completo para gestão de contas públicas municipais, desenvolvido para facilitar o controle financeiro, geração de relatórios e auditoria de operações. Este manual irá guiá-lo através de todas as funcionalidades do sistema.

### Características Principais:
- ✅ Controle detalhado de receitas e despesas
- ✅ Sistema hierárquico de categorização
- ✅ Relatórios avançados com exportação
- ✅ Auditoria completa de operações
- ✅ Interface em português brasileiro
- ✅ Design responsivo para todos os dispositivos

---

## 🚀 Primeiros Passos

### Acesso ao Sistema
1. **Abra seu navegador** e acesse a URL do sistema
2. **Faça login** com suas credenciais:
   - Email: seu email cadastrado
   - Senha: sua senha pessoal
3. **Aguarde o carregamento** do dashboard principal

### Tipos de Usuário

#### 👑 **Administrador**
- Acesso total ao sistema
- Pode criar e gerenciar usuários
- Configura categorias e classificações
- Acessa todas as funcionalidades

#### 👤 **Operador**
- Cadastra receitas e despesas
- Gera relatórios
- Visualiza auditoria
- Não pode gerenciar usuários ou configurações

---

## 📊 Dashboard

O dashboard é a tela principal do sistema, oferecendo uma visão geral das finanças municipais.

### Métricas Principais

#### 💰 **Cards de Resumo**
- **Receitas do Período**: Total de receitas no período selecionado
- **Despesas do Período**: Total de despesas no período selecionado
- **Saldo**: Diferença entre receitas e despesas
- **Variação %**: Comparação com o período anterior

#### 📈 **Gráficos Interativos**
- **Gráfico de Tendência**: Evolução das receitas e despesas nos últimos 6 períodos
- **Gráfico de Categorias**: Top 5 categorias de despesas do período

#### 📋 **Últimas Transações**
- Lista das 5 transações mais recentes
- Tipo (Receita/Despesa), valor e data
- Links diretos para edição

### Filtros de Período
- **Mensal**: Dados do mês atual vs mês anterior
- **Trimestral**: Dados do trimestre atual vs anterior
- **Anual**: Dados do ano atual vs ano anterior

---

## 💰 Gestão de Receitas

### Cadastrando uma Nova Receita

1. **Acesse o menu "Receitas"**
2. **Clique em "Nova Receita"**
3. **Preencha os campos obrigatórios:**
   - **Descrição**: Descrição clara da receita
   - **Valor**: Valor em reais (use vírgula para decimais)
   - **Data**: Data da receita (não pode ser futura)
   - **Categorização**: Selecione hierarquicamente:
     - Fonte → Bloco → Grupo → Ação
   - **Observações**: Campo opcional para detalhes adicionais

4. **Clique em "Salvar"**

### Listagem de Receitas

#### Funcionalidades da Lista:
- **Busca**: Digite qualquer termo para filtrar
- **Ordenação**: Clique nos cabeçalhos das colunas
- **Paginação**: Navegue entre as páginas
- **Ações**: Editar ou excluir cada receita

#### Filtros Disponíveis:
- **Por período**: Data inicial e final
- **Por categoria**: Fonte, Bloco, Grupo ou Ação
- **Por valor**: Faixa de valores

### Editando uma Receita

1. **Na listagem**, clique no ícone de edição (✏️)
2. **Modifique os campos** desejados
3. **Clique em "Atualizar"**

> ⚠️ **Importante**: Todas as alterações são registradas no log de auditoria

### Excluindo uma Receita

1. **Na listagem**, clique no ícone de exclusão (🗑️)
2. **Confirme a exclusão** no modal
3. **A receita será removida** permanentemente

---

## 💸 Gestão de Despesas

### Cadastrando uma Nova Despesa

1. **Acesse o menu "Despesas"**
2. **Clique em "Nova Despesa"**
3. **Preencha os campos obrigatórios:**
   - **Descrição**: Descrição clara da despesa
   - **Valor**: Valor em reais (use vírgula para decimais)
   - **Data**: Data da despesa (não pode ser futura)
   - **Categorização**: Selecione hierarquicamente:
     - Fonte → Bloco → Grupo → Ação
   - **Classificação**: Tipo de despesa (Corrente, Capital, etc.)
   - **Observações**: Campo opcional para detalhes adicionais

4. **Clique em "Salvar"**

### Diferenças das Receitas

- **Campo adicional**: Classificação de Despesa
- **Validações específicas**: Regras de negócio para despesas públicas
- **Relatórios especializados**: Relatórios por classificação

---

## 🏷️ Sistema de Categorias

### Estrutura Hierárquica

O sistema utiliza uma estrutura de 4 níveis:

```
Fonte (Nível 1)
└── Bloco (Nível 2)
    └── Grupo (Nível 3)
        └── Ação (Nível 4)
```

### Exemplos Práticos:

**Receitas:**
- Fonte: "Receitas Correntes"
  - Bloco: "Receita Tributária"
    - Grupo: "Impostos"
      - Ação: "IPTU"

**Despesas:**
- Fonte: "Despesas Correntes"
  - Bloco: "Pessoal e Encargos"
    - Grupo: "Pessoal Civil"
      - Ação: "Vencimentos e Vantagens Fixas"

### Gerenciando Categorias (Admin)

#### Criando uma Nova Categoria:
1. **Acesse "Categorias"**
2. **Clique em "Nova Categoria"**
3. **Preencha:**
   - Nome da categoria
   - Código (opcional)
   - Tipo (fonte, bloco, grupo, ação)
   - Categoria pai (se aplicável)
4. **Salve a categoria**

#### Seleção Dinâmica:
- As subcategorias são carregadas automaticamente
- Apenas categorias ativas são exibidas
- Validação de hierarquia é aplicada

---

## 📊 Relatórios

### Tipos de Relatórios Disponíveis

#### 1. **Relatório de Receitas**
- Lista todas as receitas do período
- Agrupamento por data/período
- Totalizadores por categoria

#### 2. **Relatório de Despesas**
- Lista todas as despesas do período
- Inclui classificação de despesas
- Análise por categoria e classificação

#### 3. **Relatório de Balanço**
- Comparativo receitas vs despesas
- Cálculo automático do saldo
- Análise de variações

#### 4. **Relatório por Classificação de Despesas**
- Agrupamento por tipo de despesa
- Análise de distribuição
- Comparativos percentuais

### Gerando um Relatório

1. **Acesse o menu "Relatórios"**
2. **Selecione o tipo de relatório**
3. **Configure os parâmetros:**
   - **Período**: Data inicial e final
   - **Agrupamento**: Diário, Mensal ou Anual
   - **Filtros** (opcionais):
     - Fonte, Bloco, Grupo, Ação
     - Classificação de despesa
4. **Escolha o formato:**
   - Visualizar na tela
   - Exportar para PDF
   - Exportar para Excel
5. **Clique em "Gerar Relatório"**

### Formatos de Agrupamento

#### 📅 **Diário**
- Dados agrupados por dia
- Formato: dd/mm/yyyy
- Ideal para análises detalhadas

#### 📆 **Mensal**
- Dados agrupados por mês
- Formato: mm/yyyy
- Ideal para acompanhamento mensal

#### 🗓️ **Anual**
- Dados agrupados por ano
- Formato: yyyy
- Ideal para análises de longo prazo

### 📄 Exportação para PDF

#### Características dos PDFs:
- **Layout Profissional**: Cabeçalho com logo e informações municipais
- **Dados Completos**: Fonte, Bloco, Grupo, Ação para cada item
- **Formatação Brasileira**: 
  - Valores em R$ (Real brasileiro)
  - Datas no formato dd/mm/yyyy
- **Totalizadores**: Valores totais ao final do relatório
- **Filtros Aplicados**: Mostrados no cabeçalho para referência
- **Paginação**: Numeração automática de páginas

#### Quando Usar PDF:
- Apresentações oficiais
- Arquivo para impressão
- Documentação formal
- Prestação de contas

### 📊 Exportação para Excel

#### Características dos Excels:
- **Dados Estruturados**: Planilha organizada com cabeçalhos
- **Totalizadores**: Valores totais calculados automaticamente
- **Formatação**: Valores monetários e datas formatados
- **Filtros**: Informações sobre filtros aplicados
- **Editável**: Permite análises adicionais

#### Quando Usar Excel:
- Análises detalhadas
- Cruzamento de dados
- Gráficos personalizados
- Relatórios internos

### 💡 Dicas para Relatórios

- **Performance**: Evite períodos muito longos com agrupamento diário
- **Filtros**: Use filtros para focar em categorias específicas
- **Comparação**: Gere relatórios de períodos similares para comparar
- **Backup**: Mantenha cópias dos relatórios importantes

---

## 🔍 Auditoria

O sistema de auditoria registra todas as operações realizadas, garantindo transparência e rastreabilidade.

### O que é Registrado

#### 📝 **Operações Auditadas:**
- Criação, edição e exclusão de receitas
- Criação, edição e exclusão de despesas
- Alterações em categorias (admin)
- Criação e edição de usuários (admin)
- Mudanças em configurações (admin)

#### 📊 **Informações Registradas:**
- **Usuário**: Quem realizou a ação
- **Ação**: Tipo de operação (criar, editar, excluir)
- **Data/Hora**: Timestamp preciso da operação
- **Dados Antigos**: Valores antes da alteração
- **Dados Novos**: Valores após a alteração
- **IP**: Endereço IP do usuário

### Acessando os Logs de Auditoria

1. **Acesse o menu "Auditoria"**
2. **Visualize a timeline** de operações
3. **Use os filtros** para encontrar registros específicos:
   - Por usuário
   - Por período
   - Por tipo de ação
   - Por modelo (receita, despesa, etc.)

### Interpretando os Logs

#### 🟢 **Criação (Create)**
- Ícone: ➕
- Cor: Verde
- Mostra todos os dados do novo registro

#### 🟡 **Edição (Update)**
- Ícone: ✏️
- Cor: Amarelo
- Mostra comparação antes/depois

#### 🔴 **Exclusão (Delete)**
- Ícone: 🗑️
- Cor: Vermelho
- Mostra todos os dados do registro excluído

### Detalhes Expandidos

- **Clique em qualquer log** para ver detalhes completos
- **Compare valores** lado a lado
- **Veja metadados** como IP e user agent

---

## ⚙️ Configurações

### Configurações Municipais (Admin)

#### Dados do Município:
1. **Acesse "Configurações" → "Município"**
2. **Preencha as informações:**
   - Nome do município
   - Nome da prefeitura
   - Endereço completo
   - Código IBGE
   - Estado (UF)
   - CEP
   - Telefone
   - Email institucional
   - Nome do prefeito

3. **Salve as alterações**

> 💡 **Dica**: Essas informações aparecem nos relatórios em PDF

### Gestão de Usuários (Admin)

#### Criando um Novo Usuário:
1. **Acesse "Usuários"**
2. **Clique em "Novo Usuário"**
3. **Preencha:**
   - Nome completo
   - Email (será o login)
   - Senha temporária
   - Tipo (Admin ou Operador)
   - Status (Ativo/Inativo)

4. **Salve o usuário**

#### Editando Usuários:
- **Alterar dados**: Nome, email, tipo
- **Resetar senha**: Gerar nova senha temporária
- **Ativar/Desativar**: Controlar acesso ao sistema

#### Tipos de Usuário:

**👑 Administrador:**
- Acesso total ao sistema
- Pode gerenciar outros usuários
- Configura categorias e classificações
- Acessa configurações municipais

**👤 Operador:**
- Cadastra receitas e despesas
- Gera relatórios
- Visualiza auditoria
- Edita próprio perfil

### Perfil do Usuário

#### Alterando Dados Pessoais:
1. **Clique no seu nome** (canto superior direito)
2. **Selecione "Perfil"**
3. **Edite:**
   - Nome
   - Email
4. **Salve as alterações**

#### Alterando Senha:
1. **No perfil**, clique em "Alterar Senha"
2. **Preencha:**
   - Senha atual
   - Nova senha
   - Confirmação da nova senha
3. **Salve a nova senha**

---

## 🆘 FAQ e Troubleshooting

### Perguntas Frequentes

#### ❓ **Posso cadastrar receitas/despesas futuras?**
**R:** Não, o sistema não permite datas futuras para manter a integridade dos dados financeiros.

#### ❓ **Como funciona a hierarquia de categorias?**
**R:** Fonte → Bloco → Grupo → Ação. Cada nível depende do anterior, e a seleção é dinâmica.

#### ❓ **Posso excluir uma categoria que está sendo usada?**
**R:** Não, categorias com receitas/despesas associadas não podem ser excluídas.

#### ❓ **Os relatórios têm limite de dados?**
**R:** Não há limite, mas períodos muito longos podem demorar para processar.

#### ❓ **Como recuperar uma receita/despesa excluída?**
**R:** Não é possível recuperar. Use os logs de auditoria para ver os dados e recadastre se necessário.

### Problemas Comuns

#### 🔧 **Sistema lento ou não carrega**
**Soluções:**
1. Limpe o cache do navegador
2. Verifique sua conexão com a internet
3. Tente usar outro navegador
4. Entre em contato com o suporte

#### 🔧 **Erro ao salvar dados**
**Soluções:**
1. Verifique se todos os campos obrigatórios estão preenchidos
2. Confirme se as datas estão no formato correto
3. Verifique se os valores não têm caracteres especiais
4. Tente novamente após alguns minutos

#### 🔧 **Não consigo gerar relatórios**
**Soluções:**
1. Verifique se o período selecionado tem dados
2. Confirme se os filtros não estão muito restritivos
3. Tente um período menor
4. Verifique sua permissão de usuário

#### 🔧 **Categorias não carregam dinamicamente**
**Soluções:**
1. Recarregue a página
2. Verifique se há categorias cadastradas no nível superior
3. Confirme se as categorias estão ativas
4. Limpe o cache do navegador

### Navegadores Suportados

✅ **Recomendados:**
- Google Chrome 90+
- Mozilla Firefox 88+
- Microsoft Edge 90+
- Safari 14+

⚠️ **Limitações:**
- Internet Explorer não é suportado
- Versões muito antigas podem ter problemas

### Contato e Suporte

📧 **Email**: rayhenrique@gmail.com  
🌐 **GitHub**: https://github.com/rayhenrique/klgestorpub  
📱 **Suporte**: Entre em contato para dúvidas técnicas

---

## 📋 Histórico de Versões

### 🚀 **Versão 1.4.0 (Janeiro 2025) - ATUAL**
**Arquitetura Completamente Reestruturada**
- ✅ **Migração Limpa**: Removida arquitetura obsoleta da tabela `transactions`
- ✅ **Tabelas Especializadas**: Separação clara entre `revenues` e `expenses`
- ✅ **Performance Otimizada**: Consultas mais eficientes e relacionamentos otimizados
- ✅ **Estabilidade Total**: Zero conflitos de foreign key constraints
- ✅ **Código Limpo**: Remoção de 262 linhas de código obsoleto
- ✅ **Documentação Completa**: Manual atualizado, PRD e documentação técnica
- ✅ **GitHub Sincronizado**: Repositório atualizado com todas as correções

### 📊 **Versão 1.3.0 (Agosto 2025)**
**Validação e Testes Aprimorados**
- Form Request Classes para validação robusta
- Suite completa de testes com PHPUnit
- Service Layer para melhor organização
- Factories para dados de teste
- Validação em português brasileiro

### 🔧 **Versão 1.2.0 (Fevereiro 2025)**
**Melhorias em Relatórios**
- Relatórios em PDF com layout otimizado
- Formatação brasileira de datas e valores
- Adição de colunas detalhadas (Fonte, Bloco, Grupo, Ação)
- Simplificação do sistema de relatórios
- Foco em relatórios essenciais

### 🏗️ **Versão 1.1.0 (Janeiro 2024)**
**Auditoria e Interface**
- Sistema completo de logs de auditoria
- Melhorias na interface do usuário
- Relatórios avançados implementados
- Sistema de backup automático
- Documentação inicial do sistema

### 🎯 **Versão 1.0.0 (Janeiro 2024)**
**Lançamento Inicial**
- Sistema básico de receitas e despesas
- Categorias hierárquicas implementadas
- Relatórios financeiros básicos
- Autenticação e autorização
- Configurações por município

---

## 📞 Suporte e Contato

### 🛠️ **Suporte Técnico**
- **Email**: rayhenrique@gmail.com
- **Resposta**: Até 24 horas em dias úteis
- **Horário**: Segunda a Sexta, 8h às 18h

### 📚 **Recursos Adicionais**
- **Documentação Técnica**: Disponível no repositório
- **Código Fonte**: GitHub - rayhenrique/klgestorpub
- **Atualizações**: Notificações automáticas no sistema

### 🔄 **Atualizações do Sistema**
O sistema é atualizado regularmente com:
- Correções de bugs
- Melhorias de performance
- Novas funcionalidades
- Atualizações de segurança

---

**© 2025 KL Gestor Pub v1.4.0**  
**Desenvolvido por Ray Henrique**  
**Todos os direitos reservados**