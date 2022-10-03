# 飲食店予約アプリ Rese（リーズ）
- お客様（ユーザー）が飲食店の検索、お気に入り、予約、予約変更、口コミなどをすることができる。
- 管理者（Administrator、seed時に作成）はManager（店舗代表者）の作成、management（店舗代表者登録申請）の承認、承認取り消し、削除ができる。
- 店舗代表者（Manager）は店舗代表者登録申請、新規店舗作成、管理者承認後は店舗情報の更新、メール送信、予約の確認、予約をご来店済みにすることができる。
![indexページ](https://user-images.githubusercontent.com/104754786/193504561-7348fb59-096e-48a4-9ab9-e8db9180b76d.png)


## 作成した目的
- 自社内製の飲食店予約アプリを作成することで、手数料の削減をするため。
- 他社サービスよりもUI・UXを向上させるため。


## 機能一覧
- 会員登録
- ログイン
- ログアウト
- ユーザー情報取得
- ユーザー飲食店お気に入り一覧取得
- ユーザー飲食店予約情報取得
- 飲食店一覧取得
- 飲食店詳細取得
- 飲食店お気に入り追加
- 飲食店お気に入り削除
- 飲食店予約情報追加
- 飲食店予約情報変更
- 飲食店予約情報削除
- エリアで検索する
- ジャンルで検索する
- 店名で検索する
- 認証メール送信によるメールアドレス認証
- 予約情報をQRコードにして提示できる
- ご来店後にマイページから5段階評価と口コミを投稿できる
- 管理者による店舗代表者の作成
- 管理者による店舗代表者登録申請の承認
- 管理者による店舗代表者登録申請の削除
- 管理者による店舗代表者登録申請承認の取り消し
- 店舗代表者による店舗代表者登録の申請
- 店舗代表者による新規店舗作成
- 店舗代表者による店舗情報の更新
- 店舗代表者による予約の確認
- 店舗代表者による予約のご来店済みフラグ追加
- 店舗代表者によるQRコードリーダーを利用した予約の確認とご来店済みフラグ追加（HTTP環境下ではリーダー起動ができないので、この機能はlocalで評価して頂けると幸いです。）
- 店舗代表者による3種のセグメントのユーザーへのメール送信
- Amazon LinuxのcronとLaravelのタスクスケジューラを利用した予約日のリマインダーメール送信（本番環境は評価しやすいように10分に1回送信する設定。）
- レスポンシブデザイン


### その他細かな機能
- NuxtのVeeValidateによるリアルタイムバリデーション
- 正しい情報を入力し終わるまでボタンが押せなくなる機能
- 権限をuser,admin,managerで分け、NuxtのmiddlewareでLaravelに問合せし、adminとmanagerの権限がないと入れないページがある


## 使用技術（本番環境）
- PHP  8.1.10
- Laravel  8.83.23
- Node.js 16.15.0
- Nuxt.js 2.15.8
- MySQL 8.0.30 for Linux on x86_64 (MySQL Community Server - GPL)

- Laravelデプロイ先：AWS EC2
- Nuxtデプロイ先：AWS EC2（NuxtはHerokuで良いかなと思ったが、HerokuのNuxtからはHTTPのAPIを叩けなかったため、NuxtもEC2にデプロイ。）
- MySQLデプロイ先：AWS RDS
- ストレージ：AWS S3 （後述：最後までお読みいただけますでしょうか）
- メールサーバー：gmail

## .envファイルによる環境切り分け
- 環境切り分けは.envによって行っています。見せられない情報を削除して、末尾に.exampleを付与してgit内に共有させていただきます。
- local環境は.env.local.example、localのdocker環境は.env.docker.example、本番環境は.env.production.exampleとなっております。
- laravel,nuxtそれぞれに.envがあります。nuxtには.envを扱えるモジュールを入れています。
- 環境によって主に変わるのは、URL、database、mail系です。
- メールはlocalではMailtrap、dockerではMailhog、本番ではgmailを利用しています。
- docker環境を構築する際は、git cloneした後に、laravelディレクトリでComposer install --ignore-platform-reqs、nuxtディレクトリでyarn installしてからdocker-compose up --buildすると上手くいきます。（正直仮想コンテナ内でやるべきことなのになぜかbuild,up時に実行されない。localにcomposerやyarnがない人やバージョンが合わない人のためのdockerなのに。これが私の現状のdockerのウデマエです。。今後はDockerfileをいじくりまわしてコンテナ内でできるようにしていきたい。）



## テーブル設計
- テーブルが多く冗長になるので、別途スプレッドシートの「テーブル仕様書」をご確認ください。

## ER図
![ER drawio](https://user-images.githubusercontent.com/104754786/193504507-24e09937-110d-4ffe-b5a3-5daa8960e9fc.png)


## 他に記載することがあれば記述する
#### 管理者（Administrator）
- 管理者アカウントはname:Administrator、email:administrator@admin.admin、password:administratorでシードされていますので、そちらでログインください。
- adminページには左上のハンバーガーをクリックして出てくるメニューのAdminをクリックしてください。
- adminページでは店舗代表者（Manager）の新規作成、店舗代表者申請の承認、破棄、店舗代表者承認の取り消しが行えます
- managerを一人作成した後、別ブラウザでmanagerアカウントで店舗代表者申請し、adminのブラウザで承認するとスムーズに機能チェックできると思います。

![adminページ](https://user-images.githubusercontent.com/104754786/193504593-bb5e1cdc-8b01-4953-b109-da2f802c6380.png)


#### 店舗代表者（Manager）
- 店舗代表者アカウントは機能チェックもされると思いますので、シードしていません。adminページで作成してください。
- managerページも左上のハンバーガーメニューから入れます。
- managerページではまず、店舗の新規作成か、既存店舗の店舗代表者登録申請ができます。
- 申請、Administratorの承認後に、各店舗の詳細ページに入ることができるようになります。

![managerページ](https://user-images.githubusercontent.com/104754786/193504624-304002d7-b8f6-4e26-b9b7-717dfffcb173.png)

- 店舗代表者向け店舗ページでは、店舗情報の修正、ユーザーへのメール送信、予約の確認、予約のご来店済み化を行えます。
- ユーザーへのメール送信は3つのセグメントから複数選べます。
- カメラがあれば、QRコードリーダーを起動して予約の参照ができ、そのモーダル画面でご来店済みにできます。（この機能はlocalかHTTPSでしか許されておらず、今回の模擬案件ではHTTPしか用意できませんので、localでご評価いただけると幸いです。）


![manageページ](https://user-images.githubusercontent.com/104754786/193504647-ab54c596-5597-41e8-989d-c4c33b20478c.png)

#### ユーザー
- マイページでは認証メールの送信ができ、届いたメールに記載されたページを開くと、自動でメールアドレス認証が完了します。
- メール認証が完了すると、予約ができるようになります。
- マイページでは予約の確認ができ、Managerによってご来店済み化されると、評価とコメントを投稿できるようになります。

![mypage](https://user-images.githubusercontent.com/104754786/193504655-ba12a834-4c74-450c-beed-8236c140a94d.png)


## S3へ画像をアップロードしようとする際の502 Bad Gatewayについて
- 店舗代表者が店舗の画像をS3にアップロードする際に502 Bad Gatewayが発生してしまう。
- EC2のlaravelインスタンスとS3の繋ぎこみは完了しているはず。laravelインスタンスにはIAMロールを付与し、そのロールがS3 Full Accessのパーミッションを持っている。
- nginxエラーログは以下の通り。
- recv() failed (104: Connection reset by peer) while reading response header from upstream, client: 106.73.129.130, server: 52.69.120.211, request: "POST /api/v1/restaurant-img HTTP/1.1", upstream: "fastcgi://127.0.0.1:9000", host: "52.69.120.211", referrer: "http://18.183.71.34/
- 予測される原因：upstreamのpfp-fpmとnginxの接続がfpm側からリセットされている

- 以下のサイトの対処法に当たるものは一通り試しましたが502解除されない。
- https://zenn.dev/tamakiii/scraps/037c81c0cc52c4
- https://laracasts.com/discuss/channels/forge/502-bad-gateway-with-large-file-uploads
- https://stackoverflow.com/questions/43598477/502-bad-gateway-on-php-vimeo-upload-via-ajax-with-nginx-laravel-forge
- https://pgmemo.tokyo/data/archives/1550.html
- https://www.yuulinux.tokyo/18313/

- Next Action：php-fpm側のエラーログも見てみるとSIGSEGVエラーが出ていたため、今後はcore dumpを掘り起こしてエラー原因を特定していく。
- 参考URL：https://nextat.co.jp/staff/archives/172

- Slackやコーチに質問しても回答は得られず、非常に難しい問題となってしまっている。
- 結果的にS3への画像保存はペンディングになってしまったが、nginxやphp-fpm、Linuxについて理解が深まったのでヨシ。


### どなたかこのConnection reset by peer問題についてアドバイス頂けたりしませんでしょうか？何卒よろしくお願いいたします。

- readmeは以上です。大変お手数おかけしますが、模擬案件のご評価よろしくお願いいたします。