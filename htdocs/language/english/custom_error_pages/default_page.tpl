<div id="content">
<h1><{$lang_error_title}></h1>
<hr />
<p><{$lang_error_desc}></p>

<{$lang_error_info}>
<form name="search" id="search" action="<{$xoops_url}>/search.php" method="get">
  <p><{$lang_error_search}>:
	<input type="hidden" name="op" value="results" />
	<input type="text" name="query" />
	<input type="submit" name="Submit" value="search" />
  </p>
</form>
  <hr />
  <p><{$lang_error_contact}></p>
  <p><{$lang_error_http_footer}></p>
</div>