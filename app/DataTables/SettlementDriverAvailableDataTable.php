<?php

namespace App\DataTables;

use App\Models\SettlementDriver;
use App\Models\CustomField;
use App\Models\Order;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class SettlementDriverAvailableDataTable extends DataTable
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
            ->editColumn('fee', getDriverFee() . '%')
            ->editColumn('delivery_fee', function ($data) {
                return   round($data->amount, 3);
            })->editColumn('amount', function ($data) {
                return round((getDriverFee() / 100) *  $data->amount, 3); // calculate amount;
            })
            ->addColumn('action', 'settlement_drivers.available.datatables_actions')
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
        return Order::select(
            'driver_id',
            DB::raw("IFNULL(SUM(delivery_fee),0) amount"),
            DB::raw('IFNULL(COUNT(*),0) count')
        )
            ->where('order_status_id', 80) // Order Delivered
            ->whereNull('settlement_driver_id')
            ->groupBy('driver_id')
            ->with("driver:id,name");
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
                'data' => 'driver_id',
                'title' => trans('lang.settlement_driver_driver_id'),
            ],
            [
                'data' => 'driver.name',
                'title' => trans('lang.settlement_driver_driver_id'),
                'searchable' => false,

            ],
            [
                'data' => 'count',
                'title' => trans('lang.settlement_driver_count'),
                'searchable' => false,

            ],
            [
                'data' => 'delivery_fee',
                'title' => trans('lang.settlement_driver_delivery_fee'),
                'searchable' => false,

            ],
            [
                'data' => 'amount',
                'title' => trans('lang.settlement_driver_amount'),
                'searchable' => false,

            ],
            [
                'data' => 'fee',
                'title' => trans('lang.settlement_driver_fee'),
                'searchable' => false,

            ],
        ];

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'settlement_drivers_available_datatable_' . time();
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
