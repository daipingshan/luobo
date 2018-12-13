<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/7/4
 * Time: 17:28
 */

namespace Api\Controller;

/**
 * Class IndexController
 *
 * @package Api\Controller
 */
class IndexController extends CommonController {

    /**
     * app首页
     */
    public function index() {
        $this->output('ok', 'success');
    }

}