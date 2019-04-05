<?php
namespace app\index\controller;

use think\Controller;

class Base extends Controller {
    public $base_userOnline = 0;
    public $userid = 0;
    public $user = 0;
    public $user_meta = 0;

    public function initialize() {
        $data = [];
        try {
            if (input('?get.sessionid')) {
                $data['sessionid'] = input('get.sessionid');
                $data['version']   = input('get.version');
            } else if (input('?post.sessionid')) {
                $data['sessionid'] = input('post.sessionid');
                $data['version']   = input('post.version');
            } else {
                return json(['status' => 0, 'msgname' => 'unknown_error']);
            }

            if(input('?get.token')) {
                $data['token'] = input('get.token');
                $data['version'] = input('get.version');
            } else if (input('?post.token')) {
                $data['token'] = input('post.token');
                $data['version'] = input('post.version');
            } else {
                return json(['status' =>0, 'msgname' => 'unknown_error']);
            }
        } catch (\Exception $e) {
            return json(['status' => 0, 'msgname' => 'unknown_error']);
        }

        try {
            if (!empty($data['version'])) {
                $version = $data['version'];
                $version = (double) $version;
                if (!($version >= config('app.least_version'))) {
                    json(['status' => 0, 'msgname' => 'need_update'])->send();
                    exit;
                }
            }
            
            $this->base_userOnline = model('UserOnline')->get($data['sessionid']);

            $time = 1;
	        $now_time = strtotime('now');
            if ($now_time - $this->base_userOnline->update_time < $time) {
                json(['status' => 0 , 'msgname' => 'too_quick'])->send();
                exit();
            }
            $this->base_userOnline->update_time = $now_time;
            $this->base_userOnline->save();

            $timeout = $this->base_userOnline->timeout;

            if ($now_time > $timeout) {
                json(['status' => 0, 'msgname' => "login_expired"])->send();
                exit();
            }

            if (sha1($this->base_userOnline->token) != $data['token']) {
                json(['status' => 0, 'msgname' => "login_invalid"])->send();
                exit();
            }

            $this->userid = $this->base_userOnline->user_id;

        } catch (\Exception $e) {
            return json(['status' => 0, 'msgname' => 'unknown_error']);   
        }
    }

    public function tooQuick($time = 5) {
	    $now_time = strtotime('now');
        if ($now_time - $this->base_userOnline->update_time < $time) {
            return ['status' => 0 , 'msgname' => 'too_quick'];
        }
        $this->base_userOnline->update_time = $now_time;
        $this->base_userOnline->save();
    
        return ['status' => 1];
    }
}
