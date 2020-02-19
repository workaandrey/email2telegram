<p>
    <a href="{{route('mailbox.create')}}" class="btn btn-success">Создать новую запись</a>
    или
    <a href="{{route('faq')}}">прочитайте документацию по работе с сервисом</a>
</p>
<table class="table table-hover table-bordered ">
    <thead class="thead-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">Название</th>
        <th scope="col">Активен</th>
        <th scope="col">Email</th>
        <th scope="col">Сервер</th>
        <th scope="col">Порт</th>
        <th scope="col">Шифрование</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($mailboxes as $mailbox)
    <tr>
        <th class="align-middle" scope="row">{{$mailbox->id}}</th>
        <td class="align-middle">{{$mailbox->name}}</td>
        <td class="align-middle">{{$mailbox->is_active ? 'Да': 'Нет'}}</td>
        <td class="align-middle">{{$mailbox->email}}</td>
        <td class="align-middle">{{$mailbox->host}}</td>
        <td class="align-middle">{{$mailbox->port}}</td>
        <td class="align-middle">{{$mailbox->encryption}}</td>
        <td class="align-middle text-center">
        <a href="{{route('mailbox.edit', $mailbox)}}" class="btn btn-secondary">Редактировать</a>
        |
        <a href="javascript:;" class="btn btn-danger"
           onclick="if(confirm('Вы жействительно хотите удалить эту запись?')) $(this).next().submit();">Удалить</a>
        <form action="{{route('mailbox.destroy', $mailbox)}}" method="post">
            {{method_field('DELETE')}}
            {{csrf_field()}}
        </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
