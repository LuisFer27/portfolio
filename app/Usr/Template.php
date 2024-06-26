<?php

class UsrTemplate
{
    public static $ver = 'v1';

    public static $breadcrumb = array();

    public static $title = '';

    public static $subtitle = '';

    static public function init($title)
    {
        AifView::setPath('/assets');
        $favicon = '/img/favicon';
        AifHtml::layoutHeader('HTML5', $title, $favicon . '.png');
        $favicon = AifView::getPath() . $favicon;
        AifHtml::head('meta', array('name' => 'msapplication-TileColor', 'content' => '#162946'));
        AifHtml::head('meta', array('name' => 'theme-color', 'content' => '#e72a1a'));
        AifHtml::head('meta', array('http-equiv' => 'x-ua-compatible', 'content' => 'IE=edge'));
        AifHtml::head('meta', array('name' => 'twitter:image', 'content' => $favicon . '-150x150.png'));
        AifHtml::head('meta', array('name' => 'msapplication-TileImage', 'content' => $favicon . '-300x300.png'));
        AifHtml::head('meta', array('data-react-helmet' => "true", 'property' => 'og:image', 'content' => $favicon . '-150x150.png'));
        AifHtml::head('meta', array('data-react-helmet' => "true", 'property' => 'og:title', 'content' => $title));
        AifHtml::head('meta', array('data-react-helmet' => "true", 'property' => 'og:description', 'content' => $title));
        AifHtml::head('link', array('rel' => "icon", 'type' => "image/png", 'sizes' => "32x32", 'href' => $favicon . '-150x150.png'));
        AifHtml::head('link', array('rel' => "icon", 'type' => "image/png", 'sizes' => "192x192", 'href' => $favicon . '-300x300.png'));
        AifHtml::head('link', array('rel' => "apple-touch-icon", 'href' => $favicon . '-300x300.png'));
        AifHtml::setLineBreak("\r\n");
        AifHtml::setHttpCss(
            'https://fonts.gstatic.com',
            'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap',
            //'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css',
        );
        UtilHtml::css(
            '/css/font-awesome-5.10.0.min.css',
            '/lib/owlcarousel/assets/owl.carousel.min.css',
            '/lib/lightbox/css/lightbox.min.css',
            '/css/style.css'
        );

        UtilHtml::script(
            //plugins
            '/js/jquery-3.4.1.min.js',
            '/js/bootstrap.bundle.min.js',
            '/lib/typed/typed.min.js',
            '/lib/easing/easing.min.js',
            '/lib/waypoints/waypoints.min.js',
            '/lib/owlcarousel/owl.carousel.min.js',
            '/lib/isotope/isotope.pkgd.min.js',
            '/lib/lightbox/js/lightbox.min.js',
            '/mail/jqBootstrapValidation.min.js',
            '/mail/contact.js',
            //principal
            '/js/main.js',

        );
        AifHtml::setSource('window.aif_assets="' . AifView::getPath() . '";');
        AifView::setLayout('/usr/html/layout.phtml');
        return get_defined_vars();
    }
    // obtener bredadcrumb
    public static function getBreadcrumb()
    {
        return UtilBootstrap4T4::getBreadcrumb(self::$breadcrumb);
    }
}
