<?php

class UsrDash
{
    public function index()
    {
        UtilHtml::set(
            '/usr/html/site/index.phtml'
        );
        return get_defined_vars();
    }
}
