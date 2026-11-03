<?
	include("includes/init.txt");
	$id = time().rand(100,999);
?>


var gb = false;
var menu_created = false;
var menu_showing = false;
var MenuNode = null;

if (document.all && navigator.appVersion.toLowerCase().indexOf("mac") == -1) gb = true;
if (navigator.userAgent.indexOf('Gecko') != -1) gb = true;

<? if ($mini){ ?>
var html = '<div id="TubeNode<?=$id?>" style="position: relative; width: 80px;"><div style="position: relative;"><a target="_blank" href="<?=$cfg['base_url']?>" onclick="return ToggleTubeMenu();"><img border="0" src="<?=$cfg['base_url']?>images/browser_mini.gif" width="80" height="15"></a></div></div>';
<? }else{ ?>
var html = '<div id="TubeNode<?=$id?>" style="position: relative; width: 143px;"><div style="position: relative;"><a target="_blank" href="<?=$cfg['base_url']?>" onclick="return ToggleTubeMenu();"><img border="0" src="<?=$cfg['base_url']?>images/browser.gif" width="143" height="54"></a></div></div>';
<? } ?>

if (gb) {
	document.write(html);
	document.close();
} else {
	document.write(html);
}


function create_menu(){

	MenuNode = document.createElement('DIV');
	MenuNode.style.position = 'absolute';
<? if ($mini){ ?>
	MenuNode.style.top = '14px';
<? }else{ ?>
	MenuNode.style.top = '53px';
<? } ?>
	MenuNode.style.zIndex = '10';
	MenuNode.style.backgroundColor = '#ffffff';
	MenuNode.style.width = '143px';

	InnerNode = document.createElement('DIV');
	InnerNode.style.border = '1px solid #666666';
	InnerNode.style.fontSize = '11px';
	InnerNode.style.fontFamily = 'Arial, Helvetica, sans-serif';
	InnerNode.style.padding = '8px';

	MenuNode.appendChild(InnerNode);
<?
	if (!$ids){$ids = '1';}
	$station_ids = explode(',', $ids);

	foreach($station_ids as $station){
		$station_row = mysql_fetch_array(mysql_query("SELECT * FROM tube_stations WHERE id='$station'", $db));
		$station_row[name] = str_replace("&amp;", "&", $station_row[name]);
?>

	var b = appendBold(InnerNode, "Weblogs at ");
	appendLink(b, "<?=$station_row['name']?>", "<?=$cfg['base_url']?>station.php?id=<?=$station?>");
	appendText(b, ":");
	appendBreak(InnerNode);
	appendBreak(InnerNode);

<?
		$result = mysql_query("SELECT w.*,s.station_id FROM tube_weblogs AS w, tube_weblog_stations AS s WHERE w.id=s.weblog_id AND s.station_id='$station' ORDER BY w.blog_name ASC",$db);
		while($row = mysql_fetch_array($result)){

			if (!preg_match("/^http\:\/\//i",$row[blog_url])){
				$row[blog_url] = "http://$row[blog_url]";
			}
?>
	appendText(InnerNode, "• ");
	appendLink(InnerNode, "<?=AddSlashes($row['blog_name'])?>", "<?=$row['blog_url']?>");
	appendBreak(InnerNode);
<?
		}
?>

	appendBreak(InnerNode);
<?
	}
?>

	var b = appendBold(InnerNode);
	appendLink(b, "Are you a London Blogger?", "<?=$cfg['base_url']?>");

	// append menu
	document.getElementById('TubeNode<?=$id?>').appendChild(MenuNode);

	menu_created = true;
}

function ToggleTubeMenu(){
	if (menu_showing){
		hide_menu();
	}else{
		if (!menu_created) create_menu();
		show_menu();		
	}
	return false;
}

function hide_menu(){
	MenuNode.style.display = 'none';
	menu_showing = false;
}

function show_menu(){
	MenuNode.style.display = 'block';
	menu_showing = true;
}

function appendBold(parent, text){
	var node = document.createElement('B');
	if (text){
		node.appendChild(document.createTextNode(text));
	}
	parent.appendChild(node);
	return node;
}

function appendBreak(parent){
	var node = document.createElement('BR')
	parent.appendChild(node);
	return node;
}

function appendLink(parent, text, href){
	var node = document.createElement('A');
	node.appendChild(document.createTextNode(text));
	node.href = href;
	node.target = '_blank';
	parent.appendChild(node);
	return node;
}

function appendImage(parent, src){
	var node = document.createElement('IMG');
	node.src = src;
	parent.appendChild(node);
	return node;
}

function appendText(parent, text){
	var node = document.createTextNode(text)
	parent.appendChild(node);
	return node;
}
