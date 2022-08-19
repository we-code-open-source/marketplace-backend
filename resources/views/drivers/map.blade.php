@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.driver_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.driver_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-itema ctive"><a href="{!! route('drivers.index') !!}">{{trans('lang.driver_plural')}}</a>
          </li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link" href="{!! route('drivers.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.driver_table')}}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{!! route('drivers.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.driver_create')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      <div class="row">
        
        <div class="col-12">
          <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-drivers">0</h3>
                        <p>Total drivers</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-car"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="unavailable-drivers">0</h3>
                        <p>Unavailable drivers</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-car"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="free-drivers">0</h3>
                        <p>Free drivers</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-car"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="busy-drivers">0</h3>
                        <p>Busy drivers</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-car"></i>
                    </div>
                </div>
            </div>
          </div>
        </div>
        
        <div class="col-12">
            <div id="map" style="width: 100%; height: 500px;"></div>
        </div>
        


        <!-- Back Field -->
        <div class="form-group col-12 text-right">
          <a href="{!! route('drivers.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.back')}}</a>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>



<!--Firestore Libraries-->
{{-- <script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase-firestore.js"></script> --}}
 
{{-- @include('vendor.notifications.init_firebase') --}}

@section('extra-js')

<script src="https://maps.google.com/maps/api/js?key={{ setting('google_maps_key',"AIzaSyAT07iMlfZ9bJt1gmGj9KhJDLFY8srI6dA") }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" type="text/javascript"></script>

<script type="text/javascript">

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 11,
        center: new google.maps.LatLng(32.8078757,13.2627382),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var markers = [];

    function getDriverColorStatus(index){
        const d = db_drivers[index];
        if (!d.available) return '#f44336';
        return (d.working_on_order && '#007bff') || '#28A745';
    }

        // Adds a marker to the map and push to the array.
    function addMarker(lat, long,index) {
        const colorStatus = {
            
        };
        const marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat, long),
            icon: {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 6,
            fillColor: getDriverColorStatus(index),
            fillOpacity: 0.9,
            strokeWeight: 0.2
            },
            map: map,
        });

        google.maps.event.addListener(
            marker,
            "click",
            (function(marker, index) {
            return function() {
                infowindow.setContent(`
                    ${db_drivers[index].id}
                    <div>${db_drivers[index].name}</div>
                    <h6>${db_drivers[index].phone_number}</h6>
                    <div>Last access</div>
                    <h6>${moment(db_drivers[index].last_access).format()}</h6>
                    <h6>${moment(db_drivers[index].last_access).fromNow()}</h6>
                `);
                infowindow.open(map, marker);
            };
            })(marker, index)
        );

        markers.push(marker);
    }

    function setMapOnAll(map) {
        for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }
 
</script>


<script  >
    
    function map_set_driver(data){
        
        var marker, i;

        for (i = 0; i < data.length; i++) {  
            if(data[i].available){ // skip show unavailable drivers on map
                addMarker(data[i].latitude, data[i].longitude,i);
            }
        }
    }

    var db_drivers = []; 
    var db = firebase.firestore();

    function getDriversFromFirebaseAndSetThemOnMap(){
        db.collection('drivers').get().then((querySnapshot) => {
            db_drivers = [];
            querySnapshot.forEach((doc) => {
                db_drivers.push(doc.data());
            });
            if(db_drivers.length){
                setMapOnAll(null);
                map_set_driver(db_drivers);
                $("#total-drivers").text(db_drivers.length || 0);
                $("#unavailable-drivers").text(db_drivers && db_drivers.filter(i => !i.available).length || 0);
                $("#free-drivers").text(db_drivers && db_drivers.filter(i => i.available && !i.working_on_order).length || 0);
                $("#busy-drivers").text(db_drivers && db_drivers.filter(i => i.available && i.working_on_order).length || 0);
            }
        }).catch(e => {
            console.log(e);
            alert(e.message);
        });
        
        /* const observer = db.collection('drivers').onSnapshot(snapshot => {
            db_drivers = [];
            snapshot.forEach((doc) => {
                db_drivers.push(doc.data());
            });
            if(db_drivers.length){
                setMapOnAll(null);
                map_set_driver(db_drivers);
                $("#total-drivers").text(db_drivers.length || 0);
                $("#unavailable-drivers").text(db_drivers && db_drivers.filter(i => !i.available).length || 0);
                $("#free-drivers").text(db_drivers && db_drivers.filter(i => i.available && !i.working_on_order).length || 0);
                $("#busy-drivers").text(db_drivers && db_drivers.filter(i => i.available && i.working_on_order).length || 0);
            }
        }, e => {
            console.log(e);
            alert(e.message);
        }) */
    }

    getDriversFromFirebaseAndSetThemOnMap();
    setInterval(() => {
        getDriversFromFirebaseAndSetThemOnMap()
    }, 10000);

</script>

@endsection


@endsection
