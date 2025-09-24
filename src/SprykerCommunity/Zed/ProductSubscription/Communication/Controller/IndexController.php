<?php

namespace SprykerCommunity\Zed\ProductSubscription\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request): array
    {
        return [
            'message' => 'Hello World from SprykerCommunity Dummy Module!',
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }
}
