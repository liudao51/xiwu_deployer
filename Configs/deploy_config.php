<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/16
 * Time: 18:31
 */

$config['users'] = array(
    //密码：md5($pwd.#deployer)
    'amii_test' => '39aeca889702345cadcf49a65c4845a8', //amiiplus666
    'liuzhuanwei' => '3d1865a9d05547e0a8bd46d7430d1db7',
);

define('GIT_HOST_NEW', 'https://amii_test:a123456@git-dev.amii.com:8443/r/');

$config['projects'] = array(
    // 测试服务器的部署配置
    'test' => array(
        'distest_inner' => array(
            'comment' => '测试：distest.amii.com -- amii_apps_backend_distribution_inner',
            'project_path' => '/home/vagrant/www/distest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_distribution_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/distest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'update_db_table_structure_cmd' => array(
                'migrate' => 'php laravel/artisan migrate;',
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'dtest_inner' => array(
            'comment' => 'app开发：dtest.amii.com -- amii_apps_backend_distribution_inner',
            'project_path' => '/home/vagrant/www/dtest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_distribution_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/dtest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'update_db_table_structure_cmd' => array(
                'migrate' => 'php laravel/artisan migrate;',
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'mtest_inner' => array(
            'comment' => '预生产：mtest.amii.com -- amii_apps_backend_distribution_inner',
            'project_path' => '/home/vagrant/www/mtest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_distribution_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/mtest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'update_db_table_structure_cmd' => array(
                'migrate' => 'php laravel/artisan migrate;',
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            )
        ),
        'weitest_inner' => array(
            'comment' => '老威：weitest.amii.com -- amii_apps_backend_distribution_inner',
            'project_path' => '/home/vagrant/www/weitest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_distribution_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/weitest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'update_db_table_structure_cmd' => array(
                'migrate' => 'php laravel/artisan migrate;',
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
        'natest_inner' => array(
            'comment' => '米娜：natest.amii.com -- amii_apps_backend_distribution_inner',
            'project_path' => '/home/vagrant/www/natest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_distribution_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/natest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'update_db_table_structure_cmd' => array(
                'migrate' => 'php laravel/artisan migrate;',
            ),
            'clean_cache_cmd' => array(
                'backend' => 'php laravel/artisan config:clear; php laravel/artisan route:clear; php laravel/artisan view:clear;', //命令间用";"分隔
            ),
            'delete_files' => array(
                '.idea'
            ),
        ),
        'huatest_inner' => array(
            'comment' => '汝华：huatest.amii.com -- amii_apps_backend_distribution_inner',
            'project_path' => '/home/vagrant/www/huatest_amii_web',
            'project_git_origin' => GIT_HOST_NEW . 'amii_apps_backend_distribution_inner.git',
            'switch_branch' => true,
            'backup_path' => '/www/backup/',
            'backup_files' => array(
                'laravel/.env',
                'laravel/public/huatest_inner.lock'
            ),
            'build_cmd' => array(
                'vue' => 'cd laravel; npm run prod;', //命令间用";"分隔
            ),
            'update_db_table_structure_cmd' => array(
                'migrate' => 'php laravel/artisan migrate;',
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
