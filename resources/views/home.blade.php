@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Почтовые ящики</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(!$mailboxes->count())
                        <p class="text-center"><a href="{{route('mailbox.create')}}" class="btn btn-success">Создайте первую конфигурацию для почтового ящика</a></p>
                    @else
                        @component('components.mailboxes')
                            @slot('mailboxes', $mailboxes)
                        @endcomponent
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
