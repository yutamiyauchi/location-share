@extends('layouts.app')

@section('content')
    <h1 class="text-center font-weight-bold font-family-Tahoma">DISASTER  INFORMATION</h1>
    <form id="submit_form" method="get" action="area_searches">
        @include('commons.area_search')
    </form>
    <div class="container">
        <!--検索ボタンが押された時に表示される-->
        @if(!empty($datas))
        <!--検索条件に一致した投稿を表示-->
            <div class="conteiner">
                <div class="card-group　mx-auto">
                    <div id="lists" class="row">
                        <table class="table table-striped">
                            @foreach ($datas as $data)
                                <div class="card border-0 col-6 col-sm-6 col-md-4 post-cards">
                                    <div class="profile">
                                            <a href="users/{{$data->user->id}}"><img class="float-left user-image" src="{{$data->user->image}}" width="35" height="35"></a>
                                            <div>{{$data->user->name}}</div>
                                    </div>
                                    <a href="alerts/{{$data->id}}"><img src="{{$data->image}}" width="270" height="270" class="img"></a>
                                    <div class="card-body border-bottom">
                                        <div class="contents">
                                            <div class="comment">コメント数：{{count($data->alertcomments)}}</div>
                                            <div class="text-muted">{{$data->time}}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>  
    
@endsection
<style>
    .profile{
        display:inline;
    }
    .form-group{
        width:300px;
        margin:0 auto;
    }
    .comment{
        float:left;
        margin-right:10px;
    }
    .search{
        margin-top:20px;
    }
    .img{
        border-radius:5px;
    }
</style>
