@extends('Dashboard.layouts.global5')
@section('otherlinks')
	<!-- [Rogelio R Workana] -Manejo de JQuery datatables -->
	<!-- Los css de bootstrap y dataTables js ya están agregados en la global -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.min.css"> 
@endsection
@section('content')
	<div class="row">
		<div class="column">
			<table id="accesos" class="table-responsive table-striped shadow" style="width: 100%">
				<thead>
					<tr class="row-header">
						<th>Usuario</th>
						<th>IP</th>
						<th>Estatus Acceso</th>
						<th>Direccion</th>
						<th>Geolocalizacion</th>
						<th>Fecha Ingreso</th>
						<th>Fecha Salida</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($accesos as $registro)
						<tr>
							<td class="text-center">{{$registro->email}}</td>
							<td class="text-center">{{$registro->ip}}</td>
							<td class="text-center">{{$registro->estatus}}</td>
							<td class="text-left">{{$registro->direccion}}</td>
							<td class="text-center">{{$registro->latitud}},{{$registro->longitud}}</td>
							<td class="text-center">{{$registro->fecha_ingreso}}</td>
							<td class="text-center">{{$registro->fecha_salida}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="column">
			<div class="btn-container center">
				<a class="btn btn-primary" href="{{route('logaccesos.export.csv', $user->id)}}" role="button">Exportar CSV</a>
			</div>
		</div>
	</div>				

	<div class="row">
		<div class="column">
			<div id="map" class="gmap-container" style="height: 520px;"></div>
			{{-- <div class="gmap-img shadow">
				<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=6&{{$centerMap}}size=540x320&scale=2&maptype=roadmap&{{$marcadores}}key={{env('GOOGLE_MAP_API_KEY')}}" alt="maps">
			</div> --}}
		</div>
	</div>
@endsection

@section('scripts')
	<script src="{{asset('js/jquery.metisMenu.js')}}"></script>
	{{-- <script src="{{asset('js/jquery.slimscroll.min.js')}}"></script> --}}
	<script src="{{asset('js/custom.js')}}"></script>
	{{-- <script src="{{asset('js/screenfull.js')}}"></script> --}}
	{{-- <script src="{{asset('js/jquery.nicescroll.js')}}"></script> --}}
	{{-- <script src="{{asset('js/scripts.js')}}"></script> --}}
	<script src="{{asset('js/bootstrap.min.js')}}"> </script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#accesos').DataTable({
				responsive: {details: false},
				columnDefs: [
					{ responsivePriority: 1, targets: 1 },
					{ responsivePriority: 2, targets: 2 },
					{ responsivePriority: 3, targets: 5 }
				]
			});
		});
	</script>
	<script>
		function initMap(){
			const markers = @php echo ($marcadores); @endphp;
			markers.forEach((marker)=>{
				marker.lat = parseFloat(marker.lat)
				marker.lng = parseFloat(marker.lng)
			})

			const map = new google.maps.Map(document.querySelector("#map"), {
				zoom: 6,
				center: markers[0]
			});

			markers.forEach(marker => {
				new google.maps.Marker({
					position: marker,
					map
				})
			})
			// new google.maps.Marker({
			// 	position: { lat: -33.89, lng: 151.274 },
			// 	map
			// })
		}
	</script>
	<script
		src="https://maps.googleapis.com/maps/api/js?key={{ env( "GOOGLE_MAP_API_KEY", "" ) }}&callback=initMap"
		async
	></script>
@endsection