@extends('layouts.app')

@section('content')
<div class="container">
    @if(Session::has('status'))
        <div class="alert alert-info">
            <span>{{Session::get('status')}}</span>
        </div>
        @endif
    {{$url_callback_bot ?? ''}}

<form action="{{route('admin.setting.store')}}" method="post">
{{csrf_field()}}
    <div class="form-group">
        <label>Url callback для TelegramBot</label>
        <div class="input-group">
            <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-haspopup="true" aria-expanded="false">Действие<span class="caret"></span>

                </button>
                <ul class="dropdown-menu">
                    <li><a id="postUrl" href="#" onclick="document.getElementById('url_callback_bot').value='{{$url_callback_bot ?? '' }}'">Вставить url</a></li>
                    <li><a href="#" onclick="event.preventDefault(); document.getElementById('setwebhook').submit();">Отправить url</a></li>
                    <li><a href="#" onclick="event.preventDefault(); document.getElementById('getwebhookinfo').submit();">Получить информацию</a></li>
                </ul>

            </div>
            <label for="url_callback_bot"></label><input type="url" class="form-control" id="url_callback_bot" name="url_callback_bot" value="{{$url_callback_bot ?? '' }}">

    </div>

    </div>
<button class="btn btn-primary" type="submit">Сохранить</button>
</form>
    <form id="setwebhook" action="{{route('admin.setting.setwebhook')}}" method="POST" style="display:none;">
        {{csrf_field()}}
        <input type="hidden" name="url" value="{{$url_callback_bot ?? '' }}">

    </form>
    <form id="getwebhookinfo" action="{{route('admin.setting.getwebhookinfo')}}" method="POST" style="display:none;" >
        {{csrf_field()}}
        <input type="hidden" name="url" value="{{$url_callback_bot ?? ''}}">
    </form>

</div>

    @endsection
