@extends('layouts.app')
@section('title', 'Создать конфигурацию для почтового ящика')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{route('mailbox.store')}}" method="POST">
                    {{csrf_field()}}
                    <div class="card">
                        <div class="card-header">Создать конфигурацию для почтового ящика</div>

                        <div class="card-body">
                            @include('mailboxes._form')
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
