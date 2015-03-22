<?php
/**
 * @version   $Id: item.php 11582 2013-06-20 13:09:51Z arifin $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @var $item RokSprocket_Item
 */
?>
<li<?php echo strlen($item->custom_tags) ? ' class="'.$item->custom_tags.'"' : ''; ?> data-mosaic-item>
	<div class="sprocket-mosaic-item" data-mosaic-content>
		<?php echo $item->custom_ordering_items; ?>
		<div class="sprocket-padding">
			<div class="sprocket-mosaic-head">
				<?php if ($item->getTitle()): ?>
				<h2 class="sprocket-mosaic-title">
					<?php if ($item->getPrimaryLink()): ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
						<?php echo $item->getTitle();?>
					<?php if ($item->getPrimaryLink()): ?></a><?php endif; ?>
				</h2>
				<?php endif; ?>

				<?php if (($parameters->get('mosaic_article_details') !== 'author') and ($parameters->get('mosaic_article_details') !== '0')): ?>
				<div class="sprocket-mosaic-infos">
					<span class="date"><?php echo $item->getDate();?></span>
				</div>
				<?php endif; ?>
			</div>

			<div class="sprocket-mosaic-content">
				<?php if ($item->getPrimaryImage()) :?>
				<div class="sprocket-mosaic-image-container">
					<?php if ($item->getPrimaryLink()) : ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
					<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" alt="" class="sprocket-mosaic-image" />
					<?php if ($item->getPrimaryLink()) : ?>
						<span class="sprocket-mosaic-hover"></span>
						<span class="sprocket-mosaic-hovercontent"><span class="icon-plus-sign"></span></span>
					</a>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<div class="sprocket-mosaic-text">
					<?php echo $item->getText(); ?>
				</div>				
			</div>

			<?php if (($parameters->get('mosaic_article_details') !== 'date') and ($parameters->get('mosaic_article_details') !== '0')): ?>
				<div class="sprocket-mosaic-infos">
					Posted by: 	<strong class="author"><?php echo $item->getAuthor(); ?></strong>
				</div>
			<?php endif; ?>
			
			<?php if ($item->getPrimaryLink()) : ?>
			<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="sprocket-readmore"><span><?php rc_e('READ_MORE'); ?></span></a>
			<?php endif; ?>

			<?php if (count($item->custom_tags_list)) : ?>
				<ul class="sprocket-mosaic-tags">
				<?php
					foreach($item->custom_tags_list as $key => $name){
				 		echo ' <li class="sprocket-tags-'.$key.'">'.$name.'</li>';
					}
				?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</li>
