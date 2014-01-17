<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractRestfulController
{
    // pretend that this is a model!
    protected $albums = array(
        1 => array('artist' => 'Bruce Springsteen', 'title'=>'High Hopes'),
        2 => array('artist' => 'Various', 'title'=>'The Trevor Nelson Collection 2'),
        3 => array('artist' => 'John Newman', 'title'=>'Tribute'),
        4 => array('artist' => 'London Grammar', 'title'=>'If You Wait'),
        5 => array('artist' => 'Various', 'title'=>'Frozen'),
        6 => array('artist' => 'Avicii', 'title'=>'True'),
    );

    public function getList()
    {
        return new JsonModel($this->albums);
    }

    public function get($id)
    {
        if (isset($this->albums[$id])) {
            return new JsonModel($this->albums[$id]);
        }

        $this->response->setStatusCode(404);
        return new JsonModel(array("message" => "Could not find album"));
    }
}
