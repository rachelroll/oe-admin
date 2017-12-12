@servers(['web' => ['root@60.205.110.182'],'rachel'=>['rachel@192.168.0.27']])

@setup

$env = env('APP_ENV');
$repo = 'https://github.com/rachelroll/oe-admin.git';
$release_dir = '/var/www/releases';
$app_dir = '/var/www/oe360-admin';
$release = 'release_' . date('YmdHis');
$now = new DateTime();
$environment = isset($env) ? $env : "test";

@endsetup

@story('deploy',['on'=>'web'])
    directory
    git
    composer
    update_permissions
@endstory

@task('directory')

[ -d {{ $release_dir }} ] || mkdir {{ $release_dir }};
@endtask

@task('git')

    echo git clone {{  $repo }} {{ $release }};
    cd {{ $release_dir }};
    git clone {{  $repo }} {{ $release }} --branch=master
    cd {{ $release }}
    git submodule init
    git submodule update

@endtask

@task('composer')
    cd {{$release_dir}}/{{$release}}
    pwd
    composer install --prefer-dist
@endtask

@task('update_permissions')
cd {{$release_dir}}
chgrp -R www-data {{ $release }}
chmod -R ug+rwx {{  $release }}
@endtask

@task('update_symlinks')
ln -nfs {{ $release_dir }}/{{ $release }} {{ $app_dir }};
chgrp -h www-data {{ $app_dir }};
@endtask