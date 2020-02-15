<div class="form-group">
    <label for="active">Активен?</label>
    <select name="active" id="active" class="form-control">
        <option value="1">Да</option>
        <option value="0">Нет</option>
    </select>
</div>
<div class="form-group">
    <label for="name">Название</label>
    <input id="name" type="text" name="name" value="{{old('name', $mailbox->name)}}" class="form-control">
</div>
<div class="form-group">
    <label for="host">IMAP сервер</label>
    <input id="host" type="text" name="host" value="{{old('host', $mailbox->host)}}" placeholder="imap.google.com" class="form-control">
</div>
<div class="form-group">
    <label for="host">IMAP порт</label>
    <input id="host" type="number" name="port" value="{{old('port', $mailbox->port)}}" placeholder="993" class="form-control">
</div>
<div class="form-group">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{old('email', $mailbox->email)}}" class="form-control" autocomplete="off">
</div>
<div class="form-group">
    <label for="password">Пароль</label>
    <input id="password" type="password" name="password" value="{{old('password', $mailbox->password)}}" class="form-control" autocomplete="new-password">
</div>
<div class="form-group">
    <label for="encryption">Шифрование</label>
    <select name="encryption" id="encryption" class="form-control">
        @foreach(['none', 'tls', 'ssl'] as $encryption)
            <option value="{{$encryption}}" {{$encryption == old('encryption', $mailbox->encryption)}}>{{$encryption}}</option>
        @endforeach
    </select>
</div>
