<p><a href="{{route('mailbox.create')}}" class="btn btn-success">Create mailbox</a></p>
<table class="table table-hover table-bordered ">
    <thead class="thead-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Email</th>
        <th scope="col">Host</th>
        <th scope="col">Port</th>
        <th scope="col">Encryption</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($mailboxes as $mailbox)
    <tr>
        <th class="align-middle" scope="row">{{$mailbox->id}}</th>
        <td class="align-middle">{{$mailbox->name}}</td>
        <td class="align-middle">{{$mailbox->email}}</td>
        <td class="align-middle">{{$mailbox->host}}</td>
        <td class="align-middle">{{$mailbox->port}}</td>
        <td class="align-middle">{{$mailbox->encryption}}</td>
        <td class="align-middle text-center">
        <a href="{{route('mailbox.edit', $mailbox)}}" class="btn btn-secondary">Edit</a>
        |
        <a href="javascript:;" class="btn btn-danger"
           onclick="if(confirm('Do you really want to delete this item?')) $(this).next().submit();">Delete</a>
        <form action="{{route('mailbox.destroy', $mailbox)}}" method="post">
            {{method_field('DELETE')}}
            {{csrf_field()}}
        </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
