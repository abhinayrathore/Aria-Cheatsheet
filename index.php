<?
$ROLES_LINK = 'https://www.w3.org/TR/wai-aria/roles';
$ROLES_LINK_LOCAL = 'cache/roles.html';
$STATES_PROPS_LINK = 'https://www.w3.org/TR/wai-aria/states_and_properties';
$STATES_PROPS_LINK_LOCAL = 'cache/states_and_properties.html';
$ROLES_HTML = '';
$STATES_PROPS_HMTL = '';

$ROLES = array(
	'abstract_roles' => 'Abstract Roles',
	'widget_roles' => 'Widget Roles',
	'document_structure_roles' => 'Document Structure Roles',
	'landmark_roles' => 'Landmark Roles'
);

$STATES_PROPS = array(
	'global_states' => 'Global States and Properties',
	'attrs_widgets' => 'Widget Attributes',
	'attrs_liveregions' => 'Live Region Attributes',
	'attrs_dragdrop' => 'Drag-and-Drop Attributes',
	'attrs_relationships' => 'Relationship Attributes'
);

// Update local file contents if it's older than 1 day
if (!file_exists($ROLES_LINK_LOCAL) || time() - filemtime($ROLES_LINK_LOCAL) >= 60*60*24*1) {
	$ROLES_HTML = getContent($ROLES_LINK, $ROLES);
	$STATES_PROPS_HMTL = getContent($STATES_PROPS_LINK, $STATES_PROPS);
	@file_put_contents($ROLES_LINK_LOCAL, $ROLES_HTML);
	@file_put_contents($STATES_PROPS_LINK_LOCAL, $STATES_PROPS_HMTL);
} else { // read from local cache files
	$ROLES_HTML = @file_get_contents($ROLES_LINK_LOCAL);
	$STATES_PROPS_HMTL = @file_get_contents($STATES_PROPS_LINK_LOCAL);
}

$LAST_UPDATED = date("F d\<\s\u\p\>S\<\/\s\u\p\> Y H:i:s", filemtime($ROLES_LINK_LOCAL));

// Helper function to return all RegEx matches
function match_all($regex, $str, $i = 1){
	if(preg_match_all($regex, $str, $matches) === false)
		return false;
	else
		return $matches[$i];
}

// Helper function to return RegEx match
function match($regex, $str, $i = 1){
	if(preg_match($regex, $str, $match) == 1)
		return $match[$i];
	else
		return false;
}

// Get the main section for any ARIA attribute based on the id
function getSection($id, $html) {
	return match('/<div class=".*?" id="' . $id . '">(.*?)<\/div>\s*<hr\/>/msi', $html);
}

// Get the short description for any ARIA attribute from the section HTML content
function getDescription($html) {
	return match('/<div class=".*?-description">.*?<p>(.*?)<\/p>/msi', $html);
}

// Sanitize links and convert them into skip links
function sanitizeLinks($html) {
	$html = preg_replace('/<a.*?class="termref".*?>(.*?)<\/a>/msi', '${1}', $html);
	$html = preg_replace('/href="(.*?)#(.*?)"/msi', 'href="#${2}"', $html);
	return $html;
}

// Remove unwanted stuff and clean the HTML contents
function normalizeBlock($html) {
	$html = preg_replace('/<\/?a.*?>/msi', '', $html);
	$html = preg_replace('/<p class="permalink">.*?<\/p>/msi', '', $html);
	$html = preg_replace('/<table.*?>/msi', '<table class="table table-condensed table-hover table-bordered">', $html);
	return $html;
}

// Get the rendered contents for all the properties from a URL
function getContent($URL, $MAP) {
	$HTML = @file_get_contents($URL);
	$o = "";

	foreach ($MAP as $id => $name) {
		$sectionHtml = match('/<div class="section" id="'.$id.'".*?>(.*?)<\/div>/msi', $HTML);
		$sectionHtml = preg_replace('/<p class="permalink">.*?<\/p>/msi', '', $sectionHtml);
		$topSectionDesc = normalizeBlock(match('/<p>(.*?)<\/p>/msi', $sectionHtml));
		
		$o .= "<h3><a href=\"${URL}#${id}\" class=e>${name}</a></h3>";
		$o .= "<table>";
		$o .= "<caption>${topSectionDesc}</caption>";
		
		foreach (match_all('/<ul>(.*?)<\/ul>/msi', $sectionHtml) as $ul) {
			foreach (match_all('/<li>(.*?)<\/li>/msi', $ul) as $li) {
				$nm = match('/<code>(.*?)<\/code>/msi', $li);
				$_id = match('/href=".*?\#(.*?)"/msi', $li);
				$section = getSection($_id, $HTML);
				$sectionUrl = "${URL}#${_id}";
				$desc = getDescription($section);
				
				$o .= "<tr id='${_id}'>";
					$o .= "<th><a href=\"${sectionUrl}\" class=e>${nm}</a></th>";
					$o .= "<td>" . sanitizeLinks($desc) . "</td>";
				$o .= "</tr>";
			}
		}
		$o .= "</table>";
	}

	return $o;
}
?>
<!doctype html>
<html>
	<head>
		<title>WAI-ARIA Cheatsheet</title><meta name="viewport" content="width=device-width, initial-scale=1"><link rel="stylesheet" href="c/m.css">
	</head>
	<body>
		<main>
			<h1>WAI-ARIA Cheatsheet</h1>
			<section><h2>Roles</h2><?=$ROLES_HTML;?></section>
			<section><h2>States and Properties</h2><?=$STATES_PROPS_HMTL;?></section>
			<p class=footnote>Last Updated: <?=$LAST_UPDATED;?></p>
		</main>
		<script src="j/main.js"></script>
	</body>
</html>