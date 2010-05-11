INSERT INTO module VALUES ({SGL_NEXT_ID}, 1, 'bounty', 'My Bounty module', 'The ''Default'' module includes functionality that is needed in every install, for example, configuration and interface language manangement, and module management.', 'default/maintenance', '48/module_default.png', '', NULL, NULL, NULL);


INSERT INTO `bounty_status` VALUES (1, 'open');
INSERT INTO `bounty_status` VALUES (2, 'sponsored');
INSERT INTO `bounty_status` VALUES (3, 'sponsored_and_assigned');
INSERT INTO `bounty_status` VALUES (4, 'complete');