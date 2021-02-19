<?php  
namespace Concrete\Package\TreasureHunt\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use RedirectResponse;

class TreasureHunt extends DashboardPageController
{
    public function view()
    {
        $response = new RedirectResponse($this->action('settings'));
        $response->send();
    }
}
