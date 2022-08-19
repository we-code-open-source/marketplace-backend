<?php

namespace App\DataTables;

use App\Models\SettlementManager;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class SettlementManagerDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('updated_at', function ($settlement) {
                return getDateColumn($settlement, 'updated_at');
            })
            ->addColumn('action', 'settlement_managers.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SettlementManager $model)
    {
        return $model->newQuery()->with("restaurant:id,name", "creator:id,name");
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'),
                [
                    'language' => json_decode(
                        file_get_contents(
                            base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ),
                        true
                    )
                ]
            ));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'id',
                'title' => trans('lang.settlement_manager_id'),

            ],
            [
                'data' => 'restaurant.name',
                'title' => trans('lang.settlement_manager_restaurant_id'),

            ],
            [
                'data' => 'count',
                'title' => trans('lang.settlement_manager_count'),

            ],
            [
                //'data' => 'amount',
                'data' => 'total',
                'title' => trans('lang.settlement_manager_amount'),

            ],
            [
                'data' => 'fee',
                'title' => trans('lang.settlement_manager_fee'),

            ],
            [
                'data' => 'note',
                'title' => trans('lang.settlement_manager_note'),

            ],
            [
                'data' => 'creator.name',
                'title' => trans('lang.settlement_manager_creator_id'),
            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.restaurants_payout_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(SettlementManager::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', SettlementManager::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.settlement_manager_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'settlement_managersdatatable_' . time();
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }
}
