@extends('Dashboard.layouts.global4')

@section('content')

    <div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
        
        <div data-manual 
              data-enterprise-id=""
              data-user-level="{{$user->tipo}}"
              data-base-url="{{url('')}}"
              data-back-url="{{url()->previous()}}">
        </div>   
    </div>
@endsection


