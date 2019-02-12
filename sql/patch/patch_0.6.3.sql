
ALTER TABLE `x360p_config` 
DROP INDEX `idx_cfg_name`,
ADD UNIQUE INDEX `idx_cfg_name`(`cfg_name`, `og_id`) USING BTREE;

INSERT INTO `x360p_dictionary` VALUES (161, 0, 11, '-3', '幼小', '幼儿园小班', 1, 0, 0, 0, 0, 0, 0, 0, NULL);
INSERT INTO `x360p_dictionary` VALUES (162, 0, 11, '-2', '幼中', '幼儿园中班', 1, 0, 0, 0, 0, 0, 0, 0, NULL);
INSERT INTO `x360p_dictionary` VALUES (163, 0, 11, '-1', '幼大', '幼儿园大班', 1, 0, 0, 0, 0, 0, 0, 0, NULL);