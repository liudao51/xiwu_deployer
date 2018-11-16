<?php
/**
 * Created by PhpStorm.
 * User: crazytang
 * Date: 16/3/25
 * Time: 03:02
 */

namespace App\Controllers;

use App\Libs\Response;
use App\Libs\Request;
use App\Libs\Config;
use App\Services\GitService;

class DeployerController extends BaseController
{
    private $cookie_user_key = 'deployer_name';
    private $cookie_token_key = 'deployer_token';

    public function __construct()
    {
        parent::__construct();


    }

    public function getIndex()
    {
        return view('/index');
    }

    /**
     * 登录
     */
    public function postLogin()
    {
        $user_name = $this->request->post('user_name');

        $users = Config::get('deploy_config')['users'];

        if ($user_name == '' || !isset($users[$user_name])) {
//            redirect()->back()->with('error','用户名不存在')->send();
//            return false;
            return view('index', ['error' => '用户名不存在']);
        }

        $password = $this->request->post('password');

        if ($password == '') {
            return view('index', ['error' => '密码不能为空', 'user_name' => $user_name]);
        }

        $password = md5($password . '#deployer');

        if ($password != $users[$user_name]) {
            return view('index', ['error' => '密码错误', 'user_name' => $user_name]);
        }

        $reponse = new Response();
        $c1 = cookie($this->cookie_user_key, $user_name, 180);
        $c2 = cookie($this->cookie_token_key, $password, 180);

        $reponse->setCookie($c1)->setCookie($c2);

        $reponse->redirect('/deployer/projects');

        return true;
    }

    /**
     * 取得项目列表
     *
     * @return mixed
     */
    public function getProjects()
    {
        $this->checkLogin();
        $env = env('APP_ENV', 'test');
        $projects = Config::get('deploy_config')['projects'][$env];

        $result['projects'] = $projects;
        return view('projects', $result);
    }

    /**
     * 取得某项目
     *
     * @param $project
     * @return mixed
     */
    public function getProject($project)
    {
        $this->checkLogin();

        if (!self::checkProject($project)) {
            return view('project', ['error' => '项目不存在']);
        }

        GitService::setProjectConfig($project);

        $data['branches'] = GitService::getBranches()['branches'];
        $data['project_name'] = $project;
        $data['project_path'] = GitService::getProjectPath();
        $data['current_branch_name'] = GitService::getCurrentBranch();
        $data['last_logs'] = json_encode(GitService::getLog(5)['logs']);
        $data['clear_cache_cmd'] = GitService::cleanCacheCmd();
        $data['switch_branch'] = GitService::isEnableSwitchBranch();
        $data['project_comment'] = GitService::getProjectComment();

        return view('project', $data);
    }

    /**
     * 基于master创建新的分支
     *
     * @param project_name
     * @param new_branch_name
     * @return mixed
     */
    public function postNewbranch()
    {
        $project = $this->request->post('project_name');
        $new_branch_name = $this->request->post('new_branch_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $new_branch_name = trim($new_branch_name);

        $new_branch_name = str_replace(' ', '', $new_branch_name);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        if ($new_branch_name == '') {
            return $this->returnErrorJSON('新的分支名不能为空');
        }

        GitService::setProjectConfig($project);

        if (self::checkBranch($new_branch_name)) {
            return $this->returnErrorJSON('分支已存在');
        }
        $result = GitService::newbranch($new_branch_name);

        $result['last_logs'] = GitService::getLog(5)['logs'];
        return $this->returnJSON($result);
    }

    /**
     * 基于原feature创建新的分支
     *
     * @param project_name
     * @param new_branch_name
     * @return mixed
     */
    public function postNewbranch2()
    {
        $project = $this->request->post('project_name');
        $new_branch_name = $this->request->post('new_branch_name2');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $new_branch_name = trim($new_branch_name);

        $new_branch_name = str_replace(' ', '', $new_branch_name);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        if ($new_branch_name == '') {
            return $this->returnErrorJSON('新的分支名不能为空');
        }

        GitService::setProjectConfig($project);

        if (self::checkBranch($new_branch_name)) {
            return $this->returnErrorJSON('分支已存在');
        }
        $result = GitService::newbranch2($new_branch_name);

        $result['last_logs'] = GitService::getLog(5)['logs'];
        return $this->returnJSON($result);
    }

    /**
     * 删除分支并切换到master
     *
     * @param project_name
     * @param delete_branch_name
     *
     * @return mixed
     */
    public function postDeletebranch()
    {
        $project = $this->request->post('project_name');
        $branch = $this->request->post('delete_branch_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        if (!self::checkProject($project)) {
            return view('deployer/welcome', ['error' => '项目不存在']);
        }

        GitService::setProjectConfig($project);

        if (!self::checkBranch($branch)) {
            return view('deployer/index', ['error' => '分支不存在']);
        }

        $result = GitService::deleteBranch($branch);

        $result['branch_name'] = $branch;
        $result['last_logs'] = GitService::getLog(5)['logs'];
        return $this->returnJSON($result);
    }

    /**
     * 正常切换分支
     *
     * @param project_name
     * @param switch_branch_name
     *
     * @return mixed
     */
    public function postSwitchbranch()
    {
        $project = $this->request->post('project_name');
        $branch = $this->request->post('switch_branch_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        if (!self::checkProject($project)) {
            return view('deployer/welcome', ['error' => '项目不存在']);
        }

        GitService::setProjectConfig($project);

        if (!self::checkBranch($branch)) {
            return view('deployer/index', ['error' => '分支不存在']);
        }

        $result = GitService::switchToBranch($branch);

        $result['branch_name'] = $branch;
        $result['last_logs'] = GitService::getLog(5)['logs'];
        return $this->returnJSON($result);
    }

    /**
     * 强制切换分支
     *
     * @param project_name
     * @param switch_branch_name
     *
     * @return mixed
     */
    public function postForceswitchbranch()
    {
        $project = $this->request->post('project_name');
        $branch = $this->request->post('switch_branch_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        if (!self::checkProject($project)) {
            return view('deployer/welcome', ['error' => '项目不存在']);
        }

        GitService::setProjectConfig($project);

        if (!self::checkBranch($branch)) {
            return view('deployer/index', ['error' => '分支不存在']);
        }

        $result = GitService::forceSwitchToBranch($branch);

        $result['branch_name'] = $branch;
        $result['last_logs'] = GitService::getLog(5)['logs'];
        return $this->returnJSON($result);
    }


    /**
     * 同步服务器代码到当前分支(git pull)
     *
     * @param project_name
     * @param current_branch_name
     *
     * @return mixed
     */
    public function postPull()
    {
        $project = $this->request->post('project_name');
        $branch = $this->request->post('current_branch_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $project = trim($project);

        $branch = trim($branch);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        GitService::setProjectConfig($project);

        if (!self::checkBranch($branch)) {
            return self::returnErrorJSON('分支不存在');
        }

        $result = GitService::pull($branch);


        $logs = GitService::getLog(5);
        $result['last_logs'] = $logs['logs'];

        return $this->returnJSON($result);
    }


    /**
     * 智能同步服务器代码到当前分支(git pull)
     *
     * @param project_name
     * @param current_branch_name
     *
     * @return mixed
     */
    public function postIntellpull()
    {
        $project = $this->request->post('project_name');
        $branch = $this->request->post('current_branch_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $project = trim($project);

        $branch = trim($branch);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        GitService::setProjectConfig($project);

        if (!self::checkBranch($branch)) {
            return self::returnErrorJSON('分支不存在');
        }

        $result = GitService::intellpull($branch);


        $logs = GitService::getLog(5);
        $result['last_logs'] = $logs['logs'];

        return $this->returnJSON($result);
    }

    /**
     * 拉取远端仓库全部对象到本地仓库(git fetch)
     *
     * @param project_name
     * @param current_branch_name
     *
     * @return mixed
     */
    public function postFetch()
    {
        $project = $this->request->post('project_name');
        $branch = $this->request->post('current_branch_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $project = trim($project);


        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        GitService::setProjectConfig($project);

        if (!self::checkBranch($branch)) {
            return self::returnErrorJSON('分支不存在');
        }


        $result = GitService::fetch($branch);


        $logs = GitService::getLog(5);
        $result['last_logs'] = $logs['logs'];

        return $this->returnJSON($result);
    }

    /**
     * 当前分支回滚到指定版本
     *
     * @param project_name
     * @param revsion
     * @return mixed
     */
    public function postReset()
    {
        $project = $this->request->post('project_name');
        $revsion = $this->request->post('revsion');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $revsion = trim($revsion);

        $revsion = str_replace(' ', '', $revsion);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        if ($revsion == '') {
            return $this->returnErrorJSON('版本号不能为空');
        }

        GitService::setProjectConfig($project);

        if (!GitService::checkRevsion($revsion)) {
            return $this->returnErrorJSON('版本号不存在');
        }

        $result = GitService::reset($revsion);

        $result['last_logs'] = GitService::getLog(5)['logs'];
        return $this->returnJSON($result);
    }


    /**
     * 更新数据库表结构
     *
     * @param project_name
     * @return mixed|string
     */
    public function postUpdatedbtablestructure()
    {
        $project = $this->request->post('project_name');
        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $project = trim($project);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        GitService::setProjectConfig($project);

        $result = [];

        //删除命令缓存
        $result = GitService::updateDbTableStructure();

        return $this->returnJSON($result);
    }


    /**
     * 清除缓存
     *
     * @param project_name
     * @param cache_type
     * @return mixed|string
     */
    public function postDeletecache()
    {
        $project = $this->request->post('project_name');
        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $project = trim($project);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        GitService::setProjectConfig($project);

        $result = [];

        //删除命令缓存
        $result = GitService::cleanCacheCmd();

        return $this->returnJSON($result);
    }


    /**
     * 前端build合成
     *
     * @param project_name
     * @param cache_type
     * @return mixed|string
     */
    public function postBuild()
    {
        $project = $this->request->post('project_name');

        if (!$this->checkLogin(false)) {
            return $this->returnErrorJSON('请先登录');
        }

        $project = trim($project);

        if (!self::checkProject($project)) {
            return self::returnErrorJSON('项目不存在');
        }

        GitService::setProjectConfig($project);

        $result = GitService::build();

        return $this->returnJSON($result);
    }


    private function checkProject($project)
    {

        $project = trim($project);

        $env = env('APP_ENV', 'production');

        if ($project == '' || !isset(Config::get('deploy_config')['projects'][$env][$project])) {
            return false;
        }

        return true;
    }

    private function checkBranch($branch)
    {
        $data['branches'] = GitService::getBranches()['branches'];

        $is_branch = false;

        foreach ($data['branches'] as $v) {
            if ($v['branch_name'] == $branch) {
                GitService::switchToBranch($branch);
                $is_branch = true;
                $data['branches'] = GitService::getBranches()['branches'];
                break;
            }
        }

        return $is_branch;
    }

    private function returnJSON($data)
    {
        $result['error'] = 0;
        $result['info'] = '';

        $result['data'] = $data;

        $result = json_encode($result);
        return $result;
    }


    private function returnErrorJSON($error)
    {
        $result['error'] = 1;
        $result['info'] = $error;

        $result['data'] = [];
        $result = json_encode($result);
        return $result;
    }


    private function checkLogin($autoRedirect = true)
    {
        $username = $this->request->cookie($this->cookie_user_key);
        $token = $this->request->cookie($this->cookie_token_key);

        $users = Config::get('deploy_config')['users'];

        $is_login = false;
        if ($username != '' && isset($users[$username])) {
            if ($token == $users[$username]) {
                $is_login = true;
            }
        }

        if (!$is_login) {
            if ($autoRedirect) {
                $this->response->redirect('/deployer');
            }
        }

        return $is_login;
    }
}