<?php
namespace app\index\controller;

use think\Controller;
class Index extends Base {
    public function add() {
        if (!request()->isPost()) {
            return json(['status' => 0, "msgname" => "invalid_request_way"]);
        }

        $data = input('post.');

        $newDeal = [];

        if (!empty($data['year'])) {
            if ( 2018 <= $data['year'] && $data['year'] <= date('Y')) {
                $newDeal['year'] = $data['year'];
            } else {
                return json(['status' => 0, 'msgname' => "year_wrong"]);
            }
        } else {
            $newDeal['year'] = date('Y');
        }

        if (!empty($data['month'])) {
            if ( 1 <= $data['month'] && $data['month'] <= 12) {
                $newDeal['month'] = $data['month'];
            } else {
                return json(['status' => 0, 'msgname' => "month_wrong"]);
            }
        } else {
            $newDeal['month'] = date('n');
        }

        if (!empty($data['day'])) {
            if ( 1 <= $data['day'] && $data['day'] <= 31) {
                $newDeal['day'] = $data['day'];
            } else {
                return json(['status' => 0, 'msgname' => "day_wrong"]);
            }
        } else {
            $newDeal['day'] = date('j');
        }


        if (!empty($data['io'])) {
            $newDeal['io'] = $data['io'];
        } else {
            return json(['status' => 0, 'msgname' => "unknown_error"]);
        }

        if (!empty($data['form'])) {
            if ($data['form'] == "other" && $data['other'] != "") {
                $newDeal['form'] = $data['other'];
            } else {
                $newDeal['form'] = $data['form'];
            }
        } else {
            return json(['status' => 0, 'msgname' => "unknown_error"]);
        }

        if (!empty($data['main'])) {
            $newDeal['main'] = $data['main'];
        }

        if (!empty($data['content'])) {
            $newDeal['content'] = $data['content'];
        }

        if (!empty($data['sum'])) {
            $newDeal['sum'] = $data['sum'];
        } else {
            return json(['status' => 0, 'msgname' => "amount_require"]);
        }

        $newDeal['user_id'] = $this->base_userOnline->user_id;

        try {
            $newDeal['time'] = strtotime($newDeal['year'] . '-' . $newDeal['month'] . '-' . $newDeal['day']);
            $now_time = strtotime(date('Y') . '-' . date('n') . '-' . date('j'));

            if ($newDeal['time'] > $now_time) {
                return json(['status' => 0, 'msgname' => 'unknown_error']);
            }

            model('Bill')->save($newDeal);
            
            $this->user_meta = model('UserMeta')->get(['user_id' => $newDeal['user_id']]);

            if (date('j') == $this->user_meta->start_day) {
                if (date('n') != $this->user_meta->cleared) {
                    $this->user_meta->month_used = 0;
                    $this->user_meta->cleared = date('n');
                }
            }

            if ($newDeal['io'] == 'out' ) {
                $this->user_meta->month_used = $newDeal['sum'] + $this->user_meta->month_used;
            } else {
                $this->user_meta->month_used = $this->user_meta->month_used - $newDeal['sum'];
            }
            $this->user_meta->save();

            return json(['status' => 1, 'msgname' => "add_success"]);

        } catch (\Exception $e) {
            return json(['status' => 0, 'msgname' => "unknown_error".$e->getMessage()]);
        }
    }

    public function all() {
        if (!request()->isGet()) {
            return json(['status' => 0, 'msgname' => "invalid_request_way"]);
        }

	    $data = input('get.');

        try {

            $results = model('Bill')->getBills($this->base_userOnline->user_id, $data);

            if ($results['status'] == 1) {
                $bills = $results['results'];
            } else {
                return json(['status' => 0, 'msgname' => 'unknown_error']);
            }

            $userMeta = model('UserMeta')->get(['user_id' => $this->base_userOnline->user_id]);
            $month_used = $userMeta->month_used;
            $month_fee  = $userMeta->month_fee;

            $remaining_day = $userMeta->start_day - (int) date('j') - 1 ;
            
            if ($remaining_day <= 0) {
                $now_n = (int) date('n');
                if ( $now_n == 1 || $now_n == 3 || $now_n == 5 || $now_n == 7 || $now_n == 8 || $now_n == 10 || $now_n == 12 ) {
                    $remaining_day += 31;
                } else if ( $now_n == 2) {
                    $now_year = (int) date('Y');
                    if (($now_year % 4 ==0 && $now_year % 100 != 0) || $now_year % 400 == 0) {
                        $remaining_day += 29;
                    } else {
                        $remaining_day += 28;
                    }
                } else {
                    $remaining_day += 30;
                }
            }

            return json(['status' => 1, 'my_out' => $results['my_out'], 'my_get' => $results['my_get'], 'sum' => $results['sum'], 'month_used' => $month_used, 'month_fee' => $month_fee, 'remaining_day' => $remaining_day, 'bills' => $bills]);
        } catch (\Exception $e) {
            return json(['status' => 0, 'msgname' => "unknown_error"]);
        }
    }

    public function logout() {
        if (request()->isPost()) {
            try {
                model('UserOnline')->where('id',$this->base_userOnline->id)->delete();
                return json(['status' => 1]);
            } catch (\Exception $e) {
                return json(['status' => 0, 'msgname' => 'unknown_error']);
            }
        }
    }

    public function userState() {
        return $this->fetch();
    }
}
