<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>管理后台<?php echo empty($_title) ? '' : '-'.$_title;?></title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="<?php echo staticUrl('computer/common', 'css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo staticUrl('computer/bootstrap', 'css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo staticUrl('computer/datepicker', 'css');?>">
    <link rel="stylesheet" type="text/css" href="<?php echo staticUrl('computer/space', 'css');?>">
    <?php foreach (\frame\Html::getCss() as $value) { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $value;?>">
    <?php }?>
    <script type="text/javascript" src="<?php echo staticUrl('jquery', 'js');?>"></script>
    <script type="text/javascript" src="<?php echo staticUrl('common', 'js');?>"></script>
    <script type="text/javascript" src="<?php echo staticUrl('button', 'js');?>"></script>
    <?php foreach (\frame\Html::getJs() as $value) { ?>
    <script type="text/javascript" src="<?php echo $value;?>"></script>
    <?php }?>
</head>
<body>
<script type="text/javascript">
var URI = "<?php echo env('APP_DOMAIN');?>";
</script>
<?php if (!empty($_nav)) {?>
<div id="header-nav" class="container-fluid">
    <div class="nav">
        <span><?php echo implode(' &gt; ', $_nav);?></span>
        <a href="<?php echo url();?>" class="glyphicon glyphicon-repeat ml12" title="重新加载"></a>
        <a href="<?php echo url();?>" target="_blank" class="glyphicon glyphicon-link ml12" title="新页面打开"></a>
    </div>
</div>
<?php } ?>