<!-- 首页页面布局 -->
<?= $this->element('seo',['_request_params',$_request_params]); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" > <!--<![endif]-->
<head>
  <?= $this->Html->charset() ?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <?= $this->Html->meta('icon') ?>
  <title>
    <?= $this->fetch('title') ?>_<?= $system_info['name'] ?>
  </title>
  <meta name="viewport" content="width=device-width">


  <!-- Bootstrap CSS -->

  <?= $this->Html->css(['normalize.css','bootstrap.css','bootstrap-table.css','jquery-ui-1.10.0.custom.css','font-awesome.min.css','style.css','styleBack.css','pnotify.core.min.css','swiper.css']); ?>
  <?= $this->Html->script('jQuery-2.1.3.min.js'); ?>


  <?= $this->fetch('meta') ?>
  <?= $this->fetch('css') ?>
  <?= $this->fetch('script') ?>
</head>
<body lang="zh">
  <?= $this->element('header'); ?>
  <div class="clearfix wrap-nav button">
    <?= $this->fetch('content') ?>
  </div>

  <!-- jQuery first, then Bootstrap JS. -->
  <?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap.min.js','bootstrap-table.js','jquery-ui-1.10.0.custom.min.js','plugins.js','jquery.mouse-content.js','jquery.cookie.js','Chart.js','all-page.js','socket/swfobject.js','socket/web_socket.js','pnotify.core.min.js','jquery.cropit.js','swiper.jquery.min.js']); ?>

  <script type="text/javascript" src="/js/socket/reconnecting-websocket.min.js"></script>
  <script>
    WEB_SOCKET_SWF_LOCATION = "/js/socket/WebSocketMain.swf";
    WEB_SOCKET_DEBUG = false;
    var tmpTag = 'https:' == document.location.protocol ? false : true;
    if( "https:" == document.location.protocol ){
      pro ="wss://"+window.location.host
    }else{
      pro = "ws://"+window.location.host
    }
    var conn = new ReconnectingWebSocket(pro+"/ws?uid=<?=$this->Session->read('Auth.User.id')?>");
      // conn = new WebSocket("ws://172.28.30.169:9091/ws?uid=<?=$this->Session->read('Auth.User.id')?>");
      // Set event handlers.
      conn.onopen = function(e) {
            //  alert(e);
            //console.log("open");
          };

          conn.onmessage = function(e) {
        //console.log("start-:"+e);
        data = jQuery.parseJSON(e.data)
        new PNotify({
          text:data.Msg,
          type:data.MsgType,
          styling: "bootstrap3"
        });
        //console.log("end-:"+e);
        try{
          if(notifyCallBack&&typeof(notifyCallBack)=="function"){
            notifyCallBack(data);
          }
        }catch(e){
          //console.log("error-:"+e);
        }
      };
      conn.onclose = function() {
          //console.log("WebSocket closed and retry connect")
          // conn = new WebSocket(pro+"/ws?uid=<?=$this->Session->read('Auth.User.id')?>");
        };
        conn.onerror = function() {
      //alert("onerror");
    };


  </script>
  <?= $this->fetch('script_last') ?>
</body>

</html>