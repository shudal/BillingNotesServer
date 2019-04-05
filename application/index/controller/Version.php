<?php
namespace app\index\controller;

use think\Controller;

class Version extends Controller {
    public function isPrompt() {
        $version = input('get.version');
        $version = (double) $version;

        if (!(input('get.version') >= config('app.least_version'))) {
            return json(['status' => 1, 'msgname' => 'need_update']);
        } else {
            return json(['status' => 0]);
        }
        }

    public function prompt() {
        return $this->fetch();
    }

    public function serverNormal() {
        return json(['status' => 1]);
    }
}
