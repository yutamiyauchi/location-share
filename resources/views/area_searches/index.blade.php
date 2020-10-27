@extends('layouts.app')

@section('content')
    <h1 class="text-center font-weight-bold font-family-Tahoma">DISASTER  INFORMATION</h1>
    <div class='form-row'>
        <div class="col-md-4 offset-md-8">
            <div class="submit-select">
                @include('commons.area_search')
                <div class="city"></div>
            </div>
        </div>
    </div>
    <!--検索条件に一致した投稿を表示-->
    @include('alerts.posts', ['alerts'=>$alerts])
    <script src="{{ asset('/js/city_search.js') }}"></script>
@endsection
