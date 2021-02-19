<?php  
namespace TreasureHunt;

use Controller;
use Page;

class ThController extends Controller
{
    /**
     * @return int
     */
    public function getNumberOfRemainingItems()
    {
        $min_required = $this->app['config']->get('treasure_hunt.settings.min_items');
        $min_required = ($min_required) ? $min_required : 5;

        return abs($min_required - $this->getNumberOfFoundItems());
    }

    /**
     * @return bool
     */
    public function isQuestCompleted()
    {
        return $this->getNumberOfRemainingItems() === 0;
    }

    /**
     * @return int
     */
    public function getNumberOfFoundItems()
    {
        return count($this->getItemsFound());
    }

    /**
     * @param int $cid
     */
    public function markItemAsFound($cid)
    {
        $session = $this->app->make('session');

        $found = $this->getItemsFound();
        if (!in_array($cid, $found)) {
            $found[] = $cid;
        }

        $session->set('treasure_hunt.found', $found);
    }

    /**
     * @param int $cid
     * @return bool
     */
    public function isItemOnPageFound($cid)
    {
        $found = $this->getItemsFound();

        return in_array($cid, $found);
    }

    /**
     * @return array
     */
    public function getItemsFound()
    {
        $session = $this->app->make('session');
        $found = $session->get('treasure_hunt.found');

        return is_array($found) ? $found : array();
    }

    /**
     * Set a cookie when the quest is completed.
     */
    public function markQuestAsCompleted()
    {
        $cookie = $this->app['cookie'];
        $config = $this->app['config'];

        $expire = time() + (3600 * 24 * 365);
        $cookie->set(
            'treasure-hunt',
            1,
            $expire,
            DIR_REL . '/',
            $config->get('concrete.session.cookie.cookie_domain'),
            $config->get('concrete.session.cookie.cookie_secure'),
            $config->get('concrete.session.cookie.cookie_httponly')
        );
    }

    /**
     * @return string
     */
    public function getCompletePopupMessage()
    {
        $msg = $this->app['config']->get('treasure_hunt.settings.message_complete');

        if (!$this->getRedirectURL() && empty($msg)) {
            $msg = t("Congratulations! You've found all the items!");
        }

        return $msg;
    }

    /**
     * @return bool|\Concrete\Core\Page\Page
     */
    public function getRedirectURL()
    {
        $url = false;
        $cid = $this->app['config']->get('treasure_hunt.settings.completed_cid');
        if ($cid) {
            $page = Page::getByID($cid);
            $url = (!$page || $page->isError()) ? false : $page->getCollectionLink();
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getPopupTitle()
    {
        $popup_title = trim($this->app['config']->get('treasure_hunt.settings.popup_title'));

        if (!empty($popup_title)) {
            $popup_title = str_replace('%FOUND%', $this->getNumberOfFoundItems(), $popup_title);
            $popup_title = str_replace('%REMAINING%', $this->getNumberOfRemainingItems(), $popup_title);
        } else {
            $popup_title = t('Treasure Hunt. Remaining: %s.', $this->getNumberOfRemainingItems());
        }

        return $popup_title;
    }

    /**
     * @return string
     */
    public function getRandomPopupMessage()
    {
        $popup_messages = $this->app['config']->get('treasure_hunt.settings.popup_messages');
        if (is_array($popup_messages) && count($popup_messages) > 0) {
            $popup_message = $popup_messages[array_rand($popup_messages)];
        } else {
            $popup_message = t("You found one of the items, well done!");
        }

        return $popup_message;
    }

    public function getPopupButtonCaption()
    {
        $popup_button_caption = $this->app['config']->get('treasure_hunt.settings.popup_button_caption');

        return $popup_button_caption ? $popup_button_caption : tc('Treasure Hunt Popup Button Caption', 'Ok!');
    }
}
