// 初始化
var rfb = new RFB({
  'target': $D('noVNC_canvas'),
  'encrypt': WebUtil.getQueryVar('encrypt',
    (window.location.protocol === "https:")),
  'repeaterID': WebUtil.getQueryVar('repeaterID', ''),
  'true_color': WebUtil.getQueryVar('true_color', true),
  'local_cursor': WebUtil.getQueryVar('cursor', true),
  'shared': WebUtil.getQueryVar('shared', true),
  'view_only': WebUtil.getQueryVar('view_only', false),
  //'onKeyUpdateMonitor': onKeyUpdateMonitor,
  'updateState': updateState
});

/**
 * 状态变更时的回调函数，需要重点处理的是password状态。
 * @param rfb
 * @param state
 * @param oldstate
 * @param msg
 */
function updateState(rfb, state, oldstate, msg) {
  var statusIndicator, level;
  var VNC_STATES = {
    failed: 'failed',
    fatal: 'fatal',
    normal: 'normal',
    disconnected: 'disconnected',
    loaded: 'loaded',
    password: 'password'
  };
  statusIndicator = $D('noVNC_status');
  switch (state) {
    case 'failed':
      level = "error";
      break;
    case 'fatal':
      level = "error";
      break;
    case 'normal':
      level = "normal";
      break;
    case 'disconnected':
      level = "normal";
      break;
    case 'loaded':
      level = "normal";
      break;
    default:
      level = "warn";
      break;
  }

  // 需要重点关注
  if (state === VNC_STATES.password) {
    sendVncPassword()
  }
  if (VNC_STATES[state] != undefined) {
    if (typeof(msg) !== 'undefined') {
      statusIndicator.innerHTML = msg;
    }
  }
}

/**
 * 发送password，发送前先要断开。
 */
function sendVncPassword(){

  rfb.disconnect();
  alert(password);
  rfb.sendPassword(password);
}

/**
 * 连接vnc
 */
function connectToVncServer(url,password){
  //var vncUrl = decodeURIComponent(getVncServerUrl());
  var uri = url;
  sendConnectCommand(uri,password)
}

/**
 * 连接前要断开
 * @param uri
 */
function sendConnectCommand (uri,password) {
  rfb.disconnect();
  rfb.connect(uri, 5900,password);
}

/**
 * 获取vnc地址，这里用显式的方式简单化处理，实际开发中需要从server端获取地址。
 * @returns {*|jQuery}
 */
function getVncServerUrl (){
  return $('#vncAddr').val()
}

function urlWrapper(websocketUrl) {
  var needWss = false;
  if (websocketUrl) {
    var protocol = window.location.protocol;
    if (protocol.substring(0, 5) == 'https') {
      needWss = true;
    }
    var isWss = websocketUrl.substring(0, 3).toUpperCase() == 'WSS';

    if (needWss) {
      if (isWss) {
        return websocketUrl;
      } else {
        return 'wss' + websocketUrl.substring(2)
      }
    } else {
      if (isWss) {
        return 'ws' + websocketUrl.substring(3);
      } else {
        return websocketUrl
      }
    }
  }
  return websocketUrl;
}

$('#btnConnect').on('click', function(){
  connectToVncServer()
});
