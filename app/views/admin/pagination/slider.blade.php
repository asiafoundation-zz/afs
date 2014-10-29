<?php
	$presenter = new Illuminate\Pagination\BootstrapPresenter($paginator);

	$prevUrl = $paginator->getUrl($paginator->getCurrentPage() - 1);

	$nextPage = $paginator->getCurrentPage() + 1;
	if($nextPage > $paginator->getLastPage())
	{
	    $nextPage = $paginator->getCurrentPage();
	}
	$nextUrl = $paginator->getUrl($nextPage);
?>

<div class="pagination-wrap">
    <ul class="pagination">
    <li><a href="{{ $paginator->getUrl(1) }}"><span class="glyphicon glyphicon-backward"></span></a></li>
    <li><a href="{{ $prevUrl }}"><span class="glyphicon glyphicon-play previous"></span></a></li>
    <li><a href="#">{{ $paginator->getCurrentPage() }} / {{ $paginator->getLastPage() }}</a></li>
    <li><a href="{{ $nextUrl }}"><span class="glyphicon glyphicon-play"></span></a></li>
    <li><a href="{{ $paginator->getUrl($paginator->getLastPage()) }}"><span class="glyphicon glyphicon-forward"></span></a></li>
    </ul>
</div>
