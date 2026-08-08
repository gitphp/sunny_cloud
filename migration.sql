
-- 2026-08-08 
alter table book_mark add click_count int not null DEFAULT 0 COMMENT '点击量 热度统计' after book_desc;

alter table category add category_type tinyint(1) not null DEFAULT 0 COMMENT '分类-类型 0=文章 1=商品 2=导航 3=其他' after level;


