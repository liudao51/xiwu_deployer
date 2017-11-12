<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/17
 * Time: 00:19
 */

namespace App\Services;

use App\Libs\Config;

class GitService
{
    private static $project_path;
    private static $project_git_origin;
    private static $switch_branch;
    private static $backup_path;
    private static $backup_files;
    private static $delete_files;
    private static $build_cmd;
    private static $clean_cache_cmd;
    private static $clean_file_cache;
    private static $ssh_hosts;
    private static $ssh_conns;
    private static $comment;
    private static $deploy_task;

    private static function exe_cmd($cmd)
    {
//        if (strpos($cmd, 'rm ') !== false)
//            return 'bad cmd';

        $result = '';
        if (!empty(self::$ssh_hosts)) {
            for ($i = 0; $i < count(self::$ssh_hosts); $i++) {
                $key = md5(self::$ssh_hosts[$i]);

                list($user, $tmp) = explode('@', self::$ssh_hosts[$i]);
                list($host, $port) = explode(':', $tmp);

                $port = intval($port);
                if ($port == 0) {
                    $port = 22;
                }

                if (!isset(self::$ssh_conns[$key]) || self::$ssh_conns[$key] == null) {
                    self::$ssh_conns[$key] = ssh2_connect($host, $port);
                    $r = ssh2_auth_pubkey_file(self::$ssh_conns[$key], $user, '/home/www/.ssh/id_rsa.pub', '/home/www/.ssh/id_rsa');
                }

                $result .= "\n 连接{$host}服务器...\n";
                $stream = ssh2_exec(self::$ssh_conns[$key], $cmd);
                stream_set_blocking($stream, true);
                $result .= trim(stream_get_contents($stream));
                mlog('remote.........');
                mlog($cmd);
                mlog($result);
                mlog('remote.........end');
                //by xssd zqf 20161025 每次重新连
                self::$ssh_conns[$key] = null;
            }
        } else {
            // 执行本地命令
            //$result = shell_exec($cmd);
            exec($cmd, $result);
            $result = implode("\n", $result);
            //mlog('local.........');
            //mlog($cmd);
            //mlog($result);
            //mlog('local.........end');
        }


        //$result = $cmd ."\n". $result;

        return self::format($result);
    }

    private static function format($result)
    {
        $result = trim($result);
        //$result = htmlentities($result);
        //$result = nl2br($result);
        return $result;
    }

    /**
     * 显示目录列表
     *
     * @return mixed
     */
    public static function showDir()
    {
        $cmd = 'pwd';

        $result['cmd'] = $cmd;

        $result['output'] = self::exe_cmd($cmd);

        return $result;
    }

    /**
     * 设置项目配置
     *
     * @param $project
     */
    public static function setProjectConfig($project)
    {

        $env = env('APP_ENV', 'test');
        $project_config = Config::get('deploy_config')['projects'][$env][$project];

        self::$ssh_hosts = isset($project_config['ssh_hosts']) ? $project_config['ssh_hosts'] : [];

        self::$project_path = $project_config['project_path'];
        self::$project_git_origin = $project_config['project_git_origin'];
        self::$switch_branch = $project_config['switch_branch'];
        self::$backup_path = $project_config['backup_path'];
        self::$comment = $project_config['comment'];
        self::$backup_files = isset($project_config['backup_files']) ? $project_config['backup_files'] : [];
        self::$delete_files = isset($project_config['delete_files']) ? $project_config['delete_files'] : [];
        self::$build_cmd = isset($project_config['build_cmd']) ? $project_config['build_cmd'] : '';
        self::$clean_cache_cmd = isset($project_config['clean_cache_cmd']) ? $project_config['clean_cache_cmd'] : [];
        self::$clean_file_cache = isset($project_config['clean_file_cache']) ? $project_config['clean_file_cache'] : [];

        self::$deploy_task = isset($project_config['deploy_task']) ? $project_config['deploy_task'] : '';
    }

    /**
     * 取得项目注释
     *
     * @return mixed
     */
    public static function getProjectComment()
    {
        return self::$comment;
    }

    /**
     * 取得项目状态(git status)
     * @return mixed
     */
    public static function getStatus()
    {
        $cmd = 'cd ' . self::$project_path . ';git status;';

        $result['cmd'] = $cmd;

        $result['output'] = self::exe_cmd($cmd);

        return $result;
    }

    /**
     * 是否允许切换分支
     *
     * @return mixed
     */
    public static function isEnableSwitchBranch()
    {
        return self::$switch_branch;
    }

    /**
     * 基于master创建新的分支(git branch/git checkout -b)
     * @return mixed
     */
    public static function newbranch($new_branch_name)
    {
        $result['output'] = self::backup();

        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git add .;';
        $cmd .= 'git reset --hard HEAD;';
        $cmd .= 'git checkout master;';
        $cmd .= 'git checkout -b ' . $new_branch_name . ';';

        $result['cmd'] = $cmd;

        $result['output'] .= self::exe_cmd($cmd);

        if (self::$deploy_task != '') {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= '/bin/bash ' . self::$deploy_task . ';';
            $result['output'] .= self::exe_cmd($cmd);
        }
        $result['output'] .= self::restore();

        return $result;
    }

    /**
     * 基于原feature创建新的分支(git branch/git checkout -b)
     * @return mixed
     */
    public static function newbranch2($new_branch_name)
    {
        $result['output'] = self::backup();

        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git add .;';
        $cmd .= 'git reset --hard HEAD;';
        $cmd .= 'git fetch ' . self::$project_git_origin . ' ' . $new_branch_name . ':' . $new_branch_name . ';';
        $cmd .= 'git checkout ' . $new_branch_name . ';';

        $result['cmd'] = $cmd;

        $result['output'] .= self::exe_cmd($cmd);

        if (self::$deploy_task != '') {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= '/bin/bash ' . self::$deploy_task . ';';
            $result['output'] .= self::exe_cmd($cmd);
        }
        $result['output'] .= self::restore();

        return $result;
    }

    /**
     * 删除分支并切换到master(git branch -D)
     * @param delete_branch_name
     * @return mixed
     */
    public static function deleteBranch($delete_branch_name)
    {
        $result['output'] = self::backup();

        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git add .;';
        $cmd .= 'git reset --hard HEAD;';
        $cmd .= 'git checkout master;';
        $cmd .= 'git branch -D ' . $delete_branch_name . ';';

        $result['cmd'] = $cmd;

        $result['output'] .= self::exe_cmd($cmd);

        if (self::$deploy_task != '') {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= '/bin/bash ' . self::$deploy_task . ';';
            $result['output'] .= self::exe_cmd($cmd);
        }
        $result['output'] .= self::restore();

        return $result;
    }


    /**
     * 拉取指定分支代码到本地仓库当前分支(git pull)
     * @param branch_name
     *
     * @return mixed
     */
    public static function pull($branch_name)
    {
        $result['output'] = self::backup();

        $cmd = 'cd ' . self::$project_path . ';';
        $cmd .= 'git pull ' . self::$project_git_origin . ' ' . $branch_name . ';';  //拉取指定分支代码到本地仓库当前分支

        $result['cmd'] = $cmd;

        $result['output'] .= self::exe_cmd($cmd);

        if (self::$deploy_task != '') {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= '/bin/bash ' . self::$deploy_task . ';';
            $result['output'] .= self::exe_cmd($cmd);
        }
        $result['output'] .= self::restore();

        return $result;
    }

    /**
     * 智能拉取指定分支代码到本地仓库当前分支(git pull)
     * @param branch_name
     *
     * @return mixed
     */
    public static function intellpull($branch_name)
    {
        $result['output'] = self::backup();

        $cmd = 'cd ' . self::$project_path . ';';
        $cmd .= 'git add .;';
        $cmd .= 'git reset --hard HEAD~50;'; //向前回滚50个版本
        $cmd .= 'git pull ' . self::$project_git_origin . ' ' . $branch_name . ';';  //拉取指定分支代码到本地仓库当前分支

        $result['cmd'] = $cmd;

        $result['output'] .= self::exe_cmd($cmd);

        if (self::$deploy_task != '') {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= '/bin/bash ' . self::$deploy_task . ';';
            $result['output'] .= self::exe_cmd($cmd);
        }
        $result['output'] .= self::restore();

        return $result;
    }

    /**
     * 拉取远端仓库全部对象到本地仓库(git fetch)
     * @param branch_name
     *
     * @return mixed
     */
    public static function fetch($branch_name)
    {
        $result['output'] = self::backup();

        $cmd = 'cd ' . self::$project_path . ';';
        $cmd .= 'git fetch ' . self::$project_git_origin . ';';  //拉取远端仓库全部对象到本地仓库
        $cmd .= 'git fetch ' . self::$project_git_origin . ' ' . $branch_name . ';';  //拉取远端仓库指定对象到本地仓库

        $result['cmd'] = $cmd;

        $result['output'] .= self::exe_cmd($cmd);

        if (self::$deploy_task != '') {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= '/bin/bash ' . self::$deploy_task . ';';
            $result['output'] .= self::exe_cmd($cmd);
        }
        $result['output'] .= self::restore();

        return $result;
    }

    /**
     * 取得分支列表(git branch)
     *
     * @return mixed
     */
    public static function getBranches()
    {
        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git branch -v;';

        $result['cmd'] = $cmd;

        $output = $result['output'] = self::exe_cmd($cmd);

        $tmp = explode("\n", $output);

        $branches = [];
        for ($i = 0; $i < count($tmp); $i++) {
            $branches[$i]['branch_name'] = $branches[$i]['version_num'] = $branches[$i]['comment'] = '';
            $branches[$i]['is_current'] = false;

            $tmp2 = explode(" ", $tmp[$i]);
            for ($ii = 0; $ii < count($tmp2); $ii++) {
                $tmp2[$ii] = trim($tmp2[$ii]);

                if ($branches[$i]['branch_name'] == '' && $tmp2[$ii] != '' && $tmp2[$ii] != '*') {
                    $branches[$i]['branch_name'] = $tmp2[$ii];
                } else if ($tmp2[$ii] == '*') {
                    $branches[$i]['is_current'] = true;
                } else if ($branches[$i]['branch_name'] != '' && $branches[$i]['version_num'] == '' && $tmp2[$ii] != '') {
                    $branches[$i]['version_num'] = $tmp2[$ii];
                } else if ($branches[$i]['branch_name'] != '' && $branches[$i]['version_num'] != '' && $tmp2[$ii] != '') {
                    $branches[$i]['comment'] .= $tmp2[$ii] . " ";
                }
            }
        }

        $result['branches'] = $branches;

        return $result;
    }

    /**
     * 取得当前分支
     *
     * @return string
     */
    public static function getCurrentBranch()
    {
        $result = self::getBranches();

        $branches = $result['branches'];

        for ($i = 0; $i < count($branches); $i++) {
            if ($branches[$i]['is_current'])
                return $branches[$i]['branch_name'];
        }

        return 'null';
    }

    /**
     * 取得项目路径
     *
     * @return mixed
     */
    public static function getProjectPath()
    {
        return self::$project_path;
    }

    /**
     * 正常切换分支(git checkout xxx)
     *
     * @param $branch
     * @return string
     */
    public static function switchToBranch($branch)
    {
        $branch = trim($branch);

        if ($branch == '') {
            return 'branch name can not be empty!';
        }

        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git checkout ' . $branch . ';';  //切换分支

        $result['cmd'] = $cmd;

        $result['output'] = self::exe_cmd($cmd);

        return $result;
    }

    /**
     * 强制切换分支(git checkout xxx)
     *
     * @param $branch
     * @return string
     */
    public static function forceSwitchToBranch($branch)
    {
        $branch = trim($branch);

        if ($branch == '') {
            return 'branch name can not be empty!';
        }

        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git add .;'; //把所有更改加入暂存区
        $cmd .= 'git reset --hard HEAD;'; //当前分支还原到最新版本
        $cmd .= 'git checkout ' . $branch . ';'; //切换分支

        $result['cmd'] = $cmd;

        $result['output'] = self::exe_cmd($cmd);

        return $result;
    }


    /**
     * 取得提交日志(git log)
     *
     * @param int $num
     * @return mixed
     */
    public static function getLog($num = 10)
    {
        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git log -' . $num . ';';

        $result['cmd'] = $cmd;

        $output = $result['output'] = self::exe_cmd($cmd);

        $tmp = explode('commit', $output);
        array_shift($tmp);

        $logs = [];
        foreach ($tmp as $k => $v) {
            $tmp2 = explode("\n", $v);

            foreach ($tmp2 as $kk => $vv) {
                $vv = trim($vv);
                if ($kk == 0) {
                    $logs[$k]['revsion'] = $vv;
                } else if (strpos($vv, 'Author') !== false) {
                    $vv = str_replace('Author:', '', $vv);
                    $logs[$k]['author'] = trim($vv);
                } else if (strpos($vv, 'Date') !== false) {
                    $vv = str_replace('Date:', '', $vv);
                    $vv = trim($vv);
                    $logs[$k]['date'] = date('Y-m-d H:i:m', strtotime($vv));
                    $logs[$k]['comment'] = trim($tmp2[$kk + 2]);
                }
            }
        }
        $result['logs'] = $logs;
        return $result;
    }

    /**
     * 查询提交版本(git show xxx)
     *
     * @param $revsion
     * @return bool
     */
    public static function checkRevsion($revsion)
    {
        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git show ' . $revsion . ';';

        $output = $result['output'] = self::exe_cmd($cmd);

        if ($output == '')
            return false;

        return true;
    }

    /**
     * 回滚提示(git rest --hard xxx)
     * @param $revsion
     *
     * @return mixed
     */
    public static function reset($revsion)
    {
        $result['output'] = self::backup();

        $cmd = 'cd ' . self::$project_path . ';';

        $cmd .= 'git add .;'; //把所有更改加入到暂存区
        $cmd .= 'git reset --hard ' . $revsion . ';';  //回滚到指定的版本

        $result['output'] .= self::exe_cmd($cmd);

        if (self::$deploy_task != '') {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= '/bin/bash ' . self::$deploy_task . ';';
            $result['output'] .= self::exe_cmd($cmd);
        }

        $result['output'] .= self::restore();

        return $result;
    }

    /**
     * 根据配置文件,备份指定文件
     */
    public static function backup()
    {
        if (empty(self::$backup_files))
            return;

        $backup_path = self::$backup_path . self::$project_path . '/';
        $backup_path = str_replace('//', '/', $backup_path);

        $source_path = self::$project_path . '/';

        $source_path = str_replace('//', '/', $source_path);

        $backup_files = self::$backup_files;

        if (empty($backup_files)) {
            return;
        }

        $output = "";

        $cmd = "mkdir -p $backup_path";
        self::exe_cmd($cmd);

        /*        if (!is_dir($backup_path))
                {
                    mkdir($backup_path,0777,true);
                }*/

        for ($i = 0; $i < count($backup_files); $i++) {
            $source_file = $source_path . $backup_files[$i];
            $target_file = $backup_path . $backup_files[$i];

            /*            if (file_exists($target_file))
                        {
                            continue;
                        }*/
            $tmp = explode('/', $target_file);
            array_pop($tmp);
            $target_path = implode('/', $tmp);


            $cmd = "mkdir -p $target_path";
            self::exe_cmd($cmd);

            /*            if (!file_exists($target_path))
                            mkdir($target_path,0777,true);*/

            $cmd = "\\cp $source_file $target_file";
            self::exe_cmd($cmd);

//            copy($source_file,$target_file);
            $output .= "文件:{$source_file} 已经备份到 {$target_file}\n";
        }

        return $output;
    }

    /**
     * 根据配置文件,恢复指定文件
     */
    public static function restore()
    {
        if (empty(self::$backup_files)) {
            return '';
        }

        $backup_path = self::$backup_path . self::$project_path . '/';
        $backup_path = str_replace('//', '/', $backup_path);

        $source_path = self::$project_path . '/';

        $source_path = str_replace('//', '/', $source_path);

        $backup_files = self::$backup_files;

        $output = "\n\n";
        for ($i = 0; $i < count($backup_files); $i++) {
            $source_file = $backup_path . $backup_files[$i];
            $target_file = $source_path . $backup_files[$i];

            $cmd = "\\cp $source_file $target_file";
            //dd($cmd);
            self::exe_cmd($cmd);

//            copy($source_file,$target_file);
            $output .= "文件:{$source_file} 已经恢复到 {$target_file}\n";
        }

        return $output;
    }

    /**
     * 执行build
     *
     * @return string
     */
    public static function build()
    {
        $result['output'] = '';

        if (empty(self::$build_cmd)) {
            $result['output'] = '无需build';
            return $result;
        }

        $builds = self::$build_cmd;

        $output = '';

        foreach ($builds as $build) {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= $build;
            $output .= self::exe_cmd($cmd);
        }

        $result['output'] = $output;

        return $result;
    }

    /**
     * 清除命令缓存
     *
     * @return string
     */
    public static function cleanCacheCmd()
    {
        $result['output'] = '';

        if (empty(self::$build_cmd)) {
            $result['output'] = '不需要清除命令缓存';
            return $result;
        }

        $caches = self::$clean_cache_cmd;

        $output = '';

        foreach ($caches as $cache) {
            $cmd = 'cd ' . self::$project_path . ';';
            $cmd .= $cache;
            $output .= self::exe_cmd($cmd);
        }

        $result['output'] = $output;

        return $result;
    }

    /**
     * 是否需要清除缓存
     *
     * @return bool
     */
    public static function needCleanCache()
    {
        return !empty(self::$clean_file_cache);
    }
}