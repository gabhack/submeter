@extends('Dashboard.layouts.global4')

@section('content')
    <div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
        <div data-statistic-config-list="{{$type}}"
            data-base-url="{{url('')}}"
            data-back-url="{{route('statistics.resume',['type'=>$type,'user_id'=>$user->id])}}">
        </div>  
    </div>
@endsection