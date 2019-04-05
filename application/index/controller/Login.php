<?php

namespace app\index\controller;

use app\common\model\UserOnline;
use think\Controller;

class Login extends Controller{
    public function index() {

        if (!request()->isPost()) {
            return json(["status" => 0, "msgname" => "invalid_request_way"]);
        }

        $data = input('post.');
        $username = $data['username'];
        $password = $data['password'];
        $timestamp = $data['timestamp'];

        $validate = validate('User');
        if ( !$validate->check($data)) {
            return json(["status" => 0, "msgname" =>  $validate->getError()]);
        }
        
        //是否已经过期
        if ($timestamp<strtotime("now - 1 minute")) { //密文中包含的时间要在前一分钟内
            return json(["status" => 0, "msgname" => "login_expired"]); //登录信息已过期
        }

        //用户名是否存在
        try {
            $user = model("User")->get(["username"=>$username]);

            if ($user->status != 1) {
                $user = null;
            }
        } catch(\Exception $e) {
            return json(["status" => 0, "msgname" => "unknown_error"]);
        }
        if (!$user) {
            return json(["status"=> 0,"msgname"=> "username_not_exist"]);
        }

        try {
        // 密码是否正确
        if ($password == $user->password) {
            if (sha1($username . $password . $timestamp) == $data['token']) {
                $token = md5(uniqid(microtime(true),true));

                $userOnline = [];
                $userOnline['user_id'] = $user->id;
                $userOnline['token'] = $token;
                $userOnline['timeout'] = strtotime("+5 week");
                $userOnline['update_time'] = strtotime('now');
                try {

                    $newUserOnline = new UserOnline($userOnline); 
                    $newUserOnline->save();
                    $sessionid = $newUserOnline->id;

                    $user_meta = model('UserMeta')->get(['user_id' => $user->id]);
                    return json(["status" => 1, "msgname" => "login_success", "sessionid" => $sessionid, "token" => $token, "timeout" => $userOnline['timeout'], 'start_day' => $user_meta->start_day]);
                } catch(\Exception $e) {
                    return json(["status" => 0, "msgname" => "unknown_error".$e->getMessage()]);
                }

                } else {
                return json(["status" => 0, "msgname" => "illegal_request"]);
            }

        } else {
            return json(["status" => 0, "msgname" => "password_wrong"]);
        }
        } catch(\Exception $e) {
            return json(["status" => 0, "msgname" => "illegal_request"]);
        }
    }

    public function create()
    {
      if(!request()->isPost()){
        return json(["flag"=>"failure","description"=>"请求方法错误"]);;
      }
      $username=substr($regisI[0],9);
      $password=substr($regisI[1],9);
      $password=hash("sha256",$password);
      $nickname=substr($regisI[2],9);

      $users=Account::where("username",'=',$username)->select();
      if(count($users)){
        return json(["flag"=>"failure","description"=>"用户名已存在"]);
      }

      $account = new Account;
      $account->username=$username;
      $account->password=$password;
      $account->create_time=strtotime("now");
      $account->save();

      return json(["flag"=>"success"]);

    }


}
