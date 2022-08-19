<div class='btn-group btn-group-sm'>

    @can('restaurantDistancePrices.edit')
        <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.restaurant_distance_price_edit')}}"
           href="{{ route('restaurantDistancePrices.edit', $restaurantDistancePriceID) }}" class='btn btn-link'>
            <i class="fa fa-edit"></i>
        </a>
    @endcan

    @can('restaurantDistancePrices.destroy')
    <form id={{"form$id"}} action="{{route('restaurantDistancePrices.destroy',['id' => $restaurantDistancePriceID])}}" method="POST" onsubmit="showSweetAlert(event)">
        @csrf
        @method('DELETE')
        <button class="btn btn-lnk text-danger bg-transparent" 
                data-toggle="tooltip" data-placement="bottom"
                title="{{trans('lang.restaurant_distance_price_delete')}}"
                type="submit"
                >
            <i class="fa fa-trash"></i>
        </button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script>
        function showSweetAlert(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.value) {
                    document.getElementById(event.target.id).submit();
                    }
            })
        }
    </script>
    @endcan
</div>
