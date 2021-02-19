<?php  
defined('C5_EXECUTE') or die("Access Denied.");

if (is_object($f) && $f->getFileID()) {
    $app = Core::getFacadeApplication();

    $cookie = $app->make('cookie');
    if (!$c->isEditMode() && $cookie->has('treasure-hunt')) {
        // Don't show image anymore. All items have been found by the user.
        return;
    }

    $thController = $app->make('TreasureHunt\ThController');
    if (!$c->isEditMode() && $thController->isItemOnPageFound($c->getCollectionID())) {
        // Item is already found on this page.
        return;
    }

    if ($maxWidth > 0 || $maxHeight > 0) {
        $crop = false;

        $im = $app->make('helper/image');
        $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $crop);

        $tag = new \HtmlObject\Image();
        $tag->src($thumb->src)->width($thumb->width)->height($thumb->height);
    } else {
        $image = $app->make('html/image', array($f));
        $tag = $image->getTag();
    }

    $tag->addClass('treasure-hunt ccm-image-block img-responsive bID-'.$bID);
    $tag->setAttribute('data-popup-message', h($popupMessage));

    if ($altText) {
        $tag->alt(h($altText));
    }

    if ($title) {
        $tag->title(h($title));
    }

    echo $tag;

} elseif ($c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?php   echo t('Empty Image Block.') ?></div>
    <?php  
}

if (is_object($foS)): ?>
<script>
$(function() {
    $('.bID-<?php   echo $bID; ?>')
        .mouseover(function(){$(this).attr("src", '<?php   echo $imgPaths["hover"]; ?>');})
        .mouseout(function(){$(this).attr("src", '<?php   echo $imgPaths["default"]; ?>');});
});
</script>
<?php  
endif;
