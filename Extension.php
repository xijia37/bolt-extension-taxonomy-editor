<?php

namespace Bolt\Extension\xijia37\TaxonomyEditor;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Translation\Loader as TranslationLoader;
use Symfony\Component\Yaml\Dumper as YamlDumper,
    Symfony\Component\Yaml\Parser as YamlParser,
    Symfony\Component\Yaml\Exception\ParseException;
use Bolt\Helpers\String;

class TaxonomyEditorException extends \Exception {};
class Extension extends \Bolt\BaseExtension
{
    private $authorized = false;
    private $backupDir;
    private $translationDir;
    public  $config;

    /**
     * @return array
     */
    public function getName()
    {

        return 'taxonomy_editor';

    }

    /**
     * Initialize extension
     */
    public function initialize()
    {
        //$this->config = $this->getConfig();
        $this->config = $this->app['config']->get('general/TaxonomyEditor');
        $this->backupDir = __DIR__ .'/backups';

         $this->addCSS('assets/taxonomy_editor.css');
        /**
         * ensure proper config
         */
        if (!isset($this->config['permissions']) || !is_array($this->config['permissions'])) {
            $this->config['permissions'] = ['root', 'admin', 'developer', 'chief-editor'];
        } else {
            $this->config['permissions'][] = 'root';
        }
        if (!isset($this->config['enableBackups']) || !is_bool($this->config['enableBackups'])) {
            $this->config['enableBackups'] = false;
        }
        if (!isset($this->config['keepBackups']) || !is_int($this->config['keepBackups'])) {
            $this->config['keepBackups'] = 10;
        }

        // check if user has allowed role(s)
        $currentUser    = $this->app['users']->getCurrentUser();
        $currentUserId  = $currentUser['id'];

        foreach ($this->config['permissions'] as $role) {
            if ($this->app['users']->hasRole($currentUserId, $role)) {
                $this->authorized = true;
                break;
            }
        }

        if ($this->authorized)
        {

            $this->path = $this->app['config']->get('general/branding/path') . '/extensions/taxonomy-editor';
            $this->app->match($this->path, array($this, 'loadTaxonomyEditor'));

            $this->translationDir = __DIR__.'/translations/' . $this->app['locale'];
            if (is_dir($this->translationDir))
            {
                $iterator = new \DirectoryIterator($this->translationDir);
                foreach ($iterator as $fileInfo)
                {
                    if ($fileInfo->isFile())
                    {
                        $this->app['translator']->addLoader('yml', new TranslationLoader\YamlFileLoader());
                        $this->app['translator']->addResource('yml', $fileInfo->getRealPath(), $this->app['locale']);
                    }
                }
            }

            $this->addMenuOption('编辑分类', $this->app['paths']['bolt'] . 'extensions/taxonomy-editor', "fa fa-tags fa-fw");

        }
    }

    /**
     * @param $html
     * @return mixed
     */
    private function injectAssets($html)
    {

        $urlbase = $this->getBaseUrl();

        //if ($this->dev) {
            //$assets = '<script data-main="{urlbase}assets/app" src="{urlbase}assets/bower_components/requirejs/require.js"></script>';
        //} else {
            $assets = '<script src="{urlbase}assets/taxonomy-editor.js"></script>';
        //}

        $assets .= '<link rel="stylesheet" href="{urlbase}assets/taxonomy-editor.css">';
        $assets = preg_replace('~\{urlbase\}~', $urlbase, $assets);

        // Insert just before </head>
        preg_match("~^([ \t]*)</head~mi", $html, $matches);
        $replacement = sprintf("%s\t%s\n%s", $matches[1], $assets, $matches[0]);
        return String::replaceFirst($matches[0], $replacement, $html);
        //return str_replace_first($matches[0], $replacement, $html);

    }

    /**
     * Add some awesomeness to Bolt
     *
     * @return Response|\Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function loadTaxonomyEditor()
    {

        /**
         * check if menu.yml is writable
         */
        $file = BOLT_CONFIG_DIR . '/taxonomy.yml';
        if (@!is_readable($file) || !@is_writable($file)) {
            throw new \Exception(
                __("The file '%s' is not writable. You will have to use your own editor to make modifications to this file.",
                    array('%s' => $file)));
        }
        if (!$writeLock = @filemtime($file)) {
            $writeLock = 0;
        }

        /**
         * try to set symlink to localized readme
         */
        //$lastLocale = $this->app['cache']->contains('extension_TaxonomyEditor') ? $this->app['cache']->fetch('extension_TaxonomyEditor') : 'unknown';
        //$lastLocale != $this->app['locale'] && @is_writable(__DIR__) ? $this->localizeReadme() : null;

        $taxonomies_full = $this->app['config']->get('taxonomy');
        /**
         * process xhr-post
         */
        if ($this->app['request']->isMethod('POST') &&
            true === $this->app['request']->isXmlHttpRequest())
        {

            /**
             * restore backup
             */
            //try {
                //if ($filetime = $this->app['request']->get('filetime')) {

                    //if ($this->restoreBackup($filetime)) {
                        //$this->app['session']->getFlashBag()->set('success', __('Backup successfully restored'));
                        //return $this->app->json(array('status' => 0));
                    //}

                    //throw new TaxonomyEditorException(__("Backup file could not be found"));

                //}

            //} catch (TaxonomyEditorException $e) {
                //return $this->app->json(array('status' => 1, 'error' => $e->getMessage()));
            //}

            /**
             * save taxonomy(s)
             */
            //try {
                if ($taxonomies          = $this->app['request']->get('taxonomies')) {

                    // don't proceed if the file was edited in the meantime
                    //if ($writeLock != $writeLockToken) {
                        //throw new TaxonomyEditorException($writeLock, 1);
                    //} else {
                            //die();
                    //var_dump($taxonomies); die();
                        $dumper = new YamlDumper();
                        $dumper->setIndentation(4);
                        foreach($taxonomies as $name => $options){
                            $taxonomies_full[$name]['options'] = [];
                            foreach($options as $option){
                                //replace space with underscore;
                                //$option['name'] = str_replace(' ', '_', trim($option['name']));
                                $taxonomies_full[$name]['options'][$option['name']] = $option['label'];
                                if ($option['name'] !== $option['originalname']){
                                    $prefix = $this->app['config']->get('general/database/prefix', 'bolt_');
                                    $taxonomy_table_name    = $prefix . "taxonomy";
                                    foreach($this->app['config']->get('contenttypes') as $contenttype => $settings){
                                        if( isset($settings['taxonomy']) && in_array($name, $settings['taxonomy'])){
                                            $sql = sprintf('UPDATE %s SET slug = ?, name = ? WHERE contenttype = "%s" AND slug = ?', $taxonomy_table_name, $contenttype);
                                            $rows = $this->app['db']->executeUpdate($sql, [$option['name'], $option['label'], $option['originalname']] );
                                        }
                                    }
                                }
                            }
                            //$taxonomies_full[$name]['options'] = $options;
                        }
                            //var_dump($taxonomies_full);
                        $yaml = $dumper->dump($taxonomies_full, 9999);

                        // clean up dump a little
                        $yaml = preg_replace("~(-)(\n\s+)~mi", "$1 ", $yaml);

                        try {
                            $parser = new YamlParser();
                            $parser->parse($yaml);
                        } catch (ParseException $e) {
                            throw new TaxonomyEditorException($writeLock, 2);
                        }

                        // create backup
                        //if (true === $this->config['enableBackups']) {
                            //$this->backup($writeLock);
                        //}

                        // save
                        if (!@file_put_contents($file, $yaml)) {
                            throw new TaxonomyEditorException(0, 3);
                            //throw new TaxonomyEditorException($writeLock, 3);
                        }

                        //clearstatcache(true, $file);
                        //$writeLock = filemtime($file);

                        //if (count($this->app['request']->get('taxonomies')) > 1) {
                            //$message = __("Taxonomies successfully saved");
                        //} else {
                            //$message = __("Taxonomy successfully saved");
                        //}
                        $message = '成功保存分类';
                        $this->app['session']->getFlashBag()->set('success', $message);

                        return $this->app->json(array('writeLock' => $writeLock, 'status' => 0));

                    //}

                    // broken request
                    //throw new TaxonomyEditorException($writeLock, 4);

                }

            //} catch (TaxonomyEditorException $e) {
                //return $this->app->json(array('writeLock' => $e->getMessage(), 'status' => $e->getCode()));
            //}

            /**
             * search contenttype(s)
             */
            //try {
                //if ($this->app['request']->get('action') == 'search-contenttypes') {
                    //$ct = $this->app['request']->get('ct');
                    //$q = $this->app['request']->get('q');
                    //$retVal = Array();
                    //if (empty($ct)) {
                        //$contenttypes = $this->app['config']->get('contenttypes');
						//foreach ($contenttypes as $ck => $contenttype) {
							//$retVal[] = $this->app['storage']->getContent($contenttype['name'], array('title'=> "%$q%", 'slug'=>"%$q%", 'limit'=>100, 'order'=>'title'));
						//}
                    //} else {
                        //$retVal[] = $this->app['storage']->getContent($ct, array('title'=> "%$q%", 'limit'=>100, 'order'=>'title'));
                    //}

                    //return $this->app->json(array('records' => $retVal));
                //}
            //} catch (Exception $e) {

            //}
        }

        // add eTaxonomyEditor template namespace to twig
        $this->app['twig.loader.filesystem']->addPath(__DIR__.'/views/', 'TaxonomyEditor');

        /**
         * load stuff
         */
        //$menus          = $this->app['config']->get('menu');
        //$contenttypes   = $this->app['config']->get('contenttypes');
        //$taxonomies      = $this->app['config']->get('taxonomy');

        //foreach ($contenttypes as $cK => $contenttype) {
            //$contenttypes[$cK]['records'] = $this->app['storage']->getContent($contenttype['name'], array());
        //}

        //foreach ($taxonomies_full as $tK => $taxonomy)
        //{

            //$taxonomys[$tK]['me_options'] = array();

            //// fetch slugs
            //if (isset($taxonomy['behaves_like']) && 'tags' == $taxonomy['behaves_like'])
            //{

                //$prefix = $this->app['config']->get('general/database/prefix', "bolt_");

                //$taxonomytype = $tK;
                //$query = "select distinct `%staxonomy`.`slug` from `%staxonomy` where `taxonomytype` = ? order by `slug` asc;";
                //$query = sprintf($query, $prefix, $prefix);
                //$query = $this->app['db']->executeQuery($query, array($taxonomytype));

                //if ($results = $query->fetchAll()) {
                    //foreach ($results as $result) {
                        //$taxonomys[$tK]['me_options'][$taxonomy['singular_slug'] .'/'. $result['slug']] = $result['slug'];
                    //}
                //}

            //}

            //if (isset($taxonomy['behaves_like']) && 'grouping' == $taxonomy['behaves_like']) {
                //foreach ($taxonomy['options'] as $oK => $option) {
                    //$taxonomys[$tK]['me_options'][$taxonomy['singular_slug'] .'/'. $oK] = $option;
                //}
            //}

            //if (isset($taxonomy['behaves_like']) && 'categories' == $taxonomy['behaves_like']) {
                //foreach ($taxonomy['options'] as $option) {
                    //$taxonomys[$tK]['me_options'][$taxonomy['singular_slug'] .'/'. $option] = $option;
                //}
            //}

        //}

        //// fetch backups
        //$backups = array();
        //if (true === $this->config['enableBackups'])
        //{
            //try {
                //$backups = $this->backup(0, true);

            //} catch (TaxonomyEditorException $e) {
                //$this->app['session']->getFlashBag()->set('warning', $e->getMessage());
            //}
        //}

        $taxonomies = [];
        foreach($taxonomies_full as $taxonomy_name => $taxonomy){
            if ( 'tags' !== $taxonomy['behaves_like']){
                $options = [];
                foreach($taxonomy['options'] as $name => $label){
                    $options[] = ['name' => $name, 'label' => $label];
                }
                $taxonomies[$taxonomy_name]['name'] = $taxonomy['name'];
                $taxonomies[$taxonomy_name]['options'] = $options;
            }
        }
        $body = $this->app['render']->render('@TaxonomyEditor/base.twig', array(
            //'contenttypes'  => $contenttypes,
            'menus'     => $taxonomies,
            //'menus'         => $menus,
            //'writeLock'     => $writeLock,
            //'backups'       => $backups
        ));

        //return new Response($body);
        return new Response($this->injectAssets($body));

    }


    /**
     * Saves a backup of the current menu.yml
     *
     * @param $writeLock
     * @param bool $justFetchList
     * @return array
     * @throws TaxonomyEditorException
     */
    private function backup($writeLock, $justFetchList = false)
    {

        if (!@is_dir($this->backupDir) && !@mkdir($this->backupDir)) {
            // dir doesn't exist and I can't create it
            throw new TaxonomyEditorException($justFetchList ? __("Please make sure that there is a TaxonomyEditor/backups folder or disable the backup-feature in config.yml") : $writeLock, 5);
        }

        // try to save a backup
        if (false === $justFetchList &&
            !@copy(BOLT_CONFIG_DIR . '/menu.yml', $this->backupDir . '/menu.'. time() . '.yml'))
        {
            throw new TaxonomyEditorException($writeLock, 5);
        }

        // clean up
        $backupFiles = array();
        foreach (new \DirectoryIterator($this->backupDir) as $fileinfo) {
            if ($fileinfo->isFile() && preg_match("~^menu\.[0-9]{10}\.yml$~i", $fileinfo->getFilename())) {
                $backupFiles[$fileinfo->getMTime()] = $fileinfo->getFilename();
            }
        }

        if ($justFetchList)
        {
            // make sure there's at least one backup file (first use...)
            if (count($backupFiles) == 0)
            {
                if (!@copy(BOLT_CONFIG_DIR . '/menu.yml', $this->backupDir . '/menu.'. time() . '.yml')) {
                    throw new TaxonomyEditorException(__("Please make sure that the TaxonomyEditor/backups folder is writeable by your webserver or disable the backup-feature in config.yml"));
                }
                return $this->backup(0, true);
            }

            krsort($backupFiles);
            return $backupFiles;
        }

        ksort($backupFiles);
        foreach ($backupFiles as $timestamp=>$backupFile)
        {
            if (count($backupFiles) <= (int) $this->config['keepBackups']) {
                break;
            }

            @unlink($this->backupDir . '/' . $backupFile);
            unset($backupFiles[$timestamp]);
        }

    }

    /**
     * Restores a previously saved backup, identified by its timestamp
     *
     * @param $filetime
     * @return bool
     * @throws TaxonomyEditorException
     */
    private function restoreBackup($filetime)
    {

        $backupFiles = $this->backup(0, true);

        foreach ($backupFiles as $backupFiletime=>$backupFile)
        {
            if ($backupFiletime == $filetime)
            {
                // try to overwrite menu.yml
                if (@copy($this->backupDir . '/' . $backupFile, BOLT_CONFIG_DIR . '/menu.yml')) {
                    return true;
                }

                throw new TaxonomyEditorException(__("Unable to overwrite menu.yml"));
            }
        }

        // requested backup-file was not found
        return false;
    }

    /**
     * symlinks the localized readme file, if existant
     */
    private function localizeReadme()
    {

        $this->app['cache']->save('extension_TaxonomyEditor', $this->app['locale'], 604800);

        if (@file_exists(__DIR__ . '/readme.md')) {
            if (@is_link(__DIR__ . '/readme.md')) {
                return;
            }

            if (@is_dir($this->translationDir))
            {

                // try to set symbolic link
                if (@file_exists(__DIR__.'/translations/readme_'. $this->app['locale'] .'.md'))
                {
                    @copy(__DIR__ . '/readme.md', __DIR__ . '/readme_en.md');
                    @unlink(__DIR__ . '/readme.md');
                    @symlink(__DIR__.'/translations/readme_'. $this->app['locale'] .'.md', __DIR__ . '/readme.md');
                }
            }
        }

    }
}
