<?php   
namespace Concrete\Package\TreasureHunt;

use BlockType;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Package\Package;
use Concrete\Core\View\View;
use Core;
use Config;
use Events;
use Page;
use Redirect;
use Route;
use SinglePage;
use Symfony\Component\ClassLoader\Psr4ClassLoader;
use User;

class Controller extends Package
{
    protected $pkgHandle = 'treasure_hunt';
    protected $appVersionRequired = '5.7.5.4';
    protected $pkgVersion = '1.0';

    protected $single_pages = array(
        '/dashboard/treasure_hunt' => array(
            'cName' => 'Treasure Hunt',
        ),
        '/dashboard/treasure_hunt/settings' => array(
            'cName' => 'Settings',
        ),
    );

    public function getPackageName()
    {
        return t('Treasure Hunt');
    }

    public function getPackageDescription()
    {
        return t('Treasure Hunt');
    }

    public function on_start()
    {
        $this->registerNamespace();
        $this->registerRoutes();
        $this->secureFinishPage();
        Events::addListener('on_before_render', array($this, 'registerAndRequireAssets'));
    }

    public function install()
    {
        $pkg = parent::install();

        $this->installEverything($pkg);
    }

    public function upgrade()
    {
        $pkg = parent::getByHandle($this->pkgHandle);

        $this->installEverything($pkg);
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     * @return bool
     */
    public function registerAndRequireAssets($event)
    {
        if (!is_object($event['view']->controller)) {
            return false;
        }

        $c = $event['view']->controller->c;


        if (!is_object($c) OR $c->isError() OR $c->isAdminArea()) {
            return false;
        }

        $al = AssetList::getInstance();
        $al->register('javascript', 'treasure-hunt', 'js/treasure-hunt.js', array(), 'treasure_hunt');
        $al->register('javascript', 'jpopup', 'js/jquery.jpopup.min.js', array(), 'treasure_hunt');
        $al->register('css', 'treasure-hunt', 'css/treasure-hunt.css', array(), 'treasure_hunt');
        $al->register('css', 'jpopup', 'css/jpopup.min.css', array(), 'treasure_hunt');
        $al->register('javascript-inline', 'random-popup-message', $this->getInlineJavaScript());

        $view = View::getInstance();
        $view->requireAsset('javascript', 'treasure-hunt');
        $view->requireAsset('javascript', 'jpopup');
        $view->requireAsset('css', 'treasure-hunt');
        $view->requireAsset('css', 'jpopup');
        $view->requireAsset('javascript-inline', 'random-popup-message');
    }

    /**
     * @return string
     */
    protected function getInlineJavaScript()
    {
        $controller = Core::make('TreasureHunt\ThController');

        return '
var TREASURE_HUNT_RANDOM_POPUP_MESSAGE = '.json_encode($controller->getRandomPopupMessage()).';
var TREASURE_HUNT_POPUP_TITLE = '.json_encode($controller->getPopupTitle()).';
var TREASURE_HUNT_OK_BUTTON_CAPTION = '.json_encode($controller->getPopupButtonCaption()).';
';
    }

    protected function registerRoutes()
    {
        Route::register('/treasure_hunt/gotcha', 'TreasureHunt\Ajax::foundItem');
    }

    protected function secureFinishPage()
    {
        Events::addListener('on_page_view', function ($pa) {
            $page = false;
            $cid = Core::make('config')->get('treasure_hunt.settings.completed_cid');
            if ($cid) {
                $page = Page::getByID($cid);
            }

            if (!$page || $page->isError() || $pa->getPageObject()->getCollectionID() != $cid) {
                return false;
            }

            // Don't check permissions if logged in
            $u = new User();
            if ($u->isRegistered()) {
                return false;
            }

            // We're trying to view the landingspage. Check if user has gathered all items.
            $controller = Core::make('TreasureHunt\ThController');
            if ($controller->isQuestCompleted()) {
                return false;
            }

            // User has not gathered enough items, redirect to home page...
            // Don't redirect if home page has been selected as landingpage to prevent endless loop...
            if ($cid !== HOME_CID) {
                Redirect::page(Page::getByID(HOME_CID), 301)->send();
            }
        });
    }

    protected function installEverything($pkg)
    {
        $this->installPages($pkg);
        $this->installBlockTypes($pkg);

        if (!Config::get('treasure_hunt.settings.cookie_name')) {
            Config::save('treasure_hunt.settings.cookie_name', 'treasure-hunt');
        }
    }

    /**
     * @param Package $pkg
     */
    protected function installPages($pkg)
    {
        foreach ($this->single_pages as $path => $value) {
            if (!is_array($value)) {
                $path = $value;
                $value = array();
            }
            $page = Page::getByPath($path);
            if (!$page || $page->isError()) {
                $single_page = SinglePage::add($path, $pkg);

                if ($value) {
                    $single_page->update($value);
                }
            }
        }
    }

    /**
     * @param Package $pkg
     */
    protected function installBlockTypes($pkg)
    {
        $bts = array(
            'image_th',
        );

        foreach ($bts as $btHandle) {
            if (!BlockType::getByHandle($btHandle)) {
                BlockType::installBlockType($btHandle, $pkg);
            }
        }
    }

    protected function registerNamespace()
    {
        $psr4_loader = new Psr4ClassLoader();
        $psr4_loader->addPrefix('\\TreasureHunt', __DIR__ . '/src/TreasureHunt');
        $psr4_loader->register();
    }
}
