<?php
ob_end_flush();

if (!Functions::IsLoggedIn()) {
  header('Location: /');
  die();
}

$mysqli->begin_transaction();

/* 

BAN MANAGER 

1. Search if the ip have a banType 2 and die

2. Search if the account have a ban 

*/
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

$client  = @$_SERVER['HTTP_CLIENT_IP'];
$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$remote  = $_SERVER['REMOTE_ADDR'];

if (filter_var($client, FILTER_VALIDATE_IP)) {
  $ip = $client;
} else if (filter_var($forward, FILTER_VALIDATE_IP)) {
  $ip = $forward;
} else {
  $ip = $remote;
}

$search_ban_ip = $mysqli->query("SELECT * FROM user_bans WHERE ip_user= '".$ip."' AND banType = 2");
//$search_ban_ip = $mysqli->query("SELECT * FROM user_bans WHERE ip_user= '".$ip."' AND banType = 2");

if (mysqli_num_rows($search_ban_ip) > 0) {
  header('Location: /ban');
  die();
}else{
  $search_ban_user = $mysqli->query("SELECT * FROM user_bans WHERE userId = '". $player['userId']."'");
  if (mysqli_num_rows($search_ban_user) > 0) {
    header('Location: /ban');
    die();
  }
}

/* END DeathSpace BAN MANAGER */

try {
  foreach ($mysqli->query('SELECT * FROM server_bans WHERE userId = ' . $player['userId'] . ' AND ended = 0')->fetch_all(MYSQLI_ASSOC) as $value) {
    if (new DateTime(date('d.m.Y H:i:s')) >= new DateTime($value['end_date'])) {
      $mysqli->query('UPDATE server_bans SET ended = 1 WHERE id = ' . $value['id'] . '');
    }
  }

  $mysqli->commit();
} catch (Exception $e) {
  $mysqli->rollback();
}
?>

<!DOCTYPE html>
<html lang="en">
<script>
const name = "<?= $player['pilotName']; ?>";
</script>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo SERVER_NAME; ?></title>
  <meta name="description" content="<?php echo SERVER_NAME; ?> is the ultimate space shooter. Explore the endless expanses of universe in one of the best and most exciting online browser games ever produced. Brave all dangers and go where nobody's ever gone before - either alone or with others. Set a course for undiscovered galaxies and seek out new lifeforms!">
  <meta property="og:title" content="<?php echo SERVER_NAME; ?>" />
  <meta property="og:url" content="<?php echo DOMAIN; ?>" />
  <meta property="og:site_name" content="<?php echo SERVER_NAME; ?>" />
  <meta property="og:description" content="<?php echo SERVER_NAME; ?> is the ultimate space shooter. Explore the endless expanses of universe in one of the best and most exciting online browser games ever produced. Brave all dangers and go where nobody's ever gone before - either alone or with others. Set a course for undiscovered galaxies and seek out new lifeforms!" />
  <meta name="robots" content="index, follow">
  <meta name="revisit-after" content="1 days">
  <script defer src="http://ads.charmingkingdom.com:3000/socket.io/socket.io.js"></script>
    <script defer src="script.js"></script>
<link rel="stylesheet" href="chat.css">
  <link rel="shortcut icon" href="<?php echo DOMAIN; ?>favicon.ico" />
  <link rel="stylesheet" type="text/css" href="<?php echo DOMAIN; ?>css/map-revolution.css" />
  <script type="text/javascript" src="<?php echo DOMAIN; ?>js/darkorbit/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo DOMAIN; ?>js/darkorbit/jquery.js"></script>
  <script type="text/javascript" src="<?php echo DOMAIN; ?>js/darkorbit/jquery.flashembed.js"></script>
  <script type="text/javascript" src="<?php echo DOMAIN; ?>js/darkorbit/main.js"></script>
</head>
<style>#k38f{position:fixed!important;position:absolute;z-index:9999999999;top:0;top:expression((t=document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop)+"px");left:0;width:100%;height:100%;background-color:#000;opacity:.9;filter:alpha(opacity=95);display:block}#k38f p{opacity:1;filter:none;font:bold 16px Verdana,Arial,sans-serif;text-align:center;color:white;margin:5% 0}#k38f p a,#k38f p i{font-size:12px}#k38f ~ *{display:none}</style><noscript><i id=k38f></i></noscript><script>/*<![CDATA[*/(function(b,c){var f=b.document,e=typeof c;function a(){function i(o,k){var n=f.createElement("i"),g=f.body,m=g.style,j=g.childNodes.length;if(typeof k!=e){n.setAttribute("id",k);m.margin=m.padding=0;m.height="100%";j=Math.floor(Math.random()*j)+1}n.innerHTML=o;g.insertBefore(n,g.childNodes[j-1])}function d(j,g){return !g?f.getElementById(j):f.getElementsByTagName(g)}function h(g){if(!d("k38f")){document.body.innerHTML="";i('<p><img src="https://2.bp.blogspot.com/-zKetYgyNzDI/VF5tTrSe_mI/AAAAAAABjU4/nTzAWfemjng/s1600/adblock.png"><br>¡Please disable Adblock to play!<br><a href="./map-revolution" style="color:#0887D4;text-decoration:none;">Already disabled ? CLICK HERE</a><br><i></i><br></p>',"k38f")}}(function(){var j=["ad_P","btnads","header-ad","hideads","left-bottom-ad","systemad_background","wXcds12-ad","ad","ads","adsense"],g=j.length,k,m="",n;for(k=0;k<g;k++){if(!d(j[k])){m+='<a id="'+j[k]+'"></a>'}}i(m);g=j.length;setTimeout(function(){for(k=0;k<g;k++){n=d(j[k]);if(n.offsetParent==null||(b.getComputedStyle?f.defaultView.getComputedStyle(n,null).getPropertyValue("display"):n.currentStyle.display)=="none"){return h("#"+j[k])}}},250)}());(function(){var k=d(0,"img"),g=["/adsponsor.","/adtopleft.","/adzoneplayerright.","/aff_frame.","/commercial_horizontal.","/dart_ads/ad","/googleads/ad","_feast_ad.","_top_ad_",".468x60_"],j;if(typeof k[0]!=e&&typeof k[0].src!=e){j=new Image();j.onload=function(){this.onload=e;this.onerror=function(){h(this.src)};this.src=k[0].src+"#"+g.join("")};j.src=k[0].src}}());(function(){var t={"https://pagead2.googlesyndication.com/pagead/show_ads.js":"google_ad_client","https://js.adscale.de/getads.js":"adscale_slot_id","https://get.mirando.de/mirando.js":"adPlaceId"},m=d(0,"script"),g=m.length-1,u,q,k,j,p;f.write=null;for(k=g;k>=0;--k){p=m[k];if(typeof t[p.src]!=e){u=f.createElement("script");u.type="text/javascript";u.src=p.src;j=t[p.src];b[j]=c;q=m[0];u.onload=u.onreadystatechange=function(){if(typeof b[j]==e&&(!this.readyState||this.readyState==="loaded"||this.readyState==="complete")){u.onload=u.onreadystatechange=null;q.parentNode.removeChild(u);b[j]=null}};q.parentNode.insertBefore(u,q);setTimeout(function(){if(b[j]===c){h(u.src)}},2000);break}}}())}if(f.addEventListener){b.addEventListener("load",a,false)}else{b.attachEvent("onload",a)}})(window);/*]]>*/</script>

<body>

  <?php
  $statement = $mysqli->query('SELECT * FROM server_bans WHERE typeId = 1 AND ended = 0 AND userId = ' . $player['userId'] . '');
  $fetch = $statement->fetch_assoc();

  if ($statement->num_rows <= 0) {
    $gamePlayerSettings = json_decode($mysqli->query('SELECT * FROM player_settings WHERE userId = ' . $player['userId'] . '')->fetch_assoc()['gameplay']);
  ?>
    <div id="container"></div>

    <script type="text/javascript">
      function onFailFlashembed() {
        var html = '';
        html += '<div id="flashFail">';
        html += '<div class="flashFailHead">Get the Adobe Flash Player</div>';
        html += '<div class="flashFailHeadText">';
        html += 'In order to play <?php echo SERVER_NAME; ?>, you need the latest version of Flash Player. Just install it to start playing!';
        html += '<div class="flashFailHeadLink">';
        html += 'Download the Flash Player here for free: <a href=\"http://www.adobe.com/go/getflashplayer\" style=\"text-decoration: underline; color:#b1b1e8;\">Download Flash Player<\/a>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        jQuery('#container').html(html);
      }

      function expressInstallCallback(info) {
        onFailFlashembed();
      }

      jQuery(document).ready(function() {
        var aFlashVersion = flashembed.getVersion();
        if (!Object.keys) Object.keys = function(o) {
          if (o !== Object(o))
            throw new TypeError('Object.keys called on non-object');
          var ret = [],
            p;
          for (p in o)
            if (Object.prototype.hasOwnProperty.call(o, p)) ret.push(p);
          return ret;
        }
        var sParam = '[' + '"jQuery.flashEmbed"' + ',"' + aFlashVersion[0] + "." + aFlashVersion[1] + '"' + ',"' + Object.keys(jQuery.browser)[0] + '"]';
        var data = {
          "action": "setClientBrowserConfig",
          "params": sParam,
          "isEncodeMessage": 0
        }

        jQuery.post('<?php echo DOMAIN; ?>flashAPI/loadingScreen.php', data, function(data) {});

        flashembed("container", {
          "onFail": onFailFlashembed,
          "src": "http://ads.charmingkingdom.com/spacemap/preloader.swf",
          "version": [11, 0],
          "expressInstall": "http://ads.charmingkingdom.com/swf_global/expressInstall.swf",
          "width": "100%",
          "height": "100%",
          "wmode": "direct",
          "bgcolor": "#000000",
          "id": "preloader",
          "allowfullscreen": "true",
          "allowFullScreenInteractive": "true"
        }, {
          "lang": "en",
          "userID": "<?php echo $player['userId']; ?>",
          "sessionID": "<?php echo $player['sessionId']; ?>",
          "basePath": "spacemap",
          "pid": "563",
          "boardLink": "",
          "helpLink": "",
          "loadingClaim": "DeathSpace..",
          "chatHost": "ads.charmingkingdom.com",
          "cdn": "",
          "useHash": "0",
          "host": "<?php echo $_SERVER['SERVER_NAME']; ?>",
          "browser": "Chrome",
          "Chrome": "1",
          "gameXmlHash": "",
          "resourcesXmlHash": "",
          "profileXmlHash": "",
          "languageXmlHash": "",
          "loadingscreenHash": "",
          "gameclientHash": "",
          "gameclientPath": "spacemap",
          "loadingscreenAssetsXmlHash": "",
          "showAdvertisingHint": "",
          "gameclientAllowedInitDelay": "10",
          "eventStreamContext": "",
          "useDeviceFonts": "0",
          "display2d": "<?php echo ($player['version'] ? '1' : '2'); ?>",
          "autoStartEnabled": "<?php echo (int) ($gamePlayerSettings != null ? $gamePlayerSettings->autoStartEnabled : true); ?>",
          "mapID": "1",
          "allowChat": "<?php echo (int) ($mysqli->query('SELECT * FROM server_bans WHERE typeId = 0 AND userId = ' . $player['userId'] . '')->num_rows <= 0); ?>"
        });
      });
    </script>
		<script type="text/javascript">
var time = 30;
var timerun = true;
 
function closeTheBanner1() {
    $(".roomenterad-habblet-container1").animate({
                top: -$(".roomenterad-habblet-container1").height() + "px"
        }, 1000);
        timerun = false
}
 
setInterval(function () {
    if (timerun) {
        $(".roomenterad-closing1").text("Closing in " + time +" Seconds");
        time--;
        if (time < 0) {
                        closeTheBanner1();
        }
    }
}, 1000);
 
setInterval(function () {
    var htmlz = $(".roomenterad-habblet-thead1").html();
    $(".roomenterad-habblet-thead1").text("");
    $(".roomenterad-habblet-container1").animate({
                top: "0px"
        }, 1000);
    $(".roomenterad-habblet-thead1").html(htmlz);
    time = 30;
    timerun = true
}, 1800000);
</script>
</head>

<div class="roomenterad-habblet-container1" style="display: inline-block; left: 50%; margin-left: -370px; margin-top: -30px; position: fixed; height: 195px; width: 740px; background-image: url(http://i.imgur.com/H2CeyfP.png); background-repeat: no-repeat no-repeat; boder:: 99999;">
<div style="position: absolute; right: 22px; top: 76px; color: #ffffff; font-weight: bold; font-size: 10px; font-family: Verdana, sans-serif;">Close Now</div>
<div style="position: absolute; left: 6px; top: 76px; color: rgb(255, 255, 255); font-size: 10px; font-family: Verdana, sans-serif; visibility: visible;" class="roomenterad-closing1">Closing in 30 Seconds</div>
<div style="position: absolute; right: 9px; top: 75px;"><img onclick="closeTheBanner1();" src="http://i.imgur.com/VuLhUw2.png"></div>
<div style="/* position: absolute; */left: 6px;margin-top: 96px;top: 94px;" class="roomenterad-habblet-thead1">

<center>
		<script id="mNCC" language="javascript">
    medianet_width = "728";
    medianet_height = "90";
    medianet_crid = "284389236";
    medianet_versionId = "3111299";
    </script>
<script src="https://contextual.media.net/nmedianet.js?cid=8CU9Y15B0"></script></center>
</div>
</div>

<div class="ads1" style="bottom: 0px; background-image:url('http://i.imgur.com/tMKWSmW.png');padding-top:36px;padding-left:6px;margin-left:13%;height:100px;width:740px; background-repeat: 10; z-index: 7000; width: 734px; position: fixed; height:100px; left: 50%; margin-left:-370px; margin-right:auto; overflow: hidden;">
	<span onmouseenter="myEnterFunction()" id="Cerrar" onclick="$('.ads1').slideUp('slow')" title="Cerrar publicidad por 2 minutos" style="transition: all 1s linear; z-index: 9999; position: absolute;top: 40px; left: 30px; border-radius: 15px; padding: 2px 4px; font-size: 10pt; color: red; background: white; box-shadow: 0 0 10px white; cursor: pointer;">X</span>

			<script>
                            var i = 0

                            function myEnterFunction() {
console.log(i);
                                if (i++ < 4) {
                                    var Rand = Math.floor((Math.random() * 300) + 300);
                                    document.getElementById("Cerrar").style.marginLeft = Rand + "px";
                                }
                            }
                        </script>

			<center style="margin-top: -26px;">
		<script id="mNCC" language="javascript">
    medianet_width = "728";
    medianet_height = "90";
    medianet_crid = "284389236";
    medianet_versionId = "3111299";
    </script>
<script src="https://contextual.media.net/nmedianet.js?cid=8CU9Y15B0"></script></center>

		
<script>
	 $(function(){
								var c2 = 1;
				jQuery("#close2").bind('mouseover',function(){
					if(c2 < 3){
						var lef = Math.floor((Math.random() * 682) + 50);
						jQuery("#close2").css({"left":lef + "px"})
						c2++;
					}
				})
								var c1 = 1;
				jQuery("#close1").bind('mouseover',function(){
					if(c1 < 3){
						var lefx = Math.floor((Math.random() * 682) + 50);
						jQuery("#close1").css({"right":lefx + "px"})
						c1++;
					}
				})
				                setInterval(function(){
                    $(".ads").slideDown("slow");
                 var i = 0;

                },1000);
                setInterval(function(){
                    $(".ads1").slideDown("slow");
                    var i = 0;
                },600000);
                setTimeout(function(){
                    if($("#aswift_0_expand").length != 1){
                        $("#divJoder").fadeIn(2000);
                    }
                },600000)
            });

</script>
<!-- ANUNCIO -->
  <?php } else { ?>
    <div id="container">
      <div id="banned">
        <div>You are banned by administrator.</div>
        <div>Reason: <?php echo $fetch['reason']; ?></div>
        <div>End date: <?php echo (new DateTime(date('d.m.Y H:i:s')))->diff(new DateTime($fetch['end_date']))->days >= 9998 ? 'Permanent' : date('d.m.Y H:i', strtotime($fetch['end_date'])); ?></div>
      </div>
    </div>
  <?php } ?>
  
</body>

</html>