<?php if(!defined('PLX_ROOT')) exit; ?>

	<aside class="aside col sml-12 med-3">
		<h3><?php $plxShow->lang('CATEGORIES'); ?></h3>
		<ul class="cat-list unstyled-list">
			<?php $plxShow->catList('','<li id="#cat_id"><a class="#cat_status" href="#cat_url" title="#cat_name">#cat_name</a> (#art_nb)</li>'); ?>
		</ul>

		<h3><?php $plxShow->lang('LATEST_ARTICLES'); ?></h3>
		<ul class="lastart-list unstyled-list">
			<?php $plxShow->lastArtList('<li><a class="#art_status" href="#art_url" title="#art_title">#art_title</a></li>'); ?>
		</ul>

<?php
if(defined('MULTI_USERS')) {
?>
		<h3><?php $plxShow->lang('AUTHORS'); ?></h3>
		<ul class="author-list unstyled-list">
<?php
# Par défaut, tri selon la date de l'article le plus récent de chaque auteur
$plxShow->authorList();
# Sinon tri par name, lastname ou hits
# $plxShow->authorList(plxShow::AUTHOR_PATTERN, false, false, 'hits');
?>
		</ul>
<?php
}
?>
		<h3><?php $plxShow->lang('TAGS'); ?></h3>
		<ul class="tag-list">
			<?php $plxShow->tagList('<li class="tag #tag_size"><a class="#tag_status" href="#tag_url" title="#tag_name">#tag_name</a></li>', 20); ?>
		</ul>

		<h3><?php $plxShow->lang('LATEST_COMMENTS'); ?></h3>
		<ul class="lastcom-list unstyled-list">
			<?php $plxShow->lastComList('<li><a href="#com_url">#com_author '.$plxShow->getLang('SAID').' : #com_content(34)</a></li>'); ?>
		</ul>

		<h3>Flux RSS</h3>
		<ul class="unstyled-list">
<?php
$plxShow->urlPostsRssFeed('', 'li');
?>
		</ul>

		<h3><?php $plxShow->lang('ARCHIVES'); ?></h3>
		<ul class="arch-list unstyled-list">
			<?php $plxShow->archList('<li id="#archives_id"><a class="#archives_status" href="#archives_url" title="#archives_name">#archives_name</a> (#archives_nbart)</li>'); ?>
		</ul>
	</aside>
