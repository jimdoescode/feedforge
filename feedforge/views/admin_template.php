<!DOCTYPE html>
<html>
<head>
    <title>Feed Forge Admin: <?=$title;?></title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="<?=site_url('assets/admin/admin.css');?>"/>
	<link rel="stylesheet" type="text/css" href="<?=site_url('assets/admin/fancybox/jquery.fancybox.css');?>" media="screen"/>
    <script type="text/javascript">
	var SITE = '<?=site_url();?>';
    </script>
    <script type="text/javascript" src="<?=site_url('assets/admin/jquery.min.js');?>"></script>
	<script type="text/javascript" src="<?=site_url('assets/admin/fancybox/jquery.fancybox.pack.js');?>"></script>
    <script type="text/javascript" src="<?=site_url('assets/admin/admin.js');?>"></script>
</head>
<body>
    <section>
        <header>
            <img id="logo" src="<?=site_url('assets/admin/logo.png');?>" alt=''/>
<?php if($this->uri->segment(2) != 'login'): ?>			
            <ul id="nav">
                <li><a href="<?=site_url('admin/feeds');?>" title="Feed Admin">Feeds</a></li>
                <li class='spacer'>/</li>
                <li><a href="<?=site_url('admin/variables');?>" title="Variable Admin">Variables</a></li>
                <li class='spacer'>/</li>
                <li><a href="<?=site_url('admin/config');?>" title="Config Admin">Config</a></li>
				<li class='spacer'>/</li>
                <li><a href="<?=site_url('admin/logout');?>" title="Logout">Logout</a></li>
            </ul>
<?php endif; ?>
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