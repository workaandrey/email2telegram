@setup
//user on web-server
  $user='';
  $timezone='Europe/Moscow';
//path to the directory on web-server
  $path='/var/www/telegrambot;
  $current=$path.'/current';
//where take your code (git repository)
  $repo ="";
  $branch='master';
//Directory and files with chmod 775
  $chomods=[
  'storage/logs'
  ];
  $date = new DateTime('now', new DateTimeZone($timezone));
  $release =$path.'/releases/'.$date->format('YmdHis');
@endsetup
@servers('production'=>$user.'@109.235.65.94'])
@task('clone', ['on' =>$on])
  mkdir -p {{$release}}
  git clone --depth l -b {{$branch}}"{{$repo}}"{{$release}}
  echo "#1 - Repository has been cloned"
@endtask
@task('composer', ['on' =>$on])
  composer self-update
  cd {{$release}}
  composer install --no interaction --no-dev --prefer-dist
  echo "#2 - Composer dependencies have been installed"
@endtask
@task('artisan',['on' =>$on])
  cd {{$release}}
  ln -nfs {{$path}}/.env .env;
  chgrp -h www-data .env;
  php artisan config:clear
  php artisan migrate
  php artisan clear-compiled --env=production;
  php artisan optimize --env=production;
  echo "#3 - Production dependencies have been installed";
@endtask

@task('chmod', ['on' =>$on)
  chgrp -R www-data {{$release}};
  chmod -R ug-rwx  {{$release}};
@foreach($chmods as $file)
    chmod -R 775 {{$release}}/{{$file}}
    chown -R {{$user}}:www-data{{$release}}/{{$file}}
    echo "Permission have been set for{{$file}}"
@endforeach
  echo "#4 - Pemissions has been set"
@endtask
@task('update_symlinks')
  ln -nfs {{ $release }} {{ $current }};
  chgrp -h www-data {{ $current }};
  echo "#5 - Symlink has been set"

@endtask
@macro('deploy', ['on' => 'production'])
  clone
  composer
  artisan
  chmod
  update_symlinks
@endmacro



