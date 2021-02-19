<?php   
namespace Concrete\Package\TreasureHunt\Controller\SinglePage\Dashboard\TreasureHunt;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Flysystem\Exception;
use TreasureHunt\JsonResponse;

class Settings extends DashboardPageController
{
    public function on_start()
    {
        parent::on_start();

        $al = AssetList::getInstance();

        // Backend JS file(s)
        $al->register('javascript', 'treasure-hunt-be', 'js/treasure-hunt-be.js', array(), 'treasure_hunt');
        $this->requireAsset('javascript', 'treasure-hunt-be');
    }

    public function save()
    {
        try {
            if (!$this->app['token']->validate('treasure_hunt.settings.save')) {
                throw new Exception($this->app['token']->getErrorMessage());
            }

            if (!$this->post('min_items')) {
                throw new Exception(t("Minimum items required missing"));
            }

            $this->app['config']->save('treasure_hunt.settings.message_complete', $this->post('message_complete'));
            $this->app['config']->save('treasure_hunt.settings.min_items', $this->post('min_items'));
            $this->app['config']->save('treasure_hunt.settings.completed_cid', $this->post('completed_cid'));
            $this->app['config']->save('treasure_hunt.settings.popup_title', $this->post('popup_title'));
            $this->app['config']->save('treasure_hunt.settings.popup_button_caption', $this->post('popup_button_caption'));

            $this->redirect($this->action('save_success'));
        } catch (Exception $e) {
            $this->error->add($e->getMessage());
        }
    }

    public function save_success()
    {
        $this->set('message', t('Settings saved'));
    }

    /**
     * @ajax
     *
     * Add a popup message.
     * message string, e.g. "Well done!"
     */
    public function add_popup_message()
    {
        $message = $this->post('message');

        $popup_messages = $this->getPopupMessagesFromConfig();
        $popup_messages[] = $message;

        $this->savePopupMessagesInConfig($popup_messages);

        $resp = new JsonResponse(array('success' => true));
        $resp->send();

        $this->app->shutdown();
    }

    /**
     * @ajax
     *
     * Deletes a popup message if both locale and message match in a record.
     */
    public function delete_popup_message()
    {
        $message = $this->post('message');

        $popup_messages = array_filter($this->getPopupMessagesFromConfig(), function ($value) use ($message) {
            return $value != $message;
        });

        $this->savePopupMessagesInConfig($popup_messages);

        $resp = new JsonResponse(array('success' => true));
        $resp->send();

        $this->app->shutdown();
    }

    /**
     * @return array
     */
    protected function getPopupMessagesFromConfig()
    {
        $popup_messages = $this->app['config']->get('treasure_hunt.settings.popup_messages');

        return !is_array($popup_messages) ? array() : $popup_messages;
    }

    /**
     * @param array $popup_messages
     */
    protected function savePopupMessagesInConfig($popup_messages)
    {
        $this->app['config']->save('treasure_hunt.settings.popup_messages', $popup_messages);
    }
}
