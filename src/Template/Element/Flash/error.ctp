<div class="message error"><?= h($message) ?><a id="flash-remove" class="pull-right" style="color:#fff;cursor: pointer;">x</a></div>

<script type="text/javascript">
    $("#flash-remove").click(function () {
        $(this).parent().slideUp();
    })
</script>