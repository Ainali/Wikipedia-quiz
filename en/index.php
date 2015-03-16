<html>
    <head>
		<title>Wikipedia image quiz</title>
		<meta name="keywords" content="quiz, image quiz, wikipedia game" />
		<meta name="distribution" content="global" />
		<meta name="robots" content="follow,index" />
      <meta name="author" content="Jan Ainali" >
		<meta http-equiv="Content-Language" content="en" />
		<link rel="image_src" href="rubrik.png" />
		<link rel="icon" type="image/x-icon" href="http://nyval.wtf/quiz/favicon.ico">
		<meta name="description" content="Image quiz! From which Wikipedia article is this image?" />
		<meta property="og:image" content="rubrik.png"/> 
		<style>
		body{	text-align: center;
				min-width:650px;
				background-color: white;
				font-family: Arial;
				font-size: 20px; 
  				line-height: 28px; 
  				}
  		h1   {font-family: Arial;
  				text-align: center; 
  				font-size: 50px; 
  				line-height: 50px; 
  				font-weight: bold; 
  				color:black;}
  		p    {font-family: Arial;
  				text-align: center; 
  				font-size: 20px; 
  				line-height: 28px; 
  				color:black;}
  		a    {font-family: Arial; 
  				font-size: 20px; 
  				line-height: 28px; 
  				text-align: center;
  				color:black;
				text-decoration:none;}
  		a:hover {color:black;}
  		footer    {font-family: Arial; 
  				font-size: 14px; 
  				line-height: 14px; 
  				color:gray;
  				text-align: center;}
  		.btn {
  				background: #3498db;
  				background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
  				background-image: -moz-linear-gradient(top, #3498db, #2980b9);
  				background-image: -ms-linear-gradient(top, #3498db, #2980b9);
  				background-image: -o-linear-gradient(top, #3498db, #2980b9);
  				background-image: linear-gradient(to bottom, #3498db, #2980b9);
  				-webkit-border-radius: 28;
  				-moz-border-radius: 28;
  				border-radius: 28px;
  				font-family: Arial;
  				color: #ffffff;
  				font-size: 20px;
  				padding: 10px 20px 10px 20px;
  				text-decoration: none;
				}
		.btn:hover {
  				background: #3cb0fd;
  				background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
  				background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
  				background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
  				background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
  				background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
  				text-decoration: none;
				}		
  		</style>
  		<script type="text/javascript" language="javascript">// <![CDATA[
function showHide() {
    var ele = document.getElementById("showHideDiv");
    if(ele.style.display == "block") {
            ele.style.display = "none";
      }
    else {
        ele.style.display = "block";
    }
    var ele = document.getElementById("showHideCorrect");
    if(ele.style.display == "block") {
            ele.style.display = "none";
      }
    else {
        ele.style.display = "block";
    }
}
// ]]></script>
	</head>
	<body>
<?php
//Initialize
$lang = "en";
$imagefound = 0;
$hasimages = [0,0,0,0];
while($imagefound < 1) {
   //Ask for images from four random articles
	$url1 = "http://$lang.wikipedia.org/w/api.php?action=query&prop=pageimages&format=json&piprop=thumbnail%7Cname&pithumbsize=400&pilimit=4&generator=random&grnnamespace=0&grnlimit=4";
	$result1=file_get_contents($url1);
	$returned = json_decode($result1,true);
	$images = $returned["query"]["pages"];
	//Check if any of them has an image
   $idcount = 0;
	foreach ($images as $id) {
              if(isset($id["thumbnail"])) {
              		//Only use images from Wikimedia Commons
              	  	if(strpos($id["thumbnail"]["source"], 'wikipedia/commons')!== false) {
              	  	$showimage = $id["thumbnail"]["source"];
              		$hasimages[$idcount] = 1;
              		$imagetitle = $id["pageimage"];
              		$answer = $id["title"];
              		$answerid = $id["pageid"];
              		$answercount = $idcount;
              		$imagefound ++;
              		};
              };
              //Make it a little easier to refer to them
              $titles[$idcount] = $id["title"];
              $ids[$idcount] = $id["pageid"];
              $idcount ++;
          }
	//Check if the image is used of any of the other articles
	if($imagefound > 0) {
		$url2 = "http://$lang.wikipedia.org/w/api.php?action=query&prop=images&format=json&imimages=Fil:$imagetitle&pageids=$id[0]|$id[1]|$id[2]|$id[3]";
		$result2=file_get_contents($url2);
		$check = json_decode($result2,true);
   	$imageuses = 0;
   	foreach ($check["query"]["pages"] as $nos) {
   	if(isset($nos["images"])){
   			$imageuses ++;
   		};
   		}
   	if($imageuses > 1) {
   		$imagefound = 0;
  		};
   };
};
// Get text extract for the answer
$adress = "http://$lang.wikipedia.org/w/api.php?action=query&prop=extracts&format=json&exintro=&explaintext=&pageids=$answerid";
$svar=file_get_contents($adress);
$holder = json_decode($svar,true);
foreach ($holder["query"]["pages"] as $number) {
	$learning = $number["extract"];	
	}
//Prepare for displaying the right answer
switch ($answercount) {
   case 0:
         $disp0 = "showHideCorrect";
         break;
   case 1:
         $disp1 = "showHideCorrect";
         break;
   case 2:
         $disp2 = "showHideCorrect";
         break;
   case 3:
         $disp3 = "showHideCorrect";
         break;
}
//For easier translations strings are parameters collected here	
$header = "From which of these Wikipedia articles is this image?";
$readmore1 = "Read more about ";
$readmore2 = " on Wikipedia.";
$imageclick = "Click on the image to see license information and the author.";
$showanswer = "Show answer";
$edit = "Edit ";
$editarticle = "Edit the article.";	
$newquiz = "New quiz";
$another = "Take quiz in another language.";
$createdby = "Created by ";
$followimage = "Follow image link for license information and attribution of the image. ";
$extract = "Extract";
$under = " under ";
$sourcecode = "Source code under ";
$donate = "Donate to Wikipedia";
$noimages= "No images were found.";
//Create the contents of the page
if (isset($images)) {
    echo '<h1>', $header,'</h1><p><div style="width=600; margin=0 auto";>';
    echo '<div id="', $disp0,'" style="display:none; position:relative; top:30px;"><img src="https://upload.wikimedia.org/wikipedia/commons/8/8c/Bert2_transp_green_cont_150ms.gif" width="300px" height="4px"></div>';
    echo '<a href="#" onclick="return showHide();">', $titles[0],'</a> <a href="https://', $lang,'.wikipedia.org/wiki/', $titles[0], '" title="', $readmore1, $titles[0], $readmore2,'"><img src="https://upload.wikimedia.org/wikipedia/commons/6/63/Wikipedia-logo.png" width="20px" height="20px" alt="', $readmore1, $titles[0], $readmore2,'"></a><br />';
	 echo '<div id="', $disp1,'" style="display:none; position:relative; top:30px;"><img src="https://upload.wikimedia.org/wikipedia/commons/8/8c/Bert2_transp_green_cont_150ms.gif" width="300px" height="4px"></div>';    
    echo '<a href="#" onclick="return showHide();">', $titles[1],'</a> <a href="https://', $lang,'.wikipedia.org/wiki/', $titles[1], '" title="', $readmore1, $titles[1], $readmore2,'"><img src="https://upload.wikimedia.org/wikipedia/commons/6/63/Wikipedia-logo.png" width="20px" height="20px" alt="', $readmore1, $titles[1], $readmore2,'"></a><br />';
    echo '<div id="', $disp2,'" style="display:none; position:relative; top:30px;"><img src="https://upload.wikimedia.org/wikipedia/commons/8/8c/Bert2_transp_green_cont_150ms.gif" width="300px" height="4px"></div>';
    echo '<a href="#" onclick="return showHide();">', $titles[2],'</a> <a href="https://', $lang,'.wikipedia.org/wiki/', $titles[2], '" title="', $readmore1, $titles[2], $readmore2,'"><img src="https://upload.wikimedia.org/wikipedia/commons/6/63/Wikipedia-logo.png" width="20px" height="20px" alt="', $readmore1, $titles[2], $readmore2,'"></a><br />';
    echo '<div id="', $disp3,'" style="display:none; position:relative; top:30px;"><img src="https://upload.wikimedia.org/wikipedia/commons/8/8c/Bert2_transp_green_cont_150ms.gif" width="300px" height="4px"></div>';
    echo '<a href="#" onclick="return showHide();">', $titles[3],'</a> <a href="https://', $lang,'.wikipedia.org/wiki/', $titles[3], '" title="', $readmore1, $titles[3], $readmore2,'"><img src="https://upload.wikimedia.org/wikipedia/commons/6/63/Wikipedia-logo.png" width="20px" height="20px" alt="', $readmore1, $titles[3], $readmore2,'"></a></div></p>'; 
    echo '<p><a href="https://commons.wikimedia.org/wiki/File:', $imagetitle,'" title="', $imageclick,'">
    <img src="', $showimage, '" alt="', $imageclick,'"></a></p>
    </p>';
//   echo '<p><form method="post" action="">
//			<p><input class="btn" type="button" value="', $showanswer,'" onclick="return showHide();" accesskey="s"/></p>
//			</form>'
	echo '<div id="showHideDiv" style="display:none; width:600px; margin:0 auto;"><a href="https://', $lang,'.wikipedia.org/wiki/', $answer, '" style="color:#000" title="', $answer,'">', $answer,'</a><br />
		   <span style="font-size:16px; line-height: 16px; color:#666; text-decoration:none; text-align: left;">', $learning, '</span><br />
		   <a href="https://', $lang,'.wikipedia.org/wiki/', $answer, '?gettingStartedReturn=true" style="font-size:16px; color:#669; text-decoration:none; text-align: center;" title="', $edit, $answer,'">', $editarticle,'</a></div><br />
			<FORM>
			<INPUT class="btn" TYPE="button" onClick="location.reload(true); return false;" VALUE="', $newquiz,'" accesskey="n">
			</FORM>';
    echo '<footer><a href="http://nyval.wtf/quiz/" style="font-size:14px; line-height: 14px; color:#666; text-decoration:none;" alt="', $another,'">', $another,'</a><br/>
    ', $createdby,'<a href="https://twitter.com/jan_ainali" style="font-size:14px; line-height: 14px; color:#666; text-decoration:none;" rel="author">Jan Ainali</a><br />
    ', $followimage,'<a href="https://', $lang,'.wikipedia.org/w/index.php?title=', $answer, '&action=history" style="font-size:14px; line-height: 14px; color:#666; text-decoration:none;">', $extract,'</a>', $under,'<a href="http://creativecommons.org/licenses/by-sa/3.0/deed.', $lang,'" style="font-size:14px; line-height: 14px; color:#666; text-decoration:none;" rel="license">CC BY-SA 3.0.</a><br />
    ', $sourcecode,'<a href="https://creativecommons.org/publicdomain/zero/1.0/deed.', $lang,'" style="font-size:14px; line-height: 14px; color:#666; text-decoration:none;" rel="license">CC 0</a>.<br />
    <a href="https://donate.wikimedia.org/w/index.php?title=Special:FundraiserLandingPage&uselang=', $lang,'" style="font-size:14px; line-height: 14px; color:#666; text-decoration:none;" alt="', $donate,'">', $donate,'</a></footer>';
    } else {
    		echo '<p><b>', $noimages,'</b></p>';
   		};
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-57307982-1', 'auto');
  ga('send', 'pageview');
</script>
</body>
</html>
