<style type="text/css">
body {
	background-image: url(<?php echo Asset::get_file('archelon-bg.png', 'img'); ?>);
	background-repeat: no-repeat;
	background-position: center center;
}
</style>

<div class="hero-unit">
  <h1>Archelonへようこそ!</h1>
  <p>Archelon は アカウントアグリゲーションサービスの一種です。REST APIなどなどウェブAPIを用意していないウェブアプリケーションにAPIを追加するシステムです。</p>
  <p><a href="<?php echo Uri::create('about'); ?>" class="btn btn-primary btn-large">もっと詳しく &raquo;</a></p>
</div>

<!-- Example row of columns -->
<div class="row">
  <div class="span4">
    <h2>始めるには</h2>
    <p>まず、サインアップしてユーザーを登録してください。何もないダッシュボードに驚くことはありません。落ち着いて画面上部のメニューから「アカウント追加」を選び、追加したいウェブサービスのアカウントを登録してください。</p>
    <p><a class="btn" href="<?php echo Uri::create('signup'); ?>">サインアップ &raquo;</a></p>
  </div>
  <div class="span4">
    <h2>何ができる？</h2>
    <p>REST APIを通じてブラウザで見るだけだったウェブサービスのさまざまな情報の活用が可能になります。</p>
    <p>ログイン処理やページのスクレイピングなど難しいことを考える必要はありません。広く知られたREST APIの仕組みを通じて情報を引き出すことができます。</p>
 </div>
  <div class="span4">
    <h2>使うには</h2>
    <p>APIの仕様が分かっている場合は、ダッシュボードからAPI KEYを確認しあなたが作っている、もしくは使っているアプリケーションに指定してください。</p>
    <p>APIの仕様が分からない？大丈夫！動作も確認できるAPIのドキュメントが用意されています。</p>
  </div>
</div>
