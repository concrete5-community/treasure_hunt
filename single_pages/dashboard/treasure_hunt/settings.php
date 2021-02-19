<?php    
defined('C5_EXECUTE') or die('Access Denied.');

$token = Core::make('token');
$ih = Core::make('multilingual/interface/flag');
$ps = Core::make('helper/form/page_selector');
?>

<style>
.r-popup-message {
    margin: 5px 0;
}
</style>

<form method="post" action="<?php  echo $controller->action('save') ?>">
	<?php  $token->output('treasure_hunt.settings.save'); ?>

    <div class="form-group">
        <?php 
        echo $form->label('cookie_name', t('Cookie name'));
        echo $form->text('cookie_name', Config::get('treasure_hunt.settings.cookie_name'), array('required' => 1));
        ?>
    </div>

    <div class="form-group">
        <?php  
        echo $form->label('min_items', t('Minimum items required'));
        echo $form->number('min_items', Config::get('treasure_hunt.settings.min_items'), array('min' => 1, 'placeholder' => 5));
        ?>
    </div>

    <div class="form-group">
        <?php  
        echo $form->label('message_complete', t('Show popup message when minimum number of items have been found'));
        echo $form->textarea('message_complete', Config::get('treasure_hunt.settings.message_complete'));
        ?>
    </div>

    <div class="form-group">
        <?php  
        echo $form->label('completed_cid', t("Redirect user to page when minimum number of items have been found"));
        echo $ps->selectPage('completed_cid', Config::get('treasure_hunt.settings.completed_cid'));
        ?>
    </div>

    <div class="form-group">
        <?php  
        echo $form->label('popup_title', t('Popup Title (placeholders: %FOUND%, %REMAINING%)'));
        echo $form->text('popup_title', Config::get('treasure_hunt.settings.popup_title'));
        ?>
    </div>

    <div class="form-group">
        <?php  
        echo $form->label('popup_button_caption', t('Popup Button Caption'));
        echo $form->text('popup_button_caption', Config::get('treasure_hunt.settings.popup_button_caption'));
        ?>
    </div>

    <hr />

    <div class="form-group">
        <?php  
        echo $form->label('message', t('Popup message when an item is found'));
        ?>

        <div class="row">
            <div class="col-md-10">
                <?php  
                echo $form->text('message');
                ?>
            </div>
            <div class="col-md-2">
                <a href="<?php   echo $this->action('add_popup_message') ?>" class="btn btn-primary btn-add-popup-message"><?php   echo t("Add") ?></a>
            </div>
        </div>

        <div style="margin-top: 10px;" class="popup-messages">
            <?php  
            $popupMessages = Config::get('treasure_hunt.settings.popup_messages');
            if ($popupMessages && count($popupMessages) > 0) {
                ?><strong><?php   echo t("Current popup messages:"); ?></strong><?php  

                foreach ($popupMessages as $msg) {
                    ?>
                    <div class="row r-popup-message">
                        <div class="col-msg col-md-10"><?php   echo h($msg); ?></div>
                        <div class="col-md-2"><a href="<?php   echo $this->action('delete_popup_message') ?>" class="btn btn-danger btn-sm btn-delete-popup-message"><?php   echo t("Delete") ?></a></div>
                    </div>
                    <?php  
                }
            } else {
                echo t("No popup messages have been added yet.");
            }
            ?>
        </div>
    </div>

    <hr />

	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<button class="pull-right btn btn-primary" type="submit"><?php   echo t('Save') ?></button>
		</div>
	</div>
</form>
