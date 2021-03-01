<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" type="text/css" href="<?php echo staticUrl('Common/Hui/hui.min', 'css');?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo staticUrl('Common/Hui/iconfont.min', 'css');?>" />
    <link rel="shortcut icon" href="<?php echo APP_DOMAIN;?>favicon.ico" />
    <?php foreach (\frame\Html::getCss() as $value) { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $value;?>" />
    <?php }?>
    <script type="text/javascript" src="<?php echo staticUrl('Common/jquery.min', 'js');?>"></script>
    <script type="text/javascript" src="<?php echo staticUrl('Common/hui.min', 'js');?>"></script>
    <?php foreach (\frame\Html::getJs() as $value) { ?>
    <script type="text/javascript" src="<?php echo $value;?>"></script>
    <?php }?>
</head>