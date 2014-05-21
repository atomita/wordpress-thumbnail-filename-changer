Thumbnail Changer Plugin
======================
Wordpressで生成されるサムネイルの保存名を○○-thumbnail.jpg、○○-medium.jpg、○○-large.jpgのように
決まった名前で生成することができるプラグイン。  
add_image_size($name,$w,$h,$crop);で作るサムネイルも○○-{$name}名.jpgで生成されます。

何の意味があるの？
------
[Force Regenerate Thumbnails]: http://wordpress.org/extend/plugins/force-regenerate-thumbnails/
[Regenerate Thumbnails](http://wordpress.org/extend/plugins/regenerate-thumbnails/)
保存名を固定にすることで、サムネイルのサイズ変更に伴うファイル名の変更が発生せず  
[Force Regenerate Thumbnails]プラグインでサムネイルを作り直しても記事内のリンク切れが発生しなくなります。

[Force Regenerate Thumbnails]はサムネイルを一旦全て削除して作り直すプラグインです。  
（[Regenerate Thumbnails]は削除することなく再生成するプラグインです。初心者はこちらの方が安全です）  
ハイライトプラグインを使わなくても貼り付けは可能です。

使い方
------
http://creatorish.com/lab/4612

wp-content/pluginsに解凍してできたthumbnail-filename-changer.phpを入れて、  
管理画面から有効化するだけです。  

すると、次回以降アップロードする画像は○○-thumbnail.jpgや○○-medium.jpgなどになります。  

一括でファイル名を置換する方法などはリンク先の記事を参考にしてみてください。
http://creatorish.com/lab/4612

動作確認
------
wordpress3.9で動作確認

ライセンス
--------
[MIT]: http://www.opensource.org/licenses/mit-license.php
Copyright &copy; 2012 creatorish.com
Distributed under the [MIT License][mit].

作者
--------
creatorish yuu  
Weblog: <http://creatorish.com>  
Facebook: <http://facebook.com/creatorish>  
Twitter: <http://twitter.jp/creatorish>
