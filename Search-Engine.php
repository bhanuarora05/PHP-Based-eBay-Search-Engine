<!DOCTYPE html>
<html>
<head>
<style>
body {
	font-size: 1em;
	font-family: Times, serif;
}
div.ex1{
	margin-left:auto;
	margin-right:auto;
	margin-top:1.09375em;
	width:35.7875em;
	height:16.7875em;
	background: #f8f8f8;
	border-width: 0.2em;
	border-style: solid;
	border-color: #c8c8c8;
}
div.ex1 hr{
	width:34.6875em;
	border-width: 1px;
	border-style: solid;
	border-color: #b8b8b8;
	margin-top: 0.4375em;
}
input,select {
	font-size: 0.6875em;
	font-family: Arial, Helvetica, sans-serif;
	border-width: 1.5px;
	border-style: solid;
	border-color: #D3D3D3;
}
input:disabled{
	background:#f8f8f8;
}
input[type=submit],input[type=reset]{
	background: #FFFFFF;
}
label,span {
	font-weight: bold;
}
<?php if(!isset($_GET['search'])):?>
span {
	color:#7f7f7f;
}
	<?php endif;?>
a:hover {
	color: #7f7f7f;
}
a {
	text-decoration: none;
	color: black;
}
#table {
	margin-top: 20px;
}
#table td,#table th{
	border-width: 2px;
	border-style: solid;
	border-color: #dcdcdc;
	padding: 0;
}
#table img{
	padding-left: 1px;
	padding-right: 1px;
	padding-top: 1px;
	padding-bottom: 1px;
	width: 5em;
	display: block;
}
.hidden{
	display: none;
}
#ifrm{
	padding: 0;
	margin-top: 0;
	margin-bottom: 0;
	margin-left: auto;
	margin-left: auto;
}
#simblock td{
	padding-top:0;
	padding-bottom: 0;
	padding-left: 21px;
	padding-right: 21px;
	text-align: center;
}
</style>
</head>
<body>
<?php
$json_err = "";
$json_data = "";
$json_err2 = "";
$json_data2 = "";
if(isset($_GET['productid'])){
	$url = "http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=JSON&appid=BhanuAro-WebTech-PRD-116e557a4-e7b7ee98&siteid=0&version=967&ItemID=".$_GET['productid']."&IncludeSelector=Description,Details,ItemSpecifics";
	$json = json_decode(file_get_contents($url),true);
    
    if($json['Ack']=='Failure'){
    	$json_err = $json['Errors'][0]['ShortMessage'];
    }
    else{
    	$json_data = json_encode($json['Item']);
    }
	$url = "http://svcs.ebay.com/MerchandisingService?OPERATION-NAME=getSimilarItems&SERVICE-NAME=MerchandisingService&SERVICE-VERSION=1.1.0&CONSUMER-ID=BhanuAro-WebTech-PRD-116e557a4-e7b7ee98&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&itemId=".$_GET['productid']."&maxResults=8";
	$json = json_decode(file_get_contents($url),true);
	if($json['getSimilarItemsResponse']['ack']=='Failure'){
		$json_err2 = "<table style='width:100%;border-width: 2px; border-style: solid; border-color: #dcdcdc;margin:8px;font-size:20px;'><tr><td style='text-align:center;'><b>No Similar Item found.</b></td></table>";
    }
    elseif(!array_key_exists('item',$json['getSimilarItemsResponse']['itemRecommendations'])){
    	$json_err2 = "<table style='width:100%;border-width: 2px; border-style: solid; border-color: #dcdcdc;margin:8px;font-size:20px;'><tr><td style='text-align:center;'><b>No Similar Item found.</b></td></table>";
    }
    elseif(count($json['getSimilarItemsResponse']['itemRecommendations']['item'])==0){
    	$json_err2 = "<table style='width:100%;border-width: 2px; border-style: solid; border-color: #dcdcdc;margin:8px;font-size:20px;'><tr><td style='text-align:center;'><b>No Similar Item found.</b></td></table>";
    }
    else{
    	$json_data2 = json_encode($json['getSimilarItemsResponse']['itemRecommendations']['item']);
    }
}
elseif(isset($_GET["submit"])){
	$url="http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&SERVICE-VERSION=1.0.0&SECURITY-APPNAME=BhanuAro-WebTech-PRD-116e557a4-e7b7ee98&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&paginationInput.entriesPerPage=20";
	$keyword = $_GET["keyword"];
	$url.='&keywords='.rawurlencode($keyword);
	$cat=$_GET['category'];
	if($cat!="allcategories"){
		$url.='&categoryId='.$cat;
	}
	$j=0;
	if(isset($_GET["search"])){
		if(isset($_GET["zip"]) && ($_GET['zip']=='here')){
			$zipcode=$_GET["zipcode"];
			$url.='&buyerPostalCode='.$zipcode;
		}
		elseif(isset($_GET["zip"]) && $_GET['zip']=='zip'){
			$ziptext=$_GET["ziptext"];
			$url.='&buyerPostalCode='.$ziptext;
		}
		if($_GET["dist"]!=""){
			$dist=$_GET["dist"];
			$url.='&itemFilter('.$j.').name=MaxDistance&itemFilter('.$j.').value='.$dist;
		}
		else{
			$url.='&itemFilter('.$j.').name=MaxDistance&itemFilter('.$j.').value=10';
		}
		$j++;
	}
	
	if(isset($_GET["shipping"])){
		$url.='&itemFilter('.$j.').name=FreeShippingOnly&itemFilter('.$j.').value=true';
		$j++;
	 }
	
	 
	 if(isset($_GET["pickup"])){
		$url.='&itemFilter('.$j.').name=LocalPickupOnly&itemFilter('.$j.').value=true';
		$j++;
	 }
	
	 $url.='&itemFilter('.$j.').name=HideDuplicateItems&itemFilter('.$j.').value=true';
	 $j++;
	if(isset($_GET["condition"])){
	 	$cond = $_GET['condition'];
	 	$i=0;
	 	$url.='&itemFilter('.$j.').name=Condition';
	 
    	foreach ($cond as $condition){
         	$url.='&itemFilter('.$j.').value('.($i++).')='.$condition;
    	}	
	}
	
    $json = json_decode(file_get_contents($url),true);
    if($json['findItemsAdvancedResponse'][0]['ack'][0]=='Failure'){
    	$json_err = $json['findItemsAdvancedResponse'][0]['errorMessage'][0]['error'][0]['message'][0];
    }
    elseif($json['findItemsAdvancedResponse'][0]['searchResult'][0]['@count']==0){
    	$json_err = "No Records has been found";
    }
    else{
    	$json_data = json_encode($json['findItemsAdvancedResponse'][0]['searchResult'][0]);
    }
}
?>

<script>
function startpage(){
var xmlhttp = new XMLHttpRequest();
var url = "http://ip-api.com/json";
xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var myArr = JSON.parse(this.responseText);
        document.getElementById("sbm").disabled = false;
        document.getElementById("zipcode").value = myArr.zip;
    }
};
xmlhttp.open("GET", url, true);
xmlhttp.send();
params = new URL(window.location.href);
params = params.searchParams
form = document.getElementById("form1");
if(params.get("keyword")){
	form1.keyword.value=params.get("keyword");
}
}
function enable(check) {
document.getElementById("dist").disabled = !check.checked;
document.getElementById("here").disabled = !check.checked;
document.getElementById("zip").disabled = !check.checked;
document.getElementById("ziptext").style.background = "#ffffff";
document.getElementById("dist").style.background = "#ffffff";
document.getElementById("here").style.background = "#ffffff";
document.getElementById("zip").style.background = "#ffffff";
document.getElementById("form1").getElementsByTagName('span')[0].style.color = "#000000";
document.getElementById("form1").getElementsByTagName('span')[1].style.color = "#000000";
if(!check.checked){
	document.getElementById("dist").value="";
	document.getElementById("dist").setAttribute("value","");
	document.getElementById("ziptext").value="";
	document.getElementById("ziptext").setAttribute("value","");
	document.getElementById("here").checked = true;
	document.getElementById("zip").removeAttribute("checked");
	document.getElementById("here").setAttribute("checked",true);
	document.getElementById("ziptext").disabled = true;
	document.getElementById("ziptext").style.background = "#f8f8f8";
	document.getElementById("dist").style.background = "#f8f8f8";
	document.getElementById("here").style.background = "#f8f8f8";
	document.getElementById("zip").style.background = "#f8f8f8";
	document.getElementById("form1").getElementsByTagName('span')[0].style.color = "#7f7f7f";
	document.getElementById("form1").getElementsByTagName('span')[1].style.color = "#7f7f7f";
}
}
function enablezip(check1) {
document.getElementById("ziptext").disabled = !check1.checked;
}
function disablezip(check1) {
document.getElementById("ziptext").disabled = check1.checked;
document.getElementById("ziptext").value = "";
document.getElementById("ziptext").setAttribute("value","");
}
function clr_clr(){
document.getElementById("search").removeAttribute("checked");
document.getElementById("dist").value = "";
document.getElementById("dist").setAttribute("value","");
document.getElementById("ziptext").value = "";
document.getElementById("ziptext").setAttribute("value","");
document.getElementById("dist").disabled = true;
document.getElementById("here").disabled = true;
document.getElementById("zip").disabled = true;
document.getElementById("ziptext").disabled = true;
document.getElementById("here").checked = true;
document.getElementById("zip").removeAttribute("checked");
document.getElementById("here").setAttribute("checked",true);
document.getElementById("ziptext").style.background = "#f8f8f8";
document.getElementById("dist").style.background = "#f8f8f8";
document.getElementById("here").style.background = "#f8f8f8";
document.getElementById("zip").style.background = "#f8f8f8";
document.getElementById("form1").getElementsByTagName('span')[0].style.color = "#7f7f7f";
document.getElementById("form1").getElementsByTagName('span')[1].style.color = "#7f7f7f";
document.getElementById("pickup").removeAttribute("checked");
document.getElementById("shipping").removeAttribute("checked");
document.getElementById("New").removeAttribute("checked");
document.getElementById("Used").removeAttribute("checked");
document.getElementById("Unspecified").removeAttribute("checked");
document.getElementById("category").options[document.getElementById("category").selectedIndex].removeAttribute("selected");
document.getElementById("errmsg").classList.add('hidden');
document.getElementById("table").classList.add('hidden');
if(document.getElementById("simblock")){
	document.getElementById("ifrm").classList.add('hidden');
	document.getElementById("simblock").classList.add('hidden');
	document.getElementById("simshow").classList.add('hidden');
	document.getElementById("simhide").classList.add('hidden');
	document.getElementById("smsgshow").classList.add('hidden');
	document.getElementById("smsghide").classList.add('hidden');
}
}
function checkform(){
	form = document.getElementById("form1");
	if(!form.search.checked){
		return true;
	}
	else{
		if(!form.zip[1].checked){
		}
		else{
			regexp=/^[0-9]{5}$/;
			if(!regexp.test(form.ziptext.value)){
				document.getElementById("errmsg").innerHTML = "<table style='margin-top: "+(4*7)+"px;width: "+(2*361)+"px;background-color: #f0f0f0; border-width: 2px; border-style: solid; border-color: #dcdcdc;border-collapse: collapse;margin-left:auto;margin-right:auto;margin-bottom0;text-align:center;'><tr><td>Zipcode is Invalid</td></tr></table>";
				document.getElementById("errmsg").classList.remove('hidden');
				return false;
			}
		}
		regexp=/^[0-9]*$/;
		if(regexp.test(form.dist.value)){
			if(form.dist.value.length==0){
				return true;
			}
			else if(form.dist.value<5){
				document.getElementById("errmsg").innerHTML = "<table style='margin-top: "+(4*7)+"px;width: "+(2*361)+"px;background-color: #f0f0f0; border-width: 2px; border-style: solid; border-color: #dcdcdc;border-collapse: collapse;margin-left:auto;margin-right:auto;margin-bottom0;text-align:center;'><tr><td>Maximum Distance cannot be Less than 5</td></tr></table>";
				document.getElementById("errmsg").classList.remove('hidden');
				return false;
			}
		}
		else {
			document.getElementById("errmsg").innerHTML = "<table style='margin-top: "+(4*7)+"px;width: "+(2*361)+"px;background-color: #f0f0f0; border-width: 2px; border-style: solid; border-color: #dcdcdc;border-collapse: collapse;margin-left:auto;margin-right:auto;margin-bottom0;text-align:center;'><tr><td>Maximum Distance is Invalid</td></tr></table>";
				document.getElementById("errmsg").classList.remove('hidden');
				return false;
		}
	}
	return true;
}
function smsgshow(){
	document.getElementById("smsghide").classList.remove('hidden');
	document.getElementById("smsgshow").classList.add('hidden');
	document.getElementById("ifrm").classList.remove('hidden');
	if(document.getElementById("ifrm").getElementsByTagName("iframe")[0]){
		document.getElementById("ifrm").style.height = document.getElementById("ifrm").getElementsByTagName("iframe")[0].contentWindow.document.documentElement.scrollHeight+"px";
	}
	simhide();
}
function smsghide(){
	document.getElementById("smsgshow").classList.remove('hidden');
	document.getElementById("smsghide").classList.add('hidden');
	document.getElementById("ifrm").classList.add('hidden');
}
function simshow(){
	document.getElementById("simhide").classList.remove('hidden');
	document.getElementById("simshow").classList.add('hidden');
	document.getElementById("simblock").classList.remove('hidden');
	smsghide();
}
function simhide(){
	document.getElementById("simshow").classList.remove('hidden');
	document.getElementById("simhide").classList.add('hidden');
	document.getElementById("simblock").classList.add('hidden');
}
</script>
<div class="ex1">
<h2 align="center" style="font-style: italic;font-weight: bold;font-size: 2em;font-family: Times, serif;margin:0;">Product Search</h2><hr align="center">
<form action="" method="get" id="form1" style="margin-left: 1.125em;margin-top: 10px;" onsubmit="return checkform()">
<label>Keyword</label> <input type="text" name="keyword" id="keyword" style="margin-bottom: 11px;" required><br>
<label>Category</label> <select name="category" id="category" style="margin-bottom: 12px;">
<option value="allcategories" <?php if(!isset($_GET['category']) || ($_GET['category']=='allcategories')){?> selected <?php } ?>>All Categories</option>
<option value="550" <?php if(isset($_GET['category']) && ($_GET['category']=='550')){?> selected <?php } ?>>Art</option>
<option value="2984" <?php if(isset($_GET['category']) && ($_GET['category']=='2984')){?> selected <?php } ?>>Baby</option>
<option value="267" <?php if(isset($_GET['category']) && ($_GET['category']=='267')){?> selected <?php } ?>>Books</option>
<option value="11450" <?php if(isset($_GET['category']) && ($_GET['category']=='11450')){?> selected <?php } ?>>Clothing, Shoes & Accessories</option>
<option value="58058" <?php if(isset($_GET['category']) && ($_GET['category']=='58058')){?> selected <?php } ?>>Computers/Tablets & Networking</option>
<option value="26395" <?php if(isset($_GET['category']) && ($_GET['category']=='26395')){?> selected <?php } ?>>Health & Beauty</option>
<option value="11233" <?php if(isset($_GET['category']) && ($_GET['category']=='11233')){?> selected <?php } ?>>Music</option>
<option value="1249" <?php if(isset($_GET['category']) && ($_GET['category']=='1249')){?> selected <?php } ?>>Video Games & Consoles</option>
</select><br>
<label>Condition</label>
<input type="checkbox" name="condition[]" value="New" id="New" style="margin-left: 16px;" <?php if(isset($_GET['condition']) && in_array("New", $_GET['condition'])){?> checked <?php } ?>>New
<input type="checkbox" name="condition[]" value="Used" id="Used" style="margin-left: 16px;" <?php if(isset($_GET['condition']) && in_array("Used", $_GET['condition'])){?> checked <?php } ?>>Used
<input type="checkbox" name="condition[]" value="Unspecified" id="Unspecified" style="margin-left: 16px;margin-bottom: 14.5px;" <?php if(isset($_GET['condition']) && in_array("Unspecified", $_GET['condition'])){?> checked <?php } ?>>Unspecified<br>
<label>Shipping Options</label>
<input type="checkbox" name="pickup" value="pickup" id="pickup" style="margin-left: 36px;" <?php if(isset($_GET['pickup'])){?> checked <?php } ?>>Local Pickup
<input type="checkbox" name="shipping" value="shipping" id="shipping" style="margin-left: 36px;" <?php if(isset($_GET['shipping'])){?> checked <?php } ?>>Free Shipping <br>
<div style="margin-top: 11px;float: left;display: inline-block;"><input type="checkbox" name="search" value="search" id="search" onchange="enable(this)" <?php if(isset($_GET['search'])){?> checked <?php } ?>> <label>Enable Nearby Search</label> <input type="text" name="dist" id="dist" placeholder="10" style="margin-left: 20px;width: 47px;" <?php if(isset($_GET['dist'])){?> value= <?php echo $_GET['dist'];} ?> <?php if(!isset($_GET['dist'])){?> disabled <?php } ?>> <span>miles from</span></div><div style="margin-top: 11px;float: left;"><input type="radio" name="zip" value="here" id="here" <?php if((!isset($_GET['zip'])) || (isset($_GET['zip']) && ($_GET['zip']=='here'))){?> checked <?php } ?> <?php if(!isset($_GET['zip'])){?> disabled <?php } ?> onchange="disablezip(this)"><span style="font-weight: normal;">Here</span><br>
<input type="radio" name="zip" value="zip" <?php if(isset($_GET['zip']) && ($_GET['zip']=='zip')){?> checked <?php } ?> <?php if(!isset($_GET['zip'])){?> disabled <?php } ?> id="zip" onchange="enablezip(this)"><input type="hidden" name="zipcode" id="zipcode" value=""><input type="text" name="ziptext" id="ziptext" placeholder="zip code" align="right" <?php if(!isset($_GET['ziptext'])){?> disabled <?php } ?> <?php if(isset($_GET['ziptext'])){?> value= <?php echo $_GET['ziptext'];} ?> <?php if(isset($_GET['search'])){?> style='background:#ffffff;' <?php } ?> required></div>
<input type="submit" name="submit" id="sbm" value="Search" style="margin-left: 196px;margin-top: 20px;border-radius: 3px;clear: both;" disabled >
<input type="reset" name="clear" id="clr" value="Clear" onclick="clr_clr()">
</form>

</div>
<script type="text/javascript">document.onload= startpage();</script>
<div id="errmsg" <?php if($json_err==""){?> class='hidden' <?php } ?>>
<?php
if(!($json_err=="")){
	echo "<table style='margin-top: ".(4*7)."px;width: ".(2*361)."px;background-color: #f0f0f0; border-width: 2px; border-style: solid; border-color: #dcdcdc;border-collapse: collapse;margin-left:auto;margin-right:auto;margin-bottom0;text-align:center;'><tr><td>".$json_err."</td></tr></table>";
}
?>
</div>
<div id="table"></div>
<?php if($json_err==""):
	if(isset($_GET['productid'])):
?>
<?php
	$html_text = "<div style='text-align:center;' id='smsgshow'><button style='font-size: 1em;font-family: Times, serif;margin-top: ".(4*7)."px;background-color: #FFFFFF;color: #bebebe;border: none;' onclick='smsgshow()'>click to show seller message<br><img style='width: ".(4*10)."px;height: ".(9*2)."px;margin-top: ".(4*3)."px;display: inline;' src='http://csci571.com/hw/hw6/images/arrow_down.png'/></button></div>";
	$html_text .= "<div class='hidden' id='smsghide' style='text-align:center;'><button style='font-size: 1em;font-family: Times, serif;margin-top: ".(4*7)."px;background-color: #FFFFFF;color: #bebebe;border: none;' onclick='smsghide()'>click to hide seller message<br><img style='width: ".(4*10)."px;height: ".(9*2)."px;margin-top: ".(4*3)."px;display: inline;' src='http://csci571.com/hw/hw6/images/arrow_up.png'/></button></div>";
	$json_temp = json_decode($json_data,true);
	if(array_key_exists('Description',$json_temp)){
		if(strlen($json_temp['Description'])>0){
			$html_text .= "<div class='hidden' id='ifrm' style='height: 100%;margin-top: ".(4*2)."px;margin-left:auto;margin-right:auto;margin-bottom:0; width: ".(10*100)."px;'><iframe style='width:100%;height:100%;' frameborder='0' scrolling='no'></iframe></div>";
		}
		else{
			$html_text .= "<table class='hidden' id='ifrm' style='margin-top: ".(4*7)."px;width: ".(2*361)."px;background-color: #f0f0f0; border-width: 2px; border-style: solid; border-color: #dcdcdc;border-collapse: collapse;margin-left:auto;margin-right:auto;margin-bottom:0;text-align:center;'><tr><td><b>No Seller Message found.</b></td></tr></table>";
		}
	}
	else{
		$html_text .= "<table class='hidden' id='ifrm' style='margin-top: ".(4*7)."px;width: ".(2*361)."px;background-color: #f0f0f0; border-width: 2px; border-style: solid; border-color: #dcdcdc;border-collapse: collapse;margin-left:auto;margin-right:auto;margin-bottom:0;text-align:center;'><tr><td><b>No Seller Message found.</b></td></tr></table>";
	}
	$html_text .= "<div style='text-align:center;' id='simshow'><button style='font-size: 1em;font-family: Times, serif;margin-top: ".(4*7)."px;background-color: #FFFFFF;color: #bebebe;border: none;' onclick='simshow()'>click to show similar items<br><img style='width: ".(4*10)."px;height: ".(9*2)."px;margin-top: ".(4*3)."px;display: inline;' src='http://csci571.com/hw/hw6/images/arrow_down.png'/></button></div>";
	$html_text .= "<div class='hidden' id='simhide' style='text-align:center;'><button style='font-size: 1em;font-family: Times, serif;margin-top: ".(4*7)."px;background-color: #FFFFFF;color: #bebebe;border: none;' onclick='simhide()'>click to hide similar items<br><img style='width: ".(4*10)."px;height: ".(9*2)."px;margin-top: ".(4*3)."px;display: inline;' src='http://csci571.com/hw/hw6/images/arrow_up.png'/></button></div>";
	echo $html_text;
?>
<div id="simblock" class="hidden" style="width: 766px;border-width: 2px;border-style: solid; border-color: #dcdcdc ;margin-bottom: 25px;margin-left:auto;margin-right:auto;overflow-x: auto;margin-top: 8px;">
<?php
	if($json_err2!=""){
		echo $json_err2;
	}
?>
</div>
<script>
	json_data = <?php echo $json_data;?>;
	//console.log(json_data);
	html_text="<table style='margin:0 auto;border-width: 2px;border-style: solid;border-color: #dcdcdc;border-collapse: collapse;'>"; 
	html_text+="<caption style='font-size:35px;font-weight:bold;margin-top:8px;'>Item Details</caption>";
	html_text+="<tbody>";
	if(json_data.hasOwnProperty('PictureURL')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Photo"+"</td>"+"<td><img style='width:auto;padding:0;height:194px;' src='"+json_data['PictureURL'][0]+"'></td>"+"</tr>";
	}
	else;
	if(json_data.hasOwnProperty('Title')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Title"+"</td>"+"<td style='padding-left:11px;'>"+json_data['Title']+"</td>"+"</tr>";
	}
	else;
	if(json_data.hasOwnProperty('Subtitle')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Subtitle"+"</td>"+"<td style='padding-left:11px;'>"+json_data['Subtitle']+"</td>"+"</tr>";
	}
	else;
	if(json_data.hasOwnProperty('CurrentPrice')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Price"+"</td>"+"<td style='padding-left:11px;'>"+json_data['CurrentPrice']['Value']+" "+json_data['CurrentPrice']['CurrencyID']+"</td>"+"</tr>";
	}
	else;
	if(json_data.hasOwnProperty('Location') && json_data.hasOwnProperty('PostalCode')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Location"+"</td>"+"<td style='padding-left:11px;'>"+json_data['Location']+", "+json_data['PostalCode']+"</td>"+"</tr>";
	}
	else if(json_data.hasOwnProperty('Location')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Location"+"</td>"+"<td style='padding-left:11px;'>"+json_data['Location']+"</td>"+"</tr>";
	}
	else;
	if(json_data.hasOwnProperty('Seller')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Seller"+"</td>"+"<td style='padding-left:11px;'>"+json_data['Seller']['UserID']+"</td>"+"</tr>";
	}
	else;
	if(json_data.hasOwnProperty('ReturnPolicy') && json_data['ReturnPolicy'].hasOwnProperty('ReturnsWithin')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Return Policy(US)"+"</td>"+"<td style='padding-left:11px;'>"+json_data['ReturnPolicy']['ReturnsAccepted']+" within "+json_data['ReturnPolicy']['ReturnsWithin']+"</td>"+"</tr>";
	}
	else if(json_data.hasOwnProperty('ReturnPolicy')){
		html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+"Return Policy(US)"+"</td>"+"<td style='padding-left:11px;'>"+json_data['ReturnPolicy']['ReturnsAccepted']+"</td>"+"</tr>";
	}
	else;
	if(json_data.hasOwnProperty('ItemSpecifics')){
		for(i=0;json_data['ItemSpecifics'].hasOwnProperty('NameValueList') && i<json_data['ItemSpecifics']['NameValueList'].length;i++){
			html_text+="<tr><td style='padding-left:11px;font-weight:bold;'>"+json_data['ItemSpecifics']['NameValueList'][i]['Name']+"</td>"+"<td style='padding-left:11px;'>"+json_data['ItemSpecifics']['NameValueList'][i]['Value']+"</td>"+"</tr>";
		}
	}
	else;
	html_text+="</tbody>";
	html_text+="</table>";
	
	if(json_data.hasOwnProperty('Description')){
		if(json_data['Description'].length>0){
			document.getElementById("ifrm").getElementsByTagName("iframe")[0].contentWindow.document.write(json_data['Description']);
		}
	}
	document.getElementById("table").innerHTML= html_text;
	<?php if($json_err2==""){?>
	json_data = <?php echo $json_data2;?>;
	//console.log(json_data);
	html_text = "<table><tr>";
	for(i=0;i<json_data.length;i++){
		html_text += "<td style='min-width:"+(160+5)+"px;'>";
		if(json_data[i].hasOwnProperty('imageURL')){
			html_text += "<img width='auto' height='"+(7*20)+"px' src='"+json_data[i]['imageURL']+"'/>";
		}
		else;
		html_text += "<br>";
		var params = new URL(window.location.href);
		params.searchParams.set('productid',json_data[i]['itemId']);
		html_text += "<a href='"+params.href+"'>";
		if(!json_data[i].hasOwnProperty('title')){
			html_text += "N/A";
		}
		else{
			html_text += json_data[i]['title'];
		}
		html_text += "</a><br><br>";
		if(!json_data[i].hasOwnProperty('buyItNowPrice')){
			html_text += "<b>N/A</b>";
		}
		else{
			html_text += "<b>$"+json_data[i]['buyItNowPrice']['__value__']+"</b>";
		}
		html_text += "</td>";
	}
	html_text += "</tr></table>";
	document.getElementById("simblock").innerHTML = html_text;
	<?php }?>
</script>
<?php elseif(isset($_GET['submit'])):?>
<script>
	json_data = <?php echo $json_data;?>;
	//console.log(json_data);
	//console.log(json_data['item'][0]['galleryURL'][0]);
	html_text="<table style='margin:0 auto;width:75.1875em;border-width: 2px;border-style: solid;border-color: #dcdcdc;border-collapse: collapse;margin-bottom: 1.5625em;'>"; 
	html_text+="<tbody>";
	html_text+="<tr>";
	html_text+="<th>Index</th><th>Photo</th><th>Name</th><th>Price</th><th>Zip code</th><th>Condition</th><th>Shipping Option</th></tr>";
	for(i=0;i<json_data['item'].length;i++){
		html_text+="<tr>";
		html_text+="<td>"+(i+1)+"</td>"
		if(!json_data['item'][i].hasOwnProperty('galleryURL')){
			html_text+="<td>N/A</td>";
		}
		else{
			html_text+="<td style='width:"+(2*41)+"px;'><img src='"+json_data['item'][i]['galleryURL'][0]+"'></td>"; 
		}
		if(!json_data['item'][i].hasOwnProperty('title')){
			var params = new URL(window.location.href);
			params.searchParams.set('productid',json_data['item'][i]['itemId'][0]);
			html_text+="<td><a href='"+params.href+"' >N/A</a></td>";
		}
		else{
			var params = new URL(window.location.href);
			params.searchParams.set('productid',json_data['item'][i]['itemId'][0]);
			html_text+="<td><a href='"+params.href+"' >"+json_data['item'][i]['title'][0]+"</a></td>";
		}
		if(!json_data['item'][i].hasOwnProperty('sellingStatus')){
			html_text+="<td>N/A</td>";
		}
		else{
			html_text+="<td>$"+json_data['item'][i]['sellingStatus'][0]['currentPrice'][0]['__value__']+"</td>";
		}
		if(!json_data['item'][i].hasOwnProperty('postalCode')){
			html_text+="<td>N/A</td>";
		}
		else{
			html_text+="<td>"+json_data['item'][i]['postalCode'][0]+"</td>";
		}
		if(!json_data['item'][i].hasOwnProperty('condition')){
			html_text+="<td>N/A</td>";
		}
		else{
			html_text+="<td>"+json_data['item'][i]['condition'][0]['conditionDisplayName'][0]+"</td>";
		}
		if(!json_data['item'][i].hasOwnProperty('shippingInfo')){
			html_text+="<td>N/A</td>";
		}
		else if(json_data['item'][i]['shippingInfo'][0].hasOwnProperty('shippingServiceCost')){
			if(json_data['item'][i]['shippingInfo'][0]['shippingServiceCost'][0]['__value__']== 0.0){
			 html_text+="<td>Free Shipping</td>";	
			}
			else{
				html_text+="<td>$"+json_data['item'][i]['shippingInfo'][0]['shippingServiceCost'][0]['__value__']+"</td>"
			}
			
		}
		else{
			html_text+="<td>N/A</td>";
		}
		html_text+="</tr>";
	}
	html_text+="</tbody>";
	html_text+="</table>";
	document.getElementById("table").innerHTML= html_text;
</script>
<?php endif;
endif;?>
</body>
</html>