<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/16
 * Time: 18:31
 */

$config['users'] = array(
    'amii_test' => '10adffff2713e2caaad10abcc5d207e5', //密码：md5($pwd.#deployer)
    'liuzhuanwei' => '3d1865a9d05547e0a8bd46d7430d1db7',
);

define('GIT_HOST_NEW', 'https://amii_test:a123456@git-dev.amii.com:8443/r/');

$config['projects'] = array(
    // 测试服务器的部署配置
    'test' => array(
        'mtest_outer' => array(
            'comment' => '测试1：mtest.amii.com -- amii_apps_backend_outer',
            'project_path' => '/home/vagrant/www/amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_outer.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/mtest_outer.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'mtest_inner' => array(
            'comment' => '测试1：mtest.amii.com -- amii_apps_backend_inner',
            'project_path' => '/home/vagrant/www/amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/mtest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'mtest2_outer' => array(
            'comment' => '测试2：mtest2.amii.com -- amii_apps_backend_outer',
            'project_path' => '/home/vagrant/www/mtest2_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_outer.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/mtest2_outer.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'mtest2_inner' => array(
            'comment' => '测试2：mtest2.amii.com -- amii_apps_backend_inner',
            'project_path' => '/home/vagrant/www/mtest2_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/mtest2_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'dtest_outer' => array(
            'comment' => 'app开发：dtest.amii.com -- amii_apps_backend_outer',
            'project_path' => '/home/vagrant/www/dtest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_outer.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/dtest_outer.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'dtest_inner' => array(
            'comment' => 'app开发：dtest.amii.com -- amii_apps_backend_inner',
            'project_path' => '/home/vagrant/www/dtest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/dtest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'weitest_outer' => array(
            'comment' => '老威：weitest.amii.com -- amii_apps_backend_outer',
            'project_path' => '/home/vagrant/www/weitest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_outer.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/weitest_outer.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
        'weitest_inner' => array(
            'comment' => '老威：weitest.amii.com -- amii_apps_backend_inner',
            'project_path' => '/home/vagrant/www/weitest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/weitest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
        'fetest_inner' => array(
            'comment' => '前端：fetest.amii.com -- amii_apps_backend_inner',
            'project_path' => '/home/vagrant/www/fontend',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/fetest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
        'fetest_outer' => array(
            'comment' => '前端：fetest.amii.com -- amii_apps_backend_outer',
            'project_path' => '/home/vagrant/www/fontend',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_outer.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/fetest_outer.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
        'fetest2_inner' => array(
            'comment' => '前端：fetest2.amii.com -- amii_apps_backend_inner',
            'project_path' => '/home/vagrant/www/fontend2',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/fetest2_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
        'fetest2_outer' => array(
            'comment' => '前端：fetest2.amii.com -- amii_apps_backend_outer',
            'project_path' => '/home/vagrant/www/fontend2',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_outer.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/fetest2_outer.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
    ),

    // 生产端的部署配置
    'production' => array(/*'www' => array(
            'comment' => 'v35项目 -- 生产端',
            'ssh_hosts' => array('www@10.139.109.115:22','www@10.139.48.118:22'),
            'ssh_user' => 'www',
            'project_path' => '/www/html',
            'project_git_origin' => GIT_HOST_NEW.'p2p-server-v35.git',
            'switch_branch' => false,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'public/db_config.php',
                'public/production.lock'
            ),
            'clean_file_cache' => array(
                'public/runtime/*'
            ),
            'delete_files' => array(
                'public/development.lock',
                'public/test.lock',
                '.idea'
            )
        ),*/
    )
);
return $config;
