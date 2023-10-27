@if(session()->has('locked'))
    <div class="alert alert-danger" role="alert">
        <h3 class="alert-heading"><strong>@lang('auth.account_locked_title')</strong></h3>
        <br>
        <p>@lang('auth.account_locked_explain')</p>
        <hr>
        <p class="mb-0">@lang('auth.account_locked_solution')</p>
        </div>
@endif