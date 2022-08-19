<?php

namespace App\DataTables;

use App\Models\SettlementDriver;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class SettlementDriverDataTable extends DataTable
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
            ->addColumn('action', 'settlement_drivers.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SettlementDriver $model)
    {
        return $model->newQuery()->with("driver:id,name", "creator:id,name");
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
                'title' => trans('lang.settlement_driver_id'),

            ],
            [
                'data' => 'driver.name',
                'title' => trans('lang.settlement_driver_driver_id'),

            ],
            [
                'data' => 'count',
                'title' => trans('lang.settlement_driver_count'),

            ],
            [
                'data' => 'total_amount',
                'title' => trans('lang.settlement_driver_amount'),
                'searchable' => false,
                'orderable' => false,

            ],
            [
                'data' => 'fee',
                'title' => trans('lang.settlement_driver_fee'),

            ],
            [
                'data' => 'note',
                'title' => trans('lang.settlement_driver_note'),

            ],
            [
                'data' => 'creator.name',
                'title' => trans('lang.settlement_driver_creator_id'),
            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.restaurants_payout_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(SettlementDriver::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', SettlementDriver::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.settlement_driver_' . $field->name),
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
        return 'settlement_driversdatatable_' . time();
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
