<?php

class Pages_Migration_20120424_160812_64_About extends Core_Migration_Abstract
{

    public function up()
    {
        // upgrade
        $this->insert(
            'pages', array(
                'title' => 'About ZFCore',
                'alias' => 'about',
                'pid' => 0,
                'content' => $this->getAboutContent(),
                'created' => '2012-04-24 16:08:12',
                'updated' => '2012-04-24 16:08:12')
        );
    }

    public function down()
    {
        // degrade
        $this->query('DELETE FROM `pages` WHERE `alias` = ? LIMIT 1', array('about'));
    }


    public function getDescription()
    {
        return 'Example of static pages usage';
    }

    /**
     * Get Content for About page
     *
     * @return string
    */
    private function getAboutContent()
    {
        $result = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'about-page.html');
        return $result;
    }
}

