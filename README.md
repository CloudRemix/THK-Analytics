# THK Analytics - thk.kanzae.net

## THK Analytics とは

  高機能・高速・低メモリ・レスポンシブデザインな  
  PHP + MySQL で動作するサーバーインストール型 Web アクセス解析ツールです。

## サーバー動作環境

  - OS  
    Linux、FreeBSD、Windows (Windows は XAMPP では動作確認済み、OSX は持ってないので分からない)

  - Webサーバー  
    Apache (Apache でしか動作検証してません)

  - PHP  
    バージョン 5.3 以上

  - MySQL  
    バージョン 4.2 以上 (5.0以上推奨、5.6以上だとさらに高速)

## クライアント環境

  - OS  
    Windows、OSX、Linux、FreeBSD、iOS、Android 等

  - ブラウザ  
    Javascript、Cookie が使用できるブラウザ  
    (IE、Firefox、Chrome、Chromium、Opera、Sleipnir、Konqueror、Dolphin、Android標準ブラウザ 等で検証済み)

## スマートフォン(iPhone、Android 等)の場合

  - レスポンシブデザインのため、スマホ等でも閲覧できます。
  - 横向き画面での閲覧を推奨。  
    (縦画面だとグラフ等がつぶれて、見にくいです。)
  - 閲覧中に画面サイズを変更した場合、グラフ等が画面外にはみ出すことがありますが、  
    再読み込みすれば、正常に閲覧できます。

## インストール

  1. THK Analytics 用に MySQL のデータベースを準備しておく (文字コード UTF-8)。

  2. ダウンロードしたファイルを解凍してサーバーにアップロードする。
     (アップロードしてからサーバーで解凍してもいい。て言うかむしろ、そっちの方がはやい)

  3. ファイル構成  
    ├  _core  
    ├  _data  
    ├  thk (名前変更可)  

  4. インストール画面へのアクセス  
    - ブラウザで「http://Webサーバー/thk (名前変更可)/view/」にアクセス。
    - 画面に従い、必要な情報を入力していけばインストール完了。

  5. 解析用コードの埋め込み  
    - アクセス解析をしたい Webサイトに発行された解析用コードを貼る。

  6. GeoLiteCity.dat ファイルの準備 (必須ではないが推奨)
    - 詳細は後述。


## 複数サイトの解析
  - 以下のようにファイルを設置してください。  
    ├  _core  
    ├  _data  
    ├  thk (名前変更可)  
    ├  site2 (thk のコピー、名前変更可)  
    ├  site3 (thk のコピー、名前変更可)  

  - コピーしたディレクトリの setting の中身は .htaccess のみにしてください。  
    (THK Analytics は setting ディレクトリ内にファイルを作成するため、コピーする場合は .htaccess 以外は削除してください。)
  - 解析サイトごとにデータベースを用意するか、  
    同じデータベースを使用する場合は、テーブルプレフィクスを解析サイトごとに変更してください。

## GeoLiteCity.dat ファイルのアップロード
  THK Analytics ではドメイン名で国名と都道府県名を判別しますが、  
  GeoLiteCity.dat があれば、ドメイン名で判別できなかった国名や都道府県名を判別できることがあります。

  1. [Maxmind](http://dev.maxmind.com/geoip/legacy/geolite/ "Maxmind") の Webサイトから GeoLiteCity(Binary) をダウンロードしてください。

  2. ファイルを解凍して、GeoLiteCity.dat を「_data」ディレクトリにアップロードしてください。

## アップグレード

  1. 本体とDBアップグレード用のファイルをダウンロード。

  2. ダウンロードした本体とDBアップグレード用ファイルを解凍
　　 (特に指定が無い限り _core フォルダの上書きだけで OK です)。

  3. 解凍した本体のファイルを古いファイルに上書きする。

  4. DBアップデート用ファイル（_upgrade）を以下のように設置。
    ├ _core  
    ├ _data  
    ├ thk (名前変更可)  
    ├ _upgrade  

  5. ブラウザで「http://(THK Analytics までのパス)/thk (名前変更可)/view/」にアクセス。

  6. 画面の指示に従い、データベースをアップグレードする。
　　 複数サイトの解析を行っている場合は、解析サイトごとにデータベースをアップグレードしてください  
    （上記「名前変更可」の部分を解析サイトごとにブラウザでアクセスする）。

  7. _upgrade をディレクトリごと削除。

  8. 以上でアップグレードは完了です。

## ログイン用ID・パスワードを忘れた場合
  ログイン用のID・パスワードを忘れてしまった時は、以下の操作でシステムの再設定を行ってください。

  1. thk/setting/ ディレクトリの権限(パーミッション) を 755 に変更。

  2. thk/setting/siteurl.php を削除。

  3. ブラウザで「http://(THK Analytics までのパス)/thk (名前変更可)/view/」にアクセス。

## MySQL データベースの設定を変更したい場合
  データベースの設定を変更する場合は、以下の操作でシステムの再設定を行ってください。

  1. thk/setting/ ディレクトリの権限(パーミッション) を 755 に変更。

  2. thk/setting/database.php を削除。

  3. ブラウザで「http://Webサーバー/thk (名前変更可)/view/」にアクセス。

## 実装に関して
  THK Analytics では、以下のライブラリ等を利用しています。

  - ベース部分
    - [Research Artisan Lite](http://lite.research-artisan.net/main/ "Research Artisan Lite")

  - 折れ線グラフ、積み上げグラフ、一部の円グラフの描画
    - [ccchart](http://ccchart.com/ "ccchart")

  - 円グラフの描画
    - [JavaScript Library - HTML5.JP](http://www.html5.jp/library/graph_circle.html "JavaScript Library - HTML5.JP")

  - ユーザーエージェントの解析 Level.1
    - [ua-parser PHP Library](https://github.com/ua-parser/uap-php "ua-parser PHP Library")

  - ユーザーエージェントの解析 Level.2 (ua-parser で解析できなかった部分)
    - [Woothee PHP](https://github.com/woothee/woothee "Woothee PHP")

  - CSS ファイルの最適化(圧縮)
    - [YUI CSS compressor](https://github.com/tubalmartin/YUI-CSS-compressor-PHP-port "YUI CSS compressor")

  - Javascript ファイルの最適化(圧縮)
    - [JSMin PHP](https://github.com/rgrove/jsmin-php "JSMin PHP")

## Licence
  - [GPL v2 or later](http://www.gnu.org/licenses/gpl-2.0.html "GPL v2 or later")

## Contact

  - 公式サイト: [THK Analytics](http://thk.kanzae.net/analytics/ "THK Analytics")

  - 開発者ブログ: [Thought is free](http://thk.kanzae.net/ "Thought is free")

  - Twitter: LunaNuko

## Copyright
  Copyright (C) 2015 Thought is free.
