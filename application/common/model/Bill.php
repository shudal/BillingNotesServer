<?php

namespace app\common\model;

use think\Model;

class Bill extends Model {
    public function getBills($userid, $data =[]) {
        try {
            $whereData = [];

            $whereData['user_id'] = $userid;

            $now_time = date('Y') . '-' . date('n') . '-' . date('j');
            $tommorow = date('Y') . '-' . date('n') . '-' . (date('j') + 1);

            $start_time = strtotime($now_time);
            $end_time   = strtotime($tommorow);

            //按time查询
            if(!empty($data['start_time'])) {
                $start_time = strtotime($data['start_time']);
            } 


            if(!empty($data['end_time'])) {
                $end_time = strtotime($data['end_time']);
            }

            $results = model('Bill')->where('user_id', '=', $userid)->where('time', '>=', $start_time)->where('time', '<=', $end_time)->select();

            if (!$results) {
                return null;
            }

            $my_get = 0;
            $my_out = 0;
            
            foreach ($results as $r) {
                
                if ($r->content == null) {
                    $r->content = "";
                }

                if ($r->io == "get") {
                    $my_get = $my_get + $r->sum;
                } else {
                    $my_out = $my_out + $r->sum;
                }
            }

            return (['status' => 1, 'my_get' => $my_get, 'my_out' => $my_out, 'sum' => $my_get - $my_out, 'results' => $results]);

        } catch (\Exception $e) {
            return (['status' => 0, 'msg' => $e->getMessage()]);
        }
    } 
}

