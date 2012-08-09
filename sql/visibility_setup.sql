-- --------------------------------------------------------

--
-- Table structure for table `visibility_setup`
--

DROP TABLE IF EXISTS `visibility_setup`;
CREATE TABLE IF NOT EXISTS `visibility_setup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=18 ;