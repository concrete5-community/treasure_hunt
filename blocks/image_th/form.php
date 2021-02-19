<?php  
defined('C5_EXECUTE') or die("Access Denied.");

$ps = Core::make('helper/form/page_selector');
$al = Core::make('helper/concrete/asset_library');
?>

<fieldset>
    <legend><?php   echo t('Files') ?></legend>

    <div class="form-group">
        <?php  
        echo $form->label('ccm-b-image', t('Image'));
        echo $al->image('ccm-b-image', 'fID', t('Choose Image'), $bf);
        ?>
    </div>

    <div class="form-group">
        <label class="control-label"><?php   echo t('Image Hover')?> <small style="color: #999999; font-weight: 200;"><?php   echo t('(Optional)'); ?></small></label>
        <?php  
        echo $al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo);
        ?>
    </div>
</fieldset>

<fieldset>
    <legend><?php   echo t('HTML') ?></legend>

    <div class="form-group">
        <?php  
        echo $form->label('altText', t('Alt. Text'));
        echo $form->text('altText', $altText, array('maxlength' => 255));
        ?>
    </div>

    <div class="form-group">
        <?php  
        echo $form->label('title', t('Title'));
        echo $form->text('title', $title, array('maxlength' => 255));
        ?>
    </div>

    <div class="form-group">
        <?php  
        echo $form->label('popupMessage', t('Custom Popup Message'));
        echo $form->text('popupMessage', $popupMessage, array('maxlength' => 255));
        ?>
    </div>
</fieldset>

<fieldset>
    <legend><?php   echo t('Resize Image') ?></legend>

    <div class="form-group">
        <div class="checkbox" data-checkbox-wrapper="constrain-image">
            <label>
                <?php  
                echo $form->checkbox('constrainImage', 1, $constrainImage);
                echo t('Constrain Image Size');
                ?>
            </label>
        </div>
    </div>

    <div data-fields="constrain-image" style="display: none">
        <div class="form-group">
            <?php  
            echo $form->label('maxWidth', t('Max Width'));
            echo $form->number('maxWidth', $maxWidth, array('min' => 0));
            ?>
        </div>

        <div class="form-group">
            <?php  
            echo $form->label('maxHeight', t('Max Height'));
            echo $form->number('maxHeight', $maxHeight, array('min' => 0));
            ?>
        </div>
    </div>
</fieldset>

<script type="text/javascript">
$(document).ready(function() {
    $('#constrainImage').on('change', function() {
        $('div[data-fields=constrain-image]').toggle($(this).is(':checked'));
    }).trigger('change');
});
</script>
