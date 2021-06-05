CREATE TABLE IF NOT EXISTS ? (
id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
dd VARCHAR( 2 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'アクセス日',
logtype CHAR( 1 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'ログタイプ',
uid VARCHAR( 65 )  CHARACTER SET utf8 COLLATE utf8_general_ci  DEFAULT '' COMMENT '訪問者ID',
hh VARCHAR( 2 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'アクセス時',
mi VARCHAR( 2 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'アクセス分',
ss VARCHAR( 2 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'アクセス秒',
weekday VARCHAR( 1 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '曜日',
remote_addr VARCHAR( 40 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'IPアドレス',
remote_host VARCHAR( 255 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'リモートホスト',
domain VARCHAR( 100 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'ドメイン',
jpdomain VARCHAR( 100 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'JPドメイン',
country VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '国',
pref VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '都道府県',
url TEXT  CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'URL',
title TEXT  CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'タイトル',
screenwh VARCHAR( 15 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '画面解像度',
screencol VARCHAR( 10 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '画面色',
jsck CHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'Javascript/Cookie',
os VARCHAR( 100 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'OS',
os_ver VARCHAR( 50 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'OSバージョン',
browser VARCHAR( 100 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'ブラウザ',
brow_ver VARCHAR( 50 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'ブラウザバージョン',
crawler VARCHAR( 100 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'クローラー',
keyword TEXT  CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '検索キーワード',
engine VARCHAR( 80 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '検索エンジン',
referer_title TEXT  CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'リファラータイトル',
referer_host VARCHAR( 255 )  CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT 'リファラーホスト名',
http_user_agent TEXT  CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'ユーザーエージェント',
http_referer TEXT  CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT 'リファラー',
PRIMARY KEY ( id )
)
ENGINE = InnoDB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 400
CHECKSUM = 0
MAX_ROWS = 50000000
MIN_ROWS = 1
ROW_FORMAT = DEFAULT
DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
COMMENT = 'ログテーブル'
;
