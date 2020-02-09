<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if($errors ->any())
                <div class="alert alert-danger m-b-0">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <ul class="m-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @include('flash::message')
        </div>
    </div>
</div>
