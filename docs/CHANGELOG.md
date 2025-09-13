# Changelog

## [1.4.0] - 2025-01-18

### 🚀 Principais Novidades

#### 📱 Sistema 100% Responsivo
- Interface totalmente adaptada para smartphones e tablets
- Sidebar responsivo com menu colapsável e animações suaves
- Breakpoints inteligentes: mobile (<768px), tablet (768px-1024px), desktop (>1024px)
- Tabelas adaptáveis com scroll horizontal e colunas ocultas em telas menores
- Formulários otimizados com layout responsivo em todos os CRUDs
- Navegação mobile com botão hamburger e overlay

#### 💾 Sistema de Backup e Restauração Completo
- Backup automático via interface web com compactação (.gz)
- Download seguro com autenticação e validação de arquivos
- Restauração inteligente com upload e pré-validação
- Backup pré-restauração automático antes de restaurar
- Comandos Artisan: `backup:database` e `backup:restore`
- Logs de auditoria completos para todas as operações
- Validação de arquivos (.sql e .gz) com verificação de integridade

#### ♿ Acessibilidade WAI-ARIA
- Conformidade com diretrizes WCAG
- Navegação completa por teclado
- Compatibilidade com leitores de tela
- Implementação correta de aria-labels e roles
- Contraste otimizado para baixa visão

### 🏗️ Melhorias Técnicas

#### Arquitetura Reestruturada
- Removida arquitetura obsoleta da tabela `transactions`
- Separação clara entre tabelas `revenues` e `expenses`
- Performance otimizada com consultas mais eficientes
- Zero conflitos de foreign key constraints

#### Correções e Otimizações
- Sistema de migrações completamente estável
- Remoção de 262 linhas de código obsoleto
- Validação aprimorada com tratamento robusto de erros
- Interface polida com melhorias visuais e de usabilidade
- Correção de problemas de upload de arquivos
- Tratamento adequado de erros HTTP (500/422)

### 🔧 Correções de Bugs
- Corrigido erro `SplFileInfo::getSize(): stat failed` no upload de backups
- Resolvido problema de sidebar desaparecendo no módulo de backup
- Corrigido erro `net::ERR_ABORTED` nos downloads (comportamento normal)
- Implementada validação customizada para tipos de arquivo
- Corrigidos problemas de acessibilidade com `aria-hidden`

### 📈 Performance e Estabilidade
- Otimização de consultas ao banco de dados
- Melhoria na velocidade de carregamento das páginas
- Redução do uso de memória
- Estabilidade aprimorada em todas as funcionalidades

## [1.1.0] - 2024-01-19

### Adicionado
- Favicon personalizado com o ícone do sistema
- Exibição do valor total no início dos relatórios em Excel
- Ajuste do fuso horário para America/Sao_Paulo

### Corrigido
- Correção na exibição dos valores monetários nos relatórios Excel
- Ajuste na formatação dos valores e alinhamento nas planilhas
- Correção na geração de relatórios em PDF com configurações otimizadas 

## [1.2.0] - 2025-02-11

### Adicionado
- Adicionadas colunas detalhadas nos relatórios PDF (Fonte, Bloco, Grupo, Ação)
- Formatação de datas no padrão brasileiro (dd/mm/yyyy)
- Melhorias na visualização dos relatórios em PDF

### Corrigido
- Correção na formatação de datas nos relatórios PDF para o padrão brasileiro
- Ajuste na exibição das colunas nos relatórios PDF para corresponder à visualização web
- Otimização na geração de relatórios PDF