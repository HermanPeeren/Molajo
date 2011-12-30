<?php
/**
 * @version        $Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Site
 * @subpackage    feed
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;
?>

<?php
if ($feed != false) {
    //image handling
    $iUrl = isset($feed->image->url) ? $feed->image->url : null;
    $iTitle = isset($feed->image->title) ? $feed->image->title : null;
    ?>
<div style="direction: <?php echo $rssrtl ? 'rtl' : 'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right'
        : 'left'; ?> ! important" class="feed<?php echo $view_class_suffix; ?>">
    <?php
        // feed description
    if (!is_null($feed->title) && $parameters->get('rsstitle', 1)) {
        ?>

        <h4>
            <a href="<?php echo str_replace('&', '&amp', $feed->link); ?>" target="_blank">
                <?php echo $feed->title; ?></a>
        </h4>

        <?php

    }

    // feed description
    if ($parameters->get('rssdesc', 1)) {
        ?>
        <?php echo $feed->description; ?>

        <?php

    }

    // feed image
    if ($parameters->get('rssimage', 1) && $iUrl) {
        ?>
        <img src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>"/>

        <?php

    }

    $actualItems = count($feed->items);
    $setItems = $parameters->get('rssitems', 5);

    if ($setItems > $actualItems) {
        $totalItems = $actualItems;
    } else {
        $totalItems = $setItems;
    }
    ?>

    <ul class="newsfeed<?php echo $parameters->get('view_class_suffix'); ?>">
    <?php
                $words = $parameters->def('word_count', 0);
        for ($j = 0; $j < $totalItems; $j++)
        {
            $currItem = & $feed->items[$j];
            // item title
            ?>
            <li class="newsfeed-item">
                <?php	if (!is_null($currItem->get_link())) {
                ?>
                <?php if (!is_null($feed->title) && $parameters->get('rsstitle', 1)) {
                    echo '<h5 class="feed-link">';
                }
                else
                {
                    echo '<h4 class="feed-link">';
                }
                ?>

                <a href="<?php echo $currItem->get_link(); ?>" target="_blank">
                    <?php echo $currItem->get_title(); ?></a>
                <?php if (!is_null($feed->title) && $parameters->get('rsstitle', 1)) {
                    echo '</h5>';
                }
                else
                {
                    echo '</h4>';
                }
                ?>
                <?php

            }

                // item description
                if ($parameters->get('rssitemdesc', 1)) {
                    // item description
                    $text = $currItem->get_description();
                    $text = str_replace('&apos;', "'", $text);
                    $text = strip_tags($text);
                    // word limit check
                    if ($words) {
                        $texts = explode(' ', $text);
                        $count = count($texts);
                        if ($count > $words) {
                            $text = '';
                            for ($i = 0; $i < $words; $i++) {
                                $text .= ' ' . $texts[$i];
                            }
                            $text .= '...';
                        }
                    }
                    ?>

                    <p><?php echo $text; ?></p>

                    <?php

                }
                ?>
            </li>
            <?php

        }
        ?>
    </ul>

</div>
<?php } ?>

