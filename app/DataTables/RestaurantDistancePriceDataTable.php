<?php

namespace App\DataTables;

use App\Models\RestaurantDistancePrice;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class RestaurantDistancePriceDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('updated_at', function ($restaurant_distance_price) {
                return getDateColumn($restaurant_distance_price, 'updated_at');
            })
            ->editColumn('is_available', function ($restaurant_distance_price) {
                return getBooleanColumn($restaurant_distance_price, 'is_available');
            })
            ->addColumn('action', 'restaurant_distance_prices.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
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
                'data' => 'restaurant.name',
                'title' => trans('lang.restaurant_distance_price_restaurant_name'),
            ],
            [
                'data' => 'from',
                'title' => trans('lang.restaurant_distance_price_from'),
            ],
            [
                'data' => 'to',
                'title' => trans('lang.restaurant_distance_price_to'),
            ],
            [
                'data' => 'price',
                'title' => trans('lang.restaurant_distance_price_price'),//looks weird to have double price
            ],
            [
                'data' => 'is_available',
                'title' => trans('lang.restaurant_distance_price_is_available'),
            ],
            // [
            //     'data' => 'user.name',
            //     'title' => trans('lang.delivery_address_user_id'),

            // ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.delivery_address_updated_at'),
                'searchable' => false,
            ]
        ];

        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RestaurantDistancePrice $model)
    {
        return $model->newQuery()->with("restaurant:id,name")
                                 ->select('restaurant_distance_prices.id AS restaurantDistancePriceID','restaurant_distance_prices.*')
                                 ->orderBy('updated_at','DESC');
                                 //I did this because the id of the restaurantZonePrice gets overwritten and the action buttons references the wrong id
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
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

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'restaurant_distance_prices' . now()->format('Ynj_His');;
    }
}
