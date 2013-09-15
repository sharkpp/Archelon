<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php !isset($title) ?: print(e($title) . ' - ') ?>Archelon, The account aggregation service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <?php $postfix = \Fuel::PRODUCTION != Fuel::$env ? '' : '.min'; ?>
    <?php echo Asset::css("bootstrap${postfix}.css"); ?>
    <?php echo Asset::css("bootstrap-responsive${postfix}.css"); ?>
    <?php echo Asset::css("font-awesome${postfix}.css"); ?>
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="../assets/ico/favicon.png">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="<?php echo Uri::create('/'); ?>">Archelon</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
<?php if (Auth::check()): /* ログイン済み ------------------------------------------------ */ ?>
              <li class="<?php echo ''==Uri::string()?'active':''; ?>"><a href="<?php echo Uri::create('/'); ?>"><i class="icon-home"></i> ホーム</a></li>
<?php endif; /* ------------------------------------------------ */ ?>
              <li><a href="#about">About</a></li>
<?php if (Auth::check()): /* ログイン済み ------------------------------------------------ */ ?>
              <li class="<?php echo 'account/connect'==Uri::string()?'active':''; ?>"><a href="<?php echo Uri::create('account/connect'); ?>"><i class="icon-plus-sign"></i> アカウント追加</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i> 管理 <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo Uri::create('connector/reload'); ?>" data-toggle="modal" data-target="#modal_connector_reload"><i class="icon-refresh"></i> コネクタの再読み込み</a></li>
                  <li><a href="<?php echo Uri::create('connector/config'); ?>"><i class="icon-cog"></i> コネクタの管理</a></li>
<!--
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
-->
                </ul>
              </li>
<?php endif; /* ------------------------------------------------ */ ?>
            </ul>
<?php if (Auth::check()): /* ログイン済み ------------------------------------------------ */ ?>
            <ul class="nav pull-right">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <?php echo Auth::instance()->get_screen_name(); ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#"><i class="icon-cog"></i> 設定</a></li>
                  <li class="divider"></li>
                  <li><a href="<?php echo Uri::create('signout'); ?>"><i class="icon-signout"></i> サインアウト</a></li>
                </ul>
              </li>
            </ul>
<?php else: /* 未ログイン ------------------------------------------------ */ ?>
            <ul class="nav pull-right">
              <li class="<?php echo 'signup'==Uri::string()?'active':''; ?>"><a href="<?php echo Uri::create('signup'); ?>">サインアップ</a></li>
              <li class="<?php echo 'signin'==Uri::string()?'active':''; ?>"><a href="<?php echo Uri::create('signin'); ?>"><i class="icon-signin"></i> サインイン</a></li>
            </ul>
<?php endif; /* ------------------------------------------------ */ ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

<?php if (isset($breadcrumb)): ?>
    <ul class="breadcrumb">
<?php $i = 0; foreach (array_merge(array('ホーム' => '/'), $breadcrumb) as $name => $segment): ?>
<?php if ($i < count($breadcrumb)): ?>
<?php if (empty($segment)): ?>
      <li class="active"><?php echo $name; ?> <span class="divider">/</span></li>
<?php else: ?>
      <li><a href="<?php echo Uri::create($segment); ?>"><?php echo $name; ?></a> <span class="divider">/</span></li>
<?php endif; ?>
<?php else: ?>
      <li class="active"><?php echo $name; ?></a></li>
<?php endif; ?>
<?php $i++; endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (Session::get_flash('error_message', null, true)): ?>
<div class="alert alert-error"><?php echo Session::get_flash('error_message'); ?></div>
<?php endif; ?>
<?php if (Session::get_flash('success_message', null, true)): ?>
<div class="alert alert-success"><?php echo Session::get_flash('success_message'); ?></div>
<?php endif; ?>

<?php echo $content; ?>

<div id="modal_connector_reload" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-header">
    <h3>コネクタ一覧の更新</h3>
  </div>
  <div class="modal-body">
    <p class="text-center"><?php echo Asset::img("preloader.gif"); ?> しばらくお待ちください...</p>
  </div>
  <div class="modal-footer">
    <button class="btn disabled">閉じる</button>
  </div>
</div>

      <hr>

      <footer>
        <p>&copy; sharkpp 2013</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php echo Asset::js("jquery-1.10.1${postfix}.js"); ?>
    <?php echo Asset::js("bootstrap${postfix}.js"); ?>
    <?php echo isset($script) ? $script : ''; ?>

  </body>
</html>
