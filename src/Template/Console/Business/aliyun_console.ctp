<!DOCTYPE html>
<html>
<head>
  <title>
    管理终端
  </title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <!-- Apple iOS Safari settings -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <?= $this->Html->css(['bootstrap.css','/js/aliyunConsole/scripts/include/base.css']);  ?>
  <script>
    var INCLUDE_URI = 'scripts/include/';
  </script>
  
     <?= $this->Html->script(['jQuery-2.1.3.min.js','aliyunConsole/scripts/include/util.js','aliyunConsole/scripts/include/des.js','aliyunConsole/scripts/include/webutil.js','aliyunConsole/scripts/include/base64.js','aliyunConsole/scripts/include/websock.js','aliyunConsole/scripts/include/inflator.js','aliyunConsole/scripts/include/keysym.js','aliyunConsole/scripts/include/keysymdef.js','aliyunConsole/scripts/include/keyboard.js','aliyunConsole/scripts/include/input.js','aliyunConsole/scripts/include/display.js','aliyunConsole/scripts/include/jsunzip.js','aliyunConsole/scripts/include/rfb.js']); ?>



  <style>
    #noVNC_screen{
      line-height: 0px;
      font-size:0px;
      padding:6px;
    }
  </style>
</head>

<body style="margin: 5px;">

<div class="row">

<a href="javascript:;" id="ctrlaltdel" class="btn btn-primary" style="position:relative;left:100px;">发送ctrl+alt+del</a>
  <div class="col-sm-12">
    <div id="noVNC_status" class="pull-left" style="padding-left:12px;">
      载入中……
    </div>
    <div id="noVNC_screen">
      <canvas id="noVNC_canvas" width="640px" height="400px">
        浏览器版本太低。请更新浏览器。
      </canvas>
    </div>
  </div>
</div>
  <?= $this->Html->script(['aliyunConsole/scripts/main.js']); ?>
  <script>
  var password = '<?php echo $data["password"]?>';
 $(function() {
   connectToVncServer('<?php echo $data["url"]?>','<?php echo $data["password"]?>');
   $("#ctrlaltdel").click(function(){
   	rfb.sendCtrlAltDel()
   })
 })
</script>
</body>
</html>
