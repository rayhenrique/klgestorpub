<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Revenue;
use Illuminate\Support\Facades\Cache;

class ReportCacheService
{
    private const VERSION_GLOBAL = 'report_version:global';

    private const VERSION_FONTE = 'report_version:fonte:';

    private const VERSION_BLOCO = 'report_version:bloco:';

    private const VERSION_GRUPO = 'report_version:grupo:';

    private const VERSION_ACAO = 'report_version:acao:';

    private const VERSION_CLASSIFICATION = 'report_version:classification:';

    /**
     * Build deterministic base key for report filters (without version)
     */
    public function buildBaseKey(array $filters): string
    {
        $parts = [
            'type='.($filters['report_type'] ?? ''),
            'group='.($filters['group_by'] ?? ''),
            'start='.($filters['start_date'] ?? ''),
            'end='.($filters['end_date'] ?? ''),
            'fonte='.($filters['category_id'] ?? ''),
            'bloco='.($filters['block_id'] ?? ''),
            'grupo='.($filters['group_id'] ?? ''),
            'acao='.($filters['action_id'] ?? ''),
            'class='.($filters['expense_classification_id'] ?? ''),
        ];

        return 'report:'.md5(implode('|', $parts));
    }

    /**
     * Resolve composite version string based on involved scopes
     */
    public function getCompositeVersion(array $filters): string
    {
        $global = $this->getVersion(self::VERSION_GLOBAL);
        $fonte = ! empty($filters['category_id']) ? $this->getVersion(self::VERSION_FONTE.$filters['category_id']) : 0;
        $bloco = ! empty($filters['block_id']) ? $this->getVersion(self::VERSION_BLOCO.$filters['block_id']) : 0;
        $grupo = ! empty($filters['group_id']) ? $this->getVersion(self::VERSION_GRUPO.$filters['group_id']) : 0;
        $acao = ! empty($filters['action_id']) ? $this->getVersion(self::VERSION_ACAO.$filters['action_id']) : 0;
        $class = ! empty($filters['expense_classification_id']) ? $this->getVersion(self::VERSION_CLASSIFICATION.$filters['expense_classification_id']) : 0;

        return "v{$global}-f{$fonte}-b{$bloco}-g{$grupo}-a{$acao}-c{$class}";
    }

    /**
     * Resolve dynamic TTL based on report type and grouping
     */
    public function resolveTtl(array $filters): int
    {
        $type = $filters['report_type'] ?? 'balance';
        $group = $filters['group_by'] ?? 'monthly';

        $map = [
            'revenues' => ['daily' => 300, 'monthly' => 900, 'yearly' => 1800],
            'expenses' => ['daily' => 300, 'monthly' => 900, 'yearly' => 1800],
            'balance' => ['daily' => 600, 'monthly' => 1200, 'yearly' => 3600],
            'expense_classification' => ['daily' => 600, 'monthly' => 1800, 'yearly' => 3600],
        ];

        return $map[$type][$group] ?? 900;
    }

    /**
     * Registrar chave de cache em índices por escopo para futura invalidação
     */
    public function registerCachedReport(string $cacheKey, array $filters): void
    {
        $this->addKeyToIndex('report_index:global', $cacheKey);
        if (! empty($filters['category_id'])) {
            $this->addKeyToIndex(self::VERSION_FONTE.$filters['category_id'].':index', $cacheKey);
        }
        if (! empty($filters['block_id'])) {
            $this->addKeyToIndex(self::VERSION_BLOCO.$filters['block_id'].':index', $cacheKey);
        }
        if (! empty($filters['group_id'])) {
            $this->addKeyToIndex(self::VERSION_GRUPO.$filters['group_id'].':index', $cacheKey);
        }
        if (! empty($filters['action_id'])) {
            $this->addKeyToIndex(self::VERSION_ACAO.$filters['action_id'].':index', $cacheKey);
        }
        if (! empty($filters['expense_classification_id'])) {
            $this->addKeyToIndex(self::VERSION_CLASSIFICATION.$filters['expense_classification_id'].':index', $cacheKey);
        }
    }

    /**
     * Invalidação via Cache::forget quando Revenue muda (e bump de versão)
     */
    public function touchRevenue(Revenue $revenue): void
    {
        $this->bump(self::VERSION_GLOBAL);
        $this->forgetIndex('report_index:global');
        if ($revenue->fonte_id) {
            $this->bump(self::VERSION_FONTE.$revenue->fonte_id);
            $this->forgetIndex(self::VERSION_FONTE.$revenue->fonte_id.':index');
        }
        if ($revenue->bloco_id) {
            $this->bump(self::VERSION_BLOCO.$revenue->bloco_id);
            $this->forgetIndex(self::VERSION_BLOCO.$revenue->bloco_id.':index');
        }
        if ($revenue->grupo_id) {
            $this->bump(self::VERSION_GRUPO.$revenue->grupo_id);
            $this->forgetIndex(self::VERSION_GRUPO.$revenue->grupo_id.':index');
        }
        if ($revenue->acao_id) {
            $this->bump(self::VERSION_ACAO.$revenue->acao_id);
            $this->forgetIndex(self::VERSION_ACAO.$revenue->acao_id.':index');
        }
    }

    /**
     * Invalidação via Cache::forget quando Expense muda (e bump de versão)
     */
    public function touchExpense(Expense $expense): void
    {
        $this->bump(self::VERSION_GLOBAL);
        $this->forgetIndex('report_index:global');
        if ($expense->fonte_id) {
            $this->bump(self::VERSION_FONTE.$expense->fonte_id);
            $this->forgetIndex(self::VERSION_FONTE.$expense->fonte_id.':index');
        }
        if ($expense->bloco_id) {
            $this->bump(self::VERSION_BLOCO.$expense->bloco_id);
            $this->forgetIndex(self::VERSION_BLOCO.$expense->bloco_id.':index');
        }
        if ($expense->grupo_id) {
            $this->bump(self::VERSION_GRUPO.$expense->grupo_id);
            $this->forgetIndex(self::VERSION_GRUPO.$expense->grupo_id.':index');
        }
        if ($expense->acao_id) {
            $this->bump(self::VERSION_ACAO.$expense->acao_id);
            $this->forgetIndex(self::VERSION_ACAO.$expense->acao_id.':index');
        }
        if ($expense->expense_classification_id) {
            $this->bump(self::VERSION_CLASSIFICATION.$expense->expense_classification_id);
            $this->forgetIndex(self::VERSION_CLASSIFICATION.$expense->expense_classification_id.':index');
        }
    }

    /**
     * Internal helpers
     */
    private function getVersion(string $key): int
    {
        return (int) (Cache::get($key, 1));
    }

    private function bump(string $key): void
    {
        try {
            Cache::increment($key);
        } catch (\Throwable $e) {
            $current = (int) Cache::get($key, 1);
            Cache::forever($key, $current + 1);
        }
    }

    private function addKeyToIndex(string $indexKey, string $cacheKey): void
    {
        $list = Cache::get($indexKey, []);
        if (! in_array($cacheKey, $list, true)) {
            $list[] = $cacheKey;
            Cache::forever($indexKey, $list);
        }
    }

    private function forgetIndex(string $indexKey): void
    {
        $list = Cache::get($indexKey, []);
        foreach ($list as $key) {
            Cache::forget($key);
        }
        Cache::forget($indexKey);
    }
}
