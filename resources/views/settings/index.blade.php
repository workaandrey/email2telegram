@extends('layouts.app')
@section('title', 'Настройки')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{route('settings.save')}}" method="POST">
                    {{csrf_field()}}
                    <div class="card">
                        <div class="card-header">Настройки</div>

                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Токен телеграм бота</label>
                                <input id="name" type="text" name="telegram_token" value="{{old('telegram_token', auth()->user()->telegram_token)}}" class="form-control">
                            </div>
                        </div>

                        <div class="card-footer">
                            <a class="float-left btn" href="{{route('home')}}">Отмена</a>
                            <button type="submit" class="btn btn-success float-right">Сохранить</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
