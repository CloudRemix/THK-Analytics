CREATE TABLE IF NOT EXISTS ? (
id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
dd VARCHAR( 2 )  DEFAULT '' COMMENT 'アクセス日',
logtype CHAR( 1 )  DEFAULT '' COMMENT 'ログタイプ',
uid VARCHAR( 65 )   DEFAULT '' COMMENT '訪問者ID',
hh VARCHAR( 2 )  DEFAULT '' COMMENT 'アクセス時',
mi VARCHAR( 2 )  DEFAULT '' COMMENT 'アクセス分',
ss VARCHAR( 2 )  DEFAULT '' COMMENT 'アクセス秒',
weekday VARCHAR( 1 )  DEFAULT '' COMMENT '曜日',
remote_addr VARCHAR( 40 )  DEFAULT '' COMMENT 'IPアドレス',
remote_host VARCHAR( 255 )  DEFAULT '' COMMENT 'リモートホスト',
domain VARCHAR( 100 )  DEFAULT '' COMMENT 'ドメイン',
jpdomain VARCHAR( 100 )  DEFAULT '' COMMENT 'JPドメイン',
country VARCHAR( 100 ) DEFAULT '' COMMENT '国',
pref VARCHAR( 100 ) DEFAULT '' COMMENT '都道府県',
url TEXT  COMMENT 'URL',
title TEXT  COMMENT 'タイトル',
screenwh VARCHAR( 15 )  DEFAULT '' COMMENT '画面解像度',
screencol VARCHAR( 10 )  DEFAULT '' COMMENT '画面色',
jsck CHAR( 20 ) DEFAULT '' COMMENT 'Javascript/Cookie',
os VARCHAR( 100 )  DEFAULT '' COMMENT 'OS',
os_ver VARCHAR( 50 )  DEFAULT '' COMMENT 'OSバージョン',
browser VARCHAR( 100 )  DEFAULT '' COMMENT 'ブラウザ',
brow_ver VARCHAR( 50 )  DEFAULT '' COMMENT 'ブラウザバージョン',
crawler VARCHAR( 100 )  DEFAULT '' COMMENT 'クローラー',
keyword TEXT  COMMENT '検索キーワード',
engine VARCHAR( 80 )  DEFAULT '' COMMENT '検索エンジン',
referer_title TEXT  COMMENT 'リファラータイトル',
referer_host VARCHAR( 255 )  DEFAULT '' COMMENT 'リファラーホスト名',
http_user_agent TEXT  COMMENT 'ユーザーエージェント',
http_referer TEXT  COMMENT 'リファラー',
PRIMARY KEY ( id )
)
TYPE = InnoDB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 400
CHECKSUM = 0
MAX_ROWS = 50000000
MIN_ROWS = 1
ROW_FORMAT = DEFAULT
COMMENT = 'ログテーブル'
;
