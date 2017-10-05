<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CheckController extends Controller implements JsonApiController
{

    public function pingAction(Request $request)
    {

        return ["ping" => "pong"];

    }

}
