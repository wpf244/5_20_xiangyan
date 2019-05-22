<?php
namespace app\index\controller;


class Index extends BaseHome
{
    public function index()
    {
        return $this->fetch();
    }
}
