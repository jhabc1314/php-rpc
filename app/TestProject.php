<?php
/**
 * 具体的业务处理demo
 * User: jiangheng
 * Date: 19-6-4
 * Time: 上午10:42
 */

namespace App;

class TestProject
{
    public function func1($msg)
    {
        return 'func1:' . $msg;
    }

    public function func2($msg)
    {
        return 'func2:' . $msg;
    }
}