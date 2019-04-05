<?php
namespace app\common\model;

use think\Model;

class UserMeta extends Model {
    public function changeSd($Sd, $userid) {
        if ( 1 <= $Sd && $Sd <= 31) {
            try {
                if( date('n') != 1) {
                    $year = date('Y');
                    if ($Sd <= date('j')) {
                        $preM = date('n');
                    } else {
                        $preM = date('n')  - 1;
                    }
                } else {
                    if ($Sd <= date('n')) {
                        $year = date('Y');
                        $preM = date('n');
                    } else {
                        $year = date('Y') - 1;
                        $preM = 12;
                    }
                }
                $start_time = strtotime($year . '-' . $preM . '-' . $Sd);
                $now_time = strtotime(date('Y') . '-' . date('n') . '-' . date('j'));

                $whereData['time'] = [
                    ['egt', $start_time],
                    ['elt', $now_time],
                ];

                $bills = model('Bill')->where('user_id', '=', $userid)->where('time', 'egt', $start_time)->where('time', 'elt', $now_time)->select();
                $sum  = 0;

                foreach ($bills as $r) {
                    if ($r->io == 'out') {
                        $sum = $sum + $r->sum;
                    } else {
                        $sum = $sum - $r->sum;
                    }
                }
            
                $user_meta = model('UserMeta')->get(['user_id' => $userid]);

                $user_meta->month_used = $sum;
                $user_meta->start_day  = $Sd;

                $user_meta->save();

                return ['status' => 1];
            } catch (\Exception $e) {
                return ['status' => '0', 'msg' => $e->getMessage()] ;
            }
        }
    }
}

