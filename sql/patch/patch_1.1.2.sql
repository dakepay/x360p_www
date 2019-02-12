-- 已经添加
-- +++++++++++++
ALTER TABLE `x360p_market_clue`
ADD COLUMN `is_reward` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否奖励' AFTER `is_deal`
;