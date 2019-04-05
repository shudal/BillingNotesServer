<?php
namespace app\index\controller;

class FeedBack extends Base {

    public function add() {
        if (request()->isPost()) {
            try {
                $newFb = [];

                $newFb['user_id'] = $this->base_userOnline->user_id;
                $newFb['content'] = input('post.feedback');
                $newFb['create_time'] = strtotime('now');

                if (trim($newFb['content']) == "" ) {
                    return ['status' => 0, 'msgname' => 'unknown_error'];
                }

                model('FeedBack')->save($newFb);
                
                return json(['status' => 1]);
            } catch (\Exception $e) {
                return json(['status' =>0, 'msgname' => 'unknown_error']);
            }        
        }
    }
}
