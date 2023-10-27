<?php

use Illuminate\Support\Facades\Route;

Route::get('email', 'EmailController@sendEmail');
Route::get('/', 'HomeController@index')->name('default.index');
Route::get('reinicio_password', 'HomeController@ReinicioPassword')->name('password.reinicio');
Route::post('home', 'HomeController@index')->name('home.index');
/*
@JulioM: AGREGADO SOLO PARA PRUEBAS Y RETIRADO
Route::get('testing', 'TestingController@index')->name('testing.index');
Route::get('/clear-cache', function() {
  Artisan::call('cache:clear');
  return "1 x View Cache is cleared";
});
Route::get('/view-clear', function() {
  Artisan::call('view:clear');
  return "2 x View Cache is cleared";
});
*/

// Link para usuario registrado
Route::get('usuario-registrado', 'UsuarioRegistradoController@usuarioRegistrado')->name('registro.usuario');
Route::post('save-registrado', 'UsuarioRegistradoController@saveRegistro')->name('save.registro');
Route::get('solicitud_registro', 'UsuarioRegistradoController@solicitarRegistro')->name('solicitud.registro');
Route::post('send_solicitud', 'UsuarioRegistradoController@enviarSolicitudRegistro')->name('send.solicitud.registro');

//PASSWORD RESET
Route::post("cambio_password", 'LoginController@sendResetPassword')->name('cambio.password');
Route::post("reset_password", 'LoginController@resetPassword')->name('reset.password.login');

//valida usuario/ip
Route::post("validate_user", 'Auth\LoginController@validateLogin')->name('login.validate');
Route::post("log_timeout", 'Auth\LoginController@logTimeout')->name('login.timeout');

Auth::routes();

Route::middleware(['auth'])->group(function () {
  Route::middleware(['admin'])->group(function () {
    Route::get('administrar_usuarios/{id}/{id2}', 'UserController@AdministrarUsuarios')->name('admin.users');

    Route::get('empresa', 'EmpresasController@list')->name("enterprise.index");
    Route::get('empresa/crear', 'EmpresasController@create')->name("enterprise.create");
    Route::get('empresa/users/{enterprise_id}', 'EmpresasController@getUsers')->name("enterprise.users");
    Route::get('empresa/{enterprise_id}', 'EmpresasController@edit')->name("enterprise.edit");
    Route::post('empresa', 'EmpresasController@save')->name("enterprise.save");
    Route::patch('empresa/{enterprise_id}', 'EmpresasController@update')->name("enterprise.update");
    Route::delete('empresa/{enterprise_id}', 'EmpresasController@delete')->name("enterprise.delete");

    Route::get('usuario', 'UserEnterpriseController@list')->name("user.index");
    Route::get('usuario/{user_id}', 'UserEnterpriseController@show')->name("user.show");
    Route::post('usuario', 'UserEnterpriseController@save')->name("user.save");
    Route::patch('usuario/{user_id}', 'UserEnterpriseController@update')->name("user.update");
    Route::delete('usuario/{user_id}', 'UserEnterpriseController@delete')->name("user.delete");

    Route::get('contador', 'EnergyMeterController@list')->name("energymeter.index");
    Route::get('contador/combo', 'EnergyMeterController@listCombo')->name("energymeter.indexcombo");
    // Route::get('contador/cambiar/{user_id}/{meter_id}', 'EnergyMeterController@changeCurrentMeter')->name("energymeter.change"); 
    Route::get('contador/{meter_id}', 'EnergyMeterController@show')->name("energymeter.show");
    Route::post('contador', 'EnergyMeterController@save')->name("energymeter.save");
    Route::patch('contador/{meter_id}', 'EnergyMeterController@update')->name("energymeter.update");
    Route::delete('contador/{meter_id}', 'EnergyMeterController@delete')->name("energymeter.delete");

    /*Route::get('analizador', 'AnalyzerController@list')->name("analyzer.index");
    Route::get('analizador/combo', 'AnalyzerController@listComboAnalyzer')->name("analyzer.indexcombo");
    Route::get('analizador/list', 'AnalyzerController@getAnalyzersList')->name("analyzer.list");
    Route::get('analizador/{analyzer_id}', 'AnalyzerController@show')->name("analyzer.show");
    Route::post('analizador', 'AnalyzerController@save')->name("analyzer.save");
    Route::patch('analizador/{analyzer_id}', 'AnalyzerController@update')->name("analyzer.update");
    Route::delete('analizador/{analyzer_id}', 'AnalyzerController@delete')->name("analyzer.delete");*/

    Route::get('analyzergroup/list', 'AnalyzerGroupController@getAnalyzersGroupList')->name("analyzergroup.list");
    Route::get('analyzergroup/{analyzer_group_id}', 'AnalyzerGroupController@show')->name("analyzergroup.show");
    Route::post('analyzergroup', 'AnalyzerGroupController@save')->name("analyzergroup.save");
    Route::patch('analyzergroup/{analyzer_group_id}', 'AnalyzerGroupController@update')->name("analyzergroup.update");
    Route::delete('analyzergroup/{analyzer_group_id}', 'AnalyzerGroupController@delete')->name("analyzergroup.delete");

    Route::post('eliminar_usuario', 'UserController@EliminarUsuarioLista')->name('eliminar.user.list');
    Route::get('registrar-admin', 'UserController@create')->name('create.users');
    Route::get('registrar-cliente', 'UserController@createClient')->name('create.cliente');
    Route::post('registrar-admin', 'UserController@store')->name('store.users');
    Route::get('editar-user/{id}', 'UserController@edit')->name('edit.user');
    Route::post('update-user/{id}', 'UserController@update')->name('update.user');

    // MODULO BORRAR CONTADORES Y ANALIZADORES
    Route::post('eliminar_contador', 'UserController@EliminarContador')->name('eliminar.contador.ajax');
    Route::post('eliminar_analizador', 'UserController@EliminarAnalizador')->name('eliminar.analizador.ajax');

    // GRUPOS
    Route::get('group/{group}', 'GroupsController@getGroup')->name('group.get');
    Route::get('groups', 'GroupsController@getGroups')->name('groups.get');
    Route::post('groups', 'GroupsController@storeGroup')->name('groups.store');
    Route::delete("groups", 'GroupsController@deleteGroup')->name('groups.delete');
  });

  Route::get('/home', 'HomeController@index')->name('home');
  Route::post('config_interval', 'UserController@ConfigurarIntervalo')->name('config.interval');
  Route::post('navigation', 'UserController@ConfigurarIntervaloNavegacion')->name('config.navigation');
  Route::post('subperiod', 'UserController@cargarSubPeriodo')->name('config.subperiodo');


  Route::middleware(['matchAuthUserId'])->group(function () {
    Route::get('contador/cambiar/{user_id}/{meter_id}', 'EnergyMeterController@changeCurrentMeter')->name("energymeter.change");

    //MODULO ENERGÍA Y POTENCIA
    Route::get('resumen_energia_potencia/{user_id}', 'ResumenEnergiaController@ResumenEnergiaPotencia')->name('resumen.energia.potencia');

    //MODULO CONTADORES
    // Route::get('ver_panel_usuario/{user_id}/{ctrl}', ['as' => 'ver.panel.user', 'uses' => 'ResumenContadoresController@VerPanelUser']); 
    Route::get('ver_panel_usuario/{user_id}', 'ResumenContadoresController@VerPanelUser')->name('ver.panel.user');
    Route::get('contadores_pdf/{user_id}', 'UserController@ContadoresPdf')->name('contadores.pdf');
  });

  // MÓDULO CONSUMO DE ENERGÍA
  Route::get('consumo_energia/{user_id}', 'ConsumoEnergiaController@ConsumoEnergia')->middleware('matchAuthUserId')->name('consumo.energia');
  Route::post('exportar_csv_energia', 'ConsumoEnergiaController@exportCSVConsumoEnergia')->name('export.csv.energia');
  Route::post('exportar_csv_generacion', 'ConsumoEnergiaController@exportCSVGeneracion')->name('export.csv.generacion');
  Route::post('exportar_csv_balance', 'ConsumoEnergiaController@exportCSVBalance')->name('export.csv.balance');

  //MODULO ANALISIS DE POTENCIA
  Route::middleware(['matchAuthUserId'])->group(function () {
    // Route::get('analisis_potencia/{user_id}', ['as' => 'analisis.potencia', 'uses' => 'UserController@AnalisisPotencia']); 
    Route::get('analisis_potencia_2/{user_id}', 'AnalisisPotencia@AnalisisPotencia')->name('analisis.potencia2');
    Route::get('analisis_potencia_optimizacion/{user_id}', 'AnalisisPotencia@Optimizacion')->name('analisis.potencia.optimizacion');
  });
  Route::post('analisis_potencia_optima', 'UserController@OptimizarPotencia')->name('analisis.potencia.optima');
  Route::get('analisis_potencia_envio_email', 'UserController@EnvioEmailOptimizacion')->name('analisis_potencia_envio_email');

  //MODULO SIMULACION POTENCIA
  Route::get('simulacion_potencia/{user_id}', 'SimulacionPotencia@SimulacionPotencia')->middleware('matchAuthUserId')->name('simulacion.potencia');
  Route::post('simulacion_potencia', 'SimulacionPotencia@GuardarValoresSimulacion')->name('simulacion.potencia.save');

  //MODULO MERCADO ENERGETICO
  Route::get('mercado_energetico/{user_id}', 'MercadoEnergetico@MercadoEnergetico')->middleware('matchAuthUserId')->name('mercado.energetico');
  Route::post('exportar_csv_mercado', 'MercadoEnergetico@exportCSVMercado')->name('export.csv.mercado');

  // MODULO COMPARARDOR DE OFERTAS
  Route::middleware(['matchAuthUserId'])->group(function () {
    Route::get('comparador_ofertas/{user_id}', 'UserController@ComparadorOfertas')->name('comparador.ofertas');
    Route::post('comparador_ofertas/{user_id}', 'UserController@CalculoComparadorOfertas')->name('calculo.comparador.ofertas');
    Route::get('comparador_ofertas_pdf/{user_id}', 'UserController@ComparadorOfertasPdf')->name('comparador.ofertas.pdf');
    Route::get('vista_comparador_ofertas/{user_id}', 'UserController@ExportarComparacionOfertas')->name('vista.comparador.ofertas');
  });

  // MODULO EMISIONES CO2
  Route::get('emisiones_co2/{user_id}', 'EmisionesCO2Controller@EmisionesCO2')->middleware('matchAuthUserId')->name('emisiones.co2');
  Route::post('exportar_csv_co2', 'EmisionesCO2Controller@exportCSVCo2')->name('export.csv.co2');

  // MODULO SIMULACIÓN DE FACTURAS
  Route::middleware(['matchAuthUserId'])->group(function () {
    Route::get('simulacion_facturas/{user_id}', 'SimulacionFacturasController@SimulacionFactura')->name('simulacion.facturas');
    Route::get('simulacion_facturas_pdf/{user_id}', 'SimulacionFacturasController@SimulacionFacturaPdf')->name('simulacion.facturas.pdf');
  });
  Route::get('grafica_analisis_potencia/{data}', 'AnalisisPotencia@graficaAnalisis');

  // MODULO DE INFORMES PERIÓDICOS Y ALERTAS
  Route::middleware(['matchAuthUserId'])->group(function () {
    Route::get('informes_alertas/{user_id}', 'UserController@InformesPeriodicosAlertas')->name('informes.periodicos.alertas');
    Route::post('nuevas_alertas/{user_id}', 'UserController@guardarNuevasAlertas')->name('informes.periodicos.nuevas_alertas');
    //Route::post('informes', ['as' => 'informes.programados', 'uses' => 'UserController@InformesProgramados']); 
    Route::post('informes/{user_id}', 'UserController@InformesProgramados')->name('informes.programados');
    Route::post('alertas/{user_id}', 'UserController@AlertasProgramadas')->name('alertas.programadas');
    //ruta nueva para nuevos informes
    Route::post('informes_analizadores/{user_id}', 'UserController@AnalizadoresInformesProgramados')->name('informes.analizadores.programados');
  });
  Route::get('buscar_alertas_bd/{contador_id}', 'UserController@buscarBDLista')->name('informes.periodicos.buscar_alertas_bd');
  Route::post('buscar_alertas_tablas', 'UserController@buscarBDTablas')->name('informes.periodicos.buscar_alertas_tablas');
  Route::post('actualizar_alertas/{alerta_id}', 'UserController@actualizarNuevasAlertas')->name('informes.periodicos.actualizar_alertas');
  Route::post('eliminar_alertas/{alerta_id}', 'UserController@eliminarNuevasAlertas')->name('informes.periodicos.eliminar_alertas');
  Route::post('pdf/exportacion', 'PDFController@createPDFTemplate')->name('exportacion.pdf');

  // MODULO EXPORTAR DATOS
  Route::get('exportar_datos/{user_id}', 'UserController@ExportarDatos')->name('exportar.datos');
  Route::post('exportar', 'UserController@GetExportar')->name('get.export');

  // Perfil de usuarios
  Route::get('perfil', 'UserController@perfilForm')->name('perfil.form');
  Route::post('perfil/{user_id}', 'UserController@storePerfil')->middleware('matchAuthUserId')->name('store.perfil');

  //MODULO AREA CLIENTE
  Route::middleware(['matchAuthUserId'])->group(function () {
    Route::get('area-cliente/{user_id}', 'UserController@areaCliente')->name('area.cliente');
    Route::post('area-cliente/{user_id}', 'UserController@storeAreaCliente')->name('store.area.cliente');
  });
  Route::post('delete-conditions', 'UserController@deleteConditions')->name('delete.conditions');


  // ANALIZADORES SUBMETERING
  /* Route::middleware(['matchAuthUserId'])->group(function () {
     Route::get('analizadores', 'AnalyzerController@showAnalyzers')->name('analyzersgroup_valid1');
     Route::get('analizadores/grupos', 'AnalyzerController@showAnalyzers')->name('analyzersgroup_valid2');
     Route::get('analizadores/grupos/{user_id}', 'AnalyzerController@showAnalyzers')->name('analyzersgroup');
     Route::get('analizadores/grupos/{user_id}/{group_id}', 'AnalyzerController@showAnalyzersSelected')->name('analyzersgroupselected');
     Route::get('analizadores/{user_id}', 'UserController@Analizadores')->name('analizadores');
     // Route::get('analizadores_potencia_corrientes/{user_id}/{group_id}/{user_id}', ['as' => 'analizadores.graficas', 'uses' => 'UserController@AnalizadoresGraficas']);
     // Route::post('analizadores_informes_alertas/{user_id}/{user_id}', ['as' => 'analizadores.informes.alertas', 'uses' => 'UserController@AnalizadoresInformesAlertasProgramados']);
     // Route::get('analizadores_potencia_corrientes', 'UserController@AnalizadoresGraficas')->name('analizadores.graficas_valid1');
     // Route::get('analizadores_potencia_corrientes/{user_id}', 'UserController@AnalizadoresGraficas')->name('analizadores.graficas_valid2');
     // Route::get('analizadores_potencia_corrientes/{user_id}/{group_id?}', 'UserController@AnalizadoresGraficas')->name('analizadores.graficas_valid3');
     Route::get('analizadores_potencia_corrientes/{user_id}/{group_id?}/{anlz_id?}', 'UserController@AnalizadoresGraficas')->name('analizadores.graficas');
     Route::post('analizadores_informes_alertas/{user_id}/{anlz_id}', 'UserController@AnalizadoresInformesAlertasProgramados')->name('analizadores.informes.alertas');
   });*/

  Route::post('exportar_csv_analizador', 'UserController@exportCSVAnalizador')->name('export.csv.analizador');

  //SEGUIMIENTO DE CONSUMO 
  Route::middleware(['matchAuthUserId'])->group(function () {
    Route::get('seguimiento_objetivos/{user_id}', 'SeguimientoObjetivosController@Seguimiento')->name('seguimiento.objetivos');
    Route::get('seguimiento_objetivos/{user_id}/{counter_id}', 'SeguimientoObjetivosController@SeguimientoCounter')->name('seguimiento.objetivos.count');
  });
  Route::post('seguimiento_objetivos', 'SeguimientoObjetivosController@SeguimientoCambiarFechas')->name('seguimiento.objetivos.change');
  Route::post('seguimiento_objetivos/period', 'SeguimientoObjetivosController@SeguimientoCambiarPeriodo')->name('seguimiento.objetivos.period');

  //[Rogelio R - Workana] - Menú de Log de Accesos
  Route::middleware(['matchAuthUserId'])->group(function () {
    Route::get('logaccesos/{user_id}', 'AccessLogController@getAccessLog')->name('logaccesos');
    Route::get('logaccesos-csv/{user_id}', 'AccessLogController@getAccessLogCsv')->name('logaccesos.export.csv');
  });
});

//Route::get('new_analizadores/{id}', ['as' => 'new_analizadores', 'uses' => 'AnalyzerController@showAnalyzers'])->middleware('auth');

/*
  PRODUCCIÓN SUBMETERING

  Route::get('produccion/databases', ['as' => 'production.databases', 'uses' => 'ProductionController@getDatabases'])->middleware('auth'); //# @Leo W* obtener las BD disponibles en el host de un contador 
  Route::get('produccion/counters/{enterprise}/', ['as' => 'production.counters', 'uses' => 'ProductionController@getCounters'])->middleware('auth'); //# @Leo W* obtener los contadores asociados a una empresa
  Route::get('produccion/connections/{counter}/', ['as' => 'production.connection', 'uses' => 'ProductionController@getConnections'])->middleware('auth'); //# @Leo W* obtener las conexiones de contador
  Route::get('produccion/tables/{counter}/{id}', ['as' => 'production.database_counter', 'uses' => 'ProductionController@getCounterTables'])->middleware('auth'); //# @Leo W* obtener las BD de produccion habilitadas para un contador

  Route::get('produccion/visualizar/{id}', ['as' => 'production.data', 'uses' => 'ProductionController@showProductionData'])->middleware('auth'); 
  Route::get('produccion/create', ['as' => 'production.create', 'uses' => 'ProductionController@create'])->middleware('auth'); 
  Route::get('produccion', ['as' => 'production.list', 'uses' => 'ProductionController@list'])->middleware('auth'); 
  Route::get('produccion/database', ['as' => 'production.database', 'uses' => 'ProductionController@readDataBase'])->middleware('auth'); 
  Route::get('produccion/edit/{production_id}', ['as' => 'production.edit', 'uses' => 'ProductionController@show'])->middleware('auth'); 
  Route::get('produccion/{id}', ['as' => 'produccion', 'uses' => 'UserController@Produccion'])->middleware('auth'); 
  Route::post('produccion/csv', ['as' => 'production.csv', 'uses' => 'ProductionController@exportCSV'])->middleware('auth'); 
  Route::post('produccion/', ['as' => 'production.save', 'uses' => 'ProductionController@saveProduction'])->middleware('auth'); 
  Route::post('produccion/{production_id}', ['as' => 'production.update', 'uses' => 'ProductionController@updateProduction'])->middleware('auth'); 
  Route::post('connection/connectionMeters', 'NewConnectionsController@saveConnection')->name('production.ConnectionSave')->middleware('auth'); 
  Route::delete('produccion/{id}', ['as' => 'production.delete', 'uses' => 'ProductionController@removeProduction'])->middleware('auth'); 

  IDENTIFICADORES
  Route::get('identificadores/{id}', ['as' => 'identificadores', 'uses' => 'UserController@Identificadores'])->middleware('auth'); 
*/
//RESTAURADO PARA PODER USAR LAS CONEXIONES @JulioM 2021-09-13
// PRODUCCIÓN SUBMETERING
//Route::get('produccion/databases',['as' => 'production.databases', 'uses' => 'ProductionController@getDatabases'])->middleware('auth'); //# @Leo W* obtener las BD disponibles en el host de un contador 
Route::get('produccion/counters/{enterprise}/', ['as' => 'production.counters', 'uses' => 'ProductionController@getCounters'])->middleware('auth'); //# @Leo W* obtener los contadores asociados a una empresa
Route::get('produccion/connections/{counter}/', ['as' => 'production.connection', 'uses' => 'ProductionController@getConnections'])->middleware('auth'); //# @Leo W* obtener las conexiones de contador
Route::get('produccion/tables/{counter}/{id}', ['as' => 'production.database_counter', 'uses' => 'ProductionController@getCounterTables'])->middleware('auth'); //# @Leo W* obtener las BD de produccion habilitadas para un contador

Route::get('produccion/visualizar/{id}', ['as' => 'production.data', 'uses' => 'ProductionController@showProductionData'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::get('produccion/create', ['as' => 'production.create', 'uses' => 'ProductionController@create'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::get('produccion', ['as' => 'production.list', 'uses' => 'ProductionController@list'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::get('produccion/database', ['as' => 'production.database', 'uses' => 'ProductionController@readDataBase'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::get('produccion/edit/{production_id}', ['as' => 'production.edit', 'uses' => 'ProductionController@show'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::get('produccion/{id}', ['as' => 'produccion', 'uses' => 'UserController@Produccion'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::post('produccion/csv', ['as' => 'production.csv', 'uses' => 'ProductionController@exportCSV'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::post('produccion/', ['as' => 'production.save', 'uses' => 'ProductionController@saveProduction'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::post('produccion/{production_id}', ['as' => 'production.update', 'uses' => 'ProductionController@updateProduction'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::post('connection/connectionMeters', 'NewConnectionsController@saveConnection')->name('production.ConnectionSave')->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
Route::delete('produccion/{id}', ['as' => 'production.delete', 'uses' => 'ProductionController@removeProduction'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión
//RESTAURADO PARA PODER USAR LAS CONEXIONES @JulioM 2021-09-13
// IDENTIFICADORES
Route::get('identificadores/{id}', ['as' => 'identificadores', 'uses' => 'UserController@Identificadores'])->middleware('auth'); //[Rogelio R - Workana] Se agrega la validación de la sesión

Route::get('representacion/counters/{enterprise}/', ['as' => 'representation.counters', 'uses' => 'RepresentationController@getCounters'])->middleware('auth');
Route::get('representacion/connections/{counter}/', ['as' => 'representation.connection', 'uses' => 'RepresentationController@getConnections'])->middleware('auth');
Route::get('representacion/tables/{counter}/{id}', ['as' => 'representation.database_counter', 'uses' => 'RepresentationController@getCounterTables'])->middleware('auth');
Route::get('representacion/visualizar/{id}', ['as' => 'representation.data', 'uses' => 'RepresentationController@showRepresentationData'])->middleware('auth');
Route::get('representacion/create', ['as' => 'representation.create', 'uses' => 'RepresentationController@create'])->middleware('auth');
Route::get('representacion', ['as' => 'representation.list', 'uses' => 'RepresentationController@list'])->middleware('auth');
Route::get('representacion/database', ['as' => 'representation.database', 'uses' => 'RepresentationController@readDataBase'])->middleware('auth');
Route::get('representacion/edit/{representation_id}', ['as' => 'representation.edit', 'uses' => 'RepresentationController@show'])->middleware('auth');
Route::get('representacion/{id}', ['as' => 'representacion', 'uses' => 'UserController@Representacion'])->middleware('auth');
Route::post('representacion/csv', ['as' => 'representation.csv', 'uses' => 'RepresentationController@exportCSV'])->middleware('auth');
Route::post('representacion/', ['as' => 'representation.save', 'uses' => 'RepresentationController@saveRepresentation'])->middleware('auth');
Route::post('representacion/{representation_id}', ['as' => 'representation.update', 'uses' => 'RepresentationController@updateRepresentation'])->middleware('auth');
Route::delete('representacion/{id}', ['as' => 'representation.delete', 'uses' => 'RepresentationController@removeRepresentation'])->middleware('auth');

//LeoW
Route::get('estadisticas/configuracion', 'StatisticsController@listAll')->middleware('auth')->name("statistics.listAll");
Route::get('estadisticas/configuracion/{type}/insertar', 'StatisticsController@insert')->where('type', 'produccion|indicadores|representacion')->middleware('auth')->name("statistics.insert");
Route::get('estadisticas/configuracion/{type}/{user_id?}', 'StatisticsController@list')->where('type', 'produccion|indicadores|representacion')->middleware('auth')->name("statistics.list");
Route::get('estadisticas/configuracion/{type}', 'StatisticsController@update')->middleware('auth')->name("statistics.update_valid1");
Route::get('estadisticas/configuracion/{type}/modificar/{id}', 'StatisticsController@update')->where('type', 'produccion|indicadores|representacion')->middleware('auth')->name("statistics.update");
Route::get('estadisticas/configuracion/{type}/eliminar', 'StatisticsController@delete')->where('type', 'produccion|indicadores|representacion')->middleware('auth')->name("statistics.deletevalid");
Route::get('estadisticas/configuracion/{type}/eliminar/{id}', 'StatisticsController@delete')->where('type', 'produccion|indicadores|representacion')->middleware('auth')->name("statistics.delete");
Route::get('estadisticas', 'StatisticsController@resume')->middleware('auth')->name("statistics.resume_valid1");
Route::get('estadisticas/{type}', 'StatisticsController@resume')->middleware('auth')->name("statistics.resume_valid2");
Route::get('estadisticas/{type}/{user_id}', 'StatisticsController@resume')->where('type', 'produccion|indicadores|representacion')->middleware('auth')->name("statistics.resume");

Route::get('api/statics/configs', 'StatisticsApiController@list')->middleware('auth');
Route::post('api/statics/configs', 'StatisticsApiController@insert')->middleware('auth');
Route::get('api/statics/configs/{id}', 'StatisticsApiController@get')->middleware('auth');
Route::put('api/statics/configs/{id}', 'StatisticsApiController@update')->middleware('auth');
Route::delete('api/statics/configs/{id}', 'StatisticsApiController@delete')->middleware('auth');
Route::get('api/statics/resume/{id}', 'StatisticsApiController@resume')->middleware('auth');

/*
  Route::get('indicadores', 'IndicatorController@list')->name("indicators.list"); 
  Route::post('indicadores', 'IndicatorController@insert')->name("indicators.insert"); 
  Route::put('indicadores/{id}', 'IndicatorController@update')->name("indicators.update")->middleware('auth'); 
  Route::delete('indicadores/{id}', 'IndicatorController@delete')->name("indicators.delete")->middleware('auth');

  Route::get('indicadores/configuracion', 'IndicatorController@config_list')->name("indicator_configuration.list"); 
  Route::get('indicadores/configuracion/insertar', 'IndicatorController@config_insert')->name("indicator_configuration.insert"); 
  Route::get('indicadores/configuracion/modificar/{id}', 'IndicatorController@config_update')->name("indicator_configuration.update"); 
  Route::get('indicadores/visualizar', 'IndicatorController@resume')->name("indicator.resume"); 


  Route::get('api/statics/configs', 'StaticController@listAll'); 
  Route::get('api/statics/{key}/configs', 'StaticController@list'); 
  Route::post('api/statics/{key}/configs', 'StaticController@insert'); 
  Route::get('api/statics/{key}/configs/{id}', 'StaticController@get'); 
  Route::put('api/statics/{key}/configs/{id}', 'StaticController@update'); 
  Route::delete('api/statics/{key}/configs/{id}', 'StaticController@delete'); 
  Route::get('api/statics/show/{id}', 'StaticController@show'); 
*/

Route::get('api/enterprices', 'StatisticsApiController@enterprice_list')->middleware('auth');
Route::get('api/enterprices/{id}/meters', 'StatisticsApiController@enterprice_meters_list')->middleware('auth');
Route::get('api/production_types', 'StatisticsApiController@production_types_list')->middleware('auth');

//Manual
Route::get('estadisticas/manual/{user_id}', 'StatisticsController@manual')->middleware('auth')->name("statistics.manual_data");
;
Route::get('api/manual/config/{id}', 'StatisticsApiController@manual_get_config')->middleware('auth');
Route::post('api/manual/config/{id}', 'StatisticsApiController@manual_save_config')->middleware('auth');
Route::get('api/manual/config/{id}/tables', 'StatisticsApiController@manual_get_tables')->middleware('auth');

Route::get('api/manual/fields/{id}/{table}', 'StatisticsApiController@manual_get_fields')->middleware('auth');
Route::post('api/manual/fields/{id}/{table}/{field}', 'StatisticsApiController@manual_update_field_name')->middleware('auth');

Route::get('api/manual/fields/{id}/{table}/{field}/values', 'StatisticsApiController@manual_get_fields_values')->middleware('auth');
Route::post('api/manual/fields/{id}/{table}/{field}/values', 'StatisticsApiController@manual_save_fields_values')->middleware('auth');

//Route::get('estadisticas/migration', 'StatisticsApiController@migration')->middleware('auth'); (Se ejecutó para importar las anteriores configuraciones de Produccion desarrollado por Leonardo)