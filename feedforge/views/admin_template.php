<!DOCTYPE html>
<html>
<head>
    <title>Feed Forge Admin: <?=$title;?></title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="<?=site_url('assets/css/admin.css');?>"/>
    <script type="text/javascript">
	var SITE = '<?=site_url();?>';
    </script>
    <script type="text/javascript" src="<?=site_url('assets/js/jquery.min.js');?>"></script>
    <script type="text/javascript" src="<?=site_url('assets/js/admin.js');?>"></script>
</head>
<body>
    <section>
        <header>
            <img id="logo" src="<?=site_url('assets/images/admin/logo.png');?>" alt=''/>
            <ul id="nav">
                <li><a href="<?=site_url('admin/feeds');?>" title="">Feeds</a></li>
                <li class='spacer'>/</li>
                <li><a href="<?=site_url('admin/entries');?>" title="">Entries</a></li>
                <li class='spacer'>/</li>
                <li><a href="<?=site_url('admin/variables');?>" title="">Variables</a></li>
                <li class='spacer'>/</li>
                <li><a href="<?=site_url('admin/config');?>" title="">Config</a></li>
            </ul>
        </header>
    </section>
    <section id="main">
        <?=$page;?>
    </section>
    <section>
        <footer>
        
        </footer>
    </section>
</body>
</html>