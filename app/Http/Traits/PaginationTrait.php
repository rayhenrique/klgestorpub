<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginationTrait
{
    /**
     * Apply pagination to a query builder.
     *
     * @param Builder $query
     * @param Request $request
     * @param int $defaultPerPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginateQuery(Builder $query, Request $request, int $defaultPerPage = 20)
    {
        $perPage = $request->get('per_page', $defaultPerPage);
        
        // Limit per_page to prevent abuse
        $perPage = min($perPage, 100);
        
        return $query->paginate($perPage);
    }

    /**
     * Apply search filters to a query.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $searchFields
     * @return Builder
     */
    protected function applySearch(Builder $query, Request $request, array $searchFields = []): Builder
    {
        if ($request->filled('search') && !empty($searchFields)) {
            $search = $request->search;
            
            $query->where(function($q) use ($search, $searchFields) {
                foreach ($searchFields as $field) {
                    if (str_contains($field, '.')) {
                        // Relationship field
                        [$relation, $relationField] = explode('.', $field, 2);
                        $q->orWhereHas($relation, function($relationQuery) use ($relationField, $search) {
                            $relationQuery->where($relationField, 'like', "%{$search}%");
                        });
                    } else {
                        // Direct field
                        $q->orWhere($field, 'like', "%{$search}%");
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Apply date range filters to a query.
     *
     * @param Builder $query
     * @param Request $request
     * @param string $dateField
     * @return Builder
     */
    protected function applyDateRange(Builder $query, Request $request, string $dateField = 'date'): Builder
    {
        if ($request->filled('date_start')) {
            $query->whereDate($dateField, '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate($dateField, '<=', $request->date_end);
        }

        return $query;
    }

    /**
     * Apply status filter to a query.
     *
     * @param Builder $query
     * @param Request $request
     * @param string $statusField
     * @return Builder
     */
    protected function applyStatusFilter(Builder $query, Request $request, string $statusField = 'active'): Builder
    {
        if ($request->filled($statusField)) {
            $query->where($statusField, $request->get($statusField) === '1');
        }

        return $query;
    }
}