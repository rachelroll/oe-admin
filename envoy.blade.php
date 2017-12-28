@servers(['web' => ['root@60.205.110.182'],'rachel'=>['rachel@192.168.0.27']])

@setup
    $env = env('APP_ENV');
    $repo = 'https://github.com/rachelroll/oe-admin.git';
    $release_dir = '/var/www/oe-admin/release';
    $app_dir = '/var/www/oe-admin';
    $shared_dir = '/var/www/oe-admin/shared';
    $current_dir = '/var/www/oe-admin/current';
    $release = 'release_' . date('YmdHis');
    $now = new DateTime();
    $environment = isset($env) ? $env : "test";
    $branch = isset($branch) ? $branch : "master";
@endsetup

@story('deploy',['on'=>'web'])
    directory
    git
    deployment_links
    composer
    deployment_migrate
    deployment_cache
    deployment_optimize
    deployment_finish
    cleanup
@endstory

@task('init',['confirm' => true])
    if [ ! -d {{ $release_dir }} ]; then
    mkdir -p {{ $release_dir }}
    mkdir -p {{ $shared_dir }}
    cd {{ $release_dir }}
    pwd
    git clone {{ $repo }} --branch={{ $branch }} --depth=1 -q {{ $release }}
    echo "Repository cloned"

    mv {{ $release }}/storage {{$shared_dir}}/storage

    chmod -R ug+rwx {{ $shared_dir }}/storage
    echo "Storage directory set up"
    cp {{ $release }}/.env.example {{ $shared_dir }}/.env
    php {{$release_dir}}/{{$release}}/artisan key:generate

    echo "Environment file set up"
    rm -rf {{ $release }}
    echo "Deployment path initialised. Run 'envoy run deploy' now."
    else
    echo "Deployment path already initialised (current symlink exists)!"
    fi
@endtask

@task('directory')
[ -d {{ $release_dir }} ] || mkdir {{ $release_dir }};
@endtask

@task('git')
    echo git clone {{  $repo }} {{ $release }};
    cd {{ $release_dir }};
    git clone {{  $repo }}  --branch={{ $branch }} --depth=1 -q  {{ $release }}
    echo "Repository cloned"
    cd {{ $release }}
    git submodule init
    git submodule update
    echo "submodule inited and updated"
@endtask


{{--only for git pull--}}
@task('git_pull',['on'=>'web','confirm' => true])

    echo 'Your branch is ' {{ $branch }}
    cd {{ $current_dir }}
    pwd
    git pull origin {{ $branch }}
    git submodule foreach git pull origin {{ $branch }}
    php artisan migrate

@endtask


@task('deployment_links')
    cd {{ $release_dir }}
    rm -rf {{ $release }}/storage
pwd
echo ln -s   {{ $shared_dir }}/storage {{ $release }}/storage
    ln -s   {{ $shared_dir }}/storage {{ $release }}/storage
    ln -s  {{ $shared_dir }}/storage/public {{ $release }}/public/storage
    echo "Storage directories set up"
    ln -s  {{ $shared_dir }}/.env {{ $release }}/.env
    echo "Environment file set up"
@endtask


@task('composer')
    cd {{$release_dir}}/{{$release}}
    pwd
    composer install  --quiet --no-dev
@endtask

@task('deployment_migrate')
    php {{$release_dir}}/{{$release}}/artisan migrate  --force --no-interaction
echo 'link'
@endtask

@task('deployment_cache')
    php {{$release_dir}}/{{$release}}/artisan view:clear --quiet
    php {{$release_dir}}/{{$release}}/artisan cache:clear --quiet
    php {{$release_dir}}/{{$release}}/artisan config:clear --quiet
    php {{$release_dir}}/{{$release}}/artisan config:cache --quiet
    echo 'Cache cleared'
@endtask

@task('deployment_optimize')
    php {{$release_dir}}/{{ $release }}/artisan optimize --quiet
@endtask

@task('deployment_finish')
echo ln -s {{$release_dir}}/{{ $release }} {{ $current_dir }}
rm -rf {{ $current_dir }}
ln -s {{$release_dir}}/{{ $release }} {{ $current_dir }}
chmod -R ug+rwx {{ $shared_dir }}/storage
echo "Deployment ({{ $release }}) finished"
@endtask

@task('update_permissions')
    cd {{$release_dir}}
    chgrp -R www-data {{ $release }}
    chmod -R ug+rwx {{  $release }}
@endtask

@task('update_symlinks')
    ln -nfs {{ $release_dir }}/{{$release}} {{ $current_dir }};
    chgrp -h www-data {{ $app_dir }};
@endtask

@task('cleanup')
    cd {{$release_dir}}
    ls -dt */ | tail -n +4 | xargs rm -rf
@endtask

{{--@finished--}}
    {{--@slack('webhook-url', '#bots')--}}
{{--@endfinished--}}