CREATE DATABASE IF NOT EXISTS `homestead_test`;
CREATE DATABASE IF NOT EXISTS `homestead`;
GRANT ALL ON `homestead_test`.* TO 'homestead'@'%';
GRANT ALL ON `homestead`.* TO 'homestead'@'%';
