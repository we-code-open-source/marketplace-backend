<?php

namespace App\DataTables;

use App\Models\SettlementDriver;
use App\Models\CustomField;
use App\Models\FoodOrder;
use App\Models\Order;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

class SettlementManagerAvailableDataTable extends DataTable
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
            ->editColumn('fee', function ($data) {
                return   $data->admin_commission . '%';
            })
            ->editColumn('sales_amount', function ($data) {
                return  round($data->amount, 3);
            })->editColumn('amount', function ($data) {
                return round(($data->admin_commission / 100) *  $data->amount, 3); // calculate amount;
            })
            ->addColumn('action', 'settlement_managers.available.datatables_actions')
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
        return  FoodOrder::join('foods', 'foods.id', 'food_orders.food_id')
            ->join('orders', 'orders.id', 'food_orders.order_id')
            ->join('restaurants', 'restaurants.id', 'foods.restaurant_id')
            ->select(
                'food_orders.id',
                'foods.restaurant_id',
                'restaurants.name',
                'restaurants.admin_commission',
                DB::raw("IFNULL(SUM(food_orders.quantity * food_orders.price),0) amount"),
                DB::raw('IFNULL(COUNT(DISTINCT food_orders.order_id),0) count')
            )
            ->where('orders.order_status_id', 80) // Order Delivered
            ->whereNull('orders.settlement_manager_id')
            ->groupBy('foods.restaurant_id');
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
                'data' => 'restaurant_id',
                'name' => 'restaurants.id',
                'title' => trans('lang.settlement_manager_available_restaurant_id'),
            ],
            [
                'data' => 'name',
                'title' => trans('lang.settlement_manager_available_restaurant_id'),
                'searchable' => false,

            ],
            [
                'data' => 'count',
                'title' => trans('lang.settlement_manager_available_count'),
                'searchable' => false,

            ],
            [
                'data' => 'sales_amount',
                'title' => trans('lang.settlement_manager_available_sales_amount'),
                'searchable' => false,

            ],
            [
                'data' => 'amount',
                'title' => trans('lang.settlement_manager_available_amount'),
                'searchable' => false,

            ],
            [
                'data' => 'fee',
                'title' => trans('lang.settlement_manager_available_fee'),
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
        return 'settlement_manager_available_datatable_' . time();
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
