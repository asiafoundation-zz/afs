@servers(['staging' => 'root@127.0.0.1', 'production' => 'root@127.0.0.1'])

@task('pull-staging', ['on' => ['staging']])
	cd /home/user/project
	git pull
@endtask

@task('pull-production', ['on' => ['production']])
	cd /home/user/project
	git pull
@endtask

@macro('deploy')
    pull-staging
    pull-production
@endmacro

@task('composer-staging', ['on' => ['staging']])
	cd /home/user/project
	composer update
@endtask

@task('composer-production', ['on' => ['production']])
	cd /home/user/project
	composer update
@endtask

@macro('composer')
    composer-staging
    composer-production
@endmacro