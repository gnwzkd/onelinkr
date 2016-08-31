<?php?>
<?php if(is_ajax()):  ?>
  <?php 
  $url = 'https://storage.live.com/items/'.getUrlParam('resid');
  $fp_input = fopen($url, 'r');
  if (!$fp_input) {
    die('打开远程文件失败！');
  }
  while (!feof($fp_input)) {
    $content .= fread($fp_input, 1024);
  }
  echo $content;
  fclose($fp_input);
  ?>
  <?php return;?>
<?php endif ?>
<?php
function parseUrlParam($query){
  $queryArr = explode('&', $query);
  $params = array();
  if($queryArr[0] !== ''){
    foreach( $queryArr as $param ){
      list($name, $value) = explode('=', $param);
      $params[urldecode($name)] = urldecode($value);
    }       
  }
  return $params;
}
function setUrlParams($cparams, $url = ''){
  $parse_url = $url === '' ? parse_url($_SERVER["REQUEST_URI"]) : parse_url($url);
  $query = isset($parse_url['query']) ? $parse_url['query'] : '';
  $params = parseUrlParam($query);
  foreach( $cparams as $key => $value ){
    $params[$key] = $value;
  }
  return $parse_url['path'].'?'.http_build_query($params);
}
function getUrlParam($cparam, $url = ''){
  $parse_url = $url === '' ? parse_url($_SERVER["REQUEST_URI"]) : parse_url($url);
  $query = isset($parse_url['query']) ? $parse_url['query'] : '';
  $params = parseUrlParam($query);
  return isset($params[$cparam]) ? $params[$cparam] : '';
}
function is_ajax(){
  if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return true;
    }
  }
  return false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OneLinkr</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/css/materialize.min.css">
    <style>
		i.material-icons {
			float: left;
			margin-right: 10px;
		}
    </style>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.7/js/materialize.min.js"></script>
    <script>
        $(function(){
            $("button").on("click",function(){
                $.ajax({
                    url:"./index.php?resid="+$("input").val(),
                    method:"get",
                    success:function(data){
                        $("ul").html(null);
                        var $data=$("<div>").html(data),
                        $ul=$("ul");
                        $data.find("Items>*").each(function(index,value){
                            var $type=$(value).find("ItemType").html(),
                            $resid=$(value).find("ResourceID").html(),
                            $filename=$(value).find("RelationshipName").html(),
                            $imgLinkCode="";
                            if(hasStr($filename,"jpg")||hasStr($filename,"png")||hasStr($filename,"gif")||hasStr($filename,"JPG")||hasStr($filename,"PNG")||hasStr($filename,"GIF")){
                                $imgLinkCode="<br><img src='https://storage.live.com/items/"+$resid+":Thumbnail/"+$filename+"'><br><textarea  class='materialize-textarea'>"+$("<div>").text('<a href="https://storage.live.com/items/'+$resid+':/'+$filename+'">'+'<img src="https://storage.live.com/items/'+$resid+':WebReady/'+$filename+'"></a>').html()+"</textarea>";
                            }
                            if($type=="Folder"){
                                $ul.prepend("<li class='collection-item folder'><a href='javascript:void(0);' onclick="+$("<div>").text("$('input').val('"+$resid+"');$('button').click();").html()+"><i class='material-icons'>folder</i>"+$filename+"</a></li>");
                            }else{
                                $ul.append("<li class='collection-item file'><a href='https://storage.live.com/items/"+$resid+":/"+$filename+"'><i class='material-icons'>insert_drive_file</i>"+$filename+"</a>"+$imgLinkCode+"</li>");
                            }
                        });
                        var $parentResid=$data.find("ParentResourceID").html();
                        if($parentResid&&$parentResid.length!=0){
                            $ul.prepend("<li class='collection-item parent'><a href='javascript:void(0);' onclick="+$("<div>").text("$('input').val('"+$parentResid+"');$('button').click();").html()+"><i class='material-icons'>arrow_back</i>...</a></li>");
                        }
                    }
                });
            });
        });
        function hasStr(obj,str){
            if(obj.indexOf(str)>-1){
                return true;
            }else{
                return false;
            }
        }
    </script>
</head>
<body class="container">
    <input type="text" placeholder="ResourceID">
    <button  class="waves-effect waves-light btn" type="submit">提交</button>
    <ul class="collection"></ul>
</body>
</html>
