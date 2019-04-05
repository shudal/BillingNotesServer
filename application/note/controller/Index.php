<?php

namespace app\note\controller;

use think\Controller;
use think\Request;

use app\index\controller\Base;

class index extends Base{
    public function add() {
        if (request()->isPost()) {
            try {
                $note  = [];

                $note['content'] = input('post.content');
                $note['tag'] = input('post.tag');

                $note['user_id'] = $this->base_userOnline->user_id;
                $note['create_time'] = strtotime('now');

                model('Note')->save($note);

                return json(['status' => 1]);

            } catch (\Exception $e) {
                return json(['status' => 0, 'msgname' => 'unknown_error']);            
            }
     } else {
        return json(['status' => 0, 'msgname' => 'invalid_request_way']);
     }
    }

    public function all() {
        if (request()->isGet()) {
            try {
            $data = input('get.');

            $notes = model('Note')->where('user_id', '=', $this->base_userOnline->user_id);

            // 按照tag搜索
            if (!empty($data['tag'])) {
                $notes = $notes->where('tag', 'like', "%".$data['tag']."%");
            }

            $notes = $notes->select();

            foreach ($notes as $note) {
                if ($note->tag != "") {
                    // 去掉开头空格
                    $note->tag = substr($note->tag, 1);

                    $note->tag = explode(' ', $note->tag);
                }
            }

            return json(['status' => 1, 'data' => $notes]);

            } catch (\Exception $e) {
                return json(['status' => 0, 'msgname' => $e->getMessage()]);
            }
        } else {
            return json(['status' =>0 ,'msgname' => 'invalid_request_way']);
        }
    }

}
