<?php  
namespace TreasureHunt;

use Controller;
use Page;

class Ajax extends Controller
{
    /**
     * Outputs a JSON object.
     * Listens to /treasure_hunt/gotcha.
     */
    public function foundItem()
    {
        $cid = $this->post('cID');

        $page = Page::getByID($cid);
        if (!$page || $page->isError()) {
            return;
        }

        $controller = $this->app->make('TreasureHunt\ThController');
        $controller->markItemAsFound($cid);

        $json['redirect_url'] = false;
        $json['popup_title'] = $controller->getPopupTitle();
        $json['popup_message'] = $controller->getRandomPopupMessage();
        $json['remaining_items'] = $controller->getNumberOfRemainingItems();

        if ($json['remaining_items'] === 0) {
            $controller->markQuestAsCompleted();

            $json['popup_message'] = $controller->getCompletePopupMessage();
            $json['redirect_url'] = $controller->getRedirectURL();
        }

        $resp = new JsonResponse($json);
        $resp->send();

        $this->app->shutdown();
    }
}
