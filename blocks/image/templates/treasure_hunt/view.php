<?php  
defined('C5_EXECUTE') or die("Access Denied.");

$c = Page::getCurrentPage();
if (is_object($f)) {

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
        $im = $app->make('helper/image');
        $thumb = $im->getThumbnail(
            $f,
            $maxWidth,
            $maxHeight
        ); //<-- set these 2 numbers to max width and height of thumbnails
        $tag = new \HtmlObject\Image();
        $tag->src($thumb->src)->width($thumb->width)->height($thumb->height);
    } else {
        $image = $app->make('html/image', array($f));
        $tag = $image->getTag();
    }
    $tag->addClass('treasure-hunt ccm-image-block img-responsive bID-'.$bID);
    if ($altText) {
        $tag->alt(h($altText));
    } else {
        $tag->alt('');
    }
    if ($title) {
        $tag->title(h($title));
    }
    if ($linkURL):
        print '<a href="' . $linkURL . '">';
    endif;

    print $tag;

    if ($linkURL):
        print '</a>';
    endif;
} else if ($c->isEditMode()) { ?>

    <div class="ccm-edit-mode-disabled-item"><?php   echo t('Empty Image Block.')?></div>

<?php   } ?>

<?php   if(isset($foS) && is_object($foS)) { ?>
<script>
$(function() {
    $('.bID-<?php   print $bID;?>')
        .mouseover(function(e){$(this).attr("src", '<?php   print $imgPath["hover"];?>');})
        .mouseout(function(e){$(this).attr("src", '<?php   print $imgPath["default"];?>');});
});
</script>
<?php   } ?>
