<?php

class UsrDash
{
    public function index()
    {
        $user = UsrModelUser::getById();
        $about= UsrModelAbout::getById();
        UtilHtml::set(
            
            '/usr/html/site/index.phtml'
        );
        return get_defined_vars();
    }
}
