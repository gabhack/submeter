@extends('Dashboard.layouts.pdflayout')
@section('content')
<div class="row" style="min-height:100vh;">
	<div class="col-md-12">
		@foreach($elements as $index => $element)
			@if($type_elements[$index] == 1)
			<div class="col-md-8 offset-md-2 mt-4 text-center keep-together">
				<img src="{{$element}}" class="img-fluid">
				<br/>
				<br/>
			</div>
			@elseif($type_elements[$index] == 2)
				<div class="keep-together">
    				{!! $element !!}
				</div>
			@elseif($type_elements[$index] == 3)
				<div class="break-after"></div>
			@endif
		@endforeach
	</div>
</div>
@endsection