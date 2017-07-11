<?= $this->element('network/lists/left', ['active_action' => 'host']); ?>
<div class="wrap-nav-right">
    <div>
        <input type="button" value="点我啊啊啊啊啊啊啊啊啊啊啊啊啊啊" onclick="test()" />
    </div>
</div>
<script>

function test(){
        $.ajax({
        url: "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "Hosts" ?>/<?php echo "test" ?>",
        async: false,
        data: {method:"test",uid:"92"},
        method: 'post',
        dataType: 'json',
        success: function(e) {
            //操作成功

        }
    });
}

</script>
</body>
</html>