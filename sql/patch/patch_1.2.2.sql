ALTER TABLE `x360p_lesson_standard_file`
ADD COLUMN `chapter_index` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '章节序号' AFTER  `lid`
;

