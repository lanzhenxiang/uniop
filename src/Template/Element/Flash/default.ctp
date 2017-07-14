<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
?>
<div id="flash-msg" class="<?= h($class) ?>"><?= h($message) ?>111111111111111111111111111</div>