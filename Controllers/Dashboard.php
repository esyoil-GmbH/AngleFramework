<?php
/**
 * Created by PhpStorm.
 * User: bennet
 * Date: 20.04.18
 * Time: 16:04
 */

namespace Angle\Examples\Controllers;


use Angle\Engine\Template\Engine;

class Dashboard {

    public static function display(Engine $engine)  {
        $a = $engine->render("templates/test.html");
    }

}