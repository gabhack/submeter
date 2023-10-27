@extends('Dashboard.layouts.global4')

@section('content')

    <div class="content-main col-md-12 pl-2 pr-4 content-90 gray-bg">
        <h3>
          <i class="fa fa-cogs"></i>
          Listado de Configuraciones de Administradores
        </h3>
        <hr>
        <div class="row">
          <div class="col-6">
            <div data-statistic-config-list="produccion" 
              data-base-url="{{url('')}}"
              data-back-url="">
            </div>   
          </div>
          <div class="col-6">
            <div data-statistic-config-list="indicadores"
              data-base-url="{{url('')}}"
              data-back-url="">
            </div>   
          </div>
        </div>
        
    </div>
@endsection