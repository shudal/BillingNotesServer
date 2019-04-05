<?php
namespace app\index\controller;

class UserMeta extends Base {
    public function index() {
        $this->user_meta = model('UserMeta')->get(['user_id' => $this->base_userOnline->user_id]);
        
        $nickname  = model('User')->get($this->user_meta->user_id)->nickname;
        $month_fee = $this->user_meta->month_fee;
        $start_day = $this->user_meta->start_day;

        return json(['status' => 1, 'nickname' => $nickname, 'start_day' => $start_day, 'month_fee' => $month_fee]);
    }

    public function changeNick() {
        try {
            $user = model('User')->get($this->base_userOnline->user_id);

            $user->nickname = input('post.nickname');
            $user->save();

            return json(['status' => 1]);
        } catch (\Exception $e) {
            return json(['status' => 0, 'msgname' => 'unknown_error']);
        }
    }

    public function changeSd() {
        if (request()->isPost()) {
            try {
                $Sd = input('post.start_day');
    
                $results = model('UserMeta')->changeSd($Sd, $this->base_userOnline->user_id);

                if ($results['status']) {
                    return json(['status' => 1]);
                } else {
                    return json(['status' =>0, 'msgname' => 'unknown_error'.$results['msg']]);
                }
            } catch (\Exception $e) {
                return json(['status' => 0, 'msgname' => 'unknown_error']);
            }
        }
    }

    public function changeMf() {
        if (request() ->isPost()) {
            try {
                $user_meta = model('UserMeta')->get(['user_id' => $this->base_userOnline->user_id]);

                $user_meta->month_fee = input('post.month_fee');
                $user_meta->save();

                return json(['status' => 1]);
            } catch (\Exception $e) {
                return json(['status' => 0, 'msgname' => 'unknown_error']);
            }
        }
    }
}
