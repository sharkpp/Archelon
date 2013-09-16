﻿# Archelon, The account aggregation service

## 始めに

Archelon は アカウントアグリゲーションサービスの一種です。
RESTful APIなどなどウェブAPIを用意していないウェブアプリケーションにAPIを追加するシステムです。

## インストール

1. データベースの設定は ```fuel/app/config/production/db.php``` に書かれているので環境に合わせてデータベース名などを変更してください。
2. ```oil``` コマンドでテーブルを作成します。

      # oil r migrate --all

3. 管理者として使用したいユーザー名でサインアップしてください。
4. コネクタを ``` fuel/app/modules ``` に追加したら、画面から「コネクタの再読み込み」を選び更新します。

## 使い方(設定編)

アカウントの追加方法です。

1. 画面上部の「アカウント追加」を選択します。
2. 使用できるコネクタの一覧が表示されるので追加したいアカウントを選択します。
3. 入力項目は個々のコネクタにより違いがあるので省略します。
4. 入力できたらホーム画面に戻ります。

## 使い方(API編)

APIの確認の方法です。

1. ホーム画面上の追加済みのアカウントのタイトル部分のドロップダウンから「使い方」を選択します。
2. APIの一覧とサンプルページに飛ぶのでそれぞれのAPIの動作を確認できます。
3. ホーム画面上には追加済みのアカウントに設定されたAPIキーも表示されています。

## ライセンス

Copyright(c) 2013 sharkpp All rights reserved.
このアプリケーションは、The MIT License の元で公開されています。

Archelon.svg および archelon-bg は [File:Archelon BW.jpg - Wikimedia Commons](http://commons.wikimedia.org/wiki/File:Archelon_BW.jpg?uselang=ja) を元に作成されました。
これらのファイルは クリエイティブ・コモンズ 表示-継承 3.0 非移植 (CC by-sa 3.0) の元で公開されています。

