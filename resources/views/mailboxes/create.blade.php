@extends('layouts.app')
@section('title', 'Create mailbox')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{route('mailbox.store')}}" method="POST">
                    {{csrf_field()}}
                    <div class="card">
                        <div class="card-header">Create Mailbox</div>

                        <div class="card-body">
                            @include('mailboxes._form')
                        </div>

                        <div class="card-footer">
                            <a class="float-left btn" href="{{route('home')}}">Cancel</a>
                            <button type="submit" class="btn btn-success float-right">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
